# Vinh Long Hotel

## Thông tin liên lạc
- Email: phamminhman719@gmail.com
- Điện thoại: 0823521928

## Tổng quan dự án
Đề tài: Xây dựng Website hỗ trợ tìm kiếm và đặt phòng khách sạn trên địa bàn Tỉnh Vĩnh Long.

Hệ thống đặt phòng khách sạn trực tuyến, hỗ trợ trải nghiệm đặt phòng nhanh, quản lý nội dung, quản lý đặt phòng và giao tiếp khách hàng trên nhiều kênh.

## Tính năng chính
### Khách hàng (User)
- Tìm kiếm/phân loại phòng, xem chi tiết phòng
- Đặt phòng theo ngày, xác nhận đặt phòng, lịch sử đặt phòng
- Thanh toán QR (VietQR/MoMo/ZaloPay) và các phương thức khác (hiển thị trong giao diện)
- Đánh giá phòng, nhận xét
- Đăng ký/đăng nhập tài khoản, đăng nhập Google
- Hồ sơ người dùng, lịch sử thanh toán
- Gợi ý tìm kiếm (phòng/điểm đến), đa ngôn ngữ (VI/EN)
- Nhắn tin hỗ trợ trực tuyến (live chat + bot trả lời tự động)

### Chủ khách sạn (Owner)
- Dashboard doanh thu, thống kê đặt phòng
- Quản lý phòng, xem chi tiết đặt phòng, xử lý hoàn tiền
- Tin nhắn khách hàng
- Trang cá nhân chủ khách sạn

### Quản trị viên (Admin)
- Dashboard quản trị tổng quan
- Quản lý phòng, tiện ích, phòng/điểm đến, carousel
- Quản lý booking, hoàn tiền, đánh giá, mã khuyến mãi
- Quản lý users, owners, tin nhắn, FAQ, newsletter
- Cấu hình hệ thống và xuất PDF

## Công nghệ sử dụng
- Backend: PHP 8.x
- Frontend: HTML, CSS, JavaScript
- CSDL: MySQL/MariaDB
- UI: Bootstrap 5, Swiper
- Thư viện: PHPMailer, Dompdf (trong `admin/vendor/`)

## Yêu cầu hệ thống
- PHP 8.x
- MySQL/MariaDB
- XAMPP (khuyến nghị)
- Trình duyệt hiện đại (Chrome/Edge/Firefox)

## Cài đặt nhanh (XAMPP)
1. Cài XAMPP và bật Apache + MySQL.
2. Copy project vào `C:\xampp\htdocs\vinhlong_hotel`.
3. Tạo database `vinhlong_hotel` và import `database/vinhlong_hotel.sql`.
4. (Tùy chọn) Import các file cập nhật trong `database/` nếu cần tính năng mới.
5. Truy cập: `http://localhost/vinhlong_hotel`.

## Cấu hình quan trọng
### Database
- Cấu hình tại `admin/inc/db_config.php`
- Thông số mặc định: host `localhost`, user `root`, pass rỗng, db `vinhlong_hotel`

### SMTP gửi email
- Cấu hình tại `admin/inc/smtp_config.php`
- Cập nhật lại thông tin SMTP và mật khẩu ứng dụng trước khi chạy production

### Google Login (OAuth)
- Cấu hình tại `google-login.php`
- Cập nhật `client_id`, `client_secret`, `redirect_uri` theo dự án của bạn

## Tài khoản demo
- User: 
  - Tài khoản: phamminhman888@gmail.com
  - Mật khẩu: 123123
- Admin: 
  - Tài khoản: Man
  - Mật khẩu: 123123
- Owner:
  - Tài khoản: cks2@gmail.com
  - Mật khẩu: 123123
## C?u tr?c th? m?c
```
vinhlong_hotel/
?? .cursor/
?? .vscode/
?? admin/
?  ?? ajax/
?  ?? css/
?  ?? inc/
?  ?? modals/
?  ?? scripts/
?  ?? vendor/
?  ?? booking_records.php
?  ?? carousel.php
?  ?? composer.json
?  ?? composer.lock
?  ?? dashboard.php
?  ?? destinations.php
?  ?? edit_user.php
?  ?? faqs.php
?  ?? features_facilities.php
?  ?? generate_pdf.php
?  ?? index.php
?  ?? logout.php
?  ?? messages.php
?  ?? newsletter.php
?  ?? new_bookings.php
?  ?? owners.php
?  ?? promos.php
?  ?? rate_review.php
?  ?? refund_bookings.php
?  ?? rooms.php
?  ?? settings.php
?  ?? users.php
?  ?? user_queries.php
?? owner/
?  ?? ajax/
?  ?? inc/
?  ?? bookings.php
?  ?? booking_details.php
?  ?? dashboard.php
?  ?? index.php
?  ?? invoice.php
?  ?? logout.php
?  ?? messages.php
?  ?? profile.php
?  ?? refund_bookings.php
?  ?? register.php
?  ?? revenue.php
?  ?? reviews.php
?  ?? rooms.php
?? ajax/
?? css/
?? database/
?? fonts/
?? images/
?  ?? about/
?  ?? app/
?  ?? carousel/
?  ?? cars/
?  ?? destinations/
?  ?? facilities/
?  ?? features/
?  ?? pay/
?  ?? reviews/
?  ?? rooms/
?  ?? specialties/
?  ?? team/
?  ?? The public/
?  ?? users/
?  ?? vehicles/
?  ?? Battay.png
?  ?? Chuky.png
?  ?? logo.png
?? inc/
?  ?? chat_widget.php
?  ?? footer.php
?  ?? header.php
?  ?? links.php
?  ?? modals.php
?? js/
?? lib/
?? logo/
?? .gitignore
?? about.php
?? bookings.php
?? confirm_booking.php
?? contact.php
?? destinations.php
?? destination_details.php
?? facilities.php
?? google-login.php
?? index.php
?? logout.php
?? messages.php
?? otp_log.txt
?? payment_history.php
?? pay_now.php
?? profile.php
?? README.md
?? rooms.php
?? room_details.php
?? specialty_details.php
```


## Tài nguyên hình ảnh mẫu
Logo:
![Logo](logo/Vinh%20Long%20Hotel.png)

Ảnh carousel:
![Carousel](images/carousel/1.jpg)

Ảnh giới thiệu:
![About](images/about/logotvu.png)

## Ghi chú
- Nếu thay đổi cấu trúc DB, hãy cập nhật các file SQL tương ứng trong `database/`.
- Nếu gặp lỗi hiển thị tiếng Việt, đảm bảo DB và kết nối dùng `utf8mb4`.
