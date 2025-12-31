<?php
  require('inc/essentials.php');
  adminLogin();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Trang quản lý - Cài đặt</title>
<?php require('inc/links.php'); ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>

/* ==========================================
   GLOBAL DARK THEME
========================================== */
body.bg-light{
  background:#0d1117 !important;
  color:#e6e6e6 !important;
}
#main-content{
  background:#0d1117 !important;
}

/* ==========================================
   NAVBAR + SIDEBAR
========================================== */
.navbar{
  background:#0f1622 !important;
  border-bottom:1px solid rgba(255,255,255,0.1) !important;
}
.navbar a, .navbar-brand{
  color:#e6e6e6 !important;
}
.navbar a:hover{
  color:#58a6ff !important;
}

#dashboard-menu{
  background:#0f1622 !important;
  border-right:1px solid rgba(255,255,255,0.1);
}
#dashboard-menu a{
  color:#cbd5e1 !important;
}
#dashboard-menu a:hover{
  background:#152033 !important;
  color:#58a6ff !important;
}

/* ==========================================
   PAGE TITLE
========================================== */
h3{
  color:#58a6ff !important;
  font-weight:700;
  text-shadow:0 0 18px rgba(88,166,255,0.8);
}

/* ==========================================
   CARD UI
========================================== */
.card{
  background:linear-gradient(145deg,#0a0e14,#141b29) !important;
  border-radius:20px !important;
  border:1px solid rgba(255,255,255,0.1) !important;
  color:#e6e6e6 !important;

  box-shadow:
    0 10px 25px rgba(0,0,0,0.4),
    inset 0 0 10px rgba(88,166,255,0.06) !important;
}

.card-title{
  color:#58a6ff !important;
  font-weight:600;
}

/* ==========================================
   INPUTS
========================================== */
.form-control{
  background:#0f1622 !important;
  border:1px solid rgba(255,255,255,0.15) !important;
  color:#e6e6e6 !important;
  border-radius:12px !important;
}
.form-control:focus{
  border-color:#58a6ff !important;
  box-shadow:0 0 6px rgba(88,166,255,0.35) !important;
}

/* ==========================================
   BUTTONS
========================================== */
.btn{
  border-radius:10px !important;
}
.custom-bg{
  background:#58a6ff !important;
  color:#0d1117 !important;
}
.custom-bg:hover{
  background:#7bb8ff !important;
}
.btn-dark{
  background:#152033 !important;
  border:none;
}
.btn-dark:hover{
  background:#1d2a42 !important;
}
.btn-outline-light{
  border:1px solid rgba(255,255,255,0.25) !important;
  color:white !important;
}
.btn-outline-light:hover{
  background:#58a6ff !important;
  color:#0d1117 !important;
}

/* ==========================================
   MODAL
========================================== */
.modal-content{
  background:#141b29 !important;
  border-radius:18px !important;
  border:1px solid rgba(255,255,255,0.1) !important;
  color:#e6e6e6 !important;
}
.modal-header,
.modal-footer{
  border-color:rgba(255,255,255,0.08) !important;
}

/* ==========================================
   FIX: KHÔNG MẤT CHỮ KHI HOVER
========================================== */
*{
  -webkit-text-fill-color:inherit !important;
  color:inherit !important;
}

/* ==========================================
   SWITCH BẢO TRÌ – NEON WARNING
========================================== */
.danger-switch .form-check-input {
    width:45px;
    height:24px;
    cursor:pointer;
    border:2px solid #888;
    background-color:#28a745;
    transition:0.3s;
    box-shadow:0 0 6px rgba(0,255,0,0.4);
}
.danger-switch .form-check-input:checked {
    background-color:#dc3545 !important;
    border-color:#dc3545 !important;
    box-shadow:0 0 12px rgba(255,0,0,0.8);
}

#shutdown-status.active-danger{
    color:#ff4d4d !important;
    font-weight:700;
    text-shadow:0 0 10px rgba(255,80,80,0.6);
}

</style>
</head>

<body class="bg-light">

<?php require('inc/header.php'); ?>

<div class="container-fluid" id="main-content">
<div class="row">
<div class="col-lg-10 ms-auto p-4">

<h3 class="mb-4">Cài đặt trang</h3>

<!-- ==========================================
   GENERAL SECTION
========================================== -->
<div class="card mb-4">
  <div class="card-body">

    <div class="d-flex justify-content-between mb-3">
      <h5 class="card-title m-0">Thiết lập chung</h5>
      <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#general-s">
        <i class="bi bi-pencil-square"></i> Sửa
      </button>
    </div>

    <h6 class="fw-bold">Tiêu đề trang</h6>
    <p id="site_title"></p>

    <h6 class="fw-bold">Logo trang</h6>
    <div id="site_logo_preview" class="mb-3">
      <img id="site_logo_img" src="" alt="Logo" style="max-height: 100px; max-width: 200px; object-fit: contain; border-radius: 8px; display: none;">
      <p id="site_logo_text" class="text-muted">Chưa có logo</p>
    </div>

    <h6 class="fw-bold">Về chúng tôi</h6>
    <p id="site_about"></p>

  </div>
</div>

<!-- GENERAL MODAL -->
<div class="modal fade" id="general-s">
  <div class="modal-dialog">
    <form id="general_s_form">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Thiết lập chung</h5>
        </div>
        <div class="modal-body">
          <label class="fw-bold">Tiêu đề trang</label>
          <input type="text" name="site_title" id="site_title_inp" class="form-control mb-3">

          <label class="fw-bold">Logo trang</label>
          <div class="mb-3">
            <div id="logo_preview_modal" class="mb-2">
              <img id="logo_preview_img" src="" alt="Logo preview" style="max-height: 80px; max-width: 200px; object-fit: contain; border-radius: 8px; display: none;">
            </div>
            <input type="file" name="site_logo" id="site_logo_inp" accept=".jpg,.png,.jpeg,.webp,.svg" class="form-control">
            <small class="text-muted">Định dạng: JPG, PNG, JPEG, WEBP, SVG. Kích thước tối đa: 2MB</small>
          </div>

          <label class="fw-bold">Về chúng tôi</label>
          <textarea name="site_about" id="site_about_inp" rows="6" class="form-control"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Huỷ</button>
          <button type="submit" class="btn custom-bg">Cập nhật</button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- ==========================================
   SHUTDOWN SECTION
========================================== -->
<div class="card mb-4">
  <div class="card-body">

    <div class="d-flex justify-content-between mb-3">
      <h5 class="card-title m-0">Bảo trì hệ thống</h5>

      <div class="form-check form-switch danger-switch">
        <input class="form-check-input" type="checkbox" id="shutdown-toggle" onclick="upd_shutdown()">
        <label id="shutdown-status" class="ms-2 fw-bold"></label>
      </div>
    </div>

    <p class="card-text">
      Người dùng sẽ không thể đặt phòng khi hệ thống đang bảo trì.
    </p>

  </div>
</div>


<!-- ==========================================
   CONTACT SECTION
========================================== -->

<div class="card mb-4">
  <div class="card-body">

    <div class="d-flex justify-content-between mb-3">
      <h5 class="card-title m-0">Thiết lập liên hệ</h5>
      <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#contacts-s">
        <i class="bi bi-pencil-square"></i> Sửa
      </button>
    </div>

    <div class="row">

      <div class="col-lg-6">
        <h6 class="fw-bold">Địa chỉ</h6>
        <p id="address"></p>

        <h6 class="fw-bold">Google Map</h6>
        <p id="gmap"></p>

        <h6 class="fw-bold">Số tổng đài</h6>
        <p><i class="bi bi-telephone-fill"></i> <span id="pn1"></span></p>

        <h6 class="fw-bold">E-mail</h6>
        <p id="email"></p>
      </div>

      <div class="col-lg-6">
        <h6 class="fw-bold">Mạng xã hội</h6>
        <p><i class="bi bi-facebook"></i> <span id="fb"></span></p>
        <p><i class="bi bi-instagram"></i> <span id="insta"></span></p>
        <p><i class="bi bi-twitter"></i> <span id="tw"></span></p>

        <h6 class="fw-bold">iFrame</h6>
        <iframe id="iframe" class="border p-2 w-100" loading="lazy"></iframe>
      </div>

    </div>

  </div>
</div>


<!-- CONTACT MODAL -->
<div class="modal fade" id="contacts-s">
  <div class="modal-dialog modal-lg">
    <form id="contacts_s_form">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Thiết lập liên hệ</h5></div>

        <div class="modal-body">
          <div class="row">

            <div class="col-md-6">
              <label class="fw-bold">Địa chỉ</label>
              <input id="address_inp" name="address" class="form-control mb-3">

              <label class="fw-bold">Google Map</label>
              <input id="gmap_inp" name="gmap" class="form-control mb-3">

              <label class="fw-bold">Số tổng đài</label>
              <div class="input-group mb-3">
                <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                <input id="pn1_inp" name="pn1" class="form-control">
              </div>

              <label class="fw-bold">Email</label>
              <input id="email_inp" name="email" class="form-control mb-3">
            </div>

            <div class="col-md-6">
              <label class="fw-bold">Facebook</label>
              <input id="fb_inp" name="fb" class="form-control mb-3">

              <label class="fw-bold">Instagram</label>
              <input id="insta_inp" name="insta" class="form-control mb-3">

              <label class="fw-bold">Twitter</label>
              <input id="tw_inp" name="tw" class="form-control mb-3">

              <label class="fw-bold">iFrame Src</label>
              <input id="iframe_inp" name="iframe" class="form-control mb-3">
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Huỷ</button>
          <button type="submit" class="btn custom-bg">Cập nhật</button>
        </div>

      </div>
    </form>
  </div>
</div>


<!-- ==========================================
   TEAM SECTION
========================================== -->

<div class="card mb-4">
  <div class="card-body">

    <div class="d-flex justify-content-between mb-3">
      <h5 class="card-title m-0">Đội ngũ quản lý</h5>
      <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#team-s">
        <i class="bi bi-plus-square"></i> Thêm
      </button>
    </div>

    <div class="row" id="team-data"></div>

  </div>
</div>


<!-- TEAM MODAL -->
<div class="modal fade" id="team-s">
  <div class="modal-dialog">
    <form id="team_s_form">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Thêm thành viên</h5>
        </div>

        <div class="modal-body">
          <label class="fw-bold">Tên</label>
          <input id="member_name_inp" name="member_name" class="form-control mb-3">

          <label class="fw-bold">Hình ảnh</label>
          <input id="member_picture_inp" type="file" name="picture" accept=".jpg,.png,.jpeg,.webp" class="form-control">
        </div>

        <div class="modal-footer">
          <button class="btn btn-outline-light" data-bs-dismiss="modal">Huỷ</button>
          <button class="btn custom-bg" type="submit">Cập nhật</button>
        </div>

      </div>
    </form>
  </div>
</div>


</div>
</div>
</div>

<?php require('inc/scripts.php'); ?>
<script src="scripts/settings.js"></script>

</body>
</html>
