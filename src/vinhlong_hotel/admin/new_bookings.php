<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Trang quản lý - Lượt đặt phòng mới</title>
<?php require('inc/links.php'); ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>

/* =============================
   TESLA CYBER NEON DARK THEME
   ============================= */

body.bg-light{
  background:#0d1117 !important;
  color:#e6e6e6 !important;
}

#main-content{
  background:#0d1117 !important;
}

/* =============================
   NAVBAR (HEADER)
   ============================= */
.navbar{
  background:#0f1622 !important;
  border-bottom:1px solid rgba(255,255,255,0.06) !important;
}
.navbar a, .navbar-brand{
  color:#e6e6e6 !important;
}
.navbar a:hover{
  color:#58a6ff !important;
}

/* =============================
   SIDEBAR (DASHBOARD MENU)
   ============================= */
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

/* =============================
   PAGE TITLE
   ============================= */
h3{
  color:#58a6ff !important;
  text-shadow:0 0 18px rgba(88,166,255,0.8);
  font-weight:700;
  letter-spacing:1px;
}

/* =============================
   CARD – STATIC (NO ANIMATION)
   ============================= */
.card{
  background:linear-gradient(145deg,#0a0e14,#141b29) !important;
  border-radius:20px !important;
  border:1px solid rgba(255,255,255,0.06) !important;
  color:#e6e6e6 !important;
  box-shadow:
    0 10px 26px rgba(0,0,0,0.45),
    inset 0 0 12px rgba(88,166,255,0.06) !important;
}

.card:hover{
  transform:none !important;
  box-shadow:
    0 10px 26px rgba(0,0,0,0.45),
    inset 0 0 12px rgba(88,166,255,0.06) !important;
}

/* =============================
   INPUT SEARCH
   ============================= */
.form-control{
  background:#0f1622 !important;
  border:1px solid rgba(255,255,255,0.12) !important;
  color:#e6e6e6 !important;
  border-radius:12px !important;
}

.form-control:focus{
  border-color:#58a6ff !important;
  box-shadow:0 0 8px rgba(88,166,255,0.35) !important;
}

/* =============================
   TABLE STYLE
   ============================= */
.table{
  color:#e6e6e6 !important;
}

thead tr{
  background:#111927 !important;
}

thead th{
  color:#58a6ff !important;
  padding:14px;
  font-size:14px;
  text-transform:uppercase;
  border-bottom:1px solid rgba(255,255,255,0.08) !important;
}

tbody tr{
  background:#0f1622 !important;
}

tbody tr:hover{
  background:#152033 !important;
}

/* =============================
   BUTTONS
   ============================= */
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

.btn-outline-light{
  border:1px solid rgba(255,255,255,0.3) !important;
  color:#e6e6e6 !important;
}

.btn-outline-light:hover{
  background:#58a6ff !important;
  color:#0d1117 !important;
}

/* =============================
   MODAL STYLE
   ============================= */
.modal-content{
  background:#141b29 !important;
  border:1px solid rgba(255,255,255,0.08) !important;
  color:white !important;
  border-radius:20px !important;
}

.modal-header,
.modal-footer{
  border-color:rgba(255,255,255,0.08) !important;
}
/* FIX GLOBAL HOVER TRANSPARENT */
tr:hover, td:hover, th:hover,
a:hover, p:hover, span:hover,
div:hover, *:hover {
    color: inherit !important;
    -webkit-text-fill-color: inherit !important;
}

</style>

</head>

<body class="bg-light">

<?php require('inc/header.php'); ?>

<div class="container-fluid" id="main-content">
<div class="row">
<div class="col-lg-10 ms-auto p-4">

<h3 class="mb-4">LƯỢT ĐẶT PHÒNG MỚI</h3>

<div class="card mb-4">
  <div class="card-body">

    <!-- SEARCH -->
    <div class="text-end mb-4">
      <input type="text" oninput="get_bookings(this.value)" class="form-control shadow-none w-25 ms-auto" placeholder="Nhập để tìm kiếm...">
    </div>

    <!-- TABLE -->
    <div class="table-responsive">
      <table class="table table-hover border" style="min-width:1200px;">
        <thead>
          <tr>
            <th>#</th>
            <th>Chi tiết người dùng</th>
            <th>Chi tiết phòng</th>
            <th>Chi tiết đặt chỗ</th>
            <th>Hoạt động</th>
          </tr>
        </thead>
        <tbody id="table-data"></tbody>
      </table>
    </div>

  </div>
</div>

</div>
</div>
</div>

<!-- MODAL -->
<div class="modal fade" id="assign-room" data-bs-backdrop="static" tabindex="-1">
  <div class="modal-dialog">
    <form id="assign_room_form">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Chỉ định phòng</h5>
        </div>

        <div class="modal-body">
          <label class="form-label fw-bold">Số phòng</label>
          <input type="text" name="room_no" class="form-control shadow-none mb-3" required>

          <div class="badge bg-secondary text-dark text-wrap lh-base w-100">
            * Chỉ gán số phòng khi người dùng đã đến!
          </div>

          <input type="hidden" name="booking_id">
        </div>

        <div class="modal-footer">
          <button type="reset" data-bs-dismiss="modal" class="btn btn-outline-light">Hủy</button>
          <button type="submit" class="btn custom-bg text-dark">Giao nhiệm vụ</button>
        </div>

      </div>
    </form>
  </div>
</div>

<?php require('inc/scripts.php'); ?>
<script src="scripts/new_bookings.js"></script>

</body>
</html>
