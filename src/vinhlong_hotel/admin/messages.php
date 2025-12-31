<?php
session_start();
require('inc/essentials.php');
require('inc/db_config.php');

if(!isset($_SESSION['adminLogin']) || $_SESSION['adminLogin'] != true) {
    echo "<script>window.location.href='index.php'</script>";
    exit;
}

// Kiểm tra bảng messages có tồn tại không
$check_table = @mysqli_query($con, "SHOW TABLES LIKE 'messages'");
$table_exists = $check_table && mysqli_num_rows($check_table) > 0;

// Lấy danh sách users đã nhắn tin
$conversations = [];
if($table_exists && isset($con)) {
    $sql = "SELECT DISTINCT m.user_id, uc.name, uc.email, uc.profile,
            (SELECT message FROM messages WHERE user_id=m.user_id AND owner_id IS NULL ORDER BY created_at DESC LIMIT 1) as last_msg,
            (SELECT created_at FROM messages WHERE user_id=m.user_id AND owner_id IS NULL ORDER BY created_at DESC LIMIT 1) as last_time,
            (SELECT COUNT(*) FROM messages WHERE user_id=m.user_id AND owner_id IS NULL AND sender_type='user' AND seen=0) as unread
            FROM messages m
            INNER JOIN user_cred uc ON m.user_id = uc.id
            WHERE m.owner_id IS NULL
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
    <title>Tin nhắn từ khách hàng - Admin</title>
    <?php require('inc/links.php'); ?>
    <style>
        #main-content {
            margin-left: 0;
            padding: 20px;
            background: #f8f9fa;
            min-height: calc(100vh - 80px);
        }
        
        .main-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .page-header h4 {
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .chat-container { 
            min-height: 500px;
            height: calc(100vh - 300px); 
            max-height: 700px;
            display: flex;
            flex-direction: column;
        }
        
        .conversations-list { 
            height: calc(100% - 70px); 
            overflow-y: auto;
            padding: 10px;
            background: #ffffff;
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
            background: #ffffff;
            position: relative;
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
        
        .message-item.admin { 
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
        
        .message-item.admin .message-avatar {
            background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
        }
        
        .message-item.user .message-avatar {
            background: #0f172a;
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
        
        .message-item.admin .message-bubble { 
            background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
            color: white;
            border-bottom-right-radius: 4px;
        }
        
        .message-item.user .message-bubble { 
            background: #ffffff;
            border-bottom-left-radius: 4px;
            border: 1px solid #e5e7eb;
            color: #0f172a;
        }
        
        .message-time { 
            font-size: 0.7rem; 
            color: rgba(255, 255, 255, 0.8);
            margin-top: 5px;
            opacity: 0.9;
        }
        
        .message-item.user .message-time {
            color: #0f172a;
        }
        
        .conversation-item { 
            padding: 15px; 
            border-bottom: 1px solid #e5e7eb; 
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 12px;
            margin-bottom: 8px;
            position: relative;
            overflow: hidden;
            background: #ffffff;
        }
        
        .conversation-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: #6c757d;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }
        
        .conversation-item:hover { 
            background: #f1f3f5;
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        .conversation-item:hover::before {
            transform: scaleY(1);
        }
        
        .conversation-item.active { 
            background: #e9ecef;
            border-left: 4px solid #6c757d;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .conversation-item.active::before {
            transform: scaleY(1);
        }
        
        .conversation-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #6c757d;
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
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .conversation-last-msg {
            font-size: 0.8rem;
            color: #0f172a;
            margin-top: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .unread-badge { 
            background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
            color: white; 
            border-radius: 50%; 
            width: 24px; 
            height: 24px; 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 0.75rem;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.4);
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
        
        /* Card header cho conversations list */
        .card:has(.conversations-list) .card-header {
            background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
            color: white;
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
            box-shadow: 0 0 0 0.2rem rgba(14, 165, 233, 0.25);
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
            margin-bottom: 20px;
        }
        
        .empty-state p {
            font-size: 1.1rem;
            margin: 0;
        }
        
        .chat-messages:empty::before {
            content: 'Đang tải tin nhắn...';
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #0f172a;
            padding: 40px 20px;
            font-style: italic;
            min-height: 300px;
        }
        
        .loading-message {
            text-align: center;
            padding: 40px 20px;
            color: #0f172a;
            font-style: italic;
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
    <?php require('inc/header.php'); ?>
    
    <div class="container-fluid" id="main-content">
        <div class="row">
            <div class="col-lg-10 ms-auto p-4">
                <div class="main-container">
                    <div class="page-header">
                        <h4 class="mb-0"><i class="bi bi-chat-dots me-2"></i>Tin nhắn từ khách hàng</h4>
                    </div>
                    
                    <div class="row g-3 mt-3">
                <div class="col-md-4">
                    <div class="card" style="background: #ffffff;">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-people me-2"></i>Cuộc trò chuyện</h6>
                        </div>
                        <div class="conversations-list" style="background: #ffffff;">
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
                            <script>
                            // Log initial state
                            (function() {
                                setTimeout(function() {
                                    const el = document.getElementById('chatMessages');
                                    if(el) {
                                        fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/messages.php:527','message':'Initial DOM state','data':{elExists:!!el,elInnerHTML:el.innerHTML.substring(0,300),hasNotFound:el.innerHTML.includes('Not Found')},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'F'})}).catch(()=>{});
                                    }
                                }, 1000);
                            })();
                            </script>
                            <div class="card-footer">
                                <form onsubmit="sendMessage(event)">
                                    <div class="input-group">
                                        <input type="text" id="messageInput" class="form-control" placeholder="Nhập tin nhắn..." required>
                                        <button type="submit" class="btn btn-success"><i class="bi bi-send me-1"></i> Gửi</button>
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

    <?php require('inc/scripts.php'); ?>
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
        
        // #region agent log
        const basePath = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
        const fetchUrl = basePath + '/ajax/messages.php';
        const fullUrl = new URL(fetchUrl, window.location.origin).href;
        fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/messages.php:569',message:'Fetch URL check',data:{relativeUrl:fetchUrl,absoluteUrl:fullUrl,basePath:basePath,currentPath:window.location.pathname,userId:userId},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
        // #endregion
        
        fetch(fetchUrl, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get&user_id=' + userId
        })
        .then(r => {
            // #region agent log
            fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/messages.php:578',message:'Response received',data:{status:r.status,statusText:r.statusText,ok:r.ok,contentType:r.headers.get('content-type'),url:r.url},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'B'})}).catch(()=>{});
            // #endregion
            
            if(!r.ok) {
                // #region agent log
                fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/messages.php:585',message:'Response not OK',data:{status:r.status,statusText:r.statusText,url:r.url},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'B'})}).catch(()=>{});
                // #endregion
                // Don't try to parse HTML error pages - just throw immediately
                throw new Error('HTTP ' + r.status + ': ' + r.statusText);
            }
            return r.text().then(text => {
                // #region agent log
                fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/messages.php:590',message:'Response text received',data:{textLength:text.length,textPreview:text.substring(0,200),isHtml:text.includes('<html'),isJson:text.trim().startsWith('{'),contentType:r.headers.get('content-type')},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'C'})}).catch(()=>{});
                // #endregion
                try {
                    const parsed = JSON.parse(text);
                    // #region agent log
                    fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/messages.php:593',message:'JSON parsed successfully',data:{hasStatus:!!parsed.status,status:parsed.status,messageCount:parsed.messages?parsed.messages.length:0},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'C'})}).catch(()=>{});
                    // #endregion
                    return parsed;
                } catch(e) {
                    // #region agent log
                    fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/messages.php:595',message:'JSON parse failed',data:{error:e.message,textPreview:text.substring(0,200),isHtml:text.includes('<html')},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'D'})}).catch(()=>{});
                    // #endregion
                    throw new Error('Invalid JSON response: ' + e.message);
                }
            });
        })
        .catch(err => {
            // #region agent log
            fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/messages.php:600',message:'Fetch catch error',data:{error:err.message,errorStack:err.stack},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'E'})}).catch(()=>{});
            // #endregion
            console.error('Fetch error:', err);
            return Promise.reject(err);
        })
        .then(data => {
            // #region agent log
            fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/messages.php:621',message:'Processing data for rendering',data:{hasData:!!data,hasStatus:!!(data&&data.status),status:data?.status,messageCount:data?.messages?.length,isArray:Array.isArray(data?.messages),elExists:!!el,elChildren:el?.children?.length},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'F'})}).catch(()=>{});
            // #endregion
            
            if(data && data.status == 'success' && Array.isArray(data.messages)) {
                // Lần đầu load, hiển thị tất cả
                if(isFirstLoad) {
                    // #region agent log
                    fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/messages.php:626',message:'First load - clearing element',data:{elInnerHTMLBefore:el.innerHTML.substring(0,100)},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'F'})}).catch(()=>{});
                    // #endregion
                    el.innerHTML = '';
                    displayedMsgIds.clear();
                    isFirstLoad = false;
                }
                
                if(data.messages.length === 0) {
                    if(el.children.length === 0 || el.querySelector('.loading-message')) {
                        el.innerHTML = '<div class="empty-state"><i class="bi bi-chat-left-text"></i><p>Chưa có tin nhắn nào. Hãy bắt đầu cuộc trò chuyện!</p></div>';
                    }
                } else {
                    let addedCount = 0;
                    data.messages.forEach(msg => {
                        const msgId = msg.id || (msg.time + msg.message);
                        if(!displayedMsgIds.has(msgId)) {
                            displayedMsgIds.add(msgId);
                            const div = document.createElement('div');
                            div.className = 'message-item ' + (msg.sender_type == 'admin' ? 'admin' : 'user');
                            div.setAttribute('data-msg-id', msgId);
                            
                            const avatar = document.createElement('div');
                            avatar.className = 'message-avatar';
                            if(msg.sender_type == 'admin') {
                                avatar.textContent = 'A';
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
                            
                            bubble.innerHTML = '<div>' + processedMessage + '</div><div class="message-time">' + (msg.time || '') + '</div>';
                            
                            div.appendChild(avatar);
                            div.appendChild(bubble);
                            el.appendChild(div);
                            addedCount++;
                        }
                    });
                    // #region agent log
                    fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/messages.php:650',message:'Messages rendered',data:{addedCount:addedCount,totalMessages:data.messages.length,elChildrenAfter:el.children.length},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'F'})}).catch(()=>{});
                    // #endregion
                }
                if(wasAtBottom || isFirstLoad) {
                    setTimeout(() => {
                        el.scrollTop = el.scrollHeight;
                    }, 100);
                }
            } else {
                // #region agent log
                fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/messages.php:658',message:'Invalid data format',data:{data:data},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'F'})}).catch(()=>{});
                // #endregion
                console.error('Invalid data format:', data);
                if(isFirstLoad) {
                    el.innerHTML = '<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Không thể tải tin nhắn</p></div>';
                }
            }
            isLoading = false;
        })
        .catch(e => {
            // #region agent log
            fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/messages.php:640',message:'Final catch error handler',data:{error:e.message,isFirstLoad:isFirstLoad},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'E'})}).catch(()=>{});
            // #endregion
            console.error('Error loading messages:', e);
            if(isFirstLoad) {
                el.innerHTML = '<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Lỗi kết nối. Vui lòng thử lại.</p></div>';
            }
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
        
        // Mark admin as online when sending first message (if not already marked)
        markAdminOnline();
        
        const basePath = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
        const sendUrl = basePath + '/ajax/messages.php';
        fetch(sendUrl, {
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
    
    // Mark admin as online when viewing a conversation
    function markAdminOnline() {
        const el = document.getElementById('chatMessages');
        if(!el) return;
        const userId = el.dataset.userId;
        if(!userId || userId <= 0) return;
        
        // Get session_id from user's messages by calling get_history with user's perspective
        // We need to get the session_id that the user is using
        // Since we're admin, we'll use a consistent session_id pattern: 'chat_user_' + userId
        // But first, try to get the actual session_id from the user's messages
        fetch('../ajax/messages.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get&user_id=' + userId
        })
        .then(r => r.json())
        .then(data => {
            // Try to get session_id from live_chat.php using user_id
            // This will return the session_id that the user is actually using
            fetch('../ajax/live_chat.php?action=get_history&session_id=')
                .then(r => r.json())
                .then(chatData => {
                    let sessionId = '';
                    if(chatData.status === 'success' && chatData.session_id) {
                        sessionId = chatData.session_id;
                    } else {
                        // Create a consistent session_id for this user
                        // This will match what getOrCreateSession creates
                        sessionId = 'chat_user_' + userId;
                    }
                    
                    // Mark admin as online for this session
                    fetch('../ajax/live_chat.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: 'action=admin_online&user_id=' + userId + '&session_id=' + encodeURIComponent(sessionId)
                    }).catch(err => console.error('Error marking admin online:', err));
                })
                .catch(err => {
                    // Fallback: use consistent session_id pattern
                    const sessionId = 'chat_user_' + userId;
                    fetch('../ajax/live_chat.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: 'action=admin_online&user_id=' + userId + '&session_id=' + encodeURIComponent(sessionId)
                    }).catch(e => console.error('Error marking admin online:', e));
                });
        })
        .catch(err => {
            // Fallback: use consistent session_id pattern
            const sessionId = 'chat_user_' + userId;
            fetch('../ajax/live_chat.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=admin_online&user_id=' + userId + '&session_id=' + encodeURIComponent(sessionId)
            }).catch(e => console.error('Error marking admin online:', e));
        });
    }
    
    // Mark admin as offline when leaving
    function markAdminOffline() {
        const el = document.getElementById('chatMessages');
        if(!el) return;
        const userId = el.dataset.userId;
        if(!userId || userId <= 0) return;
        
        // Get session_id for this user
        const sessionId = 'chat_user_' + userId;
        fetch('../ajax/live_chat.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=admin_offline&session_id=' + encodeURIComponent(sessionId)
        }).catch(err => console.error('Error marking admin offline:', err));
    }
    
    // Ensure DOM is ready and element exists
    function initMessages() {
        const el = document.getElementById('chatMessages');
        if(el) {
            // Clear any "Not Found" content immediately
            if(el.innerHTML.includes('Not Found') || el.innerHTML.includes('404')) {
                el.innerHTML = '<div class="loading-message">Đang tải tin nhắn...</div>';
            }
            isFirstLoad = true;
            loadMessages();
            
            // Mark admin as online when page loads
            setTimeout(markAdminOnline, 1000);
            
            // Keep admin online status updated (every 2 minutes)
            const keepAliveInterval = setInterval(() => {
                markAdminOnline();
            }, 120000);
            
            const intervalId = setInterval(loadMessages, 5000);
            
            // Cleanup on page unload
            window.addEventListener('beforeunload', () => {
                clearInterval(intervalId);
                clearInterval(keepAliveInterval);
                markAdminOffline();
            });
            
            // Also mark offline when navigating away
            window.addEventListener('pagehide', () => {
                markAdminOffline();
            });
        } else {
            // #region agent log
            fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/messages.php:743','message':'chatMessages element not found on init','data':{},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'F'})}).catch(()=>{});
            // #endregion
        }
    }
    
    if(document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMessages);
    } else {
        initMessages();
    }
    </script>
</body>
</html>

