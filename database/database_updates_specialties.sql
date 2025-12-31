-- Tạo bảng đặc sản Vĩnh Long
CREATE TABLE IF NOT EXISTS `specialties` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL COMMENT 'Tên đặc sản',
  `description` TEXT COMMENT 'Mô tả chi tiết',
  `short_description` VARCHAR(500) COMMENT 'Mô tả ngắn',
  `image` VARCHAR(255) COMMENT 'Ảnh đại diện',
  `category` VARCHAR(50) DEFAULT 'food' COMMENT 'food, drink, souvenir, fruit',
  `price_range` VARCHAR(100) COMMENT 'Khoảng giá (VD: 50.000 - 200.000 VNĐ)',
  `best_season` VARCHAR(100) COMMENT 'Mùa tốt nhất',
  `location` VARCHAR(255) COMMENT 'Địa điểm mua',
  `latitude` DECIMAL(10,8),
  `longitude` DECIMAL(11,8),
  `rating` DECIMAL(3,2) DEFAULT 0,
  `review_count` INT(11) DEFAULT 0,
  `active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng địa điểm mua đặc sản
CREATE TABLE IF NOT EXISTS `specialty_shops` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `specialty_id` INT(11) NOT NULL,
  `shop_name` VARCHAR(255) NOT NULL COMMENT 'Tên cửa hàng',
  `address` VARCHAR(500) COMMENT 'Địa chỉ',
  `phone` VARCHAR(20),
  `latitude` DECIMAL(10,8),
  `longitude` DECIMAL(11,8),
  `opening_hours` VARCHAR(200) COMMENT 'Giờ mở cửa',
  `rating` DECIMAL(3,2) DEFAULT 0,
  `active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `specialty_id` (`specialty_id`),
  FOREIGN KEY (`specialty_id`) REFERENCES `specialties`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng ảnh đặc sản
CREATE TABLE IF NOT EXISTS `specialty_images` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `specialty_id` INT(11) NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `is_primary` TINYINT(1) DEFAULT 0,
  `sort_order` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `specialty_id` (`specialty_id`),
  FOREIGN KEY (`specialty_id`) REFERENCES `specialties`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm dữ liệu mẫu đặc sản Vĩnh Long
INSERT INTO `specialties` (`name`, `short_description`, `description`, `category`, `price_range`, `best_season`, `location`, `rating`, `review_count`) VALUES
('Bưởi Năm Roi', 'Bưởi Năm Roi Vĩnh Long nổi tiếng với vị ngọt thanh, mọng nước, được trồng nhiều ở huyện Bình Minh và Vũng Liêm.', 'Bưởi Năm Roi là đặc sản nổi tiếng của Vĩnh Long, được trồng chủ yếu ở huyện Bình Minh và Vũng Liêm. Bưởi có vị ngọt thanh, mọng nước, múi dày, được nhiều người yêu thích. Mùa thu hoạch chính từ tháng 8 đến tháng 12.', 'fruit', '30.000 - 80.000 VNĐ/kg', 'Tháng 8 - 12', 'Huyện Bình Minh, Vũng Liêm', 4.8, 125),
('Chuột đồng nướng', 'Món ăn đặc trưng của vùng sông nước, chuột đồng được nướng vàng giòn, thơm ngon đặc biệt.', 'Chuột đồng nướng là món ăn đặc trưng của vùng Đồng bằng sông Cửu Long. Chuột được bắt từ đồng ruộng, làm sạch và nướng trên than hồng. Thịt chuột thơm ngon, giòn rụm, là món nhậu được nhiều người yêu thích.', 'food', '50.000 - 150.000 VNĐ/phần', 'Quanh năm', 'Các quán ăn địa phương', 4.5, 89),
('Bánh tét lá cẩm', 'Bánh tét Vĩnh Long với lá cẩm tạo màu tím đẹp mắt, nhân đậu xanh và thịt ba chỉ thơm ngon.', 'Bánh tét lá cẩm là món ăn truyền thống của Vĩnh Long, đặc biệt trong dịp Tết. Bánh được gói bằng lá cẩm tạo màu tím đẹp mắt, nhân đậu xanh và thịt ba chỉ. Bánh có vị ngọt béo, thơm mùi lá cẩm đặc trưng.', 'food', '40.000 - 100.000 VNĐ/cái', 'Dịp Tết, lễ hội', 'Chợ địa phương, các cơ sở sản xuất', 4.7, 156),
('Dừa sáp Cầu Kè', 'Dừa sáp Cầu Kè là đặc sản quý hiếm, cơm dừa dày, mềm như sáp, vị ngọt thanh đặc biệt.', 'Dừa sáp Cầu Kè là đặc sản nổi tiếng của huyện Cầu Kè, Vĩnh Long. Khác với dừa thường, dừa sáp có cơm dừa dày, mềm như sáp, vị ngọt thanh đặc biệt. Đây là loại dừa quý hiếm, được nhiều người săn lùng.', 'fruit', '80.000 - 200.000 VNĐ/trái', 'Quanh năm', 'Huyện Cầu Kè', 4.9, 203),
('Cá lóc nướng trui', 'Cá lóc tươi được nướng trực tiếp trên than hồng, thịt cá thơm ngon, giữ nguyên vị ngọt tự nhiên.', 'Cá lóc nướng trui là món ăn dân dã nhưng đậm đà hương vị miền Tây. Cá lóc tươi được nướng trực tiếp trên than hồng, không cần ướp gia vị nhiều. Thịt cá thơm ngon, giữ nguyên vị ngọt tự nhiên, ăn kèm với rau sống và nước mắm me.', 'food', '80.000 - 200.000 VNĐ/phần', 'Quanh năm', 'Các quán ăn ven sông', 4.6, 112),
('Bánh phồng tôm Sa Đéc', 'Bánh phồng tôm giòn rụm, thơm mùi tôm, là món quà lưu niệm phổ biến của Vĩnh Long.', 'Bánh phồng tôm Sa Đéc là đặc sản nổi tiếng, được làm từ bột gạo và tôm khô. Bánh giòn rụm, thơm mùi tôm đặc trưng, là món quà lưu niệm được nhiều du khách yêu thích. Có thể ăn trực tiếp hoặc nướng qua.', 'souvenir', '30.000 - 80.000 VNĐ/hộp', 'Quanh năm', 'Chợ Sa Đéc, các cửa hàng đặc sản', 4.4, 98),
('Mắm cá linh', 'Mắm cá linh là món ăn đặc trưng của vùng sông nước, có vị mặn đậm đà, thơm ngon.', 'Mắm cá linh là món ăn truyền thống của vùng Đồng bằng sông Cửu Long. Cá linh được ướp muối và lên men tự nhiên, tạo ra món mắm có vị mặn đậm đà, thơm ngon. Thường được ăn kèm với cơm nóng, rau sống và thịt ba chỉ luộc.', 'food', '50.000 - 120.000 VNĐ/hũ', 'Quanh năm', 'Chợ địa phương', 4.3, 67),
('Kẹo dừa Bến Tre', 'Kẹo dừa thơm ngon, ngọt thanh, là món quà lưu niệm phổ biến của vùng Đồng bằng sông Cửu Long.', 'Kẹo dừa Bến Tre là đặc sản nổi tiếng, được làm từ cơm dừa và đường. Kẹo có vị ngọt thanh, thơm mùi dừa đặc trưng, dai mềm. Đây là món quà lưu niệm được nhiều du khách yêu thích khi đến thăm vùng Đồng bằng sông Cửu Long.', 'souvenir', '25.000 - 60.000 VNĐ/hộp', 'Quanh năm', 'Các cửa hàng đặc sản', 4.5, 134);

-- Thêm dữ liệu địa điểm mua đặc sản
INSERT INTO `specialty_shops` (`specialty_id`, `shop_name`, `address`, `phone`, `opening_hours`, `rating`) VALUES
(1, 'Vườn bưởi Năm Roi Bình Minh', 'Huyện Bình Minh, Vĩnh Long', '0294.3xxx.xxx', '7:00 - 18:00', 4.8),
(1, 'Chợ nông sản Vũng Liêm', 'Thị trấn Vũng Liêm, Vĩnh Long', '0294.3xxx.xxx', '5:00 - 19:00', 4.6),
(2, 'Quán chuột đồng nướng Sông Hậu', 'Đường 30/4, TP. Vĩnh Long', '0294.3xxx.xxx', '17:00 - 23:00', 4.5),
(3, 'Cơ sở bánh tét lá cẩm Hương Miền Tây', 'Phường 1, TP. Vĩnh Long', '0294.3xxx.xxx', '6:00 - 20:00', 4.7),
(4, 'Vườn dừa sáp Cầu Kè', 'Huyện Cầu Kè, Vĩnh Long', '0294.3xxx.xxx', '7:00 - 17:00', 4.9),
(5, 'Quán cá lóc nướng trui Sông Tiền', 'Ven sông Tiền, Vĩnh Long', '0294.3xxx.xxx', '11:00 - 22:00', 4.6),
(6, 'Cửa hàng đặc sản Sa Đéc', 'Chợ Sa Đéc, Đồng Tháp (gần Vĩnh Long)', '0277.3xxx.xxx', '6:00 - 20:00', 4.4),
(7, 'Chợ địa phương Vĩnh Long', 'Phường 1, TP. Vĩnh Long', '0294.3xxx.xxx', '5:00 - 19:00', 4.3),
(8, 'Cửa hàng đặc sản Miền Tây', 'Đường Trần Phú, TP. Vĩnh Long', '0294.3xxx.xxx', '7:00 - 21:00', 4.5);


