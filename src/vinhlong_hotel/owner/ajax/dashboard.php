<?php
require('../../admin/inc/db_config.php');
require('../../admin/inc/essentials.php');

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['ownerLogin']) || $_SESSION['ownerLogin'] != true) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$owner_id = (int)$_SESSION['ownerId'];

// ====================== Phân tích đặt phòng ==========================
if(isset($_POST['booking_analytics']))
{
  $frm_data = filteration($_POST);
  $period = (int)($frm_data['period'] ?? 1);

  $condition = "INNER JOIN rooms r ON bo.room_id = r.id WHERE r.owner_id = $owner_id";
  
  if($period == 1){
    $condition .= " AND bo.datentime BETWEEN NOW() - INTERVAL 30 DAY AND NOW()";
  }
  else if($period == 2){
    $condition .= " AND bo.datentime BETWEEN NOW() - INTERVAL 90 DAY AND NOW()";
  }
  else if($period == 3){
    $condition .= " AND bo.datentime BETWEEN NOW() - INTERVAL 1 YEAR AND NOW()";
  }

  $result = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT 
      COUNT(CASE WHEN bo.booking_status IN ('booked','cancelled') THEN 1 END) AS total_bookings,
      SUM(CASE WHEN bo.booking_status IN ('booked','cancelled') AND (bo.trans_status='TXN_SUCCESS' OR bo.trans_status='paid') AND bo.trans_amt > 0 THEN bo.trans_amt ELSE 0 END) AS total_amt,
      COUNT(CASE WHEN bo.booking_status='booked' THEN 1 END) AS active_bookings,
      SUM(CASE WHEN bo.booking_status='booked' AND (bo.trans_status='TXN_SUCCESS' OR bo.trans_status='paid') AND bo.trans_amt > 0 THEN bo.trans_amt ELSE 0 END) AS active_amt,
      COUNT(CASE WHEN bo.booking_status='cancelled' THEN 1 END) AS cancelled_bookings,
      SUM(CASE WHEN bo.booking_status='cancelled' AND (bo.trans_status='TXN_SUCCESS' OR bo.trans_status='paid') AND bo.trans_amt > 0 THEN bo.trans_amt ELSE 0 END) AS cancelled_amt
    FROM booking_order bo
    $condition
  "));

  $result_fmt = $result;
  foreach(['total_amt','active_amt','cancelled_amt'] as $k){
    $result_fmt[$k] = $result[$k] !== null ? number_format($result[$k],0,',','.') : '0';
  }
  echo json_encode($result_fmt);
  exit;
}

// ====================== Revenue Chart (7 ngày gần đây) ==========================
if(isset($_POST['revenue_chart']))
{
  $labels = [];
  $revenues = [];
  
  for($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $date_label = date('d/m', strtotime("-$i days"));
    $labels[] = $date_label;
    
    $revenue = mysqli_fetch_assoc(mysqli_query($con, "
      SELECT COALESCE(SUM(bo.trans_amt), 0) AS total
      FROM booking_order bo
      INNER JOIN rooms r ON bo.room_id = r.id
      WHERE r.owner_id = $owner_id 
      AND (bo.trans_status='TXN_SUCCESS' OR bo.trans_status='paid')
      AND bo.trans_amt > 0
      AND DATE(bo.datentime) = '$date'
    "))['total'];
    
    $revenues[] = (int)$revenue;
  }
  
  echo json_encode([
    'labels' => $labels,
    'revenues' => $revenues
  ]);
  exit;
}
?>

