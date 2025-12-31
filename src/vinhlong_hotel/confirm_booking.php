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
require_once('admin/inc/promos_helper.php');

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

// Hàm dịch mô tả phòng (tương tự như trong room_details.php)
function t_room_description($description, $lang = 'vi') {
  if($lang === 'vi') {
    return $description; // Giữ nguyên tiếng Việt
  }

  if (stripos($description, 'VIN HOTEL') !== false) {
    return "VIN HOTEL is the top recommendation for budget travelers who want to stay at a comfortable yet affordable hotel.\n\n"
      . "For travelers who want comfortable travel with a budget-friendly approach, VIN HOTEL will be the perfect accommodation choice, providing quality amenities and excellent service.\n\n"
      . "When staying at a hotel, interior design and architecture are certainly two important factors that satisfy guests. With unique design, VIN HOTEL brings a satisfying accommodation space for guests.\n\n"
      . "From business events to company meetings, VIN HOTEL provides full services and amenities to meet all needs of guests and colleagues.\n\n"
      . "Enjoy fun time with the whole family with a range of entertainment amenities at VIN HOTEL, a wonderful hotel suitable for all vacations with loved ones.\n\n"
      . "If planning a long vacation, then VIN HOTEL is the choice for you. With full amenities and excellent service quality, VIN HOTEL will make you feel as comfortable as at home.\n\n"
      . "Solo travel is also no less interesting and VIN HOTEL is a suitable place specifically for those who value privacy during their stay.\n\n"
      . "Excellent service, complete facilities and hotel amenities provided will make you unable to complain throughout your stay at VIN HOTEL.\n\n"
      . "The 24-hour front desk is always ready to serve you from check-in to check-out or any requests. If you need help, please contact the front desk team, we are always ready to assist you.\n\n"
      . "VIN HOTEL is a hotel with full amenities and excellent service according to most guests' assessments.\n\n"
      . "With the available amenities, VIN HOTEL is truly a perfect place to stay.";
  }
  
  // Chuẩn hóa description
  $normalized = trim($description);
  $normalized = preg_replace('/\s+/', ' ', $normalized);
  
  // Mapping các mô tả phòng phổ biến
  $description_map = [
    'Phòng ốc tuy không gian hơi hẹp nhưng sạch sẽ dễ chịu' => 'The room space is a bit narrow but clean and comfortable',
    'Khách sạn ở đây rất thoải mái và yên tĩnh.' => 'The hotel here is very comfortable and quiet.',
    'Phòng ốc tuy không gian hơi hẹp nhưng sạch sẽ dễ chịu Khách sạn ở đây rất thoải mái và yên tĩnh.' => 'The room space is a bit narrow but clean and comfortable. The hotel here is very comfortable and quiet.',
    'Phòng đơn giản, phù hợp với những khách hàng cần chỗ nghỉ ngắn hạn. Được trang bị các tiện nghi cơ bản như giường thoải mái, bàn làm việc nhỏ, và Wi-Fi miễn phí.' => 'Simple room, suitable for guests who need short-term accommodation. Equipped with basic amenities such as comfortable bed, small work desk, and free Wi-Fi.',
    'Nâng cấp nhẹ so với Phòng Cơ Bản 1, mang đến không gian rộng rãi hơn và thêm các tiện ích như TV màn hình phẳng và minibar.' => 'Slight upgrade compared to Basic Room 1, offering more spacious area and additional amenities such as flat-screen TV and minibar.',
    'Phòng cơ bản cao cấp hơn với thiết kế hiện đại, ban công nhỏ hoặc cửa sổ lớn có view thành phố, tạo cảm giác thoáng đãng và thư giãn.' => 'Higher-end basic room with modern design, small balcony or large window with city view, creating an airy and relaxing feeling.',
    'Không gian rộng rãi với thiết kế sang trọng, phù hợp cho các kỳ nghỉ dài ngày. Được trang bị nội thất cao cấp, phòng tắm riêng với bồn tắm, và các tiện ích như máy pha cà phê và két an toàn.' => 'Spacious space with luxurious design, suitable for long-term stays. Equipped with high-end furniture, private bathroom with bathtub, and amenities such as coffee maker and safe.',
  ];
  
  // Kiểm tra mapping chính xác
  if(isset($description_map[$normalized])) {
    return $description_map[$normalized];
  }
  
  // Nếu không tìm thấy, trả về nguyên bản (có thể cải thiện sau)
  return $description;
}

// H�m d?ch ti?n �ch
function t_facility_name($name, $lang = 'vi') {
  if($lang === 'vi') {
    return $name;
  }
  $facility_map = [
    'Ấm Đun Nước' => 'Kettle',
    'Bàn Ủi' => 'Iron',
    'Dép Đi Trong Nhà' => 'Slippers',
    'Dịch vụ 24/24 giờ' => '24/24 Hour Service',
    'Gương Toàn Thân' => 'Full-length Mirror',
    'Máy Lạnh' => 'Air Conditioner',
    'Máy Nước Nóng' => 'Water Heater',
    'Máy Sấy Tóc' => 'Hair Dryer',
    'Máy Sưởi' => 'Heater',
    'Spa' => 'Spa',
    'Truyền Hình' => 'Television',
    'Tủ Quần Áo' => 'Wardrobe',
    'Wi-Fi' => 'Wi-Fi',
    'WiFi miễn phí' => 'Free Wi-Fi',
    'Minibar' => 'Minibar',
    'Khu Làm Việc' => 'Workspace',
    'Hồ bơi' => 'Swimming Pool',
    'Phòng gym' => 'Gym',
    'Nhà hàng' => 'Restaurant',
    'Bãi đỗ xe' => 'Parking',
    'Đồ Vệ Sinh Cá Nhân' => 'Personal Hygiene Items',
  ];
  return $facility_map[$name] ?? $name;
}

function t_facility_description($description, $lang = 'vi') {
  if($lang === 'vi') {
    return $description;
  }
  $desc_map = [
    'Ấm đun nước siêu tốc giúp pha trà, cà phê hoặc mì ly nhanh chóng.' => 'Super-fast kettle helps make tea, coffee, or instant noodles quickly.',
    'Bàn ủi tiện dụng giúp quần áo luôn phẳng phiu, chỉnh chu cho khách lưu trú.' => 'Convenient iron helps clothes stay flat and neat for guests.',
    'Dép mềm mại, đảm bảo vệ sinh và thoải mái khi di chuyển trong phòng.' => 'Soft slippers ensure hygiene and comfort when moving around the room.',
    'Dịch vụ lễ tân 24/24, luôn sẵn sàng hỗ trợ mọi nhu cầu của khách hàng.' => '24/7 front desk service, always ready to assist guests.',
    'Gương soi toàn thân lớn, tiện lợi cho việc chuẩn bị trang phục.' => 'Large full-length mirror, convenient for preparing outfits.',
    'Hệ thống điều hòa không khí hiện đại, giúp không gian phòng luôn mát mẻ và dễ chịu.' => 'Modern air conditioning system keeps the room cool and comfortable.',
    'Máy nước nóng tiện lợi, cung cấp nước nóng tức thì, đảm bảo sự thoải mái khi sử dụng phòng tắm.' => 'Convenient water heater provides instant hot water for comfortable bathing.',
    'Hồ bơi ngoài trời, nước trong xanh, thích hợp để thư giãn hoặc bơi lội.' => 'Outdoor swimming pool with clear water, ideal for relaxing or swimming.',
  ];
  return $desc_map[$description] ?? $description;
}

/* ==== VALIDATION ==== */
if(!isset($_GET['id']) || $settings_r['shutdown']==true){
  redirect('rooms.php');
}
if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
  redirect('rooms.php');
}

$data = filteration($_GET);
$room_res = select("SELECT * FROM `rooms` WHERE `id`=? AND `status`=? AND `removed`=?",[$data['id'],1,0],'iii');

if(mysqli_num_rows($room_res)==0){
  redirect('rooms.php');
}

$room_data = mysqli_fetch_assoc($room_res);
$base_price    = (int)$room_data['price'];
$discount_pct  = isset($room_data['discount']) ? (int)$room_data['discount'] : 0;
$effective_price = $base_price;
if($discount_pct > 0 && $discount_pct <= 100){
  $effective_price = max(0, $base_price - ($base_price * $discount_pct / 100));
}

$_SESSION['room'] = [
  "id" => $room_data['id'],
  "name" => $room_data['name'],
  "price" => $effective_price,
  "payment" => null,
  "available" => false,
];

$user_res = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1", [$_SESSION['uId']], "i");
$user_data = mysqli_fetch_assoc($user_res);

// Ưu đãi hạng thành viên
// Cấu hình phần trăm giảm cho mỗi hạng
$loyalty_rates = [
  'Platinum' => 8,  // 8% cho Platinum (10+ bookings)
  'Gold'     => 5,  // 5% cho Gold (5-9 bookings)
  'Silver'   => 3,  // 3% cho Silver (1-4 bookings)
  'Member'   => 0   // 0% cho Member (0 bookings)
];

$loyalty_rate = 0;
$loyalty_label = 'Member';
$completed = 0;
$stat_res = select("SELECT SUM(booking_status='booked') AS completed FROM booking_order WHERE user_id=?", [$_SESSION['uId']], 'i');
if($stat_res && mysqli_num_rows($stat_res)){
  $row = mysqli_fetch_assoc($stat_res);
  $completed = (int)($row['completed'] ?? 0);
}
if($completed >= 10){ 
  $loyalty_rate = $loyalty_rates['Platinum']; 
  $loyalty_label = 'Platinum'; 
}
else if($completed >= 5){ 
  $loyalty_rate = $loyalty_rates['Gold']; 
  $loyalty_label = 'Gold'; 
}
else if($completed >= 1){ 
  $loyalty_rate = $loyalty_rates['Silver']; 
  $loyalty_label = 'Silver'; 
}

/* Lấy thumbnail phòng */
$room_thumb = ROOMS_IMG_PATH."thumbnail.jpg";
$thumb_q = mysqli_query($con,"SELECT * FROM `room_images` WHERE `room_id`='$room_data[id]' AND `thumb`='1'");
if(mysqli_num_rows($thumb_q)>0){
  $room_thumb = ROOMS_IMG_PATH . mysqli_fetch_assoc($thumb_q)['image'];
}

// Danh sách mã giảm giá (ưu tiên từ DB, fallback dữ liệu tĩnh)
$promo_rows = getActivePromos(50);
$promo_js = [];
if(!empty($promo_rows)){
  foreach($promo_rows as $p){
    $promo_js[] = [
      'code' => strtoupper($p['code']),
      'type' => $p['discount_type'],
      'value'=> (float)$p['discount_value'],
      'min'  => (int)$p['min_amount'],
      'cap'  => isset($p['max_discount']) ? (int)$p['max_discount'] : null,
      'label'=> $p['label'] ?: $p['title'],
      'expires_at' => $p['expires_at']
    ];
  }
}

if(empty($promo_js)){
  $promo_js = [
    ['code'=>'VINHLONG10','type'=>'percent','value'=>10,'min'=>0,'cap'=>null,'label'=>'Giảm 10%'],
    ['code'=>'RIVERVIEW15','type'=>'percent','value'=>15,'min'=>0,'cap'=>null,'label'=>'Giảm 15%'],
    ['code'=>'KS1212QT','type'=>'percent','value'=>2,'min'=>3000000,'cap'=>500000,'label'=>'Giảm 2% tối đa 500k, đơn từ 3 triệu'],
    ['code'=>'KS1212VN','type'=>'percent','value'=>4,'min'=>2000000,'cap'=>300000,'label'=>'Giảm 4% tối đa 300k, đơn từ 2 triệu'],
    ['code'=>'WKND100','type'=>'percent','value'=>3,'min'=>0,'cap'=>100000,'label'=>'Giảm 3% tối đa 100k']
  ];
}
?>

<style>
  body{ background:#f4f6fb !important; }
  .booking-hero{
    background: linear-gradient(120deg, rgba(15,93,122,0.12), rgba(255,179,87,0.16));
    border: 1px solid #e1e6ef;
    border-radius: 18px;
    padding: 20px 22px;
    box-shadow: 0 18px 48px rgba(12,34,52,0.12);
    animation: fadeInUp 0.6s ease-out;
  }
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  .hero-price{
    background: linear-gradient(135deg, #0f5d7a, #0b4156);
    color: #fff;
    padding: 16px;
    border-radius: 14px;
    min-width: 240px;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.15);
  }
  .meta-pill{
    display:inline-flex;
    align-items:center;
    gap:8px;
    background:#f0f4f9;
    border:1px solid #dfe4ec;
    padding:8px 12px;
    border-radius:12px;
    font-weight:600;
    color:#1f2a3d;
  }
  .pill-soft{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:8px 12px;
    border-radius:999px;
    background:rgba(15,93,122,0.08);
    color:#0f3b56;
    font-weight:600;
  }
  .pill-link{
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:8px 12px;
    border-radius:999px;
    background:#fff;
    border:1px dashed #d0dae7;
    color:#0d6efd;
    font-weight:700;
  }
  .stepper{
    display:flex;
    gap:16px;
    margin-top:8px;
    position: relative;
  }
  .stepper::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 2px;
    background: #e5e7eb;
    z-index: 0;
    transform: translateY(-50%);
    margin: 0 40px;
  }
  .step{
    background:#fff;
    border:2px solid #dfe6ef;
    border-radius:14px;
    padding:14px 16px;
    display:flex;
    align-items:center;
    gap:12px;
    flex:1;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    z-index: 1;
  }
  .step::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #0f5d7a, #0b4156);
    transform: scaleX(0);
    transition: transform 0.3s ease;
  }
  .step.active::before {
    transform: scaleX(1);
  }
  .step-index{
    width:32px;
    height:32px;
    border-radius:50%;
    background:#e5e7eb;
    color:#6b7280;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    font-weight:800;
    font-size:14px;
    transition: all 0.3s ease;
    flex-shrink: 0;
  }
  .step.active .step-index{
    background:linear-gradient(135deg, #0f5d7a, #0b4156);
    color:#fff;
    box-shadow: 0 4px 12px rgba(15,93,122,0.3);
    transform: scale(1.1);
  }
  .step.active{
    border-color:#0f5d7a;
    box-shadow:0 8px 24px rgba(15,93,122,0.2);
    background: linear-gradient(135deg, rgba(15,93,122,0.05), rgba(11,65,86,0.05));
  }
  .step:hover:not(.active) {
    border-color: #cbd5e1;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  }
  .card-lift{
    background:#fff;
    border:1px solid #e4e9f2;
    border-radius:20px;
    box-shadow:0 18px 40px rgba(12,34,52,0.12);
    padding:24px;
    transition: all 0.3s ease;
  }
  .card-lift:hover {
    box-shadow: 0 24px 50px rgba(12,34,52,0.16);
    transform: translateY(-2px);
  }
  .gallery-stack{display:flex;flex-direction:column;gap:12px;}
  .gallery-main{
    position:relative;
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 14px 32px rgba(12,34,52,0.14);
  }
  .gallery-main img{
    width:100%;
    height:480px;
    object-fit:cover;
    display:block;
    transition:transform .35s ease;
  }
  .gallery-main:hover img{transform:scale(1.01);}
  .gallery-tag{
    position:absolute;
    left:14px;
    bottom:14px;
    background:rgba(0,0,0,0.55);
    color:#fff;
    padding:6px 12px;
    border-radius:12px;
    font-weight:700;
    font-size:13px;
    backdrop-filter:blur(2px);
  }
  .gallery-thumbs{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(140px,1fr));
    gap:10px;
  }
  .gallery-thumbs img{
    width:100%;
    height:150px;
    object-fit:cover;
    border-radius:12px;
    transition:transform .28s ease, box-shadow .28s ease;
    box-shadow:0 8px 18px rgba(12,34,52,0.1);
    cursor:pointer;
  }
  .gallery-thumbs img:hover{transform:scale(1.04);box-shadow:0 12px 26px rgba(12,34,52,0.16);}
  .thumb-overlay{
    position:relative;
    overflow:hidden;
    border-radius:12px;
    cursor:pointer;
    transition:transform 0.2s;
  }
  .thumb-overlay:hover{
    transform:scale(1.02);
  }
  .thumb-overlay span{
    position:absolute;
    inset:0;
    display:flex;
    align-items:center;
    justify-content:center;
    background:rgba(0,0,0,0.5);
    color:#fff;
    font-weight:600;
    font-size:13px;
    border-radius:12px;
    text-align:center;
    padding:8px;
    gap:6px;
    backdrop-filter:blur(4px);
    pointer-events:none; /* Cho phép click qua span */
  }
  .thumb-overlay img{
    pointer-events:none; /* Cho phép click qua img */
  }
  .thumb-overlay span i{
    font-size:16px;
  }
  /* Lightbox */
  .lb-backdrop{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.8);
    display:flex;
    align-items:center;
    justify-content:center;
    z-index:9999;
    opacity:0;
    pointer-events:none;
    transition:opacity .2s ease;
  }
  .lb-backdrop.show{
    opacity:1;
    pointer-events:auto;
  }
  .lb-frame{
    position:relative;
    width:90vw;
    max-width:90vw;
    height:80vh;
    max-height:90vh;
  }
  .lb-frame img{
    width:100%;
    height:100%;
    object-fit:contain;
    border-radius:12px;
    box-shadow:0 18px 40px rgba(0,0,0,0.35);
  }
  .lb-nav{
    position:absolute;
    top:50%;
    transform:translateY(-50%);
    width:42px;
    height:42px;
    border-radius:50%;
    border:none;
    background:rgba(255,255,255,0.85);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
    box-shadow:0 6px 16px rgba(0,0,0,0.25);
  }
  .lb-prev{left:-54px;}
  .lb-next{right:-54px;}
  .lb-close{
    position:absolute;
    top:-12px;
    right:-12px;
    width:36px;
    height:36px;
    border-radius:50%;
    border:none;
    background:rgba(255,255,255,0.9);
    box-shadow:0 6px 16px rgba(0,0,0,0.25);
    font-weight:700;
  }
  .reviews-card .review-item{
    border-bottom:1px solid #e5e7eb;
    padding-bottom:10px;
    margin-bottom:10px;
    padding-left: 60px;
    position: relative;
    animation: fadeInUp 0.4s ease-out;
  }
  .reviews-card .review-item:last-child{
    border-bottom:none;
    margin-bottom:0;
    padding-bottom:0;
  }
  .review-avatar {
    position: absolute;
    left: 0;
    top: 0;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0f5d7a, #0b4156);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 18px;
    box-shadow: 0 4px 12px rgba(15,93,122,0.2);
    object-fit: cover;
    border: 2px solid #fff;
  }
  .review-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    display: block;
  }
  .star{
    color:#f59e0b;
  }
  .review-images {
    margin-top: 12px;
    margin-bottom: 8px;
  }
  .review-img-thumb {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 10px;
    border: 2px solid #e5e7eb;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  .review-img-thumb:hover {
    transform: scale(1.08);
    border-color: #0d6efd;
    box-shadow: 0 4px 16px rgba(13,110,253,0.3);
    z-index: 10;
    position: relative;
  }
  .admin-reply {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-left: 3px solid #0d6efd !important;
  }
  .btn-helpful {
    font-size: 14px;
    border-radius: 8px;
    padding: 6px 12px;
    transition: all 0.3s ease;
    font-weight: 600;
  }
  .btn-helpful:hover {
    background-color: #f0f0f0;
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  .btn-helpful.active {
    background: #e3f2fd;
    color: #0d6efd;
    border-color: #0d6efd;
  }
  .btn-helpful.active i {
    color: #0d6efd;
  }
  .btn-helpful.active:hover {
    background: #bbdefb;
  }
  .promo-success {
    animation: successPulse 0.6s ease-out;
  }
  @keyframes successPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); background: #dcfce7; border-color: #16a34a; }
    100% { transform: scale(1); }
  }
  .warn-banner{
    display:none;
    background:#fff3cd;
    color:#664d03;
    border:1px solid #ffecb5;
    border-radius:10px;
    padding:10px 12px;
    margin-bottom:10px;
    font-weight:600;
  }
  .avail-status{
    display:flex;
    align-items:center;
    gap:8px;
    font-weight:600;
    margin-bottom:6px;
  }
  .avail-status .dot{
    width:12px;
    height:12px;
    border-radius:50%;
    display:inline-block;
    position:relative;
  }
  .avail-status .dot.checked::after{
    content:'✓';
    position:absolute;
    inset:0;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:10px;
    font-weight:700;
  }
  .info-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:12px;
  }
  .info-tile{
    background:#f8fafc;
    border:1px solid #e6ecf4;
    border-radius:14px;
    padding:12px;
  }
  .info-tile .label{
    text-transform:uppercase;
    letter-spacing:0.5px;
    font-size:12px;
    color:#6b7280;
    margin-bottom:6px;
  }
  .info-tile .value{
    font-weight:700;
    color:#1f2a3d;
  }
  .booking-shell h5.section-title{
    margin-bottom:8px;
    font-weight:800;
    color:#0f2a3f;
  }
  .summary-card{
    background:linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border:2px solid #e2e8f0;
    border-radius:16px;
    padding:18px;
    box-shadow: inset 0 2px 8px rgba(0,0,0,0.04);
    animation: fadeInUp 0.5s ease-out 0.2s both;
  }
  .summary-line{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:10px;
    font-weight:600;
    padding: 6px 0;
    color: #374151;
    transition: all 0.3s ease;
  }
  .summary-line span:last-child {
    font-variant-numeric: tabular-nums;
    transition: all 0.3s ease;
  }
  .summary-line.updated span:last-child {
    animation: numberPop 0.4s ease-out;
  }
  @keyframes numberPop {
    0% { transform: scale(1); }
    50% { transform: scale(1.15); color: #0f5d7a; }
    100% { transform: scale(1); }
  }
  .summary-line.total{
    border-top:2px solid #d8dee9;
    padding-top:14px;
    margin-top:10px;
    font-size:20px;
    font-weight:800;
    color: #1f2937;
    background: linear-gradient(135deg, rgba(15,93,122,0.05), rgba(11,65,86,0.05));
    margin-left: -18px;
    margin-right: -18px;
    padding-left: 18px;
    padding-right: 18px;
    border-radius: 0 0 14px 14px;
  }
  .qr-box{
    border:2px dashed #d1d9e8;
    border-radius:16px;
    padding:20px;
    background:linear-gradient(135deg, #f9fbff 0%, #f0f4f9 100%);
    transition: all 0.3s ease;
    position: relative;
    cursor: pointer;
  }
  .qr-box:hover {
    border-color: #0f5d7a;
    background: linear-gradient(135deg, #f0f4f9 0%, #e5eaf0 100%);
    box-shadow: 0 8px 20px rgba(15,93,122,0.1);
  }
  .qr-box::before {
    content: attr(data-double-tap-text);
    position: absolute;
    top: -8px;
    right: 12px;
    background: linear-gradient(135deg, #0f5d7a, #0b4156);
    color: #fff;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
    z-index: 10;
    white-space: nowrap;
  }
  .qr-box:hover::before {
    opacity: 1;
  }
  .qr-box img {
    animation: pulseQR 2s ease-in-out infinite;
  }
  @keyframes pulseQR {
    0%, 100% {
      transform: scale(1);
    }
    50% {
      transform: scale(1.02);
    }
  }
  .qr-box.copied {
    border-color: #16a34a;
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
  }
  .qr-box.copied::before {
    content: attr(data-copied-text);
    background: linear-gradient(135deg, #16a34a, #15803d);
  }
  .badge-ghost{
    background:#f0f4f9;
    color:#0f2a3f;
    border:1px solid #d8e0ea;
    border-radius:10px;
    padding:6px 10px;
    font-weight:700;
    font-size:12px;
  }
  .availability-badge{
    background: linear-gradient(135deg, #0ea5e9 0%, #1d4ed8 100%);
    color: #ffffff;
    border: 1px solid #1d4ed8;
    border-radius: 12px;
    padding: 6px 10px;
    font-weight: 800;
    font-size: 12px;
    box-shadow: 0 6px 16px rgba(29,78,216,0.28);
  }
  #submit-hint{
    border:1px solid #ffeeba;
    background:#fff8e1;
  }
  .saved-promos .btn{
    margin:4px 4px 0 0;
  }
  /* Back to top */
  .back-to-top{
    position:fixed;
    right:20px;
    bottom:24px;
    z-index:999;
    width:48px;
    height:48px;
    border-radius:50%;
    border:none;
    display:none;
    align-items:center;
    justify-content:center;
    background:linear-gradient(135deg,#0f5d7a,#0b4156);
    color:#fff;
    box-shadow:0 12px 28px rgba(0,0,0,0.2);
  }
  .back-to-top.show{display:flex;}
  .back-to-top:hover{filter:brightness(1.05);}
  
  /* Enhanced Form Controls */
  .form-group-modern {
    position: relative;
    margin-bottom: 1rem;
  }
  .input-icon-wrapper {
    position: relative;
  }
  .input-icon-wrapper .input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    font-size: 18px;
    z-index: 2;
    pointer-events: none;
    transition: all 0.3s ease;
  }
  .input-icon-wrapper .form-control {
    padding-left: 48px;
  }
  .input-icon-wrapper .form-control:focus + .input-icon {
    color: #0f5d7a;
    transform: translateY(-50%) scale(1.1);
  }
  .form-control, .form-select {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 12px 16px;
    transition: all 0.3s ease;
  }
  
  .form-control:focus, .form-select:focus {
    border-color: #0f5d7a;
    box-shadow: 0 0 0 4px rgba(15,93,122,0.1);
    outline: none;
  }
  
  .form-control.is-invalid {
    border-color: #ef4444;
    box-shadow: 0 0 0 4px rgba(239,68,68,0.1);
  }
  
  /* Special Request Textarea */
  textarea[name="special_request"] {
    resize: vertical;
    min-height: 80px;
  }
  
  /* Promo Code Input Group */
  .input-group .btn {
    border-radius: 0 12px 12px 0;
    border-left: none;
    font-weight: 600;
    transition: all 0.3s ease;
  }
  
  .input-group .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  
  .input-group .input-icon-wrapper {
    flex: 1;
  }
  
  .input-group .input-icon-wrapper .form-control {
    border-radius: 12px 0 0 12px;
  }
</style>

<!-- HERO -->
<div class="container-lg pt-4 pb-3">
  <div class="booking-hero">
    <div class="d-flex flex-wrap align-items-start gap-3">
      <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2 text-muted small mb-1">
          <span>Vĩnh Long Hotel</span>
          <span>•</span>
          <span data-i18n="confirmBooking.room">Phòng</span>
          <span>•</span>
          <span><?php echo htmlspecialchars(t_room_name($room_data['name'], $current_lang)); ?></span>
        </div>
        <h3 class="fw-bold mb-2"><?php echo htmlspecialchars(t_room_name($room_data['name'], $current_lang)); ?></h3>
        <div class="d-flex flex-wrap gap-2 mb-2">
          <span class="availability-badge"><span data-i18n="confirmBooking.available">Còn</span> <?php echo isset($room_data['remaining']) ? (int)$room_data['remaining'] : 0; ?> <span data-i18n="confirmBooking.rooms">phòng</span></span>
          <span class="meta-pill"><i class="bi bi-people"></i><?php echo (int)$room_data['adult']; ?> <span data-i18n="confirmBooking.adults">người lớn</span> • <?php echo (int)$room_data['children']; ?> <span data-i18n="confirmBooking.children">trẻ em</span></span>
          <span class="meta-pill"><i class="bi bi-bounding-box"></i><?php echo (int)$room_data['area']; ?> m²</span>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
          <span class="pill-soft"><i class="bi bi-wifi"></i><span data-i18n="confirmBooking.freeWifi">WiFi miễn phí</span></span>
          <span class="pill-soft"><i class="bi bi-tree"></i><span data-i18n="confirmBooking.view">View thoáng</span></span>
          <a href="#amenities" class="pill-link"><i class="bi bi-list-check"></i><span data-i18n="confirmBooking.amenities">Tiện ích</span></a>
          <button class="pill-link border-0" type="button" data-bs-toggle="collapse" data-bs-target="#hero-map" aria-expanded="false" aria-controls="hero-map">
            <i class="bi bi-geo-alt-fill"></i><span data-i18n="confirmBooking.map">Bản đồ</span>
          </button>
        </div>
      </div>
      <div class="hero-price text-end">
        <div class="text-light text-opacity-75 small" data-i18n="confirmBooking.priceFrom">Giá/phòng/đêm từ</div>
        <div class="fs-3 fw-bold mb-1"><?php echo number_format($effective_price); ?> VND</div>
        <?php if($discount_pct > 0){ ?>
          <div class="text-light text-opacity-75 small mb-2"><span data-i18n="confirmBooking.originalPrice">Giá gốc</span> <del><?php echo number_format($base_price); ?> VND</del> <span class="badge bg-danger ms-1"><?php echo $discount_pct; ?>%</span></div>
        <?php } ?>
        <a href="#booking_form" class="btn btn-light text-primary fw-bold px-3 py-2"><span data-i18n="confirmBooking.bookNow">Đặt ngay</span></a>
      </div>
    </div>
    <div class="stepper">
      <div class="step active">
        <span class="step-index">1</span>
        <div>
          <div class="fw-bold small" data-i18n="confirmBooking.step1">Chọn ngày</div>
          <div class="text-muted small" data-i18n="confirmBooking.step1Desc">Kiểm tra phòng trống</div>
        </div>
      </div>
      <div class="step">
        <span class="step-index">2</span>
        <div>
          <div class="fw-bold small" data-i18n="confirmBooking.step2">Thông tin khách</div>
          <div class="text-muted small" data-i18n="confirmBooking.step2Desc">Liên hệ & yêu cầu</div>
        </div>
      </div>
      <div class="step">
        <span class="step-index">3</span>
        <div>
          <div class="fw-bold small" data-i18n="confirmBooking.step3">Thanh toán</div>
          <div class="text-muted small" data-i18n="confirmBooking.step3Desc">Xác nhận QR</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container-lg mb-3">
  <div class="collapse" id="hero-map">
    <div class="card-lift">
      <h6 class="fw-bold mb-2"><i class="bi bi-geo-alt-fill text-danger me-2"></i><span data-i18n="confirmBooking.hotelMap">Bản đồ khách sạn</span></h6>
      <div style="width:100%; height:260px; border-radius:12px; overflow:hidden;">
        <iframe src="https://www.google.com/maps?q=<?php echo urlencode($room_data['name']); ?>&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy"></iframe>
      </div>
    </div>
  </div>
</div>

<?php
$imgs = [];
$img_q = mysqli_query($con,"SELECT * FROM `room_images` WHERE `room_id`='{$room_data['id']}'");
if(mysqli_num_rows($img_q) > 0){
  while($img = mysqli_fetch_assoc($img_q)){
    $imgs[] = ROOMS_IMG_PATH.$img['image'];
  }
}
if(empty($imgs)){
  $imgs[] = ROOMS_IMG_PATH."thumbnail.jpg";
}
$total_imgs = count($imgs);

// Ratings
$rating_stats = ['avg'=>0,'count'=>0];
$stats_row = mysqli_fetch_assoc(select("SELECT ROUND(COALESCE(AVG(rating),0),1) AS avg_rating, COUNT(*) AS total FROM rating_review WHERE room_id=?", [$room_data['id']], "i"));
if($stats_row){
  $rating_stats['avg'] = $stats_row['avg_rating'];
  $rating_stats['count'] = $stats_row['total'];
}
// Chỉ lấy đánh giá từ khách hàng thật (có user_id hợp lệ và có tên)
$reviews_res = select(
  "SELECT rr.rating, rr.review, rr.datentime, rr.user_id, uc.name AS uname, uc.profile 
   FROM rating_review rr 
   INNER JOIN user_cred uc ON rr.user_id = uc.id
   WHERE rr.room_id=? 
     AND rr.user_id > 0
     AND uc.name IS NOT NULL 
     AND uc.name != ''
   ORDER BY rr.sr_no DESC
   LIMIT 3",
  [$room_data['id']], "i"
);

// Upcoming booked ranges (show to user)
$booked_ranges = [];
$booked_res = select(
  "SELECT check_in, check_out, booking_status 
   FROM booking_order 
   WHERE room_id=? 
     AND booking_status IN ('booked','pending') 
     AND check_out >= CURDATE()
   ORDER BY check_in ASC
   LIMIT 8",
  [$room_data['id']], "i"
);
if($booked_res && mysqli_num_rows($booked_res)){
  while($br = mysqli_fetch_assoc($booked_res)){
    $booked_ranges[] = [
      'check_in' => $br['check_in'],
      'check_out'=> $br['check_out'],
      'status' => $br['booking_status']
    ];
  }
}
?>

<?php if($total_imgs > 0): ?>
<!-- Modal xem toàn bộ ảnh - Giống ảnh mẫu -->
<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content" style="background:#000;">
      <!-- Header với text "1/6 tất cả hình ảnh (6)" và nút X -->
      <div class="modal-header border-0 position-absolute top-0 start-0 end-0" style="background:transparent; z-index:10; padding:20px;">
        <div class="text-white fw-semibold" id="gallery-counter-top" style="font-size:16px;">
          <span id="gallery-current-num">1</span>/<span id="gallery-total-num"><?php echo $total_imgs; ?></span> tất cả hình ảnh (<?php echo $total_imgs; ?>)
        </div>
        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1);"></button>
      </div>
      
      <div class="modal-body p-0" style="position:relative; height:100vh;">
        <!-- Ảnh lớn ở giữa -->
        <div class="gallery-main-view" style="height:100vh; display:flex; align-items:center; justify-content:center; background:#000; position:relative;">
          <img id="gallery-main-view-img" src="<?php echo htmlspecialchars($imgs[0]); ?>" 
               alt="Ảnh phòng" 
               style="max-width:100%; max-height:100%; object-fit:contain; cursor:pointer;"
               onclick="window.nextGalleryImage && window.nextGalleryImage()">
          
          <!-- Nút điều hướng trái/phải (màu đen, hình tròn) -->
          <button class="gallery-nav-btn gallery-prev-btn" onclick="window.prevGalleryImage && window.prevGalleryImage()" 
                  style="position:absolute; left:20px; top:50%; transform:translateY(-50%); 
                         width:44px; height:44px; border-radius:50%; border:none; 
                         background:#000; color:#fff; font-size:28px; font-weight:300;
                         cursor:pointer; transition:all 0.3s; display:flex; align-items:center; justify-content:center;
                         box-shadow:0 2px 8px rgba(0,0,0,0.3); opacity:0.8;">
            ‹
          </button>
          <button class="gallery-nav-btn gallery-next-btn" onclick="window.nextGalleryImage && window.nextGalleryImage()" 
                  style="position:absolute; right:20px; top:50%; transform:translateY(-50%); 
                         width:44px; height:44px; border-radius:50%; border:none; 
                         background:#000; color:#fff; font-size:28px; font-weight:300;
                         cursor:pointer; transition:all 0.3s; display:flex; align-items:center; justify-content:center;
                         box-shadow:0 2px 8px rgba(0,0,0,0.3); opacity:0.8;">
            ›
          </button>
        </div>
        
        <!-- Thumbnails bên dưới -->
        <div class="gallery-thumbs-container position-absolute bottom-0 start-0 end-0 p-3" 
             style="background:linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%); overflow-x:auto;">
          <div class="d-flex gap-2 justify-content-center" style="min-width:fit-content;">
            <?php foreach($imgs as $idx => $img): 
              $src = htmlspecialchars($img);
              $activeClass = $idx === 0 ? 'active' : '';
            ?>
              <div class="gallery-thumb-item <?php echo $activeClass; ?>" 
                   onclick="window.showGalleryImage && window.showGalleryImage(<?php echo $idx; ?>)"
                   style="flex-shrink:0; width:80px; height:60px; border-radius:6px; overflow:hidden; 
                          cursor:pointer; border:3px solid transparent; transition:all 0.3s;">
                <img src="<?php echo $src; ?>" 
                     alt="Thumb <?php echo $idx + 1; ?>" 
                     style="width:100%; height:100%; object-fit:cover;">
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.gallery-thumb-item.active {
  border-color: #0d6efd !important;
}
.gallery-nav-btn:hover {
  opacity: 1 !important;
  transform: translateY(-50%) scale(1.1) !important;
}
</style>
<?php endif; ?>

<!-- Lightbox viewer -->
<div class="lb-backdrop" id="lb-viewer" aria-hidden="true">
  <div class="lb-frame">
    <button class="lb-close" type="button" aria-label="Đóng" id="lb-close">×</button>
    <button class="lb-nav lb-prev" type="button" aria-label="Ảnh trước" id="lb-prev">‹</button>
    <img src="" alt="Ảnh phòng" id="lb-img">
    <button class="lb-nav lb-next" type="button" aria-label="Ảnh sau" id="lb-next">›</button>
  </div>
</div>

<div class="container-lg pb-5 booking-shell">
  <div class="row g-4 align-items-start">
    <div class="col-lg-7">
      <div class="card-lift mb-3">
        <div class="gallery-stack">
          <div class="gallery-main">
            <img id="gallery-main-img" src="<?php echo $imgs[0]; ?>" alt="Ảnh phòng" data-src="<?php echo $imgs[0]; ?>">
            <div class="gallery-tag"><span data-i18n="confirmBooking.allImages">Album</span> <?php echo $total_imgs; ?> <span data-i18n="confirmBooking.images">ảnh</span></div>
          </div>
          <div class="gallery-thumbs">
            <?php 
              $thumbs = array_slice($imgs,1,min(4, $total_imgs-1));
              foreach($thumbs as $idx=>$src){
                $safeSrc = htmlspecialchars($src);
                $extraCount = $total_imgs - (1 + count($thumbs));
                if($idx === count($thumbs)-1 && $extraCount > 0){
                  echo "<div class='thumb-overlay' onclick='window.openGalleryModal && window.openGalleryModal()' style='cursor:pointer;'><img class=\"thumb-img\" src=\"{$safeSrc}\" data-src=\"{$safeSrc}\" alt=\"Ảnh phòng\"><span><i class=\"bi bi-images\"></i> <span data-i18n=\"confirmBooking.viewAllImages\">Xem tất cả hình ảnh</span></span></div>";
                } else {
                  echo "<img class=\"thumb-img\" src=\"{$safeSrc}\" data-src=\"{$safeSrc}\" alt=\"Ảnh phòng\">";
                }
              }
            ?>
            <button class="d-none" type="button" id="btn-view-all"></button>
          </div>
        </div>
      </div>

      <div class="card-lift mb-3" id="amenities">
        <h5 class="section-title" data-i18n="confirmBooking.overview">Tổng quan phòng</h5>
        <?php 
          $description = $room_data['description'] ?? '';
          if(function_exists('t_room_description')){
            $translated_description = t_room_description($description, $current_lang);
          } else {
            $translated_description = $description;
          }
          
          $full_description = nl2br(htmlspecialchars($translated_description, ENT_QUOTES, 'UTF-8'));
          $short_length = 200;
          $is_long = mb_strlen(strip_tags($translated_description)) > $short_length;
          
          if($is_long){
            $short_description = mb_substr(strip_tags($translated_description), 0, $short_length) . '...';
            $short_description_html = nl2br(htmlspecialchars($short_description, ENT_QUOTES, 'UTF-8'));
        ?>
          <div class="room-description-container">
            <div class="text-muted mb-2" id="room-description-short" style="display:block;">
              <?php echo $short_description_html; ?>
            </div>
            <div class="text-muted mb-2" id="room-description-full" style="display:none;">
              <?php echo $full_description; ?>
            </div>
            <button type="button" class="btn btn-link p-0 text-primary text-decoration-none fw-semibold" 
                    id="toggle-description-btn" onclick="toggleRoomDescription()">
              <span data-i18n="confirmBooking.readMore">Xem thêm</span>
              <i class="bi bi-chevron-down ms-1"></i>
            </button>
          </div>
        <?php } else { ?>
          <p class="text-muted mb-3"><?php echo $full_description; ?></p>
        <?php } ?>

        <div class="info-grid mb-3">
          <div class="info-tile">
            <div class="label" data-i18n="confirmBooking.capacity">Sức chứa</div>
            <div class="value"><i class="bi bi-people text-primary me-1"></i><?php echo (int)$room_data['adult']; ?> <span data-i18n="confirmBooking.adults">người lớn</span> + <?php echo (int)$room_data['children']; ?> <span data-i18n="confirmBooking.children">trẻ em</span></div>
          </div>
          <div class="info-tile">
            <div class="label" data-i18n="confirmBooking.area">Diện tích</div>
            <div class="value"><i class="bi bi-bounding-box text-primary me-1"></i><?php echo (int)$room_data['area']; ?> m²</div>
          </div>
          <div class="info-tile">
            <div class="label" data-i18n="confirmBooking.status">Tình trạng</div>
            <div class="value"><i class="bi bi-door-open text-primary me-1"></i><span data-i18n="confirmBooking.available">Còn</span> <?php echo isset($room_data['remaining']) ? (int)$room_data['remaining'] : 0; ?> <span data-i18n="confirmBooking.rooms">phòng</span></div>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <h6 class="fw-bold mb-3"><i class="bi bi-list-check text-primary me-2"></i><span data-i18n="confirmBooking.mainFacilities">Tiện ích chính</span></h6>
            <?php
              // Lấy tiện ích từ database
              $fac_result = select("SELECT f.name, f.icon, f.description FROM facilities f 
                                    INNER JOIN room_facilities rf ON f.id = rf.facilities_id 
                                    WHERE rf.room_id=? 
                                    ORDER BY f.name ASC 
                                    LIMIT 8", [$room_data['id']], "i");
              
              $facilities = [];
              if($fac_result && mysqli_num_rows($fac_result) > 0){
                while($fac = mysqli_fetch_assoc($fac_result)){
                  $facilities[] = $fac;
                }
              }
              
              if(count($facilities) > 0){
                echo '<div class="facilities-grid" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:12px;">';
                foreach($facilities as $fac){
                  $fac_name_raw = $fac['name'] ?? '';
                  $fac_desc_raw = $fac['description'] ?? '';
                  $fac_name = htmlspecialchars(t_facility_name($fac_name_raw, $current_lang), ENT_QUOTES, 'UTF-8');
                  $fac_desc = htmlspecialchars(t_facility_description($fac_desc_raw, $current_lang), ENT_QUOTES, 'UTF-8');
                  
                  // Icon mapping
                  $icon_map = [
                    'Wi-Fi' => 'bi-wifi',
                    'WiFi mi?n ph?' => 'bi-wifi',
                    'Truy?n H?nh' => 'bi-tv',
                    'M?y L?nh' => 'bi-thermometer-snow',
                    'M?y S??i' => 'bi-thermometer-sun',
                    'M?y N??c N?ng' => 'bi-droplet',
                    'M?y S?y T?c' => 'bi-wind',
                    'Minibar' => 'bi-cup-straw',
                    '?m ?un N??c' => 'bi-cup-hot',
                    'Khu L?m Vi?c' => 'bi-laptop',
                    'T? Qu?n ?o' => 'bi-box',
                    'B?n ?i' => 'bi-iron',
                    'H? b?i' => 'bi-water',
                    'Ph?ng gym' => 'bi-activity',
                    'Nh? h?ng' => 'bi-egg-fried',
                    'B?i ?? xe' => 'bi-p-circle',
                    'D?ch v? 24/24 gi?' => 'bi-clock-history',
                    'Spa' => 'bi-flower1',
                  ];
                  
                  $icon_class = $icon_map[$fac_name_raw] ?? 'bi-check-circle';
                  
                  echo '<div class="facility-item p-2 rounded border" style="background:#f8f9fa; transition:all 0.2s;" onmouseover="this.style.transform=\'translateY(-2px)\'; this.style.boxShadow=\'0 4px 8px rgba(0,0,0,0.1)\';" onmouseout="this.style.transform=\'translateY(0)\'; this.style.boxShadow=\'none\';">
                          <div class="d-flex align-items-center gap-2">
                            <i class="bi '.$icon_class.' text-primary" style="font-size:18px;"></i>
                            <div class="flex-grow-1">
                              <div class="fw-semibold small">'.$fac_name.'</div>
                              <div class="text-muted" style="font-size:11px; line-height:1.3;">'.substr($fac_desc, 0, 50).'...</div>
                            </div>
                          </div>
                        </div>';
                }
                echo '</div>';
              } else {
                // Fallback nếu không có dữ liệu
                echo '<ul class="list-unstyled text-muted mb-0">
                        <li class="mb-2"><i class="bi bi-wifi text-primary me-2"></i><span data-i18n="confirmBooking.freeWifi">WiFi miễn phí</span></li>
                        <li class="mb-2"><i class="bi bi-thermometer-sun text-primary me-2"></i><span data-i18n="confirmBooking.fullAC">Điều hòa đầy đủ</span></li>
                        <li class="mb-2"><i class="bi bi-egg-fried text-primary me-2"></i><span data-i18n="confirmBooking.optionalBreakfast">Bữa sáng tuỳ chọn</span></li>
                        <li class="mb-2"><i class="bi bi-clock-history text-primary me-2"></i><span data-i18n="confirmBooking.reception24">Lễ tân 24/7</span></li>
                      </ul>';
              }
            ?>
          </div>
          <div class="col-md-6">
            <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt text-primary me-2"></i><span data-i18n="confirmBooking.nearby">Lân cận</span></h6>
            <ul class="list-unstyled text-muted mb-0">
              <li class="mb-2 d-flex align-items-center"><i class="bi bi-shop text-primary me-2"></i><span data-i18n="confirmBooking.nightMarket">Chợ đêm / Chợ trung tâm</span> <span class="ms-auto text-dark fw-semibold">1.1 km</span></li>
              <li class="mb-2 d-flex align-items-center"><i class="bi bi-building text-primary me-2"></i><span data-i18n="confirmBooking.cathedral">Nhà thờ lớn</span> <span class="ms-auto text-dark fw-semibold">1.6 km</span></li>
              <li class="mb-2 d-flex align-items-center"><i class="bi bi-train-front text-primary me-2"></i><span data-i18n="confirmBooking.station">Ga trung tâm</span> <span class="ms-auto text-dark fw-semibold">2.0 km</span></li>
              <li class="mb-2 d-flex align-items-center"><i class="bi bi-cart text-primary me-2"></i><span data-i18n="confirmBooking.supermarket">Siêu thị & tiện ích</span> <span class="ms-auto text-dark fw-semibold">400 m</span></li>
            </ul>
          </div>
        </div>
      </div>

      <div class="card-lift mt-3 reviews-card" id="reviews-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold mb-0"><i class="bi bi-chat-quote text-warning me-2"></i><span data-i18n="confirmBooking.reviewsFromGuests">Đánh giá từ khách</span></h6>
          <span class="badge bg-light text-dark border">⭐ <?php echo $rating_stats['avg']; ?> (<?php echo $rating_stats['count']; ?>)</span>
        </div>
        
        
        <div id="reviews-container">
          <?php
            $user_id_for_like = isset($_SESSION['uId']) ? $_SESSION['uId'] : 0;
            
            // Kiểm tra bảng review_helpful có tồn tại không
            $check_table = @mysqli_query($con, "SHOW TABLES LIKE 'review_helpful'");
            $has_helpful_table = $check_table && mysqli_num_rows($check_table) > 0;
            
            // Kiểm tra các cột admin_reply có tồn tại không
            $cols_check = mysqli_query($con, "SHOW COLUMNS FROM `rating_review`");
            $existing_cols = [];
            while($col = mysqli_fetch_assoc($cols_check)){
              $existing_cols[] = $col['Field'];
            }
            $has_admin_reply_col = in_array('admin_reply', $existing_cols);
            $has_admin_reply_date_col = in_array('admin_reply_date', $existing_cols);
            
            // Xây dựng SELECT với các cột có sẵn
            $select_cols = ['rr.*', 'uc.name AS uname', 'uc.profile'];
            
            if($has_admin_reply_col) $select_cols[] = 'rr.admin_reply';
            if($has_admin_reply_date_col) $select_cols[] = 'rr.admin_reply_date';
            
            if($has_helpful_table){
              $select_cols[] = "(SELECT COUNT(*) FROM review_helpful WHERE review_id = rr.sr_no AND user_id = ?) AS user_helpful";
              $review_q = "SELECT ".implode(', ', $select_cols)."
                           FROM rating_review rr
                           LEFT JOIN user_cred uc ON rr.user_id = uc.id
                           WHERE rr.room_id='{$room_data['id']}'
                           ORDER BY rr.sr_no DESC LIMIT 15";
              $review_res = select($review_q, [$user_id_for_like], 'i');
            } else {
              $review_q = "SELECT ".implode(', ', $select_cols)."
                           FROM rating_review rr
                           LEFT JOIN user_cred uc ON rr.user_id = uc.id
                           WHERE rr.room_id='{$room_data['id']}'
                           ORDER BY rr.sr_no DESC LIMIT 15";
              $review_res = select($review_q, [], '');
            }

            if(mysqli_num_rows($review_res)==0){
              echo "<div class='text-center py-4'>
                      <i class='bi bi-inbox text-muted' style='font-size: 3rem;'></i>
                      <p class='text-muted mt-3 mb-0' data-i18n='confirmBooking.noReviews'>Chưa có đánh giá nào!</p>
                    </div>";
            } 
            else {
              while($rv = mysqli_fetch_assoc($review_res)){
                $uname = !empty($rv['uname']) ? htmlspecialchars($rv['uname'], ENT_QUOTES, 'UTF-8') : 'Khách hàng';
                $review_text = htmlspecialchars($rv['review'], ENT_QUOTES, 'UTF-8');
                // Chỉ hiển thị ảnh nếu có profile, không dùng user.png
                $profile_img = '';
                $has_profile_img = false;
                if(!empty($rv['profile']) && $rv['profile'] != 'user.png') {
                    $profile_img = USERS_IMG_PATH.$rv['profile'];
                    $has_profile_img = true;
                }
                
                // Tạo avatar từ chữ cái đầu nếu không có ảnh
                $user_initial = 'U';
                if(!empty($uname)) {
                    $user_initial = mb_strtoupper(mb_substr(trim($uname), 0, 1, 'UTF-8'), 'UTF-8');
                }
                $avatar_html = '';
                if($has_profile_img) {
                    $avatar_html = "<img src='{$profile_img}' class='rounded-circle review-avatar' alt='{$uname}' onerror=\"this.onerror=null; this.style.display='none'; const parent=this.parentElement; if(parent && !parent.querySelector('.review-avatar-initial')) { const fallback=document.createElement('div'); fallback.className='review-avatar-initial'; fallback.style.cssText='width: 56px; height: 56px; border-radius: 50%; background-color: #4A90E2; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 20px; border: 3px solid #e5e7eb;'; fallback.textContent='{$user_initial}'; parent.appendChild(fallback); }\">";
                } else {
                    $avatar_html = "<div class='review-avatar-initial' style='width: 56px; height: 56px; border-radius: 50%; background-color: #4A90E2; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 20px; border: 3px solid #e5e7eb;'>{$user_initial}</div>";
                }
                
                $review_id = (int)$rv['sr_no'];
                $helpful_count = (int)($rv['helpful_count'] ?? 0);
                $user_helpful = isset($rv['user_helpful']) ? (int)$rv['user_helpful'] > 0 : false;
                $review_date = date('d/m/Y', strtotime($rv['datentime']));
                
                $stars = '';
                for($i=1;$i<=5;$i++){
                  if($i <= $rv['rating']){
                    $stars .= '<i class="bi bi-star-fill text-warning"></i>';
                  } else {
                    $stars .= '<i class="bi bi-star text-muted"></i>';
                  }
                }
                
                // Xử lý ảnh review
                $images_html = '';
                if(!empty($rv['images'])){
                  $images_data = json_decode($rv['images'], true);
                  if(json_last_error() === JSON_ERROR_NONE && is_array($images_data) && !empty($images_data)){
                    $images_html = '<div class="review-images mt-3 d-flex gap-2 flex-wrap">';
                    foreach($images_data as $img){
                      $img_path = ltrim(str_replace('../', '', $img), '/');
                      if(!empty($img_path) && !filter_var($img_path, FILTER_VALIDATE_URL)){
                        if(strpos($img_path, 'images/') !== 0){
                          $img_path = 'images/' . ltrim($img_path, '/');
                        }
                      }
                      $images_html .= "<img src='{$img_path}' class='review-img-thumb' onclick='openImageModal(\"{$img_path}\")' alt='Review image'>";
                    }
                    $images_html .= '</div>';
                  }
                }
                
                $helpful_class = $user_helpful ? 'active' : '';
                $helpful_icon = $user_helpful ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up';
                
                // Xử lý phản hồi từ owner/admin
                $admin_reply_html = '';
                if($has_admin_reply_col && !empty($rv['admin_reply'])){
                  $admin_reply_text = htmlspecialchars($rv['admin_reply'], ENT_QUOTES, 'UTF-8');
                  $admin_reply_date = '';
                  if($has_admin_reply_date_col && !empty($rv['admin_reply_date'])){
                    $admin_reply_date = date('d/m/Y H:i', strtotime($rv['admin_reply_date']));
                  }
                  $admin_reply_html = "
                    <div class='admin-reply mt-3 p-3 bg-light rounded border-start border-primary border-3'>
                      <div class='d-flex justify-content-between align-items-start mb-2'>
                        <strong class='text-primary'>
                          <i class='bi bi-reply-fill me-1'></i><span data-i18n='confirmBooking.adminReply'>Phản hồi từ chủ khách sạn</span>:
                        </strong>
                        ".(!empty($admin_reply_date) ? "<small class='text-muted'>{$admin_reply_date}</small>" : "")."
                      </div>
                      <p class='mb-0 text-muted' style='line-height: 1.7;'>{$admin_reply_text}</p>
                    </div>";
                }
                
                echo "
                  <div class='review-item mb-4 pb-4 border-bottom' data-review-id='{$review_id}'>
                    <div class='d-flex align-items-start gap-3 mb-3'>
                      {$avatar_html}
                      <div class='flex-grow-1'>
                        <div class='d-flex justify-content-between align-items-start mb-1'>
                          <h6 class='mb-0 fw-bold'>{$uname}</h6>
                          <span class='text-muted small'>{$review_date}</span>
                        </div>
                        <div class='mb-2'>{$stars}</div>
                      </div>
                    </div>
                    <p class='mb-0 text-muted' style='line-height: 1.7;'>{$review_text}</p>
                    {$images_html}
                    {$admin_reply_html}
                    <div class='d-flex justify-content-end align-items-center mt-3 pt-2 border-top'>
                      <button class='btn-helpful btn btn-sm btn-outline-secondary border-0 p-2 {$helpful_class}' 
                              onclick='toggleHelpful({$review_id}, this)' 
                              data-i18n-title='confirmBooking.helpful'
                              title='Đánh dấu hữu ích'>
                        <i class='bi {$helpful_icon} me-1'></i>
                        <span class='helpful-count'>{$helpful_count}</span>
                      </button>
                    </div>
                  </div>";
              }
            }
          ?>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card-lift mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div>
            <p class="text-muted mb-0 small" data-i18n="confirmBooking.step2And3">Bước 2 & 3</p>
            <h5 class="fw-bold mb-0" data-i18n="confirmBooking.bookingDetails">Chi tiết đặt phòng</h5>
          </div>
          <span class="badge-ghost" data-i18n="confirmBooking.discountApplied">Giá ưu đãi đã áp dụng</span>
        </div>

        <form action="pay_now.php" method="POST" id="booking_form">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-person-fill text-primary"></i>
                <span data-i18n="confirmBooking.fullName">Họ và tên</span>
              </label>
              <div class="input-icon-wrapper">
                <input name="name" value="<?php echo htmlspecialchars($user_data['name'] ?? '', ENT_QUOTES); ?>" type="text" class="form-control rounded shadow-none" required data-i18n-placeholder="confirmBooking.fullName">
                <i class="bi bi-person-fill input-icon"></i>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-telephone-fill text-primary"></i>
                <span data-i18n="confirmBooking.phone">Số điện thoại</span>
              </label>
              <div class="input-icon-wrapper">
                <input name="phonenum" value="<?php echo htmlspecialchars($user_data['phonenum'] ?? '', ENT_QUOTES); ?>" type="tel" class="form-control rounded shadow-none" required data-i18n-placeholder="confirmBooking.phone">
                <i class="bi bi-telephone-fill input-icon"></i>
              </div>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-geo-alt-fill text-primary"></i>
                <span data-i18n="confirmBooking.address">Địa chỉ</span>
              </label>
              <div class="input-icon-wrapper">
                <textarea name="address" class="form-control rounded shadow-none" rows="2" required data-i18n-placeholder="confirmBooking.address"><?php echo htmlspecialchars($user_data['address'] ?? '', ENT_QUOTES); ?></textarea>
                <i class="bi bi-geo-alt-fill input-icon" style="top: 20px;"></i>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-calendar-check-fill text-primary"></i>
                <span data-i18n="confirmBooking.checkInDate">Ngày nhận phòng</span>
              </label>
              <div class="input-icon-wrapper">
                <input name="checkin" id="checkin_date" onchange="check_availability()" type="date" class="form-control rounded shadow-none" required min="<?php echo date('Y-m-d'); ?>">
                <i class="bi bi-calendar-check-fill input-icon"></i>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-calendar-x-fill text-primary"></i>
                <span data-i18n="confirmBooking.checkOutDate">Ngày trả phòng</span>
              </label>
              <div class="input-icon-wrapper">
                <input name="checkout" id="checkout_date" onchange="check_availability()" type="date" class="form-control rounded shadow-none" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                <i class="bi bi-calendar-x-fill input-icon"></i>
              </div>
            </div>

            <div class="col-12">
              <div id="warn-banner" class="warn-banner"></div>
            </div>

            <div class="col-12 mb-2">
              <div class="avail-status text-muted small" id="avail_status">
                <span class="dot bg-secondary"></span>
                <span data-i18n="confirmBooking.checkAvailability">Kiểm tra phòng trống & tính phí tự động sau khi chọn ngày.</span>
              </div>
              <div id="info_loader" class="spinner-border text-primary d-none" role="status" style="width:22px;height:22px;"></div>
            </div>

            <div class="col-12 mb-3">
              <div class="alert alert-warning py-2 px-3 mb-0">
                <div class="fw-semibold small mb-1"><i class="bi bi-calendar-check me-1"></i><span data-i18n="confirmBooking.upcomingBookings">Trạng thái đặt phòng sắp tới</span></div>
                <?php if(empty($booked_ranges)): ?>
                  <div class="text-muted small" data-i18n="confirmBooking.noBookings">Chưa có lịch đặt nào. Bạn có thể chọn ngày phù hợp.</div>
                <?php else: ?>
                  <ul class="mb-0 text-muted small ps-3">
                    <?php 
                      foreach($booked_ranges as $br): 
                      $ci = date('d/m/Y', strtotime($br['check_in']));
                      $co = date('d/m/Y', strtotime($br['check_out']));
                      $st = $br['status'] === 'pending' 
                        ? ($current_lang === 'en' ? 'pending confirmation' : 'chờ xác nhận')
                        : ($current_lang === 'en' ? 'booked' : 'đã đặt');
                    ?>
                      <li><?php echo $ci; ?> - <?php echo $co; ?> (<?php echo $st; ?>)</li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
              </div>
            </div>

            <div class="col-12 mb-3">
              <label class="form-label fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-tag-fill text-primary"></i>
                <span data-i18n="confirmBooking.promoCode">Mã giảm giá</span>
              </label>
              <div class="input-group" id="promo-input-group">
                <div class="input-icon-wrapper flex-grow-1">
                  <input type="text" id="promo_code" class="form-control rounded shadow-none" data-i18n-placeholder="confirmBooking.enterPromoCode">
                  <i class="bi bi-tag-fill input-icon"></i>
                </div>
                <button class="btn btn-outline-dark fw-semibold" type="button" onclick="applyPromo()">
                  <i class="bi bi-check-circle-fill me-1"></i><span data-i18n="confirmBooking.apply">Áp dụng</span>
                </button>
              </div>
              <div id="promo_msg" class="small mt-1 text-success d-none"></div>
              <div id="promo_status" class="small mt-1 text-muted"></div>
              <div class="dropdown mt-2">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="saved_promos_btn" data-bs-toggle="dropdown" aria-expanded="false">
                  <span data-i18n="confirmBooking.selectSavedCode">Chọn mã đã lưu</span>
                </button>
                <ul class="dropdown-menu" id="saved_promos_menu" aria-labelledby="saved_promos_btn"></ul>
              </div>
              <div class="text-muted small mt-1" data-i18n="confirmBooking.savedFromHome">Mã đã lưu từ trang chủ (nút "Lưu" ở danh sách mã).</div>
            </div>

            <div class="col-12 mb-3">
              <label class="form-label fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-chat-left-text-fill text-primary"></i>
                <span data-i18n="confirmBooking.specialRequest">Yêu cầu đặc biệt (tuỳ chọn)</span>
              </label>
              <div class="input-icon-wrapper">
                <textarea name="special_request" class="form-control rounded shadow-none" rows="2" data-i18n-placeholder="confirmBooking.specialRequestPlaceholder"></textarea>
                <i class="bi bi-chat-left-text-fill input-icon" style="top: 20px;"></i>
              </div>
            </div>

            <div class="col-12 mb-3 d-flex align-items-center gap-2">
              <!-- loader moved to status row above -->
              <span class="text-muted small" data-i18n="confirmBooking.checkAvailability">Kiểm tra phòng trống & tính phí tự động sau khi chọn ngày.</span>
            </div>

            <div class="col-12 mb-3">
              <div class="summary-card">
                <div class="summary-line"><span data-i18n="confirmBooking.roomPrice">Giá phòng</span><span id="sum_base">0 VND</span></div>
                <div class="summary-line <?php echo ($loyalty_rate > 0) ? '' : 'd-none'; ?>" id="loyalty_line">
                  <span><span data-i18n="confirmBooking.loyaltyDiscount">Giảm hạng</span> <?php echo htmlspecialchars($loyalty_label); ?> (<?php echo $loyalty_rate; ?>%)</span><span id="sum_loyalty">- 0 VND</span>
                </div>
                <div class="summary-line"><span data-i18n="confirmBooking.tax">Thuế (8%)</span><span id="sum_tax">0 VND</span></div>
                <div class="summary-line"><span data-i18n="confirmBooking.serviceFee">Phí dịch vụ (2%)</span><span id="sum_svc">0 VND</span></div>
                <div class="summary-line"><span data-i18n="confirmBooking.discount">Giảm giá</span><span id="sum_discount">- 0 VND</span></div>
                <div class="summary-line total"><span data-i18n="confirmBooking.total">Tổng thanh toán</span><span id="sum_total">0 VND</span></div>
              </div>
              <input type="hidden" id="loyalty_rate" value="<?php echo $loyalty_rate; ?>">
              <input type="hidden" name="promo_rate" id="promo_rate" value="0">
              <input type="hidden" name="promo_code_value" id="promo_code_value" value="">
              <input type="hidden" name="promo_amount" id="promo_amount" value="0">
              <input type="hidden" name="payment_confirmed" id="payment_confirmed" value="0">
              <input type="hidden" name="qr_payload" id="qr_payload" value="">
              <input type="hidden" name="pay_total" id="pay_total" value="0">
            </div>

            <div class="col-12 mb-3">
              <div class="qr-box d-flex align-items-center gap-3 flex-wrap" id="qr-box-container" data-double-tap-text="" data-copied-text="">
                <div style="position: relative;">
                  <img
                    id="qr-image"
                    src="<?php echo SITE_URL.'images/pay/QR.jpg'; ?>"
                    data-static="<?php echo SITE_URL.'images/pay/QR.jpg'; ?>"
                    data-bank="BIDV"
                    data-acc="73510001284830"
                    data-name="PHAM MINH MAN"
                    alt="QR thanh toán"
                    style="width:160px;height:160px;border:1px solid #e5e7eb;border-radius:12px;object-fit:contain;background:#fff;">
                  <button type="button" class="btn btn-sm btn-light position-absolute bottom-0 end-0 m-2" id="copy-qr-btn" style="display: none; border-radius: 8px; padding: 4px 8px; font-size: 11px;" title="Sao chép thông tin">
                    <i class="bi bi-copy"></i>
                  </button>
                </div>
                <div class="flex-grow-1">
                  <div class="text-muted small" data-i18n="confirmBooking.scanQR">Quét mã bằng ứng dụng ngân hàng/MoMo/ZaloPay.</div>
                  <div class="fw-semibold mt-2"><span data-i18n="confirmBooking.amount">Số tiền</span>: <span id="qr-amount">0 VND</span></div>
                  <div id="paid-status" class="small mt-2 text-danger" data-i18n="confirmBooking.selectDatesAndConfirm">Chọn ngày và xác nhận QR để mở nút đặt phòng.</div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <p id="pay_info" class="text-danger fw-semibold" data-i18n="confirmBooking.paymentInfo">Chọn ngày nhận/trả phòng, sau đó chạm đúp vào mã QR để mở nút thanh toán.</p>
              <div class="px-3 py-2 rounded-3 text-dark small border shadow-sm d-none" id="submit-hint">
                <i class="bi bi-info-circle me-1 text-danger"></i><span data-i18n="confirmBooking.paymentButtonHint">Nút thanh toán sẽ mở khi: đã chọn ngày hợp lệ, còn phòng, và xác nhận đã quét QR.</span>
              </div>
              <button name="pay_now" class="btn custom-bg w-100 text-white fw-bold py-3 rounded shadow-none d-none" disabled style="background: linear-gradient(135deg, #0f5d7a 0%, #0b4156 100%); border: none; font-size: 16px; transition: all 0.3s ease;">
                <i class="bi bi-credit-card-fill me-2"></i><span data-i18n="confirmBooking.payNow">Thanh toán ngay</span>
              </button>
              <style>
                button[name="pay_now"]:not(:disabled):hover {
                  transform: translateY(-2px);
                  box-shadow: 0 8px 24px rgba(15,93,122,0.4);
                  background: linear-gradient(135deg, #0b4156 0%, #0a2d3d 100%) !important;
                }
              </style>
            </div>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>

<?php 
  // Kiểm tra thông báo review thành công
  if(isset($_GET['review_status']) && $_GET['review_status'] == 'true'){
    echo "<script>
      // Hiển thị thông báo một lần
      if(typeof showToast === 'function'){
        showToast('success', 'Cảm ơn bạn đã để lại đánh giá!', 3000);
      }
      // Xóa tham số khỏi URL để không hiển thị lại khi refresh
      if(window.history && window.history.replaceState){
        const url = new URL(window.location);
        url.searchParams.delete('review_status');
        window.history.replaceState({}, '', url);
      }
    </script>";
  }
?>

<?php require('inc/footer.php'); ?>
<?php require('inc/modals.php'); ?>

<!-- Back to top -->
<button type="button" id="btn-back-top" class="back-to-top" aria-label="Lên đầu trang">
  <i class="bi bi-arrow-up-short fs-4"></i>
</button>

<script>
const booking_form = document.getElementById('booking_form');
const info_loader = document.getElementById('info_loader');
const pay_info = document.getElementById('pay_info');
const availStatus = document.getElementById('avail_status');
const promo_code = document.getElementById('promo_code');
const promo_msg  = document.getElementById('promo_msg');
const promo_status = document.getElementById('promo_status');
const loyalty_rate_input = document.getElementById('loyalty_rate');
const promo_rate_input = document.getElementById('promo_rate');
const promo_code_hidden = document.getElementById('promo_code_value');
const promo_amount_input = document.getElementById('promo_amount');
const pay_total_input = document.getElementById('pay_total');
const sumDiscountEl = document.getElementById('sum_discount');
const sumLoyaltyEl = document.getElementById('sum_loyalty');
const loyaltyLine = document.getElementById('loyalty_line');
const payment_confirmed_input = document.getElementById('payment_confirmed');
const qr_payload_input = document.getElementById('qr_payload');
const qrImg = document.getElementById('qr-image');
const qrAmountTxt = document.getElementById('qr-amount');
const paidStatus = document.getElementById('paid-status');
const confirmPaidBtn = document.getElementById('confirm-paid-btn');
const savedPromosBtn = document.getElementById('saved_promos_btn');
const savedPromosMenu = document.getElementById('saved_promos_menu');
const payBtn = booking_form.elements['pay_now'];
const submitHint = document.getElementById('submit-hint');
const checkinEl = booking_form.elements['checkin'];
const checkoutEl = booking_form.elements['checkout'];
// Track xem input đã được touched chưa
let checkinTouched = false;
let checkoutTouched = false;
const warnBanner = document.getElementById('warn-banner');
const backTopBtn = document.getElementById('btn-back-top');

// Danh sách các ngày đã được đặt (từ PHP)
const bookedRanges = <?php echo json_encode($booked_ranges); ?>;
console.log('Booked ranges loaded:', bookedRanges);

// Hàm kiểm tra overlap: khoảng thời gian user chọn có overlap với booking đã có không
function hasOverlapWithBookings(checkin, checkout, bookedRanges) {
  if(!checkin || !checkout || !bookedRanges || bookedRanges.length === 0) {
    return false;
  }
  
  // Parse dates - đảm bảo format YYYY-MM-DD, set time về 00:00:00
  const userCI = new Date(checkin + 'T00:00:00');
  const userCO = new Date(checkout + 'T00:00:00');
  
  if(isNaN(userCI.getTime()) || isNaN(userCO.getTime())) {
    console.error('Invalid date format:', checkin, checkout);
    return false;
  }
  
  // Kiểm tra từng booking
  for(let range of bookedRanges) {
    if(!range.check_in || !range.check_out) continue;
    
    // Parse booking dates - đảm bảo format YYYY-MM-DD
    const bookCI = new Date(range.check_in + 'T00:00:00');
    const bookCO = new Date(range.check_out + 'T00:00:00');
    
    if(isNaN(bookCI.getTime()) || isNaN(bookCO.getTime())) {
      console.warn('Invalid booking date:', range);
      continue;
    }
    
    // Overlap nếu: (booking_check_in < user_check_out) AND (booking_check_out > user_check_in)
    // Logic: Hai khoảng thời gian overlap nếu chúng có ít nhất 1 ngày chung
    // Ví dụ: 
    // - Booking 18/12-20/12 (18,19), User chọn 19/12-21/12 (19,20) => Overlap (ngày 19)
    // - Booking 18/12-20/12 (18,19), User chọn 21/12-22/12 => Không overlap
    // - Booking 18/12-20/12 (18,19), User chọn 16/12-18/12 (16,17) => Không overlap (checkout của user = checkin của booking, không overlap)
    const hasOverlap = bookCI < userCO && bookCO > userCI;
    
    if(hasOverlap) {
      console.log('OVERLAP DETECTED:', {
        booking: range.check_in + ' to ' + range.check_out,
        user: checkin + ' to ' + checkout,
        bookCI: bookCI.toISOString().split('T')[0],
        bookCO: bookCO.toISOString().split('T')[0],
        userCI: userCI.toISOString().split('T')[0],
        userCO: userCO.toISOString().split('T')[0]
      });
      return true;
    }
  }
  
  return false;
}

let qrUnlocked = false;
let qrUnlockPending = false;
let activePromo = null;
let currentDays = 0;
let availabilityOk = false;
let savedCodes = [];
let availabilityTimer = null;

const roomPrice = <?php echo (int)$effective_price; ?>;
const taxRate = 0.08;
const svcRate = 0.02;
const loyaltyRate = loyalty_rate_input ? (parseFloat(loyalty_rate_input.value) || 0) : 0;

const promoData = <?php echo json_encode($promo_js); ?>;
const promoTable = {};
promoData.forEach(p => {
  const code = (p.code || '').toUpperCase();
  if(!code) return;
  promoTable[code] = {
    type: p.type || 'percent',
    value: parseFloat(p.value) || 0,
    min: parseInt(p.min || 0),
    cap: (p.cap === null || p.cap === undefined) ? null : parseInt(p.cap),
    note: p.label || code,
    expires_at: p.expires_at || null
  };
});

function money(v){ return (v || 0).toLocaleString('vi-VN') + ' VND'; }
function hasDatesSelected(){ return !!(checkinEl.value && checkoutEl.value); }
function setAvailabilityStatus(msg, type){
  if(!availStatus) return;
  const dot = availStatus.querySelector('.dot');
  const text = availStatus.querySelector('span:last-child');
  if(dot){
    dot.className = 'dot';
    dot.style.background = (type === 'ok' || type==='done') ? '#16a34a' : (type === 'error' ? '#dc2626' : '#6b7280');
    if(type === 'done'){
      dot.classList.add('checked');
    }
  }
  if(text){
    text.textContent = msg;
    text.className = type === 'ok' || type==='done' ? 'text-success' : (type === 'error' ? 'text-danger' : 'text-muted');
  }
}
function updateReadyStatus(){
  if(!availStatus) return;
  const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
  if(availabilityOk && qrUnlocked){
    const msg = currentLang === 'en' ? 'Checked & ready to book' : 'Đã kiểm tra & sẵn sàng đặt';
    setAvailabilityStatus(msg, 'done');
  } else if(availabilityOk){
    const ci = checkinEl.value;
    const co = checkoutEl.value;
    const msg = currentLang === 'en' 
      ? `Checked: room available ${ci} → ${co}`
      : `Đã kiểm tra: còn phòng ${ci} → ${co}`;
    setAvailabilityStatus(msg, 'ok');
  }
}
function notify(type,msg){
  if(typeof showToast === 'function'){
    showToast(type,msg, 3000);
  } else {
    alert(msg);
  }
}
function fieldsFilled(){
  const f = booking_form.elements;
  return f['name'].value.trim() && f['phonenum'].value.trim() && f['address'].value.trim() && hasDatesSelected();
}
function clearInvalid(el){
  if(el){ el.classList.remove('is-invalid'); }
}
function highlightMissingFields(){
  const f = booking_form.elements;
  const missing = [];
  ['name','phonenum','address'].forEach(key=>{
    const el = f[key];
    if(!el) return;
    if(!el.value.trim()){
      el.classList.add('is-invalid');
      missing.push(el);
    } else {
      el.classList.remove('is-invalid');
    }
  });
  if(!hasDatesSelected()){
    checkinEl.classList.add('is-invalid');
    checkoutEl.classList.add('is-invalid');
    missing.push(checkinEl);
  } else {
    checkinEl.classList.remove('is-invalid');
    checkoutEl.classList.remove('is-invalid');
  }
  if(missing.length){
    missing[0].focus();
    return false;
  }
  return true;
}

function calcNights(ci, co){
  if(!ci || !co) return 0;
  const start = new Date(ci);
  const end = new Date(co);
  const diff = (end - start) / (1000*60*60*24);
  return diff > 0 ? diff : 0;
}

function formatExpiryStatus(promo){
  const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
  if(!promo || !promo.expires_at){ 
    const text = currentLang === 'en' ? 'Still valid' : 'Còn hiệu lực';
    return {text: text, cls:'text-muted'}; 
  }
  const exp = new Date(promo.expires_at);
  exp.setHours(23,59,59,999);
  const diff = exp - new Date();
  if(diff < 0) {
    const text = currentLang === 'en' ? 'Expired' : 'Đã hết hạn';
    return {text: text, cls:'text-danger'};
  }
  const days = Math.ceil(diff / (1000*60*60*24));
  let cls = 'text-success';
  if(days <= 2) cls = 'text-danger';
  else if(days <= 5) cls = 'text-warning';
  const text = currentLang === 'en'
    ? `Expires in ${days} days (${exp.toLocaleDateString('en-US')})`
    : `Hết hạn sau ${days} ngày (${exp.toLocaleDateString('vi-VN')})`;
  return {text: text, cls};
}

function discountForCode(code, totalBefore){
  const promo = promoTable[code];
  if(!promo) return 0;
  if(promo.expires_at){
    const exp = new Date(promo.expires_at);
    exp.setHours(23,59,59,999);
    if(exp < new Date()) return 0;
  }
  if(totalBefore < (promo.min || 0)) return 0;
  let amt = promo.type === 'flat' ? promo.value : totalBefore * (promo.value/100);
  if(promo.cap){ amt = Math.min(amt, promo.cap); }
  return Math.max(0, Math.round(amt));
}

function findBestPromo(totalBefore){
  let best = null;
  Object.keys(promoTable).forEach(code => {
    const amount = discountForCode(code, totalBefore);
    if(amount > 0 && (!best || amount > best.amount)){
      best = {code, amount, promo: promoTable[code]};
    }
  });
  return best;
}

function setPromoInfo(note, valid){
  if(!promo_msg) return;
  if(!note){
    promo_msg.classList.add('d-none');
    return;
  }
  promo_msg.classList.remove('d-none');
  promo_msg.classList.toggle('text-danger', !valid);
  promo_msg.classList.toggle('text-success', !!valid);
  promo_msg.textContent = note;
}

function renderSavedPromos(totalBefore){
  if(!savedPromosMenu || !savedPromosBtn) return;
  savedPromosMenu.innerHTML = '';
  if(!savedCodes.length){
    const noSavedText = window.i18n ? window.i18n.translate('confirmBooking.noSavedPromos') : 'Chưa lưu mã';
    savedPromosMenu.innerHTML = `<li><span class='dropdown-item text-muted small'>${noSavedText}</span></li>`;
    savedPromosBtn.classList.add('disabled');
    return;
  }
  savedPromosBtn.classList.remove('disabled');
  const list = savedCodes.map(code => ({
    code,
    amount: discountForCode(code, totalBefore)
  })).sort((a,b) => b.amount - a.amount);

  const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
  const maxText = window.i18n ? window.i18n.translate('confirmBooking.maxDiscount') : 'tối đa';
  const minOrderText = window.i18n ? window.i18n.translate('confirmBooking.minOrder') : 'Đơn tối thiểu';
  const noMinText = window.i18n ? window.i18n.translate('confirmBooking.noMinRequired') : 'Không yêu cầu tối thiểu';
  const savedCodeText = window.i18n ? window.i18n.translate('confirmBooking.savedCode') : 'Mã đã lưu';
  
  // Map promo labels
  const labelMap = {
    'Thanh toán QR': currentLang === 'en' ? 'QR Payment' : 'Thanh toán QR',
    'Điểm đến hot': currentLang === 'en' ? 'Hot Destination' : 'Điểm đến hot',
    'Ưu đãi ngân hàng': currentLang === 'en' ? 'Bank Offer' : 'Ưu đãi ngân hàng',
    'Sắp hết mã': currentLang === 'en' ? 'Running out' : 'Sắp hết mã',
    'Hết hạn sau 2 ngày': currentLang === 'en' ? 'Expires in 2 days' : 'Hết hạn sau 2 ngày',
    'Có hạn': currentLang === 'en' ? 'Limited' : 'Có hạn',
  };

  list.forEach(item => {
    const meta = promoTable[item.code] || {};
    const rule = meta.type === 'flat'
      ? `${(meta.value||0).toLocaleString('vi-VN')} VND`
      : `${meta.value || 0}%`;
    const capTxt = meta.cap ? ` - ${maxText} ${meta.cap.toLocaleString('vi-VN')} VND` : '';
    const minTxt = meta.min ? `${minOrderText} ${meta.min.toLocaleString('vi-VN')} VND` : noMinText;
    const noteTxt = labelMap[meta.note] || meta.note || savedCodeText;

    const li = document.createElement('li');
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'dropdown-item';
    const amountTxt = item.amount > 0 ? `- ${item.amount.toLocaleString('vi-VN')} VND` : '';
    btn.innerHTML = `
      <div class="fw-semibold">${item.code} <span class="text-success small">${amountTxt}</span></div>
      <div class="text-muted small">${noteTxt} • ${rule}${capTxt}</div>
      <div class="text-muted small">${minTxt}</div>
    `;
    btn.addEventListener('click', () => {
      promo_code.value = item.code;
      applyPromo();
    });
    li.appendChild(btn);
    savedPromosMenu.appendChild(li);
  });
}

function loadSavedPromos(totalBefore){
  if(!savedPromosMenu) return;
  fetch('ajax/promos_saved.php?list=1')
    .then(r => r.json())
    .then(resp => {
      if(resp.status === 'ok'){
        savedCodes = resp.codes || [];
        renderSavedPromos(totalBefore);
      }
    })
    .catch(()=>{});
}

function getDiscount(totalBefore){
  const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
  if(!activePromo) return {amount:0, note:'', valid:false};
  if(activePromo.expires_at){
    const exp = new Date(activePromo.expires_at);
    exp.setHours(23,59,59,999);
    if(exp < new Date()){
      const note = currentLang === 'en' ? 'Code expired' : 'Mã đã hết hạn';
      return {amount:0, note: note, valid:false};
    }
  }
  if(totalBefore < (activePromo.min || 0)){
    const note = currentLang === 'en'
      ? `Minimum order ${(activePromo.min || 0).toLocaleString('vi-VN')} VND to apply code`
      : `Đơn tối thiểu ${(activePromo.min || 0).toLocaleString('vi-VN')} VND để áp dụng mã`;
    return {amount:0, note: note, valid:false};
  }
  let amount = activePromo.type === 'flat' ? activePromo.value : totalBefore * (activePromo.value / 100);
  if(activePromo.cap){ amount = Math.min(amount, activePromo.cap); }
  const appliedNote = currentLang === 'en'
    ? `Code ${promo_code_hidden.value || ''} applied`
    : `Đã áp dụng mã ${promo_code_hidden.value || ''}`;
  return {
    amount: Math.max(0, Math.round(amount)),
    note: activePromo.note || appliedNote,
    valid: true
  };
}

function computeTotals(days){
  const nights = Math.max(0, days || 0);
  const base = nights * roomPrice;
  const loyaltyDiscount = Math.round(base * (loyaltyRate/100));
  const baseAfterLoyalty = Math.max(0, base - loyaltyDiscount);
  const tax = Math.round(baseAfterLoyalty * taxRate);
  const svc = Math.round(baseAfterLoyalty * svcRate);
  const totalBefore = baseAfterLoyalty + tax + svc;

  if(!activePromo && promoData.length){
    const best = findBestPromo(totalBefore);
    if(best){
      activePromo = best.promo;
      promo_code_hidden.value = best.code;
      promo_code.value = best.code;
      const expInfo = formatExpiryStatus(activePromo);
      if(promo_status){
        promo_status.textContent = expInfo.text;
        promo_status.className = `small ${expInfo.cls}`;
      }
    }
  }

  const promoCheck = getDiscount(totalBefore);
  const total = Math.max(0, totalBefore - promoCheck.amount);

  setPromoInfo(promoCheck.note, promoCheck.valid);

  return {
    base,
    loyalty: loyaltyDiscount,
    tax,
    svc,
    total,
    totalBefore,
    discount: promoCheck.amount,
    days: nights
  };
}

function animateNumber(el, targetText) {
  if(!el) return;
  const currentText = el.textContent || '';
  if(currentText === targetText) return;
  
  // Extract number from current and target
  const currentNum = parseInt(currentText.replace(/[^\d]/g, '')) || 0;
  const targetNum = parseInt(targetText.toString().replace(/[^\d]/g, '')) || 0;
  
  if(currentNum === targetNum) {
    el.textContent = targetText;
    el.parentElement?.classList.add('updated');
    setTimeout(() => el.parentElement?.classList.remove('updated'), 400);
    return;
  }
  
  const duration = 600;
  const startTime = performance.now();
  const animate = (currentTime) => {
    const elapsed = currentTime - startTime;
    const progress = Math.min(elapsed / duration, 1);
    const easeOut = 1 - Math.pow(1 - progress, 3);
    const current = Math.round(currentNum + (targetNum - currentNum) * easeOut);
    
    // Preserve format from target (e.g., "XXX VND" or "- XXX VND")
    if(targetText.includes('-')) {
      el.textContent = '- ' + current.toLocaleString('vi-VN') + ' VND';
    } else {
      el.textContent = current.toLocaleString('vi-VN') + ' VND';
    }
    
    if(progress < 1) {
      requestAnimationFrame(animate);
    } else {
      el.textContent = targetText;
      el.parentElement?.classList.add('updated');
      setTimeout(() => el.parentElement?.classList.remove('updated'), 400);
    }
  };
  requestAnimationFrame(animate);
}

function renderSummary(t){
  const baseEl = document.getElementById('sum_base');
  const taxEl = document.getElementById('sum_tax');
  const svcEl = document.getElementById('sum_svc');
  const totalEl = document.getElementById('sum_total');
  
  animateNumber(baseEl, money(t.base));
  animateNumber(taxEl, money(t.tax));
  animateNumber(svcEl, money(t.svc));
  
  if(sumLoyaltyEl && loyaltyLine){
    animateNumber(sumLoyaltyEl, '- ' + money(t.loyalty));
    loyaltyLine.classList.toggle('d-none', !(loyaltyRate > 0 && t.loyalty > 0));
  }
  animateNumber(sumDiscountEl, '- ' + money(t.discount));
  animateNumber(totalEl, money(t.total));
  
  pay_total_input.value = t.total;
  promo_amount_input.value = t.discount;
  promo_rate_input.value = activePromo ? activePromo.value : 0;
  updateQr(t);
  maybeEnablePay();
  renderSavedPromos(t.totalBefore);
}

function updatePayInfo(t){
  if(t.days > 0){
    pay_info.classList.remove('text-danger');
    pay_info.classList.remove('d-none');
    pay_info.innerHTML = `Bạn đang đặt <b>${t.days}</b> đêm. Tổng tạm tính sau thuế/phí: <b>${t.total.toLocaleString('vi-VN')} VND</b>.`;
  }
}

function updateQr(t){
  if(!qrImg || !qrAmountTxt) return;
  qrAmountTxt.textContent = money(t.total);
  const payload = `VinhLongHotel|U<?php echo $_SESSION['uId']; ?>|Amount:${t.total}|Checkin:${checkinEl.value}|Checkout:${checkoutEl.value}`;
  qr_payload_input.value = payload;

  if(t.total > 0 && qrImg.dataset.bank && qrImg.dataset.acc){
    const bank = qrImg.dataset.bank;
    const acc = qrImg.dataset.acc;
    const accName = encodeURIComponent(qrImg.dataset.name || '');
    const addInfo = encodeURIComponent(`VinhLongHotel-${booking_form.elements['name'].value || 'KH'}-${checkinEl.value}`);
    const qrUrl = `https://img.vietqr.io/image/${bank}-${acc}-compact.png?accountName=${accName}&amount=${t.total}&addInfo=${addInfo}`;
    qrImg.src = qrUrl;
  } else if(qrImg.dataset.static){
    qrImg.src = qrImg.dataset.static;
  }
}

function resetPayGate(message){
  qrUnlocked = false;
  payment_confirmed_input.value = '0';
  if(payBtn){
    payBtn.classList.add('d-none');
    payBtn.setAttribute('disabled', true);
  }
  if(paidStatus){
    paidStatus.textContent = message || 'Chạm đúp vào QR sau khi chọn ngày để mở nút thanh toán.';
    paidStatus.classList.remove('text-success');
    paidStatus.classList.add('text-danger');
  }
  toggleSubmitGate();
}

function toggleSubmitGate(){
  const ready = hasDatesSelected();
  if(submitHint){
    // Chỉ hiện hướng dẫn khi chưa mở khóa QR
    submitHint.classList.toggle('d-none', !ready || qrUnlocked);
  }
  const showBtn = ready && qrUnlocked && availabilityOk && currentDays > 0;
  if(payBtn){
    payBtn.classList.toggle('d-none', !showBtn);
    if(showBtn){
      payBtn.removeAttribute('disabled');
    } else {
      payBtn.setAttribute('disabled', true);
    }
  }
}

function maybeEnablePay(){
  const paid = payment_confirmed_input.value === '1' && qrUnlocked;
  const allow = availabilityOk && currentDays > 0 && paid;
  if(!payBtn) return;
  const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
  if(allow){
    payBtn.removeAttribute('disabled');
    if(paidStatus){
      const paidMsg = currentLang === 'en' 
        ? 'QR confirmed, you can pay now.'
        : 'Đã xác nhận quét QR, bạn có thể thanh toán ngay.';
      paidStatus.textContent = paidMsg;
      paidStatus.classList.remove('text-danger');
      paidStatus.classList.add('text-success');
    }
  } else {
    payBtn.setAttribute('disabled', true);
  }
}


function performQrUnlock(){
  qrUnlocked = true;
  qrUnlockPending = false;
  payment_confirmed_input.value = '1';
  pay_info.classList.add('d-none');
  const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
  const readyMsg = currentLang === 'en' ? 'Checked & ready to book' : 'Đã kiểm tra & sẵn sàng đặt';
  setAvailabilityStatus(readyMsg, 'done');
  updateReadyStatus();
  if(paidStatus){
    const paidMsg = currentLang === 'en' 
      ? 'QR confirmed, you can pay now.'
      : 'Đã xác nhận quét QR, bạn có thể thanh toán ngay.';
    paidStatus.textContent = paidMsg;
    paidStatus.classList.remove('text-danger');
    paidStatus.classList.add('text-success');
  }
  toggleSubmitGate();
  maybeEnablePay();
}

function unlockByQr(){
  const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
  if(!fieldsFilled()){
    highlightMissingFields();
    const msg1 = currentLang === 'en' 
      ? 'Please fill in Name, Phone, Address and select check-in/check-out dates first.'
      : 'Vui lòng điền Họ tên, Số điện thoại, Địa chỉ và chọn ngày nhận/trả trước.';
    notify('warning', msg1);
    pay_info.classList.remove('d-none');
    pay_info.classList.add('text-danger');
    const msg2 = currentLang === 'en'
      ? 'Fill in all information and select check-in/check-out dates before confirming QR.'
      : 'Điền đủ thông tin và chọn ngày nhận/trả phòng trước khi xác nhận QR.';
    pay_info.textContent = msg2;
    return;
  }
  if(!hasDatesSelected()){
    const msg = currentLang === 'en'
      ? 'Select check-in/check-out dates before confirming QR.'
      : 'Chọn ngày nhận/trả phòng trước khi xác nhận QR.';
    pay_info.textContent = msg;
    pay_info.classList.remove('d-none');
    pay_info.classList.add('text-danger');
    const msg3 = currentLang === 'en'
      ? 'Select check-in and check-out dates before confirming QR.'
      : 'Chọn ngày nhận và trả phòng trước khi xác nhận QR.';
    notify('warning', msg3);
    highlightMissingFields();
    return;
  }
  if(!availabilityOk){
    if(hasDatesSelected()){
      qrUnlockPending = true;
      const msg = currentLang === 'en'
        ? 'Checking room availability, please wait...'
        : 'Đang kiểm tra phòng trống, vui lòng đợi...';
      notify('warning', msg);
      const checkingMsg = currentLang === 'en' ? 'Checking...' : 'Đang kiểm tra...';
      setAvailabilityStatus(checkingMsg, 'neutral');
      check_availability();
    } else {
      const msg = currentLang === 'en'
        ? 'Select check-in and check-out dates before confirming QR.'
        : 'Chọn ngày nhận và trả phòng trước khi xác nhận QR.';
      notify('warning', msg);
    }
    pay_info.classList.remove('d-none');
    pay_info.classList.add('text-danger');
    const msg2 = currentLang === 'en'
      ? 'Check room availability before confirming QR.'
      : 'Kiểm tra phòng trống trước khi xác nhận QR.';
    pay_info.textContent = msg2;
    return;
  }
  qrUnlockPending = false;
  performQrUnlock();
}

function applyPromo(){
  const code = (promo_code.value || '').trim().toUpperCase();
  const promoGroup = document.getElementById('promo-input-group');
  
  if(code && promoTable[code]){
    activePromo = promoTable[code];
    promo_code_hidden.value = code;
    const expInfo = formatExpiryStatus(activePromo);
    if(promo_status){
      promo_status.textContent = expInfo.text;
      promo_status.className = `small ${expInfo.cls}`;
    }
    setPromoInfo(activePromo.note || `Đã áp dụng mã ${code}`, true);
    
    // Success animation
    if(promoGroup) {
      promoGroup.classList.add('promo-success');
      setTimeout(() => promoGroup.classList.remove('promo-success'), 600);
    }
  } else if(code){
    activePromo = null;
    promo_code_hidden.value = '';
    setPromoInfo('Mã không hợp lệ hoặc đã hết hạn.', false);
    if(promo_status){
      promo_status.textContent = 'Chưa có mã phù hợp';
      promo_status.className = 'small text-muted';
    }
  } else {
    activePromo = null;
    promo_code_hidden.value = '';
    setPromoInfo('', false);
    if(promo_status){
      promo_status.textContent = '';
      promo_status.className = 'small text-muted';
    }
  }
  const totals = computeTotals(currentDays || calcNights(checkinEl.value, checkoutEl.value));
  renderSummary(totals);
  updatePayInfo(totals);
}

function check_availability(){
  const checkin = checkinEl.value;
  const checkout = checkoutEl.value;
  const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';

  // Reset trạng thái
  const resetMsg = currentLang === 'en' 
    ? 'Double tap QR code after selecting valid dates.'
    : 'Chạm đúp mã QR sau khi chọn ngày hợp lệ.';
  resetPayGate(resetMsg);
  availabilityOk = false;
  checkinEl.classList.remove('is-invalid');
  checkoutEl.classList.remove('is-invalid');
  if(warnBanner) warnBanner.style.display = 'none';

  // Kiểm tra đã chọn ngày chưa
  if(!checkin || !checkout){
    const msg = currentLang === 'en' ? 'No dates selected' : 'Chưa chọn ngày';
    setAvailabilityStatus(msg, 'error');
    pay_info.classList.remove('d-none');
    pay_info.classList.add('text-danger');
    const msg2 = currentLang === 'en'
      ? 'Select check-in and check-out dates to check.'
      : 'Chọn ngày nhận và trả phòng để kiểm tra.';
    pay_info.textContent = msg2;
    toggleSubmitGate();
    return;
  }

  // Kiểm tra ngày hợp lệ
  const ci = new Date(checkin);
  const co = new Date(checkout);
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  ci.setHours(0, 0, 0, 0);
  co.setHours(0, 0, 0, 0);

  if(ci < today){
    const msg = currentLang === 'en' 
      ? 'Check-in date must be from today onwards'
      : 'Ngày nhận phải từ hôm nay trở đi';
    setAvailabilityStatus(msg, 'error');
    if(checkinTouched) {
      checkinEl.classList.add('is-invalid');
    }
    pay_info.classList.remove('d-none');
    pay_info.classList.add('text-danger');
    const msg2 = currentLang === 'en'
      ? 'Check-in date cannot be earlier than today.'
      : 'Ngày nhận phòng không được nhỏ hơn hôm nay.';
    pay_info.textContent = msg2;
    toggleSubmitGate();
    return;
  }

  if(co <= ci){
    const msg = currentLang === 'en'
      ? 'Check-out date must be after check-in date'
      : 'Ngày trả phải sau ngày nhận';
    setAvailabilityStatus(msg, 'error');
    if(checkoutTouched) {
      checkoutEl.classList.add('is-invalid');
    }
    pay_info.classList.remove('d-none');
    pay_info.classList.add('text-danger');
    const msg2 = currentLang === 'en'
      ? 'Check-out date must be after check-in date.'
      : 'Ngày trả phòng phải sau ngày nhận phòng.';
    pay_info.textContent = msg2;
    toggleSubmitGate();
    return;
  }
  
  // Kiểm tra overlap với booking đã có (client-side) - CHẶN NGAY, KHÔNG gọi server
  if(bookedRanges && bookedRanges.length > 0) {
    const hasOverlap = hasOverlapWithBookings(checkin, checkout, bookedRanges);
    if(hasOverlap) {
      availabilityOk = false;
      info_loader.classList.add('d-none');
      const msg = currentLang === 'en'
        ? 'Booking exists in this time period'
        : 'Đã có đặt phòng trong khoảng thời gian này';
      setAvailabilityStatus(msg, 'error');
      // Chỉ hiện lỗi nếu đã touched
      if(checkinTouched || checkoutTouched) {
        checkinEl.classList.add('is-invalid');
        checkoutEl.classList.add('is-invalid');
      }
      pay_info.classList.remove('d-none');
      pay_info.classList.add('text-danger');
      const msg2 = currentLang === 'en'
        ? 'This time period already has a booking. Please choose different dates.'
        : 'Khoảng thời gian này đã có đặt phòng. Vui lòng chọn ngày khác.';
      pay_info.textContent = msg2;
      if(warnBanner) {
        warnBanner.style.display = 'block';
        warnBanner.textContent = msg2;
      }
      toggleSubmitGate();
      const resetMsg2 = currentLang === 'en'
        ? 'Choose different dates to continue.'
        : 'Chọn ngày khác để tiếp tục.';
      resetPayGate(resetMsg2);
      // KHÔNG gọi server nếu có overlap
      return;
    }
  }

  // Tính toán tạm thời
  currentDays = calcNights(checkin, checkout);
  renderSummary(computeTotals(currentDays));

  pay_info.classList.add('d-none');
  info_loader.classList.remove('d-none');
  if(availabilityTimer){ clearTimeout(availabilityTimer); }
  availabilityTimer = setTimeout(()=>{
    if(!info_loader.classList.contains('d-none')){
      info_loader.classList.add('d-none');
      pay_info.classList.remove('d-none');
      pay_info.classList.add('text-danger');
      pay_info.textContent = 'Kiểm tra phòng trống mất quá lâu. Vui lòng thử lại.';
      toggleSubmitGate();
      setAvailabilityStatus('Kiểm tra quá lâu, thử lại', 'error');
    }
  }, 9000);

  const data = new FormData();
  data.append('check_availability', '');
  data.append('check_in', checkin);
  data.append('check_out', checkout);

  const xhr = new XMLHttpRequest();
  xhr.open('POST', 'ajax/confirm_booking.php', true);
  xhr.timeout = 8000;
  xhr.onload = function(){
    if(availabilityTimer){ clearTimeout(availabilityTimer); }
    let res = {};
    try { res = JSON.parse(this.responseText); } catch(e){ res.status = 'error'; }
    const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';

    if(res.status === 'check_in_out_equal'){
      const msg = currentLang === 'en'
        ? 'Cannot check out on the same day as check-in.'
        : 'Không thể trả phòng cùng ngày nhận phòng.';
      pay_info.textContent = msg;
      availabilityOk = false;
      if(checkinTouched || checkoutTouched) {
        checkinEl.classList.add('is-invalid');
        checkoutEl.classList.add('is-invalid');
      }
      const warnMsg = currentLang === 'en'
        ? 'Check-out date must be after check-in date.'
        : 'Ngày trả phải sau ngày nhận phòng.';
      if(warnBanner){ warnBanner.style.display='block'; warnBanner.textContent=warnMsg; }
      const statusMsg = currentLang === 'en'
        ? 'Check-out must be after check-in'
        : 'Ngày trả phải sau ngày nhận';
      setAvailabilityStatus(statusMsg, 'error');
    } else if(res.status === 'check_out_earlier'){
      const msg = currentLang === 'en'
        ? 'Check-out date must be after check-in date.'
        : 'Ngày trả phòng phải sau ngày nhận phòng.';
      pay_info.textContent = msg;
      availabilityOk = false;
      if(checkinTouched || checkoutTouched) {
        checkinEl.classList.add('is-invalid');
        checkoutEl.classList.add('is-invalid');
      }
      if(warnBanner){ warnBanner.style.display='block'; warnBanner.textContent=msg; }
      const statusMsg = currentLang === 'en'
        ? 'Check-out must be after check-in'
        : 'Ngày trả phải sau ngày nhận';
      setAvailabilityStatus(statusMsg, 'error');
    } else if(res.status === 'check_in_earlier'){
      const msg = currentLang === 'en'
        ? 'Check-in date cannot be earlier than today.'
        : 'Ngày nhận phòng không được nhỏ hơn hôm nay.';
      pay_info.textContent = msg;
      availabilityOk = false;
      if(checkinTouched || checkoutTouched) {
        checkinEl.classList.add('is-invalid');
        checkoutEl.classList.add('is-invalid');
      }
      if(warnBanner){ warnBanner.style.display='block'; warnBanner.textContent=msg; }
      const statusMsg = currentLang === 'en'
        ? 'Check-in must be from today onwards'
        : 'Ngày nhận phải lớn hơn hôm nay';
      setAvailabilityStatus(statusMsg, 'error');
    } else if(res.status === 'unavailable'){
      const defaultMsg = currentLang === 'en'
        ? 'No rooms available for this time period.'
        : 'Hiện không còn phòng trống cho khoảng thời gian này.';
      const msg = res.msg || defaultMsg;
      pay_info.textContent = msg;
      availabilityOk = false;
      if(checkinTouched || checkoutTouched) {
        checkinEl.classList.add('is-invalid');
        checkoutEl.classList.add('is-invalid');
      }
      if(warnBanner){ warnBanner.style.display='block'; warnBanner.textContent=msg; }
      const statusMsg = currentLang === 'en' ? 'No rooms available' : 'Không còn phòng trống';
      setAvailabilityStatus(statusMsg, 'error');
    } else if(res.status === 'available'){
      currentDays = res.days;
      const totals = computeTotals(currentDays);
      renderSummary(totals);
      updatePayInfo(totals);
      availabilityOk = true;
      clearInvalid(checkinEl);
      clearInvalid(checkoutEl);
      pay_info.classList.add('d-none');
      pay_info.classList.remove('text-danger');
      pay_info.classList.remove('text-success');
      if(warnBanner){ warnBanner.style.display='none'; warnBanner.textContent=''; }
      const statusMsg = currentLang === 'en'
        ? `Checked: room available ${checkin} -> ${checkout}`
        : `Đã kiểm tra: còn phòng ${checkin} -> ${checkout}`;
      setAvailabilityStatus(statusMsg, 'ok');
      updateReadyStatus();
      if(qrUnlockPending){
        performQrUnlock();
      } else {
        maybeEnablePay();
      }
    } else {
      const msg = currentLang === 'en'
        ? 'Cannot check availability, please try again later.'
        : 'Không thể kiểm tra phòng trống, thử lại sau.';
      pay_info.textContent = msg;
      availabilityOk = false;
      const statusMsg = currentLang === 'en' ? 'Cannot check, try again' : 'Không thể kiểm tra, thử lại';
      setAvailabilityStatus(statusMsg, 'error');
      qrUnlockPending = false;
    }

    if(res.status !== 'available'){
      qrUnlockPending = false;
    }

    toggleSubmitGate();
    if(!availabilityOk){
      pay_info.classList.remove('d-none');
    }
    info_loader.classList.add('d-none');
  };
  xhr.onerror = function(){
    if(availabilityTimer){ clearTimeout(availabilityTimer); }
    availabilityOk = false;
    qrUnlockPending = false;
    pay_info.classList.remove('d-none');
    pay_info.classList.add('text-danger');
    const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
    const msg = currentLang === 'en'
      ? 'Cannot check room availability. Please check your connection and try again.'
      : 'Không thể kiểm tra phòng trống. Vui lòng kiểm tra kết nối và thử lại.';
    pay_info.textContent = msg;
    info_loader.classList.add('d-none');
    toggleSubmitGate();
    const statusMsg = currentLang === 'en' ? 'Cannot check, try again' : 'Không thể kiểm tra, thử lại';
    setAvailabilityStatus(statusMsg, 'error');
  };
  xhr.ontimeout = function(){
    if(availabilityTimer){ clearTimeout(availabilityTimer); }
    availabilityOk = false;
    qrUnlockPending = false;
    pay_info.classList.remove('d-none');
    pay_info.classList.add('text-danger');
    const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
    const msg = currentLang === 'en'
      ? 'Checking availability took too long. Please try again.'
      : 'Kiểm tra phòng trống mất quá lâu. Vui lòng thử lại.';
    pay_info.textContent = msg;
    info_loader.classList.add('d-none');
    toggleSubmitGate();
    const statusMsg = currentLang === 'en' ? 'Check took too long, try again' : 'Kiểm tra quá lâu, thử lại';
    setAvailabilityStatus(statusMsg, 'error');
  };
  xhr.send(data);
}

document.addEventListener('DOMContentLoaded', ()=>{
  const totals = computeTotals(0);
  renderSummary(totals);
  loadSavedPromos(totals.totalBefore);
  
  // Update QR box text attributes
  const qrBox = document.getElementById('qr-box-container');
  if(qrBox && window.i18n){
    const currentLang = window.i18n.getCurrentLanguage();
    qrBox.setAttribute('data-double-tap-text', window.i18n.translate('confirmBooking.doubleTapQR'));
    qrBox.setAttribute('data-copied-text', window.i18n.translate('confirmBooking.copied'));
  }
  
  const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
  const resetMsg = currentLang === 'en'
    ? 'Double tap QR code after selecting dates to unlock payment button.'
    : 'Chạm đúp mã QR sau khi chọn ngày để mở nút thanh toán.';
  resetPayGate(resetMsg);
  toggleSubmitGate();
  if(hasDatesSelected()){
    check_availability();
  }
  // Validate real-time khi chọn ngày - CHẶN NGAY nếu overlap
  if(checkinEl && checkoutEl && bookedRanges && bookedRanges.length > 0) {
    // Đánh dấu touched khi người dùng tương tác
    checkinEl.addEventListener('blur', () => { checkinTouched = true; });
    checkinEl.addEventListener('input', () => { checkinTouched = true; });
    checkoutEl.addEventListener('blur', () => { checkoutTouched = true; });
    checkoutEl.addEventListener('input', () => { checkoutTouched = true; });
    
    const validateDates = () => {
      const checkin = checkinEl.value;
      const checkout = checkoutEl.value;
      const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
      
      // Clear previous errors
      checkinEl.classList.remove('is-invalid');
      checkoutEl.classList.remove('is-invalid');
      checkinEl.setCustomValidity('');
      checkoutEl.setCustomValidity('');
      if(warnBanner) warnBanner.style.display = 'none';
      availabilityOk = false;
      
      if(!checkin || !checkout) {
        const msg = currentLang === 'en' ? 'Not enough dates selected' : 'Chưa chọn đủ ngày';
        setAvailabilityStatus(msg, 'error');
        return;
      }
      
      // Kiểm tra overlap với booking đã có - CHẶN NGAY
      if(hasOverlapWithBookings(checkin, checkout, bookedRanges)) {
        // CHẶN: Chỉ đánh dấu invalid nếu đã touched
        if(checkinTouched || checkoutTouched) {
          checkinEl.classList.add('is-invalid');
          checkoutEl.classList.add('is-invalid');
        }
        checkinEl.setCustomValidity('Khoảng thời gian này đã có đặt phòng.');
        checkoutEl.setCustomValidity('Khoảng thời gian này đã có đặt phòng.');
        availabilityOk = false;
        setAvailabilityStatus('Đã có đặt phòng trong khoảng thời gian này', 'error');
        if(warnBanner) {
          warnBanner.style.display = 'block';
          warnBanner.textContent = 'Khoảng thời gian này đã có đặt phòng. Vui lòng chọn ngày khác.';
        }
        pay_info.classList.remove('d-none');
        pay_info.classList.add('text-danger');
        pay_info.textContent = 'Khoảng thời gian này đã có đặt phòng. Vui lòng chọn ngày khác.';
        toggleSubmitGate();
        // KHÔNG gọi check_availability() nếu có overlap
        return;
      }
      
      // Nếu không overlap, mới gọi check_availability()
      if(checkin && checkout) {
        // Delay nhỏ để tránh gọi nhiều lần
        clearTimeout(window.validateTimeout);
        window.validateTimeout = setTimeout(() => {
          check_availability();
        }, 300);
      }
    };
    
    checkinEl.addEventListener('change', validateDates);
    checkoutEl.addEventListener('change', validateDates);
  }
  
  // gắn sự kiện đổi ảnh gallery + auto xoay ảnh nhỏ sau 3s
  const mainImg = document.getElementById('gallery-main-img');
const thumbImgs = Array.from(document.querySelectorAll('.thumb-img'));
const viewAllBtn = document.getElementById('btn-view-all');
  const galleryModalEl = document.getElementById('galleryModal');
const lb = document.getElementById('lb-viewer');
const lbImg = document.getElementById('lb-img');
const lbPrev = document.getElementById('lb-prev');
const lbNext = document.getElementById('lb-next');
const lbClose = document.getElementById('lb-close');
const galleryList = <?php echo json_encode($imgs); ?>;
let lbIndex = 0;
function swapWithThumb(img){
  if(!mainImg || !img) return;
  const mainSrc = mainImg.getAttribute('data-src') || mainImg.src;
  const thumbSrc = img.getAttribute('data-src') || img.src;
    mainImg.src = thumbSrc;
    mainImg.setAttribute('data-src', thumbSrc);
    img.src = mainSrc;
    img.setAttribute('data-src', mainSrc);
  }

  thumbImgs.forEach(img=>{
    img.addEventListener('click', ()=> swapWithThumb(img));
  });

  // Gallery Modal Variables
  let currentGalleryIndex = 0;
  const galleryMainViewImg = document.getElementById('gallery-main-view-img');
  const galleryCounterTop = document.getElementById('gallery-counter-top');
  let galleryThumbItems = [];

  // Hàm hiển thị ảnh trong gallery modal - Phải là global function
  window.showGalleryImage = function(index){
    if(!galleryList || index < 0 || index >= galleryList.length) return;
    
    currentGalleryIndex = index;
    const imgSrc = galleryList[index];
    
    // Fade transition
    if(galleryMainViewImg){
      galleryMainViewImg.style.opacity = '0';
      galleryMainViewImg.style.transition = 'opacity 0.3s ease';
      
      setTimeout(() => {
        galleryMainViewImg.src = imgSrc;
        galleryMainViewImg.style.opacity = '1';
      }, 150);
    }
    
    // Cập nhật counter ở header
    const currentNumEl = document.getElementById('gallery-current-num');
    if(currentNumEl){
      currentNumEl.textContent = index + 1;
    }
    
    // Cập nhật active thumbnail
    if(galleryThumbItems.length === 0){
      galleryThumbItems = Array.from(document.querySelectorAll('.gallery-thumb-item'));
    }
    galleryThumbItems.forEach((thumb, idx) => {
      if(idx === index){
        thumb.classList.add('active');
      } else {
        thumb.classList.remove('active');
      }
    });
    
    // Scroll thumbnail vào view
    if(galleryThumbItems[index]){
      galleryThumbItems[index].scrollIntoView({behavior: 'smooth', block: 'nearest', inline: 'center'});
    }
  };

  // Chuyển ảnh tiếp theo - Phải là global function
  window.nextGalleryImage = function(){
    if(!galleryList || galleryList.length === 0) return;
    const nextIndex = (currentGalleryIndex + 1) % galleryList.length;
    if(window.showGalleryImage) window.showGalleryImage(nextIndex);
  };

  // Chuyển ảnh trước - Phải là global function
  window.prevGalleryImage = function(){
    if(!galleryList || galleryList.length === 0) return;
    const prevIndex = (currentGalleryIndex - 1 + galleryList.length) % galleryList.length;
    if(window.showGalleryImage) window.showGalleryImage(prevIndex);
  };

  // Hàm mở modal gallery - Phải là global function
  window.openGalleryModal = function(){
    if(!galleryModalEl) return;
    
    const modal = new bootstrap.Modal(galleryModalEl);
    modal.show();
    
    // Reset gallery thumb items
    galleryThumbItems = [];
    
    // Reset về ảnh đầu tiên sau khi modal hiển thị
    const handleShown = () => {
      if(window.showGalleryImage) window.showGalleryImage(0);
      galleryModalEl.removeEventListener('shown.bs.modal', handleShown);
    };
    galleryModalEl.addEventListener('shown.bs.modal', handleShown);
    
      // Keyboard navigation
      const handleKeyPress = (e) => {
        if(!galleryModalEl.classList.contains('show')) return;
        if(e.key === 'ArrowRight') window.nextGalleryImage && window.nextGalleryImage();
        if(e.key === 'ArrowLeft') window.prevGalleryImage && window.prevGalleryImage();
        if(e.key === 'Escape') modal.hide();
      };
    
    galleryModalEl.addEventListener('shown.bs.modal', () => {
      document.addEventListener('keydown', handleKeyPress);
    }, { once: true });
    
    galleryModalEl.addEventListener('hidden.bs.modal', () => {
      document.removeEventListener('keydown', handleKeyPress);
    }, { once: true });
  };

  if(viewAllBtn && galleryModalEl){
    viewAllBtn.addEventListener('click', openGalleryModal);
  }

  function openLightbox(idx){
    if(!lb || !lbImg || !galleryList.length) return;
    lbIndex = (idx + galleryList.length) % galleryList.length;
    lbImg.src = galleryList[lbIndex];
    lb.classList.add('show');
    lb.setAttribute('aria-hidden','false');
  }
  function closeLightbox(){
    if(!lb) return;
    lb.classList.remove('show');
    lb.setAttribute('aria-hidden','true');
  }
  function nextLightbox(step){
    openLightbox(lbIndex + step);
  }

  document.querySelectorAll('.thumb-img, #gallery-main-img').forEach((img, i)=>{
    img.addEventListener('click', ()=>{
      const dataSrc = img.getAttribute('data-src') || img.src;
      const idx = galleryList.indexOf(dataSrc);
      openLightbox(idx >=0 ? idx : i);
    });
  });
  if(galleryModalEl){
    galleryModalEl.querySelectorAll('img').forEach((img, i)=>{
      img.addEventListener('click', ()=>{
        const src = img.getAttribute('src');
        const idx = galleryList.indexOf(src);
        openLightbox(idx >=0 ? idx : i);
      });
    });
  }
  if(lbClose){ lbClose.addEventListener('click', closeLightbox); }
  if(lbPrev){ lbPrev.addEventListener('click', ()=>nextLightbox(-1)); }
  if(lbNext){ lbNext.addEventListener('click', ()=>nextLightbox(1)); }
  if(lb){
    lb.addEventListener('click', (e)=>{
      if(e.target === lb) closeLightbox();
    });
    document.addEventListener('keydown', (e)=>{
      if(!lb.classList.contains('show')) return;
      if(e.key === 'Escape') closeLightbox();
      if(e.key === 'ArrowRight') nextLightbox(1);
      if(e.key === 'ArrowLeft') nextLightbox(-1);
    });
  }

  // gỡ cảnh báo đỏ khi người dùng nhập lại
  ['name','phonenum','address'].forEach(key=>{
    const el = booking_form.elements[key];
    if(!el) return;
    ['input','change','blur'].forEach(evt=>{
      el.addEventListener(evt, ()=>{ if(el.value.trim()) clearInvalid(el); });
    });
  });
  ['checkin','checkout'].forEach(key=>{
    const el = booking_form.elements[key];
    if(!el) return;
    ['change','input','blur'].forEach(evt=>{
      el.addEventListener(evt, ()=>{ if(el.value) clearInvalid(el); });
    });
  });

  // chặn submit nếu thiếu thông tin/điều kiện
  booking_form.addEventListener('submit', (e)=>{
    const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
    if(!fieldsFilled()){
      e.preventDefault();
      highlightMissingFields();
      const msg = currentLang === 'en'
        ? 'Please fill in all information and check-in/check-out dates.'
        : 'Vui lòng nhập đầy đủ thông tin và ngày nhận/trả phòng.';
      notify('warning', msg);
      return;
    }
    if(!hasDatesSelected()){
      e.preventDefault();
      const msg = currentLang === 'en'
        ? 'Select check-in and check-out dates before payment.'
        : 'Chọn ngày nhận và trả phòng trước khi thanh toán.';
      notify('warning', msg);
      return;
    }
    if(!availabilityOk){
      e.preventDefault();
      const msg = currentLang === 'en'
        ? 'Check room availability before payment.'
        : 'Kiểm tra phòng trống trước khi thanh toán.';
      notify('warning', msg);
      return;
    }
    if(payment_confirmed_input.value !== '1' || !qrUnlocked){
      e.preventDefault();
      const msg = currentLang === 'en'
        ? 'Confirm QR scan before payment.'
        : 'Xác nhận đã quét QR trước khi thanh toán.';
      notify('warning', msg);
      return;
    }
  });

  if(mainImg && thumbImgs.length){
    let idx = 0;
    setInterval(()=>{
      if(!thumbImgs.length) return;
      swapWithThumb(thumbImgs[idx % thumbImgs.length]);
      idx = (idx + 1) % thumbImgs.length;
    }, 3000);
  }

  // back to top
  if(backTopBtn){
    window.addEventListener('scroll', ()=>{
      if(window.scrollY > 320){
        backTopBtn.classList.add('show');
      } else {
        backTopBtn.classList.remove('show');
      }
    });
    backTopBtn.addEventListener('click', ()=>{
      window.scrollTo({top:0, behavior:'smooth'});
    });
  }
});

if(qrImg){
  qrImg.addEventListener('dblclick', unlockByQr);
  
  // Copy QR info on click
  const copyQrBtn = document.getElementById('copy-qr-btn');
  const qrBoxContainer = document.getElementById('qr-box-container');
  
  if(copyQrBtn && qrBoxContainer) {
    qrBoxContainer.addEventListener('mouseenter', () => {
      if(qrAmountTxt && qrAmountTxt.textContent !== '0 VND') {
        copyQrBtn.style.display = 'block';
      }
    });
    qrBoxContainer.addEventListener('mouseleave', () => {
      copyQrBtn.style.display = 'none';
    });
    
    copyQrBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const bank = qrImg.dataset.bank || 'BIDV';
      const acc = qrImg.dataset.acc || '73510001284830';
      const name = qrImg.dataset.name || 'PHAM MINH MAN';
      const amount = qrAmountTxt ? qrAmountTxt.textContent : '0 VND';
      const text = `Ngân hàng: ${bank}\nSố tài khoản: ${acc}\nChủ tài khoản: ${name}\nSố tiền: ${amount}`;
      
      navigator.clipboard.writeText(text).then(() => {
        qrBoxContainer.classList.add('copied');
        copyQrBtn.innerHTML = '<i class="bi bi-check"></i>';
        setTimeout(() => {
          qrBoxContainer.classList.remove('copied');
          copyQrBtn.innerHTML = '<i class="bi bi-copy"></i>';
        }, 2000);
        if(typeof showToast === 'function') {
          showToast('success', 'Đã sao chép thông tin thanh toán', 3000);
        }
      }).catch(() => {
        if(typeof showToast === 'function') {
          showToast('error', 'Không thể sao chép', 3000);
        }
      });
    });
  }
}
if(confirmPaidBtn){
  confirmPaidBtn.addEventListener('click', unlockByQr);
}

// ==================== REVIEWS FUNCTIONALITY ====================
// Reviews đã được load trực tiếp bằng PHP, chỉ cần function toggleHelpful

function toggleHelpful(reviewId, btn){
  if(!reviewsContainer){
    console.error('Reviews container not found!');
    return;
  }
  
  console.log('Loading reviews for room:', roomId);
  
  const rating = filterRating ? filterRating.value : 0;
  const sortBy = sortReviews ? sortReviews.value : 'newest';
  
  const data = new FormData();
  data.append('get_reviews', '');
  data.append('room_id', roomId);
  data.append('rating', rating);
  data.append('sort_by', sortBy);
  
  console.log('Sending request to ajax/review_actions.php with:', {
    room_id: roomId,
    rating: rating,
    sort_by: sortBy
  });
  
  fetch('ajax/review_actions.php', {
    method: 'POST',
    body: data
  })
  .then(res => {
    console.log('Response status:', res.status);
    if(!res.ok){
      throw new Error('Network response was not ok: ' + res.status);
    }
    return res.text().then(text => {
      console.log('Raw response:', text);
      try {
        return JSON.parse(text);
      } catch(e) {
        console.error('JSON parse error:', e, 'Response text:', text);
        throw new Error('Invalid JSON response');
      }
    });
  })
  .then(data => {
    console.log('Parsed reviews response:', data);
    if(data.status === 'success'){
      console.log('Reviews count:', data.reviews ? data.reviews.length : 0);
      if(data.debug){
        console.log('Debug info:', data.debug);
      }
      renderReviews(data.reviews);
    } else {
      console.error('Error from server:', data);
      reviewsContainer.innerHTML = '<div class="text-danger small">Lỗi: ' + (data.msg || 'Không thể tải đánh giá') + '</div>';
    }
  })
  .catch(err => {
    console.error('Error loading reviews:', err);
    reviewsContainer.innerHTML = '<div class="text-danger small">Lỗi tải đánh giá: ' + err.message + '</div>';
  });
}

function renderReviews(reviews){
  if(!reviews || reviews.length === 0){
    reviewsContainer.innerHTML = '<div class="text-center py-4"><i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i><p class="text-muted mt-2 mb-0">Chưa có đánh giá nào cho phòng này.</p></div>';
    return;
  }
  
  let html = '';
  reviews.forEach(review => {
    const stars = '⭐'.repeat(review.rating);
    const avatar = review.user_avatar || '';
    const initial = review.user_name.charAt(0).toUpperCase();
    const imagesHtml = review.images && review.images.length > 0 
      ? `<div class="review-images mt-2 d-flex gap-2 flex-wrap">
           ${review.images.map(img => {
             // Đảm bảo đường dẫn ảnh đúng
             let imgPath = img;
             if(imgPath && !imgPath.startsWith('http') && !imgPath.startsWith('/')){
               imgPath = imgPath.startsWith('../') ? imgPath.substring(3) : imgPath;
               imgPath = imgPath.startsWith('images/') ? imgPath : 'images/' + imgPath.replace(/^images\//, '');
             }
             return `<img src="${imgPath}" alt="Review image" class="review-img-thumb" onclick="openImageModal('${imgPath}')" onerror="this.style.display='none'">`;
           }).join('')}
         </div>`
      : '';
    const adminReplyHtml = review.admin_reply 
      ? `<div class="admin-reply mt-2 p-2 bg-light rounded border-start border-3 border-primary">
           <div class="d-flex align-items-center gap-2 mb-1">
             <i class="bi bi-shield-check text-primary"></i>
             <strong class="small">Phản hồi từ quản trị viên</strong>
             ${review.admin_reply_date ? `<span class="text-muted small ms-auto">${review.admin_reply_date}</span>` : ''}
           </div>
           <div class="small text-muted">${review.admin_reply}</div>
         </div>`
      : '';
    const helpfulClass = review.user_helpful ? 'active' : '';
    const helpfulIcon = review.user_helpful ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up';
    
    html += `
      <div class="review-item" data-review-id="${review.id}">
        <div class="review-avatar">
          ${avatar ? `<img src="${avatar}" alt="${review.user_name}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">` : ''}
          <span style="${avatar ? 'display: none;' : ''}">${initial}</span>
        </div>
        <div class="flex-grow-1">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="fw-semibold">${review.user_name}</span>
            <span class="small text-muted">${review.date}</span>
          </div>
          <div class="mb-1">${stars}</div>
          <div class="text-muted small mb-2">${review.review || 'Không có nhận xét'}</div>
          ${imagesHtml}
          ${adminReplyHtml}
          <div class="d-flex justify-content-between align-items-center mt-2">
            <button class="btn-helpful btn btn-sm btn-outline-secondary border-0 p-1 ${helpfulClass}" 
                    onclick="toggleHelpful(${review.id}, this)" 
                    title="Đánh dấu hữu ích">
              <i class="bi ${helpfulIcon} me-1"></i>
              <span class="helpful-count">${review.helpful_count || 0}</span>
            </button>
          </div>
        </div>
      </div>
    `;
  });
  
  reviewsContainer.innerHTML = html;
}

function toggleHelpful(reviewId, btn){
  if(!<?php echo isset($_SESSION['login']) && $_SESSION['login'] ? 'true' : 'false'; ?>){
    if(typeof showToast === 'function'){
      showToast('warning', 'Vui lòng đăng nhập để đánh dấu hữu ích', 3000);
    }
    return;
  }
  
  const data = new FormData();
  data.append('mark_helpful', '');
  data.append('review_id', reviewId);
  
  fetch('ajax/review_actions.php', {
    method: 'POST',
    body: data
  })
  .then(res => res.json())
  .then(data => {
    if(data.status === 'added' || data.status === 'removed'){
      const icon = btn.querySelector('i');
      const countSpan = btn.querySelector('.helpful-count');
      if(data.status === 'added'){
        btn.classList.add('active');
        icon.className = 'bi bi-hand-thumbs-up-fill me-1';
        // Animation khi like
        btn.style.transform = 'scale(1.1)';
        setTimeout(() => {
          btn.style.transform = '';
        }, 200);
      } else {
        btn.classList.remove('active');
        icon.className = 'bi bi-hand-thumbs-up me-1';
      }
      countSpan.textContent = data.count;
    } else if(data.status === 'error'){
      if(typeof showToast === 'function'){
        showToast('error', data.msg || 'Có lỗi xảy ra', 3000);
      }
    }
  })
  .catch(err => {
    console.error('Error toggling helpful:', err);
    if(typeof showToast === 'function'){
      showToast('error', 'Có lỗi xảy ra. Vui lòng thử lại.', 3000);
    }
  });
}

function openImageModal(imgSrc){
  // Tạo modal xem ảnh lớn với style đẹp hơn
  const modal = document.createElement('div');
  modal.className = 'modal fade';
  modal.setAttribute('tabindex', '-1');
  modal.innerHTML = `
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content bg-transparent border-0">
        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 bg-dark rounded-circle" style="z-index: 10; opacity: 0.9;" data-bs-dismiss="modal" aria-label="Close"></button>
        <img src="${imgSrc}" class="img-fluid rounded shadow-lg" alt="Review image" style="max-height: 90vh; width: 100%; object-fit: contain; background: rgba(0,0,0,0.5);">
      </div>
    </div>
  `;
  document.body.appendChild(modal);
  const bsModal = new bootstrap.Modal(modal);
  bsModal.show();
  modal.addEventListener('hidden.bs.modal', () => modal.remove());
}

// Toggle room description
function toggleRoomDescription(){
  const shortDesc = document.getElementById('room-description-short');
  const fullDesc = document.getElementById('room-description-full');
  const toggleBtn = document.getElementById('toggle-description-btn');
  
  if(!shortDesc || !fullDesc || !toggleBtn) return;
  
  const isExpanded = fullDesc.style.display === 'block';
  
  if(isExpanded){
    // Thu gọn
    shortDesc.style.display = 'block';
    fullDesc.style.display = 'none';
    const readMoreText = window.i18n && window.i18n.translate ? window.i18n.translate('confirmBooking.readMore') : 'Xem thêm';
    toggleBtn.innerHTML = `<span data-i18n="confirmBooking.readMore">${readMoreText}</span> <i class="bi bi-chevron-down ms-1"></i>`;
  } else {
    // Mở rộng
    shortDesc.style.display = 'none';
    fullDesc.style.display = 'block';
    const readLessText = window.i18n && window.i18n.translate ? window.i18n.translate('confirmBooking.readLess') : 'Thu gọn';
    toggleBtn.innerHTML = `<span data-i18n="confirmBooking.readLess">${readLessText}</span> <i class="bi bi-chevron-up ms-1"></i>`;
  }
  
  // Cập nhật i18n nếu có
  if(window.i18n && window.i18n.updateTranslations){
    window.i18n.updateTranslations();
  }
}

</script>
