<?php
require('inc/essentials.php');
require('../admin/inc/db_config.php');
ownerLogin();

$owner_id = getOwnerId();

// Thống kê doanh thu - Năm hiện tại
$monthly_revenue = [];
$current_year = date('Y');
for ($i = 1; $i <= 12; $i++) {
    $month_start = "$current_year-$i-01";
    $month_end = date("Y-m-t", strtotime($month_start));
    
    $revenue = mysqli_fetch_assoc(mysqli_query($con, "
        SELECT COALESCE(SUM(trans_amt), 0) AS total
        FROM booking_order bo
        INNER JOIN rooms r ON bo.room_id = r.id
        WHERE r.owner_id=$owner_id 
        AND (bo.trans_status='TXN_SUCCESS' OR bo.trans_status='paid')
        AND bo.trans_amt > 0
        AND DATE(bo.datentime) BETWEEN '$month_start' AND '$month_end'
    "))['total'];
    
    $monthly_revenue[$i] = $revenue;
}

// Tổng doanh thu
$total_revenue = array_sum($monthly_revenue);

// Top phòng doanh thu cao nhất
$top_rooms = select("
    SELECT r.name, r.id, 
           COALESCE(SUM(bo.trans_amt), 0) AS revenue,
           COUNT(bo.booking_id) AS booking_count
    FROM rooms r
    LEFT JOIN booking_order bo ON r.id = bo.room_id 
        AND (bo.trans_status='TXN_SUCCESS' OR bo.trans_status='paid')
        AND bo.trans_amt > 0
    WHERE r.owner_id=?
    GROUP BY r.id, r.name
    ORDER BY revenue DESC
    LIMIT 10
", [$owner_id], 'i');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doanh thu - Chủ khách sạn</title>
  <?php require('../admin/inc/links.php'); ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Page Header */
    .page-header {
      background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
      color: white;
      border-radius: 16px;
      padding: 2rem 2.5rem;
      margin-bottom: 2rem;
      box-shadow: 0 10px 26px rgba(0, 0, 0, 0.05);
      position: relative;
      overflow: hidden;
    }
    
    .page-header h4 {
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      text-shadow: 0 2px 4px rgba(0,0,0,0.1);
      color: white;
    }
    
    .page-header p {
      font-size: 1rem;
      opacity: 0.95;
      margin-bottom: 0;
      color: rgba(255, 255, 255, 0.9);
    }
    
    .page-header .form-select {
      border: 1px solid rgba(255, 255, 255, 0.3);
      background: rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 8px;
    }
    
    .page-header .form-select:focus {
      border-color: rgba(255, 255, 255, 0.5);
      background: rgba(255, 255, 255, 0.3);
      color: white;
      box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
    }
    
    .page-header .form-select option {
      background: #0f172a;
      color: white;
    }
    
    /* Cards */
    .card {
      border: 1px solid #e5e7eb;
      border-radius: 15px;
      box-shadow: 0 10px 26px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
    }
    
    .card:hover {
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
      transform: translateY(-2px);
    }
    
    .card-title {
      color: #0f172a;
      font-weight: 600;
    }
    
    /* Total Revenue Card */
    .bg-gradient {
      background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%) !important;
    }
    
    /* List Group */
    .list-group-item {
      color: #0f172a;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .list-group-item:last-child {
      border-bottom: none;
    }
    
    /* Badges */
    .badge {
      padding: 6px 12px;
      border-radius: 8px;
      font-weight: 500;
    }
    
    /* Text Colors */
    .text-muted {
      color: #6c757d !important;
    }
    
    .text-success {
      color: #20c997 !important;
    }
  </style>
</head>
<body class="bg-light">
  <div class="container-fluid p-0">
    <div class="row g-0">
      <?php require('inc/header.php'); ?>

      <div class="col-lg-10 p-4" id="main-content">
        
        <!-- Page Header -->
        <div class="page-header mb-4">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
              <h4 class="mb-2">
                <i class="bi bi-currency-dollar me-2"></i>Doanh thu
              </h4>
              <p class="mb-0 opacity-90">Theo dõi doanh thu và thống kê tài chính</p>
            </div>
            <select class="form-select shadow-none w-auto" id="revenue_period" onchange="loadRevenue()">
              <option value="year">Năm hiện tại</option>
              <option value="last_year">Năm trước</option>
              <option value="all">Tất cả</option>
            </select>
          </div>
        </div>

        <!-- Total Revenue Card -->
        <div class="row g-4 mb-4">
          <div class="col-md-12">
            <div class="card bg-gradient">
              <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <p class="mb-1 opacity-75">Tổng doanh thu năm <?php echo $current_year; ?></p>
                    <h2 class="mb-0 fw-bold"><?php echo number_format($total_revenue, 0, ',', '.'); ?> đ</h2>
                  </div>
                  <div class="fs-1 opacity-50">
                    <i class="bi bi-graph-up-arrow"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Chart -->
        <div class="row g-4 mb-4">
          <div class="col-md-8">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title mb-4">Doanh thu theo tháng</h5>
                <canvas id="revenueChart" height="100"></canvas>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title mb-4">Top phòng doanh thu</h5>
                <div class="list-group list-group-flush">
                  <?php 
                  if ($top_rooms && mysqli_num_rows($top_rooms) > 0):
                    $rank = 1;
                    while ($room = mysqli_fetch_assoc($top_rooms)):
                  ?>
                    <div class="list-group-item border-0 px-0">
                      <div class="d-flex justify-content-between align-items-start">
                        <div>
                          <span class="badge bg-primary me-2">#<?php echo $rank++; ?></span>
                          <strong><?php echo htmlspecialchars($room['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                          <br>
                          <small class="text-muted"><?php echo $room['booking_count']; ?> đơn</small>
                        </div>
                        <div class="text-end">
                          <strong class="text-success"><?php echo number_format($room['revenue'], 0, ',', '.'); ?> đ</strong>
                        </div>
                      </div>
                    </div>
                  <?php 
                    endwhile;
                  else:
                  ?>
                    <p class="text-muted text-center">Chưa có dữ liệu</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script>
  let revenueChart = null;

  function loadRevenue() {
    let period = document.getElementById('revenue_period').value;
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/revenue.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      if(this.status === 200) {
        let data = JSON.parse(this.responseText);
        updateChart(data);
        updateTotal(data.total);
      }
    };
    xhr.send('get_revenue=1&period=' + period);
  }

  function updateChart(data) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    if(revenueChart) {
      revenueChart.destroy();
    }
    revenueChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: data.labels || ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
        datasets: [{
          label: 'Doanh thu (VNĐ)',
          data: data.revenues || [<?php echo implode(',', $monthly_revenue); ?>],
          borderColor: 'rgb(13, 110, 253)',
          backgroundColor: 'rgba(13, 110, 253, 0.1)',
          tension: 0.4,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
              }
            }
          }
        }
      }
    });
  }

  function updateTotal(total) {
    document.querySelector('.card-body.text-white h2').textContent = new Intl.NumberFormat('vi-VN').format(total) + ' đ';
  }

  // Load initial chart
  const ctx = document.getElementById('revenueChart').getContext('2d');
  revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
      datasets: [{
        label: 'Doanh thu (VNĐ)',
        data: [<?php echo implode(',', $monthly_revenue); ?>],
        borderColor: 'rgb(102, 126, 234)',
        backgroundColor: 'rgba(102, 126, 234, 0.1)',
        tension: 0.4,
        fill: true
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
            }
          }
        }
      }
    }
  });
  </script>

  <?php require('../admin/inc/scripts.php'); ?>
</body>
</html>

