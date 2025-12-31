<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('admin/inc/db_config.php');
require('admin/inc/essentials.php');
require('admin/inc/promos_helper.php');

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if(!(isset($_SESSION['login']) && $_SESSION['login'] == true)){
  redirect('index.php');
}

if(isset($_POST['pay_now']))
{
    if(!isset($_SESSION['room']['id'])){
        redirect('rooms.php');
    }

    $ORDER_ID = 'ORD_'.$_SESSION['uId'].random_int(11111,9999999);    
    $CUST_ID  = $_SESSION['uId'];
    $room_id  = $_SESSION['room']['id'];

    $frm_data = filteration($_POST);
    $checkin  = $frm_data['checkin'];
    $checkout = $frm_data['checkout'];
    $payment_confirmed = isset($frm_data['payment_confirmed']) ? (int)$frm_data['payment_confirmed'] : 0;

    if($payment_confirmed !== 1){
        redirect('confirm_booking.php?id='.$room_id.'&msg=payment_not_confirmed');
        exit;
    }

    /*--------------------------------------------
      1) VALIDATE NGÀY
    ---------------------------------------------*/
    $today = new DateTime(date('Y-m-d'));
    $ci = new DateTime($checkin);
    $co = new DateTime($checkout);
    if($ci >= $co || $ci < $today){
        redirect('rooms.php?msg=invalid_date');
        exit;
    }

    /*--------------------------------------------
      2) KIỂM TRA PHÒNG CÒN VÀ CHỒNG LỊCH
    ---------------------------------------------*/
    $remain_q = select("SELECT remaining FROM rooms WHERE id=?", [$room_id], "i");
    $remain   = mysqli_fetch_assoc($remain_q);

    if($remain['remaining'] <= 0){
        redirect('rooms.php?msg=hetphong');
        exit;
    }

    $tb_query = "SELECT COUNT(*) AS total_bookings 
                 FROM booking_order
                 WHERE booking_status IN ('booked','pending')
                 AND room_id=?
                 AND check_out > ?
                 AND check_in < ?";
    $tb_fetch = mysqli_fetch_assoc(select($tb_query, [$room_id, $checkin, $checkout], "iss"));
    if($tb_fetch['total_bookings'] >= $remain['remaining']){
        redirect('rooms.php?msg=unavailable');
        exit;
    }

    /*--------------------------------------------
      3) TÍNH TIỀN AN TOÀN + PROMO
    ---------------------------------------------*/
    $days = $ci->diff($co)->days;
    $base = $_SESSION['room']['price'] * $days;
    $tax  = round($base * 0.08);
    $svc  = round($base * 0.02);

    $promo_code = strtoupper(trim($frm_data['promo_code_value'] ?? ''));
    $promo_rate = 0;
    $discount = 0;
    $total_before = $base + $tax + $svc;
    $promo_note = '';

    // Ưu tiên bảng promos do admin quản lý
    $promo_row = !empty($promo_code) ? getPromoByCode($promo_code) : null;

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
        $promo_note = $promo_row['title'] ?: $promo_code;
      }
    } else {
      // Fallback tĩnh khi chưa cấu hình bảng
      $fallback_promos = [
        'VINHLONG10' => ['type'=>'percent','value'=>10,'min'=>0,'cap'=>null,'note'=>'Giảm 10%'],
        'RIVERVIEW15'=> ['type'=>'percent','value'=>15,'min'=>0,'cap'=>null,'note'=>'Giảm 15%'],
        'KS1212QT'   => ['type'=>'percent','value'=>2,'min'=>3000000,'cap'=>500000,'note'=>'Giảm 2% tối đa 500k, đơn từ 3 triệu'],
        'KS1212VN'   => ['type'=>'percent','value'=>4,'min'=>2000000,'cap'=>300000,'note'=>'Giảm 4% tối đa 300k, đơn từ 2 triệu'],
        'WKND100'    => ['type'=>'percent','value'=>3,'min'=>0,'cap'=>100000,'note'=>'Giảm 3% tối đa 100k']
      ];

      if(isset($fallback_promos[$promo_code])){
        $p = $fallback_promos[$promo_code];
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
        }
      }
    }

    $TXN_AMOUNT = $total_before - $discount;
    if($TXN_AMOUNT < 0) $TXN_AMOUNT = 0;

    /*--------------------------------------------
      4) GHÉP GHI CHÚ (PROMO + YÊU CẦU)
    ---------------------------------------------*/
    $address = $frm_data['address'];
    if(!empty($frm_data['special_request'])){
        $address .= " | SR: ".$frm_data['special_request'];
    }
    if($promo_rate > 0){
        $address .= " | Mã KM: ".$promo_code." (-".$discount." VND)";
    }

    /*--------------------------------------------
      5) TẠO BOOKING ORDER
    ---------------------------------------------*/
    $query1 = "INSERT INTO booking_order(user_id, room_id, check_in, check_out, order_id)
               VALUES (?,?,?,?,?)";

    insert($query1, [
        $CUST_ID,
        $room_id,
        $checkin,
        $checkout,
        $ORDER_ID
    ], 'issss');
    
    $booking_id = mysqli_insert_id($con);

    /*--------------------------------------------
      6) LƯU CHI TIẾT BOOKING
    ---------------------------------------------*/
    $query2 = "INSERT INTO booking_details(booking_id, room_name, price, total_pay,
                user_name, phonenum, address)
               VALUES (?,?,?,?,?,?,?)";

    insert($query2, [
        $booking_id,
        $_SESSION['room']['name'],
        $_SESSION['room']['price'],
        $TXN_AMOUNT,
        $frm_data['name'],
        $frm_data['phonenum'],
        $address
    ], 'issssss');

    /*--------------------------------------------
      7) TRỪ PHÒNG
    ---------------------------------------------*/
    $update_q = "UPDATE rooms 
                 SET remaining = GREATEST(remaining - 1, 0)
                 WHERE id=?";
    update($update_q, [$room_id], "i");

    /*--------------------------------------------
      8) CẬP NHẬT TRẠNG THÁI THANH TOÁN
    ---------------------------------------------*/
    $trans_id = $ORDER_ID;
    $resp_msg = "QR mock success";
    $upd_pay = "UPDATE booking_order 
                SET trans_id=?, trans_amt=?, trans_status='paid', trans_resp_msg=?, booking_status='booked'
                WHERE booking_id=?";
    update($upd_pay, [$trans_id, $TXN_AMOUNT, $resp_msg, $booking_id], "sisi");

    // Lưu lịch sử áp dụng mã KM (nếu có)
    if($promo_rate > 0 && !empty($promo_code)){
      logPromoUsage($CUST_ID, $ORDER_ID, $booking_id, $promo_code, $discount);
    }
}

/*--------------------------------------------
  9) CHUYỂN TỚI TRANG LỊCH SỬ ĐẶT PHÒNG
---------------------------------------------*/
redirect('bookings.php');
?>
