<?php
require('inc/essentials.php');
require('../admin/inc/db_config.php');
ownerLogin();

$owner_id = getOwnerId();

// Thống kê cho owner
$stats = [
    'total_rooms' => mysqli_fetch_assoc(mysqli_query($con, "
        SELECT COUNT(*) AS c FROM rooms 
        WHERE owner_id=$owner_id AND removed=0
    "))['c'],
    
    'active_rooms' => mysqli_fetch_assoc(mysqli_query($con, "
        SELECT COUNT(*) AS c FROM rooms 
        WHERE owner_id=$owner_id AND status=1 AND removed=0
    "))['c'],
    
    'total_bookings' => mysqli_fetch_assoc(mysqli_query($con, "
        SELECT COUNT(*) AS c FROM booking_order bo
        INNER JOIN rooms r ON bo.room_id = r.id
        WHERE r.owner_id=$owner_id
    "))['c'],
    
    'pending_bookings' => mysqli_fetch_assoc(mysqli_query($con, "
        SELECT COUNT(*) AS c FROM booking_order bo
        INNER JOIN rooms r ON bo.room_id = r.id
        WHERE r.owner_id=$owner_id AND bo.booking_status='pending'
    "))['c'],
    
    'total_revenue' => mysqli_fetch_assoc(mysqli_query($con, "
        SELECT COALESCE(SUM(trans_amt), 0) AS c FROM booking_order bo
        INNER JOIN rooms r ON bo.room_id = r.id
        WHERE r.owner_id=$owner_id 
        AND (bo.trans_status='TXN_SUCCESS' OR bo.trans_status='paid')
        AND bo.trans_amt > 0
    "))['c'],
];

// Booking gần đây
$recent_bookings = select("
    SELECT bo.*, r.name AS room_name, bd.user_name, bd.phonenum
    FROM booking_order bo
    INNER JOIN rooms r ON bo.room_id = r.id
    LEFT JOIN booking_details bd ON bo.booking_id = bd.booking_id
    WHERE r.owner_id=?
    ORDER BY bo.booking_id DESC
    LIMIT 10
", [$owner_id], 'i');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Chủ khách sạn</title>
  <?php require('../admin/inc/links.php'); ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Page Header */
    .page-header-section {
      background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
      color: white;
      padding: 20px 24px;
      border-radius: 15px;
      margin-bottom: 24px;
      box-shadow: 0 10px 26px rgba(0, 0, 0, 0.05);
    }
    
    .page-header-section h3 {
      color: white;
      margin: 0;
    }
    
    .page-header-section p {
      color: rgba(255, 255, 255, 0.9);
      margin: 0;
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
    
    .card-header {
      background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
      color: white;
      border-bottom: none;
      padding: 15px 20px;
      border-radius: 15px 15px 0 0;
    }
    
    .card-title {
      color: #0f172a;
      font-weight: 600;
    }
    
    /* Stats Cards */
    .stat-card {
      border: 1px solid #e5e7eb;
      border-radius: 15px;
      box-shadow: 0 10px 26px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      background: #ffffff;
    }
    
    .stat-card:hover {
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
      transform: translateY(-2px);
    }
    
    .stat-card .text-primary {
      color: #0d6efd !important;
    }
    
    .stat-card .text-success {
      color: #20c997 !important;
    }
    
    .stat-card .text-info {
      color: #0ea5e9 !important;
    }
    
    /* Analytics Cards */
    .card.bg-primary {
      background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%) !important;
      border: none;
    }
    
    .card.bg-success {
      background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%) !important;
      border: none;
    }
    
    .card.bg-danger {
      background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%) !important;
      border: none;
    }
    
    /* Table */
    .table thead {
      background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
      color: white;
    }
    
    .table thead th {
      border: none;
      padding: 12px 16px;
      font-weight: 600;
    }
    
    .table tbody tr {
      border-bottom: 1px solid #e5e7eb;
    }
    
    .table tbody tr:hover {
      background: #f8f9fa;
    }
    
    .table tbody td {
      color: #0f172a;
      padding: 12px 16px;
    }
    
    /* Badges */
    .badge {
      padding: 6px 12px;
      border-radius: 8px;
      font-weight: 500;
    }
    
    /* Form Select */
    .form-select {
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      color: #0f172a;
    }
    
    .form-select:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    /* Form Select in Card Header */
    .card-header .form-select {
      background: rgba(255, 255, 255, 0.95);
      color: #0f172a;
      border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .card-header .form-select:focus {
      background: white;
      border-color: rgba(255, 255, 255, 0.5);
      box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
    }
    
    .card-header .form-select option {
      color: #0f172a;
      background: white;
    }
    
    /* Chart Colors */
    .chart-container {
      padding: 20px;
    }
    
    /* Text Colors */
    .text-muted {
      color: #6c757d !important;
    }
    
    .text-secondary {
      color: #0f172a !important;
    }
  </style>
</head>
<body class="bg-light">
  <div class="container-fluid p-0">
    <div class="row g-0">
      <?php require('inc/header.php'); ?>

      <div class="col-lg-10 p-4" id="main-content">
        <div class="page-header-section">
          <div>
            <p class="text-uppercase small mb-1" style="opacity: 0.9;">Dashboard</p>
            <h3 class="mb-0 fw-bold">Xin chào, <?php echo htmlspecialchars($_SESSION['ownerName'] ?? 'Chủ khách sạn', ENT_QUOTES, 'UTF-8'); ?>!</h3>
            <p class="mb-0" style="opacity: 0.9;"><?php echo htmlspecialchars($_SESSION['ownerHotelName'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
          <div class="col-md-3">
            <div class="stat-card">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <p class="text-muted small mb-1">Tổng số phòng</p>
                    <h3 class="mb-0" style="color: #0f172a;"><?php echo $stats['total_rooms']; ?></h3>
                  </div>
                  <div class="text-primary fs-1">
                    <i class="bi bi-door-open"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="stat-card">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <p class="text-muted small mb-1">Phòng đang hoạt động</p>
                    <h3 class="mb-0" style="color: #0f172a;"><?php echo $stats['active_rooms']; ?></h3>
                  </div>
                  <div class="text-success fs-1">
                    <i class="bi bi-check-circle"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="stat-card">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <p class="text-muted small mb-1">Tổng đặt phòng</p>
                    <h3 class="mb-0" style="color: #0f172a;"><?php echo $stats['total_bookings']; ?></h3>
                  </div>
                  <div class="text-info fs-1">
                    <i class="bi bi-calendar-check"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="stat-card">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <p class="text-muted small mb-1">Doanh thu</p>
                    <h3 class="mb-0" style="color: #0f172a;"><?php echo number_format($stats['total_revenue'], 0, ',', '.'); ?> đ</h3>
                  </div>
                  <div class="text-success fs-1">
                    <i class="bi bi-currency-dollar"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Booking Analytics -->
        <div class="card mb-4">
          <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-0" style="color: white;">Phân tích đặt phòng</h5>
              <select class="form-select shadow-none w-auto" id="analytics_period" onchange="loadAnalytics()">
                <option value="1">30 ngày gần đây</option>
                <option value="2">90 ngày gần đây</option>
                <option value="3">1 năm gần đây</option>
                <option value="4">Tất cả thời gian</option>
              </select>
            </div>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-4">
                <div class="card bg-primary text-white">
                  <div class="card-body">
                    <h6 class="mb-2">Tổng số đặt chỗ</h6>
                    <h2 id="analytics_total_bookings">0</h2>
                    <small id="analytics_total_amt">0 đ</small>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card bg-success text-white">
                  <div class="card-body">
                    <h6 class="mb-2">Đặt chỗ thành công</h6>
                    <h2 id="analytics_active_bookings">0</h2>
                    <small id="analytics_active_amt">0 đ</small>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card bg-danger text-white">
                  <div class="card-body">
                    <h6 class="mb-2">Đặt chỗ đã hủy</h6>
                    <h2 id="analytics_cancelled_bookings">0</h2>
                    <small id="analytics_cancelled_amt">0 đ</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Revenue Chart -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0" style="color: white;">Doanh thu 7 ngày gần đây</h5>
          </div>
          <div class="card-body chart-container">
            <canvas id="revenueChart" height="80"></canvas>
          </div>
        </div>

        <!-- Recent Bookings -->
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0" style="color: white;">Đặt phòng gần đây</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Mã đơn</th>
                    <th>Phòng</th>
                    <th>Khách hàng</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Trạng thái</th>
                    <th>Tổng tiền</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($recent_bookings && mysqli_num_rows($recent_bookings) > 0): ?>
                    <?php while ($booking = mysqli_fetch_assoc($recent_bookings)): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($booking['order_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($booking['room_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($booking['user_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($booking['check_in'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($booking['check_out'])); ?></td>
                        <td>
                          <?php
                          $status_class = 'bg-secondary';
                          if ($booking['booking_status'] == 'booked') $status_class = 'bg-success';
                          elseif ($booking['booking_status'] == 'pending') $status_class = 'bg-warning';
                          elseif ($booking['booking_status'] == 'cancelled') $status_class = 'bg-danger';
                          ?>
                          <span class="badge <?php echo $status_class; ?>">
                            <?php echo htmlspecialchars($booking['booking_status'], ENT_QUOTES, 'UTF-8'); ?>
                          </span>
                        </td>
                        <td><?php echo number_format($booking['trans_amt'] ?? 0, 0, ',', '.'); ?> đ</td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="7" class="text-center text-muted">Chưa có đặt phòng nào</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
  // Load analytics
  function loadAnalytics() {
    let period = document.getElementById('analytics_period').value;
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/dashboard.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      if(this.status === 200) {
        let data = JSON.parse(this.responseText);
        document.getElementById('analytics_total_bookings').textContent = data.total_bookings || 0;
        document.getElementById('analytics_total_amt').textContent = (data.total_amt || '0') + ' đ';
        document.getElementById('analytics_active_bookings').textContent = data.active_bookings || 0;
        document.getElementById('analytics_active_amt').textContent = (data.active_amt || '0') + ' đ';
        document.getElementById('analytics_cancelled_bookings').textContent = data.cancelled_bookings || 0;
        document.getElementById('analytics_cancelled_amt').textContent = (data.cancelled_amt || '0') + ' đ';
      }
    };
    xhr.send('booking_analytics=1&period=' + period);
  }

  // Load revenue chart
  function loadRevenueChart() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/dashboard.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      if(this.status === 200) {
        let data = JSON.parse(this.responseText);
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
          type: 'line',
          data: {
            labels: data.labels || [],
            datasets: [{
              label: 'Doanh thu (VNĐ)',
              data: data.revenues || [],
              borderColor: '#0d6efd',
              backgroundColor: 'rgba(13, 110, 253, 0.1)',
              tension: 0.4,
              fill: true
            }]
          },
          options: {
            responsive: true,
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
    };
    xhr.send('revenue_chart=1');
  }

  window.onload = function() {
    loadAnalytics();
    loadRevenueChart();
  };
  </script>

  <?php require('../admin/inc/scripts.php'); ?>
</body>
</html>

