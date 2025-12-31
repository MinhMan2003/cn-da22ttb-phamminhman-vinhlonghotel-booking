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
  <title>Đặt phòng - Chủ khách sạn</title>
  <?php require('../admin/inc/links.php'); ?>
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
    
    /* Modal */
    .modal-content {
      border-radius: 16px;
      border: 1px solid #e5e7eb;
      box-shadow: 0 10px 26px rgba(0, 0, 0, 0.05);
    }
    
    .modal-header {
      background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
      color: white;
      border-bottom: none;
      border-radius: 16px 16px 0 0;
    }
    
    .modal-header .btn-close {
      filter: invert(1);
    }
    
    .modal-header h5 {
      color: white;
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
    
    /* Text Colors */
    .text-secondary {
      color: rgba(255, 255, 255, 0.9) !important;
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
            <p class="text-uppercase small mb-1" style="opacity: 0.9;">Quản lý đặt phòng</p>
            <h3 class="mb-0 fw-bold">Đặt phòng của khách sạn</h3>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <!-- SEARCH -->
            <div class="text-end mb-4">
              <input type="text" id="filter_keyword" class="form-control shadow-none w-25 ms-auto" 
                     placeholder="Nhập để tìm kiếm..." oninput="fetchBookings()">
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
                    <th>Số phòng</th>
                    <th>Hoạt động</th>
                  </tr>
                </thead>
                <tbody id="booking-data"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Giao phòng -->
  <div class="modal fade" id="assign-room" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
      <form id="assign_room_form">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Giao phòng</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <label class="form-label fw-bold">Số phòng</label>
            <input type="text" name="room_no" class="form-control shadow-none mb-3" required placeholder="VD: 101, 201...">
            <div class="alert alert-info mb-0">
              <i class="bi bi-info-circle me-2"></i>
              <small>Chỉ gán số phòng khi khách đã đến!</small>
            </div>
            <input type="hidden" name="booking_id">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-success">Giao phòng</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script>
  function fetchBookings() {
    let keyword = document.getElementById('filter_keyword').value || '';
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/bookings.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      if(this.status === 200) {
        document.getElementById('booking-data').innerHTML = this.responseText;
      } else {
        console.error('Error:', this.status, this.responseText);
        document.getElementById('booking-data').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Có lỗi xảy ra khi tải dữ liệu!</td></tr>';
      }
    };
    xhr.onerror = function() {
      console.error('Network error');
      document.getElementById('booking-data').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Lỗi kết nối!</td></tr>';
    };
    xhr.send('get_bookings=1&search=' + encodeURIComponent(keyword));
  }

  // Hàm giao phòng qua modal
  function assign_room(id) {
    document.getElementById('assign_room_form').elements['booking_id'].value = id;
  }

  // Hàm giao phòng trực tiếp từ input trong bảng
  function assignRoomDirect(booking_id) {
    let roomNoInput = document.getElementById('room_no_' + booking_id);
    let room_no = roomNoInput.value.trim();
    
    if (!room_no) {
      alert('Vui lòng nhập số phòng!');
      roomNoInput.focus();
      return;
    }

    let data = new FormData();
    data.append('room_no', room_no);
    data.append('booking_id', booking_id);
    data.append('assign_room', '');

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/bookings.php", true);

    xhr.onload = function() {
      if(this.responseText == 1){
        // Thông báo thành công
        let successBadge = document.createElement('span');
        successBadge.className = 'badge bg-success';
        successBadge.innerHTML = '<i class="bi bi-door-open me-1"></i>' + room_no;
        
        let editBtn = document.createElement('button');
        editBtn.type = 'button';
        editBtn.className = 'btn btn-outline-secondary btn-sm shadow-none';
        editBtn.title = 'Sửa số phòng';
        editBtn.onclick = function() { editRoomNo(booking_id, room_no); };
        editBtn.innerHTML = '<i class="bi bi-pencil"></i>';
        
        let container = document.createElement('div');
        container.className = 'd-flex align-items-center gap-2';
        container.appendChild(successBadge);
        container.appendChild(editBtn);
        
        roomNoInput.parentElement.replaceWith(container);
        
        // Thông báo
        showToast('Đã giao phòng thành công!', 'success');
      } else {
        alert('Có lỗi xảy ra! Vui lòng thử lại.');
      }
    };

    xhr.send(data);
  }

  // Hàm sửa số phòng đã giao
  function editRoomNo(booking_id, current_room_no) {
    let newRoomNo = prompt('Nhập số phòng mới:', current_room_no);
    if (newRoomNo === null || newRoomNo.trim() === '') {
      return;
    }

    let data = new FormData();
    data.append('room_no', newRoomNo.trim());
    data.append('booking_id', booking_id);
    data.append('assign_room', '');

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/bookings.php", true);

    xhr.onload = function() {
      if(this.responseText == 1){
        fetchBookings();
        showToast('Đã cập nhật số phòng!', 'success');
      } else {
        alert('Có lỗi xảy ra!');
      }
    };

    xhr.send(data);
  }

  // Hàm hủy đặt phòng
  function cancel_booking(id) {
    if(confirm("Bạn có chắc chắn muốn hủy đặt phòng này không?")) {
      let data = new FormData();
      data.append('booking_id', id);
      data.append('cancel_booking', '');

      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/bookings.php", true);

      xhr.onload = function() {
        if(this.responseText == 1){
          showToast('Đặt chỗ đã bị hủy!', 'success');
          fetchBookings();
        } else {
          alert('Có lỗi xảy ra!');
        }
      };

      xhr.send(data);
    }
  }

  // Hàm in hóa đơn
  function printInvoice(booking_id) {
    window.open('invoice.php?id=' + booking_id, '_blank');
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

  // Xử lý form giao phòng qua modal
  document.getElementById('assign_room_form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let data = new FormData();
    data.append('room_no', this.elements['room_no'].value);
    data.append('booking_id', this.elements['booking_id'].value);
    data.append('assign_room', '');

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/bookings.php", true);

    xhr.onload = function() {
      var myModal = document.getElementById('assign-room');
      var modal = bootstrap.Modal.getInstance(myModal);
      modal.hide();

      if(this.responseText == 1){
        showToast('Đã giao phòng thành công!', 'success');
        document.getElementById('assign_room_form').reset();
        fetchBookings();
      } else {
        alert('Có lỗi xảy ra! Vui lòng thử lại.');
      }
    };

    xhr.send(data);
  });
  
  window.onload = fetchBookings;
  </script>

  <?php require('../admin/inc/scripts.php'); ?>
</body>
</html>

