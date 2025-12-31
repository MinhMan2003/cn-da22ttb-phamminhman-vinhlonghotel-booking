<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - Danh sách phòng</title>
  <style>
    /* Enhanced List Hero Section - Modern Style */
    .list-hero {
      background: #ffffff;
      border: none;
      border-radius: 24px;
      padding: 32px 36px;
      margin-bottom: 28px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
      position: relative;
      overflow: visible;
    }
    
    .list-hero::after {
      content: '';
      position: absolute;
      top: -2px;
      left: -2px;
      right: -2px;
      bottom: -2px;
      background: linear-gradient(135deg, #0f5d7a, #0ea5e9, #0f5d7a);
      border-radius: 24px;
      z-index: -1;
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    
    .list-hero:hover::after {
      opacity: 0.1;
    }
    
    .list-hero .vl-badge {
      background: #0f5d7a;
      border: none;
      color: #ffffff;
      padding: 10px 18px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 12px;
      box-shadow: 0 4px 12px rgba(15, 93, 122, 0.25);
      transition: all 0.3s ease;
      letter-spacing: 0.5px;
    }
    
    .list-hero .vl-badge:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(15, 93, 122, 0.35);
      background: #0c4a62;
    }
    
    .list-hero .vl-badge i {
      color: #ffffff;
    }
    
    .list-hero .text-muted.small {
      background: #f8f9fa;
      padding: 10px 16px;
      border-radius: 10px;
      border: 1px solid #e9ecef;
      font-size: 13px;
      color: #495057;
      transition: all 0.3s ease;
    }
    
    .list-hero .text-muted.small:hover {
      background: #e9ecef;
      border-color: #dee2e6;
    }
    
    .list-hero .text-muted.small i {
      color: #ffc107;
    }
    
    .list-hero h2 {
      font-size: 2.5rem;
      font-weight: 900;
      color: #000000;
      margin: 20px 0 16px;
      display: flex;
      align-items: center;
      gap: 14px;
      letter-spacing: -0.5px;
    }
    
    .list-hero h2 i {
      font-size: 2.2rem;
      color: #0f5d7a;
      background: linear-gradient(135deg, #0f5d7a 0%, #0ea5e9 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .list-hero p {
      color: #6c757d;
      font-size: 15px;
      line-height: 1.7;
      margin: 0;
      display: flex;
      align-items: flex-start;
      gap: 10px;
      font-weight: 500;
    }
    
    .list-hero p i {
      margin-top: 3px;
      color: #0f5d7a;
      font-size: 18px;
    }
    
    /* Modern Rooms Page Enhancements */
    .filter-title{ 
      font-size:17px; 
      font-weight:700; 
      color: #1f2937;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .filter-title::before {
      content: '';
      width: 4px;
      height: 20px;
      background: linear-gradient(180deg, #0d6efd, #0ea5e9);
      border-radius: 2px;
    }
    
    .price-chip{
      background:linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
      border:2px solid #e5e7eb;
      border-radius:999px;
      padding:6px 14px;
      box-shadow:0 2px 8px rgba(0,0,0,0.06);
      font-weight:600;
      color:#0d6efd;
      transition:all 0.3s ease;
    }
    
    .price-chip:hover {
      border-color: #0d6efd;
      box-shadow:0 4px 12px rgba(13,110,253,0.15);
      transform: translateY(-2px);
    }
    
    .price-range-wrap input[type=range]{
      accent-color:#0d6efd;
      height:8px;
      background:linear-gradient(90deg,#e5e7eb,#d1d5db);
      border-radius:12px;
      transition:all 0.3s ease;
    }
    
    .price-range-wrap input[type=range]:hover{
      accent-color:#0ea5e9;
    }
    
    .price-range-wrap input[type=range]::-webkit-slider-thumb{
      -webkit-appearance:none;
      appearance:none;
      width:20px;
      height:20px;
      border-radius:50%;
      border:3px solid #ffffff;
      background:linear-gradient(135deg,#0d6efd,#0ea5e9);
      box-shadow:0 4px 12px rgba(13,110,253,0.3);
      cursor:pointer;
      transition:all 0.3s ease;
    }
    
    .price-range-wrap input[type=range]::-webkit-slider-thumb:hover{
      transform:scale(1.2);
      box-shadow:0 6px 16px rgba(13,110,253,0.4);
    }
    
    .price-range-wrap input[type=range]::-moz-range-thumb{
      width:20px;
      height:20px;
      border-radius:50%;
      border:3px solid #ffffff;
      background:linear-gradient(135deg,#0d6efd,#0ea5e9);
      box-shadow:0 4px 12px rgba(13,110,253,0.3);
      cursor:pointer;
      transition:all 0.3s ease;
    }
    
    .price-range-wrap input[type=range]::-moz-range-thumb:hover{
      transform:scale(1.2);
      box-shadow:0 6px 16px rgba(13,110,253,0.4);
    }
    
    /* Loading Skeleton */
    .rooms-loading-skeleton {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1.5rem;
    }
    
    .room-card-skeleton {
      background: #ffffff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .skeleton-image {
      width: 100%;
      height: 220px;
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200% 100%;
      animation: skeleton-loading 1.5s ease-in-out infinite;
    }
    
    .skeleton-content {
      padding: 1rem;
    }
    
    .skeleton-title {
      width: 70%;
      height: 20px;
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200% 100%;
      animation: skeleton-loading 1.5s ease-in-out infinite;
      border-radius: 4px;
      margin-bottom: 12px;
    }
    
    .skeleton-text {
      width: 100%;
      height: 14px;
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200% 100%;
      animation: skeleton-loading 1.5s ease-in-out infinite;
      border-radius: 4px;
      margin-bottom: 8px;
    }
    
    .skeleton-text.short {
      width: 60%;
    }
    
    .skeleton-price {
      width: 40%;
      height: 24px;
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200% 100%;
      animation: skeleton-loading 1.5s ease-in-out infinite;
      border-radius: 4px;
      margin-top: 12px;
    }
    
    @keyframes skeleton-loading {
      0% { background-position: 200% 0; }
      100% { background-position: -200% 0; }
    }
    
    /* Empty State */
    .empty-state {
      padding: 4rem 2rem;
    }
    
    .empty-icon {
      animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }
    
    /* View Toggle */
    .view-toggle-btn {
      border-radius: 8px;
      padding: 6px 12px;
      transition: all 0.3s ease;
    }
    
    .view-toggle-btn.active {
      background: #0ea5e9;
      border-color: #0ea5e9;
      color: #ffffff;
    }
    
    .view-toggle-btn:hover:not(.active) {
      background: rgba(14, 165, 233, 0.1);
      border-color: #0ea5e9;
      color: #0ea5e9;
    }
    
    /* List View */
    .rooms-results.list-view {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }
    
    .rooms-results.list-view .room-card,
    .rooms-results.list-view .room-item {
      display: flex;
      flex-direction: row;
      max-width: 100%;
    }
    
    .rooms-results.list-view .room-card img,
    .rooms-results.list-view .room-item img {
      width: 300px;
      height: 200px;
      object-fit: cover;
      flex-shrink: 0;
    }
    
    .home-fav-btn{
      position:absolute;
      top:12px;
      right:12px;
      width:44px;
      height:44px;
      border:none;
      border-radius:50%;
      background:rgba(255,255,255,0.95);
      backdrop-filter:blur(10px);
      box-shadow:0 4px 16px rgba(0,0,0,0.15);
      display:flex;
      align-items:center;
      justify-content:center;
      color:#ef4444;
      z-index:5;
      transition:all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .home-fav-btn:hover{
      transform:scale(1.1);
      box-shadow:0 6px 20px rgba(239,68,68,0.3);
      background:#ffffff;
    }
    
    .home-fav-btn.active{
      background:linear-gradient(135deg,#ffeae6,#ffd1cc);
      color:#e11d48;
      box-shadow:0 4px 16px rgba(225,29,72,0.25);
    }
    
    .home-fav-btn.active:hover{
      box-shadow:0 6px 20px rgba(225,29,72,0.35);
    }
    
    .list-card__img-wrap{
      position:relative;
      overflow:hidden;
      border-radius:16px;
    }
    .list-card__badge--dest{
      background:#e0f2fe;
      color:#0d6efd;
      border:1px solid #bfdbfe;
    }
    
    .list-card__img{
      border-radius:16px;
      transition:transform 0.4s ease;
    }
    
    .list-card__img-wrap:hover .list-card__img{
      transform:scale(1.05);
    }
    
    /* Enhanced Filter Card */
    .filter-card {
      background: rgba(255,255,255,0.98);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(226, 231, 240, 0.8);
      box-shadow: 0 20px 50px rgba(10,38,70,0.12);
    }
    
    .filter-card .border {
      border: 2px solid #e5e7eb !important;
      border-radius: 16px !important;
      transition: all 0.3s ease;
    }
    
    .filter-card .border:hover {
      border-color: #0d6efd !important;
      box-shadow: 0 4px 12px rgba(13,110,253,0.1);
    }
    
    /* Enhanced Filter Chips */
    .filter-chip{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:10px 16px;
      border-radius:12px;
      background:linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
      border:2px solid rgba(13,110,253,0.2);
      font-weight:600;
      color:#0d6efd;
      transition:all 0.3s ease;
      position:relative;
      overflow:hidden;
    }
    
    .filter-chip::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(13,110,253,0.1);
      transform: translate(-50%, -50%);
      transition: width 0.4s, height 0.4s;
    }
    
    .filter-chip:hover::before {
      width: 200px;
      height: 200px;
    }
    
    .filter-chip:hover{
      transform:translateY(-2px);
      box-shadow:0 6px 16px rgba(13,110,253,0.2);
      border-color:rgba(13,110,253,0.4);
    }
    
    .filter-chip .btn-close {
      opacity: 0.6;
      transition: opacity 0.3s;
      font-size: 0.75rem;
    }
    
    .filter-chip .btn-close:hover {
      opacity: 1;
    }
    
    .filter-chip i {
      font-size: 14px;
    }
    
    /* Enhanced Form Controls */
    .form-control, .form-select {
      transition: all 0.3s ease;
      border: 2px solid #e5e7eb;
    }
    
    .form-control:focus, .form-select:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 4px rgba(13,110,253,0.1);
    }
    
    /* Loading State */
    .rooms-results .spinner-border {
      width: 3rem;
      height: 3rem;
      border-width: 4px;
      color: #0d6efd;
    }
    
    /* Filter Header */
    .filter-header h4 {
      font-size: 1.5rem;
      font-weight: 800;
      background: linear-gradient(135deg, #0d6efd 0%, #0ea5e9 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .filter-header .btn {
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .filter-header .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    /* Checkbox Enhancement */
    .form-check-input {
      width: 20px;
      height: 20px;
      border: 2px solid #d1d5db;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    
    .form-check-input:checked {
      background-color: #0d6efd;
      border-color: #0d6efd;
      box-shadow: 0 0 0 4px rgba(13,110,253,0.1);
    }
    
    .form-check-input:hover {
      border-color: #0d6efd;
    }
    
    .form-check-label {
      cursor: pointer;
      transition: color 0.3s ease;
    }
    
    .form-check-label:hover {
      color: #0d6efd;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
      .filter-card {
        margin-bottom: 2rem;
      }
    }
  </style>
</head>
<body class="bg-light">

<?php
require('inc/header.php');
$checkin_default="";
$checkout_default="";
$adult_default="";
$children_default="";

function table_exists($con, $table){
  $table = mysqli_real_escape_string($con, $table);
  $res = mysqli_query($con, "SHOW TABLES LIKE '{$table}'");
  return $res && mysqli_num_rows($res) > 0;
}

function column_exists($con, $table, $column){
  $table = mysqli_real_escape_string($con, $table);
  $column = mysqli_real_escape_string($con, $column);
  $res = mysqli_query(
    $con,
    "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table}' AND COLUMN_NAME = '{$column}' LIMIT 1"
  );
  return $res && mysqli_num_rows($res) > 0;
}

?>

<div class="container mt-4 px-lg-4">
  <div class="list-hero">
    <div class="d-flex align-items-center gap-3 flex-wrap mb-3">
      <span class="vl-badge">
        <i class="bi bi-geo-alt-fill me-1"></i> 
        <span data-i18n="rooms.explore">Khám phá Vĩnh Long</span>
      </span>
      <span class="text-muted small fw-semibold d-flex align-items-center gap-2">
        <i class="bi bi-lightning-charge-fill text-warning"></i>
        <span data-i18n="rooms.quickSearch">Tìm nhanh phòng còn trống, giá tốt, đủ tiện ích</span>
      </span>
    </div>
    <h2 class="fw-bold d-flex align-items-center gap-2">
      <i class="bi bi-house-door-fill"></i>
      <span data-i18n="section.rooms">Danh sách phòng</span>
    </h2>
    <p class="d-flex align-items-start gap-2 mt-2">
      <i class="bi bi-info-circle"></i>
      <span data-i18n="rooms.filterDesc">Lọc theo ngày, hạng sao, tiện ích và số khách để đặt phòng chuẩn nhu cầu.</span>
    </p>
  </div>
</div>

<div class="container-fluid rooms-shell pb-5">
  <div class="container px-lg-4">
    <div class="rooms-layout">

      <!-- FILTERS -->
      <aside class="filter-card">
        <div class="filter-header mb-4">
          <h4 class="m-0 d-flex align-items-center gap-2">
            <i class="bi bi-funnel-fill text-primary"></i>
            <span data-i18n="rooms.filters">Bộ lọc</span>
          </h4>
          <button class="btn btn-sm btn-outline-dark rounded-pill" type="button" onclick="clear_all_filters()">
            <i class="bi bi-x-circle me-1"></i><span data-i18n="rooms.clearAll">Xóa tất cả</span>
          </button>
        </div>

        <!-- SEARCH -->
        <div class="border bg-light p-3 rounded mb-3">
          <h5 class="filter-title mb-3">
            <i class="bi bi-search text-primary"></i>
            <span data-i18n="rooms.search">Tìm kiếm</span>
          </h5>
          <input type="text" id="keyword" class="form-control shadow-none"
                 placeholder="Nhập tên phòng hoặc mô tả..." data-i18n-placeholder="rooms.searchPlaceholder" oninput="currentPage=1; fetch_rooms();">
          <div class="mt-2">
            <label class="form-label small mb-1" data-i18n="rooms.area">Khu vực Vĩnh Long</label>
            <select id="district" class="form-select form-select-sm shadow-none" onchange="currentPage=1; fetch_rooms();">
              <option value="" data-i18n="rooms.allDistricts">Tất cả quận/huyện</option>
              <option value="TP. Vĩnh Long">TP. Vĩnh Long</option>
              <option value="Long Hồ">Long Hồ</option>
              <option value="Mang Thít">Mang Thít</option>
              <option value="Vũng Liêm">Vũng Liêm</option>
              <option value="Tam Bình">Tam Bình</option>
              <option value="Bình Tân">Bình Tân</option>
              <option value="Trà Ôn">Trà Ôn</option>
              <option value="Bình Minh">Thị xã Bình Minh</option>
            </select>
          </div>
        </div>

        <!-- PRICE -->
        <div class="border bg-light p-3 rounded mb-3">
          <h5 class="filter-title mb-3">
            <i class="bi bi-currency-dollar text-primary"></i>
            <span data-i18n="rooms.priceRange">Khoảng giá (VND/đêm)</span>
          </h5>
          <div class="d-flex justify-content-between align-items-center mb-2 price-range-wrap">
            <span class="price-chip" id="price_min_label">0 đ</span>
            <span class="text-muted small">↔</span>
            <span class="price-chip" id="price_max_label">5.0m đ</span>
          </div>
          <input type="hidden" id="min_price" value="0">
          <input type="hidden" id="max_price" value="5000000">
          <input type="range" id="price_min_range" class="form-range" min="0" max="5000000" step="50000">
          <input type="range" id="price_max_range" class="form-range mt-1" min="0" max="5000000" step="50000">
        </div>

        <!-- STARS -->
        <div class="border bg-light p-3 rounded mb-3">
          <h5 class="filter-title mb-3">
            <i class="bi bi-star-fill text-warning"></i>
            <span data-i18n="search.stars">Số sao</span>
          </h5>
          <select id="star" class="form-select shadow-none" onchange="currentPage=1; fetch_rooms();">
              <option value="" data-i18n="rooms.all">Tất cả</option>
              <option value="1">★</option>
              <option value="2">★★</option>
              <option value="3">★★★</option>
              <option value="4">★★★★</option>
              <option value="5">★★★★★</option>
          </select>
        </div>

        <!-- DATES -->
        <div class="border bg-light p-3 rounded mb-3">
          <h5 class="d-flex align-items-center justify-content-between mb-3 filter-title">
            <span class="d-flex align-items-center gap-2">
              <i class="bi bi-calendar-check text-primary"></i>
              <span data-i18n="rooms.checkAvailability">Kiểm tra phòng trống</span>
            </span>
            <button id="chk_avail_btn" onclick="chk_avail_clear()" class="btn shadow-none btn-sm text-secondary d-none rounded-pill">
              <i class="bi bi-arrow-clockwise me-1"></i><span data-i18n="rooms.refresh">Làm mới</span>
            </button>
          </h5>
          <label class="form-label" data-i18n="booking.checkIn">Nhận phòng</label>
          <input type="date" class="form-control shadow-none mb-3" id="checkin" onchange="chk_avail_filter()">
          <label class="form-label" data-i18n="booking.checkOut">Trả phòng</label>
          <input type="date" class="form-control shadow-none" id="checkout" onchange="chk_avail_filter()">
        </div>

        <!-- FACILITIES -->
        <div class="border bg-light p-3 rounded mb-3">
          <h5 class="d-flex align-items-center justify-content-between mb-3 filter-title">
            <span class="d-flex align-items-center gap-2">
              <i class="bi bi-grid-3x3-gap text-primary"></i>
              <span data-i18n="nav.facilities">Tiện ích</span>
            </span>
            <button id="facilities_btn" onclick="facilities_clear()" class="btn shadow-none btn-sm text-secondary d-none rounded-pill">
              <i class="bi bi-arrow-clockwise me-1"></i><span data-i18n="rooms.refresh">Làm mới</span>
            </button>
          </h5>
          <?php
            // Mapping facilities names to i18n keys
            $facility_i18n_map = [
              'Wi-Fi' => 'facilities.wifi',
              'Truyền Hình' => 'facilities.tv',
              'Spa' => 'facilities.spa',
              'Máy Sưởi' => 'facilities.heater',
              'Máy Lạnh' => 'facilities.airConditioner',
              'Máy Nước Nóng' => 'facilities.waterHeater',
              'Máy Sấy Tóc' => 'facilities.hairDryer',
              'Đồ Vệ Sinh Cá Nhân' => 'facilities.personalHygiene',
              'Minibar' => 'facilities.minibar',
              'Ấm Đun Nước' => 'facilities.kettle',
              'Khu Làm Việc' => 'facilities.workspace',
              'Tủ Quần Áo' => 'facilities.wardrobe',
              'Dép Đi Trong Nhà' => 'facilities.slippers',
              'Gương Toàn Thân' => 'facilities.fullMirror',
              'Bàn Ủi' => 'facilities.iron',
              'Quầy bar' => 'facilities.bar',
              'Sân golf' => 'facilities.golfCourse',
              'Hồ bơi' => 'facilities.swimmingPool',
              'Xe đưa đón sân bay' => 'facilities.airportShuttle',
              'Dịch vụ 24/24 giờ' => 'facilities.service24',
            ];
            
            $facilities_q = selectAll('facilities');
            while($row = mysqli_fetch_assoc($facilities_q))
            {
              $fac_name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
              $i18n_key = isset($facility_i18n_map[$fac_name]) ? $facility_i18n_map[$fac_name] : '';
              $i18n_attr = $i18n_key ? " data-i18n=\"{$i18n_key}\"" : '';
              
              echo <<<FAC
                <div class="mb-2">
                  <input type="checkbox" onclick="currentPage=1; fetch_rooms();" name="facilities" value="$row[id]"
                         class="form-check-input shadow-none me-1" id="fac_$row[id]">
                  <label class="form-check-label" for="fac_$row[id]"{$i18n_attr}>$fac_name</label>
                </div>
              FAC;
            }
          ?>
        </div>

        <!-- GUESTS -->
        <div class="border bg-light p-3 rounded mb-3">
          <h5 class="d-flex align-items-center justify-content-between mb-3 filter-title">
            <span class="d-flex align-items-center gap-2">
              <i class="bi bi-people text-primary"></i>
              <span data-i18n="rooms.guestsCount">Số lượng khách</span>
            </span>
            <button id="guests_btn" onclick="guests_clear()" class="btn shadow-none btn-sm text-secondary d-none rounded-pill">
              <i class="bi bi-arrow-clockwise me-1"></i><span data-i18n="rooms.refresh">Làm mới</span>
            </button>
          </h5>
          <div class="d-flex">
            <div class="me-3">
              <label class="form-label" data-i18n="search.adults">Người lớn</label>
              <input type="number" min="0" id="adults" class="form-control shadow-none" oninput="guests_filter()">
            </div>
            <div>
              <label class="form-label" data-i18n="search.children">Trẻ em</label>
              <input type="number" min="0" id="children" class="form-control shadow-none" oninput="guests_filter()">
            </div>
          </div>
        </div>

        <!-- DESTINATIONS -->
        <div class="border bg-light p-3 rounded mb-3">
          <h5 class="d-flex align-items-center justify-content-between mb-3 filter-title">
            <span class="d-flex align-items-center gap-2">
              <i class="bi bi-geo-alt text-primary"></i>
              <span data-i18n="rooms.nearDestinations">Phòng gần điểm du lịch</span>
            </span>
            <button id="destination_btn" onclick="destination_clear()" class="btn shadow-none btn-sm text-secondary d-none rounded-pill">
              <i class="bi bi-arrow-clockwise me-1"></i><span data-i18n="rooms.refresh">Làm mới</span>
            </button>
          </h5>
          <?php
            require('admin/inc/db_config.php');
            
            // Mapping destinations names to i18n keys
            $destination_i18n_map = [
              'Chợ nổi Cái Bè' => 'destinations.caiBeFloatingMarket',
              'Chùa Tiên Châu' => 'destinations.tienChauTemple',
              'Cù lao An Bình' => 'destinations.anBinhIsland',
              'Khu du lịch Bình Hòa Phước' => 'destinations.binhHoaPhuoc',
              'Khu du lịch sinh thái Tràm Chim' => 'destinations.tramChimEco',
              'Làng nghề đan lát Long Hồ' => 'destinations.longHoVillage',
              'Vườn cây trái Cái Mơn' => 'destinations.caiMonOrchard',
              'Đình Long Thanh' => 'destinations.longThanhTemple',
            ];
            
            $destinations_q = mysqli_query($con, "SELECT id, name FROM `destinations` WHERE `active` = 1 ORDER BY `name` ASC");
            if($destinations_q && mysqli_num_rows($destinations_q) > 0) {
              while($dest = mysqli_fetch_assoc($destinations_q)) {
                $dest_name = htmlspecialchars($dest['name'], ENT_QUOTES, 'UTF-8');
                $i18n_key = isset($destination_i18n_map[$dest_name]) ? $destination_i18n_map[$dest_name] : '';
                $i18n_attr = $i18n_key ? " data-i18n=\"{$i18n_key}\"" : '';
                
                echo <<<DEST
                  <div class="mb-2">
                    <input type="checkbox" onclick="currentPage=1; fetch_rooms();" name="destinations" value="{$dest['id']}"
                           class="form-check-input shadow-none me-1" id="dest_{$dest['id']}">
                    <label class="form-check-label" for="dest_{$dest['id']}"{$i18n_attr}>$dest_name</label>
                  </div>
                DEST;
              }
            } else {
              echo '<p class="text-muted small mb-0"><span data-i18n="rooms.noDestinations">Chưa có điểm du lịch nào</span></p>';
            }
          ?>
        </div>

        <!-- SORT -->
        <div class="border bg-light p-3 rounded mb-1">
          <h5 class="filter-title mb-3">
            <i class="bi bi-sort-down text-primary"></i>
            <span data-i18n="rooms.sort">Sắp xếp</span>
          </h5>
          <select id="sort_by" class="form-select shadow-none" onchange="currentPage=1; fetch_rooms();">
            <option value="" data-i18n="rooms.sortDefault">Mặc định</option>
            <option value="price_asc" data-i18n="rooms.sortPriceAsc">Giá tăng dần</option>
            <option value="price_desc" data-i18n="rooms.sortPriceDesc">Giá giảm dần</option>
          </select>
        </div>
      </aside>

      <!-- RESULTS -->
      <section>
        <div id="filters_state" class="d-flex align-items-center gap-2 flex-wrap mb-3"></div>
        
        <div id="rooms-data" class="rooms-results"></div>
      </section>
    </div>
  </div>
</div>

<script>
let rooms_data    = document.getElementById('rooms-data');
let checkin       = document.getElementById('checkin');
let checkout      = document.getElementById('checkout');
let adults        = document.getElementById('adults');
let children      = document.getElementById('children');
let star          = document.getElementById('star');
let keyword       = document.getElementById('keyword');
let district      = document.getElementById('district');
let min_price     = document.getElementById('min_price');
let max_price     = document.getElementById('max_price');
let sort_by       = document.getElementById('sort_by');
let chk_avail_btn = document.getElementById('chk_avail_btn');
let guests_btn    = document.getElementById('guests_btn');
let facilities_btn= document.getElementById('facilities_btn');
let filters_state = document.getElementById('filters_state');
let priceMinRange = document.getElementById('price_min_range');
let priceMaxRange = document.getElementById('price_max_range');
let priceMinLabel = document.getElementById('price_min_label');
let priceMaxLabel = document.getElementById('price_max_label');

// Biến phân trang
let currentPage = 1;

const urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('keyword')) { keyword.value = urlParams.get('keyword'); }
if (urlParams.has('district')) { district.value = urlParams.get('district'); }
if (urlParams.has('checkin')) { checkin.value = urlParams.get('checkin'); }
if (urlParams.has('checkout')){ checkout.value = urlParams.get('checkout'); }
if (urlParams.has('adult'))   { adults.value = urlParams.get('adult'); }
if (urlParams.has('children')){ children.value = urlParams.get('children'); }
if (urlParams.has('star'))    { star.value = urlParams.get('star'); }
if (urlParams.has('min_price')) { min_price.value = urlParams.get('min_price'); }
if (urlParams.has('max_price')) { max_price.value = urlParams.get('max_price'); }
if (priceMinRange && min_price.value){ priceMinRange.value = min_price.value; }
if (priceMaxRange && max_price.value){ priceMaxRange.value = max_price.value; }

function renderActiveFilters(){
  const chips = [];
  const addChip = (text, clearFn = null) => {
    const closeBtn = clearFn ? `<button type="button" onclick="${clearFn}" class="btn-close btn-close-sm ms-2" aria-label="Đóng"></button>` : '';
    chips.push(`<span class="filter-chip d-inline-flex align-items-center">${text}${closeBtn}</span>`);
  };

  updatePriceLabels();

  const translate = window.i18n?.translate || ((key, lang) => key);
  const currentLang = window.i18n?.getCurrentLanguage() || 'vi';
  
  if(keyword.value.trim()){
    const keywordLabel = translate('rooms.filterKeyword', currentLang);
    addChip(`<i class="bi bi-search me-1"></i>${keywordLabel}: ${keyword.value.trim()}`, 'keyword.value=\'\'; fetch_rooms();');
  }
  if(district.value){
    const areaLabel = translate('rooms.filterArea', currentLang);
    addChip(`<i class="bi bi-geo-alt me-1"></i>${areaLabel}: ${district.value}`, 'district.value=\'\'; fetch_rooms();');
  }
  if(star.value){
    const starLabel = translate('rooms.star', currentLang);
    addChip(`<i class="bi bi-star-fill me-1 text-warning"></i>${star.value} ${starLabel}`, 'star.value=\'\'; fetch_rooms();');
  }
  if(min_price.value || max_price.value){
    const minTxt = min_price.value ? `${formatPriceShort(Number(min_price.value))}đ` : '';
    const maxTxt = max_price.value ? `${formatPriceShort(Number(max_price.value))}đ` : '';
    const priceLabel = translate('rooms.filterPrice', currentLang);
    addChip(`<i class="bi bi-currency-dollar me-1"></i>${priceLabel} ${minTxt}${(minTxt && maxTxt)?' - ':''}${maxTxt}`, 'min_price.value=\'\'; max_price.value=\'\'; priceMinRange.value=priceMinRange.min; priceMaxRange.value=priceMaxRange.max; updatePriceLabels(); fetch_rooms();');
  }
  if(checkin.value && checkout.value){
    const stayLabel = translate('rooms.filterStay', currentLang);
    addChip(`<i class="bi bi-calendar-check me-1"></i>${stayLabel}: ${checkin.value} → ${checkout.value}`, 'chk_avail_clear();');
    chk_avail_btn.classList.remove('d-none');
  } else {
    chk_avail_btn.classList.add('d-none');
  }

  const a = Number(adults.value || 0);
  const c = Number(children.value || 0);
  if(a || c){
    const guestsLabel = translate('rooms.filterGuests', currentLang);
    const adultsLabel = translate('room.adults', currentLang);
    const childrenLabel = translate('room.children', currentLang);
    addChip(`<i class="bi bi-people me-1"></i>${guestsLabel}: ${a} ${adultsLabel} · ${c} ${childrenLabel}`, 'guests_clear();');
    guests_btn.classList.remove('d-none');
  } else {
    guests_btn.classList.add('d-none');
  }

  const facilities = Array.from(document.querySelectorAll('[name="facilities"]:checked'));
  if(facilities.length){
    const label = facilities.map(f => f.nextElementSibling?.innerText || '').filter(Boolean).join(', ');
    const facilitiesLabel = translate('rooms.filterFacilities', currentLang);
    addChip(`<i class="bi bi-grid-3x3-gap me-1"></i>${facilitiesLabel}: ${label}`, 'facilities_clear();');
    facilities_btn.classList.remove('d-none');
  } else {
    facilities_btn.classList.add('d-none');
  }

  const destinations = Array.from(document.querySelectorAll('[name="destinations"]:checked'));
  if(destinations.length){
    const label = destinations.map(d => d.nextElementSibling?.innerText || '').filter(Boolean).join(', ');
    const destinationsLabel = translate('rooms.filterDestinations', currentLang);
    addChip(`<i class="bi bi-geo-alt me-1"></i>${destinationsLabel}: ${label}`, 'destination_clear();');
    destination_btn.classList.remove('d-none');
  } else {
    destination_btn.classList.add('d-none');
  }

  if(sort_by.value){
    const sortLowHigh = translate('rooms.sortLowHigh', currentLang);
    const sortHighLow = translate('rooms.sortHighLow', currentLang);
    addChip(`<i class="bi bi-sort-down me-1"></i>${sort_by.value === 'price_asc' ? sortLowHigh : sortHighLow}`, 'sort_by.value=\'\'; fetch_rooms();');
  }

  const noFiltersHint = translate('rooms.noFiltersHint', currentLang);
  filters_state.innerHTML = chips.length
    ? chips.join('')
    : `<span class="text-muted small">${noFiltersHint}</span>`;
}

// Hàm chuyển trang
function goToPage(page){
  currentPage = page;
  fetch_rooms();
  // Scroll lên đầu phần kết quả
  const roomsData = document.getElementById('rooms-data');
  if(roomsData){
    roomsData.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

function fetch_rooms(resetPage = false)
{
  if(resetPage) currentPage = 1; // Reset về trang 1 nếu cần
  renderActiveFilters();

  // sync min/max from sliders to hidden inputs
  if(priceMinRange && priceMaxRange){
    let minVal = Number(priceMinRange.value);
    let maxVal = Number(priceMaxRange.value);
    if(minVal > maxVal){
      // keep ranges valid
      if(event && event.target === priceMinRange){
        maxVal = minVal;
        priceMaxRange.value = maxVal;
      }else{
        minVal = maxVal;
        priceMinRange.value = minVal;
      }
    }
    min_price.value = minVal;
    max_price.value = maxVal;
    updatePriceLabels();
  }

  let chk_avail = JSON.stringify({
    checkin: checkin.value,
    checkout: checkout.value
  });

  let guests = JSON.stringify({
    adults: adults.value,
    children: children.value
  });

  let facilities = document.querySelectorAll('[name="facilities"]:checked');
  let facility_list = { facilities: [] };
  facilities.forEach(f => facility_list.facilities.push(f.value));
  facility_list = JSON.stringify(facility_list);

  let destinations = document.querySelectorAll('[name="destinations"]:checked');
  let destination_list = { destinations: [] };
  destinations.forEach(d => destination_list.destinations.push(d.value));
  destination_list = JSON.stringify(destination_list);

  let xhr = new XMLHttpRequest();
  xhr.open("GET",
    "ajax/rooms.php?fetch_rooms"
    +"&keyword="+encodeURIComponent(keyword.value)
    +"&district="+encodeURIComponent(district.value)
    +"&star="+star.value
    +"&min_price="+min_price.value
    +"&max_price="+max_price.value
    +"&sort_by="+sort_by.value
    +"&chk_avail="+chk_avail
    +"&guests="+guests
    +"&facility_list="+facility_list
    +"&destination_list="+destination_list
    +"&page="+currentPage
  , true);

  xhr.onprogress = function(){
    const translate = window.i18n?.translate || ((key, lang) => key);
    const currentLang = window.i18n?.getCurrentLanguage() || 'vi';
    const loadingText = translate('rooms.loading', currentLang);
    const searchingText = translate('rooms.searching', currentLang);
    rooms_data.innerHTML = `
      <div class='text-center py-5'>
        <div class='spinner-border text-primary d-block mx-auto mb-3' role='status'>
          <span class='visually-hidden'>${loadingText}</span>
        </div>
        <p class='text-muted mb-0'>${searchingText}</p>
      </div>
    `;
  };

  xhr.onload = function(){
    rooms_data.innerHTML = this.responseText;
    renderActiveFilters();
    bindFavButtons();
    // Cập nhật translations cho các phần tử mới được thêm vào DOM
    if(window.i18n && window.i18n.updateTranslations){
      window.i18n.updateTranslations();
    }
  };

  xhr.send();
}

function chk_avail_filter(){
  currentPage = 1; // Reset về trang 1 khi filter thay đổi
  renderActiveFilters();
  fetch_rooms();
}
function chk_avail_clear(){
  currentPage = 1; // Reset về trang 1
  checkin.value = '';
  checkout.value = '';
  chk_avail_btn.classList.add('d-none');
  fetch_rooms();
}
function guests_filter(){
  currentPage = 1; // Reset về trang 1 khi filter thay đổi
  renderActiveFilters();
  fetch_rooms();
}
function guests_clear(){
  currentPage = 1; // Reset về trang 1
  adults.value = '';
  children.value = '';
  guests_btn.classList.add('d-none');
  fetch_rooms();
}
function facilities_clear(){
  currentPage = 1; // Reset về trang 1
  document.querySelectorAll('[name="facilities"]:checked').forEach(f => f.checked=false);
  facilities_btn.classList.add('d-none');
  fetch_rooms();
}
function destination_clear(){
  currentPage = 1; // Reset về trang 1
  document.querySelectorAll('[name="destinations"]:checked').forEach(d => d.checked=false);
  destination_btn.classList.add('d-none');
  fetch_rooms();
}
function formatPriceShort(v){
  if(v >= 1000000){
    return (v/1000000).toFixed(v%1000000===0 ? 0 : 1) + 'm';
  }
  return (v/1000).toFixed(v%1000===0 ? 0 : 0) + 'k';
}
function updatePriceLabels(){
  if(priceMinLabel && priceMaxLabel){
    const minVal = Number(min_price.value || 0);
    const maxVal = Number(max_price.value || 0);
    priceMinLabel.innerText = formatPriceShort(minVal) + ' đ';
    priceMaxLabel.innerText = formatPriceShort(maxVal) + ' đ';
  }
}
if(priceMinRange && priceMaxRange){
  priceMinRange.addEventListener('input', function(){ currentPage=1; fetch_rooms(); });
  priceMaxRange.addEventListener('input', function(){ currentPage=1; fetch_rooms(); });
  updatePriceLabels();
}
function clear_all_filters(){
  keyword.value='';
  district.value='';
  star.value='';
  min_price.value='';
  max_price.value='';
  priceMinRange.value = priceMinRange.min;
  priceMaxRange.value = priceMaxRange.max;
  updatePriceLabels();
  sort_by.value='';
  checkin.value='';
  checkout.value='';
  adults.value='';
  children.value='';
  document.querySelectorAll('[name="facilities"]:checked').forEach(f => f.checked=false);
  document.querySelectorAll('[name="destinations"]:checked').forEach(d => d.checked=false);
  chk_avail_btn.classList.add('d-none');
  guests_btn.classList.add('d-none');
  facilities_btn.classList.add('d-none');
  destination_btn.classList.add('d-none');
  fetch_rooms();
}

function bindFavButtons(){
  document.querySelectorAll('.home-fav-btn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const roomId = btn.getAttribute('data-room');
      fetch('ajax/favorites.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body: new URLSearchParams({toggle:1, room_id: roomId})
      }).then(res=>res.json()).then(res=>{
        if(res.status === 'login_required'){
          if(typeof showToast === 'function'){
            showToast('warning','Vui lòng đăng nhập để lưu yêu thích');
          } else {
            alert('Vui lòng đăng nhập để lưu yêu thích');
          }
          return;
        }
        if(res.status === 'added'){
          btn.classList.add('active');
          const icon = btn.querySelector('i');
          if(icon) icon.className='bi bi-heart-fill';
        } else if(res.status === 'removed'){
          btn.classList.remove('active');
          const icon = btn.querySelector('i');
          if(icon) icon.className='bi bi-heart';
        }
      });
    });
  });
}


window.onload = function() {
  fetch_rooms();
};


// Lazy loading images
if('IntersectionObserver' in window) {
  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if(entry.isIntersecting) {
        const img = entry.target;
        if(img.dataset.src) {
          img.src = img.dataset.src;
          img.removeAttribute('data-src');
          observer.unobserve(img);
        }
      }
    });
  });
  
  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('img[data-src]').forEach(img => {
      imageObserver.observe(img);
    });
  });
}

</script>

<?php require('inc/footer.php'); ?>
<?php require('inc/modals.php'); ?>
</body>
</html>
