<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();

// session + CSRF
if(session_status() !== PHP_SESSION_ACTIVE) session_start();
if(!isset($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Trang quản lý - Người dùng</title>
<?php require('inc/links.php'); ?>
<link rel="stylesheet" href="assets/css/admin-dark.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
/* ...giữ CSS hiện tại của bạn (đã có ở file) ... */
</style>
</head>
<body class="bg-light">
<?php require('inc/header.php'); ?>

<div class="container-fluid" id="main-content">
  <div class="row">
    <div class="col-lg-10 ms-auto p-4">
      <h3 class="mb-4">Danh sách người dùng</h3>

      <div class="card mb-4">
        <div class="card-body">

          <!-- SEARCH + PER PAGE -->
          <div class="d-flex mb-4">
            <input id="search-input" type="text" oninput="search_user(this.value)"
                   class="form-control shadow-none w-25" placeholder="Nhập tên / email / số điện thoại...">
            <div class="ms-auto d-flex align-items-center gap-2">
              <label class="text-muted mb-0">Hiển thị</label>
              <select id="per-page" class="form-select form-select-sm" style="width:100px;">
                <option value="10">10</option>
                <option value="25" selected>25</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>
            </div>
          </div>

          <!-- TABLE -->
          <div class="table-responsive">
            <table class="table table-hover border text-center" style="min-width: 1100px;">
              <thead>
                <tr>
                  <th>#</th><th>Họ và tên</th><th>Email</th><th>Số điện thoại</th>
                  <th>Địa chỉ</th><th>Ngày sinh</th><th>Giới tính</th><th>Trạng thái</th>
                  <th>Ngày tạo</th><th>Thao tác</th>
                </tr>
              </thead>
              <tbody id="users-data"></tbody>
            </table>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted" id="users-info">Đang tải...</div>
            <nav aria-label="Pagination">
              <ul class="pagination mb-0" id="users-pagination"></ul>
            </nav>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>
<style>
  :root{
  --bg:#0b0f14;
  --card:#0f1720;
  --muted:#9aa6b2;
  --accent:#1e90ff; /* neon blue */
  --accent-2:#0fb6ff;
  --danger:#ff4d63;
  --success:#28c76f;
  --soft:#12151a;
  --table-border:rgba(255,255,255,0.04);
}

html,body{
  height:100%;
  background:linear-gradient(180deg,var(--bg),#050607) fixed;
  color:#dbe7ef;
  font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

/* Page container/card */
#main-content { padding-top:18px; }
.card{
  background: linear-gradient(180deg, rgba(255,255,255,0.01), rgba(255,255,255,0.0));
  border:1px solid rgba(255,255,255,0.04);
  box-shadow: 0 8px 30px rgba(2,6,12,0.6), inset 0 1px 0 rgba(255,255,255,0.02);
  border-radius:12px;
}

/* Headline neon */
h3.mb-4{
  color:var(--accent);
  text-shadow: 0 0 8px rgba(30,144,255,0.35), 0 2px 12px rgba(0,0,0,0.6);
  letter-spacing:0.6px;
}

/* Search input + selects */
#search-input{
  background: rgba(255,255,255,0.02);
  border:1px solid rgba(255,255,255,0.04);
  color:var(--muted);
  box-shadow:none;
}
#search-input::placeholder{ color: rgba(155,170,185,0.35); }

.form-select{
  background: rgba(255,255,255,0.02);
  border:1px solid rgba(255,255,255,0.04);
  color:var(--muted);
}

/* Table */
.table{
  color:#cfe8ff;
}
.table thead th{
  color:var(--accent);
  border-bottom:1px solid var(--table-border);
  font-weight:700;
  letter-spacing:0.6px;
}
.table tbody td{
  border-top:1px solid rgba(255,255,255,0.02);
  vertical-align:middle;
  color:#bcd7ef;
}
.table-hover tbody tr:hover{
  background: linear-gradient(90deg, rgba(30,144,255,0.03), rgba(15,20,25,0.02));
  transform: translateY(-1px);
}

/* Badges/buttons */
.badge{
  font-weight:600;
  color:#fff;
}
.badge.bg-success{ background:var(--success); color:#01220b; }
.badge.bg-secondary{ background:#3b3f46; color:#e3e9ee; }
.badge.bg-warning{ background:#ffb020; color:#2a1d00; }
.btn-primary{
  background: linear-gradient(180deg,var(--accent),var(--accent-2));
  border: none;
  box-shadow: 0 6px 18px rgba(15,136,255,0.12);
}
.btn-danger{
  background: linear-gradient(180deg,var(--danger),#ff2b4a);
  border: none;
}

/* Action column buttons spacing */
.table td .btn{ margin-right:6px; }

/* Pagination */
.pagination .page-item .page-link{
  background: transparent;
  color: var(--muted);
  border:1px solid transparent;
}
.pagination .page-item.active .page-link{
  background: linear-gradient(90deg,var(--accent),var(--accent-2));
  color:#001322;
  border-radius:6px;
  box-shadow: 0 6px 20px rgba(30,144,255,0.12);
}

/* Users info */
#users-info{ color:var(--muted); }

/* Sidebar active style override */
#dashboard-menu .active-menu{
  background: linear-gradient(90deg, rgba(30,144,255,0.12), rgba(15,136,255,0.06)) !important;
  color: var(--accent) !important;
}

/* small responsive */
@media (max-width:991px){
  .table{ font-size:13px; }
  #search-input{ width:100% !important; }
}

/* subtle utility */
.text-muted{ color:var(--muted) !important; }
</style>
<?php require('inc/scripts.php'); ?>
<script>const CSRF_TOKEN = '<?php echo $csrf_token; ?>';</script>
<script src="scripts/users.js?v=<?php echo time(); ?>"></script>
</body>
</html>