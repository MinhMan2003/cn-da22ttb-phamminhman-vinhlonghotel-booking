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

if(isset($_POST['get_revenue']))
{
  $frm_data = filteration($_POST);
  $period = $frm_data['period'] ?? 'year';
  
  $monthly_revenue = [];
  $labels = [];
  $current_year = date('Y');
  
  if($period == 'year') {
    // Năm hiện tại
    for ($i = 1; $i <= 12; $i++) {
      $month_start = "$current_year-$i-01";
      $month_end = date("Y-m-t", strtotime($month_start));
      $labels[] = "T$i";
      
      $revenue = mysqli_fetch_assoc(mysqli_query($con, "
        SELECT COALESCE(SUM(trans_amt), 0) AS total
        FROM booking_order bo
        INNER JOIN rooms r ON bo.room_id = r.id
        WHERE r.owner_id=$owner_id 
        AND (bo.trans_status='TXN_SUCCESS' OR bo.trans_status='paid')
        AND bo.trans_amt > 0
        AND DATE(bo.datentime) BETWEEN '$month_start' AND '$month_end'
      "))['total'];
      
      $monthly_revenue[] = (int)$revenue;
    }
    $total = array_sum($monthly_revenue);
  } else if($period == 'last_year') {
    // Năm trước
    $last_year = $current_year - 1;
    for ($i = 1; $i <= 12; $i++) {
      $month_start = "$last_year-$i-01";
      $month_end = date("Y-m-t", strtotime($month_start));
      $labels[] = "T$i";
      
      $revenue = mysqli_fetch_assoc(mysqli_query($con, "
        SELECT COALESCE(SUM(trans_amt), 0) AS total
        FROM booking_order bo
        INNER JOIN rooms r ON bo.room_id = r.id
        WHERE r.owner_id=$owner_id 
        AND (bo.trans_status='TXN_SUCCESS' OR bo.trans_status='paid')
        AND bo.trans_amt > 0
        AND DATE(bo.datentime) BETWEEN '$month_start' AND '$month_end'
      "))['total'];
      
      $monthly_revenue[] = (int)$revenue;
    }
    $total = array_sum($monthly_revenue);
  } else {
    // Tất cả thời gian - chia theo năm
    $years = [];
    $result = mysqli_query($con, "
      SELECT YEAR(bo.datentime) AS year, SUM(bo.trans_amt) AS total
      FROM booking_order bo
      INNER JOIN rooms r ON bo.room_id = r.id
      WHERE r.owner_id=$owner_id 
      AND (bo.trans_status='TXN_SUCCESS' OR bo.trans_status='paid')
      AND bo.trans_amt > 0
      GROUP BY YEAR(bo.datentime)
      ORDER BY year
    ");
    
    while($row = mysqli_fetch_assoc($result)) {
      $labels[] = $row['year'];
      $monthly_revenue[] = (int)$row['total'];
    }
    
    $total = array_sum($monthly_revenue);
  }
  
  echo json_encode([
    'labels' => $labels,
    'revenues' => $monthly_revenue,
    'total' => $total
  ]);
  exit;
}
?>

