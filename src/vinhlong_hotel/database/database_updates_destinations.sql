-- ============================================
-- DATABASE UPDATES: ĐIỂM ĐẾN DU LỊCH VĨNH LONG
-- ============================================

-- Tạo bảng destinations (Điểm du lịch)
CREATE TABLE IF NOT EXISTS `destinations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL COMMENT 'Tên điểm du lịch',
  `description` TEXT COMMENT 'Mô tả chi tiết',
  `short_description` VARCHAR(500) COMMENT 'Mô tả ngắn',
  `location` VARCHAR(255) COMMENT 'Địa chỉ',
  `latitude` DECIMAL(10,8) COMMENT 'Vĩ độ',
  `longitude` DECIMAL(11,8) COMMENT 'Kinh độ',
  `image` VARCHAR(255) COMMENT 'Ảnh đại diện',
  `category` VARCHAR(50) DEFAULT 'other' COMMENT 'temple, nature, market, culture, other',
  `rating` DECIMAL(3,2) DEFAULT 0.00 COMMENT 'Đánh giá trung bình',
  `review_count` INT(11) DEFAULT 0 COMMENT 'Số lượng đánh giá',
  `active` TINYINT(1) DEFAULT 1 COMMENT '1=hiển thị, 0=ẩn',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_active` (`active`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tạo bảng room_destinations (Liên kết phòng - điểm du lịch)
CREATE TABLE IF NOT EXISTS `room_destinations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `room_id` INT(11) NOT NULL,
  `destination_id` INT(11) NOT NULL,
  `distance` DECIMAL(5,2) COMMENT 'Khoảng cách (km)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_room_destination` (`room_id`, `destination_id`),
  KEY `idx_room_id` (`room_id`),
  KEY `idx_destination_id` (`destination_id`),
  CONSTRAINT `fk_room_destinations_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_room_destinations_destination` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- INSERT DỮ LIỆU MẪU: ĐIỂM DU LỊCH VĨNH LONG
-- ============================================

INSERT INTO `destinations` (`name`, `description`, `short_description`, `location`, `latitude`, `longitude`, `image`, `category`, `rating`, `review_count`, `active`) VALUES
('Chùa Tiên Châu', 
'Chùa Tiên Châu là một trong những ngôi chùa cổ kính và nổi tiếng nhất tại Vĩnh Long. Ngôi chùa được xây dựng từ thế kỷ 19, mang đậm nét kiến trúc Phật giáo Nam Bộ với những họa tiết tinh xảo. Chùa nằm trên cù lao An Bình, được bao quanh bởi dòng sông Cổ Chiên hiền hòa, tạo nên một không gian thanh tịnh, yên bình. Đây là điểm đến lý tưởng cho những ai muốn tìm hiểu về văn hóa tâm linh và kiến trúc cổ của vùng đất Nam Bộ.',
'Ngôi chùa cổ kính từ thế kỷ 19, nằm trên cù lao An Bình, mang đậm nét kiến trúc Phật giáo Nam Bộ.',
'Cù lao An Bình, Long Hồ, Vĩnh Long',
10.2500, 105.9500,
'destinations/chua-tien-chau.jpg',
'temple', 4.5, 120, 1),

('Cù lao An Bình', 
'Cù lao An Bình là một hòn đảo xanh tươi nằm giữa sông Cổ Chiên, được mệnh danh là "viên ngọc xanh" của Vĩnh Long. Nơi đây nổi tiếng với những vườn cây trái sum suê, đặc biệt là nhãn, chôm chôm, sầu riêng. Du khách có thể tham quan các vườn cây ăn trái, thưởng thức trái cây tươi ngon ngay tại vườn, trải nghiệm cuộc sống miền quê yên bình. Cù lao còn có nhiều homestay và nhà vườn để du khách nghỉ lại, tham gia các hoạt động như câu cá, chèo xuồng, tham quan làng nghề truyền thống.',
'Hòn đảo xanh tươi giữa sông Cổ Chiên, nổi tiếng với vườn cây trái sum suê và cuộc sống miền quê yên bình.',
'Long Hồ, Vĩnh Long',
10.2400, 105.9600,
'destinations/cu-lao-an-binh.jpg',
'nature', 4.7, 200, 1),

('Khu du lịch sinh thái Tràm Chim', 
'Khu du lịch sinh thái Tràm Chim là một trong những điểm đến sinh thái nổi bật của Vĩnh Long. Khu vực này có hệ sinh thái đa dạng với nhiều loài chim quý hiếm, đặc biệt là sếu đầu đỏ. Du khách có thể tham gia các hoạt động như đi thuyền tham quan, ngắm chim, câu cá, tham quan rừng tràm. Đây là nơi lý tưởng để tìm hiểu về thiên nhiên hoang dã và hệ sinh thái đặc trưng của vùng đồng bằng sông Cửu Long.',
'Khu du lịch sinh thái với hệ động thực vật đa dạng, nơi sinh sống của nhiều loài chim quý hiếm.',
'Vĩnh Long',
10.2000, 105.9000,
'destinations/tram-chim.jpg',
'nature', 4.6, 150, 1),

('Chợ nổi Cái Bè', 
'Chợ nổi Cái Bè là một trong những chợ nổi lớn và sầm uất nhất khu vực đồng bằng sông Cửu Long. Chợ hoạt động từ sáng sớm, là nơi giao thương của người dân các tỉnh lân cận. Du khách có thể tham quan, mua sắm các sản vật địa phương như trái cây, rau củ, cá tôm tươi sống. Đặc biệt, chợ nổi còn có các ghe bán hàng ăn uống, phục vụ các món ăn đặc sản miền Tây ngay trên sông. Đây là trải nghiệm văn hóa độc đáo không thể bỏ qua khi đến Vĩnh Long.',
'Chợ nổi sầm uất trên sông, nơi giao thương và trải nghiệm văn hóa miền Tây độc đáo.',
'Cái Bè, Vĩnh Long',
10.3000, 105.8500,
'destinations/cho-noi-cai-be.jpg',
'market', 4.4, 180, 1),

('Vườn cây trái Cái Mơn', 
'Vườn cây trái Cái Mơn là điểm đến lý tưởng cho những ai yêu thích trái cây miền Tây. Vườn có diện tích rộng lớn với nhiều loại cây trái đặc sản như chôm chôm, nhãn, sầu riêng, măng cụt, xoài. Du khách có thể tham quan vườn, tìm hiểu về quy trình trồng trọt, thưởng thức trái cây tươi ngon ngay tại vườn. Đặc biệt, vào mùa trái chín, vườn trở thành điểm đến hấp dẫn với không khí nhộn nhịp, nhiều hoạt động vui chơi và mua sắm.',
'Vườn cây trái rộng lớn với nhiều loại đặc sản miền Tây, nơi thưởng thức trái cây tươi ngon.',
'Cái Mơn, Vĩnh Long',
10.2200, 105.9200,
'destinations/vuon-cay-trai.jpg',
'nature', 4.3, 100, 1),

('Đình Long Thanh', 
'Đình Long Thanh là một trong những ngôi đình cổ kính và có giá trị lịch sử cao tại Vĩnh Long. Đình được xây dựng từ thế kỷ 19, là nơi thờ cúng các vị thần và tổ tiên của làng. Kiến trúc đình mang đậm nét văn hóa Nam Bộ với những họa tiết chạm khắc tinh xảo. Đây là điểm đến văn hóa quan trọng, giúp du khách hiểu thêm về truyền thống và tín ngưỡng của người dân địa phương.',
'Ngôi đình cổ kính từ thế kỷ 19, nơi thờ cúng và tìm hiểu về văn hóa, tín ngưỡng địa phương.',
'Long Thanh, Vĩnh Long',
10.2600, 105.9400,
'destinations/dinh-long-thanh.jpg',
'culture', 4.2, 80, 1),

('Làng nghề đan lát Long Hồ', 
'Làng nghề đan lát Long Hồ là nơi lưu giữ và phát triển nghề đan lát truyền thống của vùng đất Nam Bộ. Du khách có thể tham quan các xưởng sản xuất, xem các nghệ nhân đan các sản phẩm từ tre, nứa như rổ, rá, giỏ, nón. Đây cũng là cơ hội để mua các sản phẩm thủ công mỹ nghệ làm quà lưu niệm. Làng nghề không chỉ là điểm tham quan mà còn là nơi góp phần bảo tồn và phát huy giá trị văn hóa truyền thống.',
'Làng nghề truyền thống với các sản phẩm đan lát từ tre, nứa, nơi tìm hiểu và mua sắm đồ thủ công.',
'Long Hồ, Vĩnh Long',
10.2300, 105.9300,
'destinations/lang-nghe-dan-lat.jpg',
'culture', 4.1, 60, 1),

('Khu du lịch Bình Hòa Phước', 
'Khu du lịch Bình Hòa Phước là điểm đến sinh thái và giải trí nổi tiếng tại Vĩnh Long. Khu vực này có cảnh quan thiên nhiên đẹp mắt với nhiều cây xanh, ao hồ, vườn hoa. Du khách có thể tham gia các hoạt động như đi bộ tham quan, chụp ảnh, thư giãn, thưởng thức các món ăn đặc sản. Đây là nơi lý tưởng cho các gia đình và nhóm bạn tụ tập, vui chơi vào cuối tuần.',
'Khu du lịch sinh thái với cảnh quan đẹp, nhiều hoạt động giải trí và thư giãn.',
'Bình Hòa Phước, Vĩnh Long',
10.2100, 105.9100,
'destinations/binh-hoa-phuoc.jpg',
'nature', 4.5, 130, 1);

-- ============================================
-- KẾT THÚC
-- ============================================


