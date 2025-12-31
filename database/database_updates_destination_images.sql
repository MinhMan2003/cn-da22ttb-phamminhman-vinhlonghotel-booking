-- ============================================
-- DATABASE UPDATES: NHIỀU ẢNH CHO ĐIỂM DU LỊCH
-- ============================================

-- Tạo bảng destination_images (Nhiều ảnh cho mỗi điểm du lịch)
CREATE TABLE IF NOT EXISTS `destination_images` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `destination_id` INT(11) NOT NULL,
  `image` VARCHAR(255) NOT NULL COMMENT 'Tên file ảnh',
  `is_primary` TINYINT(1) DEFAULT 0 COMMENT '1=ảnh chính, 0=ảnh phụ',
  `sort_order` INT(11) DEFAULT 0 COMMENT 'Thứ tự sắp xếp',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_destination_id` (`destination_id`),
  KEY `idx_is_primary` (`is_primary`),
  CONSTRAINT `fk_destination_images_destination` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Di chuyển ảnh hiện tại từ destinations.image sang destination_images
INSERT INTO `destination_images` (`destination_id`, `image`, `is_primary`, `sort_order`)
SELECT `id`, `image`, 1, 1
FROM `destinations`
WHERE `image` IS NOT NULL AND `image` != '';

-- ============================================
-- KẾT THÚC
-- ============================================


