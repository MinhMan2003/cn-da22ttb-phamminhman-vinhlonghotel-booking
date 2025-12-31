<?php

// ======================
// ENVIRONMENT CONFIGURATION
// ======================
// Kiểm tra môi trường: development hoặc production
// Development: localhost, 127.0.0.1, hoặc domain có chứa 'localhost'
// Production: domain thật (ví dụ: vinhlonghotel.com)
if(!defined('IS_DEVELOPMENT')) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $is_dev = (
        strpos($host, 'localhost') !== false ||
        strpos($host, '127.0.0.1') !== false ||
        strpos($host, '::1') !== false ||
        (isset($_SERVER['SERVER_NAME']) && strpos($_SERVER['SERVER_NAME'], 'localhost') !== false)
    );
    define('IS_DEVELOPMENT', $is_dev);
}

// ======================
// URL để hiển thị ảnh
// ======================
if(!defined('SITE_URL')) {
    define('SITE_URL', 'http://localhost/vinhlong_hotel/');
}

if(!defined('ABOUT_IMG_PATH')) {
    define('ABOUT_IMG_PATH',      SITE_URL.'images/about/');
}
if(!defined('CAROUSEL_IMG_PATH')) {
    define('CAROUSEL_IMG_PATH',   SITE_URL.'images/carousel/');
}
if(!defined('FACILITIES_IMG_PATH')) {
    define('FACILITIES_IMG_PATH', SITE_URL.'images/facilities/');
}
if(!defined('ROOMS_IMG_PATH')) {
    define('ROOMS_IMG_PATH',      SITE_URL.'images/rooms/');
}
if(!defined('USERS_IMG_PATH')) {
    define('USERS_IMG_PATH',      SITE_URL.'images/users/');
}
if(!defined('TEAM_IMG_PATH')) {
    define('TEAM_IMG_PATH',       SITE_URL.'images/team/');
}
if(!defined('DESTINATIONS_IMG_PATH')) {
    define('DESTINATIONS_IMG_PATH', SITE_URL.'images/destinations/');
}
if(!defined('SPECIALTIES_IMG_PATH')) {
    define('SPECIALTIES_IMG_PATH', SITE_URL.'images/specialties/');
}

// ======================
// Đường dẫn vật lý
// ======================
if(!defined('UPLOAD_IMAGE_PATH')) {
    define('UPLOAD_IMAGE_PATH', $_SERVER['DOCUMENT_ROOT'].'/vinhlong_hotel/images/');
}

if(!defined('ABOUT_FOLDER')) {
    define('ABOUT_FOLDER',      'about/');
}
if(!defined('CAROUSEL_FOLDER')) {
    define('CAROUSEL_FOLDER',   'carousel/');
}
if(!defined('FACILITIES_FOLDER')) {
    define('FACILITIES_FOLDER', 'facilities/');
}
if(!defined('ROOMS_FOLDER')) {
    define('ROOMS_FOLDER',      'rooms/');
}
if(!defined('USERS_FOLDER')) {
    define('USERS_FOLDER',      'users/');
}
if(!defined('TEAM_FOLDER')) {
    define('TEAM_FOLDER',       'team/');
}
if(!defined('DESTINATIONS_FOLDER')) {
    define('DESTINATIONS_FOLDER', 'destinations/');
}
if(!defined('SPECIALTIES_FOLDER')) {
    define('SPECIALTIES_FOLDER', 'specialties/');
}

// ======================
// OTP Log Helper (chỉ ghi log khi development)
// ======================
if(!function_exists('logOTP')) {
    /**
     * Ghi log OTP vào file (chỉ khi ở môi trường development)
     * @param string $type Loại OTP: 'REGISTER', 'EMAIL_CHANGE', etc.
     * @param string $email Email người dùng
     * @param string $otp Mã OTP
     * @param string $name Tên người dùng
     * @param bool $sent Trạng thái gửi email
     * @return void
     */
    function logOTP($type, $email, $otp, $name = '', $sent = false) {
        // Chỉ ghi log khi ở môi trường development
        if(!defined('IS_DEVELOPMENT') || !IS_DEVELOPMENT) {
            return;
        }
        
        $log_file = __DIR__ . '/../../otp_log.txt';
        $type_label = $type === 'EMAIL_CHANGE' ? 'EMAIL CHANGE' : 'REGISTER';
        $log_message = date('Y-m-d H:i:s') . " - {$type_label} - Email: $email - OTP: $otp";
        
        if(!empty($name)) {
            $log_message .= " - User: $name";
        }
        
        $log_message .= " - Sent: " . ($sent ? 'YES' : 'NO') . "\n";
        
        @file_put_contents($log_file, $log_message, FILE_APPEND);
    }
}

// Favorites table helper
if(!function_exists('ensureFavoritesTable')) {
    function ensureFavoritesTable(){
        $con = $GLOBALS['con'] ?? null;
        if(!$con) return;
        mysqli_query($con, "CREATE TABLE IF NOT EXISTS `favorites` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` INT UNSIGNED NOT NULL,
            `room_id` INT UNSIGNED NOT NULL,
            `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `user_room_unique` (`user_id`,`room_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }
}


// ======================
// Check admin login
// ======================
if(!function_exists('adminLogin')) {
    function adminLogin() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true)) {
            echo "<script>window.location.href='index.php'</script>";
            exit;
        }
    }
}


// ======================
// Redirect helper
// ======================
if(!function_exists('redirect')) {
    function redirect($url) {
        echo "<script>window.location.href='$url'</script>";
        exit;
    }
}


// ======================
// Bootstrap alert
// ======================
if(!function_exists('alert')) {
    function alert($type, $msg) {
        $bs_class = ($type == 'success') ? 'alert-success' : 'alert-danger';
        $msg_escaped = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
        $alert_id = 'auto-alert-' . time() . '-' . rand(1000, 9999);
        echo <<<ALERTHTML
      <div class="alert $bs_class alert-dismissible fade show custom-alert" role="alert" id="$alert_id">
        <strong class="me-3">$msg_escaped</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <script>
        (function(){
          // Xóa alert cũ nếu có
          const existingAlerts = document.querySelectorAll('.custom-alert');
          existingAlerts.forEach(function(alertEl){
            if(alertEl.id && alertEl.id.startsWith('auto-alert-') && alertEl.id !== '$alert_id'){
              alertEl.remove();
            }
          });
          
          // Tự động xóa alert sau 3 giây
          setTimeout(function(){
            const alertEl = document.getElementById('$alert_id');
            if(alertEl){
              alertEl.classList.remove('show');
              setTimeout(function(){
                alertEl.remove();
              }, 300);
            }
          }, 3000);
        })();
      </script>
ALERTHTML;
    }
}


// ======================
// Upload image (ALL folders)
// ======================
if(!function_exists('uploadImage')) {
    function uploadImage($image, $folder)
    {
    // Không có file
    if (!isset($image['tmp_name']) || !is_uploaded_file($image['tmp_name'])) {
        return 'upd_failed';
    }

    // Tạo folder nếu chưa có
    // Normalize destination directory path
    $destDir = rtrim(UPLOAD_IMAGE_PATH, '/\\') . '/' . trim($folder, '/\\') . '/';
    if (!is_dir($destDir)) {
        if (!@mkdir($destDir, 0777, true)) {
            error_log("uploadImage: Failed to create directory: {$destDir}");
            return 'upd_failed';
        }
    }

    // Kiểm tra kích thước (10MB)
    if (($image['size'] / (1024 * 1024)) > 10) {
        return 'inv_size';
    }

    // Cho phép định dạng
    $allowed_ext = ['jpg','jpeg','png','webp','gif'];
    $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_ext)) {
        return 'inv_img';
    }

    // Tạo tên file mới
    $newName = "IMG_" . time() . "_" . rand(1111,9999) . "." . $ext;

    // Full path
    $filePath = $destDir . $newName;

    // Upload
    $tmpName = $image['tmp_name'] ?? '';
    if (!is_uploaded_file($tmpName)) {
        error_log("uploadImage: tmp file is not an uploaded file or missing. tmpName={$tmpName} | image_info=" . print_r(array_intersect_key($image, ['name'=>0,'type'=>0,'size'=>0,'error'=>0,'tmp_name'=>0]), true));
        return 'upd_failed';
    }

    if (move_uploaded_file($tmpName, $filePath)) {
        return $newName;
    }

    $lastErr = error_get_last();
    error_log("uploadImage: move_uploaded_file failed for tmp={$tmpName} to dest={$filePath}. lastErr=" . print_r($lastErr, true));
    return 'upd_failed';
    }
}


// ======================
// XÓA ẢNH CHUẨN 100%
// ======================
if(!function_exists('deleteImage')) {
    function deleteImage($image, $folder)
    {
    if (!$image) return false;

    // Chuẩn hoá folder
    if (substr($folder, -1) !== '/') {
        $folder .= '/';
    }

    $path = UPLOAD_IMAGE_PATH . $folder . $image;

    if (file_exists($path)) {
        return unlink($path);
    }

    return false;
    }
}


// ======================
// Upload avatar (users)
// ======================
if(!function_exists('uploadUserImage')) {
    function uploadUserImage($image) {

    if (!isset($image['tmp_name']) || !is_uploaded_file($image['tmp_name'])) {
        return 'upd_failed';
    }

    $destDir = UPLOAD_IMAGE_PATH . USERS_FOLDER;

    if (!is_dir($destDir)) mkdir($destDir, 0777, true);

    if (($image['size'] / (1024 * 1024)) > 2) return 'inv_size';

    $allowed_ext = ['jpg','jpeg','png','webp'];
    $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_ext)) return 'inv_img';

    $newName = "IMG_" . time() . "_" . rand(1000,9999) . "." . $ext;

    if (move_uploaded_file($image['tmp_name'], $destDir . $newName)) {
        return $newName;
    }

    return 'upd_failed';
    }
}

// ======================
// Tạo avatar từ chữ cái đầu của tên
// ======================
if(!function_exists('generateAvatar')) {
    function generateAvatar($name, $gender = 'male') {
        // Lấy chữ cái đầu tiên của tên (bỏ qua khoảng trắng)
        $name = trim($name);
        if (empty($name)) {
            $initial = 'U'; // User mặc định
        } else {
            // Lấy chữ cái đầu tiên, chuyển sang chữ hoa
            $initial = mb_strtoupper(mb_substr($name, 0, 1, 'UTF-8'), 'UTF-8');
        }
        
        // Màu sắc dựa trên giới tính
        $colors = [
            'male' => ['bg' => '#4A90E2', 'text' => '#FFFFFF'], // Xanh dương cho nam
            'female' => ['bg' => '#E94B7D', 'text' => '#FFFFFF'] // Hồng cho nữ
        ];
        
        $color = $colors[$gender] ?? $colors['male'];
        
        // Tạo tên file avatar
        $avatarName = 'avatar_' . md5($name . $gender . time()) . '.png';
        $avatarPath = UPLOAD_IMAGE_PATH . USERS_FOLDER . $avatarName;
        
        // Tạo thư mục nếu chưa có
        $destDir = UPLOAD_IMAGE_PATH . USERS_FOLDER;
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }
        
        // Tạo ảnh avatar bằng GD
        $size = 200; // Kích thước ảnh
        $image = imagecreatetruecolor($size, $size);
        
        // Chuyển đổi màu hex sang RGB
        $bgColor = hex2rgb($color['bg']);
        $textColor = hex2rgb($color['text']);
        
        $bg = imagecolorallocate($image, $bgColor['r'], $bgColor['g'], $bgColor['b']);
        $text = imagecolorallocate($image, $textColor['r'], $textColor['g'], $textColor['b']);
        
        // Tô nền
        imagefilledrectangle($image, 0, 0, $size, $size, $bg);
        
        // Vẽ chữ cái - sử dụng font lớn hơn và căn giữa tốt hơn
        $fontSize = 100;
        
        // Kiểm tra xem có font file không (thường ở C:\Windows\Fonts\ trên Windows)
        $fontFile = null;
        $possibleFonts = [
            'C:/Windows/Fonts/arial.ttf',
            'C:/Windows/Fonts/arialbd.ttf',
            'C:/Windows/Fonts/calibri.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf', // Linux
            '/System/Library/Fonts/Helvetica.ttc' // macOS
        ];
        
        foreach($possibleFonts as $font) {
            if(file_exists($font)) {
                $fontFile = $font;
                break;
            }
        }
        
        if($fontFile && function_exists('imagettfbbox')) {
            // Sử dụng TrueType font nếu có
            $bbox = imagettfbbox($fontSize, 0, $fontFile, $initial);
            $textWidth = abs($bbox[4] - $bbox[0]);
            $textHeight = abs($bbox[5] - $bbox[1]);
            $x = ($size - $textWidth) / 2;
            $y = ($size + $textHeight) / 2;
            
            imagettftext($image, $fontSize, 0, $x, $y, $text, $fontFile, $initial);
        } else {
            // Sử dụng built-in font lớn nhất (font 5)
            $font = 5;
            $textWidth = imagefontwidth($font) * strlen($initial); // strlen vì built-in font không hỗ trợ UTF-8 tốt
            $textHeight = imagefontheight($font);
            $x = ($size - $textWidth) / 2;
            $y = ($size - $textHeight) / 2;
            
            // Chỉ vẽ ký tự ASCII đầu tiên nếu là built-in font
            $asciiInitial = mb_convert_encoding($initial, 'ASCII', 'UTF-8');
            if(empty($asciiInitial)) {
                $asciiInitial = 'U'; // Fallback
            }
            imagestring($image, $font, $x, $y, $asciiInitial, $text);
        }
        
        // Lưu ảnh
        imagepng($image, $avatarPath);
        imagedestroy($image);
        
        return $avatarName;
    }
    
    // Helper function để chuyển hex sang RGB
    function hex2rgb($hex) {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return ['r' => $r, 'g' => $g, 'b' => $b];
    }
}

// ======================
// Gửi email OTP
// ======================
if(!function_exists('sendOTPEmail')) {
    function sendOTPEmail($email, $otp, $name = '') {
        // Load SMTP config nếu có
        $smtp_config_file = __DIR__ . '/smtp_config.php';
        if (file_exists($smtp_config_file)) {
            require_once $smtp_config_file;
        }
        
        $site_title = 'Vĩnh Long Hotel';
        
        // Lấy site title từ database nếu có
        if(isset($GLOBALS['con']) && $GLOBALS['con']) {
            $settings_query = mysqli_query($GLOBALS['con'], "SELECT site_title FROM settings LIMIT 1");
            if($settings_row = mysqli_fetch_assoc($settings_query)) {
                $site_title = $settings_row['site_title'];
            }
        }
        
        $subject = "Mã xác thực email - $site_title";
        
        $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #003d5c 0%, #005a7a 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .otp-box { background: white; border: 2px dashed #003d5c; padding: 20px; text-align: center; margin: 20px 0; border-radius: 8px; }
                .otp-code { font-size: 32px; font-weight: bold; color: #003d5c; letter-spacing: 5px; font-family: 'Courier New', monospace; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>$site_title</h1>
                    <p>Xác thực email của bạn</p>
                </div>
                <div class='content'>
                    <p>Xin chào " . htmlspecialchars($name ?: 'Bạn') . ",</p>
                    <p>Cảm ơn bạn đã đăng ký tài khoản tại <strong>$site_title</strong>.</p>
                    <p>Vui lòng sử dụng mã xác thực sau để hoàn tất đăng ký:</p>
                    <div class='otp-box'>
                        <div class='otp-code'>$otp</div>
                    </div>
                    <p><strong>Lưu ý:</strong></p>
                    <ul>
                        <li>Mã xác thực có hiệu lực trong <strong>10 phút</strong></li>
                        <li>Không chia sẻ mã này với bất kỳ ai</li>
                        <li>Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này</li>
                    </ul>
                    <p>Trân trọng,<br><strong>Đội ngũ $site_title</strong></p>
                </div>
                <div class='footer'>
                    <p>Email này được gửi tự động, vui lòng không trả lời.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Kiểm tra xem có dùng SMTP không
        if (defined('USE_SMTP') && USE_SMTP === true) {
            $result = sendOTPEmailViaSMTP($email, $subject, $message, $name, $site_title);
            if (!$result && defined('SMTP_DEBUG') && SMTP_DEBUG === true) {
                error_log("SMTP send failed for email: $email");
            }
            return $result;
        } else {
            // Dùng mail() mặc định
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: $site_title <noreply@" . $_SERVER['HTTP_HOST'] . ">" . "\r\n";
            $headers .= "Reply-To: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
            
            $result = @mail($email, $subject, $message, $headers);
            return $result;
        }
    }
}

// ======================
// Gửi email OTP qua SMTP (PHPMailer)
// ======================
if(!function_exists('sendOTPEmailViaSMTP')) {
    function sendOTPEmailViaSMTP($email, $subject, $message, $name = '', $site_title = 'Vĩnh Long Hotel') {
        try {
            // Load PHPMailer - Thử nhiều đường dẫn
            $autoload_paths = [
                __DIR__ . '/../vendor/autoload.php',  // admin/vendor
                __DIR__ . '/../../logo/vendor/autoload.php',  // logo/vendor (nếu cài ở đây)
            ];
            
            $autoload_path = null;
            foreach ($autoload_paths as $path) {
                if (file_exists($path)) {
                    $autoload_path = $path;
                    break;
                }
            }
            
            if (!$autoload_path) {
                // Thử load trực tiếp từ logo/vendor nếu autoload không tìm thấy
                $phpmailer_direct = __DIR__ . '/../../logo/vendor/phpmailer/phpmailer/src/PHPMailer.php';
                if (file_exists($phpmailer_direct)) {
                    require_once __DIR__ . '/../../logo/vendor/phpmailer/phpmailer/src/PHPMailer.php';
                    require_once __DIR__ . '/../../logo/vendor/phpmailer/phpmailer/src/Exception.php';
                    require_once __DIR__ . '/../../logo/vendor/phpmailer/phpmailer/src/SMTP.php';
                } else {
                    $error_msg = "PHPMailer not found. Tried autoload: " . implode(', ', $autoload_paths) . " and direct: $phpmailer_direct";
                    error_log($error_msg);
                    $GLOBALS['last_email_error'] = [
                        'error_info' => $error_msg,
                        'exception' => 'PHPMailer files not found'
                    ];
                    return false;
                }
            } else {
                require_once $autoload_path;
            }
            
            // Kiểm tra class có tồn tại không
            if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                $error_msg = "PHPMailer class not found after loading";
                error_log($error_msg);
                $GLOBALS['last_email_error'] = [
                    'error_info' => $error_msg,
                    'exception' => 'PHPMailer class not found'
                ];
                return false;
            }
            
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host       = defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = defined('SMTP_USER') ? SMTP_USER : '';
            // Loại bỏ dấu cách trong App Password (Google App Password không có dấu cách)
            $smtp_pass = defined('SMTP_PASS') ? str_replace(' ', '', trim(SMTP_PASS)) : '';
            $mail->Password   = $smtp_pass;
            $mail->SMTPSecure = defined('SMTP_SECURE') ? (SMTP_SECURE === 'ssl' ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS) : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = defined('SMTP_PORT') ? SMTP_PORT : 587;
            $mail->CharSet    = 'UTF-8';
            
            // Debug mode - Chỉ log khi cần thiết (tắt trong production)
            $mail->SMTPDebug = defined('SMTP_DEBUG') && SMTP_DEBUG === true ? 2 : 0;
            $debug_output = [];
            if ($mail->SMTPDebug > 0) {
                $mail->Debugoutput = function($str, $level) use (&$debug_output) {
                    // Chỉ log các thông điệp quan trọng, bỏ qua các dòng HTML/CSS trong body
                    if (strpos($str, 'CLIENT -> SERVER:') !== false && 
                        (strpos($str, '.otp-box') !== false || 
                         strpos($str, '.otp-code') !== false ||
                         strpos($str, '<div') !== false ||
                         strpos($str, '<p>') !== false ||
                         strpos($str, '<li>') !== false)) {
                        // Bỏ qua các dòng HTML trong body message
                        return;
                    }
                    $debug_output[] = $str;
                    error_log("PHPMailer: $str");
                };
            }
            
            // Tắt output buffer để tránh làm nhiễu response
            ob_start();
            
            // Email content
            $from_email = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : SMTP_USER;
            $from_name = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : $site_title;
            
            $mail->setFrom($from_email, $from_name);
            $mail->addAddress($email, $name);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->AltBody = strip_tags($message); // Plain text version
            
            // Xóa output buffer nếu có
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            
            // Gửi email và kiểm tra lỗi
            if (!$mail->send()) {
                // Nếu send() trả về false, lấy ErrorInfo
                $error_info = $mail->ErrorInfo ?? 'Unknown error - send() returned false';
                error_log("PHPMailer send() failed: $error_info");
                
                // Lưu lỗi vào global
                if (!isset($GLOBALS['last_email_error'])) {
                    $GLOBALS['last_email_error'] = [];
                }
                $GLOBALS['last_email_error'] = [
                    'error_info' => $error_info,
                    'exception' => 'send() returned false',
                    'debug_output' => $debug_output ?? []
                ];
                
                return false;
            }
            
            return true;
            
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            $error_info = isset($mail) ? ($mail->ErrorInfo ?? 'Unknown error') : 'Mail object not created';
            $error_msg = "PHPMailer Error: $error_info | Exception: " . $e->getMessage();
            error_log($error_msg);
            
            // Lưu lỗi vào global để có thể lấy sau
            if (!isset($GLOBALS['last_email_error'])) {
                $GLOBALS['last_email_error'] = [];
            }
            $GLOBALS['last_email_error'] = [
                'error_info' => $error_info,
                'exception' => $e->getMessage(),
                'debug_output' => $debug_output ?? []
            ];
            
            return false;
        } catch (\Exception $e) {
            $error_msg = "Email Exception: " . $e->getMessage() . " | Class: " . get_class($e);
            error_log($error_msg);
            
            if (!isset($GLOBALS['last_email_error'])) {
                $GLOBALS['last_email_error'] = [];
            }
            $GLOBALS['last_email_error'] = [
                'exception' => $e->getMessage(),
                'class' => get_class($e)
            ];
            
            return false;
        } catch (\Throwable $e) {
            $error_msg = "Fatal Email Error: " . $e->getMessage() . " | Class: " . get_class($e);
            error_log($error_msg);
            
            if (!isset($GLOBALS['last_email_error'])) {
                $GLOBALS['last_email_error'] = [];
            }
            $GLOBALS['last_email_error'] = [
                'fatal_error' => $e->getMessage(),
                'class' => get_class($e)
            ];
            
            return false;
        }
    }
}

?>
