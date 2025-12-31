<?php
  // Xử lý ngôn ngữ TRƯỚC KHI output HTML
  // Ưu tiên đọc từ URL parameter (nếu có) để force reload, sau đó mới đọc cookie
  $lang_from_url = isset($_GET['_lang']) ? trim($_GET['_lang']) : '';
  if ($lang_from_url === 'en' || $lang_from_url === 'vi') {
    // Set cookie từ URL parameter với path và domain đúng
    setcookie('lang', $lang_from_url, time() + (365 * 24 * 60 * 60), '/', '', false, true);
    // Set trong $_COOKIE ngay lập tức để sử dụng trong request hiện tại
    $_COOKIE['lang'] = $lang_from_url;
    $current_lang = $lang_from_url;
  } else {
    // Đọc từ cookie
    $lang_cookie = isset($_COOKIE['lang']) ? trim($_COOKIE['lang']) : '';
    $current_lang = ($lang_cookie === 'en') ? 'en' : 'vi';
  }
  
  // Đảm bảo $current_lang luôn có giá trị hợp lệ
  if ($current_lang !== 'en' && $current_lang !== 'vi') {
    $current_lang = 'vi';
  }
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <?php
    
    function t($key, $lang = 'vi') {
      $translations = [
        'vi' => [
          'destinations.pageTitle' => 'Điểm đến Vĩnh Long',
        ],
        'en' => [
          'destinations.pageTitle' => 'Vinh Long Destinations',
        ]
      ];
      return $translations[$lang][$key] ?? $key;
    }
  ?>
  <title><?php echo $settings_r['site_title'] . " - " . t('destinations.pageTitle', $current_lang); ?></title>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    
    * {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    
    /* Modern Destinations Page Styles */
    body {
      background: #ffffff;
      min-height: 100vh;
      position: relative;
    }
    
    /* Header Section */
    .destinations-header {
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
    
    .destinations-header h2 {
      font-size: 3rem;
      font-weight: 800;
      color: #1a202c;
      margin-bottom: 1rem;
      letter-spacing: 0.02em;
      text-transform: uppercase;
    }
    
    .h-line {
      width: 100px;
      height: 4px;
      background: linear-gradient(90deg, #a78bfa 0%, #ec4899 100%);
      border-radius: 2px;
      margin: 1.5rem auto;
    }
    
    .destinations-header p {
      max-width: 900px;
      margin: 0 auto;
      line-height: 1.9;
      color: #4a5568;
      font-size: 1.1rem;
      font-weight: 400;
      text-align: center;
    }
    
    .destinations-header .text-uppercase.text-muted {
      color: #6b7280 !important;
      font-weight: 500;
      letter-spacing: 2px;
    }
    
    /* Category Filter */
    /* Breadcrumb */
    .breadcrumb {
      background: transparent;
      padding: 0;
    }
    
    .breadcrumb-item a {
      color: #667eea;
      transition: color 0.3s ease;
    }
    
    .breadcrumb-item a:hover {
      color: #764ba2;
    }
    
    /* Search Container */
    .search-container {
      margin-bottom: 2rem;
    }
    
    .search-container .input-group {
      border-radius: 16px;
      overflow: hidden;
    }
    
    .search-container .form-control {
      border-left: none;
      padding: 0.875rem 1.25rem;
      font-size: 1rem;
    }
    
    .search-container .input-group-text {
      border-right: none;
      padding: 0.875rem 1.25rem;
    }
    
    .search-container .btn {
      border-radius: 0 16px 16px 0;
      padding: 0.875rem 1.75rem;
      font-weight: 600;
    }
    
    /* Sort Select */
    #sortSelect {
      border-radius: 12px;
      border: 2px solid #e5e7eb;
      padding: 0.75rem 1rem;
      transition: all 0.3s ease;
    }
    
    #sortSelect:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    #resultCount {
      font-size: 0.95rem;
      font-weight: 500;
    }
    
    .category-filter {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      justify-content: center;
      margin-bottom: 4rem;
      padding: 2rem;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(30px);
      border-radius: 24px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.3);
      animation: fadeInUp 0.6s ease-out;
      animation-delay: 0.2s;
      animation-fill-mode: both;
    }
    
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .category-btn {
      padding: 0.75rem 1.75rem;
      border: 2px solid rgba(102, 126, 234, 0.2);
      background: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(10px);
      border-radius: 16px;
      font-weight: 600;
      color: #4a5568;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.95rem;
      letter-spacing: 0.01em;
    }
    
    .category-btn i {
      font-size: 1.1rem;
    }
    
    .category-btn:hover {
      transform: translateY(-3px) scale(1.05);
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
      border-color: rgba(102, 126, 234, 0.4);
    }
    
    .category-btn.active {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #ffffff;
      border-color: transparent;
      transform: translateY(-3px) scale(1.05);
      box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
    }
    
    /* Destination Card */
    .destination-card {
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(30px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.5);
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      height: 100%;
      display: flex;
      flex-direction: column;
      animation: fadeInUp 0.6s ease-out;
      animation-fill-mode: both;
    }
    
    .destination-card:nth-child(1) { animation-delay: 0.1s; }
    .destination-card:nth-child(2) { animation-delay: 0.2s; }
    .destination-card:nth-child(3) { animation-delay: 0.3s; }
    .destination-card:nth-child(4) { animation-delay: 0.4s; }
    .destination-card:nth-child(5) { animation-delay: 0.5s; }
    .destination-card:nth-child(6) { animation-delay: 0.6s; }
    
    .destination-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    
    .destination-card:hover {
      transform: translateY(-12px) scale(1.02);
      box-shadow: 0 30px 80px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.6);
    }
    
    .destination-card:hover::before {
      opacity: 1;
    }
    
    /* Image Wrapper */
    .destination-image-wrapper {
      position: relative;
      width: 100%;
      height: 280px;
      overflow: hidden;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    }
    
    .destination-image-wrapper::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.1) 100%);
      pointer-events: none;
    }
    
    .destination-image-wrapper img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .destination-card:hover .destination-image-wrapper img {
      transform: scale(1.15);
    }
    
    /* Category Badge */
    .category-badge {
      position: absolute;
      top: 1rem;
      right: 1rem;
      padding: 0.6rem 1.2rem;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 16px;
      font-size: 0.8rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
      z-index: 10;
      transition: all 0.3s ease;
    }
    
    .destination-card:hover .category-badge {
      transform: scale(1.1);
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
    }
    
    .images-count-badge {
      position: absolute;
      bottom: 1rem;
      left: 1rem;
      padding: 0.5rem 1rem;
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(10px);
      border-radius: 12px;
      font-size: 0.85rem;
      font-weight: 600;
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
      z-index: 10;
      transition: all 0.3s ease;
    }
    
    .destination-card:hover .images-count-badge {
      background: rgba(0, 0, 0, 0.85);
      transform: scale(1.05);
    }
    
    .category-badge.temple { 
      color: #8b5cf6;
      background: linear-gradient(135deg, rgba(139, 92, 246, 0.15) 0%, rgba(139, 92, 246, 0.25) 100%);
      border: 1px solid rgba(139, 92, 246, 0.3);
    }
    .category-badge.nature { 
      color: #10b981;
      background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(16, 185, 129, 0.25) 100%);
      border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .category-badge.market { 
      color: #f59e0b;
      background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(245, 158, 11, 0.25) 100%);
      border: 1px solid rgba(245, 158, 11, 0.3);
    }
    .category-badge.culture { 
      color: #ef4444;
      background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(239, 68, 68, 0.25) 100%);
      border: 1px solid rgba(239, 68, 68, 0.3);
    }
    
    /* Card Content */
    .destination-card-body {
      padding: 2rem;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      position: relative;
    }
    
    .destination-card h5 {
      font-size: 1.5rem;
      font-weight: 800;
      color: #1a202c;
      margin-bottom: 1rem;
      line-height: 1.3;
      letter-spacing: -0.02em;
      background: linear-gradient(135deg, #1a202c 0%, #667eea 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .destination-card .location {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: #6b7280;
      font-size: 0.9rem;
      margin-bottom: 0.75rem;
    }
    
    .destination-card .location i {
      color: #0d6efd;
    }
    
    .destination-card p {
      color: #6b7280;
      line-height: 1.7;
      margin-bottom: 1rem;
      flex-grow: 1;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    
    /* Rating */
    .destination-rating {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 1rem;
    }
    
    .destination-rating .stars {
      color: #fbbf24;
      font-size: 1rem;
    }
    
    .destination-rating .rating-text {
      color: #6b7280;
      font-size: 0.9rem;
      font-weight: 600;
    }
    
    /* View Button */
    .destination-btn {
      width: 100%;
      padding: 0.75rem;
      background: linear-gradient(135deg, #0d6efd, #0ea5e9);
      color: #ffffff;
      border: none;
      border-radius: 12px;
      font-weight: 600;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
      text-align: center;
    }
    
    .destination-btn:hover {
      background: linear-gradient(135deg, #0ea5e9, #0d6efd);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
      color: #ffffff;
    }
    
    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
      color: #6b7280;
    }
    
    .empty-state i {
      font-size: 4rem;
      color: #d1d5db;
      margin-bottom: 1rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .destinations-header h2 {
        font-size: 2rem;
      }
      
      .destination-image-wrapper {
        height: 200px;
      }
      
      .category-filter {
        justify-content: flex-start;
        overflow-x: auto;
        padding-bottom: 0.5rem;
      }
    }
    
    /* Fade in animation */
    .destination-card {
      opacity: 0;
      transform: translateY(20px);
      animation: fadeInUp 0.6s ease forwards;
    }
    
    .destination-card:nth-child(1) { animation-delay: 0.1s; }
    .destination-card:nth-child(2) { animation-delay: 0.2s; }
    .destination-card:nth-child(3) { animation-delay: 0.3s; }
    .destination-card:nth-child(4) { animation-delay: 0.4s; }
    .destination-card:nth-child(5) { animation-delay: 0.5s; }
    .destination-card:nth-child(6) { animation-delay: 0.6s; }
    .destination-card:nth-child(7) { animation-delay: 0.7s; }
    .destination-card:nth-child(8) { animation-delay: 0.8s; }
    
    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    /* ==================== ENHANCED SEARCH SUGGESTIONS ==================== */
    .search-container {
      position: relative;
    }
    
    .search-suggestions-dropdown {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.05);
      margin-top: 8px;
      z-index: 1000;
      max-height: 500px;
      overflow-y: auto;
      animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .suggestions-header {
      padding: 12px 16px;
      border-bottom: 1px solid #e5e7eb;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #f9fafb;
      position: sticky;
      top: 0;
      z-index: 10;
    }
    
    .suggestions-list {
      padding: 8px 0;
    }
    
    .suggestion-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 16px;
      cursor: pointer;
      transition: all 0.2s ease;
      border-left: 3px solid transparent;
    }
    
    .suggestion-item:hover,
    .suggestion-item.selected {
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.08), rgba(118, 75, 162, 0.08));
      border-left-color: #667eea;
    }
    
    .suggestion-item i:first-child {
      font-size: 1.2rem;
      flex-shrink: 0;
      width: 24px;
      text-align: center;
    }
    
    .suggestion-content {
      flex: 1;
      min-width: 0;
    }
    
    .suggestion-title {
      font-weight: 600;
      color: #1a202c;
      margin-bottom: 4px;
      font-size: 0.95rem;
    }
    
    .suggestion-subtitle {
      font-size: 0.85rem;
      color: #6b7280;
    }
    
    .suggestion-item i:last-child {
      flex-shrink: 0;
      opacity: 0;
      transition: opacity 0.2s ease;
    }
    
    .suggestion-item:hover i:last-child {
      opacity: 1;
    }
    
    .suggestion-item mark {
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2));
      color: #667eea;
      font-weight: 700;
      padding: 0 2px;
      border-radius: 3px;
    }
    
    .search-history {
      border-top: 1px solid #e5e7eb;
    }
    
    /* Clear search button */
    #clearSearchBtn {
      border: none;
      background: transparent;
      color: #6b7280;
      padding: 0.5rem;
      transition: all 0.2s ease;
    }
    
    #clearSearchBtn:hover {
      color: #ef4444;
      background: rgba(239, 68, 68, 0.1);
    }
    
    /* Highlight search results in cards */
    .destination-card h5 mark,
    .destination-card .location mark,
    .destination-card p mark {
      background: linear-gradient(135deg, rgba(255, 215, 0, 0.3), rgba(255, 193, 7, 0.3));
      color: #b8860b;
      font-weight: 700;
      padding: 2px 4px;
      border-radius: 4px;
    }
    
    /* Responsive suggestions */
    @media (max-width: 768px) {
      .search-suggestions-dropdown {
        max-height: 400px;
      }
      
      .suggestion-item {
        padding: 10px 12px;
      }
    }
  </style>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="container mt-4">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none" data-i18n="nav.home">Trang chủ</a></li>
      <li class="breadcrumb-item active" aria-current="page" data-i18n="nav.destinations">Điểm đến</li>
    </ol>
  </nav>

  <!-- Tiêu đề trang -->
  <div class="my-5 px-4 destinations-header">
    <div class="text-center">
      <p class="text-uppercase text-muted small mb-2" style="letter-spacing: 2px;" data-i18n="destinations.subtitle">KHÁM PHÁ VĨNH LONG</p>
      <h2 class="fw-bold text-center" data-i18n="destinations.heading">ĐIỂM ĐẾN DU LỊCH VĨNH LONG</h2>
      <div class="h-line mx-auto mb-4"></div>
    </div>
    <p class="text-center mt-4" data-i18n="destinations.intro" data-i18n-html="true">
      Vĩnh Long tự hào sở hữu nhiều điểm đến du lịch hấp dẫn, từ những ngôi chùa cổ kính, cù lao xanh tươi, 
      đến các khu du lịch sinh thái và chợ nổi sầm uất. Khám phá vẻ đẹp văn hóa và thiên nhiên độc đáo của vùng đất Nam Bộ, 
      và tìm những phòng nghỉ gần nhất để có trải nghiệm trọn vẹn.
    </p>
  </div>

  <!-- Search & Filter Section -->
  <div class="container mb-4">
    <!-- Enhanced Search Bar -->
    <div class="row mb-4">
      <div class="col-lg-8 mx-auto">
        <div class="search-container position-relative">
          <div class="input-group input-group-lg shadow-sm">
            <span class="input-group-text bg-white border-end-0">
              <i class="bi bi-search text-primary"></i>
            </span>
            <input type="text" id="searchInput" class="form-control border-start-0 shadow-none" 
                   data-i18n-placeholder="destinations.searchPlaceholder"
                   placeholder="Tìm kiếm điểm đến, địa danh, vị trí..." 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                   autocomplete="off">
            <button class="btn btn-outline-secondary" type="button" id="clearSearchBtn" style="display: none;" onclick="clearSearch()">
              <i class="bi bi-x-lg"></i>
            </button>
            <button class="btn btn-primary" type="button" onclick="performSearch()">
              <i class="bi bi-search me-1"></i><span data-i18n="destinations.searchButton">Tìm kiếm</span>
            </button>
          </div>
          
          <!-- Search Suggestions Dropdown -->
          <div id="searchSuggestions" class="search-suggestions-dropdown" style="display: none;">
            <div class="suggestions-header">
              <small class="text-muted">
                <i class="bi bi-lightbulb me-1"></i><span data-i18n="destinations.searchSuggestions">Gợi ý tìm kiếm</span>
              </small>
            </div>
            <div id="suggestionsList" class="suggestions-list"></div>
            <div id="searchHistory" class="search-history" style="display: none;">
              <div class="suggestions-header">
                <small class="text-muted">
                  <i class="bi bi-clock-history me-1"></i><span data-i18n="destinations.searchHistory">Lịch sử tìm kiếm</span>
                </small>
                <button class="btn btn-sm btn-link text-danger p-0" onclick="clearSearchHistory()">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
              <div id="historyList" class="suggestions-list"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php 
      // Khởi tạo biến trước khi sử dụng
      $total_results = 0;
      if(!isset($con)) {
        require('admin/inc/db_config.php');
        require('admin/inc/essentials.php');
      }
      
      // Tính toán total_results nếu bảng destinations tồn tại
      $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'destinations'");
      if($table_check && mysqli_num_rows($table_check) > 0) {
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $category = isset($_GET['category']) ? trim($_GET['category']) : '';
        
        $search_keyword = '';
        if(!empty($search)) {
          $search_escaped = mysqli_real_escape_string($con, $search);
          $search_keyword = "AND (`name` LIKE '%$search_escaped%' OR `description` LIKE '%$search_escaped%' OR `location` LIKE '%$search_escaped%')";
        }
        
        $category_filter = '';
        if(!empty($category) && $category != 'all') {
          $category_escaped = mysqli_real_escape_string($con, $category);
          $category_filter = "AND `category` = '$category_escaped'";
        }
        
        $count_query = "SELECT COUNT(*) as total FROM `destinations` WHERE `active` = 1 $category_filter $search_keyword";
        $count_result = @mysqli_query($con, $count_query);
        if($count_result) {
          $count_row = mysqli_fetch_assoc($count_result);
          $total_results = (int)$count_row['total'];
        }
      }
    ?>
    
    <!-- Sort & Filter Row -->
    <div class="row mb-3 align-items-center">
      <div class="col-md-6 mb-3 mb-md-0">
        <label class="form-label fw-semibold mb-2">
          <i class="bi bi-sort-down me-1"></i><span data-i18n="destinations.sortBy">Sắp xếp theo:</span>
        </label>
        <select id="sortSelect" class="form-select shadow-sm" onchange="applyFilters()">
          <option value="rating_desc" data-i18n="destinations.sortRatingDesc" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'rating_desc') ? 'selected' : ''; ?>>Đánh giá cao nhất</option>
          <option value="rating_asc" data-i18n="destinations.sortRatingAsc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'rating_asc') ? 'selected' : ''; ?>>Đánh giá thấp nhất</option>
          <option value="name_asc" data-i18n="destinations.sortNameAsc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : ''; ?>>Tên A-Z</option>
          <option value="name_desc" data-i18n="destinations.sortNameDesc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_desc') ? 'selected' : ''; ?>>Tên Z-A</option>
          <option value="reviews_desc" data-i18n="destinations.sortReviewsDesc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'reviews_desc') ? 'selected' : ''; ?>>Nhiều đánh giá nhất</option>
        </select>
      </div>
      <div class="col-md-6 text-md-end">
        <span class="text-muted" id="resultCount"><span data-i18n="destinations.foundResults">Tìm thấy</span> <strong><?php echo $total_results; ?></strong> <span data-i18n="destinations.destinations">điểm du lịch</span></span>
      </div>
    </div>

    <!-- Category Filter -->
    <div class="category-filter">
      <a href="destinations.php<?php echo isset($_GET['search']) ? '?search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['sort']) ? (isset($_GET['search']) ? '&' : '?') . 'sort=' . urlencode($_GET['sort']) : ''; ?>" 
         class="category-btn <?php echo (!isset($_GET['category']) || $_GET['category'] == 'all') ? 'active' : ''; ?>">
        <span data-i18n="destinations.all">Tất cả</span>
      </a>
      <a href="destinations.php?category=temple<?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : ''; ?>" 
         class="category-btn <?php echo (isset($_GET['category']) && $_GET['category'] == 'temple') ? 'active' : ''; ?>">
        <i class="bi bi-building"></i> <span data-i18n="destinations.categoryTemple">Chùa, Đình</span>
      </a>
      <a href="destinations.php?category=nature<?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : ''; ?>" 
         class="category-btn <?php echo (isset($_GET['category']) && $_GET['category'] == 'nature') ? 'active' : ''; ?>">
        <i class="bi bi-tree"></i> <span data-i18n="destinations.categoryNature">Thiên nhiên</span>
      </a>
      <a href="destinations.php?category=market<?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : ''; ?>" 
         class="category-btn <?php echo (isset($_GET['category']) && $_GET['category'] == 'market') ? 'active' : ''; ?>">
        <i class="bi bi-shop"></i> <span data-i18n="destinations.categoryMarket">Chợ nổi</span>
      </a>
      <a href="destinations.php?category=culture<?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : ''; ?>" 
         class="category-btn <?php echo (isset($_GET['category']) && $_GET['category'] == 'culture') ? 'active' : ''; ?>">
        <i class="bi bi-palette"></i> <span data-i18n="destinations.categoryCulture">Văn hóa</span>
      </a>
    </div>
  </div>

  <!-- Nội dung danh sách điểm du lịch -->
  <div class="container mb-5">
    <?php 
      require('admin/inc/db_config.php');
      require('admin/inc/essentials.php');
      
      // Lấy ngôn ngữ hiện tại (đã được xử lý ở trên)
      // Không cần set lại vì đã được set ở đầu file
      
      // Khởi tạo biến
      $total_results = 0;
      $destinations_result = null;
      
      // Function to highlight search terms
      function highlightSearchTerm($text, $searchTerm) {
        if(empty($searchTerm) || empty($text)) {
          return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        }
        $escaped_text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $escaped_search = htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8');
        $pattern = '/(' . preg_quote($escaped_search, '/') . ')/iu';
        return preg_replace($pattern, '<mark>$1</mark>', $escaped_text);
      }
      
      // Function to translate destination names and descriptions
      function t_destinations($key, $lang = 'vi') {
        $translations = [
          'vi' => [
            // Vietnamese - return as is
          ],
          'en' => [
            // Names
            'Chùa Tiên Châu' => 'Tien Chau Temple',
            'Cù lao An Bình' => 'An Binh Island',
            'Khu du lịch sinh thái Tràm Chim' => 'Tram Chim Ecological Tourist Area',
            'Chợ nổi Cái Bè' => 'Cai Be Floating Market',
            'Khu du lịch Bình Hòa Phước' => 'Binh Hoa Phuoc Tourist Area',
            'Làng nghề đan lát Long Hồ' => 'Long Ho Weaving Village',
            'Vườn cây trái Cái Mơn' => 'Cai Mon Fruit Garden',
            'Đình Long Thanh' => 'Long Thanh Communal House',
            // Descriptions
            'Ngôi chùa cổ kính từ thế kỷ 19, nằm trên cù lao An Bình, mang đậm nét kiến trúc Phật giáo Nam Bộ.' => 'An ancient temple from the 19th century, located on An Binh Island, featuring distinctive Southern Buddhist architecture.',
            'Hòn đảo xanh tươi giữa sông Cổ Chiên, nổi tiếng với vườn cây trái sum suê và cuộc sống miền quê yên bình.' => 'A lush green island in the Co Chien River, famous for its abundant fruit gardens and peaceful countryside life.',
            'Khu du lịch sinh thái với hệ động thực vật đa dạng, nơi sinh sống của nhiều loài chim quý hiếm.' => 'An ecological tourist area with diverse flora and fauna, home to many rare bird species.',
            'Chợ nổi sầm uất trên sông, nơi giao thương và trải nghiệm văn hóa miền Tây độc đáo.' => 'A bustling floating market on the river, a place for trading and experiencing unique Mekong Delta culture.',
            'Vườn cây trái rộng lớn với nhiều loại đặc sản miền Tây, nơi thưởng thức trái cây tươi ngon.' => 'A vast fruit garden with various Mekong Delta specialties, where you can enjoy fresh, delicious fruits.',
            'Ngôi đình cổ kính từ thế kỷ 19, nơi thờ cúng và tìm hiểu về văn hóa, tín ngưỡng địa phương.' => 'An ancient communal house from the 19th century, a place of worship and learning about local culture and beliefs.',
            'Làng nghề truyền thống với các sản phẩm đan lát từ tre, nứa, nơi tìm hiểu và mua sắm đồ thủ công.' => 'A traditional craft village with bamboo and rattan weaving products, where you can learn and shop for handicrafts.',
            'Khu du lịch sinh thái với cảnh quan đẹp, nhiều hoạt động giải trí và thư giãn.' => 'An ecological tourist area with beautiful landscapes, various recreational activities and relaxation.',
          ]
        ];
        // If Vietnamese, return original
        if($lang === 'vi') {
          return $key;
        }
        // If English, return translation or original if not found
        return $translations[$lang][$key] ?? $key;
      }
      
      // Lấy search keyword
        $search_keyword = '';
        $search_term = '';
        if(isset($_GET['search']) && !empty(trim($_GET['search']))) {
          $search = filteration($_GET['search']);
          $search_term = $search; // Store for highlighting
          $search = mysqli_real_escape_string($con, $search);
          $search_keyword = "AND (`name` LIKE '%$search%' OR `description` LIKE '%$search%' OR `location` LIKE '%$search%')";
        }
        
        // Lấy category filter
        $category_filter = '';
        if(isset($_GET['category']) && $_GET['category'] != 'all' && !empty($_GET['category'])) {
          $category = filteration($_GET['category']);
          // Escape để tránh SQL injection
          $category = mysqli_real_escape_string($con, $category);
          $category_filter = "AND `category` = '$category'";
        }
        
        // Lấy sort option
        $sort_order = "ORDER BY `rating` DESC, `name` ASC";
        if(isset($_GET['sort']) && !empty($_GET['sort'])) {
          $sort = filteration($_GET['sort']);
          switch($sort) {
            case 'rating_desc':
              $sort_order = "ORDER BY `rating` DESC, `review_count` DESC, `name` ASC";
              break;
            case 'rating_asc':
              $sort_order = "ORDER BY `rating` ASC, `review_count` ASC, `name` ASC";
              break;
            case 'name_asc':
              $sort_order = "ORDER BY `name` ASC";
              break;
            case 'name_desc':
              $sort_order = "ORDER BY `name` DESC";
              break;
            case 'reviews_desc':
              $sort_order = "ORDER BY `review_count` DESC, `rating` DESC, `name` ASC";
              break;
            default:
              $sort_order = "ORDER BY `rating` DESC, `name` ASC";
          }
        }
        
      // Kiểm tra bảng destinations có tồn tại không
      $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'destinations'");
      if($table_check && mysqli_num_rows($table_check) > 0) {
        // Query lấy danh sách điểm du lịch
        $destinations_query = "SELECT * FROM `destinations` 
                              WHERE `active` = 1 
                              $category_filter
                              $search_keyword
                              $sort_order";
        
        $destinations_result = @mysqli_query($con, $destinations_query);
        $total_results = $destinations_result ? mysqli_num_rows($destinations_result) : 0;
      }
      
      $path = DESTINATIONS_IMG_PATH;
        
      // Category labels - dịch theo ngôn ngữ
      $category_labels = [
        'temple' => $current_lang === 'en' ? 'Temples, Communal Houses' : 'Chùa, Đình',
        'nature' => $current_lang === 'en' ? 'Nature' : 'Thiên nhiên',
        'market' => $current_lang === 'en' ? 'Floating Markets' : 'Chợ nổi',
        'culture' => $current_lang === 'en' ? 'Culture' : 'Văn hóa',
        'other' => $current_lang === 'en' ? 'Other' : 'Khác'
      ];
    ?>
    <div class="row g-4">
      <?php
      if($destinations_result && $total_results > 0) {
          while($row = mysqli_fetch_assoc($destinations_result)) {
            $id = $row['id'];
            
            // Dịch name và description
            $name_raw = $row['name'];
            $name_translated = t_destinations($name_raw, $current_lang);
            $name = !empty($search_term) ? highlightSearchTerm($name_translated, $search_term) : htmlspecialchars($name_translated, ENT_QUOTES, 'UTF-8');
            
            $description_raw = $row['short_description'] ?? $row['description'];
            $description_translated = t_destinations($description_raw, $current_lang);
            $description = !empty($search_term) ? highlightSearchTerm($description_translated, $search_term) : htmlspecialchars($description_translated, ENT_QUOTES, 'UTF-8');
            
            $location = !empty($search_term) ? highlightSearchTerm($row['location'], $search_term) : htmlspecialchars($row['location'], ENT_QUOTES, 'UTF-8');
            $category = $row['category'];
            $rating = (float)$row['rating'];
            $review_count = (int)$row['review_count'];
            
            // Lấy ảnh chính từ destination_images hoặc fallback về destinations.image
            $primary_image = '';
            $images_count = 0;
            
            // Check if destination_images table exists
            $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'destination_images'");
            if($table_check && mysqli_num_rows($table_check) > 0){
              $primary_image_query = "SELECT image FROM `destination_images` WHERE `destination_id` = ? AND `is_primary` = 1 ORDER BY `sort_order` ASC LIMIT 1";
              $primary_image_result = @select($primary_image_query, [$id], 'i');
              if($primary_image_result && mysqli_num_rows($primary_image_result) > 0){
                $primary_image_data = mysqli_fetch_assoc($primary_image_result);
                $primary_image = $primary_image_data['image'];
              }
              
              // Đếm số ảnh
              $images_count_query = "SELECT COUNT(*) as count FROM `destination_images` WHERE `destination_id` = ?";
              $images_count_result = @select($images_count_query, [$id], 'i');
              if($images_count_result && mysqli_num_rows($images_count_result) > 0){
                $images_count_data = mysqli_fetch_assoc($images_count_result);
                $images_count = (int)$images_count_data['count'];
              }
            }
            
            // Fallback to old image field if no primary image in destination_images
            if(empty($primary_image)){
              $primary_image = $row['image'] ? $row['image'] : 'default.jpg';
            }
            
            // Đảm bảo path không có double slash
            $path_clean = rtrim($path, '/') . '/';
            $image = $path_clean . $primary_image;
            // Nếu không có ảnh thực sự (default), hiển thị vùng trống thay vì default.jpg
            if(empty($primary_image) || $primary_image === 'default.jpg'){
              $image_html = "<div style='width:100%;height:280px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#9ca3af;'>No Image</div>";
            } else {
              $image_html = "<img src=\"$image\" alt=\"$name\" onerror=\"this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'400\\' height=\\'280\\'%3E%3Crect fill=\\'%23f3f4f6\\' width=\\'400\\' height=\\'280\\'/%3E%3Ctext x=\\'50%25\\' y=\\'50%25\\' text-anchor=\\'middle\\' dy=\\'.3em\\' fill=\\'%239ca3af\\' font-family=\\'Arial\\' font-size=\\'18\\'%3ENo Image%3C/text%3E%3C/svg%3E';\">";
            }
            $category_label = $category_labels[$category] ?? 'Khác';
            
            // Generate stars
            $stars = '';
            $full_stars = floor($rating);
            $has_half = ($rating - $full_stars) >= 0.5;
            for($i = 0; $i < $full_stars; $i++) {
              $stars .= '<i class="bi bi-star-fill"></i>';
            }
            if($has_half) {
              $stars .= '<i class="bi bi-star-half"></i>';
            }
            for($i = $full_stars + ($has_half ? 1 : 0); $i < 5; $i++) {
              $stars .= '<i class="bi bi-star"></i>';
            }
            
            // Tạo badge số lượng ảnh
            $images_badge = '';
            if($images_count > 1) {
              $images_text = $current_lang === 'en' ? 'images' : 'ảnh';
              $images_badge = "<span class='images-count-badge'><i class='bi bi-images me-1'></i>$images_count $images_text</span>";
            }
            
            // Dịch text cho reviews và button
            $reviews_text = $current_lang === 'en' ? 'reviews' : 'đánh giá';
            $view_details_text = $current_lang === 'en' ? 'View details' : 'Xem chi tiết';
            
            echo<<<data
              <div class="col-lg-4 col-md-6">
                <div class="destination-card">
                  <div class="destination-image-wrapper">
                    $image_html
                    <span class="category-badge $category">$category_label</span>
                    $images_badge
                  </div>
                  <div class="destination-card-body">
                    <h5>$name</h5>
                    <div class="location">
                      <i class="bi bi-geo-alt-fill"></i>
                      <span>$location</span>
                    </div>
                    <p>$description</p>
                    <div class="destination-rating">
                      <div class="stars">$stars</div>
                      <span class="rating-text">$rating</span>
                      <span class="rating-text text-muted">($review_count $reviews_text)</span>
                    </div>
                    <a href="destination_details.php?id=$id" class="destination-btn">
                      <i class="bi bi-arrow-right-circle me-2"></i>$view_details_text
                    </a>
                  </div>
                </div>
              </div>
            data;
          }
        } else {
          $no_results_title = $current_lang === 'en' ? 'No destinations found' : 'Không tìm thấy điểm du lịch';
          $no_results_desc = $current_lang === 'en' ? 'Please try again with different filters.' : 'Vui lòng thử lại với bộ lọc khác.';
          echo<<<data
            <div class="col-12">
              <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h4>$no_results_title</h4>
                <p>$no_results_desc</p>
              </div>
            </div>
          data;
        }
      ?>
    </div>
  </div>

  <!-- Section Đặc sản Vĩnh Long -->
  <div class="container my-5 py-5">
    <div class="destinations-header">
      <div class="text-center">
        <p class="text-uppercase text-muted small mb-2" style="letter-spacing: 2px;" data-i18n="destinations.specialtiesSubtitle">ĐẶC SẢN ĐỊA PHƯƠNG</p>
        <h2 class="fw-bold text-center" data-i18n="destinations.specialtiesHeading">ĐẶC SẢN VĨNH LONG</h2>
        <div class="h-line mx-auto mb-4"></div>
      </div>
      <p data-i18n="destinations.specialtiesIntro">Khám phá những món ăn, trái cây và quà lưu niệm đặc trưng của vùng đất Vĩnh Long</p>
    </div>

    <?php
    // Load đặc sản từ database - Safe check
    $specialties_result = null;
    $specialties_path = defined('SPECIALTIES_IMG_PATH') ? SPECIALTIES_IMG_PATH : 'images/specialties/';
    
    // Function to translate specialty names and descriptions
    function t_specialties($key, $lang = 'vi') {
      $translations = [
        'vi' => [
          // Vietnamese - return as is
        ],
        'en' => [
          // Names
          'Bưởi Năm Roi' => 'Nam Roi Pomelo',
          'Chuột đồng nướng' => 'Grilled Field Rat',
          'Bánh tét lá cẩm' => 'Purple Sticky Rice Cake',
          'Dừa sáp Cầu Kè' => 'Cau Ke Waxy Coconut',
          'Bánh phồng tôm' => 'Shrimp Crackers',
          'Mắm cá linh' => 'Linh Fish Sauce',
          'Bánh xèo' => 'Vietnamese Pancake',
          'Cá lóc nướng trui' => 'Grilled Snakehead Fish',
          'Chuối xiêm' => 'Siamese Banana',
          'Sầu riêng' => 'Durian',
          'Chôm chôm' => 'Rambutan',
          'Nhãn' => 'Longan',
          'Dừa sáp Cầu Kè' => 'Cau Ke Waxy Coconut',
          'Bánh phồng tôm Sa Đéc' => 'Sa Dec Shrimp Crackers',
          'Kẹo dừa Bến Tre' => 'Ben Tre Coconut Candy',
          // Descriptions
          'Bưởi Năm Roi Vĩnh Long nổi tiếng với vị ngọt thanh, mọng nước, được trồng nhiều ở huyện Bình Minh và Vũng Liêm.' => 'Vinh Long Nam Roi Pomelo is famous for its sweet, refreshing taste and juiciness, mainly grown in Binh Minh and Vung Liem districts.',
          'Món ăn đặc trưng của vùng sông nước, chuột đồng được nướng vàng giòn, thơm ngon đặc biệt.' => 'A signature dish of the river region, field rat is grilled to golden crispiness with a unique delicious flavor.',
          'Bánh tét Vĩnh Long với lá cẩm tạo màu tím đẹp mắt, nhân đậu xanh và thịt ba chỉ thơm ngon.' => 'Vinh Long sticky rice cake with purple leaves creating beautiful purple color, filled with mung beans and delicious pork belly.',
          'Đặc sản dừa sáp độc đáo của Cầu Kè, Vĩnh Long, với phần cơm dừa đặc, dẻo, thơm ngon đặc biệt.' => 'Unique specialty waxy coconut from Cau Ke, Vinh Long, with thick, chewy, and uniquely fragrant coconut meat.',
          'Bánh phồng tôm giòn tan, thơm mùi tôm đặc trưng, là món ăn vặt được yêu thích.' => 'Crispy shrimp crackers with distinctive shrimp aroma, a favorite snack.',
          'Mắm cá linh truyền thống, được làm từ cá linh tươi, có vị mặn đậm đà đặc trưng.' => 'Traditional linh fish sauce, made from fresh linh fish, with a distinctive rich salty flavor.',
          'Bánh xèo Vĩnh Long với nhân tôm thịt, rau sống, bánh giòn thơm, ăn kèm nước mắm chua ngọt.' => 'Vinh Long Vietnamese pancake with shrimp and pork filling, fresh vegetables, crispy and fragrant, served with sweet and sour fish sauce.',
          'Cá lóc nướng trui theo cách truyền thống, thịt cá thơm ngon, da giòn, ăn kèm rau sống và nước mắm gừng.' => 'Snakehead fish grilled in traditional style, with fragrant meat, crispy skin, served with fresh vegetables and ginger fish sauce.',
          'Dừa sáp Cầu Kè là đặc sản quý hiếm, cơm dừa dày, mềm như sáp, vị ngọt thanh đặc biệt.' => 'Cau Ke Waxy Coconut is a rare specialty, with thick, soft, waxy-like coconut meat and a uniquely sweet taste.',
          'Cá lóc tươi được nướng trực tiếp trên than hồng, thịt cá thơm ngon, giữ nguyên vị ngọt tự nhiên.' => 'Fresh snakehead fish grilled directly over hot coals, with fragrant meat that retains its natural sweetness.',
          'Bánh phồng tôm giòn rụm, thơm mùi tôm đặc trưng, là món quà lưu niệm phổ biến của Vĩnh Long.' => 'Crispy shrimp crackers with distinctive shrimp aroma, a popular souvenir from Vinh Long.',
          'Mắm cá linh là món ăn đặc trưng của vùng sông nước, có vị mặn đậm đà, thơm ngon.' => 'Linh fish sauce is a signature dish of the river region, with a rich salty flavor and delicious taste.',
          'Kẹo dừa thơm ngon, ngọt thanh, là món quà lưu niệm phổ biến của vùng Đồng bằng sông Cửu Long.' => 'Delicious coconut candy with a sweet, refreshing taste, a popular souvenir from the Mekong Delta region.',
        ]
      ];
      // If Vietnamese, return original
      if($lang === 'vi') {
        return $key;
      }
      // If English, return translation or original if not found
      return $translations[$lang][$key] ?? $key;
    }
    
    // Kiểm tra bảng specialties có tồn tại không
    $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'specialties'");
    if($table_check && mysqli_num_rows($table_check) > 0) {
      $specialties_query = "SELECT * FROM `specialties` WHERE `active` = 1 ORDER BY `rating` DESC, `review_count` DESC LIMIT 8";
      $specialties_result = @mysqli_query($con, $specialties_query);
    }
    
    if($specialties_result && mysqli_num_rows($specialties_result) > 0) {
      echo '<div class="row g-4">';
      while($spec = mysqli_fetch_assoc($specialties_result)) {
        $spec_id = $spec['id'];
        
        // Dịch specialty name và description
        $spec_name_raw = $spec['name'];
        $spec_name_translated = t_specialties($spec_name_raw, $current_lang);
        $spec_name = htmlspecialchars($spec_name_translated, ENT_QUOTES, 'UTF-8');
        
        $spec_desc_raw = mb_substr($spec['short_description'] ?? $spec['description'], 0, 120);
        $spec_desc_translated = t_specialties($spec_desc_raw, $current_lang);
        $spec_desc = htmlspecialchars($spec_desc_translated . '...', ENT_QUOTES, 'UTF-8');
        
        // Lấy ảnh chính từ specialty_images hoặc fallback về specialties.image
        $spec_primary_image = '';
        $spec_images_count = 0;
        
        // Check if specialty_images table exists
        $img_table_check = @mysqli_query($con, "SHOW TABLES LIKE 'specialty_images'");
        if($img_table_check && mysqli_num_rows($img_table_check) > 0){
          // Try to get primary image
          $primary_image_query = "SELECT image FROM `specialty_images` WHERE `specialty_id` = ? AND `is_primary` = 1 ORDER BY `sort_order` ASC LIMIT 1";
          $primary_image_result = @select($primary_image_query, [$spec_id], 'i');
          if($primary_image_result && mysqli_num_rows($primary_image_result) > 0){
            $primary_image_data = mysqli_fetch_assoc($primary_image_result);
            $spec_primary_image = $primary_image_data['image'];
          } else {
            // If no primary image, get first image
            $first_image_query = "SELECT image FROM `specialty_images` WHERE `specialty_id` = ? ORDER BY `sort_order` ASC LIMIT 1";
            $first_image_result = @select($first_image_query, [$spec_id], 'i');
            if($first_image_result && mysqli_num_rows($first_image_result) > 0){
              $first_image_data = mysqli_fetch_assoc($first_image_result);
              $spec_primary_image = $first_image_data['image'];
            }
          }
          
          // Đếm số ảnh
          $images_count_query = "SELECT COUNT(*) as count FROM `specialty_images` WHERE `specialty_id` = ?";
          $images_count_result = @select($images_count_query, [$spec_id], 'i');
          if($images_count_result && mysqli_num_rows($images_count_result) > 0){
            $images_count_data = mysqli_fetch_assoc($images_count_result);
            $spec_images_count = (int)$images_count_data['count'];
          }
        }
        
        // Fallback to old image field if no primary image in specialty_images
        if(empty($spec_primary_image)){
          $spec_primary_image = !empty($spec['image']) ? $spec['image'] : 'default.jpg';
        }
        
        // Đảm bảo path không có double slash
        $specialties_path_clean = rtrim($specialties_path, '/') . '/';
        $spec_image = $specialties_path_clean . $spec_primary_image;
        // Nếu không có ảnh thực sự (default), hiển thị vùng trống thay vì default.jpg
        if(empty($spec_primary_image) || $spec_primary_image === 'default.jpg'){
          $spec_image_html = "<div style='width:100%;height:200px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#9ca3af;'>No Image</div>";
        } else {
          $spec_image_html = "<img src=\"$spec_image\" alt=\"$spec_name\" onerror=\"this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'400\\' height=\\'280\\'%3E%3Crect fill=\\'%23f3f4f6\\' width=\\'400\\' height=\\'280\\'/%3E%3Ctext x=\\'50%25\\' y=\\'50%25\\' text-anchor=\\'middle\\' dy=\\'.3em\\' fill=\\'%239ca3af\\' font-family=\\'Arial\\' font-size=\\'18\\'%3ENo Image%3C/text%3E%3C/svg%3E';\">";
        }
        $spec_category = htmlspecialchars($spec['category'], ENT_QUOTES, 'UTF-8');
        // Dịch price_range nếu cần
        $spec_price_raw = $spec['price_range'] ?? 'Liên hệ';
        if($current_lang === 'en') {
          if($spec_price_raw === 'Liên hệ') {
            $spec_price = 'Contact';
          } else {
            // Dịch các đơn vị trong price_range
            $spec_price = $spec_price_raw;
            $spec_price = str_replace('VNĐ/kg', 'VND/kg', $spec_price);
            $spec_price = str_replace('VNĐ/phần', 'VND/serving', $spec_price);
            $spec_price = str_replace('VNĐ/cái', 'VND/piece', $spec_price);
            $spec_price = str_replace('VNĐ/hộp', 'VND/box', $spec_price);
            $spec_price = str_replace('VNĐ/hũ', 'VND/jar', $spec_price);
            $spec_price = str_replace('VNĐ/trái', 'VND/fruit', $spec_price);
            $spec_price = htmlspecialchars($spec_price, ENT_QUOTES, 'UTF-8');
          }
        } else {
          $spec_price = htmlspecialchars($spec_price_raw, ENT_QUOTES, 'UTF-8');
        }
        $spec_location = htmlspecialchars($spec['location'] ?? '', ENT_QUOTES, 'UTF-8');
        $spec_rating = number_format($spec['rating'], 1);
        $spec_review_count = (int)$spec['review_count'];
        
        // Category labels - dịch theo ngôn ngữ
        $category_labels = [
          'food' => $current_lang === 'en' ? 'Food' : 'Món ăn',
          'fruit' => $current_lang === 'en' ? 'Fruits' : 'Trái cây',
          'drink' => $current_lang === 'en' ? 'Beverages' : 'Đồ uống',
          'souvenir' => $current_lang === 'en' ? 'Souvenirs' : 'Quà lưu niệm'
        ];
        $category_label = $category_labels[$spec_category] ?? ($current_lang === 'en' ? 'Specialties' : 'Đặc sản');
        
        // Stars
        $stars = '';
        $full_stars = floor($spec['rating']);
        $half_star = ($spec['rating'] - $full_stars) >= 0.5;
        for($i = 0; $i < $full_stars; $i++) {
          $stars .= '<i class="bi bi-star-fill"></i>';
        }
        if($half_star) {
          $stars .= '<i class="bi bi-star-half"></i>';
        }
        for($i = $full_stars + ($half_star ? 1 : 0); $i < 5; $i++) {
          $stars .= '<i class="bi bi-star"></i>';
        }
        
        // Tạo badge số lượng ảnh
        $spec_images_badge = '';
        if($spec_images_count > 1) {
          $spec_images_text = $current_lang === 'en' ? 'images' : 'ảnh';
          $spec_images_badge = "<span class='images-count-badge'><i class='bi bi-images me-1'></i>$spec_images_count $spec_images_text</span>";
        }
        
        // Dịch text cho button
        $spec_view_details_text = $current_lang === 'en' ? 'View details' : 'Xem chi tiết';
        
        echo<<<data
          <div class="col-lg-3 col-md-6">
            <div class="destination-card specialty-card">
              <div class="destination-image-wrapper">
                $spec_image_html
                <span class="category-badge $spec_category">$category_label</span>
                $spec_images_badge
              </div>
              <div class="destination-card-body">
                <h5>$spec_name</h5>
                <div class="location">
                  <i class="bi bi-geo-alt-fill"></i>
                  <span>$spec_location</span>
                </div>
                <p>$spec_desc</p>
                <div class="specialty-info mb-2">
                  <small class="text-muted"><i class="bi bi-currency-dollar"></i> $spec_price</small>
                </div>
                <div class="destination-rating mb-3">
                  <div class="stars">$stars</div>
                  <span class="rating-text">$spec_rating</span>
                  <span class="rating-text text-muted">($spec_review_count)</span>
                </div>
                <a href="specialty_details.php?id=$spec_id" class="destination-btn">
                  <i class="bi bi-arrow-right-circle me-2"></i>$spec_view_details_text
                </a>
              </div>
            </div>
          </div>
        data;
      }
      echo '</div>';
    } else {
      $no_specialties_title = $current_lang === 'en' ? 'No specialty information available' : 'Chưa có thông tin đặc sản';
      $no_specialties_desc = $current_lang === 'en' ? 'Specialty information will be updated soon.' : 'Thông tin đặc sản sẽ được cập nhật sớm.';
      echo<<<data
        <div class="col-12">
          <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h4>$no_specialties_title</h4>
            <p>$no_specialties_desc</p>
          </div>
        </div>
      data;
    }
    ?>
  </div>

  <?php require('inc/footer.php'); ?>
  <?php require('inc/modals.php'); ?>
  
  <script>
    // Intersection Observer for fade-in animations
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    }, observerOptions);
    
    // Observe all destination cards
    document.querySelectorAll('.destination-card').forEach(card => {
      card.classList.add('fade-in');
      observer.observe(card);
    });
    
    // Lazy loading images
    if ('loading' in HTMLImageElement.prototype) {
      const images = document.querySelectorAll('img[data-src]');
      images.forEach(img => {
        img.src = img.dataset.src;
      });
    } else {
      // Fallback for browsers that don't support lazy loading
      const script = document.createElement('script');
      script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
      document.body.appendChild(script);
    }
    
    // Update result count on page load
    document.addEventListener('DOMContentLoaded', function() {
      const resultCount = document.getElementById('resultCount');
      const totalCards = document.querySelectorAll('.destination-card').length;
      if (resultCount && totalCards > 0) {
        const foundText = window.i18n ? window.i18n.translate('destinations.foundResults', window.i18n.getCurrentLanguage()) : 'Tìm thấy';
        const destinationsText = window.i18n ? window.i18n.translate('destinations.destinations', window.i18n.getCurrentLanguage()) : 'điểm du lịch';
        resultCount.innerHTML = `${foundText} <strong>${totalCards}</strong> ${destinationsText}`;
      }
    });
    
    // ==================== ENHANCED SEARCH FUNCTIONALITY ====================
    let searchTimeout;
    let suggestionsTimeout;
    let currentSuggestions = [];
    const searchInput = document.getElementById('searchInput');
    const searchSuggestions = document.getElementById('searchSuggestions');
    const suggestionsList = document.getElementById('suggestionsList');
    const searchHistory = document.getElementById('searchHistory');
    const historyList = document.getElementById('historyList');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    
    // Search History Management
    function getSearchHistory() {
      try {
        const history = localStorage.getItem('search_history_destinations');
        return history ? JSON.parse(history) : [];
      } catch(e) {
        return [];
      }
    }
    
    function saveSearchHistory(query) {
      if(!query || query.trim().length === 0) return;
      try {
        let history = getSearchHistory();
        query = query.trim();
        // Remove if exists
        history = history.filter(h => h.toLowerCase() !== query.toLowerCase());
        // Add to beginning
        history.unshift(query);
        // Keep only last 10
        history = history.slice(0, 10);
        localStorage.setItem('search_history_destinations', JSON.stringify(history));
        renderSearchHistory();
      } catch(e) {
        console.error('Error saving search history:', e);
      }
    }
    
    function clearSearchHistory() {
      const confirmMsg = window.i18n ? window.i18n.translate('destinations.clearHistoryConfirm', window.i18n.getCurrentLanguage()) : 'Bạn có chắc muốn xóa lịch sử tìm kiếm?';
      if(confirm(confirmMsg)) {
        localStorage.removeItem('search_history_destinations');
        renderSearchHistory();
      }
    }
    
    function renderSearchHistory() {
      const history = getSearchHistory();
      if(history.length === 0) {
        searchHistory.style.display = 'none';
        return;
      }
      
      searchHistory.style.display = 'block';
      historyList.innerHTML = history.map(item => `
        <div class="suggestion-item" onclick="selectSearchHistory('${item.replace(/'/g, "\\'")}')">
          <i class="bi bi-clock-history text-muted"></i>
          <span>${escapeHtml(item)}</span>
        </div>
      `).join('');
    }
    
    function selectSearchHistory(query) {
      searchInput.value = query;
      searchInput.focus();
      hideSuggestions();
      performSearch();
    }
    
    // Fetch Search Suggestions
    function fetchSuggestions(query) {
      if(!query || query.trim().length < 2) {
        hideSuggestions();
        renderSearchHistory();
        return;
      }
      
      clearTimeout(suggestionsTimeout);
      suggestionsTimeout = setTimeout(() => {
        fetch(`ajax/search_suggestions.php?q=${encodeURIComponent(query)}&type=destinations&limit=8`)
          .then(response => response.json())
          .then(data => {
            currentSuggestions = data.suggestions || [];
            renderSuggestions();
          })
          .catch(error => {
            console.error('Error fetching suggestions:', error);
            hideSuggestions();
          });
      }, 300);
    }
    
    function renderSuggestions() {
      if(currentSuggestions.length === 0) {
        const noSuggestionsText = window.i18n ? window.i18n.translate('destinations.noSuggestions', window.i18n.getCurrentLanguage()) : 'Không tìm thấy gợi ý';
        suggestionsList.innerHTML = '<div class="suggestion-item text-muted"><i class="bi bi-info-circle me-2"></i>' + noSuggestionsText + '</div>';
      } else {
        suggestionsList.innerHTML = currentSuggestions.map(item => {
          const highlightedTitle = highlightText(item.title, searchInput.value);
          const highlightedSubtitle = highlightText(item.subtitle, searchInput.value);
          return `
            <div class="suggestion-item" onclick="selectSuggestion('${item.url}', '${item.title.replace(/'/g, "\\'")}')">
              <i class="bi ${item.icon} text-primary"></i>
              <div class="suggestion-content">
                <div class="suggestion-title">${highlightedTitle}</div>
                <div class="suggestion-subtitle">${highlightedSubtitle}</div>
              </div>
              <i class="bi bi-arrow-right text-muted"></i>
            </div>
          `;
        }).join('');
      }
      searchHistory.style.display = 'none';
      searchSuggestions.style.display = 'block';
    }
    
    function highlightText(text, query) {
      if(!query || query.trim().length === 0) return escapeHtml(text);
      const escapedText = escapeHtml(text);
      const escapedQuery = escapeHtml(query);
      const regex = new RegExp(`(${escapedQuery})`, 'gi');
      return escapedText.replace(regex, '<mark>$1</mark>');
    }
    
    function escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }
    
    function selectSuggestion(url, title) {
      saveSearchHistory(title);
      window.location.href = url;
    }
    
    function hideSuggestions() {
      searchSuggestions.style.display = 'none';
    }
    
    function showSuggestions() {
      const query = searchInput.value.trim();
      if(query.length >= 2) {
        fetchSuggestions(query);
      } else {
        renderSearchHistory();
        searchSuggestions.style.display = 'block';
      }
    }
    
    function clearSearch() {
      searchInput.value = '';
      searchInput.focus();
      hideSuggestions();
      clearSearchBtn.style.display = 'none';
      performSearch();
    }
    
    // Event Listeners
    if (searchInput) {
      // Show suggestions on focus
      searchInput.addEventListener('focus', function() {
        showSuggestions();
      });
      
      // Input event for suggestions
      searchInput.addEventListener('input', function() {
        const value = this.value.trim();
        clearSearchBtn.style.display = value.length > 0 ? 'block' : 'none';
        
        if(value.length >= 2) {
          fetchSuggestions(value);
        } else {
          hideSuggestions();
          if(document.activeElement === searchInput) {
            renderSearchHistory();
            searchSuggestions.style.display = 'block';
          }
        }
        
        // Debounced search - chỉ search khi user không đang chọn suggestion
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
          // Chỉ auto-search nếu suggestions đang ẩn (user không đang chọn)
          // Và chỉ khi có giá trị search
          if(searchSuggestions.style.display === 'none' && value.length > 0) {
            // Không tự động search, chỉ update URL khi user nhấn Enter hoặc click search button
            // performSearch(); // Comment out để tránh auto-search liên tục
          }
        }, 800);
      });
      
      // Enter key
      searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          clearTimeout(searchTimeout);
          hideSuggestions();
          performSearch();
        }
      });
      
      // Arrow keys navigation
      let selectedIndex = -1;
      searchInput.addEventListener('keydown', function(e) {
        if(e.key === 'ArrowDown' || e.key === 'ArrowUp') {
          e.preventDefault();
          const items = searchSuggestions.querySelectorAll('.suggestion-item');
          if(items.length === 0) return;
          
          if(e.key === 'ArrowDown') {
            selectedIndex = (selectedIndex + 1) % items.length;
          } else {
            selectedIndex = (selectedIndex - 1 + items.length) % items.length;
          }
          
          items.forEach((item, idx) => {
            item.classList.toggle('selected', idx === selectedIndex);
          });
        } else if(e.key === 'Enter' && selectedIndex >= 0) {
          e.preventDefault();
          const items = searchSuggestions.querySelectorAll('.suggestion-item');
          if(items[selectedIndex]) {
            items[selectedIndex].click();
          }
        }
      });
      
      // Hide suggestions when clicking outside
      document.addEventListener('click', function(e) {
        if(!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
          hideSuggestions();
        }
      });
    }
    
    // Initial render
    renderSearchHistory();
    
    // Enhanced performSearch function
    function performSearch() {
      const searchValue = searchInput ? searchInput.value.trim() : '';
      
      if(searchValue) {
        saveSearchHistory(searchValue);
      }
      
      const currentUrl = new URL(window.location.href);
      
      if (searchValue) {
        currentUrl.searchParams.set('search', searchValue);
      } else {
        currentUrl.searchParams.delete('search');
      }
      
      // Preserve category and sort
      const category = currentUrl.searchParams.get('category') || '';
      const sort = currentUrl.searchParams.get('sort') || '';
      
      // Reload page immediately without showing loading (page will reload anyway)
      window.location.href = currentUrl.toString();
    }
    
    // Apply filters (sort and category)
    function applyFilters() {
      const sortSelect = document.getElementById('sortSelect');
      const currentUrl = new URL(window.location.href);
      
      // Get sort value
      const sortValue = sortSelect ? sortSelect.value : '';
      
      // Update sort parameter
      if (sortValue) {
        currentUrl.searchParams.set('sort', sortValue);
      } else {
        currentUrl.searchParams.delete('sort');
      }
      
      // Preserve search and category
      const search = currentUrl.searchParams.get('search') || '';
      const category = currentUrl.searchParams.get('category') || '';
      
      // Reload page immediately without showing loading (page will reload anyway)
      window.location.href = currentUrl.toString();
    }
    
    // Smooth scroll for category filter
    document.querySelectorAll('.category-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        // Add smooth scroll to top of results
        setTimeout(() => {
          const resultsSection = document.querySelector('.row.g-4');
          if (resultsSection) {
            resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }, 100);
      });
    });
  </script>

</body>
</html>
