<?php
session_start();
require(__DIR__ . '/../../admin/inc/db_config.php');
require(__DIR__ . '/../../admin/inc/essentials.php');

header('Content-Type: application/json');

if(!isset($_SESSION['ownerLogin']) || $_SESSION['ownerLogin'] != true) {
    echo json_encode(['status' => 'error', 'msg' => 'Chưa đăng nhập']);
    exit;
}

$owner_id = $_SESSION['ownerId'] ?? 0;
if($owner_id <= 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid owner']);
    exit;
}

$action = $_POST['action'] ?? '';

if($action == 'get') {
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    if($user_id <= 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid user']);
        exit;
    }
    
    // Kiểm tra bảng messages
    $check_table = @mysqli_query($con, "SHOW TABLES LIKE 'messages'");
    if(!$check_table || mysqli_num_rows($check_table) == 0) {
        echo json_encode(['status' => 'success', 'messages' => []]);
        exit;
    }
    
    // Lấy thông tin owner profile
    $owner_info = select("SELECT name, profile FROM hotel_owners WHERE id=?", [$owner_id], 'i');
    $owner_data = mysqli_fetch_assoc($owner_info);
    $owner_name = $owner_data['name'] ?? 'Chủ khách sạn';
    $owner_profile = $owner_data['profile'] ?? 'user.png';
    $owner_profile_img = !empty($owner_profile) && $owner_profile != 'user.png' ? USERS_IMG_PATH . $owner_profile : '';
    $owner_initial = strtoupper(substr($owner_name, 0, 1));
    
    $sql = "SELECT m.*, uc.name as user_name, uc.profile as user_profile 
            FROM messages m 
            LEFT JOIN user_cred uc ON m.user_id = uc.id 
            WHERE m.user_id=? AND m.owner_id=? 
            ORDER BY m.created_at ASC";
    $result = @select($sql, [$user_id, $owner_id], 'ii');
    $messages = [];
    if($result) {
        while($row = mysqli_fetch_assoc($result)) {
            $user_initial = '';
            if($row['sender_type'] == 'user' && !empty($row['user_name'])) {
                $user_initial = strtoupper(substr($row['user_name'], 0, 1));
            }
            $messages[] = [
                'id' => $row['id'],
                'sender_type' => $row['sender_type'],
                'message' => htmlspecialchars($row['message'] ?? ''),
                'time' => date('d/m/Y H:i', strtotime($row['created_at'] ?? 'now')),
                'user_initial' => $user_initial,
                'user_profile' => !empty($row['user_profile']) ? USERS_IMG_PATH . $row['user_profile'] : '',
                'owner_profile' => $owner_profile_img,
                'owner_initial' => $owner_initial
            ];
        }
    }
    
    // Mark as read
    @update("UPDATE messages SET seen=1 WHERE user_id=? AND owner_id=? AND sender_type='user'", [$user_id, $owner_id], 'ii');
    
    echo json_encode(['status' => 'success', 'messages' => $messages]);
    
} elseif($action == 'send') {
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $message = trim($_POST['message'] ?? '');
    
    if($user_id <= 0 || empty($message)) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid data']);
        exit;
    }
    
    // Kiểm tra bảng messages
    $check_table = @mysqli_query($con, "SHOW TABLES LIKE 'messages'");
    if(!$check_table || mysqli_num_rows($check_table) == 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Bảng messages chưa tồn tại']);
        exit;
    }
    
    $sql = "INSERT INTO messages (user_id, owner_id, sender_type, message) VALUES (?, ?, 'owner', ?)";
    if(@insert($sql, [$user_id, $owner_id, $message], 'iis')) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Không thể gửi']);
    }
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid action']);
}
?>

