<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý Chủ khách sạn - Admin</title>
  <?php require('inc/links.php'); ?>
  <style>
    body {
      background-color: #f8f9fa;
    }
    
    .page-header {
      background: linear-gradient(135deg, #0d6efd 0%, #0ea5e9 100%);
      color: white;
      border-radius: 12px;
      padding: 1.75rem 2rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 4px 16px rgba(13,110,253,0.15);
    }
    
    .page-header h4 {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 0.25rem;
    }
    
    .page-header p {
      font-size: 0.9rem;
      opacity: 0.9;
      margin-bottom: 0;
    }
    
    .filter-card {
      background: white;
      border-radius: 10px;
      padding: 1.25rem;
      margin-bottom: 1.25rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    
    .table-card {
      background: white;
      border-radius: 10px;
      padding: 0;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      overflow: hidden;
    }
    
    .table-card .table {
      margin-bottom: 0;
    }
    
    .table-card thead {
      background-color: #f8f9fa;
      border-bottom: 2px solid #dee2e6;
    }
    
    .table-card thead th {
      font-weight: 600;
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #495057;
      padding: 1rem 0.75rem;
      border-bottom: 2px solid #dee2e6;
    }
    
    .table-card tbody td {
      padding: 1rem 0.75rem;
      vertical-align: middle;
      font-size: 0.9rem;
    }
    
    .table-card tbody tr {
      transition: background-color 0.2s ease;
    }
    
    .table-card tbody tr:hover {
      background-color: #f8f9fa;
    }
    
    .btn-action {
      padding: 0.375rem 0.75rem;
      font-size: 0.875rem;
      border-radius: 6px;
    }
    
    .empty-state {
      padding: 3rem 1rem;
      text-align: center;
    }
    
    .empty-state i {
      font-size: 4rem;
      color: #dee2e6;
      margin-bottom: 1rem;
    }
    
    .empty-state p {
      color: #6c757d;
      font-size: 1rem;
      margin-bottom: 0;
    }
    
    .address-cell {
      max-width: 200px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    
    /* Style cho form controls và nút làm mới */
    .filter-card .form-control,
    .filter-card .form-select {
      background-color: #212529;
      border-color: #495057;
      color: #ffffff;
      border-radius: 8px;
    }
    
    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
      background-color: #212529;
      border-color: #0d6efd;
      color: #ffffff;
      box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .filter-card .form-select option {
      background-color: #212529;
      color: #ffffff;
    }
    
    .filter-card .btn-outline-secondary {
      background-color: #212529;
      border-color: #495057;
      color: #ffffff;
      border-radius: 8px;
    }
    
    .filter-card .btn-outline-secondary:hover {
      background-color: #495057;
      border-color: #495057;
      color: #ffffff;
    }
    
    @media (max-width: 768px) {
      .page-header {
        padding: 1.25rem 1.5rem;
      }
      
      .page-header h4 {
        font-size: 1.25rem;
      }
    }
  </style>
</head>
<body class="bg-light">
  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4">
        
        <!-- Page Header -->
        <div class="page-header">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h4 class="mb-1">
                <i class="bi bi-people me-2"></i>Quản lý Chủ khách sạn
              </h4>
              <p class="mb-0">Quản lý tài khoản chủ khách sạn trong hệ thống</p>
            </div>
            <button class="btn btn-light shadow-none" onclick="openAddOwnerModal()">
              <i class="bi bi-plus-circle me-2"></i>Thêm mới
            </button>
          </div>
        </div>

        <!-- Filter & Search -->
        <div class="filter-card">
          <div class="row g-3 align-items-end">
            <div class="col-md-6">
              <label class="form-label small text-muted fw-semibold mb-1">Tìm kiếm</label>
              <input type="text" id="filter_keyword" class="form-control shadow-none" 
                     placeholder="Tìm theo tên, email, tên khách sạn..." 
                     oninput="debounceFetchOwners()">
            </div>
            <div class="col-md-4">
              <label class="form-label small text-muted fw-semibold mb-1">Lọc theo trạng thái</label>
              <select id="filter_status" class="form-select shadow-none" onchange="get_all_owners()">
                <option value="">Tất cả</option>
                <option value="active">Đã kích hoạt</option>
                <option value="inactive">Đã khóa</option>
              </select>
            </div>
            <div class="col-md-2">
              <button class="btn btn-outline-secondary shadow-none w-100" type="button" onclick="clearFilters()">
                <i class="bi bi-arrow-clockwise me-1"></i>Làm mới
              </button>
            </div>
          </div>
        </div>

        <!-- Owners Table -->
        <div class="table-card">
          <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0">
              <thead class="sticky-top">
                <tr>
                  <th scope="col" width="4%" class="text-center">#</th>
                  <th scope="col" width="12%">Tên chủ KS</th>
                  <th scope="col" width="14%">Email</th>
                  <th scope="col" width="10%">Số điện thoại</th>
                  <th scope="col" width="12%">Tên khách sạn</th>
                  <th scope="col" width="15%">Địa chỉ</th>
                  <th scope="col" width="8%" class="text-center">Số phòng</th>
                  <th scope="col" width="10%" class="text-center">Trạng thái</th>
                  <th scope="col" width="10%" class="text-center">Ngày đăng ký</th>
                  <th scope="col" width="9%" class="text-center">Hành động</th>
                </tr>
              </thead>
              <tbody id="owner-data">
                <tr>
                  <td colspan="10" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Đang tải...</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Add/Edit Owner Modal -->
  <div class="modal fade" id="add-edit-owner-modal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fw-semibold" id="add-edit-modal-title">
            <i class="bi bi-person-plus me-2"></i>Thêm Chủ khách sạn
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <form id="owner-form">
            <input type="hidden" id="owner_id" name="owner_id" value="">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="owner_name" name="name" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="owner_email" name="email" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" id="password-label">Mật khẩu <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="owner_password" name="password">
                <small class="text-muted" id="password-hint">Để trống nếu không đổi mật khẩu (khi sửa)</small>
              </div>
              <div class="col-md-6">
                <label class="form-label">Số điện thoại</label>
                <input type="tel" class="form-control" id="owner_phone" name="phone">
              </div>
              <div class="col-md-6">
                <label class="form-label">Tên khách sạn <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="owner_hotel_name" name="hotel_name" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Trạng thái</label>
                <select class="form-select" id="owner_status" name="status">
                  <option value="1">Đã kích hoạt</option>
                  <option value="-1">Đã khóa</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Địa chỉ</label>
                <textarea class="form-control" id="owner_address" name="address" rows="2"></textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-secondary shadow-none" data-bs-dismiss="modal">Hủy</button>
          <button type="button" class="btn btn-primary shadow-none" onclick="saveOwner()">Lưu</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Owner Details Modal -->
  <div class="modal fade" id="view-owner-modal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title fw-semibold">
            <i class="bi bi-person-circle me-2"></i>Chi tiết Chủ khách sạn
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4" id="owner-details-content">
          <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Đang tải...</span>
            </div>
          </div>
        </div>
        <div class="modal-footer border-top">
          <button type="button" class="btn btn-secondary shadow-none" data-bs-dismiss="modal">Đóng</button>
        </div>
      </div>
    </div>
  </div>

  <script src="scripts/owners.js"></script>
  <?php require('inc/scripts.php'); ?>
</body>
</html>
