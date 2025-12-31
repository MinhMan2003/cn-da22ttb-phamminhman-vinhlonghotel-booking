-- Cập nhật bảng messages để hỗ trợ tin nhắn với admin
-- Admin sẽ có owner_id = NULL

-- Kiểm tra và cập nhật foreign key nếu cần
ALTER TABLE `messages` 
  MODIFY COLUMN `owner_id` int(11) NULL COMMENT 'ID chủ khách sạn (NULL = admin)';

-- Xóa foreign key cũ nếu có
SET @fk_name = (SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'messages' 
                AND CONSTRAINT_NAME = 'messages_ibfk_2');
SET @sql = IF(@fk_name IS NOT NULL, 
              CONCAT('ALTER TABLE `messages` DROP FOREIGN KEY `', @fk_name, '`'), 
              'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Thêm lại foreign key với ON DELETE SET NULL
ALTER TABLE `messages` 
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `hotel_owners` (`id`) ON DELETE SET NULL;

