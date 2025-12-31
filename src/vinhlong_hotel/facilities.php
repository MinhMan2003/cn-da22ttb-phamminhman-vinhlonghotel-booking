<!DOCTYPE html>
<html lang="<?php echo $_COOKIE['lang'] ?? 'vi'; ?>">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - <?php echo ($_COOKIE['lang'] ?? 'vi') === 'en' ? 'Facilities' : 'Tiện ích'; ?></title>

   <!-- Modern CSS for Facilities Page -->
  <style>
    /* Modern Facilities Page Styles */
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
    }
    
    /* Header Section */
    .facilities-header {
      margin-bottom: 4rem;
      padding: 3rem 0;
      text-align: center;
      animation: fadeInDown 0.6s ease-out;
    }
    
    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translateY(-30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .facilities-header h2 {
      font-size: 3rem;
      font-weight: 800;
      color: #1a202c;
      margin-bottom: 1rem;
      letter-spacing: 0.02em;
      text-transform: uppercase;
    }
    
    .h-line {
      width: 100px;
      height: 4px;
      background: linear-gradient(90deg, #a78bfa 0%, #ec4899 100%);
      border-radius: 2px;
      margin: 1.5rem auto;
    }
    
    .facilities-header p {
      max-width: 900px;
      margin: 0 auto;
      line-height: 1.9;
      color: #4a5568;
      font-size: 1.1rem;
      font-weight: 400;
      text-align: center;
    }
    
    .facilities-header .text-uppercase.text-muted {
      color: #6b7280 !important;
      font-weight: 500;
      letter-spacing: 2px;
    }
    
    /* Modern Facility Card */
    .facility-card {
      background: #ffffff;
      border: none;
      border-radius: 20px;
      padding: 2rem;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      height: 100%;
      display: flex;
      flex-direction: column;
    }
    
    .facility-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 5px;
      background: #1f2937;
      transform: scaleX(0);
      transition: transform 0.4s;
    }
    
    .facility-card:hover::before {
      transform: scaleX(1);
    }
    
    .facility-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 32px rgba(31, 41, 55, 0.15);
      border-color: transparent;
    }
    
    /* Icon Wrapper */
    .facility-icon-wrapper {
      width: 64px;
      height: 64px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
      border-radius: 16px;
      margin-bottom: 1rem;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 4px 12px rgba(31, 41, 55, 0.1);
      position: relative;
      overflow: hidden;
    }
    
    .facility-icon-wrapper::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);
      opacity: 0;
      transition: opacity 0.3s;
    }
    
    .facility-card:hover .facility-icon-wrapper {
      transform: scale(1.1) rotate(5deg);
      box-shadow: 0 8px 20px rgba(31, 41, 55, 0.2);
      background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
    }
    
    .facility-card:hover .facility-icon-wrapper::before {
      opacity: 1;
    }
    
    .facility-icon-wrapper img {
      width: 40px;
      height: 40px;
      object-fit: contain;
      position: relative;
      z-index: 1;
      transition: transform 0.3s;
    }
    
    .facility-card:hover .facility-icon-wrapper img {
      transform: scale(1.15);
    }
    
    /* Card Content */
    .facility-card h5 {
      font-size: 1.25rem;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 0.75rem;
      transition: color 0.3s;
    }
    
    .facility-card:hover h5 {
      color: #1f2937;
    }
    
    .facility-card p {
      color: #6b7280;
      line-height: 1.7;
      margin: 0;
      flex-grow: 1;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .facilities-header h2 {
        font-size: 2rem;
      }
      
      .facility-card {
        padding: 1.5rem;
      }
      
      .facility-icon-wrapper {
        width: 56px;
        height: 56px;
      }
      
      .facility-icon-wrapper img {
        width: 36px;
        height: 36px;
      }
    }
    
    /* Fade in animation */
    .facility-card {
      opacity: 0;
      transform: translateY(20px);
      animation: fadeInUp 0.6s ease forwards;
    }
    
    .facility-card:nth-child(1) { animation-delay: 0.1s; }
    .facility-card:nth-child(2) { animation-delay: 0.2s; }
    .facility-card:nth-child(3) { animation-delay: 0.3s; }
    .facility-card:nth-child(4) { animation-delay: 0.4s; }
    .facility-card:nth-child(5) { animation-delay: 0.5s; }
    .facility-card:nth-child(6) { animation-delay: 0.6s; }
    .facility-card:nth-child(7) { animation-delay: 0.7s; }
    .facility-card:nth-child(8) { animation-delay: 0.8s; }
    .facility-card:nth-child(9) { animation-delay: 0.9s; }
    
    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

   <!-- Tiêu đề trang -->
  <div class="my-5 px-4 facilities-header">
    <div class="text-center">
      <p class="text-uppercase text-muted small mb-2" style="letter-spacing: 2px;" data-i18n="facilities.subtitle">TIỆN NGHI & DỊCH VỤ</p>
      <h2 class="fw-bold text-center" data-i18n="nav.facilities">TIỆN ÍCH</h2>
      <div class="h-line mx-auto mb-4"></div>
    </div>
    <!-- Đoạn mô tả giới thiệu chung về tiện ích -->
    <p class="text-center mt-4" data-i18n="facilities.intro" data-i18n-html="true">
        Khách sạn Vĩnh Long Hotel mang đến hệ thống tiện nghi hiện đại và cao cấp bậc nhất, đáp ứng mọi nhu cầu nghỉ dưỡng của quý khách. <br>
        Từ Wi-Fi tốc độ cao, máy lạnh, truyền hình và máy nước nóng, đến minibar, ấm đun nước, bàn làm việc, tủ quần áo, gương toàn thân hay bàn ủi – tất cả đều được trang bị chu đáo nhằm mang lại sự thoải mái tuyệt đối. <br>
        Quý khách có thể thư giãn tại spa chuyên nghiệp, tận hưởng không gian ban công thoáng mát, hay đắm mình trong bồn tắm riêng sang trọng. <br>
        Vĩnh Long Hotel tự hào mang đến trải nghiệm lưu trú trọn vẹn, tiện nghi và đẳng cấp, giúp quý khách cảm nhận sự khác biệt trong từng khoảnh khắc nghỉ ngơi.
    </p>
  </div>

 <!-- Nội dung danh sách tiện ích -->
  <div class="container mb-5">
    <div class="row g-4">
      <?php 
       /*
          Lấy toàn bộ danh sách tiện ích từ CSDL
          - Bảng: facilities
          - Mỗi dòng gồm: id, name, description, icon
          - Đường dẫn icon được định nghĩa sẵn trong FACILITIES_IMG_PATH (inc/essentials.php)
       */
        $res = selectAll('facilities');
        $path = FACILITIES_IMG_PATH;
        
        // Lấy ngôn ngữ hiện tại từ cookie
        $current_lang = $_COOKIE['lang'] ?? 'vi';
        
        // Hàm dịch đơn giản
        function t_facilities($key, $lang = 'vi') {
            $translations = [
                'vi' => [
                    'facilities.wifi' => 'Wi-Fi',
                    'facilities.tv' => 'Truyền Hình',
                    'facilities.spa' => 'Spa',
                    'facilities.heater' => 'Máy Sưởi',
                    'facilities.airConditioner' => 'Máy Lạnh',
                    'facilities.waterHeater' => 'Máy Nước Nóng',
                    'facilities.hairDryer' => 'Máy Sấy Tóc',
                    'facilities.personalHygiene' => 'Đồ Vệ Sinh Cá Nhân',
                    'facilities.minibar' => 'Minibar',
                    'facilities.kettle' => 'Ấm Đun Nước',
                    'facilities.workspace' => 'Khu Làm Việc',
                    'facilities.wardrobe' => 'Tủ Quần Áo',
                    'facilities.slippers' => 'Dép Đi Trong Nhà',
                    'facilities.fullMirror' => 'Gương Toàn Thân',
                    'facilities.iron' => 'Bàn Ủi',
                    'facilities.bar' => 'Quầy bar',
                    'facilities.golfCourse' => 'Sân golf',
                    'facilities.swimmingPool' => 'Hồ bơi',
                    'facilities.airportShuttle' => 'Xe đưa đón sân bay',
                    'facilities.service24' => 'Dịch vụ 24/24 giờ',
                ],
                'en' => [
                    'facilities.wifi' => 'Wi-Fi',
                    'facilities.tv' => 'Television',
                    'facilities.spa' => 'Spa',
                    'facilities.heater' => 'Heater',
                    'facilities.airConditioner' => 'Air Conditioner',
                    'facilities.waterHeater' => 'Water Heater',
                    'facilities.hairDryer' => 'Hair Dryer',
                    'facilities.personalHygiene' => 'Personal Hygiene Items',
                    'facilities.minibar' => 'Minibar',
                    'facilities.kettle' => 'Kettle',
                    'facilities.workspace' => 'Workspace',
                    'facilities.wardrobe' => 'Wardrobe',
                    'facilities.slippers' => 'Slippers',
                    'facilities.fullMirror' => 'Full-length Mirror',
                    'facilities.iron' => 'Iron',
                    'facilities.bar' => 'Bar Counter',
                    'facilities.golfCourse' => 'Golf Course',
                    'facilities.swimmingPool' => 'Swimming Pool',
                    'facilities.airportShuttle' => 'Airport Shuttle',
                    'facilities.service24' => '24/24 Hour Service',
                ]
            ];
            return $translations[$lang][$key] ?? $key;
        }
        
        // Map các facility names phổ biến để dịch (bao gồm cả tiếng Việt và tiếng Anh)
        $facility_map = [
            // Tiếng Việt
            'Wi-Fi' => 'facilities.wifi',
            'Truyền Hình' => 'facilities.tv',
            'Spa' => 'facilities.spa',
            'Máy Sưởi' => 'facilities.heater',
            'Máy Lạnh' => 'facilities.airConditioner',
            'Máy Nước Nóng' => 'facilities.waterHeater',
            'Máy Sấy Tóc' => 'facilities.hairDryer',
            'Đồ Vệ Sinh Cá Nhân' => 'facilities.personalHygiene',
            'Minibar' => 'facilities.minibar',
            'Ấm Đun Nước' => 'facilities.kettle',
            'Khu Làm Việc' => 'facilities.workspace',
            'Tủ Quần Áo' => 'facilities.wardrobe',
            'Dép Đi Trong Nhà' => 'facilities.slippers',
            'Gương Toàn Thân' => 'facilities.fullMirror',
            'Bàn Ủi' => 'facilities.iron',
            'Quầy bar' => 'facilities.bar',
            'Sân golf' => 'facilities.golfCourse',
            'Hồ bơi' => 'facilities.swimmingPool',
            'Xe đưa đón sân bay' => 'facilities.airportShuttle',
            'Dịch vụ 24/24 giờ' => 'facilities.service24',
            // Tiếng Anh (để map ngược lại)
            'Heater' => 'facilities.heater',
            'Air Conditioner' => 'facilities.airConditioner',
            'Water Heater' => 'facilities.waterHeater',
            'Hair Dryer' => 'facilities.hairDryer',
            'Personal Hygiene' => 'facilities.personalHygiene',
            'Personal Hygiene Items' => 'facilities.personalHygiene',
            'Kettle' => 'facilities.kettle',
            'Workspace' => 'facilities.workspace',
            'Wardrobe' => 'facilities.wardrobe',
            'Slippers' => 'facilities.slippers',
            'Full-length Mirror' => 'facilities.fullMirror',
            'Iron' => 'facilities.iron',
            'Bar Counter' => 'facilities.bar',
            'Golf Course' => 'facilities.golfCourse',
            'Swimming Pool' => 'facilities.swimmingPool',
            'Airport Shuttle' => 'facilities.airportShuttle',
            '24/24 Hour Service' => 'facilities.service24',
            'Television' => 'facilities.tv',
        ];

        // Map descriptions để dịch - Tất cả descriptions từ database
        $description_map = [
            'Kết nối Internet tốc độ cao, miễn phí trong toàn bộ khách sạn, giúp bạn dễ dàng làm việc hoặc giải trí trực tuyến.' => [
                'vi' => 'Kết nối Internet tốc độ cao, miễn phí trong toàn bộ khách sạn, giúp bạn dễ dàng làm việc hoặc giải trí trực tuyến.',
                'en' => 'High-speed Internet connection, free throughout the hotel, helping you easily work or entertain online.'
            ],
            'TV màn hình phẳng với đa dạng kênh giải trí trong nước và quốc tế, đáp ứng nhu cầu thư giãn của khách hàng.' => [
                'vi' => 'TV màn hình phẳng với đa dạng kênh giải trí trong nước và quốc tế, đáp ứng nhu cầu thư giãn của khách hàng.',
                'en' => 'Flat-screen TV with a variety of domestic and international entertainment channels, meeting customers\' relaxation needs.'
            ],
            'Dịch vụ spa chuyên nghiệp với liệu trình thư giãn, chăm sóc cơ thể và phục hồi sức khỏe.' => [
                'vi' => 'Dịch vụ spa chuyên nghiệp với liệu trình thư giãn, chăm sóc cơ thể và phục hồi sức khỏe.',
                'en' => 'Professional spa service with relaxing treatments, body care, and health recovery.'
            ],
            'Hệ thống sưởi ấm chất lượng cao, giữ không gian ấm áp, đặc biệt phù hợp vào những ngày lạnh giá.' => [
                'vi' => 'Hệ thống sưởi ấm chất lượng cao, giữ không gian ấm áp, đặc biệt phù hợp vào những ngày lạnh giá.',
                'en' => 'High-quality heating system, keeping the space warm, especially suitable for cold days.'
            ],
            'Hệ thống sưởi ấm giúp khách hàng tận hưởng không gian ấm áp trong mùa lạnh.' => [
                'vi' => 'Hệ thống sưởi ấm giúp khách hàng tận hưởng không gian ấm áp trong mùa lạnh.',
                'en' => 'The heating system helps guests enjoy a warm space in the cold season.'
            ],
            'Hệ thống điều hòa không khí hiện đại, giúp không gian phòng luôn mát mẻ và dễ chịu.' => [
                'vi' => 'Hệ thống điều hòa không khí hiện đại, giúp không gian phòng luôn mát mẻ và dễ chịu.',
                'en' => 'Modern air conditioning system, keeping the room space always cool and pleasant.'
            ],
            'Máy lạnh hiện đại, điều chỉnh nhiệt độ linh hoạt, mang lại không gian mát mẻ và thoải mái.' => [
                'vi' => 'Máy lạnh hiện đại, điều chỉnh nhiệt độ linh hoạt, mang lại không gian mát mẻ và thoải mái.',
                'en' => 'Modern air conditioner with flexible temperature control, providing a cool and comfortable space.'
            ],
            'Máy nước nóng tiện lợi, cung cấp nước nóng tức thì, đảm bảo sự thoải mái khi sử dụng phòng tắm.' => [
                'vi' => 'Máy nước nóng tiện lợi, cung cấp nước nóng tức thì, đảm bảo sự thoải mái khi sử dụng phòng tắm.',
                'en' => 'Convenient water heater, providing instant hot water, ensuring comfort when using the bathroom.'
            ],
            'Máy nước nóng đảm bảo nguồn nước ấm ổn định cho việc tắm rửa và sinh hoạt hàng ngày.' => [
                'vi' => 'Máy nước nóng đảm bảo nguồn nước ấm ổn định cho việc tắm rửa và sinh hoạt hàng ngày.',
                'en' => 'Water heater ensures stable warm water for bathing and daily activities.'
            ],
            'Dễ dàng sấy tóc sau khi tắm, tiện lợi cho du khách.' => [
                'vi' => 'Dễ dàng sấy tóc sau khi tắm, tiện lợi cho du khách.',
                'en' => 'Easy to dry hair after bathing, convenient for guests.'
            ],
            'Máy sấy tóc tiện lợi, giúp bạn làm khô tóc nhanh chóng sau khi tắm.' => [
                'vi' => 'Máy sấy tóc tiện lợi, giúp bạn làm khô tóc nhanh chóng sau khi tắm.',
                'en' => 'Convenient hair dryer, helping you dry your hair quickly after bathing.'
            ],
            'Được chuẩn bị sạch sẽ mỗi ngày, bao gồm khăn, xà phòng, và bàn chải.' => [
                'vi' => 'Được chuẩn bị sạch sẽ mỗi ngày, bao gồm khăn, xà phòng, và bàn chải.',
                'en' => 'Prepared cleanly every day, including towels, soap, and toothbrush.'
            ],
            'Đầy đủ các vật dụng vệ sinh cá nhân như dầu gội, sữa tắm, kem đánh răng và khăn tắm.' => [
                'vi' => 'Đầy đủ các vật dụng vệ sinh cá nhân như dầu gội, sữa tắm, kem đánh răng và khăn tắm.',
                'en' => 'Full personal hygiene items such as shampoo, body wash, toothpaste and towels.'
            ],
            'Tủ lạnh nhỏ trong phòng, phục vụ đồ uống và snack tiện lợi.' => [
                'vi' => 'Tủ lạnh nhỏ trong phòng, phục vụ đồ uống và snack tiện lợi.',
                'en' => 'Small refrigerator in the room, serving drinks and snacks conveniently.'
            ],
            'Minibar được trang bị đầy đủ đồ uống và snack, phục vụ nhu cầu giải khát của khách hàng.' => [
                'vi' => 'Minibar được trang bị đầy đủ đồ uống và snack, phục vụ nhu cầu giải khát của khách hàng.',
                'en' => 'Minibar is fully equipped with drinks and snacks, serving customers\' refreshment needs.'
            ],
            'Ấm đun nước siêu tốc giúp pha trà, cà phê hoặc mì ly nhanh chóng.' => [
                'vi' => 'Ấm đun nước siêu tốc giúp pha trà, cà phê hoặc mì ly nhanh chóng.',
                'en' => 'Super-fast kettle helps make tea, coffee or instant noodles quickly.'
            ],
            'Ấm đun nước tiện lợi để pha trà, cà phê hoặc đồ uống nóng khác ngay trong phòng.' => [
                'vi' => 'Ấm đun nước tiện lợi để pha trà, cà phê hoặc đồ uống nóng khác ngay trong phòng.',
                'en' => 'Convenient kettle for making tea, coffee or other hot drinks right in the room.'
            ],
            'Bàn làm việc riêng tư, thuận tiện cho công việc hoặc học tập.' => [
                'vi' => 'Bàn làm việc riêng tư, thuận tiện cho công việc hoặc học tập.',
                'en' => 'Private work desk, convenient for work or study.'
            ],
            'Khu vực làm việc riêng với bàn và ghế thoải mái, phù hợp cho công việc hoặc học tập.' => [
                'vi' => 'Khu vực làm việc riêng với bàn và ghế thoải mái, phù hợp cho công việc hoặc học tập.',
                'en' => 'Private workspace with comfortable desk and chair, suitable for work or study.'
            ],
            'Tủ đựng đồ rộng rãi, giúp sắp xếp hành lý gọn gàng.' => [
                'vi' => 'Tủ đựng đồ rộng rãi, giúp sắp xếp hành lý gọn gàng.',
                'en' => 'Spacious storage closet, helping organize luggage neatly.'
            ],
            'Tủ quần áo rộng rãi để cất giữ đồ đạc cá nhân một cách gọn gàng và ngăn nắp.' => [
                'vi' => 'Tủ quần áo rộng rãi để cất giữ đồ đạc cá nhân một cách gọn gàng và ngăn nắp.',
                'en' => 'Spacious wardrobe to store personal belongings neatly and organized.'
            ],
            'Dép mềm mại, đảm bảo vệ sinh và thoải mái khi di chuyển trong phòng.' => [
                'vi' => 'Dép mềm mại, đảm bảo vệ sinh và thoải mái khi di chuyển trong phòng.',
                'en' => 'Soft slippers, ensuring hygiene and comfort when moving around the room.'
            ],
            'Dép đi trong nhà sạch sẽ và thoải mái, mang lại cảm giác như ở nhà.' => [
                'vi' => 'Dép đi trong nhà sạch sẽ và thoải mái, mang lại cảm giác như ở nhà.',
                'en' => 'Clean and comfortable slippers, giving you a home-like feeling.'
            ],
            'Gương soi toàn thân lớn, tiện lợi cho việc chuẩn bị trang phục.' => [
                'vi' => 'Gương soi toàn thân lớn, tiện lợi cho việc chuẩn bị trang phục.',
                'en' => 'Large full-length mirror, convenient for preparing outfits.'
            ],
            'Gương toàn thân giúp bạn kiểm tra trang phục và ngoại hình một cách dễ dàng.' => [
                'vi' => 'Gương toàn thân giúp bạn kiểm tra trang phục và ngoại hình một cách dễ dàng.',
                'en' => 'Full-length mirror helps you check your outfit and appearance easily.'
            ],
            'Bàn ủi tiện dụng giúp quần áo luôn phẳng phiu, chỉnh chu cho khách lưu trú.' => [
                'vi' => 'Bàn ủi tiện dụng giúp quần áo luôn phẳng phiu, chỉnh chu cho khách lưu trú.',
                'en' => 'Convenient ironing board helps clothes always stay smooth and neat for guests.'
            ],
            'Bàn ủi và bàn là để làm phẳng quần áo, đảm bảo bạn luôn chỉn chu trong mọi dịp.' => [
                'vi' => 'Bàn ủi và bàn là để làm phẳng quần áo, đảm bảo bạn luôn chỉn chu trong mọi dịp.',
                'en' => 'Iron and ironing board to smooth clothes, ensuring you are always neat on every occasion.'
            ],
            'Quầy bar sang trọng đa dạng đồ uống, phục vụ khách hàng thư giãn buổi tối.' => [
                'vi' => 'Quầy bar sang trọng đa dạng đồ uống, phục vụ khách hàng thư giãn buổi tối.',
                'en' => 'Luxurious bar with a variety of drinks, serving guests for evening relaxation.'
            ],
            'Sân golf gần khách sạn, khu vực giải trí cao cấp.' => [
                'vi' => 'Sân golf gần khách sạn, khu vực giải trí cao cấp.',
                'en' => 'Golf course near the hotel, a high-class entertainment area.'
            ],
            'Hồ bơi ngoài trời, nước trong xanh, thích hợp để thư giãn hoặc bơi lội.' => [
                'vi' => 'Hồ bơi ngoài trời, nước trong xanh, thích hợp để thư giãn hoặc bơi lội.',
                'en' => 'Outdoor swimming pool, clear blue water, suitable for relaxing or swimming.'
            ],
            'Dịch vụ đưa đón sân bay nhanh chóng, tiện lợi và an toàn.' => [
                'vi' => 'Dịch vụ đưa đón sân bay nhanh chóng, tiện lợi và an toàn.',
                'en' => 'Fast, convenient, and safe airport shuttle service.'
            ],
            'Dịch vụ lễ tân 24/24, luôn sẵn sàng hỗ trợ mọi nhu cầu của khách hàng.' => [
                'vi' => 'Dịch vụ lễ tân 24/24, luôn sẵn sàng hỗ trợ mọi nhu cầu của khách hàng.',
                'en' => '24/24 reception service, always ready to support all customer needs.'
            ],
        ];
        
        // Duyệt từng tiện ích và hiển thị ra giao diện
        while($row = mysqli_fetch_assoc($res)){
          $name_raw = $row['name'];
          $name = htmlspecialchars($name_raw, ENT_QUOTES, 'UTF-8');
          $description_raw = $row['description'];
          $description = htmlspecialchars($description_raw, ENT_QUOTES, 'UTF-8');
          
          // Dịch tên facility nếu có trong map
          $f_i18n_key = isset($facility_map[$name_raw]) ? $facility_map[$name_raw] : '';
          $name_display = $f_i18n_key ? t_facilities($f_i18n_key, $current_lang) : $name;
          
          // Dịch description nếu có trong map
          $description_display = $description;
          if(isset($description_map[$description_raw]) && isset($description_map[$description_raw][$current_lang])){
            $description_display = $description_map[$description_raw][$current_lang];
          } else {
            // Nếu không có trong map, giữ nguyên description gốc
            $description_display = $description;
          }
          
          echo<<<data
            <div class="col-lg-4 col-md-6">
              <div class="facility-card">
                <div class="facility-icon-wrapper">
                  <img src="$path$row[icon]" alt="$name_display">
                </div>
                <h5>$name_display</h5>
                <p>$description_display</p>
              </div>
            </div>
          data;
        }
      ?>
    </div>
  </div>


  <?php require('inc/footer.php'); ?>
  <?php require('inc/modals.php'); ?>

</body>
</html>