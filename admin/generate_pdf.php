<?php
require('inc/essentials.php');
require('inc/db_config.php');
require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

adminLogin();

/* ==============================
   DOMPDF CONFIG – FONT + IMAGE
================================= */
$fontDir  = __DIR__ . "/../fonts";
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Serif');   // ⭐ FONT ĐẸP – CHUẨN TV
$options->set('fontDir', $fontDir);
$options->set('fontCache', $fontDir);
$options->setChroot(__DIR__ . "/..");

$pdf = new Dompdf($options);

/* ==============================
   CHECK BOOKING ID
================================= */
if (!isset($_GET['id'])) {
    die("Không tìm thấy booking ID!");
}

$booking_id = $_GET['id'];

/* ==============================
   GET DATABASE
================================= */
$sql = "SELECT bo.*, 
        bd.user_name, bd.phonenum, bd.address, bd.total_pay, bd.room_no,
        r.name AS room_name
        FROM booking_order bo
        INNER JOIN booking_details bd ON bo.booking_id = bd.booking_id
        INNER JOIN rooms r ON bo.room_id = r.id
        WHERE bo.booking_id = ?";

$res = select($sql, [$booking_id], 'i');
if (mysqli_num_rows($res) == 0) {
    die("Không tìm thấy dữ liệu đặt phòng!");
}

$data = mysqli_fetch_assoc($res);

/* ==============================
   DATE + PAYMENT
================================= */
date_default_timezone_set("Asia/Ho_Chi_Minh");

$ngay_dat  = date("d/m/Y H:i", strtotime($data['datentime']));
$ngay_nhan = date("d/m/Y", strtotime($data['check_in']));
$ngay_tra  = date("d/m/Y", strtotime($data['check_out']));
$today     = date("d/m/Y H:i");

$days  = (strtotime($data['check_out']) - strtotime($data['check_in'])) / 86400;
$price = number_format($data['total_pay'] / $days, 0, ',', '.');
$total = number_format($data['total_pay'], 0, ',', '.');
$vat   = number_format($data['total_pay'] * 0.1, 0, ',', '.');
$final = number_format($data['total_pay'] * 1.1, 0, ',', '.');

/* ==============================
   STATUS COLOR
================================= */
$status_color = [
    "pending"   => ["Đang chờ xử lý", "#f1c40f"],
    "booked"    => ["Đã đặt phòng thành công", "#27ae60"],
    "cancelled" => ["Đã hủy phòng", "#e74c3c"]
];

[$status_vi, $color] = $status_color[$data['booking_status']] ?? ["Không xác định","#7f8c8d"];

/* ==============================
   ABSOLUTE PATHS
================================= */
$logo = __DIR__ . "/../images/logo.png";
$signature = __DIR__ . "/../images/Chuky.png";

/* ==============================
   HTML TEMPLATE
================================= */

$html = "
<style>
    body { 
        font-family: 'DejaVu Serif'; 
        font-size: 14px; 
        margin: 25px; 
    }

    .header-box {
        text-align: center;
        background: #0a3d62;
        color: white;
        padding: 15px 0;
        border-radius: 6px;
        margin-bottom: 25px;
    }

    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    th, td { padding: 10px; border: 1px solid #dcdde1; }
    th { background:#f1f2f6; }

    .status-box {
        padding: 6px 12px;
        color: white;
        font-weight: bold;
        border-radius: 4px;
        background: {$color};
    }

    .note-box{
        margin-top:25px;
        padding:12px;
        background:#f8f9fa;
        border-left:4px solid #0a3d62;
    }

    .signature {
        margin-top: 50px;
        text-align: right;
    }
</style>

<div class='header-box'>
    <h2>HÓA ĐƠN THANH TOÁN</h2>
    <h3>VĨNH LONG HOTEL</h3>
    <p>An Trường, Càng Long, Trà Vinh • Hotline: 0823 521 928</p>
</div>

<div style='text-align:center; margin-bottom:20px;'>
    <img src='$logo' width='90'>
</div>

<p><b>Ngày tạo hóa đơn:</b> $today</p>

<table>
    <tr><th>Mã đơn</th><td>{$data['order_id']}</td></tr>
    <tr><th>Khách hàng</th><td>{$data['user_name']}</td></tr>
    <tr><th>Số điện thoại</th><td>{$data['phonenum']}</td></tr>
    <tr><th>Địa chỉ</th><td>{$data['address']}</td></tr>
    <tr><th>Số phòng</th><td>{$data['room_no']}</td></tr>
    <tr><th>Loại phòng</th><td>{$data['room_name']}</td></tr>
    <tr><th>Giá phòng</th><td>$price VNĐ / đêm</td></tr>
    <tr><th>Số ngày thuê</th><td>$days ngày</td></tr>
    <tr><th>Nhận phòng</th><td>$ngay_nhan</td></tr>
    <tr><th>Trả phòng</th><td>$ngay_tra</td></tr>
    <tr><th>Trạng thái</th><td><span class='status-box'>$status_vi</span></td></tr>
</table>

<h3 style='margin-top:25px;'>Tổng kết thanh toán</h3>

<table>
    <tr><th>Tạm tính</th><td>$total VNĐ</td></tr>
    <tr><th>VAT (10%)</th><td>$vat VNĐ</td></tr>
    <tr><th>Thành tiền</th><td><b>$final VNĐ</b></td></tr>
</table>

<div class='note-box'>
    <b>Lưu ý:</b><br>
    • Check-in: 14:00 — Check-out: 12:00<br>
    • Xuất trình CCCD khi nhận phòng<br>
    • Hạn chế tiếng ồn sau 22:00<br>
    • Liên hệ lễ tân: 0823 521 928
</div>

<div class='signature'>
    <b>Đại diện khách sạn</b><br>
    </b><br>
    <img src='$signature' width='150'><br>   <!-- ⭐ THÊM CHỮ KÝ -->
    <small>(Đã ký)</small>
</div>
";

$pdf->loadHtml($html);
$pdf->setPaper('A4');
$pdf->render();
$pdf->stream("HOADON_".$data['order_id'].".pdf", ["Attachment" => true]);
?>
