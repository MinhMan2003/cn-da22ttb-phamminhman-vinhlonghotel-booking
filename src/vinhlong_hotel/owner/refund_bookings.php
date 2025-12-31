<?php
require('inc/essentials.php');
require('../admin/inc/db_config.php');
ownerLogin();

$owner_id = getOwnerId();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hoàn tiền - Chủ khách sạn</title>
  <?php require('../admin/inc/links.php'); ?>
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
    
    /* Table */
    .table thead {
      background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
      color: white;
    }
    
    .table thead th {
      border: none;
      padding: 12px 16px;
      font-weight: 600;
      color: white;
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
    
    /* Form Controls */
    .form-control {
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      color: #0f172a;
    }
    
    .form-control:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    /* Buttons */
    .btn-success {
      background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
      border: none;
      transition: all 0.3s ease;
    }
    
    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }
    
    /* Badges */
    .badge {
      padding: 6px 12px;
      border-radius: 8px;
      font-weight: 500;
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
          <div>
            <h4 class="mb-2">
              <i class="bi bi-arrow-counterclockwise me-2"></i>Hoàn tiền
            </h4>
            <p class="mb-0 opacity-90">Quản lý yêu cầu hoàn tiền cho các đơn đã hủy</p>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <!-- SEARCH -->
            <div class="text-end mb-4">
              <input type="text" id="filter_keyword" class="form-control shadow-none w-25 ms-auto" 
                     placeholder="Nhập để tìm kiếm..." oninput="fetchRefunds()">
            </div>

            <!-- TABLE -->
            <div class="table-responsive">
              <table class="table table-hover" style="min-width:1200px;">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Chi tiết người dùng</th>
                    <th>Chi tiết phòng</th>
                    <th>Chi tiết đặt chỗ</th>
                    <th>Số tiền hoàn lại</th>
                    <th>Hoạt động</th>
                  </tr>
                </thead>
                <tbody id="refund-data"></tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script>
  function fetchRefunds() {
    let keyword = document.getElementById('filter_keyword').value || '';
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/refund_bookings.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      if(this.status === 200) {
        document.getElementById('refund-data').innerHTML = this.responseText;
      } else {
        console.error('Error:', this.status, this.responseText);
        document.getElementById('refund-data').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Có lỗi xảy ra khi tải dữ liệu!</td></tr>';
      }
    };
    xhr.onerror = function() {
      console.error('Network error');
      document.getElementById('refund-data').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Lỗi kết nối!</td></tr>';
    };
    xhr.send('get_bookings=1&search=' + encodeURIComponent(keyword));
  }

  // Hàm hoàn tiền
  function refund_booking(id) {
    if(confirm("Bạn có chắc chắn muốn hoàn tiền cho đơn đặt phòng này không?")) {
      let data = new FormData();
      data.append('booking_id', id);
      data.append('refund_booking', '');

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/refund_bookings.php", true);

      xhr.onload = function() {
        if(this.responseText == 1 || this.responseText == "1"){
          showToast('Đã hoàn tiền thành công!', 'success');
          fetchRefunds();
        } else {
          alert('Có lỗi xảy ra hoặc đơn này đã được hoàn tiền!');
        }
      };

      xhr.send(data);
    }
  }

  // Hàm hiển thị toast notification
  function showToast(message, type) {
    let toast = document.createElement('div');
    toast.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show position-fixed';
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    document.body.appendChild(toast);
    
    setTimeout(function() {
      toast.remove();
    }, 3000);
  }
  
  window.onload = fetchRefunds;
  </script>

  <?php require('../admin/inc/scripts.php'); ?>
</body>
</html>

