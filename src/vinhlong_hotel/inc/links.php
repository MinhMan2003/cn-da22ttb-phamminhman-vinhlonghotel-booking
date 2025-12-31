<!-- ============================
     BOOTSTRAP 5
============================= -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- ============================
     ICONS
============================= -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- ============================
     GOOGLE FONTS (Gộp 3 font vào 1 link)
============================= -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&
family=Merriweather:wght@300;400;700;900&
family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" 
rel="stylesheet">

<!-- ============================
     SWIPER
============================= -->
<link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css">

<!-- ============================
     CUSTOM CSS
============================= -->
<link rel="stylesheet" href="css/common.css?v=<?php echo time(); ?>">

<!-- FONT INTER -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">


<?php
/* ============================
   PHP CẤU HÌNH & TRUY VẤN DB
============================= */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

// Lấy ngôn ngữ từ cookie (để sử dụng trong HTML lang attribute)
$html_lang = isset($_COOKIE['lang']) && ($_COOKIE['lang'] === 'en' || $_COOKIE['lang'] === 'vi') 
    ? $_COOKIE['lang'] 
    : 'vi';

/* Lấy thông tin cài đặt */
$contact_r  = mysqli_fetch_assoc(select(
                    "SELECT * FROM `contact_details` WHERE `sr_no`=?", 
                    [1], 'i'
                ));

$settings_r = mysqli_fetch_assoc(select(
                    "SELECT * FROM `settings` WHERE `sr_no`=?", 
                    [1], 'i'
                ));


/* ============================
   THÔNG BÁO BẢO TRÌ
============================= */

if($settings_r['shutdown']){
    echo <<<HTML
    <div class="rb-alert">
        <div class="rb-flash"></div>
        <div class="rb-scan"></div>

        <div class="rb-icon">
            <i class="bi bi-exclamation-octagon-fill"></i>
        </div>

        <div class="rb-title blink-text">HỆ THỐNG ĐANG BẢO TRÌ</div>

        <div class="rb-sub">
            Hệ thống tạm ngưng để nâng cấp.<br>
            Tạm thời không thể đặt phòng.
        </div>
    </div>

<style>
/* ====== NỀN ĐỎ ĐEN CARBON ====== */
.rb-alert{
    position: relative;
    background: repeating-linear-gradient(
        135deg, #0c0c0c 0px, #0c0c0c 4px, #111 4px, #111 8px
    );
    border: 2px solid #7a0000;
    padding: 25px;
    margin-bottom: 25px;
    border-radius: 12px;
    text-align: center;
    overflow: hidden;
    color: #fff;
    box-shadow: 0 0 25px rgba(255,0,0,0.35);
}

/* ====== NEON VIỀN ====== */
.rb-flash{
    position: absolute;
    inset: 0;
    border-radius: 12px;
    pointer-events: none;
    animation: flashGlow 1.2s infinite ease-in-out;
    box-shadow: 0 0 30px rgba(255,0,0,0.8) inset;
}
@keyframes flashGlow{
    0%{opacity:0.23;} 
    50%{opacity:0.8;} 
    100%{opacity:0.23;}
}

/* ====== LASER SCAN ====== */
.rb-scan{
    position: absolute;
    top: 0;
    left: -150%;
    width: 300%;
    height: 6px;
    background: linear-gradient(90deg, transparent, red, transparent);
    animation: scanMove 2s infinite linear;
}
@keyframes scanMove{
    0%{left:-150%;} 
    100%{left:150%;}
}

/* ====== ICON CẢNH BÁO ====== */
.rb-icon i{
    font-size: 60px;
    color: #ff3333;
    text-shadow: 0 0 25px rgba(255,0,0,0.9);
    animation: iconPulse 1.1s infinite ease-in-out;
}
@keyframes iconPulse{
    0%{transform:scale(1);opacity:0.8;} 
    50%{transform:scale(1.2);opacity:1;} 
    100%{transform:scale(1);opacity:0.8;}
}

/* ====== TEXT ====== */
.rb-title{
    margin-top: 12px;
    font-size: 26px;
    font-weight: 900;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.blink-text{
    animation: blinkText 0.9s infinite;
}
@keyframes blinkText{
    0%{color:#ff4d4d;text-shadow:0 0 8px #ff1a1a;}
    50%{color:#ff0000;text-shadow:0 0 18px #ff0000;}
    100%{color:#ff4d4d;text-shadow:0 0 8px #ff1a1a;}
}

.rb-sub{
    margin-top: 10px;
    font-size: 15px;
    opacity: 0.9;
}
</style>
HTML;
}
?>
