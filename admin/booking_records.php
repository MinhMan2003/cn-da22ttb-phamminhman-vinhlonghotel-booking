<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Trang quản lý - Thống kê đặt phòng</title>
<?php require('inc/links.php'); ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>

/* ==========================================
   TESLA CYBER NEON DARK THEME (GLOBAL)
   ========================================== */

body.bg-light{
  background:#0d1117 !important;
  color:#e6e6e6 !important;
}

#main-content{
  background:#0d1117 !important;
}

/* ==========================================
   SIDEBAR FIX (header.php)
   ========================================== */
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

/* ==========================================
   TOP NAVBAR
   ========================================== */
.navbar{
  background:#0f1622 !important;
  border-bottom:1px solid rgba(255,255,255,0.05) !important;
}
.navbar a, .navbar-brand{
  color:#e6e6e6 !important;
}
.navbar a:hover{
  color:#58a6ff !important;
}

/* ==========================================
   TITLE
   ========================================== */
h3{
  color:#58a6ff !important;
  text-shadow:0 0 18px rgba(88,166,255,0.8);
  font-weight:700;
  letter-spacing:1px;
}

/* ==========================================
   CARD
   ========================================== */
.card{
  background:linear-gradient(145deg,#0a0e14,#141b29) !important;
  border-radius:20px !important;
  border:1px solid rgba(255,255,255,0.08) !important;
  color:#e6e6e6 !important;

  box-shadow:
    0 10px 26px rgba(0,0,0,0.45),
    inset 0 0 12px rgba(88,166,255,0.06) !important;
}

/* ==========================================
   SEARCH INPUT
   ========================================== */
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

/* ==========================================
   TABLE
   ========================================== */
.table{
  color:#e6e6e6 !important;
}

thead tr{
  background:#111927 !important;
}
thead th{
  color:#58a6ff !important;
  padding:14px;
  text-transform:uppercase;
  letter-spacing:0.5px;
  border-bottom:1px solid rgba(255,255,255,0.06) !important;
}

tbody tr{
  background:#0f1622 !important;
}
tbody tr:hover{
  background:#152033 !important;
}

/* Fix mất chữ khi hover */
tbody tr:hover *{
  color:#e6e6e6 !important;
  -webkit-text-fill-color:#e6e6e6 !important;
}

/* ==========================================
   PAGINATION
   ========================================== */
.pagination li a{
  background:#0f1622 !important;
  border:1px solid rgba(255,255,255,0.12) !important;
  color:#e6e6e6 !important;
}
.pagination li a:hover{
  background:#58a6ff !important;
  color:#0d1117 !important;
}
.pagination .active a{
  background:#58a6ff !important;
  color:#0d1117 !important;
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
  border:none;
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

/* ==========================================
   MODAL
   ========================================== */
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

</style>
</head>

<body class="bg-light">

<?php require('inc/header.php'); ?>

<div class="container-fluid" id="main-content">
<div class="row">
<div class="col-lg-10 ms-auto p-4">

<h3 class="mb-4">Thống kê</h3>

<div class="card mb-4">
  <div class="card-body">

    <!-- SEARCH -->
    <div class="text-end mb-4">
      <input type="text" 
             id="search_input" 
             oninput="get_bookings(this.value)" 
             class="form-control shadow-none w-25 ms-auto" 
             placeholder="Nhập để tìm kiếm...">
    </div>

    <!-- TABLE -->
    <div class="table-responsive">
      <table class="table table-hover border" style="min-width:1200px;">
        <thead>
          <tr>
            <th>#</th>
            <th>Thông tin người dùng</th>
            <th>Thông tin phòng</th>
            <th>Thông tin đặt phòng</th>
            <th>Tình trạng</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody id="table-data"></tbody>
      </table>
    </div>

    <!-- PAGINATION -->
    <nav>
      <ul class="pagination mt-3" id="table-pagination"></ul>
    </nav>

  </div>
</div>

</div>
</div>
</div>

<?php require('inc/scripts.php'); ?>
<script src="scripts/booking_records.js"></script>

</body>
</html>

