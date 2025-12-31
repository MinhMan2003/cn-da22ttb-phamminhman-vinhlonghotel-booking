<?php
/**
 * Script tự động tạo/cập nhật bảng messages nếu chưa tồn tại
 * Hỗ trợ cả admin (owner_id = NULL) và owner
 */

if (!isset($con)) {
  require_once __DIR__ . '/db_config.php';
}

// Chỉ chạy nếu $con đã được set
if (isset($con)) {
  // Kiểm tra xem bảng messages đã tồn tại chưa
  $check_table = @mysqli_query($con, "SHOW TABLES LIKE 'messages'");

  if (!$check_table || mysqli_num_rows($check_table) === 0) {
    // Tạo bảng messages
    $create_table_sql = "
      CREATE TABLE IF NOT EXISTS `messages` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL COMMENT 'ID khách hàng',
        `owner_id` int(11) DEFAULT NULL COMMENT 'ID chủ khách sạn (NULL = admin)',
        `room_id` int(11) DEFAULT NULL COMMENT 'ID phòng (nếu liên quan đến phòng cụ thể)',
        `sender_type` enum('user','owner','admin') NOT NULL COMMENT 'Loại người gửi: user, owner hoặc admin',
        `message` text NOT NULL COMMENT 'Nội dung tin nhắn',
        `seen` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Đã đọc: 0=chưa đọc, 1=đã đọc',
        `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Thời gian gửi',
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`),
        KEY `owner_id` (`owner_id`),
        KEY `room_id` (`room_id`),
        KEY `seen` (`seen`),
        KEY `created_at` (`created_at`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Bảng lưu tin nhắn giữa khách hàng, admin và chủ khách sạn'
    ";
    
    // Thử tạo bảng
    if (@mysqli_query($con, $create_table_sql)) {
      // Thêm foreign keys nếu bảng đã tồn tại
      // Kiểm tra foreign key đã tồn tại chưa
      $fk_check = @mysqli_query($con, "
        SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'messages' 
        AND CONSTRAINT_NAME = 'messages_ibfk_1'
      ");
      
      if (!$fk_check || mysqli_num_rows($fk_check) === 0) {
        // Thêm foreign keys (có thể fail nếu bảng đã có dữ liệu, nhưng không sao)
        @mysqli_query($con, "ALTER TABLE `messages` 
          ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_cred` (`id`) ON DELETE CASCADE");
      }
      
      $fk_check2 = @mysqli_query($con, "
        SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'messages' 
        AND CONSTRAINT_NAME = 'messages_ibfk_2'
      ");
      
      if (!$fk_check2 || mysqli_num_rows($fk_check2) === 0) {
        @mysqli_query($con, "ALTER TABLE `messages` 
          ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `hotel_owners` (`id`) ON DELETE SET NULL");
      }
      
      $fk_check3 = @mysqli_query($con, "
        SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'messages' 
        AND CONSTRAINT_NAME = 'messages_ibfk_3'
      ");
      
      if (!$fk_check3 || mysqli_num_rows($fk_check3) === 0) {
        @mysqli_query($con, "ALTER TABLE `messages` 
          ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL");
      }
    }
  } else {
    // Cập nhật bảng nếu đã tồn tại
    // Kiểm tra và cập nhật owner_id để cho phép NULL
    $check_col = @mysqli_query($con, "SHOW COLUMNS FROM `messages` LIKE 'owner_id'");
    if($check_col && mysqli_num_rows($check_col) > 0) {
      $col_info = mysqli_fetch_assoc($check_col);
      if($col_info['Null'] === 'NO') {
        @mysqli_query($con, "ALTER TABLE `messages` MODIFY COLUMN `owner_id` int(11) DEFAULT NULL COMMENT 'ID chủ khách sạn (NULL = admin)'");
      }
    }
    
    // Kiểm tra và cập nhật sender_type để thêm 'admin'
    $check_enum = @mysqli_query($con, "SHOW COLUMNS FROM `messages` LIKE 'sender_type'");
    if($check_enum && mysqli_num_rows($check_enum) > 0) {
      $enum_info = mysqli_fetch_assoc($check_enum);
      if(strpos($enum_info['Type'], "'admin'") === false) {
        @mysqli_query($con, "ALTER TABLE `messages` MODIFY COLUMN `sender_type` enum('user','owner','admin') NOT NULL COMMENT 'Loại người gửi: user, owner hoặc admin'");
      }
    }
  }
}
?>
