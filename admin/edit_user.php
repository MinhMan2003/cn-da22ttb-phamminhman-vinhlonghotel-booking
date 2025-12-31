<?php

require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();

if(session_status() !== PHP_SESSION_ACTIVE) session_start();
if(!isset($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf_token'];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if(!$id){ echo 'ID không hợp lệ'; exit; }

$err = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $token = $_POST['csrf_token'] ?? '';
    if(!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)){
        $err = 'CSRF token không hợp lệ';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phonenum'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $dob = trim($_POST['dob'] ?? '');
        $pincode = isset($_POST['pincode']) ? (int)$_POST['pincode'] : 0;
        $gender = trim($_POST['gender'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
        $is_verified = isset($_POST['is_verified']) ? (int)$_POST['is_verified'] : 0;

        if($name === ''){
            $err = 'Tên không được để trống';
        } elseif($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)){
            $err = 'Email không hợp lệ';
        } else {
            // Kiểm tra xem cột gender có tồn tại không
            $check_gender = $con->query("SHOW COLUMNS FROM `user_cred` LIKE 'gender'");
            $has_gender = $check_gender && mysqli_num_rows($check_gender) > 0;
            
            // Kiểm tra email trùng (trừ user hiện tại)
            $check_email = $con->prepare("SELECT id FROM `user_cred` WHERE email=? AND id!=? LIMIT 1");
            $check_email->bind_param("si", $email, $id);
            $check_email->execute();
            $email_result = $check_email->get_result();
            if($email_result && mysqli_num_rows($email_result) > 0){
                $err = 'Email này đã được sử dụng bởi người dùng khác!';
                $check_email->close();
            } else {
                $check_email->close();
                
                // Kiểm tra số điện thoại trùng (trừ user hiện tại)
                $check_phone = $con->prepare("SELECT id FROM `user_cred` WHERE phonenum=? AND id!=? LIMIT 1");
                $check_phone->bind_param("si", $phone, $id);
                $check_phone->execute();
                $phone_result = $check_phone->get_result();
                if($phone_result && mysqli_num_rows($phone_result) > 0){
                    $err = 'Số điện thoại này đã được sử dụng bởi người dùng khác!';
                    $check_phone->close();
                } else {
                    $check_phone->close();
                    
                    // Xây dựng query UPDATE
                    if(!empty($password)){
                        // Có đổi mật khẩu
                        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                        if($has_gender){
                            $query = "UPDATE `user_cred` SET `name`=?, `email`=?, `phonenum`=?, `address`=?, `dob`=?, `pincode`=?, `gender`=?, `password`=?, `status`=?, `is_verified`=? WHERE id = ?";
                            $stmt = $con->prepare($query);
                            $stmt->bind_param("sssssissiii", $name, $email, $phone, $address, $dob, $pincode, $gender, $hashed_password, $status, $is_verified, $id);
                        } else {
                            $query = "UPDATE `user_cred` SET `name`=?, `email`=?, `phonenum`=?, `address`=?, `dob`=?, `pincode`=?, `password`=?, `status`=?, `is_verified`=? WHERE id = ?";
                            $stmt = $con->prepare($query);
                            $stmt->bind_param("sssssissii", $name, $email, $phone, $address, $dob, $pincode, $hashed_password, $status, $is_verified, $id);
                        }
                    } else {
                        // Không đổi mật khẩu
                        if($has_gender){
                            $query = "UPDATE `user_cred` SET `name`=?, `email`=?, `phonenum`=?, `address`=?, `dob`=?, `pincode`=?, `gender`=?, `status`=?, `is_verified`=? WHERE id = ?";
                            $stmt = $con->prepare($query);
                            $stmt->bind_param("sssssissii", $name, $email, $phone, $address, $dob, $pincode, $gender, $status, $is_verified, $id);
                        } else {
                            $query = "UPDATE `user_cred` SET `name`=?, `email`=?, `phonenum`=?, `address`=?, `dob`=?, `pincode`=?, `status`=?, `is_verified`=? WHERE id = ?";
                            $stmt = $con->prepare($query);
                            $stmt->bind_param("sssssissi", $name, $email, $phone, $address, $dob, $pincode, $status, $is_verified, $id);
                        }
                    }
                    
                    if($stmt->execute()){
                        $stmt->close();
                        header('Location: users.php?msg=updated'); exit;
                    } else {
                        $err = 'Lỗi khi cập nhật: ' . mysqli_error($con);
                        $stmt->close();
                    }
                }
            }
        }
    }
}

// load user
$stmt = $con->prepare("SELECT * FROM `user_cred` WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();
if(!$user){ echo 'Không tìm thấy'; exit; }
?>
<!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8">
<title>Chỉnh sửa người dùng</title>
<?php require('inc/links.php'); ?>
<link rel="stylesheet" href="assets/css/admin-dark.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
<?php require('inc/header.php'); ?>

<div class="container-fluid" id="main-content">
  <div class="row">
    <div class="col-lg-10 ms-auto p-4">
      <div class="card mb-4">
        <div class="card-body">
          <h4 class="mb-3">Chỉnh sửa người dùng #<?php echo $id; ?></h4>

          <?php if($err): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
          <?php endif; ?>

          <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">
                  <i class="bi bi-person-fill me-2"></i>Họ và tên <span class="text-danger">*</span>
                </label>
                <input class="form-control" name="name" required value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">
                  <i class="bi bi-envelope-fill me-2"></i>Email <span class="text-danger">*</span>
                </label>
                <input class="form-control" name="email" type="email" required value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">
                  <i class="bi bi-telephone-fill me-2"></i>Số điện thoại
                </label>
                <input class="form-control" name="phonenum" value="<?php echo htmlspecialchars($user['phonenum'] ?? ($user['phone'] ?? '')); ?>">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">
                  <i class="bi bi-calendar-event-fill me-2"></i>Ngày sinh
                </label>
                <input class="form-control" name="dob" type="date" value="<?php echo htmlspecialchars($user['dob'] ?? ''); ?>">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">
                  <i class="bi bi-geo-alt-fill me-2"></i>Địa chỉ
                </label>
                <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">
                  <i class="bi bi-shield-check-fill me-2"></i>Mã bưu điện
                </label>
                <input class="form-control" name="pincode" type="number" value="<?php echo htmlspecialchars($user['pincode'] ?? '0'); ?>">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">
                  <i class="bi bi-gender-ambiguous me-2"></i>Giới tính
                </label>
                <select class="form-select" name="gender">
                  <option value="">-- Chọn giới tính --</option>
                  <option value="male" <?php echo (isset($user['gender']) && $user['gender'] == 'male') ? 'selected' : ''; ?>>Nam</option>
                  <option value="female" <?php echo (isset($user['gender']) && $user['gender'] == 'female') ? 'selected' : ''; ?>>Nữ</option>
                </select>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">
                  <i class="bi bi-lock-fill me-2"></i>Mật khẩu mới (để trống nếu không đổi)
                </label>
                <input class="form-control" name="password" type="password" placeholder="Nhập mật khẩu mới">
                <small class="text-muted">Tối thiểu 6 ký tự</small>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">
                  <i class="bi bi-shield-check-fill me-2"></i>Trạng thái xác minh
                </label>
                <select class="form-select" name="is_verified">
                  <option value="0" <?php echo (empty($user['is_verified']) || $user['is_verified'] == 0) ? 'selected' : ''; ?>>Chưa xác minh</option>
                  <option value="1" <?php echo (!empty($user['is_verified']) && $user['is_verified'] == 1) ? 'selected' : ''; ?>>Đã xác minh</option>
                </select>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">
                  <i class="bi bi-toggle-on me-2"></i>Trạng thái tài khoản
                </label>
                <select class="form-select" name="status">
                  <option value="1" <?php echo (isset($user['status']) && $user['status'] == 1) ? 'selected' : ''; ?>>Hoạt động</option>
                  <option value="0" <?php echo (empty($user['status']) || $user['status'] == 0) ? 'selected' : ''; ?>>Không hoạt động</option>
                </select>
              </div>
            </div>

            <div class="mt-4 pt-3 border-top">
              <div class="d-flex gap-2">
                <button class="btn btn-primary" type="submit">
                  <i class="bi bi-check-circle-fill me-2"></i>Lưu thay đổi
                </button>
                <a class="btn btn-secondary" href="users.php">
                  <i class="bi bi-x-circle me-2"></i>Hủy
                </a>
              </div>
            </div>
          </form>
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
/* ...existing code... */

@media (min-width: 992px){
  /* center form card and increase contrast */
  #main-content .card {
    max-width: 900px;
    margin: 0 auto;
    border-radius: 12px;
    padding: 0;
    overflow: hidden;
  }
  #main-content .card .card-body{
    padding: 28px 34px;
    background: linear-gradient(180deg, rgba(255,255,255,0.015), rgba(255,255,255,0.01));
  }
}

/* form tweaks */
.form-label { color: #9fb6c9; font-weight:600; }
.form-control {
  background: rgba(255,255,255,0.03);
  border:1px solid rgba(255,255,255,0.04);
  color:#e8f6ff;
  height:46px;
  padding:10px 14px;
}
.form-control::placeholder { color: rgba(180,200,215,0.35); }

/* smaller label spacing */
.mb-3 { margin-bottom: 14px !important; }

/* action row */
.d-flex .btn { min-width:84px; }

/* heading */
.card h4 { color: var(--accent); margin-bottom: 18px; font-weight:700; }

/* badges */
.badge { font-size: 0.86rem; padding: .32rem .55rem; border-radius:6px; }

/* reduce sidebar overlap on narrow screens */
@media (max-width: 1199px){
  #dashboard-menu{ z-index:9999; }
  #main-content{ padding-left:20px; padding-right:20px; }
}

/* optional: soften card border */
#main-content .card { border:1px solid rgba(255,255,255,0.03); }
</style>
<?php require('inc/scripts.php'); ?>
</body>
</html>