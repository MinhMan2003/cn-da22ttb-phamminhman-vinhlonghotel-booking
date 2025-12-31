-- Cập nhật bảng rating_review để thêm các tính năng mới
-- Chạy file này để cập nhật database (an toàn, không bị lỗi nếu cột đã tồn tại)

-- Kiểm tra và thêm cột images nếu chưa có
SET @dbname = DATABASE();
SET @tablename = "rating_review";
SET @columnname = "images";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " TEXT NULL DEFAULT NULL COMMENT 'JSON array chứa đường dẫn ảnh' AFTER `review`")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Kiểm tra và thêm cột helpful_count nếu chưa có
SET @columnname = "helpful_count";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " INT(11) NOT NULL DEFAULT 0 COMMENT 'Số lượt đánh dấu hữu ích' AFTER `seen`")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Kiểm tra và thêm cột admin_reply nếu chưa có
SET @columnname = "admin_reply";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " TEXT NULL DEFAULT NULL COMMENT 'Phản hồi từ admin' AFTER `helpful_count`")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Kiểm tra và thêm cột admin_reply_date nếu chưa có
SET @columnname = "admin_reply_date";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " DATETIME NULL DEFAULT NULL COMMENT 'Ngày admin phản hồi' AFTER `admin_reply`")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Tạo bảng lưu trữ lượt đánh dấu hữu ích của từng user (nếu chưa tồn tại)
CREATE TABLE IF NOT EXISTS `review_helpful` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `review_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_helpful` (`review_id`, `user_id`),
  FOREIGN KEY (`review_id`) REFERENCES `rating_review`(`sr_no`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `user_cred`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_CI;

