<!DOCTYPE html>
<html lang="<?php echo $_COOKIE['lang'] ?? 'vi'; ?>">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <?php 
    $current_lang = $_COOKIE['lang'] ?? 'vi';
    function t_contact($key, $lang = 'vi') {
      $translations = [
        'vi' => [
          'contact.pageTitle' => 'Liên hệ',
          'contact.fillAllFields' => 'Vui lòng điền đầy đủ thông tin!',
          'contact.invalidEmail' => 'Email không hợp lệ!',
          'contact.minCharsError' => 'Nội dung tin nhắn phải có ít nhất 10 ký tự!',
          'contact.success' => 'Tin nhắn đã được gửi thành công! Chúng tôi sẽ phản hồi sớm nhất có thể.',
          'contact.error' => 'Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại sau ít phút! Lỗi: ',
        ],
        'en' => [
          'contact.pageTitle' => 'Contact',
          'contact.fillAllFields' => 'Please fill in all fields!',
          'contact.invalidEmail' => 'Invalid email!',
          'contact.minCharsError' => 'Message content must be at least 10 characters!',
          'contact.success' => 'Message sent successfully! We will respond as soon as possible.',
          'contact.error' => 'An error occurred while sending the message. Please try again in a few minutes! Error: ',
        ]
      ];
      return $translations[$lang][$key] ?? $key;
    }
  ?>
  <title><?php echo $settings_r['site_title'] . " - " . t_contact('contact.pageTitle', $current_lang); ?></title>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>
  
  <?php
    // Tạo URL Google Maps đúng format từ địa chỉ
    $address = !empty($contact_r['address']) ? urlencode($contact_r['address']) : '';
    $maps_search_url = !empty($address) ? 'https://www.google.com/maps/search/?api=1&query=' . $address : ($contact_r['gmap'] ?? '#');
  ?>

  <style>
    /* Modern Contact Page - Same style as About Page */
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
    }
    
    /* Hero Section */
    .contact-hero-section{
      margin-bottom: 4rem;
      padding: 3rem 0;
      text-align: center;
      animation: fadeInDown 0.6s ease-out;
    }
    
    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translateY(-30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .contact-hero-content {
      position: relative;
    }
    
    .contact-hero-title{
      font-size: 3rem;
      font-weight: 800;
      color: #1a202c;
      margin-bottom: 1rem;
      letter-spacing: 0.02em;
      text-transform: uppercase;
    }
    .contact-hero-line{
      width: 100px;
      height: 4px;
      background: linear-gradient(90deg, #a78bfa 0%, #ec4899 100%);
      margin: 1.5rem auto;
      border-radius: 2px;
    }
    .contact-hero-desc{
      font-size: 1.1rem;
      line-height: 1.9;
      color: #4a5568;
      margin-top: 1.5rem;
      max-width: 900px;
      margin-left: auto;
      margin-right: auto;
    }
    
    .contact-hero-section .text-uppercase.text-muted {
      color: #6b7280 !important;
      font-weight: 500;
      letter-spacing: 2px;
    }
    .letter-spacing-1{
      letter-spacing: 1px;
    }

    /* Map Card */
    .contact-map-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .contact-map-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 28px rgba(0,0,0,0.12) !important;
    }
    .contact-map-header{
      background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
      border-bottom: 2px solid #e9ecef !important;
    }
    .map-icon-wrapper{
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
    }

    /* Map Info Footer */
    .map-info-footer{
      background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
      border-top: 2px solid #e9ecef;
    }
    .map-info-icon{
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: linear-gradient(135deg, rgba(13,110,253,0.1), rgba(13,110,253,0.05));
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      flex-shrink: 0;
      transition: all 0.3s ease;
    }
    .map-info-footer .col-md-6:hover .map-info-icon{
      transform: scale(1.1) rotate(5deg);
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .map-info-footer h6{
      color: #1f2937;
      font-size: 15px;
    }
    .map-info-footer .badge{
      font-size: 11px;
      padding: 6px 10px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    .map-info-footer .badge:hover{
      background: #0d6efd !important;
      color: #fff !important;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(13,110,253,0.3);
    }

    /* Contact Cards */
    .contact-card{
      background: #ffffff;
      border: 2px solid #e9ecef;
      border-radius: 14px;
      padding: 16px;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .contact-card-link:hover .contact-card{
      transform: translateX(6px);
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    }
    .contact-card-primary:hover{
      border-color: #0d6efd;
      background: linear-gradient(135deg, rgba(13,110,253,0.05), rgba(13,110,253,0.02));
    }
    .contact-card-danger:hover{
      border-color: #dc3545;
      background: linear-gradient(135deg, rgba(220,53,69,0.05), rgba(220,53,69,0.02));
    }
    .contact-card-success:hover{
      border-color: #198754;
      background: linear-gradient(135deg, rgba(25,135,84,0.05), rgba(25,135,84,0.02));
    }
    .contact-card-info{
      border-color: #0dcaf0;
    }

    /* Contact Send Button */
    .contact-send-btn{
      border-radius: 12px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(13,110,253,0.2);
    }
    .contact-send-btn:hover{
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(13,110,253,0.3);
    }

    /* Copy Button */
    .btn-copy-contact{
      position: absolute;
      top: 12px;
      right: 12px;
      width: 32px;
      height: 32px;
      border-radius: 8px;
      border: none;
      background: rgba(0,0,0,0.05);
      color: #6c757d;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: all 0.3s ease;
      cursor: pointer;
      z-index: 10;
    }
    .contact-card:hover .btn-copy-contact{
      opacity: 1;
    }
    .btn-copy-contact:hover{
      background: rgba(13,110,253,0.1);
      color: #0d6efd;
      transform: scale(1.1);
    }
    .btn-copy-contact.copied{
      background: #198754;
      color: #fff;
      opacity: 1;
    }
    .btn-copy-contact.copied i::before{
      content: "\f4c8";
    }

    /* Floating Action Buttons */
    .fab-container{
      position: fixed;
      bottom: 24px;
      right: 24px;
      z-index: 1000;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    .fab{
      width: 56px;
      height: 56px;
      border-radius: 50%;
      border: none;
      box-shadow: 0 4px 16px rgba(0,0,0,0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      color: #fff;
      transition: all 0.3s ease;
      cursor: pointer;
      position: relative;
      overflow: hidden;
    }
    .fab::before{
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255,255,255,0.3);
      transform: translate(-50%, -50%);
      transition: width 0.4s, height 0.4s;
    }
    .fab:hover::before{
      width: 100px;
      height: 100px;
    }
    .fab:hover{
      transform: scale(1.1);
      box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
    .fab-primary{
      background: linear-gradient(135deg, #0d6efd, #0a58ca);
    }
    .fab-danger{
      background: linear-gradient(135deg, #dc3545, #bb2d3b);
    }
    .fab-label{
      position: absolute;
      right: 70px;
      background: #333;
      color: #fff;
      padding: 8px 12px;
      border-radius: 8px;
      font-size: 14px;
      white-space: nowrap;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s;
    }
    .fab:hover .fab-label{
      opacity: 1;
    }

    /* Copy Toast */
    .copy-toast{
      position: fixed;
      bottom: 100px;
      right: 24px;
      background: #198754;
      color: #fff;
      padding: 12px 20px;
      border-radius: 12px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.2);
      z-index: 1001;
      display: flex;
      align-items: center;
      gap: 8px;
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.3s ease;
    }
    .copy-toast.show{
      opacity: 1;
      transform: translateY(0);
    }

    /* Success Animation */
    @keyframes successPulse{
      0%, 100%{ transform: scale(1); }
      50%{ transform: scale(1.05); }
    }
    .form-success{
      animation: successPulse 0.5s ease;
    }

    /* Scroll Animation */
    .fade-in-up{
      opacity: 0;
      transform: translateY(30px);
      transition: all 0.6s ease;
    }
    .fade-in-up.visible{
      opacity: 1;
      transform: translateY(0);
    }

    .contact-info-item {
      transition: all 0.3s ease;
    }
    .contact-info-item:hover {
      transform: translateX(4px);
    }

    .contact-icon-wrapper {
      min-width: 64px;
      height: 64px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .contact-icon-wrapper i {
      font-size: 24px;
      position: relative;
      z-index: 1;
      transition: all 0.3s ease;
    }
    
    .contact-icon-wrapper::before {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: 16px;
      background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);
      opacity: 0;
      transition: opacity 0.3s;
    }
    
    .contact-info-item:hover .contact-icon-wrapper {
      transform: scale(1.1) rotate(5deg);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    
    .contact-info-item:hover .contact-icon-wrapper::before {
      opacity: 1;
    }
    
    .contact-info-item:hover .contact-icon-wrapper i {
      transform: scale(1.15);
    }
    
    /* Modern Icon Backgrounds with Gradient */
    .contact-icon-wrapper.bg-primary-subtle {
      background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    }
    
    .contact-icon-wrapper.bg-primary-subtle .text-primary {
      color: #1976d2 !important;
      text-shadow: 0 2px 4px rgba(25, 118, 210, 0.2);
    }
    
    .contact-icon-wrapper.bg-success-subtle {
      background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    }
    
    .contact-icon-wrapper.bg-success-subtle .text-success {
      color: #388e3c !important;
      text-shadow: 0 2px 4px rgba(56, 142, 60, 0.2);
    }
    
    .contact-icon-wrapper.bg-warning-subtle {
      background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
    }
    
    .contact-icon-wrapper.bg-warning-subtle .text-warning {
      color: #f57c00 !important;
      text-shadow: 0 2px 4px rgba(245, 124, 0, 0.2);
    }
    
    .contact-icon-wrapper.bg-info-subtle {
      background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
    }
    
    .contact-icon-wrapper.bg-info-subtle .text-info {
      color: #0097a7 !important;
      text-shadow: 0 2px 4px rgba(0, 151, 167, 0.2);
    }
    
    .contact-icon-wrapper.bg-danger-subtle {
      background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%);
    }
    
    .contact-icon-wrapper.bg-danger-subtle .text-danger {
      color: #c2185b !important;
      text-shadow: 0 2px 4px rgba(194, 24, 91, 0.2);
    }
    
    /* Form Icon Wrappers */
    .form-icon-wrapper.bg-primary-subtle {
      background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    }
    
    .form-icon-wrapper.bg-warning-subtle {
      background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
    }
    
    .form-icon-wrapper.bg-info-subtle {
      background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
    }
    
    .form-icon-wrapper.bg-success-subtle {
      background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    }

    /* Modern Form Icons */
    .modern-icon-wrapper {
      width: 48px;
      height: 48px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .modern-icon-wrapper i {
      font-size: 20px;
    }
    
    .form-icon-wrapper {
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    }
    
    .form-icon-wrapper i {
      font-size: 16px;
    }
    
    .form-label:hover .form-icon-wrapper {
      transform: scale(1.1);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    
    /* Modern Social Links */
    .social-link {
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 48px;
      height: 48px;
      border-radius: 14px;
      background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
      border: 2px solid #e5e7eb;
      position: relative;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }
    
    .social-link::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(13, 110, 253, 0.1);
      transform: translate(-50%, -50%);
      transition: width 0.4s, height 0.4s;
    }
    
    .social-link:hover::before {
      width: 100px;
      height: 100px;
    }
    
    .social-link:hover {
      transform: translateY(-4px) scale(1.1);
      box-shadow: 0 8px 20px rgba(13, 110, 253, 0.2);
      border-color: #0d6efd;
      background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%);
    }
    
    .social-link i {
      position: relative;
      z-index: 1;
      transition: transform 0.3s;
    }
    
    .social-link:hover i {
      transform: scale(1.2);
    }
    
    .social-link.text-primary:hover {
      background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    }
    
    .social-link.text-danger:hover {
      background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%);
    }
    
    .social-link.text-dark:hover {
      background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
    }

    .contact-input {
      border: 2px solid #e5e7eb;
      border-radius: 12px;
      padding: 12px 16px;
      transition: all 0.3s ease;
      font-size: 15px;
    }
    .contact-input:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
      outline: none;
    }

    .contact-submit-btn {
      border-radius: 12px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }
    .contact-submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(13, 110, 253, 0.3);
    }
    .contact-submit-btn:active {
      transform: translateY(0);
    }
    .contact-submit-btn:disabled {
      opacity: 0.7;
      cursor: not-allowed;
    }

    .contact-submit-btn.loading::after {
      content: '';
      position: absolute;
      width: 20px;
      height: 20px;
      top: 50%;
      left: 50%;
      margin-left: -10px;
      margin-top: -10px;
      border: 3px solid rgba(255,255,255,0.3);
      border-top-color: #fff;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
      .contact-hero-section {
        padding: 2rem 0;
      }
      
      .contact-hero-title {
        font-size: 2rem;
      }
      
      .contact-icon-wrapper {
        min-width: 48px;
        height: 48px;
        padding: 12px !important;
      }
    }
  </style>

  <!-- Hero Section -->
  <div class="container my-5 px-4">
    <div class="contact-hero-section">
      <div class="contact-hero-content">
        <div class="text-center">
          <p class="text-uppercase text-muted small mb-2" style="letter-spacing: 2px;" data-i18n="contact.support247">
            <i class="bi bi-headset me-2"></i>HỖ TRỢ 24/7
          </p>
          <h1 class="contact-hero-title" data-i18n="contact.title">LIÊN HỆ</h1>
          <div class="contact-hero-line"></div>
          <p class="contact-hero-desc" data-i18n="contact.readyToHelp">
            Chúng tôi luôn sẵn sàng hỗ trợ bạn mọi lúc
          </p>
        </div>
      </div>
    </div>
  </div>

    <!-- Nội dung chính -->
  <div class="container mb-5">
    <div class="row g-4">
       <!-- Cột trái: hiển thị bản đồ và thông tin liên hệ -->
      <div class="col-lg-6 col-md-6">

        <!-- Bản đồ -->
        <div class="contact-map-card bg-white rounded-4 shadow-lg border-0 mb-4 overflow-hidden">
          <div class="contact-map-header d-flex align-items-center justify-content-between p-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
              <div class="map-icon-wrapper">
                <i class="bi bi-geo-alt-fill text-primary"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-0" data-i18n="contact.hotelLocation">Vị trí khách sạn</h6>
                <small class="text-muted" data-i18n="contact.viewMap">Xem vị trí trên bản đồ</small>
              </div>
            </div>
            <a href="<?php echo $maps_search_url; ?>" target="_blank" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-2">
              <i class="bi bi-box-arrow-up-right"></i>
              <span data-i18n="contact.viewLargeMap">Xem bản đồ lớn hơn</span>
            </a>
          </div>
          <iframe 
          class="w-100"
          height="420px"
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3917.157884165833!2d106.33733957589317!3d9.934397774158645!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a028df3db49bcb%3A0xc7b070d16d2e2f54!2zVHLGsOG7nW5nIMSQw6BpIGjhu41jIFRy4bqhaSBWaW5o!5e0!3m2!1svi!2svi!4v1734444444444!5m2!1svi!2svi"
          loading="lazy"
          allowfullscreen
          style="border: 0;">
          </iframe>
          
          <!-- Thông tin chi tiết bên dưới bản đồ -->
          <div class="map-info-footer p-4 bg-light">
            <div class="row g-3">
              <!-- Địa chỉ chi tiết -->
              <div class="col-md-6">
                <div class="d-flex align-items-start gap-3">
                  <div class="map-info-icon">
                    <i class="bi bi-geo-alt-fill text-primary"></i>
                  </div>
                  <div>
                    <h6 class="fw-bold mb-2" data-i18n="contact.address">Địa chỉ</h6>
                    <p class="text-muted mb-0 small"><?php echo $contact_r['address'] ?></p>
                    <a href="<?php echo $maps_search_url; ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                      <i class="bi bi-arrow-right-circle me-1"></i><span data-i18n="contact.directions">Chỉ đường</span>
                    </a>
                  </div>
                </div>
              </div>
              
              <!-- Hướng dẫn đường đi -->
              <div class="col-md-6">
                <div class="d-flex align-items-start gap-3">
                  <div class="map-info-icon">
                    <i class="bi bi-signpost-2-fill text-success"></i>
                  </div>
                  <div>
                    <h6 class="fw-bold mb-2" data-i18n="contact.guide">Hướng dẫn</h6>
                    <ul class="text-muted small mb-0 ps-3">
                      <li data-i18n="contact.guide1">Dễ dàng tìm thấy từ trung tâm thành phố</li>
                      <li data-i18n="contact.guide2">Có bãi đỗ xe miễn phí</li>
                      <li data-i18n="contact.guide3">Gần các điểm du lịch nổi tiếng</li>
                    </ul>
                  </div>
                </div>
              </div>
              
              <!-- Điểm đánh dấu gần đó -->
              <div class="col-md-6">
                <div class="d-flex align-items-start gap-3">
                  <div class="map-info-icon">
                    <i class="bi bi-pin-map-fill text-warning"></i>
                  </div>
                  <div>
                    <h6 class="fw-bold mb-2" data-i18n="contact.nearby">Điểm gần đây</h6>
                    <div class="d-flex flex-wrap gap-2">
                      <span class="badge bg-light text-dark border"><span data-i18n="contact.nightMarket">Chợ đêm</span> - 1.1 <span data-i18n="contact.km">km</span></span>
                      <span class="badge bg-light text-dark border"><span data-i18n="contact.cathedral">Nhà thờ lớn</span> - 1.6 <span data-i18n="contact.km">km</span></span>
                      <span class="badge bg-light text-dark border"><span data-i18n="contact.station">Ga trung tâm</span> - 2.0 <span data-i18n="contact.km">km</span></span>
                      <span class="badge bg-light text-dark border"><span data-i18n="contact.supermarket">Siêu thị</span> - 400 <span data-i18n="contact.m">m</span></span>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Giao thông -->
              <div class="col-md-6">
                <div class="d-flex align-items-start gap-3">
                  <div class="map-info-icon">
                    <i class="bi bi-bus-front-fill text-info"></i>
                  </div>
                  <div>
                    <h6 class="fw-bold mb-2" data-i18n="contact.transportation">Giao thông</h6>
                    <div class="d-flex flex-column gap-1">
                      <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-car-front-fill text-primary small"></i>
                        <span class="small text-muted" data-i18n="contact.transport1">Có bãi đỗ xe</span>
                      </div>
                      <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-taxi-front-fill text-warning small"></i>
                        <span class="small text-muted" data-i18n="contact.transport2">Dễ gọi taxi</span>
                      </div>
                      <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-bicycle text-success small"></i>
                        <span class="small text-muted" data-i18n="contact.transport3">Có thể đi bộ từ trung tâm</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Thông tin liên hệ -->
        <div class="bg-white rounded-4 shadow-lg border-0 p-4">
          <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
            <div class="modern-icon-wrapper bg-primary-subtle rounded-3 p-2">
              <i class="bi bi-headset text-primary"></i>
            </div>
            <span data-i18n="contact.quickConnect">Kết nối nhanh</span>
          </h5>

          <!-- Hotline -->
          <div class="contact-card contact-card-primary mb-3 position-relative" data-copy="+<?php echo $contact_r['pn1'] ?>">
            <a href="tel:+<?php echo $contact_r['pn1'] ?>" class="contact-card-link text-decoration-none d-block">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <h6 class="fw-bold mb-0" data-i18n="contact.hotline">Hotline</h6>
                  <p class="text-muted mb-0 small phone-number">+<?php echo $contact_r['pn1'] ?></p>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
              </div>
            </a>
            <button class="btn-copy-contact" data-copy="+<?php echo $contact_r['pn1'] ?>" data-i18n-title="contact.copyPhone" title="Sao chép số điện thoại">
              <i class="bi bi-clipboard"></i>
            </button>
          </div>

          <!-- Email -->
          <div class="contact-card contact-card-danger mb-3 position-relative" data-copy="<?php echo $contact_r['email'] ?>">
            <a href="mailto:<?php echo $contact_r['email'] ?>" class="contact-card-link text-decoration-none d-block">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <h6 class="fw-bold mb-0" data-i18n="contact.emailLabel">Email</h6>
                  <p class="text-muted mb-0 small email-address"><?php echo $contact_r['email'] ?></p>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
              </div>
            </a>
            <button class="btn-copy-contact" data-copy="<?php echo $contact_r['email'] ?>" data-i18n-title="contact.copyEmail" title="Sao chép email">
              <i class="bi bi-clipboard"></i>
            </button>
          </div>

          <!-- Địa chỉ -->
          <a href="<?php echo $maps_search_url; ?>" target="_blank" class="contact-card-link text-decoration-none">
            <div class="contact-card contact-card-success mb-3">
              <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                  <div class="contact-icon-wrapper bg-success-subtle">
                    <i class="bi bi-geo-alt-fill text-success"></i>
                  </div>
                  <div>
                    <h6 class="fw-bold mb-0" data-i18n="contact.address">Địa chỉ</h6>
                    <p class="text-muted mb-0 small"><?php echo $contact_r['address'] ?></p>
                  </div>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
              </div>
            </div>
          </a>

          <!-- Giờ làm việc -->
          <div class="contact-card contact-card-info mb-3">
            <div class="d-flex align-items-center gap-3">
              <div class="contact-icon-wrapper bg-info-subtle">
                <i class="bi bi-clock-history text-info"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1" data-i18n="contact.workingHours">Giờ làm việc</h6>
                <p class="text-muted mb-0 small" data-i18n="contact.hours247">Thứ 2 - Chủ nhật: 24/7</p>
                <p class="text-muted mb-0 small" data-i18n="contact.supportAnytime">Hỗ trợ khách hàng mọi lúc</p>
              </div>
            </div>
          </div>

          <!-- Mạng xã hội -->
          <div class="mb-3">
            <h6 class="fw-semibold mb-3" data-i18n="contact.followUs">Theo dõi chúng tôi</h6>
            <div class="d-flex align-items-center gap-2">
              <?php 
              // Hiển thị Twitter nếu có
                if($contact_r['tw']!=''){
                  echo<<<data
                    <a href="$contact_r[tw]" target="_blank" class="social-link text-dark" title="Twitter">
                      <i class="bi bi-twitter-x"></i>
                    </a>
                  data;
                }
              ?>
              <!-- Facebook -->
              <a href="<?php echo $contact_r['fb'] ?>" target="_blank" class="social-link text-primary" title="Facebook">
                <i class="bi bi-facebook"></i>
              </a>
              <!-- Instagram -->
              <a href="<?php echo $contact_r['insta'] ?>" target="_blank" class="social-link text-danger" title="Instagram">
                <i class="bi bi-instagram"></i>
              </a>
            </div>
          </div>

          <!-- Gửi tin nhắn button -->
          <a href="#contact-form" class="btn btn-primary w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2 contact-send-btn">
            <i class="bi bi-chat-dots-fill"></i>
            <span data-i18n="contact.sendMessage">Gửi tin nhắn</span>
          </a>

          <!-- Phản hồi nhanh badge -->
          <div class="mt-3 p-3 rounded-3 border border-warning bg-warning bg-opacity-10 d-flex align-items-center gap-2">
            <i class="bi bi-lightning-charge-fill text-warning fs-5"></i>
            <span class="small fw-semibold text-dark" data-i18n="contact.responseTime">Phản hồi trong vòng 1 giờ</span>
          </div>
        </div>

        <!-- Cột phải: form gửi lời nhắn -->
      </div>
      <div class="col-lg-6 col-md-6">
        <div class="bg-white rounded-4 shadow-lg border-0 p-4 h-100" id="contact-form-section">
          <div class="mb-4">
            <h5 class="fw-bold h-font mb-2 d-flex align-items-center gap-2">
              <div class="modern-icon-wrapper bg-primary-subtle rounded-3 p-2">
                <i class="bi bi-chat-dots-fill text-primary"></i>
              </div>
              <span data-i18n="contact.leaveMessage">Để lại lời nhắn</span>
            </h5>
            <p class="text-muted small mb-0 d-flex align-items-center gap-1">
              <i class="bi bi-clock-history text-primary"></i>
              <span data-i18n="contact.response24h">Chúng tôi sẽ phản hồi trong vòng 24 giờ</span>
            </p>
          </div>

          <form method="POST" action="" id="contact-form" class="contact-form-modern" novalidate>
            <!-- Tên người gửi -->
            <div class="mb-4">
              <label class="form-label fw-semibold mb-2 d-flex align-items-center gap-2">
                <div class="form-icon-wrapper bg-primary-subtle rounded-2 p-1">
                  <i class="bi bi-person-fill text-primary"></i>
                </div>
                <span><span data-i18n="contact.nameLabel">Tên</span> <span class="text-danger">*</span></span>
              </label>
              <input 
                name="name" 
                required 
                type="text" 
                class="form-control shadow-none contact-input"
                data-i18n-placeholder="contact.namePlaceholder"
                placeholder="Nhập tên của bạn"
                autocomplete="name"
                value="<?php echo isset($_SESSION['login']) && $_SESSION['login'] ? htmlspecialchars($_SESSION['uName'] ?? '') : ''; ?>">
            </div>

            <!-- Email người gửi -->
            <div class="mb-4">
              <label class="form-label fw-semibold mb-2 d-flex align-items-center gap-2">
                <div class="form-icon-wrapper bg-warning-subtle rounded-2 p-1">
                  <i class="bi bi-envelope-fill text-warning"></i>
                </div>
                <span><span data-i18n="contact.emailLabel">Email</span> <span class="text-danger">*</span></span>
              </label>
              <input 
                name="email" 
                required 
                type="email" 
                class="form-control shadow-none contact-input"
                placeholder="your.email@example.com"
                autocomplete="email"
                value="<?php 
                  if(isset($_SESSION['login']) && $_SESSION['login']){
                    $user_res = select("SELECT email FROM user_cred WHERE id=?", [$_SESSION['uId']], "i");
                    if($user_res && mysqli_num_rows($user_res)){
                      $user_data = mysqli_fetch_assoc($user_res);
                      echo htmlspecialchars($user_data['email'] ?? '');
                    }
                  }
                ?>">
            </div>

             <!-- Tiêu đề thư -->
            <div class="mb-4">
              <label class="form-label fw-semibold mb-2 d-flex align-items-center gap-2">
                <div class="form-icon-wrapper bg-info-subtle rounded-2 p-1">
                  <i class="bi bi-tag-fill text-info"></i>
                </div>
                <span><span data-i18n="contact.subjectLabel">Tiêu đề</span> <span class="text-danger">*</span></span>
              </label>
              <input 
                name="subject" 
                required 
                type="text" 
                class="form-control shadow-none contact-input"
                data-i18n-placeholder="contact.subjectPlaceholder"
                placeholder="Tiêu đề tin nhắn">
            </div>

            <!-- Nội dung lời nhắn -->
            <div class="mb-4">
              <label class="form-label fw-semibold mb-2 d-flex align-items-center gap-2">
                <div class="form-icon-wrapper bg-success-subtle rounded-2 p-1">
                  <i class="bi bi-chat-left-text-fill text-success"></i>
                </div>
                <span><span data-i18n="contact.messageLabel">Nội dung</span> <span class="text-danger">*</span></span>
              </label>
              <textarea 
                name="message" 
                required 
                class="form-control shadow-none contact-input" 
                rows="6" 
                style="resize: none;"
                data-i18n-placeholder="contact.messagePlaceholder"
                placeholder="Nhập nội dung tin nhắn của bạn..."></textarea>
              <div class="form-text small d-flex align-items-center gap-1 mt-2">
                <i class="bi bi-info-circle-fill text-primary"></i>
                <span data-i18n="contact.minChars">Tối thiểu 10 ký tự</span>
              </div>
            </div>

            <!-- Nút gửi -->
            <button type="submit" name="send" class="btn btn-primary w-100 py-3 fw-semibold contact-submit-btn d-flex align-items-center justify-content-center gap-2">
              <i class="bi bi-send-fill"></i>
              <span data-i18n="contact.sendMessage">Gửi tin nhắn</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>


  <?php 

  /*
      Xử lý khi người dùng gửi form liên hệ
      - Lọc dữ liệu (filteration)
      - Lưu vào bảng user_queries
      - Hiển thị thông báo phản hồi (alert)
    */
    if(isset($_POST['send']))
    {
      // Debug: Log POST data
      error_log("=== CONTACT FORM SUBMISSION ===");
      error_log("POST data received: " . print_r($_POST, true));
      
      // Lọc dữ liệu form để tránh tấn công XSS
      $frm_data = filteration($_POST);
      error_log("After filteration: " . print_r($frm_data, true));

      // Kiểm tra dữ liệu đầu vào
      $name = trim($frm_data['name'] ?? '');
      $email = trim($frm_data['email'] ?? '');
      $subject = trim($frm_data['subject'] ?? '');
      $message = trim($frm_data['message'] ?? '');
      
      error_log("Extracted values - name: '$name', email: '$email', subject: '$subject', message length: " . strlen($message));
      
      if(empty($name) || empty($email) || empty($subject) || empty($message)){
        alert('error', t_contact('contact.fillAllFields', $current_lang));
      }
      else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        alert('error', t_contact('contact.invalidEmail', $current_lang));
      }
      else if(strlen($message) < 10){
        alert('error', t_contact('contact.minCharsError', $current_lang));
      }
      else{
        // Câu lệnh SQL thêm liên hệ vào bảng user_queries
        $q = "INSERT INTO `user_queries`(`name`, `email`, `subject`, `message`) VALUES (?,?,?,?)";
        $values = [$name, $email, $subject, $message];

        // Thực thi câu lệnh
        error_log("Attempting insert with values: " . print_r($values, true));
        $res = insert($q,$values,'ssss');
        error_log("Insert result: " . var_export($res, true));

        // Kiểm tra kết quả - thêm logging để debug
        if($res !== false && $res > 0){
          error_log("Insert SUCCESS - Message saved to database");
          // Insert thành công
          alert('success', t_contact('contact.success', $current_lang));
          // Reset form sau khi gửi thành công
          echo "<script>
            setTimeout(function(){
              const form = document.getElementById('contact-form');
              if(form) {
                form.reset();
                const counter = document.getElementById('message-counter');
                if(counter) {
                  const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
                  const charsText = currentLang === 'en' ? 'characters' : 'ký tự';
                  counter.textContent = '0 ' + charsText;
                }
                form.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
                  el.classList.remove('is-valid', 'is-invalid');
                });
                form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
              }
            }, 1000);
          </script>";
        }
        else{
          // Insert thất bại - kiểm tra lại database
          global $con;
          $error_msg = isset($con) ? mysqli_error($con) : 'No connection';
          
          // Double check: Query xem có record mới không
          $verify_q = "SELECT sr_no FROM user_queries WHERE email=? AND subject=? ORDER BY sr_no DESC LIMIT 1";
          $verify_res = select($verify_q, [$email, $subject], 'ss');
          
          if($verify_res && mysqli_num_rows($verify_res) > 0){
            // Có record trong database - insert đã thành công
            alert('success', t_contact('contact.success', $current_lang));
            echo "<script>
              setTimeout(function(){
                const form = document.getElementById('contact-form');
                if(form) {
                  form.reset();
                  const counter = document.getElementById('message-counter');
                  if(counter) {
                    const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
                    const charsText = currentLang === 'en' ? 'characters' : 'ký tự';
                    counter.textContent = '0 ' + charsText;
                  }
                  form.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
                    el.classList.remove('is-valid', 'is-invalid');
                  });
                  form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
                }
              }, 1000);
            </script>";
          } else {
            // Không có record - insert thất bại
            alert('error', t_contact('contact.error', $current_lang) . htmlspecialchars($error_msg));
          }
        }
      }
    }
  ?>

  <!-- Copy Toast -->
  <div class="copy-toast" id="copy-toast">
    <i class="bi bi-check-circle-fill"></i>
    <span data-i18n="contact.copied">Đã sao chép!</span>
  </div>

  <?php require('inc/footer.php'); ?>
  <?php require('inc/modals.php'); ?>

  <script>
    // Form validation and UX improvements
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.getElementById('contact-form');
      if(!form){
        console.error('Contact form not found!');
        return;
      }
      const submitBtn = form.querySelector('button[type="submit"]');
      const inputs = form.querySelectorAll('input, textarea');

      // Add input validation
      inputs.forEach(input => {
        input.addEventListener('blur', function() {
          validateField(this);
        });

        input.addEventListener('input', function() {
          if (this.classList.contains('is-invalid')) {
            validateField(this);
          }
        });
      });

      function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        // Remove previous validation classes
        field.classList.remove('is-valid', 'is-invalid');
        const existingFeedback = field.parentElement.querySelector('.invalid-feedback');
        if (existingFeedback) {
          existingFeedback.remove();
        }

        // Validate based on field type
        const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
        const requiredText = currentLang === 'en' ? 'This field is required' : 'Trường này là bắt buộc';
        const invalidEmailText = currentLang === 'en' ? 'Invalid email' : 'Email không hợp lệ';
        const minCharsText = currentLang === 'en' ? 'Content must be at least 10 characters' : 'Nội dung phải có ít nhất 10 ký tự';
        
        if (field.hasAttribute('required') && !value) {
          isValid = false;
          errorMessage = requiredText;
        } else if (field.type === 'email' && value) {
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = invalidEmailText;
          }
        } else if (field.name === 'message' && value && value.length < 10) {
          isValid = false;
          errorMessage = minCharsText;
        }

        // Apply validation classes
        if (field.hasAttribute('required') || value) {
          if (isValid && value) {
            field.classList.add('is-valid');
          } else if (!isValid) {
            field.classList.add('is-invalid');
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = errorMessage;
            field.parentElement.appendChild(feedback);
          }
        }
      }

      // Character counter for message
      const messageField = form.querySelector('textarea[name="message"]');
      if (messageField) {
        const counter = document.createElement('div');
        counter.className = 'form-text text-end small mt-1';
        counter.id = 'message-counter';
        messageField.parentElement.appendChild(counter);

        messageField.addEventListener('input', function() {
          const length = this.value.length;
          const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
          const charsText = currentLang === 'en' ? 'characters' : 'ký tự';
          counter.textContent = `${length} ${charsText}`;
          if (length < 10) {
            counter.classList.add('text-danger');
            counter.classList.remove('text-success');
          } else {
            counter.classList.remove('text-danger');
            counter.classList.add('text-success');
          }
        });
      }

      // Form submission
      form.addEventListener('submit', function(e) {
        console.log('Form submit event triggered');
        let isFormValid = true;

        // Validate all fields
        const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
        const requiredText = currentLang === 'en' ? 'This field is required' : 'Trường này là bắt buộc';
        const minCharsText = currentLang === 'en' ? 'Content must be at least 10 characters' : 'Nội dung phải có ít nhất 10 ký tự';
        const sendingText = currentLang === 'en' ? 'Sending...' : 'Đang gửi...';
        
        inputs.forEach(input => {
          validateField(input);
          if (input.hasAttribute('required') && !input.value.trim()) {
            input.classList.add('is-invalid');
            if (!input.parentElement.querySelector('.invalid-feedback')) {
              const feedback = document.createElement('div');
              feedback.className = 'invalid-feedback';
              feedback.textContent = requiredText;
              input.parentElement.appendChild(feedback);
            }
            isFormValid = false;
          } else if (input.classList.contains('is-invalid')) {
            isFormValid = false;
          }
        });

        // Validate message length
        if (messageField && messageField.value.trim().length < 10) {
          messageField.classList.add('is-invalid');
          if (!messageField.parentElement.querySelector('.invalid-feedback')) {
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = minCharsText;
            messageField.parentElement.appendChild(feedback);
          }
          isFormValid = false;
        }

        console.log('Form validation result:', isFormValid);

        if (!isFormValid) {
          console.log('Form validation failed - preventing submit');
          e.preventDefault();
          e.stopPropagation();
          const firstInvalid = form.querySelector('.is-invalid');
          if (firstInvalid) {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstInvalid.focus();
          }
          const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
          const sendMessageText = currentLang === 'en' ? 'Send Message' : 'Gửi tin nhắn';
          submitBtn.disabled = false;
          submitBtn.classList.remove('loading');
          submitBtn.innerHTML = '<i class="bi bi-send-fill"></i><span>' + sendMessageText + '</span>';
          return false;
        }

        // Form is valid - show loading and submit
        console.log('Form validation passed - allowing submit');
        
        // IMPORTANT: Don't disable button or prevent default - let form submit normally
        // The form will POST to server and PHP will handle it
        // Just show loading state briefly
        const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
        const sendingText = currentLang === 'en' ? 'Sending...' : 'Đang gửi...';
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>' + sendingText;
        
        // Success animation
        setTimeout(() => {
          form.classList.add('form-success');
          setTimeout(() => {
            form.classList.remove('form-success');
          }, 500);
        }, 100);
        
        // Allow form to submit normally - don't prevent default
        // Form will POST to server and reload page with success/error message
        console.log('Form will submit to server');
      });
    });

    // Copy to clipboard functionality
    document.querySelectorAll('.btn-copy-contact').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const text = this.getAttribute('data-copy');
        const toast = document.getElementById('copy-toast');
        
        // Update button title if needed
        if (window.i18n && this.hasAttribute('data-i18n-title')) {
          const key = this.getAttribute('data-i18n-title');
          const translatedTitle = window.i18n.translate(key);
          if (translatedTitle) {
            this.setAttribute('title', translatedTitle);
          }
        }
        
        navigator.clipboard.writeText(text).then(() => {
          // Show toast
          toast.classList.add('show');
          setTimeout(() => {
            toast.classList.remove('show');
          }, 2000);
          
          // Update button state
          this.classList.add('copied');
          setTimeout(() => {
            this.classList.remove('copied');
          }, 2000);
        }).catch(() => {
          // Fallback for older browsers
          const textarea = document.createElement('textarea');
          textarea.value = text;
          document.body.appendChild(textarea);
          textarea.select();
          document.execCommand('copy');
          document.body.removeChild(textarea);
          
          toast.classList.add('show');
          setTimeout(() => {
            toast.classList.remove('show');
          }, 2000);
          
          this.classList.add('copied');
          setTimeout(() => {
            this.classList.remove('copied');
          }, 2000);
        });
      });
    });

    // Scroll animations
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    }, observerOptions);

    document.querySelectorAll('.contact-card, .contact-map-card, #contact-form-section').forEach(el => {
      el.classList.add('fade-in-up');
      observer.observe(el);
    });

    // Smooth scroll to form
    document.querySelectorAll('a[href="#contact-form"]').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        const formSection = document.getElementById('contact-form-section');
        if (formSection) {
          formSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
          setTimeout(() => {
            formSection.querySelector('input[name="name"]')?.focus();
          }, 500);
        }
      });
    });
  </script>

</body>
</html>