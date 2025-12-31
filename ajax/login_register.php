<?php 
require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

// ⚠️ BẬT ERROR REPORTING ĐỂ LOG ĐƯỢC LỖI
ini_set('display_errors', 0); // Không hiển thị lỗi ra browser
ini_set('log_errors', 1); // Bật log errors
ini_set('error_log', 'C:\xampp\apache\logs\error.log'); // Đường dẫn log file
error_reporting(E_ALL); // Báo tất cả lỗi

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =====================================================
   ===============   KIỂM TRA EMAIL ĐÃ XÁC THỰC   =====
   ===================================================== */
if(isset($_POST['check_email_verified'])) {
    $data = filteration($_POST);
    $email = strtolower(trim($data['email'] ?? ''));
    
    if(empty($email)) {
        echo 'invalid_email';
        exit;
    }
    
    // Kiểm tra email đã được xác thực trong session chưa
    $verified_email = isset($_SESSION['email_verified']) ? strtolower(trim($_SESSION['email_verified'])) : '';
    
    if(empty($verified_email) || $verified_email !== $email) {
        echo 'not_verified';
        exit;
    }
    
    // Kiểm tra xác thực còn hiệu lực không (30 phút)
    if(!isset($_SESSION['email_verified_time']) || (time() - $_SESSION['email_verified_time']) > (30 * 60)) {
        unset($_SESSION['email_verified'], $_SESSION['email_verified_time']);
        echo 'verification_expired';
        exit;
    }
    
    echo 'verified';
    exit;
}

/* =====================================================
   ===============   GỬI MÃ OTP   ====================
   ===================================================== */
if(isset($_POST['send_otp'])) {
    error_log("=== SEND OTP REQUEST START ===");
    
    // ⚠️ QUAN TRỌNG: Đảm bảo session đã được start TRƯỚC KHI làm bất cứ điều gì
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    error_log("SEND OTP - Session ID: " . session_id());
    error_log("SEND OTP - Session status: " . (session_status() === PHP_SESSION_ACTIVE ? 'ACTIVE' : 'NOT ACTIVE'));
    
    // Đảm bảo output buffer sạch
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Set header
    header('Content-Type: text/plain; charset=utf-8');
    
    try {
        $data = filteration($_POST);
        $email = strtolower(trim($data['email'] ?? ''));
        $name = trim($data['name'] ?? '');
        
        error_log("SEND OTP - Email: $email, Name: $name");
        error_log("SEND OTP - POST data: " . print_r($data, true));
        
        // Validate email
        if(empty($email)) {
            error_log("SEND OTP - BLOCKED: Email is empty");
            echo 'invalid_email';
            exit;
        }
        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error_log("SEND OTP - BLOCKED: Invalid email format: $email");
            echo 'invalid_email';
            exit;
        }
        
        // Kiểm tra email đã tồn tại chưa
        error_log("SEND OTP - Checking if email exists: $email");
        $u_exist = select(
            "SELECT * FROM `user_cred` WHERE `email`=? LIMIT 1",
            [$email], "s"
        );
        
        if($u_exist === false) {
            error_log("SEND OTP - ERROR: Database query failed");
            echo 'otp_send_failed';
            exit;
        }
        
        if(mysqli_num_rows($u_exist) > 0) {
            error_log("SEND OTP - BLOCKED: Email already exists: $email");
            echo 'email_already';
            exit;
        }
        
        // ⚠️ LƯU THÔNG TIN FORM VÀO SESSION ĐỂ DÙNG KHI VERIFY OTP
        $_SESSION['register_data'] = [
            'name' => trim($data['name'] ?? ''),
            'email' => $email,
            'phonenum' => trim($data['phonenum'] ?? ''),
            'address' => trim($data['address'] ?? ''),
            'pincode' => !empty($data['pincode']) ? (int)$data['pincode'] : 0,
            'dob' => trim($data['dob'] ?? ''),
            'password' => trim($data['pass'] ?? ''),
            'gender' => trim($data['gender'] ?? 'male') // Lưu giới tính
        ];
        
        // Tạo mã OTP 6 số
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        error_log("SEND OTP - Generated OTP: $otp");
        
        // Lưu OTP vào session (hết hạn sau 10 phút)
        $_SESSION['otp_code'] = $otp;
        $_SESSION['otp_email'] = $email;
        $_SESSION['otp_expire'] = time() + (10 * 60);
        
        error_log("SEND OTP - OTP saved to session. Expire at: " . date('Y-m-d H:i:s', $_SESSION['otp_expire']));
        error_log("SEND OTP - Session data saved: " . print_r(['otp_code' => $_SESSION['otp_code'], 'otp_email' => $_SESSION['otp_email'], 'register_data' => isset($_SESSION['register_data']) ? 'SET' : 'NOT SET'], true));
        
        // ⚠️ KHÔNG GỌI session_write_close() - Session sẽ tự động lưu khi script kết thúc
        // Việc gọi session_write_close() có thể làm mất session data
        
        // Gửi email OTP
        error_log("SEND OTP - Attempting to send email to: $email");
        $sent = sendOTPEmail($email, $otp, $name);
        error_log("SEND OTP - Email send result: " . ($sent ? 'SUCCESS' : 'FAILED'));
        
        // Log OTP vào file (chỉ khi ở môi trường development)
        if(function_exists('logOTP')) {
            logOTP('REGISTER', $email, $otp, $name, $sent);
        }
        
        if($sent) {
            error_log("SEND OTP - SUCCESS: OTP sent to $email");
            echo 'otp_sent';
        } else {
            error_log("SEND OTP - FAILED: Could not send email to $email");
            echo 'otp_send_failed';
        }
        
        error_log("=== SEND OTP REQUEST END ===");
        exit;
        
    } catch (Exception $e) {
        error_log("SEND OTP - EXCEPTION: " . $e->getMessage());
        error_log("SEND OTP - Stack trace: " . $e->getTraceAsString());
        echo 'otp_send_failed';
        exit;
    }
}

/* =====================================================
   ===============   XÁC THỰC OTP   ====================
   ===================================================== */
if(isset($_POST['verify_otp'])) {
    // ⚠️ QUAN TRỌNG: Đảm bảo output buffer sạch TRƯỚC KHI làm bất cứ điều gì
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Set header TRƯỚC KHI log hoặc làm bất cứ điều gì
    header('Content-Type: text/plain; charset=utf-8');
    
    error_log("=== VERIFY OTP REQUEST START ===");
    
    // ⚠️ QUAN TRỌNG: Đảm bảo session đã được start TRƯỚC KHI làm bất cứ điều gì
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    error_log("VERIFY OTP - Session ID: " . session_id());
    error_log("VERIFY OTP - Session status: " . (session_status() === PHP_SESSION_ACTIVE ? 'ACTIVE' : 'NOT ACTIVE'));
    error_log("VERIFY OTP - Session data keys: " . implode(', ', array_keys($_SESSION)));
    error_log("VERIFY OTP - OTP code in session: " . (isset($_SESSION['otp_code']) ? $_SESSION['otp_code'] : 'NOT SET'));
    error_log("VERIFY OTP - OTP email in session: " . (isset($_SESSION['otp_email']) ? $_SESSION['otp_email'] : 'NOT SET'));
    error_log("VERIFY OTP - OTP expire in session: " . (isset($_SESSION['otp_expire']) ? date('Y-m-d H:i:s', $_SESSION['otp_expire']) : 'NOT SET'));
    error_log("VERIFY OTP - Register data in session: " . (isset($_SESSION['register_data']) ? 'EXISTS' : 'NOT SET'));
    if(isset($_SESSION['register_data'])) {
        error_log("VERIFY OTP - Register data content: " . print_r($_SESSION['register_data'], true));
    }
    
    try {
        $data = filteration($_POST);
        $otp_input = trim($data['otp'] ?? '');
        $email = strtolower(trim($data['email'] ?? ''));
        
        error_log("VERIFY OTP - Email: $email, OTP: $otp_input");
        
        // ⚠️ XỬ LÝ UPLOAD ẢNH NẾU CÓ, NẾU KHÔNG THÌ TẠO AVATAR TỪ CHỮ CÁI ĐẦU
        $profile_image = 'user.png'; // Mặc định
        error_log("VERIFY OTP - Checking for profile image upload...");
        error_log("VERIFY OTP - \$_FILES['profile'] exists: " . (isset($_FILES['profile']) ? 'YES' : 'NO'));
        if(isset($_FILES['profile'])) {
            error_log("VERIFY OTP - \$_FILES['profile'] error: " . ($_FILES['profile']['error'] ?? 'NOT SET'));
            error_log("VERIFY OTP - \$_FILES['profile'] name: " . ($_FILES['profile']['name'] ?? 'NOT SET'));
            error_log("VERIFY OTP - \$_FILES['profile'] size: " . ($_FILES['profile']['size'] ?? 'NOT SET'));
        }
        
        if(isset($_FILES['profile']) && $_FILES['profile']['error'] == 0 && !empty($_FILES['profile']['name'])) {
            error_log("VERIFY OTP - ✅ Profile image uploaded: " . $_FILES['profile']['name'] . " (size: " . $_FILES['profile']['size'] . " bytes)");
            $uploaded_img = uploadUserImage($_FILES['profile']);
            error_log("VERIFY OTP - Upload result: $uploaded_img");
            if($uploaded_img != 'inv_img' && $uploaded_img != 'upd_failed' && $uploaded_img != 'inv_size') {
                $profile_image = $uploaded_img;
                error_log("VERIFY OTP - ✅ Profile image saved successfully: $profile_image");
            } else {
                error_log("VERIFY OTP - ❌ WARNING: Profile image upload failed: $uploaded_img, will generate avatar from name");
                // Upload thất bại, sẽ tạo avatar từ chữ cái đầu sau
            }
        } else {
            if(isset($_FILES['profile'])) {
                error_log("VERIFY OTP - ❌ Profile image upload error: " . ($_FILES['profile']['error'] ?? 'UNKNOWN'));
            } else {
                error_log("VERIFY OTP - ❌ No profile image in \$_FILES, will generate avatar from name");
            }
        }
        
        // Validate input
        if(empty($otp_input) || empty($email)) {
            error_log("VERIFY OTP - BLOCKED: Empty OTP or email");
            echo 'invalid_input';
            exit;
        }
        
        if(strlen($otp_input) !== 6 || !ctype_digit($otp_input)) {
            error_log("VERIFY OTP - BLOCKED: Invalid OTP format");
            echo 'invalid_otp_format';
            exit;
        }
        
        // Kiểm tra session OTP
        if(!isset($_SESSION['otp_code']) || !isset($_SESSION['otp_email']) || !isset($_SESSION['otp_expire'])) {
            error_log("VERIFY OTP - BLOCKED: OTP session not found");
            echo 'otp_not_found';
            exit;
        }
        
        // Kiểm tra hết hạn
        if(time() > $_SESSION['otp_expire']) {
            unset($_SESSION['otp_code'], $_SESSION['otp_email'], $_SESSION['otp_expire']);
            error_log("VERIFY OTP - BLOCKED: OTP expired");
            echo 'otp_expired';
            exit;
        }
        
        // Kiểm tra email khớp
        if($_SESSION['otp_email'] !== $email) {
            error_log("VERIFY OTP - BLOCKED: Email mismatch. Session: " . $_SESSION['otp_email'] . ", Input: $email");
            echo 'otp_email_mismatch';
            exit;
        }
        
        // Kiểm tra mã OTP
        if($_SESSION['otp_code'] !== $otp_input) {
            error_log("VERIFY OTP - BLOCKED: Invalid OTP. Expected: " . $_SESSION['otp_code'] . ", Got: $otp_input");
            echo 'otp_invalid';
            exit;
        }
        
        // ✅ OTP HỢP LỆ - CHỈ KHI NÀY MỚI ĐƯỢC PHÉP INSERT VÀO DATABASE
        error_log("VERIFY OTP - ✅ OTP verified successfully. Proceeding to register user...");
        
        // ⚠️ CHỈ KHI OTP ĐÃ ĐƯỢC XÁC MINH THÀNH CÔNG MỚI LẤY THÔNG TIN VÀ INSERT
        if(!isset($_SESSION['register_data']) || empty($_SESSION['register_data'])) {
            error_log("VERIFY OTP - ❌ ERROR: Register data not found in session");
            error_log("VERIFY OTP - Session ID: " . session_id());
            error_log("VERIFY OTP - All session keys: " . implode(', ', array_keys($_SESSION)));
            error_log("VERIFY OTP - Full session data: " . print_r($_SESSION, true));
            // ⚠️ KHÔNG XÓA OTP SESSION NẾU CHƯA INSERT THÀNH CÔNG
            echo 'register_data_missing';
            exit;
        }
        
        $register_data = $_SESSION['register_data'];
        
        // Validate các trường bắt buộc
        $name = trim($register_data['name'] ?? '');
        $phonenum = trim($register_data['phonenum'] ?? '');
        $address = trim($register_data['address'] ?? '');
        $pincode = !empty($register_data['pincode']) ? (int)$register_data['pincode'] : 0;
        $dob = trim($register_data['dob'] ?? '');
        $password = trim($register_data['password'] ?? '');
        $gender = trim($register_data['gender'] ?? 'male'); // Lấy giới tính từ session
        
        // ✅ TẠO AVATAR: Nếu có ảnh upload thì dùng, nếu không thì tạo từ chữ cái đầu
        $profile = $profile_image; // Dùng ảnh đã upload nếu có
        if($profile == 'user.png' || empty($profile)) {
            // Không có ảnh upload hoặc upload thất bại, tạo avatar từ chữ cái đầu
            if(!empty($name) && function_exists('generateAvatar')) {
                error_log("VERIFY OTP - No profile image, generating avatar for name: $name, gender: $gender");
                $profile = generateAvatar($name, $gender);
                error_log("VERIFY OTP - ✅ Avatar generated: $profile");
            } else {
                error_log("VERIFY OTP - ⚠️ Cannot generate avatar (name empty or function not exists), using default: user.png");
            }
        } else {
            error_log("VERIFY OTP - ✅ Using uploaded profile image: $profile");
        }
        
        // Validate các trường bắt buộc
        if(empty($name) || empty($phonenum) || empty($address) || empty($dob) || empty($password)) {
            error_log("VERIFY OTP - ERROR: Required fields are empty");
            // ⚠️ KHÔNG XÓA OTP SESSION NẾU VALIDATION FAIL
            echo 'missing_required_fields';
            exit;
        }
        
        // Validate số điện thoại
        $phone = preg_replace('/[^0-9]/', '', $phonenum);
        if(!preg_match('/^0[0-9]{9}$/', $phone)) {
            error_log("VERIFY OTP - ERROR: Invalid phone: $phone");
            // ⚠️ KHÔNG XÓA OTP SESSION NẾU VALIDATION FAIL
            echo 'invalid_phone';
            exit;
        }
        
        // Validate tuổi
        if(!empty($dob)) {
            $dob_obj = new DateTime($dob);
            $today = new DateTime();
            $age = $today->diff($dob_obj)->y;
            if($age < 18) {
                error_log("VERIFY OTP - ERROR: Age under 18: $age");
                // ⚠️ KHÔNG XÓA OTP SESSION NẾU VALIDATION FAIL
                echo 'age_under_18';
                exit;
            }
        }
        
        // ⚠️ KIỂM TRA USER ĐÃ TỒN TẠI CHƯA TRƯỚC KHI INSERT
        $u_exist = select(
            "SELECT * FROM `user_cred` WHERE `email`=? OR (`phonenum`=? AND `phonenum` IS NOT NULL AND `phonenum` != '') LIMIT 1",
            [$email, $phone], "ss"
        );
        
        if($u_exist && mysqli_num_rows($u_exist) > 0) {
            $exist_row = mysqli_fetch_assoc($u_exist);
            error_log("VERIFY OTP - ERROR: User already exists. ID=" . $exist_row['id'] . ", Email=" . $exist_row['email']);
            // ⚠️ KHÔNG XÓA OTP SESSION - ĐỂ USER CÓ THỂ THỬ LẠI VỚI EMAIL KHÁC
            // Chỉ xóa register_data vì không cần nữa
            unset($_SESSION['register_data']);
            echo 'user_already_exists';
            exit;
        }
        
        // ✅ CHỈ KHI OTP ĐÃ ĐƯỢC XÁC MINH THÀNH CÔNG MỚI INSERT VÀO DATABASE
        // Insert user vào database với is_verified = 1 (vì đã xác minh email qua OTP)
        // Kiểm tra xem cột gender có tồn tại không
        global $con;
        if(!isset($con) || !$con) {
            error_log("VERIFY OTP - ERROR: Database connection not available");
            echo 'registration_failed';
            exit;
        }
        
        // Kiểm tra cột gender có tồn tại không
        $check_gender = mysqli_query($con, "SHOW COLUMNS FROM `user_cred` LIKE 'gender'");
        $has_gender = mysqli_num_rows($check_gender) > 0;
        
        if($has_gender) {
            $query = "INSERT INTO `user_cred`
                (`name`,`email`,`phonenum`,`address`,`pincode`,`dob`,`password`,`profile`,`gender`,`is_verified`) 
                VALUES (?,?,?,?,?,?,?,?,?,1)";
            $values = [
                $name, 
                $email, 
                $phone,
                $address, 
                $pincode,
                $dob, 
                $password, 
                $profile,
                $gender
            ];
            $datatypes = 'ssssissss';
        } else {
            // Nếu chưa có cột gender, chỉ insert các trường cũ
            $query = "INSERT INTO `user_cred`
                (`name`,`email`,`phonenum`,`address`,`pincode`,`dob`,`password`,`profile`,`is_verified`) 
                VALUES (?,?,?,?,?,?,?,?,1)";
            $values = [
                $name, 
                $email, 
                $phone,
                $address, 
                $pincode,
                $dob, 
                $password, 
                $profile
            ];
            $datatypes = 'ssssisss';
        }
        
        error_log("VERIFY OTP - ✅ OTP verified, inserting user into database...");
        error_log("VERIFY OTP - User data: name=$name, email=$email, phone=$phone, gender=$gender, profile=$profile");
        error_log("VERIFY OTP - Profile image to insert: $profile (type: " . gettype($profile) . ", length: " . strlen($profile) . ")");
        error_log("VERIFY OTP - Has gender column: " . ($has_gender ? 'YES' : 'NO'));
        
        error_log("VERIFY OTP - About to insert with profile: $profile");
        error_log("VERIFY OTP - Values array before insert: " . print_r($values, true));
        error_log("VERIFY OTP - Query: $query");
        error_log("VERIFY OTP - Datatypes: $datatypes");
        
        $insert_result = insert($query, $values, $datatypes);
        $insert_id = isset($con) && $con ? mysqli_insert_id($con) : 0;
        
        error_log("VERIFY OTP - Insert result: " . var_export($insert_result, true) . ", Insert ID: " . ($insert_id ?: 'NULL'));
        
        // Kiểm tra profile có được lưu không
        if($insert_id > 0) {
            $check_profile = select("SELECT profile FROM `user_cred` WHERE `id`=? LIMIT 1", [$insert_id], 'i');
            if($check_profile && mysqli_num_rows($check_profile) > 0) {
                $check_row = mysqli_fetch_assoc($check_profile);
                error_log("VERIFY OTP - Profile saved in DB: " . ($check_row['profile'] ?? 'NULL'));
            }
        }
        
        // Kiểm tra insert thành công
        if($insert_result === false) {
            error_log("VERIFY OTP - ❌ ERROR: Insert function returned false");
            $mysql_error = mysqli_error($con);
            $mysql_errno = mysqli_errno($con);
            error_log("VERIFY OTP - MySQL Error #$mysql_errno: $mysql_error");
            echo 'registration_failed';
            exit;
        }
        
        // Kiểm tra insert_id hoặc affected rows
        if($insert_id > 0) {
            error_log("VERIFY OTP - ✅ Insert successful (insert_id=$insert_id)");
        } elseif($insert_result > 0) {
            error_log("VERIFY OTP - ✅ Insert successful (affected_rows=$insert_result)");
        } else {
            error_log("VERIFY OTP - ❌ ERROR: Insert failed - no insert_id and no affected rows");
            echo 'registration_failed';
            exit;
        }
        
        // Xác nhận user đã được lưu
        $verify_query = "SELECT id, email, name, profile, phonenum, is_verified, status FROM `user_cred` WHERE `email`=? LIMIT 1";
        $verify_res = select($verify_query, [$email], "s");
        
        if($verify_res && mysqli_num_rows($verify_res) == 1) {
            $verify_row = mysqli_fetch_assoc($verify_res);
            $saved_profile = $verify_row['profile'] ?? 'user.png';
            error_log("VERIFY OTP - ✅ User registered successfully: ID=" . $verify_row['id'] . ", Email=" . $verify_row['email'] . ", Profile=" . $saved_profile);
            
            // ✅ TỰ ĐỘNG ĐĂNG NHẬP SAU KHI ĐĂNG KÝ THÀNH CÔNG
            $_SESSION['login'] = true;
            $_SESSION['uId'] = $verify_row['id'];
            $_SESSION['uName'] = $verify_row['name'];
            $_SESSION['uPic'] = $saved_profile; // Đảm bảo dùng ảnh từ database
            $_SESSION['uPhone'] = $verify_row['phonenum'] ?? '';
            
            error_log("VERIFY OTP - ✅ Session set: uPic=" . $_SESSION['uPic'] . " (from DB: " . $saved_profile . ")");
            
            // ⚠️ KIỂM TRA ẢNH CÓ TỒN TẠI TRÊN SERVER KHÔNG
            if($saved_profile !== 'user.png' && !empty($saved_profile)) {
                $image_path = UPLOAD_IMAGE_PATH . USERS_FOLDER . $saved_profile;
                if(file_exists($image_path)) {
                    error_log("VERIFY OTP - ✅ Profile image file exists: $image_path");
                } else {
                    error_log("VERIFY OTP - ❌ WARNING: Profile image file NOT found: $image_path");
                }
            }
            
            error_log("VERIFY OTP - ✅ Auto-login: User ID=" . $verify_row['id'] . ", Name=" . $verify_row['name']);
            
            // ✅ CHỈ XÓA SESSION DATA KHI INSERT THÀNH CÔNG
            unset($_SESSION['register_data']);
            unset($_SESSION['otp_code'], $_SESSION['otp_email'], $_SESSION['otp_expire']); // Xóa OTP đã dùng
            unset($_SESSION['email_verified'], $_SESSION['email_verified_time']); // Xóa session xác thực email
            
            error_log("=== VERIFY OTP REQUEST END (SUCCESS - USER REGISTERED & AUTO-LOGGED IN) ===");
            echo 'registration_success';
            exit;
        } else {
            error_log("VERIFY OTP - ERROR: User not found after insert");
            // ⚠️ KHÔNG XÓA OTP SESSION NẾU INSERT FAIL - ĐỂ USER CÓ THỂ THỬ LẠI
            echo 'registration_failed';
            exit;
        }
        
    } catch (Exception $e) {
        error_log("VERIFY OTP - EXCEPTION: " . $e->getMessage());
        error_log("VERIFY OTP - Stack trace: " . $e->getTraceAsString());
        
        // Đảm bảo output buffer sạch trước khi echo
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: text/plain; charset=utf-8');
        echo 'otp_verify_failed';
        exit;
    } catch (Error $e) {
        error_log("VERIFY OTP - FATAL ERROR: " . $e->getMessage());
        error_log("VERIFY OTP - Stack trace: " . $e->getTraceAsString());
        
        // Đảm bảo output buffer sạch trước khi echo
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: text/plain; charset=utf-8');
        echo 'otp_verify_failed';
        exit;
    }
}

/* =====================================================
   ===============   XỬ LÝ ĐĂNG KÝ   ====================
   ===================================================== */
if(isset($_POST['register'])) {
    error_log("=== REGISTER REQUEST START ===");
    
    // Đảm bảo output buffer sạch ngay từ đầu
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    $data = filteration($_POST);
    
    // Log POST data
    error_log("REGISTER - POST data: " . print_r($data, true));
    
    // Chuẩn hóa email
    $data['email'] = strtolower(trim($data['email'] ?? ''));
    
    // ⚠️ KIỂM TRA EMAIL ĐÃ XÁC THỰC - PHẢI ĐẶT Ở ĐẦU TIÊN, TRƯỚC TẤT CẢ VALIDATION KHÁC
    $email_normalized = strtolower(trim($data['email']));
    $verified_email = isset($_SESSION['email_verified']) ? strtolower(trim($_SESSION['email_verified'])) : '';
    $verified_time = isset($_SESSION['email_verified_time']) ? (int)$_SESSION['email_verified_time'] : 0;
    
    // Log để debug
    error_log("REGISTER - Email: $email_normalized, Session verified: " . ($verified_email ?: 'NOT SET'));
    error_log("REGISTER - Verified time: " . ($verified_time ? date('Y-m-d H:i:s', $verified_time) : 'NOT SET'));
    error_log("REGISTER - Current time: " . date('Y-m-d H:i:s', time()));
    error_log("REGISTER - Time difference: " . ($verified_time ? (time() - $verified_time) : 'N/A') . " seconds");
    error_log("REGISTER - Session ID: " . session_id());
    error_log("REGISTER - Full session data: " . print_r($_SESSION, true));
    
    // BẮT BUỘC: Email phải được xác thực
    if(empty($verified_email)) {
        error_log("REGISTER - BLOCKED: Email verification session not found. Email: $email_normalized");
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: text/plain; charset=utf-8');
        echo 'email_not_verified';
        exit;
    }
    
    // BẮT BUỘC: Email trong form phải khớp với email đã xác thực
    if($verified_email !== $email_normalized) {
        error_log("REGISTER - BLOCKED: Email mismatch. Form email: $email_normalized, Verified email: $verified_email");
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: text/plain; charset=utf-8');
        echo 'email_not_verified';
        exit;
    }
    
    // BẮT BUỘC: Xác thực không được quá 30 phút
    if($verified_time === 0 || (time() - $verified_time) > (30 * 60)) {
        unset($_SESSION['email_verified'], $_SESSION['email_verified_time']);
        $time_diff = $verified_time ? (time() - $verified_time) : 0;
        error_log("REGISTER - BLOCKED: Email verification expired. Time difference: $time_diff seconds (max: 1800 seconds)");
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: text/plain; charset=utf-8');
        echo 'email_verification_expired';
        exit;
    }
    
    // Log xác nhận email đã được xác thực
    error_log("REGISTER - Email verification OK: $email_normalized (verified at " . date('Y-m-d H:i:s', $verified_time) . ")");
    
    // Sau đó mới kiểm tra các validation khác
    // Kiểm tra nhập lại mật khẩu
    if($data['pass'] != $data['cpass']) {
        error_log("REGISTER - BLOCKED: Password mismatch");
        while (ob_get_level()) {
            ob_end_clean();
        }
        echo 'pass_mismatch';
        exit;
    }
    
    // Kiểm tra độ dài mật khẩu
    if(strlen($data['pass']) < 6) {
        error_log("REGISTER - BLOCKED: Password too short");
        while (ob_get_level()) {
            ob_end_clean();
        }
        echo 'pass_too_short';
        exit;
    }
    
    // Kiểm tra số điện thoại Việt Nam (bắt đầu bằng 0, có 10 số)
    $phone = preg_replace('/[^0-9]/', '', $data['phonenum']);
    if(!preg_match('/^0[0-9]{9}$/', $phone)) {
        error_log("REGISTER - BLOCKED: Invalid phone: $phone");
        while (ob_get_level()) {
            ob_end_clean();
        }
        echo 'invalid_phone';
        exit;
    }
    $data['phonenum'] = $phone;
    
    // Kiểm tra tuổi (phải ít nhất 18 tuổi)
    if(!empty($data['dob'])) {
        $dob = new DateTime($data['dob']);
        $today = new DateTime();
        $age = $today->diff($dob)->y;
        
        if($age < 18) {
            error_log("REGISTER - BLOCKED: Age under 18: $age");
            while (ob_get_level()) {
                ob_end_clean();
            }
            echo 'age_under_18';
            exit;
        }
    }
    
    // Kiểm tra tồn tại email hoặc số điện thoại
    error_log("REGISTER - Checking if user exists: email=$email_normalized, phone=" . $data['phonenum']);
    $u_exist = select(
        "SELECT * FROM `user_cred` WHERE `email`=? OR (`phonenum`=? AND `phonenum` IS NOT NULL AND `phonenum` != '') LIMIT 1",
        [$data['email'], $data['phonenum']], "ss"
    );

    if(mysqli_num_rows($u_exist) != 0) {
        $u_exist_fetch = mysqli_fetch_assoc($u_exist);
        error_log("REGISTER - BLOCKED: User already exists");
        while (ob_get_level()) {
            ob_end_clean();
        }
        if($u_exist_fetch['email'] == $data['email']){
            echo 'email_already';
        } else {
            echo 'phone_already';
        }
        exit;
    }

    // Upload ảnh nếu có, nếu không thì tạo avatar từ chữ cái đầu
    $profile = 'user.png';
    if(!empty($_FILES['profile']['name'])) {
        error_log("REGISTER - Uploading profile image");
        $img = uploadUserImage($_FILES['profile']);
        if($img == 'inv_img'){ 
            error_log("REGISTER - BLOCKED: Invalid image");
            while (ob_get_level()) {
                ob_end_clean();
            }
            echo 'inv_img'; 
            exit; 
        }
        if($img == 'upd_failed'){ 
            error_log("REGISTER - BLOCKED: Upload failed");
            while (ob_get_level()) {
                ob_end_clean();
            }
            echo 'upd_failed'; 
            exit; 
        }
        $profile = $img;
        error_log("REGISTER - Profile image uploaded: $profile");
    } else {
        // Không có ảnh upload, tạo avatar từ chữ cái đầu của tên
        $name = trim($data['name'] ?? '');
        $gender = trim($data['gender'] ?? 'male');
        if(!empty($name) && function_exists('generateAvatar')) {
            error_log("REGISTER - No profile image uploaded, generating avatar from name: $name, gender: $gender");
            $profile = generateAvatar($name, $gender);
            error_log("REGISTER - ✅ Avatar generated: $profile");
        }
    }

    // Insert user với is_verified = 1 (đã xác thực email)
    $query = "INSERT INTO `user_cred`
        (`name`,`email`,`phonenum`,`address`,`pincode`,`dob`,`password`,`profile`,`is_verified`) 
        VALUES (?,?,?,?,?,?,?,?,1)";

    // Chuẩn hóa và validate các giá trị
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $phonenum = trim($data['phonenum'] ?? '');
    $address = trim($data['address'] ?? '');
    $pincode = !empty($data['pincode']) ? (int)$data['pincode'] : 0;
    $dob = trim($data['dob'] ?? '');
    $password = trim($data['pass'] ?? '');
    $profile_final = trim($profile ?? 'user.png');
    
    // Validate các trường bắt buộc không được rỗng
    if(empty($name) || empty($email) || empty($phonenum) || empty($address) || empty($dob) || empty($password)) {
        error_log("REGISTER - BLOCKED: Required fields are empty");
        error_log("REGISTER - name: " . ($name ?: 'EMPTY') . ", email: " . ($email ?: 'EMPTY') . ", phone: " . ($phonenum ?: 'EMPTY') . ", address: " . ($address ?: 'EMPTY') . ", dob: " . ($dob ?: 'EMPTY') . ", pass: " . ($password ? 'SET' : 'EMPTY'));
        while (ob_get_level()) {
            ob_end_clean();
        }
        echo 'missing_required_fields';
        exit;
    }
    
    // Đảm bảo các giá trị không null
    $values = [
        $name, 
        $email, 
        $phonenum,
        $address, 
        $pincode,  // int
        $dob, 
        $password, 
        $profile_final
    ];

    // Đảm bảo output buffer sạch trước khi insert
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Log trước khi insert với chi tiết
    error_log("REGISTER - Attempting insert for: $email_normalized");
    error_log("REGISTER - Query: $query");
    error_log("REGISTER - Values: name='$name', email='$email', phone='$phonenum', address='$address', pincode=$pincode, dob='$dob', profile='$profile_final'");
    error_log("REGISTER - Values array: " . print_r($values, true));
    error_log("REGISTER - Datatypes: ssssisss (8 chars: name,email,phone,address,pincode(int),dob,pass,profile)");
    error_log("REGISTER - Values count: " . count($values) . ", Datatypes length: " . strlen('ssssisss'));
    
    // Kiểm tra kết nối database trước
    global $con;
    if(!isset($con) || !$con) {
        error_log("REGISTER - ERROR: Database connection not available!");
        while (ob_get_level()) {
            ob_end_clean();
        }
        echo 'registration_failed';
        exit;
    }
    
    error_log("REGISTER - Database connection OK");
    error_log("REGISTER - About to call insert() function");
    error_log("REGISTER - Query: " . str_replace(["\r", "\n"], " ", $query));
    error_log("REGISTER - Values count: " . count($values));
    error_log("REGISTER - Datatypes: 'ssssisss' (length: " . strlen('ssssisss') . ")");
    
    // Datatypes: s=string, i=integer (pincode là int)
    error_log("REGISTER - Calling insert() with query and " . count($values) . " values");
    $insert_result = insert($query, $values, 'ssssisss');
    
    error_log("REGISTER - Returned from insert() function. Result: " . var_export($insert_result, true));
    error_log("REGISTER - Insert result type: " . gettype($insert_result));
    error_log("REGISTER - Insert result is false: " . ($insert_result === false ? 'YES' : 'NO'));
    error_log("REGISTER - Insert result > 0: " . ($insert_result > 0 ? 'YES' : 'NO'));
    
    // Lấy insert_id để kiểm tra
    $insert_id = isset($con) && $con ? mysqli_insert_id($con) : 0;
    error_log("REGISTER - MySQL insert_id: " . ($insert_id ?: 'NULL'));
    
    // Kiểm tra kết quả insert
    if($insert_result === false) {
        // Insert function trả về false - có lỗi
        $mysql_error = isset($con) && $con ? mysqli_error($con) : 'No connection';
        $mysql_errno = isset($con) && $con ? mysqli_errno($con) : 0;
        error_log("REGISTER - Insert function returned FALSE");
        error_log("REGISTER - MySQL Error #$mysql_errno: $mysql_error");
        error_log("REGISTER - Query was: $query");
        error_log("REGISTER - Values were: " . print_r($values, true));
        
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: text/plain; charset=utf-8');
        echo 'registration_failed';
        error_log("=== REGISTER REQUEST END (FAILED - insert returned false) ===");
        exit;
    }
    
    // ⚠️ QUAN TRỌNG: Kiểm tra insert thành công bằng cả insert_id và affected rows
    $insert_success = false;
    
    // Nếu có insert_id > 0, có nghĩa là insert thành công (ngay cả khi affected = 0)
    if($insert_id > 0) {
        error_log("REGISTER - Insert SUCCESS (detected by insert_id): ID=$insert_id, Affected rows: $insert_result");
        $insert_success = true;
    } 
    // Hoặc nếu affected rows > 0
    elseif($insert_result > 0) {
        error_log("REGISTER - Insert SUCCESS (detected by affected rows): Affected rows: $insert_result");
        $insert_success = true;
    }
    
    // Nếu insert thành công, xác nhận lại trong database
    if($insert_success) {
        error_log("REGISTER - Insert SUCCESS for: $email_normalized");
        
        // Xác nhận user đã được lưu trong database
        $verify_query = "SELECT id, email, name, is_verified, status FROM `user_cred` WHERE `email`=? LIMIT 1";
        $verify_res = select($verify_query, [$email_normalized], "s");
        
        if($verify_res && mysqli_num_rows($verify_res) == 1) {
            $verify_row = mysqli_fetch_assoc($verify_res);
            error_log("REGISTER - ✅ User confirmed in DB: ID=" . $verify_row['id'] . ", Email=" . $verify_row['email'] . ", Name=" . $verify_row['name'] . ", Verified=" . $verify_row['is_verified'] . ", Status=" . $verify_row['status']);
            
            // Xóa session xác thực email sau khi đăng ký thành công
            unset($_SESSION['email_verified'], $_SESSION['email_verified_time']);
            
            // Đảm bảo output buffer sạch trước khi echo
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            error_log("REGISTER - Sending success response");
            header('Content-Type: text/plain; charset=utf-8');
            echo 'registration_success';
            error_log("=== REGISTER REQUEST END (SUCCESS) ===");
            exit;
        } else {
            // User không tìm thấy sau khi insert - có thể có vấn đề
            error_log("REGISTER - ⚠️ WARNING: User not found after insert! Email: " . $email_normalized);
            error_log("REGISTER - Insert ID was: " . ($insert_id ?: 'NULL'));
            error_log("REGISTER - Affected rows was: " . $insert_result);
            
            // Thử kiểm tra lại sau 1 giây (có thể do delay)
            sleep(1);
            $verify_res2 = select($verify_query, [$email_normalized], "s");
            if($verify_res2 && mysqli_num_rows($verify_res2) == 1) {
                $verify_row = mysqli_fetch_assoc($verify_res2);
                error_log("REGISTER - ✅ User found on retry: ID=" . $verify_row['id']);
                
                unset($_SESSION['email_verified'], $_SESSION['email_verified_time']);
                while (ob_get_level()) {
                    ob_end_clean();
                }
                header('Content-Type: text/plain; charset=utf-8');
                echo 'registration_success';
                error_log("=== REGISTER REQUEST END (SUCCESS - after retry) ===");
                exit;
            } else {
                // Vẫn không tìm thấy - có lỗi thực sự
                error_log("REGISTER - ❌ ERROR: User still not found after retry!");
                while (ob_get_level()) {
                    ob_end_clean();
                }
                header('Content-Type: text/plain; charset=utf-8');
                echo 'registration_failed';
                error_log("=== REGISTER REQUEST END (FAILED - user not in DB) ===");
                exit;
            }
        }
    } else {
        // Insert không thành công
        error_log("REGISTER - ❌ Insert FAILED: insert_id=" . ($insert_id ?: 'NULL') . ", affected_rows=" . $insert_result);
        
        // Kiểm tra xem user đã tồn tại chưa (có thể đã insert trước đó)
        $check_exist = select(
            "SELECT id, email, name, is_verified FROM `user_cred` WHERE `email`=? LIMIT 1",
            [$email_normalized], "s"
        );
        if(mysqli_num_rows($check_exist) > 0) {
            $exist_row = mysqli_fetch_assoc($check_exist);
            error_log("REGISTER - User already exists in DB: ID=" . $exist_row['id'] . ", Email=" . $exist_row['email'] . ", Verified=" . $exist_row['is_verified']);
            
            // Nếu user đã tồn tại nhưng chưa verified, có thể update is_verified
            if($exist_row['is_verified'] == 0) {
                error_log("REGISTER - User exists but not verified. Updating is_verified to 1...");
                $update_verify = update(
                    "UPDATE `user_cred` SET `is_verified` = 1 WHERE `email` = ? LIMIT 1",
                    [$email_normalized], "s"
                );
                if($update_verify > 0) {
                    error_log("REGISTER - Successfully updated is_verified to 1 for existing user");
                    // Xóa session xác thực email
                    unset($_SESSION['email_verified'], $_SESSION['email_verified_time']);
                    while (ob_get_level()) {
                        ob_end_clean();
                    }
                    header('Content-Type: text/plain; charset=utf-8');
                    echo 'registration_success';
                    error_log("=== REGISTER REQUEST END (SUCCESS - updated existing user) ===");
                    exit;
                }
            }
            
            // User đã tồn tại và đã verified
            while (ob_get_level()) {
                ob_end_clean();
            }
            header('Content-Type: text/plain; charset=utf-8');
            echo 'email_already';
            error_log("=== REGISTER REQUEST END (FAILED - user already exists) ===");
            exit;
        }
        
        // User không tồn tại nhưng insert trả về 0 - có vấn đề
        error_log("REGISTER - CRITICAL: User does not exist but insert returned 0!");
        
        // Log lỗi MySQL chi tiết
        $mysql_error = isset($con) && $con ? mysqli_error($con) : 'No connection';
        $mysql_errno = isset($con) && $con ? mysqli_errno($con) : 0;
        error_log("REGISTER - Insert FAILED for: $email_normalized");
        error_log("REGISTER - MySQL Error #$mysql_errno: $mysql_error");
        error_log("REGISTER - Query was: $query");
        error_log("REGISTER - Values were: " . print_r($values, true));
        error_log("REGISTER - Datatypes were: ssssisss");
        
        // Kiểm tra lỗi cụ thể
        if($mysql_errno == 1062) {
            error_log("REGISTER - Duplicate entry error (email or phone already exists)");
        } elseif($mysql_errno == 1366) {
            error_log("REGISTER - Incorrect data type error");
        } elseif($mysql_errno == 1048) {
            error_log("REGISTER - Column cannot be null error");
        }
        
        // Đảm bảo output buffer sạch trước khi echo
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: text/plain; charset=utf-8');
        error_log("REGISTER - Sending failed response");
        echo 'registration_failed';
        error_log("=== REGISTER REQUEST END (FAILED) ===");
        exit;
    }
}


/* =====================================================
   ===============   XỬ LÝ ĐĂNG NHẬP   ===================
   ===================================================== */
if(isset($_POST['login'])) {
    $data = filteration($_POST);
    
    // Chuẩn hóa email/phone
    $email_mob = trim($data['email_mob']);
    $email_mob_lower = strtolower($email_mob);

    // Lấy user theo email hoặc số điện thoại
    $query = "SELECT * FROM `user_cred` WHERE `email`=? OR `phonenum`=? LIMIT 1";
    $values = [$email_mob_lower, $email_mob];
    $res = select($query, $values, "ss");

    $num_rows = mysqli_num_rows($res);
    
    // Log để debug
    error_log("LOGIN DEBUG - Email: $email_mob, Normalized: $email_mob_lower, Found: $num_rows row(s)");

    if($num_rows == 1) {
        $row = mysqli_fetch_assoc($res);
        
        error_log("LOGIN DEBUG - User ID: " . $row['id'] . ", Status: " . $row['status'] . ", Verified: " . $row['is_verified']);

        // Kiểm tra tài khoản bị khóa
        if($row['status'] == 0){
            error_log("LOGIN DEBUG - Account inactive");
            echo 'inactive';
            exit;
        }

        // ⚠️ KIỂM TRA EMAIL ĐÃ XÁC THỰC - Bắt buộc phải verify email trước khi đăng nhập
        // Kiểm tra is_verified phải = 1 (đã xác thực), nếu = 0 hoặc NULL thì không cho đăng nhập
        $is_verified = isset($row['is_verified']) ? (int)$row['is_verified'] : 0;
        if($is_verified != 1){
            error_log("LOGIN DEBUG - Email not verified. is_verified value: " . var_export($row['is_verified'], true));
            echo 'not_verified';
            exit;
        }

        // Kiểm tra mật khẩu
        $db_password = $row['password'];
        $input_password = $data['pass'];
        
        // Kiểm tra xem mật khẩu trong DB có phải là hash không (bắt đầu bằng $2y$, $2a$, $2b$)
        $is_hashed = (strpos($db_password, '$2y$') === 0 || strpos($db_password, '$2a$') === 0 || strpos($db_password, '$2b$') === 0);
        
        if($is_hashed) {
            // Mật khẩu đã được hash, sử dụng password_verify()
            $password_match = password_verify($input_password, $db_password);
            error_log("LOGIN DEBUG - Password check (HASHED): Using password_verify() - " . ($password_match ? 'MATCH' : 'NO MATCH'));
        } else {
            // Mật khẩu plain text (tương thích với dữ liệu cũ)
            $password_match = ($db_password === $input_password);
            error_log("LOGIN DEBUG - Password check (PLAIN TEXT): DB='" . substr($db_password, 0, 3) . "...' (len:" . strlen($db_password) . ") vs Input='" . substr($input_password, 0, 3) . "...' (len:" . strlen($input_password) . ") = " . ($password_match ? 'MATCH' : 'NO MATCH'));
        }
        
        if(!$password_match) {
            echo 'invalid_password';
            exit;
        }

        // Đăng nhập thành công
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Kiểm tra xem có đang đăng nhập từ tài khoản khác không
        $previous_user_id = isset($_SESSION['uId']) ? $_SESSION['uId'] : null;
        $is_different_account = ($previous_user_id !== null && $previous_user_id != $row['id']);
        
        $_SESSION['login'] = true;
        $_SESSION['uId'] = $row['id'];
        $_SESSION['uName'] = $row['name'];
        $_SESSION['uPic'] = $row['profile'];
        $_SESSION['uPhone'] = $row['phonenum'];

        // Lưu thông báo cho lần reload tiếp theo
        if($is_different_account) {
            $_SESSION['login_msg'] = "Đã chuyển sang tài khoản " . $row['name'] . "! Xin chào " . $row['name'];
        } else {
            $_SESSION['login_msg'] = "Đăng nhập thành công! Xin chào " . $row['name'];
        }

        echo 'login_success';
        exit;

    } else {
        echo 'invalid_email_mob';
        exit;
    }
}

/* =====================================================
   ===============   KHÔI PHỤC TÀI KHOẢN   =============
   ===================================================== */
if(isset($_POST['recover_user'])) {
    $data = filteration($_POST);
    $t_date = date("Y-m-d");

    // Kiểm tra token + email còn hiệu lực hôm nay
    $q = select(
        "SELECT id FROM user_cred WHERE email=? AND token=? AND t_expire=? LIMIT 1",
        [$data['email'], $data['token'], $t_date],
        "sss"
    );

    if(mysqli_num_rows($q) != 1){
        echo 'failed';
        exit;
    }

    // Cập nhật mật khẩu và xoá token
    $update = update(
        "UPDATE user_cred SET `password`=?, `token`=NULL, `t_expire`=NULL WHERE email=? LIMIT 1",
        [$data['pass'], $data['email']],
        "ss"
    );

    echo ($update == 1) ? 'success' : 'failed';
    exit;
}

/* =====================================================
   ===============   GỬI OTP QUÊN MẬT KHẨU (TỪ LOGIN)   ===
   ===================================================== */
if(isset($_POST['send_forgot_password_otp_login']))
{
    header('Content-Type: text/plain; charset=utf-8');
    
    $frm_data = filteration($_POST);
    $email_mob = trim($frm_data['email_mob'] ?? '');
    $email_mob_lower = strtolower($email_mob);

    if(empty($email_mob)){
      echo 'email_required';
      exit;
    }

    // Tìm user theo email hoặc số điện thoại
    $user_query = select("SELECT `email`, `name`, `phonenum` FROM `user_cred` WHERE `email`=? OR `phonenum`=? LIMIT 1", [$email_mob_lower, $email_mob], "ss");
    if(!$user_query || mysqli_num_rows($user_query) == 0){
      echo 'email_not_found';
      exit;
    }
    
    $user_data = mysqli_fetch_assoc($user_query);
    $user_email = strtolower(trim($user_data['email']));
    $user_name = $user_data['name'];

    // Tạo mã OTP 6 số
    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Lưu OTP vào session (hết hạn sau 10 phút)
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['forgot_password_otp_login'] = $otp;
    $_SESSION['forgot_password_email_login'] = $user_email;
    $_SESSION['forgot_password_expire_login'] = time() + (10 * 60);

    // Gửi email OTP
    error_log("FORGOT PASSWORD LOGIN OTP - Attempting to send OTP to: $user_email, OTP: $otp, User: $user_name");
    
    if(!function_exists('sendOTPEmail')){
      echo 'otp_send_failed';
      exit;
    }
    
    $sent = sendOTPEmail($user_email, $otp, $user_name);
    error_log("FORGOT PASSWORD LOGIN OTP - Email send result: " . ($sent ? 'SUCCESS' : 'FAILED'));

    if($sent){
      if(function_exists('logOTP')) {
        logOTP('FORGOT_PASSWORD_LOGIN', $user_email, $otp, $user_name, true);
      }
      echo 'otp_sent';
    } else {
      error_log("FORGOT PASSWORD LOGIN OTP - FAILED: Could not send email to $user_email");
      echo 'otp_send_failed';
    }
    exit;
}

/* =====================================================
   ===============   XÁC THỰC OTP VÀ ĐẶT LẠI MẬT KHẨU (TỪ LOGIN) ===
   ===================================================== */
if(isset($_POST['verify_forgot_password_otp_login']))
{
    header('Content-Type: text/plain; charset=utf-8');
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $frm_data = filteration($_POST);
    $email_mob = trim($frm_data['email_mob'] ?? '');
    $email_mob_lower = strtolower($email_mob);
    $otp_input = trim($frm_data['otp'] ?? '');
    $new_password = trim($frm_data['new_password'] ?? '');

    // Kiểm tra OTP có trong session không
    if(!isset($_SESSION['forgot_password_otp_login'])){
      echo 'otp_not_found';
      exit;
    }

    // Kiểm tra OTP hết hạn chưa
    if(!isset($_SESSION['forgot_password_expire_login']) || time() > $_SESSION['forgot_password_expire_login']){
      unset($_SESSION['forgot_password_otp_login'], $_SESSION['forgot_password_email_login'], $_SESSION['forgot_password_expire_login']);
      echo 'otp_expired';
      exit;
    }

    // Kiểm tra OTP có đúng không
    if($otp_input !== $_SESSION['forgot_password_otp_login']){
      echo 'otp_invalid';
      exit;
    }

    // Kiểm tra email có khớp không
    $user_email = $_SESSION['forgot_password_email_login'];
    $user_query = select("SELECT `id` FROM `user_cred` WHERE (`email`=? OR `phonenum`=?) AND `email`=? LIMIT 1", [$email_mob_lower, $email_mob, $user_email], "sss");
    if(!$user_query || mysqli_num_rows($user_query) == 0){
      echo 'email_mismatch';
      exit;
    }
    
    $user_data = mysqli_fetch_assoc($user_query);
    $user_id = $user_data['id'];

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
    $values = [$hashed_password, $user_id];

    if(update($query,$values,'ss')){
      // Xóa OTP session
      unset($_SESSION['forgot_password_otp_login'], $_SESSION['forgot_password_email_login'], $_SESSION['forgot_password_expire_login']);
      error_log("FORGOT PASSWORD LOGIN - Successfully reset password for user ID: $user_id");
      echo 'password_reset_success';
    }
    else{
      echo 'update_failed';
    }
    exit;
}

?>
