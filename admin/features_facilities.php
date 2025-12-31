<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Trang quản lý - Không gian & Tiện ích</title>
<?php require('inc/links.php'); ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>

/* =============================
   DARK BLUE – SIMPLE – NO EFFECT
   ============================= */
body.bg-light{
  background:#0d1117 !important;
  color:#e6e6e6 !important;
}
#main-content{
  background:#0d1117 !important;
}

.navbar{
  background:#0f1622 !important;
  border-bottom:1px solid rgba(255,255,255,0.08);
}
.navbar a, .navbar-brand{
  color:#e6e6e6 !important;
}
.navbar a:hover{
  color:#e6e6e6 !important;
}

#dashboard-menu{
  background:#0f1622 !important;
  border-right:1px solid rgba(255,255,255,0.08);
}
#dashboard-menu a{
  color:#cbd5e1 !important;
}
#dashboard-menu a:hover{
  background:#0f1622 !important;   /* Không sáng */
  color:#cbd5e1 !important;
}

/* TITLE */
h3, .card-title{
  color:#58a6ff !important;
  font-weight:600;
}

/* CARD */
.card{
  background:#141b29 !important;
  border-radius:16px !important;
  border:1px solid rgba(255,255,255,0.1) !important;
  box-shadow:none !important;
}

/* INPUT */
.form-control{
  background:#0f1622 !important;
  color:#e6e6e6 !important;
  border:1px solid rgba(255,255,255,0.15) !important;
}
.form-control:focus{
  border-color:#444 !important;
  box-shadow:none !important;
}

/* BUTTONS */
.btn{
  border-radius:10px !important;
  transition:none !important;
}
.btn-dark{
  background:#1f2b3a !important;
  border:none !important;
  color:#e6e6e6 !important;
}
.btn-dark:hover{
  background:#1f2b3a !important;
  color:#e6e6e6 !important;
}

.custom-bg{
  background:#58a6ff !important;
  color:#0d1117 !important;
}
.custom-bg:hover{
  background:#58a6ff !important;
  color:#0d1117 !important;
}

/* TABLE */
table{
  color:#e6e6e6 !important;
}
thead tr{
  background:#111927 !important;
}
thead th{
  color:#58a6ff !important;
}
tbody tr{
  background:#0f1622 !important;
}
tbody tr:hover{
  background:#0f1622 !important; /* Không đổi màu */
}

/* SCROLLBAR */
.table-responsive-md::-webkit-scrollbar{
  width:6px;
}
.table-responsive-md::-webkit-scrollbar-thumb{
  background:#58a6ff !important;
  border-radius:6px;
}

/* MODAL */
.modal-content{
  background:#141b29 !important;
  border:1px solid rgba(255,255,255,0.1) !important;
  border-radius:16px;
  box-shadow:none !important;
}

/* =============================
   ICON ALWAYS WHITE — PNG + SVG
   ============================= */

/* Tất cả SVG icon từ database */
svg,
img[src$=".svg"] {
  filter: brightness(0) invert(1) !important;
}

/* Tất cả icon PNG tự động chuyển trắng */
img[src$=".png"],
img[src$=".jpg"],
img[src$=".jpeg"],
img[src$=".webp"] {
  filter: brightness(0) invert(1) !important;
}

/* Xóa sạch hiệu ứng toàn site */
* {
  transition:none !important;
  box-shadow:none !important;
}

</style>

</head>
<body class="bg-light">

<?php require('inc/header.php'); ?>

<div class="container-fluid" id="main-content">
<div class="row">
<div class="col-lg-10 ms-auto p-4 overflow-hidden">

<h3 class="mb-4">Không gian và Tiện ích</h3>

<!-- ======================= KHÔNG GIAN ======================= -->
<div class="card mb-4">
  <div class="card-body">

    <div class="d-flex justify-content-between mb-3">
      <h5 class="card-title m-0">Không gian</h5>
      <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#feature-s">
        <i class="bi bi-plus-square"></i> Thêm
      </button>
    </div>

    <div class="table-responsive-md" style="height:350px;overflow-y:scroll;">
      <table class="table border">
        <thead>
          <tr>
            <th>#</th>
            <th>Tên</th>
            <th>Hành Động</th>
          </tr>
        </thead>
        <tbody id="features-data"></tbody>
      </table>
    </div>

  </div>
</div>

<!-- ======================= TIỆN ÍCH ======================= -->
<div class="card mb-4">
  <div class="card-body">

    <div class="d-flex justify-content-between mb-3">
      <h5 class="card-title m-0">Tiện ích</h5>
      <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#facility-s">
        <i class="bi bi-plus-square"></i> Thêm
      </button>
    </div>

    <div class="table-responsive-md" style="height:350px;overflow-y:scroll;">
      <table class="table border">
        <thead>
          <tr>
            <th>#</th>
            <th>Biểu tượng</th>
            <th>Tên</th>
            <th width="40%">Mô tả</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody id="facilities-data"></tbody>
      </table>
    </div>

  </div>
</div>

</div>
</div>
</div>

<!-- MODAL: KHÔNG GIAN -->
<div class="modal fade" id="feature-s" data-bs-backdrop="static">
  <div class="modal-dialog">
    <form id="feature_s_form">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Thêm Không Gian</h5>
        </div>

        <div class="modal-body">
          <label class="form-label fw-bold">Tên</label>
          <input type="text" name="feature_name" class="form-control" required>
        </div>

        <div class="modal-footer">
          <button class="btn btn-outline-light" data-bs-dismiss="modal">Huỷ</button>
          <button type="submit" class="btn custom-bg">Cập nhật</button>
        </div>

      </div>
    </form>
  </div>
</div>

<!-- MODAL: TIỆN ÍCH -->
<div class="modal fade" id="facility-s" data-bs-backdrop="static">
  <div class="modal-dialog">
    <form id="facility_s_form">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Thêm Tiện Ích</h5>
        </div>

        <div class="modal-body">
          <label class="form-label fw-bold">Tên</label>
          <input type="text" name="facility_name" class="form-control" required>

          <label class="form-label fw-bold mt-3">Icon (SVG/PNG)</label>
          <input type="file" name="facility_icon" accept=".svg,.png,.jpg,.jpeg" class="form-control" required>

          <label class="form-label fw-bold mt-3">Mô tả</label>
          <textarea name="facility_desc" rows="3" class="form-control"></textarea>
        </div>

        <div class="modal-footer">
          <button class="btn btn-outline-light" data-bs-dismiss="modal">Huỷ</button>
          <button type="submit" class="btn custom-bg">Cập nhật</button>
        </div>

      </div>
    </form>
  </div>
</div>

<?php require('inc/scripts.php'); ?>
<script src="scripts/features_facilities.js"></script>

</body>
</html>
