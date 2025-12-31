<?php
require('inc/essentials.php');
require('../admin/inc/db_config.php');
ownerLogin();

$owner_id = getOwnerId();

if (!isset($_GET['id'])) {
    die("Không tìm thấy booking ID!");
}

$booking_id = (int)$_GET['id'];

// Lấy thông tin booking - chỉ phòng của owner
$sql = "SELECT bo.*, 
        bd.*, 
        r.name AS room_name, r.price AS room_price,
        uc.email AS user_email, uc.profile AS user_profile
        FROM booking_order bo
        INNER JOIN booking_details bd ON bo.booking_id = bd.booking_id
        INNER JOIN rooms r ON bo.room_id = r.id
        LEFT JOIN user_cred uc ON bo.user_id = uc.id
        WHERE bo.booking_id = ? AND r.owner_id = ?";

$res = select($sql, [$booking_id, $owner_id], 'ii');
if (!$res || mysqli_num_rows($res) == 0) {
    die("Không tìm thấy dữ liệu đặt phòng hoặc bạn không có quyền xem!");
}

$data = mysqli_fetch_assoc($res);

// Định dạng ngày tháng
date_default_timezone_set("Asia/Ho_Chi_Minh");
$ngay_dat = date("d/m/Y H:i", strtotime($data['datentime']));
$ngay_nhan = date("d/m/Y", strtotime($data['check_in']));
$ngay_tra = date("d/m/Y", strtotime($data['check_out']));

// Tính số đêm
$checkin_dt = new DateTime($data['check_in']);
$checkout_dt = new DateTime($data['check_out']);
$days = $checkout_dt->diff($checkin_dt)->days;
if($days == 0){ $days = 1; }

// Trạng thái
$status_map = [
    "pending" => ["Đang chờ xử lý", "warning"],
    "booked" => ["Đã đặt phòng thành công", "success"],
    "cancelled" => ["Đã hủy phòng", "danger"]
];
[$status_vi, $status_class] = $status_map[$data['booking_status']] ?? ["Không xác định", "secondary"];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chi tiết đặt phòng - <?php echo htmlspecialchars($data['order_id'], ENT_QUOTES, 'UTF-8'); ?></title>
  <?php require('../admin/inc/links.php'); ?>
</head>
<body class="bg-light">
  <div class="container-fluid p-0">
    <div class="row g-0">
      <?php require('inc/header.php'); ?>

      <div class="col-lg-10 p-4" id="main-content">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h4 class="mb-2">
              <i class="bi bi-calendar-check me-2"></i>Chi tiết đặt phòng
            </h4>
            <p class="mb-0 text-muted">Mã đơn: <strong><?php echo htmlspecialchars($data['order_id'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
          </div>
          <div>
            <button onclick="window.print()" class="btn btn-primary">
              <i class="bi bi-printer me-2"></i>In
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
              <i class="bi bi-x-lg me-2"></i>Đóng
            </button>
          </div>
        </div>

        <div class="row g-4">
          <!-- Thông tin khách hàng -->
          <div class="col-md-6">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-person me-2"></i>Thông tin khách hàng</h5>
              </div>
              <div class="card-body">
                <p><strong>Tên:</strong> <?php echo htmlspecialchars($data['user_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>SĐT:</strong> <?php echo htmlspecialchars($data['phonenum'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php if (!empty($data['user_email'])): ?>
                  <p><strong>Email:</strong> <?php echo htmlspecialchars($data['user_email'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
                <?php if (!empty($data['address'])): ?>
                  <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($data['address'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- Thông tin đặt phòng -->
          <div class="col-md-6">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-door-open me-2"></i>Thông tin phòng</h5>
              </div>
              <div class="card-body">
                <p><strong>Phòng:</strong> <?php echo htmlspecialchars($data['room_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php if (!empty($data['room_no'])): ?>
                  <p><strong>Số phòng:</strong> <span class="badge bg-success"><?php echo htmlspecialchars($data['room_no'], ENT_QUOTES, 'UTF-8'); ?></span></p>
                <?php endif; ?>
                <p><strong>Check-in:</strong> <?php echo $ngay_nhan; ?></p>
                <p><strong>Check-out:</strong> <?php echo $ngay_tra; ?></p>
                <p><strong>Số đêm:</strong> <?php echo $days; ?> đêm</p>
                <p><strong>Trạng thái:</strong> 
                  <span class="badge bg-<?php echo $status_class; ?>"><?php echo $status_vi; ?></span>
                </p>
              </div>
            </div>
          </div>

          <!-- Thông tin thanh toán -->
          <div class="col-md-12">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-currency-dollar me-2"></i>Thông tin thanh toán</h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <p><strong>Ngày đặt:</strong> <?php echo $ngay_dat; ?></p>
                    <p><strong>Giá phòng:</strong> <?php echo number_format($data['room_price'] ?? ($data['total_pay'] / $days), 0, ',', '.'); ?> đ/đêm</p>
                    <p><strong>Tạm tính:</strong> <?php echo number_format($data['total_pay'], 0, ',', '.'); ?> đ</p>
                  </div>
                  <div class="col-md-6">
                    <?php if (!empty($data['trans_id'])): ?>
                      <p><strong>Mã giao dịch:</strong> <?php echo htmlspecialchars($data['trans_id'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                    <p><strong>Đã thanh toán:</strong> 
                      <span class="text-success fw-bold">
                        <?php echo number_format($data['trans_amt'] ?? 0, 0, ',', '.'); ?> đ
                      </span>
                    </p>
                    <p><strong>Trạng thái thanh toán:</strong> 
                      <?php 
                      $trans_status = strtolower($data['trans_status'] ?? 'pending');
                      $trans_class = ($trans_status == 'paid' || $trans_status == 'txn_success') ? 'success' : 'warning';
                      $trans_text = ($trans_status == 'paid' || $trans_status == 'txn_success') ? 'Đã thanh toán' : 'Chưa thanh toán';
                      ?>
                      <span class="badge bg-<?php echo $trans_class; ?>"><?php echo $trans_text; ?></span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <?php require('../admin/inc/scripts.php'); ?>
</body>
</html>

