<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  require('inc/destinations_helper.php');
  adminLogin();
  
  // Ensure destination_images table exists
  ensureDestinationImagesTable($con);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý - Điểm du lịch</title>
  <?php require('inc/links.php'); ?>
  <link rel="stylesheet" href="css/destinations.css?v=1">
</head>
<body class="bg-light destinations-page">

<?php require('inc/header.php'); ?>

<div class="container-fluid" id="main-content">
  <div class="row">
    <div class="col-lg-10 ms-auto p-4 overflow-hidden">

      <div class="page-header">
        <div>
          <p class="page-eyebrow">Du lich Vinh Long</p>
          <h3 class="page-title">Quan ly Diem den & Dac san</h3>
          <p class="page-subtitle">To chuc du lieu, hinh anh va trang thai hien thi.</p>
        </div>
        <div class="page-actions">
          <button class="btn btn-primary btn-sm shadow-none" data-bs-toggle="modal" data-bs-target="#destinationModal">
            <i class="bi bi-plus-circle"></i> Them diem du lich
          </button>
          <button class="btn btn-outline-light btn-sm shadow-none" data-bs-toggle="modal" data-bs-target="#specialtyModal">
            <i class="bi bi-plus-circle"></i> Them dac san
          </button>
        </div>
      </div>

      <!-- Tabs Navigation -->
      <ul class="nav nav-tabs mb-4" id="managementTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="destinations-tab" data-bs-toggle="tab" data-bs-target="#destinations-pane" type="button" role="tab" aria-controls="destinations-pane" aria-selected="true">
            <i class="bi bi-geo-alt me-2"></i>Điểm du lịch
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="specialties-tab" data-bs-toggle="tab" data-bs-target="#specialties-pane" type="button" role="tab" aria-controls="specialties-pane" aria-selected="false">
            <i class="bi bi-gift me-2"></i>Đặc sản Vĩnh Long
          </button>
        </li>
      </ul>

      <!-- Tab Content -->
      <div class="tab-content" id="managementTabsContent">
        <!-- Tab 1: Điểm du lịch -->
        <div class="tab-pane fade show active" id="destinations-pane" role="tabpanel" aria-labelledby="destinations-tab">
          <div class="stats-strip">
            <div class="stat-card">
              <div class="stat-label">Tong diem den</div>
              <div class="stat-value" id="destinations-total">0</div>
            </div>
            <div class="stat-card">
              <div class="stat-label">Dang hien thi</div>
              <div class="stat-value" id="destinations-active">0</div>
            </div>
            <div class="stat-card">
              <div class="stat-label">Tong anh</div>
              <div class="stat-value" id="destinations-images">0</div>
            </div>
          </div>

          <div class="page-toolbar">
            <div class="toolbar-group">
              <div class="search-field">
                <i class="bi bi-search"></i>
                <input type="search" id="destinations-search" class="form-control shadow-none" placeholder="Tim theo ten, dia chi, loai...">
              </div>
              <select id="destinations-category" class="form-select shadow-none">
                <option value="">Tat ca loai</option>
                <option value="temple">Chua, dinh</option>
                <option value="nature">Thien nhien</option>
                <option value="market">Cho noi</option>
                <option value="culture">Van hoa</option>
                <option value="other">Khac</option>
              </select>
              <select id="destinations-status" class="form-select shadow-none">
                <option value="">Tat ca trang thai</option>
                <option value="active">Hien thi</option>
                <option value="inactive">An</option>
              </select>
              <button type="button" class="btn btn-outline-light btn-sm" id="destinations-reset">Dat lai</button>
            </div>
          </div>

          <div id="destination-alert"></div>

          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="table-responsive" style="max-height:600px; overflow-y:auto;">
                <table class="table table-hover text-center align-middle">
                  <thead class="table-dark">
                    <tr>
                      <th>#</th>
                      <th>Anh</th>
                      <th>Ten diem du lich</th>
                      <th>Dia chi</th>
                      <th>Loai</th>
                      <th>Danh gia</th>
                      <th>Phong lien ket</th>
                      <th>Trang thai</th>
                      <th>Hanh dong</th>
                    </tr>
                  </thead>
                  <tbody id="destination-data"></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab 2: Đặc sản -->
        <div class="tab-pane fade" id="specialties-pane" role="tabpanel" aria-labelledby="specialties-tab">
          <div class="stats-strip">
            <div class="stat-card">
              <div class="stat-label">Tong dac san</div>
              <div class="stat-value" id="specialties-total">0</div>
            </div>
            <div class="stat-card">
              <div class="stat-label">Dang hien thi</div>
              <div class="stat-value" id="specialties-active">0</div>
            </div>
            <div class="stat-card">
              <div class="stat-label">Tong anh</div>
              <div class="stat-value" id="specialties-images">0</div>
            </div>
          </div>

          <div class="page-toolbar">
            <div class="toolbar-group">
              <div class="search-field">
                <i class="bi bi-search"></i>
                <input type="search" id="specialties-search" class="form-control shadow-none" placeholder="Tim theo ten, loai, dia chi...">
              </div>
              <select id="specialties-category" class="form-select shadow-none">
                <option value="">Tat ca loai</option>
                <option value="food">Mon an</option>
                <option value="fruit">Trai cay</option>
                <option value="drink">Do uong</option>
                <option value="souvenir">Qua luu niem</option>
              </select>
              <select id="specialties-status" class="form-select shadow-none">
                <option value="">Tat ca trang thai</option>
                <option value="active">Hien thi</option>
                <option value="inactive">An</option>
              </select>
              <button type="button" class="btn btn-outline-light btn-sm" id="specialties-reset">Dat lai</button>
            </div>
          </div>

          <div id="specialty-alert"></div>

          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="table-responsive" style="max-height:600px; overflow-y:auto;">
                <table class="table table-hover text-center align-middle">
                  <thead class="table-dark">
                    <tr>
                      <th>#</th>
                      <th>Anh</th>
                      <th>Ten diem du lich</th>
                      <th>Dia chi</th>
                      <th>Loai</th>
                      <th>Danh gia</th>
                      <th>Phong lien ket</th>
                      <th>Trang thai</th>
                      <th>Hanh dong</th>
                    </tr>
                  </thead>
                  <tbody id="specialty-data"></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Modal add/edit Điểm du lịch -->
<div class="modal fade" id="destinationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="bi bi-geo-alt me-2"></i> Thêm / Sửa điểm du lịch</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="destination-form" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="id" id="destination_id">
          
          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label fw-semibold">Tên điểm du lịch <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control shadow-none" required>
            </div>
            
            <div class="col-md-12">
              <label class="form-label fw-semibold">Mô tả ngắn</label>
              <textarea name="short_description" class="form-control shadow-none" rows="2" placeholder="Mô tả ngắn gọn (tối đa 500 ký tự)"></textarea>
            </div>
            
            <div class="col-md-12">
              <label class="form-label fw-semibold">Mô tả chi tiết</label>
              <textarea name="description" class="form-control shadow-none" rows="4" required></textarea>
            </div>
            
            <div class="col-md-12">
              <label class="form-label fw-semibold">Địa chỉ <span class="text-danger">*</span></label>
              <input type="text" name="location" class="form-control shadow-none" required>
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Vĩ độ (Latitude)</label>
              <input type="number" name="latitude" class="form-control shadow-none" step="0.00000001" placeholder="10.2500">
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Kinh độ (Longitude)</label>
              <input type="number" name="longitude" class="form-control shadow-none" step="0.00000001" placeholder="105.9500">
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Loại điểm du lịch</label>
              <select name="category" class="form-select shadow-none" required>
                <option value="temple">Chùa, Đình</option>
                <option value="nature">Thiên nhiên</option>
                <option value="market">Chợ nổi</option>
                <option value="culture">Văn hóa</option>
                <option value="other">Khác</option>
              </select>
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Đánh giá (0-5)</label>
              <input type="number" name="rating" class="form-control shadow-none" step="0.1" min="0" max="5" value="0">
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Số lượng đánh giá</label>
              <input type="number" name="review_count" class="form-control shadow-none" min="0" value="0">
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Trạng thái</label>
              <select name="active" class="form-select shadow-none">
                <option value="1">Hiển thị</option>
                <option value="0">Ẩn</option>
              </select>
            </div>
            
            <div class="col-md-12">
              <label class="form-label fw-semibold">Ảnh điểm du lịch</label>
              <input type="file" name="images[]" class="form-control shadow-none" accept="image/*" id="destination_images" multiple>
              <small class="text-muted">Có thể chọn nhiều ảnh. Định dạng: JPG, PNG, WEBP. Kích thước tối đa: 5MB/ảnh</small>
              <div id="images-preview" class="mt-3 row g-2"></div>
              <div id="existing-images" class="mt-3"></div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-primary">Lưu</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal add/edit Đặc sản -->
<div class="modal fade" id="specialtyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="bi bi-gift me-2"></i> Thêm / Sửa đặc sản</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="specialty-form" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="id" id="specialty_id">
          
          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label fw-semibold">Tên đặc sản <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control shadow-none" required>
            </div>
            
            <div class="col-md-12">
              <label class="form-label fw-semibold">Mô tả ngắn</label>
              <textarea name="short_description" class="form-control shadow-none" rows="2" placeholder="Mô tả ngắn gọn (tối đa 500 ký tự)"></textarea>
            </div>
            
            <div class="col-md-12">
              <label class="form-label fw-semibold">Mô tả chi tiết</label>
              <textarea name="description" class="form-control shadow-none" rows="4" required></textarea>
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Loại đặc sản <span class="text-danger">*</span></label>
              <select name="category" class="form-select shadow-none" required>
                <option value="food">Món ăn</option>
                <option value="fruit">Trái cây</option>
                <option value="drink">Đồ uống</option>
                <option value="souvenir">Quà lưu niệm</option>
              </select>
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Khoảng giá</label>
              <input type="text" name="price_range" class="form-control shadow-none" placeholder="VD: 50.000 - 200.000 VNĐ">
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Mùa tốt nhất</label>
              <input type="text" name="best_season" class="form-control shadow-none" placeholder="VD: Tháng 8 - 12">
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Địa điểm mua</label>
              <input type="text" name="location" class="form-control shadow-none" placeholder="VD: Huyện Bình Minh, Vĩnh Long">
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Vĩ độ (Latitude)</label>
              <input type="number" name="latitude" class="form-control shadow-none" step="0.00000001" placeholder="10.2500">
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Kinh độ (Longitude)</label>
              <input type="number" name="longitude" class="form-control shadow-none" step="0.00000001" placeholder="105.9500">
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Đánh giá (0-5)</label>
              <input type="number" name="rating" class="form-control shadow-none" step="0.1" min="0" max="5" value="0">
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Số lượng đánh giá</label>
              <input type="number" name="review_count" class="form-control shadow-none" min="0" value="0">
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Trạng thái</label>
              <select name="active" class="form-select shadow-none">
                <option value="1">Hiển thị</option>
                <option value="0">Ẩn</option>
              </select>
            </div>
            
            <div class="col-md-12">
              <label class="form-label fw-semibold">Ảnh đặc sản</label>
              <input type="file" name="images[]" class="form-control shadow-none" accept="image/*" id="specialty_images" multiple>
              <small class="text-muted">Có thể chọn nhiều ảnh. Định dạng: JPG, PNG, WEBP. Kích thước tối đa: 5MB/ảnh</small>
              <div id="specialty-images-preview" class="mt-3 row g-2"></div>
              <div id="specialty-existing-images" class="mt-3"></div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-primary">Lưu</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal quản lý địa điểm mua đặc sản -->
<div class="modal fade" id="shopModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="bi bi-shop me-2"></i> Quản lý địa điểm mua</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="shop-form">
        <div class="modal-body">
          <input type="hidden" name="specialty_id" id="shop_specialty_id">
          <input type="hidden" name="shop_id" id="shop_id">
          
          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label fw-semibold">Tên cửa hàng <span class="text-danger">*</span></label>
              <input type="text" name="shop_name" class="form-control shadow-none" required>
            </div>
            
            <div class="col-md-12">
              <label class="form-label fw-semibold">Địa chỉ <span class="text-danger">*</span></label>
              <input type="text" name="address" class="form-control shadow-none" required>
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Số điện thoại</label>
              <input type="text" name="phone" class="form-control shadow-none">
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Giờ mở cửa</label>
              <input type="text" name="opening_hours" class="form-control shadow-none" placeholder="VD: 7:00 - 18:00">
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Vĩ độ (Latitude)</label>
              <input type="number" name="latitude" class="form-control shadow-none" step="0.00000001">
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Kinh độ (Longitude)</label>
              <input type="number" name="longitude" class="form-control shadow-none" step="0.00000001">
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Đánh giá (0-5)</label>
              <input type="number" name="rating" class="form-control shadow-none" step="0.1" min="0" max="5" value="0">
            </div>
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Trạng thái</label>
              <select name="active" class="form-select shadow-none">
                <option value="1">Hiển thị</option>
                <option value="0">Ẩn</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-primary">Lưu</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require('inc/scripts.php'); ?>
<script src="scripts/destinations.js"></script>


</body>
</html>




