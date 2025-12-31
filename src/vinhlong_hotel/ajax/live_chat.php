<?php
// Disable error display to prevent HTML output
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

// Set JSON header early
header('Content-Type: application/json; charset=utf-8');

// Prevent any output before JSON
ob_start();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ========== SEND MESSAGE ==========
if($action == 'send') {
  ob_end_clean();
  
  // Check login
  if(!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập']);
    exit;
  }
  
  $user_id = (int)$_SESSION['uId'];
  $message = trim($_POST['message'] ?? '');
  $session_id = $_POST['session_id'] ?? '';
  
  if(empty($message)) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'Tin nhắn không được để trống']);
    exit;
  }
  
  // Ensure tables exist
  ensureChatTables();
  
  // Get or create session (this will find existing session_id from user's messages)
  $session_id = getOrCreateSession($user_id, $session_id);
  
  // Save user message
  // Check if session_id column exists
  $check_session_col = @mysqli_query($con, "SHOW COLUMNS FROM `messages` LIKE 'session_id'");
  $has_session_col = $check_session_col && mysqli_num_rows($check_session_col) > 0;
  
  // Try insert with session_id first, fallback to without if fails
  $insert_result = false;
  if($has_session_col && !empty($session_id)) {
    // Insert with session_id
    $insert_msg = "INSERT INTO `messages` (`user_id`, `owner_id`, `sender_type`, `message`, `session_id`, `message_type`, `is_bot`) 
                   VALUES (?, NULL, 'user', ?, ?, 'text', 0)";
    $insert_result = insert($insert_msg, [$user_id, $message, $session_id], 'iss');
    
    // If insert fails, try without session_id columns
    if(!$insert_result) {
      $error = mysqli_error($con);
      error_log("Insert with session_id failed: " . $error . " - Trying fallback...");
      $insert_msg = "INSERT INTO `messages` (`user_id`, `owner_id`, `sender_type`, `message`) 
                     VALUES (?, NULL, 'user', ?)";
      $insert_result = insert($insert_msg, [$user_id, $message], 'is');
    }
  } else {
    // Insert without session_id (fallback for old database structure)
    $insert_msg = "INSERT INTO `messages` (`user_id`, `owner_id`, `sender_type`, `message`) 
                   VALUES (?, NULL, 'user', ?)";
    $insert_result = insert($insert_msg, [$user_id, $message], 'is');
  }
  
  if(!$insert_result) {
    $error = mysqli_error($con);
    error_log("Insert message error: " . $error);
    ob_end_clean();
    // Don't expose database error to user, just log it
    echo json_encode(['status' => 'error', 'message' => 'Không thể lưu tin nhắn. Vui lòng thử lại.']);
    exit;
  }
  $user_message_id = mysqli_insert_id($con);
  
  // Get user info for personalized responses
  $user_info_query = select("SELECT name, email FROM user_cred WHERE id=?", [$user_id], 'i');
  $user_info = null;
  if($user_info_query && mysqli_num_rows($user_info_query) > 0) {
    $user_info = mysqli_fetch_assoc($user_info_query);
  }
  
  // Check if admin has sent a message recently - if yes, don't respond with chatbot
  // Logic: Check if there's a message from admin (sender_type='admin', owner_id IS NULL) 
  // in the last 5 minutes for this user
  $admin_online = false;
  if(!empty($session_id)) {
    // Check if admin has sent a message in the last 5 minutes
    $check_admin_msg = "SELECT COUNT(*) as cnt FROM `messages` 
                        WHERE `user_id` = ? 
                        AND `owner_id` IS NULL 
                        AND `sender_type` = 'admin' 
                        AND `is_bot` = 0
                        AND `created_at` > (NOW() - INTERVAL 5 MINUTE)";
    $result = select($check_admin_msg, [$user_id], 'i');
    if($result && mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
      $admin_online = (int)$row['cnt'] > 0;
    }
  }
  
  // Process with chatbot - ALWAYS respond (unless admin has sent a message recently)
  $bot_response = null;
  if(!$admin_online) {
    // Always call processChatbot - it should always return a response
    $bot_response = processChatbot($message, $user_id, $user_info);
    
    // CRITICAL: Ensure we always have a response
    // processChatbot should never return null, but double-check
    if(empty($bot_response) || trim($bot_response) === '') {
      $bot_response = "Xin chào! 👋 Tôi là trợ lý ảo của Vĩnh Long Hotel. Tôi có thể giúp bạn về:\n- Đặt phòng\n- Giá cả\n- Tiện ích\n- Địa điểm du lịch\n- Thanh toán\n\nBạn muốn biết thông tin gì?";
    }
  }
  
  // Save bot response if exists
  $bot_message_id = null;
  if($bot_response) {
    // Keep the original response with \n for proper line breaks
    // Store as-is, frontend will handle \n to <br> conversion
    $bot_response_clean = $bot_response;
    
    // Try insert with session_id first, fallback to without if fails
    $insert_bot_result = false;
    if($has_session_col && !empty($session_id)) {
      // Insert with session_id
      $insert_bot = "INSERT INTO `messages` (`user_id`, `owner_id`, `sender_type`, `message`, `session_id`, `message_type`, `is_bot`) 
                     VALUES (?, NULL, 'admin', ?, ?, 'bot', 1)";
      $insert_bot_result = insert($insert_bot, [$user_id, $bot_response_clean, $session_id], 'iss');
      
      // If insert fails, try without session_id columns
      if(!$insert_bot_result) {
        $error = mysqli_error($con);
        error_log("Insert bot with session_id failed: " . $error . " - Trying fallback...");
        $insert_bot = "INSERT INTO `messages` (`user_id`, `owner_id`, `sender_type`, `message`) 
                       VALUES (?, NULL, 'admin', ?)";
        $insert_bot_result = insert($insert_bot, [$user_id, $bot_response_clean], 'is');
      }
    } else {
      // Insert without session_id (fallback)
      $insert_bot = "INSERT INTO `messages` (`user_id`, `owner_id`, `sender_type`, `message`) 
                     VALUES (?, NULL, 'admin', ?)";
      $insert_bot_result = insert($insert_bot, [$user_id, $bot_response_clean], 'is');
    }
    
    if($insert_bot_result) {
      $bot_message_id = mysqli_insert_id($con);
    } else {
      $error = mysqli_error($con);
      error_log("Insert bot message error: " . $error);
    }
  }
  
  // Check if needs human (admin) response
  $needs_human = shouldEscalateToHuman($message);
  if($needs_human) {
    // Mark session as waiting for admin
    updateSessionStatus($session_id, 'waiting');
  }
  
  // CRITICAL: Always ensure bot_response is set if admin is not online
  if(!$admin_online && (empty($bot_response) || trim($bot_response) === '')) {
    $bot_response = "Xin chào! 👋 Tôi là trợ lý ảo của Vĩnh Long Hotel. Tôi có thể giúp bạn về:\n- Đặt phòng\n- Giá cả\n- Tiện ích\n- Địa điểm du lịch\n- Thanh toán\n\nBạn muốn biết thông tin gì?";
  }
  
  ob_end_clean();
  
  // Debug: Log response data
  $response_data = [
    'status' => 'success',
    'message_id' => $user_message_id,
    'bot_message_id' => $bot_message_id ? $bot_message_id : null,
    'bot_response' => $bot_response ? $bot_response : null,
    'needs_human' => $needs_human,
    'admin_online' => $admin_online,
    'session_id' => $session_id,
    'debug_message' => $message,
    'debug_user_id' => $user_id
  ];
  
  // Log for debugging
  error_log("Chat response - Message: '$message', Bot response: " . ($bot_response ? 'YES (' . strlen($bot_response) . ' chars)' : 'NO') . ", Admin online: " . ($admin_online ? 'YES' : 'NO'));
  
  echo json_encode($response_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

// ========== GET HISTORY ==========
if($action == 'get_history') {
  ob_end_clean();
  
  if(!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập']);
    exit;
  }
  
  $user_id = (int)$_SESSION['uId'];
  $session_id = $_GET['session_id'] ?? '';
  
  ensureChatTables();
  
  // Get or create session
  $session_id = getOrCreateSession($user_id, $session_id);
  
  // Get user profile info
  $user_info = select("SELECT name, profile FROM user_cred WHERE id=?", [$user_id], 'i');
  $user_data = mysqli_fetch_assoc($user_info);
  $user_profile = $user_data['profile'] ?? 'user.png';
  $user_profile_img = !empty($user_profile) && $user_profile != 'user.png' ? USERS_IMG_PATH . $user_profile : '';
  $user_initial = strtoupper(substr($user_data['name'] ?? 'U', 0, 1));
  $user_name = $user_data['name'] ?? 'Người dùng';
  
  $messages = [];
  // Get messages - for admin conversations (owner_id IS NULL), get ALL messages regardless of session_id
  // This ensures chat widget and messages page show the same conversation
  // But if session_id is provided and there are messages with that session_id, prefer those
  // Otherwise, get all messages for this user with owner_id IS NULL
  if($session_id) {
    // First, try to get messages with this session_id
    $query = "SELECT id, sender_type, message, created_at, message_type, is_bot 
              FROM `messages` 
              WHERE `user_id` = ? AND `owner_id` IS NULL AND `session_id` = ? 
              ORDER BY `created_at` ASC 
              LIMIT 50";
    $result = select($query, [$user_id, $session_id], 'is');
    
    // If no messages with this session_id, get all messages for this user (to sync with messages.php)
    if(!$result || mysqli_num_rows($result) == 0) {
      $query = "SELECT id, sender_type, message, created_at, message_type, is_bot 
                FROM `messages` 
                WHERE `user_id` = ? AND `owner_id` IS NULL 
                ORDER BY `created_at` ASC 
                LIMIT 50";
      $result = select($query, [$user_id], 'i');
      
      // Update session_id for all these messages to current session_id (for consistency)
      if($result && mysqli_num_rows($result) > 0) {
        @mysqli_query($con, "UPDATE `messages` SET `session_id` = '" . mysqli_real_escape_string($con, $session_id) . "' WHERE `user_id` = " . (int)$user_id . " AND `owner_id` IS NULL AND (`session_id` IS NULL OR `session_id` = '')");
      }
    }
    
    if($result && mysqli_num_rows($result) > 0) {
      while($row = mysqli_fetch_assoc($result)) {
        $messages[] = [
          'id' => (int)$row['id'],
          'sender_type' => $row['sender_type'],
          'message' => $row['message'],
          'created_at' => date('H:i', strtotime($row['created_at'])),
          'message_type' => $row['message_type'] ?? 'text',
          'is_bot' => (int)$row['is_bot'],
          'user_profile' => $user_profile_img,
          'user_initial' => $user_initial,
          'user_name' => $user_name
        ];
      }
    }
  }
  
  ob_end_clean();
  echo json_encode([
    'status' => 'success',
    'messages' => $messages,
    'session_id' => $session_id,
    'user_profile' => $user_profile_img,
    'user_initial' => $user_initial,
    'user_name' => $user_name
  ]);
  exit;
}

// ========== CHECK NEW MESSAGES ==========
if($action == 'check_new') {
  ob_end_clean();
  
  if(!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    echo json_encode(['status' => 'error']);
    exit;
  }
  
  $user_id = (int)$_SESSION['uId'];
  $session_id = $_GET['session_id'] ?? '';
  $last_id = (int)($_GET['last_id'] ?? 0);
  
  ensureChatTables();
  
  // Get user profile info
  $user_info = select("SELECT name, profile FROM user_cred WHERE id=?", [$user_id], 'i');
  $user_data = mysqli_fetch_assoc($user_info);
  $user_profile = $user_data['profile'] ?? 'user.png';
  $user_profile_img = !empty($user_profile) && $user_profile != 'user.png' ? USERS_IMG_PATH . $user_profile : '';
  $user_initial = strtoupper(substr($user_data['name'] ?? 'U', 0, 1));
  $user_name = $user_data['name'] ?? 'Người dùng';
  
  $messages = [];
  if($session_id) {
    // Check for new messages - for admin conversations, check all messages (not just session_id)
    // This ensures messages from messages.php appear in chat widget
    // Get all new messages for this user (regardless of session_id) to sync with messages.php
    $query = "SELECT id, sender_type, message, created_at, message_type, is_bot 
              FROM `messages` 
              WHERE `user_id` = ? AND `owner_id` IS NULL AND `id` > ? 
              ORDER BY `created_at` ASC";
    $result = select($query, [$user_id, $last_id], 'ii');
    
    if($result && mysqli_num_rows($result) > 0) {
      $new_ids = [];
      $all_rows = [];
      
      while($row = mysqli_fetch_assoc($result)) {
        $all_rows[] = $row;
        $new_ids[] = (int)$row['id'];
      }
      
      // Update session_id for new messages to current session_id (for consistency)
      if(!empty($new_ids)) {
        $ids_str = implode(',', array_map('intval', $new_ids));
        @mysqli_query($con, "UPDATE `messages` SET `session_id` = '" . mysqli_real_escape_string($con, $session_id) . "' WHERE `id` IN ($ids_str)");
      }
      
      // Build messages array from stored rows
      foreach($all_rows as $row) {
        $messages[] = [
          'id' => (int)$row['id'],
          'sender_type' => $row['sender_type'],
          'message' => $row['message'],
          'created_at' => date('H:i', strtotime($row['created_at'])),
          'message_type' => $row['message_type'] ?? 'text',
          'is_bot' => (int)$row['is_bot'],
          'user_profile' => $user_profile_img,
          'user_initial' => $user_initial,
          'user_name' => $user_name
        ];
      }
    }
  }
  
  ob_end_clean();
  echo json_encode([
    'status' => 'success',
    'messages' => $messages
  ]);
  exit;
}

// ========== HELPER FUNCTIONS ==========

function ensureChatTables() {
  global $con;
  
  // Check if messages table exists
  $check_table = @mysqli_query($con, "SHOW TABLES LIKE 'messages'");
  if(!$check_table || mysqli_num_rows($check_table) == 0) {
    // Create messages table if it doesn't exist
    @mysqli_query($con, "CREATE TABLE IF NOT EXISTS `messages` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `owner_id` int(11) DEFAULT NULL,
      `sender_type` enum('user','owner','admin') NOT NULL,
      `message` text NOT NULL,
      `seen` tinyint(1) DEFAULT 0,
      `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `user_id` (`user_id`),
      KEY `owner_id` (`owner_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }
  
  // Check if messages table has new columns
  $check_columns = @mysqli_query($con, "SHOW COLUMNS FROM `messages` LIKE 'session_id'");
  if(!$check_columns || mysqli_num_rows($check_columns) == 0) {
    // Add columns one by one to avoid errors if some already exist
    @mysqli_query($con, "ALTER TABLE `messages` ADD COLUMN `session_id` VARCHAR(100) DEFAULT NULL");
    @mysqli_query($con, "ALTER TABLE `messages` ADD COLUMN `message_type` ENUM('text', 'booking', 'system', 'bot') DEFAULT 'text'");
    @mysqli_query($con, "ALTER TABLE `messages` ADD COLUMN `metadata` TEXT");
    @mysqli_query($con, "ALTER TABLE `messages` ADD COLUMN `is_bot` TINYINT(1) DEFAULT 0");
    
    // Add indexes if they don't exist
    @mysqli_query($con, "ALTER TABLE `messages` ADD KEY `idx_session_id` (`session_id`)");
    @mysqli_query($con, "ALTER TABLE `messages` ADD KEY `idx_message_type` (`message_type`)");
  }
  
  // Check if chat_sessions table exists
  $check_sessions = @mysqli_query($con, "SHOW TABLES LIKE 'chat_sessions'");
  if(!$check_sessions || mysqli_num_rows($check_sessions) == 0) {
    @mysqli_query($con, "CREATE TABLE IF NOT EXISTS `chat_sessions` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `user_id` INT(11) DEFAULT NULL,
      `session_id` VARCHAR(100) NOT NULL,
      `status` ENUM('active', 'closed', 'waiting') DEFAULT 'active',
      `assigned_to` INT(11) DEFAULT NULL,
      `last_message_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `unique_session` (`session_id`),
      KEY `idx_user_id` (`user_id`),
      KEY `idx_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }
  
  // Check if faqs table exists
  $check_faqs = @mysqli_query($con, "SHOW TABLES LIKE 'faqs'");
  if(!$check_faqs || mysqli_num_rows($check_faqs) == 0) {
    @mysqli_query($con, "CREATE TABLE IF NOT EXISTS `faqs` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `question` VARCHAR(500) NOT NULL,
      `answer` TEXT NOT NULL,
      `keywords` TEXT,
      `category` VARCHAR(100) DEFAULT 'general',
      `priority` INT(11) DEFAULT 0,
      `active` TINYINT(1) DEFAULT 1,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_category` (`category`),
      KEY `idx_active` (`active`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }
}

function getOrCreateSession($user_id, $session_id) {
  global $con;
  
  // If session_id is empty, try to find existing session from user's messages
  if(empty($session_id)) {
    // Try to get session_id from most recent message
    $msg_query = @select("SELECT session_id FROM `messages` WHERE `user_id` = ? AND `owner_id` IS NULL AND `session_id` IS NOT NULL AND `session_id` != '' ORDER BY `created_at` DESC LIMIT 1", [$user_id], 'i');
    if($msg_query && mysqli_num_rows($msg_query) > 0) {
      $msg_row = mysqli_fetch_assoc($msg_query);
      $session_id = $msg_row['session_id'];
    } else {
      // Create a consistent session_id for this user (for admin tracking)
      $session_id = 'chat_user_' . $user_id;
    }
  }
  
  // Check if session exists (by session_id or by user_id with pattern)
  $check = @select("SELECT id, session_id FROM `chat_sessions` WHERE `session_id` = ? OR (`user_id` = ? AND `session_id` LIKE ?)", [$session_id, $user_id, 'chat_user_' . $user_id . '%'], 'sis');
  if(!$check || mysqli_num_rows($check) == 0) {
    // Create new session
    @insert("INSERT INTO `chat_sessions` (`user_id`, `session_id`, `status`) VALUES (?, ?, 'active')", 
           [$user_id, $session_id], 'is');
  } else {
    // Update last message time and ensure session_id is consistent
    $session_row = mysqli_fetch_assoc($check);
    $existing_session_id = $session_row['session_id'];
    if($existing_session_id != $session_id) {
      // Update session_id to match (for consistency)
      @mysqli_query($con, "UPDATE `chat_sessions` SET `session_id` = '" . mysqli_real_escape_string($con, $session_id) . "', `last_message_at` = NOW() WHERE `id` = " . (int)$session_row['id']);
    } else {
      @mysqli_query($con, "UPDATE `chat_sessions` SET `last_message_at` = NOW() WHERE `session_id` = '" . mysqli_real_escape_string($con, $session_id) . "'");
    }
  }
  
  return $session_id;
}

function processChatbot($message, $user_id, $user_info = null) {
  global $con;
  
  $message_lower = mb_strtolower($message, 'UTF-8');
  $message_lower = trim($message_lower);
  
  // Handle common greetings first
  if(strpos($message_lower, 'xin chào') !== false || 
     strpos($message_lower, 'chào') !== false || 
     strpos($message_lower, 'hello') !== false || 
     strpos($message_lower, 'hi') !== false ||
     strpos($message_lower, 'hey') !== false) {
    return "Xin chào! 👋 Tôi là trợ lý ảo của Vĩnh Long Hotel. Tôi có thể giúp bạn về:\n- Đặt phòng\n- Giá cả\n- Tiện ích\n- Điểm du lịch\n- Thanh toán\n\nBạn muốn biết thông tin gì? Hoặc bạn có thể nói 'đặt phòng' để tôi hỗ trợ trực tiếp!";
  }
  
  // Special handling for "Bạn biết tôi là ai không?" - personalized response
  if(strpos($message_lower, 'bạn biết tôi') !== false || 
     strpos($message_lower, 'bạn biết tôi là ai') !== false ||
     strpos($message_lower, 'tôi là ai') !== false ||
     strpos($message_lower, 'do you know me') !== false ||
     strpos($message_lower, 'who am i') !== false) {
    
    if($user_info && !empty($user_info['name'])) {
      $user_name = htmlspecialchars($user_info['name'], ENT_QUOTES, 'UTF-8');
      return "Chào bạn {$user_name}! 👋\n\nTôi biết bạn là khách hàng của Vĩnh Long Hotel. Tôi ở đây để hỗ trợ bạn với mọi thắc mắc về:\n- Đặt phòng\n- Giá cả\n- Tiện ích\n- Điểm du lịch\n- Thanh toán\n\nBạn cần tôi giúp gì hôm nay?";
    } else {
      return "Xin chào! Tôi biết bạn là khách hàng của Vĩnh Long Hotel. Nếu bạn đã đăng nhập, tôi có thể biết thêm thông tin về bạn. Tôi ở đây để hỗ trợ bạn với mọi thắc mắc về khách sạn, đặt phòng và các dịch vụ khác. Bạn cần tôi giúp gì hôm nay?";
    }
  }
  
  // Check FAQs - PRIORITY: Check FAQs BEFORE default keyword responses
  // Ensure FAQs table exists
  $check_faqs_table = @mysqli_query($con, "SHOW TABLES LIKE 'faqs'");
  if(!$check_faqs_table || mysqli_num_rows($check_faqs_table) == 0) {
    // Table doesn't exist, create it
    ensureChatTables();
  }
  
  $faqs_query = "SELECT * FROM `faqs` WHERE `active` = 1 ORDER BY `priority` DESC, `id` ASC";
  $faqs_result = @mysqli_query($con, $faqs_query);
  
  if($faqs_result === false) {
    // Query failed, log error but continue with default responses
    $error = mysqli_error($con);
    error_log("FAQs query error: " . $error);
  } elseif($faqs_result && mysqli_num_rows($faqs_result) > 0) {
    $best_match = null;
    $best_score = 0;
    
    while($faq = mysqli_fetch_assoc($faqs_result)) {
      $score = 0;
      
      // Check exact question match (highest priority)
      $question_lower = mb_strtolower(trim($faq['question']), 'UTF-8');
      if($message_lower == $question_lower) {
        $score += 10; // Exact match gets highest score
      } elseif(strpos($message_lower, $question_lower) !== false || strpos($question_lower, $message_lower) !== false) {
        $score += 5; // Partial question match
      }
      
      // Check keywords (if provided)
      if(!empty($faq['keywords'])) {
        $keywords = explode(',', $faq['keywords']);
        foreach($keywords as $keyword) {
          $keyword = trim(mb_strtolower($keyword, 'UTF-8'));
          if(!empty($keyword)) {
            // Exact keyword match
            if($message_lower == $keyword) {
              $score += 5;
            } elseif(strpos($message_lower, $keyword) !== false) {
              $score += 3; // Keyword found in message
            }
          }
        }
      }
      
      // Check partial word matches in question
      $question_words = explode(' ', $question_lower);
      $message_words = explode(' ', $message_lower);
      $matched_words = 0;
      foreach($question_words as $qword) {
        $qword = trim($qword);
        if(strlen($qword) > 2) {
          foreach($message_words as $mword) {
            $mword = trim($mword);
            if($qword == $mword) {
              $matched_words++;
              $score += 2; // Exact word match
            } elseif(strpos($mword, $qword) !== false || strpos($qword, $mword) !== false) {
              $score += 1; // Partial word match
            }
          }
        }
      }
      
      // Bonus for priority
      if(isset($faq['priority']) && $faq['priority'] > 0) {
        $score += (int)$faq['priority'] * 0.1; // Add small bonus for priority
      }
      
      if($score > $best_score) {
        $best_score = $score;
        $best_match = $faq;
      }
    }
    
    // Lower threshold to 1 to make FAQs easier to match
    if($best_match && $best_score >= 1) {
      // Personalize response if user info available
      $answer = $best_match['answer'];
      if($user_info && !empty($user_info['name']) && strpos($answer, '{user_name}') !== false) {
        $answer = str_replace('{user_name}', htmlspecialchars($user_info['name'], ENT_QUOTES, 'UTF-8'), $answer);
      }
      return $answer;
    }
  }
  
  // Default responses based on keywords (only if no FAQ matched)
  if(strpos($message_lower, 'đặt phòng') !== false || strpos($message_lower, 'booking') !== false || strpos($message_lower, 'book') !== false || strpos($message_lower, 'muốn đặt') !== false) {
    return "Tôi có thể giúp bạn đặt phòng! Bạn muốn đặt phòng cho ngày nào? Vui lòng cho tôi biết:\n- Ngày check-in\n- Ngày check-out\n- Số lượng khách\n\nHoặc bạn có thể vào trang 'Phòng' để xem và đặt trực tiếp!";
  }
  
  if(strpos($message_lower, 'giá') !== false || strpos($message_lower, 'price') !== false || strpos($message_lower, 'cost') !== false) {
    return "Giá phòng tại Vĩnh Long Hotel rất đa dạng. Bạn có thể xem giá chi tiết trên trang 'Phòng'. Chúng tôi cũng có nhiều chương trình khuyến mãi và mã giảm giá. Bạn muốn xem phòng nào cụ thể không?";
  }
  
  if((strpos($message_lower, 'có gì') !== false || strpos($message_lower, 'what is there') !== false || strpos($message_lower, 'what to do') !== false) && 
     (strpos($message_lower, 'vĩnh long') !== false || strpos($message_lower, 'vl') !== false || strpos($message_lower, 'hotel') !== false || strpos($message_lower, 'khách sạn') !== false)) {
    return "Vĩnh Long có rất nhiều điều thú vị:\n\n🏨 **Vĩnh Long Hotel:** Khách sạn nghỉ dưỡng sang trọng với đầy đủ tiện ích\n\n🏛️ **Điểm du lịch:**\n- Chùa Tiên Châu\n- Cù lao An Bình\n- Khu du lịch sinh thái Tràm Chim\n- Chợ nổi Cái Bè\n\n🍜 **Đặc sản:**\n- Bánh tráng nem Lai Vung\n- Nem Lai Vung\n- Bưởi Năm Roi\n- Và nhiều đặc sản khác...\n\nBạn muốn biết thêm về điều gì?";
  }
  
  if(strpos($message_lower, 'cảm ơn') !== false || strpos($message_lower, 'thanks') !== false) {
    return "Không có gì! Nếu bạn cần thêm thông tin gì, cứ hỏi tôi nhé! 😊";
  }
  
  // If no match, return default greeting
  // This should NEVER return null or empty
  return "Xin chào! 👋 Tôi là trợ lý ảo của Vĩnh Long Hotel. Tôi có thể giúp bạn về:\n- Đặt phòng\n- Giá cả\n- Tiện ích\n- Địa điểm du lịch\n- Thanh toán\n\nBạn muốn biết thông tin gì? Hoặc bạn có thể nói 'đặt phòng' để tôi hỗ trợ trực tiếp!";
}

function shouldEscalateToHuman($message) {
  $message_lower = mb_strtolower($message, 'UTF-8');
  
  // Keywords that need human intervention
  $escalate_keywords = ['khiếu nại', 'phàn nàn', 'vấn đề', 'lỗi', 'sai', 'hủy', 'hoàn tiền', 'refund'];
  
  foreach($escalate_keywords as $keyword) {
    if(strpos($message_lower, $keyword) !== false) {
      return true;
    }
  }
  
  return false;
}

function updateSessionStatus($session_id, $status) {
  global $con;
  @mysqli_query($con, "UPDATE `chat_sessions` SET `status` = '" . mysqli_real_escape_string($con, $status) . "', `updated_at` = NOW() WHERE `session_id` = '" . mysqli_real_escape_string($con, $session_id) . "'");
}

function isAdminOnlineForSession($session_id) {
  global $con;
  
  // If session_id is empty, return false (no admin online)
  if(empty($session_id)) {
    return false;
  }
  
  // Ensure chat_sessions table exists
  $check_sessions = @mysqli_query($con, "SHOW TABLES LIKE 'chat_sessions'");
  if(!$check_sessions || mysqli_num_rows($check_sessions) == 0) {
    return false; // Table doesn't exist, so no admin can be online
  }
  
  // IMPORTANT: Only check if admin is actively viewing THIS specific session
  // We need to match the exact session_id, not just user_id
  // Admin is only "online" if they are viewing the messages page for THIS session
  
  $timeout_seconds = 60; // 1 minute (reduced to be more strict)
  
  // Check if admin is online for this EXACT session_id
  // Only return true if admin is actively viewing THIS conversation
  $query = "SELECT COUNT(*) as cnt FROM `chat_sessions` 
            WHERE `session_id` = ? 
            AND `assigned_to` = -1 
            AND `last_message_at` > (NOW() - INTERVAL ? SECOND)";
  $result = select($query, [$session_id, $timeout_seconds], 'si');
  
  if($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $count = (int)$row['cnt'];
    
    // Log for debugging
    error_log("Admin online check - session_id: $session_id, count: $count");
    
    return $count > 0;
  }
  
  // Don't check by user_id pattern - only exact session_id match
  // This prevents false positives when admin is viewing a different conversation
  return false;
}

function markAdminOnline($session_id, $admin_id) {
  global $con;
  // Use -1 to indicate admin is online and actively viewing
  @mysqli_query($con, "UPDATE `chat_sessions` SET `assigned_to` = -1, `status` = 'active', `last_message_at` = NOW() WHERE `session_id` = '" . mysqli_real_escape_string($con, $session_id) . "'");
}

function markAdminOffline($session_id) {
  global $con;
  @mysqli_query($con, "UPDATE `chat_sessions` SET `assigned_to` = NULL, `status` = 'active', `last_message_at` = NOW() WHERE `session_id` = '" . mysqli_real_escape_string($con, $session_id) . "' AND `assigned_to` = -1");
}

// ========== ADMIN ONLINE/OFFLINE/STATUS ACTIONS ==========
if($action == 'admin_online') {
  ob_end_clean();
  
  if(!isset($_SESSION['adminLogin']) || $_SESSION['adminLogin'] != true) {
    echo json_encode(['status' => 'error', 'message' => 'Chưa đăng nhập admin']);
    exit;
  }
  $session_id = $_POST['session_id'] ?? '';
  $user_id = (int)($_POST['user_id'] ?? 0);
  if(empty($session_id) || $user_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid session or user ID']);
    exit;
  }
  ensureChatTables();
  getOrCreateSession($user_id, $session_id);
  markAdminOnline($session_id, (int)$_SESSION['adminId']);
  echo json_encode(['status' => 'success']);
  exit;
}

if($action == 'admin_offline') {
  ob_end_clean();
  
  if(!isset($_SESSION['adminLogin']) || $_SESSION['adminLogin'] != true) {
    echo json_encode(['status' => 'error', 'message' => 'Chưa đăng nhập admin']);
    exit;
  }
  $session_id = $_POST['session_id'] ?? '';
  if(empty($session_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid session ID']);
    exit;
  }
  ensureChatTables();
  markAdminOffline($session_id);
  echo json_encode(['status' => 'success']);
  exit;
}

if($action == 'check_admin_status') {
  ob_end_clean();
  
  $session_id = $_GET['session_id'] ?? '';
  if(empty($session_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid session ID']);
    exit;
  }
  ensureChatTables();
  $admin_online = isAdminOnlineForSession($session_id);
  echo json_encode(['status' => 'success', 'admin_online' => $admin_online]);
  exit;
}

// Invalid action
ob_end_clean();
echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
exit;

