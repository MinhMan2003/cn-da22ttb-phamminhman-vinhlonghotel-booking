-- Tạo bảng messages để lưu tin nhắn giữa khách hàng và chủ khách sạn
-- Chạy file này để tạo bảng messages

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'ID khách hàng',
  `owner_id` int(11) NOT NULL COMMENT 'ID chủ khách sạn',
  `room_id` int(11) DEFAULT NULL COMMENT 'ID phòng (nếu liên quan đến phòng cụ thể)',
  `sender_type` enum('user','owner') NOT NULL COMMENT 'Loại người gửi: user hoặc owner',
  `message` text NOT NULL COMMENT 'Nội dung tin nhắn',
  `seen` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Đã đọc: 0=chưa đọc, 1=đã đọc',
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Thời gian gửi',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `owner_id` (`owner_id`),
  KEY `room_id` (`room_id`),
  KEY `seen` (`seen`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_cred` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `hotel_owners` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Bảng lưu tin nhắn giữa khách hàng và chủ khách sạn';

