<?php 

  require('../inc/db_config.php');
  require('../inc/essentials.php');
  adminLogin();

  // 📦 Lấy danh sách đặt phòng mới
  if(isset($_POST['get_bookings']))
  {
    $frm_data = filteration($_POST);

    // ✅ Hiển thị đơn "pending" (dù arrival null/0) + "booked" chưa giao phòng (arrival null/0)
    // Chỉ hiển thị booking của phòng do admin tạo (owner_id IS NULL)
    $query = "SELECT bo.*, bd.*, uc.email AS user_email 
      FROM `booking_order` bo
      INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
      INNER JOIN `rooms` r ON bo.room_id = r.id
      LEFT JOIN user_cred uc ON bo.user_id = uc.id
      WHERE (bo.order_id LIKE ? OR bd.phonenum LIKE ? OR bd.user_name LIKE ?) 
      AND (r.owner_id IS NULL)
      AND ( bo.booking_status='pending' 
            OR (bo.booking_status='booked' AND COALESCE(bo.arrival,0)=0) )
      ORDER BY bo.booking_id ASC";

    $res = select($query,[
      "%$frm_data[search]%",
      "%$frm_data[search]%",
      "%$frm_data[search]%"
    ],'sss');
    
    $i=1;
    $table_data = "";

    if(mysqli_num_rows($res)==0){
      echo"<b>No Data Found!</b>";
      exit;
    }

    while($data = mysqli_fetch_assoc($res))
{
  // 👉 Định dạng ngày tháng từ dữ liệu trong CSDL
  $date = date("d-m-Y", strtotime($data['datentime']));     // Ngày tạo đơn
  $checkin = date("d-m-Y", strtotime($data['check_in']));   // Ngày nhận phòng
  $checkout = date("d-m-Y", strtotime($data['check_out'])); // Ngày trả phòng

  // 👉 TÍNH SỐ ĐÊM & TỔNG TIỀN
  $checkin_dt  = new DateTime($data['check_in']);
  $checkout_dt = new DateTime($data['check_out']);
  $days = $checkout_dt->diff($checkin_dt)->days; // Tính số ngày chênh lệch
  if($days == 0){ $days = 1; } // Nếu cùng ngày thì vẫn tính là 1 đêm

  $total = $data['price'] * $days; // Tổng tiền = giá/đêm × số đêm

  // 👉 Định dạng tiền cho dễ nhìn (vd: 3.500.000)
  $price_fmt = number_format($data['price'], 0, ',', '.');     // Giá / đêm
  $total_fmt = number_format($total, 0, ',', '.');             // Tổng tiền
  $paid_fmt  = number_format($data['trans_amt'], 0, ',', '.'); // Số tiền đã thanh toán
  $email     = htmlspecialchars($data['user_email'] ?? '', ENT_QUOTES, 'UTF-8');
  $email_line = $email ? "<br>\n        <b>Email:</b> $email" : "";
  // Tách ghi chú, yêu cầu, mã KM khỏi địa chỉ (hỗ trợ cả định dạng cũ "| Promo:")
  $raw_address = $data['address'] ?? '';
  $promo_text = '';
  $special_req = '';

  // Yêu cầu đặc biệt
  if(strpos($raw_address, '| SR:') !== false){
    [$raw_address, $sr_part] = explode('| SR:', $raw_address, 2);
    // Loại promo ra khỏi phần yêu cầu nếu bị dính kèm
    if(strpos($sr_part, '| Mã KM:') !== false){
      [$sr_part, $promo_in_sr] = explode('| Mã KM:', $sr_part, 2);
      $promo_text = trim($promo_text ?: $promo_in_sr);
    } else if(strpos($sr_part, '| Promo:') !== false){
      [$sr_part, $promo_in_sr] = explode('| Promo:', $sr_part, 2);
      $promo_text = trim($promo_text ?: $promo_in_sr);
    }
    $special_req = trim($sr_part);
  }
  // Mã khuyến mãi
  if(strpos($raw_address, '| Mã KM:') !== false){
    [$raw_address, $promo_part] = explode('| Mã KM:', $raw_address, 2);
    $promo_text = trim($promo_part);
  } else if(strpos($raw_address, '| Promo:') !== false){
    [$raw_address, $promo_part] = explode('| Promo:', $raw_address, 2);
    $promo_text = trim($promo_part);
  }

  $address    = htmlspecialchars(trim($raw_address), ENT_QUOTES, 'UTF-8');
  $promo_safe = htmlspecialchars($promo_text, ENT_QUOTES, 'UTF-8');
  $sr_safe    = $special_req !== '' ? htmlspecialchars($special_req, ENT_QUOTES, 'UTF-8') : '—';
  $trans_id  = htmlspecialchars($data['trans_id'] ?? '', ENT_QUOTES, 'UTF-8');
  // hiển thị mã KM dạng text (không dùng badge)

  // 👉 Ghép chuỗi HTML tạo từng hàng <tr> trong bảng
  $table_data .="
    <tr>
      <td>$i</td>
      <td>
        <span class='badge bg-primary'>
          Order ID: $data[order_id]
        </span>
        <br>
        <b>Tên:</b> $data[user_name]
        <br>
        <b>Số điện thoại:</b> $data[phonenum]
        $email_line
        <br>
        <b>Địa chỉ / ghi chú:</b> $address
        <br>
        <b>Yêu cầu:</b> $sr_safe
      </td>
      <td>
        <b>Phòng:</b> $data[room_name]
        <br>
        <b>Giá:</b> {$price_fmt} VND / đêm
        <br>
        <b>Số phòng:</b> {$days}
        <br>
        <b>Tổng cộng:</b> {$total_fmt} VND
        <br>
        <b>Mã KM:</b> ".($promo_safe ?: '—')."
      </td>
      <td>
        <b>Check-in:</b> $checkin
        <br>
        <b>Check-out:</b> $checkout
        <br>
        <b>Đã thanh toán:</b> {$paid_fmt} VND
        <br>
        <b>Mã giao dịch:</b> ".($trans_id ?: '—')."
        <br>
        <b>Ngày đặt:</b> $date
      </td>
      <td>
        <!-- Nút giao phòng -->
        <button type='button' onclick='assign_room($data[booking_id])' 
                class='btn text-white btn-sm fw-bold custom-bg shadow-none' 
                data-bs-toggle='modal' data-bs-target='#assign-room'>
          <i class='bi bi-check2-square'></i> Giao phòng
        </button>
        <br>
        <!-- Nút hủy đặt phòng -->
        <button type='button' onclick='cancel_booking($data[booking_id])' 
                class='mt-2 btn btn-outline-danger btn-sm fw-bold shadow-none'>
          <i class='bi bi-trash'></i> Huỷ đặt phòng
        </button>
      </td>
    </tr>
  ";

  $i++; // Tăng biến đếm để đánh số thứ tự từng dòng
}



    echo $table_data;
  }

  // 🏨 Giao phòng (Assign Room) - Chỉ cho phòng của admin
  if(isset($_POST['assign_room']))
  {
    $frm_data = filteration($_POST);
    $booking_id = (int)$frm_data['booking_id'];

    // Kiểm tra booking có thuộc về phòng của admin không
    $check_query = "SELECT bo.booking_id, r.owner_id 
                    FROM booking_order bo
                    INNER JOIN rooms r ON bo.room_id = r.id
                    WHERE bo.booking_id = ? AND r.owner_id IS NULL";
    $check_res = select($check_query, [$booking_id], 'i');
    
    if (!$check_res || mysqli_num_rows($check_res) == 0) {
      echo 0; // Không có quyền
      exit;
    }

    // ✅ Cập nhật cả arrival và booking_status = 'booked'
    $query = "UPDATE `booking_order` bo 
      INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
      INNER JOIN `rooms` r ON bo.room_id = r.id
      SET bo.arrival = ?, 
          bo.rate_review = ?, 
          bd.room_no = ?, 
          bo.booking_status = ?
      WHERE bo.booking_id = ? AND r.owner_id IS NULL";

    $values = [1, 0, $frm_data['room_no'], 'booked', $booking_id];

    $res = update($query, $values, 'iissi'); 

    echo ($res >= 1) ? 1 : 0;
  }

  // ❌ Huỷ đặt phòng - Chỉ cho phòng của admin
  if(isset($_POST['cancel_booking']))
  {
    $frm_data = filteration($_POST);
    $booking_id = (int)$frm_data['booking_id'];

    // Kiểm tra booking có thuộc về phòng của admin không
    $check_query = "SELECT bo.booking_id, r.owner_id 
                    FROM booking_order bo
                    INNER JOIN rooms r ON bo.room_id = r.id
                    WHERE bo.booking_id = ? AND r.owner_id IS NULL";
    $check_res = select($check_query, [$booking_id], 'i');
    
    if (!$check_res || mysqli_num_rows($check_res) == 0) {
      echo 0; // Không có quyền
      exit;
    }
    
    $query = "UPDATE `booking_order` bo
              INNER JOIN `rooms` r ON bo.room_id = r.id
              SET bo.booking_status=?, bo.refund=? 
              WHERE bo.booking_id=? AND r.owner_id IS NULL";
    $values = ['cancelled', 0, $booking_id];
    $res = update($query, $values, 'sii');

    echo $res;
  }
  

?>
