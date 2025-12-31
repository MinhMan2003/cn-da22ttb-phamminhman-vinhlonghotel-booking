<?php
require('inc/links.php');
require('inc/header.php');

if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
  redirect('index.php');
}

$current_lang = 'vi';
$lang_from_url = isset($_GET['_lang']) ? trim($_GET['_lang']) : '';
if ($lang_from_url === 'en' || $lang_from_url === 'vi') {
  setcookie('lang', $lang_from_url, time() + (365 * 24 * 60 * 60), '/', '', false, true);
  $_COOKIE['lang'] = $lang_from_url;
  $current_lang = $lang_from_url;
}

// Hàm dịch cho trang bookings
function t_bookings($key, $lang = 'vi') {
  $translations = [
    'vi' => [
      'bookings.pageTitle' => 'Lịch sử đặt phòng',
      'bookings.home' => 'Trang chủ',
      'bookings.bookingHistory' => 'Lịch sử đặt phòng',
      'bookings.noBookings' => 'Chưa có đặt phòng nào',
      'bookings.noBookingsDesc' => 'Bạn chưa có lịch sử đặt phòng. Hãy đặt phòng ngay để trải nghiệm dịch vụ của chúng tôi!',
      'bookings.viewRooms' => 'Xem phòng trống',
      'bookings.checkIn' => 'Nhận phòng',
      'bookings.checkOut' => 'Trả phòng',
      'bookings.nights' => 'Số đêm',
      'bookings.totalAmount' => 'Tổng tiền',
      'bookings.paymentMethod' => 'Thanh toán bằng',
      'bookings.orderId' => 'Mã đơn',
      'bookings.bookingDate' => 'Ngày đặt',
      'bookings.address' => 'Địa chỉ',
      'bookings.roomNumber' => 'Số phòng',
      'bookings.statusBooked' => 'Đã đặt phòng',
      'bookings.statusCancelled' => 'Đã hủy phòng',
      'bookings.statusPending' => 'Đang chờ',
      'bookings.statusProcessing' => 'Đang xử lý',
      'bookings.statusCancelledBadge' => 'Đã hủy',
      'bookings.paymentNotPaid' => 'Chưa thanh toán',
      'bookings.paymentQR' => 'QR Code / VietQR',
      'bookings.paymentMoMo' => 'MoMo',
      'bookings.paymentZaloPay' => 'ZaloPay',
      'bookings.paymentBank' => 'Chuyển khoản ngân hàng',
      'bookings.paymentCard' => 'Thẻ tín dụng/Ghi nợ',
      'bookings.paymentProcessing' => 'Đang xử lý',
      'bookings.requirement' => 'Yêu cầu',
      'bookings.promoCode' => 'Mã',
      'bookings.refunding' => 'Đang hoàn tiền',
      'bookings.refunded' => 'Đã hoàn tiền',
      'bookings.yourReview' => 'Đánh giá của bạn',
      'bookings.adminReply' => 'Phản hồi từ quản trị viên',
      'bookings.review' => 'Đánh giá',
      'bookings.cancel' => 'Hủy đặt',
      'bookings.reviewAndComment' => 'Đánh giá & Nhận xét',
      'bookings.rating' => 'Đánh giá',
      'bookings.comment' => 'Nhận xét',
      'bookings.reviewImages' => 'Ảnh đánh giá (tối đa 5 ảnh, mỗi ảnh tối đa 5MB)',
      'bookings.acceptFormats' => 'Chấp nhận: JPG, PNG, WEBP',
      'bookings.selectRating' => '-- Chọn đánh giá --',
      'bookings.rating5' => '⭐ 5 - Xuất sắc',
      'bookings.rating4' => '⭐ 4 - Tốt',
      'bookings.rating3' => '⭐ 3 - Bình thường',
      'bookings.rating2' => '⭐ 2 - Kém',
      'bookings.rating1' => '⭐ 1 - Tệ',
      'bookings.reviewPlaceholder' => 'Chia sẻ trải nghiệm của bạn về phòng này...',
      'bookings.cancelBtn' => 'Hủy',
      'bookings.submitReview' => 'Gửi đánh giá',
      'bookings.perNight' => '/ đêm',
      'bookings.nightsUnit' => 'đêm',
      'bookings.confirmCancel' => 'Bạn có chắc muốn hủy đặt phòng này?',
      'bookings.cancelSuccess' => 'Hủy đặt phòng!',
      'bookings.cancelFailed' => 'Hủy đặt phòng không thành công!',
      'bookings.reviewSuccess' => 'Cảm ơn bạn đã để lại đánh giá!',
      'bookings.selectRatingError' => 'Vui lòng chọn đánh giá!',
      'bookings.enterCommentError' => 'Vui lòng nhập nhận xét!',
      'bookings.sending' => 'Đang gửi...',
      'bookings.reviewSent' => 'Đánh giá đã được gửi thành công!',
      'bookings.reviewFailed' => 'Đánh giá thất bại!',
      'bookings.connectionError' => 'Lỗi kết nối. Vui lòng thử lại.',
      'bookings.maxImagesError' => 'Chỉ được chọn tối đa {max} ảnh! Bạn đã chọn {count} ảnh.',
      'bookings.invalidImages' => 'Có {count} ảnh không hợp lệ đã bị bỏ qua.',
      'bookings.selectedImages' => 'Đã chọn {valid}/{max} ảnh',
    ],
    'en' => [
      'bookings.pageTitle' => 'Booking History',
      'bookings.home' => 'Home',
      'bookings.bookingHistory' => 'Booking History',
      'bookings.noBookings' => 'No bookings yet',
      'bookings.noBookingsDesc' => 'You have no booking history. Book a room now to experience our services!',
      'bookings.viewRooms' => 'View Available Rooms',
      'bookings.checkIn' => 'Check-in',
      'bookings.checkOut' => 'Check-out',
      'bookings.nights' => 'Nights',
      'bookings.totalAmount' => 'Total Amount',
      'bookings.paymentMethod' => 'Payment Method',
      'bookings.orderId' => 'Order ID',
      'bookings.bookingDate' => 'Booking Date',
      'bookings.address' => 'Address',
      'bookings.roomNumber' => 'Room Number',
      'bookings.statusBooked' => 'Booked',
      'bookings.statusCancelled' => 'Cancelled',
      'bookings.statusPending' => 'Pending',
      'bookings.statusProcessing' => 'Processing',
      'bookings.statusCancelledBadge' => 'Cancelled',
      'bookings.paymentNotPaid' => 'Not Paid',
      'bookings.paymentQR' => 'QR Code / VietQR',
      'bookings.paymentMoMo' => 'MoMo',
      'bookings.paymentZaloPay' => 'ZaloPay',
      'bookings.paymentBank' => 'Bank Transfer',
      'bookings.paymentCard' => 'Credit/Debit Card',
      'bookings.paymentProcessing' => 'Processing',
      'bookings.requirement' => 'Requirement',
      'bookings.promoCode' => 'Code',
      'bookings.refunding' => 'Refunding',
      'bookings.refunded' => 'Refunded',
      'bookings.yourReview' => 'Your Review',
      'bookings.adminReply' => 'Admin Reply',
      'bookings.review' => 'Review',
      'bookings.cancel' => 'Cancel Booking',
      'bookings.reviewAndComment' => 'Review & Comment',
      'bookings.rating' => 'Rating',
      'bookings.comment' => 'Comment',
      'bookings.reviewImages' => 'Review Images (max 5 images, 5MB each)',
      'bookings.acceptFormats' => 'Accepted: JPG, PNG, WEBP',
      'bookings.selectRating' => '-- Select Rating --',
      'bookings.rating5' => '⭐ 5 - Excellent',
      'bookings.rating4' => '⭐ 4 - Good',
      'bookings.rating3' => '⭐ 3 - Average',
      'bookings.rating2' => '⭐ 2 - Poor',
      'bookings.rating1' => '⭐ 1 - Terrible',
      'bookings.reviewPlaceholder' => 'Share your experience about this room...',
      'bookings.cancelBtn' => 'Cancel',
      'bookings.submitReview' => 'Submit Review',
      'bookings.perNight' => '/ night',
      'bookings.nightsUnit' => 'nights',
      'bookings.confirmCancel' => 'Are you sure you want to cancel this booking?',
      'bookings.cancelSuccess' => 'Booking cancelled!',
      'bookings.cancelFailed' => 'Failed to cancel booking!',
      'bookings.reviewSuccess' => 'Thank you for leaving a review!',
      'bookings.selectRatingError' => 'Please select a rating!',
      'bookings.enterCommentError' => 'Please enter a comment!',
      'bookings.sending' => 'Sending...',
      'bookings.reviewSent' => 'Review submitted successfully!',
      'bookings.reviewFailed' => 'Failed to submit review!',
      'bookings.connectionError' => 'Connection error. Please try again.',
      'bookings.maxImagesError' => 'Maximum {max} images allowed! You selected {count} images.',
      'bookings.invalidImages' => '{count} invalid images were skipped.',
      'bookings.selectedImages' => 'Selected {valid}/{max} images',
    ]
  ];
  return $translations[$lang][$key] ?? $key;
}

$query = "SELECT bo.*, bd.* FROM `booking_order` bo
  INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
  WHERE ((bo.booking_status='booked') 
  OR (bo.booking_status='cancelled')
  OR (bo.booking_status='payment failed')
  OR (bo.booking_status='pending'))
  AND (bo.user_id=?)
  ORDER BY bo.booking_id DESC";

$result = select($query, [$_SESSION['uId']], 'i');

// Payment history is now in separate file: payment_history.php
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $settings_r['site_title'] ?> - <?php echo t_bookings('bookings.pageTitle', $current_lang); ?></title>
  
  <style>
    /* Modern Bookings Page */
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
    
    /* Booking Card */
    .booking-card {
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
    
    .booking-card:hover {
      box-shadow: 0 16px 50px rgba(0,0,0,0.12);
      transform: translateY(-4px);
    }
    
    .booking-card-header {
      border-bottom: 2px solid #e5e7eb;
      padding-bottom: 1rem;
      margin-bottom: 1rem;
    }
    
    .booking-card-header h5 {
      font-size: 1.35rem;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 0.5rem;
    }
    
    .booking-price {
      font-size: 1.1rem;
      font-weight: 600;
      color: #059669;
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
    
    /* Status Badge */
    .status-badge {
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
    
    .status-badge.booked {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(16,185,129,0.3);
    }
    
    .status-badge.cancelled {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(239,68,68,0.3);
    }
    
    .status-badge.pending {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(245,158,11,0.3);
    }
    
    .status-badge.payment-failed {
      background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(107,114,128,0.3);
    }
    
    /* Action Buttons */
    .booking-actions {
      margin-top: auto;
      padding-top: 1rem;
      border-top: 1px solid #e5e7eb;
      display: flex;
      gap: 0.75rem;
      flex-wrap: wrap;
    }
    
    .btn-action {
      border-radius: 10px;
      font-weight: 600;
      padding: 10px 20px;
      transition: all 0.3s ease;
      border: none;
    }
    
    .btn-action:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }
    
    .btn-review {
      background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
      color: #ffffff;
    }
    
    .btn-review:hover {
      background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
      color: #ffffff;
    }
    
    .btn-cancel {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      color: #ffffff;
    }
    
    .btn-cancel:hover {
      background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
      color: #ffffff;
    }
    
    /* Badges */
    .info-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 600;
      margin: 4px 4px 0 0;
    }
    
    .info-badge.requirement {
      background: #dbeafe;
      color: #1e40af;
      border: 1px solid #93c5fd;
    }
    
    .info-badge.promo {
      background: #fef3c7;
      color: #92400e;
      border: 1px solid #fde68a;
    }
    
    .info-badge.refund {
      background: #dbeafe;
      color: #1e40af;
      border: 1px solid #93c5fd;
    }
    
    .info-badge.refunded {
      background: #d1fae5;
      color: #065f46;
      border: 1px solid #6ee7b7;
    }
    
    /* Review Section */
    .review-section {
      background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
      border: 1px solid #e5e7eb;
    }
    
    .review-images-mini img {
      border: 1px solid #dee2e6;
      transition: transform 0.2s;
    }
    
    .review-images-mini img:hover {
      transform: scale(1.1);
      border-color: #0d6efd;
    }
    
    .admin-reply-mini {
      background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
      border-left: 3px solid #0d6efd !important;
    }
    
    /* Room Number */
    .room-number {
      font-size: 1.1rem;
      font-weight: 700;
      padding: 8px 16px;
      border-radius: 10px;
      display: inline-block;
    }
    
    .room-number.success {
      background: #d1fae5;
      color: #065f46;
    }
    
    .room-number.pending {
      background: #fee2e2;
      color: #991b1b;
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
    
    /* Payment History Section removed - now in payment_history.php */
    
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
    
    /* Modal Enhancement */
    .modal-content {
      border-radius: 20px;
      border: none;
      box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }
    
    .modal-header {
      border-bottom: 2px solid #e5e7eb;
      padding: 1.5rem;
    }
    
    .modal-title {
      font-weight: 700;
      color: #1f2937;
    }
    
    .modal-body {
      padding: 1.5rem;
    }
    
    .form-label {
      font-weight: 600;
      color: #374151;
      margin-bottom: 8px;
    }
    
    .form-select, .form-control {
      border: 2px solid #e5e7eb;
      border-radius: 10px;
      padding: 10px 14px;
      transition: all 0.3s ease;
    }
    
    .form-select:focus, .form-control:focus {
      border-color: #1f2937;
      box-shadow: 0 0 0 4px rgba(31,41,55,0.1);
      outline: none;
    }
    
    .btn-submit {
      background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
      color: #ffffff;
      border: none;
      border-radius: 10px;
      padding: 10px 20px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
      background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
      color: #ffffff;
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(31,41,55,0.3);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .booking-card {
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
      <h2 class="fw-bold h-font" data-i18n="bookings.bookingHistory"><?php echo t_bookings('bookings.bookingHistory', $current_lang); ?></h2>
      <div class="breadcrumb-modern">
        <a href="index.php"><i class="bi bi-house-door me-1"></i><span data-i18n="bookings.home"><?php echo t_bookings('bookings.home', $current_lang); ?></span></a>
        <span class="text-secondary mx-2">/</span>
        <span class="text-dark fw-semibold" data-i18n="bookings.bookingHistory"><?php echo t_bookings('bookings.bookingHistory', $current_lang); ?></span>
      </div>
    </div>

<?php 
  $has_bookings = false;
  if($result && mysqli_num_rows($result) > 0) {
    $has_bookings = true;
    mysqli_data_seek($result, 0); // Reset pointer
  }
  
  if(!$has_bookings): ?>
    <div class="col-12 px-4">
      <div class="empty-bookings">
        <i class="bi bi-calendar-x"></i>
        <h4 data-i18n="bookings.noBookings"><?php echo t_bookings('bookings.noBookings', $current_lang); ?></h4>
        <p data-i18n="bookings.noBookingsDesc"><?php echo t_bookings('bookings.noBookingsDesc', $current_lang); ?></p>
        <a href="rooms.php" class="btn btn-primary mt-3">
          <i class="bi bi-search me-2"></i><span data-i18n="bookings.viewRooms"><?php echo t_bookings('bookings.viewRooms', $current_lang); ?></span>
        </a>
      </div>
    </div>
  <?php else:
    while($data = mysqli_fetch_assoc($result)): 
  $date = date("d-m-Y", strtotime($data['datentime']));
  $checkin = date("d-m-Y", strtotime($data['check_in']));
  $checkout = date("d-m-Y", strtotime($data['check_out']));

  $checkin_dt  = new DateTime($data['check_in']);
  $checkout_dt = new DateTime($data['check_out']);
  $days = $checkout_dt->diff($checkin_dt)->days;
  if ($days == 0) { $days = 1; }

  $total_amount = $data['price'] * $days;
  $total_format = number_format($total_amount, 0, ',', '.');
  $price_format = number_format($data['price'], 0, ',', '.');

  // Tách địa chỉ / yêu cầu / mã khuyến mãi
  $address_full = $data['address'];
  $special_req = "";
  $promo_note = "";
  $address_main = $address_full;
  if(strpos($address_full,'| SR:') !== false){
    [$address_main,$rest] = explode('| SR:', $address_full, 2);
    $rest = trim($rest);
    if(strpos($rest,'| Mã KM:') !== false){
      [$rest,$promo_note] = explode('| Mã KM:', $rest, 2);
      $promo_note = trim($promo_note);
    } else if(strpos($rest,'| Promo:') !== false){
      [$rest,$promo_note] = explode('| Promo:', $rest, 2);
      $promo_note = trim($promo_note);
    }
    $special_req = trim($rest);
  } else if(strpos($address_full,'| Mã KM:') !== false){
    [$address_main,$promo_note] = explode('| Mã KM:', $address_full, 2);
    $promo_note = trim($promo_note);
  } else if(strpos($address_full,'| Promo:') !== false){
    [$address_main,$promo_note] = explode('| Promo:', $address_full, 2);
    $promo_note = trim($promo_note);
  }
  $address_main = trim($address_main);

  // Mặc định số phòng giao
  $processing_text = t_bookings('bookings.statusProcessing', $current_lang);
  $room_no_html = "<span class='text-danger' data-i18n='bookings.statusProcessing'>$processing_text</span>";

  // Trạng thái
  $status = strtolower(trim($data['booking_status']));
  $status_bg = "bg-secondary";
  $status_text = ucfirst($status);
  $status_i18n_key = '';
  $btn = "";

  if ($status == 'booked') {
    $status_bg = "bg-success";
    $status_text = t_bookings('bookings.statusBooked', $current_lang);
    $status_i18n_key = 'bookings.statusBooked';
    if ($data['arrival'] == 1) {
      if ($data['rate_review'] == 0) {
        $review_text = t_bookings('bookings.review', $current_lang);
        $btn .= "<button type='button' onclick='review_room($data[booking_id],$data[room_id])' data-bs-toggle='modal' data-bs-target='#reviewModal' class='btn btn-action btn-review'><i class='bi bi-star-fill me-1'></i><span data-i18n='bookings.review'>$review_text</span></button>";
      }
    } else {
      $cancel_text = t_bookings('bookings.cancel', $current_lang);
      $btn = "<button onclick='cancel_booking($data[booking_id])' type='button' class='btn btn-action btn-cancel'><i class='bi bi-x-circle-fill me-1'></i><span data-i18n='bookings.cancel'>$cancel_text</span></button>";
    }
    if(!empty($data['room_no'])){
      $room_no_html = "<span class='room-number success'><i class='bi bi-door-open-fill me-1'></i>$data[room_no]</span>";
    }
  } else if ($status == 'cancelled') {
    $status_bg = "bg-danger";
    $status_text = t_bookings('bookings.statusCancelled', $current_lang);
    $status_i18n_key = 'bookings.statusCancelled';
    $btn = "";
    $cancelled_badge = t_bookings('bookings.statusCancelledBadge', $current_lang);
    $room_no_html = "<span class='room-number pending'><i class='bi bi-x-circle-fill me-1'></i><span data-i18n='bookings.statusCancelledBadge'>$cancelled_badge</span></span>";
  } else if ($status == 'pending') {
    $status_bg = "bg-warning text-dark";
    $status_text = t_bookings('bookings.statusPending', $current_lang);
    $status_i18n_key = 'bookings.statusPending';
    $btn = "";
  }

  $requirement_label = t_bookings('bookings.requirement', $current_lang);
  $promo_label = t_bookings('bookings.promoCode', $current_lang);
  $req_badge = $special_req ? "<span class='info-badge requirement'><i class='bi bi-info-circle'></i><span data-i18n='bookings.requirement'>$requirement_label</span>: ".htmlspecialchars($special_req)."</span>" : "";
  $promo_badge = $promo_note ? "<span class='info-badge promo'><i class='bi bi-tag-fill'></i><span data-i18n='bookings.promoCode'>$promo_label</span>: ".htmlspecialchars($promo_note)."</span>" : "";
  
  // Lấy review và admin reply nếu đã đánh giá
  $review_html = '';
  if($data['rate_review'] == 1){
    // JOIN với rooms và hotel_owners để lấy thông tin owner
    $review_query = "SELECT rr.rating, rr.review, rr.images, rr.admin_reply, rr.admin_reply_date, rr.datentime,
                            r.owner_id, ho.name AS owner_name, ho.hotel_name
                     FROM rating_review rr 
                     INNER JOIN rooms r ON rr.room_id = r.id
                     LEFT JOIN hotel_owners ho ON r.owner_id = ho.id
                     WHERE rr.booking_id = ? AND rr.user_id = ? 
                     LIMIT 1";
    $review_res = select($review_query, [$data['booking_id'], $_SESSION['uId']], 'ii');
    if($review_res && mysqli_num_rows($review_res) > 0){
      $review_data = mysqli_fetch_assoc($review_res);
      $stars = str_repeat('⭐', (int)$review_data['rating']);
      $review_text = htmlspecialchars($review_data['review']);
      $review_date = date('d/m/Y', strtotime($review_data['datentime']));
      
      // Xử lý ảnh
      $review_images = '';
      if(!empty($review_data['images'])){
        $images = json_decode($review_data['images'], true);
        if(is_array($images) && !empty($images)){
          $review_images = '<div class="review-images-mini mt-2 d-flex gap-1">';
          foreach(array_slice($images, 0, 3) as $img){
            $img_path = ltrim(str_replace('../', '', $img), '/');
            $review_images .= "<img src='$img_path' style='width:40px;height:40px;object-fit:cover;border-radius:4px;cursor:pointer;' onclick='window.open(\"$img_path\",\"_blank\")'>";
          }
          if(count($images) > 3){
            $review_images .= "<span class='badge bg-secondary'>+".(count($images)-3)."</span>";
          }
          $review_images .= '</div>';
        }
      }
      
      // Admin reply - xác định người phản hồi
      $admin_reply_html = '';
      if(!empty($review_data['admin_reply'])){
        $reply_date = !empty($review_data['admin_reply_date']) ? date('d/m/Y', strtotime($review_data['admin_reply_date'])) : '';
        
        // Xác định người phản hồi
        $reply_by_label = '';
        $reply_by_icon = 'bi-shield-check';
        if(!empty($review_data['owner_id']) && !empty($review_data['owner_name'])){
          // Phản hồi từ chủ khách sạn
          $owner_display_name = !empty($review_data['hotel_name']) ? htmlspecialchars($review_data['hotel_name'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($review_data['owner_name'], ENT_QUOTES, 'UTF-8');
          $reply_by_label = $current_lang === 'en' ? "Reply from {$owner_display_name}" : "Phản hồi từ {$owner_display_name}";
          $reply_by_icon = 'bi-building';
        } else {
          // Phản hồi từ quản trị viên
          $reply_by_label = $current_lang === 'en' ? 'Reply from administrator' : 'Phản hồi từ quản trị viên';
          $reply_by_icon = 'bi-shield-check';
        }
        
        $admin_reply_html = "<div class='admin-reply-mini mt-2 p-2 bg-light rounded border-start border-3 border-primary'>
          <div class='d-flex align-items-center gap-1 mb-1'>
            <i class='bi {$reply_by_icon} text-primary' style='font-size:12px;'></i>
            <strong class='small' style='font-size:11px;'>{$reply_by_label}</strong>
            ".($reply_date ? "<span class='text-muted small ms-auto' style='font-size:10px;'>$reply_date</span>" : "")."
          </div>
          <div class='small text-muted' style='font-size:12px;'>".htmlspecialchars($review_data['admin_reply'])."</div>
        </div>";
      }
      
      $your_review_label = t_bookings('bookings.yourReview', $current_lang);
      $review_html = "<div class='review-section mt-3 p-2 bg-light rounded'>
        <div class='d-flex justify-content-between align-items-center mb-1'>
          <span class='small fw-semibold' data-i18n='bookings.yourReview'>$your_review_label:</span>
          <span class='small text-muted'>$review_date</span>
        </div>
        <div class='mb-1'>$stars</div>
        <div class='small text-muted mb-1'>$review_text</div>
        $review_images
        $admin_reply_html
      </div>";
    }
  }
  
  $status_class = '';
  if($status == 'booked') $status_class = 'booked';
  else if($status == 'cancelled') $status_class = 'cancelled';
  else if($status == 'pending') $status_class = 'pending';
  else if($status == 'payment failed') $status_class = 'payment-failed';
?>
    <div class='col-md-4 px-4 mb-4'>
      <div class='booking-card'>
        <div class='booking-card-header'>
          <h5 class='fw-bold'><?php echo htmlspecialchars($data['room_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
          <div class='booking-price'><?php echo $price_format; ?> VND <span data-i18n="bookings.perNight"><?php echo t_bookings('bookings.perNight', $current_lang); ?></span></div>
        </div>
        
        <div class='booking-info-item'>
          <i class='bi bi-calendar-check-fill'></i>
          <div>
            <span class='label' data-i18n="bookings.checkIn"><?php echo t_bookings('bookings.checkIn', $current_lang); ?>:</span>
            <span class='value'><?php echo $checkin; ?></span>
          </div>
        </div>
        
        <div class='booking-info-item'>
          <i class='bi bi-calendar-x-fill'></i>
          <div>
            <span class='label' data-i18n="bookings.checkOut"><?php echo t_bookings('bookings.checkOut', $current_lang); ?>:</span>
            <span class='value'><?php echo $checkout; ?></span>
          </div>
        </div>
        
        <div class='booking-info-item'>
          <i class='bi bi-moon-stars-fill'></i>
          <div>
            <span class='label' data-i18n="bookings.nights"><?php echo t_bookings('bookings.nights', $current_lang); ?>:</span>
            <span class='value'><?php echo $days; ?> <span data-i18n="bookings.nightsUnit"><?php echo t_bookings('bookings.nightsUnit', $current_lang); ?></span></span>
          </div>
        </div>
        
        <div class='booking-info-item'>
          <i class='bi bi-cash-stack'></i>
          <div>
            <span class='label' data-i18n="bookings.totalAmount"><?php echo t_bookings('bookings.totalAmount', $current_lang); ?>:</span>
            <span class='value fw-bold text-success'><?php echo $total_format; ?> VND</span>
          </div>
        </div>
        
        <?php 
        // Xác định phương thức thanh toán
        $payment_method = t_bookings('bookings.paymentNotPaid', $current_lang);
        $payment_method_icon = 'bi-clock';
        $resp_msg_lower = strtolower(trim($data['trans_resp_msg'] ?? ''));
        $trans_status_lower = strtolower(trim($data['trans_status'] ?? ''));
        
        if($data['trans_amt'] > 0) {
          if(strpos($resp_msg_lower, 'qr') !== false || strpos($resp_msg_lower, 'vietqr') !== false) {
            $payment_method = t_bookings('bookings.paymentQR', $current_lang);
            $payment_method_icon = 'bi-qr-code-scan';
          } else if(strpos($resp_msg_lower, 'momo') !== false) {
            $payment_method = t_bookings('bookings.paymentMoMo', $current_lang);
            $payment_method_icon = 'bi-wallet2';
          } else if(strpos($resp_msg_lower, 'zalopay') !== false || strpos($resp_msg_lower, 'zalo') !== false) {
            $payment_method = t_bookings('bookings.paymentZaloPay', $current_lang);
            $payment_method_icon = 'bi-wallet2';
          } else if(strpos($resp_msg_lower, 'bank') !== false || strpos($resp_msg_lower, 'ngân hàng') !== false) {
            $payment_method = t_bookings('bookings.paymentBank', $current_lang);
            $payment_method_icon = 'bi-bank';
          } else if(strpos($resp_msg_lower, 'card') !== false || strpos($resp_msg_lower, 'thẻ') !== false) {
            $payment_method = t_bookings('bookings.paymentCard', $current_lang);
            $payment_method_icon = 'bi-credit-card';
          } else if($trans_status_lower == 'paid' || $trans_status_lower == 'txn_success' || $trans_status_lower == 'success') {
            // Mặc định là QR nếu đã thanh toán thành công nhưng không có thông tin cụ thể
            $payment_method = t_bookings('bookings.paymentQR', $current_lang);
            $payment_method_icon = 'bi-qr-code-scan';
          } else {
            $payment_method = t_bookings('bookings.paymentProcessing', $current_lang);
            $payment_method_icon = 'bi-hourglass-split';
          }
        }
        ?>
        
        <?php if($data['trans_amt'] > 0): ?>
        <div class='booking-info-item'>
          <i class='bi <?php echo $payment_method_icon; ?>'></i>
          <div>
            <span class='label' data-i18n="bookings.paymentMethod"><?php echo t_bookings('bookings.paymentMethod', $current_lang); ?>:</span>
            <span class='value'><?php echo htmlspecialchars($payment_method, ENT_QUOTES, 'UTF-8'); ?></span>
          </div>
        </div>
        <?php endif; ?>
        
        <div class='booking-info-item'>
          <i class='bi bi-receipt'></i>
          <div>
            <span class='label' data-i18n="bookings.orderId"><?php echo t_bookings('bookings.orderId', $current_lang); ?>:</span>
            <span class='value'><?php echo htmlspecialchars($data['order_id'], ENT_QUOTES, 'UTF-8'); ?></span>
          </div>
        </div>
        
        <div class='booking-info-item'>
          <i class='bi bi-clock-history'></i>
          <div>
            <span class='label' data-i18n="bookings.bookingDate"><?php echo t_bookings('bookings.bookingDate', $current_lang); ?>:</span>
            <span class='value'><?php echo $date; ?></span>
          </div>
        </div>
        
        <?php if($address_main): ?>
        <div class='booking-info-item'>
          <i class='bi bi-geo-alt-fill'></i>
          <div>
            <span class='label' data-i18n="bookings.address"><?php echo t_bookings('bookings.address', $current_lang); ?>:</span>
            <span class='value'><?php echo htmlspecialchars($address_main, ENT_QUOTES, 'UTF-8'); ?></span>
          </div>
        </div>
        <?php endif; ?>
        
        <?php if($req_badge || $promo_badge): ?>
        <div class='mb-2'>
          <?php echo $req_badge . ' ' . $promo_badge; ?>
        </div>
        <?php endif; ?>
        
        <div class='booking-info-item'>
          <i class='bi bi-door-open-fill'></i>
          <div>
            <span class='label' data-i18n="bookings.roomNumber"><?php echo t_bookings('bookings.roomNumber', $current_lang); ?>:</span>
            <span class='value'><?php echo $room_no_html; ?></span>
          </div>
        </div>
        
        <div class='mb-3'>
          <span class='status-badge <?php echo $status_class; ?>'>
            <i class='bi bi-<?php echo $status == 'booked' ? 'check-circle' : ($status == 'cancelled' ? 'x-circle' : 'clock'); ?>-fill'></i>
            <?php if($status_i18n_key): ?>
              <span data-i18n="<?php echo $status_i18n_key; ?>"><?php echo $status_text; ?></span>
            <?php else: ?>
              <?php echo $status_text; ?>
            <?php endif; ?>
          </span>
        </div>
        
        <?php if($status == 'cancelled'): ?>
          <?php if($data['refund'] == 0): ?>
            <div class='mb-2'>
              <span class='info-badge refund'><i class='bi bi-arrow-counterclockwise'></i><span data-i18n="bookings.refunding"><?php echo t_bookings('bookings.refunding', $current_lang); ?></span></span>
            </div>
          <?php else: ?>
            <div class='mb-2'>
              <span class='info-badge refunded'><i class='bi bi-check-circle-fill'></i><span data-i18n="bookings.refunded"><?php echo t_bookings('bookings.refunded', $current_lang); ?></span></span>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        
        <?php if($review_html): ?>
          <?php echo $review_html; ?>
        <?php endif; ?>
        
        <div class='booking-actions'>
          <?php echo $btn; ?>
        </div>
      </div>
    </div>
<?php 
    endwhile;
  endif; 
?>
  </div>
</div>

<!-- Modal review -->
<div class="modal fade" id="reviewModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="review-form">
        <div class="modal-header">
          <h5 class="modal-title d-flex align-items-center">
            <i class="bi bi-chat-square-heart-fill fs-3 me-2 text-primary"></i> 
            <span data-i18n="bookings.reviewAndComment"><?php echo t_bookings('bookings.reviewAndComment', $current_lang); ?></span>
          </h5>
          <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">
              <i class="bi bi-star-fill text-warning me-1"></i><span data-i18n="bookings.rating"><?php echo t_bookings('bookings.rating', $current_lang); ?></span>
            </label>
            <select class="form-select shadow-none" name="rating" required>
              <option value="" data-i18n="bookings.selectRating"><?php echo t_bookings('bookings.selectRating', $current_lang); ?></option>
              <option value="5" data-i18n="bookings.rating5"><?php echo t_bookings('bookings.rating5', $current_lang); ?></option>
              <option value="4" data-i18n="bookings.rating4"><?php echo t_bookings('bookings.rating4', $current_lang); ?></option>
              <option value="3" data-i18n="bookings.rating3"><?php echo t_bookings('bookings.rating3', $current_lang); ?></option>
              <option value="2" data-i18n="bookings.rating2"><?php echo t_bookings('bookings.rating2', $current_lang); ?></option>
              <option value="1" data-i18n="bookings.rating1"><?php echo t_bookings('bookings.rating1', $current_lang); ?></option>
            </select>
          </div>
          <div class="mb-4">
            <label class="form-label">
              <i class="bi bi-chat-left-text me-1"></i><span data-i18n="bookings.comment"><?php echo t_bookings('bookings.comment', $current_lang); ?></span>
            </label>
            <textarea name="review" rows="4" required class="form-control shadow-none" data-i18n-placeholder="bookings.reviewPlaceholder" placeholder="<?php echo t_bookings('bookings.reviewPlaceholder', $current_lang); ?>"></textarea>
          </div>
          <div class="mb-4">
            <label class="form-label">
              <i class="bi bi-images me-1"></i><span data-i18n="bookings.reviewImages"><?php echo t_bookings('bookings.reviewImages', $current_lang); ?></span>
            </label>
            <input type="file" name="review_images[]" class="form-control shadow-none" accept="image/jpeg,image/jpg,image/png,image/webp" multiple>
            <small class="text-muted" data-i18n="bookings.acceptFormats"><?php echo t_bookings('bookings.acceptFormats', $current_lang); ?></small>
            <div id="image-preview" class="mt-2 d-flex gap-2 flex-wrap"></div>
          </div>
          <input type="hidden" name="booking_id">
          <input type="hidden" name="room_id">
          <div class="d-flex gap-2 justify-content-end">
            <button type="reset" class="btn btn-outline-secondary shadow-none" data-bs-dismiss="modal" data-i18n="bookings.cancelBtn"><?php echo t_bookings('bookings.cancelBtn', $current_lang); ?></button>
            <button type="submit" class="btn btn-submit">
              <i class="bi bi-send-fill me-2"></i><span data-i18n="bookings.submitReview"><?php echo t_bookings('bookings.submitReview', $current_lang); ?></span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<?php 
  if(isset($_GET['cancel_status'])){
    $cancel_success_msg = t_bookings('bookings.cancelSuccess', $current_lang);
    echo "<script>showToast('success','$cancel_success_msg', 3000);</script>";
  }  
  else if(isset($_GET['review_status'])){
    $review_success_msg = t_bookings('bookings.reviewSuccess', $current_lang);
    echo "<script>showToast('success','$review_success_msg', 3000);</script>";
  }  
?>

<?php require('inc/footer.php'); ?>
<?php require('inc/modals.php'); ?>

<script>
  function cancel_booking(id)
  {
    const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
    const confirmMsg = currentLang === 'en' 
      ? 'Are you sure you want to cancel this booking?'
      : 'Bạn có chắc muốn hủy đặt phòng này?';
    const errorMsg = currentLang === 'en'
      ? 'Failed to cancel booking!'
      : 'Hủy đặt phòng không thành công!';
    
    if(confirm(confirmMsg))
    {        
      let xhr = new XMLHttpRequest();
      xhr.open("POST","ajax/cancel_booking.php",true);
      xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
      xhr.onload = function(){
        if(this.responseText==1){
          window.location.href="bookings.php?cancel_status=true";
        }
        else{
          if(typeof showToast === 'function'){
            showToast('error', errorMsg, 3000);
          } else {
            alert(errorMsg);
          }
        }
      }
      xhr.send('cancel_booking&id='+id);
    }
  }

  let review_form = document.getElementById('review-form');

  function review_room(bid,rid){
    review_form.elements['booking_id'].value = bid;
    review_form.elements['room_id'].value = rid;
  }

  // Preview ảnh khi chọn
  const imageInput = review_form.querySelector('input[type="file"]');
  const imagePreview = document.getElementById('image-preview');
  
  imageInput.addEventListener('change', function(e){
    imagePreview.innerHTML = '';
    const files = e.target.files;
    const maxFiles = 5;
    const maxSize = 5 * 1024 * 1024; // 5MB
    
    if(files.length > maxFiles){
      const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
      const errorMsg = currentLang === 'en'
        ? `Maximum ${maxFiles} images allowed! You selected ${files.length} images.`
        : `Chỉ được chọn tối đa ${maxFiles} ảnh! Bạn đã chọn ${files.length} ảnh.`;
      if(typeof showToast === 'function'){
        showToast('error', errorMsg, 3000);
      } else {
        alert(errorMsg);
      }
      e.target.value = '';
      return;
    }
    
    let validFiles = 0;
    let invalidFiles = [];
    
    for(let i = 0; i < files.length; i++){
      const file = files[i];
      
      const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
      // Kiểm tra kích thước
      if(file.size > maxSize){
        const sizeError = currentLang === 'en' ? '(exceeds 5MB)' : '(vượt quá 5MB)';
        invalidFiles.push(`${file.name} ${sizeError}`);
        continue;
      }
      
      // Kiểm tra loại file
      const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
      if(!allowedTypes.includes(file.type)){
        const formatError = currentLang === 'en' ? '(invalid format)' : '(không đúng định dạng)';
        invalidFiles.push(`${file.name} ${formatError}`);
        continue;
      }
      
      // Chỉ hiển thị preview cho file hợp lệ và chưa vượt quá 5 file
      if(validFiles < maxFiles){
        const reader = new FileReader();
        reader.onload = function(e){
          const img = document.createElement('img');
          img.src = e.target.result;
          img.style.width = '80px';
          img.style.height = '80px';
          img.style.objectFit = 'cover';
          img.style.borderRadius = '8px';
          img.style.border = '2px solid #dee2e6';
          img.style.cursor = 'pointer';
          img.title = file.name;
          imagePreview.appendChild(img);
        };
        reader.readAsDataURL(file);
        validFiles++;
      }
    }
    
    // Hiển thị thông báo nếu có file không hợp lệ
    if(invalidFiles.length > 0){
      const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
      const warningMsg = currentLang === 'en'
        ? `${invalidFiles.length} invalid images were skipped.`
        : `Có ${invalidFiles.length} ảnh không hợp lệ đã bị bỏ qua.`;
      if(typeof showToast === 'function'){
        showToast('warning', warningMsg, 3000);
      }
    }
    
    // Hiển thị số lượng ảnh đã chọn
    if(validFiles > 0){
      const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
      const selectedText = currentLang === 'en' 
        ? `Selected ${validFiles}/${maxFiles} images`
        : `Đã chọn ${validFiles}/${maxFiles} ảnh`;
      const countBadge = document.createElement('div');
      countBadge.className = 'mt-2 small text-muted';
      countBadge.innerHTML = `<i class="bi bi-info-circle me-1"></i>${selectedText}`;
      imagePreview.appendChild(countBadge);
    }
  });

  review_form.addEventListener('submit',function(e){
    e.preventDefault();
    
    const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
    // Validate
    if(!review_form.elements['rating'].value){
      const ratingError = currentLang === 'en' 
        ? 'Please select a rating!'
        : 'Vui lòng chọn đánh giá!';
      if(typeof showToast === 'function'){
        showToast('error', ratingError, 3000);
      } else {
        alert(ratingError);
      }
      return;
    }
    if(!review_form.elements['review'].value.trim()){
      const commentError = currentLang === 'en'
        ? 'Please enter a comment!'
        : 'Vui lòng nhập nhận xét!';
      if(typeof showToast === 'function'){
        showToast('error', commentError, 3000);
      } else {
        alert(commentError);
      }
      return;
    }
    
    let data = new FormData();
    data.append('review_form','');
    data.append('rating',review_form.elements['rating'].value);
    data.append('review',review_form.elements['review'].value);
    data.append('booking_id',review_form.elements['booking_id'].value);
    data.append('room_id',review_form.elements['room_id'].value);
    
    // Thêm ảnh vào FormData (chỉ append 1 lần)
    const files = review_form.elements['review_images[]'].files;
    for(let i = 0; i < files.length; i++){
      data.append('review_images[]', files[i]);
    }

    // Disable button để tránh submit nhiều lần
    const submitBtn = review_form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    const sendingText = currentLang === 'en' ? 'Sending...' : 'Đang gửi...';
    submitBtn.disabled = true;
    submitBtn.innerHTML = `<i class="bi bi-hourglass-split me-2"></i>${sendingText}`;

    let xhr = new XMLHttpRequest();
    xhr.open("POST","ajax/review_room.php",true);
    xhr.onload = function()
    {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
      
      try {
        const response = JSON.parse(this.responseText);
        const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
        if(response.status === 'success'){
          var myModal = document.getElementById('reviewModal');
          var modal = bootstrap.Modal.getInstance(myModal);
          modal.hide();
          const successMsg = response.msg || (currentLang === 'en' ? 'Review submitted successfully!' : 'Đánh giá đã được gửi thành công!');
          if(typeof showToast === 'function'){
            showToast('success', successMsg, 3000);
          }
          setTimeout(() => {
            window.location.href = 'bookings.php?review_status=true';
          }, 1000);
        } else {
          const errorMsg = response.msg || (currentLang === 'en' ? 'Failed to submit review!' : 'Đánh giá thất bại!');
          if(typeof showToast === 'function'){
            showToast('error', errorMsg, 3000);
          } else {
            alert(errorMsg);
          }
        }
      } catch(e) {
        // Fallback cho response không phải JSON (backward compatibility)
        if(this.responseText == 1 || this.responseText.trim() == '1'){
          var myModal = document.getElementById('reviewModal');
          var modal = bootstrap.Modal.getInstance(myModal);
          modal.hide();
          window.location.href = 'bookings.php?review_status=true';
        } else {
          const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
          const errorMsg = currentLang === 'en' 
            ? 'Failed to submit review! Please try again.'
            : 'Đánh giá thất bại! Vui lòng thử lại.';
          if(typeof showToast === 'function'){
            showToast('error', errorMsg, 3000);
          } else {
            alert(errorMsg);
          }
        }
      }
    }
    xhr.onerror = function(){
      const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
      const errorMsg = currentLang === 'en'
        ? 'Connection error. Please try again.'
        : 'Lỗi kết nối. Vui lòng thử lại.';
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
      if(typeof showToast === 'function'){
        showToast('error', errorMsg, 3000);
      } else {
        alert(errorMsg);
      }
    }
    xhr.send(data);
  });
</script>
</body>
</html>
