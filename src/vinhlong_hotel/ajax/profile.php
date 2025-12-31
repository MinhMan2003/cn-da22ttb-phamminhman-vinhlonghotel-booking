<?php 

  require('../admin/inc/db_config.php');
  require('../admin/inc/essentials.php');

  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }

  if(isset($_POST['info_form']))
  {
    $frm_data = filteration($_POST);

    $u_exist = select("SELECT * FROM `user_cred` WHERE `phonenum`=? AND `id`!=? LIMIT 1",
      [$frm_data['phonenum'],$_SESSION['uId']],"ss");

    if(mysqli_num_rows($u_exist)!=0){
      echo 'phone_already';
      exit;
    }

    // Kiểm tra xem cột gender có tồn tại không
    global $con;
    $check_gender = mysqli_query($con, "SHOW COLUMNS FROM `user_cred` LIKE 'gender'");
    $has_gender = mysqli_num_rows($check_gender) > 0;
    
    if($has_gender) {
      $query = "UPDATE `user_cred` SET `name`=?, `address`=?, `phonenum`=?,
        `pincode`=?, `dob`=?, `gender`=? WHERE `id`=? LIMIT 1";
      
      $values = [$frm_data['name'],$frm_data['address'],$frm_data['phonenum'],
        $frm_data['pincode'],$frm_data['dob'],$frm_data['gender'] ?? 'male',$_SESSION['uId']];
      
      $datatypes = 'sssssss';
    } else {
      $query = "UPDATE `user_cred` SET `name`=?, `address`=?, `phonenum`=?,
        `pincode`=?, `dob`=? WHERE `id`=? LIMIT 1";
      
      $values = [$frm_data['name'],$frm_data['address'],$frm_data['phonenum'],
        $frm_data['pincode'],$frm_data['dob'],$_SESSION['uId']];
      
      $datatypes = 'ssssss';
    }

    if(update($query,$values,$datatypes)){
      $_SESSION['uName'] = $frm_data['name'];
      echo 1;
    }
    else{
      echo 0;
    }

  }


  if(isset($_POST['profile_form']))
  {
    $img = uploadUserImage($_FILES['profile']);
    
    if($img == 'inv_img'){
      echo 'inv_img';
      exit;
    }
    else if($img == 'upd_failed'){
      echo 'upd_failed';
      exit;
    }


    //fetching old image and deleting it

    $u_exist = select("SELECT `profile` FROM `user_cred` WHERE `id`=? LIMIT 1",[$_SESSION['uId']],"s");
    $u_fetch = mysqli_fetch_assoc($u_exist);

    deleteImage($u_fetch['profile'],USERS_FOLDER);


    $query = "UPDATE `user_cred` SET `profile`=? WHERE `id`=? LIMIT 1";
    
    $values = [$img,$_SESSION['uId']];

    if(update($query,$values,'ss')){
      $_SESSION['uPic'] = $img;
      echo 1;
    }
    else{
      echo 0;
    }

  }

  if(isset($_POST['pass_form']))
  {
    // Kiểm tra session
    if(!isset($_SESSION['uId']) || empty($_SESSION['uId'])){
      echo 'session_error';
      exit;
    }

    $frm_data = filteration($_POST);

    // Kiểm tra dữ liệu đầu vào
    if(empty($frm_data['old_pass']) || empty($frm_data['new_pass']) || empty($frm_data['confirm_pass'])){
      echo 'empty_password';
      exit;
    }

    // Kiểm tra mật khẩu cũ
    $user_query = select("SELECT `password` FROM `user_cred` WHERE `id`=? LIMIT 1", [$_SESSION['uId']], "i");
    if(!$user_query || mysqli_num_rows($user_query) == 0){
      echo 'user_not_found';
      exit;
    }
    
    $user_data = mysqli_fetch_assoc($user_query);
    $db_password = $user_data['password'];
    $old_password = $frm_data['old_pass'];
    
    // Kiểm tra mật khẩu cũ có đúng không
    // Hệ thống có thể lưu mật khẩu dưới dạng hash hoặc plain text
    $password_match = false;
    
    // Thử kiểm tra với password_verify (nếu là hash)
    if(function_exists('password_verify') && password_verify($old_password, $db_password)){
      $password_match = true;
    }
    // Nếu không phải hash, so sánh trực tiếp (plain text)
    else if($db_password === $old_password){
      $password_match = true;
    }
    
    if(!$password_match){
      echo 'old_password_incorrect';
      exit;
    }

    // Kiểm tra mật khẩu mới không được trùng với mật khẩu cũ
    if($old_password === $frm_data['new_pass']){
      echo 'same_password';
      exit;
    }

    if($frm_data['new_pass']!=$frm_data['confirm_pass']){
      echo 'mismatch';
      exit;
    }

    // Kiểm tra độ dài mật khẩu
    if(strlen($frm_data['new_pass']) < 6){
      echo 'password_too_short';
      exit;
    }

    // Hash mật khẩu mới
    $enc_pass = password_hash($frm_data['new_pass'],PASSWORD_BCRYPT);

    $query = "UPDATE `user_cred` SET `password`=? WHERE `id`=? LIMIT 1";
    $values = [$enc_pass,$_SESSION['uId']];

    if(update($query,$values,'ss')){
      echo 1;
    }
    else{
      echo 0;
    }

  }

  /* =====================================================
     ===============   GỬI OTP ĐỔI EMAIL   ==============
     ===================================================== */
  if(isset($_POST['send_email_change_otp']))
  {
    // Set header
    header('Content-Type: text/plain; charset=utf-8');
    
    // Kiểm tra session
    if(!isset($_SESSION['uId']) || empty($_SESSION['uId'])){
      echo 'session_error';
      exit;
    }

    $frm_data = filteration($_POST);
    $new_email = strtolower(trim($frm_data['new_email'] ?? ''));

    // Validate email
    if(empty($new_email)){
      echo 'invalid_email';
      exit;
    }

    if(!filter_var($new_email, FILTER_VALIDATE_EMAIL)){
      echo 'invalid_email';
      exit;
    }

    // Lấy email hiện tại của user
    $u_exist = select("SELECT `email`, `name` FROM `user_cred` WHERE `id`=? LIMIT 1", [$_SESSION['uId']], "i");
    if(!$u_exist || mysqli_num_rows($u_exist) == 0){
      echo 'user_not_found';
      exit;
    }
    $u_fetch = mysqli_fetch_assoc($u_exist);
    $current_email = strtolower(trim($u_fetch['email']));
    $user_name = $u_fetch['name'];

    // Kiểm tra email mới có khác email cũ không
    if($new_email === $current_email){
      echo 'same_email';
      exit;
    }

    // Kiểm tra email mới đã được sử dụng chưa
    $email_check = select("SELECT `id` FROM `user_cred` WHERE `email`=? AND `id`!=? LIMIT 1", [$new_email, $_SESSION['uId']], "si");
    if($email_check && mysqli_num_rows($email_check) > 0){
      echo 'email_already';
      exit;
    }

    // Tạo mã OTP 6 số
    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Lưu OTP vào session (hết hạn sau 10 phút)
    $_SESSION['email_change_otp'] = $otp;
    $_SESSION['email_change_new_email'] = $new_email;
    $_SESSION['email_change_expire'] = time() + (10 * 60);

    // Gửi email OTP
    error_log("EMAIL CHANGE OTP - Attempting to send OTP to: $new_email, OTP: $otp, User: $user_name");
    
    // Kiểm tra xem hàm sendOTPEmail có tồn tại không
    if(!function_exists('sendOTPEmail')){
      error_log("EMAIL CHANGE OTP - ERROR: sendOTPEmail function not found!");
      echo 'otp_send_failed';
      exit;
    }
    
    $sent = sendOTPEmail($new_email, $otp, $user_name);
    error_log("EMAIL CHANGE OTP - Email send result: " . ($sent ? 'SUCCESS' : 'FAILED'));

    if($sent){
      // Log OTP vào file (chỉ khi ở môi trường development)
      if(function_exists('logOTP')) {
        logOTP('EMAIL_CHANGE', $new_email, $otp, $user_name, true);
      }
      
      echo 'otp_sent';
    } else {
      error_log("EMAIL CHANGE OTP - FAILED: Could not send email to $new_email");
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
    if(!isset($_SESSION['uId']) || empty($_SESSION['uId'])){
      echo 'session_error';
      exit;
    }

    $frm_data = filteration($_POST);
    $otp_input = trim($frm_data['otp'] ?? '');

    // Kiểm tra OTP có trong session không
    if(!isset($_SESSION['email_change_otp']) || !isset($_SESSION['email_change_new_email'])){
      echo 'otp_not_found';
      exit;
    }

    // Kiểm tra OTP hết hạn chưa
    if(!isset($_SESSION['email_change_expire']) || time() > $_SESSION['email_change_expire']){
      unset($_SESSION['email_change_otp'], $_SESSION['email_change_new_email'], $_SESSION['email_change_expire']);
      echo 'otp_expired';
      exit;
    }

    // Kiểm tra OTP có đúng không
    if($otp_input !== $_SESSION['email_change_otp']){
      echo 'otp_invalid';
      exit;
    }

    // Lấy email mới từ session
    $new_email = $_SESSION['email_change_new_email'];

    // Kiểm tra lại email mới đã được sử dụng chưa (để tránh race condition)
    $email_check = select("SELECT `id` FROM `user_cred` WHERE `email`=? AND `id`!=? LIMIT 1", [$new_email, $_SESSION['uId']], "si");
    if($email_check && mysqli_num_rows($email_check) > 0){
      unset($_SESSION['email_change_otp'], $_SESSION['email_change_new_email'], $_SESSION['email_change_expire']);
      echo 'email_already';
      exit;
    }

    // Cập nhật email
    $query = "UPDATE `user_cred` SET `email`=? WHERE `id`=? LIMIT 1";
    $values = [$new_email, $_SESSION['uId']];

    if(update($query,$values,'si')){
      // Xóa OTP session
      unset($_SESSION['email_change_otp'], $_SESSION['email_change_new_email'], $_SESSION['email_change_expire']);
      echo 'email_changed';
    }
    else{
      echo 'update_failed';
    }
    exit;
  }

  /* =====================================================
     ===============   GỬI OTP QUÊN MẬT KHẨU   ============
     ===================================================== */
  if(isset($_POST['send_forgot_password_otp']))
  {
    // Set header
    header('Content-Type: text/plain; charset=utf-8');
    
    // Kiểm tra session
    if(!isset($_SESSION['uId']) || empty($_SESSION['uId'])){
      echo 'session_error';
      exit;
    }

    // Lấy email của user hiện tại
    $u_exist = select("SELECT `email`, `name` FROM `user_cred` WHERE `id`=? LIMIT 1", [$_SESSION['uId']], "i");
    if(!$u_exist || mysqli_num_rows($u_exist) == 0){
      echo 'user_not_found';
      exit;
    }
    $u_fetch = mysqli_fetch_assoc($u_exist);
    $user_email = strtolower(trim($u_fetch['email']));
    $user_name = $u_fetch['name'];

    // Tạo mã OTP 6 số
    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Lưu OTP vào session (hết hạn sau 10 phút)
    $_SESSION['forgot_password_otp'] = $otp;
    $_SESSION['forgot_password_expire'] = time() + (10 * 60);

    // Gửi email OTP
    error_log("FORGOT PASSWORD OTP - Attempting to send OTP to: $user_email, OTP: $otp, User: $user_name");
    
    // Kiểm tra xem hàm sendOTPEmail có tồn tại không
    if(!function_exists('sendOTPEmail')){
      echo 'otp_send_failed';
      exit;
    }
    
    $sent = sendOTPEmail($user_email, $otp, $user_name);
    error_log("FORGOT PASSWORD OTP - Email send result: " . ($sent ? 'SUCCESS' : 'FAILED'));

    if($sent){
      // Log OTP vào file (chỉ khi ở môi trường development)
      if(function_exists('logOTP')) {
        logOTP('FORGOT_PASSWORD', $user_email, $otp, $user_name, true);
      }
      
      echo 'otp_sent';
    } else {
      error_log("FORGOT PASSWORD OTP - FAILED: Could not send email to $user_email");
      echo 'otp_send_failed';
    }
    exit;
  }

  /* =====================================================
     ===============   XÁC THỰC OTP VÀ ĐẶT LẠI MẬT KHẨU   ===
     ===================================================== */
  if(isset($_POST['verify_forgot_password_otp']))
  {
    // Set header
    header('Content-Type: text/plain; charset=utf-8');
    
    // Kiểm tra session
    if(!isset($_SESSION['uId']) || empty($_SESSION['uId'])){
      echo 'session_error';
      exit;
    }

    $frm_data = filteration($_POST);
    $otp_input = trim($frm_data['otp'] ?? '');
    $new_password = trim($frm_data['new_password'] ?? '');

    // Kiểm tra OTP có trong session không
    if(!isset($_SESSION['forgot_password_otp'])){
      echo 'otp_not_found';
      exit;
    }

    // Kiểm tra OTP hết hạn chưa
    if(!isset($_SESSION['forgot_password_expire']) || time() > $_SESSION['forgot_password_expire']){
      unset($_SESSION['forgot_password_otp'], $_SESSION['forgot_password_expire']);
      echo 'otp_expired';
      exit;
    }

    // Kiểm tra OTP có đúng không
    if($otp_input !== $_SESSION['forgot_password_otp']){
      echo 'otp_invalid';
      exit;
    }

    // Kiểm tra mật khẩu mới
    if(empty($new_password)){
      echo 'empty_password';
      exit;
    }

    if(strlen($new_password) < 6){
      echo 'password_too_short';
      exit;
    }

    // Hash mật khẩu mới
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Cập nhật mật khẩu
    $query = "UPDATE `user_cred` SET `password`=? WHERE `id`=? LIMIT 1";
    $values = [$hashed_password, $_SESSION['uId']];

    if(update($query,$values,'ss')){
      // Xóa OTP session
      unset($_SESSION['forgot_password_otp'], $_SESSION['forgot_password_expire']);
      error_log("FORGOT PASSWORD - Successfully reset password for user ID: " . $_SESSION['uId']);
      echo 'password_reset_success';
    }
    else{
      echo 'update_failed';
    }
    exit;
  }

?>