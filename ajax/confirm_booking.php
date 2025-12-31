<?php 
require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');
require('../admin/inc/promos_helper.php');

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

header('Content-Type: application/json');

// Kiểm tra mã khuyến mãi
if(isset($_POST['check_promo']))
{
    if(!isset($_SESSION['room']['id'])){
        echo json_encode(["status"=>"session_error"]);
        exit;
    }

    $frm = filteration($_POST);
    $code = strtoupper(trim($frm['code'] ?? ''));
    $checkin  = $frm['check_in'] ?? '';
    $checkout = $frm['check_out'] ?? '';

    if($code === ''){
        echo json_encode(["status"=>"invalid_code","msg"=>"Vui lòng nhập mã khuyến mãi."]);
        exit;
    }
    if(empty($checkin) || empty($checkout)){
        echo json_encode(["status"=>"missing_date","msg"=>"Chọn ngày check-in/check-out trước khi kiểm tra mã."]);
        exit;
    }

    try{
        $ci = new DateTime($checkin);
        $co = new DateTime($checkout);
    } catch(Exception $e){
        echo json_encode(["status"=>"invalid_date","msg"=>"Ngày không hợp lệ."]);
        exit;
    }

    if($ci >= $co){
        echo json_encode(["status"=>"invalid_date","msg"=>"Check-out phải sau check-in."]);
        exit;
    }

    $days = date_diff($ci,$co)->days;
    if($days <= 0){ $days = 1; }

    $price = isset($_SESSION['room']['price']) ? (int)$_SESSION['room']['price'] : 0;
    $base = $price * $days;
    $tax  = round($base * 0.08);
    $svc  = round($base * 0.02);
    $total_before = $base + $tax + $svc;

    $promo_rate = 0;
    $discount = 0;
    $promo_note = '';

    $promo_row = getPromoByCode($code);
    if($promo_row){
      $min_amount = (int)$promo_row['min_amount'];
      if($total_before >= $min_amount){
        if($promo_row['discount_type'] === 'flat'){
          $discount = (int)$promo_row['discount_value'];
        } else {
          $discount = round($total_before * ($promo_row['discount_value'] / 100));
        }
        if(!empty($promo_row['max_discount'])){
          $discount = min($discount, (int)$promo_row['max_discount']);
        }
        $promo_rate = (float)$promo_row['discount_value'];
        $promo_note = $promo_row['title'] ?: $code;
      } else {
        echo json_encode(["status"=>"min_not_met","msg"=>"Đơn tối thiểu ".number_format($min_amount)." VND","total_before"=>$total_before]);
        exit;
      }
    } else {
      // fallback mã mẫu
      $fallback_promos = [
        'VINHLONG10' => ['type'=>'percent','value'=>10,'min'=>0,'cap'=>null,'note'=>'Giảm 10%'],
        'RIVERVIEW15'=> ['type'=>'percent','value'=>15,'min'=>0,'cap'=>null,'note'=>'Giảm 15%'],
        'KS1212QT'   => ['type'=>'percent','value'=>2,'min'=>3000000,'cap'=>500000,'note'=>'Giảm 2% tối đa 500k, đơn từ 3 triệu'],
        'KS1212VN'   => ['type'=>'percent','value'=>4,'min'=>2000000,'cap'=>300000,'note'=>'Giảm 4% tối đa 300k, đơn từ 2 triệu'],
        'WKND100'    => ['type'=>'percent','value'=>3,'min'=>0,'cap'=>100000,'note'=>'Giảm 3% tối đa 100k']
      ];

      if(isset($fallback_promos[$code])){
        $p = $fallback_promos[$code];
        if($total_before >= $p['min']){
          if($p['type'] === 'flat'){
            $discount = (int)$p['value'];
          } else {
            $discount = round($total_before * ($p['value'] / 100));
          }
          if(!empty($p['cap'])){
            $discount = min($discount, (int)$p['cap']);
          }
          $promo_rate = $p['value'];
          $promo_note = $p['note'];
        } else {
          echo json_encode(["status"=>"min_not_met","msg"=>"Đơn tối thiểu ".number_format($p['min'])." VND","total_before"=>$total_before]);
          exit;
        }
      } else {
        echo json_encode(["status"=>"invalid_code","msg"=>"Mã không hợp lệ hoặc đã hết hiệu lực."]);
        exit;
      }
    }

    $total_after = max(0, $total_before - $discount);
    echo json_encode([
      "status"=>"ok",
      "discount"=>$discount,
      "total_before"=>$total_before,
      "total_after"=>$total_after,
      "code"=>$code,
      "note"=>$promo_note
    ]);
    exit;
}

// Kiểm tra phòng trống
if(isset($_POST['check_availability']))
{
    if(!isset($_SESSION['room']['id'])){
        echo json_encode(["status"=>"session_error"]);
        exit;
    }

    $frm_data = filteration($_POST);

    $today_date = new DateTime(date("Y-m-d"));
    $checkin_date = new DateTime($frm_data['check_in']);
    $checkout_date = new DateTime($frm_data['check_out']);

    if($checkin_date == $checkout_date){
        echo json_encode(["status"=>"check_in_out_equal"]);
        exit;
    }

    if($checkout_date < $checkin_date){
        echo json_encode(["status"=>"check_out_earlier"]);
        exit;
    }

    if($checkin_date < $today_date){
        echo json_encode(["status"=>"check_in_earlier"]);
        exit;
    }

    $room_id = $_SESSION['room']['id'];

    $remain_q = select("SELECT remaining FROM rooms WHERE id=?", [$room_id], "i");
    $remain = mysqli_fetch_assoc($remain_q);
    $remaining = isset($remain['remaining']) ? max(0, (int)$remain['remaining']) : 1; // fallback 1 nếu thiếu dữ liệu

    if($remaining <= 0){
        echo json_encode(["status"=>"unavailable"]);
        exit;
    }

    // Kiểm tra overlap: booking overlap nếu (check_in < user_checkout) AND (check_out > user_checkin)
    // Nếu có BẤT KỲ booking nào overlap, thì chặn luôn
    $overlap_query = "SELECT COUNT(*) AS total_bookings 
                      FROM booking_order
                      WHERE booking_status IN ('booked','pending')
                      AND room_id=?
                      AND check_in < ?
                      AND check_out > ?";

    $overlap_result = mysqli_fetch_assoc(
        select($overlap_query, 
               [$room_id, $frm_data['check_out'], $frm_data['check_in']], 
               "iss")
    );
    $overlap_count = (int)$overlap_result['total_bookings'];

    // CHẶN NẾU: có booking overlap
    if($overlap_count > 0){
        echo json_encode([
            "status"=>"unavailable",
            "msg"=>"Khoảng thời gian này đã có đặt phòng. Vui lòng chọn ngày khác."
        ]);
        exit;
    }

    $count_days = date_diff($checkin_date,$checkout_date)->days;
    $payment = $_SESSION['room']['price'] * $count_days;
    
    // Tính slots_left
    $slots_left = $remaining - $overlap_count;

    $_SESSION['room']['payment'] = $payment;
    $_SESSION['room']['available'] = true;

    echo json_encode([
        "status"=>"available",
        "days"=>$count_days,
        "payment"=>$payment,
        "slots_left"=>$slots_left
    ]);
}
?>
