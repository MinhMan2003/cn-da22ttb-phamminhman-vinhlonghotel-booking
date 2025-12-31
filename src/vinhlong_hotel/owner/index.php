<?php
require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Nếu đã đăng nhập, chuyển đến dashboard
if (isset($_SESSION['ownerLogin']) && $_SESSION['ownerLogin'] == true) {
    redirect('dashboard.php');
}

// Kiểm tra bảng hotel_owners có tồn tại không
$table_exists = false;
$check_table = mysqli_query($con, "SHOW TABLES LIKE 'hotel_owners'");
if ($check_table && mysqli_num_rows($check_table) > 0) {
    $table_exists = true;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng nhập Chủ khách sạn</title>
<?php require('../admin/inc/links.php'); ?>
<style>
body {
    margin: 0;
    height: 100vh;
    font-family: 'Poppins', sans-serif;
    overflow: hidden;
    background: radial-gradient(circle at 50% 50%, #0a0f1c, #04060b 70%);
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
}

#space-bg {
    position: absolute;
    inset: 0;
    z-index: 1;
    background: url('https://static.vecteezy.com/system/resources/previews/002/141/366/non_2x/abstract-particle-background-with-light-free-video.jpg');
    background-size: cover;
    opacity: 0.25;
    filter: blur(2px);
    animation: bgMove 40s linear infinite;
}

@keyframes bgMove {
    from { transform: scale(1.05) translateX(0); }
    to   { transform: scale(1.1) translateX(-25px); }
}

#photon {
    position: absolute;
    width: 100%;
    height: 100%;
    z-index: 2;
}

.photon-dot {
    position: absolute;
    width: 6px;
    height: 6px;
    background: radial-gradient(circle, #8affff, #5ac9ff, #00d0ff);
    border-radius: 50%;
    box-shadow: 0 0 15px #00d0ff;
    animation: floatPhoton 6s infinite ease-in-out;
    opacity: .75;
}

@keyframes floatPhoton {
    0%   { transform: translateY(0) scale(1); opacity: .5; }
    50%  { transform: translateY(-40px) scale(1.5); opacity: 1; }
    100% { transform: translateY(0) scale(1); opacity: .5; }
}

.login-box {
    width: 420px;
    padding: 40px;
    background: rgba(15, 23, 42, 0.85);
    border: 2px solid rgba(0, 210, 255, 0.3);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 210, 255, 0.2);
    backdrop-filter: blur(10px);
    z-index: 10;
    position: relative;
}

.login-box h2 {
    color: #00d0ff;
    text-align: center;
    margin-bottom: 30px;
    font-size: 28px;
    text-shadow: 0 0 20px rgba(0, 210, 255, 0.5);
}

.login-box input {
    width: 100%;
    padding: 14px;
    margin-bottom: 20px;
    border: 2px solid rgba(0, 210, 255, 0.3);
    border-radius: 10px;
    background: rgba(15, 23, 42, 0.6);
    color: #fff;
    font-size: 16px;
    transition: all 0.3s;
}

.login-box input:focus {
    outline: none;
    border-color: #00d0ff;
    box-shadow: 0 0 15px rgba(0, 210, 255, 0.3);
}

.login-box button {
    width: 100%;
    padding: 14px;
    border-radius: 12px;
    font-size: 18px;
    border: none;
    color: white;
    cursor: pointer;
    background: linear-gradient(100deg, #0066ff, #00eaff);
    box-shadow: 0 6px 0 #003c78, 0 15px 30px rgba(0,180,255,0.55);
    transition: 0.25s;
}

.login-box button:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 25px rgba(0,180,255,0.75);
}

.login-box button:active {
    transform: translateY(2px);
}

.login-links {
    text-align: center;
    margin-top: 20px;
    color: #8affff;
}

.login-links a {
    color: #00d0ff;
    text-decoration: none;
    margin: 0 10px;
}

.login-links a:hover {
    text-decoration: underline;
}

#space-bg, #photon {
    pointer-events: none !important;
    z-index: 0 !important;
}

.login-box, .login-box * {
    pointer-events: auto !important;
    z-index: 10 !important;
    position: relative;
}

.alert-info {
    background: rgba(0, 210, 255, 0.1);
    border: 1px solid rgba(0, 210, 255, 0.3);
    color: #8affff;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
}
</style>
</head>
<body>

<div id="space-bg"></div>
<div id="photon"></div>

<div class="login-box">
    <h2><i class="bi bi-building"></i> Chủ Khách Sạn</h2>
    
    <?php if (!$table_exists): ?>
        <div class="alert alert-info">
            <strong>Lưu ý:</strong> Vui lòng chạy script SQL <code>database/database_updates_hotel_owners.sql</code> để tạo bảng hotel_owners trước.
        </div>
    <?php endif; ?>

    <form method="POST" id="loginForm">
        <input type="email" name="email" placeholder="Email đăng nhập" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit" name="login">Đăng Nhập</button>
    </form>

    <div class="login-links">
        <a href="register.php">Đăng ký tài khoản</a> | 
        <a href="../admin/index.php">Đăng nhập Admin</a>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const space = document.getElementById("photon");
    for (let i = 0; i < 35; i++) {
        const dot = document.createElement("div");
        dot.className = "photon-dot";
        dot.style.left = Math.random()*100 + "%";
        dot.style.top = Math.random()*100 + "%";
        dot.style.animationDelay = (Math.random()*3)+"s";
        space.appendChild(dot);
    }
});
</script>

<?php
if (isset($_POST['login'])) {
    if (!$table_exists) {
        alert('error', 'Hệ thống chưa được cấu hình. Vui lòng liên hệ quản trị viên.');
    } else {
        $frm_data = filteration($_POST);
        $email = $frm_data['email'];
        $password = $frm_data['password'];

        $query = "SELECT * FROM hotel_owners WHERE email=? AND status=1";
        $res = select($query, [$email], "s");

        if ($res && mysqli_num_rows($res) == 1) {
            $row = mysqli_fetch_assoc($res);
            // Kiểm tra mật khẩu (hash hoặc plain text - tùy cách lưu)
            // Nếu dùng password_hash() thì dùng password_verify()
            // Nếu lưu plain text (tạm thời) thì so sánh trực tiếp
            if (password_verify($password, $row['password']) || $row['password'] === $password) {
                $_SESSION['ownerLogin'] = true;
                $_SESSION['ownerId'] = $row['id'];
                $_SESSION['ownerName'] = $row['name'];
                $_SESSION['ownerEmail'] = $row['email'];
                $_SESSION['ownerHotelName'] = $row['hotel_name'] ?? '';
                redirect('dashboard.php');
            } else {
                alert('error', 'Mật khẩu không đúng!');
            }
        } else {
            alert('error', 'Email không tồn tại hoặc tài khoản đã bị khóa!');
        }
    }
}
?>

<?php require('../admin/inc/scripts.php'); ?>
</body>
</html>

