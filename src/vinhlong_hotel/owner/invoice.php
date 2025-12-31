<?php
require('inc/essentials.php');
require('../admin/inc/db_config.php');
ownerLogin();

$owner_id = getOwnerId();

// Kiểm tra booking ID
if (!isset($_GET['id'])) {
    die("Không tìm thấy booking ID!");
}

$booking_id = (int)$_GET['id'];

// Lấy thông tin booking - chỉ phòng của owner
$sql = "SELECT bo.*, 
        bd.user_name, bd.phonenum, bd.address, bd.total_pay, bd.room_no,
        r.name AS room_name, r.price AS room_price,
        uc.email AS user_email
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

// Lấy thông tin owner
$owner_info = select("SELECT * FROM hotel_owners WHERE id=?", [$owner_id], 'i');
$owner_data = mysqli_fetch_assoc($owner_info);

// Định dạng ngày tháng
date_default_timezone_set("Asia/Ho_Chi_Minh");
$ngay_dat  = date("d/m/Y H:i", strtotime($data['datentime']));
$ngay_nhan = date("d/m/Y", strtotime($data['check_in']));
$ngay_tra  = date("d/m/Y", strtotime($data['check_out']));
$today     = date("d/m/Y H:i");

// Tính số đêm
$checkin_dt = new DateTime($data['check_in']);
$checkout_dt = new DateTime($data['check_out']);
$days = $checkout_dt->diff($checkin_dt)->days;
if($days == 0){ $days = 1; }

// Tính tiền
$price_per_night = $data['room_price'] ?? ($data['total_pay'] / $days);
$subtotal = $data['total_pay'];
$vat = $subtotal * 0.1;
$total = $subtotal + $vat;

// Định dạng tiền
$price_fmt = number_format($price_per_night, 0, ',', '.');
$subtotal_fmt = number_format($subtotal, 0, ',', '.');
$vat_fmt = number_format($vat, 0, ',', '.');
$total_fmt = number_format($total, 0, ',', '.');

// Trạng thái
$status_map = [
    "pending" => ["Đang chờ xử lý", "#f1c40f"],
    "booked" => ["Đã đặt phòng thành công", "#27ae60"],
    "cancelled" => ["Đã hủy phòng", "#e74c3c"]
];
[$status_vi, $status_color] = $status_map[$data['booking_status']] ?? ["Không xác định", "#7f8c8d"];

// Thông tin khách sạn
$hotel_name = htmlspecialchars($owner_data['hotel_name'] ?? 'Khách sạn', ENT_QUOTES, 'UTF-8');
$hotel_address = htmlspecialchars($owner_data['address'] ?? '', ENT_QUOTES, 'UTF-8');
$hotel_phone = htmlspecialchars($owner_data['phonenum'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hóa đơn - <?php echo $data['order_id']; ?></title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Arial', 'DejaVu Sans', sans-serif;
      font-size: 14px;
      line-height: 1.6;
      color: #333;
      background: #f5f5f5;
      padding: 20px;
    }
    
    .invoice-container {
      max-width: 800px;
      margin: 0 auto;
      background: white;
      padding: 40px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    
    .header {
      text-align: center;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 30px 20px;
      border-radius: 8px;
      margin-bottom: 30px;
    }
    
    .header h1 {
      font-size: 28px;
      margin-bottom: 10px;
    }
    
    .header h2 {
      font-size: 20px;
      font-weight: normal;
      margin-bottom: 5px;
    }
    
    .header p {
      font-size: 14px;
      opacity: 0.9;
    }
    
    .info-section {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
      margin-bottom: 30px;
    }
    
    .info-box {
      background: #f8f9fa;
      padding: 15px;
      border-radius: 6px;
      border-left: 4px solid #667eea;
    }
    
    .info-box h3 {
      font-size: 16px;
      margin-bottom: 10px;
      color: #667eea;
    }
    
    .info-box p {
      margin: 5px 0;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
    }
    
    table th,
    table td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    
    table th {
      background: #f8f9fa;
      font-weight: bold;
      color: #333;
    }
    
    table tr:last-child td {
      border-bottom: none;
    }
    
    .status-badge {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 4px;
      color: white;
      font-weight: bold;
      background: <?php echo $status_color; ?>;
    }
    
    .total-section {
      margin-top: 30px;
      padding: 20px;
      background: #f8f9fa;
      border-radius: 6px;
    }
    
    .total-row {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid #ddd;
    }
    
    .total-row:last-child {
      border-bottom: none;
      font-size: 18px;
      font-weight: bold;
      color: #667eea;
      margin-top: 10px;
      padding-top: 15px;
    }
    
    .note-box {
      margin-top: 30px;
      padding: 15px;
      background: #fff3cd;
      border-left: 4px solid #ffc107;
      border-radius: 4px;
    }
    
    .note-box h4 {
      margin-bottom: 10px;
      color: #856404;
    }
    
    .note-box ul {
      margin-left: 20px;
      color: #856404;
    }
    
    .signature {
      margin-top: 50px;
      text-align: right;
    }
    
    .print-btn {
      text-align: center;
      margin: 20px 0;
    }
    
    .print-btn button {
      background: #667eea;
      color: white;
      border: none;
      padding: 12px 30px;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s;
    }
    
    .print-btn button:hover {
      background: #5568d3;
    }
    
    @media print {
      body {
        background: white;
        padding: 0;
      }
      
      .invoice-container {
        box-shadow: none;
        padding: 20px;
      }
      
      .print-btn {
        display: none;
      }
      
      @page {
        margin: 1cm;
      }
    }
  </style>
</head>
<body>
  <div class="print-btn">
    <button onclick="window.print()">
      <i class="bi bi-printer"></i> In hóa đơn
    </button>
    <button onclick="window.close()" style="background: #6c757d; margin-left: 10px;">
      Đóng
    </button>
  </div>

  <div class="invoice-container">
    <div class="header">
      <h1>HÓA ĐƠN THANH TOÁN</h1>
      <h2><?php echo $hotel_name; ?></h2>
      <?php if ($hotel_address): ?>
        <p><?php echo $hotel_address; ?></p>
      <?php endif; ?>
      <?php if ($hotel_phone): ?>
        <p>Hotline: <?php echo $hotel_phone; ?></p>
      <?php endif; ?>
    </div>

    <div class="info-section">
      <div class="info-box">
        <h3>Thông tin khách hàng</h3>
        <p><strong>Tên:</strong> <?php echo htmlspecialchars($data['user_name'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>SĐT:</strong> <?php echo htmlspecialchars($data['phonenum'], ENT_QUOTES, 'UTF-8'); ?></p>
        <?php if (!empty($data['user_email'])): ?>
          <p><strong>Email:</strong> <?php echo htmlspecialchars($data['user_email'], ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if (!empty($data['address'])): ?>
          <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($data['address'], ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
      </div>

      <div class="info-box">
        <h3>Thông tin đặt phòng</h3>
        <p><strong>Mã đơn:</strong> <?php echo htmlspecialchars($data['order_id'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Ngày đặt:</strong> <?php echo $ngay_dat; ?></p>
        <p><strong>Check-in:</strong> <?php echo $ngay_nhan; ?></p>
        <p><strong>Check-out:</strong> <?php echo $ngay_tra; ?></p>
        <p><strong>Trạng thái:</strong> <span class="status-badge"><?php echo $status_vi; ?></span></p>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>Mô tả</th>
          <th style="text-align: right;">Số lượng</th>
          <th style="text-align: right;">Đơn giá</th>
          <th style="text-align: right;">Thành tiền</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <strong><?php echo htmlspecialchars($data['room_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
            <?php if (!empty($data['room_no'])): ?>
              <br><small>Số phòng: <?php echo htmlspecialchars($data['room_no'], ENT_QUOTES, 'UTF-8'); ?></small>
            <?php endif; ?>
          </td>
          <td style="text-align: right;"><?php echo $days; ?> đêm</td>
          <td style="text-align: right;"><?php echo $price_fmt; ?> đ</td>
          <td style="text-align: right;"><?php echo $subtotal_fmt; ?> đ</td>
        </tr>
      </tbody>
    </table>

    <div class="total-section">
      <div class="total-row">
        <span>Tạm tính:</span>
        <span><?php echo $subtotal_fmt; ?> đ</span>
      </div>
      <div class="total-row">
        <span>VAT (10%):</span>
        <span><?php echo $vat_fmt; ?> đ</span>
      </div>
      <div class="total-row">
        <span>Tổng cộng:</span>
        <span><?php echo $total_fmt; ?> đ</span>
      </div>
    </div>

    <?php if (!empty($data['trans_id'])): ?>
      <div style="margin-top: 20px; padding: 15px; background: #e7f3ff; border-radius: 6px;">
        <p><strong>Mã giao dịch:</strong> <?php echo htmlspecialchars($data['trans_id'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Đã thanh toán:</strong> <?php echo number_format($data['trans_amt'] ?? 0, 0, ',', '.'); ?> đ</p>
      </div>
    <?php endif; ?>

    <div class="note-box">
      <h4>Lưu ý:</h4>
      <ul>
        <li>Check-in: 14:00 — Check-out: 12:00</li>
        <li>Xuất trình CCCD/CMND khi nhận phòng</li>
        <li>Hạn chế tiếng ồn sau 22:00</li>
        <?php if ($hotel_phone): ?>
          <li>Liên hệ lễ tân: <?php echo $hotel_phone; ?></li>
        <?php endif; ?>
      </ul>
    </div>

    <div class="signature">
      <p><strong>Đại diện khách sạn</strong></p>
      <br><br>
      <p>_________________________</p>
      <p><small>(Ký và ghi rõ họ tên)</small></p>
    </div>

    <div style="text-align: center; margin-top: 30px; color: #999; font-size: 12px;">
      <p>Hóa đơn được tạo tự động từ hệ thống quản lý khách sạn</p>
      <p>Ngày in: <?php echo $today; ?></p>
    </div>
  </div>

  <script>
    // Tự động in khi mở trang (tùy chọn)
    // window.onload = function() {
    //   window.print();
    // }
  </script>
</body>
</html>

