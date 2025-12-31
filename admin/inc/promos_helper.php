<?php

/**
 * Đảm bảo bảng promos tồn tại (dành cho quản trị mã giảm giá).
 * Bảng này lưu đầy đủ thông tin để áp dụng giảm giá phía client và khi thanh toán.
 */
function ensurePromosTable($con){
  $sql = "CREATE TABLE IF NOT EXISTS `promos` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `label` VARCHAR(120) DEFAULT '',
    `title` VARCHAR(255) NOT NULL,
    `description` VARCHAR(255) DEFAULT '',
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `category` ENUM('hot','bank','wallet','destination') NOT NULL DEFAULT 'hot',
    `discount_type` ENUM('percent','flat') NOT NULL DEFAULT 'percent',
    `discount_value` INT NOT NULL DEFAULT 0,
    `min_amount` INT NOT NULL DEFAULT 0,
    `max_discount` INT DEFAULT NULL,
    `priority` INT NOT NULL DEFAULT 0,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `expires_at` DATE DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

  mysqli_query($con, $sql);
}

/**
 * Lưu lịch sử áp dụng mã KM.
 */
function ensurePromoUsageTable($con){
  $sql = "CREATE TABLE IF NOT EXISTS `promo_usage` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `order_id` VARCHAR(50) NOT NULL,
    `booking_id` INT UNSIGNED DEFAULT NULL,
    `promo_code` VARCHAR(50) NOT NULL,
    `discount_amount` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_order` (`order_id`),
    KEY `idx_code` (`promo_code`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
  mysqli_query($con, $sql);
}

function logPromoUsage($user_id, $order_id, $booking_id, $code, $discount){
  $con = $GLOBALS['con'];
  if(!$code){ return; }
  ensurePromoUsageTable($con);
  $user_id = (int)$user_id;
  $booking_id = (int)$booking_id;
  $order_id = mysqli_real_escape_string($con, $order_id);
  $code = mysqli_real_escape_string($con, strtoupper(trim($code)));
  $discount = (int)$discount;
  mysqli_query($con, "
    INSERT INTO promo_usage(user_id, order_id, booking_id, promo_code, discount_amount)
    VALUES ($user_id, '$order_id', $booking_id, '$code', $discount)
  ");
}

/**
 * Lưu danh sách mã KM đã lưu của user.
 */
function ensureSavedPromoTable($con){
  $sql = "CREATE TABLE IF NOT EXISTS `promo_saved` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `promo_code` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_user_code` (`user_id`,`promo_code`),
    KEY `idx_user` (`user_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
  mysqli_query($con, $sql);
}

/**
 * Tạo dữ liệu mẫu khi bảng đang rỗng.
 */
function seedDefaultPromos($con){
  $check = mysqli_query($con,"SELECT COUNT(*) AS c FROM `promos`");
  $cnt = ($check && $row=mysqli_fetch_assoc($check)) ? (int)$row['c'] : 0;
  if($cnt>0) return;

  $defaults = [
    ['label'=>'Hết hạn sau 2 ngày','title'=>'Giảm đến 500.000đ quốc tế','description'=>'Giảm 2% tối đa 500.000đ · Đặt tối thiểu 3.000.000đ','code'=>'KS1212QT','category'=>'hot','discount_type'=>'percent','discount_value'=>2,'min_amount'=>3000000,'max_discount'=>500000,'priority'=>30,'active'=>1],
    ['label'=>'Sắp hết mã','title'=>'Giảm 50% phòng nội địa','description'=>'Giảm 4% tối đa 300.000đ · Đặt tối thiểu 2.000.000đ','code'=>'KS1212VN','category'=>'hot','discount_type'=>'percent','discount_value'=>4,'min_amount'=>2000000,'max_discount'=>300000,'priority'=>25,'active'=>1],
    ['label'=>'Có hạn','title'=>'Giảm 100.000đ cuối tuần','description'=>'Giảm 3% tối đa 100.000đ · Không yêu cầu tối thiểu','code'=>'WKND100','category'=>'hot','discount_type'=>'percent','discount_value'=>3,'min_amount'=>0,'max_discount'=>100000,'priority'=>20,'active'=>1],
    ['label'=>'Ưu đãi ngân hàng','title'=>'Giảm 10% thanh toán thẻ','description'=>'Giảm 10% tối đa 400.000đ · Thứ 6 hàng tuần','code'=>'BANK10','category'=>'bank','discount_type'=>'percent','discount_value'=>10,'min_amount'=>500000,'max_discount'=>400000,'priority'=>18,'active'=>1],
    ['label'=>'Thanh toán QR','title'=>'Giảm 5% qua ví/QR','description'=>'Giảm 5% tối đa 150.000đ · Cho mọi đơn','code'=>'WALLET5','category'=>'wallet','discount_type'=>'percent','discount_value'=>5,'min_amount'=>0,'max_discount'=>150000,'priority'=>15,'active'=>1],
    ['label'=>'Điểm đến hot','title'=>'Giảm 8% phòng Vĩnh Long','description'=>'Giảm 8% tối đa 250.000đ · Chỉ áp dụng Vĩnh Long','code'=>'HOTVL8','category'=>'destination','discount_type'=>'percent','discount_value'=>8,'min_amount'=>800000,'max_discount'=>250000,'priority'=>12,'active'=>1],
  ];

  foreach($defaults as $p){
    mysqli_query($con, "
      INSERT INTO `promos`
      (`label`,`title`,`description`,`code`,`category`,`discount_type`,`discount_value`,`min_amount`,`max_discount`,`priority`,`active`)
      VALUES (
        '".mysqli_real_escape_string($con,$p['label'])."',
        '".mysqli_real_escape_string($con,$p['title'])."',
        '".mysqli_real_escape_string($con,$p['description'])."',
        '".mysqli_real_escape_string($con,$p['code'])."',
        '".mysqli_real_escape_string($con,$p['category'])."',
        '".$p['discount_type']."',
        '".(int)$p['discount_value']."',
        '".(int)$p['min_amount']."',
        ".(isset($p['max_discount']) ? (int)$p['max_discount'] : "NULL").",
        '".(int)$p['priority']."',
        '".(int)$p['active']."'
      )
    ");
  }
}

/**
 * Lấy danh sách mã khuyến mãi đang kích hoạt (có thể giới hạn số lượng).
 *
 * @param int|null $limit
 * @return array
 */
function getActivePromos($limit = null){
  $con = $GLOBALS['con'];
  ensurePromosTable($con);

  $limit_sql = '';
  if($limit !== null){
    $limit_sql = ' LIMIT '.intval($limit);
  }

  $res = mysqli_query($con, "
    SELECT * FROM `promos`
    WHERE `active`=1
      AND (`expires_at` IS NULL OR `expires_at` >= CURDATE())
    ORDER BY `priority` DESC, `id` DESC
    $limit_sql
  ");

  $data = [];
  if($res){
    while($row = mysqli_fetch_assoc($res)){
      $data[] = $row;
    }
  }
  return $data;
}

/**
 * Lấy thông tin một mã giảm giá đang còn hiệu lực theo code.
 *
 * @param string $code
 * @return array|null
 */
function getPromoByCode($code){
  $con = $GLOBALS['con'];
  ensurePromosTable($con);
  $code = strtoupper(trim($code));

  $res = select(
    "SELECT * FROM `promos`
      WHERE `code`=? AND `active`=1
        AND (`expires_at` IS NULL OR `expires_at` >= CURDATE())
      LIMIT 1",
    [$code],
    's'
  );

  if($res && mysqli_num_rows($res)){
    return mysqli_fetch_assoc($res);
  }
  return null;
}

?>
