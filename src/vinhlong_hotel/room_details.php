<?php
  $current_lang = 'vi';
  $lang_from_url = isset($_GET['_lang']) ? trim($_GET['_lang']) : '';
  if ($lang_from_url === 'en' || $lang_from_url === 'vi') {
    setcookie('lang', $lang_from_url, time() + (365 * 24 * 60 * 60), '/', '', false, true);
    $_COOKIE['lang'] = $lang_from_url;
    $current_lang = $lang_from_url;
  }
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <?php
    function t_room_details($key, $lang = 'vi') {
      $translations = [
        'vi' => [
          'roomDetails.pageTitle' => 'Chi tiết phòng',
        ],
        'en' => [
          'roomDetails.pageTitle' => 'Room Details',
        ]
      ];
      return $translations[$lang][$key] ?? $key;
    }
    
    // Hàm dịch tên phòng
    function t_room_name($name, $lang = 'vi') {
      // Decode HTML entities nhiều lần nếu cần (xử lý trường hợp bị encode nhiều lần)
      $decoded = $name;
      $prev_decoded = '';
      while($decoded !== $prev_decoded) {
        $prev_decoded = $decoded;
        $decoded = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
      }
      $name = $decoded;
      
      if($lang === 'vi') {
        return $name; // Giữ nguyên tiếng Việt
      }
      
      // Mapping các tên phòng phổ biến
      $room_name_map = [
        'Phòng Premium' => 'Premium Room',
        'Phòng Cao Cấp' => 'Premium Room', // Giữ lại để tương thích ngược
        'Phòng Deluxe' => 'Deluxe Room',
        'Phòng Suite' => 'Suite Room',
        'Phòng Standard' => 'Standard Room',
        'Phòng Cơ Bản' => 'Basic Room',
        'Phòng Cơ Bản 1' => 'Basic Room 1',
        'Phòng Cơ Bản 2' => 'Basic Room 2',
        'Phòng Cơ Bản 3' => 'Basic Room 3',
        'Phòng Superior' => 'Superior Room',
        'Phòng Executive' => 'Executive Room',
        'Phòng Family' => 'Family Room',
        'Phòng Twin' => 'Twin Room',
        'Phòng Single' => 'Single Room',
        'Phòng Double' => 'Double Room',
        'Phòng Triple' => 'Triple Room',
      ];
      
      // Kiểm tra mapping chính xác
      if(isset($room_name_map[$name])) {
        return $room_name_map[$name];
      }
      
      // Nếu không tìm thấy, thử dịch các từ phổ biến
      $translated = $name;
      $common_words = [
        'Phòng' => 'Room',
        'Cao Cấp' => 'Premium',
        'Deluxe' => 'Deluxe',
        'Suite' => 'Suite',
        'Standard' => 'Standard',
        'Cơ Bản' => 'Basic',
        'Superior' => 'Superior',
        'Executive' => 'Executive',
        'Family' => 'Family',
        'Twin' => 'Twin',
        'Single' => 'Single',
        'Double' => 'Double',
        'Triple' => 'Triple',
      ];
      
      foreach($common_words as $vi => $en) {
        $translated = str_replace($vi, $en, $translated);
      }
      
      return $translated;
    }
    
    // Hàm dịch tên và mô tả facilities
    function t_facility_name($name, $lang = 'vi') {
      if($lang === 'vi') {
        return $name;
      }
      
      $facility_map = [
        'Ấm Đun Nước' => 'Kettle',
        'Bàn Ủi' => 'Iron',
        'Dép Đi Trong Nhà' => 'Slippers',
        'Dịch vụ 24/24 giờ' => '24/24 Hour Service',
        'Gương Toàn Thân' => 'Full-length Mirror',
        'Máy Lạnh' => 'Air Conditioner',
        'Máy Nước Nóng' => 'Water Heater',
        'Máy Sấy Tóc' => 'Hair Dryer',
        'Máy Sưởi' => 'Heater',
        'Spa' => 'Spa',
        'Truyền Hình' => 'Television',
        'Tủ Quần Áo' => 'Wardrobe',
        'Wi-Fi' => 'Wi-Fi',
        'WiFi miễn phí' => 'Free Wi-Fi',
        'Minibar' => 'Minibar',
        'Khu Làm Việc' => 'Workspace',
        'Hồ bơi' => 'Swimming Pool',
        'Phòng gym' => 'Gym',
        'Nhà hàng' => 'Restaurant',
        'Bãi đỗ xe' => 'Parking',
        'Đồ Vệ Sinh Cá Nhân' => 'Personal Hygiene Items',
      ];
      
      return $facility_map[$name] ?? $name;
    }
    
    function t_facility_description($description, $lang = 'vi') {
      if($lang === 'vi') {
        return $description;
      }
      
      $desc_map = [
        'Ấm đun nước siêu tốc giúp pha trà, cà phê nhanh chóng và tiện lợi.' => 'Super-fast kettle helps make tea, coffee quickly and conveniently.',
        'Bàn ủi tiện dụng giúp quần áo luôn phẳng phiu, chỉn chu.' => 'Convenient iron helps clothes always flat and neat.',
        'Dép mềm mại, đảm bảo vệ sinh và thoải mái khi đi lại trong phòng.' => 'Soft slippers, ensuring hygiene and comfort when moving around the room.',
        'Dịch vụ lễ tân 24/24, luôn sẵn sàng hỗ trợ khách hàng mọi lúc.' => '24/24 reception service, always ready to assist customers at any time.',
        'Gương soi toàn thân lớn, tiện lợi cho việc chỉnh trang trước khi ra ngoài.' => 'Large full-length mirror, convenient for grooming before going out.',
        'Hệ thống điều hòa không khí hiện đại, tạo không gian mát mẻ và dễ chịu.' => 'Modern air conditioning system, creating a cool and comfortable space.',
        'Máy nước nóng tiện lợi, cung cấp nước nóng 24/7 cho nhu cầu sinh hoạt.' => 'Convenient water heater, providing hot water 24/7 for daily needs.',
        'Dễ dàng sấy tóc sau khi tắm, tiện lợi và nhanh chóng.' => 'Easy to dry hair after bathing, convenient and quick.',
        'Hệ thống sưởi ấm chất lượng cao, giữ không gian ấm áp, đặc biệt phù hợp vào những ngày lạnh giá.' => 'High-quality heating system, keeping the space warm, especially suitable for cold days.',
        'Dịch vụ spa chuyên nghiệp với liệu trình thư giãn, chăm sóc cơ thể và phục hồi sức khỏe.' => 'Professional spa service with relaxing treatments, body care, and health recovery.',
        'TV màn hình phẳng với đa dạng kênh giải trí trong nước và quốc tế, đáp ứng nhu cầu thư giãn của khách hàng.' => 'Flat-screen TV with a variety of domestic and international entertainment channels, meeting customers\' relaxation needs.',
        'Tủ đựng đồ rộng rãi, giúp sắp xếp quần áo và đồ dùng cá nhân một cách gọn gàng.' => 'Spacious wardrobe, helps organize clothes and personal items neatly.',
        'Được chuẩn bị sạch sẽ mỗi ngày, bao gồm khăn tắm, xà phòng, dầu gội và các vật dụng vệ sinh cần thiết.' => 'Prepared cleanly every day, including towels, soap, shampoo and necessary hygiene items.',
        'Tủ lạnh nhỏ trong phòng, phục vụ đồ uống và snack tiện lợi.' => 'Small refrigerator in the room, serving drinks and snacks conveniently.',
        'Kết nối Internet tốc độ cao, miễn phí cho khách hàng.' => 'High-speed Internet connection, free for guests.',
      ];
      
      // Tìm mapping chính xác
      if(isset($desc_map[$description])) {
        return $desc_map[$description];
      }
      
      // Nếu không tìm thấy, thử tìm phần đầu của description
      foreach($desc_map as $vi_desc => $en_desc) {
        if(strpos($description, substr($vi_desc, 0, 30)) === 0) {
          return $en_desc;
        }
      }
      
      return $description;
    }
    
    // Hàm dịch mô tả phòng
    function t_room_description($description, $lang = 'vi') {
      if($lang === 'vi') {
        return $description; // Giữ nguyên tiếng Việt
      }
      
      // Decode HTML entities trước (vì có thể description có &quot; thay vì ")
      $decoded_desc = html_entity_decode($description, ENT_QUOTES, 'UTF-8');
      $description = $decoded_desc; // Sử dụng decoded version
      
      // Chuẩn hóa description - thử cả hai cách: với và không có normalize
      $normalized = trim($description);
      $normalized_single_line = preg_replace('/\s+/', ' ', $normalized); // Tất cả whitespace thành 1 space
      $normalized_preserve_lines = preg_replace('/[ \t]+/', ' ', $normalized); // Giữ line breaks, normalize spaces
      $normalized_preserve_lines = preg_replace('/\n\s*\n/', "\n\n", $normalized_preserve_lines);
      
      // Mapping các mô tả phòng phổ biến (full text hoặc từng câu)
      $description_map = [
        // Mô tả VIN HOTEL (bản đầy đủ và chính xác)
        'VIN HOTEL là đề xuất hàng đầu dành cho những tín đồ du lịch "bụi" mong muốn được nghỉ tại một khách sạn vừa thoải mái lại hợp túi tiền.

Dành cho những du khách muốn du lịch thoải mái cùng ngân sách tiết kiệm, VIN HOTEL sẽ là lựa chọn lưu trú hoàn hảo, nơi cung cấp các tiện nghi chất lượng và dịch vụ tuyệt vời.

Khi lưu trú tại khách sạn thì nội thất và kiến trúc hẳn là hai yếu tố quan trọng khiến quý khách mãn nhãn. Với thiết kế độc đáo, VIN HOTEL mang đến không gian lưu trú làm hài lòng quý khách.

Từ sự kiện doanh nghiệp đến họp mặt công ty, VIN HOTEL cung cấp đầy đủ các dịch vụ và tiện nghi đáp ứng mọi nhu cầu của quý khách và đồng nghiệp.

Hãy tận hưởng thời gian vui vẻ cùng cả gia đình với hàng loạt tiện nghi giải trí tại VIN HOTEL, một khách sạn tuyệt vời phù hợp cho mọi kỳ nghỉ bên người thân.

Nếu dự định có một kỳ nghỉ dài, thì VIN HOTEL chính là lựa chọn dành cho quý khách. Với đầy đủ tiện nghi với chất lượng dịch vụ tuyệt vời, VIN HOTEL sẽ khiến quý khách cảm thấy thoải mái như đang ở nhà vậy.

Du lịch một mình cũng không hề kém phần thú vị và VIN HOTEL là nơi thích hợp dành riêng cho những ai đề cao sự riêng tư trong kỳ lưu trú.

Dịch vụ tuyệt vời, cơ sở vật chất hoàn chỉnh và các tiện nghi khách sạn cung cấp sẽ khiến quý khách không thể phàn nàn trong suốt kỳ lưu trú tại VIN HOTEL.

Quầy tiếp tân 24 giờ luôn sẵn sàng phục vụ quý khách từ thủ tục nhận phòng đến trả phòng hay bất kỳ yêu cầu nào. Nếu cần giúp đỡ xin hãy liên hệ đội ngũ tiếp tân, chúng tôi luôn sẵn sàng hỗ trợ quý khách.

VIN HOTEL là khách sạn sở hữu đầy đủ tiện nghi và dịch vụ xuất sắc theo nhận định của hầu hết khách lưu trú.

Với những tiện nghi sẵn có VIN HOTEL thực sự là một nơi lưu trú hoàn hảo.' => 'VIN HOTEL is the top recommendation for budget travelers who want to stay at a comfortable yet affordable hotel.

For travelers who want comfortable travel with a budget-friendly approach, VIN HOTEL will be the perfect accommodation choice, providing quality amenities and excellent service.

When staying at a hotel, interior design and architecture are certainly two important factors that satisfy guests. With unique design, VIN HOTEL brings a satisfying accommodation space for guests.

From business events to company meetings, VIN HOTEL provides full services and amenities to meet all needs of guests and colleagues.

Enjoy fun time with the whole family with a range of entertainment amenities at VIN HOTEL, a wonderful hotel suitable for all vacations with loved ones.

If planning a long vacation, then VIN HOTEL is the choice for you. With full amenities and excellent service quality, VIN HOTEL will make you feel as comfortable as at home.

Solo travel is also no less interesting and VIN HOTEL is a suitable place specifically for those who value privacy during their stay.

Excellent service, complete facilities and hotel amenities provided will make you unable to complain throughout your stay at VIN HOTEL.

The 24-hour front desk is always ready to serve you from check-in to check-out or any requests. If you need help, please contact the front desk team, we are always ready to assist you.

VIN HOTEL is a hotel with full amenities and excellent service according to most guests\' assessments.

With the available amenities, VIN HOTEL is truly a perfect place to stay.',
        
        // Mô tả cụ thể từ user
        'Phòng ốc tuy không gian hơi hẹp nhưng sạch sẽ dễ chịu' => 'The room space is a bit narrow but clean and comfortable',
        'Khách sạn ở đây rất thoải mái và yên tĩnh.' => 'The hotel here is very comfortable and quiet.',
        'Phòng ốc tuy không gian hơi hẹp nhưng sạch sẽ dễ chịu Khách sạn ở đây rất thoải mái và yên tĩnh.' => 'The room space is a bit narrow but clean and comfortable. The hotel here is very comfortable and quiet.',
        
        // Mô tả từ database update_rooms_vinhlong.sql
        'Phòng Superior nằm ở trung tâm thành phố Vĩnh Long, gần các điểm du lịch và nhà hàng. Tiện nghi đầy đủ, giá cả hợp lý.' => 'Superior room located in the center of Vinh Long city, near tourist attractions and restaurants. Full amenities, reasonable price.',
        'Phòng Deluxe với view đẹp ra sông Tiền, không gian rộng rãi, nội thất hiện đại. Phù hợp cho cặp đôi hoặc gia đình nhỏ muốn tận hưởng cảnh quan sông nước miền Tây.' => 'Deluxe room with beautiful view of Tien River, spacious space, modern furniture. Suitable for couples or small families who want to enjoy the Mekong Delta river scenery.',
        'Phòng Family Suite rộng rãi, phù hợp cho gia đình 4-6 người. Có 2 giường đôi, phòng khách riêng, ban công view thành phố.' => 'Spacious Family Suite, suitable for families of 4-6 people. Has 2 double beds, separate living room, city view balcony.',
        'Phòng Standard với giá cả phải chăng, phù hợp cho khách du lịch tiết kiệm. Đầy đủ tiện nghi cơ bản, sạch sẽ, thoáng mát.' => 'Standard room with affordable price, suitable for budget travelers. Full basic amenities, clean, airy.',
        'Phòng có view đẹp ra đồng ruộng xanh mướt của huyện Long Hồ. Không gian yên tĩnh, phù hợp để nghỉ dưỡng và thư giãn.' => 'Room with beautiful view of the green fields of Long Ho district. Quiet space, suitable for relaxation and rest.',
        'Phòng Bungalow độc đáo nằm trong khu vườn cây ăn trái. Trải nghiệm nghỉ dưỡng gần gũi với thiên nhiên, không khí trong lành.' => 'Unique Bungalow room located in a fruit garden. Experience a nature-friendly stay, fresh air.',
        'Phòng homestay ven sông, mang đậm nét văn hóa miền Tây. Gần các làng nghề truyền thống, phù hợp cho du khách muốn trải nghiệm văn hóa địa phương.' => 'Riverside homestay room, rich in Mekong Delta culture. Near traditional craft villages, suitable for tourists who want to experience local culture.',
        'Phòng tiêu chuẩn gần chợ nổi Mang Thít. Thuận tiện để tham quan chợ nổi vào buổi sáng sớm, trải nghiệm văn hóa sông nước độc đáo.' => 'Standard room near Mang Thit floating market. Convenient for visiting the floating market early in the morning, experiencing unique river culture.',
        'Phòng Deluxe tại thị trấn Vũng Liêm, không gian sang trọng, tiện nghi hiện đại. Gần các điểm tham quan và nhà hàng địa phương.' => 'Deluxe room in Vung Liem town, luxurious space, modern amenities. Near tourist attractions and local restaurants.',
        'Phòng Superior với không gian yên bình của vùng nông thôn Vũng Liêm. Phù hợp cho những ai muốn tránh xa ồn ào thành phố.' => 'Superior room with the peaceful space of Vung Liem countryside. Suitable for those who want to escape city noise.',
        'Phòng có view đẹp ra cánh đồng lúa bát ngát của huyện Tam Bình. Không gian thoáng đãng, không khí trong lành, lý tưởng để nghỉ dưỡng.' => 'Room with beautiful view of the vast rice fields of Tam Binh district. Spacious space, fresh air, ideal for relaxation.',
        'Phòng Standard với giá cả hợp lý tại Tam Bình. Đầy đủ tiện nghi cơ bản, sạch sẽ, phục vụ chu đáo.' => 'Standard room with reasonable price in Tam Binh. Full basic amenities, clean, attentive service.',
        'Phòng Deluxe tại huyện Bình Tân, thiết kế hiện đại, không gian rộng rãi. Gần các điểm du lịch sinh thái và vườn cây ăn trái.' => 'Deluxe room in Binh Tan district, modern design, spacious space. Near eco-tourism sites and fruit gardens.',
        'Phòng Family phù hợp cho gia đình tại Bình Tân. Có không gian riêng cho trẻ em, gần các khu vui chơi và điểm tham quan.' => 'Family room suitable for families in Binh Tan. Has separate space for children, near playgrounds and tourist attractions.',
        'Phòng homestay tại huyện Trà Ôn, mang đậm nét văn hóa địa phương. Gần các làng nghề và điểm du lịch văn hóa.' => 'Homestay room in Tra On district, rich in local culture. Near craft villages and cultural tourist sites.',
        'Phòng tiêu chuẩn với giá cả phải chăng tại Trà Ôn. Phù hợp cho khách du lịch tiết kiệm muốn khám phá vùng đất này.' => 'Standard room with affordable price in Tra On. Suitable for budget travelers who want to explore this area.',
        'Phòng Deluxe tại thị xã Bình Minh, không gian sang trọng, view đẹp. Gần trung tâm thị xã, thuận tiện cho việc đi lại và tham quan.' => 'Deluxe room in Binh Minh town, luxurious space, beautiful view. Near town center, convenient for travel and sightseeing.',
        'Phòng Superior với thiết kế hiện đại tại Bình Minh. Tiện nghi đầy đủ, giá cả hợp lý, phục vụ chuyên nghiệp.' => 'Superior room with modern design in Binh Minh. Full amenities, reasonable price, professional service.',
        'Phòng Executive Suite cao cấp với không gian rộng lớn, phòng khách riêng, phòng ngủ sang trọng. Phù hợp cho doanh nhân hoặc kỳ nghỉ đặc biệt.' => 'Premium Executive Suite with spacious area, separate living room, luxurious bedroom. Suitable for business people or special vacations.',
        'Phòng Studio hiện đại với view đẹp ra thành phố Vĩnh Long. Thiết kế mở, không gian sống và làm việc kết hợp, phù hợp cho khách du lịch dài ngày.' => 'Modern Studio room with beautiful view of Vinh Long city. Open design, combined living and working space, suitable for long-term travelers.',
        
        // Các mô tả phòng khác
        'Phòng đơn giản, phù hợp với những khách hàng cần chỗ nghỉ ngắn hạn. Được trang bị các tiện nghi cơ bản như giường thoải mái, bàn làm việc nhỏ, và Wi-Fi miễn phí.' => 'Simple room, suitable for guests who need short-term accommodation. Equipped with basic amenities such as comfortable bed, small work desk, and free Wi-Fi.',
        'Nâng cấp nhẹ so với Phòng Cơ Bản 1, mang đến không gian rộng rãi hơn và thêm các tiện ích như TV màn hình phẳng và minibar.' => 'Slight upgrade compared to Basic Room 1, offering more spacious area and additional amenities such as flat-screen TV and minibar.',
        'Phòng cơ bản cao cấp hơn với thiết kế hiện đại, ban công nhỏ hoặc cửa sổ lớn có view thành phố, tạo cảm giác thoáng đãng và thư giãn.' => 'Higher-end basic room with modern design, small balcony or large window with city view, creating an airy and relaxing feeling.',
        'Không gian rộng rãi với thiết kế sang trọng, phù hợp cho các kỳ nghỉ dài ngày. Được trang bị nội thất cao cấp, phòng tắm riêng với bồn tắm, và các tiện ích như máy pha cà phê và két an toàn.' => 'Spacious space with luxurious design, suitable for long-term stays. Equipped with high-end furniture, private bathroom with bathtub, and amenities such as coffee maker and safe.',
        
        // Mô tả về đỗ xe, Wi-Fi và tiện ích
        'Đỗ xe và Wi-Fi luôn miễn phí, vì vậy quý khách có thể giữ liên lạc, đến và đi tùy ý. Nằm ở vị trí trung tâm tại Vĩnh Long của Vĩnh Long, chỗ nghỉ này đặt quý khách ở near các điểm thu hút và tùy chọn ăn uống thú vị. Bể bơi trong nhà, mát-xa và bể bơi ngoài trời là một trong những amenities đặc biệt sẽ nâng cao kỳ nghỉ của quý khách với sự tiện lợi ngay trong khuôn viên.' => 'Parking and Wi-Fi are always free, so guests can stay connected, come and go as they please. Located in the central area of Vinh Long, this accommodation places guests near attractions and interesting dining options. Indoor swimming pool, massage, and outdoor swimming pool are among the special amenities that will enhance your stay with convenience right on the premises.',
        'Đỗ xe và Wi-Fi luôn miễn phí, vì vậy quý khách có thể giữ liên lạc, đến và đi tùy ý.' => 'Parking and Wi-Fi are always free, so guests can stay connected, come and go as they please.',
        'Nằm ở vị trí trung tâm tại Vĩnh Long, chỗ nghỉ này đặt quý khách ở gần các điểm thu hút và tùy chọn ăn uống thú vị.' => 'Located in the central area of Vinh Long, this accommodation places guests near attractions and interesting dining options.',
        'Bể bơi trong nhà, mát-xa và bể bơi ngoài trời là một trong những tiện ích đặc biệt sẽ nâng cao kỳ nghỉ của quý khách với sự tiện lợi ngay trong khuôn viên.' => 'Indoor swimming pool, massage, and outdoor swimming pool are among the special amenities that will enhance your stay with convenience right on the premises.',
        'Đỗ xe và Wi-Fi luôn miễn phí' => 'Parking and Wi-Fi are always free',
        'vì vậy quý khách có thể giữ liên lạc' => 'so guests can stay connected',
        'đến và đi tùy ý' => 'come and go as they please',
        'Nằm ở vị trí trung tâm tại Vĩnh Long' => 'Located in the central area of Vinh Long',
        'chỗ nghỉ này đặt quý khách ở gần các điểm thu hút' => 'this accommodation places guests near attractions',
        'tùy chọn ăn uống thú vị' => 'interesting dining options',
        'Bể bơi trong nhà' => 'Indoor swimming pool',
        'mát-xa' => 'massage',
        'Bể bơi ngoài trời' => 'Outdoor swimming pool',
        'là một trong những tiện ích đặc biệt' => 'are among the special amenities',
        'sẽ nâng cao kỳ nghỉ của quý khách' => 'will enhance your stay',
        'với sự tiện lợi ngay trong khuôn viên' => 'with convenience right on the premises',
      ];
      
      // Kiểm tra xem có mapping chính xác không - thử nhiều cách normalize
      // Thử với description gốc trước (không normalize) - quan trọng nhất
      if(isset($description_map[$description])) {
        return $description_map[$description];
      }
      // Thử với normalized preserve lines
      if(isset($description_map[$normalized_preserve_lines])) {
        return $description_map[$normalized_preserve_lines];
      }
      // Thử với normalized single line
      if(isset($description_map[$normalized_single_line])) {
        return $description_map[$normalized_single_line];
      }
      // Thử với normalized
      if(isset($description_map[$normalized])) {
        return $description_map[$normalized];
      }
      
      // Nếu không tìm thấy mapping chính xác, thử tìm phần đầu của mô tả
      // Lấy 100 ký tự đầu để so sánh (đủ để nhận diện)
      $desc_start = mb_substr(trim($description), 0, 100, 'UTF-8');
      $desc_start_normalized = preg_replace('/\s+/', ' ', $desc_start);
      $desc_start_decoded = html_entity_decode($desc_start, ENT_QUOTES, 'UTF-8');
      
      foreach($description_map as $vi_text => $en_text) {
        // Kiểm tra xem description có chứa phần đầu của vi_text không
        $vi_start = mb_substr(trim($vi_text), 0, 100, 'UTF-8');
        $vi_start_normalized = preg_replace('/\s+/', ' ', $vi_start);
        $vi_start_decoded = html_entity_decode($vi_start, ENT_QUOTES, 'UTF-8');
        
        // So sánh với nhiều cách (bao gồm cả decoded version)
        if($desc_start === $vi_start || 
           $desc_start_normalized === $vi_start_normalized ||
           $desc_start_decoded === $vi_start_decoded ||
           mb_strpos($description, $vi_start, 0, 'UTF-8') === 0 ||
           mb_strpos($normalized, $vi_start_normalized, 0, 'UTF-8') === 0 ||
           mb_strpos($decoded_desc, $vi_start_decoded, 0, 'UTF-8') === 0) {
          return $en_text;
        }
      }
      
      // Nếu vẫn không tìm thấy, thử tìm với 50 ký tự đầu (ít chính xác hơn nhưng vẫn có thể khớp)
      $desc_start_50 = mb_substr(trim($description), 0, 50, 'UTF-8');
      $desc_start_50_normalized = preg_replace('/\s+/', ' ', $desc_start_50);
      
      foreach($description_map as $vi_text => $en_text) {
        $vi_start_50 = mb_substr(trim($vi_text), 0, 50, 'UTF-8');
        $vi_start_50_normalized = preg_replace('/\s+/', ' ', $vi_start_50);
        
        if($desc_start_50 === $vi_start_50 || 
           $desc_start_50_normalized === $vi_start_50_normalized ||
           mb_strpos($description, $vi_start_50, 0, 'UTF-8') === 0) {
          return $en_text;
        }
      }
      
      // Nếu không tìm thấy, thử tách theo dòng và dịch từng câu
      $lines = preg_split('/[\r\n]+/', $description);
      $translated_lines = [];
      
      foreach($lines as $line) {
        $line = trim($line);
        if(empty($line)) {
          $translated_lines[] = '';
          continue;
        }
        
        // Kiểm tra xem có mapping cho câu này không
        if(isset($description_map[$line])) {
          $translated_lines[] = $description_map[$line];
        } else {
          // Thử tìm mapping cho các cụm từ/câu con trong câu này
          $translated_line = $line;
          $found_partial = false;
          
          // Danh sách các cụm từ/câu phổ biến (ưu tiên cụm dài)
          $sentence_patterns = [
            'Đỗ xe và Wi-Fi luôn miễn phí, vì vậy quý khách có thể giữ liên lạc, đến và đi tùy ý' => 'Parking and Wi-Fi are always free, so guests can stay connected, come and go as they please',
            'Nằm ở vị trí trung tâm tại Vĩnh Long' => 'Located in the central area of Vinh Long',
            'chỗ nghỉ này đặt quý khách ở gần các điểm thu hút và tùy chọn ăn uống thú vị' => 'this accommodation places guests near attractions and interesting dining options',
            'Bể bơi trong nhà, mát-xa và bể bơi ngoài trời là một trong những tiện ích đặc biệt sẽ nâng cao kỳ nghỉ của quý khách với sự tiện lợi ngay trong khuôn viên' => 'Indoor swimming pool, massage, and outdoor swimming pool are among the special amenities that will enhance your stay with convenience right on the premises',
            'Đỗ xe và Wi-Fi luôn miễn phí' => 'Parking and Wi-Fi are always free',
            'vì vậy quý khách có thể giữ liên lạc' => 'so guests can stay connected',
            'đến và đi tùy ý' => 'come and go as they please',
            'tùy chọn ăn uống thú vị' => 'interesting dining options',
            'Bể bơi trong nhà' => 'Indoor swimming pool',
            'Bể bơi ngoài trời' => 'Outdoor swimming pool',
            'là một trong những tiện ích đặc biệt' => 'are among the special amenities',
            'sẽ nâng cao kỳ nghỉ của quý khách' => 'will enhance your stay',
            'với sự tiện lợi ngay trong khuôn viên' => 'with convenience right on the premises',
          ];
          
          // Thử thay thế các cụm từ/câu dài trước
          uksort($sentence_patterns, function($a, $b) {
            return strlen($b) - strlen($a);
          });
          
          foreach($sentence_patterns as $vi_pattern => $en_pattern) {
            if(mb_stripos($translated_line, $vi_pattern, 0, 'UTF-8') !== false) {
              $translated_line = str_ireplace($vi_pattern, $en_pattern, $translated_line);
              $found_partial = true;
            }
          }
          
          // Nếu đã thay thế một số cụm từ, tiếp tục với các cụm từ nhỏ hơn
          $common_phrases = [
            // Cụm từ dài trước
            'quý khách' => 'guests',
            'của Vĩnh Long' => 'of Vinh Long',
            'các điểm thu hút' => 'attractions',
            'tùy chọn ăn uống' => 'dining options',
            'mát-xa' => 'massage',
            'Bể bơi' => 'Swimming pool',
            'bể bơi' => 'swimming pool',
            'trong nhà' => 'indoor',
            'ngoài trời' => 'outdoor',
            'tiện ích' => 'amenities',
            'khuôn viên' => 'premises',
            'sự tiện lợi' => 'convenience',
            'nâng cao' => 'enhance',
            'kỳ nghỉ' => 'stay',
            'chỗ nghỉ' => 'accommodation',
            'đặt' => 'places',
            'ở gần' => 'near',
            'vị trí trung tâm' => 'central area',
            'luôn miễn phí' => 'always free',
            'giữ liên lạc' => 'stay connected',
            'tùy ý' => 'as they please',
            'Bãi đỗ xe miễn phí' => 'Free parking',
            'Wi-Fi miễn phí' => 'Free Wi-Fi',
            'WiFi miễn phí' => 'Free Wi-Fi',
            'Phòng ốc' => 'Room',
            'Không gian rộng rãi' => 'Spacious space',
            'Thiết kế hiện đại' => 'Modern design',
            'Tiện nghi đầy đủ' => 'Full amenities',
            'Giá cả hợp lý' => 'Reasonable price',
            'Gần trung tâm' => 'Near the center',
            'Gần các điểm du lịch' => 'Near tourist attractions',
            'Gần nhà hàng' => 'Near restaurants',
            'Hồ bơi trong nhà' => 'Indoor pool',
            'Hồ bơi ngoài trời' => 'Outdoor pool',
            'Dịch vụ massage' => 'Massage service',
            'Dịch vụ spa' => 'Spa service',
            'Phù hợp cho gia đình' => 'Suitable for families',
            'Phù hợp cho cặp đôi' => 'Suitable for couples',
            'View đẹp' => 'Beautiful view',
            'View thành phố' => 'City view',
            'View sông' => 'River view',
            'Cửa sổ lớn' => 'Large window',
            'Ban công' => 'Balcony',
            'Phòng tắm riêng' => 'Private bathroom',
            'Máy lạnh' => 'Air conditioning',
            'Truyền hình cáp' => 'Cable TV',
            'Tủ lạnh' => 'Refrigerator',
            'Minibar' => 'Minibar',
            'Két an toàn' => 'Safe',
            'Máy pha cà phê' => 'Coffee maker',
            'Máy sấy tóc' => 'Hair dryer',
            'Bàn làm việc' => 'Work desk',
            'Giường đôi' => 'Double bed',
            'Giường đơn' => 'Single bed',
            'Nội thất cao cấp' => 'High-end furniture',
            'Sạch sẽ' => 'Clean',
            'Thoáng mát' => 'Airy',
            'Yên tĩnh' => 'Quiet',
            'Thoải mái' => 'Comfortable',
            'Dễ chịu' => 'Comfortable',
            'Ấm cúng' => 'Cozy',
            'Sang trọng' => 'Luxurious',
            'Đầy đủ' => 'Full',
            'Hiện đại' => 'Modern',
            'Phòng' => 'Room',
            'Khách sạn' => 'Hotel',
            'Không gian' => 'Space',
            'rộng rãi' => 'spacious',
            'sạch sẽ' => 'clean',
            'thoải mái' => 'comfortable',
            'dễ chịu' => 'comfortable',
            'yên tĩnh' => 'quiet',
            'hiện đại' => 'modern',
            'ấm cúng' => 'cozy',
            'thoáng đãng' => 'airy',
            'view đẹp' => 'nice view',
            'view thành phố' => 'city view',
            'ban công' => 'balcony',
            'cửa sổ lớn' => 'large window',
            'gia đình' => 'family',
            'cặp đôi' => 'couple',
            'phù hợp' => 'suitable',
            'giá cả hợp lý' => 'reasonable price',
            'tiện nghi đầy đủ' => 'full amenities',
            'tiện nghi' => 'amenities',
            'gần trung tâm' => 'near the center',
            'gần' => 'near',
            'dịch vụ' => 'service',
            'tại' => 'at',
            'ở' => 'in',
            'với' => 'with',
            'và' => 'and',
            'hoặc' => 'or',
            'có' => 'has',
            'được trang bị' => 'equipped with',
            'được' => 'is',
            'là' => 'is',
            'cho' => 'for',
            'để' => 'to',
            'từ' => 'from',
            'đến' => 'to',
            'trong' => 'in',
            'ngoài' => 'outside',
            'trên' => 'on',
            'dưới' => 'under',
            'gần' => 'near',
            'xa' => 'far',
            'rất' => 'very',
            'khá' => 'quite',
            'hơi' => 'a bit',
            'tuy' => 'although',
            'nhưng' => 'but',
            'nếu' => 'if',
            'khi' => 'when',
            'sẽ' => 'will',
            'có thể' => 'can',
            'mang đến' => 'brings',
            'cung cấp' => 'provides',
            'đáp ứng' => 'meets',
            'phục vụ' => 'serves',
          ];
          
          // Sắp xếp theo độ dài (dài trước) để thay thế cụm từ dài trước
          uksort($common_phrases, function($a, $b) {
            return strlen($b) - strlen($a);
          });
          
          // Thay thế không phân biệt hoa thường
          foreach($common_phrases as $vi => $en) {
            // Thay thế cả chữ hoa và chữ thường
            $translated_line = str_ireplace($vi, $en, $translated_line);
          }
          
          // Capitalize đầu câu
          $translated_line = ucfirst(trim($translated_line));
          
          $translated_lines[] = $translated_line;
        }
      }
      
      return implode("\n", $translated_lines);
    }
  ?>
  <title><?php echo $settings_r['site_title'] . " - " . t_room_details('roomDetails.pageTitle', $current_lang); ?></title>

  <style>
    /* Modern Room Details Page */
    body { 
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%) !important;
      min-height: 100vh;
    }
    
    /* Breadcrumb */
    .breadcrumb-modern {
      font-size: 14px;
      margin-bottom: 1rem;
    }
    
    .breadcrumb-modern a {
      color: #6b7280;
      text-decoration: none;
      transition: color 0.3s;
    }
    
    .breadcrumb-modern a:hover {
      color: #0d6efd;
    }
    
    /* Hero Section */
    .detail-hero{
      background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(248,250,252,0.95) 100%);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(229,231,235,0.8);
      border-radius: 24px;
      padding: 2rem;
      box-shadow: 0 20px 50px rgba(0,0,0,0.08);
      position: relative;
      overflow: hidden;
    }
    
    .detail-hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #0d6efd, #0ea5e9, #764ba2);
      background-size: 200% 100%;
      animation: gradientMove 3s ease infinite;
    }
    
    @keyframes gradientMove {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }
    
    .detail-hero h2 {
      font-size: 2rem;
      font-weight: 800;
      color: #000000;
      margin-bottom: 1rem;
    }
    
    /* Meta Badges */
    .detail-meta{ 
      display:flex; 
      gap:12px; 
      flex-wrap:wrap; 
      margin-top:1rem; 
    }
    
    .detail-meta .badge{
      border-radius: 12px;
      background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
      color: #0d6efd;
      border: 2px solid rgba(13,110,253,0.2);
      padding: 10px 16px;
      font-weight: 600;
      font-size: 14px;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    
    .detail-meta .badge:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(13,110,253,0.2);
      border-color: rgba(13,110,253,0.4);
    }
    
    .detail-meta .badge i {
      font-size: 16px;
    }

    .detail-meta .availability-badge{
      background: linear-gradient(135deg, #0ea5e9 0%, #1d4ed8 100%);
      color: #ffffff;
      border-color: #1d4ed8;
      font-weight: 800;
      box-shadow: 0 6px 16px rgba(29,78,216,0.28);
    }

    .detail-meta .availability-badge.availability-badge--soldout{
      background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
      color: #ffffff;
      border-color: #7f1d1d;
      box-shadow: 0 6px 16px rgba(185,28,28,0.28);
    }

    /* Poster (người đăng) */
    .poster-card{
      background: linear-gradient(120deg, #f6f9ff 0%, #eef4ff 100%);
      border: 1px solid #e0e7ff;
      border-radius: 16px;
      padding: 14px 16px;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      gap: 12px;
      box-shadow: 0 10px 30px rgba(15,93,122,0.08);
    }
    .poster-icon{
      width: 46px;
      height: 46px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 12px;
      font-size: 20px;
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.6);
    }
    .poster-pill{
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 12px;
      border-radius: 999px;
      font-weight: 700;
      font-size: 13px;
      border: 1px dashed rgba(0,0,0,0.08);
    }
    
    /* Modern Gallery - Layout giống Traveloka 90%: ảnh chính lớn trái, 5 ảnh phụ phải (3 trên, 2 dưới) */
    .gallery-stack{
      display:grid;
      grid-template-columns: 2fr 1fr;
      gap:8px;
      height:600px;
      min-height:600px;
      width:100%;
      max-width:100%;
    }
    .gallery-main{
      position:relative;
      border-radius:8px;
      overflow:hidden;
      box-shadow:0 2px 8px rgba(0,0,0,0.1);
      height:100%;
      min-height:600px;
    }
    .gallery-main img{
      width:100%;
      height:100%;
      object-fit:cover;
      display:block;
      transition:transform .3s ease, opacity 0.5s ease;
      cursor:pointer;
      opacity:1;
    }
    .gallery-main:hover img{transform:scale(1.01);}
    .gallery-tag{
      display:none; /* Ẩn tag trên ảnh chính để giống Traveloka */
    }
    .gallery-thumbs{
      display:grid;
      grid-template-columns:repeat(3,1fr);
      grid-template-rows:repeat(2,1fr);
      gap:8px;
      height:100%;
      min-height:600px;
    }
    .gallery-thumbs img{
      width:100%;
      height:100%;
      object-fit:cover;
      border-radius:8px;
      transition:transform .25s ease, box-shadow .25s ease;
      box-shadow:0 2px 6px rgba(0,0,0,0.08);
      cursor:pointer;
    }
    .gallery-thumbs img:hover{transform:scale(1.03);box-shadow:0 4px 10px rgba(0,0,0,0.12);}
    .thumb-overlay{
      position:relative;
      overflow:hidden;
      border-radius:8px;
      grid-column:span 1;
    }
    .thumb-overlay img{
      width:100%;
      height:100%;
      object-fit:cover;
    }
    .thumb-overlay span{
      position:absolute;
      inset:0;
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      background:rgba(0,0,0,0.5);
      color:#fff;
      border-radius:8px;
      font-size:12px;
      font-weight:600;
      backdrop-filter:blur(1px);
      cursor:pointer;
      transition:background .3s ease;
      gap:4px;
      text-align:center;
      padding:8px;
    }
    .thumb-overlay span i{
      font-size:18px;
    }
    .thumb-overlay:hover span{
      background:rgba(0,0,0,0.65);
    }
    /* Giữ layout ngang trên mọi màn hình */
    @media (max-width: 768px) {
      .gallery-stack{
        height:500px;
        min-height:500px;
      }
      .gallery-main{
        min-height:500px;
      }
      .gallery-thumbs{
        min-height:500px;
        grid-template-columns:repeat(3,1fr);
        grid-template-rows:repeat(2,1fr);
      }
    }
    /* Lightbox */
    .lb-backdrop{
      position:fixed;
      inset:0;
      background:rgba(0,0,0,0.8);
      display:flex;
      align-items:center;
      justify-content:center;
      z-index:9999;
      opacity:0;
      pointer-events:none;
      transition:opacity .2s ease;
    }
    .lb-backdrop.show{
      opacity:1;
      pointer-events:auto;
    }
    .lb-frame{
      position:relative;
      width:90vw;
      max-width:90vw;
      height:80vh;
      max-height:90vh;
    }
    .lb-frame img{
      width:100%;
      height:100%;
      object-fit:contain;
      border-radius:12px;
      box-shadow:0 18px 40px rgba(0,0,0,0.35);
    }
    .lb-nav{
      position:absolute;
      top:50%;
      transform:translateY(-50%);
      width:42px;
      height:42px;
      border-radius:50%;
      border:none;
      background:rgba(255,255,255,0.85);
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:18px;
      box-shadow:0 6px 16px rgba(0,0,0,0.25);
      cursor:pointer;
      transition:all .2s ease;
    }
    .lb-nav:hover{
      background:rgba(255,255,255,1);
      transform:translateY(-50%) scale(1.1);
    }
    .lb-prev{left:-54px;}
    .lb-next{right:-54px;}
    .lb-close{
      position:absolute;
      top:-12px;
      right:-12px;
      width:36px;
      height:36px;
      border-radius:50%;
      border:none;
      background:rgba(255,255,255,0.9);
      box-shadow:0 6px 16px rgba(0,0,0,0.25);
      font-weight:700;
      font-size:20px;
      cursor:pointer;
      transition:all .2s ease;
    }
    .lb-close:hover{
      background:rgba(255,255,255,1);
      transform:scale(1.1);
    }
    
    /* Description Box */
    .desc-box{ 
      background:#ffffff; 
      padding:2rem; 
      border-radius:20px; 
      box-shadow:0 8px 24px rgba(0,0,0,0.08);
      border: 1px solid rgba(229,231,235,0.5);
      transition: all 0.3s ease;
      margin-bottom: 2rem;
    }
    
    .desc-box:hover {
      box-shadow: 0 12px 32px rgba(0,0,0,0.12);
      transform: translateY(-2px);
    }
    
    .desc-box h5 {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .desc-box h5::before {
      content: '';
      width: 4px;
      height: 24px;
      background: linear-gradient(180deg, #0d6efd, #0ea5e9);
      border-radius: 2px;
    }
    
    .desc-box p {
      color: #4b5563;
      line-height: 1.8;
      font-size: 15px;
    }
    
    /* Room Card Sticky */
    .room-card{
      background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
      border-radius: 24px;
      box-shadow: 0 20px 50px rgba(0,0,0,0.12);
      padding: 2rem;
      position: sticky;
      top: 100px;
      border: 1px solid rgba(229,231,235,0.5);
      transition: all 0.3s ease;
    }
    
    .room-card:hover {
      box-shadow: 0 24px 60px rgba(13,110,253,0.15);
    }
    
    /* Price Styling */
    .price-card{
      background: linear-gradient(135deg, #0f5d7a 0%, #0b3b56 100%);
      color: #fff;
      border-radius: 18px;
      padding: 16px;
      display: inline-block;
      min-width: 260px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.18);
      border: 1px solid rgba(255,255,255,0.08);
    }
    .price-card .price-big{
      font-size: 1.9rem;
      font-weight: 800;
      line-height: 1.2;
      color: #fff;
    }
    .price-card .price-old{
      text-decoration: line-through;
      color: rgba(255,255,255,0.85);
      font-size: 14px;
      margin: 2px 0 4px 0;
    }
    .price-card .price-badge{
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 4px;
      padding: 4px 10px;
      border-radius: 12px;
      background: #e63946;
      color: #fff;
      font-weight: 700;
      font-size: 12px;
      box-shadow: none;
    }
    .price-card .btn-book{
      margin-top: 10px;
      padding: 10px 18px;
      font-weight: 700;
      border-radius: 12px;
      border: none;
      color: #0f5d7a;
      background: #fff;
    }
    
    /* Map Container */
    .map-container { 
      width: 100%; 
      height: 320px; 
      border-radius: 20px; 
      overflow: hidden; 
      box-shadow: 0 12px 32px rgba(0,0,0,0.12); 
      margin-top: 1.5rem;
      border: 1px solid rgba(229,231,235,0.5);
      transition: all 0.3s ease;
    }
    
    .map-container:hover {
      box-shadow: 0 16px 40px rgba(0,0,0,0.16);
    }
    
    .map-container iframe { 
      width: 100%; 
      height: 100%; 
      border: 0; 
    }
    
    /* Thumbnail Photo */
    .thumb-photo{
      transition: all 0.3s ease;
      position: relative;
    }
    
    .thumb-photo.active{
      outline: 3px solid #0d6efd;
      outline-offset: 2px;
      box-shadow: 0 0 0 4px rgba(13,110,253,0.2);
      transform: scale(0.98);
    }
    
    /* Review Section */
    .review-item {
      padding: 1.5rem;
      background: #f9fafb;
      border-radius: 16px;
      margin-bottom: 1rem;
      border: 1px solid #e5e7eb;
      transition: all 0.3s ease;
    }
    
    .review-item:hover {
      background: #ffffff;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      transform: translateX(4px);
    }
    
    .review-avatar {
      width: 48px;
      height: 48px;
      border: 3px solid #ffffff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    /* Review Images */
    .review-images {
      margin-top: 1rem;
    }
    
    .review-image-thumb {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 8px;
      cursor: pointer;
      border: 2px solid #e5e7eb;
      transition: all 0.3s ease;
    }
    
    .review-image-thumb:hover {
      transform: scale(1.05);
      border-color: #0d6efd;
      box-shadow: 0 4px 12px rgba(13,110,253,0.3);
    }
    
    /* Helpful Button */
    .btn-helpful {
      border-radius: 8px;
      transition: all 0.3s ease;
      font-size: 14px;
    }
    
    .btn-helpful:hover {
      background: #f0f0f0;
      transform: translateY(-2px);
    }
    
    .btn-helpful.active {
      background: #e3f2fd;
      color: #0d6efd;
      border-color: #0d6efd;
    }
    
    .btn-helpful.active i {
      color: #0d6efd;
    }
    
    /* Features & Facilities */
    .feature-badge, .facility-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 14px;
      border-radius: 12px;
      background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
      border: 2px solid rgba(13,110,253,0.2);
      color: #0d6efd;
      font-weight: 600;
      font-size: 13px;
      margin: 4px 8px 4px 0;
      transition: all 0.3s ease;
    }
    
    .feature-badge:hover, .facility-badge:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(13,110,253,0.2);
      border-color: rgba(13,110,253,0.4);
    }
    
    /* Booking Button */
    .btn-booking {
      background: linear-gradient(135deg, #0d6efd 0%, #0ea5e9 100%);
      border: none;
      border-radius: 16px;
      padding: 14px 24px;
      font-weight: 700;
      font-size: 16px;
      box-shadow: 0 8px 20px rgba(13,110,253,0.3);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }
    
    .btn-booking::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255,255,255,0.3);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }
    
    .btn-booking:hover::before {
      width: 300px;
      height: 300px;
    }
    
    .btn-booking:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 28px rgba(13,110,253,0.4);
    }
    
    /* Price Summary */
    .price-summary {
      background: #f9fafb;
      border-radius: 16px;
      padding: 1rem;
      margin: 1rem 0;
    }
    
    .price-summary li {
      padding: 8px 0;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .price-summary li:last-child {
      border-bottom: none;
      font-size: 1.1rem;
      padding-top: 12px;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
      .room-card {
        position: static;
        margin-top: 2rem;
      }
      
      .gallery-grid {
        grid-template-columns: 1fr;
      }
      
      .gallery-sub {
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: auto;
      }
    }
  </style>
</head>

<body class="bg-light">

<?php require('inc/header.php'); ?>
<?php require('inc/modals.php'); ?>

<?php 
  if(!isset($_GET['id'])) redirect('rooms.php');

  $data = filteration($_GET);
  // Kiểm tra xem cột approved có tồn tại không
  $has_approved = false;
  $check_approved = mysqli_query($con, "SHOW COLUMNS FROM `rooms` LIKE 'approved'");
  if($check_approved && mysqli_num_rows($check_approved) > 0){
    $has_approved = true;
  }
  
  $approved_condition = $has_approved ? " AND approved = 1" : "";
  // Lấy thông tin phòng kèm thông tin owner nếu có
  $room_query = "SELECT r.*, ho.name AS owner_name, ho.hotel_name AS owner_hotel_name, ho.id AS owner_id_val
                  FROM rooms r 
                  LEFT JOIN hotel_owners ho ON r.owner_id = ho.id 
                  WHERE r.id=? AND r.status=? AND r.removed=?{$approved_condition}
                  AND (r.owner_id IS NULL OR ho.status = 1)";
  $room_res = select($room_query, [$data['id'],1,0],'iii');
  if(mysqli_num_rows($room_res)==0) redirect('rooms.php');

  $room_data = mysqli_fetch_assoc($room_res);
  
  // Xác định người đăng bài
  $posted_by = null;
  $message_link = '';
  if(!empty($room_data['owner_id_val'])) {
    // Phòng do owner đăng
    $posted_by = [
      'type' => 'owner',
      'name' => $room_data['owner_hotel_name'] ?: $room_data['owner_name'],
      'id' => $room_data['owner_id_val']
    ];
    $message_link = 'messages.php?type=owner&id=' . $room_data['owner_id_val'];
  } else {
    // Phòng do admin đăng
    $posted_by = [
      'type' => 'admin',
      'name' => 'Quản trị viên',
      'id' => 0
    ];
    $message_link = 'messages.php?type=admin&id=0';
  }
  $avg_rating = mysqli_fetch_assoc(
      select("SELECT ROUND(AVG(rating),1) AS avg FROM rating_review WHERE room_id=?", [$room_data['id']], 'i')
  )['avg'] ?? 0;
  $location_text = htmlspecialchars($room_data['location'] ?? 'Vĩnh Long', ENT_QUOTES, 'UTF-8');

  // Giá sau giảm nếu có discount
  $base_price      = (int)$room_data['price'];
  $discount_pct    = isset($room_data['discount']) ? (int)$room_data['discount'] : 0;
  $effective_price = $base_price;
  if($discount_pct > 0 && $discount_pct <= 100){
    $effective_price = max(0, $base_price - ($base_price * $discount_pct / 100));
  }
  $price_fmt     = number_format($effective_price,0,',','.');
  $old_price_fmt = number_format($base_price,0,',','.');

  // Chuẩn bị tính giá theo số đêm và phụ phí
  $checkin  = $_GET['checkin']  ?? null;
  $checkout = $_GET['checkout'] ?? null;
  $nights   = 1;
  if($checkin && $checkout){
    try{
      $ci = new DateTime($checkin);
      $co = new DateTime($checkout);
      $diff = $ci->diff($co)->days;
      if($diff > 0) $nights = $diff;
    } catch(Exception $e){}
  }
  $base_total  = $effective_price * $nights;
  $tax_fee     = round($base_total * 0.08); // VAT 8%
  $svc_fee     = round($base_total * 0.02); // phí dịch vụ 2%
  $grand_total = $base_total + $tax_fee + $svc_fee;
?>

<div class="container mt-4">

  <div class="detail-hero mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
      <div class="flex-grow-1">
        <div class="breadcrumb-modern">
          <a href="index.php"><i class="bi bi-house-door me-1"></i><span data-i18n="roomDetails.home">Trang chủ</span></a>
          <span class="text-secondary mx-2">/</span>
          <a href="rooms.php"><span data-i18n="roomDetails.roomList">Danh sách phòng</span></a>
          <span class="text-secondary mx-2">/</span>
          <span class="text-dark fw-semibold"><?php echo htmlspecialchars(t_room_name($room_data['name'], $current_lang), ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <h2 class="fw-bold mt-2 mb-2"><?php echo htmlspecialchars(t_room_name($room_data['name'], $current_lang), ENT_QUOTES, 'UTF-8'); ?></h2>
        <div class="detail-meta">
          <span class="badge">
            <i class="bi bi-people-fill"></i>
            <?php echo $room_data['adult'] ?> <span data-i18n="roomDetails.adults">người lớn</span> • <?php echo $room_data['children'] ?> <span data-i18n="roomDetails.children">trẻ em</span>
          </span>
          <span class="badge">
            <i class="bi bi-rulers"></i>
            <?php echo $room_data['area'] ?> m²
          </span>
          <span class="badge">
            <i class="bi bi-star-fill text-warning"></i>
            <?php echo $avg_rating ?: 'N/A'; ?> / 5
          </span>
          <span class="badge">
            <i class="bi bi-geo-alt-fill"></i>
            <?php echo $location_text; ?>
          </span>
          <?php 
            $remain = isset($room_data['remaining']) ? (int)$room_data['remaining'] : 0;
            if($remain > 0){
          ?>
          <span class="badge availability-badge">
            <i class="bi bi-check-circle-fill"></i>
            <span data-i18n="roomDetails.available">Còn</span> <?php echo $remain; ?> <span data-i18n="roomDetails.rooms">phòng</span>
          </span>
          <?php } else { ?>
          <span class="badge availability-badge availability-badge--soldout">
            <i class="bi bi-x-circle-fill"></i>
            <span data-i18n="roomDetails.soldOut">Hết phòng</span>
          </span>
          <?php } ?>
        </div>
        <!-- Thông tin người đăng bài -->
        <div class="poster-card mt-3">
          <div class="d-flex align-items-center gap-3">
            <div class="poster-icon <?php echo $posted_by['type'] == 'admin' ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success'; ?>">
              <i class="bi <?php echo $posted_by['type'] == 'admin' ? 'bi-shield-check' : 'bi-building'; ?>"></i>
            </div>
            <div>
              <div class="text-uppercase text-muted small fw-semibold" data-i18n="roomDetails.postedBy">Đăng bởi</div>
              <div class="fw-bold text-dark"><?php echo htmlspecialchars($posted_by['name']); ?></div>
              <div class="text-muted small" data-i18n="<?php echo $posted_by['type'] == 'admin' ? 'roomDetails.admin' : 'roomDetails.owner'; ?>">
                <?php echo $posted_by['type'] == 'admin' ? 'Quản trị viên hệ thống' : 'Chủ khách sạn'; ?>
              </div>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="poster-pill <?php echo $posted_by['type'] == 'admin' ? 'text-primary bg-primary-subtle' : 'text-success bg-success-subtle'; ?>">
              <i class="bi bi-patch-check-fill me-1"></i><?php echo $posted_by['type'] == 'admin' ? ($current_lang === 'en' ? 'Verified' : 'Đã xác minh') : ($current_lang === 'en' ? 'Approved' : 'Được kiểm duyệt'); ?>
            </span>
            <?php if(isset($_SESSION['login']) && $_SESSION['login']): ?>
              <a href="<?php echo $message_link; ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                <i class="bi bi-chat-dots-fill me-1"></i><span data-i18n="roomDetails.message">Nhắn tin</span>
              </a>
            <?php else: ?>
              <button class="btn btn-sm btn-outline-dark rounded-pill px-3" onclick="checkLoginToMessage()">
                <i class="bi bi-chat-dots-fill me-1"></i><span data-i18n="roomDetails.message">Nhắn tin</span>
              </button>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="text-end">
        <div class="price-card">
          <div class="text-white small mb-1" style="opacity: 0.9;" data-i18n="roomDetails.priceFrom">Giá/phòng/đêm từ</div>
          <div class="d-flex align-items-center gap-2">
            <div class="price-big mb-0"><?php echo $price_fmt ?> VND</div>
            <span class="text-white small" style="opacity: 0.9;" data-i18n="roomDetails.perNight">/ đêm</span>
          </div>
          <?php if($discount_pct > 0){ ?>
            <div class="price-old mb-1"><?php echo $old_price_fmt ?> VND</div>
            <span class="price-badge">
              <?php echo $discount_pct; ?>%
            </span>
          <?php } ?>
          <div class="mt-2 text-center">
            <?php
              $login = isset($_SESSION['login']) ? 1 : 0;
              $remain = isset($room_data['remaining']) ? (int)$room_data['remaining'] : 0;
              if(!$settings_r['shutdown']){
                if($remain > 0){
                  echo "<button type='button' onclick='checkLoginToBook($login,{$room_data['id']})' class='btn btn-book w-100'>
                          <span data-i18n='roomDetails.bookNow'>Đặt ngay</span>
                        </button>";
                } else {
                  echo "<button class='btn btn-book w-100' disabled>
                          <span data-i18n='roomDetails.soldOut'>Hết phòng</span>
                        </button>";
                }
              }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php
    $images = [];
    $img_q = mysqli_query($con,"SELECT * FROM room_images WHERE room_id='{$room_data['id']}'");
    while($img_res = mysqli_fetch_assoc($img_q)){
      $images[] = ROOMS_IMG_PATH.$img_res['image'];
    }
    if(empty($images)){
      $images[] = ROOMS_IMG_PATH."thumbnail.jpg";
    }
    $total_imgs = count($images);
  ?>

  <div class="gallery-stack mb-4" style="width:100%;">
    <div class="gallery-main">
      <img id="gallery-main-img" src="<?php echo $images[0]; ?>" alt="Ảnh phòng" data-src="<?php echo $images[0]; ?>">
    </div>
    <div class="gallery-thumbs">
      <?php 
        // Hiển thị 4 ảnh đầu tiên (hàng trên 3 ảnh, hàng dưới 2 ảnh - 1 ảnh có overlay)
        $thumbs = array_slice($images,1,min(4, $total_imgs-1));
        $extraCount = $total_imgs - (1 + count($thumbs));
        
        // Hiển thị 4 ảnh đầu tiên
        foreach($thumbs as $idx=>$src){
          $safeSrc = htmlspecialchars($src);
          echo "<img class=\"thumb-img\" src=\"{$safeSrc}\" data-src=\"{$safeSrc}\" alt=\"Ảnh phòng\">";
        }
        
            // Ảnh thứ 5 (ô cuối cùng hàng dưới) có overlay "Xem tất cả hình ảnh" nếu có nhiều hơn 5 ảnh
            if($total_imgs > 5){
              $lastThumbSrc = count($thumbs) > 0 ? htmlspecialchars($thumbs[count($thumbs)-1]) : htmlspecialchars($images[0]);
              echo "<div class='thumb-overlay' onclick='openGalleryModal()'>
                      <img class=\"thumb-img\" src=\"{$lastThumbSrc}\" data-src=\"{$lastThumbSrc}\" alt=\"Ảnh phòng\">
                      <span><i class=\"bi bi-images\"></i><span data-i18n=\"confirmBooking.viewAllImages\">Xem tất cả hình ảnh</span></span>
                    </div>";
            } else if(count($thumbs) < 4) {
              // Nếu không đủ 5 ảnh, hiển thị ảnh mặc định cho các ô trống
              $remaining = 5 - count($thumbs);
              for($i = 0; $i < $remaining; $i++){
                $fallback = ROOMS_IMG_PATH."thumbnail.jpg";
                if($i === $remaining - 1 && $total_imgs > 1){
                  // Ảnh cuối cùng có overlay nếu có nhiều hơn 1 ảnh
                  echo "<div class='thumb-overlay' onclick='openGalleryModal()'>
                          <img class=\"thumb-img\" src=\"{$fallback}\" data-src=\"{$fallback}\" alt=\"Ảnh phòng\">
                          <span><i class=\"bi bi-images\"></i><span data-i18n=\"confirmBooking.viewAllImages\">Xem tất cả hình ảnh</span></span>
                        </div>";
                } else {
                  echo "<img class=\"thumb-img\" src=\"{$fallback}\" data-src=\"{$fallback}\" alt=\"Ảnh phòng\">";
                }
              }
            }
      ?>
    </div>
  </div>

  <div class="row">

    <!-- LEFT -->
    <div class="col-lg-7 col-md-12 px-4">

      <div class="desc-box">
        <h5 class="fw-bold">
          <i class="bi bi-info-circle-fill text-primary"></i>
          <span data-i18n="roomDetails.description">Mô tả phòng</span>
        </h5>
        <?php 
          $description = $room_data['description'] ?? '';
          $translated_description = t_room_description($description, $current_lang);
          
          $full_description = nl2br(htmlspecialchars($translated_description, ENT_QUOTES, 'UTF-8'));
          $short_length = 200;
          $is_long = mb_strlen(strip_tags($translated_description)) > $short_length;
          
          if($is_long){
            $short_description = mb_substr(strip_tags($translated_description), 0, $short_length) . '...';
            $short_description_html = nl2br(htmlspecialchars($short_description, ENT_QUOTES, 'UTF-8'));
        ?>
          <div class="room-description-container mt-3">
            <div class="text-muted" id="room-description-short" style="display:block; font-size: 15px; line-height: 1.8;"
                 data-description-vi="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>"
                 data-description-en="<?php 
                   // Decode HTML entities trước khi dịch
                   $decoded_desc = html_entity_decode($description, ENT_QUOTES, 'UTF-8');
                   $en_desc = t_room_description($decoded_desc, 'en');
                   echo htmlspecialchars($en_desc, ENT_QUOTES, 'UTF-8'); 
                 ?>">
              <?php echo $short_description_html; ?>
            </div>
            <div class="text-muted" id="room-description-full" style="display:none; font-size: 15px; line-height: 1.8;"
                 data-description-vi="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>"
                 data-description-en="<?php 
                   $decoded_desc = html_entity_decode($description, ENT_QUOTES, 'UTF-8');
                   $en_desc = t_room_description($decoded_desc, 'en');
                   echo htmlspecialchars($en_desc, ENT_QUOTES, 'UTF-8'); 
                 ?>">
              <?php echo $full_description; ?>
            </div>
            <button type="button" class="btn btn-link p-0 text-primary text-decoration-none fw-semibold mt-2" 
                    id="toggle-description-btn" onclick="toggleRoomDescription()">
              <span data-i18n="roomDetails.readMore">Xem thêm</span>
              <i class="bi bi-chevron-down ms-1"></i>
            </button>
          </div>
        <?php } else { ?>
          <p class="text-muted mt-3" style="font-size: 15px; line-height: 1.8;"
             data-description-vi="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>"
             data-description-en="<?php echo htmlspecialchars(t_room_description($description, 'en'), ENT_QUOTES, 'UTF-8'); ?>">
            <?php echo $full_description; ?>
          </p>
        <?php } ?>
      </div>

      <!-- Điểm du lịch gần đây -->
      <div class="desc-box">
        <h5 class="fw-bold mb-4">
          <i class="bi bi-geo-alt-fill text-primary"></i>
          <span data-i18n="roomDetails.nearbyDestinations">Điểm du lịch gần đây</span>
        </h5>
        <?php
          // Lấy danh sách điểm du lịch gần phòng này
          $nearby_destinations_query = "SELECT d.*, rd.distance 
                                        FROM `destinations` d
                                        INNER JOIN `room_destinations` rd ON d.id = rd.destination_id
                                        WHERE rd.room_id = ? 
                                        AND d.active = 1
                                        ORDER BY rd.distance ASC
                                        LIMIT 6";
          $nearby_destinations_result = select($nearby_destinations_query, [$room_data['id']], 'i');
          
          if(mysqli_num_rows($nearby_destinations_result) > 0):
            $dest_path = DESTINATIONS_IMG_PATH;
        ?>
          <div class="row g-3">
            <?php while($dest = mysqli_fetch_assoc($nearby_destinations_result)): 
              $dest_image = $dest['image'] ? $dest_path . $dest['image'] : $dest_path . 'default.jpg';
              $dest_distance = number_format((float)$dest['distance'], 1);
              $dest_rating = (float)$dest['rating'];
              
              // Generate stars
              $dest_stars = '';
              $full_stars = floor($dest_rating);
              $has_half = ($dest_rating - $full_stars) >= 0.5;
              for($i = 0; $i < $full_stars; $i++) {
                $dest_stars .= '<i class="bi bi-star-fill text-warning"></i>';
              }
              if($has_half) {
                $dest_stars .= '<i class="bi bi-star-half text-warning"></i>';
              }
              for($i = $full_stars + ($has_half ? 1 : 0); $i < 5; $i++) {
                $dest_stars .= '<i class="bi bi-star text-muted"></i>';
              }
            ?>
              <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100" style="transition: all 0.3s ease;">
                  <div class="row g-0 h-100">
                    <div class="col-4">
                      <img src="<?php echo $dest_image; ?>" 
                           alt="<?php echo htmlspecialchars($dest['name']); ?>" 
                           class="img-fluid rounded-start h-100" 
                           style="object-fit: cover;"
                           onerror="this.src='<?php echo $dest_path; ?>default.jpg'">
                    </div>
                    <div class="col-8">
                      <div class="card-body p-3 d-flex flex-column h-100">
                        <h6 class="card-title mb-2" style="font-size: 0.95rem; font-weight: 600;">
                          <?php echo htmlspecialchars($dest['name']); ?>
                        </h6>
                        <div class="mb-2" style="font-size: 0.75rem;">
                          <?php echo $dest_stars; ?>
                          <span class="text-muted ms-1"><?php echo number_format($dest_rating, 1); ?></span>
                        </div>
                        <div class="text-muted small mb-2" style="font-size: 0.8rem;">
                          <i class="bi bi-signpost-2"></i> <span data-i18n="roomDetails.kmAway">Cách</span> <?php echo $dest_distance; ?> <span data-i18n="roomDetails.km">km</span>
                        </div>
                        <a href="destination_details.php?id=<?php echo $dest['id']; ?>" 
                           class="btn btn-sm btn-outline-primary mt-auto" 
                           style="font-size: 0.8rem;">
                          <span data-i18n="roomDetails.viewDetails">Xem chi tiết</span>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
          <div class="text-center mt-3">
            <a href="destinations.php" class="btn btn-outline-primary">
              <i class="bi bi-arrow-right me-2"></i><span data-i18n="roomDetails.viewAllDestinations">Xem tất cả điểm du lịch</span>
            </a>
          </div>
        <?php else: ?>
          <div class="text-center py-4">
            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-3 mb-0" data-i18n="roomDetails.noNearbyDestinations">Chưa có thông tin điểm du lịch gần phòng này.</p>
            <a href="destinations.php" class="btn btn-outline-primary mt-3">
              <i class="bi bi-geo-alt me-2"></i><span data-i18n="roomDetails.exploreDestinations">Khám phá điểm du lịch Vĩnh Long</span>
            </a>
          </div>
        <?php endif; ?>
      </div>

      <div class="desc-box">
        <h5 class="fw-bold mb-4">
          <i class="bi bi-chat-dots-fill text-primary"></i>
          <span data-i18n="roomDetails.customerExperience">Trải nghiệm khách hàng</span>
        </h5>
        <?php
          $user_id_for_like = isset($_SESSION['uId']) ? $_SESSION['uId'] : 0;
          
          // Kiểm tra các cột có tồn tại không
          $cols_check = mysqli_query($con, "SHOW COLUMNS FROM `rating_review`");
          $existing_cols = [];
          while($col = mysqli_fetch_assoc($cols_check)){
            $existing_cols[] = $col['Field'];
          }
          
          $has_admin_reply_col = in_array('admin_reply', $existing_cols);
          $has_admin_reply_date_col = in_array('admin_reply_date', $existing_cols);
          
          // Xây dựng SELECT với các cột có sẵn
          $select_cols = ['rr.*', 'uc.name AS uname', 'uc.profile'];
          
          if($has_admin_reply_col) $select_cols[] = 'rr.admin_reply';
          if($has_admin_reply_date_col) $select_cols[] = 'rr.admin_reply_date';
          
          $select_cols[] = "(SELECT COUNT(*) FROM review_helpful WHERE review_id = rr.sr_no AND user_id = ?) AS user_helpful";
          
          $review_q = "SELECT ".implode(', ', $select_cols)."
                       FROM rating_review rr
                       INNER JOIN user_cred uc ON rr.user_id = uc.id
                       WHERE rr.room_id='{$room_data['id']}'
                       ORDER BY sr_no DESC LIMIT 15";

          $review_res = select($review_q, [$user_id_for_like], 'i');

          if(mysqli_num_rows($review_res)==0){
            echo "<div class='text-center py-4'>
                    <i class='bi bi-inbox text-muted' style='font-size: 3rem;'></i>
                    <p class='text-muted mt-3 mb-0' data-i18n='roomDetails.noReviews'>Chưa có đánh giá nào!</p>
                  </div>";
          } 
          else {
            while($rv = mysqli_fetch_assoc($review_res)){
              $uname = htmlspecialchars($rv['uname'], ENT_QUOTES, 'UTF-8');
              $review_text = htmlspecialchars($rv['review'], ENT_QUOTES, 'UTF-8');
              $profile_img = USERS_IMG_PATH.$rv['profile'];
              $review_id = (int)$rv['sr_no'];
              $helpful_count = (int)($rv['helpful_count'] ?? 0);
              $user_helpful = (int)($rv['user_helpful'] ?? 0) > 0;
              $review_date = date('d/m/Y', strtotime($rv['datentime']));
              
              $stars = '';
              for($i=1;$i<=5;$i++){
                if($i <= $rv['rating']){
                  $stars .= '<i class="bi bi-star-fill text-warning"></i>';
                } else {
                  $stars .= '<i class="bi bi-star text-muted"></i>';
                }
              }
              
              // Xử lý ảnh review
              $images_html = '';
              if(!empty($rv['images'])){
                $images_data = json_decode($rv['images'], true);
                if(json_last_error() === JSON_ERROR_NONE && is_array($images_data) && !empty($images_data)){
                  $images_html = '<div class="review-images mt-3 d-flex gap-2 flex-wrap">';
                  foreach($images_data as $img){
                    $img_path = ltrim(str_replace('../', '', $img), '/');
                    $images_html .= "<img src='{$img_path}' class='review-image-thumb' onclick='openImageModal(\"{$img_path}\")' alt='Review image'>";
                  }
                  $images_html .= '</div>';
                }
              }
              
              $helpful_class = $user_helpful ? 'active' : '';
              $helpful_icon = $user_helpful ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up';
              $login_status = isset($_SESSION['login']) && $_SESSION['login'] ? 'true' : 'false';
              
              // Xử lý phản hồi từ owner/admin
              $admin_reply_html = '';
              if($has_admin_reply_col && !empty($rv['admin_reply'])){
                $admin_reply_text = htmlspecialchars($rv['admin_reply'], ENT_QUOTES, 'UTF-8');
                $admin_reply_date = '';
                if($has_admin_reply_date_col && !empty($rv['admin_reply_date'])){
                  $admin_reply_date = date('d/m/Y H:i', strtotime($rv['admin_reply_date']));
                }
                $admin_reply_html = "
                  <div class='admin-reply mt-3 p-3 bg-light rounded border-start border-primary border-3'>
                    <div class='d-flex justify-content-between align-items-start mb-2'>
                      <strong class='text-primary'>
                        <i class='bi bi-reply-fill me-1'></i><span data-i18n='roomDetails.adminReply'>Phản hồi từ chủ khách sạn</span>:
                      </strong>
                      ".(!empty($admin_reply_date) ? "<small class='text-muted'>{$admin_reply_date}</small>" : "")."
                    </div>
                    <p class='mb-0 text-muted' style='line-height: 1.7;'>{$admin_reply_text}</p>
                  </div>";
              }
              
              echo "
                <div class='review-item mb-4 pb-4 border-bottom' data-review-id='{$review_id}'>
                  <div class='d-flex align-items-start gap-3 mb-3'>
                    <img src='{$profile_img}' class='rounded-circle review-avatar' alt='{$uname}'>
                    <div class='flex-grow-1'>
                      <div class='d-flex justify-content-between align-items-start mb-1'>
                        <h6 class='mb-0 fw-bold'>{$uname}</h6>
                        <span class='text-muted small'>{$review_date}</span>
                      </div>
                      <div class='mb-2'>{$stars}</div>
                    </div>
                  </div>
                  <p class='mb-0 text-muted' style='line-height: 1.7;'>{$review_text}</p>
                  {$images_html}
                  {$admin_reply_html}
                  <div class='d-flex justify-content-end align-items-center mt-3 pt-2 border-top'>
                    <button class='btn-helpful btn btn-sm btn-outline-secondary border-0 p-2 {$helpful_class}' 
                            onclick='toggleHelpful({$review_id}, this)' 
                            data-i18n-title='roomDetails.helpful'
                            title='Đánh dấu hữu ích'>
                      <i class='bi {$helpful_icon} me-1'></i>
                      <span class='helpful-count'>{$helpful_count}</span>
                    </button>
                  </div>
                </div>";
            }
          }
        ?>
      </div>

      <!-- Phòng tương tự/liên quan -->
      <div class="desc-box mt-4">
        <h5 class="fw-bold mb-4">
          <i class="bi bi-house-heart-fill text-primary"></i>
          <span data-i18n="roomDetails.relatedRooms">Phòng tương tự</span>
        </h5>
        <?php
          // Lấy phòng tương tự dựa trên location, price range, hoặc random
          $current_room_id = $room_data['id'];
          $current_location = $room_data['location'] ?? '';
          $current_price = $effective_price;
          $price_min = max(0, $current_price * 0.7); // -30%
          $price_max = $current_price * 1.3; // +30%
          
          // Ưu tiên: cùng location, sau đó cùng price range, cuối cùng random
          // Đơn giản hóa query để tránh lặp tham số
          $related_query = "SELECT r.*, 
                            (SELECT image FROM room_images WHERE room_id = r.id LIMIT 1) as first_image,
                            (SELECT ROUND(AVG(rating),1) FROM rating_review WHERE room_id = r.id) as avg_rating
                            FROM rooms r
                            WHERE r.id != ? 
                            AND r.status = 1 
                            AND r.removed = 0
                            AND (r.remaining > 0 OR r.remaining IS NULL)
                            AND (
                              (r.location = ? AND r.location IS NOT NULL AND r.location != '')
                              OR (r.price BETWEEN ? AND ?)
                            )
                            ORDER BY 
                              CASE 
                                WHEN r.location = ? THEN 1
                                WHEN r.price BETWEEN ? AND ? THEN 2
                                ELSE 3
                              END,
                              RAND()
                            LIMIT 6";
          
          // 7 placeholders: id, location (2 lần), price_min (2 lần), price_max (2 lần)
          // Type string: i (id) + s (location) + i (min) + i (max) + s (location) + i (min) + i (max) = 7 ký tự
          // Đếm: i-s-i-i-s-i-i = 7 ký tự
          $related_result = select($related_query, [
            $current_room_id,           // 1: i
            $current_location,           // 2: s
            $price_min,                  // 3: i
            $price_max,                  // 4: i
            $current_location,           // 5: s
            $price_min,                  // 6: i
            $price_max                   // 7: i
          ], "isiisii");
          
          if($related_result && mysqli_num_rows($related_result) > 0):
            $rooms_path = ROOMS_IMG_PATH;
        ?>
          <div class="row g-3">
            <?php while($related_room = mysqli_fetch_assoc($related_result)): 
              $room_img = $related_room['first_image'] ? $rooms_path . $related_room['first_image'] : $rooms_path . 'thumbnail.jpg';
              $room_price = (int)$related_room['price'];
              $room_discount = isset($related_room['discount']) ? (int)$related_room['discount'] : 0;
              $room_effective_price = $room_price;
              if($room_discount > 0 && $room_discount <= 100){
                $room_effective_price = max(0, $room_price - ($room_price * $room_discount / 100));
              }
              $room_rating = $related_room['avg_rating'] ?? 0;
              $room_remaining = isset($related_room['remaining']) ? (int)$related_room['remaining'] : 0;
            ?>
              <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100" style="transition: all 0.3s ease; cursor:pointer;" onclick="window.location.href='room_details.php?id=<?php echo $related_room['id']; ?>'">
                  <div class="position-relative">
                    <img src="<?php echo $room_img; ?>" 
                         alt="<?php echo htmlspecialchars($related_room['name']); ?>" 
                         class="card-img-top" 
                         style="height:200px; object-fit:cover;"
                         onerror="this.src='<?php echo $rooms_path; ?>thumbnail.jpg'">
                    <?php if($room_discount > 0): ?>
                      <span class="position-absolute top-0 end-0 m-2 badge bg-danger">
                        -<?php echo $room_discount; ?>%
                      </span>
                    <?php endif; ?>
                    <?php if($room_remaining > 0): ?>
                      <span class="position-absolute top-0 start-0 m-2 badge bg-success">
                        <span data-i18n="roomDetails.available">Còn</span> <?php echo $room_remaining; ?>
                      </span>
                    <?php endif; ?>
                  </div>
                  <div class="card-body d-flex flex-column">
                    <h6 class="card-title fw-bold mb-2" style="font-size: 1rem;">
                      <?php echo htmlspecialchars(t_room_name($related_room['name'], $current_lang)); ?>
                    </h6>
                    <div class="mb-2">
                      <?php if($room_rating > 0): ?>
                        <div class="d-flex align-items-center gap-1 mb-1">
                          <i class="bi bi-star-fill text-warning"></i>
                          <span class="small fw-semibold"><?php echo number_format($room_rating, 1); ?></span>
                        </div>
                      <?php endif; ?>
                      <?php if(!empty($related_room['location'])): ?>
                        <div class="text-muted small mb-2">
                          <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($related_room['location']); ?>
                        </div>
                      <?php endif; ?>
                    </div>
                    <div class="mt-auto">
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                          <div class="fw-bold text-primary fs-5">
                            <?php echo number_format($room_effective_price, 0, ',', '.'); ?> VND
                          </div>
                          <div class="text-muted small" data-i18n="roomDetails.perNight">/ đêm</div>
                        </div>
                        <?php if($room_remaining > 0): ?>
                          <a href="room_details.php?id=<?php echo $related_room['id']; ?>" 
                             class="btn btn-primary btn-sm rounded-pill"
                             onclick="event.stopPropagation();">
                            <span data-i18n="roomDetails.viewDetails">Xem chi tiết</span>
                          </a>
                        <?php else: ?>
                          <span class="badge bg-secondary">
                            <span data-i18n="roomDetails.soldOut">Hết phòng</span>
                          </span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
          <div class="text-center mt-3">
            <a href="rooms.php" class="btn btn-outline-primary">
              <i class="bi bi-arrow-right me-2"></i><span data-i18n="roomDetails.viewAllRooms">Xem tất cả phòng</span>
            </a>
          </div>
        <?php else: ?>
          <div class="text-center py-4">
            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-3 mb-0" data-i18n="roomDetails.noRelatedRooms">Chưa có phòng tương tự.</p>
            <a href="rooms.php" class="btn btn-outline-primary mt-3">
              <i class="bi bi-house-door me-2"></i><span data-i18n="roomDetails.exploreRooms">Khám phá tất cả phòng</span>
            </a>
          </div>
        <?php endif; ?>
      </div>

    </div>

    <!-- RIGHT -->
    <div class="col-lg-5 col-md-12 px-4">

      <div class="room-card">


        <!-- Vị trí - Di chuyển lên đầu -->
        <div class="map-container mb-4">
          <div class="d-flex align-items-center justify-content-between p-3 bg-white border-bottom">
            <span class="fw-semibold d-flex align-items-center gap-2">
              <i class="bi bi-geo-alt-fill text-primary"></i>
              <span data-i18n="roomDetails.location">Vị trí</span>
            </span>
            <a href="https://www.google.com/maps?q=<?php echo urlencode($room_data['name'].' '.$location_text); ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
              <i class="bi bi-box-arrow-up-right me-1"></i><span data-i18n="roomDetails.viewLargeMap">Xem bản đồ lớn</span>
            </a>
          </div>
          <iframe src="https://www.google.com/maps?q=<?php echo urlencode($room_data['name'].' '.$location_text); ?>&output=embed" allowfullscreen loading="lazy" style="border:0;"></iframe>
        </div>

        <hr class="my-4">

        <!-- Tóm tắt giá - Di chuyển lên đầu -->
        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
          <i class="bi bi-calculator text-primary"></i>
          <span><span data-i18n="roomDetails.priceSummary">Tóm tắt giá</span> (<?php echo $nights; ?> <span data-i18n="roomDetails.nights">đêm</span>)</span>
        </h6>
        <div class="price-summary mb-4">
          <ul class="list-unstyled small mb-0">
            <li class="d-flex justify-content-between">
              <span class="text-muted" data-i18n="roomDetails.roomPrice">Giá phòng</span>
              <span class="fw-semibold"><?php echo number_format($base_total, 0, ',', '.'); ?> VND</span>
            </li>
            <li class="d-flex justify-content-between">
              <span class="text-muted" data-i18n="roomDetails.tax">Thuế (8%)</span>
              <span class="fw-semibold"><?php echo number_format($tax_fee, 0, ',', '.'); ?> VND</span>
            </li>
            <li class="d-flex justify-content-between">
              <span class="text-muted" data-i18n="roomDetails.serviceFee">Phí dịch vụ (2%)</span>
              <span class="fw-semibold"><?php echo number_format($svc_fee, 0, ',', '.'); ?> VND</span>
            </li>
            <li class="d-flex justify-content-between fw-bold text-dark pt-2">
              <span data-i18n="roomDetails.subtotal">Tạm tính</span>
              <span class="text-primary" id="grand_total" data-base="<?php echo $grand_total; ?>">
                <?php echo number_format($grand_total, 0, ',', '.'); ?> VND
              </span>
            </li>
          </ul>
          <?php 
            $login = isset($_SESSION['login']) ? 1 : 0;
            $remain = isset($room_data['remaining']) ? (int)$room_data['remaining'] : 0;
            if(!$settings_r['shutdown']){
              if($remain > 0){
                echo "<button onclick='checkLoginToBook($login,{$room_data['id']})' class='btn btn-booking w-100 text-white mt-3'>
                        <i class='bi bi-calendar-check-fill me-2'></i><span data-i18n='roomDetails.bookNow'>Đặt ngay</span>
                      </button>";
              } else {
                echo "<button class='btn w-100 btn-secondary shadow-none mt-3' disabled>
                        <i class='bi bi-x-circle-fill me-2'></i><span data-i18n='roomDetails.soldOut'>Hết phòng</span>
                      </button>";
              }
            }
          ?>
        </div>

        <hr class="my-4">

        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
          <i class="bi bi-grid-3x3-gap text-primary"></i>
          <span data-i18n="roomDetails.space">Không gian</span>
        </h6>
        <div class="mb-4">
          <?php 
            $fea_q = select("SELECT f.name FROM features f INNER JOIN room_features rf ON f.id = rf.features_id WHERE rf.room_id=?", [$room_data['id']], "i");
            $has_features = false;
            // Map các feature names phổ biến để dịch
            $feature_map = [
                'Phòng Ngủ' => 'features.bedroom',
                'Ban Công' => 'features.balcony',
                'Nhà Bếp' => 'features.kitchen',
                'Ghế Sofa' => 'features.sofa',
                'Phòng Tắm' => 'features.bathroom',
                'Phòng Khách' => 'features.livingRoom',
                'Tủ Lạnh' => 'features.refrigerator',
                'Máy Lạnh' => 'features.airConditioner',
                'TV' => 'features.tv',
                'WiFi' => 'features.wifi',
            ];
            
            while($f = mysqli_fetch_assoc($fea_q)){
              $has_features = true;
              $fname_raw = $f['name'] ?? '';
              $fname = htmlspecialchars($fname_raw, ENT_QUOTES, 'UTF-8');
              $f_i18n_key = isset($feature_map[$fname_raw]) ? $feature_map[$fname_raw] : '';
              
              if($f_i18n_key) {
                echo "<span class='feature-badge'><i class='bi bi-check-circle-fill'></i><span data-i18n='{$f_i18n_key}'>{$fname}</span></span>";
              } else {
                echo "<span class='feature-badge'><i class='bi bi-check-circle-fill'></i>{$fname}</span>";
              }
            }
            if(!$has_features){
              echo "<span class='text-muted small' data-i18n='roomDetails.noInfo'>Chưa có thông tin</span>";
            }
          ?>
        </div>

        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
          <i class="bi bi-list-check text-primary"></i>
          <span data-i18n="roomDetails.facilities">Tiện ích</span>
        </h6>
        <div class="mb-4">
          <?php 
            $fac_result = select("SELECT f.name, f.icon, f.description FROM facilities f 
                                  INNER JOIN room_facilities rf ON f.id = rf.facilities_id 
                                  WHERE rf.room_id=? 
                                  ORDER BY f.name ASC 
                                  LIMIT 12", [$room_data['id']], "i");
            
            $facilities = [];
            if($fac_result && mysqli_num_rows($fac_result) > 0){
              while($fac = mysqli_fetch_assoc($fac_result)){
                $facilities[] = $fac;
              }
            }
            
            $has_facilities = count($facilities) > 0;
            // Icon mapping
            $icon_map = [
              'Wi-Fi' => 'bi-wifi',
              'WiFi miễn phí' => 'bi-wifi',
              'Truyền Hình' => 'bi-tv',
              'Máy Lạnh' => 'bi-thermometer-snow',
              'Máy Sưởi' => 'bi-thermometer-sun',
              'Máy Nước Nóng' => 'bi-droplet',
              'Máy Sấy Tóc' => 'bi-wind',
              'Minibar' => 'bi-cup-straw',
              'Ấm Đun Nước' => 'bi-cup-hot',
              'Khu Làm Việc' => 'bi-laptop',
              'Tủ Quần Áo' => 'bi-box',
              'Bàn Ủi' => 'bi-iron',
              'Hồ bơi' => 'bi-water',
              'Phòng gym' => 'bi-activity',
              'Nhà hàng' => 'bi-egg-fried',
              'Bãi đỗ xe' => 'bi-p-circle',
              'Dịch vụ 24/24 giờ' => 'bi-clock-history',
              'Spa' => 'bi-flower1',
            ];
            
            if($has_facilities){
              echo '<div class="facilities-grid" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:12px;">';
              foreach($facilities as $fac){
                $fac_name_raw = $fac['name'];
                $fac_desc_raw = $fac['description'];
                
                // Dịch tên và mô tả
                $fac_name = t_facility_name($fac_name_raw, $current_lang);
                $fac_desc = t_facility_description($fac_desc_raw, $current_lang);
                
                $fac_name_html = htmlspecialchars($fac_name, ENT_QUOTES, 'UTF-8');
                $fac_desc_html = htmlspecialchars(substr($fac_desc, 0, 50), ENT_QUOTES, 'UTF-8');
                $icon_class = $icon_map[$fac_name_raw] ?? 'bi-check-circle';
                
                // Lấy bản dịch tiếng Anh
                $fac_name_en = t_facility_name($fac_name_raw, 'en');
                $fac_desc_en = t_facility_description($fac_desc_raw, 'en');
                $fac_name_en_html = htmlspecialchars($fac_name_en, ENT_QUOTES, 'UTF-8');
                $fac_desc_en_html = htmlspecialchars(substr($fac_desc_en, 0, 50), ENT_QUOTES, 'UTF-8');
                
                echo '<div class="facility-item p-2 rounded border" style="background:#f8f9fa; transition:all 0.2s;" onmouseover="this.style.transform=\'translateY(-2px)\'; this.style.boxShadow=\'0 4px 8px rgba(0,0,0,0.1)\';" onmouseout="this.style.transform=\'translateY(0)\'; this.style.boxShadow=\'none\';">
                        <div class="d-flex align-items-center gap-2">
                          <i class="bi '.$icon_class.' text-primary" style="font-size:18px;"></i>
                          <div class="flex-grow-1">
                            <div class="fw-semibold small facility-name" 
                                 data-name-vi="'.htmlspecialchars($fac_name_raw, ENT_QUOTES, 'UTF-8').'" 
                                 data-name-en="'.$fac_name_en_html.'">'.$fac_name_html.'</div>
                            <div class="text-muted facility-desc" 
                                 style="font-size:11px; line-height:1.3;"
                                 data-desc-vi="'.htmlspecialchars(substr($fac_desc_raw, 0, 50), ENT_QUOTES, 'UTF-8').'" 
                                 data-desc-en="'.$fac_desc_en_html.'">'.$fac_desc_html.'...</div>
                          </div>
                        </div>
                      </div>';
              }
              echo '</div>';
            } else {
              echo "<span class='text-muted small' data-i18n='roomDetails.noInfo'>Chưa có thông tin</span>";
            }
          ?>
        </div>

        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
          <i class="bi bi-info-circle text-primary"></i>
          <span data-i18n="roomDetails.capacity">Sức chứa & diện tích</span>
        </h6>
        <div class="mb-4">
          <span class="feature-badge">
            <i class="bi bi-people-fill"></i>
            <?php echo $room_data['adult'] ?> <span data-i18n="roomDetails.adults">người lớn</span>
          </span>
          <span class="feature-badge">
            <i class="bi bi-person-heart"></i>
            <?php echo $room_data['children'] ?> <span data-i18n="roomDetails.children">trẻ em</span>
          </span>
          <span class="feature-badge">
            <i class="bi bi-rulers"></i>
            <?php echo $room_data['area'] ?> m²
          </span>
        </div>

      </div>

    </div>

  </div>
</div>

<?php if($total_imgs > 0): ?>
<!-- Modal xem toàn bộ ảnh - Kiểu Lightbox Gallery -->
<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content" style="background:#000;">
      <div class="modal-header border-0 border-bottom border-secondary position-relative">
        <h5 class="modal-title text-white">
          <i class="bi bi-images me-2"></i>Tất cả hình ảnh (<?php echo $total_imgs; ?>)
        </h5>
        <!-- Counter ở góc trên bên phải -->
        <div class="position-absolute end-5 top-50 translate-middle-y bg-dark bg-opacity-75 rounded-pill px-3 py-1" id="gallery-counter-top">
          <span class="text-white fw-semibold">1 / <?php echo $total_imgs; ?></span>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0" style="position:relative;">
        <!-- Ảnh lớn ở giữa -->
        <div class="gallery-main-view" style="height:75vh; display:flex; align-items:center; justify-content:center; background:#000; position:relative;">
          <img id="gallery-main-view-img" src="<?php echo htmlspecialchars($images[0]); ?>" 
               alt="Ảnh phòng" 
               style="max-width:100%; max-height:100%; object-fit:contain; cursor:pointer;"
               onclick="nextGalleryImage()">
          <!-- Nút điều hướng -->
          <button class="gallery-nav-btn gallery-prev-btn" onclick="prevGalleryImage()" style="position:absolute; left:20px; top:50%; transform:translateY(-50%); width:50px; height:50px; border-radius:50%; border:none; background:rgba(255,255,255,0.2); color:#fff; font-size:24px; cursor:pointer; transition:all 0.3s;">
            ‹
          </button>
          <button class="gallery-nav-btn gallery-next-btn" onclick="nextGalleryImage()" style="position:absolute; right:20px; top:50%; transform:translateY(-50%); width:50px; height:50px; border-radius:50%; border:none; background:rgba(255,255,255,0.2); color:#fff; font-size:24px; cursor:pointer; transition:all 0.3s;">
            ›
          </button>
        </div>
        <!-- Thumbnails bên dưới -->
        <div class="gallery-thumbs-container p-3" style="background:#1a1a1a; border-top:1px solid #333; overflow-x:auto;">
          <div class="d-flex gap-2 justify-content-center" style="min-width:fit-content;">
            <?php foreach($images as $idx => $img): 
              $src = htmlspecialchars($img);
              $activeClass = $idx === 0 ? 'active' : '';
            ?>
              <div class="gallery-thumb-item <?php echo $activeClass; ?>" 
                   onclick="showGalleryImage(<?php echo $idx; ?>)"
                   style="flex-shrink:0; width:100px; height:80px; border-radius:8px; overflow:hidden; cursor:pointer; border:3px solid transparent; transition:all 0.3s;">
                <img src="<?php echo $src; ?>" 
                     alt="Thumb <?php echo $idx + 1; ?>" 
                     style="width:100%; height:100%; object-fit:cover;">
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.gallery-thumb-item.active {
  border-color: #0d6efd !important;
  transform: scale(1.1);
}
.gallery-nav-btn:hover {
  background: rgba(255,255,255,0.4) !important;
  transform: translateY(-50%) scale(1.1) !important;
}
</style>
<?php endif; ?>

<!-- Lightbox viewer -->
<div class="lb-backdrop" id="lb-viewer" aria-hidden="true">
  <div class="lb-frame">
    <button class="lb-close" type="button" aria-label="Đóng" id="lb-close">×</button>
    <button class="lb-nav lb-prev" type="button" aria-label="Ảnh trước" id="lb-prev">‹</button>
    <img src="" alt="Ảnh phòng" id="lb-img">
    <button class="lb-nav lb-next" type="button" aria-label="Ảnh sau" id="lb-next">›</button>
  </div>
</div>

<?php require('inc/footer.php'); ?>

<script>
// Gallery functionality - Giống confirm_booking.php
const mainImg = document.getElementById('gallery-main-img');
const thumbImgs = Array.from(document.querySelectorAll('.thumb-img'));
const viewAllBtn = document.getElementById('btn-view-all');
const galleryModalEl = document.getElementById('galleryModal');
const lb = document.getElementById('lb-viewer');
const lbImg = document.getElementById('lb-img');
const lbPrev = document.getElementById('lb-prev');
const lbNext = document.getElementById('lb-next');
const lbClose = document.getElementById('lb-close');
const galleryList = <?php echo json_encode($images); ?>;
let lbIndex = 0;
let currentImageIndex = 0;
let autoSlideInterval = null;
let isPaused = false;

// Hàm chuyển sang ảnh tiếp theo
function nextImage(){
  if(!mainImg || !galleryList.length) return;
  
  currentImageIndex = (currentImageIndex + 1) % galleryList.length;
  const nextSrc = galleryList[currentImageIndex];
  
  // Fade out
  mainImg.style.opacity = '0';
  mainImg.style.transition = 'opacity 0.5s ease';
  
  setTimeout(() => {
    mainImg.src = nextSrc;
    mainImg.setAttribute('data-src', nextSrc);
    // Fade in
    mainImg.style.opacity = '1';
    
    // Cập nhật thumbnail tương ứng (nếu có)
    thumbImgs.forEach((thumb, idx) => {
      const thumbSrc = thumb.getAttribute('data-src') || thumb.src;
      if(thumbSrc === nextSrc && idx < galleryList.length - 1){
        // Swap với ảnh hiện tại trong thumbnail
        const currentMainSrc = galleryList[currentImageIndex === 0 ? galleryList.length - 1 : currentImageIndex - 1];
        thumb.src = currentMainSrc;
        thumb.setAttribute('data-src', currentMainSrc);
      }
    });
  }, 250);
}

// Bắt đầu auto-slide
function startAutoSlide(){
  if(galleryList.length <= 1) return;
  stopAutoSlide(); // Dừng interval cũ nếu có
  autoSlideInterval = setInterval(() => {
    if(!isPaused){
      nextImage();
    }
  }, 3000); // 3 giây
}

// Dừng auto-slide
function stopAutoSlide(){
  if(autoSlideInterval){
    clearInterval(autoSlideInterval);
    autoSlideInterval = null;
  }
}

// Tạm dừng khi hover
if(mainImg){
  mainImg.addEventListener('mouseenter', () => {
    isPaused = true;
  });
  mainImg.addEventListener('mouseleave', () => {
    isPaused = false;
  });
}

// Dừng khi click vào ảnh
if(mainImg){
  mainImg.addEventListener('click', () => {
    stopAutoSlide();
  });
}

// Bắt đầu auto-slide khi trang load
if(galleryList.length > 1){
  startAutoSlide();
}

function swapWithThumb(img){
  if(!mainImg || !img) return;
  
  // Dừng auto-slide khi người dùng click
  stopAutoSlide();
  
  const mainSrc = mainImg.getAttribute('data-src') || mainImg.src;
  const thumbSrc = img.getAttribute('data-src') || img.src;
  
  // Tìm index của ảnh mới
  const newIndex = galleryList.indexOf(thumbSrc);
  if(newIndex >= 0){
    currentImageIndex = newIndex;
  }
  
  // Fade transition
  mainImg.style.opacity = '0';
  mainImg.style.transition = 'opacity 0.5s ease';
  
  setTimeout(() => {
    mainImg.src = thumbSrc;
    mainImg.setAttribute('data-src', thumbSrc);
    img.src = mainSrc;
    img.setAttribute('data-src', mainSrc);
    mainImg.style.opacity = '1';
    
    // Tiếp tục auto-slide sau 3 giây
    setTimeout(() => {
      startAutoSlide();
    }, 3000);
  }, 250);
}

thumbImgs.forEach(img=>{
  img.addEventListener('click', ()=> swapWithThumb(img));
});

// Gallery Modal Variables
let currentGalleryIndex = 0;
const galleryMainViewImg = document.getElementById('gallery-main-view-img');
const galleryCounterTop = document.getElementById('gallery-counter-top');
let galleryThumbItems = [];

// Hàm hiển thị ảnh trong gallery modal
function showGalleryImage(index){
  if(!galleryList || index < 0 || index >= galleryList.length) return;
  
  currentGalleryIndex = index;
  const imgSrc = galleryList[index];
  
  // Fade transition
  if(galleryMainViewImg){
    galleryMainViewImg.style.opacity = '0';
    galleryMainViewImg.style.transition = 'opacity 0.3s ease';
    
    setTimeout(() => {
      galleryMainViewImg.src = imgSrc;
      galleryMainViewImg.style.opacity = '1';
    }, 150);
  }
  
  // Cập nhật counter ở header
  const counterText = `${index + 1} / ${galleryList.length}`;
  if(galleryCounterTop){
    galleryCounterTop.querySelector('span').textContent = counterText;
  }
  
  // Cập nhật active thumbnail
  if(galleryThumbItems.length === 0){
    galleryThumbItems = Array.from(document.querySelectorAll('.gallery-thumb-item'));
  }
  galleryThumbItems.forEach((thumb, idx) => {
    if(idx === index){
      thumb.classList.add('active');
    } else {
      thumb.classList.remove('active');
    }
  });
  
  // Scroll thumbnail vào view
  if(galleryThumbItems[index]){
    galleryThumbItems[index].scrollIntoView({behavior: 'smooth', block: 'nearest', inline: 'center'});
  }
}

// Chuyển ảnh tiếp theo
function nextGalleryImage(){
  const nextIndex = (currentGalleryIndex + 1) % galleryList.length;
  showGalleryImage(nextIndex);
}

// Chuyển ảnh trước
function prevGalleryImage(){
  const prevIndex = (currentGalleryIndex - 1 + galleryList.length) % galleryList.length;
  showGalleryImage(prevIndex);
}

// Hàm mở modal gallery
function openGalleryModal(){
  if(galleryModalEl){
    const modal = new bootstrap.Modal(galleryModalEl);
    modal.show();
    // Dừng auto-slide khi mở modal
    stopAutoSlide();
    
    // Reset gallery thumb items
    galleryThumbItems = [];
    
    // Reset về ảnh đầu tiên sau khi modal hiển thị
    galleryModalEl.addEventListener('shown.bs.modal', () => {
      showGalleryImage(0);
    }, { once: true });
    
    // Keyboard navigation
    const handleKeyPress = (e) => {
      if(!galleryModalEl.classList.contains('show')) return;
      if(e.key === 'ArrowRight') nextGalleryImage();
      if(e.key === 'ArrowLeft') prevGalleryImage();
      if(e.key === 'Escape') modal.hide();
    };
    
    galleryModalEl.addEventListener('shown.bs.modal', () => {
      document.addEventListener('keydown', handleKeyPress);
    });
    
    galleryModalEl.addEventListener('hidden.bs.modal', () => {
      document.removeEventListener('keydown', handleKeyPress);
    });
  }
}

if(viewAllBtn && galleryModalEl){
  viewAllBtn.addEventListener('click', openGalleryModal);
}

function openLightbox(idx){
  if(!lb || !lbImg || !galleryList.length) return;
  lbIndex = (idx + galleryList.length) % galleryList.length;
  lbImg.src = galleryList[lbIndex];
  lb.classList.add('show');
  lb.setAttribute('aria-hidden','false');
}
function closeLightbox(){
  if(!lb) return;
  lb.classList.remove('show');
  lb.setAttribute('aria-hidden','true');
}
function nextLightbox(step){
  openLightbox(lbIndex + step);
}

document.querySelectorAll('.thumb-img, #gallery-main-img').forEach((img, i)=>{
  img.addEventListener('click', ()=>{
    const dataSrc = img.getAttribute('data-src') || img.src;
    const idx = galleryList.indexOf(dataSrc);
    openLightbox(idx >=0 ? idx : i);
  });
});
if(galleryModalEl){
  galleryModalEl.querySelectorAll('img').forEach((img, i)=>{
    img.addEventListener('click', ()=>{
      const src = img.getAttribute('src');
      const idx = galleryList.indexOf(src);
      openLightbox(idx >=0 ? idx : i);
    });
  });
}
if(lbClose){ lbClose.addEventListener('click', closeLightbox); }
if(lbPrev){ lbPrev.addEventListener('click', ()=>nextLightbox(-1)); }
if(lbNext){ lbNext.addEventListener('click', ()=>nextLightbox(1)); }
if(lb){
  lb.addEventListener('click', (e)=>{
    if(e.target === lb) closeLightbox();
  });
  document.addEventListener('keydown', (e)=>{
    if(!lb.classList.contains('show')) return;
    if(e.key === 'Escape') closeLightbox();
    if(e.key === 'ArrowRight') nextLightbox(1);
    if(e.key === 'ArrowLeft') nextLightbox(-1);
  });
}

// Smooth scroll for page load
window.addEventListener('load', () => {
  document.body.style.opacity = '0';
  document.body.style.transition = 'opacity 0.5s ease';
  setTimeout(() => {
    document.body.style.opacity = '1';
  }, 100);
});

// Add animation to cards
document.addEventListener('DOMContentLoaded', () => {
  const cards = document.querySelectorAll('.desc-box, .room-card');
  cards.forEach((card, index) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    card.style.transition = 'all 0.6s ease';
    setTimeout(() => {
      card.style.opacity = '1';
      card.style.transform = 'translateY(0)';
    }, 300 + (index * 100));
  });
});

// Toggle helpful function
function toggleHelpful(reviewId, btn){
  const loginStatus = <?php echo isset($_SESSION['login']) && $_SESSION['login'] ? 'true' : 'false'; ?>;
  
  if(!loginStatus){
    const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
    const msg = currentLang === 'en' ? 'Please login to mark as helpful' : 'Vui lòng đăng nhập để đánh dấu hữu ích';
    if(typeof showToast === 'function'){
      showToast('warning', msg, 3000);
    } else {
      alert(msg);
    }
    return;
  }
  
  const data = new FormData();
  data.append('mark_helpful', '');
  data.append('review_id', reviewId);
  
  fetch('ajax/review_actions.php', {
    method: 'POST',
    body: data
  })
  .then(res => res.json())
  .then(data => {
    if(data.status === 'added' || data.status === 'removed'){
      const icon = btn.querySelector('i');
      const countSpan = btn.querySelector('.helpful-count');
      if(data.status === 'added'){
        btn.classList.add('active');
        icon.className = 'bi bi-hand-thumbs-up-fill me-1';
      } else {
        btn.classList.remove('active');
        icon.className = 'bi bi-hand-thumbs-up me-1';
      }
      countSpan.textContent = data.count;
    } else if(data.status === 'error'){
      if(typeof showToast === 'function'){
        showToast('error', data.msg || 'Có lỗi xảy ra', 3000);
      }
    }
  })
  .catch(err => {
    console.error('Error toggling helpful:', err);
    if(typeof showToast === 'function'){
      showToast('error', 'Có lỗi xảy ra. Vui lòng thử lại.', 3000);
    }
  });
}

// Open image modal
function openImageModal(imgSrc){
  const modal = document.createElement('div');
  modal.className = 'modal fade';
  modal.setAttribute('tabindex', '-1');
  modal.innerHTML = `
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content bg-transparent border-0">
        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 bg-dark rounded-circle" style="z-index: 10;" data-bs-dismiss="modal"></button>
        <img src="${imgSrc}" class="img-fluid rounded shadow-lg" alt="Review image" style="max-height: 90vh;">
      </div>
    </div>
  `;
  document.body.appendChild(modal);
  const bsModal = new bootstrap.Modal(modal);
  bsModal.show();
  modal.addEventListener('hidden.bs.modal', () => modal.remove());
}

// Check login before messaging
function checkLoginToMessage(){
  const loginStatus = <?php echo isset($_SESSION['login']) && $_SESSION['login'] ? 'true' : 'false'; ?>;
  if(!loginStatus){
    const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
    const msg = currentLang === 'en' ? 'Please login to send a message' : 'Vui lòng đăng nhập để nhắn tin';
    if(typeof showToast === 'function'){
      showToast('warning', msg, 3000);
    } else {
      alert(msg);
    }
    // Mở modal đăng nhập nếu có
    const loginModal = document.getElementById('loginModal');
    if(loginModal){
      const bsModal = new bootstrap.Modal(loginModal);
      bsModal.show();
    }
  } else {
    // Nếu đã đăng nhập, chuyển đến trang tin nhắn
    window.location.href = '<?php echo $message_link; ?>';
  }
}

// Cập nhật mô tả phòng khi đổi ngôn ngữ
function updateRoomDescription() {
  try {
    const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
    const descAttr = currentLang === 'en' ? 'data-description-en' : 'data-description-vi';
    
    console.log('Updating room description, lang:', currentLang, 'attr:', descAttr);
    
    // Helper function để format description
    function formatDescription(text, isShort = false) {
      if(!text) return '';
      
      // Decode HTML entities
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = text;
      let decodedText = tempDiv.textContent || tempDiv.innerText || text;
      
      // Nếu là mô tả ngắn và dài hơn 200 ký tự, cắt ngắn
      if(isShort) {
        const shortLength = 200;
        if(decodedText.length > shortLength) {
          decodedText = decodedText.substring(0, shortLength) + '...';
        }
      }
      
      // Chuyển line breaks thành <br> và escape HTML
      return decodedText.replace(/\n/g, '<br>');
    }
    
    // Cập nhật phần mô tả ngắn - LUÔN cập nhật để đảm bảo đúng ngôn ngữ
    const shortDesc = document.getElementById('room-description-short');
    if(shortDesc) {
      const newDesc = shortDesc.getAttribute(descAttr);
      console.log('Short desc found, newDesc:', newDesc ? newDesc.substring(0, 50) + '...' : 'null');
      if(newDesc) {
        const formatted = formatDescription(newDesc, true);
        // Luôn cập nhật để đảm bảo đúng ngôn ngữ
        shortDesc.innerHTML = formatted;
      }
    }
    
    // Cập nhật phần mô tả đầy đủ - LUÔN cập nhật để đảm bảo đúng ngôn ngữ
    const fullDesc = document.getElementById('room-description-full');
    if(fullDesc) {
      const newDesc = fullDesc.getAttribute(descAttr);
      console.log('Full desc found, newDesc:', newDesc ? newDesc.substring(0, 50) + '...' : 'null');
      if(newDesc) {
        const formatted = formatDescription(newDesc, false);
        // Luôn cập nhật để đảm bảo đúng ngôn ngữ
        fullDesc.innerHTML = formatted;
      }
    }
    
    // Cập nhật phần mô tả không có toggle (khi mô tả ngắn)
    const simpleDesc = document.querySelector('.desc-box p.text-muted[data-description-vi]');
    if(simpleDesc) {
      const newDesc = simpleDesc.getAttribute(descAttr);
      if(newDesc) {
        simpleDesc.innerHTML = formatDescription(newDesc, false);
      }
    }
  } catch(e) {
    console.error('Error updating room description:', e);
  }
}

// Gọi updateRoomDescription ngay khi DOM ready
document.addEventListener('DOMContentLoaded', function() {
  // Đợi một chút để đảm bảo i18n đã được khởi tạo
  setTimeout(function() {
    updateRoomDescription();
  }, 300);
});

// Cập nhật facilities khi đổi ngôn ngữ
function updateFacilities() {
  const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
  
  // Cập nhật tên và mô tả facilities
  document.querySelectorAll('.facility-name').forEach(function(el) {
    const attr = currentLang === 'en' ? 'data-name-en' : 'data-name-vi';
    const translated = el.getAttribute(attr);
    if(translated) {
      el.textContent = translated;
    }
  });
  
  document.querySelectorAll('.facility-desc').forEach(function(el) {
    const attr = currentLang === 'en' ? 'data-desc-en' : 'data-desc-vi';
    const translated = el.getAttribute(attr);
    if(translated) {
      el.textContent = translated + '...';
    }
  });
}

document.addEventListener('languageChanged', function() {
  const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
  
  // Cập nhật mô tả phòng
  setTimeout(function() {
    updateRoomDescription();
  }, 100);
  
  // Cập nhật facilities
  setTimeout(function() {
    updateFacilities();
  }, 100);
  
  // Map facilities translations (giữ lại để tương thích nếu cần)
  const facilityNameMap = {
    'vi': {
      'Kettle': 'Ấm Đun Nước',
      'Iron': 'Bàn Ủi',
      'Slippers': 'Dép Đi Trong Nhà',
      '24/24 Hour Service': 'Dịch vụ 24/24 giờ',
      'Full-length Mirror': 'Gương Toàn Thân',
      'Air Conditioner': 'Máy Lạnh',
      'Water Heater': 'Máy Nước Nóng',
      'Hair Dryer': 'Máy Sấy Tóc',
      'Heater': 'Máy Sưởi',
      'Spa': 'Spa',
      'Television': 'Truyền Hình',
      'Wardrobe': 'Tủ Quần Áo',
    },
    'en': {
      'Ấm Đun Nước': 'Kettle',
      'Bàn Ủi': 'Iron',
      'Dép Đi Trong Nhà': 'Slippers',
      'Dịch vụ 24/24 giờ': '24/24 Hour Service',
      'Gương Toàn Thân': 'Full-length Mirror',
      'Máy Lạnh': 'Air Conditioner',
      'Máy Nước Nóng': 'Water Heater',
      'Máy Sấy Tóc': 'Hair Dryer',
      'Máy Sưởi': 'Heater',
      'Spa': 'Spa',
      'Truyền Hình': 'Television',
      'Tủ Quần Áo': 'Wardrobe',
    }
  };
  
  const facilityDescMap = {
    'vi': {
      'Super-fast kettle helps make tea, coffee quickly and conveniently.': 'Ấm đun nước siêu tốc giúp pha trà, cà phê nhanh chóng và tiện lợi.',
      'Convenient iron helps clothes always flat and neat.': 'Bàn ủi tiện dụng giúp quần áo luôn phẳng phiu, chỉn chu.',
      'Soft slippers, ensuring hygiene and comfort when moving around the room.': 'Dép mềm mại, đảm bảo vệ sinh và thoải mái khi đi lại trong phòng.',
      '24/24 reception service, always ready to assist customers at any time.': 'Dịch vụ lễ tân 24/24, luôn sẵn sàng hỗ trợ khách hàng mọi lúc.',
      'Large full-length mirror, convenient for grooming before going out.': 'Gương soi toàn thân lớn, tiện lợi cho việc chỉnh trang trước khi ra ngoài.',
      'Modern air conditioning system, creating a cool and comfortable space.': 'Hệ thống điều hòa không khí hiện đại, tạo không gian mát mẻ và dễ chịu.',
      'Convenient water heater, providing hot water 24/7 for daily needs.': 'Máy nước nóng tiện lợi, cung cấp nước nóng 24/7 cho nhu cầu sinh hoạt.',
      'Easy to dry hair after bathing, convenient and quick.': 'Dễ dàng sấy tóc sau khi tắm, tiện lợi và nhanh chóng.',
      'High-quality heating system, keeping the space warm, especially suitable for cold days.': 'Hệ thống sưởi ấm chất lượng cao, giữ không gian ấm áp, đặc biệt phù hợp vào những ngày lạnh giá.',
      'Professional spa service with relaxing treatments, body care, and health recovery.': 'Dịch vụ spa chuyên nghiệp với liệu trình thư giãn, chăm sóc cơ thể và phục hồi sức khỏe.',
      'Flat-screen TV with a variety of domestic and international entertainment channels, meeting customers\' relaxation needs.': 'TV màn hình phẳng với đa dạng kênh giải trí trong nước và quốc tế, đáp ứng nhu cầu thư giãn của khách hàng.',
      'Spacious wardrobe, helps organize clothes and personal items neatly.': 'Tủ đựng đồ rộng rãi, giúp sắp xếp quần áo và đồ dùng cá nhân một cách gọn gàng.',
    },
    'en': {
      'Ấm đun nước siêu tốc giúp pha trà, cà phê nhanh chóng và tiện lợi.': 'Super-fast kettle helps make tea, coffee quickly and conveniently.',
      'Bàn ủi tiện dụng giúp quần áo luôn phẳng phiu, chỉn chu.': 'Convenient iron helps clothes always flat and neat.',
      'Dép mềm mại, đảm bảo vệ sinh và thoải mái khi đi lại trong phòng.': 'Soft slippers, ensuring hygiene and comfort when moving around the room.',
      'Dịch vụ lễ tân 24/24, luôn sẵn sàng hỗ trợ khách hàng mọi lúc.': '24/24 reception service, always ready to assist customers at any time.',
      'Gương soi toàn thân lớn, tiện lợi cho việc chỉnh trang trước khi ra ngoài.': 'Large full-length mirror, convenient for grooming before going out.',
      'Hệ thống điều hòa không khí hiện đại, tạo không gian mát mẻ và dễ chịu.': 'Modern air conditioning system, creating a cool and comfortable space.',
      'Máy nước nóng tiện lợi, cung cấp nước nóng 24/7 cho nhu cầu sinh hoạt.': 'Convenient water heater, providing hot water 24/7 for daily needs.',
      'Dễ dàng sấy tóc sau khi tắm, tiện lợi và nhanh chóng.': 'Easy to dry hair after bathing, convenient and quick.',
      'Hệ thống sưởi ấm chất lượng cao, giữ không gian ấm áp, đặc biệt phù hợp vào những ngày lạnh giá.': 'High-quality heating system, keeping the space warm, especially suitable for cold days.',
      'Dịch vụ spa chuyên nghiệp với liệu trình thư giãn, chăm sóc cơ thể và phục hồi sức khỏe.': 'Professional spa service with relaxing treatments, body care, and health recovery.',
      'TV màn hình phẳng với đa dạng kênh giải trí trong nước và quốc tế, đáp ứng nhu cầu thư giãn của khách hàng.': 'Flat-screen TV with a variety of domestic and international entertainment channels, meeting customers\' relaxation needs.',
      'Tủ đựng đồ rộng rãi, giúp sắp xếp quần áo và đồ dùng cá nhân một cách gọn gàng.': 'Spacious wardrobe, helps organize clothes and personal items neatly.',
    }
  };
});

// Gọi updateFacilities ngay khi DOM ready
document.addEventListener('DOMContentLoaded', function() {
  setTimeout(function() {
    updateFacilities();
  }, 300);
});

// Toggle room description
function toggleRoomDescription(){
  const shortDesc = document.getElementById('room-description-short');
  const fullDesc = document.getElementById('room-description-full');
  const toggleBtn = document.getElementById('toggle-description-btn');
  
  if(!shortDesc || !fullDesc || !toggleBtn) return;
  
  // Cập nhật mô tả trước khi toggle (để đảm bảo đúng ngôn ngữ)
  updateRoomDescription();
  
  const isExpanded = fullDesc.style.display === 'block' || !fullDesc.classList.contains('d-none');
  
  if(isExpanded){
    // Thu gọn
    shortDesc.style.display = 'block';
    fullDesc.style.display = 'none';
    fullDesc.classList.add('d-none');
    const readMoreText = window.i18n && window.i18n.translate ? window.i18n.translate('roomDetails.readMore') : 'Xem thêm';
    toggleBtn.innerHTML = `<span data-i18n="roomDetails.readMore">${readMoreText}</span> <i class="bi bi-chevron-down ms-1"></i>`;
    
    // Cập nhật lại mô tả ngắn sau khi thu gọn
    setTimeout(function() {
      updateRoomDescription();
    }, 50);
  } else {
    // Mở rộng
    shortDesc.style.display = 'none';
    fullDesc.style.display = 'block';
    fullDesc.classList.remove('d-none');
    const readLessText = window.i18n && window.i18n.translate ? window.i18n.translate('roomDetails.readLess') : 'Thu gọn';
    toggleBtn.innerHTML = `<span data-i18n="roomDetails.readLess">${readLessText}</span> <i class="bi bi-chevron-up ms-1"></i>`;
    
    // Cập nhật lại mô tả đầy đủ sau khi mở rộng
    setTimeout(function() {
      updateRoomDescription();
    }, 50);
  }
  
  // Cập nhật i18n nếu có
  if(window.i18n && window.i18n.updateTranslations){
    window.i18n.updateTranslations();
  }
  
  // Lưu phòng đã xem vào localStorage
  function saveRecentlyViewedRoom() {
    const roomId = <?php echo $room_data['id']; ?>;
    if(!roomId) return;
    
    try {
      // Lấy danh sách phòng đã xem
      let viewedRooms = JSON.parse(localStorage.getItem('recently_viewed_rooms') || '[]');
      
      // Loại bỏ room_id nếu đã tồn tại (để đưa lên đầu)
      viewedRooms = viewedRooms.filter(id => id !== roomId);
      
      // Thêm room_id vào đầu danh sách
      viewedRooms.unshift(roomId);
      
      // Giới hạn tối đa 10 phòng
      viewedRooms = viewedRooms.slice(0, 10);
      
      // Lưu lại vào localStorage
      localStorage.setItem('recently_viewed_rooms', JSON.stringify(viewedRooms));
    } catch(e) {
      console.error('Error saving recently viewed room:', e);
    }
  }
  
  // Gọi hàm khi trang load
  if(document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', saveRecentlyViewedRoom);
  } else {
    saveRecentlyViewedRoom();
  }
}
</script>

</body>
</html>
