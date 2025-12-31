<?php
  // Load database config and essentials first
  require('admin/inc/db_config.php');
  require('admin/inc/essentials.php');
  
  if(!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('destinations.php');
  }
  
  $specialty_id = (int)$_GET['id'];
  
  // Mặc định là tiếng Việt, chỉ chuyển sang tiếng Anh khi có cookie 'lang' = 'en'
  // Đảm bảo chỉ nhận 'vi' hoặc 'en', không tự động phát hiện ngôn ngữ trình duyệt
  // Đọc cookie và validate
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
  
  // Hàm dịch cho trang specialty details
  function t_specialty_details($key, $lang = 'vi') {
    $translations = [
      'vi' => [
        'specialtyDetails.home' => 'Trang chủ',
        'specialtyDetails.destinations' => 'Điểm đến',
        'specialtyDetails.price' => 'Giá',
        'specialtyDetails.bestSeason' => 'Mùa tốt nhất',
        'specialtyDetails.location' => 'Địa điểm',
        'specialtyDetails.rating' => 'Đánh giá',
        'specialtyDetails.reviews' => 'đánh giá',
        'specialtyDetails.viewOnMap' => 'Xem trên bản đồ',
        'specialtyDetails.getDirections' => 'Chỉ đường',
        'specialtyDetails.buyLocations' => 'Địa điểm mua',
        'specialtyDetails.contact' => 'Liên hệ',
        'specialtyDetails.address' => 'Địa chỉ',
        'specialtyDetails.phone' => 'Điện thoại',
        'specialtyDetails.website' => 'Website',
        'specialtyDetails.giftSuggestion' => 'Gợi ý quà lưu niệm',
        'specialtyDetails.openingHours' => 'Giờ mở cửa',
      ],
      'en' => [
        'specialtyDetails.home' => 'Home',
        'specialtyDetails.destinations' => 'Destinations',
        'specialtyDetails.price' => 'Price',
        'specialtyDetails.bestSeason' => 'Best Season',
        'specialtyDetails.location' => 'Location',
        'specialtyDetails.rating' => 'Rating',
        'specialtyDetails.reviews' => 'reviews',
        'specialtyDetails.viewOnMap' => 'View on Map',
        'specialtyDetails.getDirections' => 'Get Directions',
        'specialtyDetails.buyLocations' => 'Buy Locations',
        'specialtyDetails.contact' => 'Contact',
        'specialtyDetails.address' => 'Address',
        'specialtyDetails.phone' => 'Phone',
        'specialtyDetails.website' => 'Website',
        'specialtyDetails.giftSuggestion' => 'Gift Suggestion',
        'specialtyDetails.openingHours' => 'Opening Hours',
      ]
    ];
    return $translations[$lang][$key] ?? $translations['vi'][$key] ?? $key;
  }
  
  // Hàm dịch tên và mô tả đặc sản
  function t_specialties($key, $lang = 'vi') {
    // Nếu tiếng Việt, trả về nguyên bản
    if($lang === 'vi') {
      return $key;
    }
    
    // Mapping các tên đặc sản phổ biến
    $specialty_name_map = [
      'Bưởi Năm Roi' => 'Nam Roi Pomelo',
      'Chôm chôm' => 'Rambutan',
      'Sầu riêng' => 'Durian',
      'Nhãn' => 'Longan',
      'Măng cụt' => 'Mangosteen',
      'Dừa' => 'Coconut',
      'Xoài' => 'Mango',
      'Bánh pía' => 'Pia Cake',
      'Kẹo dừa' => 'Coconut Candy',
      'Bánh tráng' => 'Rice Paper',
      'Chuột đồng nướng' => 'Grilled Field Rat',
      'Bánh tét lá cẩm' => 'Purple Sticky Rice Cake',
    ];
    
    // Kiểm tra mapping chính xác
    if(isset($specialty_name_map[$key])) {
      return $specialty_name_map[$key];
    }
    
    // Mapping các description đầy đủ
    $specialty_desc_map = [
      'Bưởi Năm Roi là đặc sản nổi tiếng của Vĩnh Long, được trồng chủ yếu ở huyện Bình Minh và Vũng Liêm. Bưởi có vị ngọt thanh, mọng nước, múi dày, được nhiều người yêu thích. Mùa thu hoạch chính từ tháng 8 đến tháng 12.' => 'Nam Roi Pomelo is a famous specialty of Vinh Long, mainly grown in Binh Minh and Vung Liem districts. The pomelo has a sweet, refreshing taste, is juicy, with thick segments, and is loved by many. The main harvest season is from August to December.',
      'Nam Roi Pomelo là món quà lưu niệm tuyệt vời khi đến thăm Vĩnh Long. Trái cây tươi ngon này có thể mua về làm quà, đặc biệt vào mùa thu hoạch. Bạn có thể mua tại các địa điểm được gợi ý ở trên hoặc tại các cửa hàng đặc sản trong thành phố.' => 'Nam Roi Pomelo is a great souvenir when visiting Vinh Long. This fresh and delicious fruit can be bought as a gift, especially during the harvest season. You can buy it at the suggested locations above or at specialty stores in the city.',
      'Chuột đồng nướng là món ăn đặc trưng của vùng Đồng bằng sông Cửu Long. Chuột được bắt từ đồng ruộng, làm sạch và nướng trên than hồng. Thịt chuột thơm ngon, giòn rụm, là món nhậu được nhiều người yêu thích.' => 'Grilled field rat is a characteristic dish of the Mekong Delta region. Rats are caught from the fields, cleaned and grilled over hot coals. The rat meat is fragrant, crispy, and is a favorite snack dish for many people.',
      'Bánh tét lá cẩm là món ăn truyền thống của Vĩnh Long, đặc biệt trong dịp Tết. Bánh được gói bằng lá cẩm tạo màu tím đẹp mắt, nhân đậu xanh và thịt ba chỉ. Bánh có vị ngọt béo, thơm mùi lá cẩm đặc trưng.' => 'Purple sticky rice cake (Banh tet la cam) is a traditional dish of Vinh Long, especially during Tet holiday. The cake is wrapped with purple leaves creating a beautiful purple color, filled with mung beans and pork belly. The cake has a sweet, fatty taste with the characteristic aroma of purple leaves.',
    ];
    
    // Kiểm tra mapping description
    if(isset($specialty_desc_map[$key])) {
      return $specialty_desc_map[$key];
    }
    
    // Mapping best_season
    $best_season_map = [
      'Dịp Tết, lễ hội' => 'Tet holiday, festivals',
      'Tháng 8 - 12' => 'August - December',
      'Quanh năm' => 'Year round',
    ];
    if(isset($best_season_map[$key])) {
      return $best_season_map[$key];
    }
    
    // Mapping location
    $location_map = [
      'Chợ địa phương, các cơ sở sản xuất' => 'Local markets, production facilities',
      'Huyện Bình Minh, Vũng Liêm' => 'Binh Minh District, Vung Liem',
      'Các quán ăn địa phương' => 'Local restaurants',
    ];
    if(isset($location_map[$key])) {
      return $location_map[$key];
    }
    
    // Mapping shop names
    $shop_name_map = [
      'Cơ sở bánh tét lá cẩm Hương Miền Tây' => 'Huong Mien Tay Purple Sticky Rice Cake Facility',
      'Vườn bưởi Năm Roi Bình Minh' => 'Binh Minh Nam Roi Pomelo Garden',
      'Chợ nông sản Vũng Liêm' => 'Vung Liem Agricultural Market',
    ];
    if(isset($shop_name_map[$key])) {
      return $shop_name_map[$key];
    }
    
    // Mapping shop addresses
    $shop_address_map = [
      'Huyện Bình Minh, Vĩnh Long' => 'Binh Minh District, Vinh Long',
      'Phường 1, TP. Vĩnh Long' => 'Ward 1, Vinh Long City',
      'Thị trấn Vũng Liêm, Vĩnh Long' => 'Vung Liem Town, Vinh Long',
    ];
    if(isset($shop_address_map[$key])) {
      return $shop_address_map[$key];
    }
    
    // Nếu không tìm thấy, trả về nguyên bản
    return $key;
  }
  
  // Lấy thông tin đặc sản
  $specialty_query = "SELECT * FROM `specialties` WHERE `id` = ? AND `active` = 1";
  $specialty_result = select($specialty_query, [$specialty_id], 'i');
  
  if(mysqli_num_rows($specialty_result) == 0) {
    redirect('destinations.php');
  }
  
  $specialty = mysqli_fetch_assoc($specialty_result);
  $path = SPECIALTIES_IMG_PATH;
  
  // Lấy nhiều ảnh từ specialty_images
  $images = [];
  $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'specialty_images'");
  if($table_check && mysqli_num_rows($table_check) > 0){
    $images_query = "SELECT image FROM `specialty_images` WHERE `specialty_id` = ? ORDER BY `is_primary` DESC, `sort_order` ASC";
    $images_result = @select($images_query, [$specialty_id], 'i');
    if($images_result && mysqli_num_rows($images_result) > 0){
      while($img_row = mysqli_fetch_assoc($images_result)){
        // Loại bỏ đường dẫn nếu có trong tên ảnh (tránh lặp path)
        $image_filename = basename(str_replace('specialties/', '', $img_row['image']));
        $images[] = $path . $image_filename;
      }
    }
  }
  
  // Fallback to old image field if no images in specialty_images
  if(empty($images)){
    if(!empty($specialty['image'])){
      // Loại bỏ đường dẫn nếu có trong tên ảnh (tránh lặp path)
      $old_image = basename(str_replace('specialties/', '', $specialty['image']));
      $images[] = $path . $old_image;
    } else {
      // Nếu không có ảnh, sử dụng inline SVG placeholder
      $images[] = 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'500\'%3E%3Crect fill=\'%23f3f4f6\' width=\'800\' height=\'500\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-family=\'Arial\' font-size=\'24\'%3ENo Image%3C/text%3E%3C/svg%3E';
    }
  }
  
  // Lấy danh sách địa điểm mua
  $shops = [];
  $shops_query = "SELECT * FROM `specialty_shops` WHERE `specialty_id` = ? AND `active` = 1 ORDER BY `rating` DESC";
  $shops_result = @select($shops_query, [$specialty_id], 'i');
  if($shops_result && mysqli_num_rows($shops_result) > 0){
    while($shop_row = mysqli_fetch_assoc($shops_result)){
      $shops[] = $shop_row;
    }
  }
  
  // Category labels với translation
  $category_labels_vi = [
    'food' => 'Món ăn',
    'fruit' => 'Trái cây',
    'drink' => 'Đồ uống',
    'souvenir' => 'Quà lưu niệm'
  ];
  $category_labels_en = [
    'food' => 'Food',
    'fruit' => 'Fruit',
    'drink' => 'Drink',
    'souvenir' => 'Souvenir'
  ];
  $category_label = $current_lang === 'en' 
    ? ($category_labels_en[$specialty['category']] ?? 'Specialty')
    : ($category_labels_vi[$specialty['category']] ?? 'Đặc sản');
  
  // SEO Meta Tags - Sử dụng tên đã dịch
  $spec_name = htmlspecialchars(t_specialties($specialty['name'], $current_lang), ENT_QUOTES, 'UTF-8');
  $spec_desc_text = $specialty['short_description'] ?? substr($specialty['description'], 0, 160);
  $spec_desc = htmlspecialchars(t_specialties($spec_desc_text, $current_lang), ENT_QUOTES, 'UTF-8');
  $spec_image = !empty($images[0]) ? htmlspecialchars($images[0], ENT_QUOTES, 'UTF-8') : '';
  $page_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  
  <title><?php echo $spec_name; ?> - Đặc sản Vĩnh Long - <?php echo $settings_r['site_title']; ?></title>
  <meta name="description" content="<?php echo $spec_desc; ?>">
  <meta name="keywords" content="<?php echo $spec_name; ?>, đặc sản Vĩnh Long, quà lưu niệm Vĩnh Long">
  
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?php echo $page_url; ?>">
  <meta property="og:title" content="<?php echo $spec_name; ?> - Đặc sản Vĩnh Long">
  <meta property="og:description" content="<?php echo $spec_desc; ?>">
  <?php if($spec_image): ?>
  <meta property="og:image" content="<?php echo $spec_image; ?>">
  <?php endif; ?>
  
  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="<?php echo $page_url; ?>">
  <meta property="twitter:title" content="<?php echo $spec_name; ?>">
  <meta property="twitter:description" content="<?php echo $spec_desc; ?>">
  <?php if($spec_image): ?>
  <meta property="twitter:image" content="<?php echo $spec_image; ?>">
  <?php endif; ?>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    
    * {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    
    body { 
      background: #ffffff;
      min-height: 100vh;
    }
    
    .specialty-hero {
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(30px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 32px;
      padding: 2.5rem;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(255, 255, 255, 0.5);
      margin-bottom: 2.5rem;
      animation: fadeInUp 0.6s ease-out;
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
    
    .specialty-main-image {
      width: 100%;
      height: 500px;
      object-fit: cover;
      border-radius: 24px;
      cursor: pointer;
      transition: transform 0.3s ease;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }
    
    .specialty-main-image:hover {
      transform: scale(1.02);
    }

    .gallery-container {
      display: grid;
      gap: 1rem;
    }

    .gallery-main-large {
      position: relative;
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
      cursor: pointer;
    }

    .gallery-main-large img {
      width: 100%;
      height: 500px;
      object-fit: cover;
      transition: transform 0.4s ease;
    }

    .gallery-main-large:hover img {
      transform: scale(1.03);
    }

    .gallery-view-all {
      position: absolute;
      bottom: 16px;
      right: 16px;
      background: rgba(0, 0, 0, 0.65);
      color: #fff;
      padding: 8px 14px;
      border-radius: 999px;
      font-size: 0.9rem;
      display: flex;
      gap: 6px;
      align-items: center;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .gallery-view-all:hover {
      background: rgba(0, 0, 0, 0.8);
    }

    .gallery-grid-below {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 12px;
    }

    .gallery-grid-item {
      position: relative;
      border-radius: 16px;
      overflow: hidden;
      cursor: pointer;
      border: 2px solid transparent;
    }

    .gallery-grid-item.active {
      border-color: #667eea;
    }

    .gallery-grid-item img {
      width: 100%;
      height: 100px;
      object-fit: cover;
    }

    .gallery-grid-item-number {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-weight: 700;
      background: rgba(0, 0, 0, 0.45);
    }

    .image-gallery-modal .modal-dialog {
      margin: 0;
      max-width: 100%;
    }

    .image-gallery-modal .modal-content {
      background: #0c1220;
      border-radius: 0;
    }

    .gallery-modal-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 24px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      color: #fff;
    }

    .gallery-header-left,
    .gallery-header-center,
    .gallery-header-right {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .gallery-header-center {
      flex: 1;
      justify-content: center;
    }

    .gallery-hotel-name {
      font-size: 1rem;
      font-weight: 600;
    }

    .gallery-header-title {
      font-size: 0.95rem;
      font-weight: 600;
      color: rgba(255, 255, 255, 0.85);
    }

    .gallery-close-btn {
      background: transparent;
      border: none;
      color: #fff;
      font-size: 1.1rem;
      cursor: pointer;
    }

    .gallery-modal-body {
      padding: 24px;
    }

    .gallery-modal-main {
      position: relative;
      border-radius: 24px;
      overflow: hidden;
      background: #0b1220;
    }

    .gallery-modal-main-img {
      width: 100%;
      height: 70vh;
      object-fit: contain;
      transition: opacity 0.2s ease;
    }

    .gallery-nav-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(0, 0, 0, 0.6);
      border: none;
      color: #fff;
      width: 44px;
      height: 44px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
    }

    .gallery-nav-prev {
      left: 16px;
    }

    .gallery-nav-next {
      right: 16px;
    }

    .gallery-thumbnails-container {
      padding: 16px 24px 24px;
    }

    .gallery-thumbnails-grid {
      display: flex;
      gap: 10px;
      overflow-x: auto;
      padding-bottom: 6px;
    }

    .gallery-thumbnail-item {
      border-radius: 12px;
      overflow: hidden;
      border: 2px solid transparent;
      cursor: pointer;
      flex: 0 0 auto;
    }

    .gallery-thumbnail-item.active {
      border-color: #667eea;
    }

    .gallery-thumbnail-item img {
      width: 120px;
      height: 90px;
      object-fit: cover;
    }
    
    .specialty-title {
      font-size: 2.5rem;
      font-weight: 800;
      color: #1a202c;
      margin-bottom: 1rem;
      line-height: 1.2;
    }
    
    .specialty-category {
      display: inline-block;
      padding: 0.5rem 1.2rem;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #ffffff;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: 700;
      margin-bottom: 1rem;
    }
    
    .specialty-info-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 1rem;
      padding: 0.75rem;
      background: rgba(102, 126, 234, 0.05);
      border-radius: 12px;
    }
    
    .specialty-info-item i {
      font-size: 1.2rem;
      color: #667eea;
    }
    
    .specialty-description {
      font-size: 1.1rem;
      line-height: 1.9;
      color: #4a5568;
      margin-top: 2rem;
    }
    
    .shops-section {
      margin-top: 3rem;
      padding-top: 3rem;
      border-top: 2px solid #e5e7eb;
    }
    
    .shop-card {
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(30px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 24px;
      padding: 2rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }
    
    .shop-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 30px 80px rgba(0, 0, 0, 0.15);
    }
    
    .shop-name {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1a202c;
      margin-bottom: 0.5rem;
    }
    
    .shop-address {
      color: #6b7280;
      margin-bottom: 0.5rem;
    }
    
    .shop-info {
      display: flex;
      gap: 1.5rem;
      margin-top: 1rem;
      flex-wrap: wrap;
    }
    
    .shop-info-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: #4a5568;
    }
    
    .shop-info-item i {
      color: #667eea;
    }
    
    .gift-suggestion {
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
      border-radius: 20px;
      padding: 2rem;
      margin-top: 2rem;
      border-left: 4px solid #667eea;
    }
    
    .gift-suggestion h4 {
      color: #1a202c;
      margin-bottom: 1rem;
      font-weight: 700;
    }
    
    .gift-suggestion p {
      color: #4a5568;
      line-height: 1.8;
      margin: 0;
    }
    
    @media (max-width: 768px) {
      .specialty-title {
        font-size: 1.8rem;
      }
      
      .specialty-main-image {
        height: 300px;
      }
    }
  </style>
</head>
<body>

  <?php require('inc/header.php'); ?>

  <div class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php"><span data-i18n="specialtyDetails.home" data-i18n-skip><?php echo t_specialty_details('specialtyDetails.home', $current_lang); ?></span></a></li>
        <li class="breadcrumb-item"><a href="destinations.php"><span data-i18n="specialtyDetails.destinations" data-i18n-skip><?php echo t_specialty_details('specialtyDetails.destinations', $current_lang); ?></span></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo $spec_name; ?></li>
      </ol>
    </nav>

    <!-- Hero Section -->
    <div class="specialty-hero">
      <div class="row g-4">
        <div class="col-lg-6">
          <div class="gallery-container">
            <div class="gallery-main-large" onclick="openImageModal(0)">
              <img id="main-gallery-image" src="<?php echo htmlspecialchars($images[0]); ?>" alt="<?php echo $spec_name; ?>"
                   onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'500\'%3E%3Crect fill=\'%23f3f4f6\' width=\'800\' height=\'500\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-family=\'Arial\' font-size=\'24\'%3ENo Image%3C/text%3E%3C/svg%3E';">
              <?php if(count($images) > 1): ?>
              <div class="gallery-view-all" onclick="openImageModal(0); event.stopPropagation();">
                <i class="bi bi-images"></i>
                <span><?php echo $current_lang === 'en' ? 'View all images' : 'Xem tat ca anh'; ?></span>
              </div>
              <?php endif; ?>
            </div>
            <?php if(count($images) > 1): ?>
            <div class="gallery-grid-below">
              <?php for($i = 1; $i < min(count($images), 5); $i++): ?>
                <div class="gallery-grid-item <?php echo $i == 1 ? 'active' : ''; ?>" onclick="changeMainImage(<?php echo $i; ?>)" data-index="<?php echo $i; ?>">
                  <img src="<?php echo htmlspecialchars($images[$i]); ?>" alt="Anh <?php echo $i + 1; ?>"
                       onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'150\'%3E%3Crect fill=\'%23f3f4f6\' width=\'200\' height=\'150\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-family=\'Arial\' font-size=\'14\'%3ENo Image%3C/text%3E%3C/svg%3E';">
                  <div class="gallery-grid-item-number"><?php echo $i + 1; ?></div>
                </div>
              <?php endfor; ?>
              <?php if(count($images) > 5): ?>
                <div class="gallery-grid-item" onclick="openImageModal(5)">
                  <img src="<?php echo htmlspecialchars($images[5]); ?>" alt="Anh <?php echo 6; ?>"
                       onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'150\'%3E%3Crect fill=\'%23f3f4f6\' width=\'200\' height=\'150\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-family=\'Arial\' font-size=\'14\'%3ENo Image%3C/text%3E%3C/svg%3E';">
                  <div class="gallery-grid-item-number">+<?php echo count($images) - 5; ?></div>
                </div>
              <?php endif; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <div class="col-lg-6">
          <span class="specialty-category"><?php echo $category_label; ?></span>
          <h1 class="specialty-title"><?php echo $spec_name; ?></h1>
          
          <div class="specialty-info-item">
            <i class="bi bi-currency-dollar"></i>
            <div>
              <strong data-i18n="specialtyDetails.price" data-i18n-skip><?php echo t_specialty_details('specialtyDetails.price', $current_lang); ?>:</strong> <?php echo htmlspecialchars($specialty['price_range'] ?? t_specialty_details('specialtyDetails.contact', $current_lang), ENT_QUOTES, 'UTF-8'); ?>
            </div>
          </div>
          
          <?php if($specialty['best_season']): ?>
          <div class="specialty-info-item">
            <i class="bi bi-calendar3"></i>
            <div>
              <strong data-i18n="specialtyDetails.bestSeason" data-i18n-skip><?php echo t_specialty_details('specialtyDetails.bestSeason', $current_lang); ?>:</strong> <?php echo htmlspecialchars(t_specialties($specialty['best_season'], $current_lang), ENT_QUOTES, 'UTF-8'); ?>
            </div>
          </div>
          <?php endif; ?>
          
          <?php if($specialty['location']): ?>
          <div class="specialty-info-item">
            <i class="bi bi-geo-alt-fill"></i>
            <div>
              <strong data-i18n="specialtyDetails.location" data-i18n-skip><?php echo t_specialty_details('specialtyDetails.location', $current_lang); ?>:</strong> <?php echo htmlspecialchars(t_specialties($specialty['location'], $current_lang), ENT_QUOTES, 'UTF-8'); ?>
            </div>
          </div>
          <?php endif; ?>
          
          <div class="specialty-info-item">
            <i class="bi bi-star-fill" style="color: #fbbf24;"></i>
            <div>
              <strong data-i18n="specialtyDetails.rating" data-i18n-skip><?php echo t_specialty_details('specialtyDetails.rating', $current_lang); ?>:</strong> 
              <?php echo number_format($specialty['rating'], 1); ?> 
              (<?php echo (int)$specialty['review_count']; ?> <span data-i18n="specialtyDetails.reviews" data-i18n-skip><?php echo t_specialty_details('specialtyDetails.reviews', $current_lang); ?></span>)
            </div>
          </div>
          
          <?php if($specialty['latitude'] && $specialty['longitude']): ?>
          <div class="mt-3">
            <a href="https://www.google.com/maps?q=<?php echo $specialty['latitude']; ?>,<?php echo $specialty['longitude']; ?>" 
               target="_blank" class="btn btn-primary">
              <i class="bi bi-map me-2"></i><span data-i18n="specialtyDetails.viewOnMap" data-i18n-skip><?php echo t_specialty_details('specialtyDetails.viewOnMap', $current_lang); ?></span>
            </a>
          </div>
          <?php endif; ?>
        </div>
      </div>
      
      <div class="specialty-description">
        <?php echo nl2br(htmlspecialchars(t_specialties($specialty['description'], $current_lang), ENT_QUOTES, 'UTF-8')); ?>
      </div>
    </div>

    <!-- Địa điểm mua -->
    <?php if(!empty($shops)): ?>
    <div class="shops-section">
      <h2 class="mb-4" style="font-weight: 800; color: #1a202c;">
        <i class="bi bi-shop me-2" style="color: #667eea;"></i><span data-i18n="specialtyDetails.buyLocations" data-i18n-skip><?php echo t_specialty_details('specialtyDetails.buyLocations', $current_lang); ?></span> <?php echo $spec_name; ?>
      </h2>
      
      <div class="row g-4">
        <?php foreach($shops as $shop): ?>
        <div class="col-md-6">
          <div class="shop-card">
            <h3 class="shop-name"><?php echo htmlspecialchars(t_specialties($shop['shop_name'], $current_lang), ENT_QUOTES, 'UTF-8'); ?></h3>
            <div class="shop-address">
              <i class="bi bi-geo-alt-fill me-2" style="color: #667eea;"></i>
              <strong data-i18n="specialtyDetails.address" data-i18n-skip><?php echo t_specialty_details('specialtyDetails.address', $current_lang); ?>:</strong> <?php echo htmlspecialchars(t_specialties($shop['address'], $current_lang), ENT_QUOTES, 'UTF-8'); ?>
            </div>
            
            <div class="shop-info">
              <?php if($shop['phone']): ?>
              <div class="shop-info-item">
                <i class="bi bi-telephone-fill"></i>
                <span><strong data-i18n="specialtyDetails.phone" data-i18n-skip><?php echo t_specialty_details('specialtyDetails.phone', $current_lang); ?>:</strong> <?php echo htmlspecialchars($shop['phone'], ENT_QUOTES, 'UTF-8'); ?></span>
              </div>
              <?php endif; ?>
              
              <?php if($shop['opening_hours']): ?>
              <div class="shop-info-item">
                <i class="bi bi-clock"></i>
                <span><strong data-i18n="specialtyDetails.openingHours" data-i18n-skip><?php echo t_specialty_details('specialtyDetails.openingHours', $current_lang); ?>:</strong> <?php echo htmlspecialchars($shop['opening_hours'], ENT_QUOTES, 'UTF-8'); ?></span>
              </div>
              <?php endif; ?>
              
              <div class="shop-info-item">
                <i class="bi bi-star-fill" style="color: #fbbf24;"></i>
                <span><strong data-i18n="specialtyDetails.rating" data-i18n-skip><?php echo t_specialty_details('specialtyDetails.rating', $current_lang); ?>:</strong> <?php echo number_format($shop['rating'], 1); ?></span>
              </div>
            </div>
            
            <?php if($shop['latitude'] && $shop['longitude']): ?>
            <div class="mt-3">
              <a href="https://www.google.com/maps?q=<?php echo $shop['latitude']; ?>,<?php echo $shop['longitude']; ?>" 
                 target="_blank" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-map me-2"></i><span data-i18n="specialtyDetails.getDirections" data-i18n-skip><?php echo t_specialty_details('specialtyDetails.getDirections', $current_lang); ?></span>
              </a>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Gợi ý quà lưu niệm -->
    <div class="gift-suggestion">
      <h4><i class="bi bi-gift me-2"></i><span data-i18n="specialtyDetails.giftSuggestion" data-i18n-skip><?php echo t_specialty_details('specialtyDetails.giftSuggestion', $current_lang); ?></span></h4>
      <p>
        <?php
          // Tạo text động dựa trên category
          $gift_text = '';
          if($specialty['category'] == 'souvenir') {
            $gift_text = $current_lang === 'en' 
              ? 'This product is beautifully packaged, suitable as a gift for family and friends.'
              : 'Sản phẩm này được đóng gói đẹp mắt, phù hợp làm quà tặng cho người thân và bạn bè.';
          } elseif($specialty['category'] == 'fruit') {
            $gift_text = $current_lang === 'en'
              ? 'This fresh and delicious fruit can be bought as a gift, especially during the harvest season.'
              : 'Trái cây tươi ngon này có thể mua về làm quà, đặc biệt vào mùa thu hoạch.';
          } else {
            $gift_text = $current_lang === 'en'
              ? 'This is a unique local specialty, a meaningful gift with the distinctive flavor of the Mekong Delta.'
              : 'Đây là đặc sản địa phương độc đáo, là món quà ý nghĩa mang đậm hương vị miền Tây.';
          }
          
          $ending_text = $current_lang === 'en'
            ? 'You can buy it at the suggested locations above or at specialty stores in the city.'
            : 'Bạn có thể mua tại các địa điểm được gợi ý ở trên hoặc tại các cửa hàng đặc sản trong thành phố.';
          
          $intro_text = $current_lang === 'en'
            ? 'is a great souvenir when visiting Vinh Long.'
            : 'là món quà lưu niệm tuyệt vời khi đến thăm Vĩnh Long.';
        ?>
        <strong><?php echo $spec_name; ?></strong> <?php echo $intro_text; ?> 
        <?php echo $gift_text; ?>
        <?php echo $ending_text; ?>
      </p>
    </div>
  </div>

  <?php if(count($images) > 1): ?>
  <div class="modal fade image-gallery-modal" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content bg-dark">
        <div class="gallery-modal-header">
          <div class="gallery-header-left">
            <h5 class="gallery-hotel-name mb-0"><?php echo $spec_name; ?></h5>
          </div>
          <div class="gallery-header-center">
            <span class="gallery-header-title active"><?php echo $current_lang === 'en' ? 'Specialty Images' : 'Hinh anh dac san'; ?></span>
          </div>
          <div class="gallery-header-right">
            <button type="button" class="gallery-close-btn" data-bs-dismiss="modal" aria-label="Close">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>
        <div class="gallery-modal-body">
          <div class="gallery-modal-main">
            <img id="modal-image" src="" alt="<?php echo $spec_name; ?>" class="gallery-modal-main-img">
            <button class="gallery-nav-btn gallery-nav-prev" onclick="prevImage()">
              <i class="bi bi-chevron-left"></i>
            </button>
            <button class="gallery-nav-btn gallery-nav-next" onclick="nextImage()">
              <i class="bi bi-chevron-right"></i>
            </button>
          </div>
        </div>
        <div class="gallery-thumbnails-container">
          <div class="gallery-thumbnails-grid" id="gallery-thumbnails-grid">
            <?php foreach($images as $index => $img): ?>
              <div class="gallery-thumbnail-item <?php echo $index == 0 ? 'active' : ''; ?>" onclick="goToImage(<?php echo $index; ?>)" data-index="<?php echo $index; ?>">
                <img src="<?php echo htmlspecialchars($img); ?>" alt="Anh <?php echo $index + 1; ?>"
                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'120\' height=\'90\'%3E%3Crect fill=\'%23f3f4f6\' width=\'120\' height=\'90\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-family=\'Arial\' font-size=\'12\'%3ENo Image%3C/text%3E%3C/svg%3E';">
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <script>
    const specialtyImages = <?php echo json_encode($images); ?>;
    let currentImageIndex = 0;

    function changeMainImage(index) {
      if(index >= 0 && index < specialtyImages.length) {
        const mainImage = document.getElementById('main-gallery-image');
        if(mainImage) {
          mainImage.style.opacity = '0';
          mainImage.style.transition = 'opacity 0.2s ease';
          setTimeout(() => {
            mainImage.src = specialtyImages[index];
            mainImage.style.opacity = '1';
          }, 150);
        }

        document.querySelectorAll('.gallery-grid-item').forEach((item) => {
          const itemIndex = parseInt(item.getAttribute('data-index') || '0', 10);
          item.classList.toggle('active', itemIndex === index);
        });
      }
    }

    function openImageModal(index) {
      if(!specialtyImages.length) return;
      currentImageIndex = index >= 0 ? index : 0;
      updateModalImage();
      const modalElement = document.getElementById('imageModal');
      if(modalElement) {
        const modal = new bootstrap.Modal(modalElement, {
          backdrop: true,
          keyboard: true
        });
        modal.show();
      }
    }

    function updateModalImage() {
      if(currentImageIndex >= 0 && currentImageIndex < specialtyImages.length) {
        const modalImage = document.getElementById('modal-image');
        if(modalImage) {
          modalImage.style.opacity = '0';
          setTimeout(() => {
            modalImage.src = specialtyImages[currentImageIndex];
            modalImage.style.opacity = '1';
          }, 150);
        }

        document.querySelectorAll('.gallery-thumbnail-item').forEach((item, idx) => {
          item.classList.toggle('active', idx === currentImageIndex);
          if(idx === currentImageIndex) {
            item.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
          }
        });
      }
    }

    function goToImage(index) {
      if(index >= 0 && index < specialtyImages.length) {
        currentImageIndex = index;
        updateModalImage();
      }
    }

    function prevImage() {
      if(!specialtyImages.length) return;
      currentImageIndex = currentImageIndex > 0 ? currentImageIndex - 1 : specialtyImages.length - 1;
      updateModalImage();
    }

    function nextImage() {
      if(!specialtyImages.length) return;
      currentImageIndex = currentImageIndex < specialtyImages.length - 1 ? currentImageIndex + 1 : 0;
      updateModalImage();
    }
  </script>

  <?php require('inc/footer.php'); ?>
  <?php require('inc/modals.php'); ?>

</body>
</html>
