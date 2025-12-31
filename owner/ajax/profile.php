<?php
require_once __DIR__ . '/../../admin/inc/db_config.php';
require_once __DIR__ . '/../../admin/inc/essentials.php';
require_once __DIR__ . '/../inc/essentials.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ownerLogin();

$owner_id = getOwnerId();

/* =====================================================
   ===============   GỬI OTP ĐẾN EMAIL CŨ   ============
   ===================================================== */
if(isset($_POST['send_email_change_otp']))
{
    // Set header
    header('Content-Type: text/plain; charset=utf-8');
    
    // Kiểm tra session
    if(!isset($owner_id) || empty($owner_id)){
        echo 'session_error';
        exit;
    }

    $frm_data = filteration($_POST);
    $new_email = strtolower(trim($frm_data['new_email'] ?? ''));

    // Validate email mới
    if(empty($new_email)){
        echo 'invalid_email';
        exit;
    }

    if(!filter_var($new_email, FILTER_VALIDATE_EMAIL)){
        echo 'invalid_email';
        exit;
    }

    // Lấy email hiện tại của owner
    $owner_exist = select("SELECT `email`, `name` FROM `hotel_owners` WHERE `id`=? LIMIT 1", [$owner_id], "i");
    if(!$owner_exist || mysqli_num_rows($owner_exist) == 0){
        echo 'owner_not_found';
        exit;
    }
    $owner_fetch = mysqli_fetch_assoc($owner_exist);
    $current_email = strtolower(trim($owner_fetch['email']));
    $owner_name = $owner_fetch['name'];

    // Kiểm tra email mới có khác email cũ không
    if($new_email === $current_email){
        echo 'same_email';
        exit;
    }

    // Kiểm tra email mới đã được sử dụng chưa
    $email_check = select("SELECT `id` FROM `hotel_owners` WHERE `email`=? AND `id`!=? LIMIT 1", [$new_email, $owner_id], "si");
    if($email_check && mysqli_num_rows($email_check) > 0){
        echo 'email_already';
        exit;
    }

    // Tạo mã OTP 6 số
    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Lưu OTP vào session (hết hạn sau 10 phút)
    $_SESSION['owner_email_change_otp'] = $otp;
    $_SESSION['owner_email_change_new_email'] = $new_email;
    $_SESSION['owner_email_change_current_email'] = $current_email;
    $_SESSION['owner_email_change_expire'] = time() + (10 * 60);

    // Gửi email OTP đến EMAIL CŨ
    error_log("OWNER EMAIL CHANGE OTP - Attempting to send OTP to CURRENT email: $current_email, OTP: $otp, Owner: $owner_name");
    
    // Kiểm tra xem hàm sendOTPEmail có tồn tại không
    if(!function_exists('sendOTPEmail')){
        echo 'otp_send_failed';
        exit;
    }
    
    // Gửi OTP đến EMAIL CŨ (không phải email mới)
    $sent = sendOTPEmail($current_email, $otp, $owner_name);
    error_log("OWNER EMAIL CHANGE OTP - Email send result: " . ($sent ? 'SUCCESS' : 'FAILED'));

    if($sent){
        // Log OTP vào file (chỉ khi ở môi trường development)
        if(function_exists('logOTP')) {
            logOTP('OWNER_EMAIL_CHANGE', $current_email, $otp, $owner_name, true);
        }
        
        echo 'otp_sent';
    } else {
        error_log("OWNER EMAIL CHANGE OTP - FAILED: Could not send email to $current_email");
        echo 'otp_send_failed';
    }
    exit;
}

/* =====================================================
   ===============   XÁC THỰC OTP VÀ ĐỔI EMAIL   =====
   ===================================================== */
if(isset($_POST['verify_email_change_otp']))
{
    // Set header
    header('Content-Type: text/plain; charset=utf-8');
    
    // Kiểm tra session
    if(!isset($owner_id) || empty($owner_id)){
        echo 'session_error';
        exit;
    }

    $frm_data = filteration($_POST);
    $otp_input = trim($frm_data['otp'] ?? '');

    // Kiểm tra OTP có trong session không
    if(!isset($_SESSION['owner_email_change_otp']) || !isset($_SESSION['owner_email_change_new_email'])){
        echo 'otp_not_found';
        exit;
    }

    // Kiểm tra OTP hết hạn chưa
    if(!isset($_SESSION['owner_email_change_expire']) || time() > $_SESSION['owner_email_change_expire']){
        unset($_SESSION['owner_email_change_otp'], $_SESSION['owner_email_change_new_email'], $_SESSION['owner_email_change_current_email'], $_SESSION['owner_email_change_expire']);
        echo 'otp_expired';
        exit;
    }

    // Kiểm tra OTP có đúng không
    if($otp_input !== $_SESSION['owner_email_change_otp']){
        echo 'otp_invalid';
        exit;
    }

    // Lấy email mới từ session
    $new_email = $_SESSION['owner_email_change_new_email'];

    // Kiểm tra lại email mới đã được sử dụng chưa (để tránh race condition)
    $email_check = select("SELECT `id` FROM `hotel_owners` WHERE `email`=? AND `id`!=? LIMIT 1", [$new_email, $owner_id], "si");
    if($email_check && mysqli_num_rows($email_check) > 0){
        unset($_SESSION['owner_email_change_otp'], $_SESSION['owner_email_change_new_email'], $_SESSION['owner_email_change_current_email'], $_SESSION['owner_email_change_expire']);
        echo 'email_already';
        exit;
    }

    // CẬP NHẬT EMAIL TRONG DATABASE NGAY SAU KHI XÁC THỰC OTP THÀNH CÔNG
    $query = "UPDATE `hotel_owners` SET `email`=? WHERE `id`=? LIMIT 1";
    $values = [$new_email, $owner_id];
    
    $update_result = update($query, $values, 'si');
    
    if($update_result){
        // Cập nhật session nếu có
        if(isset($_SESSION['ownerEmail'])) {
            $_SESSION['ownerEmail'] = $new_email;
        }
        
        // Xóa OTP session
        unset($_SESSION['owner_email_change_otp'], $_SESSION['owner_email_change_new_email'], $_SESSION['owner_email_change_current_email'], $_SESSION['owner_email_change_expire']);
        
        error_log("OWNER EMAIL CHANGE - Successfully updated email to: $new_email for owner ID: $owner_id");
        echo 'email_changed';
    } else {
        error_log("OWNER EMAIL CHANGE - Failed to update email in database for owner ID: $owner_id");
        echo 'update_failed';
    }
    exit;
}

?>

