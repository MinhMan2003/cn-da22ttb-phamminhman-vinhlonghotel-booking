<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$current_page = basename($_SERVER['PHP_SELF']);

// ----- User mini-data cho dropdown -----
// Lấy ngôn ngữ hiện tại - Mặc định là tiếng Việt
$lang_cookie = isset($_COOKIE['lang']) ? trim($_COOKIE['lang']) : '';
$current_lang = ($lang_cookie === 'en') ? 'en' : 'vi';
if ($current_lang !== 'en' && $current_lang !== 'vi') {
  $current_lang = 'vi';
}

$notif_total = 0;
$last_booking_text = 'Chưa có đơn nào';
$last_booking_meta = '';
$tier_label = 'Thành viên mới';
$ui_lang = $_COOKIE['lang'] ?? 'vi';
$ui_currency = $_COOKIE['currency'] ?? 'vnd';
$unread_messages_count = 0; // Khởi tạo biến để tránh lỗi

if(isset($_SESSION['login']) && $_SESSION['login']==true){
  $uId = $_SESSION['uId'] ?? 0;
  if($uId){
    $status_class = 'status-default';
    // Thống kê nhanh
    $stat_res = select("SELECT 
        SUM(booking_status='pending') AS pending,
        SUM(booking_status='cancelled' AND refund=0) AS refund_wait,
        SUM(booking_status='booked' AND arrival=1) AS completed,
        COUNT(*) AS total
      FROM booking_order WHERE user_id=?", [$uId], 'i');
    $stat_row = $stat_res ? mysqli_fetch_assoc($stat_res) : ['pending'=>0,'refund_wait'=>0,'completed'=>0,'total'=>0];
    $notif_total = (int)($stat_row['pending'] + $stat_row['refund_wait']);
    $completed = (int)$stat_row['completed'];
    // Xác định hạng và dịch theo ngôn ngữ
    $tier_slug = 'member';
    $tier_icon = 'bi-person-badge';
    $tier_i18n_key = 'tier.newMember';
    $tier_label = 'Thành viên mới';
    
    if($completed >= 10){ 
      $tier_slug = 'platinum';
      $tier_icon = 'bi-gem';
      $tier_i18n_key = 'tier.platinumTier';
      $tier_label = 'Hạng Platinum';
    }
    else if($completed >= 5){ 
      $tier_slug = 'gold';
      $tier_icon = 'bi-star-fill';
      $tier_i18n_key = 'tier.goldTier';
      $tier_label = 'Hạng Gold';
    }
    else if($completed >= 1){ 
      $tier_slug = 'silver';
      $tier_icon = 'bi-award';
      $tier_i18n_key = 'tier.silverTier';
      $tier_label = 'Hạng Silver';
    }

    // Tiến độ thăng hạng
    $tiers = [
      ['label'=>'Silver','threshold'=>1],
      ['label'=>'Gold','threshold'=>5],
      ['label'=>'Platinum','threshold'=>10],
    ];
    $next_label = 'Platinum';
    $next_threshold = 10;
    foreach($tiers as $t){
      if($completed < $t['threshold']){
        $next_label = $t['label'];
        $next_threshold = $t['threshold'];
        break;
      }
    }
    $remaining = max(0, $next_threshold - $completed);
    $progress_pct = min(100, round(($completed / $next_threshold) * 100));
    
    // Dịch progress text theo ngôn ngữ
    if($completed >= $next_threshold) {
      $progress_text = $current_lang === 'en' 
        ? 'You are at the highest tier.'
        : 'Bạn đang ở hạng cao nhất.';
    } else {
      // Dịch tên hạng
      $next_label_translated = $next_label;
      if($current_lang === 'en') {
        $tier_name_map = [
          'Silver' => 'Silver',
          'Gold' => 'Gold',
          'Platinum' => 'Platinum'
        ];
        $next_label_translated = $tier_name_map[$next_label] ?? $next_label;
        $progress_text = "{$remaining} more order" . ($remaining > 1 ? 's' : '') . " to reach {$next_label_translated}.";
      } else {
        $tier_name_map = [
          'Silver' => 'Silver',
          'Gold' => 'Gold',
          'Platinum' => 'Platinum'
        ];
        $next_label_translated = $tier_name_map[$next_label] ?? $next_label;
        $progress_text = "Còn {$remaining} đơn nữa để lên {$next_label_translated}.";
      }
    }

    // Đếm tin nhắn chưa đọc
    $unread_messages_count = 0;
    if(!isset($con)) {
      if(isset($GLOBALS['con'])) {
        $con = $GLOBALS['con'];
      } else {
        if(!isset($con)) {
          require_once('admin/inc/db_config.php');
        }
      }
    }
    if(isset($con)) {
      $check_table = @mysqli_query($con, "SHOW TABLES LIKE 'messages'");
      if($check_table && mysqli_num_rows($check_table) > 0) {
        $user_id = (int)$uId;
        $sql = "SELECT COUNT(*) AS c FROM messages WHERE user_id = $user_id AND sender_type IN ('admin', 'owner') AND seen = 0";
        $query_result = @mysqli_query($con, $sql);
        if($query_result) {
          $result_row = mysqli_fetch_assoc($query_result);
          $unread_messages_count = isset($result_row['c']) ? (int)$result_row['c'] : 0;
        }
      }
    }

    // Đơn gần nhất
    $last_res = select("SELECT bo.booking_status, bo.check_in, bo.check_out, bo.datentime, bo.refund, bo.arrival, bo.order_id, r.name AS room_name 
                        FROM booking_order bo 
                        LEFT JOIN rooms r ON bo.room_id = r.id
                        WHERE bo.user_id=?
                        ORDER BY bo.booking_id DESC LIMIT 1", [$uId], 'i');
    if($last_res && mysqli_num_rows($last_res)){
      $lr = mysqli_fetch_assoc($last_res);
      $room_name = $lr['room_name'] ?: ($current_lang === 'en' ? 'Booking' : 'Đặt phòng');
      
      // Dịch status theo ngôn ngữ
      if($current_lang === 'en') {
        $status_map = [
          'booked' => ['Booked','status-booked'],
          'pending' => ['Pending','status-pending'],
          'cancelled' => [($lr['refund'] ? 'Cancelled • Refunded' : 'Cancelled • Pending refund'),'status-cancel'],
          'payment failed' => ['Payment failed','status-failed']
        ];
        $booking_date_label = 'Booking date';
        $order_code_label = 'Order code';
      } else {
        $status_map = [
          'booked' => ['Đã đặt phòng','status-booked'],
          'pending' => ['Đang chờ','status-pending'],
          'cancelled' => [($lr['refund'] ? 'Đã hủy • Đã hoàn' : 'Đã hủy • Chờ hoàn'),'status-cancel'],
          'payment failed' => ['Thanh toán lỗi','status-failed']
        ];
        $booking_date_label = 'Ngày đặt';
        $order_code_label = 'Mã đơn';
      }
      
      [$status_txt, $status_class] = $status_map[$lr['booking_status']] ?? [ucfirst($lr['booking_status']), 'status-default'];
      $date_txt = date('d/m', strtotime($lr['datentime'] ?? $lr['check_in']));
      $last_booking_text = "{$room_name} • {$status_txt}";
      $last_booking_meta = "{$booking_date_label}: {$date_txt} • {$order_code_label}: {$lr['order_id']}";
    } else {
      // Dịch "Chưa có đơn nào"
      $last_booking_text = $current_lang === 'en' ? 'No bookings yet' : 'Chưa có đơn nào';
    }
  }
}
?>

<nav id="nav-bar" class="navbar navbar-expand-lg navbar-light bg-nav shadow-sm">
  <div class="container px-lg-3">
    <div class="d-flex align-items-center w-100">
      <!-- Logo - Bên trái riêng -->
      <a class="navbar-brand d-flex align-items-center gap-2 fw-bold h-font modern-logo me-auto" href="index.php" style="flex-shrink: 0;">
        <?php 
        // Load settings nếu chưa có
        if(!isset($settings_r)) {
          if(!isset($con)) {
            if(isset($GLOBALS['con'])) {
              $con = $GLOBALS['con'];
            } else {
              require_once('admin/inc/db_config.php');
            }
          }
          if(isset($con)) {
            $settings_res = @mysqli_query($con, "SELECT * FROM `settings` WHERE `sr_no`=1");
            if($settings_res && mysqli_num_rows($settings_res) > 0) {
              $settings_r = mysqli_fetch_assoc($settings_res);
            }
          }
        }
        
        // Kiểm tra xem có logo trong settings không
        $has_logo = isset($settings_r) && isset($settings_r['site_logo']) && !empty($settings_r['site_logo']);
        $logo_path = '';
        $site_title = isset($settings_r) && isset($settings_r['site_title']) ? htmlspecialchars($settings_r['site_title'], ENT_QUOTES, 'UTF-8') : 'Vĩnh Long Hotel';
        
        if($has_logo) {
          // Ưu tiên logo từ settings
          $logo_path = 'images/about/' . htmlspecialchars($settings_r['site_logo'], ENT_QUOTES, 'UTF-8');
          if(!file_exists($logo_path)) {
            $logo_path = '';
          }
        }
        
        // Nếu chưa có logo, kiểm tra logo trong thư mục logo
        if(empty($logo_path)) {
          $logo_files = ['logo/Vĩnh Long Hotel.png', 'logo/logo.png', 'images/logo.png'];
          foreach($logo_files as $file) {
            if(file_exists($file)) {
              $logo_path = $file;
              break;
            }
          }
        }
        
        // Luôn hiển thị logo (nếu có) và text tiêu đề
        if(!empty($logo_path) && file_exists($logo_path)) {
          // Hiển thị ảnh logo với cache buster
          $logo_url = htmlspecialchars($logo_path, ENT_QUOTES, 'UTF-8') . '?v=' . time();
          echo '<img src="' . $logo_url . '" alt="' . $site_title . '" class="site-logo-img" style="max-height: 50px; max-width: 200px; object-fit: contain;" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">';
          echo '<div class="logo-icon-wrapper" style="display:none;">
            <i class="bi bi-building"></i>
          </div>';
        } else {
          // Hiển thị icon nếu không có logo
          echo '<div class="logo-icon-wrapper">
            <i class="bi bi-building"></i>
          </div>';
        }
        // Luôn hiển thị text tiêu đề trang
        echo '<span class="logo-text">' . $site_title . '</span>';
        ?>
      </a>

      <!-- Navbar Toggler -->
      <button class="navbar-toggler shadow-none border-0 ms-auto ms-lg-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-label="Mở menu" style="flex-shrink: 0;">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center" style="flex-wrap: nowrap; gap: 4px;">
        <li class="nav-item" style="white-space: nowrap;">
          <a class="nav-link nav-pill-modern <?php if($current_page=='index.php') echo 'active'; ?>" href="index.php">
            <span class="d-none d-xl-inline" data-i18n="nav.home">Trang chủ</span>
            <span class="d-xl-none" data-i18n="nav.home">Trang chủ</span>
          </a>
        </li>
        <li class="nav-item" style="white-space: nowrap;">
          <a class="nav-link nav-pill-modern <?php if($current_page=='rooms.php') echo 'active'; ?>" href="rooms.php">
            <span data-i18n="nav.rooms">Phòng</span>
          </a>
        </li>
        <li class="nav-item" style="white-space: nowrap;">
          <a class="nav-link nav-pill-modern <?php if($current_page=='facilities.php') echo 'active'; ?>" href="facilities.php">
            <span class="d-none d-lg-inline" data-i18n="nav.facilities">Tiện ích</span>
            <span class="d-lg-none" data-i18n="nav.facilities">Tiện ích</span>
          </a>
        </li>
        <li class="nav-item" style="white-space: nowrap;">
          <a class="nav-link nav-pill-modern <?php if($current_page=='destinations.php') echo 'active'; ?>" href="destinations.php">
            <span class="d-none d-lg-inline" data-i18n="nav.destinations">Điểm đến & đặc sản</span>
            <span class="d-lg-none" data-i18n="nav.destinations">Điểm đến </span>
          </a>
        </li>
        <li class="nav-item" style="white-space: nowrap;">
          <a class="nav-link nav-pill-modern <?php if($current_page=='about.php') echo 'active'; ?>" href="about.php">
            <span class="d-none d-xl-inline" data-i18n="nav.about">Về chúng tôi</span>
            <span class="d-xl-none" data-i18n="nav.about">Về chúng tôi</span>
          </a>
        </li>
        <li class="nav-item" style="white-space: nowrap;">
          <a class="nav-link nav-pill-modern <?php if($current_page=='contact.php') echo 'active'; ?>" href="contact.php">
            <span class="d-none d-xl-inline" data-i18n="nav.contact">Liên hệ</span>
            <span class="d-xl-none" data-i18n="nav.contact">Liên hệ</span>
          </a>
        </li>
      </ul>

      <div class="d-flex align-items-center gap-2 ms-lg-3" style="flex-wrap: nowrap;">

        <!-- Language Toggle -->
        <button id="language-toggle" class="language-toggle-btn" type="button" aria-label="Chuyển đổi ngôn ngữ" title="Chuyển đổi ngôn ngữ">
          <i class="bi bi-translate"></i>
          <span class="lang-text d-none d-md-inline ms-1" style="font-size: 12px; font-weight: 600;">VI</span>
        </button>

        <?php 
        // Kiểm tra owner đã đăng nhập chưa
        $owner_logged_in = isset($_SESSION['ownerLogin']) && $_SESSION['ownerLogin'] == true;
        
        if(isset($_SESSION['login']) && $_SESSION['login']==true){
            // ⚠️ Đảm bảo lấy ảnh từ database để luôn có ảnh mới nhất
            $pic = 'user.png'; // Mặc định
            $user_name = $_SESSION['uName'] ?? '';
            $user_gender = 'male'; // Mặc định
            $uId = $_SESSION['uId'] ?? 0;
            if($uId > 0) {
                // Luôn load ảnh từ database để đảm bảo có ảnh mới nhất
                $user_pic_res = select("SELECT profile, name, gender FROM `user_cred` WHERE `id`=? LIMIT 1", [$uId], 'i');
                if($user_pic_res && mysqli_num_rows($user_pic_res) > 0) {
                    $user_pic_row = mysqli_fetch_assoc($user_pic_res);
                    $db_pic = $user_pic_row['profile'] ?? 'user.png';
                    $user_name = $user_pic_row['name'] ?? $user_name;
                    $user_gender = $user_pic_row['gender'] ?? 'male';
                    if(!empty($db_pic)) {
                        $pic = $db_pic;
                        // Cập nhật session với ảnh từ database
                        $_SESSION['uPic'] = $pic;
                    }
                }
            }
            
            // Kiểm tra xem có ảnh thật không (không phải user.png hoặc avatar tự tạo)
            $has_real_image = false;
            $avatar_path = '';
            // Chỉ kiểm tra nếu pic không phải là 'user.png' và không rỗng
            if($pic != 'user.png' && !empty($pic) && $pic != '') {
                $avatar_path = filter_var($pic, FILTER_VALIDATE_URL) ? $pic : (defined('USERS_IMG_PATH') ? USERS_IMG_PATH : 'images/users/') . $pic;
                // Kiểm tra xem file có tồn tại không
                $full_path = filter_var($pic, FILTER_VALIDATE_URL) ? '' : (defined('UPLOAD_IMAGE_PATH') ? UPLOAD_IMAGE_PATH . USERS_FOLDER . $pic : '');
                if($full_path && file_exists($full_path)) {
                    $has_real_image = true;
                } elseif(filter_var($pic, FILTER_VALIDATE_URL)) {
                    $has_real_image = true; // URL bên ngoài
                }
            }
            // Nếu pic là 'user.png' hoặc rỗng, không có ảnh thật -> sẽ hiển thị chữ cái đầu
            
            // Tạo chữ cái đầu nếu không có ảnh
            $user_initial = 'U';
            if(!empty($user_name)) {
                $user_initial = mb_strtoupper(mb_substr(trim($user_name), 0, 1, 'UTF-8'), 'UTF-8');
            }
            
            // Màu nền theo giới tính
            $avatar_bg_color = '#4A90E2'; // Xanh dương cho nam
            if($user_gender == 'female') {
                $avatar_bg_color = '#E94B7D'; // Hồng cho nữ
            }
            
            $badge_html = "";
            $bookings_badge_html = "";
            // Tạo badge cho tin nhắn
            $messages_badge_html = "";
            if(isset($unread_messages_count) && is_numeric($unread_messages_count) && $unread_messages_count > 0) {
                $messages_badge_html = '<span class="badge bg-danger ms-2">' . (int)$unread_messages_count . '</span>';
            }
            
            // HTML cho avatar
            if($has_real_image) {
                // Thêm onerror handler để fallback về chữ cái đầu nếu ảnh không load được
                $avatar_html = '<img src="' . htmlspecialchars($avatar_path, ENT_QUOTES, 'UTF-8') . '" class="avatar-img" alt="Avatar" onerror="this.onerror=null; this.style.display=\'none\'; const parent=this.parentElement; if(parent && !parent.querySelector(\'.avatar-initial\')) { const fallback=document.createElement(\'div\'); fallback.className=\'avatar-initial\'; fallback.style.cssText=\'background-color: ' . htmlspecialchars($avatar_bg_color, ENT_QUOTES, 'UTF-8') . '; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 16px;\'; fallback.textContent=\'' . htmlspecialchars($user_initial, ENT_QUOTES, 'UTF-8') . '\'; parent.appendChild(fallback); }">';
            } else {
                $avatar_html = '<div class="avatar-initial" style="background-color: ' . htmlspecialchars($avatar_bg_color, ENT_QUOTES, 'UTF-8') . '; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 16px;">' . htmlspecialchars($user_initial, ENT_QUOTES, 'UTF-8') . '</div>';
            }
            
            echo <<<HTML
            <div class="btn-group">
              <button type="button" class="btn btn-outline-primary shadow-none dropdown-toggle user-pill-modern"
                      data-bs-toggle="dropdown" aria-expanded="false">
                <span class="avatar-wrap-modern">
                  $avatar_html
                  $badge_html
                </span>
                <span class="fw-semibold user-name-text">{$_SESSION['uName']}</span>
                <span class="user-pill-separator"></span>
                <span class="tier-display-pill tier-{$tier_slug}">
                  <i class="bi {$tier_icon}"></i>
                  <span class="tier-text-pill" data-i18n="{$tier_i18n_key}">{$tier_label}</span>
                </span>
                <i class="bi bi-chevron-down ms-1"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow-lg modern-dropdown">
                <li class="px-3 pt-3 pb-3">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted" data-i18n="auth.lastBooking">Đơn gần nhất</span>
                    <span class="tier-badge tier-{$tier_slug}" title="{$progress_text}">
                      <i class="bi {$tier_icon}"></i><span data-i18n="{$tier_i18n_key}">{$tier_label}</span>
                    </span>
                  </div>
                  <div class="d-flex align-items-start gap-2 last-booking">
                    <span class="status-dot {$status_class}"></span>
                    <div>
                      <div class="fw-semibold text-dark">$last_booking_text</div>
                      <div class="text-muted small">$last_booking_meta</div>
                    </div>
                  </div>
                  <div class="mt-2">
                    <div class="d-flex justify-content-between small text-muted">
                      <span data-i18n="auth.tierProgress">Tiến độ thăng hạng</span>
                      <span>{$completed}/{$next_threshold}</span>
                    </div>
                    <div class="progress tier-progress" role="progressbar" data-i18n-aria="auth.tierProgress" aria-valuenow="{$progress_pct}" aria-valuemin="0" aria-valuemax="100">
                      <div class="progress-bar" style="width: {$progress_pct}%;"></div>
                    </div>
                    <div class="small text-muted mt-1">{$progress_text}</div>
                  </div>
                </li>
                <li><hr class="dropdown-divider-modern"></li>
                <li>
                  <a class="dropdown-item-modern" href="profile.php">
                    <i class="bi bi-person-badge-fill dropdown-icon-main"></i>
                    <span data-i18n="auth.profile">Hồ sơ & bảo mật</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item-modern" href="bookings.php">
                    <i class="bi bi-calendar-check-fill dropdown-icon-main"></i>
                    <span data-i18n="auth.bookings">Lịch sử đặt phòng</span>
                    $bookings_badge_html
                    <i class="bi bi-chevron-right ms-auto"></i>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item-modern" href="messages.php">
                    <i class="bi bi-chat-dots-fill dropdown-icon-main"></i>
                    <span data-i18n="auth.messages">Tin nhắn</span>
                    $messages_badge_html
                    <i class="bi bi-chevron-right ms-auto"></i>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item-modern" href="payment_history.php">
                    <i class="bi bi-credit-card-2-front-fill dropdown-icon-main"></i>
                    <span data-i18n="auth.paymentHistory">Lịch sử thanh toán</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                  </a>
                </li>
                <li>
                  <button type="button" class="dropdown-item-modern w-100 text-start" onclick="openLoginModal()">
                    <i class="bi bi-arrow-repeat dropdown-icon-main"></i>
                    <span data-i18n="auth.loginOther">Đăng nhập tài khoản khác</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                  </button>
                </li>
                <li><hr class="dropdown-divider-modern"></li>
                <li>
                  <a class="dropdown-item-modern" href="owner/index.php" target="_blank">
                    <i class="bi bi-building dropdown-icon-main"></i>
                    <span data-i18n="auth.ownerLogin">Đăng nhập Chủ khách sạn</span>
                    <i class="bi bi-box-arrow-in-right ms-auto"></i>
                  </a>
                </li>
                <li><hr class="dropdown-divider-modern"></li>
                <li>
                  <a class="dropdown-item-modern text-danger" href="logout.php">
                    <i class="bi bi-box-arrow-right dropdown-icon-main"></i>
                    <span data-i18n="auth.logout">Đăng xuất</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                  </a>
                </li>
              </ul>
            </div>
            HTML;
        } else {
            echo <<<HTML
              <button type="button" class="btn btn-login shadow-none rounded-pill px-3 px-md-4 btn-auth"
                      data-bs-toggle="modal" data-bs-target="#loginModal">
                <span class="d-none d-md-inline" data-i18n="auth.login">Đăng nhập</span>
                <span class="d-md-none" data-i18n="auth.login">Đăng nhập</span>
              </button>
              <button type="button" class="btn btn-register shadow-none rounded-pill px-3 px-md-4 btn-auth"
                      data-bs-toggle="modal" data-bs-target="#registerModal">
                <span class="d-none d-md-inline" data-i18n="auth.register">Đăng ký</span>
                <span class="d-md-none" data-i18n="auth.register">Đăng ký</span>
              </button>
              <div class="dropdown">
                <button class="btn btn-owner shadow-none rounded-pill px-2 px-md-3 btn-auth" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Chủ khách sạn">
                  <span class="d-none d-lg-inline" data-i18n="auth.owner">Chủ KS</span>
                  <span class="d-lg-none" data-i18n="auth.owner">Chủ KS</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                  <li><a class="dropdown-item" href="owner/index.php"><i class="bi bi-box-arrow-in-right me-2"></i><span data-i18n="auth.login">Đăng nhập</span></a></li>
                  <li><a class="dropdown-item" href="owner/register.php"><i class="bi bi-person-plus me-2"></i><span data-i18n="auth.register">Đăng ký</span></a></li>
                </ul>
              </div>
            HTML;
        }
        ?>
      </div>
    </div>
  </div>
</nav>

<!-- Toast -->
<script>
function showToast(type, message, duration = 3000){
    // Xóa toast cũ nếu có để tránh trùng lặp
    const existingToasts = document.querySelectorAll('.custom-toast');
    existingToasts.forEach(toast => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 100);
    });
    
    const colors = {success:'#27ae60', warning:'#f1c40f', error:'#e74c3c'};
    let toast = document.createElement('div');
    toast.className = 'custom-toast';
    toast.style.background = colors[type] || colors.error;
    toast.innerHTML = `<span>${message}</span>`;
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 50);
    setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, duration);
}
</script>

<?php 
// Hiển thị lỗi đăng nhập Google nếu có
if(isset($_SESSION['google_login_error']) && !empty($_SESSION['google_login_error'])){
    $google_error = $_SESSION['google_login_error'];
    unset($_SESSION['google_login_error']);
    ?>
    <script>
    window.addEventListener('load', function() {
        if(typeof showToast === 'function') {
            setTimeout(function() {
                showToast('error', <?php echo json_encode($google_error, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>, 5000);
            }, 500);
        } else {
            alert(<?php echo json_encode($google_error, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>);
        }
    });
    </script>
    <?php
}

// Lấy thông tin user để cập nhật localStorage (cho cả đăng nhập thường và Google)
$user_email = '';
$user_name = '';
$user_profile = '';
$isGoogleAccount = false;
if(isset($_SESSION['login']) && $_SESSION['login'] == true && isset($_SESSION['uId'])) {
    // Lấy thông tin từ database
    if(isset($con) || isset($GLOBALS['con'])) {
        $db_con = isset($con) ? $con : $GLOBALS['con'];
        if($db_con) {
            $user_query = "SELECT email, name, profile, password FROM user_cred WHERE id = ? LIMIT 1";
            $user_stmt = mysqli_prepare($db_con, $user_query);
            if($user_stmt) {
                mysqli_stmt_bind_param($user_stmt, "i", $_SESSION['uId']);
                mysqli_stmt_execute($user_stmt);
                $user_result = mysqli_stmt_get_result($user_stmt);
                if($user_row = mysqli_fetch_assoc($user_result)) {
                    $user_email = $user_row['email'];
                    $user_name = $user_row['name'];
                    $user_profile = $user_row['profile'];
                    $isGoogleAccount = ($user_row['password'] === 'google');
                }
                mysqli_stmt_close($user_stmt);
            }
        }
    }
}

// Hiển thị thông báo đăng nhập nếu có (chỉ hiện 1 lần)
if(isset($_SESSION['login_msg']) && !empty($_SESSION['login_msg'])){
    $login_msg = $_SESSION['login_msg'];
    // Xóa thông báo ngay để tránh hiển thị lại
    unset($_SESSION['login_msg']);
    ?>
    <script>
    // Cập nhật thông tin user vào localStorage sau khi đăng nhập thành công
    <?php if(!empty($user_email)): ?>
    (function() {
        try {
            var savedAccounts = JSON.parse(localStorage.getItem('saved_accounts') || '[]');
            var accountIndex = savedAccounts.findIndex(function(acc) {
                return acc.email_mob === '<?php echo addslashes($user_email); ?>';
            });
            
            var isGoogleAccount = <?php echo $isGoogleAccount ? 'true' : 'false'; ?>;
            
            if(accountIndex >= 0) {
                // Cập nhật thông tin name và profile
                savedAccounts[accountIndex].name = <?php echo json_encode($user_name, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;
                savedAccounts[accountIndex].profile = <?php echo json_encode($user_profile, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;
                if(isGoogleAccount) {
                    // Đánh dấu đây là tài khoản Google (không có mật khẩu)
                    savedAccounts[accountIndex].isGoogle = true;
                    savedAccounts[accountIndex].password = ''; // Xóa mật khẩu nếu có
                }
                localStorage.setItem('saved_accounts', JSON.stringify(savedAccounts));
            } else {
                // Nếu chưa có trong danh sách, thêm mới (cho cả tài khoản Google và thường)
                if(typeof saveAccountToLocal === 'function') {
                    saveAccountToLocal('<?php echo addslashes($user_email); ?>', '', <?php echo json_encode($user_name, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>, <?php echo json_encode($user_profile, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>, isGoogleAccount);
                }
            }
        } catch(e) {
            console.error('Lỗi khi cập nhật thông tin user:', e);
        }
    })();
    <?php endif; ?>
    
    // Hiển thị thông báo đăng nhập - đảm bảo chạy sau khi showToast đã được định nghĩa
    window.addEventListener('load', function() {
        var loginMessage = <?php echo json_encode($login_msg, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;
        var shown = false;
        
        function displayLoginMsg() {
            if(shown) return;
            if(typeof showToast === 'function') {
                shown = true;
                setTimeout(function() {
                    showToast('success', loginMessage, 4000);
                }, 500);
                return;
            }
            // Nếu showToast chưa có, thử lại sau 200ms
            setTimeout(displayLoginMsg, 200);
        }
        
        // Bắt đầu hiển thị sau 500ms để đảm bảo DOM đã sẵn sàng
        setTimeout(displayLoginMsg, 500);
    });
    </script>
    <?php
}
?>

<?php 
// Luôn luôn lưu tài khoản vào localStorage khi đăng nhập (không phụ thuộc vào login_msg)
if(isset($_SESSION['login']) && $_SESSION['login'] == true && isset($_SESSION['uId']) && !empty($user_email)) {
    ?>
    <script>
    // Lưu tài khoản vào localStorage (chạy mỗi lần load trang nếu đã đăng nhập)
    (function() {
        try {
            var savedAccounts = JSON.parse(localStorage.getItem('saved_accounts') || '[]');
            var accountIndex = savedAccounts.findIndex(function(acc) {
                return acc.email_mob === '<?php echo addslashes($user_email); ?>';
            });
            
            var isGoogleAccount = <?php echo $isGoogleAccount ? 'true' : 'false'; ?>;
            
            if(accountIndex >= 0) {
                // Cập nhật thông tin name và profile nếu có thay đổi
                if(savedAccounts[accountIndex].name !== <?php echo json_encode($user_name, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?> ||
                   savedAccounts[accountIndex].profile !== <?php echo json_encode($user_profile, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>) {
                    savedAccounts[accountIndex].name = <?php echo json_encode($user_name, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;
                    savedAccounts[accountIndex].profile = <?php echo json_encode($user_profile, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;
                }
                if(isGoogleAccount) {
                    // Đánh dấu đây là tài khoản Google (không có mật khẩu)
                    savedAccounts[accountIndex].isGoogle = true;
                    savedAccounts[accountIndex].password = ''; // Xóa mật khẩu nếu có
                }
                localStorage.setItem('saved_accounts', JSON.stringify(savedAccounts));
            } else {
                // Nếu chưa có trong danh sách, thêm mới (cho cả tài khoản Google và thường)
                if(typeof saveAccountToLocal === 'function') {
                    saveAccountToLocal('<?php echo addslashes($user_email); ?>', '', <?php echo json_encode($user_name, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>, <?php echo json_encode($user_profile, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>, isGoogleAccount);
                } else {
                    // Fallback nếu hàm chưa được định nghĩa
                    var newAccount = {
                        email_mob: '<?php echo addslashes($user_email); ?>',
                        password: '',
                        name: <?php echo json_encode($user_name, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>,
                        profile: <?php echo json_encode($user_profile, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>,
                        isGoogle: isGoogleAccount,
                        savedAt: new Date().toISOString()
                    };
                    if(savedAccounts.length >= 5) {
                        savedAccounts.shift();
                    }
                    savedAccounts.push(newAccount);
                    localStorage.setItem('saved_accounts', JSON.stringify(savedAccounts));
                }
            }
        } catch(e) {
            console.error('Lỗi khi lưu tài khoản:', e);
        }
    })();
    </script>
    <?php
}
?>

<script>
function setPreference(el){
    const key = el.getAttribute('data-pref');
    const val = el.getAttribute('data-value');
    document.cookie = `${key}=${val};path=/;max-age=31536000`;

    // Toggle active state
    document.querySelectorAll(`button.pref-chip[data-pref='${key}']`).forEach(btn => btn.classList.remove('active'));
    el.classList.add('active');

    if(key==='lang'){ 
        const langLabel = document.getElementById('lang-label');
        if(langLabel) langLabel.innerText = val.toUpperCase(); 
    }
    if(key==='currency'){ 
        const currencyLabel = document.getElementById('currency-label');
        if(currencyLabel) currencyLabel.innerText = val.toUpperCase(); 
    }
}
</script>

<style>
/* ========================= BODY PADDING FOR FIXED HEADER ========================= */
body {
  padding-top: 72px;
}

@media (max-width: 991px) {
  body {
    padding-top: 72px;
  }
}


/* ========================= NAVBAR BASE - PREMIUM STYLE ========================= */
.bg-nav{
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.99) 0%, rgba(255, 255, 255, 0.97) 100%);
  backdrop-filter: blur(24px) saturate(200%);
  -webkit-backdrop-filter: blur(24px) saturate(200%);
  border-bottom: 1px solid rgba(226, 232, 240, 0.6);
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  width: 100%;
  z-index: 1100;
  box-shadow: 
    0 1px 2px rgba(0, 0, 0, 0.03),
    0 4px 12px rgba(0, 0, 0, 0.05),
    0 8px 24px rgba(0, 0, 0, 0.03);
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  padding: 1.125rem 0;
  min-height: 72px;
}

.bg-nav.scrolled {
  box-shadow: 
    0 2px 4px rgba(0, 0, 0, 0.06),
    0 8px 16px rgba(0, 0, 0, 0.08),
    0 16px 32px rgba(0, 0, 0, 0.04);
  border-bottom-color: rgba(203, 213, 225, 0.8);
  padding: 0.875rem 0;
  background: linear-gradient(180deg, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0.98) 100%);
  min-height: 68px;
}

.navbar{z-index:1100;}
.dropdown-menu{z-index:2000;}
.dropdown-menu.show{display:block;}
.btn-group>.btn.dropdown-toggle{display:flex;align-items:center;}
.navbar .btn-group{position:relative;}
.navbar .dropdown-menu{min-width: 16rem;}
.navbar, .navbar *{overflow:visible;}

/* ========================= NAVBAR TOGGLER ========================= */
.navbar-toggler{
  border:none;
  outline:none;
  padding: 8px;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.navbar-toggler:hover {
  background: #f3f4f6;
}

.navbar-toggler-icon{
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(0,0,0,0.75)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
  transition: transform 0.3s ease;
}

.navbar-toggler[aria-expanded="true"] .navbar-toggler-icon {
  transform: rotate(90deg);
}

/* ========================= LOGO - PREMIUM STYLE ========================= */
.navbar-brand {
  flex-shrink: 0 !important;
  margin-right: auto !important;
  margin-left: 0 !important;
  z-index: 10;
  padding: 0;
  white-space: nowrap;
  overflow: hidden;
  display: flex;
  align-items: center;
  height: 100%;
}

.site-logo-img {
  height: auto;
  width: auto;
  max-height: 54px;
  max-width: 240px;
  object-fit: contain;
  transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
  filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.1));
}


.modern-logo {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  padding: 0;
  position: relative;
  z-index: 10;
  border-radius: 12px;
  display: flex;
  align-items: center;
}


.logo-icon-wrapper {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  background: linear-gradient(135deg, #003d5c 0%, #004d6b 50%, #005a7a 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  font-size: 22px;
  box-shadow: 
    0 4px 14px rgba(0, 61, 92, 0.25),
    0 2px 6px rgba(0, 61, 92, 0.15),
    inset 0 1px 0 rgba(255, 255, 255, 0.2);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.logo-icon-wrapper::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  transform: rotate(45deg);
  transition: all 0.6s ease;
  opacity: 0;
}


.logo-text {
  font-size: 1.68rem;
  background: linear-gradient(135deg, #003d5c 0%, #004d6b 50%, #005a7a 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-weight: 900;
  letter-spacing: -0.3px;
  transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
  position: relative;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  text-shadow: 0 2px 4px rgba(0, 61, 92, 0.1);
  -webkit-text-stroke: 0.3px rgba(0, 61, 92, 0.3);
  text-stroke: 0.3px rgba(0, 61, 92, 0.3);
  filter: drop-shadow(0 1px 2px rgba(0, 61, 92, 0.15));
}


/* ========================= NAV LINKS - PREMIUM STYLE ========================= */
.nav-link.nav-pill-modern{
  color: #1a1a1a !important;
  padding: 11px 18px;
  border-radius: 16px;
  transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0;
  font-weight: 600;
  position: relative;
  margin: 0 4px;
  font-size: 15px;
  letter-spacing: -0.4px;
  overflow: hidden;
  white-space: nowrap;
  height: fit-content;
  line-height: 1.5;
  text-transform: none;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.nav-link.nav-pill-modern::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(0, 61, 92, 0.08), rgba(0, 77, 107, 0.06));
  border-radius: 14px;
  opacity: 0;
  transition: opacity 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: -1;
}

.nav-link.nav-pill-modern::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%) scaleX(0);
  width: 75%;
  height: 3.5px;
  background: linear-gradient(90deg, #003d5c, #004d6b, #005a7a, #004d6b, #003d5c);
  border-radius: 4px 4px 0 0;
  transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
  box-shadow: 
    0 -2px 10px rgba(0, 61, 92, 0.35),
    0 -1px 4px rgba(0, 61, 92, 0.2);
  background-size: 200% 100%;
  animation: gradient-shift 3s ease infinite;
}

@keyframes gradient-shift {
  0%, 100% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
}



.nav-link.nav-pill-modern:hover {
  color: #003d5c !important;
  transform: translateY(-2px) scale(1.02);
  box-shadow: 
    0 6px 16px rgba(0, 61, 92, 0.15),
    0 3px 8px rgba(0, 61, 92, 0.1),
    0 1px 4px rgba(0, 61, 92, 0.08);
}

.nav-link.nav-pill-modern:hover::before {
  opacity: 1;
}

.nav-link.nav-pill-modern:hover::after {
  transform: translateX(-50%) scaleX(1);
}


.nav-link.nav-pill-modern.active {
  color: #003d5c !important;
  font-weight: 700;
  box-shadow: 
    0 4px 12px rgba(0, 61, 92, 0.18),
    0 2px 6px rgba(0, 61, 92, 0.12),
    0 1px 3px rgba(0, 61, 92, 0.08);
  transform: translateY(-1px);
}

.nav-link.nav-pill-modern.active::before {
  opacity: 1;
  background: linear-gradient(135deg, rgba(0, 61, 92, 0.15), rgba(0, 77, 107, 0.12));
}

.nav-link.nav-pill-modern.active::after {
  transform: translateX(-50%) scaleX(1);
  width: 90%;
  height: 3.5px;
  box-shadow: 
    0 -3px 14px rgba(0, 61, 92, 0.45),
    0 -1px 6px rgba(0, 61, 92, 0.25);
  animation: gradient-shift 3s ease infinite;
}

/* ========================= BUTTONS - PREMIUM STYLE ========================= */
.btn-gold{
  background: linear-gradient(135deg, #003d5c 0%, #004d6b 50%, #005a7a 100%);
  color: #ffffff;
  font-weight: 700;
  border: none;
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 
    0 4px 14px rgba(0, 61, 92, 0.3),
    0 2px 6px rgba(0, 61, 92, 0.2);
  position: relative;
  overflow: hidden;
  white-space: nowrap;
  letter-spacing: -0.2px;
}

.btn-gold::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  transition: left 0.6s ease;
}

.btn-gold:hover::before {
  left: 100%;
}

.btn-gold:hover{
  color: #ffffff;
  background: linear-gradient(135deg, #005a7a 0%, #004d6b 50%, #003d5c 100%);
  transform: translateY(-3px) scale(1.03);
  box-shadow: 
    0 10px 28px rgba(0, 61, 92, 0.45),
    0 5px 14px rgba(0, 61, 92, 0.35),
    0 2px 7px rgba(0, 61, 92, 0.25);
}

.btn-auth{
  box-shadow: 
    0 2px 8px rgba(0, 61, 92, 0.15),
    0 1px 4px rgba(0, 61, 92, 0.1);
  border-width: 2px;
  border-color: #003d5c;
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  font-weight: 600;
  white-space: nowrap;
  padding: 9px 18px;
  font-size: 14.5px;
  height: fit-content;
  letter-spacing: -0.2px;
  border-radius: 12px !important;
  color: #003d5c;
  background: transparent;
}

.btn-auth:hover{
  transform: translateY(-3px) scale(1.02);
  box-shadow: 
    0 8px 24px rgba(0, 61, 92, 0.3),
    0 4px 12px rgba(0, 61, 92, 0.2),
    0 2px 6px rgba(0, 61, 92, 0.15);
  border-color: #004d6b;
  background: linear-gradient(135deg, rgba(0, 61, 92, 0.1), rgba(0, 77, 107, 0.08));
  color: #003d5c;
}

/* ========================= BUTTON STYLES - DISTINCT ========================= */
/* Nút Đăng nhập - Outline style với màu logo */
.btn-login.btn-auth{
  color: #003d5c !important;
  border-color: #003d5c !important;
  background: transparent !important;
}

.btn-login.btn-auth:hover{
  color: #ffffff !important;
  border-color: #003d5c !important;
  background: #003d5c !important;
  transform: translateY(-3px) scale(1.02);
  box-shadow: 
    0 8px 24px rgba(0, 61, 92, 0.3),
    0 4px 12px rgba(0, 61, 92, 0.2),
    0 2px 6px rgba(0, 61, 92, 0.15);
}

/* Nút Đăng ký - Solid style với màu logo */
.btn-register.btn-auth{
  color: #ffffff !important;
  border-color: #003d5c !important;
  background: linear-gradient(135deg, #003d5c 0%, #004d6b 50%, #005a7a 100%) !important;
  font-weight: 700 !important;
}

.btn-register.btn-auth:hover{
  color: #ffffff !important;
  border-color: #004d6b !important;
  background: linear-gradient(135deg, #005a7a 0%, #004d6b 50%, #003d5c 100%) !important;
  transform: translateY(-3px) scale(1.03);
  box-shadow: 
    0 10px 28px rgba(0, 61, 92, 0.45),
    0 5px 14px rgba(0, 61, 92, 0.35),
    0 2px 7px rgba(0, 61, 92, 0.25);
}

/* Nút Chủ KS - Outline style với màu xám để phân biệt */
.btn-owner.btn-auth{
  color: #6b7280 !important;
  border-color: #d1d5db !important;
  background: transparent !important;
}

.btn-owner.btn-auth:hover{
  color: #374151 !important;
  border-color: #9ca3af !important;
  background: linear-gradient(135deg, rgba(107, 114, 128, 0.1), rgba(156, 163, 175, 0.08)) !important;
  transform: translateY(-3px) scale(1.02);
  box-shadow: 
    0 8px 24px rgba(107, 114, 128, 0.2),
    0 4px 12px rgba(107, 114, 128, 0.15),
    0 2px 6px rgba(107, 114, 128, 0.1);
}

/* ========================= CONTACT BUTTONS - PREMIUM STYLE ========================= */
.btn-sm {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border-width: 2px;
  font-weight: 600;
  white-space: nowrap;
}

.btn-sm:hover {
  transform: translateY(-3px) scale(1.05);
  box-shadow: 0 6px 16px rgba(0,0,0,0.2);
}

.btn-outline-success:hover {
  background: #22c55e;
  border-color: #22c55e;
  color: #ffffff;
}

.btn-outline-primary:hover {
  background: #003d5c;
  border-color: #003d5c;
  color: #ffffff;
}

/* ========================= USER PROFILE CONTAINER - MOVE LEFT ========================= */
.d-flex.align-items-center.gap-2.ms-lg-3 {
  transform: translateX(15%);
}

/* ========================= USER PILL - PREMIUM STYLE ========================= */
.user-pill-modern{
  display:inline-flex;
  align-items:center;
  gap:10px;
  border-radius:14px;
  padding:9px 16px;
  border: 2px solid rgba(0, 61, 92, 0.25);
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  background: linear-gradient(135deg, rgba(0, 61, 92, 0.08), rgba(0, 77, 107, 0.06));
  font-size: 14.5px;
  position: relative;
  overflow: hidden;
  white-space: nowrap;
  max-width: 100%;
  height: fit-content;
  font-weight: 600;
  letter-spacing: -0.2px;
  box-shadow: 
    0 2px 8px rgba(0, 61, 92, 0.1),
    0 1px 4px rgba(0, 61, 92, 0.05);
}

.user-pill-modern::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
  transition: left 0.5s ease;
}

.user-pill-modern:hover::before {
  left: 100%;
}

.user-pill-modern:hover {
  border-color: #004d6b;
  background: linear-gradient(135deg, rgba(0, 61, 92, 0.18), rgba(0, 77, 107, 0.15));
  transform: translateY(-3px) scale(1.02);
  box-shadow: 
    0 10px 28px rgba(0, 61, 92, 0.3),
    0 5px 14px rgba(0, 61, 92, 0.2),
    0 2px 7px rgba(0, 61, 92, 0.15);
}
.user-pill-modern:hover .user-name-text {
  color: #003d5c !important;
}

.user-pill-modern.dropdown-toggle::after {
  display: none;
}

.user-pill-modern i {
  transition: transform 0.3s ease, color 0.3s ease;
}


.user-pill-modern[aria-expanded="true"] i {
  transform: rotate(180deg);
}

.avatar-wrap-modern{
  position:relative;
  width:34px;
  height:34px;
  border-radius:50%;
  overflow:hidden;
  border:2.5px solid #003d5c;
  box-shadow:
    0 0 0 2px rgba(0, 61, 92, 0.12),
    0 2px 8px rgba(0, 61, 92, 0.2),
    0 1px 4px rgba(0, 61, 92, 0.1);
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  flex-shrink: 0;
}

.user-pill-modern:hover .avatar-wrap-modern {
  box-shadow: 
    0 0 0 3px rgba(0, 61, 92, 0.25),
    0 4px 16px rgba(0, 61, 92, 0.35),
    0 2px 8px rgba(0, 61, 92, 0.2);
  transform: scale(1.1) rotate(6deg);
  border-color: #004d6b;
}

.avatar-img{
  width:100%;
  height:100%;
  object-fit:cover;
  display:block;
  transition: transform 0.3s ease;
}

.user-pill-modern:hover .avatar-img {
  transform: scale(1.1);
}

.avatar-initial{
  width:100%;
  height:100%;
  display:flex;
  align-items:center;
  justify-content:center;
  color:#fff;
  font-weight:bold;
  font-size:16px;
  transition: transform 0.3s ease;
  user-select:none;
}

.user-pill-modern:hover .avatar-initial {
  transform: scale(1.1);
}

.avatar-badge-modern{
  position:absolute;
  top:-4px;
  right:-4px;
  background:linear-gradient(135deg, #003d5c, #002e42);
  color:#fff;
  border:2px solid #fff;
  border-radius:50%;
  padding:2px 6px;
  font-size:10px;
  font-weight:700;
  min-width:18px;
  min-height:18px;
  line-height:14px;
  text-align:center;
  box-shadow:0 3px 10px rgba(0,61,92,0.4);
  animation: pulse 2s infinite;
  z-index: 10;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.1); }
}

.user-name-text {
  max-width: 150px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  color: #003d5c !important;
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  font-size: 14.5px;
  font-weight: 600;
  display: inline-block;
  letter-spacing: -0.2px;
}
.user-pill-modern:hover .user-name-text {
  color: #004d6b !important;
}

/* ========================= USER PILL TIER DISPLAY ========================= */
.user-pill-separator {
  width: 1px;
  height: 24px;
  background: rgba(0, 61, 92, 0.2);
  margin: 0 8px;
  flex-shrink: 0;
}

.tier-display-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 700;
  white-space: nowrap;
  transition: all 0.3s ease;
}

.tier-display-pill i {
  font-size: 14px;
}

.tier-display-pill.tier-member {
  background: linear-gradient(135deg, rgba(0,0,0,0.08), rgba(26,26,26,0.08));
  color: #1a1a1a;
  border: 1px solid rgba(0,0,0,0.1);
}

.tier-display-pill.tier-silver {
  background: linear-gradient(135deg, rgba(140,140,140,0.95), rgba(160,160,160,1), rgba(140,140,140,0.95));
  color: #ffffff;
  border: 1px solid rgba(160,160,160,0.8);
  box-shadow: 0 2px 8px rgba(140,140,140,0.4), inset 0 1px 0 rgba(255,255,255,0.3);
}

.tier-display-pill.tier-gold {
  background: linear-gradient(135deg, rgba(255,215,0,0.2), rgba(255,193,7,0.18));
  color: #b8860b;
  border: 1px solid rgba(255,215,0,0.45);
}

.tier-display-pill.tier-platinum {
  background: linear-gradient(135deg, rgba(0,0,0,0.85), rgba(20,20,20,0.9), rgba(0,0,0,0.85));
  color: #ffd700;
  border: 1px solid rgba(255,215,0,0.6);
  box-shadow: 0 2px 8px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,215,0,0.2);
}

.tier-text-pill {
  font-size: 11px;
  font-weight: 700;
}

.user-pill-modern:hover .tier-display-pill {
  transform: scale(1.05);
}

/* ========================= DROPDOWN MENU ========================= */
.modern-dropdown {
  border-radius: 16px;
  border: 1px solid rgba(229,231,235,0.5);
  padding: 8px;
  margin-top: 8px;
  background: #ffffff;
  box-shadow: 0 10px 40px rgba(0,0,0,0.15);
  animation: dropdownFadeIn 0.3s ease;
}

@keyframes dropdownFadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.dropdown-item-modern {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 10px;
  color: #1a1a1a;
  text-decoration: none;
  transition: all 0.3s ease;
  font-weight: 600;
  font-size: 14px;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
}

.dropdown-item-modern i:first-child,
.dropdown-item-modern .dropdown-icon-main {
  display: inline-flex !important;
  align-items: center;
  justify-content: center;
  width: 20px;
  min-width: 20px;
  color: #003d5c;
  font-size: 16px;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  flex-shrink: 0;
  visibility: visible !important;
  opacity: 1 !important;
}

.dropdown-item-modern i:last-child {
  color: #1a1a1a;
  font-size: 12px;
  opacity: 0;
  transition: all 0.3s ease;
}

.dropdown-item-modern:hover {
  background: linear-gradient(135deg, rgba(0, 61, 92, 0.1), rgba(0, 77, 107, 0.08));
  color: #003d5c;
  transform: translateX(4px);
  text-decoration: none;
}

.dropdown-item-modern:hover i:first-child,
.dropdown-item-modern:hover .dropdown-icon-main {
  transform: scale(1.2) translateY(-1px);
  color: #003d5c;
}

.dropdown-item-modern:hover i:last-child {
  opacity: 1;
  color: #004d6b;
}

.dropdown-item-modern.text-danger {
  color: #ef4444;
}

.dropdown-item-modern.text-danger:hover {
  background: linear-gradient(135deg, rgba(239,68,68,0.1), rgba(220,38,38,0.1));
  color: #dc2626;
}

.dropdown-item-modern.text-danger i:first-child,
.dropdown-item-modern.text-danger .dropdown-icon-main {
  color: #ef4444;
}

.dropdown-item-modern.text-danger:hover i:first-child,
.dropdown-item-modern.text-danger:hover .dropdown-icon-main {
  color: #dc2626;
}

.dropdown-divider-modern {
  margin: 8px 0;
  border-color: #e5e7eb;
  opacity: 1;
}

.badge-modern {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 20px;
  height: 20px;
  padding: 0 6px;
  border-radius: 10px;
  font-size: 11px;
  font-weight: 700;
}

/* ========================= TIER BADGE ========================= */
.tier-badge{
  display:inline-flex;
  align-items:center;
  gap:6px;
  background:linear-gradient(135deg, rgba(0,61,92,0.1), rgba(0,46,66,0.1));
  color:#003d5c;
  border-radius:12px;
  padding:6px 12px;
  font-size:12px;
  font-weight:700;
  border: 1px solid rgba(0,61,92,0.2);
  transition: all 0.3s ease;
}

.tier-badge:hover {
  transform: scale(1.05);
}

.tier-badge.tier-member{
  background: linear-gradient(135deg, rgba(0,0,0,0.08), rgba(26,26,26,0.08));
  color: #1a1a1a;
  border-color: rgba(0,0,0,0.2);
}
.tier-badge.tier-member:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.tier-badge.tier-silver{
  background: linear-gradient(135deg, rgba(140,140,140,0.95), rgba(160,160,160,1), rgba(140,140,140,0.95));
  color: #ffffff;
  border-color: rgba(160,160,160,0.8);
  box-shadow: 0 2px 8px rgba(140,140,140,0.4), inset 0 1px 0 rgba(255,255,255,0.3);
  position: relative;
  overflow: hidden;
}
.tier-badge.tier-silver::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(45deg, transparent, rgba(255,255,255,0.5), transparent);
  transform: rotate(45deg);
  animation: silverShine 3s infinite;
  z-index: 0;
}
.tier-badge.tier-silver i,
.tier-badge.tier-silver span {
  position: relative;
  z-index: 1;
  color: #ffffff;
}
.tier-badge.tier-silver:hover {
  box-shadow: 0 4px 15px rgba(192,192,192,0.5), inset 0 1px 0 rgba(255,255,255,0.5);
  border-color: rgba(192,192,192,0.9);
  transform: scale(1.05);
}
@keyframes silverShine {
  0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
  100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

.tier-badge.tier-gold{
  background: linear-gradient(135deg, rgba(255,215,0,0.2), rgba(255,193,7,0.18));
  color: #b8860b;
  border-color: rgba(255,215,0,0.45);
}
.tier-badge.tier-gold:hover {
  box-shadow: 0 4px 12px rgba(255,215,0,0.35);
}

/* ========================= PLATINUM TIER BADGE ========================= */
.tier-badge.tier-platinum {
  background: linear-gradient(135deg, rgba(0,0,0,0.85), rgba(20,20,20,0.9), rgba(0,0,0,0.85));
  color: #ffd700;
  border-color: rgba(255,215,0,0.6);
  box-shadow: 0 2px 8px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,215,0,0.2), 0 0 0 1px rgba(255,215,0,0.3);
  position: relative;
  overflow: hidden;
  font-weight: 700;
  padding: 4px 10px;
  font-size: 11px;
  border-radius: 10px;
}

.tier-badge.tier-platinum i,
.tier-badge.tier-platinum span {
  position: relative;
  z-index: 1;
}

.tier-badge.tier-platinum::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(45deg, transparent, rgba(255,215,0,0.2), transparent);
  transform: rotate(45deg);
  animation: platinumShine 3s infinite;
  z-index: 0;
}

.tier-badge.tier-platinum:hover {
  box-shadow: 0 4px 15px rgba(255,215,0,0.4), inset 0 1px 0 rgba(255,215,0,0.3);
  border-color: rgba(255,215,0,0.8);
  transform: scale(1.05);
}

/* ========================= DIAMOND ICON SPARKLE ========================= */
.tier-badge.tier-platinum i.bi-gem {
  color: #ffd700;
  display: inline-block;
  position: relative;
  animation: diamondSparkle 2s ease-in-out infinite, diamondRotate 3s linear infinite;
  font-size: 12px;
}

.tier-badge.tier-platinum i.bi-gem::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 100%;
  height: 100%;
  background: radial-gradient(circle, rgba(255,255,255,0.8) 0%, transparent 70%);
  transform: translate(-50%, -50%);
  animation: diamondGlow 1.5s ease-in-out infinite;
  pointer-events: none;
  z-index: -1;
}

/* ========================= ANIMATIONS ========================= */
@keyframes platinumShine {
  0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
  100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

@keyframes diamondSparkle {
  0%, 100% {
    filter: drop-shadow(0 0 3px rgba(255,215,0,0.8)) drop-shadow(0 0 6px rgba(255,215,0,0.5)) brightness(1);
    color: #ffd700;
  }
  25% {
    filter: drop-shadow(0 0 5px rgba(255,215,0,1)) drop-shadow(0 0 10px rgba(255,215,0,0.7)) brightness(1.3);
    color: #ffed4e;
  }
  50% {
    filter: drop-shadow(0 0 6px rgba(255,215,0,1)) drop-shadow(0 0 12px rgba(255,215,0,0.9)) brightness(1.5);
    color: #ffd700;
  }
  75% {
    filter: drop-shadow(0 0 5px rgba(255,215,0,1)) drop-shadow(0 0 10px rgba(255,215,0,0.7)) brightness(1.3);
    color: #ffed4e;
  }
}

@keyframes diamondRotate {
  0% { transform: rotate(0deg) scale(1); }
  25% { transform: rotate(6deg) scale(1.1); }
  50% { transform: rotate(0deg) scale(1); }
  75% { transform: rotate(-6deg) scale(1.1); }
  100% { transform: rotate(0deg) scale(1); }
}

@keyframes diamondGlow {
  0%, 100% {
    opacity: 0;
    transform: translate(-50%, -50%) scale(0.8);
  }
  50% {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1.2);
  }
}

.tier-progress{
  height:10px;
  border-radius:999px;
  background:#e9eef5;
  overflow: hidden;
}

.tier-progress .progress-bar{
  background:linear-gradient(90deg, #003d5c, #002e42);
  border-radius: 999px;
  transition: width 0.6s ease;
}

.last-booking{line-height:1.25;}

.status-dot{
  width:12px;
  height:12px;
  border-radius:50%;
  background:#1a1a1a;
  margin-top:4px;
  flex-shrink:0;
  box-shadow: 0 0 0 3px rgba(0,0,0,0.1);
  animation: statusPulse 2s infinite;
}

@keyframes statusPulse {
  0%, 100% { box-shadow: 0 0 0 3px rgba(0,0,0,0.1); }
  50% { box-shadow: 0 0 0 6px rgba(0,0,0,0.05); }
}

.status-dot.status-pending{background:#fbbf24;box-shadow: 0 0 0 3px rgba(251,191,36,0.2);}
.status-dot.status-booked{background:#22c55e;box-shadow: 0 0 0 3px rgba(34,197,94,0.2);}
.status-dot.status-cancel{background:#ef4444;box-shadow: 0 0 0 3px rgba(239,68,68,0.2);}
.status-dot.status-failed{background:#6b7280;box-shadow: 0 0 0 3px rgba(107,114,128,0.2);}
/* ========================= TOAST ========================= */
.custom-toast{
    position: fixed;
    top: 85px;
    right: -400px;
    padding: 15px 22px;
    color: #fff;
    font-size: 15px;
    border-radius: 12px;
    z-index: 99999;
    transition: all 0.35s ease;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
}

.custom-toast.show{ right: 25px; }

/* ========================= RESPONSIVE ========================= */
@media (max-width: 991px) {
  .navbar-collapse {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    margin-top: 16px;
    padding: 16px;
    box-shadow: 
      0 10px 40px rgba(0, 0, 0, 0.1),
      0 2px 8px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(226, 232, 240, 0.8);
  }
  
  .nav-link.nav-pill-modern {
    margin: 4px 0;
    padding: 12px 16px;
    border-radius: 12px;
  }
  
  .user-pill-modern {
    width: 100%;
    justify-content: center;
    margin-top: 12px;
  }

  .d-flex.gap-2 {
    flex-wrap: wrap;
    justify-content: center;
    gap: 8px !important;
  }

  .btn-auth {
    width: 100%;
    justify-content: center;
  }

}

@media (max-width: 768px) {
  .logo-text {
    font-size: 1.6rem;
    font-weight: 900;
  }
  
  .logo-icon-wrapper {
    width: 36px;
    height: 36px;
    font-size: 18px;
  }
  
  .user-name-text {
    max-width: 100px;
  }

  .navbar-brand {
    margin-right: auto !important;
    flex-shrink: 0;
  }

  .navbar-toggler {
    margin-left: auto;
    order: 2;
  }
}
</style>

<script>
// Navbar scroll effect
document.addEventListener('DOMContentLoaded', function() {
  const navbar = document.getElementById('nav-bar');
  if (navbar) {
    let lastScroll = 0;
    window.addEventListener('scroll', function() {
      const currentScroll = window.pageYOffset;
      
      if (currentScroll > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
      
      lastScroll = currentScroll;
    });
  }
  
  // Smooth scroll for nav links
  document.querySelectorAll('.nav-link.nav-pill-modern').forEach(link => {
    link.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href && href.includes('#')) {
        e.preventDefault();
        const targetId = href.split('#')[1];
        const target = document.getElementById(targetId);
        if (target) {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }
    });
  });
});
</script>
<script>
function checkLoginToBook(login, room_id){
    // #region agent log
    fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'inc/header.php:1115',message:'checkLoginToBook called',data:{login:login,room_id:room_id,loginType:typeof login},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'D'})}).catch(()=>{});
    // #endregion
    
    if(login == 1){
        // #region agent log
        fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'inc/header.php:1118',message:'Redirecting to confirm_booking',data:{room_id:room_id,url:'confirm_booking.php?id=' + room_id},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'D'})}).catch(()=>{});
        // #endregion
        window.location.href = "confirm_booking.php?id=" + room_id;
    } else {
        // #region agent log
        fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'inc/header.php:1121',message:'Opening login modal',data:{hasLoginModal:!!document.getElementById('loginModal')},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'D'})}).catch(()=>{});
        // #endregion
        const loginModalEl = document.getElementById('loginModal');
        if(loginModalEl){
            let loginModal = new bootstrap.Modal(loginModalEl);
            loginModal.show();
        } else {
            // #region agent log
            fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'inc/header.php:1127',message:'Login modal not found',data:{},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'D'})}).catch(()=>{});
            // #endregion
            alert('Vui lòng đăng nhập để đặt phòng');
            if(typeof openLoginModal === 'function'){
                openLoginModal();
            }
        }
    }
}

function openLoginModal(){
    const loginModalEl = document.getElementById('loginModal');
    if(loginModalEl){
      const loginModal = new bootstrap.Modal(loginModalEl);
      loginModal.show();
    }
  }

// Tier info modal handler
document.addEventListener('DOMContentLoaded', function() {
  // Thêm sự kiện click vào tier badge
  document.querySelectorAll('.tier-display-pill, .tier-badge').forEach(function(tierEl) {
    tierEl.style.cursor = 'pointer';
    tierEl.addEventListener('click', function(e) {
      e.stopPropagation(); // Ngăn dropdown đóng
      e.preventDefault();
      const tierModal = new bootstrap.Modal(document.getElementById('tierInfoModal'));
      tierModal.show();
    });
  });
});

</script>

<!-- Tier Info Modal -->
<div class="modal fade" id="tierInfoModal" tabindex="-1" aria-labelledby="tierInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
      <div class="modal-header" style="border-bottom: 1px solid rgba(0,0,0,0.1); padding: 24px 24px 16px;">
        <h5 class="modal-title fw-bold" id="tierInfoModalLabel" style="font-size: 1.5rem; color: #003d5c;">
          <i class="bi bi-award-fill me-2" style="color: #ffd700;"></i>
          <span data-i18n="tier.memberProgram">Chương trình thành viên</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="padding: 24px;">
        <div class="tier-info-container">
          <!-- Member Tier -->
          <div class="tier-info-card tier-member mb-3" style="border-radius: 16px; padding: 20px; background: linear-gradient(135deg, rgba(0,0,0,0.05), rgba(26,26,26,0.05)); border: 2px solid rgba(0,0,0,0.15);">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-person-badge" style="font-size: 24px; color: #6b7280;"></i>
                <h6 class="mb-0 fw-bold" style="color: #1a1a1a;"><span data-i18n="tier.newMember">Thành viên mới</span></h6>
              </div>
              <span class="badge bg-light text-dark" style="font-size: 0.9rem; padding: 6px 12px;"><span data-i18n="tier.discount">Giảm</span> 0%</span>
            </div>
            <p class="mb-0 small text-muted"><span data-i18n="tier.condition">Điều kiện</span>: <span data-i18n="tier.conditionNewMember">Chưa đặt phòng lần nào</span></p>
          </div>

          <!-- Silver Tier -->
          <div class="tier-info-card tier-silver mb-3" style="border-radius: 16px; padding: 20px; background: linear-gradient(135deg, rgba(140,140,140,0.1), rgba(160,160,160,0.08)); border: 2px solid rgba(140,140,140,0.3);">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-award" style="font-size: 24px; color: #8c8c8c;"></i>
                <h6 class="mb-0 fw-bold" style="color: #1a1a1a;"><span data-i18n="tier.silverTier">Hạng Silver</span></h6>
              </div>
              <span class="badge bg-secondary" style="font-size: 0.9rem; padding: 6px 12px;"><span data-i18n="tier.discount">Giảm</span> 3%</span>
            </div>
            <p class="mb-0 small text-muted"><span data-i18n="tier.condition">Điều kiện</span>: <span data-i18n="tier.conditionSilver">Đã đặt phòng từ 1 lần</span></p>
          </div>

          <!-- Gold Tier -->
          <div class="tier-info-card tier-gold mb-3" style="border-radius: 16px; padding: 20px; background: linear-gradient(135deg, rgba(255,215,0,0.15), rgba(255,193,7,0.12)); border: 2px solid rgba(255,215,0,0.4);">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-star-fill" style="font-size: 24px; color: #ffd700;"></i>
                <h6 class="mb-0 fw-bold" style="color: #b8860b;"><span data-i18n="tier.goldTier">Hạng Gold</span></h6>
              </div>
              <span class="badge bg-warning text-dark" style="font-size: 0.9rem; padding: 6px 12px;"><span data-i18n="tier.discount">Giảm</span> 5%</span>
            </div>
            <p class="mb-0 small text-muted"><span data-i18n="tier.condition">Điều kiện</span>: <span data-i18n="tier.conditionGold">Đã đặt phòng từ 5 lần</span></p>
          </div>

          <!-- Platinum Tier -->
          <div class="tier-info-card tier-platinum mb-3" style="border-radius: 16px; padding: 20px; background: linear-gradient(135deg, rgba(0,0,0,0.1), rgba(20,20,20,0.08)); border: 2px solid rgba(255,215,0,0.5);">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-gem" style="font-size: 24px; color: #ffd700;"></i>
                <h6 class="mb-0 fw-bold" style="color: #1a1a1a;"><span data-i18n="tier.platinumTier">Hạng Platinum</span></h6>
              </div>
              <span class="badge bg-dark" style="font-size: 0.9rem; padding: 6px 12px;"><span data-i18n="tier.discount">Giảm</span> 8%</span>
            </div>
            <p class="mb-0 small text-muted"><span data-i18n="tier.condition">Điều kiện</span>: <span data-i18n="tier.conditionPlatinum">Đã đặt phòng từ 10 lần</span></p>
          </div>

          <div class="alert alert-info mb-0" style="border-radius: 12px; border: none; background: rgba(0, 61, 92, 0.08); color: #003d5c;">
            <i class="bi bi-info-circle me-2"></i>
            <small><strong data-i18n="tier.note">Lưu ý</strong>: <span data-i18n="tier.noteText">Ưu đãi giảm giá sẽ được áp dụng tự động khi đặt phòng. Phần trăm giảm giá được tính trên tổng giá phòng trước thuế và phí dịch vụ.</span></small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</script>

