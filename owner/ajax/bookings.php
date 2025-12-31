<?php
require('../../admin/inc/db_config.php');
require('../../admin/inc/essentials.php');

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['ownerLogin']) || $_SESSION['ownerLogin'] != true) {
    echo "Unauthorized";
    exit;
}

$owner_id = (int)$_SESSION['ownerId'];

if(isset($_POST['get_bookings']))
{
  $frm_data = filteration($_POST);
  $search = $frm_data['search'] ?? '';

  $conditions = ["r.owner_id = $owner_id"];
  $params = [];
  $types = '';

  if($search !== ''){
    $conditions[] = "(bo.order_id LIKE ? OR bd.phonenum LIKE ? OR bd.user_name LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'sss';
  }

  $where = implode(' AND ', $conditions);
  
  $query = "SELECT bo.*, bd.*, r.name AS room_name, r.price, r.owner_id, uc.email AS user_email
            FROM booking_order bo
            INNER JOIN rooms r ON bo.room_id = r.id
            LEFT JOIN booking_details bd ON bo.booking_id = bd.booking_id
            LEFT JOIN user_cred uc ON bo.user_id = uc.id
            WHERE $where
            ORDER BY bo.booking_id DESC";

  if($types){
    $res = select($query, $params, $types);
  } else {
    $res = mysqli_query($con, $query);
  }

  // Kiểm tra lỗi query
  if(!$res){
    echo "<tr><td colspan='6' class='text-center text-danger'>Lỗi truy vấn: " . mysqli_error($con) . "</td></tr>";
    exit;
  }

  $data = "";
  if(mysqli_num_rows($res) == 0){
    echo "<tr><td colspan='6' class='text-center text-muted'>Không có đặt phòng nào</td></tr>";
    exit;
  }

  $i = 1;
  while($row = mysqli_fetch_assoc($res))
  {
    // Định dạng ngày tháng
    $date = date("d-m-Y", strtotime($row['datentime']));
    $checkin = date("d-m-Y", strtotime($row['check_in']));
    $checkout = date("d-m-Y", strtotime($row['check_out']));

    // Tính số đêm & tổng tiền
    $checkin_dt = new DateTime($row['check_in']);
    $checkout_dt = new DateTime($row['check_out']);
    $days = $checkout_dt->diff($checkin_dt)->days;
    if($days == 0){ $days = 1; }

    $total = $row['price'] * $days;

    // Định dạng tiền
    $price_fmt = number_format($row['price'], 0, ',', '.');
    $total_fmt = number_format($total, 0, ',', '.');
    $paid_fmt = number_format($row['trans_amt'] ?? 0, 0, ',', '.');
    $email = htmlspecialchars($row['user_email'] ?? '', ENT_QUOTES, 'UTF-8');
    $email_line = $email ? "<br><b>Email:</b> $email" : "";

    // Xử lý địa chỉ, yêu cầu, mã KM
    $raw_address = $row['address'] ?? '';
    $promo_text = '';
    $special_req = '';

    // Yêu cầu đặc biệt
    if(strpos($raw_address, '| SR:') !== false){
      [$raw_address, $sr_part] = explode('| SR:', $raw_address, 2);
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

    $address = htmlspecialchars(trim($raw_address), ENT_QUOTES, 'UTF-8');
    $promo_safe = htmlspecialchars($promo_text, ENT_QUOTES, 'UTF-8');
    $sr_safe = $special_req !== '' ? htmlspecialchars($special_req, ENT_QUOTES, 'UTF-8') : '—';
    $trans_id = htmlspecialchars($row['trans_id'] ?? '', ENT_QUOTES, 'UTF-8');
    $order_id = htmlspecialchars($row['order_id'], ENT_QUOTES, 'UTF-8');
    $user_name = htmlspecialchars($row['user_name'] ?? '', ENT_QUOTES, 'UTF-8');
    $phonenum = htmlspecialchars($row['phonenum'] ?? '', ENT_QUOTES, 'UTF-8');
    $room_name = htmlspecialchars($row['room_name'], ENT_QUOTES, 'UTF-8');

    // Kiểm tra xem có thể giao phòng không (pending hoặc booked)
    $can_assign = ($row['booking_status'] == 'pending' || $row['booking_status'] == 'booked');
    $room_no = htmlspecialchars($row['room_no'] ?? '', ENT_QUOTES, 'UTF-8');
    $has_room_no = !empty($room_no);
    $booking_id = (int)$row['booking_id'];

    // Ô nhập số phòng - luôn hiển thị input nếu có thể giao phòng
    $room_input = '';
    if ($can_assign) {
      if ($has_room_no) {
        // Đã có số phòng - hiển thị với input để sửa
        $room_input = "
          <div class='d-flex gap-1 align-items-center justify-content-center'>
            <input type='text' 
                   id='room_no_{$booking_id}' 
                   class='form-control form-control-sm shadow-none' 
                   value='$room_no'
                   placeholder='Nhập số phòng'
                   style='width: 100px;'
                   onkeypress='if(event.key===\"Enter\") assignRoomDirect({$booking_id})'>
            <button type='button' 
                    onclick='assignRoomDirect({$booking_id})' 
                    class='btn btn-success btn-sm shadow-none' 
                    title='Cập nhật số phòng'>
              <i class='bi bi-check-lg'></i>
            </button>
          </div>";
      } else {
        // Chưa có số phòng - hiển thị input trống
        $room_input = "
          <div class='d-flex gap-1 align-items-center justify-content-center'>
            <input type='text' 
                   id='room_no_{$booking_id}' 
                   class='form-control form-control-sm shadow-none' 
                   placeholder='Nhập số phòng'
                   style='width: 100px;'
                   onkeypress='if(event.key===\"Enter\") assignRoomDirect({$booking_id})'>
            <button type='button' 
                    onclick='assignRoomDirect({$booking_id})' 
                    class='btn btn-success btn-sm shadow-none' 
                    title='Giao phòng'>
              <i class='bi bi-check-lg'></i>
            </button>
          </div>";
      }
    } else {
      // Booking đã hủy hoặc không thể giao phòng
      if ($has_room_no) {
        $room_input = "<span class='badge bg-secondary'><i class='bi bi-door-open me-1'></i>$room_no</span>";
      } else {
        $room_input = "<span class='text-muted'>—</span>";
      }
    }

    // Nút hành động - luôn hiển thị nút "Giao phòng" nếu có thể
    $action_buttons = '';
    if ($can_assign) {
      $action_buttons = "
        <button type='button' onclick='assign_room({$booking_id})' 
                class='btn btn-success btn-sm fw-bold shadow-none mb-2' 
                data-bs-toggle='modal' data-bs-target='#assign-room'>
          <i class='bi bi-check2-square'></i> Giao phòng
        </button><br>
        <button type='button' onclick='printInvoice({$booking_id})' 
                class='btn btn-info btn-sm fw-bold shadow-none mb-2'>
          <i class='bi bi-printer'></i> In hóa đơn
        </button><br>
        <button type='button' onclick='cancel_booking({$booking_id})' 
                class='btn btn-outline-danger btn-sm fw-bold shadow-none'>
          <i class='bi bi-trash'></i> Huỷ đặt phòng
        </button>";
    } else {
      $action_buttons = "
        <button type='button' onclick='printInvoice({$booking_id})' 
                class='btn btn-info btn-sm fw-bold shadow-none mb-2'>
          <i class='bi bi-printer'></i> In hóa đơn
        </button><br>
        <button type='button' onclick='cancel_booking({$booking_id})' 
                class='btn btn-outline-danger btn-sm fw-bold shadow-none'>
          <i class='bi bi-trash'></i> Huỷ đặt phòng
        </button>";
    }

    $data .= "
      <tr>
        <td>$i</td>
        <td>
          <span class='badge bg-primary'>
            Order ID: $order_id
          </span>
          <br>
          <b>Tên:</b> $user_name
          <br>
          <b>Số điện thoại:</b> $phonenum
          $email_line
          <br>
          <b>Địa chỉ / ghi chú:</b> $address
          <br>
          <b>Yêu cầu:</b> $sr_safe
        </td>
        <td>
          <b>Phòng:</b> $room_name
          <br>
          <b>Giá:</b> {$price_fmt} VND / đêm
          <br>
          <b>Số đêm:</b> {$days}
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
                        <td>$room_input</td>
        <td>$action_buttons</td>
      </tr>";
    
    $i++;
  }

  echo $data;
}

// 🏨 Giao phòng (Assign Room) - Chỉ cho phòng của owner
if(isset($_POST['assign_room']))
{
  $frm_data = filteration($_POST);
  $booking_id = (int)$frm_data['booking_id'];
  $room_no = $frm_data['room_no'] ?? '';

  // Kiểm tra booking có thuộc về phòng của owner không
  $check_query = "SELECT bo.booking_id, r.owner_id 
                  FROM booking_order bo
                  INNER JOIN rooms r ON bo.room_id = r.id
                  WHERE bo.booking_id = ? AND r.owner_id = ?";
  $check_res = select($check_query, [$booking_id, $owner_id], 'ii');
  
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
    WHERE bo.booking_id = ? AND r.owner_id = ?";

  $values = [1, 0, $room_no, 'booked', $booking_id, $owner_id];

  $res = update($query, $values, 'iissii'); 

  echo ($res >= 1) ? 1 : 0;
}

// ❌ Hủy đặt phòng - Chỉ cho phòng của owner
if(isset($_POST['cancel_booking']))
{
  $frm_data = filteration($_POST);
  $booking_id = (int)$frm_data['booking_id'];

  // Kiểm tra booking có thuộc về phòng của owner không
  $check_query = "SELECT bo.booking_id, r.owner_id 
                  FROM booking_order bo
                  INNER JOIN rooms r ON bo.room_id = r.id
                  WHERE bo.booking_id = ? AND r.owner_id = ?";
  $check_res = select($check_query, [$booking_id, $owner_id], 'ii');
  
  if (!$check_res || mysqli_num_rows($check_res) == 0) {
    echo 0; // Không có quyền
    exit;
  }

  // Khi hủy booking, tự động set refund=0 (chưa hoàn tiền)
  // Owner sẽ cần vào trang hoàn tiền để xử lý
  $query = "UPDATE `booking_order` 
            SET `booking_status`=?, `refund`=0 
            WHERE `booking_id`=?";
  $values = ['cancelled', $booking_id];
  $res = update($query, $values, 'si');

  echo $res;
}
?>

