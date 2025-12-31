<?php
session_start();
require_once('../admin/inc/db_config.php');
require_once('../admin/inc/essentials.php');

header('Content-Type: application/json');

if(!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    echo json_encode(['status' => 'error', 'msg' => 'Chưa đăng nhập']);
    exit;
}

$user_id = $_SESSION['uId'] ?? 0;
if($user_id <= 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid user']);
    exit;
}

$action = $_POST['action'] ?? '';

if($action == 'get') {
    $type = $_POST['type'] ?? '';
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    $owner_id = null;
    if($type == 'owner' && $id > 0) {
        $owner_id = $id;
    } elseif($type == 'admin') {
        // Admin luôn có owner_id = NULL
        $owner_id = null;
    }
    
    // Check if session_id column exists
    $check_session_col = @mysqli_query($con, "SHOW COLUMNS FROM `messages` LIKE 'session_id'");
    $has_session_col = $check_session_col && mysqli_num_rows($check_session_col) > 0;
    
    // For admin conversations (owner_id IS NULL), get ALL messages regardless of session_id
    // This ensures chat widget and messages page show the same conversation
    $sql = "SELECT * FROM messages WHERE user_id=? AND ";
    if($owner_id === null) {
        $sql .= "owner_id IS NULL";
        $params = [$user_id];
        $types = 'i';
    } else {
        $sql .= "owner_id=?";
        $params = [$user_id, $owner_id];
        $types = 'ii';
    }
    // Don't filter by session_id - show all messages for this user/owner combination
    // This makes chat widget and messages page share the same conversation
    $sql .= " ORDER BY created_at ASC";
    
    // Lấy thông tin user profile
    $user_info = select("SELECT name, profile FROM user_cred WHERE id=?", [$user_id], 'i');
    $user_data = mysqli_fetch_assoc($user_info);
    $user_profile = $user_data['profile'] ?? 'user.png';
    $user_profile_img = !empty($user_profile) && $user_profile != 'user.png' ? USERS_IMG_PATH . $user_profile : '';
    $user_initial = strtoupper(substr($user_data['name'] ?? 'U', 0, 1));
    
    // Lấy thông tin owner profile nếu type = owner
    $owner_profile_img = '';
    $owner_initial = '';
    if($type == 'owner' && $id > 0) {
        $owner_info = select("SELECT name, profile FROM hotel_owners WHERE id=? AND status=1", [$id], 'i');
        if($owner_info && mysqli_num_rows($owner_info) > 0) {
            $owner_data = mysqli_fetch_assoc($owner_info);
            $owner_profile = $owner_data['profile'] ?? 'user.png';
            $owner_profile_img = !empty($owner_profile) && $owner_profile != 'user.png' ? USERS_IMG_PATH . $owner_profile : '';
            $owner_initial = strtoupper(substr($owner_data['name'] ?? 'O', 0, 1));
        }
    }
    
    // Check if message_type and is_bot columns exist
    $check_message_type = @mysqli_query($con, "SHOW COLUMNS FROM `messages` LIKE 'message_type'");
    $has_message_type = $check_message_type && mysqli_num_rows($check_message_type) > 0;
    $check_is_bot = @mysqli_query($con, "SHOW COLUMNS FROM `messages` LIKE 'is_bot'");
    $has_is_bot = $check_is_bot && mysqli_num_rows($check_is_bot) > 0;
    
    $result = select($sql, $params, $types);
    $messages = [];
    if($result) {
        while($row = mysqli_fetch_assoc($result)) {
            $messages[] = [
                'id' => $row['id'],
                'sender_type' => $row['sender_type'],
                'message' => htmlspecialchars($row['message'] ?? ''),
                'time' => date('H:i', strtotime($row['created_at'] ?? 'now')),
                'user_profile' => $user_profile_img,
                'user_initial' => $user_initial,
                'owner_profile' => $owner_profile_img,
                'owner_initial' => $owner_initial,
                'message_type' => ($has_message_type && isset($row['message_type'])) ? $row['message_type'] : 'text',
                'is_bot' => ($has_is_bot && isset($row['is_bot'])) ? (int)$row['is_bot'] : 0
            ];
        }
    }
    
    // Mark as read
    if($owner_id === null) {
        update("UPDATE messages SET seen=1 WHERE user_id=? AND owner_id IS NULL AND sender_type IN ('admin','owner')", [$user_id], 'i');
    } else {
        update("UPDATE messages SET seen=1 WHERE user_id=? AND owner_id=? AND sender_type IN ('owner','admin')", [$user_id, $owner_id], 'ii');
    }
    
    echo json_encode(['status' => 'success', 'messages' => $messages]);
    
} elseif($action == 'send') {
    $type = $_POST['type'] ?? '';
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $message = trim($_POST['message'] ?? '');
    
    if(empty($message)) {
        echo json_encode(['status' => 'error', 'msg' => 'Vui lòng nhập tin nhắn']);
        exit;
    }
    
    $owner_id = null;
    if($type == 'owner' && $id > 0) {
        $owner_id = $id;
    } elseif($type == 'admin') {
        // Admin luôn có owner_id = NULL
        $owner_id = null;
    }
    
    $sql = "INSERT INTO messages (user_id, owner_id, sender_type, message) VALUES (?, ?, 'user', ?)";
    $params = [$user_id, $owner_id, $message];
    $types = 'iss';
    
    if(insert($sql, $params, $types)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Không thể gửi tin nhắn']);
    }
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid action']);
}
?>

