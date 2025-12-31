<?php
session_start();
require_once('admin/inc/db_config.php');
require_once('admin/inc/essentials.php');

// Kiểm tra session TRƯỚC khi output bất kỳ thứ gì
if(!isset($_SESSION['login']) || $_SESSION['login'] != true){
    redirect('index.php');
}

// Mặc định là tiếng Việt, chỉ chuyển sang tiếng Anh khi có cookie 'lang' = 'en'
$lang_from_url = isset($_GET['_lang']) ? trim($_GET['_lang']) : '';
if ($lang_from_url === 'en' || $lang_from_url === 'vi') {
  setcookie('lang', $lang_from_url, time() + (365 * 24 * 60 * 60), '/', '', false, true);
  $_COOKIE['lang'] = $lang_from_url;
  $current_lang = $lang_from_url;
} else {
  $lang_cookie = isset($_COOKIE['lang']) ? trim($_COOKIE['lang']) : '';
  $current_lang = ($lang_cookie === 'en') ? 'en' : 'vi';
}
if ($current_lang !== 'en' && $current_lang !== 'vi') {
  $current_lang = 'vi';
}

// Kiểm tra user_id TRƯỚC khi output bất kỳ thứ gì
$user_id = $_SESSION['uId'] ?? 0;
if($user_id <= 0) {
    redirect('index.php');
}

require_once('inc/links.php');
require_once('inc/header.php');

// Hàm dịch cho trang messages
function t_messages($key, $lang = 'vi') {
  $translations = [
    'vi' => [
      'messages.pageTitle' => 'Tin nhắn',
      'messages.messages' => 'Tin nhắn',
      'messages.admin' => 'Quản trị viên',
      'messages.enterMessage' => 'Nhập tin nhắn...',
      'messages.send' => 'Gửi',
      'messages.noMessages' => 'Chưa có tin nhắn nào. Hãy bắt đầu cuộc trò chuyện!',
      'messages.autoMessage' => 'Tin nhắn tự động',
      'messages.sendError' => 'Có lỗi xảy ra khi gửi tin nhắn',
      'messages.conversations' => 'Cuộc trò chuyện',
      'messages.selectConversation' => 'Chọn một cuộc trò chuyện để bắt đầu',
    ],
    'en' => [
      'messages.pageTitle' => 'Messages',
      'messages.messages' => 'Messages',
      'messages.admin' => 'Administrator',
      'messages.enterMessage' => 'Enter message...',
      'messages.send' => 'Send',
      'messages.noMessages' => 'No messages yet. Start a conversation!',
      'messages.autoMessage' => 'Auto message',
      'messages.sendError' => 'An error occurred while sending message',
      'messages.conversations' => 'Conversations',
      'messages.selectConversation' => 'Select a conversation to start',
    ]
  ];
  return $translations[$lang][$key] ?? $translations['vi'][$key] ?? $key;
}

// Verify localStorage session_id matches current user_id
// This will be checked in JavaScript to ensure chat session belongs to current user

// Kiểm tra bảng messages
$check_table = mysqli_query($con, "SHOW TABLES LIKE 'messages'");
if(!$check_table || mysqli_num_rows($check_table) == 0) {
    // Tạo bảng nếu chưa có
    $create_sql = "CREATE TABLE IF NOT EXISTS `messages` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `owner_id` int(11) DEFAULT NULL COMMENT 'NULL = admin',
        `sender_type` enum('user','owner','admin') NOT NULL,
        `message` text NOT NULL,
        `seen` tinyint(1) DEFAULT 0,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`),
        KEY `owner_id` (`owner_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    mysqli_query($con, $create_sql);
}

// Lấy danh sách conversations - chỉ những người đã nhắn tin
$conversations = [];
$type = $_GET['type'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Luôn có conversation với admin (nếu đã nhắn tin)
$admin_conv = null;
$admin_check = @mysqli_query($con, "SELECT COUNT(*) as cnt FROM messages WHERE user_id=$user_id AND owner_id IS NULL");
if($admin_check && mysqli_num_rows($admin_check) > 0) {
    $admin_count = mysqli_fetch_assoc($admin_check)['cnt'];
    if($admin_count > 0) {
        $admin_sql = "SELECT 
                        (SELECT message FROM messages WHERE user_id=$user_id AND owner_id IS NULL ORDER BY created_at DESC LIMIT 1) as last_msg,
                        (SELECT created_at FROM messages WHERE user_id=$user_id AND owner_id IS NULL ORDER BY created_at DESC LIMIT 1) as last_time,
                        (SELECT COUNT(*) FROM messages WHERE user_id=$user_id AND owner_id IS NULL AND sender_type IN ('admin','owner') AND seen=0) as unread";
        $admin_result = @mysqli_query($con, $admin_sql);
        if($admin_result) {
            $admin_row = mysqli_fetch_assoc($admin_result);
            $admin_conv = [
                'type' => 'admin',
                'name' => t_messages('messages.admin', $current_lang),
                'id' => 0,
                'last_msg' => $admin_row['last_msg'] ?? '',
                'last_time' => $admin_row['last_time'] ?? '',
                'unread' => (int)($admin_row['unread'] ?? 0)
            ];
        }
    }
}

// Lấy danh sách owners đã nhắn tin với user này
$owners_conv_sql = "SELECT DISTINCT ho.id, ho.name, ho.hotel_name, ho.profile,
                    (SELECT message FROM messages WHERE user_id=$user_id AND owner_id=ho.id ORDER BY created_at DESC LIMIT 1) as last_msg,
                    (SELECT created_at FROM messages WHERE user_id=$user_id AND owner_id=ho.id ORDER BY created_at DESC LIMIT 1) as last_time,
                    (SELECT COUNT(*) FROM messages WHERE user_id=$user_id AND owner_id=ho.id AND sender_type='owner' AND seen=0) as unread
                    FROM messages m
                    INNER JOIN hotel_owners ho ON m.owner_id = ho.id
                    WHERE m.user_id=$user_id AND ho.status=1
                    ORDER BY last_time DESC";
$owners_conv_result = @mysqli_query($con, $owners_conv_sql);
$owners_conversations = [];
if($owners_conv_result) {
    while($row = mysqli_fetch_assoc($owners_conv_result)) {
        $owner_profile = $row['profile'] ?? 'user.png';
        $owner_profile_img = USERS_IMG_PATH . $owner_profile;
        $owner_has_avatar = !empty($owner_profile) && $owner_profile != 'user.png' && file_exists(UPLOAD_IMAGE_PATH . USERS_FOLDER . $owner_profile);
        $owner_initial = strtoupper(substr($row['name'] ?? 'O', 0, 1));
        $owners_conversations[] = [
            'type' => 'owner',
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'hotel_name' => $row['hotel_name'],
            'profile' => $owner_profile_img,
            'has_avatar' => $owner_has_avatar,
            'initial' => $owner_initial,
            'last_msg' => $row['last_msg'] ?? '',
            'last_time' => $row['last_time'] ?? '',
            'unread' => (int)($row['unread'] ?? 0)
        ];
    }
}

$selected_conv = null;
if($type == 'admin') {
    // Admin không cần id cụ thể
    $selected_conv = ['type' => 'admin', 'name' => t_messages('messages.admin', $current_lang), 'id' => 0];
} elseif($type == 'owner' && $id > 0) {
    $owner_check = select("SELECT * FROM hotel_owners WHERE id=? AND status=1", [$id], 'i');
    if($owner_check && mysqli_num_rows($owner_check) > 0) {
        $owner_data = mysqli_fetch_assoc($owner_check);
        $owner_profile = $owner_data['profile'] ?? 'user.png';
        $owner_profile_img = USERS_IMG_PATH . $owner_profile;
        $owner_has_avatar = !empty($owner_profile) && $owner_profile != 'user.png' && file_exists(UPLOAD_IMAGE_PATH . USERS_FOLDER . $owner_profile);
        $owner_initial = strtoupper(substr($owner_data['name'] ?? 'O', 0, 1));
        $selected_conv = [
            'type' => 'owner',
            'id' => (int)$id,
            'name' => $owner_data['name'],
            'hotel_name' => $owner_data['hotel_name'],
            'profile' => $owner_profile_img,
            'has_avatar' => $owner_has_avatar,
            'initial' => $owner_initial
        ];
        // Nếu chưa có trong danh sách conversations, thêm vào
        $found = false;
        foreach($owners_conversations as $oc) {
            if($oc['id'] == $id) {
                $found = true;
                break;
            }
        }
        if(!$found) {
            $owners_conversations[] = [
                'type' => 'owner',
                'id' => (int)$id,
                'name' => $owner_data['name'],
                'hotel_name' => $owner_data['hotel_name'],
                'last_msg' => '',
                'last_time' => '',
                'unread' => 0
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t_messages('messages.pageTitle', $current_lang); ?> - <?php echo $settings_r['site_title'] ?? 'Vĩnh Long Hotel'; ?></title>
    <style>
        .chat-container { height: calc(100vh - 200px); max-height: 700px; }
        .conversations-list { height: calc(100% - 60px); overflow-y: auto; }
        .chat-messages { height: calc(100% - 100px); overflow-y: auto; padding: 1rem; background: #f8f9fa; }
        .message-item { margin-bottom: 1rem; display: flex; gap: 0.5rem; }
        .message-item.user { flex-direction: row-reverse; }
        .message-bubble { max-width: 70%; padding: 0.75rem 1rem; border-radius: 18px; }
        .message-item.user .message-bubble { background: #0d6efd; color: white; }
        .message-item.owner .message-bubble, .message-item.admin .message-bubble { background: white; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
        .message-time { font-size: 0.75rem; color: #6c757d; margin-top: 0.25rem; }
        .conversation-item { padding: 1rem; border-bottom: 1px solid #e9ecef; cursor: pointer; }
        .conversation-item:hover { background: #f8f9fa; }
        .conversation-item.active { background: #e7f1ff; border-left: 3px solid #0d6efd; }
        .unread-badge { background: #dc3545; color: white; border-radius: 50%; width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; }
        
        /* System Message */
        .message-item.system-message {
            justify-content: center;
            margin: 12px 0;
        }
        .system-message-content {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #e3f2fd;
            border: 1px solid #90caf9;
            border-radius: 20px;
            color: #1565c0;
            font-size: 13px;
            font-weight: 500;
        }
        .system-message-content i {
            font-size: 16px;
            color: #1976d2;
        }
        
        /* Message Label (Bot or Admin) */
        .chat-message-auto-label {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 4px;
            font-weight: 500;
            opacity: 0.7;
            white-space: nowrap;
        }
        .chat-message-auto-label i {
            font-size: 11px;
            color: #9ca3af;
            flex-shrink: 0;
        }
        .chat-message-auto-label.admin-label {
            color: #059669;
            opacity: 0.9;
        }
        .chat-message-auto-label.admin-label i {
            color: #10b981;
        }
        
        /* Đảm bảo navbar và dropdown có z-index cao nhất và không bị che phủ */
        body {
            position: relative;
            padding-top: 72px; /* Để tránh nội dung bị che bởi fixed navbar */
        }
        
        .navbar {
            z-index: 9999 !important;
            /* Giữ position: fixed từ header.php để navbar cố định ở đầu trang */
        }
        
        .navbar-collapse {
            z-index: 10000 !important;
        }
        
        .navbar-nav {
            z-index: 10001 !important;
        }
        
        /* Đảm bảo user pill và dropdown có thể click */
        .d-flex.align-items-center.gap-2 {
            position: relative !important;
            z-index: 10002 !important;
            pointer-events: auto !important;
        }
        
        .user-pill-modern {
            pointer-events: auto !important;
            cursor: pointer !important;
            position: relative !important;
            z-index: 10003 !important;
        }
        
        .user-pill-modern.dropdown-toggle {
            pointer-events: auto !important;
            cursor: pointer !important;
            position: relative !important;
            z-index: 10004 !important;
        }
        
        .dropdown {
            position: relative !important;
            z-index: 10005 !important;
        }
        
        .navbar .dropdown-menu {
            z-index: 10006 !important;
        }
        
        .dropdown-toggle {
            pointer-events: auto !important;
            cursor: pointer !important;
        }
        
        /* Đảm bảo container không che khuất navbar */
        .container-fluid {
            position: relative;
            z-index: 1 !important;
        }
        
        .card {
            position: relative;
            z-index: 1 !important;
        }
        
        .page-header {
            position: relative;
            z-index: 1 !important;
        }
        
        /* Đảm bảo không có element nào che phủ navbar */
        * {
            box-sizing: border-box;
        }
        
        /* Loại bỏ pointer-events: none nếu có */
        .navbar * {
            pointer-events: auto !important;
        }
        
        /* Đảm bảo navbar collapse không che phủ dropdown trên mobile */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                z-index: 10001 !important;
            }
            
            .navbar-nav {
                z-index: 10002 !important;
            }
            
            .d-flex.align-items-center.gap-2 {
                z-index: 10003 !important;
            }
            
            .user-pill-modern.dropdown-toggle {
                z-index: 10004 !important;
            }
        }
        
        /* Đảm bảo không có overlay nào che phủ */
        .navbar-collapse.show {
            z-index: 10001 !important;
        }
        
        /* Đảm bảo dropdown menu hiển thị trên mobile */
        .navbar .dropdown-menu {
            position: absolute !important;
        }
        
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <div class="page-header mb-4" style="background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%); color: white; border-radius: 16px; padding: 20px 24px; box-shadow: 0 10px 26px rgba(0, 0, 0, 0.05);">
            <div class="d-flex align-items-center gap-3">
                <div class="logo-wrapper" style="width: 60px; height: 60px; background: rgba(255, 255, 255, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="bi bi-building" style="font-size: 32px; color: white;"></i>
                </div>
                <div class="flex-grow-1">
                    <h4 class="mb-0" style="color: white; font-weight: 700; font-size: 1.75rem;">
                        <i class="bi bi-chat-dots me-2"></i><span data-i18n="messages.messages" data-i18n-skip><?php echo t_messages('messages.messages', $current_lang); ?></span>
                    </h4>
                    <p class="mb-0 mt-1" style="color: rgba(255, 255, 255, 0.9); font-size: 0.95rem;">
                        <?php echo htmlspecialchars($settings_r['site_title'] ?? 'Vĩnh Long Hotel', ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0" data-i18n="messages.conversations" data-i18n-skip><?php echo t_messages('messages.conversations', $current_lang); ?></h6>
                    </div>
                    <div class="conversations-list">
                        <?php if($admin_conv): ?>
                            <div class="conversation-item <?php echo ($type=='admin') ? 'active' : ''; ?>" 
                                 onclick="startChat('admin', 0)">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold d-flex align-items-center gap-2">
                                            <i class="bi bi-shield-check text-primary"></i>
                                            <?php echo htmlspecialchars($admin_conv['name']); ?>
                                        </div>
                                        <?php if(!empty($admin_conv['last_msg'])): ?>
                                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                                <?php echo htmlspecialchars(mb_substr($admin_conv['last_msg'], 0, 30)) . (mb_strlen($admin_conv['last_msg']) > 30 ? '...' : ''); ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <?php if($admin_conv['unread'] > 0): ?>
                                        <span class="unread-badge"><?php echo $admin_conv['unread']; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="p-3">
                                <button class="btn btn-primary btn-sm w-100 mb-2 <?php echo ($type=='admin') ? 'active' : ''; ?>" onclick="startChat('admin', 0)">
                                    <i class="bi bi-shield-check me-1"></i> Nhắn với Admin
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(count($owners_conversations) > 0): ?>
                            <div class="px-3 py-2 border-top">
                                <small class="text-muted fw-semibold">Chủ khách sạn</small>
                            </div>
                            <?php foreach($owners_conversations as $owner_conv): ?>
                                <div class="conversation-item <?php echo ($type=='owner' && $id==$owner_conv['id']) ? 'active' : ''; ?>" 
                                     onclick="startChat('owner', <?php echo $owner_conv['id']; ?>)">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2 flex-grow-1" style="min-width: 0;">
                                            <?php if(isset($owner_conv['has_avatar']) && $owner_conv['has_avatar']): ?>
                                                <img src="<?php echo htmlspecialchars($owner_conv['profile'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                                                     alt="<?php echo htmlspecialchars($owner_conv['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                     class="rounded-circle flex-shrink-0" 
                                                     style="width: 48px; height: 48px; object-fit: cover;">
                                            <?php endif; ?>
                                            <div class="flex-grow-1" style="min-width: 0;">
                                                <div class="fw-bold">
                                                    <?php echo htmlspecialchars($owner_conv['hotel_name'] ?? $owner_conv['name']); ?>
                                                </div>
                                                <small class="text-muted d-block"><?php echo htmlspecialchars($owner_conv['name']); ?></small>
                                                <?php if(!empty($owner_conv['last_msg'])): ?>
                                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                                        <?php echo htmlspecialchars(mb_substr($owner_conv['last_msg'], 0, 30)) . (mb_strlen($owner_conv['last_msg']) > 30 ? '...' : ''); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php if($owner_conv['unread'] > 0): ?>
                                            <span class="unread-badge ms-2 flex-shrink-0"><?php echo $owner_conv['unread']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card shadow-sm chat-container">
                    <?php if($selected_conv): ?>
                        <div class="card-header bg-white">
                            <h6 class="mb-0">
                                <?php if($selected_conv['type'] == 'admin'): ?>
                                    <i class="bi bi-shield-check text-primary me-1"></i>
                                    <?php echo htmlspecialchars($selected_conv['name'] ?? 'Admin'); ?>
                                <?php else: ?>
                                    <i class="bi bi-building text-success me-1"></i>
                                    <?php echo htmlspecialchars($selected_conv['hotel_name'] ?? $selected_conv['name']); ?>
                                <?php endif; ?>
                            </h6>
                        </div>
                        <div class="chat-messages" id="chatMessages" data-type="<?php echo $selected_conv['type']; ?>" data-id="<?php echo ($selected_conv['type'] == 'admin') ? '0' : $id; ?>"></div>
                        <div class="card-footer bg-white">
                            <form onsubmit="sendMessage(event)">
                                <div class="input-group">
                                    <input type="text" id="messageInput" class="form-control" data-i18n-placeholder="messages.enterMessage" placeholder="<?php echo t_messages('messages.enterMessage', $current_lang); ?>" required>
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> <span data-i18n="messages.send" data-i18n-skip><?php echo t_messages('messages.send', $current_lang); ?></span></button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                            <div class="text-center">
                                <i class="bi bi-chat-left-text fs-1 d-block mb-3"></i>
                                <p data-i18n="messages.selectConversation" data-i18n-skip><?php echo t_messages('messages.selectConversation', $current_lang); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php require('admin/inc/scripts.php'); ?>
    <script>
    // Clear localStorage if user_id doesn't match
    const currentUserId = <?php echo $user_id; ?>;
    const storedSessionId = localStorage.getItem('chat_session_id') || '';
    
    // Check if stored session_id belongs to current user
    // Session ID format: chat_user_{user_id}
    if (storedSessionId && !storedSessionId.includes('chat_user_' + currentUserId)) {
      // Clear all chat-related localStorage if session doesn't match
      try {
        localStorage.removeItem('chat_session_id');
        localStorage.removeItem('chat_history');
        localStorage.removeItem('chat_user_profile');
        localStorage.removeItem('lastMessageId');
        
        // Clear all localStorage items that start with 'chat_'
        const keysToRemove = [];
        for (let i = 0; i < localStorage.length; i++) {
          const key = localStorage.key(i);
          if (key && key.startsWith('chat_')) {
            keysToRemove.push(key);
          }
        }
        keysToRemove.forEach(key => localStorage.removeItem(key));
        
        console.log('Cleared localStorage: Session ID did not match current user');
      } catch (e) {
        console.error('Error clearing localStorage:', e);
      }
    }
    
    function startChat(type, id) {
        window.location.href = 'messages.php?type=' + type + '&id=' + id;
    }
    
    let displayedMsgIds = new Set();
    let isLoading = false;
    let isFirstLoad = true;
    
    function loadMessages() {
        if(isLoading) return;
        const el = document.getElementById('chatMessages');
        if(!el) return;
        const type = el.dataset.type;
        const id = el.dataset.id;
        if(!type) return;
        // Cho phép id = 0 khi type = 'admin'
        if(type != 'admin' && (!id || id <= 0)) return;
        
        isLoading = true;
        const wasAtBottom = el.scrollHeight - el.scrollTop <= el.clientHeight + 50;
        
        fetch('ajax/messages.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get&type=' + type + '&id=' + id
        })
        .then(r => {
            if(!r.ok) {
                throw new Error('HTTP error: ' + r.status);
            }
            return r.json();
        })
        .then(data => {
            if(data.status == 'success' && Array.isArray(data.messages)) {
                // Lần đầu load, hiển thị tất cả
                if(isFirstLoad) {
                    el.innerHTML = '';
                    displayedMsgIds.clear();
                    isFirstLoad = false;
                }
                
                if(data.messages.length === 0) {
                    if(el.children.length === 0 || el.querySelector('.loading-message')) {
                        const noMessagesText = window.i18n ? window.i18n.translate('messages.noMessages') : 'Chưa có tin nhắn nào. Hãy bắt đầu cuộc trò chuyện!';
                        el.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted"><div class="text-center"><i class="bi bi-chat-left-text fs-1 d-block mb-3"></i><p>' + noMessagesText + '</p></div></div>';
                    }
                } else {
                    data.messages.forEach(msg => {
                        const msgId = msg.id || (msg.time + msg.message);
                        if(!displayedMsgIds.has(msgId)) {
                            displayedMsgIds.add(msgId);
                            
                            // Check if this is a system message
                            const isSystemMessage = msg.message_type === 'system';
                            
                            // System messages have special styling (centered, no avatar)
                            if (isSystemMessage) {
                                const systemDiv = document.createElement('div');
                                systemDiv.className = 'message-item system-message';
                                systemDiv.setAttribute('data-msg-id', msgId);
                                
                                // Process message text
                                let processedMessage = String(msg.message || '');
                                processedMessage = processedMessage.replace(/\\n/g, '\n');
                                processedMessage = processedMessage.replace(/\\r\\n/g, '\n');
                                processedMessage = processedMessage.replace(/\\r/g, '\n');
                                processedMessage = processedMessage.replace(/\r\n/g, '<br>').replace(/\r/g, '<br>').replace(/\n/g, '<br>');
                                
                                systemDiv.innerHTML = `
                                    <div class="system-message-content">
                                        <i class="bi bi-info-circle"></i>
                                        <span>${processedMessage}</span>
                                    </div>
                                `;
                                el.appendChild(systemDiv);
                                return;
                            }
                            
                            const div = document.createElement('div');
                            div.className = 'message-item ' + (msg.sender_type == 'user' ? 'user' : (msg.sender_type == 'admin' ? 'admin' : 'owner'));
                            div.setAttribute('data-msg-id', msgId);
                            
                            // Determine if this is a bot message or real admin message
                            const isBotMessage = msg.is_bot === 1;
                            
                            // Tạo avatar - chỉ hiển thị nếu có ảnh
                            let avatar = null;
                            if(msg.sender_type == 'user' && msg.user_profile) {
                                avatar = document.createElement('div');
                                avatar.className = 'message-avatar';
                                avatar.style.cssText = 'width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;';
                                const img = document.createElement('img');
                                img.src = msg.user_profile;
                                img.alt = msg.user_initial || 'U';
                                img.style.cssText = 'width: 100%; height: 100%; object-fit: cover; border-radius: 50%;';
                                img.onerror = function() {
                                    if(avatar) avatar.style.display = 'none';
                                };
                                avatar.appendChild(img);
                            } else if((msg.sender_type == 'owner' || msg.sender_type == 'admin')) {
                                avatar = document.createElement('div');
                                avatar.className = 'message-avatar';
                                avatar.style.cssText = 'width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: ' + (isBotMessage ? 'linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%)' : 'linear-gradient(135deg, #10b981 0%, #059669 100%)') + '; color: white; font-weight: bold;';
                                if(msg.owner_profile) {
                                    const img = document.createElement('img');
                                    img.src = msg.owner_profile;
                                    img.alt = msg.owner_initial || 'O';
                                    img.style.cssText = 'width: 100%; height: 100%; object-fit: cover; border-radius: 50%;';
                                    img.onerror = function() {
                                        avatar.innerHTML = isBotMessage ? '<i class="bi bi-robot"></i>' : '<i class="bi bi-person-badge"></i>';
                                    };
                                    avatar.appendChild(img);
                                } else {
                                    avatar.innerHTML = isBotMessage ? '<i class="bi bi-robot"></i>' : '<i class="bi bi-person-badge"></i>';
                                }
                            }
                            
                            // Tạo bubble
                            const bubble = document.createElement('div');
                            bubble.className = 'message-bubble';
                            
                            // Add label to distinguish bot messages from real admin messages
                            let labelHtml = '';
                            if(msg.sender_type == 'admin' || msg.sender_type == 'owner') {
                                if(isBotMessage) {
                                    const autoMessageText = window.i18n ? window.i18n.translate('messages.autoMessage') : 'Tin nhắn tự động';
                                    labelHtml = '<div class="chat-message-auto-label"><i class="bi bi-robot"></i> ' + autoMessageText + '</div>';
                                } else {
                                    labelHtml = '<div class="chat-message-auto-label admin-label"><i class="bi bi-person-check"></i> Nhân viên hỗ trợ</div>';
                                }
                            }
                            
                            // Process message text: convert \n to <br> and handle markdown
                            let processedMessage = String(msg.message || '');
                            
                            // Handle escaped newlines from JSON (\\n -> \n)
                            processedMessage = processedMessage.replace(/\\n/g, '\n');
                            processedMessage = processedMessage.replace(/\\r\\n/g, '\n');
                            processedMessage = processedMessage.replace(/\\r/g, '\n');
                            
                            // Convert all types of newlines to <br>
                            processedMessage = processedMessage.replace(/\r\n/g, '<br>').replace(/\r/g, '<br>').replace(/\n/g, '<br>');
                            
                            // Convert markdown **text** to <strong>text</strong>
                            processedMessage = processedMessage.replace(/\*\*([^*<]+?)\*\*/g, '<strong>$1</strong>');
                            
                            bubble.innerHTML = labelHtml + '<div>' + processedMessage + '</div><div class="message-time">' + (msg.time || '') + '</div>';
                            
                            if(msg.sender_type == 'user') {
                                div.appendChild(bubble);
                                if(avatar) div.appendChild(avatar);
                            } else {
                                if(avatar) div.appendChild(avatar);
                                div.appendChild(bubble);
                            }
                            
                            el.appendChild(div);
                        }
                    });
                    if(wasAtBottom || isFirstLoad) {
                        setTimeout(() => {
                            el.scrollTop = el.scrollHeight;
                        }, 100);
                    }
                }
            }
            isLoading = false;
        })
        .catch(e => {
            console.error('Error loading messages:', e);
            isLoading = false;
        });
    }
    
    function sendMessage(e) {
        e.preventDefault();
        const el = document.getElementById('chatMessages');
        const input = document.getElementById('messageInput');
        if(!el || !input) return;
        
        const type = el.dataset.type;
        const id = el.dataset.id;
        const msg = input.value.trim();
        if(!msg) return;
        
        fetch('ajax/messages.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=send&type=' + type + '&id=' + id + '&message=' + encodeURIComponent(msg)
        })
        .then(r => {
            if(!r.ok) {
                throw new Error('HTTP error: ' + r.status);
            }
            return r.json();
        })
        .then(data => {
            if(data.status == 'success') {
                input.value = '';
                isFirstLoad = true;
                loadMessages();
            } else {
                alert('Lỗi: ' + (data.msg || 'Không thể gửi'));
            }
        })
        .catch(e => {
            console.error('Error sending message:', e);
            const errorMsg = window.i18n ? window.i18n.translate('messages.sendError') : 'Có lỗi xảy ra khi gửi tin nhắn';
            alert(errorMsg);
        });
    }
    
    // Note: Admin status notification is now only shown when admin actually sends a message
    // (via system message in admin/ajax/messages.php), not when admin just views the page
    
    if(document.getElementById('chatMessages')) {
        isFirstLoad = true;
        loadMessages();
        
        const intervalId = setInterval(loadMessages, 5000);
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            clearInterval(intervalId);
        });
    }
    
    // Đảm bảo dropdown menu tài khoản hoạt động
    (function() {
        function initDropdown() {
            const userPill = document.querySelector('.user-pill-modern.dropdown-toggle');
            if (!userPill) {
                setTimeout(initDropdown, 100);
                return;
            }
            
            // Đảm bảo dropdown button có thể click
            userPill.style.pointerEvents = 'auto';
            userPill.style.cursor = 'pointer';
            userPill.style.zIndex = '10004';
            userPill.style.position = 'relative';
            userPill.style.touchAction = 'manipulation'; // Cho mobile
            
            // Đảm bảo không có element nào che phủ
            const userPillParent = userPill.closest('.d-flex.align-items-center.gap-2');
            if (userPillParent) {
                userPillParent.style.pointerEvents = 'auto';
                userPillParent.style.zIndex = '10002';
                userPillParent.style.position = 'relative';
            }
            
            // Đảm bảo navbar không bị che phủ
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                navbar.style.zIndex = '9999';
                navbar.style.position = 'relative';
            }
            
            // Đảm bảo navbar-collapse không che phủ trên mobile
            const navbarCollapse = document.querySelector('.navbar-collapse');
            if (navbarCollapse) {
                navbarCollapse.style.zIndex = '10001';
            }
            
            // Khởi tạo Bootstrap dropdown
            if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                try {
                    // Xóa instance cũ nếu có
                    const existingInstance = bootstrap.Dropdown.getInstance(userPill);
                    if (existingInstance) {
                        existingInstance.dispose();
                    }
                    // Tạo instance mới
                    const dropdownInstance = new bootstrap.Dropdown(userPill);
                    console.log('Dropdown initialized successfully');
                    
                    // Thêm event listener trực tiếp để đảm bảo hoạt động
                    userPill.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const dropdownMenu = userPill.nextElementSibling;
                        if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                            const isShown = dropdownMenu.classList.contains('show');
                            // Đóng tất cả dropdown khác
                            document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                                menu.classList.remove('show');
                            });
                            // Toggle dropdown hiện tại
                            if (!isShown) {
                                dropdownMenu.classList.add('show');
                            }
                        }
                    }, true);
                } catch(e) {
                    console.error('Error initializing dropdown:', e);
                }
            } else {
                // Thử lại sau khi Bootstrap load
                setTimeout(initDropdown, 200);
            }
        }
        
        // Chạy khi DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initDropdown);
        } else {
            initDropdown();
        }
        
        // Thử lại sau khi window load
        window.addEventListener('load', function() {
            setTimeout(initDropdown, 100);
        });
    })();
    </script>

<?php require('inc/footer.php'); ?>
<?php require('inc/modals.php'); ?>

