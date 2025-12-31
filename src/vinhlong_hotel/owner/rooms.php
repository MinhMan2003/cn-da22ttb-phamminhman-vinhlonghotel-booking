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
  <title>Quản lý phòng - Chủ khách sạn</title>
  <?php require('../admin/inc/links.php'); ?>
  <style>
    :root {
      --primary-color: #0d6efd;
      --success-color: #198754;
      --warning-color: #ffc107;
      --danger-color: #dc3545;
      --info-color: #0dcaf0;
      --light-bg: #f8f9fa;
      --card-shadow: 0 2px 12px rgba(0,0,0,0.08);
      --card-shadow-hover: 0 4px 20px rgba(0,0,0,0.12);
    }

    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
    }
    
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

    .page-header::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -10%;
      width: 300px;
      height: 300px;
      background: rgba(255,255,255,0.1);
      border-radius: 50%;
    }

    .page-header::after {
      content: '';
      position: absolute;
      bottom: -30%;
      left: -5%;
      width: 200px;
      height: 200px;
      background: rgba(255,255,255,0.05);
      border-radius: 50%;
    }

    .page-header > * {
      position: relative;
      z-index: 1;
    }
    
    .page-header h4 {
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .page-header p {
      font-size: 1rem;
      opacity: 0.95;
      margin-bottom: 0;
    }

    .page-header .btn {
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      transition: all 0.3s ease;
    }

    .page-header .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.2);
    }
    
    .stat-card {
      background: white;
      border-radius: 16px;
      padding: 1.5rem;
      box-shadow: 0 10px 26px rgba(0, 0, 0, 0.05);
      border: 1px solid #e5e7eb;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      border-left: 5px solid transparent;
      height: 100%;
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 100px;
      height: 100px;
      background: linear-gradient(135deg, rgba(0,0,0,0.02) 0%, transparent 100%);
      border-radius: 0 0 0 100%;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
    }
    
    .stat-card.active {
      border-left-color: #0d6efd;
    }
    
    .stat-card.inactive {
      border-left-color: #ffc107;
    }
    
    .stat-card.total {
      border-left-color: #20c997;
    }
    
    .stat-card .stat-icon {
      width: 56px;
      height: 56px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      margin-bottom: 1rem;
      transition: all 0.3s ease;
    }

    .stat-card:hover .stat-icon {
      transform: scale(1.1) rotate(5deg);
    }
    
    .stat-card.active .stat-icon {
      background: linear-gradient(135deg, rgba(13,110,253,0.15) 0%, rgba(13,110,253,0.05) 100%);
      color: #0d6efd;
    }
    
    .stat-card.inactive .stat-icon {
      background: linear-gradient(135deg, rgba(255,193,7,0.15) 0%, rgba(255,193,7,0.05) 100%);
      color: #ffc107;
    }
    
    .stat-card.total .stat-icon {
      background: linear-gradient(135deg, rgba(32,201,151,0.15) 0%, rgba(32,201,151,0.05) 100%);
      color: #20c997;
    }
    
    .stat-card .stat-label {
      font-size: 0.875rem;
      color: #6c757d;
      margin-bottom: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .stat-card .stat-value {
      font-size: 2.25rem;
      font-weight: 800;
      color: #0f172a;
      margin-bottom: 0;
      line-height: 1;
    }
    
    .filter-card {
      background: white;
      border-radius: 16px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 10px 26px rgba(0, 0, 0, 0.05);
      border: 1px solid #e5e7eb;
    }

    .filter-card .form-label {
      font-weight: 600;
      color: #0f172a;
      margin-bottom: 0.5rem;
    }
    
    .table-card {
      background: white;
      border-radius: 16px;
      padding: 0;
      box-shadow: 0 10px 26px rgba(0, 0, 0, 0.05);
      overflow: hidden;
      border: 1px solid #e5e7eb;
    }
    
    .table-card .table {
      margin-bottom: 0;
    }
    
    .table-card thead {
      background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
      border-bottom: none;
    }
    
    .table-card thead th {
      font-weight: 700;
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: white;
      padding: 1.25rem 1rem;
      border-bottom: none;
      white-space: nowrap;
    }
    
    .table-card tbody td {
      padding: 1.25rem 1rem;
      vertical-align: middle;
      font-size: 0.9rem;
      border-bottom: 1px solid #e5e7eb;
      color: #0f172a;
    }
    
    .table-card tbody tr {
      transition: all 0.2s ease;
    }
    
    .table-card tbody tr:hover {
      background: #f8f9fa;
      transform: scale(1.01);
    }

    .table-card tbody tr:last-child td {
      border-bottom: none;
    }
    
    .btn-action {
      padding: 0.5rem 0.75rem;
      font-size: 0.875rem;
      border-radius: 8px;
      transition: all 0.2s ease;
      border: none;
    }

    .btn-action:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .empty-state {
      padding: 4rem 2rem;
      text-align: center;
    }
    
    .empty-state i {
      font-size: 5rem;
      color: #0f172a;
      margin-bottom: 1.5rem;
      opacity: 0.3;
    }
    
    .empty-state p {
      color: #0f172a;
      font-size: 1.1rem;
      margin-bottom: 0;
      font-weight: 500;
    }

    .badge {
      padding: 0.5rem 0.75rem;
      font-weight: 600;
      border-radius: 8px;
      font-size: 0.75rem;
    }

    .modal-content {
      border-radius: 16px;
      border: 1px solid #e5e7eb;
      box-shadow: 0 10px 26px rgba(0, 0, 0, 0.05);
    }

    .modal-header {
      border-radius: 16px 16px 0 0;
      padding: 1.5rem;
      background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
      color: white;
      border-bottom: none;
    }
    
    .modal-header .btn-close {
      filter: invert(1);
    }
    
    .modal-header h5 {
      color: white;
    }

    .modal-body {
      padding: 1.5rem;
    }

    .form-control:focus, .form-select:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.25);
    }

    .spinner-border {
      width: 3rem;
      height: 3rem;
    }

    @media (max-width: 768px) {
      .page-header {
        padding: 1.5rem 1.5rem;
      }
      
      .page-header h4 {
        font-size: 1.5rem;
      }

      .stat-card {
        margin-bottom: 1rem;
      }

      .table-card {
        overflow-x: auto;
      }
    }

    /* Custom scrollbar */
    .table-responsive::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
      background: #555;
    }
  </style>
</head>
<body class="bg-light">
  <div class="container-fluid p-0">
    <div class="row g-0">
      <?php require('inc/header.php'); ?>

      <div class="col-lg-10 p-4" id="main-content">
        
        <!-- Page Header -->
        <div class="page-header">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
              <h4 class="mb-2">
                <i class="bi bi-door-open me-2"></i>Quản lý phòng
              </h4>
              <p class="mb-0 opacity-90">Quản lý và cập nhật thông tin phòng của khách sạn bạn</p>
            </div>
            <button type="button" class="btn btn-light shadow-none fw-bold px-4 py-2" data-bs-toggle="modal" data-bs-target="#add-room">
              <i class="bi bi-plus-circle-fill me-2"></i>Thêm phòng mới
            </button>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
          <div class="col-md-4">
            <div class="stat-card active">
              <div class="stat-icon">
                <i class="bi bi-check-circle-fill"></i>
              </div>
              <div class="stat-label">Phòng đang hoạt động</div>
              <div class="stat-value" id="stat-active">0</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="stat-card inactive">
              <div class="stat-icon">
                <i class="bi bi-pause-circle-fill"></i>
              </div>
              <div class="stat-label">Phòng tạm ngưng</div>
              <div class="stat-value" id="stat-inactive">0</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="stat-card total">
              <div class="stat-icon">
                <i class="bi bi-building-fill"></i>
              </div>
              <div class="stat-label">Tổng số phòng</div>
              <div class="stat-value" id="stat-total">0</div>
            </div>
          </div>
        </div>

        <!-- Filter & Search -->
        <div class="filter-card">
          <div class="row g-3 align-items-end">
            <div class="col-md-6">
              <label class="form-label mb-2">
                <i class="bi bi-search me-2 text-primary"></i>Tìm kiếm
              </label>
              <input type="text" id="filter_keyword" class="form-control shadow-none" 
                     placeholder="Tìm theo tên phòng, vị trí, mô tả..." 
                     oninput="debounceFetchRooms()">
            </div>
            <div class="col-md-4">
              <label class="form-label mb-2">
                <i class="bi bi-funnel me-2 text-primary"></i>Lọc theo trạng thái
              </label>
              <select id="filter_status" class="form-select shadow-none" onchange="get_all_rooms()">
                <option value="">Tất cả trạng thái</option>
                <option value="active">Đang hoạt động</option>
                <option value="inactive">Tạm ngưng</option>
                <option value="soldout">Hết phòng</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label mb-2 d-block">&nbsp;</label>
              <button class="btn btn-outline-primary shadow-none w-100 fw-semibold" type="button" onclick="clearFilters()">
                <i class="bi bi-arrow-clockwise me-1"></i>Làm mới
              </button>
            </div>
          </div>
        </div>

        <!-- Room Table -->
        <div class="table-card">
          <div class="table-responsive" style="max-height: 650px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0">
              <thead class="sticky-top">
                <tr>
                  <th scope="col" width="5%" class="text-center">#</th>
                  <th scope="col" width="20%">Tên phòng</th>
                  <th scope="col" width="15%">Vị trí</th>
                  <th scope="col" width="10%" class="text-center">Diện tích</th>
                  <th scope="col" width="12%" class="text-end">Giá</th>
                  <th scope="col" width="8%" class="text-center">Số lượng</th>
                  <th scope="col" width="15%" class="text-center">Trạng thái</th>
                  <th scope="col" width="15%" class="text-center">Hành động</th>
                </tr>
              </thead>
              <tbody id="room-data">
                <tr>
                  <td colspan="8" class="text-center py-5">
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

  <!-- Add Room Modal -->
  <div class="modal fade" id="add-room" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <form id="add_room_form" autocomplete="off">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fw-bold">
              <i class="bi bi-plus-circle-fill me-2"></i>Thêm phòng mới
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-tag me-1 text-primary"></i>Tên phòng <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-geo-alt me-1 text-primary"></i>Vị trí / Khu vực <span class="text-danger">*</span>
                </label>
                <input type="text" name="location" class="form-control shadow-none" placeholder="VD: Long Hồ, TP. Vĩnh Long" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">
                  <i class="bi bi-arrows-angle-expand me-1 text-primary"></i>Diện tích (m²) <span class="text-danger">*</span>
                </label>
                <input type="number" min="1" name="area" class="form-control shadow-none" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">
                  <i class="bi bi-currency-dollar me-1 text-primary"></i>Giá (VND) <span class="text-danger">*</span>
                </label>
                <input type="number" min="1" name="price" class="form-control shadow-none" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">
                  <i class="bi bi-percent me-1 text-primary"></i>Giảm giá (%)
                </label>
                <input type="number" min="0" max="100" name="discount" class="form-control shadow-none" value="0">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">
                  <i class="bi bi-box-seam me-1 text-primary"></i>Số lượng <span class="text-danger">*</span>
                </label>
                <input type="number" min="1" name="quantity" class="form-control shadow-none" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">
                  <i class="bi bi-person me-1 text-primary"></i>Người lớn (tối đa) <span class="text-danger">*</span>
                </label>
                <input type="number" min="1" name="adult" class="form-control shadow-none" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">
                  <i class="bi bi-person-heart me-1 text-primary"></i>Trẻ em (tối đa) <span class="text-danger">*</span>
                </label>
                <input type="number" min="1" name="children" class="form-control shadow-none" required>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">
                  <i class="bi bi-grid-3x3-gap me-1 text-primary"></i>Không gian
                </label>
                <div class="border rounded p-3" style="max-height: 150px; overflow-y: auto; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                  <div class="row g-2">
                    <?php 
                      $res = selectAll('features');
                      if($res && mysqli_num_rows($res) > 0){
                        while($opt = mysqli_fetch_assoc($res)){
                          echo"
                            <div class='col-md-4 col-lg-3'>
                              <div class='form-check'>
                                <input type='checkbox' name='features[]' value='{$opt['id']}' class='form-check-input shadow-none' id='feat_{$opt['id']}'>
                                <label class='form-check-label' for='feat_{$opt['id']}'>{$opt['name']}</label>
                              </div>
                            </div>
                          ";
                        }
                      } else {
                        echo "<div class='col-12 text-muted small'>Chưa có không gian nào</div>";
                      }
                    ?>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">
                  <i class="bi bi-star me-1 text-primary"></i>Tiện ích
                </label>
                <div class="border rounded p-3" style="max-height: 150px; overflow-y: auto; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                  <div class="row g-2">
                    <?php 
                      $res = selectAll('facilities');
                      if($res && mysqli_num_rows($res) > 0){
                        while($opt = mysqli_fetch_assoc($res)){
                          echo"
                            <div class='col-md-4 col-lg-3'>
                              <div class='form-check'>
                                <input type='checkbox' name='facilities[]' value='{$opt['id']}' class='form-check-input shadow-none' id='fac_{$opt['id']}'>
                                <label class='form-check-label' for='fac_{$opt['id']}'>{$opt['name']}</label>
                              </div>
                            </div>
                          ";
                        }
                      } else {
                        echo "<div class='col-12 text-muted small'>Chưa có tiện ích nào</div>";
                      }
                    ?>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">
                  <i class="bi bi-file-text me-1 text-primary"></i>Mô tả <span class="text-danger">*</span>
                </label>
                <textarea name="desc" rows="4" class="form-control shadow-none" placeholder="Mô tả chi tiết về phòng..." required></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer border-top bg-light">
            <button type="reset" class="btn btn-secondary shadow-none px-4" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-primary shadow-none px-4 fw-semibold">
              <i class="bi bi-check-circle-fill me-2"></i>Thêm phòng
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Room Modal -->
  <div class="modal fade" id="edit-room" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <form id="edit_room_form" autocomplete="off">
        <div class="modal-content">
          <div class="modal-header bg-info text-white">
            <h5 class="modal-title fw-bold">
              <i class="bi bi-pencil-square me-2"></i>Cập nhật phòng
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-tag me-1 text-info"></i>Tên phòng <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-geo-alt me-1 text-info"></i>Vị trí / Khu vực <span class="text-danger">*</span>
                </label>
                <input type="text" name="location" class="form-control shadow-none" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">
                  <i class="bi bi-arrows-angle-expand me-1 text-info"></i>Diện tích (m²) <span class="text-danger">*</span>
                </label>
                <input type="number" min="1" name="area" class="form-control shadow-none" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">
                  <i class="bi bi-currency-dollar me-1 text-info"></i>Giá (VND) <span class="text-danger">*</span>
                </label>
                <input type="number" min="1" name="price" class="form-control shadow-none" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">
                  <i class="bi bi-percent me-1 text-info"></i>Giảm giá (%)
                </label>
                <input type="number" min="0" max="100" name="discount" class="form-control shadow-none" value="0">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">
                  <i class="bi bi-box-seam me-1 text-info"></i>Số lượng <span class="text-danger">*</span>
                </label>
                <input type="number" min="1" name="quantity" class="form-control shadow-none" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">
                  <i class="bi bi-box-arrow-in-down me-1 text-info"></i>Phòng còn lại
                </label>
                <div class="input-group">
                  <button type="button" class="btn btn-outline-secondary" onclick="changeRemain(-1)">
                    <i class="bi bi-dash"></i>
                  </button>
                  <input type="number" min="0" name="remaining" class="form-control shadow-none text-center fw-bold" required>
                  <button type="button" class="btn btn-outline-secondary" onclick="changeRemain(1)">
                    <i class="bi bi-plus"></i>
                  </button>
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">
                  <i class="bi bi-person me-1 text-info"></i>Người lớn (tối đa) <span class="text-danger">*</span>
                </label>
                <input type="number" min="1" name="adult" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-person-heart me-1 text-info"></i>Trẻ em (tối đa) <span class="text-danger">*</span>
                </label>
                <input type="number" min="1" name="children" class="form-control shadow-none" required>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">
                  <i class="bi bi-grid-3x3-gap me-1 text-info"></i>Không gian
                </label>
                <div class="border rounded p-3" style="max-height: 150px; overflow-y: auto; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);" id="edit-features-container">
                  <div class="row g-2">
                    <?php 
                      $res = selectAll('features');
                      if($res && mysqli_num_rows($res) > 0){
                        while($opt = mysqli_fetch_assoc($res)){
                          echo"
                            <div class='col-md-4 col-lg-3'>
                              <div class='form-check'>
                                <input type='checkbox' name='features[]' value='{$opt['id']}' class='form-check-input shadow-none' id='edit_feat_{$opt['id']}'>
                                <label class='form-check-label' for='edit_feat_{$opt['id']}'>{$opt['name']}</label>
                              </div>
                            </div>
                          ";
                        }
                      }
                    ?>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">
                  <i class="bi bi-star me-1 text-info"></i>Tiện ích
                </label>
                <div class="border rounded p-3" style="max-height: 150px; overflow-y: auto; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);" id="edit-facilities-container">
                  <div class="row g-2">
                    <?php 
                      $res = selectAll('facilities');
                      if($res && mysqli_num_rows($res) > 0){
                        while($opt = mysqli_fetch_assoc($res)){
                          echo"
                            <div class='col-md-4 col-lg-3'>
                              <div class='form-check'>
                                <input type='checkbox' name='facilities[]' value='{$opt['id']}' class='form-check-input shadow-none' id='edit_fac_{$opt['id']}'>
                                <label class='form-check-label' for='edit_fac_{$opt['id']}'>{$opt['name']}</label>
                              </div>
                            </div>
                          ";
                        }
                      }
                    ?>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">
                  <i class="bi bi-file-text me-1 text-info"></i>Mô tả <span class="text-danger">*</span>
                </label>
                <textarea name="desc" rows="4" class="form-control shadow-none" required></textarea>
              </div>
              <input type="hidden" name="room_id">
            </div>
          </div>
          <div class="modal-footer border-top bg-light">
            <button type="reset" class="btn btn-secondary shadow-none px-4" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-info text-white shadow-none px-4 fw-semibold">
              <i class="bi bi-check-circle-fill me-2"></i>Lưu thay đổi
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Room Images Modal -->
  <div class="modal fade" id="room-images" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title fw-bold" id="room-images-title">
            <i class="bi bi-images me-2"></i>Quản lý ảnh phòng
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div id="image-alert"></div>
          <div class="border-bottom pb-3 mb-3">
            <form id="add_image_form">
              <label class="form-label fw-semibold mb-2">
                <i class="bi bi-cloud-upload me-1 text-info"></i>Thêm hình ảnh (có thể chọn nhiều ảnh)
              </label>
              <div class="d-flex gap-2">
                <input type="file" name="image[]" accept=".jpg, .png, .webp, .jpeg" class="form-control shadow-none" multiple required>
                <button class="btn btn-primary shadow-none fw-semibold">
                  <i class="bi bi-upload me-2"></i>Thêm ảnh
                </button>
              </div>
              <small class="text-muted d-block mt-1">
                <i class="bi bi-info-circle me-1"></i>Bạn có thể chọn nhiều ảnh cùng lúc bằng cách giữ phím Ctrl (Windows) hoặc Cmd (Mac)
              </small>
              <input type="hidden" name="room_id">
            </form>
          </div>
          <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light sticky-top">
                <tr>
                  <th scope="col" width="60%">Hình phòng</th>
                  <th scope="col" width="20%" class="text-center">Ảnh đại diện</th>
                  <th scope="col" width="20%" class="text-center">Xóa</th>
                </tr>
              </thead>
              <tbody id="room-image-data"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
  // Load danh sách phòng
  function get_all_rooms() {
    let keyword = document.getElementById('filter_keyword')?.value || '';
    let status = document.getElementById('filter_status')?.value || '';
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/rooms.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      document.getElementById('room-data').innerHTML = this.responseText;
      updateStats();
    };
    xhr.send('get_all_rooms=1&search=' + encodeURIComponent(keyword) + '&status=' + encodeURIComponent(status));
  }

  // Cập nhật thống kê
  function updateStats() {
    // Đọc stats từ comment trong HTML response
    let roomDataHtml = document.getElementById('room-data').innerHTML;
    let statsMatch = roomDataHtml.match(/<!--STATS:({[^}]+})-->/);
    
    if(statsMatch) {
      try {
        let stats = JSON.parse(statsMatch[1]);
        document.getElementById('stat-active').textContent = stats.active || 0;
        document.getElementById('stat-inactive').textContent = stats.inactive || 0;
        document.getElementById('stat-total').textContent = stats.total || 0;
        return;
      } catch(e) {
        console.error('Error parsing stats:', e);
      }
    }
    
    // Fallback: đếm từ table rows (bỏ qua empty-state)
    let rows = document.querySelectorAll('#room-data tr');
    let active = 0, inactive = 0, total = 0;
    rows.forEach(row => {
      // Bỏ qua row có class empty-state hoặc không có button status
      if(row.classList.contains('empty-state') || row.querySelector('.empty-state')) {
        return;
      }
      
      let statusBtn = row.querySelector('button[onclick*="toggle_status"]');
      if(statusBtn) {
        total++;
        if(statusBtn.textContent.includes('Đang hoạt động')) {
          active++;
        } else {
          inactive++;
        }
      }
    });
    document.getElementById('stat-active').textContent = active;
    document.getElementById('stat-inactive').textContent = inactive;
    document.getElementById('stat-total').textContent = total;
  }

  // Xử lý form thêm phòng
  let add_room_form = document.getElementById('add_room_form');
  if(add_room_form){
    add_room_form.addEventListener('submit', function(e){
      e.preventDefault();
      add_room();
    });
  }

  function add_room() {
    let form = document.getElementById('add_room_form');
    let data = new FormData();
    data.append('add_room', '1');
    data.append('name', form.elements['name'].value);
    data.append('location', form.elements['location'].value);
    data.append('area', form.elements['area'].value);
    data.append('price', form.elements['price'].value);
    data.append('discount', form.elements['discount'].value || '0');
    data.append('quantity', form.elements['quantity'].value);
    data.append('adult', form.elements['adult'].value);
    data.append('children', form.elements['children'].value);
    data.append('desc', form.elements['desc'].value);

    // Features
    let features = [];
    form.querySelectorAll('input[name="features[]"]:checked').forEach(cb => features.push(cb.value));
    data.append('features', JSON.stringify(features));

    // Facilities
    let facilities = [];
    form.querySelectorAll('input[name="facilities[]"]:checked').forEach(cb => facilities.push(cb.value));
    data.append('facilities', JSON.stringify(facilities));

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/rooms.php", true);

    xhr.onload = function(){
      let myModal = document.getElementById('add-room');
      let modal = bootstrap.Modal.getInstance(myModal);
      
      let response = this.responseText.trim();
      
      try {
        let json = JSON.parse(response);
        if(json.error){
          if(typeof alert === 'function'){
            alert('error', json.error);
          } else {
            alert('Lỗi: ' + json.error);
          }
          return;
        }
      } catch(e) {}

      if(response == '1'){
        modal.hide();
        if(typeof alert === 'function'){
          alert('success','Thêm phòng mới thành công!');
        } else {
          alert('Thêm phòng mới thành công!');
        }
        form.reset();
        get_all_rooms();
      }
      else{
        if(typeof alert === 'function'){
          alert('error','Lỗi: ' + response);
        } else {
          alert('Lỗi: ' + response);
        }
      }
    };
    
    xhr.onerror = function(){
      if(typeof alert === 'function'){
        alert('error','Lỗi kết nối! Vui lòng thử lại.');
      } else {
        alert('Lỗi kết nối! Vui lòng thử lại.');
      }
    };

    xhr.send(data);
  }

  // Edit room
  let edit_room_form = document.getElementById('edit_room_form');
  if(edit_room_form){
    edit_room_form.addEventListener('submit', function(e){
      e.preventDefault();
      edit_room();
    });
  }

  function edit_details(id) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/rooms.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function(){
      try{
        let data = JSON.parse(this.responseText);
        if(data.error){
          if(typeof alert === 'function'){
            alert('error', data.error);
          }
          return;
        }

        let form = document.getElementById('edit_room_form');
        form.elements['room_id'].value = data.roomdata.id;
        form.elements['name'].value = data.roomdata.name;
        form.elements['location'].value = data.roomdata.location || '';
        form.elements['area'].value = data.roomdata.area;
        form.elements['price'].value = data.roomdata.price;
        form.elements['discount'].value = data.roomdata.discount || 0;
        form.elements['quantity'].value = data.roomdata.quantity;
        form.elements['remaining'].value = data.roomdata.remaining;
        form.elements['adult'].value = data.roomdata.adult;
        form.elements['children'].value = data.roomdata.children;
        form.elements['desc'].value = data.roomdata.description;

        // Reset checkboxes
        form.querySelectorAll('input[name="features[]"]').forEach(cb => cb.checked = false);
        form.querySelectorAll('input[name="facilities[]"]').forEach(cb => cb.checked = false);

        // Check features
        if(data.features && Array.isArray(data.features)){
          data.features.forEach(fid => {
            let cb = form.querySelector('input[name="features[]"][value="' + fid + '"]');
            if(cb) cb.checked = true;
          });
        }

        // Check facilities
        if(data.facilities && Array.isArray(data.facilities)){
          data.facilities.forEach(fid => {
            let cb = form.querySelector('input[name="facilities[]"][value="' + fid + '"]');
            if(cb) cb.checked = true;
          });
        }
      } catch(e){
        if(typeof alert === 'function'){
          alert('error','Lỗi khi tải dữ liệu!');
        }
      }
    };

    xhr.send('get_room='+id);
  }

  function edit_room() {
    let form = document.getElementById('edit_room_form');
    if(!form){
      console.error('Form edit_room_form không tồn tại!');
      return;
    }
    
    // Validate form
    if(!form.elements['room_id'] || !form.elements['room_id'].value){
      if(typeof alert === 'function'){
        alert('error', 'Lỗi: Không tìm thấy ID phòng!');
      } else {
        alert('Lỗi: Không tìm thấy ID phòng!');
      }
      return;
    }
    
    let data = new FormData();
    data.append('edit_room', '1');
    data.append('room_id', form.elements['room_id'].value);
    data.append('name', form.elements['name'].value);
    data.append('location', form.elements['location'].value);
    data.append('area', form.elements['area'].value);
    data.append('price', form.elements['price'].value);
    data.append('discount', form.elements['discount'].value || '0');
    data.append('quantity', form.elements['quantity'].value);
    data.append('remaining', form.elements['remaining'].value);
    data.append('adult', form.elements['adult'].value);
    data.append('children', form.elements['children'].value);
    data.append('desc', form.elements['desc'].value);

    // Features
    let features = [];
    form.querySelectorAll('input[name="features[]"]:checked').forEach(cb => features.push(cb.value));
    data.append('features', JSON.stringify(features));

    // Facilities
    let facilities = [];
    form.querySelectorAll('input[name="facilities[]"]:checked').forEach(cb => facilities.push(cb.value));
    data.append('facilities', JSON.stringify(facilities));

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/rooms.php", true);
    
    // Thêm loading state
    let submitBtn = form.querySelector('button[type="submit"]');
    let originalText = '';
    if(submitBtn){
      submitBtn.disabled = true;
      originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Đang lưu...';
    }

    xhr.onload = function(){
      let myModal = document.getElementById('edit-room');
      let modal = bootstrap.Modal.getInstance(myModal);
      
      let response = this.responseText.trim();
      console.log('Response từ server:', response);
      
      // Restore button
      if(submitBtn){
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
      
      // Kiểm tra nếu response là JSON error
      try {
        let json = JSON.parse(response);
        if(json.error){
          if(typeof alert === 'function'){
            alert('error', json.error);
          } else {
            alert('Lỗi: ' + json.error);
          }
          return;
        }
      } catch(e) {
        // Không phải JSON, tiếp tục xử lý như bình thường
        console.log('Response không phải JSON:', e);
      }
      
      if(response == '1'){
        modal.hide();
        if(typeof alert === 'function'){
          alert('success','Cập nhật phòng thành công!');
        } else {
          alert('Cập nhật phòng thành công!');
        }
        get_all_rooms();
      }
      else{
        console.error('Lỗi từ server:', response);
        if(typeof alert === 'function'){
          alert('error','Lỗi: ' + response);
        } else {
          alert('Lỗi: ' + response);
        }
      }
    };
    
    xhr.onerror = function(){
      console.error('Lỗi kết nối khi gửi request');
      if(submitBtn){
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
      if(typeof alert === 'function'){
        alert('error','Lỗi kết nối! Vui lòng thử lại.');
      } else {
        alert('Lỗi kết nối! Vui lòng thử lại.');
      }
    };
    
    xhr.onabort = function(){
      console.error('Request bị hủy');
      if(submitBtn){
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
    };

    xhr.send(data);
  }

  function changeRemain(value) {
    let input = document.getElementById('edit_room_form').elements['remaining'];
    let current = parseInt(input.value) || 0;
    current += value;
    if(current < 0) current = 0;
    input.value = current;
  }

  // Room images
  function room_images(id, name) {
    document.getElementById('room-images-title').innerHTML = '<i class="bi bi-images me-2"></i>Quản lý ảnh: ' + name;
    document.getElementById('add_image_form').elements['room_id'].value = id;
    get_room_images(id);
  }

  let add_image_form = document.getElementById('add_image_form');
  if(add_image_form){
    add_image_form.addEventListener('submit', function(e){
      e.preventDefault();
      add_image();
    });
  }

  function add_image() {
    let form = document.getElementById('add_image_form');
    let files = form.elements['image[]'].files;
    
    if(files.length === 0){
      if(typeof alert === 'function'){
        alert('error', 'Vui lòng chọn ít nhất một ảnh!');
      } else {
        alert('Vui lòng chọn ít nhất một ảnh!');
      }
      return;
    }
    
    let data = new FormData();
    data.append('add_image', '1');
    data.append('room_id', form.elements['room_id'].value);
    
    // Thêm tất cả các file vào FormData
    for(let i = 0; i < files.length; i++){
      data.append('image[]', files[i]);
    }

    // Disable button và hiển thị loading
    let submitBtn = form.querySelector('button[type="submit"]');
    let originalText = '';
    if(submitBtn){
      submitBtn.disabled = true;
      originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Đang tải...';
    }

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/rooms.php", true);

    xhr.onload = function(){
      let response = this.responseText.trim();
      
      // Restore button
      if(submitBtn){
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
      
      // Kiểm tra nếu response là JSON
      try {
        let json = JSON.parse(response);
        if(json.error){
          if(typeof alert === 'function'){
            alert('error', json.error);
          } else {
            alert('Lỗi: ' + json.error);
          }
          return;
        }
        if(json.success){
          if(typeof alert === 'function'){
            alert('success', json.message || 'Thêm ảnh thành công!');
          } else {
            alert(json.message || 'Thêm ảnh thành công!');
          }
          form.reset();
          get_room_images(form.elements['room_id'].value);
          return;
        }
      } catch(e) {
        // Không phải JSON, xử lý như cũ
      }
      
      if(response == '1' || response.includes('success')){
        if(typeof alert === 'function'){
          alert('success','Thêm ảnh thành công!');
        } else {
          alert('Thêm ảnh thành công!');
        }
        form.reset();
        get_room_images(form.elements['room_id'].value);
      }
      else{
        if(typeof alert === 'function'){
          alert('error','Lỗi: ' + response);
        } else {
          alert('Lỗi: ' + response);
        }
      }
    };
    
    xhr.onerror = function(){
      if(submitBtn){
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
      if(typeof alert === 'function'){
        alert('error','Lỗi kết nối! Vui lòng thử lại.');
      } else {
        alert('Lỗi kết nối! Vui lòng thử lại.');
      }
    };

    xhr.send(data);
  }

  function get_room_images(room_id) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/rooms.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function(){
      document.getElementById('room-image-data').innerHTML = this.responseText;
    };

    xhr.send('get_room_images='+room_id);
  }

  function thumb_image(image_id, room_id, btnElement) {
    // Hiện thông báo ngay lập tức
    if(typeof alert === 'function'){
      alert('success','Đang đặt làm ảnh đại diện...');
    }

    // Lưu reference đến button được click
    let clickedBtn = btnElement || event?.target?.closest('button');
    if(!clickedBtn) return;

    // Cập nhật UI ngay (optimistic update)
    // Reset tất cả về trạng thái không phải ảnh đại diện
    let allThumbCells = document.querySelectorAll('#room-image-data td:nth-child(2)');
    allThumbCells.forEach(cell => {
      let icon = cell.querySelector('i.bi-check-lg.text-light.bg-success');
      if(icon) {
        // Chuyển icon thành button
        let imgId = icon.getAttribute('data-image-id');
        let roomId = icon.getAttribute('data-room-id');
        if(imgId && roomId) {
          cell.innerHTML = `<button onclick='thumb_image(${imgId},${roomId}, this)' class='btn btn-secondary shadow-none btn-sm' data-image-id='${imgId}' data-room-id='${roomId}'>
            <i class='bi bi-check-lg'></i>
          </button>`;
        }
      }
    });

    // Đặt ảnh được chọn thành ảnh đại diện (hiển thị icon)
    let clickedRow = clickedBtn.closest('tr');
    let clickedThumbCell = clickedRow?.querySelector('td:nth-child(2)');
    if(clickedThumbCell) {
      clickedThumbCell.innerHTML = `<i class='bi bi-check-lg text-light bg-success px-2 py-1 rounded fs-5' data-thumb='1' data-image-id='${image_id}' data-room-id='${room_id}'></i>`;
    }

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/rooms.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function(){
      if(this.responseText == '1'){
        if(typeof alert === 'function'){
          alert('success','Đặt làm ảnh đại diện thành công!');
        }
        // Refresh để đảm bảo đồng bộ
        get_room_images(room_id);
      } else {
        // Rollback nếu có lỗi
        get_room_images(room_id);
        if(typeof alert === 'function'){
          alert('error','Không thể đặt làm ảnh đại diện!');
        }
      }
    };

    xhr.onerror = function(){
      // Rollback nếu có lỗi kết nối
      get_room_images(room_id);
      if(typeof alert === 'function'){
        alert('error','Lỗi kết nối! Vui lòng thử lại.');
      }
    };

    xhr.send('thumb_image=1&image_id='+image_id+'&room_id='+room_id);
  }

  function rem_image(image_id, room_id) {
    if(!confirm('Bạn có chắc muốn xóa ảnh này?')) return;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/rooms.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function(){
      if(this.responseText == '1'){
        if(typeof alert === 'function'){
          alert('success','Xóa ảnh thành công!');
        }
        get_room_images(room_id);
      }
    };

    xhr.send('rem_image=1&image_id='+image_id+'&room_id='+room_id);
  }

  // Toggle status
  function toggle_status(id, val) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/rooms.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function(){
      if(this.responseText == '1'){
        if(typeof alert === 'function'){
          alert('success','Thay đổi trạng thái thành công!');
        }
        get_all_rooms();
      }
      else{
        if(typeof alert === 'function'){
          alert('error','Không thể thay đổi trạng thái!');
        }
      }
    };

    xhr.send('toggle_status='+id+'&value='+val);
  }

  // Filter helpers
  let debounceTimer;
  function debounceFetchRooms(){
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => get_all_rooms(), 350);
  }

  function clearFilters(){
    document.getElementById('filter_keyword').value = '';
    document.getElementById('filter_status').value = '';
    get_all_rooms();
  }
  
  window.onload = get_all_rooms;
  </script>

      </div>
    </div>
  </div>

  <?php require('../admin/inc/scripts.php'); ?>
</body>
</html>
