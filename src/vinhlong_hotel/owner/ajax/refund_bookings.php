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

/* =====================================================
   📊 LẤY DANH SÁCH CÁC ĐƠN HỦY CẦN HOÀN TIỀN (CHỈ PHÒNG CỦA OWNER)
===================================================== */
if (isset($_POST['get_bookings'])) {
  $frm_data = filteration($_POST);
  $search = $frm_data['search'] ?? '';

  $conditions = [
    "r.owner_id = $owner_id",
    "bo.booking_status='cancelled'",
    "COALESCE(bo.refund, 0) = 0"
  ];
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

  $query = "SELECT bo.*, bd.*, r.name AS room_name
            FROM booking_order bo
            INNER JOIN rooms r ON bo.room_id = r.id
            LEFT JOIN booking_details bd ON bo.booking_id = bd.booking_id
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
    echo "<tr><td colspan='6' class='text-center text-muted'>Không có đơn nào cần hoàn tiền</td></tr>";
    exit;
  }

  $i = 1;
  while ($row = mysqli_fetch_assoc($res)) {
    $date = date("d-m-Y", strtotime($row['datentime']));
    $checkin = date("d-m-Y", strtotime($row['check_in']));
    $checkout = date("d-m-Y", strtotime($row['check_out']));
    $amt = $row['trans_amt'] ? number_format($row['trans_amt'], 0, ',', '.') : '0';
    
    $order_id = htmlspecialchars($row['order_id'], ENT_QUOTES, 'UTF-8');
    $user_name = htmlspecialchars($row['user_name'] ?? '', ENT_QUOTES, 'UTF-8');
    $phonenum = htmlspecialchars($row['phonenum'] ?? '', ENT_QUOTES, 'UTF-8');
    $room_name = htmlspecialchars($row['room_name'], ENT_QUOTES, 'UTF-8');
    $booking_id = (int)$row['booking_id'];

    $data .= "
      <tr>
        <td>$i</td>
        <td>
          <span class='badge bg-primary'>Order ID: $order_id</span><br>
          <b>Tên:</b> $user_name<br>
          <b>Số điện thoại:</b> $phonenum
        </td>
        <td>
          <b>Phòng:</b> $room_name
        </td>
        <td>
          <b>Check-in:</b> $checkin<br>
          <b>Check-out:</b> $checkout<br>
          <b>Ngày đặt:</b> $date
        </td>
        <td><b class='text-danger'>{$amt} VND</b></td>
        <td>
          <button type='button' onclick='refund_booking($booking_id)' 
            class='btn btn-success btn-sm fw-bold shadow-none'>
            <i class='bi bi-cash-stack'></i> Hoàn tiền
          </button>
        </td>
      </tr>
    ";

    $i++;
  }

  echo $data;
  exit;
}

/* =====================================================
   💰 HOÀN TIỀN CHO MỘT ĐƠN BOOKING (CHỈ PHÒNG CỦA OWNER)
===================================================== */
if (isset($_POST['refund_booking'])) {
  $frm_data = filteration($_POST);
  $booking_id = (int)($frm_data['booking_id'] ?? 0);

  if ($booking_id <= 0) {
    echo 0;
    exit;
  }

  // Kiểm tra booking có thuộc về phòng của owner không
  $check_query = "SELECT bo.booking_id, r.owner_id 
                  FROM booking_order bo
                  INNER JOIN rooms r ON bo.room_id = r.id
                  WHERE bo.booking_id = ? AND r.owner_id = ? AND bo.booking_status='cancelled' AND COALESCE(bo.refund, 0) = 0";
  $check_res = select($check_query, [$booking_id, $owner_id], 'ii');
  
  if (!$check_res || mysqli_num_rows($check_res) == 0) {
    echo 0; // Không có quyền hoặc đã hoàn tiền
    exit;
  }

  // Cập nhật trạng thái refund
  $query = "UPDATE booking_order 
            SET refund=1, refund_date=NOW() 
            WHERE booking_id=? AND refund=0";

  $res = update($query, [$booking_id], 'i');

  echo ($res > 0) ? 1 : 0;
  exit;
}
?>

