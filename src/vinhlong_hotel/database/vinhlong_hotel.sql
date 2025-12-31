

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Cơ sở dữ liệu: `vinhlong_hotel`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin_cred`
--

CREATE TABLE `admin_cred` (
  `sr_no` int(11) NOT NULL,
  `admin_name` varchar(150) NOT NULL,
  `admin_pass` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admin_cred`
--

INSERT INTO `admin_cred` (`sr_no`, `admin_name`, `admin_pass`) VALUES
(1, 'Man', '123123');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_details`
--

CREATE TABLE `booking_details` (
  `sr_no` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `total_pay` int(11) NOT NULL,
  `room_no` varchar(100) DEFAULT NULL,
  `user_name` varchar(100) NOT NULL,
  `phonenum` varchar(100) NOT NULL,
  `address` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `booking_details`
--

INSERT INTO `booking_details` (`sr_no`, `booking_id`, `room_name`, `price`, `total_pay`, `room_no`, `user_name`, `phonenum`, `address`) VALUES
(2, 23, 'RUBY HOTEL Vinh Long', 500000, 1000000, '9', 'Mẫn', '08235218288', 'Trà Vinh Vĩnh Long'),
(5, 26, 'Phòng Cơ Bản 3', 1200000, 2400000, '1', 'Trinh', '12312312113', 'Trà Vinh Bến Tre'),
(6, 27, 'Phòng Cơ Bản 3', 1200000, 2400000, NULL, 'Trinh', '12312312113', 'Trà Vinh Bến Tre'),
(7, 28, 'Phòng Cơ Bản 3', 1200000, 8400000, NULL, 'Trinh', '12312312113', 'Trà Vinh Bến Tre'),
(8, 29, 'RUBY HOTEL Vinh Long', 500000, 500000, '9', 'Trinh', '12312312113', 'Trà Vinh Bến Tre'),
(9, 30, 'RUBY HOTEL Vinh Long', 500000, 1000000, 'e', 'Trinh', '12312312113', 'Trà Vinh Bến Tre'),
(10, 31, 'RUBY HOTEL Vinh Long', 500000, 1000000, '1', 'Trinh', '12312312113', 'Trà Vinh Bến Tre'),
(11, 32, 'RUBY HOTEL Vinh Long', 500000, 500000, NULL, 'Trinh', '12312312113', 'Trà Vinh Bến Tre'),
(12, 33, 'RUBY HOTEL Vinh Long', 500000, 1000000, '9', 'Mẫn', '08235218288', 'Trà Vinh Vĩnh Long'),
(13, 34, 'Phòng Cơ Bản 3', 1200000, 3600000, NULL, 'Nguyễn Hoàng Kha', '0987988167', 'Ấp 3'),
(14, 35, 'RUBY HOTEL Vinh Long', 500000, 500000, '1', 'Nguyễn Hoàng Kha', '0987988167', 'Ấp 3'),
(15, 36, 'RUBY HOTEL Vinh Long', 500000, 500000, '9', 'Phạm Minh Mẫn', '0823521928', 'Càng Long'),
(16, 37, 'RUBY HOTEL Vinh Long', 500000, 1500000, '12', 'Phạm Minh Mẫn', '0823521928', 'Càng Long'),
(17, 38, 'RUBY HOTEL Vinh Long', 500000, 4500000, '9', 'Phạm Minh Mẫn', '0823521928', 'Càng Long'),
(18, 39, 'Phòng Cơ Bản 1', 800000, 4000000, '1', 'Phạm Minh Mẫn', '0823521928', 'Càng Long'),
(19, 40, 'Phòng Cơ Bản 1', 800000, 3200000, 'a', 'Phạm Minh Mẫn', '0823521928', 'Càng Long'),
(20, 41, 'Phòng Sang Trọng', 3500000, 7000000, '9', 'Phạm Minh Mẫn', '0823521928', 'Càng Long');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_order`
--

CREATE TABLE `booking_order` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `arrival` int(11) NOT NULL DEFAULT 0,
  `refund` tinyint(1) NOT NULL DEFAULT 0,
  `refund_date` datetime DEFAULT NULL,
  `booking_status` varchar(100) NOT NULL DEFAULT 'pending',
  `order_id` varchar(150) NOT NULL,
  `trans_id` varchar(200) DEFAULT NULL,
  `trans_amt` int(11) DEFAULT NULL,
  `trans_status` varchar(100) NOT NULL DEFAULT 'pending',
  `trans_resp_msg` varchar(200) DEFAULT NULL,
  `rate_review` int(11) DEFAULT NULL,
  `datentime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `booking_order`
--

INSERT INTO `booking_order` (`booking_id`, `user_id`, `room_id`, `check_in`, `check_out`, `arrival`, `refund`, `refund_date`, `booking_status`, `order_id`, `trans_id`, `trans_amt`, `trans_status`, `trans_resp_msg`, `rate_review`, `datentime`) VALUES
(1, 2, 3, '2024-12-12', '2024-12-14', 0, 0, NULL, 'booked', 'ORD_21055700', NULL, 0, 'pending', NULL, NULL, '2024-11-30 01:50:12'),
(2, 2, 3, '2024-12-03', '2024-12-04', 1, 0, NULL, 'booked', 'ORD_24215693', '20220720111212800110168128204225279', 600, 'TXN_SUCCESS', 'Txn Success', NULL, '2024-11-30 02:14:44'),
(3, 2, 3, '2024-12-13', '2024-12-17', 0, 1, NULL, 'cancelled', 'ORD_26312547', '20220720111212800110168165603901976', 1800, 'TXN_SUCCESS', 'Txn Success', NULL, '2024-11-30 02:19:00'),
(21, 7, 3, '2024-12-01', '2024-12-07', 0, 1, NULL, 'cancelled', 'ORD_74731476', NULL, NULL, 'TXN_SUCCESS', NULL, NULL, '2024-12-01 11:25:29'),
(22, 7, 4, '2024-12-29', '2024-12-31', 0, 0, NULL, 'booked', 'ORD_72382450', NULL, NULL, 'TXN_SUCCESS', NULL, NULL, '2024-12-01 11:32:34'),
(23, 14, 7, '2025-10-28', '2025-10-30', 1, 0, NULL, 'booked', 'ORD_148767611', NULL, NULL, 'pending', NULL, 1, '2025-10-26 17:36:39'),
(26, 15, 3, '2025-10-29', '2025-10-31', 1, 0, NULL, 'booked', 'ORD_154436636', NULL, NULL, 'pending', NULL, 0, '2025-10-27 12:07:21'),
(27, 15, 3, '2025-10-28', '2025-10-30', 0, 0, NULL, 'pending', 'ORD_155157690', NULL, NULL, 'pending', NULL, NULL, '2025-10-27 13:34:33'),
(28, 15, 3, '2025-10-30', '2025-11-06', 0, 0, NULL, 'cancelled', 'ORD_159507596', NULL, NULL, 'pending', NULL, NULL, '2025-10-27 14:19:57'),
(29, 15, 7, '2025-10-29', '2025-10-30', 1, 0, NULL, 'booked', 'ORD_156636068', NULL, NULL, 'pending', NULL, 0, '2025-10-27 14:28:32'),
(30, 15, 7, '2025-10-28', '2025-10-30', 1, 0, NULL, 'booked', 'ORD_152209398', NULL, NULL, 'pending', NULL, 0, '2025-10-27 14:29:28'),
(31, 15, 7, '2025-10-27', '2025-10-29', 1, 0, NULL, 'booked', 'ORD_151770498', NULL, NULL, 'pending', NULL, 0, '2025-10-27 15:04:52'),
(32, 15, 7, '2025-10-29', '2025-10-30', 0, 0, NULL, 'pending', 'ORD_159096394', NULL, NULL, 'pending', NULL, NULL, '2025-10-27 16:05:20'),
(33, 14, 7, '2025-11-07', '2025-11-09', 1, 0, NULL, 'booked', 'ORD_14457951', NULL, NULL, 'pending', NULL, 1, '2025-11-06 12:39:32'),
(34, 16, 3, '2025-11-13', '2025-11-16', 0, 1, '2025-11-10 08:44:33', 'cancelled', 'ORD_16110128', NULL, NULL, 'pending', NULL, NULL, '2025-11-10 08:42:07'),
(35, 16, 7, '2025-11-13', '2025-11-14', 1, 0, NULL, 'booked', 'ORD_169408042', NULL, NULL, 'pending', NULL, 0, '2025-11-10 08:45:26'),
(36, 12, 7, '2025-11-12', '2025-11-13', 1, 0, NULL, 'booked', 'ORD_125094493', NULL, NULL, 'pending', NULL, 0, '2025-11-11 14:51:08'),
(37, 12, 7, '2025-11-13', '2025-11-16', 1, 0, NULL, 'booked', 'ORD_121308187', NULL, NULL, 'pending', NULL, 0, '2025-11-11 14:52:03'),
(38, 12, 7, '2025-11-13', '2025-11-22', 1, 0, NULL, 'booked', 'ORD_12355850', NULL, NULL, 'pending', NULL, 0, '2025-11-11 14:52:23'),
(39, 12, 1, '2025-11-14', '2025-11-19', 1, 0, NULL, 'booked', 'ORD_124516772', NULL, NULL, 'pending', NULL, 0, '2025-11-11 14:55:14'),
(40, 12, 1, '2025-11-19', '2025-11-23', 1, 0, NULL, 'booked', 'ORD_121861564', NULL, NULL, 'pending', NULL, 0, '2025-11-11 14:55:46'),
(41, 12, 5, '2025-11-12', '2025-11-14', 1, 0, NULL, 'booked', 'ORD_126763420', NULL, NULL, 'pending', NULL, 0, '2025-11-11 15:03:13');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carousel`
--

CREATE TABLE `carousel` (
  `sr_no` int(11) NOT NULL,
  `image` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `carousel`
--

INSERT INTO `carousel` (`sr_no`, `image`) VALUES
(4, '1.png'),
(5, '2.png'),
(19, 'Phoi-canh_636380426831654370 (1).jpg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contact_details`
--

CREATE TABLE `contact_details` (
  `sr_no` int(11) NOT NULL,
  `address` varchar(50) NOT NULL,
  `gmap` varchar(100) NOT NULL,
  `pn1` bigint(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fb` varchar(100) NOT NULL,
  `insta` varchar(100) NOT NULL,
  `tw` varchar(100) NOT NULL,
  `iframe` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `contact_details`
--

INSERT INTO `contact_details` (`sr_no`, `address`, `gmap`, `pn1`, `email`, `fb`, `insta`, `tw`, `iframe`) VALUES
(1, 'An Trường, Càng Long, Trà Vinh', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3917.157884165833!2d106.33733957589317!3d9.934', 823521928, '110122113@st.tvu.edu.vn', 'https://www.facebook.com/share/1J9NezCar9/?mibextid=wwXIfr', 'https://www.instagram.com/pmm2311?igsh=cWY3YmFrdnBvajNt&utm_source=qr', '', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3917.157884165833!2d106.33733957589317!3d9.934397774158645!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a028df3db49bcb%3A0xc7b070d16d2e2f54!2zVHLGsOG7nW5nIMSQw6BpIGjhu41jIFRy4bqhaSBWaW5o!5e0!3m2!1svi!2svi!4v1734444444444!5m2!1svi!2svi');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `facilities`
--

CREATE TABLE `facilities` (
  `id` int(11) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  `icon_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `facilities`
--

INSERT INTO `facilities` (`id`, `icon`, `name`, `description`, `icon_path`) VALUES
(61, 'IMG_43553.svg', 'Wi-Fi', 'Kết nối Internet tốc độ cao, miễn phí trong toàn bộ khách sạn, giúp bạn dễ dàng làm việc hoặc giải trí trực tuyến.', NULL),
(62, 'IMG_41622.svg', 'Truyền Hình', 'TV màn hình phẳng với đa dạng kênh giải trí trong nước và quốc tế, đáp ứng nhu cầu thư giãn của khách hàng.', NULL),
(63, 'IMG_47816.svg', 'Spa', 'Dịch vụ spa chuyên nghiệp với liệu trình thư giãn, chăm sóc cơ thể và phục hồi sức khỏe.', NULL),
(64, 'IMG_47816.svg', 'Máy Sưởi', 'Hệ thống sưởi ấm chất lượng cao, giữ không gian ấm áp, đặc biệt phù hợp vào những ngày lạnh giá.', NULL),
(65, 'IMG_49949.svg', 'Máy Lạnh', 'Hệ thống điều hòa không khí hiện đại, giúp không gian phòng luôn mát mẻ và dễ chịu.', NULL),
(66, 'water-heater.png', 'Máy Nước Nóng', 'Máy nước nóng tiện lợi, cung cấp nước nóng tức thì, đảm bảo sự thoải mái khi sử dụng phòng tắm.', NULL),
(67, 'hairdryer.png', 'Máy Sấy Tóc', 'Dễ dàng sấy tóc sau khi tắm, tiện lợi cho du khách.', NULL),
(68, 'toiletries.png', 'Đồ Vệ Sinh Cá Nhân', 'Được chuẩn bị sạch sẽ mỗi ngày, bao gồm khăn, xà phòng, và bàn chải.', NULL),
(69, 'minibar.png', 'Minibar', 'Tủ lạnh nhỏ trong phòng, phục vụ đồ uống và snack tiện lợi.', NULL),
(70, 'kettle.png', 'Ấm Đun Nước', 'Ấm đun nước siêu tốc giúp pha trà, cà phê hoặc mì ly nhanh chóng.', NULL),
(71, 'working.png', 'Khu Làm Việc', 'Bàn làm việc riêng tư, thuận tiện cho công việc hoặc học tập.', NULL),
(72, 'wardrobe.png', 'Tủ Quần Áo', 'Tủ đựng đồ rộng rãi, giúp sắp xếp hành lý gọn gàng.', NULL),
(73, 'slippers.png', 'Dép Đi Trong Nhà', 'Dép mềm mại, đảm bảo vệ sinh và thoải mái khi di chuyển trong phòng.', NULL),
(74, 'full-length-mirror.png', 'Gương Toàn Thân', 'Gương soi toàn thân lớn, tiện lợi cho việc chuẩn bị trang phục.', NULL),
(75, 'iron.png', 'Bàn Ủi', 'Bàn ủi tiện dụng giúp quần áo luôn phẳng phiu, chỉnh chu cho khách lưu trú.', NULL),
(76, 'bar-counter.png', 'Quầy bar', 'Quầy bar sang trọng đa dạng đồ uống, phục vụ khách hàng thư giãn buổi tối.', NULL),
(77, 'golf-field.png', 'Sân golf', 'Sân golf gần khách sạn, khu vực giải trí cao cấp.', NULL),
(78, 'swimming pool.png', 'Hồ bơi', 'Hồ bơi ngoài trời, nước trong xanh, thích hợp để thư giãn hoặc bơi lội.', NULL),
(79, 'airport.png', 'Xe đưa đón sân bay', 'Dịch vụ đưa đón sân bay nhanh chóng, tiện lợi và an toàn.', NULL),
(80, '24-hour-service.png', 'Dịch vụ 24/24 giờ', 'Dịch vụ lễ tân 24/24, luôn sẵn sàng hỗ trợ mọi nhu cầu của khách hàng.', NULL),
(81, 'IMG_96423.svg', 'Máy Sưởi', 'Hệ thống sưởi ấm giúp khách hàng tận hưởng không gian ấm áp trong mùa lạnh.', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `features`
--

CREATE TABLE `features` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `features`
--

INSERT INTO `features` (`id`, `name`) VALUES
(13, 'Phòng Ngủ'),
(14, 'Ban Công'),
(15, 'Nhà Bếp'),
(17, 'Ghế Sofa'),
(18, 'Sân Thượng'),
(19, 'Sân Vườn Riêng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rating_review`
--

CREATE TABLE `rating_review` (
  `sr_no` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review` varchar(200) NOT NULL,
  `seen` int(11) NOT NULL DEFAULT 0,
  `datentime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `rating_review`
--

INSERT INTO `rating_review` (`sr_no`, `booking_id`, `room_id`, `user_id`, `rating`, `review`, `seen`, `datentime`) VALUES
(4, 21, 5, 2, 5, 'Dịch vụ tuyệt vời, không gian đẳng cấp và được trang bị đầy đủ tiện nghi hiện đại. Rất phù hợp cho những dịp đặc biệt hoặc nghỉ dưỡng cao cấp.', 1, '2022-08-20 00:22:25'),
(5, 22, 4, 5, 3, 'Chất lượng dịch vụ xuất sắc, phòng rộng rãi, đầy đủ tiện nghi. Không gian sang trọng và thoải mái, rất đáng giá cho kỳ nghỉ.', 1, '2022-08-20 00:22:30'),
(6, 1, 3, 6, 4, 'Tương tự như “Phòng Cơ bản”, nhưng một số chi tiết như ánh sáng hoặc nội thất cần được cải thiện để mang lại trải nghiệm tốt hơn.', 1, '2022-08-20 00:22:34'),
(8, 21, 5, 7, 5, 'Nhân viên phục vụ rất chuyên nghiệp, mang lại cảm giác thoải mái và đáng nhớ cho kỳ nghỉ.', 1, '2022-08-20 00:22:25'),
(9, 22, 3, 8, 4, 'Dịch vụ ổn định, phòng sạch sẽ và gọn gàng. Tuy nhiên, tiện nghi chỉ ở mức cơ bản, phù hợp cho những ai cần chỗ ở ngắn hạn.', 1, '2022-08-20 00:22:34'),
(10, 1, 6, 2, 5, 'Phòng đẳng cấp, dịch vụ chu đáo, không gian sang trọng. Tuy nhiên, giá thành hơi cao so với những gì nhận được.', 1, '2022-08-20 00:22:34'),
(12, 1, 3, 7, 5, 'Rất tốt, đỉnh nóc kịch trần, bay phấp pha phấp phới.\r\nHãy gửi voucher discount về cho tôi vì đã để lại bình luận tốt!', 1, '2024-12-01 11:33:41'),
(13, 23, 7, 14, 5, 'Xịn lắm', 1, '2025-10-26 17:37:42'),
(14, 33, 7, 14, 5, 'Mát, xạnh, đẹp', 1, '2025-11-06 12:40:05');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `area` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `adult` int(11) NOT NULL,
  `children` int(11) NOT NULL,
  `description` text NOT NULL,
  `map_link` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `da_xoa` tinyint(4) DEFAULT NULL,
  `removed` tinyint(1) NOT NULL DEFAULT 0,
  `availability` enum('available','unavailable') DEFAULT 'available',
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `area`, `price`, `quantity`, `adult`, `children`, `description`, `map_link`, `status`, `da_xoa`, `removed`, `availability`, `image`) VALUES
(1, 'Phòng Cơ Bản 1', '34', 800000, 56, 2, 1, 'Phòng đơn giản, phù hợp với những khách hàng cần chỗ nghỉ ngắn hạn. Được trang bị các tiện nghi cơ bản như giường thoải mái, bàn làm việc nhỏ, và Wi-Fi miễn phí.', NULL, 1, NULL, 0, 'available', NULL),
(2, 'Phòng Cơ Bản 2', '40', 1000000, 30, 2, 1, 'Nâng cấp nhẹ so với Phòng Cơ Bản 1, mang đến không gian rộng rãi hơn và thêm các tiện ích như TV màn hình phẳng và minibar.', NULL, 1, NULL, 1, 'available', NULL),
(3, 'Phòng Cơ Bản 3', '60', 1200000, 20, 4, 2, 'Phòng cơ bản cao cấp hơn với thiết kế hiện đại, ban công nhỏ hoặc cửa sổ lớn có view thành phố, tạo cảm giác thoáng đãng và thư giãn.', NULL, 1, NULL, 0, 'available', NULL),
(4, 'Phòng Cao Cấp', '50', 2000000, 15, 2, 1, 'Không gian rộng rãi với thiết kế sang trọng, phù hợp cho các kỳ nghỉ dài ngày. Được trang bị nội thất cao cấp, phòng tắm riêng với bồn tắm, và các tiện ích như máy pha cà phê và két an toàn.', NULL, 1, NULL, 0, 'available', NULL),
(5, 'Phòng Sang Trọng', '50', 3500000, 15, 2, 1, 'Thiết kế đẳng cấp với nội thất tinh tế và các tiện nghi hiện đại. Phòng có không gian sống riêng biệt, ban công hoặc cửa sổ lớn với view đẹp, mang lại trải nghiệm thư giãn hoàn hảo.', NULL, 1, NULL, 0, 'available', NULL),
(6, 'Phòng Tổng Thống', '120', 10000000, 5, 6, 3, 'Hạng phòng cao cấp nhất, mang đến sự xa hoa với không gian rộng lớn, nội thất tinh xảo và dịch vụ đặc biệt. Bao gồm phòng khách riêng, phòng ngủ lớn, phòng tắm xa hoa và nhiều tiện nghi VIP như quản gia riêng.', NULL, 1, NULL, 0, 'available', NULL),
(7, 'RUBY HOTEL Vinh Long', '18', 500000, 17, 2, 1, 'Phòng ốc tuy không gian hơi hẹp nhưng sạch sẽ dễ chịu\r\n\r\nKhách sạn ở đây rất thoải mái và yên tĩnh.', '\n', 1, NULL, 0, 'available', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `room_facilities`
--

CREATE TABLE `room_facilities` (
  `sr_no` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `facilities_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `room_facilities`
--

INSERT INTO `room_facilities` (`sr_no`, `room_id`, `facilities_id`) VALUES
(189, 7, 61),
(190, 7, 62),
(191, 7, 64),
(192, 7, 65),
(193, 7, 66),
(194, 7, 67),
(195, 7, 68),
(196, 7, 69),
(197, 7, 70),
(198, 7, 72),
(199, 7, 73),
(200, 7, 74),
(201, 7, 75),
(202, 7, 77),
(203, 7, 78),
(204, 7, 80);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `room_features`
--

CREATE TABLE `room_features` (
  `sr_no` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `features_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `room_features`
--

INSERT INTO `room_features` (`sr_no`, `room_id`, `features_id`) VALUES
(31, 3, 13),
(32, 3, 14),
(33, 3, 17),
(34, 4, 13),
(35, 4, 14),
(36, 4, 15),
(37, 5, 13),
(38, 5, 14),
(39, 5, 15),
(40, 6, 13),
(41, 6, 14),
(42, 6, 15),
(67, 7, 13),
(68, 7, 14),
(69, 7, 17);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `room_images`
--

CREATE TABLE `room_images` (
  `sr_no` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `image` varchar(150) NOT NULL,
  `thumb` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `room_images`
--

INSERT INTO `room_images` (`sr_no`, `room_id`, `image`, `thumb`) VALUES
(15, 3, 'IMG_39782.png', 0),
(16, 3, 'IMG_65019.png', 0),
(17, 4, 'IMG_44867.png', 0),
(18, 4, 'IMG_78809.png', 1),
(19, 4, 'IMG_11892.png', 0),
(21, 5, 'IMG_17474.png', 0),
(22, 5, 'IMG_42663.png', 1),
(23, 5, 'IMG_70583.png', 0),
(24, 6, 'IMG_67761.png', 0),
(25, 6, 'IMG_69824.png', 1),
(29, 7, 'rubi1.jpg', 1),
(30, 7, 'rubi2.jpg', 0),
(31, 7, 'rubi3.jpg', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `settings`
--

CREATE TABLE `settings` (
  `sr_no` int(11) NOT NULL,
  `site_title` varchar(50) CHARACTER SET utf8 COLLATE utf8_vietnamese_ci NOT NULL,
  `site_about` varchar(250) CHARACTER SET utf8 COLLATE utf8_vietnamese_ci NOT NULL,
  `shutdown` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `settings`
--

INSERT INTO `settings` (`sr_no`, `site_title`, `site_about`, `shutdown`) VALUES
(1, 'Vĩnh Long Hotel', 'Trải nghiệm dịch vụ đặt phòng khách sạn trực tuyến nhanh chóng, tiện lợi với đa dạng lựa chọn tại các điểm đến du lịch nổi tiếng trên khắp Việt Nam. Hãy để hành trình của bạn bắt đầu chỉ với vài cú nhấp chuột!', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_cred`
--

CREATE TABLE `user_cred` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `address` varchar(120) NOT NULL,
  `phonenum` varchar(100) NOT NULL,
  `pincode` int(11) NOT NULL,
  `dob` date NOT NULL,
  `profile` varchar(100) NOT NULL DEFAULT 'chill-guy.png',
  `password` varchar(200) NOT NULL,
  `is_verified` int(11) NOT NULL DEFAULT 0,
  `token` varchar(200) DEFAULT NULL,
  `t_expire` date DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `datentime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user_cred`
--

INSERT INTO `user_cred` (`id`, `name`, `email`, `address`, `phonenum`, `pincode`, `dob`, `profile`, `password`, `is_verified`, `token`, `t_expire`, `status`, `datentime`) VALUES
(2, 'Trung', 'trung@gmail.com', 'ad', '123', 123324, '2022-06-12', 'chill-guy2.png', '123456', 1, NULL, NULL, 1, '2024-11-30 16:05:59'),
(12, 'Phạm Minh Mẫn', 'phamminhman719@gmail.com', 'Càng Long', '0823521928', 12345, '2003-11-23', 'IMG_16104.jpg', '123123', 1, 'abc123xyz456', NULL, 1, '2025-10-16 22:40:53'),
(13, 'Trinh', 'phamminhman917@gmail.com', 'tt', '1231231231', 123123, '2006-06-07', 'IMG_49938.jpg', '$2y$10$xvahTd07YwS5T4gPlBRBxumkPTQZHleZeREXVS0nTZz/bGhel9xuS', 0, NULL, NULL, 1, '2025-10-21 00:59:51'),
(14, 'Mẫn', 'phamminhman999@gmail.com', 'Trà Vinh Vĩnh Long', '08235218288', 123123, '2003-11-23', 'IMG_45827.jpg', '123123', 1, NULL, NULL, 1, '2025-10-26 11:08:21'),
(15, 'Trinh', 'phamminhman888@gmail.com', 'Trà Vinh Bến Tre', '12312312113', 1232131123, '2021-10-27', 'IMG_84193.jpg', '123123', 0, NULL, NULL, 1, '2025-10-27 12:06:40'),
(16, 'Nguyễn Hoàng Kha', 'cfvn1@gmail.com', 'Ấp 3', '0987988167', 332, '2004-06-16', 'IMG_46947.jpg', 'cf1', 0, NULL, NULL, 1, '2025-11-10 08:38:57');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_queries`
--

CREATE TABLE `user_queries` (
  `sr_no` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` varchar(500) NOT NULL,
  `datentime` datetime NOT NULL DEFAULT current_timestamp(),
  `seen` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user_queries`
--

INSERT INTO `user_queries` (`sr_no`, `name`, `email`, `subject`, `message`, `datentime`, `seen`) VALUES
(11, 'Hung', 'hung@gmail.com', 'Tôi muốn đặt phòng', 'Cần hỗ trợ đặt phòng Tổng Thống.', '2024-11-29 00:00:00', 1),
(13, 'Trung', 'trung@gmail.com', 'Yêu cầu hoàn tiền', 'Cần hỗ trợ hoàn tiền do huỷ đột xuất.', '2024-12-06 10:10:48', 1),
(15, 'Phạm Minh Mẫn', 'phamminhman719@gmail.com', 'Phòng', 'Cần thêm dạng', '2025-10-17 01:50:02', 1),
(17, 'Phạm Minh Mẫn', 'phamminhman719@gmail.com', 'Phòng', 'dd', '2025-10-17 13:47:38', 1),
(18, 'Phạm Minh Mẫn', 'phamminhman719@gmail.com', 'Phòng', 'dd', '2025-10-17 13:55:13', 1),
(20, 'Phạm Minh Mẫn', 'ytbfam.n012@gmail.com', 'kkkk', 'đ', '2025-10-18 13:02:16', 1),
(21, 'Phạm Minh Mẫn', 'phamminhman719@gmail.com', 'Phòng', 'lll', '2025-10-18 13:51:24', 1),
(22, 'Phạm Minh Mẫn', 'phamminhman719@gmail.com', 'Phòng', '5467', '2025-10-18 14:10:30', 1),
(23, 'Phạm Minh Mẫn', 'phamminhman719@gmail.com', 'Phòng', 'dsvdsv', '2025-10-20 14:25:27', 1),
(24, 'Phạm Minh Mẫn', 'phamminhman719@gmail.com', 'Phòng', 'dsvdsv', '2025-10-20 14:33:05', 1),
(25, 'Tuấn', 'ngocnhutmmo3@gmail.com', 'Yêu cầu hỗ trợ', 'Alo', '2025-10-21 01:07:24', 1),
(26, 'Phạm Minh Mẫn', 'phamminhman999@gmail.com', 'Yêu cầu hỗ trợ', '123', '2025-10-27 11:50:12', 1),
(27, 'Phạm Minh Mẫn', 'phamminhman719@gmail.com', 'Yêu cầu hỗ trợ', 'qưdqwd', '2025-10-30 14:52:14', 1),
(28, 'Tùng', 'khoiphuctaikhoan23@gmail.com', 'Yêu cầu hỗ trợ', 'Hoàn tiền', '2025-11-11 14:38:14', 1),
(29, 'Tùng', 'khoiphuctaikhoan23@gmail.com', 'Yêu cầu hỗ trợ', 'Hoàn tiền', '2025-11-11 14:50:36', 1),
(30, 'Tùng', 'khoiphuctaikhoan23@gmail.com', 'Yêu cầu hỗ trợ', 'Hoàn tiền', '2025-11-11 14:50:39', 1);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin_cred`
--
ALTER TABLE `admin_cred`
  ADD PRIMARY KEY (`sr_no`);

--
-- Chỉ mục cho bảng `booking_details`
--
ALTER TABLE `booking_details`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Chỉ mục cho bảng `booking_order`
--
ALTER TABLE `booking_order`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Chỉ mục cho bảng `carousel`
--
ALTER TABLE `carousel`
  ADD PRIMARY KEY (`sr_no`);

--
-- Chỉ mục cho bảng `contact_details`
--
ALTER TABLE `contact_details`
  ADD PRIMARY KEY (`sr_no`);

--
-- Chỉ mục cho bảng `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `features`
--
ALTER TABLE `features`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `rating_review`
--
ALTER TABLE `rating_review`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `room_facilities`
--
ALTER TABLE `room_facilities`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `facilities id` (`facilities_id`),
  ADD KEY `room id` (`room_id`);

--
-- Chỉ mục cho bảng `room_features`
--
ALTER TABLE `room_features`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `features id` (`features_id`),
  ADD KEY `rm id` (`room_id`);

--
-- Chỉ mục cho bảng `room_images`
--
ALTER TABLE `room_images`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `room_id` (`room_id`);

--
-- Chỉ mục cho bảng `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`sr_no`);

--
-- Chỉ mục cho bảng `user_cred`
--
ALTER TABLE `user_cred`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `user_queries`
--
ALTER TABLE `user_queries`
  ADD PRIMARY KEY (`sr_no`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin_cred`
--
ALTER TABLE `admin_cred`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `booking_details`
--
ALTER TABLE `booking_details`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `booking_order`
--
ALTER TABLE `booking_order`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT cho bảng `carousel`
--
ALTER TABLE `carousel`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `contact_details`
--
ALTER TABLE `contact_details`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT cho bảng `features`
--
ALTER TABLE `features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `rating_review`
--
ALTER TABLE `rating_review`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `room_facilities`
--
ALTER TABLE `room_facilities`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=205;

--
-- AUTO_INCREMENT cho bảng `room_features`
--
ALTER TABLE `room_features`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT cho bảng `room_images`
--
ALTER TABLE `room_images`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT cho bảng `settings`
--
ALTER TABLE `settings`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `user_cred`
--
ALTER TABLE `user_cred`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `user_queries`
--
ALTER TABLE `user_queries`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `booking_details`
--
ALTER TABLE `booking_details`
  ADD CONSTRAINT `booking_details_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking_order` (`booking_id`);

--
-- Các ràng buộc cho bảng `booking_order`
--
ALTER TABLE `booking_order`
  ADD CONSTRAINT `booking_order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_cred` (`id`),
  ADD CONSTRAINT `booking_order_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Các ràng buộc cho bảng `rating_review`
--
ALTER TABLE `rating_review`
  ADD CONSTRAINT `rating_review_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking_order` (`booking_id`),
  ADD CONSTRAINT `rating_review_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `rating_review_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user_cred` (`id`);

--
-- Các ràng buộc cho bảng `room_facilities`
--
ALTER TABLE `room_facilities`
  ADD CONSTRAINT `facilities id` FOREIGN KEY (`facilities_id`) REFERENCES `facilities` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `room id` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON UPDATE NO ACTION;

--
-- Các ràng buộc cho bảng `room_features`
--
ALTER TABLE `room_features`
  ADD CONSTRAINT `features id` FOREIGN KEY (`features_id`) REFERENCES `features` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `rm id` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON UPDATE NO ACTION;

--
-- Các ràng buộc cho bảng `room_images`
--
ALTER TABLE `room_images`
  ADD CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);
COMMIT;

