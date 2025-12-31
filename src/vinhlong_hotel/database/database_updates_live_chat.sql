-- ============================================
-- DATABASE UPDATES: LIVE CHAT SYSTEM
-- ============================================

-- Tạo bảng FAQs cho chatbot
CREATE TABLE IF NOT EXISTS `faqs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `question` VARCHAR(500) NOT NULL COMMENT 'Câu hỏi',
  `answer` TEXT NOT NULL COMMENT 'Câu trả lời',
  `keywords` TEXT COMMENT 'Từ khóa để tìm kiếm (phân cách bằng dấu phẩy)',
  `category` VARCHAR(100) DEFAULT 'general' COMMENT 'Danh mục: general, booking, payment, room, etc',
  `priority` INT(11) DEFAULT 0 COMMENT 'Độ ưu tiên (số càng cao càng ưu tiên)',
  `active` TINYINT(1) DEFAULT 1 COMMENT '1=hiển thị, 0=ẩn',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_active` (`active`),
  KEY `idx_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tạo bảng chat_sessions để quản lý phiên chat
CREATE TABLE IF NOT EXISTS `chat_sessions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL COMMENT 'ID người dùng (NULL nếu chưa đăng nhập)',
  `session_id` VARCHAR(100) NOT NULL COMMENT 'Session ID cho guest',
  `status` ENUM('active', 'closed', 'waiting') DEFAULT 'active' COMMENT 'Trạng thái chat',
  `assigned_to` INT(11) DEFAULT NULL COMMENT 'ID admin được phân công',
  `last_message_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_session` (`session_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_assigned_to` (`assigned_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Cập nhật bảng messages để thêm các trường mới
ALTER TABLE `messages` 
  ADD COLUMN IF NOT EXISTS `session_id` VARCHAR(100) DEFAULT NULL COMMENT 'ID phiên chat',
  ADD COLUMN IF NOT EXISTS `message_type` ENUM('text', 'booking', 'system', 'bot') DEFAULT 'text' COMMENT 'Loại tin nhắn',
  ADD COLUMN IF NOT EXISTS `metadata` TEXT COMMENT 'Dữ liệu bổ sung (JSON)',
  ADD COLUMN IF NOT EXISTS `is_bot` TINYINT(1) DEFAULT 0 COMMENT '1=bot, 0=người dùng',
  ADD KEY IF NOT EXISTS `idx_session_id` (`session_id`),
  ADD KEY IF NOT EXISTS `idx_message_type` (`message_type`);

-- Insert dữ liệu FAQ mẫu
INSERT INTO `faqs` (`question`, `answer`, `keywords`, `category`, `priority`) VALUES
('Xin chào', 'Xin chào! Tôi là trợ lý ảo của Vĩnh Long Hotel. Tôi có thể giúp gì cho bạn? Bạn có thể hỏi về: đặt phòng, giá cả, tiện ích, địa điểm du lịch, hoặc thanh toán.', 'xin chào, hello, hi, chào', 'general', 10),
('Làm sao để đặt phòng?', 'Để đặt phòng, bạn có thể:\n1. Vào trang "Phòng" và chọn phòng yêu thích\n2. Chọn ngày check-in và check-out\n3. Nhấn "Đặt ngay" và điền thông tin\n4. Thanh toán qua QR Code\n\nHoặc bạn có thể nói "Đặt phòng" để tôi hỗ trợ trực tiếp!', 'đặt phòng, booking, book, reserve, đặt', 'booking', 9),
('Giá phòng như thế nào?', 'Giá phòng tại Vĩnh Long Hotel rất đa dạng, phù hợp với mọi nhu cầu. Bạn có thể:\n- Xem giá chi tiết trên trang "Phòng"\n- Áp dụng mã giảm giá để tiết kiệm\n- Thành viên Silver/Gold/Platinum được giảm giá đặc biệt\n\nBạn muốn xem phòng nào cụ thể không?', 'giá, giá cả, price, cost, bao nhiêu tiền', 'room', 8),
('Có những tiện ích gì?', 'Khách sạn có đầy đủ tiện ích:\n- WiFi miễn phí\n- Bãi đỗ xe\n- Phòng gym\n- Nhà hàng\n- Spa & Massage\n- Hồ bơi\n- Dịch vụ giặt ủi\n\nBạn có thể xem chi tiết tại trang "Tiện ích"!', 'tiện ích, facilities, dịch vụ, amenities', 'room', 7),
('Có địa điểm du lịch nào gần đây không?', 'Vĩnh Long có nhiều điểm du lịch hấp dẫn:\n- Chùa Tiên Châu\n- Cù lao An Bình\n- Khu du lịch sinh thái Tràm Chim\n- Chợ nổi Cái Bè\n- Và nhiều điểm khác...\n\nBạn có thể xem chi tiết tại trang "Điểm đến"!', 'địa điểm, điểm du lịch, du lịch, destination, tham quan', 'general', 6),
('Thanh toán như thế nào?', 'Chúng tôi hỗ trợ thanh toán qua:\n- QR Code (VietQR) - nhanh chóng và tiện lợi\n- Chuyển khoản ngân hàng\n- Tiền mặt tại khách sạn\n\nSau khi đặt phòng, bạn sẽ nhận được mã QR để quét và thanh toán ngay!', 'thanh toán, payment, pay, trả tiền, QR', 'payment', 8),
('Có thể hủy đặt phòng không?', 'Có! Bạn có thể hủy đặt phòng:\n- Hủy miễn phí nếu hủy trước 24h\n- Vào trang "Lịch sử đặt phòng" để hủy\n- Hoặc liên hệ trực tiếp với chúng tôi\n\nChúng tôi luôn hỗ trợ bạn tốt nhất!', 'hủy, cancel, hủy đặt, hủy phòng', 'booking', 7),
('Có mã giảm giá không?', 'Có! Chúng tôi thường xuyên có các chương trình khuyến mãi:\n- Mã giảm giá theo mùa\n- Ưu đãi cho thành viên\n- Combo đặc biệt\n\nBạn có thể xem các mã giảm giá hiện có trên trang chủ!', 'mã giảm giá, voucher, discount, khuyến mãi, promo', 'general', 6),
('Làm sao để trở thành thành viên?', 'Bạn tự động trở thành thành viên khi đăng ký tài khoản:\n- Silver: Thành viên mới\n- Gold: Đã đặt phòng 3 lần\n- Platinum: Đã đặt phòng 10 lần\n\nMỗi hạng có ưu đãi riêng!', 'thành viên, member, membership, hạng', 'general', 5),
('Có đặc sản Vĩnh Long không?', 'Có! Vĩnh Long nổi tiếng với nhiều đặc sản:\n- Bánh tráng nem Lai Vung\n- Nem Lai Vung\n- Bưởi Năm Roi\n- Cá tai tượng chiên xù\n- Và nhiều món khác...\n\nBạn có thể xem chi tiết tại trang "Điểm đến" phần "Đặc sản Vĩnh Long"!', 'đặc sản, specialty, món ăn, quà lưu niệm', 'general', 5);

-- ============================================
-- KẾT THÚC
-- ============================================


