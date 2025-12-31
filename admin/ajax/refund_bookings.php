<?php
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

/* =====================================================
   📊 LẤY DANH SÁCH CÁC ĐƠN HỦY CẦN HOÀN TIỀN
===================================================== */
if (isset($_POST['get_bookings'])) {
  $frm_data = filteration($_POST);

  $query = "SELECT bo.*, bd.* 
            FROM booking_order bo
            INNER JOIN rooms r ON bo.room_id = r.id
            LEFT JOIN booking_details bd ON bo.booking_id = bd.booking_id
            WHERE (r.owner_id IS NULL)
            AND (bo.order_id LIKE ? 
              OR bd.phonenum LIKE ? 
              OR bd.user_name LIKE ?) 
            AND bo.booking_status='cancelled' 
            AND bo.refund=0 
            ORDER BY bo.booking_id ASC";

  $res = select($query, [
    "%{$frm_data['search']}%",
    "%{$frm_data['search']}%",
    "%{$frm_data['search']}%"
  ], 'sss');

  $i = 1;
  $table_data = "";

  if (mysqli_num_rows($res) == 0) {
    echo "<b>No Data Found!</b>";
    exit;
  }

  while ($data = mysqli_fetch_assoc($res)) {
    $date = date("d-m-Y", strtotime($data['datentime']));
    $checkin = date("d-m-Y", strtotime($data['check_in']));
    $checkout = date("d-m-Y", strtotime($data['check_out']));
    $amt = $data['trans_amt'] ? number_format($data['trans_amt'], 0, ',', '.') : '0';

    $table_data .= "
      <tr>
        <td>$i</td>
        <td>
          <span class='badge bg-primary'>Order ID: $data[order_id]</span><br>
          <b>Tên:</b> $data[user_name]<br>
          <b>Số điện thoại:</b> $data[phonenum]
        </td>
        <td>
          <b>Phòng:</b> $data[room_name]<br>
          <b>Check-in:</b> $checkin<br>
          <b>Check-out:</b> $checkout<br>
          <b>Ngày: </b> $date
        </td>
        <td><b>{$amt} VND</b></td>
        <td>
          <button type='button' onclick='refund_booking($data[booking_id])' 
            class='btn btn-success btn-sm fw-bold shadow-none'>
            <i class='bi bi-cash-stack'></i> Hoàn tiền
          </button>
        </td>
      </tr>
    ";

    $i++;
  }

  echo $table_data;
  exit;
}

/* =====================================================
   💰 HOÀN TIỀN CHO MỘT ĐƠN BOOKING (CHỈ PHÒNG CỦA ADMIN)
===================================================== */
if (isset($_POST['refund_booking'])) {
  // Lấy ID truyền từ JS
  $booking_id = (int)($_POST['booking_id'] ?? 0);

  if ($booking_id <= 0) {
    echo "invalid_id";
    exit;
  }

  // Kiểm tra booking có thuộc về phòng của admin không
  $check_query = "SELECT bo.booking_id, r.owner_id 
                  FROM booking_order bo
                  INNER JOIN rooms r ON bo.room_id = r.id
                  WHERE bo.booking_id = ? AND r.owner_id IS NULL 
                  AND bo.booking_status='cancelled' AND COALESCE(bo.refund, 0) = 0";
  $check_res = select($check_query, [$booking_id], 'i');
  
  if (!$check_res || mysqli_num_rows($check_res) == 0) {
    echo "0"; // Không có quyền hoặc đã hoàn tiền
    exit;
  }

  // Cập nhật trạng thái refund
  $query = "UPDATE booking_order bo
            INNER JOIN rooms r ON bo.room_id = r.id
            SET bo.refund=1, bo.refund_date=NOW() 
            WHERE bo.booking_id=? AND r.owner_id IS NULL AND bo.refund=0";

  $res = update($query, [$booking_id], 'i');

  if ($res > 0) {
    echo "1"; // Hoàn tiền thành công
  } else {
    echo "0"; // Không có dòng nào được cập nhật (đã hoàn hoặc lỗi)
  }
  exit;
}
?>
