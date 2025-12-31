<?php
/**
 * Cấu hình SMTP để gửi email
 * 
 * HƯỚNG DẪN CẤU HÌNH:
 * 
 * 1. GMAIL:
 *    - SMTP_HOST: 'smtp.gmail.com'
 *    - SMTP_PORT: 587
 *    - SMTP_USER: 'your-email@gmail.com'
 *    - SMTP_PASS: 'your-app-password' (KHÔNG phải mật khẩu thường!)
 *    - Tạo App Password: https://myaccount.google.com/apppasswords
 * 
 * 2. OUTLOOK/HOTMAIL:
 *    - SMTP_HOST: 'smtp-mail.outlook.com'
 *    - SMTP_PORT: 587
 *    - SMTP_USER: 'your-email@outlook.com'
 *    - SMTP_PASS: 'your-password'
 * 
 * 3. YAHOO:
 *    - SMTP_HOST: 'smtp.mail.yahoo.com'
 *    - SMTP_PORT: 587
 *    - SMTP_USER: 'your-email@yahoo.com'
 *    - SMTP_PASS: 'your-app-password'
 * 
 * 4. CUSTOM SMTP:
 *    - Điền thông tin SMTP server của bạn
 */

// ======================
// CẤU HÌNH SMTP
// ======================

// Bật/tắt gửi email qua SMTP (true = bật, false = dùng mail() mặc định)
define('USE_SMTP', true);

// Thông tin SMTP Server
define('SMTP_HOST', 'smtp.gmail.com');        // Địa chỉ SMTP server
define('SMTP_PORT', 587);                      // Port (587 cho TLS, 465 cho SSL)
define('SMTP_USER', 'phamminhman719@gmail.com');  // Email đăng nhập
define('SMTP_PASS', 'rgdd cwzl nbnj pdxc');     // App Password mới (có dấu cách)
define('SMTP_SECURE', 'tls');                 // 'tls' hoặc 'ssl'
define('SMTP_FROM_EMAIL', 'phamminhman719@gmail.com'); // Email người gửi
define('SMTP_FROM_NAME', 'Vinh Long Hotel');  // Tên người gửi

// Debug (true = hiển thị lỗi chi tiết, false = ẩn lỗi)
define('SMTP_DEBUG', true);  // Bật để xem lỗi chi tiết

?>

