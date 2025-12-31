<?php
session_start();
require('inc/essentials.php');
require('../admin/inc/db_config.php');

if(!isset($_SESSION['ownerLogin']) || $_SESSION['ownerLogin'] != true) {
    echo "<script>window.location.href='index.php'</script>";
    exit;
}

$owner_id = $_SESSION['ownerId'] ?? 0;
if($owner_id <= 0) {
    echo "<script>window.location.href='index.php'</script>";
    exit;
}

// Lấy thông tin owner (bao gồm avatar)
$owner_info = select("SELECT name, hotel_name, profile FROM hotel_owners WHERE id=?", [$owner_id], 'i');
$owner_data = mysqli_fetch_assoc($owner_info);
$owner_name = $owner_data['name'] ?? 'Chủ khách sạn';
$owner_hotel = $owner_data['hotel_name'] ?? '';
$owner_profile = $owner_data['profile'] ?? 'user.png';
$owner_profile_img = USERS_IMG_PATH . $owner_profile;
$owner_has_avatar = !empty($owner_profile) && $owner_profile != 'user.png' && file_exists(UPLOAD_IMAGE_PATH . USERS_FOLDER . $owner_profile);
$owner_initial = strtoupper(substr($owner_name, 0, 1));

// Kiểm tra bảng messages có tồn tại không
$check_table = @mysqli_query($con, "SHOW TABLES LIKE 'messages'");
$table_exists = $check_table && mysqli_num_rows($check_table) > 0;

// Lấy danh sách users đã nhắn tin
$conversations = [];
if($table_exists && isset($con) && $owner_id > 0) {
    $sql = "SELECT DISTINCT m.user_id, uc.name, uc.email, uc.profile,
            (SELECT message FROM messages WHERE user_id=m.user_id AND owner_id=m.owner_id ORDER BY created_at DESC LIMIT 1) as last_msg,
            (SELECT created_at FROM messages WHERE user_id=m.user_id AND owner_id=m.owner_id ORDER BY created_at DESC LIMIT 1) as last_time,
            (SELECT COUNT(*) FROM messages WHERE user_id=m.user_id AND owner_id=m.owner_id AND sender_type='user' AND seen=0) as unread
            FROM messages m
            INNER JOIN user_cred uc ON m.user_id = uc.id
            WHERE m.owner_id = $owner_id
            ORDER BY last_time DESC";
    $conversations_result = @mysqli_query($con, $sql);
    if($conversations_result) {
        while($row = mysqli_fetch_assoc($conversations_result)) {
            $conversations[] = $row;
        }
    }
}

$selected_user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$selected_user = null;
if($selected_user_id > 0) {
    $user_check = select("SELECT * FROM user_cred WHERE id=?", [$selected_user_id], 'i');
    if($user_check && mysqli_num_rows($user_check) > 0) {
        $selected_user = mysqli_fetch_assoc($user_check);
    } else {
        $selected_user_id = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tin nhắn từ khách hàng - Chủ khách sạn</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        body {
            background: #f8f9fa;
        }
        
        .chat-container { 
            height: calc(100vh - 250px); 
            max-height: 750px;
            display: flex;
            flex-direction: column;
        }
        
        .conversations-list { 
            height: calc(100% - 70px); 
            overflow-y: auto;
            padding: 10px;
        }
        
        .conversations-list::-webkit-scrollbar {
            width: 6px;
        }
        
        .conversations-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .conversations-list::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        .conversations-list::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        .chat-messages { 
            flex: 1;
            min-height: 300px;
            overflow-y: auto; 
            padding: 20px;
            background: linear-gradient(to bottom, #f5f7fa 0%, #e8ecf1 100%);
        }
        
        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }
        
        .chat-messages::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .chat-messages::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        .message-item { 
            margin-bottom: 15px; 
            display: flex; 
            gap: 10px;
            align-items: flex-end;
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .message-item.owner { 
            flex-direction: row-reverse; 
        }
        
        .message-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            flex-shrink: 0;
            overflow: hidden;
            background-size: cover;
            background-position: center;
        }
        
        .message-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .message-item.owner .message-avatar {
            background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
        }
        
        .message-item.user .message-avatar {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }
        
        .message-avatar.has-image {
            background: none;
        }
        
        .message-bubble { 
            max-width: 70%; 
            padding: 12px 16px; 
            border-radius: 18px;
            position: relative;
            word-wrap: break-word;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .message-bubble:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .message-item.owner .message-bubble { 
            background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
            color: white;
            border-bottom-right-radius: 4px;
        }
        
        .message-item.user .message-bubble { 
            background: white;
            border-bottom-left-radius: 4px;
            border: 1px solid #e9ecef;
        }
        
        .message-time { 
            font-size: 0.7rem; 
            color: rgba(255, 255, 255, 0.8);
            margin-top: 5px;
            opacity: 0.9;
        }
        
        .message-item.user .message-time {
            color: #6c757d;
        }
        
        .conversation-item { 
            padding: 15px; 
            border-bottom: 1px solid #e9ecef; 
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 12px;
            margin-bottom: 8px;
            position: relative;
            overflow: hidden;
        }
        
        .conversation-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }
        
        .conversation-item:hover { 
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .conversation-item:hover::before {
            transform: scaleY(1);
        }
        
        .conversation-item.active { 
            background: #f8f9fa;
            border-left: 4px solid #0d6efd;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
        }
        
        .conversation-item.active::before {
            transform: scaleY(1);
        }
        
        .conversation-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            margin-right: 12px;
            flex-shrink: 0;
            overflow: hidden;
            background-size: cover;
            background-position: center;
        }
        
        .conversation-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .conversation-avatar.has-image {
            background: none;
        }
        
        .conversation-info {
            flex: 1;
            min-width: 0;
        }
        
        .conversation-name {
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .conversation-email {
            font-size: 0.85rem;
            color: #6c757d;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .conversation-last-msg {
            font-size: 0.8rem;
            color: #868e96;
            margin-top: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .unread-badge { 
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white; 
            border-radius: 50%; 
            width: 24px; 
            height: 24px; 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 0.75rem;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        
        .card {
            border: 1px solid #e5e7eb;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 26px rgba(0, 0, 0, 0.05);
        }
        
        .card-header {
            background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
            color: white;
            padding: 15px 20px;
            border-bottom: none;
        }
        
        .card-header h6 {
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .card-footer {
            background: white;
            border-top: 1px solid #e9ecef;
            padding: 15px 20px;
        }
        
        .form-control {
            border-radius: 25px;
            border: 2px solid #e9ecef;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.4);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #0f172a;
            min-height: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #0f172a;
            opacity: 0.3;
            margin-bottom: 20px;
        }
        
        .empty-state p {
            font-size: 1.1rem;
            margin: 0;
            color: #0f172a;
        }
        
        .chat-header {
            background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .chat-header-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
            overflow: hidden;
            background-size: cover;
            background-position: center;
        }
        
        .chat-header-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .chat-header-avatar.has-image {
            background: none;
        }
        
        .chat-header-info h6 {
            margin: 0;
            font-weight: 600;
        }
        
        .chat-header-info small {
            opacity: 0.9;
            font-size: 0.85rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid p-0">
        <div class="row g-0">
            <?php require('inc/header.php'); ?>
            <div class="col-lg-10 p-4">
                <div class="page-header mb-4" style="background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%); color: white; border-radius: 16px; padding: 20px 24px; box-shadow: 0 10px 26px rgba(0, 0, 0, 0.05);">
                    <h4 class="mb-0" style="color: white; font-weight: 700;"><i class="bi bi-chat-dots me-2"></i>Tin nhắn từ khách hàng</h4>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h6 class="mb-0" style="color: white;">Cuộc trò chuyện</h6>
                            </div>
                            <div class="conversations-list">
                                <?php if(count($conversations) > 0): ?>
                                    <?php foreach($conversations as $conv): 
                                        $initial = strtoupper(substr($conv['name'] ?? 'U', 0, 1));
                                        $profile_img = !empty($conv['profile']) ? USERS_IMG_PATH . $conv['profile'] : '';
                                        $has_avatar = !empty($profile_img) && file_exists(UPLOAD_IMAGE_PATH . USERS_FOLDER . $conv['profile']);
                                    ?>
                                        <div class="conversation-item <?php echo ($selected_user_id == $conv['user_id']) ? 'active' : ''; ?>" 
                                             onclick="loadChat(<?php echo $conv['user_id']; ?>)">
                                            <div class="d-flex align-items-center">
                                                <div class="conversation-avatar <?php echo $has_avatar ? 'has-image' : ''; ?>">
                                                    <?php if($has_avatar): ?>
                                                        <img src="<?php echo $profile_img; ?>" alt="<?php echo htmlspecialchars($conv['name'] ?? ''); ?>" onerror="this.parentElement.classList.remove('has-image'); this.remove(); this.parentElement.textContent='<?php echo $initial; ?>';">
                                                    <?php else: ?>
                                                        <?php echo $initial; ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="conversation-info flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div class="flex-grow-1" style="min-width: 0;">
                                                            <div class="conversation-name"><?php echo htmlspecialchars($conv['name'] ?? ''); ?></div>
                                                            <div class="conversation-email"><?php echo htmlspecialchars($conv['email'] ?? ''); ?></div>
                                                            <?php if(!empty($conv['last_msg'])): ?>
                                                                <div class="conversation-last-msg"><?php echo htmlspecialchars(mb_substr($conv['last_msg'], 0, 30)) . (mb_strlen($conv['last_msg']) > 30 ? '...' : ''); ?></div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php if(isset($conv['unread']) && $conv['unread'] > 0): ?>
                                                            <span class="unread-badge ms-2"><?php echo $conv['unread']; ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>Chưa có cuộc trò chuyện</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="card chat-container">
                            <?php if($selected_user): 
                                $user_initial = strtoupper(substr($selected_user['name'] ?? 'U', 0, 1));
                                $user_profile_img = !empty($selected_user['profile']) ? USERS_IMG_PATH . $selected_user['profile'] : '';
                                $user_has_avatar = !empty($user_profile_img) && file_exists(UPLOAD_IMAGE_PATH . USERS_FOLDER . $selected_user['profile']);
                            ?>
                                <div class="chat-header">
                                    <div class="chat-header-avatar <?php echo $user_has_avatar ? 'has-image' : ''; ?>">
                                        <?php if($user_has_avatar): ?>
                                            <img src="<?php echo $user_profile_img; ?>" alt="<?php echo htmlspecialchars($selected_user['name'] ?? ''); ?>" onerror="this.parentElement.classList.remove('has-image'); this.remove(); this.parentElement.textContent='<?php echo $user_initial; ?>';">
                                        <?php else: ?>
                                            <?php echo $user_initial; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="chat-header-info flex-grow-1">
                                        <h6><?php echo htmlspecialchars($selected_user['name'] ?? ''); ?></h6>
                                        <small><?php echo htmlspecialchars($selected_user['email'] ?? ''); ?></small>
                                    </div>
                                </div>
                                <div class="chat-messages" id="chatMessages" data-user-id="<?php echo $selected_user_id; ?>">
                                    <div class="loading-message">Đang tải tin nhắn...</div>
                                </div>
                                <div class="card-footer bg-white">
                                    <form onsubmit="sendMessage(event)">
                                        <div class="input-group">
                                            <input type="text" id="messageInput" class="form-control" placeholder="Nhập tin nhắn..." required>
                                            <button type="submit" class="btn btn-success"><i class="bi bi-send"></i> Gửi</button>
                                        </div>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="bi bi-chat-left-text"></i>
                                    <p>Chọn một cuộc trò chuyện để bắt đầu</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require('../admin/inc/scripts.php'); ?>
    <script>
    function loadChat(userId) {
        window.location.href = 'messages.php?user_id=' + userId;
    }
    
    let displayedMsgIds = new Set();
    let isLoading = false;
    let isFirstLoad = true;
    
    function loadMessages() {
        if(isLoading) return;
        const el = document.getElementById('chatMessages');
        if(!el) return;
        const userId = el.dataset.userId;
        if(!userId || userId <= 0) return;
        
        isLoading = true;
        const wasAtBottom = el.scrollHeight - el.scrollTop <= el.clientHeight + 50;
        
        fetch('ajax/messages.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get&user_id=' + userId
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
                        el.innerHTML = '<div class="empty-state"><i class="bi bi-chat-left-text"></i><p>Chưa có tin nhắn nào. Hãy bắt đầu cuộc trò chuyện!</p></div>';
                    }
                } else {
                    data.messages.forEach(msg => {
                        const msgId = msg.id || (msg.time + msg.message);
                        if(!displayedMsgIds.has(msgId)) {
                            displayedMsgIds.add(msgId);
                            const div = document.createElement('div');
                            div.className = 'message-item ' + (msg.sender_type == 'owner' ? 'owner' : 'user');
                            div.setAttribute('data-msg-id', msgId);
                            
                            const avatar = document.createElement('div');
                            avatar.className = 'message-avatar';
                            if(msg.sender_type == 'owner') {
                                // Owner message - show owner avatar
                                if(msg.owner_profile) {
                                    const img = document.createElement('img');
                                    img.src = msg.owner_profile;
                                    img.alt = msg.owner_initial || 'O';
                                    img.onerror = function() {
                                        this.remove();
                                        avatar.textContent = msg.owner_initial || 'O';
                                        avatar.classList.remove('has-image');
                                    };
                                    avatar.classList.add('has-image');
                                    avatar.appendChild(img);
                                } else {
                                    avatar.textContent = msg.owner_initial || 'O';
                                }
                            } else {
                                // User message - show avatar if available
                                if(msg.user_profile) {
                                    const img = document.createElement('img');
                                    img.src = msg.user_profile;
                                    img.alt = msg.user_initial || 'U';
                                    img.onerror = function() {
                                        this.remove();
                                        avatar.textContent = msg.user_initial || 'U';
                                        avatar.classList.remove('has-image');
                                    };
                                    avatar.classList.add('has-image');
                                    avatar.appendChild(img);
                                } else {
                                    avatar.textContent = msg.user_initial || 'U';
                                }
                            }
                            
                            const bubble = document.createElement('div');
                            bubble.className = 'message-bubble';
                            bubble.innerHTML = '<div>' + (msg.message || '') + '</div><div class="message-time">' + (msg.time || '') + '</div>';
                            
                            div.appendChild(avatar);
                            div.appendChild(bubble);
                            el.appendChild(div);
                        }
                    });
                }
                if(wasAtBottom || isFirstLoad) {
                    el.scrollTop = el.scrollHeight;
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
        
        const userId = el.dataset.userId;
        const msg = input.value.trim();
        if(!msg) return;
        
        fetch('ajax/messages.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=send&user_id=' + userId + '&message=' + encodeURIComponent(msg)
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
            alert('Có lỗi xảy ra khi gửi tin nhắn');
        });
    }
    
    if(document.getElementById('chatMessages')) {
        isFirstLoad = true;
        loadMessages();
        setInterval(loadMessages, 5000);
    }
    </script>
</body>
</html>

