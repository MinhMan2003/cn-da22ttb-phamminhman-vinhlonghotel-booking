<?php
require('inc/essentials.php');
require('inc/db_config.php');

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if ((isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true)) {
    redirect('dashboard.php');
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng nhập ADMIN</title>

<?php require('inc/links.php'); ?>

<style>
/* ============================
   🌌 DARK MATTER PHOTON BACKGROUND
   ============================ */

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

/* Dark matter particles */
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

/* Photon particles */
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

/* ============================
   🧊 LOGIN BOX — 3D PHOTON PANEL
   ============================ */
.login-box {
    width: 420px;
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(18px);
    border-radius: 20px;
    padding: 0 0 25px 0;
    position: relative;
    z-index: 10;

    border: 1px solid rgba(120,200,255,0.15);
    box-shadow:
        0 0 25px rgba(0,150,255,0.35),
        inset 0 0 25px rgba(155,0,255,0.15);

    transform-style: preserve-3d;
    animation: floatBox 4.5s infinite ease-in-out alternate;
}

/* Floating effect */
@keyframes floatBox {
    0%   { transform: translateY(0px) rotateX(0deg); }
    100% { transform: translateY(-12px) rotateX(5deg); }
}

/* Neon border pulse */
.login-box::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 20px;
    padding: 2px;
    background: linear-gradient(130deg, #7a00ff, #00eaff, #7a00ff);
    background-size: 300%;
    animation: holoBorder 4s linear infinite;

    -webkit-mask:
        linear-gradient(#fff 0 0) content-box,
        linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
            mask-composite: exclude;
}

@keyframes holoBorder {
    0%   { background-position: 0%; }
    100% { background-position: 300%; }
}

/* ============================
   HEADER
   ============================ */
.login-box h2 {
    margin: 0;
    padding: 22px 0;
    text-align: center;
    font-size: 22px;
    color: #dff4ff;
    text-shadow: 0 0 15px #00d0ff;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

/* ============================
   INPUTS
   ============================ */
.login-box input {
    width: 80%;
    margin: 18px auto;
    display: block;

    padding: 13px;
    border-radius: 12px;
    border: none;
    text-align: center;
    font-size: 16px;

    background: rgba(255,255,255,0.9);
    box-shadow: inset 0 4px 8px rgba(0,0,0,0.25);
    transition: 0.25s;
}

.login-box input:focus {
    transform: scale(1.05);
    box-shadow: 0 0 15px #00d0ff;
}

/* ============================
   BUTTON — PHOTON 3D
   ============================ */
.login-box button {
    width: 80%;
    margin: 20px auto 0 auto;
    display: block;

    padding: 14px;
    border-radius: 12px;
    font-size: 18px;
    border: none;

    color: white;
    cursor: pointer;
    background: linear-gradient(100deg, #0066ff, #00eaff);

    box-shadow:
       0 6px 0 #003c78,
       0 15px 30px rgba(0,180,255,0.55),
       inset 0 0 12px rgba(255,255,255,0.18);

    transition: 0.25s;
}

.login-box button:hover {
    transform: translateY(-6px) rotateX(6deg);
    box-shadow:
       0 10px 25px rgba(0,180,255,0.75),
       0 4px 10px rgba(0,0,0,0.45);
}

.login-box button:active {
    transform: translateY(2px);
    box-shadow: inset 0 4px 8px rgba(0,0,0,0.5);
}
/* FIX CLICK – đảm bảo hiệu ứng không che form */
#space-bg, #photon {
    pointer-events: none !important;
    z-index: 0 !important;
}

.login-box, .login-box * {
    pointer-events: auto !important;
    z-index: 10 !important;
    position: relative;
}

</style>

<script>
// Create photon particles
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

</head>

<body>

<div id="space-bg"></div>
<div id="photon"></div>

<!-- LOGIN BOX -->
<div class="login-box">
    <form method="POST">
        <h2>Hệ Thống Quản Trị</h2>

        <input type="text" name="admin_name" placeholder="Tên quản trị viên" required>
        <input type="password" name="admin_pass" placeholder="Mật khẩu" required>

        <button type="submit" name="login">Đăng Nhập</button>
    </form>
</div>

<?php
if (isset($_POST['login'])) {
    $frm_data = filteration($_POST);

    $query = "SELECT * FROM admin_cred WHERE admin_name=? AND admin_pass=?";
    $values = [$frm_data['admin_name'], $frm_data['admin_pass']];

    $res = select($query, $values, "ss");

    if ($res->num_rows == 1) {
        $row = mysqli_fetch_assoc($res);
        $_SESSION['adminLogin'] = true;
        $_SESSION['adminId'] = $row['sr_no'];
        redirect('dashboard.php');
    } else {
        alert('error', 'Đăng nhập thất bại!');
    }
}
?>

<?php require('inc/scripts.php'); ?>
</body>
</html>
