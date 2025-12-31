<?php
$current_lang = 'vi';
$lang_from_url = isset($_GET['_lang']) ? trim($_GET['_lang']) : '';
if ($lang_from_url === 'en' || $lang_from_url === 'vi') {
  setcookie('lang', $lang_from_url, time() + (365 * 24 * 60 * 60), '/', '', false, true);
  $_COOKIE['lang'] = $lang_from_url;
  $current_lang = $lang_from_url;
}

require('inc/links.php');
require('inc/header.php');

if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
  redirect('index.php');
}

function t_payment_history($key, $lang = 'vi') {
  $translations = [
    'vi' => [
      'paymentHistory.pageTitle' => 'Lịch sử thanh toán',
      'paymentHistory.home' => 'Trang chủ',
      'paymentHistory.paymentHistory' => 'Lịch sử thanh toán',
      'paymentHistory.totalPaid' => 'Tổng đã thanh toán',
      'paymentHistory.totalTransactions' => 'Tổng giao dịch',
      'paymentHistory.noPayments' => 'Chưa có lịch sử thanh toán',
      'paymentHistory.noPaymentsDesc' => 'Bạn chưa có giao dịch thanh toán nào. Hãy đặt phòng và thanh toán để xem lịch sử tại đây!',
      'paymentHistory.orderId' => 'Mã đơn',
      'paymentHistory.paymentDate' => 'Ngày thanh toán',
      'paymentHistory.status' => 'Trạng thái',
      'paymentHistory.amount' => 'Số tiền',
      'paymentHistory.roomName' => 'Tên phòng',
      'paymentHistory.checkIn' => 'Nhận phòng',
      'paymentHistory.checkOut' => 'Trả phòng',
      'paymentHistory.statusSuccess' => 'Thành công',
      'paymentHistory.statusPending' => 'Đang chờ',
      'paymentHistory.statusFailed' => 'Thất bại',
      'paymentHistory.viewDetails' => 'Xem chi tiết',
      'paymentHistory.transactionId' => 'Mã giao dịch',
      'paymentHistory.checkIn' => 'Nhận phòng',
      'paymentHistory.checkOut' => 'Trả phòng',
      'paymentHistory.nights' => 'Số đêm',
      'paymentHistory.pricePerNight' => 'Giá/đêm',
      'paymentHistory.booking' => 'Đặt phòng',
      'paymentHistory.nightsUnit' => 'đêm',
    ],
    'en' => [
      'paymentHistory.pageTitle' => 'Payment History',
      'paymentHistory.home' => 'Home',
      'paymentHistory.paymentHistory' => 'Payment History',
      'paymentHistory.totalPaid' => 'Total Paid',
      'paymentHistory.totalTransactions' => 'Total Transactions',
      'paymentHistory.noPayments' => 'No Payment History',
      'paymentHistory.noPaymentsDesc' => 'You have no payment transactions yet. Book a room and make a payment to see your history here!',
      'paymentHistory.orderId' => 'Order ID',
      'paymentHistory.paymentDate' => 'Payment Date',
      'paymentHistory.status' => 'Status',
      'paymentHistory.amount' => 'Amount',
      'paymentHistory.roomName' => 'Room Name',
      'paymentHistory.checkIn' => 'Check-in',
      'paymentHistory.checkOut' => 'Check-out',
      'paymentHistory.statusSuccess' => 'Success',
      'paymentHistory.statusPending' => 'Pending',
      'paymentHistory.statusFailed' => 'Failed',
      'paymentHistory.viewDetails' => 'View Details',
      'paymentHistory.transactionId' => 'Transaction ID',
      'paymentHistory.checkIn' => 'Check-in',
      'paymentHistory.checkOut' => 'Check-out',
      'paymentHistory.nights' => 'Nights',
      'paymentHistory.pricePerNight' => 'Price/Night',
      'paymentHistory.booking' => 'Booking',
      'paymentHistory.nightsUnit' => 'nights',
    ]
  ];
  return $translations[$lang][$key] ?? $translations['vi'][$key] ?? $key;
}

// Query lịch sử thanh toán - chỉ đặt phòng
$booking_payment_query = "SELECT 
    bo.booking_id as id,
    bo.order_id,
    bo.user_id,
    bo.trans_amt as amount,
    bo.trans_status as payment_status,
    bo.trans_id,
    bo.trans_resp_msg,
    bo.datentime as payment_date,
    bd.room_name,
    bd.price,
    bo.check_in,
    bo.check_out
  FROM `booking_order` bo
  INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
  WHERE bo.user_id = ? 
  AND bo.trans_amt IS NOT NULL 
  AND bo.trans_amt > 0
  ORDER BY payment_date DESC";

$payment_result = select($booking_payment_query, [$_SESSION['uId']], 'i');

// Khởi tạo biến trước khi sử dụng
$has_payments = false;
$total_paid = 0;
$total_count = 0;

if($payment_result && mysqli_num_rows($payment_result) > 0) {
  $has_payments = true;
  mysqli_data_seek($payment_result, 0); // Reset pointer
  
  // Tính tổng số tiền đã thanh toán
  while($row = mysqli_fetch_assoc($payment_result)) {
    $amount = isset($row['amount']) ? (float)$row['amount'] : 0;
    $total_paid += $amount;
    $total_count++;
  }
  mysqli_data_seek($payment_result, 0); // Reset lại để hiển thị
}

$total_paid_format = number_format($total_paid, 0, ',', '.');
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $settings_r['site_title'] ?> - <?php echo t_payment_history('paymentHistory.pageTitle', $current_lang); ?></title>
  
  <style>
    /* Modern Payment History Page */
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
    }
    
    /* Header */
    .bookings-header h2 {
      font-size: 2.5rem;
      font-weight: 800;
      color: #1f2937;
      margin-bottom: 0.5rem;
    }
    
    .breadcrumb-modern {
      font-size: 14px;
    }
    
    .breadcrumb-modern a {
      color: #6b7280;
      text-decoration: none;
      transition: color 0.3s;
    }
    
    .breadcrumb-modern a:hover {
      color: #1f2937;
    }
    
    .booking-info-item {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      margin-bottom: 0.75rem;
      padding: 0.5rem;
      background: #f9fafb;
      border-radius: 10px;
    }
    
    .booking-info-item i {
      color: #1f2937;
      font-size: 18px;
      margin-top: 2px;
    }
    
    .booking-info-item .label {
      font-weight: 600;
      color: #374151;
      min-width: 100px;
    }
    
    .booking-info-item .value {
      color: #1f2937;
      flex: 1;
    }
    
    /* Payment Card */
    .payment-card {
      background: #ffffff;
      border-radius: 20px;
      padding: 1.75rem;
      box-shadow: 0 10px 40px rgba(0,0,0,0.08);
      border: 1px solid rgba(229,231,235,0.5);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      height: 100%;
      display: flex;
      flex-direction: column;
    }
    
    .payment-card:hover {
      box-shadow: 0 16px 50px rgba(0,0,0,0.12);
      transform: translateY(-4px);
    }
    
    .payment-card-header {
      border-bottom: 2px solid #e5e7eb;
      padding-bottom: 1rem;
      margin-bottom: 1rem;
    }
    
    .payment-card-header h5 {
      font-size: 1.35rem;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 0.5rem;
    }
    
    .payment-amount {
      font-size: 1.5rem;
      font-weight: 700;
      color: #059669;
    }
    
    .payment-status-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 16px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .payment-status-badge.success {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(16,185,129,0.3);
    }
    
    .payment-status-badge.pending {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(245,158,11,0.3);
    }
    
    .payment-status-badge.failed {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(239,68,68,0.3);
    }
    
    /* Empty State */
    .empty-bookings {
      text-align: center;
      padding: 4rem 2rem;
      background: #ffffff;
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    }
    
    .empty-bookings i {
      font-size: 5rem;
      color: #d1d5db;
      margin-bottom: 1.5rem;
    }
    
    .empty-bookings h4 {
      color: #6b7280;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    
    .empty-bookings p {
      color: #9ca3af;
    }
    
    /* Payment Summary */
    .payment-summary-card {
      background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
      border: 2px solid #e5e7eb;
      border-radius: 16px;
      padding: 1.25rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    
    /* View Details Button */
    .btn-view-details {
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
      border: 2px solid #0f5d7a;
    }
    
    .btn-view-details:hover {
      background: linear-gradient(135deg, #0f5d7a 0%, #0b4156 100%);
      color: #ffffff;
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(15,93,122,0.3);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .payment-card {
        padding: 1.25rem;
      }
      
      .bookings-header h2 {
        font-size: 2rem;
      }
    }
  </style>
</head>
<body class="bg-light">

<div class="container">
  <div class="row">
    <div class="col-12 my-5 px-4 bookings-header">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h2 class="fw-bold h-font" data-i18n="paymentHistory.paymentHistory" data-i18n-skip><?php echo t_payment_history('paymentHistory.paymentHistory', $current_lang); ?></h2>
          <div class="breadcrumb-modern">
            <a href="index.php"><i class="bi bi-house-door me-1"></i><span data-i18n="paymentHistory.home" data-i18n-skip><?php echo t_payment_history('paymentHistory.home', $current_lang); ?></span></a>
            <span class="text-secondary mx-2">/</span>
            <span class="text-dark fw-semibold" data-i18n="paymentHistory.paymentHistory" data-i18n-skip><?php echo t_payment_history('paymentHistory.paymentHistory', $current_lang); ?></span>
          </div>
        </div>
        <?php if($has_payments): ?>
        <div class="mt-3 mt-md-0">
          <div class="bg-white rounded-3 p-3 shadow-sm border">
            <div class="text-muted small mb-1" data-i18n="paymentHistory.totalPaid" data-i18n-skip><?php echo t_payment_history('paymentHistory.totalPaid', $current_lang); ?></div>
            <div class="fw-bold text-success fs-5"><?php echo $total_paid_format; ?> VND</div>
            <div class="text-muted small mt-1"><?php echo $total_count; ?> <?php echo $current_lang === 'en' ? 'transactions' : 'giao dịch'; ?></div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

<?php 
  if(!$has_payments): ?>
    <div class="col-12 px-4">
      <div class="empty-bookings">
        <i class="bi bi-credit-card"></i>
        <h4 data-i18n="paymentHistory.noPayments" data-i18n-skip><?php echo t_payment_history('paymentHistory.noPayments', $current_lang); ?></h4>
        <p data-i18n="paymentHistory.noPaymentsDesc" data-i18n-skip><?php echo t_payment_history('paymentHistory.noPaymentsDesc', $current_lang); ?></p>
        <a href="rooms.php" class="btn btn-primary mt-3">
          <i class="bi bi-search me-2"></i><?php echo $current_lang === 'en' ? 'View Available Rooms' : 'Xem phòng trống'; ?>
        </a>
      </div>
    </div>
  <?php else:
    while($payment_data = mysqli_fetch_assoc($payment_result)): 
  // Xử lý ngày thanh toán
  $payment_date = isset($payment_data['payment_date']) ? date("d-m-Y H:i", strtotime($payment_data['payment_date'])) : 'N/A';
  
  // Xử lý số tiền
  $payment_amount = isset($payment_data['amount']) ? (float)$payment_data['amount'] : 0;
  $payment_amount_format = number_format($payment_amount, 0, ',', '.');
  
  // Trạng thái thanh toán
  $trans_status = strtolower(trim($payment_data['payment_status'] ?? 'pending'));
  $status_class = 'pending';
  $status_text = 'Đang chờ';
  $status_icon = 'clock';
  
  if($trans_status == 'paid' || $trans_status == 'txn_success' || $trans_status == 'success') {
    $status_class = 'success';
    $status_text = t_payment_history('paymentHistory.statusSuccess', $current_lang);
    $status_icon = 'check-circle';
  } else if($trans_status == 'failed' || $trans_status == 'txn_failure' || $trans_status == 'failure') {
    $status_class = 'failed';
    $status_text = t_payment_history('paymentHistory.statusFailed', $current_lang);
    $status_icon = 'x-circle';
  } else {
    $status_class = 'pending';
    $status_text = t_payment_history('paymentHistory.statusPending', $current_lang);
    $status_icon = 'clock';
  }
  
  // Xử lý mã giao dịch
  $raw_trans_id = $payment_data['trans_id'] ?? null;
  $order_id = $payment_data['order_id'] ?? '';
  $trans_id = (!empty($raw_trans_id) && $raw_trans_id !== $order_id) ? $raw_trans_id : null;
  $has_separate_trans_id = ($trans_id !== null);
  
  $resp_msg = $payment_data['trans_resp_msg'] ?? '';
  $item_id = isset($payment_data['id']) ? $payment_data['id'] : 0;
  
  // Xử lý thông tin đặt phòng
  $item_name = $payment_data['room_name'] ?? 'Đặt phòng';
  $checkin = isset($payment_data['check_in']) ? date("d-m-Y", strtotime($payment_data['check_in'])) : 'N/A';
  $checkout = isset($payment_data['check_out']) ? date("d-m-Y", strtotime($payment_data['check_out'])) : 'N/A';
  
  // Tính số đêm
  if(isset($payment_data['check_in']) && isset($payment_data['check_out'])) {
    $checkin_dt = new DateTime($payment_data['check_in']);
    $checkout_dt = new DateTime($payment_data['check_out']);
    $days = $checkout_dt->diff($checkin_dt)->days;
    if ($days == 0) { $days = 1; }
  } else {
    $days = 1;
  }
  
  $room_price = isset($payment_data['price']) ? (float)$payment_data['price'] : 0;
  $room_price_format = number_format($room_price, 0, ',', '.');
?>
    <div class='col-md-4 px-4 mb-4'>
      <div class='payment-card'>
        <div class='payment-card-header'>
          <h5 class='fw-bold'><?php echo htmlspecialchars($item_name, ENT_QUOTES, 'UTF-8'); ?></h5>
          <div class='payment-amount'><?php echo $payment_amount_format; ?> VND</div>
          <span class='badge bg-primary mt-2' data-i18n="paymentHistory.booking" data-i18n-skip><?php echo t_payment_history('paymentHistory.booking', $current_lang); ?></span>
        </div>
        
        <div class='booking-info-item'>
          <i class='bi bi-receipt-cutoff'></i>
          <div>
            <span class='label' data-i18n="paymentHistory.orderId" data-i18n-skip><?php echo t_payment_history('paymentHistory.orderId', $current_lang); ?>:</span>
            <span class='value'><?php echo htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8'); ?></span>
          </div>
        </div>
        
        <?php if($has_separate_trans_id): ?>
        <div class='booking-info-item'>
          <i class='bi bi-credit-card-2-front-fill'></i>
          <div>
            <span class='label' data-i18n="paymentHistory.transactionId" data-i18n-skip><?php echo t_payment_history('paymentHistory.transactionId', $current_lang); ?>:</span>
            <span class='value'><?php echo htmlspecialchars($trans_id, ENT_QUOTES, 'UTF-8'); ?></span>
          </div>
        </div>
        <?php endif; ?>
        
        <div class='booking-info-item'>
          <i class='bi bi-calendar-check-fill'></i>
          <div>
            <span class='label' data-i18n="paymentHistory.checkIn" data-i18n-skip><?php echo t_payment_history('paymentHistory.checkIn', $current_lang); ?>:</span>
            <span class='value'><?php echo $checkin; ?></span>
          </div>
        </div>
        
        <div class='booking-info-item'>
          <i class='bi bi-calendar-x-fill'></i>
          <div>
            <span class='label' data-i18n="paymentHistory.checkOut" data-i18n-skip><?php echo t_payment_history('paymentHistory.checkOut', $current_lang); ?>:</span>
            <span class='value'><?php echo $checkout; ?></span>
          </div>
        </div>
        
        <div class='booking-info-item'>
          <i class='bi bi-moon-stars-fill'></i>
          <div>
            <span class='label' data-i18n="paymentHistory.nights" data-i18n-skip><?php echo t_payment_history('paymentHistory.nights', $current_lang); ?>:</span>
            <span class='value'><?php echo $days; ?> <?php echo t_payment_history('paymentHistory.nightsUnit', $current_lang); ?></span>
          </div>
        </div>
        
        <div class='booking-info-item'>
          <i class='bi bi-cash-stack'></i>
          <div>
            <span class='label' data-i18n="paymentHistory.pricePerNight" data-i18n-skip><?php echo t_payment_history('paymentHistory.pricePerNight', $current_lang); ?>:</span>
            <span class='value'><?php echo $room_price_format; ?> VND</span>
          </div>
        </div>
        
        <div class='booking-info-item'>
          <i class='bi bi-clock-history'></i>
          <div>
            <span class='label' data-i18n="paymentHistory.paymentDate" data-i18n-skip><?php echo t_payment_history('paymentHistory.paymentDate', $current_lang); ?>:</span>
            <span class='value'><?php echo $payment_date; ?></span>
          </div>
        </div>
        
        <?php 
        // Xác định phương thức thanh toán
        $payment_method_payment = 'QR Code / VietQR';
        $payment_method_icon_payment = 'bi-qr-code-scan';
        $resp_msg_lower_payment = strtolower(trim($resp_msg ?? ''));
        
        if(strpos($resp_msg_lower_payment, 'qr') !== false || strpos($resp_msg_lower_payment, 'vietqr') !== false) {
          $payment_method_payment = 'QR Code / VietQR';
          $payment_method_icon_payment = 'bi-qr-code-scan';
        } else if(strpos($resp_msg_lower_payment, 'momo') !== false) {
          $payment_method_payment = 'MoMo';
          $payment_method_icon_payment = 'bi-wallet2';
        } else if(strpos($resp_msg_lower_payment, 'zalopay') !== false || strpos($resp_msg_lower_payment, 'zalo') !== false) {
          $payment_method_payment = 'ZaloPay';
          $payment_method_icon_payment = 'bi-wallet2';
        } else if(strpos($resp_msg_lower_payment, 'bank') !== false || strpos($resp_msg_lower_payment, 'ngân hàng') !== false) {
          $payment_method_payment = 'Chuyển khoản ngân hàng';
          $payment_method_icon_payment = 'bi-bank';
        } else if(strpos($resp_msg_lower_payment, 'card') !== false || strpos($resp_msg_lower_payment, 'thẻ') !== false) {
          $payment_method_payment = 'Thẻ tín dụng/Ghi nợ';
          $payment_method_icon_payment = 'bi-credit-card';
        } else if($status_class == 'success') {
          // Mặc định là QR nếu đã thanh toán thành công
          $payment_method_payment = 'QR Code / VietQR';
          $payment_method_icon_payment = 'bi-qr-code-scan';
        }
        ?>
        
        <div class='booking-info-item'>
          <i class='bi <?php echo $payment_method_icon_payment; ?>'></i>
          <div>
            <span class='label'><?php echo $current_lang === 'en' ? 'Payment Method:' : 'Thanh toán bằng:'; ?></span>
            <span class='value'><?php echo htmlspecialchars($payment_method_payment, ENT_QUOTES, 'UTF-8'); ?></span>
          </div>
        </div>
        
        <?php if($resp_msg): ?>
        <div class='booking-info-item'>
          <i class='bi bi-info-circle-fill'></i>
          <div>
            <span class='label'><?php echo $current_lang === 'en' ? 'Note:' : 'Ghi chú:'; ?></span>
            <span class='value'><?php echo htmlspecialchars($resp_msg, ENT_QUOTES, 'UTF-8'); ?></span>
          </div>
        </div>
        <?php endif; ?>
        
        <div class='mb-3 mt-2'>
          <span class='payment-status-badge <?php echo $status_class; ?>'>
            <i class='bi bi-<?php echo $status_icon; ?>-fill'></i>
            <?php echo $status_text; ?>
          </span>
        </div>
        
        <?php if($item_id > 0): ?>
        <div class='mt-auto pt-3 border-top'>
          <a href='bookings.php' class='btn btn-view-details w-100'>
            <i class='bi bi-calendar-check me-2'></i><span data-i18n="paymentHistory.viewDetails" data-i18n-skip><?php echo t_payment_history('paymentHistory.viewDetails', $current_lang); ?></span>
          </a>
        </div>
        <?php endif; ?>
      </div>
    </div>
<?php 
    endwhile;
  endif; 
?>
  </div>
</div>

<?php require('inc/footer.php'); ?>
<?php require('inc/modals.php'); ?>

</body>
</html>

