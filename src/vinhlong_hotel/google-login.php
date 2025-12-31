<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

/* ============================================================
   GOOGLE OAUTH CONFIG
   ============================================================ */

$env_path = __DIR__ . DIRECTORY_SEPARATOR . '.env';
if (is_file($env_path)) {
    foreach (file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }
        $key = trim($parts[0]);
        $value = trim($parts[1]);
        if ($key !== '' && getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}

$client_id     = getenv('GOOGLE_CLIENT_ID') ?: '';
$client_secret = getenv('GOOGLE_CLIENT_SECRET') ?: '';
$redirect_uri  = getenv('GOOGLE_REDIRECT_URI') ?: 'http://localhost/vinhlong_hotel/google-login.php';

if ($client_id === '' || $client_secret === '') {
    $_SESSION['google_login_error'] = 'Google OAuth credentials are not configured.';
    header('Location: index.php');
    exit;
}

/* ============================================================
   HÀM TẢI ẢNH GOOGLE VỀ SERVER
   ============================================================ */

function download_google_avatar($url)
{
    $save_dir = "images/users/";

    if (!is_dir($save_dir)) {
        mkdir($save_dir, 0777, true);
    }

    $img_data = @file_get_contents($url);

    if (!$img_data) {
        return "";   // Trả về rỗng, sẽ tạo avatar từ chữ cái đầu
    }

    $filename = "IMG_" . time() . rand(1000,9999) . ".jpg";
    $fullpath = $save_dir . $filename;

    file_put_contents($fullpath, $img_data);

    return $filename;
}

/* ============================================================
   1. CHUYỂN HƯỚNG ĐẾN GOOGLE LOGIN
   ============================================================ */

if (!isset($_GET['code'])) {
    // Kiểm tra lỗi từ Google
    if (isset($_GET['error'])) {
        $error = $_GET['error'];
        $error_description = $_GET['error_description'] ?? 'Không rõ lỗi';
        
        // Lưu lỗi vào session để hiển thị
        $_SESSION['google_login_error'] = "Lỗi đăng nhập Google: $error - $error_description";
        header("Location: index.php");
        exit;
    }

    $auth_url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
        "client_id"     => $client_id,
        "redirect_uri"  => $redirect_uri,
        "response_type" => "code",
        "scope"         => "openid email profile",
        "access_type"   => "online",
        "prompt"        => "consent select_account"
    ]);

    header("Location: $auth_url");
    exit;
}


/* ============================================================
   2. LẤY ACCESS TOKEN
   ============================================================ */

$token_url = "https://oauth2.googleapis.com/token";

$data = [
    "code"          => $_GET['code'],
    "client_id"     => $client_id,
    "client_secret" => $client_secret,
    "redirect_uri"  => $redirect_uri,
    "grant_type"    => "authorization_code"
];

$options = [
    "http" => [
        "header"  => "Content-Type: application/x-www-form-urlencoded\r\n",
        "method"  => "POST",
        "content" => http_build_query($data)
    ]
];

$response = @file_get_contents($token_url, false, stream_context_create($options));
$token = json_decode($response, true);

if (!isset($token['access_token'])) {
    $error_msg = isset($token['error']) ? $token['error'] : 'Không rõ lỗi';
    $error_description = isset($token['error_description']) ? $token['error_description'] : '';
    
    // Lưu lỗi vào session
    $_SESSION['google_login_error'] = "Lỗi xác thực Google: $error_msg - $error_description";
    header("Location: index.php");
    exit;
}


/* ============================================================
   3. LẤY THÔNG TIN USER GOOGLE
   ============================================================ */

$user_info = @file_get_contents(
    "https://www.googleapis.com/oauth2/v2/userinfo?access_token=" . $token['access_token']
);

if (!$user_info) {
    $_SESSION['google_login_error'] = "Không thể lấy thông tin từ Google!";
    header("Location: index.php");
    exit;
}

$user = json_decode($user_info, true);

if (isset($user['error'])) {
    $_SESSION['google_login_error'] = "Lỗi Google API: " . ($user['error_description'] ?? $user['error']);
    header("Location: index.php");
    exit;
}

$email = $user["email"] ?? "";
$name  = $user["name"] ?? "";
$google_pic_url = $user["picture"] ?? "";

if (empty($email)) {
    $_SESSION['google_login_error'] = "Không thể lấy email từ tài khoản Google!";
    header("Location: index.php");
    exit;
}


/* ============================================================
   4. KIỂM TRA / TẠO USER TRONG DATABASE
   ============================================================ */

require "admin/inc/db_config.php";

$q = mysqli_query($con, "SELECT * FROM user_cred WHERE email='$email' LIMIT 1");

if (mysqli_num_rows($q) == 0) {

    // lần đầu login → tải avatar Google về server
    $local_pic = download_google_avatar($google_pic_url);

    $sql = "INSERT INTO user_cred (name,email,password,profile,is_verified)
            VALUES ('$name','$email','google','$local_pic',1)";
    mysqli_query($con, $sql);

    $uid = mysqli_insert_id($con);
    $profile = $local_pic;

} else {

    // đã tồn tại → cập nhật avatar từ Google
    $row = mysqli_fetch_assoc($q);
    $uid = $row['id'];

    $local_pic = download_google_avatar($google_pic_url);

    mysqli_query($con,
        "UPDATE user_cred SET profile='$local_pic' WHERE id='$uid' LIMIT 1"
    );

    $profile = $local_pic;
}


/* ============================================================
   5. TẠO SESSION LOGIN
   ============================================================ */

// Kiểm tra xem có đang đăng nhập từ tài khoản khác không
$previous_user_id = isset($_SESSION['uId']) ? $_SESSION['uId'] : null;
$previous_user_name = isset($_SESSION['uName']) ? $_SESSION['uName'] : null;
$is_different_account = ($previous_user_id !== null && $previous_user_id != $uid);

$_SESSION['login'] = true;
$_SESSION['uId']   = $uid;
$_SESSION['uName'] = $name;
$_SESSION['uPic']  = $profile;   // Là tên file IMG_xxx.jpg

// 🔔 Lưu thông báo cho lần reload tiếp theo
if($is_different_account) {
    $_SESSION['login_msg'] = "Đã chuyển sang tài khoản " . $name . " bằng Google! Xin chào " . $name;
} else {
    $_SESSION['login_msg'] = "Đăng nhập thành công bằng Google! Xin chào " . $name;
}


/* ============================================================
   6. QUAY LẠI TRANG CHỦ
   ============================================================ */

header("Location: index.php");
exit;

?>
