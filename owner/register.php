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
<title>Đăng ký Chủ khách sạn</title>
<?php require('../admin/inc/links.php'); ?>
<style>
body {
    margin: 0;
    min-height: 100vh;
    font-family: 'Poppins', sans-serif;
    background: radial-gradient(circle at 50% 50%, #0a0f1c, #04060b 70%);
    padding: 40px 20px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.register-box {
    width: 900px;
    max-width: 100%;
    padding: 40px;
    background: rgba(15, 23, 42, 0.85);
    border: 2px solid rgba(0, 210, 255, 0.3);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 210, 255, 0.2);
    backdrop-filter: blur(10px);
}

.register-box .row {
    margin-left: 0;
    margin-right: 0;
}

.register-box .row > [class*="col-"] {
    padding-left: 15px;
    padding-right: 15px;
}

@media (max-width: 768px) {
    .register-box {
        width: 100%;
        padding: 20px;
    }
}

.register-box h2 {
    color: #00d0ff;
    text-align: center;
    margin-bottom: 30px;
    font-size: 28px;
    text-shadow: 0 0 20px rgba(0, 210, 255, 0.5);
}

.register-box .form-label {
    color: #8affff;
    margin-bottom: 8px;
    font-weight: 500;
}

.register-box .mb-3 {
    margin-bottom: 1rem !important;
}

.register-box input, .register-box textarea, .register-box select {
    width: 100%;
    padding: 12px;
    margin-bottom: 0;
    border: 2px solid rgba(0, 210, 255, 0.3);
    border-radius: 10px;
    background: rgba(15, 23, 42, 0.6);
    color: #fff;
    font-size: 15px;
    transition: all 0.3s;
}

.register-box .position-relative input[type="password"],
.register-box .position-relative input#address {
    padding-right: 50px !important;
}

.register-box input:focus, .register-box textarea:focus, .register-box select:focus {
    outline: none;
    border-color: #00d0ff;
    box-shadow: 0 0 15px rgba(0, 210, 255, 0.3);
}

.register-box select option {
    background: rgba(15, 23, 42, 0.95);
    color: #fff;
}

.register-box .position-relative input[type="password"],
.register-box .position-relative input#address {
    padding-right: 50px !important;
}

.register-box button {
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
    margin-top: 10px;
}

.register-box button:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 25px rgba(0,180,255,0.75);
}

.register-box button:active {
    transform: translateY(2px);
}

.register-box button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.register-box button:disabled:hover {
    transform: none;
    box-shadow: 0 6px 0 #003c78, 0 15px 30px rgba(0,180,255,0.55);
}

.register-box .btn {
    background: rgba(15, 23, 42, 0.6);
    border: 2px solid rgba(0, 210, 255, 0.3);
    color: #00d0ff;
    transition: all 0.3s;
}

.register-box .btn:hover:not(:disabled) {
    border-color: #00d0ff;
    box-shadow: 0 0 15px rgba(0, 210, 255, 0.3);
}

.register-box .btn-outline-info {
    border-color: rgba(0, 210, 255, 0.5);
    color: #00d0ff;
}

.register-box .btn-outline-success {
    border-color: rgba(0, 255, 150, 0.5);
    color: #00ff96;
}

.register-box small {
    display: block;
    margin-top: 5px;
    font-size: 12px;
}

.register-box small.text-danger {
    color: #ff6b6b !important;
}

.register-box small.text-success {
    color: #00ff96 !important;
}

.register-box small.text-muted {
    color: #8affff !important;
    opacity: 0.7;
}

.register-links {
    text-align: center;
    margin-top: 20px;
    color: #8affff;
}

.register-links a {
    color: #00d0ff;
    text-decoration: none;
}

.register-links a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<div class="register-box">
    <h2><i class="bi bi-person-plus"></i> Đăng Ký Chủ Khách Sạn</h2>
    
    <?php if (!$table_exists): ?>
        <div class="alert alert-info">
            <strong>Lưu ý:</strong> Vui lòng chạy script SQL <code>database/database_updates_hotel_owners.sql</code> để tạo bảng hotel_owners trước.
        </div>
    <?php endif; ?>

    <form method="POST" id="registerForm">
        <!-- Bước 1: Thông tin đăng ký -->
        <div id="step1">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-person me-1"></i>Họ và tên *</label>
                        <input type="text" name="name" id="name" required placeholder="Nhập họ và tên">
                        <small class="text-danger" id="name-error" style="display: none;"></small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-envelope me-1"></i>Email *</label>
                        <input type="email" name="email" id="email" required placeholder="Nhập email">
                        <small class="text-danger" id="email-error" style="display: none;"></small>
                        <small class="text-muted d-block mt-1">
                            <i class="bi bi-info-circle"></i> Lưu ý: Mã xác thực sẽ được gửi tự động khi bạn ấn nút "Đăng ký"
                        </small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-telephone me-1"></i>Số điện thoại *</label>
                        <input type="tel" name="phone" id="phone" required pattern="[0][0-9]{9}" maxlength="10" placeholder="Nhập số điện thoại (VD: 0987654321)">
                        <small class="text-muted d-block mt-1">
                            <i class="bi bi-info-circle"></i> Số điện thoại Việt Nam (10 số, bắt đầu bằng 0)
                        </small>
                        <small class="text-danger" id="phone-error" style="display: none;"></small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-calendar-event me-1"></i>Ngày sinh *</label>
                        <input type="date" name="dob" id="dob" required max="">
                        <small class="text-muted d-block mt-1">
                            <i class="bi bi-info-circle"></i> Phải từ 18 tuổi trở lên
                        </small>
                        <small class="text-danger" id="dob-error" style="display: none;"></small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-geo-alt me-1"></i>Địa chỉ *</label>
                        <div class="position-relative">
                            <input type="text" name="address" id="address" required placeholder="Nhập địa chỉ hoặc click icon bên phải để lấy vị trí" style="padding-right: 50px;">
                            <button type="button" class="btn btn-sm position-absolute" id="btn-get-location" title="Lấy vị trí hiện tại" onclick="getCurrentLocation()" style="right: 10px; top: 20%; transform: translateY(-50%); z-index: 10; border: none; background: transparent; color: #8affff; padding: 8px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                <i class="bi bi-geo-alt-fill"></i>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-1">
                            <i class="bi bi-info-circle"></i> Nhấp vào biểu tượng <i class="bi bi-geo-alt-fill" style="font-size: 12px;"></i> bên phải để lấy vị trí hiện tại
                        </small>
                        <small class="text-danger" id="address-error" style="display: none;"></small>
                    </div>
                </div>
        
                <div class="col-md-6">
        <div class="mb-3">
                        <label class="form-label"><i class="bi bi-lock me-1"></i>Mật khẩu *</label>
                        <div class="position-relative">
                            <input type="password" name="password" id="password" required minlength="6" placeholder="Nhập mật khẩu" style="padding-right: 50px;">
                            <button type="button" class="btn btn-sm position-absolute" onclick="togglePassword('password', this)" style="right: 10px; top: 20%; transform: translateY(-50%); z-index: 10; border: none; background: transparent; color: #8affff; padding: 8px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-1">Tối thiểu 6 ký tự</small>
                        <small class="text-danger" id="password-error" style="display: none;"></small>
                    </div>
        </div>
        
                <div class="col-md-6">
        <div class="mb-3">
                        <label class="form-label"><i class="bi bi-lock me-1"></i>Xác nhận mật khẩu *</label>
                        <div class="position-relative">
                            <input type="password" name="password_confirm" id="password_confirm" required placeholder="Nhập lại mật khẩu" style="padding-right: 50px;">
                            <button type="button" class="btn btn-sm position-absolute" onclick="togglePassword('password_confirm', this)" style="right: 10px; top: 20%; transform: translateY(-50%); z-index: 10; border: none; background: transparent; color: #8affff; padding: 8px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="text-danger" id="password_confirm-error" style="display: none;"></small>
                    </div>
        </div>
        
                <div class="col-md-6">
        <div class="mb-3">
                        <label class="form-label"><i class="bi bi-gender-ambiguous me-1"></i>Giới tính *</label>
                        <select name="gender" id="gender" class="form-control" required>
                            <option value="">-- Chọn giới tính --</option>
                            <option value="male">Nam</option>
                            <option value="female">Nữ</option>
                        </select>
                        <small class="text-danger" id="gender-error" style="display: none;"></small>
                    </div>
        </div>
        
                <div class="col-md-6">
        <div class="mb-3">
                        <label class="form-label"><i class="bi bi-building me-1"></i>Tên khách sạn *</label>
                        <input type="text" name="hotel_name" id="hotel_name" required placeholder="Nhập tên khách sạn">
                        <small class="text-danger" id="hotel_name-error" style="display: none;"></small>
                    </div>
                </div>
            </div>
            
            <button type="button" id="registerBtn" class="w-100 mt-3">
                <i class="bi bi-person-plus me-2"></i>Đăng Ký
            </button>
        </div>
        
        <!-- Bước 2: Xác thực email OTP (ẩn ban đầu) -->
        <div id="step2" style="display: none;">
            <div class="alert alert-info mb-3" style="background: rgba(0, 210, 255, 0.1); border: 1px solid rgba(0, 210, 255, 0.3); color: #00d0ff; border-radius: 10px; padding: 15px;">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Mã OTP đã được gửi đến email của bạn!</strong>
                <br>
                <small>Vui lòng kiểm tra hộp thư và nhập mã 6 số để hoàn tất đăng ký. Mã có hiệu lực trong 10 phút.</small>
        </div>
        
        <div class="mb-3">
                <label class="form-label">Mã xác thực email *</label>
                <div class="d-flex gap-2">
                    <input type="text" name="otp" id="otp" placeholder="Nhập mã 6 số" maxlength="6" pattern="[0-9]{6}" style="font-size: 20px; text-align: center; letter-spacing: 5px;">
                    <button type="button" id="verifyOtpBtn" class="btn btn-outline-success" style="white-space: nowrap; padding: 12px 20px;">
                        <i class="bi bi-check-circle"></i> Xác thực
                    </button>
                </div>
                <small class="text-danger" id="otp-error" style="display: none;"></small>
                <small class="text-success" id="otp-success" style="display: none;"></small>
            </div>
            
            <div class="d-flex gap-2">
                <button type="button" id="backBtn" class="btn btn-outline-secondary" style="flex: 1;">
                    <i class="bi bi-arrow-left me-2"></i>Quay lại
                </button>
                <button type="button" id="resendOtpBtn" class="btn btn-outline-info" style="flex: 1;">
                    <i class="bi bi-arrow-clockwise me-2"></i>Gửi lại mã
                </button>
            </div>
        </div>
    </form>

    <div class="register-links">
        <a href="index.php">Đã có tài khoản? Đăng nhập</a>
    </div>
</div>

<?php require('../admin/inc/scripts.php'); ?>

<script>
// ===================== XỬ LÝ FORM ĐĂNG KÝ =====================
const registerForm = document.getElementById('registerForm');
const step1 = document.getElementById('step1');
const step2 = document.getElementById('step2');
const verifyOtpBtn = document.getElementById('verifyOtpBtn');
const registerBtn = document.getElementById('registerBtn');
const backBtn = document.getElementById('backBtn');
const resendOtpBtn = document.getElementById('resendOtpBtn');
let emailVerified = false;

// Khi click nút Đăng Ký - Validate và gửi OTP
registerBtn.addEventListener('click', function() {
    // Validate tất cả trường
    if(!validateForm()) {
        return;
    }
    
    // Disable button
    registerBtn.disabled = true;
    registerBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Đang gửi mã OTP...';
    
    // Lấy dữ liệu form
    const formData = new FormData();
    formData.append('send_otp', '1');
    formData.append('email', document.getElementById('email').value.trim());
    formData.append('name', document.getElementById('name').value.trim());
    formData.append('phone', document.getElementById('phone').value.trim());
    formData.append('dob', document.getElementById('dob').value);
    formData.append('gender', document.getElementById('gender').value);
    formData.append('password', document.getElementById('password').value);
    formData.append('hotel_name', document.getElementById('hotel_name').value.trim());
    formData.append('address', document.getElementById('address').value.trim());
    
    // Gửi request gửi OTP
    fetch('ajax/register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            // Ẩn step1, hiện step2
            step1.style.display = 'none';
            step2.style.display = 'block';
            document.getElementById('otp').focus();
            
            // Hiển thị OTP trong dev (server trả về otp_debug)
            if(data.otp_debug) {
                console.info('DEV OTP (owner):', data.otp_debug);
                if(typeof showToast === 'function') {
                    showToast('info', 'DEV OTP: ' + data.otp_debug, 4000);
                }
            }
            
            if(typeof showToast === 'function') {
                showToast('success', data.msg, 3000);
            }
    } else {
            registerBtn.disabled = false;
            registerBtn.innerHTML = '<i class="bi bi-person-plus me-2"></i>Đăng Ký';
            showError('email-error', data.msg || 'Không thể gửi mã OTP!');
            if(typeof showToast === 'function') {
                showToast('error', data.msg || 'Không thể gửi mã OTP!', 3000);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        registerBtn.disabled = false;
        registerBtn.innerHTML = '<i class="bi bi-person-plus me-2"></i>Đăng Ký';
        if(typeof showToast === 'function') {
            showToast('error', 'Có lỗi xảy ra. Vui lòng thử lại!', 3000);
        }
    });
});

// Gửi lại mã OTP
resendOtpBtn.addEventListener('click', function() {
    // Validate lại form
    if(!validateForm()) {
        step1.style.display = 'block';
        step2.style.display = 'none';
        return;
    }
    
    // Disable button
    resendOtpBtn.disabled = true;
    resendOtpBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Đang gửi...';
    
    // Lấy dữ liệu form
    const formData = new FormData();
    formData.append('send_otp', '1');
    formData.append('email', document.getElementById('email').value.trim());
    formData.append('name', document.getElementById('name').value.trim());
    formData.append('phone', document.getElementById('phone').value.trim());
    formData.append('dob', document.getElementById('dob').value);
    formData.append('address', document.getElementById('address').value.trim());
    formData.append('gender', document.getElementById('gender').value);
    formData.append('password', document.getElementById('password').value);
    formData.append('hotel_name', document.getElementById('hotel_name').value.trim());
    
    // Gửi request
    fetch('ajax/register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        resendOtpBtn.disabled = false;
        resendOtpBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Gửi lại mã';
        
        if(data.status === 'success') {
            hideError('otp-error');
            
            if(data.otp_debug) {
                console.info('DEV OTP (owner - resend):', data.otp_debug);
                if(typeof showToast === 'function') {
                    showToast('info', 'DEV OTP: ' + data.otp_debug, 4000);
                }
            }
            
            if(typeof showToast === 'function') {
                showToast('success', 'Mã OTP mới đã được gửi!', 3000);
            }
            document.getElementById('otp').value = '';
            document.getElementById('otp').focus();
        } else {
            showError('otp-error', data.msg || 'Không thể gửi lại mã OTP!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        resendOtpBtn.disabled = false;
        resendOtpBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Gửi lại mã';
        if(typeof showToast === 'function') {
            showToast('error', 'Có lỗi xảy ra. Vui lòng thử lại!', 3000);
        }
    });
});

// Quay lại bước 1
backBtn.addEventListener('click', function() {
    step1.style.display = 'block';
    step2.style.display = 'none';
    emailVerified = false;
    document.getElementById('otp').value = '';
    hideError('otp-error');
    hideError('otp-success');
});

// Xác thực mã OTP và đăng ký
verifyOtpBtn.addEventListener('click', function() {
    const otp = document.getElementById('otp').value.trim();
    const email = document.getElementById('email').value.trim();
    
    if(!otp || otp.length !== 6) {
        showError('otp-error', 'Mã OTP phải có 6 số!');
        return;
    }
    
    // Disable button
    verifyOtpBtn.disabled = true;
    verifyOtpBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Đang xác thực...';
    
    // Gửi request xác thực OTP
    const formData = new FormData();
    formData.append('verify_otp', '1');
    formData.append('otp', otp);
    formData.append('email', email);
    
    fetch('ajax/register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            // OTP hợp lệ - Tiến hành đăng ký
            emailVerified = true;
            hideError('otp-error');
            showSuccess('otp-success', 'Email đã được xác thực! Đang hoàn tất đăng ký...');
            
            // Gửi request đăng ký
            const registerData = new FormData();
            registerData.append('register', '1');
            registerData.append('email', email);
            registerData.append('name', document.getElementById('name').value.trim());
            registerData.append('phone', document.getElementById('phone').value.trim());
            registerData.append('dob', document.getElementById('dob').value);
            registerData.append('address', document.getElementById('address').value.trim());
            registerData.append('gender', document.getElementById('gender').value);
            registerData.append('password', document.getElementById('password').value);
            registerData.append('hotel_name', document.getElementById('hotel_name').value.trim());
            
            return fetch('ajax/register.php', {
                method: 'POST',
                body: registerData
            });
        } else {
            verifyOtpBtn.disabled = false;
            verifyOtpBtn.innerHTML = '<i class="bi bi-check-circle"></i> Xác thực';
            showError('otp-error', data.msg || 'Mã OTP không đúng!');
            return Promise.reject(new Error(data.msg));
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            if(typeof showToast === 'function') {
                showToast('success', data.msg, 3000);
            } else {
                alert(data.msg);
            }
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 2000);
        } else {
            verifyOtpBtn.disabled = false;
            verifyOtpBtn.innerHTML = '<i class="bi bi-check-circle"></i> Xác thực';
            if(typeof showToast === 'function') {
                showToast('error', data.msg, 3000);
            } else {
                alert(data.msg);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        verifyOtpBtn.disabled = false;
        verifyOtpBtn.innerHTML = '<i class="bi bi-check-circle"></i> Xác thực';
        if(typeof showToast === 'function') {
            showToast('error', 'Có lỗi xảy ra. Vui lòng thử lại!', 3000);
        }
    });
});

// Validation functions
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^[0-9]{10,11}$/;
    return re.test(phone);
}

function validateForm() {
    let isValid = true;
    
    // Name
    const name = document.getElementById('name').value.trim();
    if(!name) {
        showError('name-error', 'Vui lòng nhập họ và tên!');
        isValid = false;
    } else {
        hideError('name-error');
    }
    
    // Email
    const email = document.getElementById('email').value.trim();
    if(!email || !validateEmail(email)) {
        showError('email-error', 'Email không hợp lệ!');
        isValid = false;
    } else {
        hideError('email-error');
    }
    
    // Password
    const password = document.getElementById('password').value;
    if(!password || password.length < 6) {
        showError('password-error', 'Mật khẩu phải có ít nhất 6 ký tự!');
        isValid = false;
    } else {
        hideError('password-error');
    }
    
    // Password confirm
    const passwordConfirm = document.getElementById('password_confirm').value;
    if(password !== passwordConfirm) {
        showError('password_confirm-error', 'Mật khẩu xác nhận không khớp!');
        isValid = false;
    } else {
        hideError('password_confirm-error');
    }
    
    // Phone
    const phone = document.getElementById('phone').value.trim();
    if(!phone || !validatePhone(phone)) {
        showError('phone-error', 'Số điện thoại không hợp lệ! (10-11 số)');
        isValid = false;
    } else {
        hideError('phone-error');
    }
    
    // Hotel name
    const hotelName = document.getElementById('hotel_name').value.trim();
    if(!hotelName) {
        showError('hotel_name-error', 'Vui lòng nhập tên khách sạn!');
        isValid = false;
    } else {
        hideError('hotel_name-error');
    }
    
    // Address
    const address = document.getElementById('address').value.trim();
    if(!address) {
        showError('address-error', 'Vui lòng nhập địa chỉ khách sạn!');
        isValid = false;
    } else {
        hideError('address-error');
    }
    
    // DOB
    const dob = document.getElementById('dob').value;
    if(!dob) {
        showError('dob-error', 'Vui lòng chọn ngày sinh!');
        isValid = false;
    } else {
        // Kiểm tra tuổi >= 18
        const birthDate = new Date(dob);
        const today = new Date();
        const age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        if(age < 18) {
            showError('dob-error', 'Bạn phải từ 18 tuổi trở lên!');
            isValid = false;
        } else {
            hideError('dob-error');
        }
    }
    
    // Gender
    const gender = document.getElementById('gender').value;
    if(!gender) {
        showError('gender-error', 'Vui lòng chọn giới tính!');
        isValid = false;
    } else {
        hideError('gender-error');
    }
    
    return isValid;
}

function showError(id, message) {
    const errorEl = document.getElementById(id);
    if(errorEl) {
        errorEl.textContent = message;
        errorEl.style.display = 'block';
    }
}

function hideError(id) {
    const errorEl = document.getElementById(id);
    if(errorEl) {
        errorEl.style.display = 'none';
    }
}

function showSuccess(id, message) {
    const successEl = document.getElementById(id);
    if(successEl) {
        successEl.textContent = message;
        successEl.style.display = 'block';
    }
}

// Real-time validation
document.getElementById('password_confirm').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const passwordConfirm = this.value;
    if(passwordConfirm && password !== passwordConfirm) {
        showError('password_confirm-error', 'Mật khẩu xác nhận không khớp!');
    } else {
        hideError('password_confirm-error');
    }
});

// Chỉ cho phép nhập số cho OTP
document.getElementById('otp').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// Chỉ cho phép nhập số cho phone
document.getElementById('phone').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// Enter key để submit OTP
document.getElementById('otp').addEventListener('keypress', function(e) {
    if(e.key === 'Enter') {
        verifyOtpBtn.click();
    }
});

// Set max date cho input date (18 tuổi)
document.addEventListener('DOMContentLoaded', function() {
    const dobInput = document.getElementById('dob');
    if(dobInput) {
        const today = new Date();
        const maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
        dobInput.max = maxDate.toISOString().split('T')[0];
    }
});

// Toggle password visibility
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if(input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Get current location
function getCurrentLocation() {
    const addressInput = document.getElementById('address');
    const btnLocation = document.getElementById('btn-get-location');
    
    if (!addressInput || !btnLocation) return;
    
    // Hiển thị loading
    btnLocation.disabled = true;
    btnLocation.innerHTML = '<i class="bi bi-hourglass-split"></i>';
    
    // Kiểm tra xem browser có hỗ trợ Geolocation không
    if (!navigator.geolocation) {
        if (typeof showToast === 'function') {
            showToast('error', 'Trình duyệt không hỗ trợ lấy vị trí', 3000);
        } else {
            alert('Trình duyệt không hỗ trợ lấy vị trí');
        }
        btnLocation.disabled = false;
        btnLocation.innerHTML = '<i class="bi bi-geo-alt-fill"></i>';
        return;
    }
    
    // Lấy vị trí hiện tại
    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            // Sử dụng reverse geocoding để lấy địa chỉ
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language=vi`, {
                headers: {
                    'User-Agent': 'VinhLongHotel/1.0'
                }
            })
            .then(response => response.json())
            .then(data => {
                let fullAddress = '';
                
                if (data && data.display_name) {
                    fullAddress = data.display_name;
                } else if (data && data.address) {
                    const addressParts = [];
                    if (data.address.road) addressParts.push(data.address.road);
                    if (data.address.suburb || data.address.neighbourhood) addressParts.push(data.address.suburb || data.address.neighbourhood);
                    if (data.address.city || data.address.town || data.address.village) addressParts.push(data.address.city || data.address.town || data.address.village);
                    if (data.address.state) addressParts.push(data.address.state);
                    if (data.address.country) addressParts.push(data.address.country);
                    fullAddress = addressParts.join(', ');
                }
                
                if (!fullAddress || fullAddress.trim() === '') {
                    if (typeof showToast === 'function') {
                        showToast('warning', 'Không thể lấy địa chỉ tự động. Vui lòng nhập địa chỉ thủ công.', 4000);
                    }
                    addressInput.value = '';
                    addressInput.focus();
                } else {
                    addressInput.value = fullAddress;
                    if (typeof showToast === 'function') {
                        showToast('success', 'Đã lấy địa chỉ hiện tại!', 2000);
                    }
                }
                
                btnLocation.disabled = false;
                btnLocation.innerHTML = '<i class="bi bi-geo-alt-fill"></i>';
            })
            .catch(error => {
                console.error('Lỗi khi lấy địa chỉ:', error);
                if (typeof showToast === 'function') {
                    showToast('error', 'Không thể lấy địa chỉ tự động. Vui lòng nhập địa chỉ thủ công.', 4000);
                }
                addressInput.value = '';
                addressInput.focus();
                btnLocation.disabled = false;
                btnLocation.innerHTML = '<i class="bi bi-geo-alt-fill"></i>';
            });
        },
        function(error) {
            let errorMsg = 'Không thể lấy vị trí';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMsg = 'Bạn đã từ chối quyền truy cập vị trí. Vui lòng cho phép trong cài đặt trình duyệt.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMsg = 'Thông tin vị trí không khả dụng.';
                    break;
                case error.TIMEOUT:
                    errorMsg = 'Hết thời gian chờ lấy vị trí.';
                    break;
            }
            
            if (typeof showToast === 'function') {
                showToast('error', errorMsg, 4000);
            } else {
                alert(errorMsg);
            }
            
            btnLocation.disabled = false;
            btnLocation.innerHTML = '<i class="bi bi-geo-alt-fill"></i>';
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

// Attach getCurrentLocation to button
document.addEventListener('DOMContentLoaded', function() {
    const btnLocation = document.getElementById('btn-get-location');
    if(btnLocation) {
        btnLocation.addEventListener('click', getCurrentLocation);
    }
});
</script>
</body>
</html>

