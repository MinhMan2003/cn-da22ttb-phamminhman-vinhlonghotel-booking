<?php
  // Load database config and essentials first
  require('admin/inc/db_config.php');
  require('admin/inc/essentials.php');
  
  if(!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('destinations.php');
  }
  
  $destination_id = (int)$_GET['id'];
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
  
  // Hàm dịch cho trang destination details
  function t_destination_details($key, $lang = 'vi') {
    $translations = [
      'vi' => [
        'destinationDetails.pageTitle' => 'Chi tiết điểm du lịch',
        'destinationDetails.home' => 'Trang chủ',
        'destinationDetails.destinations' => 'Điểm đến',
        'destinationDetails.viewAllImages' => 'Xem tất cả hình ảnh',
        'destinationDetails.address' => 'Địa chỉ',
        'destinationDetails.coordinates' => 'Tọa độ',
        'destinationDetails.viewNearbyRooms' => 'Xem phòng gần đây',
        'destinationDetails.share' => 'Chia sẻ',
        'destinationDetails.facebook' => 'Facebook',
        'destinationDetails.print' => 'In',
        'destinationDetails.backToList' => 'Quay lại danh sách',
        'destinationDetails.introduction' => 'Giới thiệu',
        'destinationDetails.summary' => 'Tóm tắt',
        'destinationDetails.map' => 'Bản đồ',
        'destinationDetails.viewLargeMap' => 'Xem bản đồ lớn',
        'destinationDetails.nearbyRooms' => 'Phòng nghỉ gần đây',
        'destinationDetails.noNearbyRooms' => 'Chưa có phòng nghỉ gần điểm du lịch này.',
        'destinationDetails.kmAway' => 'Cách',
        'destinationDetails.perNight' => '/đêm',
        'destinationDetails.viewRoom' => 'Xem phòng',
        'destinationDetails.similarDestinations' => 'Điểm đến tương tự',
        'destinationDetails.viewDetails' => 'Xem chi tiết',
        'destinationDetails.destinationImages' => 'Hình của điểm du lịch',
        'destinationDetails.allImages' => 'Tất cả hình ảnh',
        'destinationDetails.image' => 'Ảnh',
        'destinationDetails.reviews' => 'đánh giá',
      ],
      'en' => [
        'destinationDetails.pageTitle' => 'Destination Details',
        'destinationDetails.home' => 'Home',
        'destinationDetails.destinations' => 'Destinations',
        'destinationDetails.viewAllImages' => 'View All Images',
        'destinationDetails.address' => 'Address',
        'destinationDetails.coordinates' => 'Coordinates',
        'destinationDetails.viewNearbyRooms' => 'View Nearby Rooms',
        'destinationDetails.share' => 'Share',
        'destinationDetails.facebook' => 'Facebook',
        'destinationDetails.print' => 'Print',
        'destinationDetails.backToList' => 'Back to List',
        'destinationDetails.introduction' => 'Introduction',
        'destinationDetails.summary' => 'Summary',
        'destinationDetails.map' => 'Map',
        'destinationDetails.viewLargeMap' => 'View Large Map',
        'destinationDetails.nearbyRooms' => 'Nearby Rooms',
        'destinationDetails.noNearbyRooms' => 'No rooms near this destination yet.',
        'destinationDetails.kmAway' => 'Away',
        'destinationDetails.perNight' => '/night',
        'destinationDetails.viewRoom' => 'View Room',
        'destinationDetails.similarDestinations' => 'Similar Destinations',
        'destinationDetails.viewDetails' => 'View Details',
        'destinationDetails.destinationImages' => 'Destination Images',
        'destinationDetails.allImages' => 'All Images',
        'destinationDetails.image' => 'Image',
        'destinationDetails.reviews' => 'reviews',
      ]
    ];
    return $translations[$lang][$key] ?? $key;
  }
  
  // Hàm dịch tên phòng
  function t_room_name($name, $lang = 'vi') {
    if($lang === 'vi') {
      return $name; // Giữ nguyên tiếng Việt
    }
    
    // Mapping các tên phòng phổ biến
    $room_name_map = [
      'Phòng Premium' => 'Premium Room',
      'Phòng Cao Cấp' => 'Premium Room', // Giữ lại để tương thích ngược
      'Phòng Deluxe' => 'Deluxe Room',
      'Phòng Suite' => 'Suite Room',
      'Phòng Standard' => 'Standard Room',
      'Phòng Cơ Bản' => 'Basic Room',
      'Phòng Cơ Bản 1' => 'Basic Room 1',
      'Phòng Cơ Bản 2' => 'Basic Room 2',
      'Phòng Cơ Bản 3' => 'Basic Room 3',
      'Phòng Superior' => 'Superior Room',
      'Phòng Executive' => 'Executive Room',
      'Phòng Family' => 'Family Room',
      'Phòng Twin' => 'Twin Room',
      'Phòng Single' => 'Single Room',
      'Phòng Double' => 'Double Room',
      'Phòng Triple' => 'Triple Room',
    ];
    
    // Kiểm tra mapping chính xác
    if(isset($room_name_map[$name])) {
      return $room_name_map[$name];
    }
    
    // Nếu không tìm thấy, thử dịch các từ phổ biến
    $translated = $name;
    $common_words = [
      'Phòng' => 'Room',
      'Cao Cấp' => 'Premium',
      'Deluxe' => 'Deluxe',
      'Suite' => 'Suite',
      'Standard' => 'Standard',
      'Cơ Bản' => 'Basic',
      'Superior' => 'Superior',
      'Executive' => 'Executive',
      'Family' => 'Family',
      'Twin' => 'Twin',
      'Single' => 'Single',
      'Double' => 'Double',
      'Triple' => 'Triple',
    ];
    
    foreach($common_words as $vi => $en) {
      $translated = str_replace($vi, $en, $translated);
    }
    
    return $translated;
  }
  
  // Sử dụng lại hàm t_destinations từ destinations.php
  function t_destinations($key, $lang = 'vi') {
    // If Vietnamese, return original
    if($lang === 'vi') {
      return $key;
    }
    
    // Normalize key để so sánh (trim, normalize whitespace)
    $normalize = function($text) {
      // Decode HTML entities (cả single và double)
      $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
      // Decode lại một lần nữa để xử lý nested entities như &amp;amp;quot;
      $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
      // Trim
      $text = trim($text);
      // Normalize multiple spaces/newlines/tabs to single space
      $text = preg_replace('/[\s\n\r\t]+/', ' ', $text);
      // Loại bỏ các ký tự đặc biệt không cần thiết
      $text = str_replace(["\r\n", "\r", "\n"], ' ', $text);
      $text = preg_replace('/\s+/', ' ', $text);
      return trim($text);
    };
    
    $normalized_key = $normalize($key);
    
    $translations = [
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
        'Vườn cây ăn trái Cái Mơn' => 'Cai Mon Fruit Garden',
        'Đình Long Hồ' => 'Long Ho Communal House',
        'Chùa Phước Hậu' => 'Phuoc Hau Pagoda',
        'Khu du lịch Cồn Chim' => 'Con Chim Tourism Area',
        'Chợ nổi Ngã Năm' => 'Nga Nam Floating Market',
        'Vườn quốc gia Tràm Chim' => 'Tram Chim National Park',
        'Chùa Vĩnh Tràng' => 'Vinh Trang Pagoda',
        // Descriptions (short) - normalized
        'Ngôi chùa cổ kính từ thế kỷ 19, nằm trên cù lao An Bình, mang đậm nét kiến trúc Phật giáo Nam Bộ.' => 'An ancient temple from the 19th century, located on An Binh Island, featuring distinctive Southern Buddhist architecture.',
        'Hòn đảo xanh tươi giữa sông Cổ Chiên, nổi tiếng với vườn cây trái sum suê và cuộc sống miền quê yên bình.' => 'A lush green island in the Co Chien River, famous for its abundant fruit gardens and peaceful countryside life.',
        // Full Descriptions - normalized
        'Chùa Tiên Châu là một trong những ngôi chùa cổ kính và nổi tiếng nhất tại Vĩnh Long. Ngôi chùa được xây dựng từ thế kỷ 19, mang đậm nét kiến trúc Phật giáo Nam Bộ với những họa tiết tinh xảo. Chùa nằm trên cù lao An Bình, được bao quanh bởi dòng sông Cổ Chiên hiền hòa, tạo nên một không gian thanh tịnh, yên bình. Đây là điểm đến lý tưởng cho những ai muốn tìm hiểu về văn hóa tâm linh và kiến trúc cổ của vùng đất Nam Bộ.' => 'Tien Chau Temple is one of the most ancient and famous temples in Vinh Long. The temple was built in the 19th century, featuring distinctive Southern Buddhist architecture with exquisite details. The temple is located on An Binh Island, surrounded by the gentle Co Chien River, creating a peaceful and serene space. This is an ideal destination for those who want to learn about the spiritual culture and ancient architecture of the Southern region.',
        'Cù lao An Bình là một hòn đảo xanh tươi nằm giữa sông Cổ Chiên, được mệnh danh là "viên ngọc xanh" của Vĩnh Long. Nơi đây nổi tiếng với những vườn cây trái sum suê, đặc biệt là nhãn, chôm chôm, sầu riêng. Du khách có thể tham quan các vườn cây ăn trái, thưởng thức trái cây tươi ngon ngay tại vườn, trải nghiệm cuộc sống miền quê yên bình. Cù lao còn có nhiều homestay và nhà vườn để du khách nghỉ lại, tham gia các hoạt động như câu cá, chèo xuồng, tham quan làng nghề truyền thống.' => 'An Binh Island is a lush green island located in the middle of the Co Chien River, known as the "green gem" of Vinh Long. This place is famous for its abundant fruit gardens, especially longan, rambutan, and durian. Visitors can explore the fruit gardens, enjoy fresh fruit right at the garden, and experience peaceful countryside life. The island also has many homestays and garden houses for visitors to stay, participate in activities such as fishing, rowing, and visiting traditional craft villages.',
        'Khu du lịch sinh thái Tràm Chim là một trong những điểm đến sinh thái nổi bật của Vĩnh Long. Khu vực này có hệ sinh thái đa dạng với nhiều loài chim quý hiếm, đặc biệt là sếu đầu đỏ. Du khách có thể tham gia các hoạt động như đi thuyền tham quan, ngắm chim, câu cá, tham quan rừng tràm. Đây là nơi lý tưởng để tìm hiểu về thiên nhiên hoang dã và hệ sinh thái đặc trưng của vùng đồng bằng sông Cửu Long.' => 'Tram Chim Ecological Tourist Area is one of the outstanding ecological destinations in Vinh Long. This area has a diverse ecosystem with many rare bird species, especially the red-headed crane. Visitors can participate in activities such as boat tours, bird watching, fishing, and exploring the melaleuca forest. This is an ideal place to learn about the wild nature and characteristic ecosystem of the Mekong Delta region.',
        'Chợ nổi Cái Bè là một trong những chợ nổi lớn và sầm uất nhất khu vực đồng bằng sông Cửu Long. Chợ hoạt động từ sáng sớm, là nơi giao thương của người dân các tỉnh lân cận. Du khách có thể tham quan, mua sắm các sản vật địa phương như trái cây, rau củ, cá tôm tươi sống. Đặc biệt, chợ nổi còn có các ghe bán hàng ăn uống, phục vụ các món ăn đặc sản miền Tây ngay trên sông. Đây là trải nghiệm văn hóa độc đáo không thể bỏ qua khi đến Vĩnh Long.' => 'Cai Be Floating Market is one of the largest and busiest floating markets in the Mekong Delta region. The market operates from early morning, serving as a trading place for people from neighboring provinces. Visitors can explore and shop for local products such as fruits, vegetables, and fresh seafood. In particular, the floating market also has food boats serving Mekong Delta specialties right on the river. This is a unique cultural experience not to be missed when visiting Vinh Long.',
        'Khu du lịch sinh thái với hệ động thực vật đa dạng, nơi sinh sống của nhiều loài chim quý hiếm.' => 'An ecological tourist area with diverse flora and fauna, home to many rare bird species.',
        'Chợ nổi sầm uất trên sông, nơi giao thương và trải nghiệm văn hóa miền Tây độc đáo.' => 'A bustling floating market on the river, a place for trading and experiencing unique Mekong Delta culture.',
        'Vườn cây trái rộng lớn với nhiều loại đặc sản miền Tây, nơi thưởng thức trái cây tươi ngon.' => 'A vast fruit garden with various Mekong Delta specialties, where you can enjoy fresh, delicious fruits.',
        'Ngôi đình cổ kính từ thế kỷ 19, nơi thờ cúng và tìm hiểu về văn hóa, tín ngưỡng địa phương.' => 'An ancient communal house from the 19th century, a place of worship and learning about local culture and beliefs.',
        'Làng nghề truyền thống với các sản phẩm đan lát từ tre, nứa, nơi tìm hiểu và mua sắm đồ thủ công.' => 'A traditional craft village with bamboo and rattan weaving products, where you can learn and shop for handicrafts.',
        'Khu du lịch sinh thái với cảnh quan đẹp, nhiều hoạt động giải trí và thư giãn.' => 'An ecological tourist area with beautiful landscapes, various recreational activities and relaxation.',
        // Full description for Long Ho Weaving Village
        'Làng nghề đan lát Long Hồ là nơi lưu giữ và phát triển nghề đan lát truyền thống của vùng đất Nam Bộ. Du khách có thể tham quan các xưởng sản xuất, xem các nghệ nhân đan các sản phẩm từ tre, nứa như rổ, rá, giỏ, nón. Đây cũng là cơ hội để mua các sản phẩm thủ công mỹ nghệ làm quà lưu niệm. Làng nghề không chỉ là điểm tham quan mà còn là nơi góp phần bảo tồn và phát huy giá trị văn hóa truyền thống.' => 'Long Ho Weaving Village is a place that preserves and develops the traditional weaving craft of the Southern region. Visitors can tour production workshops, watch artisans weave products from bamboo and rattan such as baskets, trays, bags, and hats. This is also an opportunity to buy handicraft products as souvenirs. The craft village is not only a tourist destination but also a place that contributes to preserving and promoting traditional cultural values.',
      ]
    ];
    
    // Thử tìm exact match trước
    if(isset($translations[$lang][$key])) {
      return $translations[$lang][$key];
    }
    
    // Thử tìm với normalized key
    foreach($translations[$lang] as $vi_text => $en_text) {
      $normalized_vi = $normalize($vi_text);
      if($normalized_vi === $normalized_key) {
        return $en_text;
      }
    }
    
    // Thử partial match - nếu key chứa một phần của mapping
    // Hoặc ngược lại, nếu mapping chứa một phần của key
    foreach($translations[$lang] as $vi_text => $en_text) {
      $normalized_vi = $normalize($vi_text);
      
      // Nếu key chứa ít nhất 80% nội dung của mapping
      if(strlen($normalized_key) > 50 && strlen($normalized_vi) > 50) {
        $similarity = 0;
        similar_text($normalized_key, $normalized_vi, $similarity);
        if($similarity > 85) {
          return $en_text;
        }
      }
      
      // Kiểm tra nếu key chứa các từ khóa quan trọng của mapping
      // Đặc biệt cho "Cù lao An Bình"
      if(mb_strpos($normalized_key, 'cù lao an bình') !== false || 
         mb_strpos($normalized_key, 'hòn đảo xanh tươi') !== false ||
         (mb_strpos($normalized_key, 'sông cổ chiên') !== false && mb_strpos($normalized_key, 'vườn cây trái') !== false)) {
        // Kiểm tra xem mapping có phải là mô tả về "Cù lao An Bình" không
        if(mb_strpos($normalized_vi, 'cù lao an bình') !== false || 
           mb_strpos($normalized_vi, 'hòn đảo xanh tươi') !== false) {
          return $en_text;
        }
      }
    }
    
    // Fallback: Kiểm tra các từ khóa chính để dịch tự động
    if(strlen($normalized_key) > 50) {
      $key_lower = mb_strtolower($normalized_key, 'UTF-8');
      
      // Kiểm tra "Cù lao An Bình"
      if(mb_strpos($key_lower, 'cù lao an bình') !== false || 
         (mb_strpos($key_lower, 'hòn đảo xanh tươi') !== false && mb_strpos($key_lower, 'sông cổ chiên') !== false)) {
        return 'An Binh Island is a lush green island located in the middle of the Co Chien River, known as the "green gem" of Vinh Long. This place is famous for its abundant fruit gardens, especially longan, rambutan, and durian. Visitors can explore the fruit gardens, enjoy fresh fruit right at the garden, and experience peaceful countryside life. The island also has many homestays and garden houses for visitors to stay, participate in activities such as fishing, rowing, and visiting traditional craft villages.';
      }
      
      // Kiểm tra "Chùa Tiên Châu"
      if(mb_strpos($key_lower, 'chùa tiên châu') !== false) {
        return 'Tien Chau Temple is one of the most ancient and famous temples in Vinh Long. The temple was built in the 19th century, featuring distinctive Southern Buddhist architecture with exquisite details. The temple is located on An Binh Island, surrounded by the gentle Co Chien River, creating a peaceful and serene space. This is an ideal destination for those who want to learn about the spiritual culture and ancient architecture of the Southern region.';
      }
    }
    
    // Nếu không tìm thấy, trả về key gốc
    return $key;
  }
  
  // Lấy thông tin điểm du lịch
  $destination_query = "SELECT * FROM `destinations` WHERE `id` = ? AND `active` = 1";
  $destination_result = select($destination_query, [$destination_id], 'i');
  
  if(mysqli_num_rows($destination_result) == 0) {
    redirect('destinations.php');
  }
  
  $destination = mysqli_fetch_assoc($destination_result);
  $path = DESTINATIONS_IMG_PATH;
  
  // Lấy nhiều ảnh từ destination_images
  $images = [];
  $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'destination_images'");
  if($table_check && mysqli_num_rows($table_check) > 0){
    $images_query = "SELECT image FROM `destination_images` WHERE `destination_id` = ? ORDER BY `is_primary` DESC, `sort_order` ASC";
    $images_result = @select($images_query, [$destination_id], 'i');
    if($images_result && mysqli_num_rows($images_result) > 0){
      while($img_row = mysqli_fetch_assoc($images_result)){
        $img_name = $img_row['image'];
        // Loại bỏ đường dẫn nếu có trong tên ảnh (tránh lặp path)
        if(strpos($img_name, 'destinations/') !== false){
          $img_name = basename($img_name);
        } else if(strpos($img_name, '/') !== false && strpos($img_name, 'http') === false){
          $img_name = basename($img_name);
        }
        $images[] = $path . $img_name;
      }
    }
  }
  
  // Fallback to old image field if no images in destination_images
  if(empty($images)){
    $old_image = $destination['image'] ? $destination['image'] : 'default.jpg';
    // Loại bỏ đường dẫn nếu có trong tên ảnh
    if(strpos($old_image, 'destinations/') !== false){
      $old_image = basename($old_image);
    } else if(strpos($old_image, '/') !== false && strpos($old_image, 'http') === false){
      $old_image = basename($old_image);
    }
    $images[] = $path . $old_image;
  }
  
  // SEO Meta Tags - Sử dụng tên đã dịch
  $dest_name = htmlspecialchars(t_destinations($destination['name'], $current_lang), ENT_QUOTES, 'UTF-8');
  $dest_desc_text = $destination['short_description'] ?? substr($destination['description'], 0, 160);
  $dest_desc = htmlspecialchars(t_destinations($dest_desc_text, $current_lang), ENT_QUOTES, 'UTF-8');
  $dest_image = !empty($images[0]) ? htmlspecialchars($images[0], ENT_QUOTES, 'UTF-8') : '';
  $page_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  
  <title><?php echo t_destinations($dest_name, $current_lang); ?> - <?php echo $settings_r['site_title']; ?></title>
  <meta name="description" content="<?php echo $dest_desc; ?>">
  <meta name="keywords" content="<?php echo $dest_name; ?>, điểm du lịch Vĩnh Long, du lịch Vĩnh Long">
  
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?php echo $page_url; ?>">
  <meta property="og:title" content="<?php echo $dest_name; ?> - <?php echo $settings_r['site_title']; ?>">
  <meta property="og:description" content="<?php echo $dest_desc; ?>">
  <?php if($dest_image): ?>
  <meta property="og:image" content="<?php echo $dest_image; ?>">
  <?php endif; ?>
  
  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="<?php echo $page_url; ?>">
  <meta property="twitter:title" content="<?php echo $dest_name; ?>">
  <meta property="twitter:description" content="<?php echo $dest_desc; ?>">
  <?php if($dest_image): ?>
  <meta property="twitter:image" content="<?php echo $dest_image; ?>">
  <?php endif; ?>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    
    * {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    
    body { 
      background: #ffffff;
      min-height: 100vh;
      position: relative;
    }
    
    .detail-hero {
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(30px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 32px;
      padding: 2.5rem;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(255, 255, 255, 0.5);
      margin-bottom: 2.5rem;
      animation: fadeInUp 0.6s ease-out;
      position: relative;
      overflow: hidden;
    }
    
    .detail-hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
      pointer-events: none;
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
    
    .destination-image {
      width: 100%;
      height: 450px;
      object-fit: cover;
      border-radius: 24px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.1);
      cursor: pointer;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
    }
    
    .destination-image:hover {
      transform: scale(1.02) translateY(-5px);
      box-shadow: 0 30px 80px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.2);
    }
    
    /* Gallery Layout - Main Image on Top, Grid Below */
    .gallery-container {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    
    .gallery-main-large {
      position: relative;
      width: 100%;
      height: 500px;
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
      background: #000;
      cursor: pointer;
    }
    
    .gallery-main-large::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.1) 100%);
      pointer-events: none;
    }
    
    .gallery-main-large img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .gallery-main-large:hover img {
      transform: scale(1.05);
    }
    
    .gallery-view-all {
      position: absolute;
      bottom: 1.5rem;
      right: 1.5rem;
      background: rgba(0, 0, 0, 0.8);
      backdrop-filter: blur(10px);
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 16px;
      font-size: 0.95rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      z-index: 10;
      transition: all 0.3s ease;
      border: 1px solid rgba(255, 255, 255, 0.2);
      cursor: pointer;
    }
    
    .gallery-view-all:hover {
      background: rgba(0, 0, 0, 0.95);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
    }
    
    .gallery-view-all i {
      font-size: 1.1rem;
    }
    
    /* Grid Gallery Below */
    .gallery-grid-below {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
      gap: 1rem;
      margin-top: 1rem;
    }
    
    .gallery-grid-item {
      position: relative;
      aspect-ratio: 4/3;
      border-radius: 16px;
      overflow: hidden;
      cursor: pointer;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      border: 3px solid transparent;
      background: #000;
    }
    
    .gallery-grid-item::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.3) 100%);
      opacity: 0;
      transition: opacity 0.3s ease;
      z-index: 1;
    }
    
    .gallery-grid-item:hover {
      transform: translateY(-5px) scale(1.03);
      box-shadow: 0 12px 32px rgba(102, 126, 234, 0.3);
      border-color: #667eea;
    }
    
    .gallery-grid-item:hover::before {
      opacity: 1;
    }
    
    .gallery-grid-item.active {
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3), 0 12px 32px rgba(102, 126, 234, 0.3);
    }
    
    .gallery-grid-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .gallery-grid-item:hover img {
      transform: scale(1.1);
    }
    
    .gallery-grid-item-number {
      position: absolute;
      top: 0.75rem;
      left: 0.75rem;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(10px);
      color: white;
      padding: 0.4rem 0.7rem;
      border-radius: 12px;
      font-weight: 700;
      font-size: 0.85rem;
      z-index: 2;
    }
    
    @media (max-width: 768px) {
      .gallery-main-large {
        height: 350px;
      }
      
      .gallery-grid-below {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 0.75rem;
      }
    }
    
    /* Gallery Modal - Traveloka Style - Fullscreen */
    .image-gallery-modal {
      z-index: 9999 !important;
    }
    
    .image-gallery-modal .modal-dialog {
      max-width: 100%;
      width: 100%;
      height: 100vh;
      margin: 0;
      padding: 0;
    }
    
    .image-gallery-modal .modal-content {
      background: #2d2d2d !important;
      border: none;
      height: 100vh;
      display: flex;
      flex-direction: column;
      border-radius: 0;
    }
    
    /* Header - Traveloka Style */
    .gallery-modal-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1rem 2rem;
      background: #2d2d2d;
      border-bottom: 1px solid #404040;
      flex-shrink: 0;
    }
    
    .gallery-header-left {
      flex: 1;
    }
    
    .gallery-hotel-name {
      color: #ffffff;
      font-size: 1.25rem;
      font-weight: 600;
      margin: 0;
    }
    
    .gallery-header-center {
      flex: 1;
      text-align: center;
    }
    
    .gallery-header-title {
      color: #ffffff;
      font-size: 1rem;
      font-weight: 500;
      padding-bottom: 0.5rem;
      border-bottom: 2px solid transparent;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .gallery-header-title.active {
      border-bottom-color: #667eea;
      font-weight: 600;
    }
    
    .gallery-header-right {
      flex: 1;
      display: flex;
      justify-content: flex-end;
    }
    
    .gallery-close-btn {
      background: transparent;
      border: none;
      color: #ffffff;
      font-size: 1.5rem;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .gallery-close-btn:hover {
      background: rgba(255, 255, 255, 0.1);
    }
    
    /* Main Body */
    .gallery-modal-body {
      flex: 1;
      background: #1a1a1a;
      padding: 2rem;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
      min-height: 0;
      height: calc(100vh - 200px);
    }
    
    .gallery-modal-main {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #000;
      position: relative;
      padding: 1rem;
    }
    
    .gallery-modal-main-img {
      max-width: 95%;
      max-height: 90vh;
      width: auto;
      height: auto;
      object-fit: contain;
      transition: opacity 0.3s ease, transform 0.3s ease;
      cursor: zoom-in;
    }
    
    .gallery-modal-main-img.zoomed {
      cursor: zoom-out;
      transform: scale(2);
      max-width: 100%;
      max-height: 100%;
      transition: transform 0.3s ease;
    }
    
    .gallery-nav-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      width: 48px;
      height: 48px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
      z-index: 10;
      font-size: 1.5rem;
    }
    
    .gallery-nav-btn:hover {
      background: rgba(0, 0, 0, 0.7);
      border-color: rgba(255, 255, 255, 0.4);
    }
    
    .gallery-nav-prev {
      left: 1.5rem;
    }
    
    .gallery-nav-next {
      right: 1.5rem;
    }
    
    .gallery-image-info {
      position: absolute;
      bottom: 2rem;
      left: 50%;
      transform: translateX(-50%);
      text-align: center;
      z-index: 10;
    }
    
    .gallery-caption {
      color: #ffffff;
      font-size: 1rem;
      margin-bottom: 0.5rem;
      font-weight: 500;
    }
    
    .gallery-counter {
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(10px);
      color: white;
      padding: 0.4rem 1rem;
      border-radius: 20px;
      font-weight: 600;
      font-size: 0.9rem;
      display: inline-block;
    }
    
    /* Filter Bar */
    .gallery-filter-bar {
      background: #2d2d2d;
      border-top: 1px solid #404040;
      border-bottom: 1px solid #404040;
      padding: 0.75rem 2rem;
      flex-shrink: 0;
      overflow-x: auto;
    }
    
    .gallery-filter-scroll {
      display: flex;
      gap: 1rem;
      white-space: nowrap;
    }
    
    .gallery-filter-btn {
      background: transparent;
      border: 1px solid #404040;
      color: #ffffff;
      padding: 0.5rem 1.25rem;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
      white-space: nowrap;
    }
    
    .gallery-filter-btn:hover {
      border-color: #667eea;
      color: #667eea;
    }
    
    .gallery-filter-btn.active {
      background: #667eea;
      border-color: #667eea;
      color: #ffffff;
    }
    
    /* Thumbnails Container */
    .gallery-thumbnails-container {
      background: #2d2d2d;
      padding: 1rem 2rem;
      flex-shrink: 0;
      max-height: 200px;
      overflow-y: auto;
    }
    
    .gallery-thumbnails-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
      gap: 0.75rem;
    }
    
    .gallery-thumbnail-item {
      position: relative;
      aspect-ratio: 4/3;
      border-radius: 8px;
      overflow: hidden;
      cursor: pointer;
      border: 3px solid transparent;
      transition: all 0.3s ease;
      opacity: 0.8;
    }
    
    .gallery-thumbnail-item:hover {
      opacity: 1;
      transform: scale(1.05);
    }
    
    .gallery-thumbnail-item.active {
      opacity: 1;
      border-color: #667eea;
      box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.3);
    }
    
    .gallery-thumbnail-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    @media (max-width: 768px) {
      .gallery-modal-header {
        padding: 1rem;
        flex-direction: column;
        gap: 0.5rem;
      }
      
      .gallery-header-left,
      .gallery-header-center,
      .gallery-header-right {
        flex: none;
        width: 100%;
        text-align: center;
      }
      
      .gallery-nav-btn {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
      }
      
      .gallery-nav-prev {
        left: 0.5rem;
      }
      
      .gallery-nav-next {
        right: 0.5rem;
      }
      
      .gallery-filter-bar {
        padding: 0.75rem 1rem;
      }
      
      .gallery-thumbnails-container {
        padding: 1rem;
      }
      
      .gallery-thumbnails-grid {
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 0.5rem;
      }
    }
    
    .image-gallery-section {
      margin-top: 2rem;
      padding: 2rem;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(30px);
      border-radius: 24px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .image-gallery-section h6 {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1a202c;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    
    .image-gallery-section h6 i {
      color: #667eea;
      font-size: 1.75rem;
    }
    
    .image-gallery-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 1.25rem;
      margin-top: 1rem;
    }
    
    .image-gallery-item {
      position: relative;
      border-radius: 16px;
      overflow: hidden;
      cursor: pointer;
      aspect-ratio: 4/3;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      border: 2px solid transparent;
      background: #000;
    }
    
    .image-gallery-item::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.2) 100%);
      opacity: 0;
      transition: opacity 0.3s ease;
      z-index: 1;
    }
    
    .image-gallery-item:hover {
      transform: translateY(-8px) scale(1.03);
      box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
      border-color: #667eea;
    }
    
    .image-gallery-item:hover::before {
      opacity: 1;
    }
    
    .image-gallery-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .image-gallery-item:hover img {
      transform: scale(1.1);
    }
    
    .image-gallery-item-number {
      position: absolute;
      bottom: 0.75rem;
      left: 0.75rem;
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      color: #1a202c;
      padding: 0.5rem 0.75rem;
      border-radius: 12px;
      font-weight: 700;
      font-size: 0.85rem;
      z-index: 2;
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    
    .image-gallery-item:hover .image-gallery-item-number {
      opacity: 1;
    }
    
    @media (max-width: 768px) {
      .image-gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 1rem;
      }
      
      .image-gallery-section {
        padding: 1.5rem;
      }
    }
    
    .destination-info-card {
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(30px);
      border-radius: 24px;
      padding: 2.5rem;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.5);
      margin-bottom: 2rem;
      animation: fadeInUp 0.6s ease-out;
      animation-fill-mode: both;
      position: relative;
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .destination-info-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 30px 80px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.6);
    }
    
    .destination-info-card:nth-child(1) { animation-delay: 0.1s; }
    .destination-info-card:nth-child(2) { animation-delay: 0.2s; }
    .destination-info-card:nth-child(3) { animation-delay: 0.3s; }
    
    .destination-info-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    }
    
    .destination-info-card::after {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 200px;
      height: 200px;
      background: radial-gradient(circle, rgba(102, 126, 234, 0.08) 0%, transparent 70%);
      pointer-events: none;
    }
    
    .destination-info-card h5 {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1a202c;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      letter-spacing: -0.02em;
    }
    
    .destination-info-card h5 i {
      color: #667eea;
      font-size: 1.75rem;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .destination-description {
      line-height: 2;
      color: #4a5568;
      font-size: 1.05rem;
      text-align: justify;
    }
    
    .destination-description p {
      margin-bottom: 1rem;
    }
    
    .info-item {
      display: flex;
      align-items: flex-start;
      gap: 1rem;
      padding: 1rem 1.25rem;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
      border-radius: 16px;
      margin-bottom: 1rem;
      border: 1px solid rgba(102, 126, 234, 0.1);
      transition: all 0.3s ease;
    }
    
    .info-item:hover {
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
      transform: translateX(5px);
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }
    
    .info-item i {
      color: #667eea;
      font-size: 1.25rem;
      margin-top: 2px;
      min-width: 24px;
    }
    
    .info-item .label {
      font-weight: 600;
      color: #2d3748;
      min-width: 120px;
      font-size: 0.95rem;
    }
    
    .info-item .value {
      color: #4a5568;
      flex: 1;
      font-size: 0.95rem;
      line-height: 1.6;
    }
    
    .map-container {
      width: 100%;
      height: 450px;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.2);
      position: relative;
    }
    
    .map-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
      pointer-events: none;
      z-index: 1;
      border-radius: 20px;
    }
    
    .map-container iframe {
      position: relative;
      z-index: 2;
    }
    
    .rooms-nearby {
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(30px);
      border-radius: 24px;
      padding: 2.5rem;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.5);
      animation: fadeInUp 0.6s ease-out;
      animation-delay: 0.4s;
      animation-fill-mode: both;
      position: relative;
      overflow: hidden;
      margin-top: 2rem;
    }
    
    .rooms-nearby::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    }
    
    .rooms-nearby::after {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 200px;
      height: 200px;
      background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
      pointer-events: none;
    }
    
    .rooms-nearby h5 {
      font-size: 1.75rem;
      font-weight: 700;
      color: #1a202c;
      margin-bottom: 2rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      letter-spacing: -0.02em;
    }
    
    .rooms-nearby h5 i {
      color: #667eea;
      font-size: 2rem;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .room-card-mini {
      border: 1px solid rgba(102, 126, 234, 0.1);
      border-radius: 20px;
      overflow: hidden;
      background: #ffffff;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      height: 100%;
      display: flex;
      flex-direction: column;
      position: relative;
    }
    
    .room-card-mini::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.03) 0%, rgba(118, 75, 162, 0.03) 100%);
      opacity: 0;
      transition: opacity 0.3s ease;
      pointer-events: none;
    }
    
    .room-card-mini:hover {
      border-color: #667eea;
      box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2);
      transform: translateY(-8px) scale(1.02);
    }
    
    .room-card-mini:hover::before {
      opacity: 1;
    }
    
    .room-card-mini-image {
      width: 100%;
      height: 200px;
      object-fit: cover;
      transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
    }
    
    .room-card-mini:hover .room-card-mini-image {
      transform: scale(1.1);
    }
    
    .room-card-mini-body {
      padding: 1.5rem;
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    
    .room-card-mini h6 {
      font-size: 1.1rem;
      font-weight: 700;
      color: #1a202c;
      margin-bottom: 1rem;
      line-height: 1.4;
      letter-spacing: -0.01em;
    }
    
    .room-card-mini .distance {
      color: #718096;
      font-size: 0.9rem;
      margin-bottom: 0.75rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .room-card-mini .distance i {
      color: #667eea;
    }
    
    .room-card-mini .price {
      color: #059669;
      font-weight: 800;
      font-size: 1.25rem;
      margin-bottom: 1rem;
      background: linear-gradient(135deg, #059669 0%, #10b981 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .room-card-mini .btn {
      margin-top: auto;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      border-radius: 12px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .room-card-mini .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }
    
    .stars {
      color: #fbbf24;
      filter: drop-shadow(0 2px 4px rgba(251, 191, 36, 0.3));
    }
    
    /* Modern Badge */
    .badge {
      padding: 0.5rem 1rem;
      border-radius: 12px;
      font-weight: 600;
      font-size: 0.875rem;
      letter-spacing: 0.02em;
    }
    
    .badge.bg-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    /* Hero Title */
    .detail-hero h2 {
      font-size: 2.5rem;
      font-weight: 800;
      color: #1a202c;
      letter-spacing: -0.03em;
      line-height: 1.2;
      margin-bottom: 1.5rem;
      background: linear-gradient(135deg, #1a202c 0%, #667eea 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    /* Breadcrumb */
    .breadcrumb {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 16px;
      padding: 1rem 1.5rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.3);
      margin-bottom: 2rem;
    }
    
    .breadcrumb-item a {
      color: #667eea;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    
    .breadcrumb-item a:hover {
      color: #764ba2;
      text-decoration: underline;
    }
    
    .breadcrumb-item.active {
      color: #4a5568;
      font-weight: 600;
    }
    
    /* Back Button */
    .btn-outline-primary {
      border: 2px solid #667eea;
      color: #667eea;
      border-radius: 16px;
      padding: 0.75rem 2rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .btn-outline-primary:hover {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-color: transparent;
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .detail-hero {
        padding: 1.5rem;
        border-radius: 24px;
      }
      
      .detail-hero h2 {
        font-size: 1.75rem;
      }
      
      .gallery-grid {
        grid-template-columns: 1fr;
        height: auto;
      }
      
      .gallery-thumbs {
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: auto;
      }
      
      .destination-image {
        height: 300px;
      }
    }
  </style>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <?php
    // Set main image (first image)
    $image = $images[0];
    
    // Category labels với translation
    $category_labels_vi = [
      'temple' => 'Chùa, Đình',
      'nature' => 'Thiên nhiên',
      'market' => 'Chợ nổi',
      'culture' => 'Văn hóa',
      'other' => 'Khác'
    ];
    $category_labels_en = [
      'temple' => 'Temple, Communal House',
      'nature' => 'Nature',
      'market' => 'Floating Market',
      'culture' => 'Culture',
      'other' => 'Other'
    ];
    $category_label = $current_lang === 'en' 
      ? ($category_labels_en[$destination['category']] ?? 'Other')
      : ($category_labels_vi[$destination['category']] ?? 'Khác');
    
    // Generate stars
    $rating = (float)$destination['rating'];
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
    
    // Lấy danh sách phòng gần đó
    $rooms_nearby_query = "SELECT r.*, rd.distance 
                          FROM `rooms` r
                          INNER JOIN `room_destinations` rd ON r.id = rd.room_id
                          WHERE rd.destination_id = ? 
                          AND r.status = 1 
                          AND r.removed = 0
                          ORDER BY rd.distance ASC
                          LIMIT 6";
    $rooms_nearby_result = select($rooms_nearby_query, [$destination_id], 'i');
    
    // Lưu vào array để có thể lặp lại
    $rooms_nearby = [];
    if($rooms_nearby_result && mysqli_num_rows($rooms_nearby_result) > 0){
      while($room_row = mysqli_fetch_assoc($rooms_nearby_result)){
        $rooms_nearby[] = $room_row;
      }
    }
  ?>

  <div class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php"><span data-i18n="destinationDetails.home" data-i18n-skip><?php echo t_destination_details('destinationDetails.home', $current_lang); ?></span></a></li>
        <li class="breadcrumb-item"><a href="destinations.php"><span data-i18n="destinationDetails.destinations" data-i18n-skip><?php echo t_destination_details('destinationDetails.destinations', $current_lang); ?></span></a></li>
        <li class="breadcrumb-item active"><?php echo htmlspecialchars(t_destinations($destination['name'], $current_lang)); ?></li>
      </ol>
    </nav>

    <!-- Hero Section -->
    <div class="detail-hero">
      <div class="row g-4">
        <div class="col-lg-8">
          <?php if(count($images) > 1): ?>
            <!-- Hiển thị ảnh lớn và grid ảnh bên dưới -->
            <div class="gallery-container">
              <div class="gallery-main-large">
                <img id="main-gallery-image" src="<?php echo htmlspecialchars($images[0]); ?>" 
                     alt="<?php echo htmlspecialchars(t_destinations($destination['name'], $current_lang)); ?>" 
                     loading="eager"
                     onclick="openImageModal(0)"
                     style="cursor: pointer;"
                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'500\'%3E%3Crect fill=\'%23f3f4f6\' width=\'800\' height=\'500\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-family=\'Arial\' font-size=\'24\'%3ENo Image%3C/text%3E%3C/svg%3E';">
                <div class="gallery-view-all" onclick="openImageModal(0); event.stopPropagation();">
                  <i class="bi bi-grid-3x3-gap"></i>
                  <span data-i18n="destinationDetails.viewAllImages" data-i18n-skip><?php echo t_destination_details('destinationDetails.viewAllImages', $current_lang); ?></span> (<?php echo count($images); ?>)
                </div>
              </div>
              
              <!-- Grid ảnh bên dưới - hiển thị tối đa 5 ảnh đầu tiên -->
              <?php if(count($images) > 1): ?>
                <div class="gallery-grid-below">
                  <?php 
                    $display_count = min(5, count($images)); // Hiển thị tối đa 5 ảnh
                    for($i = 1; $i < $display_count; $i++): 
                      $img_src = htmlspecialchars($images[$i], ENT_QUOTES, 'UTF-8');
                  ?>
                    <div class="gallery-grid-item <?php echo $i == 1 ? 'active' : ''; ?>" 
                         onclick="changeMainImage(<?php echo $i; ?>); openImageModal(<?php echo $i; ?>);"
                         data-index="<?php echo $i; ?>">
                      <img src="<?php echo $img_src; ?>" 
                           alt="<?php echo htmlspecialchars(t_destinations($destination['name'], $current_lang)); ?> - <?php echo $i + 1; ?>"
                           loading="lazy"
                           onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'150\' height=\'113\'%3E%3Crect fill=\'%23f3f4f6\' width=\'150\' height=\'113\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-family=\'Arial\' font-size=\'12\'%3ENo Image%3C/text%3E%3C/svg%3E';">
                      <div class="gallery-grid-item-number"><?php echo $i + 1; ?></div>
                    </div>
                  <?php endfor; ?>
                  
                  <?php if(count($images) > 5): ?>
                    <!-- Nút xem thêm nếu có nhiều hơn 5 ảnh -->
                    <div class="gallery-grid-item" 
                         onclick="openImageModal(0);"
                         style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%); display: flex; align-items: center; justify-content: center; cursor: pointer;">
                      <div class="text-center text-white">
                        <i class="bi bi-images" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                        <div class="fw-bold">+<?php echo count($images) - 5; ?></div>
                        <div class="small"><?php echo t_destination_details('destinationDetails.viewAllImages', $current_lang); ?></div>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <!-- Chỉ có 1 ảnh -->
            <img src="<?php echo htmlspecialchars($image); ?>" 
                 alt="<?php echo htmlspecialchars(t_destinations($destination['name'], $current_lang)); ?>" 
                 class="destination-image" 
                 data-bs-toggle="modal" 
                 data-bs-target="#imageModal"
                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'500\'%3E%3Crect fill=\'%23f3f4f6\' width=\'800\' height=\'500\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-family=\'Arial\' font-size=\'24\'%3ENo Image%3C/text%3E%3C/svg%3E';">
          <?php endif; ?>
        </div>
        <div class="col-lg-4">
          <div class="position-sticky" style="top: 2rem;">
            <h2 class="mb-4"><?php echo htmlspecialchars(t_destinations($destination['name'], $current_lang)); ?></h2>
            
            <div class="mb-4">
              <span class="badge bg-primary mb-3 d-inline-block px-3 py-2" style="font-size: 0.95rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                <i class="bi bi-tag-fill me-1"></i><?php echo $category_label; ?>
              </span>
              <div class="stars mb-3 d-flex align-items-center gap-2">
                <?php echo $stars; ?>
                <span class="ms-2 fw-bold text-dark fs-5"><?php echo number_format($rating, 1); ?></span>
                <span class="text-muted">(<?php echo $destination['review_count']; ?> <span data-i18n="destinationDetails.reviews" data-i18n-skip><?php echo t_destination_details('destinationDetails.reviews', $current_lang); ?></span>)</span>
              </div>
            </div>
            
            <div class="info-item mb-3">
              <i class="bi bi-geo-alt-fill"></i>
              <span class="label" data-i18n="destinationDetails.address" data-i18n-skip><?php echo t_destination_details('destinationDetails.address', $current_lang); ?>:</span>
              <span class="value"><?php echo htmlspecialchars($destination['location']); ?></span>
            </div>
            
            <?php if($destination['latitude'] && $destination['longitude']): ?>
            <div class="info-item mb-3">
              <i class="bi bi-map"></i>
              <span class="label" data-i18n="destinationDetails.coordinates" data-i18n-skip><?php echo t_destination_details('destinationDetails.coordinates', $current_lang); ?>:</span>
              <span class="value"><?php echo $destination['latitude']; ?>, <?php echo $destination['longitude']; ?></span>
            </div>
            <?php endif; ?>
            
            <div class="mt-4 pt-4 border-top">
              <a href="#rooms-nearby" class="btn btn-primary w-100 rounded-pill py-3 fw-bold mb-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3); transition: all 0.3s ease;">
                <i class="bi bi-house-door me-2"></i><span data-i18n="destinationDetails.viewNearbyRooms" data-i18n-skip><?php echo t_destination_details('destinationDetails.viewNearbyRooms', $current_lang); ?></span>
              </a>
              
              <!-- Share & Actions -->
              <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary flex-fill rounded-pill" onclick="shareDestination()" data-i18n-title="destinationDetails.share" title="<?php echo t_destination_details('destinationDetails.share', $current_lang); ?>">
                  <i class="bi bi-share-fill me-1"></i><span data-i18n="destinationDetails.share" data-i18n-skip><?php echo t_destination_details('destinationDetails.share', $current_lang); ?></span>
                </button>
                <button class="btn btn-outline-secondary flex-fill rounded-pill" onclick="shareToFacebook()" data-i18n-title="destinationDetails.facebook" title="<?php echo t_destination_details('destinationDetails.facebook', $current_lang); ?>">
                  <i class="bi bi-facebook me-1"></i><span data-i18n="destinationDetails.facebook"><?php echo t_destination_details('destinationDetails.facebook', $current_lang); ?></span>
                </button>
                <button class="btn btn-outline-secondary flex-fill rounded-pill" onclick="printPage()" data-i18n-title="destinationDetails.print" title="<?php echo t_destination_details('destinationDetails.print', $current_lang); ?>">
                  <i class="bi bi-printer me-1"></i><span data-i18n="destinationDetails.print" data-i18n-skip><?php echo t_destination_details('destinationDetails.print', $current_lang); ?></span>
                </button>
              </div>
              
              <a href="destinations.php" class="btn btn-outline-primary w-100 rounded-pill py-2 fw-semibold">
                <i class="bi bi-arrow-left me-2"></i><span data-i18n="destinationDetails.backToList" data-i18n-skip><?php echo t_destination_details('destinationDetails.backToList', $current_lang); ?></span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Description -->
    <div class="destination-info-card">
      <h5><i class="bi bi-info-circle"></i> <span data-i18n="destinationDetails.introduction" data-i18n-skip><?php echo t_destination_details('destinationDetails.introduction', $current_lang); ?></span></h5>
      <div class="destination-description">
        <?php 
          // Lấy mô tả và dịch
          $description_text = $destination['description'];
          
          // Debug: Log để kiểm tra (chỉ khi lang = en)
          if($current_lang === 'en') {
            // Kiểm tra xem có match không
            $test_translation = t_destinations($description_text, 'en');
            // Nếu không dịch được (trả về gốc), thử tìm bằng cách khác
            if($test_translation === $description_text) {
              // Thử tìm bằng cách kiểm tra xem có chứa key phrases không
              $description_lower = mb_strtolower($description_text, 'UTF-8');
              
              // Kiểm tra các key phrases
              if(mb_strpos($description_lower, 'cù lao an bình') !== false || 
                 mb_strpos($description_lower, 'hòn đảo xanh tươi') !== false ||
                 mb_strpos($description_lower, 'sông cổ chiên') !== false) {
                // Nếu chứa key phrases của "Cù lao An Bình", sử dụng translation đã biết
                $test_translation = 'An Binh Island is a lush green island located in the middle of the Co Chien River, known as the "green gem" of Vinh Long. This place is famous for its abundant fruit gardens, especially longan, rambutan, and durian. Visitors can explore the fruit gardens, enjoy fresh fruit right at the garden, and experience peaceful countryside life. The island also has many homestays and garden houses for visitors to stay, participate in activities such as fishing, rowing, and visiting traditional craft villages.';
              }
            }
            $translated_description = $test_translation;
          } else {
            $translated_description = $description_text;
          }
          
          // Xử lý HTML entities và line breaks
          $translated_description = html_entity_decode($translated_description, ENT_QUOTES | ENT_HTML5, 'UTF-8');
          // Decode lại một lần nữa để xử lý nested entities
          $translated_description = html_entity_decode($translated_description, ENT_QUOTES | ENT_HTML5, 'UTF-8');
          echo nl2br(htmlspecialchars($translated_description, ENT_QUOTES, 'UTF-8')); 
        ?>
      </div>
      <?php if(!empty($destination['short_description'])): ?>
        <div class="destination-short-desc mt-3 p-3 rounded" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%); border-left: 4px solid #667eea;">
          <strong class="d-block mb-2 text-primary"><i class="bi bi-quote me-2"></i><span data-i18n="destinationDetails.summary" data-i18n-skip><?php echo t_destination_details('destinationDetails.summary', $current_lang); ?></span>:</strong>
          <p class="mb-0" style="line-height: 1.8; color: #4a5568;">
            <?php 
              $short_desc_text = $destination['short_description'];
              $translated_short = t_destinations($short_desc_text, $current_lang);
              $translated_short = html_entity_decode($translated_short, ENT_QUOTES, 'UTF-8');
              echo htmlspecialchars($translated_short, ENT_QUOTES, 'UTF-8'); 
            ?>
          </p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Map -->
    <?php if($destination['latitude'] && $destination['longitude']): 
      // Tạo URL bản đồ với tọa độ
      $map_query = $destination['latitude'] . ',' . $destination['longitude'];
      if(!empty($destination['location'])){
        // Sử dụng tên đã dịch cho map query
        $map_query = urlencode(t_destinations($destination['name'], $current_lang) . ', ' . $destination['location']);
      }
      $map_embed_url = "https://www.google.com/maps?q=" . $map_query . "&output=embed";
      $map_full_url = "https://www.google.com/maps?q=" . $map_query;
    ?>
    <div class="destination-info-card">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="mb-0"><i class="bi bi-geo-alt"></i> <span data-i18n="destinationDetails.map" data-i18n-skip><?php echo t_destination_details('destinationDetails.map', $current_lang); ?></span></h5>
        <a href="<?php echo htmlspecialchars($map_full_url); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
          <i class="bi bi-box-arrow-up-right me-1"></i><span data-i18n="destinationDetails.viewLargeMap" data-i18n-skip><?php echo t_destination_details('destinationDetails.viewLargeMap', $current_lang); ?></span>
        </a>
      </div>
      <div class="map-container">
        <iframe 
          width="100%" 
          height="100%" 
          style="border:0" 
          loading="lazy" 
          allowfullscreen
          src="<?php echo htmlspecialchars($map_embed_url); ?>">
        </iframe>
      </div>
    </div>
    <?php elseif(!empty($destination['location'])): 
      // Fallback: dùng địa chỉ nếu không có tọa độ
      // Sử dụng tên đã dịch cho map query
      $map_query = urlencode(t_destinations($destination['name'], $current_lang) . ', ' . $destination['location']);
      $map_embed_url = "https://www.google.com/maps?q=" . $map_query . "&output=embed";
      $map_full_url = "https://www.google.com/maps?q=" . $map_query;
    ?>
    <div class="destination-info-card">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="mb-0"><i class="bi bi-geo-alt"></i> <span data-i18n="destinationDetails.map" data-i18n-skip><?php echo t_destination_details('destinationDetails.map', $current_lang); ?></span></h5>
        <a href="<?php echo htmlspecialchars($map_full_url); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
          <i class="bi bi-box-arrow-up-right me-1"></i><span data-i18n="destinationDetails.viewLargeMap" data-i18n-skip><?php echo t_destination_details('destinationDetails.viewLargeMap', $current_lang); ?></span>
        </a>
      </div>
      <div class="map-container">
        <iframe 
          width="100%" 
          height="100%" 
          style="border:0" 
          loading="lazy" 
          allowfullscreen
          src="<?php echo htmlspecialchars($map_embed_url); ?>">
        </iframe>
      </div>
    </div>
    <?php endif; ?>

    <!-- Rooms Nearby -->
    <div class="rooms-nearby" id="rooms-nearby">
      <h5 class="mb-4"><i class="bi bi-house-door"></i> <span data-i18n="destinationDetails.nearbyRooms" data-i18n-skip><?php echo t_destination_details('destinationDetails.nearbyRooms', $current_lang); ?></span></h5>
      
      <?php if(!empty($rooms_nearby)): ?>
        <div class="row g-3">
          <?php 
            foreach($rooms_nearby as $room): 
              $room_price = (int)$room['price'];
              $room_price_format = number_format($room_price, 0, ',', '.');
              $distance = number_format((float)$room['distance'], 1);
              
              // Lấy ảnh phòng
              $room_thumb = ROOMS_IMG_PATH . "thumbnail.jpg";
              $thumb_query = "SELECT image FROM `room_images` WHERE `room_id` = ? AND `thumb` = 1 LIMIT 1";
              $thumb_result = select($thumb_query, [$room['id']], 'i');
              if($thumb_result && mysqli_num_rows($thumb_result) > 0){
                $thumb_data = mysqli_fetch_assoc($thumb_result);
                $room_thumb = ROOMS_IMG_PATH . $thumb_data['image'];
              }
              
              // Lấy đánh giá
              $rating_query = "SELECT ROUND(AVG(rating)) as avg_rating, COUNT(*) as review_count 
                              FROM `rating_review` 
                              WHERE `room_id` = ?";
              $rating_result = select($rating_query, [$room['id']], 'i');
              $avg_rating = 0;
              $review_count = 0;
              if($rating_result && mysqli_num_rows($rating_result) > 0){
                $rating_data = mysqli_fetch_assoc($rating_result);
                $avg_rating = (int)$rating_data['avg_rating'];
                $review_count = (int)$rating_data['review_count'];
              }
              
              // Generate stars
              $stars_html = '';
              if($avg_rating > 0){
                for($i = 1; $i <= 5; $i++){
                  if($i <= $avg_rating){
                    $stars_html .= '<i class="bi bi-star-fill text-warning"></i>';
                  } else {
                    $stars_html .= '<i class="bi bi-star text-muted"></i>';
                  }
                }
              }
          ?>
            <div class="col-md-6 col-lg-4">
              <div class="room-card-mini">
                <img src="<?php echo htmlspecialchars($room_thumb); ?>" 
                     alt="<?php echo htmlspecialchars(t_room_name($room['name'], $current_lang)); ?>" 
                     class="room-card-mini-image"
                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'200\'%3E%3Crect fill=\'%23f3f4f6\' width=\'400\' height=\'200\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-family=\'Arial\' font-size=\'16\'%3ENo Image%3C/text%3E%3C/svg%3E';">
                <div class="room-card-mini-body">
                  <h6><?php echo htmlspecialchars(t_room_name($room['name'], $current_lang)); ?></h6>
                  
                  <?php if($avg_rating > 0): ?>
                    <div class="mb-2 d-flex align-items-center gap-1" style="font-size: 0.85rem;">
                      <?php echo $stars_html; ?>
                      <span class="text-muted ms-1"><?php echo $avg_rating; ?></span>
                      <?php if($review_count > 0): ?>
                        <span class="text-muted">(<?php echo $review_count; ?>)</span>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                  
                  <div class="distance">
                    <i class="bi bi-signpost-2"></i> <span data-i18n="destinationDetails.kmAway"><?php echo t_destination_details('destinationDetails.kmAway', $current_lang); ?></span> <?php echo $distance; ?> km
                  </div>
                  
                  <div class="price">
                    <?php echo $room_price_format; ?> VND<span data-i18n="destinationDetails.perNight"><?php echo t_destination_details('destinationDetails.perNight', $current_lang); ?></span>
                  </div>
                  
                  <a href="room_details.php?id=<?php echo $room['id']; ?>" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-eye me-1"></i><span data-i18n="destinationDetails.viewRoom"><?php echo t_destination_details('destinationDetails.viewRoom', $current_lang); ?></span>
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="text-center text-muted py-4">
          <i class="bi bi-inbox" style="font-size: 3rem;"></i>
          <p class="mt-3" data-i18n="destinationDetails.noNearbyRooms"><?php echo t_destination_details('destinationDetails.noNearbyRooms', $current_lang); ?></p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Similar Destinations -->
    <?php
      // Lấy các điểm đến tương tự (cùng category, khác ID)
      $similar_destinations_query = "SELECT d.* 
                                    FROM `destinations` d
                                    WHERE d.category = ? 
                                    AND d.id != ? 
                                    AND d.active = 1
                                    ORDER BY d.rating DESC, d.review_count DESC
                                    LIMIT 6";
      
      $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'destination_images'");
      $similar_result = select($similar_destinations_query, [$destination['category'], $destination_id], 'si');
      
      if($similar_result && mysqli_num_rows($similar_result) > 0):
    ?>
    <div class="rooms-nearby" style="margin-top: 3rem;">
      <h5 class="mb-4"><i class="bi bi-compass"></i> <span data-i18n="destinationDetails.similarDestinations" data-i18n-skip><?php echo t_destination_details('destinationDetails.similarDestinations', $current_lang); ?></span></h5>
      <div class="row g-3">
        <?php 
          while($similar = mysqli_fetch_assoc($similar_result)):
            $similar_id = $similar['id'];
            // Dịch tên điểm đến và escape để hiển thị an toàn
            $similar_name_translated = t_destinations($similar['name'], $current_lang);
            $similar_name_escaped = htmlspecialchars($similar_name_translated, ENT_QUOTES, 'UTF-8');
            $similar_location = htmlspecialchars($similar['location'], ENT_QUOTES, 'UTF-8');
            $similar_rating = (float)$similar['rating'];
            $similar_review_count = (int)$similar['review_count'];
            
            // Lấy ảnh chính từ destination_images hoặc fallback về destinations.image
            $similar_primary_image = '';
            if($table_check && mysqli_num_rows($table_check) > 0){
              $similar_primary_image_query = "SELECT image FROM `destination_images` WHERE `destination_id` = ? AND `is_primary` = 1 ORDER BY `sort_order` ASC LIMIT 1";
              $similar_primary_image_result = @select($similar_primary_image_query, [$similar_id], 'i');
              if($similar_primary_image_result && mysqli_num_rows($similar_primary_image_result) > 0){
                $similar_primary_image_data = mysqli_fetch_assoc($similar_primary_image_result);
                $similar_primary_image = $similar_primary_image_data['image'];
              }
            }
            
            // Fallback to old image field if no primary image in destination_images
            if(empty($similar_primary_image)){
              $similar_primary_image = $similar['image'] ? $similar['image'] : 'default.jpg';
            }
            
            // Loại bỏ đường dẫn nếu có trong tên ảnh (tránh lặp path)
            // Nếu image chứa "destinations/" hoặc đường dẫn đầy đủ, chỉ lấy tên file
            if(strpos($similar_primary_image, 'destinations/') !== false){
              $similar_primary_image = basename($similar_primary_image);
            } else if(strpos($similar_primary_image, '/') !== false && strpos($similar_primary_image, 'http') === false){
              // Nếu có dấu / nhưng không phải URL đầy đủ, chỉ lấy tên file
              $similar_primary_image = basename($similar_primary_image);
            }
            
            $similar_image = $path . $similar_primary_image;
            
            // Generate stars
            $similar_stars = '';
            $similar_full_stars = floor($similar_rating);
            $similar_has_half = ($similar_rating - $similar_full_stars) >= 0.5;
            for($i = 0; $i < $similar_full_stars; $i++) {
              $similar_stars .= '<i class="bi bi-star-fill text-warning"></i>';
            }
            if($similar_has_half) {
              $similar_stars .= '<i class="bi bi-star-half text-warning"></i>';
            }
            for($i = $similar_full_stars + ($similar_has_half ? 1 : 0); $i < 5; $i++) {
              $similar_stars .= '<i class="bi bi-star text-muted"></i>';
            }
        ?>
          <div class="col-md-6 col-lg-4">
            <div class="room-card-mini">
              <img src="<?php echo $similar_image; ?>" 
                   alt="<?php echo $similar_name_escaped; ?>" 
                   class="room-card-mini-image"
                   onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'200\'%3E%3Crect fill=\'%23f3f4f6\' width=\'400\' height=\'200\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-family=\'Arial\' font-size=\'16\'%3ENo Image%3C/text%3E%3C/svg%3E';">
              <div class="room-card-mini-body">
                <h6><?php echo $similar_name_escaped; ?></h6>
                
                <div class="distance mb-2">
                  <i class="bi bi-geo-alt-fill"></i> <?php echo $similar_location; ?>
                </div>
                
                <?php if($similar_rating > 0): ?>
                <div class="mb-2">
                  <div class="stars d-inline-block"><?php echo $similar_stars; ?></div>
                  <span class="text-muted ms-1"><?php echo number_format($similar_rating, 1); ?></span>
                  <?php if($similar_review_count > 0): ?>
                    <span class="text-muted">(<?php echo $similar_review_count; ?>)</span>
                  <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <a href="destination_details.php?id=<?php echo $similar_id; ?>" class="btn btn-sm btn-primary w-100">
                  <i class="bi bi-eye me-1"></i><span data-i18n="destinationDetails.viewDetails"><?php echo t_destination_details('destinationDetails.viewDetails', $current_lang); ?></span>
                </a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>
  
  <!-- Floating Scroll to Top Button -->
  <button id="scrollToTop" class="btn btn-primary rounded-circle position-fixed bottom-0 end-0 m-4" 
          style="width: 56px; height: 56px; display: none; z-index: 1000; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4); transition: all 0.3s ease;">
    <i class="bi bi-arrow-up text-white fs-5"></i>
  </button>
  
  <script>
    const destinationImages = <?php echo json_encode($images); ?>;
    
    // Hàm thay đổi ảnh chính khi click vào ảnh trong grid
    function changeMainImage(index) {
      if(index >= 0 && index < destinationImages.length) {
        const mainImage = document.getElementById('main-gallery-image');
        if(mainImage) {
          // Fade out
          mainImage.style.opacity = '0';
          mainImage.style.transition = 'opacity 0.3s ease';
          
          setTimeout(() => {
            mainImage.src = destinationImages[index];
            mainImage.style.opacity = '1';
          }, 150);
        }
        
        // Cập nhật active state cho grid items
        document.querySelectorAll('.gallery-grid-item').forEach((item, idx) => {
          const itemIndex = parseInt(item.getAttribute('data-index') || '0');
          if(itemIndex === index) {
            item.classList.add('active');
          } else {
            item.classList.remove('active');
          }
        });
      }
    }
    
    // Scroll to top button
    const scrollToTopBtn = document.getElementById('scrollToTop');
    
    window.addEventListener('scroll', function() {
      if (window.pageYOffset > 300) {
        scrollToTopBtn.style.display = 'block';
      } else {
        scrollToTopBtn.style.display = 'none';
      }
    });
    
    scrollToTopBtn.addEventListener('click', function() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });
    
    // Add fade-in animation on scroll
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }
      });
    }, observerOptions);
    
    document.querySelectorAll('.destination-info-card, .rooms-nearby').forEach(el => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(20px)';
      el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
      observer.observe(el);
    });
  </script>

  <?php require('inc/footer.php'); ?>
  <?php require('inc/modals.php'); ?>

  <!-- Image Gallery Modal - Traveloka Style -->
  <div class="modal fade image-gallery-modal" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content bg-dark">
        <!-- Header - Traveloka Style -->
        <div class="gallery-modal-header">
          <div class="gallery-header-left">
            <h5 class="gallery-hotel-name mb-0"><?php echo htmlspecialchars(t_destinations($destination['name'], $current_lang)); ?></h5>
          </div>
          <div class="gallery-header-center">
            <span class="gallery-header-title active" data-i18n="destinationDetails.destinationImages"><?php echo t_destination_details('destinationDetails.destinationImages', $current_lang); ?></span>
          </div>
          <div class="gallery-header-right">
            <button type="button" class="gallery-close-btn" data-bs-dismiss="modal" aria-label="Close">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>
        
        <!-- Main Content -->
        <div class="gallery-modal-body">
          <!-- Main Image Container -->
          <div class="gallery-modal-main">
            <img id="modal-image" src="" alt="<?php echo htmlspecialchars(t_destinations($destination['name'], $current_lang)); ?>" 
                 class="gallery-modal-main-img">
            
            <!-- Navigation Arrows -->
            <button class="gallery-nav-btn gallery-nav-prev" onclick="prevImage()">
              <i class="bi bi-chevron-left"></i>
            </button>
            <button class="gallery-nav-btn gallery-nav-next" onclick="nextImage()">
              <i class="bi bi-chevron-right"></i>
            </button>
            
            <!-- Image Info -->
            <div class="gallery-image-info">
              <div class="gallery-caption">
                <span data-i18n="destinationDetails.image"><?php echo t_destination_details('destinationDetails.image', $current_lang); ?></span> <span id="image-caption">1</span>
              </div>
              <div class="gallery-counter">
                <span id="image-counter">1 / <?php echo count($images); ?></span>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Category Filter Bar -->
        <div class="gallery-filter-bar">
          <div class="gallery-filter-scroll">
            <button class="gallery-filter-btn active" onclick="filterGalleryImages('all')">
              <span data-i18n="destinationDetails.allImages"><?php echo t_destination_details('destinationDetails.allImages', $current_lang); ?></span> (<?php echo count($images); ?>)
            </button>
            <?php 
              // Group images by category if needed (for future enhancement)
              // For now, just show "all"
            ?>
          </div>
        </div>
        
        <!-- Thumbnail Grid Footer -->
        <div class="gallery-thumbnails-container">
          <div class="gallery-thumbnails-grid" id="gallery-thumbnails-grid">
            <?php foreach($images as $index => $img): ?>
              <div class="gallery-thumbnail-item <?php echo $index == 0 ? 'active' : ''; ?>" 
                   onclick="goToImage(<?php echo $index; ?>)"
                   data-index="<?php echo $index; ?>">
                <img src="<?php echo htmlspecialchars($img); ?>" 
                     alt="Ảnh <?php echo $index + 1; ?>"
                     loading="lazy"
                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'120\' height=\'90\'%3E%3Crect fill=\'%23f3f4f6\' width=\'120\' height=\'90\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-family=\'Arial\' font-size=\'12\'%3ENo Image%3C/text%3E%3C/svg%3E';">
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // destinationImages đã được khai báo ở trên
    let currentImageIndex = 0;
    
    
    // Click vào ảnh chính để mở modal
    document.addEventListener('DOMContentLoaded', function() {
      const mainImage = document.getElementById('main-gallery-image');
      if(mainImage) {
        mainImage.addEventListener('click', function() {
          currentImageIndex = 0;
          updateModalImage();
        });
      }
    });
    
    
    function openImageModal(index) {
      currentImageIndex = index >= 0 ? index : 0;
      updateModalImage();
      
      const modalElement = document.getElementById('imageModal');
      if(modalElement) {
        const modal = new bootstrap.Modal(modalElement, {
          backdrop: true,
          keyboard: true
        });
        modal.show();
        
        // Prevent body scroll when modal is open
        document.body.style.overflow = 'hidden';
        
        // Click to zoom image
        const modalImage = document.getElementById('modal-image');
        if(modalImage) {
          modalImage.addEventListener('click', function() {
            this.classList.toggle('zoomed');
          });
        }
      }
    }
    
    // Close modal and restore scroll
    const imageModal = document.getElementById('imageModal');
    if(imageModal) {
      imageModal.addEventListener('hidden.bs.modal', function() {
        document.body.style.overflow = '';
        // Reset zoom
        const modalImage = document.getElementById('modal-image');
        if(modalImage) {
          modalImage.classList.remove('zoomed');
        }
      });
    }
    
    function updateModalImage() {
      if(currentImageIndex >= 0 && currentImageIndex < destinationImages.length) {
        const modalImage = document.getElementById('modal-image');
        const counter = document.getElementById('image-counter');
        const caption = document.getElementById('image-caption');
        
        // Fade out
        if(modalImage) {
          modalImage.style.opacity = '0';
          setTimeout(() => {
            modalImage.src = destinationImages[currentImageIndex];
            modalImage.style.opacity = '1';
          }, 200);
        }
        
        // Update counter
        if(counter) {
          counter.textContent = (currentImageIndex + 1) + ' / ' + destinationImages.length;
        }
        
        // Update caption
        if(caption) {
          const imageText = window.i18n ? window.i18n.translate('destinationDetails.image') : 'Ảnh';
          caption.textContent = imageText + ' ' + (currentImageIndex + 1);
        }
        
        // Update active thumbnail
        document.querySelectorAll('.gallery-thumbnail-item').forEach((item, index) => {
          if(index === currentImageIndex) {
            item.classList.add('active');
            // Scroll thumbnail into view
            item.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
          } else {
            item.classList.remove('active');
          }
        });
      }
    }
    
    function goToImage(index) {
      if(index >= 0 && index < destinationImages.length) {
        currentImageIndex = index;
        updateModalImage();
      }
    }
    
    function filterGalleryImages(category) {
      // Update active filter button
      const event = window.event || arguments[0];
      if(event && event.target) {
        document.querySelectorAll('.gallery-filter-btn').forEach(btn => {
          btn.classList.remove('active');
        });
        event.target.classList.add('active');
      }
      
      // Future enhancement: filter thumbnails by category
      // For now, show all images
    }
    
    function prevImage() {
      if(currentImageIndex > 0) {
        currentImageIndex--;
      } else {
        currentImageIndex = destinationImages.length - 1;
      }
      updateModalImage();
    }
    
    function nextImage() {
      if(currentImageIndex < destinationImages.length - 1) {
        currentImageIndex++;
      } else {
        currentImageIndex = 0;
      }
      updateModalImage();
    }
    
    // Handle main image click
    document.addEventListener('DOMContentLoaded', function() {
      const mainImage = document.getElementById('main-gallery-image');
      if(mainImage) {
        mainImage.addEventListener('click', function() {
          currentImageIndex = 0;
          updateModalImage();
        });
      }
      
      // Handle keyboard navigation in modal
      document.getElementById('imageModal')?.addEventListener('shown.bs.modal', function() {
        document.addEventListener('keydown', handleKeyboardNav);
      });
      
      document.getElementById('imageModal')?.addEventListener('hidden.bs.modal', function() {
        document.removeEventListener('keydown', handleKeyboardNav);
      });
    });
    
    function handleKeyboardNav(e) {
      if(e.key === 'ArrowLeft') {
        prevImage();
      } else if(e.key === 'ArrowRight') {
        nextImage();
      }
    }
    
    // Share destination
    function shareDestination() {
      const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
      const url = window.location.href;
      const title = document.querySelector('.detail-hero h2')?.textContent || (currentLang === 'en' ? 'Destination' : 'Điểm du lịch');
      const shareText = currentLang === 'en' ? 'View this destination' : 'Xem điểm du lịch này';
      const copiedText = currentLang === 'en' ? 'Link copied to clipboard!' : 'Đã sao chép link vào clipboard!';
      const copyPrompt = currentLang === 'en' ? 'Copy this link:' : 'Sao chép link này:';
      
      if(navigator.share) {
        navigator.share({
          title: title,
          text: shareText,
          url: url
        }).catch(err => console.log('Error sharing', err));
      } else {
        // Fallback: Copy to clipboard
        if(navigator.clipboard) {
          navigator.clipboard.writeText(url).then(() => {
            // Show toast notification
            showToast(copiedText, 'success');
          }).catch(() => {
            // Fallback: Show URL
            prompt(copyPrompt, url);
          });
        } else {
          prompt(copyPrompt, url);
        }
      }
    }
    
    // Share to Facebook
    function shareToFacebook() {
      const url = encodeURIComponent(window.location.href);
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
    }
    
    // Toast notification
    function showToast(message, type = 'info') {
      const toast = document.createElement('div');
      toast.className = `alert alert-${type === 'success' ? 'success' : 'info'} position-fixed top-0 start-50 translate-middle-x mt-3`;
      toast.style.zIndex = '9999';
      toast.style.minWidth = '300px';
      toast.style.boxShadow = '0 4px 20px rgba(0,0,0,0.15)';
      toast.innerHTML = `
        <div class="d-flex align-items-center">
          <i class="bi bi-${type === 'success' ? 'check-circle' : 'info-circle'}-fill me-2"></i>
          <span>${message}</span>
        </div>
      `;
      document.body.appendChild(toast);
      
      setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
      }, 3000);
    }
    
    // Print page
    function printPage() {
      window.print();
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if(href !== '#' && href.length > 1) {
          e.preventDefault();
          const target = document.querySelector(href);
          if (target) {
            const offset = 100;
            const elementPosition = target.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - offset;
            
            window.scrollTo({
              top: offsetPosition,
              behavior: 'smooth'
            });
          }
        }
      });
    });
  </script>

</body>
</html>

