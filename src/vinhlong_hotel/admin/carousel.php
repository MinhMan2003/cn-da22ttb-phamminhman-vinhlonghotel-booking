<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Trang quản lý - Trình chiếu</title>
<?php require('inc/links.php'); ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>

/* ======================================
   GLOBAL TESLA CYBER NEON
====================================== */
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
  color:#58a6ff !important;
}

#dashboard-menu{
  background:#0f1622 !important;
  border-right:1px solid rgba(255,255,255,0.08);
}
#dashboard-menu a{
  color:#cbd5e1 !important;
}
#dashboard-menu a:hover{
  background:#152033 !important;
  color:#58a6ff !important;
}

/* ======================================
   PAGE TITLE
====================================== */
h3{
  color:#58a6ff !important;
  text-shadow:0 0 18px rgba(88,166,255,0.5);
  font-weight:700;
}

/* ======================================
   CARD
====================================== */
.card{
  background:linear-gradient(145deg,#0a0e14,#141b29) !important;
  border-radius:20px !important;
  border:1px solid rgba(255,255,255,0.1) !important;
  color:#e6e6e6 !important;

  box-shadow:
    0 10px 24px rgba(0,0,0,0.45),
    inset 0 0 12px rgba(88,166,255,0.06) !important;
}

.card-title{
  color:#58a6ff !important;
  font-weight:600;
}

/* ======================================
   BUTTONS
====================================== */
.btn{
  border-radius:10px !important;
}
.btn-dark{
  background:#1a2536 !important;
  border:none;
}
.btn-dark:hover{
  background:#223048 !important;
}
.custom-bg{
  background:#58a6ff !important;
  color:#0d1117 !important;
}
.custom-bg:hover{
  background:#7bb8ff !important;
}

/* ======================================
   INPUT
====================================== */
.form-control{
  background:#0f1622 !important;
  border:1px solid rgba(255,255,255,0.14);
  color:#e6e6e6 !important;
  border-radius:12px;
}
.form-control:focus{
  border-color:#58a6ff !important;
  box-shadow:0 0 8px rgba(88,166,255,0.3) !important;
}

/* ======================================
   MODAL
====================================== */
.modal-content{
  background:#141b29 !important;
  border:1px solid rgba(255,255,255,0.1);
  color:white !important;
  border-radius:18px;
}

/* ======================================
   FIX KHÔNG MẤT CHỮ KHI HOVER
====================================== */
*{
  -webkit-text-fill-color:inherit !important;
  color:inherit !important;
}

/* ======================================
   THUMBNAIL ẢNH
====================================== */
.carousel-box{
    width:100%;
    height:260px;
    overflow:hidden;
    border-radius:14px;
    border:2px solid rgba(88,166,255,0.35);
    background:#0f1622;
    box-shadow:0 0 14px rgba(88,166,255,0.25);
    transition:0.2s;
}

.carousel-box:hover{
    border-color:#7bb8ff;
    box-shadow:0 0 18px rgba(88,166,255,0.45);
}

.carousel-box img{
    width:100%;
    height:100%;
    object-fit:cover;
}

/* Delete icon */
.delete-btn{
    position:absolute;
    top:10px;
    right:10px;
    background:rgba(0,0,0,0.55);
    border:none;
    padding:6px 8px;
    border-radius:6px;
    color:#ff6b6b;
    font-size:18px;
    cursor:pointer;
    transition:0.2s;
}
.delete-btn:hover{
    background:rgba(0,0,0,0.75);
    color:#ff3b3b;
}

</style>

</head>

<body class="bg-light">

<?php require('inc/header.php'); ?>

<div class="container-fluid" id="main-content">
<div class="row">
<div class="col-lg-10 ms-auto p-4">

<h3 class="mb-4">Hình ảnh trình chiếu</h3>

<!-- ======================= LIST CAROUSEL ======================= -->
<div class="card mb-4">
  <div class="card-body">

    <div class="d-flex justify-content-between mb-3">
      <h5 class="card-title m-0">Danh sách hình ảnh</h5>
      <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#carousel-s">
        <i class="bi bi-plus-square"></i> Thêm ảnh
      </button>
    </div>

    <div class="row g-4" id="carousel-data"></div>

  </div>
</div>

<!-- ======================= MODAL ADD IMAGE ======================= -->
<div class="modal fade" id="carousel-s" data-bs-backdrop="static">
  <div class="modal-dialog">
    <form id="carousel_s_form">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Thêm hình ảnh</h5>
        </div>

        <div class="modal-body">
          <label class="form-label fw-bold">Chọn ảnh</label>
          <input type="file" name="carousel_picture" id="carousel_picture_inp"
                 accept=".jpg, .jpeg, .png, .webp"
                 class="form-control" required>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">
            Huỷ
          </button>
          <button type="submit" class="btn custom-bg">
            Tải lên
          </button>
        </div>

      </div>
    </form>
  </div>
</div>

</div>
</div>
</div>

<?php require('inc/scripts.php'); ?>
<script src="scripts/carousel.js"></script>

</body>
</html>
