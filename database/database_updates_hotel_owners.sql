-- ============================================
-- HỆ THỐNG QUẢN LÝ CHO CHỦ KHÁCH SẠN
-- ============================================

-- 1. Tạo bảng hotel_owners (Chủ khách sạn)
CREATE TABLE IF NOT EXISTS `hotel_owners` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL COMMENT 'Tên chủ khách sạn',
  `email` VARCHAR(255) NOT NULL UNIQUE COMMENT 'Email đăng nhập',
  `password` VARCHAR(255) NOT NULL COMMENT 'Mật khẩu (hash)',
  `phone` VARCHAR(20) DEFAULT NULL COMMENT 'Số điện thoại',
  `hotel_name` VARCHAR(255) DEFAULT NULL COMMENT 'Tên khách sạn',
  `address` TEXT DEFAULT NULL COMMENT 'Địa chỉ khách sạn',
  `status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Thêm cột owner_id vào bảng rooms (nullable - nếu null thì là phòng của admin)
ALTER TABLE `rooms` 
ADD COLUMN IF NOT EXISTS `owner_id` INT(11) DEFAULT NULL COMMENT 'ID chủ khách sạn, NULL = phòng của admin' AFTER `id`,
ADD INDEX IF NOT EXISTS `idx_owner_id` (`owner_id`),
ADD CONSTRAINT `fk_rooms_owner` FOREIGN KEY (`owner_id`) REFERENCES `hotel_owners`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- 3. Insert dữ liệu mẫu (mật khẩu: 123456 - cần hash trong code)
-- Lưu ý: Trong production, password phải được hash bằng password_hash()
INSERT INTO `hotel_owners` (`name`, `email`, `password`, `phone`, `hotel_name`, `address`, `status`) VALUES
('Nguyễn Văn A', 'owner1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0901234567', 'Khách sạn Sông Tiền', '123 Đường ABC, TP. Vĩnh Long', 1),
('Trần Thị B', 'owner2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0907654321', 'Riverside Hotel Vĩnh Long', '456 Đường XYZ, Long Hồ', 1);

-- 4. Gán một số phòng cho chủ khách sạn (ví dụ: phòng ID 1,2 cho owner 1; phòng 3,4 cho owner 2)
-- UPDATE `rooms` SET `owner_id` = 1 WHERE `id` IN (1, 2);
-- UPDATE `rooms` SET `owner_id` = 2 WHERE `id` IN (3, 4);

-- 5. Tạo bảng booking_notifications (thông báo booking cho owner - tùy chọn)
CREATE TABLE IF NOT EXISTS `booking_notifications` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `owner_id` INT(11) NOT NULL,
  `booking_id` INT(11) NOT NULL,
  `room_id` INT(11) NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_owner_id` (`owner_id`),
  INDEX `idx_booking_id` (`booking_id`),
  INDEX `idx_is_read` (`is_read`),
  CONSTRAINT `fk_notif_owner` FOREIGN KEY (`owner_id`) REFERENCES `hotel_owners`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_notif_booking` FOREIGN KEY (`booking_id`) REFERENCES `booking_order`(`booking_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

