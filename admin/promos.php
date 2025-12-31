<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  require('inc/promos_helper.php');
  adminLogin();
  ensurePromosTable($con);
  seedDefaultPromos($con);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý - Mã giảm giá</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

<?php require('inc/header.php'); ?>

<div class="container-fluid" id="main-content">
  <div class="row">
    <div class="col-lg-10 ms-auto p-4 overflow-hidden">

      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
          <p class="text-uppercase text-secondary small mb-1">Khuyến mãi</p>
          <h3 class="mb-0 fw-bold">Mã giảm giá</h3>
        </div>
        <button class="btn btn-dark btn-sm shadow-none" data-bs-toggle="modal" data-bs-target="#promoModal">
          <i class="bi bi-plus-circle"></i> Thêm mã
        </button>
      </div>

      <div id="promo-alert"></div>

      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="table-responsive" style="max-height:520px; overflow-y:auto;">
            <table class="table table-hover text-center align-middle">
              <thead class="table-dark">
                <tr>
                  <th>#</th>
                  <th>Mã</th>
                  <th>Tiêu đề</th>
                  <th>Nhóm</th>
                  <th>Giá trị</th>
                  <th>ĐK tối thiểu</th>
                  <th>Giảm tối đa</th>
                  <th>Hết hạn</th>
                  <th>Ưu tiên</th>
                  <th>Trạng thái</th>
                  <th>Hành động</th>
                </tr>
              </thead>
              <tbody id="promo-data"></tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Modal add/edit -->
<div class="modal fade" id="promoModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="bi bi-ticket-perforated me-2"></i> Thêm / Sửa mã giảm giá</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="promo-form">
        <div class="modal-body">
          <input type="hidden" name="id" id="promo_id">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Nhãn hiển thị</label>
              <input type="text" name="label" class="form-control shadow-none" placeholder="VD: Hết hạn sau 2 ngày">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Mã code</label>
              <input type="text" name="code" class="form-control shadow-none text-uppercase" required>
            </div>
            <div class="col-md-12">
              <label class="form-label fw-semibold">Tiêu đề</label>
              <input type="text" name="title" class="form-control shadow-none" required>
            </div>
            <div class="col-md-12">
              <label class="form-label fw-semibold">Mô tả ngắn</label>
              <input type="text" name="description" class="form-control shadow-none" placeholder="Giảm 5% tối đa 200k...">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Nhóm hiển thị</label>
              <select name="category" class="form-select shadow-none">
                <option value="hot">Mã giảm giá hot</option>
                <option value="bank">Ngân hàng</option>
                <option value="wallet">Ví/QR</option>
                <option value="destination">Điểm đến hot</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Loại giảm</label>
              <select name="discount_type" class="form-select shadow-none">
                <option value="percent">% (theo %)</option>
                <option value="flat">Số tiền cố định</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Giá trị</label>
              <input type="number" name="discount_value" class="form-control shadow-none" min="0" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Đơn tối thiểu (VND)</label>
              <input type="number" name="min_amount" class="form-control shadow-none" min="0" value="0">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Giảm tối đa (VND)</label>
              <input type="number" name="max_discount" class="form-control shadow-none" min="0" placeholder="0 = không giới hạn">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Ưu tiên</label>
              <input type="number" name="priority" class="form-control shadow-none" value="0">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Hết hạn</label>
              <input type="date" name="expires_at" class="form-control shadow-none">
            </div>
            <div class="col-md-4 d-flex align-items-end">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" name="active" value="1" id="promo_active" checked>
                <label class="form-check-label" for="promo_active">Đang kích hoạt</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary shadow-none" data-bs-dismiss="modal">Đóng</button>
          <button type="submit" class="btn btn-dark shadow-none">Lưu</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="scripts/promos.js?v=<?php echo time(); ?>"></script>
</body>
</html>
