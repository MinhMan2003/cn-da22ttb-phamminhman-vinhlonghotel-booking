<?php
require('../../admin/inc/db_config.php');
require('../../admin/inc/essentials.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Đảm bảo output buffer sạch
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json; charset=utf-8');

// ===================== GỬI MÃ OTP =====================
if(isset($_POST['send_otp'])) {
    try {
        $data = filteration($_POST);
        $email = strtolower(trim($data['email'] ?? ''));
        $name = trim($data['name'] ?? '');
        
        // Validate email
        if(empty($email)) {
            echo json_encode(['status' => 'error', 'msg' => 'Email không được để trống!']);
            exit;
        }
        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'msg' => 'Email không hợp lệ!']);
            exit;
        }
        
        // Kiểm tra email đã tồn tại chưa trong hotel_owners
        $check_email = select("SELECT id FROM hotel_owners WHERE email=? LIMIT 1", [$email], "s");
        if($check_email && mysqli_num_rows($check_email) > 0) {
            echo json_encode(['status' => 'error', 'msg' => 'Email này đã được sử dụng!']);
            exit;
        }
        
        // Validate và lưu thông tin form vào session
        $phone = trim($data['phone'] ?? '');
        $dob = trim($data['dob'] ?? '');
        $address = trim($data['address'] ?? '');
        $gender = trim($data['gender'] ?? '');
        $password = trim($data['password'] ?? '');
        $hotel_name = trim($data['hotel_name'] ?? '');
        
        // Validate phone (10 số, bắt đầu bằng 0)
        if(empty($phone) || !preg_match('/^0[0-9]{9}$/', $phone)) {
            echo json_encode(['status' => 'error', 'msg' => 'Số điện thoại không hợp lệ! (10 số, bắt đầu bằng 0)']);
            exit;
        }
        
        // Validate DOB (phải từ 18 tuổi)
        if(empty($dob)) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng chọn ngày sinh!']);
            exit;
        }
        $birthDate = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
        if($age < 18) {
            echo json_encode(['status' => 'error', 'msg' => 'Bạn phải từ 18 tuổi trở lên!']);
            exit;
        }
        
        // Validate các trường bắt buộc
        if(empty($address) || empty($gender) || empty($password) || empty($hotel_name)) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng điền đầy đủ thông tin!']);
            exit;
        }
        
        // Lưu thông tin form vào session
        $_SESSION['owner_register_data'] = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'dob' => $dob,
            'address' => $address,
            'gender' => $gender,
            'password' => $password,
            'hotel_name' => $hotel_name
        ];
        
        // Tạo mã OTP 6 số
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Lưu OTP vào session (hết hạn sau 10 phút)
        $_SESSION['owner_otp_code'] = $otp;
        $_SESSION['owner_otp_email'] = $email;
        $_SESSION['owner_otp_expire'] = time() + (10 * 60);
        
        // Gửi email OTP
        $sent = sendOTPEmail($email, $otp, $name);
        
        // Log OTP vào file (chỉ khi ở môi trường development)
        if(function_exists('logOTP')) {
            logOTP('OWNER_REGISTER', $email, $otp, $name, $sent);
        }
        
        if($sent) {
            $response = ['status' => 'success', 'msg' => 'Mã OTP đã được gửi đến email của bạn!'];
            // Ở môi trường dev, trả về OTP để dễ debug
            if(defined('IS_DEVELOPMENT') && IS_DEVELOPMENT === true) {
                $response['otp_debug'] = $otp;
            }
            echo json_encode($response);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Không thể gửi email. Vui lòng thử lại!']);
        }
    } catch(Exception $e) {
        error_log("SEND OTP ERROR: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'msg' => 'Có lỗi xảy ra. Vui lòng thử lại!']);
    }
    exit;
}

// ===================== XÁC THỰC MÃ OTP =====================
if(isset($_POST['verify_otp'])) {
    try {
        $data = filteration($_POST);
        $otp_input = trim($data['otp'] ?? '');
        $email = strtolower(trim($data['email'] ?? ''));
        
        // Validate
        if(empty($otp_input) || empty($email)) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng nhập đầy đủ thông tin!']);
            exit;
        }
        
        // Kiểm tra session OTP
        if(!isset($_SESSION['owner_otp_code']) || !isset($_SESSION['owner_otp_email'])) {
            echo json_encode(['status' => 'error', 'msg' => 'Mã OTP không tồn tại hoặc đã hết hạn!']);
            exit;
        }
        
        // Kiểm tra hết hạn
        if(time() > $_SESSION['owner_otp_expire']) {
            unset($_SESSION['owner_otp_code'], $_SESSION['owner_otp_email'], $_SESSION['owner_otp_expire']);
            echo json_encode(['status' => 'error', 'msg' => 'Mã OTP đã hết hạn! Vui lòng gửi lại mã mới.']);
            exit;
        }
        
        // Kiểm tra email khớp
        if($_SESSION['owner_otp_email'] !== $email) {
            echo json_encode(['status' => 'error', 'msg' => 'Email không khớp!']);
            exit;
        }
        
        // Kiểm tra mã OTP
        if($_SESSION['owner_otp_code'] !== $otp_input) {
            echo json_encode(['status' => 'error', 'msg' => 'Mã OTP không đúng!']);
            exit;
        }
        
        // ✅ OTP HỢP LỆ - Đánh dấu email đã được xác thực
        $_SESSION['owner_email_verified'] = $email;
        $_SESSION['owner_email_verified_time'] = time();
        
        // Xóa OTP session (giữ lại register_data)
        unset($_SESSION['owner_otp_code'], $_SESSION['owner_otp_email'], $_SESSION['owner_otp_expire']);
        
        echo json_encode(['status' => 'success', 'msg' => 'Email đã được xác thực thành công!']);
    } catch(Exception $e) {
        error_log("VERIFY OTP ERROR: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'msg' => 'Có lỗi xảy ra. Vui lòng thử lại!']);
    }
    exit;
}

// ===================== ĐĂNG KÝ SAU KHI XÁC THỰC =====================
if(isset($_POST['register'])) {
    try {
        $data = filteration($_POST);
        $email = strtolower(trim($data['email'] ?? ''));
        
        // Kiểm tra email đã được xác thực chưa
        $verified_email = isset($_SESSION['owner_email_verified']) ? strtolower(trim($_SESSION['owner_email_verified'])) : '';
        $verified_time = isset($_SESSION['owner_email_verified_time']) ? (int)$_SESSION['owner_email_verified_time'] : 0;
        
        // Email phải được xác thực và không quá 30 phút
        if(empty($verified_email) || $verified_email !== $email || (time() - $verified_time) > 1800) {
            echo json_encode(['status' => 'error', 'msg' => 'Email chưa được xác thực hoặc đã hết hạn! Vui lòng xác thực lại.']);
            exit;
        }
        
        // Lấy dữ liệu từ session hoặc POST
        $register_data = $_SESSION['owner_register_data'] ?? [];
        $name = trim($data['name'] ?? $register_data['name'] ?? '');
        $password = trim($data['password'] ?? $register_data['password'] ?? '');
        $phone = trim($data['phone'] ?? $register_data['phone'] ?? '');
        $dob = trim($data['dob'] ?? $register_data['dob'] ?? '');
        $address = trim($data['address'] ?? $register_data['address'] ?? '');
        $gender = trim($data['gender'] ?? $register_data['gender'] ?? '');
        $hotel_name = trim($data['hotel_name'] ?? $register_data['hotel_name'] ?? '');
        
        // Validation đầy đủ
        if(empty($name)) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng nhập họ và tên!']);
            exit;
        }
        
        if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'msg' => 'Email không hợp lệ!']);
            exit;
        }
        
        if(empty($password) || strlen($password) < 6) {
            echo json_encode(['status' => 'error', 'msg' => 'Mật khẩu phải có ít nhất 6 ký tự!']);
            exit;
        }
        
        // Validate phone
        if(empty($phone) || !preg_match('/^0[0-9]{9}$/', $phone)) {
            echo json_encode(['status' => 'error', 'msg' => 'Số điện thoại không hợp lệ!']);
            exit;
        }
        
        // Validate DOB
        if(empty($dob)) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng chọn ngày sinh!']);
            exit;
        }
        $birthDate = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
        if($age < 18) {
            echo json_encode(['status' => 'error', 'msg' => 'Bạn phải từ 18 tuổi trở lên!']);
            exit;
        }
        
        // Validate các trường bắt buộc
        if(empty($address) || empty($gender) || empty($hotel_name)) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng điền đầy đủ thông tin!']);
            exit;
        }
        
        // Kiểm tra email đã tồn tại chưa (double check)
        $check_email = select("SELECT id FROM hotel_owners WHERE email=? LIMIT 1", [$email], "s");
        if($check_email && mysqli_num_rows($check_email) > 0) {
            echo json_encode(['status' => 'error', 'msg' => 'Email này đã được sử dụng!']);
            exit;
        }
        
        // Hash mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert vào database
        // Tự duyệt: set status = 1 sau khi email đã xác thực bằng OTP
        $query = "INSERT INTO hotel_owners (name, email, password, phone, hotel_name, address, status) VALUES (?,?,?,?,?,?,1)";
        $values = [$name, $email, $hashed_password, $phone, $hotel_name, $address];
        
        if(insert($query, $values, "ssssss")) {
            // Xóa session data
            unset($_SESSION['owner_register_data'], $_SESSION['owner_email_verified'], $_SESSION['owner_email_verified_time']);
            
            echo json_encode(['status' => 'success', 'msg' => 'Đăng ký thành công! Tài khoản đã được kích hoạt.']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Đăng ký thất bại! Vui lòng thử lại.']);
        }
    } catch(Exception $e) {
        error_log("REGISTER ERROR: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'msg' => 'Có lỗi xảy ra. Vui lòng thử lại!']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'msg' => 'Invalid request']);
?>

