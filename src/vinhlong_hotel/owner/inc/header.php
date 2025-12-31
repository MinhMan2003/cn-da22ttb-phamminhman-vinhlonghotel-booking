<?php
require_once __DIR__ . '/../../admin/inc/db_config.php';
require_once __DIR__ . '/../../admin/inc/check_messages_table.php';
require_once __DIR__ . '/essentials.php';
ownerLogin();

$owner_id = getOwnerId();
$owner_name = $_SESSION['ownerName'] ?? 'Chủ khách sạn';
$hotel_name = $_SESSION['ownerHotelName'] ?? '';

// Lấy thông tin avatar của owner
$owner_profile = 'user.png'; // Mặc định
$owner_profile_img = '';
$owner_has_avatar = false;
if($owner_id > 0) {
    $owner_profile_res = select("SELECT profile FROM `hotel_owners` WHERE `id`=? LIMIT 1", [$owner_id], 'i');
    if($owner_profile_res && mysqli_num_rows($owner_profile_res) > 0) {
        $owner_profile_row = mysqli_fetch_assoc($owner_profile_res);
        $db_profile = $owner_profile_row['profile'] ?? 'user.png';
        if(!empty($db_profile) && $db_profile != 'user.png') {
            $owner_profile = $db_profile;
            // Kiểm tra file có tồn tại không
            if(defined('UPLOAD_IMAGE_PATH') && defined('USERS_FOLDER')) {
                $owner_has_avatar = file_exists(UPLOAD_IMAGE_PATH . USERS_FOLDER . $owner_profile);
            }
        }
    }
    // Tạo đường dẫn ảnh
    if(defined('USERS_IMG_PATH')) {
        $owner_profile_img = USERS_IMG_PATH . $owner_profile;
    } else {
        $owner_profile_img = 'images/users/' . $owner_profile;
    }
}

// Thống kê nhanh
$pending_count = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT COUNT(*) AS c FROM booking_order bo
    INNER JOIN rooms r ON bo.room_id = r.id
    WHERE r.owner_id=$owner_id 
    AND (bo.booking_status='pending' 
         OR (bo.booking_status='booked' AND COALESCE(bo.arrival,0)=0))
"))['c'];

$refund_count = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT COUNT(*) AS c FROM booking_order bo
    INNER JOIN rooms r ON bo.room_id = r.id
    WHERE r.owner_id=$owner_id 
    AND bo.booking_status='cancelled' 
    AND COALESCE(bo.refund, 0) = 0
"))['c'];

// Đếm phòng chờ duyệt
$pending_rooms_count = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT COUNT(*) AS c FROM rooms
    WHERE owner_id=$owner_id 
    AND removed=0
    AND (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_SCHEMA = DATABASE() 
         AND TABLE_NAME = 'rooms' 
         AND COLUMN_NAME = 'approved') > 0
    AND approved=0
"))['c'];
?>
<!-- ===== SIDEBAR ===== -->
<div class="col-lg-2 bg-dark" id="dashboard-menu" style="min-height: 100vh;">
      <!-- Top Bar trong Sidebar -->
      <div class="bg-dark border-bottom border-secondary p-3">
        <div class="mb-3">
          <h3 class="mb-0 fw-bold text-white" style="font-size: 1.5rem;">
            <i class="bi bi-building me-2"></i> Vĩnh Long Hotel
          </h3>
        </div>
        <div class="mb-2 d-flex align-items-center gap-2">
          <?php if($owner_has_avatar && !empty($owner_profile_img)): ?>
            <img src="<?php echo htmlspecialchars($owner_profile_img, ENT_QUOTES, 'UTF-8'); ?>" 
                 alt="<?php echo htmlspecialchars($owner_name, ENT_QUOTES, 'UTF-8'); ?>"
                 class="rounded-circle flex-shrink-0" 
                 style="width: 40px; height: 40px; object-fit: cover; border: 2px solid rgba(255,255,255,0.3);"
                 onerror="this.onerror=null; this.style.display='none'; const parent=this.parentElement; if(parent && !parent.querySelector('.owner-avatar-initial')) { const fallback=document.createElement('div'); fallback.className='owner-avatar-initial'; fallback.style.cssText='width: 40px; height: 40px; border-radius: 50%; background-color: #4A90E2; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 16px; border: 2px solid rgba(255,255,255,0.3);'; fallback.textContent='<?php echo htmlspecialchars(mb_strtoupper(mb_substr(trim($owner_name), 0, 1, 'UTF-8'), 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?>'; parent.appendChild(fallback); }">
          <?php else: ?>
            <div class="owner-avatar-initial rounded-circle flex-shrink-0 bg-primary d-flex align-items-center justify-content-center text-white fw-bold" 
                 style="width: 40px; height: 40px; font-size: 16px; border: 2px solid rgba(255,255,255,0.3);">
              <?php echo htmlspecialchars(mb_strtoupper(mb_substr(trim($owner_name), 0, 1, 'UTF-8'), 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?>
            </div>
          <?php endif; ?>
          <div class="flex-grow-1" style="min-width: 0;">
            <strong class="text-white d-block" style="font-size: 1rem; font-weight: 700;"><?php echo htmlspecialchars($hotel_name ?: 'Quản lý khách sạn', ENT_QUOTES, 'UTF-8'); ?></strong>
            <small class="text-muted d-block"><?php echo htmlspecialchars($owner_name, ENT_QUOTES, 'UTF-8'); ?></small>
          </div>
        </div>
        <div class="mt-2">
          <a href="logout.php" class="btn btn-danger btn-sm w-100 px-2 py-1">
            <i class="bi bi-box-arrow-right me-1"></i>Đăng xuất
          </a>
        </div>
      </div>
      
      <!-- Menu Navigation -->
      <nav class="navbar navbar-expand-lg navbar-dark p-0">
        <div class="container-fluid flex-lg-column align-items-stretch p-0">
          <div class="px-3 py-2 border-bottom border-secondary">
            <h6 class="mb-0 text-light"><i class="bi bi-speedometer2 me-2"></i>Menu</h6>
          </div>
          <button class="navbar-toggler shadow-none m-3" type="button" data-bs-toggle="collapse" data-bs-target="#ownerDropdown">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse flex-column align-items-stretch" id="ownerDropdown">
            <ul class="nav nav-pills flex-column px-2 py-2">
              <li class="nav-item">
                <a class="nav-link text-white" href="dashboard.php">
                  <i class="bi bi-speedometer me-2"></i> Dashboard
                </a>
              </li>
              <li class="nav-item d-flex justify-content-between align-items-center">
                <a class="nav-link text-white" href="rooms.php">
                  <i class="bi bi-door-open me-2"></i> Quản lý phòng
                </a>
                <?php if ($pending_rooms_count > 0): ?>
                  <span class="badge bg-warning text-dark" title="Phòng chờ duyệt"><?php echo $pending_rooms_count; ?></span>
                <?php endif; ?>
              </li>
              <li class="nav-item d-flex justify-content-between align-items-center">
                <a class="nav-link text-white" href="bookings.php">
                  <i class="bi bi-calendar-check me-2"></i> Đặt phòng
                </a>
                <?php if ($pending_count > 0): ?>
                  <span class="badge bg-danger" title="Booking mới cần xử lý"><?php echo $pending_count; ?></span>
                <?php endif; ?>
              </li>
              <li class="nav-item d-flex justify-content-between align-items-center">
                <a class="nav-link text-white" href="refund_bookings.php">
                  <i class="bi bi-arrow-counterclockwise me-2"></i> Hoàn tiền
                </a>
                <?php if ($refund_count > 0): ?>
                  <span class="badge bg-warning text-dark" title="Booking cần hoàn tiền"><?php echo $refund_count; ?></span>
                <?php endif; ?>
              </li>
              <li class="nav-item d-flex justify-content-between align-items-center">
                <a class="nav-link text-white" href="messages.php">
                  <i class="bi bi-chat-dots me-2"></i> Tin nhắn từ khách hàng
                </a>
                <?php
                // Kiểm tra bảng messages có tồn tại không
                $check_table = @mysqli_query($con, "SHOW TABLES LIKE 'messages'");
                $table_exists = $check_table && mysqli_num_rows($check_table) > 0;
                
                if($table_exists) {
                  $unread_messages = mysqli_fetch_assoc(mysqli_query($con, "
                    SELECT COUNT(*) AS c FROM messages 
                    WHERE owner_id = $owner_id 
                    AND sender_type = 'user' 
                    AND seen = 0
                  "))['c'] ?? 0;
                  if ($unread_messages > 0): ?>
                    <span class="badge bg-danger" title="Tin nhắn chưa đọc"><?php echo $unread_messages; ?></span>
                  <?php endif;
                }
                ?>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white" href="revenue.php">
                  <i class="bi bi-currency-dollar me-2"></i> Doanh thu
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white" href="reviews.php">
                  <i class="bi bi-star-half me-2"></i> Đánh giá
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white" href="profile.php">
                  <i class="bi bi-person-circle me-2"></i> Hồ sơ
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
  // #region agent log
  fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'owner/inc/header.php:140',message:'Active menu script started',data:{pathname:location.pathname,href:location.href},timestamp:Date.now(),sessionId:'debug-session',runId:'run3',hypothesisId:'A'})}).catch(()=>{});
  // #endregion
  
  // Lấy pathname hiện tại và normalize
  let currentPath = location.pathname;
  let currentFile = currentPath.split('/').pop() || '';
  
  // #region agent log
  fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'owner/inc/header.php:149',message:'Current file extracted',data:{currentFile:currentFile,currentPath:currentPath},timestamp:Date.now(),sessionId:'debug-session',runId:'run3',hypothesisId:'A'})}).catch(()=>{});
  // #endregion
  
  // Xóa active-menu khỏi TẤT CẢ links trước (quan trọng!)
  let allLinks = document.querySelectorAll("#dashboard-menu a");
  let removedCount = 0;
  allLinks.forEach(a => {
    if (a.classList.contains("active-menu")) {
      removedCount++;
    }
    a.classList.remove("active-menu");
  });
  
  // #region agent log
  fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'owner/inc/header.php:162',message:'Removed active-menu classes',data:{totalLinks:allLinks.length,removedCount:removedCount},timestamp:Date.now(),sessionId:'debug-session',runId:'run3',hypothesisId:'B'})}).catch(()=>{});
  // #endregion
  
  // Tìm link match chính xác
  let links = document.querySelectorAll("#dashboard-menu a");
  let foundMatch = null;
  let checkedLinks = [];
  
  links.forEach((a, index) => {
    let linkFile = '';
    let linkHref = a.getAttribute('href') || a.href || '';
    
    try {
      // Thử parse như absolute URL
      let linkUrl = new URL(a.href, window.location.origin);
      let linkPath = linkUrl.pathname;
      linkFile = linkPath.split('/').pop() || '';
    } catch(e) {
      // Nếu là relative URL, lấy từ href attribute
      linkFile = linkHref.split('/').pop() || '';
    }
    
    // #region agent log
    checkedLinks.push({index: index, href: linkHref, linkFile: linkFile, matches: linkFile === currentFile});
    // #endregion
    
    // So sánh CHÍNH XÁC tên file (không phải substring, không phải contains)
    // Ví dụ: "refund_bookings.php" !== "bookings.php"
    if (linkFile === currentFile && linkFile !== '' && currentFile !== '') {
      // Chỉ lưu match đầu tiên (không highlight ngay)
      if (!foundMatch) {
        foundMatch = a;
      }
    }
  });
  
  // #region agent log
  fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'owner/inc/header.php:195',message:'Checked all links',data:{currentFile:currentFile,checkedLinks:checkedLinks,foundMatch:foundMatch ? foundMatch.getAttribute('href') : null},timestamp:Date.now(),sessionId:'debug-session',runId:'run3',hypothesisId:'A'})}).catch(()=>{});
  // #endregion
  
  // Chỉ highlight 1 item duy nhất
  if (foundMatch) {
    // #region agent log
    fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'owner/inc/header.php:202',message:'Adding active-menu to single item',data:{href:foundMatch.getAttribute('href')},timestamp:Date.now(),sessionId:'debug-session',runId:'run3',hypothesisId:'C'})}).catch(()=>{});
    // #endregion
    foundMatch.classList.add("active-menu");
  }
  
  // #region agent log
  // Verify final state
  setTimeout(() => {
    let activeItems = [];
    document.querySelectorAll("#dashboard-menu a.active-menu").forEach(a => {
      activeItems.push(a.getAttribute('href'));
    });
    fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'owner/inc/header.php:212',message:'Final state check',data:{activeItems:activeItems,count:activeItems.length},timestamp:Date.now(),sessionId:'debug-session',runId:'run3',hypothesisId:'D'})}).catch(()=>{});
  }, 100);
  // #endregion
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

