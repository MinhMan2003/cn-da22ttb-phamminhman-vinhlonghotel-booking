<?php
// #region agent log
$logFile = __DIR__ . '/../../.cursor/debug.log';
file_put_contents($logFile, json_encode(['location'=>'admin/ajax/messages.php:2','message'=>'File accessed','data'=>['method'=>$_SERVER['REQUEST_METHOD']??'','uri'=>$_SERVER['REQUEST_URI']??''],'timestamp'=>time()*1000,'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n", FILE_APPEND);
// #endregion

session_start();
// #region agent log
file_put_contents($logFile, json_encode(['location'=>'admin/ajax/messages.php:6','message'=>'Session started','data'=>['hasSession'=>isset($_SESSION),'hasAdminLogin'=>isset($_SESSION['adminLogin']),'adminLoginValue'=>$_SESSION['adminLogin']??null],'timestamp'=>time()*1000,'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n", FILE_APPEND);
// #endregion

require(__DIR__ . '/../inc/db_config.php');
require(__DIR__ . '/../inc/essentials.php');

header('Content-Type: application/json');

if(!isset($_SESSION['adminLogin']) || $_SESSION['adminLogin'] != true) {
    // #region agent log
    file_put_contents($logFile, json_encode(['location'=>'admin/ajax/messages.php:11','message'=>'Auth failed','data'=>['hasSession'=>isset($_SESSION),'hasAdminLogin'=>isset($_SESSION['adminLogin'])],'timestamp'=>time()*1000,'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n", FILE_APPEND);
    // #endregion
    echo json_encode(['status' => 'error', 'msg' => 'Chưa đăng nhập']);
    exit;
}

$action = $_POST['action'] ?? '';
// #region agent log
file_put_contents($logFile, json_encode(['location'=>'admin/ajax/messages.php:16','message'=>'Action received','data'=>['action'=>$action,'postData'=>array_keys($_POST)],'timestamp'=>time()*1000,'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n", FILE_APPEND);
// #endregion

if($action == 'get') {
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    // #region agent log
    file_put_contents($logFile, json_encode(['location'=>'admin/ajax/messages.php:18','message'=>'Get action - user_id check','data'=>['userId'=>$user_id,'postUserId'=>$_POST['user_id']??null],'timestamp'=>time()*1000,'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n", FILE_APPEND);
    // #endregion
    if($user_id <= 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid user']);
        exit;
    }
    
    // Kiểm tra bảng messages
    $check_table = @mysqli_query($con, "SHOW TABLES LIKE 'messages'");
    // #region agent log
    file_put_contents($logFile, json_encode(['location'=>'admin/ajax/messages.php:26','message'=>'Table check','data'=>['tableExists'=>($check_table && mysqli_num_rows($check_table) > 0),'hasCon'=>isset($con)],'timestamp'=>time()*1000,'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n", FILE_APPEND);
    // #endregion
    if(!$check_table || mysqli_num_rows($check_table) == 0) {
        echo json_encode(['status' => 'success', 'messages' => []]);
        exit;
    }
    
    // Check if message_type and is_bot columns exist
    $check_message_type = @mysqli_query($con, "SHOW COLUMNS FROM `messages` LIKE 'message_type'");
    $has_message_type = $check_message_type && mysqli_num_rows($check_message_type) > 0;
    $check_is_bot = @mysqli_query($con, "SHOW COLUMNS FROM `messages` LIKE 'is_bot'");
    $has_is_bot = $check_is_bot && mysqli_num_rows($check_is_bot) > 0;
    
    $sql = "SELECT m.*, uc.name as user_name, uc.profile as user_profile 
            FROM messages m 
            LEFT JOIN user_cred uc ON m.user_id = uc.id 
            WHERE m.user_id=? AND m.owner_id IS NULL 
            ORDER BY m.created_at ASC";
    $result = @select($sql, [$user_id], 'i');
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
                'time' => date('H:i', strtotime($row['created_at'] ?? 'now')),
                'user_initial' => $user_initial,
                'user_profile' => !empty($row['user_profile']) ? USERS_IMG_PATH . $row['user_profile'] : '',
                'message_type' => ($has_message_type && isset($row['message_type'])) ? $row['message_type'] : 'text',
                'is_bot' => ($has_is_bot && isset($row['is_bot'])) ? (int)$row['is_bot'] : 0
            ];
        }
    }
    // #region agent log
    file_put_contents($logFile, json_encode(['location'=>'admin/ajax/messages.php:42','message'=>'Query executed','data'=>['messageCount'=>count($messages),'hasResult'=>!!$result],'timestamp'=>time()*1000,'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n", FILE_APPEND);
    // #endregion
    
    // Mark as read
    @update("UPDATE messages SET seen=1 WHERE user_id=? AND owner_id IS NULL AND sender_type='user'", [$user_id], 'i');
    
    $response = ['status' => 'success', 'messages' => $messages];
    // #region agent log
    file_put_contents($logFile, json_encode(['location'=>'admin/ajax/messages.php:48','message'=>'Sending response','data'=>['responseSize'=>strlen(json_encode($response))],'timestamp'=>time()*1000,'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n", FILE_APPEND);
    // #endregion
    echo json_encode($response);
    
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
    
    // Check if session_id column exists
    $check_session_col = @mysqli_query($con, "SHOW COLUMNS FROM `messages` LIKE 'session_id'");
    $has_session_col = $check_session_col && mysqli_num_rows($check_session_col) > 0;
    
    // Get or create session_id for this user
    $session_id = 'chat_user_' . $user_id;
    $check_session = @mysqli_query($con, "SELECT session_id FROM `messages` WHERE `user_id` = $user_id AND `owner_id` IS NULL AND `session_id` IS NOT NULL AND `session_id` != '' ORDER BY `created_at` DESC LIMIT 1");
    if($check_session && mysqli_num_rows($check_session) > 0) {
        $session_row = mysqli_fetch_assoc($check_session);
        $session_id = $session_row['session_id'];
    }
    
    // Insert admin message (no system message - notification will show only for new messages)
    $insert_result = false;
    if($has_session_col && !empty($session_id)) {
        // Insert with session_id and is_bot = 0 (admin message, not bot)
        $sql = "INSERT INTO messages (user_id, owner_id, sender_type, message, session_id, message_type, is_bot) VALUES (?, NULL, 'admin', ?, ?, 'text', 0)";
        $insert_result = @insert($sql, [$user_id, $message, $session_id], 'iss');
    }
    
    // Fallback if session_id column doesn't exist
    if(!$insert_result) {
        $sql = "INSERT INTO messages (user_id, owner_id, sender_type, message) VALUES (?, NULL, 'admin', ?)";
        $insert_result = @insert($sql, [$user_id, $message], 'is');
    }
    
    if($insert_result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Không thể gửi']);
    }
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid action']);
}
?>

