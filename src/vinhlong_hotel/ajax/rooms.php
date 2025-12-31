<?php

require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
ensureFavoritesTable();

// Lấy ngôn ngữ hiện tại từ cookie
$current_lang = $_COOKIE['lang'] ?? 'vi';

// Hàm dịch tên phòng
function t_room_name($name, $lang = 'vi') {
  // Decode HTML entities nhiều lần nếu cần (xử lý trường hợp bị encode nhiều lần)
  $decoded = $name;
  $prev_decoded = '';
  while($decoded !== $prev_decoded) {
    $prev_decoded = $decoded;
    $decoded = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  }
  $name = $decoded;
  
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

// Hàm dịch đơn giản
function t($key, $lang = 'vi') {
    $translations = [
        'vi' => [
            'room.adults' => 'người lớn',
            'room.children' => 'trẻ em',
            'room.remaining' => 'Còn',
            'room.rooms' => 'phòng',
            'rooms.soldOut' => 'Hết phòng',
            'rooms.new' => 'Mới',
            'room.space' => 'Không gian',
            'room.facilities' => 'Tiện ích',
            'promo.offers' => 'Ưu đãi',
            'rooms.freeCancel' => 'Hủy miễn phí',
            'rooms.priceNote' => 'Giá cho 1 đêm đã gồm thuế/phí cơ bản',
            'rooms.bookNow' => 'Đặt ngay',
            'rooms.details' => 'Chi tiết',
            'rooms.temporarilyClosed' => 'Tạm ngưng',
            'rooms.noRating' => 'Chưa có đánh giá',
            'room.discountPercent' => 'Giảm',
            'features.bedroom' => 'Phòng Ngủ',
            'features.balcony' => 'Ban Công',
            'features.kitchen' => 'Nhà Bếp',
            'features.sofa' => 'Ghế Sofa',
            'features.bathroom' => 'Phòng Tắm',
            'features.livingRoom' => 'Phòng Khách',
            'features.refrigerator' => 'Tủ Lạnh',
            'features.airConditioner' => 'Máy Lạnh',
            'features.tv' => 'TV',
            'features.wifi' => 'WiFi',
            'facilities.wifi' => 'Wi-Fi',
            'facilities.tv' => 'Truyền Hình',
            'facilities.spa' => 'Spa',
            'facilities.heater' => 'Máy Sưởi',
            'facilities.airConditioner' => 'Máy Lạnh',
            'facilities.waterHeater' => 'Máy Nước Nóng',
            'facilities.hairDryer' => 'Máy Sấy Tóc',
            'facilities.personalHygiene' => 'Đồ Vệ Sinh Cá Nhân',
            'facilities.minibar' => 'Minibar',
            'facilities.kettle' => 'Ấm Đun Nước',
            'facilities.workspace' => 'Khu Làm Việc',
            'facilities.wardrobe' => 'Tủ Quần Áo',
            'facilities.slippers' => 'Dép Đi Trong Nhà',
            'facilities.fullMirror' => 'Gương Toàn Thân',
            'facilities.iron' => 'Bàn Ủi',
            'facilities.bar' => 'Quầy bar',
            'facilities.golfCourse' => 'Sân golf',
            'facilities.swimmingPool' => 'Hồ bơi',
            'facilities.airportShuttle' => 'Xe đưa đón sân bay',
            'facilities.service24' => 'Dịch vụ 24/24 giờ',
        ],
        'en' => [
            'room.adults' => 'adults',
            'room.children' => 'children',
            'room.remaining' => 'Remaining',
            'room.rooms' => 'rooms',
            'rooms.soldOut' => 'Sold Out',
            'rooms.new' => 'New',
            'room.space' => 'Space',
            'room.facilities' => 'Facilities',
            'promo.offers' => 'Offer',
            'rooms.freeCancel' => 'Free Cancellation',
            'rooms.priceNote' => 'Price for 1 night includes basic taxes/fees',
            'rooms.bookNow' => 'Book Now',
            'rooms.details' => 'Details',
            'rooms.temporarilyClosed' => 'Temporarily Closed',
            'rooms.noRating' => 'No rating yet',
            'room.discountPercent' => 'Discount',
            'features.bedroom' => 'Bedroom',
            'features.balcony' => 'Balcony',
            'features.kitchen' => 'Kitchen',
            'features.sofa' => 'Sofa',
            'features.bathroom' => 'Bathroom',
            'features.livingRoom' => 'Living Room',
            'features.refrigerator' => 'Refrigerator',
            'features.airConditioner' => 'Air Conditioner',
            'features.tv' => 'TV',
            'features.wifi' => 'WiFi',
            'facilities.wifi' => 'Wi-Fi',
            'facilities.tv' => 'Television',
            'facilities.spa' => 'Spa',
            'facilities.heater' => 'Heater',
            'facilities.airConditioner' => 'Air Conditioner',
            'facilities.waterHeater' => 'Water Heater',
            'facilities.hairDryer' => 'Hair Dryer',
            'facilities.personalHygiene' => 'Personal Hygiene Items',
            'facilities.minibar' => 'Minibar',
            'facilities.kettle' => 'Kettle',
            'facilities.workspace' => 'Workspace',
            'facilities.wardrobe' => 'Wardrobe',
            'facilities.slippers' => 'Slippers',
            'facilities.fullMirror' => 'Full-length Mirror',
            'facilities.iron' => 'Iron',
            'facilities.bar' => 'Bar Counter',
            'facilities.golfCourse' => 'Golf Course',
            'facilities.swimmingPool' => 'Swimming Pool',
            'facilities.airportShuttle' => 'Airport Shuttle',
            'facilities.service24' => '24/24 Hour Service',
        ]
    ];
    return $translations[$lang][$key] ?? $key;
}

function table_exists($con, $table){
    $table = mysqli_real_escape_string($con, $table);
    $res = mysqli_query($con, "SHOW TABLES LIKE '{$table}'");
    return $res && mysqli_num_rows($res) > 0;
}

function column_exists($con, $table, $column){
    $table = mysqli_real_escape_string($con, $table);
    $column = mysqli_real_escape_string($con, $column);
    $res = mysqli_query($con, "SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
    return $res && mysqli_num_rows($res) > 0;
}

if(!function_exists('mb_str_split')){
    function mb_str_split($string){
        return preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
    }
}

/**
 * Remove Vietnamese accents for loose keyword matching.
 */
function vn_str_filter($str) {
    $str = mb_strtolower($str, 'UTF-8');
    $unicode = [
        'a' => 'áàảãạăắằẳẵặâấầẩẫậ',
        'd' => 'đ',
        'e' => 'éèẻẽẹêếềểễệ',
        'i' => 'íìỉĩị',
        'o' => 'óòỏõọôốồổỗộơớờởỡợ',
        'u' => 'úùủũụưứừửữự',
        'y' => 'ýỳỷỹỵ'
    ];
    foreach ($unicode as $ascii => $chars) {
        $str = str_replace(mb_str_split($chars), $ascii, $str);
    }
    $str = preg_replace('/[^a-z0-9\s]/', ' ', $str);
    $str = preg_replace('/\s+/', ' ', $str);
    return trim($str);
}

if(isset($_GET['fetch_rooms']))
{
    /* =====================================================
       1. GET & NORMALIZE INPUT
    ===================================================== */
    $keyword     = trim($_GET['keyword'] ?? "");
    $keyword     = mb_substr($keyword, 0, 120);
    $keyword_n   = vn_str_filter($keyword);
    $district    = trim($_GET['district'] ?? "");
    $district    = mb_substr($district, 0, 120);
    $district_n  = vn_str_filter($district);
    $star_filter = isset($_GET['star']) ? (int)$_GET['star'] : 0;

    $chk_avail = json_decode($_GET['chk_avail'] ?? "[]", true) ?: [];
    $guests    = json_decode($_GET['guests'] ?? "[]", true) ?: [];

    $adults   = max(0, (int)($guests['adults']   ?? 0));
    $children = max(0, (int)($guests['children'] ?? 0));

    $facility_list = json_decode($_GET['facility_list'] ?? "[]", true) ?: [];
    if(!isset($facility_list['facilities'])) $facility_list['facilities'] = [];

    $destination_list = json_decode($_GET['destination_list'] ?? "[]", true) ?: [];
    if(!isset($destination_list['destinations'])) $destination_list['destinations'] = [];

    $min_price = isset($_GET['min_price']) ? max(0, (int)$_GET['min_price']) : 0;
    $max_price = isset($_GET['max_price']) ? max(0, (int)$_GET['max_price']) : 0;
    $sort_by   = $_GET['sort_by'] ?? '';

    // Phân trang
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $per_page = 10; // 10 phòng mỗi trang


    /* =====================================================
       2. VALIDATE DATE
    ===================================================== */
    if(!empty($chk_avail['checkin']) && !empty($chk_avail['checkout']))
    {
        $ci = new DateTime($chk_avail['checkin']);
        $co = new DateTime($chk_avail['checkout']);
        $today = new DateTime(date("Y-m-d"));

        if($ci >= $co || $ci < $today){
            echo "<h3 class='text-center text-danger'>Ngày không hợp lệ!</h3>";
            exit;
        }
    }


    /* =====================================================
       3. BASE QUERY (PREPARED)
    ===================================================== */
    // Kiểm tra xem cột approved có tồn tại không
    $has_approved = column_exists($con, 'rooms', 'approved');
    
    // Nếu cột chưa tồn tại, tự động tạo nó
    if(!$has_approved){
        $alter_sql = "ALTER TABLE `rooms` 
                      ADD COLUMN `approved` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0=chờ duyệt, 1=đã duyệt' 
                      AFTER `status`";
        @mysqli_query($con, $alter_sql);
        // Kiểm tra lại sau khi thêm
        $has_approved = column_exists($con, 'rooms', 'approved');
    }
    
    // Chỉ hiển thị phòng đã duyệt (approved = 1), không hiển thị phòng chờ duyệt (approved = 0)
    $approved_condition = $has_approved ? " AND r.approved = 1" : "";
    
    $room_stmt = $con->prepare("
        SELECT r.*,
        ho.name AS owner_name,
        ho.hotel_name,
        (
            SELECT ROUND(AVG(rating))
            FROM rating_review WHERE room_id = r.id
        ) AS avg_star
        FROM rooms r
        LEFT JOIN hotel_owners ho ON r.owner_id = ho.id
        WHERE r.adult >= ? AND r.children >= ? AND r.status = 1 AND r.removed = 0{$approved_condition}
        AND (r.owner_id IS NULL OR ho.status = 1)
    ");
    $room_stmt->bind_param('ii', $adults, $children);
    $room_stmt->execute();
    $room_res = $room_stmt->get_result();

    // Favorites của user
    $fav_ids = [];
    if(isset($_SESSION['login']) && $_SESSION['login']==true){
        $fav_q = select("SELECT room_id FROM favorites WHERE user_id=?", [$_SESSION['uId']], 'i');
        if($fav_q){
            while($fav_row = mysqli_fetch_assoc($fav_q)){
                $fav_ids[] = (int)$fav_row['room_id'];
            }
        }
    }

    $settings = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM settings WHERE sr_no=1"));

    $book_stmt = $con->prepare("
        SELECT COUNT(*) AS total FROM booking_order
        WHERE booking_status='booked'
          AND room_id=?
          AND check_out > ?
          AND check_in  < ?
    ");

    $fac_stmt = $con->prepare("
        SELECT f.id, f.name
        FROM facilities f
        INNER JOIN room_facilities rf ON f.id = rf.facilities_id
        WHERE rf.room_id = ?
    ");

    $fea_stmt = $con->prepare("
        SELECT f.name FROM features f
        INNER JOIN room_features rf ON f.id = rf.features_id
        WHERE rf.room_id=?
    ");

    $thumb_stmt = $con->prepare("
        SELECT image FROM room_images
        WHERE room_id=? AND thumb=1 LIMIT 1
    ");


    $output = "";
    $count = 0;


    /* =====================================================
       4. FILTER AFTER FETCH
    ===================================================== */
    $rooms = [];
    while($row = mysqli_fetch_assoc($room_res)) {
        $rooms[] = $row;
    }

    if($sort_by === 'price_asc') {
        usort($rooms, function($a,$b){ return $a['price'] <=> $b['price']; });
    } else if($sort_by === 'price_desc') {
        usort($rooms, function($a,$b){ return $b['price'] <=> $a['price']; });
    }

    // Lọc phòng theo các điều kiện - Lưu vào mảng trước
    $filtered_rooms = [];
    foreach($rooms as $row)
    {
        $room_name       = htmlspecialchars(t_room_name($row['name'], $current_lang), ENT_QUOTES, 'UTF-8');
        $room_name_plain = vn_str_filter($row['name']);
        $location_text   = htmlspecialchars($row['location'] ?? 'Vĩnh Long', ENT_QUOTES, 'UTF-8');
        $location_plain  = vn_str_filter($row['location'] ?? '');

        /* -------------------------------------------------
           4.1 FILTER KEYWORD
        ------------------------------------------------- */
        if($keyword !== ""){
            if(strpos($room_name_plain, $keyword_n) === false && strpos($location_plain, $keyword_n) === false){
                continue;
            }
        }
        /* -------------------------------------------------
           4.1.1 FILTER DISTRICT
        ------------------------------------------------- */
        if($district_n !== ""){
            if(strpos($location_plain, $district_n) === false){
                continue;
            }
        }

        /* -------------------------------------------------
           4.2 FILTER STAR
        ------------------------------------------------- */
        if($star_filter > 0 && (int)$row['avg_star'] !== $star_filter){
            continue;
        }

        /* -------------------------------------------------
           4.2.1 FILTER PRICE
        ------------------------------------------------- */
        if($min_price > 0 && $row['price'] < $min_price){
            continue;
        }
        if($max_price > 0 && $row['price'] > $max_price){
            continue;
        }


        /* -------------------------------------------------
           4.3 DATE AVAILABILITY CHECK
        ------------------------------------------------- */
        if(!empty($chk_avail['checkin']) && !empty($chk_avail['checkout']))
        {
            $ci = $chk_avail['checkin'];
            $co = $chk_avail['checkout'];

            $book_stmt->bind_param('iss', $row['id'], $ci, $co);
            $book_stmt->execute();
            $book = $book_stmt->get_result()->fetch_assoc();

            $remaining = isset($row['remaining']) ? (int)$row['remaining'] : 1; // Default to 1 if not set
            if($book['total'] > 0 || $remaining <= 0){
                continue;
            }
        }

        /* -------------------------------------------------
           4.4 FACILITIES FILTER
        ------------------------------------------------- */
        $fac_html = "";
        $fac_match = 0;

        $fac_stmt->bind_param('i', $row['id']);
        $fac_stmt->execute();
        $fac_q = $fac_stmt->get_result();

        // Map các facility names phổ biến để dịch
        $facility_map = [
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
            'WiFi miễn phí' => 'facilities.freeWifi',
            'Bãi đỗ xe' => 'facilities.parking',
            'Nhà hàng' => 'facilities.restaurant',
            'Phòng gym' => 'facilities.gym',
            'Dịch vụ giặt ủi' => 'facilities.laundry',
        ];

        while($f = mysqli_fetch_assoc($fac_q))
        {
            $f_name_raw = $f['name'] ?? '';
            $f_name = htmlspecialchars($f_name_raw, ENT_QUOTES, 'UTF-8');
            $f_i18n_key = isset($facility_map[$f_name_raw]) ? $facility_map[$f_name_raw] : '';
            
            if($f_i18n_key) {
                $fac_html .= "<span class='badge bg-light text-dark me-1 mb-1'>" . t($f_i18n_key, $current_lang) . "</span>";
            } else {
            $fac_html .= "<span class='badge bg-light text-dark me-1 mb-1'>{$f_name}</span>";
            }

            if(in_array((int)$f['id'], $facility_list['facilities'])){
                $fac_match++;
            }
        }

        if(count($facility_list['facilities']) > 0){
            if($fac_match != count($facility_list['facilities'])){
                continue;
            }
        }

        /* -------------------------------------------------
           4.5 DESTINATIONS FILTER
        ------------------------------------------------- */
        if(count($destination_list['destinations']) > 0){
            $dest_check_query = "SELECT COUNT(*) as count FROM `room_destinations` 
                                WHERE `room_id` = ? AND `destination_id` IN (" . implode(',', array_map('intval', $destination_list['destinations'])) . ")";
            $dest_check_result = select($dest_check_query, [$row['id']], 'i');
            $dest_match = 0;
            if($dest_check_result && mysqli_num_rows($dest_check_result) > 0){
                $dest_check_data = mysqli_fetch_assoc($dest_check_result);
                $dest_match = (int)$dest_check_data['count'];
            }
            if($dest_match == 0){
                continue;
            }
        }

        /* -------------------------------------------------
           4.6 FEATURES
        ------------------------------------------------- */
        // Map các feature names phổ biến để dịch
        $feature_map = [
            'Phòng Ngủ' => 'features.bedroom',
            'Ban Công' => 'features.balcony',
            'Nhà Bếp' => 'features.kitchen',
            'Ghế Sofa' => 'features.sofa',
            'Phòng Tắm' => 'features.bathroom',
            'Phòng Khách' => 'features.livingRoom',
            'Tủ Lạnh' => 'features.refrigerator',
            'Máy Lạnh' => 'features.airConditioner',
            'TV' => 'features.tv',
            'WiFi' => 'features.wifi',
        ];
        
        $fea_html = "";
        $fea_stmt->bind_param('i', $row['id']);
        $fea_stmt->execute();
        $fea_q = $fea_stmt->get_result();

        while($fea = mysqli_fetch_assoc($fea_q)){
            $fea_name_raw = $fea['name'] ?? '';
            $fea_name = htmlspecialchars($fea_name_raw, ENT_QUOTES, 'UTF-8');
            $fea_i18n_key = isset($feature_map[$fea_name_raw]) ? $feature_map[$fea_name_raw] : '';
            
            if($fea_i18n_key) {
                $fea_html .= "<span class='badge bg-light text-dark me-1 mb-1'>" . t($fea_i18n_key, $current_lang) . "</span>";
            } else {
            $fea_html .= "<span class='badge bg-light text-dark me-1 mb-1'>{$fea_name}</span>";
            }
        }

        /* -------------------------------------------------
           4.6 THUMBNAIL
        ------------------------------------------------- */
        $thumb = ROOMS_IMG_PATH."thumbnail.jpg";
        $thumb_stmt->bind_param('i', $row['id']);
        $thumb_stmt->execute();
        $img_q = $thumb_stmt->get_result();

        if(mysqli_num_rows($img_q)){
            $thumb = ROOMS_IMG_PATH . mysqli_fetch_assoc($img_q)['image'];
        }

        /* -------------------------------------------------
           4.7 STARS / RATING
        ------------------------------------------------- */
        $stars_html = "";
        $avg_star = (int)$row['avg_star'];
        if($avg_star > 0){
            $stars_html = "<div class='text-warning fw-bold' aria-label='Đánh giá {$avg_star} sao'>"
                           . str_repeat("&#9733;", $avg_star)
                           . str_repeat("&#9734;", max(0, 5-$avg_star))
                           . "</div>";
        } else {
            $stars_html = "<span class='text-muted small'>" . t('rooms.noRating', $current_lang) . "</span>";
        }

        /* -------------------------------------------------
           4.8 STATUS
        ------------------------------------------------- */
        $remaining = isset($row['remaining']) ? (int)$row['remaining'] : 1; // Default to 1 if not set
        if($remaining > 0) {
            $status_text = t('room.remaining', $current_lang) . " {$remaining} " . t('room.rooms', $current_lang);
        } else {
            $status_text = t('rooms.soldOut', $current_lang);
        }


        /* -------------------------------------------------
           4.9 BOOK BUTTON
        ------------------------------------------------- */
        if($settings['shutdown']){
            $book_btn = "<button disabled class='btn btn-sm btn-danger w-100 mb-2'>" . t('rooms.temporarilyClosed', $current_lang) . "</button>";
        } else {
            $remaining = isset($row['remaining']) ? (int)$row['remaining'] : 1; // Default to 1 if not set
            if($remaining > 0){
                $login = isset($_SESSION['login']) ? 1 : 0;
                $book_btn = "
                  <button onclick='checkLoginToBook($login,{$row['id']})'
                    class='btn btn-sm w-100 text-white custom-bg mb-2'>
                    " . t('rooms.bookNow', $current_lang) . "
                  </button>";
            } else {
                $book_btn = "<button disabled class='btn btn-sm btn-danger w-100 mb-2'>" . t('rooms.soldOut', $current_lang) . "</button>";
            }
        }

        /* -------------------------------------------------
           4.10 BUILD CARD
        ------------------------------------------------- */
        $discount = isset($row['discount']) ? (int)$row['discount'] : 0;
        $base_price = $row['price'];
        $final_price = $base_price;
        if($discount > 0 && $discount <= 100){
            $final_price = max(0, $base_price - ($base_price * $discount / 100));
        }
        $price_fmt = number_format($final_price,0,',','.');
        $old_price = number_format($base_price,0,',','.');
        if($discount > 0) {
            $discount_label_html = t('room.discountPercent', $current_lang) . " {$discount}%";
        } else {
            $discount_label_html = t('promo.offers', $current_lang);
        }
        $discount_badge = $discount > 0
            ? "<span class='badge bg-danger text-white ms-1'>{$discount}%</span>"
            : "";
        $guest_text = "{$row['adult']} " . t('room.adults', $current_lang) . " · {$row['children']} " . t('room.children', $current_lang);

        $is_fav = in_array((int)$row['id'],$fav_ids);
        $fav_class = $is_fav ? 'active' : '';
        $fav_icon = $is_fav ? 'bi-heart-fill' : 'bi-heart';

        // Hiển thị tên owner/admin
        $owner_display = '';
        if(!empty($row['owner_name']) || !empty($row['hotel_name'])){
            $owner_label = !empty($row['hotel_name']) ? htmlspecialchars($row['hotel_name'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($row['owner_name'], ENT_QUOTES, 'UTF-8');
            $owner_display = "<small class='text-muted d-block mb-1' style='font-size: 0.75rem;'>
                <i class='bi bi-building me-1'></i>{$owner_label}
            </small>";
        } else {
            $owner_display = "<small class='text-muted d-block mb-1' style='font-size: 0.75rem;'>
                <i class='bi bi-shield-check me-1'></i>Admin
            </small>";
        }

        $output .= "
        <article class='list-card'>
            <div>
                <div class='list-card__img-wrap'>
                  <img src='$thumb' class='list-card__img' alt='Ảnh phòng'>
                  <button class=\"home-fav-btn $fav_class\" data-room=\"{$row['id']}\" title=\"Yêu thích\"><i class=\"bi $fav_icon\"></i></button>
                </div>
                <div class='d-flex flex-wrap gap-2 mt-2'>
                    <span class='list-card__badge'><i class='bi bi-check2-circle me-1'></i>$status_text</span>
                </div>
            </div>
            <div class='list-card__info'>
                <h5 class='mb-0'>{$room_name}</h5>
                {$owner_display}
                <div class='list-card__meta mb-2'>
                    <i class='bi bi-geo-alt me-1'></i> {$location_text}
                    <span class='ms-2'><i class='bi bi-people me-1'></i> {$guest_text}</span>
                </div>
                <div class='mb-2 d-flex align-items-center gap-2'>
                    $stars_html
                    <span class='badge bg-light text-dark'>".($avg_star ? "{$avg_star}/5" : t('rooms.new', $current_lang))."</span>
                </div>
                <div class='list-card__tags mb-2'>
                    <div class='small text-uppercase text-muted fw-semibold mb-1'>" . t('room.space', $current_lang) . "</div>
                    $fea_html
                </div>
                <div class='list-card__tags'>
                    <div class='small text-uppercase text-muted fw-semibold mb-1'>" . t('room.facilities', $current_lang) . "</div>
                    $fac_html
                </div>
            </div>
            <div class='list-card__price d-flex flex-column justify-content-between'>
                <div class='text-end'>
                    <div class='list-card__price-old'>{$old_price} đ</div>
                    <div class='list-card__price-new'>{$price_fmt} đ {$discount_badge}</div>
                    <div class='small text-success fw-semibold'>" . t('promo.offers', $current_lang) . " · " . t('rooms.freeCancel', $current_lang) . "</div>
                    <div class='small text-muted'>" . t('rooms.priceNote', $current_lang) . "</div>
                </div>
                <div class='list-card__cta'>
                    $book_btn
                    <a href='room_details.php?id={$row['id']}' class='btn btn-outline-dark btn-sm w-100'>" . t('rooms.details', $current_lang) . "</a>
                </div>
            </div>
        </article>";
        $count++;
        $filtered_rooms[] = $row; // Lưu phòng đã filter
    }

    /* =====================================================
       5. PHÂN TRANG
    ===================================================== */
    $total_rooms = count($filtered_rooms);
    $total_pages = ceil($total_rooms / $per_page);
    $offset = ($page - 1) * $per_page;
    
    // Lấy phòng cho trang hiện tại
    $paginated_rooms = array_slice($filtered_rooms, $offset, $per_page);
    
    // Reset output và count để render lại chỉ phòng của trang hiện tại
    $output = "";
    $count = 0;
    
    // Render lại chỉ phòng của trang hiện tại
    foreach($paginated_rooms as $row)
    {
        // Lấy lại các thông tin cần thiết
        $room_name = htmlspecialchars(t_room_name($row['name'], $current_lang), ENT_QUOTES, 'UTF-8');
        $location_text = htmlspecialchars($row['location'] ?? 'Vĩnh Long', ENT_QUOTES, 'UTF-8');
        
        // Lấy thumbnail
        $thumb = ROOMS_IMG_PATH."thumbnail.jpg";
        $thumb_stmt->bind_param('i', $row['id']);
        $thumb_stmt->execute();
        $img_q = $thumb_stmt->get_result();
        if(mysqli_num_rows($img_q)){
            $thumb = ROOMS_IMG_PATH . mysqli_fetch_assoc($img_q)['image'];
        }
        
        // Stars
        $stars_html = "";
        $avg_star = (int)$row['avg_star'];
        if($avg_star > 0){
            $stars_html = "<div class='text-warning fw-bold' aria-label='Đánh giá {$avg_star} sao'>"
                           . str_repeat("&#9733;", $avg_star)
                           . str_repeat("&#9734;", max(0, 5-$avg_star))
                           . "</div>";
        } else {
            $stars_html = "<span class='text-muted small'>" . t('rooms.noRating', $current_lang) . "</span>";
        }
        
        // Status
        $remaining = isset($row['remaining']) ? (int)$row['remaining'] : 1;
        if($remaining > 0) {
            $status_text = "{$remaining} " . t('room.remaining', $current_lang) . " " . t('room.rooms', $current_lang);
        } else {
            $status_text = t('rooms.soldOut', $current_lang);
        }
        
        // Book button
        if($settings['shutdown']){
            $book_btn = "<button disabled class='btn btn-sm btn-danger w-100 mb-2' data-i18n='rooms.temporarilyClosed'>Tạm ngưng</button>";
        } else {
            if($remaining > 0){
                $login = isset($_SESSION['login']) ? 1 : 0;
                $book_btn = "
                  <button onclick='checkLoginToBook($login,{$row['id']})'
                    class='btn btn-sm w-100 text-white custom-bg mb-2'>
                    " . t('rooms.bookNow', $current_lang) . "
                  </button>";
            } else {
                $book_btn = "<button disabled class='btn btn-sm btn-danger w-100 mb-2'>" . t('rooms.soldOut', $current_lang) . "</button>";
            }
        }
        
        // Price
        $discount = isset($row['discount']) ? (int)$row['discount'] : 0;
        $base_price = $row['price'];
        $final_price = $base_price;
        if($discount > 0 && $discount <= 100){
            $final_price = max(0, $base_price - ($base_price * $discount / 100));
        }
        $price_fmt = number_format($final_price,0,',','.');
        $old_price = number_format($base_price,0,',','.');
        $discount_badge = $discount > 0
            ? "<span class='badge bg-danger text-white ms-1'>{$discount}%</span>"
            : "";
        $guest_text = "{$row['adult']} " . t('room.adults', $current_lang) . " · {$row['children']} " . t('room.children', $current_lang);
        
        // Facilities
        $fac_html = "";
        // Đóng result set cũ nếu có
        if(isset($fac_q) && $fac_q instanceof mysqli_result){
            mysqli_free_result($fac_q);
        }
        $fac_stmt->bind_param('i', $row['id']);
        $fac_stmt->execute();
        $fac_q = $fac_stmt->get_result();
        
        // Map các facility names phổ biến để dịch
        $facility_map = [
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
            'WiFi miễn phí' => 'facilities.freeWifi',
            'Bãi đỗ xe' => 'facilities.parking',
            'Nhà hàng' => 'facilities.restaurant',
            'Phòng gym' => 'facilities.gym',
            'Dịch vụ giặt ủi' => 'facilities.laundry',
        ];
        
        while($f = mysqli_fetch_assoc($fac_q)){
            $f_name_raw = $f['name'] ?? '';
            $f_name = htmlspecialchars($f_name_raw, ENT_QUOTES, 'UTF-8');
            $f_i18n_key = isset($facility_map[$f_name_raw]) ? $facility_map[$f_name_raw] : '';
            
            if($f_i18n_key) {
                $fac_html .= "<span class='badge bg-light text-dark me-1 mb-1'>" . t($f_i18n_key, $current_lang) . "</span>";
            } else {
                $fac_html .= "<span class='badge bg-light text-dark me-1 mb-1'>{$f_name}</span>";
            }
        }
        
        // Features
        $fea_html = "";
        // Đóng result set cũ nếu có
        if(isset($fea_q) && $fea_q instanceof mysqli_result){
            mysqli_free_result($fea_q);
        }
        $fea_stmt->bind_param('i', $row['id']);
        $fea_stmt->execute();
        $fea_q = $fea_stmt->get_result();
        
        // Map các feature names phổ biến để dịch
        $feature_map = [
            'Phòng Ngủ' => 'features.bedroom',
            'Ban Công' => 'features.balcony',
            'Nhà Bếp' => 'features.kitchen',
            'Ghế Sofa' => 'features.sofa',
            'Phòng Tắm' => 'features.bathroom',
            'Phòng Khách' => 'features.livingRoom',
            'Tủ Lạnh' => 'features.refrigerator',
            'Máy Lạnh' => 'features.airConditioner',
            'TV' => 'features.tv',
            'WiFi' => 'features.wifi',
        ];
        
        while($fea = mysqli_fetch_assoc($fea_q)){
            $fea_name_raw = $fea['name'] ?? '';
            $fea_name = htmlspecialchars($fea_name_raw, ENT_QUOTES, 'UTF-8');
            $fea_i18n_key = isset($feature_map[$fea_name_raw]) ? $feature_map[$fea_name_raw] : '';
            
            if($fea_i18n_key) {
                $fea_html .= "<span class='badge bg-light text-dark me-1 mb-1'>" . t($fea_i18n_key, $current_lang) . "</span>";
            } else {
                $fea_html .= "<span class='badge bg-light text-dark me-1 mb-1'>{$fea_name}</span>";
            }
        }
        
        // Favorite
        $is_fav = in_array((int)$row['id'],$fav_ids);
        $fav_class = $is_fav ? 'active' : '';
        $fav_icon = $is_fav ? 'bi-heart-fill' : 'bi-heart';
        
        // Hiển thị tên owner/admin
        $owner_display = '';
        if(!empty($row['owner_name']) || !empty($row['hotel_name'])){
            $owner_label = !empty($row['hotel_name']) ? htmlspecialchars($row['hotel_name'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($row['owner_name'], ENT_QUOTES, 'UTF-8');
            $owner_display = "<small class='text-muted d-block mb-1' style='font-size: 0.75rem;'>
                <i class='bi bi-building me-1'></i>{$owner_label}
            </small>";
        } else {
            $owner_display = "<small class='text-muted d-block mb-1' style='font-size: 0.75rem;'>
                <i class='bi bi-shield-check me-1'></i>Admin
            </small>";
        }
        
        $output .= "
        <article class='list-card'>
            <div>
                <div class='list-card__img-wrap'>
                  <img src='$thumb' class='list-card__img' alt='Ảnh phòng'>
                  <button class=\"home-fav-btn $fav_class\" data-room=\"{$row['id']}\" title=\"Yêu thích\"><i class=\"bi $fav_icon\"></i></button>
                </div>
                <div class='d-flex flex-wrap gap-2 mt-2'>
                    <span class='list-card__badge'><i class='bi bi-check2-circle me-1'></i>$status_text</span>
                </div>
            </div>
            <div class='list-card__info'>
                <h5 class='mb-0'>{$room_name}</h5>
                {$owner_display}
                <div class='list-card__meta mb-2'>
                    <i class='bi bi-geo-alt me-1'></i> {$location_text}
                    <span class='ms-2'><i class='bi bi-people me-1'></i> {$guest_text}</span>
                </div>
                <div class='mb-2 d-flex align-items-center gap-2'>
                    $stars_html
                    <span class='badge bg-light text-dark'>".($avg_star ? "{$avg_star}/5" : t('rooms.new', $current_lang))."</span>
                </div>
                <div class='list-card__tags mb-2'>
                    <div class='small text-uppercase text-muted fw-semibold mb-1'>" . t('room.space', $current_lang) . "</div>
                    $fea_html
                </div>
                <div class='list-card__tags'>
                    <div class='small text-uppercase text-muted fw-semibold mb-1'>" . t('room.facilities', $current_lang) . "</div>
                    $fac_html
                </div>
            </div>
            <div class='list-card__price d-flex flex-column justify-content-between'>
                <div class='text-end'>
                    <div class='list-card__price-old'>{$old_price} đ</div>
                    <div class='list-card__price-new'>{$price_fmt} đ {$discount_badge}</div>
                    <div class='small text-success fw-semibold'>" . t('promo.offers', $current_lang) . " · " . t('rooms.freeCancel', $current_lang) . "</div>
                    <div class='small text-muted'>" . t('rooms.priceNote', $current_lang) . "</div>
                </div>
                <div class='list-card__cta'>
                    $book_btn
                    <a href='room_details.php?id={$row['id']}' class='btn btn-outline-dark btn-sm w-100'>" . t('rooms.details', $current_lang) . "</a>
                </div>
            </div>
        </article>";
        $count++;
    }

    /* =====================================================
       6. TẠO HTML PHÂN TRANG
    ===================================================== */
    $pagination_html = "";
    if($total_pages > 1){
        $pagination_html = "<div class='pagination-wrapper mt-4 mb-3'>";
        $pagination_html .= "<nav aria-label='Phân trang'>";
        $pagination_html .= "<ul class='pagination justify-content-center flex-wrap'>";
        
        // Nút Previous
        if($page > 1){
            $prev_page = $page - 1;
            $pagination_html .= "<li class='page-item'><a class='page-link' href='#' onclick='goToPage($prev_page); return false;'><i class='bi bi-chevron-left'></i> <span data-i18n='rooms.prev'>Trước</span></a></li>";
        } else {
            $pagination_html .= "<li class='page-item disabled'><span class='page-link'><i class='bi bi-chevron-left'></i> <span data-i18n='rooms.prev'>Trước</span></span></li>";
        }
        
        // Các số trang
        $start_page = max(1, $page - 2);
        $end_page = min($total_pages, $page + 2);
        
        if($start_page > 1){
            $pagination_html .= "<li class='page-item'><a class='page-link' href='#' onclick='goToPage(1); return false;'>1</a></li>";
            if($start_page > 2){
                $pagination_html .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
            }
        }
        
        for($i = $start_page; $i <= $end_page; $i++){
            if($i == $page){
                $pagination_html .= "<li class='page-item active'><span class='page-link'>{$i}</span></li>";
            } else {
                $pagination_html .= "<li class='page-item'><a class='page-link' href='#' onclick='goToPage($i); return false;'>{$i}</a></li>";
            }
        }
        
        if($end_page < $total_pages){
            if($end_page < $total_pages - 1){
                $pagination_html .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
            }
            $pagination_html .= "<li class='page-item'><a class='page-link' href='#' onclick='goToPage($total_pages); return false;'>{$total_pages}</a></li>";
        }
        
        // Nút Next
        if($page < $total_pages){
            $next_page = $page + 1;
            $pagination_html .= "<li class='page-item'><a class='page-link' href='#' onclick='goToPage($next_page); return false;'><span data-i18n='rooms.next'>Sau</span> <i class='bi bi-chevron-right'></i></a></li>";
        } else {
            $pagination_html .= "<li class='page-item disabled'><span class='page-link'><span data-i18n='rooms.next'>Sau</span> <i class='bi bi-chevron-right'></i></span></li>";
        }
        
        $pagination_html .= "</ul>";
        $pagination_html .= "</nav>";
        $start_num = (($page-1)*$per_page + 1);
        $end_num = min($page*$per_page, $total_rooms);
        $pagination_html .= "<div class='text-center text-muted small mt-2'><span data-i18n='rooms.showing'>Hiển thị</span> {$start_num}-{$end_num} <span data-i18n='rooms.ofTotal'>trong tổng số</span> {$total_rooms} <span data-i18n='room.rooms'>phòng</span></div>";
        $pagination_html .= "</div>";
    }

    /* =====================================================
       7. RETURN
    ===================================================== */
    if($count > 0){
        echo $output . $pagination_html;
    } else {
        echo "<h3 class='text-center text-danger' data-i18n='rooms.noRoomsFound'>Không có phòng phù hợp!</h3>";
    }
}

// ===================== LẤY PHÒNG GẦN ĐÂY (RECENTLY VIEWED) =====================
if(isset($_GET['recently_viewed']) && $_GET['recently_viewed'] == '1') {
    $room_ids_json = $_GET['room_ids'] ?? '[]';
    $room_ids = json_decode($room_ids_json, true);
    
    if(!is_array($room_ids) || empty($room_ids)) {
        echo json_encode(['status' => 'empty']);
        exit;
    }
    
    // Validate room IDs
    $room_ids = array_filter(array_map('intval', $room_ids));
    if(empty($room_ids)) {
        echo json_encode(['status' => 'empty']);
        exit;
    }
    
    // Giới hạn tối đa 6 phòng
    $room_ids = array_slice($room_ids, 0, 6);
    $placeholders = implode(',', array_fill(0, count($room_ids), '?'));
    
    // Kiểm tra cột approved
    $has_approved = false;
    $check_approved = mysqli_query($con, "SHOW COLUMNS FROM `rooms` LIKE 'approved'");
    if($check_approved && mysqli_num_rows($check_approved) > 0){
        $has_approved = true;
    }
    $approved_condition = $has_approved ? " AND r.approved = 1" : "";
    
    // Query lấy thông tin phòng
    $query = "SELECT r.*, 
              COALESCE(AVG(rr.rating), 0) AS avg_rating, 
              COUNT(rr.sr_no) AS review_count,
              ho.name AS owner_name, ho.hotel_name
              FROM `rooms` r
              LEFT JOIN `rating_review` rr ON r.id = rr.room_id
              LEFT JOIN `hotel_owners` ho ON r.owner_id = ho.id
              WHERE r.id IN ($placeholders) 
              AND r.status = 1 
              AND r.removed = 0
              $approved_condition
              AND (r.owner_id IS NULL OR ho.status = 1)
              GROUP BY r.id
              ORDER BY FIELD(r.id, $placeholders)";
    
    $params = array_merge($room_ids, $room_ids);
    $types = str_repeat('i', count($room_ids) * 2);
    
    $result = select($query, $params, $types);
    
    $rooms = [];
    if($result && mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            // Lấy thumbnail
            $thumb = ROOMS_IMG_PATH . "thumbnail.jpg";
            $thumb_query = "SELECT image FROM `room_images` WHERE `room_id` = ? AND `thumb` = 1 LIMIT 1";
            $thumb_result = select($thumb_query, [$row['id']], 'i');
            if($thumb_result && mysqli_num_rows($thumb_result) > 0) {
                $thumb_data = mysqli_fetch_assoc($thumb_result);
                $thumb = ROOMS_IMG_PATH . $thumb_data['image'];
            }
            
            $rooms[] = [
                'id' => (int)$row['id'],
                'name' => t_room_name($row['name'], $current_lang),
                'location' => $row['location'] ?? 'Vĩnh Long',
                'price' => (int)$row['price'],
                'discount' => (int)($row['discount'] ?? 0),
                'adult' => (int)$row['adult'],
                'children' => (int)$row['children'],
                'area' => (int)$row['area'],
                'avg_rating' => round((float)$row['avg_rating'], 1),
                'review_count' => (int)$row['review_count'],
                'thumb' => $thumb,
                'owner_name' => $row['owner_name'] ?? '',
                'hotel_name' => $row['hotel_name'] ?? ''
            ];
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok', 'rooms' => $rooms]);
    exit;
}

?>
