<?php
require('inc/essentials.php');
require('../admin/inc/db_config.php');
ownerLogin();

$owner_id = getOwnerId();

// Lấy thông tin owner
$owner_data = select("SELECT * FROM hotel_owners WHERE id=?", [$owner_id], 'i');
$owner = mysqli_fetch_assoc($owner_data);

// Xử lý cập nhật profile
$alert = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $frm_data = filteration($_POST);
    
    $name = $frm_data['name'] ?? '';
    $email = $frm_data['email'] ?? '';
    $phonenum = $frm_data['phonenum'] ?? '';
    $hotel_name = $frm_data['hotel_name'] ?? '';
    $address = $frm_data['address'] ?? '';
    
    // Kiểm tra email có thay đổi không
    $current_email = strtolower(trim($owner['email'] ?? ''));
    $new_email = strtolower(trim($email));
    
    // Nếu email thay đổi, kiểm tra xem email đã được đổi qua OTP chưa
    // (Email sẽ được tự động cập nhật trong ajax/profile.php sau khi xác thực OTP thành công)
    if($new_email !== $current_email) {
        // Kiểm tra email trùng
        $check_email = select("SELECT id FROM hotel_owners WHERE email=? AND id!=?", [$email, $owner_id], 'si');
        if (mysqli_num_rows($check_email) > 0) {
            $alert = '<div class="alert alert-danger">Email đã được sử dụng!</div>';
        } else {
            // Cập nhật email và các thông tin khác
            $update = update("
                UPDATE hotel_owners 
                SET name=?, email=?, phonenum=?, hotel_name=?, address=?
                WHERE id=?
            ", [$name, $email, $phonenum, $hotel_name, $address, $owner_id], 'sssssi');
            
            if ($update) {
                $_SESSION['ownerName'] = $name;
                $_SESSION['ownerHotelName'] = $hotel_name;
                $alert = '<div class="alert alert-success">Cập nhật thành công!</div>';
                // Reload để hiển thị dữ liệu mới
                header("Location: profile.php");
                exit;
            } else {
                $alert = '<div class="alert alert-danger">Có lỗi xảy ra!</div>';
            }
        }
    } else {
        // Email không thay đổi, cập nhật bình thường
        $update = update("
            UPDATE hotel_owners 
            SET name=?, phonenum=?, hotel_name=?, address=?
            WHERE id=?
        ", [$name, $phonenum, $hotel_name, $address, $owner_id], 'ssssi');
        
        if ($update) {
            $_SESSION['ownerName'] = $name;
            $_SESSION['ownerHotelName'] = $hotel_name;
            $alert = '<div class="alert alert-success">Cập nhật thành công!</div>';
            // Reload để hiển thị dữ liệu mới
            header("Location: profile.php");
            exit;
        } else {
            $alert = '<div class="alert alert-danger">Có lỗi xảy ra!</div>';
        }
    }
}

// Xử lý upload avatar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_avatar'])) {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        // Kiểm tra xem cột profile có tồn tại không
        $check_column = mysqli_query($con, "SHOW COLUMNS FROM `hotel_owners` LIKE 'profile'");
        if (!$check_column || mysqli_num_rows($check_column) == 0) {
            // Thêm cột profile nếu chưa có
            mysqli_query($con, "ALTER TABLE `hotel_owners` ADD COLUMN `profile` VARCHAR(255) DEFAULT NULL AFTER `address`");
        }
        
        // Upload ảnh
        $img = uploadUserImage($_FILES['avatar']);
        
        if ($img == 'inv_img') {
            $alert = '<div class="alert alert-danger">Định dạng ảnh không hợp lệ! Chỉ chấp nhận JPG, JPEG, PNG, WEBP.</div>';
        } elseif ($img == 'inv_size') {
            $alert = '<div class="alert alert-danger">Kích thước ảnh quá lớn! Tối đa 2MB.</div>';
        } elseif ($img == 'upd_failed') {
            $alert = '<div class="alert alert-danger">Không thể upload ảnh! Vui lòng thử lại.</div>';
        } else {
            // Xóa ảnh cũ nếu có
            if (!empty($owner['profile']) && $owner['profile'] != 'user.png') {
                deleteImage($owner['profile'], USERS_FOLDER);
            }
            
            // Cập nhật profile
            $update = update("UPDATE hotel_owners SET profile=? WHERE id=?", [$img, $owner_id], 'si');
            
            if ($update) {
                $alert = '<div class="alert alert-success">Cập nhật avatar thành công!</div>';
                // Reload để hiển thị avatar mới
                header("Location: profile.php");
                exit;
            } else {
                $alert = '<div class="alert alert-danger">Có lỗi xảy ra khi cập nhật!</div>';
            }
        }
    } else {
        $alert = '<div class="alert alert-danger">Vui lòng chọn ảnh!</div>';
    }
    
    // Reload để lấy dữ liệu mới
    $owner_data = select("SELECT * FROM hotel_owners WHERE id=?", [$owner_id], 'i');
    $owner = mysqli_fetch_assoc($owner_data);
}

// Xử lý đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $frm_data = filteration($_POST);
    
    $old_pass = $frm_data['old_pass'] ?? '';
    $new_pass = $frm_data['new_pass'] ?? '';
    $confirm_pass = $frm_data['confirm_pass'] ?? '';
    
    // Kiểm tra mật khẩu cũ
    if (!password_verify($old_pass, $owner['password'])) {
        $alert = '<div class="alert alert-danger">Mật khẩu cũ không đúng!</div>';
    } elseif ($new_pass !== $confirm_pass) {
        $alert = '<div class="alert alert-danger">Mật khẩu mới không khớp!</div>';
    } elseif (strlen($new_pass) < 6) {
        $alert = '<div class="alert alert-danger">Mật khẩu phải có ít nhất 6 ký tự!</div>';
    } else {
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        $update = update("UPDATE hotel_owners SET password=? WHERE id=?", [$hashed_pass, $owner_id], 'si');
        
        if ($update) {
            $alert = '<div class="alert alert-success">Đổi mật khẩu thành công!</div>';
        } else {
            $alert = '<div class="alert alert-danger">Có lỗi xảy ra!</div>';
        }
    }
    
    // Reload để lấy dữ liệu mới
    $owner_data = select("SELECT * FROM hotel_owners WHERE id=?", [$owner_id], 'i');
    $owner = mysqli_fetch_assoc($owner_data);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hồ sơ - Chủ khách sạn</title>
  <?php require('../admin/inc/links.php'); ?>
  <style>
    /* Page Header */
    .page-header {
      background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
      color: white;
      border-radius: 16px;
      padding: 2rem 2.5rem;
      margin-bottom: 2rem;
      box-shadow: 0 10px 26px rgba(0, 0, 0, 0.05);
      position: relative;
      overflow: hidden;
    }
    
    .page-header h4 {
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      text-shadow: 0 2px 4px rgba(0,0,0,0.1);
      color: white;
    }
    
    .page-header p {
      font-size: 1rem;
      opacity: 0.95;
      margin-bottom: 0;
      color: rgba(255, 255, 255, 0.9);
    }
    
    /* Cards */
    .card {
      border: 1px solid #e5e7eb;
      border-radius: 15px;
      box-shadow: 0 10px 26px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
    }
    
    .card:hover {
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
      transform: translateY(-2px);
    }
    
    .card-title {
      color: #0f172a;
      font-weight: 600;
    }
    
    /* Form Controls */
    .form-control {
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      color: #0f172a;
    }
    
    .form-control:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .form-label {
      color: #0f172a;
      font-weight: 500;
    }
    
    /* Buttons */
    .btn-primary {
      background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
      border: none;
      transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }
    
    .btn-warning {
      background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
      border: none;
      color: #0f172a;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .btn-warning:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
      color: #0f172a;
    }
    
    /* Badges */
    .badge {
      padding: 6px 12px;
      border-radius: 8px;
      font-weight: 500;
    }
    
    /* Alerts */
    .alert {
      border-radius: 12px;
      border: 1px solid #e5e7eb;
    }
    
    /* Avatar */
    #avatar-preview {
      transition: all 0.3s ease;
    }
    
    #avatar-preview:hover {
      opacity: 0.8;
      transform: scale(1.05);
    }
    
    /* OTP Inputs */
    .otp-input:focus {
      outline: none;
      transform: scale(1.1);
    }
    
    .otp-input::placeholder {
      color: rgba(13, 110, 253, 0.3);
    }
    
    /* Modal OTP */
    #emailChangeOTPModal .modal-content {
      border-radius: 15px;
      overflow: hidden;
    }
    
    #emailChangeOTPModal .modal-body {
      padding: 2rem;
    }
  </style>
  <script>
    function previewAvatar(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('avatar-preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>
</head>
<body class="bg-light">
  <div class="container-fluid p-0">
    <div class="row g-0">
      <?php require('inc/header.php'); ?>

      <div class="col-lg-10 p-4" id="main-content">
        
        <!-- Page Header -->
        <div class="page-header mb-4">
          <div>
            <h4 class="mb-2">
              <i class="bi bi-person-circle me-2"></i>Hồ sơ
            </h4>
            <p class="mb-0 opacity-90">Quản lý thông tin tài khoản của bạn</p>
          </div>
        </div>

        <?php echo $alert; ?>

        <!-- Profile Info - Full Width -->
        <div class="row g-4 mb-4">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title mb-4">Thông tin cá nhân</h5>
                <form method="POST">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label">Họ tên</label>
                      <input type="text" name="name" class="form-control shadow-none" 
                             value="<?php echo htmlspecialchars($owner['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Email</label>
                      <div class="d-flex gap-2">
                        <input type="email" name="email" id="owner-email-input" class="form-control shadow-none" 
                               value="<?php echo htmlspecialchars($owner['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required readonly>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="btn-change-email" style="white-space: nowrap;">
                          <i class="bi bi-pencil-fill me-1"></i>Đổi email
                        </button>
                      </div>
                      <small class="text-muted d-block mt-1">
                        <i class="bi bi-info-circle me-1"></i>Nhấn "Đổi email" để thay đổi. Cần xác thực qua mã OTP gửi đến email cũ.
                      </small>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Số điện thoại</label>
                      <input type="text" name="phonenum" class="form-control shadow-none" 
                             value="<?php echo htmlspecialchars($owner['phonenum'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Tên khách sạn</label>
                      <input type="text" name="hotel_name" class="form-control shadow-none" 
                             value="<?php echo htmlspecialchars($owner['hotel_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-12">
                      <label class="form-label">Địa chỉ</label>
                      <textarea name="address" class="form-control shadow-none" rows="2"><?php echo htmlspecialchars($owner['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                    <div class="col-md-12">
                      <label class="form-label">Trạng thái tài khoản</label>
                      <div>
                        <?php 
                        $status_class = $owner['status'] == 1 ? 'bg-success' : 'bg-warning';
                        $status_text = $owner['status'] == 1 ? 'Đã duyệt' : 'Chờ duyệt';
                        ?>
                        <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Cập nhật thông tin
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- Avatar and Change Password - Side by Side -->
        <div class="row g-4">
          <!-- Avatar Upload -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-body text-center">
                <h5 class="card-title mb-4">Ảnh đại diện</h5>
                <?php
                $profile_img = $owner['profile'] ?? 'user.png';
                $avatar_path = USERS_IMG_PATH . $profile_img;
                ?>
                <div class="mb-3">
                  <img src="<?php echo htmlspecialchars($avatar_path, ENT_QUOTES, 'UTF-8'); ?>" 
                       alt="Avatar" 
                       id="avatar-preview"
                       class="rounded-circle border border-3 border-primary"
                       style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;"
                       onclick="document.getElementById('avatar-input').click()">
                </div>
                <form method="POST" enctype="multipart/form-data" id="avatar-form">
                  <input type="file" 
                         name="avatar" 
                         id="avatar-input" 
                         accept=".jpg,.jpeg,.png,.webp" 
                         style="display: none;"
                         onchange="previewAvatar(this)">
                  <button type="submit" name="upload_avatar" class="btn btn-primary w-100">
                    <i class="bi bi-upload me-2"></i>Upload Avatar
                  </button>
                </form>
                <small class="text-muted d-block mt-2">Chấp nhận: JPG, PNG, WEBP (tối đa 2MB)</small>
              </div>
            </div>
          </div>

          <!-- Change Password -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title mb-4">Đổi mật khẩu</h5>
                <form method="POST">
                  <div class="mb-3">
                    <label class="form-label">Mật khẩu cũ</label>
                    <input type="password" name="old_pass" class="form-control shadow-none" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Mật khẩu mới</label>
                    <input type="password" name="new_pass" class="form-control shadow-none" required minlength="6">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" name="confirm_pass" class="form-control shadow-none" required minlength="6">
                  </div>
                  <button type="submit" name="change_password" class="btn btn-warning w-100">
                    <i class="bi bi-key me-2"></i>Đổi mật khẩu
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <?php require('../admin/inc/scripts.php'); ?>
  
  <!-- Modal nhập email mới -->
  <div class="modal fade" id="changeEmailModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">
            <i class="bi bi-envelope-fill me-2"></i>Đổi email
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="bi bi-info-circle-fill me-2"></i>
            <strong>Lưu ý:</strong> Để đổi email, bạn cần xác thực bằng mã OTP gửi đến email cũ của bạn.
          </div>
          
          <div class="mb-3">
            <label class="form-label fw-bold">Email hiện tại:</label>
            <input type="email" id="current-email-display-input" class="form-control shadow-none" 
                   value="<?php echo htmlspecialchars($owner['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly 
                   style="background-color: #f8f9fa;">
            <small class="text-muted">Mã OTP sẽ được gửi đến email này</small>
          </div>
          
          <div class="mb-3">
            <label class="form-label fw-bold">Email mới:</label>
            <input type="email" id="new-email-input" class="form-control shadow-none" 
                   placeholder="Nhập email mới của bạn" autocomplete="email">
            <small class="text-muted">Email mới phải khác email hiện tại</small>
            <div class="invalid-feedback" id="new-email-error"></div>
          </div>
          
          <div class="alert alert-warning mb-0">
            <i class="bi bi-shield-exclamation me-2"></i>
            <small>Sau khi nhập email mới, hệ thống sẽ gửi mã OTP đến email cũ của bạn để xác thực.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="button" class="btn btn-primary" id="btn-send-otp">
            <i class="bi bi-send-fill me-1"></i>Gửi mã OTP
          </button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Modal OTP đổi email -->
  <div class="modal fade" id="emailChangeOTPModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%); border: none;">
        <div class="modal-header border-0">
          <h5 class="modal-title text-white">
            <i class="bi bi-shield-check-fill me-2"></i>Xác thực đổi email
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-white">
          <div class="alert alert-success bg-success bg-opacity-25 border-success mb-4">
            <div class="d-flex align-items-center">
              <i class="bi bi-envelope-check-fill me-2" style="font-size: 1.5rem;"></i>
              <div>
                <strong class="d-block">Mã xác thực đã được gửi!</strong>
                <small class="d-block mt-1">Mã OTP đã được gửi đến email cũ của bạn:</small>
                <strong id="current-email-display" class="text-white d-block mt-1" style="font-size: 1.1rem;"></strong>
              </div>
            </div>
          </div>
          
          <div class="mb-3">
            <label class="form-label text-white">
              <i class="bi bi-key-fill me-2"></i>Mã xác thực (6 số)
            </label>
            <div class="d-flex gap-2 justify-content-center mb-2" id="email-change-otp-inputs">
              <?php for($i = 1; $i <= 6; $i++): ?>
                <input type="text" 
                       class="form-control text-center fw-bold otp-input" 
                       id="email-otp-<?php echo $i; ?>" 
                       maxlength="1" 
                       inputmode="numeric"
                       pattern="[0-9]"
                       autocomplete="off"
                       style="width: 50px; height: 60px; font-size: 24px; 
                              background: rgba(255,255,255,0.1); 
                              border: 2px solid rgba(255,255,255,0.2); 
                              color: #fff;
                              transition: all 0.3s ease;">
              <?php endfor; ?>
            </div>
            <input type="hidden" id="email-otp-full-code">
            <div class="error-message text-danger small mt-2" id="email-otp-error" style="display: none;"></div>
          </div>
          
          <div class="mb-3">
            <label class="form-label text-white-50 small">Email mới sẽ đổi thành:</label>
            <input type="email" id="email-change-new-email" class="form-control shadow-none" readonly style="background-color: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.3);">
          </div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-secondary flex-grow-1" data-bs-dismiss="modal">Hủy</button>
            <button type="button" class="btn btn-primary flex-grow-1" id="email-otp-submit-btn">
              <i class="bi bi-check-circle me-1"></i>Xác thực
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Xử lý đổi email
    const btnChangeEmail = document.getElementById('btn-change-email');
    const emailInput = document.getElementById('owner-email-input');
    const originalEmail = '<?php echo htmlspecialchars($owner['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>';
    
    // Mở modal nhập email mới
    if(btnChangeEmail) {
      btnChangeEmail.addEventListener('click', function() {
        // Reset form
        document.getElementById('new-email-input').value = '';
        document.getElementById('new-email-input').classList.remove('is-invalid');
        document.getElementById('new-email-error').textContent = '';
        document.getElementById('current-email-display-input').value = originalEmail;
        
        // Hiển thị modal
        const modal = new bootstrap.Modal(document.getElementById('changeEmailModal'));
        modal.show();
        
        // Focus vào ô email mới
        setTimeout(() => {
          document.getElementById('new-email-input').focus();
        }, 300);
      });
    }
    
    // Xử lý nút "Gửi mã OTP" trong modal
    const btnSendOTP = document.getElementById('btn-send-otp');
    const newEmailInput = document.getElementById('new-email-input');
    
    if(btnSendOTP && newEmailInput) {
      btnSendOTP.addEventListener('click', function() {
        const newEmail = newEmailInput.value.trim();
        const emailError = document.getElementById('new-email-error');
        
        // Reset error
        newEmailInput.classList.remove('is-invalid');
        emailError.textContent = '';
        
        // Validate
        if(!newEmail) {
          newEmailInput.classList.add('is-invalid');
          emailError.textContent = 'Vui lòng nhập email mới!';
          newEmailInput.focus();
          return;
        }

        if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(newEmail)) {
          newEmailInput.classList.add('is-invalid');
          emailError.textContent = 'Email không hợp lệ!';
          newEmailInput.focus();
          return;
        }

        if(newEmail === originalEmail) {
          newEmailInput.classList.add('is-invalid');
          emailError.textContent = 'Email mới phải khác email hiện tại!';
          newEmailInput.focus();
          return;
        }

        // Đóng modal nhập email
        const changeEmailModal = bootstrap.Modal.getInstance(document.getElementById('changeEmailModal'));
        changeEmailModal.hide();
        
        // Gửi OTP đến email cũ
        sendEmailChangeOTP(newEmail);
      });
      
      // Cho phép Enter để gửi
      newEmailInput.addEventListener('keypress', function(e) {
        if(e.key === 'Enter') {
          btnSendOTP.click();
        }
      });
    }

    // Gửi OTP đổi email
    function sendEmailChangeOTP(newEmail) {
      btnSendOTP.disabled = true;
      btnSendOTP.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang gửi...';

      const formData = new FormData();
      formData.append('send_email_change_otp', '');
      formData.append('new_email', newEmail);

      fetch('ajax/profile.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.text())
      .then(res => {
        console.log('Email change OTP response:', res);
        console.log('Response length:', res.length);
        console.log('Response trimmed:', res.trim());
        btnSendOTP.disabled = false;
        btnSendOTP.innerHTML = '<i class="bi bi-send-fill me-1"></i>Gửi mã OTP';

        if(res.trim() === 'otp_sent') {
          console.log('OTP sent successfully, showing modal...');
          // Cập nhật email mới vào modal OTP
          const emailNewInput = document.getElementById('email-change-new-email');
          const emailDisplay = document.getElementById('current-email-display');
          
          if(emailNewInput) emailNewInput.value = newEmail;
          if(emailDisplay) emailDisplay.textContent = originalEmail;
          
          // Hiển thị thông báo gửi OTP thành công
          showOwnerToast('success', '📧 Mã OTP đã được gửi đến email cũ của bạn!', 3000);
          
          // Đóng modal nhập email trước
          const changeEmailModalEl = document.getElementById('changeEmailModal');
          if(changeEmailModalEl) {
            const changeEmailModal = bootstrap.Modal.getInstance(changeEmailModalEl);
            if(changeEmailModal) {
              changeEmailModal.hide();
            }
          }
          
          // Đợi modal nhập email đóng xong rồi mới hiển thị modal OTP
          setTimeout(() => {
            const otpModalEl = document.getElementById('emailChangeOTPModal');
            if(!otpModalEl) {
              showOwnerToast('error', '❌ Lỗi: Không tìm thấy modal OTP. Vui lòng refresh trang!', 4000);
              console.error('Modal OTP element not found!');
              return;
            }
            
            // Tạo và hiển thị modal OTP
            const otpModal = new bootstrap.Modal(otpModalEl, {
              backdrop: 'static',
              keyboard: false
            });
            
            otpModal.show();
            
            // Đảm bảo các ô OTP được setup
            setupEmailChangeOTPInputs();
            
            // Focus vào ô OTP đầu tiên sau khi modal hiển thị hoàn toàn
            otpModalEl.addEventListener('shown.bs.modal', function() {
              const firstInput = document.getElementById('email-otp-1');
              if(firstInput) {
                firstInput.focus();
              }
            }, { once: true });
            
            // Nếu modal đã hiển thị rồi thì focus luôn
            setTimeout(() => {
              const firstInput = document.getElementById('email-otp-1');
              if(firstInput) {
                firstInput.focus();
              }
            }, 500);
          }, 400);
        } else if(res.trim() === 'invalid_email') {
          showOwnerToast('error', '❌ Email không hợp lệ! Vui lòng kiểm tra lại.', 3000);
        } else if(res.trim() === 'same_email') {
          showOwnerToast('warning', '⚠️ Email mới phải khác email hiện tại!', 3000);
        } else if(res.trim() === 'email_already') {
          showOwnerToast('error', '❌ Email này đã được sử dụng bởi tài khoản khác!', 4000);
        } else if(res.trim() === 'otp_send_failed') {
          showOwnerToast('error', '❌ Không thể gửi mã OTP. Vui lòng thử lại!', 4000);
        } else {
          showOwnerToast('error', '❌ Có lỗi xảy ra. Vui lòng thử lại!', 3000);
        }
      })
      .catch(err => {
        console.error('Email change OTP error:', err);
        btnSendOTP.disabled = false;
        btnSendOTP.innerHTML = '<i class="bi bi-send-fill me-1"></i>Gửi mã OTP';
        alert('Có lỗi xảy ra. Vui lòng thử lại!');
      });
    }

    // Setup OTP inputs cho đổi email
    function setupEmailChangeOTPInputs() {
      for(let i = 1; i <= 6; i++) {
        const input = document.getElementById(`email-otp-${i}`);
        if(input) {
          // Chỉ cho phép nhập số
          input.addEventListener('input', function(e) {
            // Chỉ giữ lại số
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            
            if(e.target.value.length === 1) {
              // Highlight ô hiện tại
              e.target.style.borderColor = 'rgba(255,255,255,0.4)';
              e.target.style.background = 'rgba(255,255,255,0.15)';
              
              if(i < 6) {
                const nextInput = document.getElementById(`email-otp-${i + 1}`);
                if(nextInput) nextInput.focus();
              }
            }
            updateEmailOTPCode();
          });
          
          input.addEventListener('keydown', function(e) {
            if(e.key === 'Backspace' && !e.target.value && i > 1) {
              // Reset style khi xóa
              e.target.style.borderColor = 'rgba(255,255,255,0.2)';
              e.target.style.background = 'rgba(255,255,255,0.1)';
              const prevInput = document.getElementById(`email-otp-${i - 1}`);
              if(prevInput) {
                prevInput.focus();
                prevInput.value = '';
              }
            }
            updateEmailOTPCode();
          });
          
          input.addEventListener('focus', function(e) {
            e.target.style.borderColor = 'rgba(255,255,255,0.5)';
            e.target.style.background = 'rgba(255,255,255,0.2)';
          });
          
          input.addEventListener('blur', function(e) {
            if(!e.target.value) {
              e.target.style.borderColor = 'rgba(255,255,255,0.2)';
              e.target.style.background = 'rgba(255,255,255,0.1)';
            } else {
              e.target.style.borderColor = 'rgba(255,255,255,0.4)';
              e.target.style.background = 'rgba(255,255,255,0.15)';
            }
          });
        }
      }
    }

    function updateEmailOTPCode() {
      let otpCode = '';
      for(let i = 1; i <= 6; i++) {
        const input = document.getElementById(`email-otp-${i}`);
        if (input) otpCode += input.value;
      }
      document.getElementById('email-otp-full-code').value = otpCode;
    }

    // Xác thực OTP đổi email
    const emailOTPSubmitBtn = document.getElementById('email-otp-submit-btn');
    if(emailOTPSubmitBtn) {
      emailOTPSubmitBtn.addEventListener('click', function() {
        const otpCode = document.getElementById('email-otp-full-code').value;
        const otpError = document.getElementById('email-otp-error');

        if(otpCode.length !== 6) {
          if(otpError) {
            otpError.textContent = 'Vui lòng nhập đầy đủ 6 số';
            otpError.style.display = 'block';
          }
          return;
        }

        if(otpError) {
          otpError.style.display = 'none';
        }

        emailOTPSubmitBtn.disabled = true;
        emailOTPSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang xác thực...';

        const formData = new FormData();
        formData.append('verify_email_change_otp', '');
        formData.append('otp', otpCode);

        fetch('ajax/profile.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.text())
        .then(res => {
          emailOTPSubmitBtn.disabled = false;
          emailOTPSubmitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Xác thực';

          if(res.trim() === 'email_changed') {
            // Email đã được đổi thành công
            const modal = bootstrap.Modal.getInstance(document.getElementById('emailChangeOTPModal'));
            if(modal) modal.hide();
            
            // Hiển thị thông báo thành công rõ ràng
            showOwnerToast('success', '✅ Đổi email thành công! Email của bạn đã được cập nhật.', 4000);
            
            // Reload trang để cập nhật email mới
            setTimeout(() => {
              location.reload();
            }, 2000);
          } else if(res.trim() === 'otp_verified') {
            // Fallback - nếu vẫn trả về otp_verified (code cũ)
            const modal = bootstrap.Modal.getInstance(document.getElementById('emailChangeOTPModal'));
            if(modal) modal.hide();
            showOwnerToast('success', '✅ Xác thực thành công! Email đã được cập nhật.', 4000);
            setTimeout(() => {
              location.reload();
            }, 2000);
          } else if(res.trim() === 'update_failed') {
            if(otpError) {
              otpError.textContent = '❌ Không thể cập nhật email. Vui lòng thử lại!';
              otpError.style.display = 'block';
            }
            showOwnerToast('error', '❌ Không thể cập nhật email. Vui lòng thử lại!', 4000);
          } else if(res.trim() === 'otp_invalid') {
            if(otpError) {
              otpError.textContent = '❌ Mã xác thực không đúng! Vui lòng nhập lại.';
              otpError.style.display = 'block';
            }
            showOwnerToast('error', '❌ Mã xác thực không đúng! Vui lòng kiểm tra lại.', 3000);
            // Xóa các ô OTP
            for(let i = 1; i <= 6; i++) {
              const input = document.getElementById(`email-otp-${i}`);
              if(input) input.value = '';
            }
            document.getElementById('email-otp-1').focus();
          } else if(res.trim() === 'otp_expired') {
            if(otpError) {
              otpError.textContent = '⏰ Mã xác thực đã hết hạn. Vui lòng gửi lại mã!';
              otpError.style.display = 'block';
            }
            showOwnerToast('warning', '⏰ Mã xác thực đã hết hạn. Vui lòng gửi lại mã!', 4000);
          } else if(res.trim() === 'otp_not_found') {
            if(otpError) {
              otpError.textContent = '⚠️ Không tìm thấy mã xác thực. Vui lòng gửi lại mã!';
              otpError.style.display = 'block';
            }
            showOwnerToast('warning', '⚠️ Không tìm thấy mã xác thực. Vui lòng gửi lại mã!', 4000);
          } else if(res.trim() === 'email_already') {
            if(otpError) {
              otpError.textContent = '❌ Email này đã được sử dụng bởi tài khoản khác!';
              otpError.style.display = 'block';
            }
            showOwnerToast('error', '❌ Email này đã được sử dụng. Vui lòng chọn email khác!', 4000);
          } else {
            console.error('Unexpected response:', res);
            if(otpError) {
              otpError.textContent = '❌ Có lỗi xảy ra. Vui lòng thử lại!';
              otpError.style.display = 'block';
            }
            showOwnerToast('error', '❌ Có lỗi xảy ra. Vui lòng thử lại!', 4000);
          }
        })
        .catch(err => {
          console.error('Verify OTP error:', err);
          emailOTPSubmitBtn.disabled = false;
          emailOTPSubmitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Xác thực';
          if(otpError) {
            otpError.textContent = 'Có lỗi xảy ra. Vui lòng thử lại!';
            otpError.style.display = 'block';
          }
        });
      });
    }

    // Khởi tạo OTP inputs khi modal được hiển thị
    const emailChangeModal = document.getElementById('emailChangeOTPModal');
    if(emailChangeModal) {
      emailChangeModal.addEventListener('shown.bs.modal', function() {
        setupEmailChangeOTPInputs();
        document.getElementById('email-otp-1').focus();
      });
    }
    
    // Hàm hiển thị toast notification cho owner
    function showOwnerToast(type, message, duration = 3000) {
      // Xóa toast cũ nếu có
      const existingToasts = document.querySelectorAll('.owner-toast');
      existingToasts.forEach(toast => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 100);
      });
      
      // Tạo toast mới
      const toast = document.createElement('div');
      toast.className = 'owner-toast';
      
      // Màu sắc theo type
      const colors = {
        success: { bg: '#28a745', icon: 'bi-check-circle-fill' },
        error: { bg: '#dc3545', icon: 'bi-x-circle-fill' },
        warning: { bg: '#ffc107', icon: 'bi-exclamation-triangle-fill' },
        info: { bg: '#17a2b8', icon: 'bi-info-circle-fill' }
      };
      
      const color = colors[type] || colors.info;
      
      toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${color.bg};
        color: white;
        padding: 16px 24px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        z-index: 10000;
        min-width: 350px;
        max-width: 500px;
        font-size: 15px;
        font-weight: 500;
        opacity: 0;
        transform: translateX(400px);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 12px;
      `;
      
      toast.innerHTML = `
        <i class="bi ${color.icon}" style="font-size: 20px;"></i>
        <span style="flex: 1;">${message}</span>
        <button type="button" class="btn-close btn-close-white" onclick="this.parentElement.remove()" style="opacity: 0.8;"></button>
      `;
      
      document.body.appendChild(toast);
      
      // Hiển thị với animation
      setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
      }, 50);
      
      // Tự động ẩn sau duration
      setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(400px)';
        setTimeout(() => toast.remove(), 300);
      }, duration);
    }
  </script>
</body>
</html>

