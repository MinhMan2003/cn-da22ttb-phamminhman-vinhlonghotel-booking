<?php 

require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

// ====================== Phân tích đặt phòng ==========================
if(isset($_POST['booking_analytics']))
{
  $frm_data = filteration($_POST);

  $time_condition = "";
  if($frm_data['period'] == 1){
    $time_condition = "AND bo.datentime BETWEEN NOW() - INTERVAL 30 DAY AND NOW()";
  }
  else if($frm_data['period'] == 2){
    $time_condition = "AND bo.datentime BETWEEN NOW() - INTERVAL 90 DAY AND NOW()";
  }
  else if($frm_data['period'] == 3){
    $time_condition = "AND bo.datentime BETWEEN NOW() - INTERVAL 1 YEAR AND NOW()";
  }

  // Đếm/sum đúng trạng thái: booked (dù arrival 0/1) và cancelled; bỏ qua payment failed
  // Chỉ tính booking của phòng admin (owner_id IS NULL)
  $result = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT 
      COUNT(CASE WHEN bo.booking_status IN ('booked','cancelled') THEN 1 END) AS total_bookings,
      SUM(CASE WHEN bo.booking_status IN ('booked','cancelled') THEN bo.trans_amt END) AS total_amt,

      COUNT(CASE WHEN bo.booking_status='booked' THEN 1 END) AS active_bookings,
      SUM(CASE WHEN bo.booking_status='booked' THEN bo.trans_amt END) AS active_amt,

      COUNT(CASE WHEN bo.booking_status='cancelled' THEN 1 END) AS cancelled_bookings,
      SUM(CASE WHEN bo.booking_status='cancelled' THEN bo.trans_amt END) AS cancelled_amt
    FROM booking_order bo
    INNER JOIN rooms r ON bo.room_id = r.id
    WHERE r.owner_id IS NULL
    $time_condition
  "));

  // format with thousands separator
  $result_fmt = $result;
  foreach(['total_amt','active_amt','cancelled_amt'] as $k){
    $result_fmt[$k] = $result[$k] !== null ? number_format($result[$k],0,',','.') : '0';
  }
  echo json_encode($result_fmt);
  exit;
}

// ====================== USER ANALYTICS ==========================
if(isset($_POST['user_analytics']))
{
  $frm_data = filteration($_POST);

  $condition = "";
  if($frm_data['period'] == 1){
    $condition = "WHERE datentime BETWEEN NOW() - INTERVAL 30 DAY AND NOW()";
  }
  else if($frm_data['period'] == 2){
    $condition = "WHERE datentime BETWEEN NOW() - INTERVAL 90 DAY AND NOW()";
  }
  else if($frm_data['period'] == 3){
    $condition = "WHERE datentime BETWEEN NOW() - INTERVAL 1 YEAR AND NOW()";
  }

  $total_reviews = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(sr_no) AS count FROM rating_review $condition"));
  $total_queries = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(sr_no) AS count FROM user_queries $condition"));
  $total_new_reg = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(id) AS count FROM user_cred $condition"));

  $output = [
    'total_queries' => $total_queries['count'],
    'total_reviews' => $total_reviews['count'],
    'total_new_reg' => $total_new_reg['count']
  ];

  echo json_encode($output);
  exit;
}

// ====================== NEW BOOKINGS & REFUNDS ==========================
if (isset($_POST['new_bookings'])) {
  // Không lọc theo thời gian để khớp sidebar: đếm booking mới chưa arrival (chỉ phòng admin)
  $sql = "
    SELECT 
      COUNT(CASE WHEN bo.booking_status='pending' OR (bo.booking_status='booked' AND COALESCE(bo.arrival,0)=0) THEN 1 END) AS new_bookings,
      COUNT(CASE WHEN bo.booking_status='cancelled' AND bo.refund=1 THEN 1 END) AS refunds_done,
      COUNT(CASE WHEN bo.booking_status='cancelled' AND bo.refund=0 THEN 1 END) AS refunds_wait
    FROM booking_order bo
    INNER JOIN rooms r ON bo.room_id = r.id
    WHERE r.owner_id IS NULL
  ";

  $result = mysqli_fetch_assoc(mysqli_query($con, $sql));
  // Trả thêm key refunds cho JS cũ
  $result['refunds'] = $result['refunds_wait'];
  echo json_encode($result);
  exit;
}


?>
