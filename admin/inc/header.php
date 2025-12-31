<?php
// Use absolute includes based on this file's directory so paths work regardless of caller
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/essentials.php';

// In case db_config did not set $con for any reason, try a fallback (rare)
if (!isset($con)) {
  require_once __DIR__ . '/db_config.php';
}

// Lấy số lượng thông báo cho sidebar (với error handling)
$sidebar_counts = [
    "new_bookings" => (function() use ($con) {
        $res = @mysqli_query($con, "
            SELECT COUNT(*) AS c 
            FROM booking_order bo
            INNER JOIN booking_details bd ON bo.booking_id = bd.booking_id
            INNER JOIN rooms r ON bo.room_id = r.id
            WHERE (r.owner_id IS NULL)
            AND (bo.booking_status='pending'
               OR (bo.booking_status='booked' AND COALESCE(bo.arrival,0)=0))
        ");
        return $res ? (mysqli_fetch_assoc($res)['c'] ?? 0) : 0;
    })(),

    "refunds" => (function() use ($con) {
        $res = @mysqli_query($con, "
            SELECT COUNT(*) AS c 
            FROM booking_order bo
            INNER JOIN rooms r ON bo.room_id = r.id
            WHERE r.owner_id IS NULL
            AND bo.booking_status='cancelled' 
            AND COALESCE(bo.refund,0)=0
        ");
        return $res ? (mysqli_fetch_assoc($res)['c'] ?? 0) : 0;
    })(),

    "queries" => (function() use ($con) {
        $check = @mysqli_query($con, "SHOW TABLES LIKE 'user_queries'");
        if($check && mysqli_num_rows($check) > 0){
            $res = @mysqli_query($con, "SELECT COUNT(*) AS c FROM user_queries WHERE seen = 0");
            return $res ? (mysqli_fetch_assoc($res)['c'] ?? 0) : 0;
        }
        return 0;
    })(),

    "reviews" => (function() use ($con) {
        $check = @mysqli_query($con, "SHOW TABLES LIKE 'rating_review'");
        if($check && mysqli_num_rows($check) > 0){
            $res = @mysqli_query($con, "SELECT COUNT(*) AS c FROM rating_review WHERE seen = 0");
            return $res ? (mysqli_fetch_assoc($res)['c'] ?? 0) : 0;
        }
        return 0;
    })(),

    "newsletter" => (function() use ($con) {
        $check = @mysqli_query($con, "SHOW TABLES LIKE 'newsletter_subscribers'");
        if($check && mysqli_num_rows($check) > 0){
            $res = @mysqli_query($con, "SELECT COUNT(*) AS c FROM newsletter_subscribers");
            return $res ? (mysqli_fetch_assoc($res)['c'] ?? 0) : 0;
        }
        return 0;
    })(),

    // NEW - user notifications
    "user_unverified" => (function() use ($con) {
        $check = @mysqli_query($con, "SHOW COLUMNS FROM `user_cred` LIKE 'is_verified'");
        if($check && mysqli_num_rows($check) > 0){
            $res = @mysqli_query($con, "SELECT COUNT(*) AS c FROM user_cred WHERE is_verified = 0");
            return $res ? (mysqli_fetch_assoc($res)['c'] ?? 0) : 0;
        }
        return 0;
    })(),

    // Hotel owners pending approval
    "owners_pending" => (function() use ($con) {
        $check = @mysqli_query($con, "SHOW TABLES LIKE 'hotel_owners'");
        if($check && mysqli_num_rows($check) > 0){
            $res = @mysqli_query($con, "SELECT COUNT(*) AS c FROM hotel_owners WHERE status = 0");
            return $res ? (mysqli_fetch_assoc($res)['c'] ?? 0) : 0;
        }
        return 0;
    })(),

    // Rooms pending approval
    "rooms_pending" => (function() use ($con) {
        $check = @mysqli_query($con, "SHOW COLUMNS FROM `rooms` LIKE 'approved'");
        if($check && mysqli_num_rows($check) > 0){
            $res = @mysqli_query($con, "SELECT COUNT(*) AS c FROM rooms WHERE approved = 0 AND removed = 0");
            return $res ? (mysqli_fetch_assoc($res)['c'] ?? 0) : 0;
        }
        return 0;
    })(),
];
?>

<!-- ===== TOP BAR ===== -->
<div class="container-fluid bg-dark text-light p-3 d-flex align-items-center justify-content-between sticky-top">
  <h5 class="mb-0 fw-bold h-font"><i class="bi bi-building me-2"></i> Vĩnh Long Hotel</h5>
  <a href="logout.php" class="btn btn-danger btn-sm px-3 rounded">
    Đăng xuất
</a>

</div>

<!-- ===== SIDEBAR ===== -->
<div class="col-lg-2 bg-dark border-top border-3 border-secondary" id="dashboard-menu">

  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid flex-lg-column align-items-stretch">

      <h4 class="mt-2 text-light"><i class="bi bi-speedometer2 me-2"></i>Trang quản lý</h4>

      <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#adminDropdown">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse flex-column align-items-stretch mt-2" id="adminDropdown">

        <ul class="nav nav-pills flex-column">

          <!-- Dash -->
          <li class="nav-item">
            <a class="nav-link text-white" href="dashboard.php">
              <i class="bi bi-speedometer me-2"></i> Bảng theo dõi
            </a>
          </li>

          <!-- BOOKINGS -->
          <li class="nav-item">
            <button class="btn text-white px-3 w-100 shadow-none text-start d-flex align-items-center justify-content-between"
                    data-bs-toggle="collapse" data-bs-target="#bookingLinks">
              <span><i class="bi bi-journal-text me-2"></i>Bookings</span>
              <i class="bi bi-caret-down-fill"></i>
            </button>

            <div class="collapse show px-3 small mb-1" id="bookingLinks">
              <ul class="nav nav-pills flex-column rounded border border-secondary">

                <li class="nav-item d-flex justify-content-between align-items-center">
                  <a class="nav-link text-white" href="new_bookings.php">
                    <i class="bi bi-bell me-2"></i> Lượt đặt phòng mới
                  </a>
                  <?php if ($sidebar_counts['new_bookings'] > 0): ?>
                    <span class="badge bg-danger"><?php echo $sidebar_counts['new_bookings']; ?></span>
                  <?php endif; ?>
                </li>

                <li class="nav-item d-flex justify-content-between align-items-center">
                  <a class="nav-link text-white" href="refund_bookings.php">
                    <i class="bi bi-arrow-counterclockwise me-2"></i> Yêu cầu hoàn tiền
                  </a>
                  <?php if ($sidebar_counts['refunds'] > 0): ?>
                    <span class="badge bg-warning text-dark"><?php echo $sidebar_counts['refunds']; ?></span>
                  <?php endif; ?>
                </li>

                <li class="nav-item">
                  <a class="nav-link text-white" href="booking_records.php">
                    <i class="bi bi-bar-chart me-2"></i> Thống kê đặt phòng
                  </a>
                </li>

              </ul>
            </div>
          </li>

          <!-- USERS -->
          <li class="nav-item d-flex justify-content-between align-items-center">
            <a class="nav-link text-white" href="users.php">
              <i class="bi bi-people me-2"></i> Người dùng
            </a>
            <?php if ($sidebar_counts['user_unverified'] > 0): ?>
              <span class="badge bg-info"><?php echo $sidebar_counts['user_unverified']; ?></span>
            <?php endif; ?>
          </li>

          <!-- MESSAGES -->
          <li class="nav-item d-flex justify-content-between align-items-center">
            <a class="nav-link text-white" href="messages.php">
              <i class="bi bi-chat-dots me-2"></i> Tin nhắn từ khách hàng
            </a>
            <?php
            $check_table = @mysqli_query($con, "SHOW TABLES LIKE 'messages'");
            $table_exists = $check_table && mysqli_num_rows($check_table) > 0;
            if($table_exists) {
              $unread_messages = mysqli_fetch_assoc(mysqli_query($con, "
                SELECT COUNT(*) AS c FROM messages 
                WHERE owner_id IS NULL 
                AND sender_type = 'user' 
                AND seen = 0
              "))['c'] ?? 0;
              if ($unread_messages > 0): ?>
                <span class="badge bg-danger" title="Tin nhắn chưa đọc"><?php echo $unread_messages; ?></span>
              <?php endif;
            }
            ?>
          </li>

          <!-- HOTEL OWNERS -->
          <li class="nav-item d-flex justify-content-between align-items-center">
            <a class="nav-link text-white" href="owners.php">
              <i class="bi bi-building me-2"></i> Chủ khách sạn
            </a>
            <?php 
            $pending_owners = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS c FROM hotel_owners WHERE status=0"))['c'] ?? 0;
            if ($pending_owners > 0): ?>
              <span class="badge bg-warning text-dark"><?php echo $pending_owners; ?></span>
            <?php endif; ?>
          </li>

          <!-- QUERIES -->
          <li class="nav-item d-flex justify-content-between align-items-center">
            <a class="nav-link text-white" href="user_queries.php">
              <i class="bi bi-chat-left-text me-2"></i> Liên hệ
            </a>
            <?php if ($sidebar_counts['queries'] > 0): ?>
              <span class="badge bg-danger"><?php echo $sidebar_counts['queries']; ?></span>
            <?php endif; ?>
          </li>

          <!-- REVIEWS -->
          <li class="nav-item d-flex justify-content-between align-items-center">
            <a class="nav-link text-white" href="rate_review.php">
              <i class="bi bi-star-half me-2"></i> Đánh giá
            </a>
            <?php if ($sidebar_counts['reviews'] > 0): ?>
              <span class="badge bg-primary"><?php echo $sidebar_counts['reviews']; ?></span>
            <?php endif; ?>
          </li>

          <!-- NEWSLETTER -->
          <li class="nav-item d-flex justify-content-between align-items-center">
            <a class="nav-link text-white" href="newsletter.php">
              <i class="bi bi-envelope-paper me-2"></i> Bản tin đăng ký KM%
            </a>
            <?php if ($sidebar_counts['newsletter'] > 0): ?>
              <span class="badge bg-success"><?php echo $sidebar_counts['newsletter']; ?></span>
            <?php endif; ?>
          </li>

          <!-- ROOMS -->
          <li class="nav-item d-flex justify-content-between align-items-center">
            <a class="nav-link text-white" href="rooms.php">
              <i class="bi bi-door-open me-2"></i> Danh sách phòng
            </a>
            <?php if ($sidebar_counts['rooms_pending'] > 0): ?>
              <span class="badge bg-warning text-dark"><?php echo $sidebar_counts['rooms_pending']; ?></span>
            <?php endif; ?>
          </li>

          <!-- FACILITIES -->
          <li class="nav-item">
            <a class="nav-link text-white" href="features_facilities.php">
              <i class="bi bi-layers-half me-2"></i> Không Gian & Tiện Nghi
            </a>
          </li>

          <!-- CAROUSEL -->
          <li class="nav-item">
            <a class="nav-link text-white" href="carousel.php">
              <i class="bi bi-images me-2"></i> Trình chiếu
            </a>
          </li>

          <!-- PROMOS -->
          <li class="nav-item">
            <a class="nav-link text-white" href="promos.php">
              <i class="bi bi-ticket-perforated me-2"></i> Mã giảm giá
            </a>
          </li>

          <!-- DESTINATIONS -->
          <li class="nav-item">
            <a class="nav-link text-white" href="destinations.php">
              <i class="bi bi-geo-alt me-2"></i> Quản lý Điểm đến & Đặc sản
            </a>
          </li>
          <li>
            <a class="nav-link text-white" href="faqs.php">
              <i class="bi bi-question-circle me-2"></i> Quản lý FAQ (Chatbot)
            </a>
          </li>

          <!-- SETTINGS -->
          <li class="nav-item">
            <a class="nav-link text-white" href="settings.php">
              <i class="bi bi-gear me-2"></i> Cài đặt trang
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</div>

<!-- ACTIVE MENU -->
<script>
(function() {
  // Lấy pathname hiện tại (bỏ query string và hash)
  let currentPath = location.pathname;
  let currentFile = currentPath.split('/').pop() || 'index.php';
  
  // Xóa active-menu khỏi tất cả links trước
  document.querySelectorAll("#dashboard-menu a").forEach(a => {
    a.classList.remove("active-menu");
  });
  
  // Tìm và highlight link chính xác nhất
  let links = document.querySelectorAll("#dashboard-menu a");
  let bestMatch = null;
  let bestMatchLength = 0;
  
  links.forEach(a => {
    let linkPath = new URL(a.href).pathname;
    let linkFile = linkPath.split('/').pop() || '';
    
    // So sánh chính xác file name
    if (linkFile === currentFile) {
      // Nếu match chính xác, ưu tiên link có path dài hơn (specific hơn)
      if (linkPath.length > bestMatchLength) {
        bestMatch = a;
        bestMatchLength = linkPath.length;
      }
    }
  });
  
  // Chỉ highlight link tốt nhất
  if (bestMatch) {
    bestMatch.classList.add("active-menu");
  }
})();
</script>

<style>
.active-menu {
  background: #0d6efd !important;
  font-weight: bold;
  border-radius: 6px;
}
#dashboard-menu .badge {
  font-size: 12px;
  padding: 4px 7px;
  margin-left: auto;
  margin-right: 8px;
  flex-shrink: 0;
}
#dashboard-menu .nav-item.d-flex .nav-link {
  flex: 1;
  min-width: 0;
}
#dashboard-menu .nav-item.d-flex {
  padding-right: 0;
  gap: 8px;
}
</style>
