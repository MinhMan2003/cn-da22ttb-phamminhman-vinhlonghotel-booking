<!DOCTYPE html>
<?php
  // Mặc định là tiếng Việt, chỉ chuyển sang tiếng Anh khi có cookie 'lang' = 'en'
  $lang_from_url = isset($_GET['_lang']) ? trim($_GET['_lang']) : '';
  if ($lang_from_url === 'en' || $lang_from_url === 'vi') {
    setcookie('lang', $lang_from_url, time() + (365 * 24 * 60 * 60), '/', '', false, true);
    $_COOKIE['lang'] = $lang_from_url;
    $current_lang = $lang_from_url;
  } else {
    $lang_cookie = isset($_COOKIE['lang']) ? trim($_COOKIE['lang']) : '';
    $current_lang = ($lang_cookie === 'en') ? 'en' : 'vi';
  }
  if ($current_lang !== 'en' && $current_lang !== 'vi') {
    $current_lang = 'vi';
  }
  
  // Hàm dịch cho trang profile
  function t_profile($key, $lang = 'vi') {
    $translations = [
      'vi' => [
        'profile.pageTitle' => 'Hồ sơ cá nhân',
        'profile.home' => 'Trang chủ',
        'profile.profile' => 'Hồ sơ cá nhân',
        'profile.myInfo' => 'Thông tin của tôi',
        'profile.basicInfo' => 'Thông tin cơ bản',
        'profile.name' => 'Tên',
        'profile.email' => 'Email',
        'profile.changeEmail' => 'Đổi email',
        'profile.password' => 'Mật khẩu',
        'profile.forgotPassword' => 'Quên mật khẩu?',
        'profile.forgotPasswordDesc' => 'Nhấn để nhận mã OTP đặt lại mật khẩu',
        'profile.phone' => 'Số điện thoại',
        'profile.dob' => 'Ngày tháng năm sinh',
        'profile.idNumber' => 'Mã định danh',
        'profile.gender' => 'Giới tính',
        'profile.selectGender' => '-- Chọn giới tính --',
        'profile.male' => 'Nam',
        'profile.female' => 'Nữ',
        'profile.address' => 'Địa chỉ',
        'profile.saveChanges' => 'Lưu thay đổi',
        'profile.avatar' => 'Ảnh đại diện',
        'profile.selectNewImage' => 'Chọn ảnh mới',
        'profile.imageSupport' => 'Hỗ trợ: JPG, PNG, WEBP',
        'profile.changePassword' => 'Đổi mật khẩu',
        'profile.oldPassword' => 'Mật khẩu cũ',
        'profile.newPassword' => 'Mật khẩu mới',
        'profile.confirmPassword' => 'Xác nhận mật khẩu mới',
        'profile.enterCurrentPassword' => 'Nhập mật khẩu hiện tại',
        'profile.enterNewPassword' => 'Nhập mật khẩu mới',
        'profile.reEnterNewPassword' => 'Nhập lại mật khẩu mới',
        'profile.currentPasswordDesc' => 'Vui lòng nhập mật khẩu hiện tại để xác thực',
        'profile.minPasswordLength' => 'Tối thiểu 6 ký tự',
        'profile.passwordMatch' => 'Phải khớp với mật khẩu mới',
        'profile.cancel' => 'Hủy',
        'profile.favorites' => 'Yêu thích',
        'profile.noFavorites' => 'Chưa có phòng yêu thích nào',
        'profile.addToFavorites' => 'Thêm vào yêu thích',
        'profile.list' => 'Danh sách',
        'profile.details' => 'Chi tiết',
        'profile.bookNow' => 'Đặt ngay',
        'profile.adults' => 'người lớn',
        'profile.children' => 'trẻ em',
        'profile.night' => 'đêm',
        'profile.removeFavorite' => 'Bỏ yêu thích',
      ],
      'en' => [
        'profile.pageTitle' => 'Profile',
        'profile.home' => 'Home',
        'profile.profile' => 'Profile',
        'profile.myInfo' => 'My Information',
        'profile.basicInfo' => 'Basic Information',
        'profile.name' => 'Name',
        'profile.email' => 'Email',
        'profile.changeEmail' => 'Change Email',
        'profile.password' => 'Password',
        'profile.forgotPassword' => 'Forgot Password?',
        'profile.forgotPasswordDesc' => 'Click to receive OTP code to reset password',
        'profile.phone' => 'Phone Number',
        'profile.dob' => 'Date of Birth',
        'profile.idNumber' => 'ID Number',
        'profile.gender' => 'Gender',
        'profile.selectGender' => '-- Select Gender --',
        'profile.male' => 'Male',
        'profile.female' => 'Female',
        'profile.address' => 'Address',
        'profile.saveChanges' => 'Save Changes',
        'profile.avatar' => 'Avatar',
        'profile.selectNewImage' => 'Select New Image',
        'profile.imageSupport' => 'Supported: JPG, PNG, WEBP',
        'profile.changePassword' => 'Change Password',
        'profile.oldPassword' => 'Old Password',
        'profile.newPassword' => 'New Password',
        'profile.confirmPassword' => 'Confirm New Password',
        'profile.enterCurrentPassword' => 'Enter current password',
        'profile.enterNewPassword' => 'Enter new password',
        'profile.reEnterNewPassword' => 'Re-enter new password',
        'profile.currentPasswordDesc' => 'Please enter your current password to verify',
        'profile.minPasswordLength' => 'Minimum 6 characters',
        'profile.passwordMatch' => 'Must match new password',
        'profile.cancel' => 'Cancel',
        'profile.favorites' => 'Favorites',
        'profile.noFavorites' => 'No favorite rooms yet',
        'profile.addToFavorites' => 'Add to Favorites',
      ]
    ];
    return $translations[$lang][$key] ?? $translations['vi'][$key] ?? $key;
  }
?>
<html lang="<?php echo $current_lang; ?>">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - <?php echo t_profile('profile.pageTitle', $current_lang); ?></title>
  
  <style>
    /* Modern Profile Page */
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
    }
    
    /* Header Section */
    .profile-header {
      margin-bottom: 3rem;
    }
    
    .profile-header h2 {
      font-size: 2.5rem;
      font-weight: 800;
      color: #1f2937;
      margin-bottom: 0.5rem;
    }
    
    .breadcrumb-modern {
      font-size: 14px;
    }
    
    .breadcrumb-modern a {
      color: #6b7280;
      text-decoration: none;
      transition: color 0.3s;
    }
    
    .breadcrumb-modern a:hover {
      color: #1f2937;
    }
    
    /* Profile Cards */
    .profile-card {
      background: #ffffff;
      border-radius: 24px;
      padding: 2rem;
      box-shadow: 0 10px 40px rgba(0,0,0,0.08);
      border: 1px solid rgba(229,231,235,0.5);
      transition: all 0.3s ease;
      margin-bottom: 2rem;
    }
    
    .profile-card:hover {
      box-shadow: 0 16px 50px rgba(0,0,0,0.12);
      transform: translateY(-2px);
    }
    
    .profile-card h5 {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 10px;
      padding-bottom: 1rem;
      border-bottom: 2px solid #e5e7eb;
    }
    
    .profile-card h5::before {
      content: '';
      width: 4px;
      height: 24px;
      background: #1f2937;
      border-radius: 2px;
    }
    
    /* Form Inputs */
    .form-label {
      font-weight: 600;
      color: #374151;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    
    .form-label i {
      color: #1f2937;
      font-size: 16px;
    }
    
    .form-control {
      border: 2px solid #e5e7eb;
      border-radius: 12px;
      padding: 12px 16px;
      transition: all 0.3s ease;
      font-size: 15px;
    }
    
    .form-control:focus {
      border-color: #1f2937;
      box-shadow: 0 0 0 4px rgba(31,41,55,0.1);
      outline: none;
    }
    
    /* Avatar Section */
    .avatar-wrapper {
      position: relative;
      display: inline-block;
      margin-bottom: 1.5rem;
    }
    
    .avatar-preview {
      width: 160px;
      height: 160px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #ffffff;
      box-shadow: 0 8px 24px rgba(0,0,0,0.12);
      transition: all 0.3s ease;
    }
    
    .avatar-wrapper:hover .avatar-preview {
      transform: scale(1.05);
      box-shadow: 0 12px 32px rgba(0,0,0,0.16);
    }
    
    .avatar-overlay {
      position: absolute;
      inset: 0;
      border-radius: 50%;
      background: rgba(31,41,55,0.7);
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: opacity 0.3s ease;
      cursor: pointer;
    }
    
    .avatar-wrapper:hover .avatar-overlay {
      opacity: 1;
    }
    
    .avatar-overlay i {
      color: #ffffff;
      font-size: 2rem;
    }
    
    .file-input-wrapper {
      position: relative;
      overflow: hidden;
      display: inline-block;
      width: 100%;
    }
    
    .file-input-wrapper input[type=file] {
      position: absolute;
      left: -9999px;
    }
    
    .file-input-label {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 12px 20px;
      background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
      color: #ffffff;
      border-radius: 12px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-weight: 600;
    }
    
    .file-input-label:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(31,41,55,0.3);
    }
    
    /* Submit Buttons */
    .btn-submit {
      background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
      border: none;
      border-radius: 12px;
      padding: 12px 24px;
      font-weight: 600;
      color: #ffffff;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(31,41,55,0.2);
    }
    
    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(31,41,55,0.3);
      background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
      color: #ffffff;
    }
    
    /* Favorite Cards */
    .fav-card {
      border-radius: 20px;
      overflow: hidden;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      border: 1px solid #e5e7eb;
    }
    
    .fav-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 16px 40px rgba(0,0,0,0.12);
    }
    
    .fav-card .card-img-top {
      transition: transform 0.4s ease;
    }
    
    .fav-card:hover .card-img-top {
      transform: scale(1.1);
    }
    
    .remove-fav {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: rgba(255,255,255,0.95);
      backdrop-filter: blur(10px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      transition: all 0.3s ease;
      border: none !important;
    }
    
    .remove-fav:hover {
      transform: scale(1.1);
      background: #ffffff;
      box-shadow: 0 6px 16px rgba(239,68,68,0.3);
    }
    
    .fav-card .btn {
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .fav-card .btn-outline-dark:hover {
      background: #1f2937;
      border-color: #1f2937;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(31,41,55,0.2);
    }
    
    .fav-card .btn-primary {
      background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
      border: none;
    }
    
    .fav-card .btn-primary:hover {
      background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(31,41,55,0.3);
    }
    
    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 3rem 1rem;
      color: #9ca3af;
    }
    
    .empty-state i {
      font-size: 4rem;
      margin-bottom: 1rem;
      opacity: 0.5;
    }
    
    /* Section Title */
    .section-title {
      font-size: 1.25rem;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 1.5rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .profile-card {
        padding: 1.5rem;
      }
      
      .avatar-preview {
        width: 120px;
        height: 120px;
      }
    }
  </style>
</head>
<body class="bg-light">

  <?php 
    require('inc/header.php'); 
    if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
      redirect('index.php');
    }
    $u_exist = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1",[$_SESSION['uId']],'s');
    if(mysqli_num_rows($u_exist)==0){
      redirect('index.php');
    }
    $u_fetch = mysqli_fetch_assoc($u_exist);
  ?>

  <div class="container">
    <div class="row">

      <div class="col-12 my-5 px-4 profile-header">
        <h2 class="fw-bold" data-i18n="profile.myInfo" data-i18n-skip><?php echo t_profile('profile.myInfo', $current_lang); ?></h2>
        <div class="breadcrumb-modern">
          <a href="index.php"><i class="bi bi-house-door me-1"></i><span data-i18n="profile.home" data-i18n-skip><?php echo t_profile('profile.home', $current_lang); ?></span></a>
          <span class="text-secondary mx-2">/</span>
          <span class="text-dark fw-semibold" data-i18n="profile.profile" data-i18n-skip><?php echo t_profile('profile.profile', $current_lang); ?></span>
        </div>
      </div>
      
      <div class="col-12 mb-5 px-4">
        <div class="profile-card">
          <form id="info-form">
            <h5 class="mb-3 fw-bold">
              <i class="bi bi-person-badge-fill"></i>
              <span data-i18n="profile.basicInfo" data-i18n-skip><?php echo t_profile('profile.basicInfo', $current_lang); ?></span>
            </h5>
            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label">
                  <i class="bi bi-person-fill"></i>
                  <span data-i18n="profile.name" data-i18n-skip><?php echo t_profile('profile.name', $current_lang); ?></span>
                </label>
                <input name="name" type="text" value="<?php echo htmlspecialchars($u_fetch['name'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control shadow-none" required>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">
                  <i class="bi bi-envelope-fill"></i>
                  <span data-i18n="profile.email" data-i18n-skip><?php echo t_profile('profile.email', $current_lang); ?></span>
                </label>
                <div class="d-flex gap-2">
                  <input name="email" id="email-input" type="email" value="<?php echo htmlspecialchars($u_fetch['email'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control shadow-none" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                  <button type="button" class="btn btn-outline-primary btn-sm" id="btn-change-email" style="white-space: nowrap;">
                    <i class="bi bi-pencil-fill me-1"></i><span data-i18n="profile.changeEmail" data-i18n-skip><?php echo t_profile('profile.changeEmail', $current_lang); ?></span>
                  </button>
                </div>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">
                  <i class="bi bi-shield-lock-fill"></i>
                  <span data-i18n="profile.password" data-i18n-skip><?php echo t_profile('profile.password', $current_lang); ?></span>
                </label>
                <button type="button" class="btn btn-outline-danger w-100" id="btn-forgot-password">
                  <i class="bi bi-question-circle-fill me-2"></i><span data-i18n="profile.forgotPassword" data-i18n-skip><?php echo t_profile('profile.forgotPassword', $current_lang); ?></span>
                </button>
                <small class="text-muted d-block mt-1" style="font-size: 12px;">
                  <i class="bi bi-info-circle me-1"></i><span data-i18n="profile.forgotPasswordDesc" data-i18n-skip><?php echo t_profile('profile.forgotPasswordDesc', $current_lang); ?></span>
                </small>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">
                  <i class="bi bi-telephone-fill"></i>
                  <span data-i18n="profile.phone" data-i18n-skip><?php echo t_profile('profile.phone', $current_lang); ?></span>
                </label>
                <input name="phonenum" type="tel" value="<?php echo htmlspecialchars($u_fetch['phonenum'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control shadow-none" required>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">
                  <i class="bi bi-calendar-event-fill"></i>
                  <span data-i18n="profile.dob" data-i18n-skip><?php echo t_profile('profile.dob', $current_lang); ?></span>
                </label>
                <input name="dob" type="date" value="<?php echo htmlspecialchars($u_fetch['dob'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control shadow-none" required>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">
                  <i class="bi bi-shield-check-fill"></i>
                  <span data-i18n="profile.idNumber" data-i18n-skip><?php echo t_profile('profile.idNumber', $current_lang); ?></span>
                </label>
                <input name="pincode" type="text" value="<?php echo htmlspecialchars($u_fetch['pincode'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control shadow-none" required>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">
                  <i class="bi bi-gender-ambiguous"></i>
                  <span data-i18n="profile.gender" data-i18n-skip><?php echo t_profile('profile.gender', $current_lang); ?></span>
                </label>
                <select name="gender" class="form-control shadow-none" required>
                  <option value=""><span data-i18n="profile.selectGender" data-i18n-skip><?php echo t_profile('profile.selectGender', $current_lang); ?></span></option>
                  <option value="male" <?php echo (isset($u_fetch['gender']) && $u_fetch['gender'] == 'male') ? 'selected' : ''; ?> data-i18n="profile.male" data-i18n-skip><?php echo t_profile('profile.male', $current_lang); ?></option>
                  <option value="female" <?php echo (isset($u_fetch['gender']) && $u_fetch['gender'] == 'female') ? 'selected' : ''; ?> data-i18n="profile.female" data-i18n-skip><?php echo t_profile('profile.female', $current_lang); ?></option>
                </select>
              </div>
              <div class="col-md-8 mb-4">
                <label class="form-label">
                  <i class="bi bi-geo-alt-fill"></i>
                  <span data-i18n="profile.address" data-i18n-skip><?php echo t_profile('profile.address', $current_lang); ?></span>
                </label>
                <textarea name="address" class="form-control shadow-none" rows="2" required><?php echo htmlspecialchars($u_fetch['address'], ENT_QUOTES, 'UTF-8'); ?></textarea>
              </div>
            </div>
            <button type="submit" class="btn btn-submit">
              <i class="bi bi-check-circle-fill me-2"></i><span data-i18n="profile.saveChanges" data-i18n-skip><?php echo t_profile('profile.saveChanges', $current_lang); ?></span>
            </button>
          </form>
        </div>
      </div>

      <div class="col-md-4 mb-5 px-4">
        <div class="profile-card">
          <form id="profile-form">
            <h5 class="mb-3 fw-bold">
              <i class="bi bi-image-fill"></i>
              <span data-i18n="profile.avatar" data-i18n-skip><?php echo t_profile('profile.avatar', $current_lang); ?></span>
            </h5>
            <?php
              $pic = $u_fetch['profile'];
              $avatar = filter_var($pic, FILTER_VALIDATE_URL) ? $pic : USERS_IMG_PATH.$pic;
            ?>
            <div class="d-flex justify-content-center">
              <div class="avatar-wrapper">
                <img src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>" class="avatar-preview" id="avatar-preview" alt="Avatar">
                <div class="avatar-overlay">
                  <i class="bi bi-camera-fill"></i>
                </div>
              </div>
            </div>
            
            <div class="file-input-wrapper mb-3">
              <label for="profile-input" class="file-input-label">
                <i class="bi bi-upload"></i>
                <span data-i18n="profile.selectNewImage" data-i18n-skip><?php echo t_profile('profile.selectNewImage', $current_lang); ?></span>
              </label>
              <input name="profile" id="profile-input" type="file" accept=".jpg, .jpeg, .png, .webp" required>
            </div>
            
            <div class="text-center">
              <small class="text-muted d-block mb-3">
                <i class="bi bi-info-circle me-1"></i>
                <span data-i18n="profile.imageSupport" data-i18n-skip><?php echo t_profile('profile.imageSupport', $current_lang); ?></span>
              </small>
              <button type="submit" class="btn btn-submit w-100">
                <i class="bi bi-check-circle-fill me-2"></i>Lưu thay đổi
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="col-md-8 mb-5 px-4">
        <div class="profile-card">
          <form id="pass-form">
            <h5 class="mb-3 fw-bold">
              <i class="bi bi-shield-lock-fill"></i>
              <span data-i18n="profile.changePassword" data-i18n-skip><?php echo t_profile('profile.changePassword', $current_lang); ?></span>
            </h5>
            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label">
                  <i class="bi bi-lock-fill"></i>
                  <span data-i18n="profile.oldPassword" data-i18n-skip><?php echo t_profile('profile.oldPassword', $current_lang); ?></span>
                </label>
                <div class="position-relative">
                  <input name="old_pass" id="old_pass" type="password" class="form-control shadow-none" required data-i18n-placeholder="profile.enterCurrentPassword" placeholder="<?php echo t_profile('profile.enterCurrentPassword', $current_lang); ?>" style="padding-right: 45px;">
                  <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y p-0 me-2" onclick="togglePassword('old_pass', this)" style="text-decoration: none; border: none; background: none; color: #6c757d; z-index: 10;">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
                <small class="text-muted d-block mt-1" style="font-size: 12px;">
                  <i class="bi bi-info-circle me-1"></i><span data-i18n="profile.currentPasswordDesc" data-i18n-skip><?php echo t_profile('profile.currentPasswordDesc', $current_lang); ?></span>
                </small>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">
                  <i class="bi bi-key-fill"></i>
                  <span data-i18n="profile.newPassword" data-i18n-skip><?php echo t_profile('profile.newPassword', $current_lang); ?></span>
                </label>
                <div class="position-relative">
                  <input name="new_pass" id="new_pass" type="password" class="form-control shadow-none" required data-i18n-placeholder="profile.enterNewPassword" placeholder="<?php echo t_profile('profile.enterNewPassword', $current_lang); ?>" style="padding-right: 45px;">
                  <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y p-0 me-2" onclick="togglePassword('new_pass', this)" style="text-decoration: none; border: none; background: none; color: #6c757d; z-index: 10;">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
                <small class="text-muted d-block mt-1" style="font-size: 12px;">
                  <i class="bi bi-info-circle me-1"></i><span data-i18n="profile.minPasswordLength" data-i18n-skip><?php echo t_profile('profile.minPasswordLength', $current_lang); ?></span>
                </small>
              </div>
              <div class="col-md-4 mb-4">
                <label class="form-label">
                  <i class="bi bi-key-fill"></i>
                  <span data-i18n="profile.confirmPassword" data-i18n-skip><?php echo t_profile('profile.confirmPassword', $current_lang); ?></span>
                </label>
                <div class="position-relative">
                  <input name="confirm_pass" id="confirm_pass" type="password" class="form-control shadow-none" required data-i18n-placeholder="profile.reEnterNewPassword" placeholder="<?php echo t_profile('profile.reEnterNewPassword', $current_lang); ?>" style="padding-right: 45px;">
                  <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y p-0 me-2" onclick="togglePassword('confirm_pass', this)" style="text-decoration: none; border: none; background: none; color: #6c757d; z-index: 10;">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
                <small class="text-muted d-block mt-1" style="font-size: 12px;">
                  <i class="bi bi-info-circle me-1"></i><span data-i18n="profile.passwordMatch" data-i18n-skip><?php echo t_profile('profile.passwordMatch', $current_lang); ?></span>
                </small>
              </div>
            </div>
            <button type="submit" class="btn btn-submit">
              <i class="bi bi-check-circle-fill me-2"></i><span data-i18n="profile.saveChanges" data-i18n-skip><?php echo t_profile('profile.saveChanges', $current_lang); ?></span>
            </button>
          </form>
        </div>
      </div>

      <!-- Phòng yêu thích -->
      <div class="col-12 mb-5 px-4">
        <div class="profile-card">
          <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <div>
              <p class="text-uppercase text-muted small mb-1" data-i18n="profile.list"><?php echo t_profile('profile.list', $current_lang); ?></p>
              <h5 class="fw-bold mb-0">
                <i class="bi bi-heart-fill text-danger me-2"></i><span data-i18n="profile.favorites" data-i18n-skip><?php echo t_profile('profile.favorites', $current_lang); ?></span>
              </h5>
            </div>
          </div>
          <div class="row g-4" id="fav-list">
            <?php
              if(function_exists('ensureFavoritesTable')){ ensureFavoritesTable(); }
              $fav_q = select("SELECT r.* FROM favorites f INNER JOIN rooms r ON f.room_id = r.id WHERE f.user_id=? AND r.removed=0", [$_SESSION['uId']], 'i');
              if($fav_q && mysqli_num_rows($fav_q)){
                while($room = mysqli_fetch_assoc($fav_q)){
                  $thumb = ROOMS_IMG_PATH."thumbnail.jpg";
                  $thumb_q = mysqli_query($con,"SELECT image FROM room_images WHERE room_id='{$room['id']}' AND thumb='1' LIMIT 1");
                  if($thumb_q && mysqli_num_rows($thumb_q)){
                    $trow = mysqli_fetch_assoc($thumb_q);
                    $thumb = ROOMS_IMG_PATH.$trow['image'];
                  }
                  $price_txt = isset($room['price']) ? number_format((int)$room['price'],0,',','.') : '0';
                  $location = isset($room['location']) ? $room['location'] : (isset($room['area']) ? $room['area'] : 'Vĩnh Long');
                  $area = !empty($room['area']) ? $room['area'].' m²' : '';
                  $room_name = htmlspecialchars($room['name'], ENT_QUOTES, 'UTF-8');
                  $remove_fav_title = htmlspecialchars(t_profile('profile.removeFavorite', $current_lang), ENT_QUOTES, 'UTF-8');
                  $night_text = t_profile('profile.night', $current_lang);
                  $details_text = t_profile('profile.details', $current_lang);
                  $book_now_text = t_profile('profile.bookNow', $current_lang);
                  $adult_count = (int)$room['adult'];
                  $children_count = (int)$room['children'];
                  echo <<<FAV
                  <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 fav-card" data-room="{$room['id']}">
                      <div class="position-relative">
                        <img src="$thumb" class="card-img-top" alt="$room_name" style="height:180px;object-fit:cover;">
                        <button class="btn btn-sm btn-light position-absolute top-0 end-0 m-2 border remove-fav" data-room="{$room['id']}" title="{$remove_fav_title}">
                          <i class="bi bi-heart-fill text-danger"></i>
                        </button>
                      </div>
                      <div class="card-body d-flex flex-column">
                        <h6 class="fw-bold mb-1">$room_name</h6>
                        <div class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i>$location</div>
                        <div class="fw-semibold text-primary mb-2">$price_txt VND / <span data-i18n="profile.night">{$night_text}</span></div>
                        <div class="small text-muted mb-2"><i class="bi bi-people me-1"></i>{$adult_count} <span data-i18n="profile.adults">người lớn</span> • {$children_count} <span data-i18n="profile.children">trẻ em</span></div>
                        <div class="small text-muted mb-3">$area</div>
                        <div class="mt-auto d-flex gap-2">
                          <a href="room_details.php?id={$room['id']}" class="btn btn-outline-dark btn-sm flex-grow-1" data-i18n="profile.details">{$details_text}</a>
                          <button class="btn btn-primary btn-sm flex-grow-1 book-btn" data-room="{$room['id']}" type="button" data-i18n="profile.bookNow">{$book_now_text}</button>
                        </div>
                      </div>
                    </div>
                  </div>
FAV;
                }
              } else {
                $noFavoritesText = t_profile('profile.noFavorites', $current_lang);
                echo '<div class="col-12 empty-state">
                  <i class="bi bi-heart"></i>
                  <p class="mb-0" data-i18n="profile.noFavorites" data-i18n-skip>' . htmlspecialchars($noFavoritesText, ENT_QUOTES, 'UTF-8') . '</p>
                </div>';
              }
            ?>
          </div>
        </div>
      </div>


    </div>
  </div>


  <?php require('inc/footer.php'); ?>
  <?php require('inc/modals.php'); ?>

  <!-- Modal OTP đổi email -->
  <div class="modal fade" id="emailChangeOTPModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="background: linear-gradient(135deg, #1f2937 0%, #374151 100%); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px;">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title text-white fw-bold">
            <i class="bi bi-shield-check-fill me-2"></i>Xác thực đổi email
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pt-3">
          <p class="text-white-50 mb-3">Mã xác thực đã được gửi đến email mới của bạn. Vui lòng nhập mã để hoàn tất đổi email.</p>
          
          <div class="mb-3">
            <label class="form-label text-white">
              <i class="bi bi-envelope-fill me-2"></i>Email mới
            </label>
            <input type="email" id="email-change-new-email" class="form-control shadow-none" readonly style="background-color: rgba(255,255,255,0.1); color: #fff;">
          </div>

          <div class="mb-3">
            <label class="form-label text-white">
              <i class="bi bi-key-fill me-2"></i>Mã xác thực (6 số)
            </label>
            <div class="d-flex gap-2 justify-content-center mb-2" id="email-change-otp-inputs">
              <?php for($i=1; $i<=6; $i++): ?>
                <input type="text" class="form-control text-center fw-bold" id="email-otp-<?php echo $i; ?>" maxlength="1" style="width: 50px; height: 60px; font-size: 24px; background: rgba(255,255,255,0.1); border: 2px solid rgba(255,255,255,0.2); color: #fff;">
              <?php endfor; ?>
            </div>
            <input type="hidden" id="email-otp-full-code">
            <div class="error-message text-danger small mt-2" id="email-otp-error" style="display: none;"></div>
          </div>

          <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary flex-grow-1" id="email-otp-submit-btn">
              <span class="btn-text">Xác thực</span>
              <span class="btn-loading d-none">
                <span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...
              </span>
            </button>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Avatar Preview
    const profileInput = document.getElementById('profile-input');
    const avatarPreview = document.getElementById('avatar-preview');
    const fileInputLabel = document.querySelector('.file-input-label');
    
    if(profileInput && avatarPreview) {
      profileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if(file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            avatarPreview.src = e.target.result;
            avatarPreview.style.opacity = '0';
            setTimeout(() => {
              avatarPreview.style.transition = 'opacity 0.3s ease';
              avatarPreview.style.opacity = '1';
            }, 10);
          };
          reader.readAsDataURL(file);
          
          // Update label text
          const labelSpan = fileInputLabel.querySelector('span');
          if(labelSpan) {
            labelSpan.textContent = file.name.length > 20 ? file.name.substring(0, 20) + '...' : file.name;
          }
        }
      });
      
      // Click avatar to trigger file input
      avatarPreview.addEventListener('click', function() {
        profileInput.click();
      });
    }

    // Xử lý đổi email
    const btnChangeEmail = document.getElementById('btn-change-email');
    const emailInput = document.getElementById('email-input');
    let originalEmail = emailInput.value;

    if(btnChangeEmail && emailInput) {
      btnChangeEmail.addEventListener('click', function() {
        // Cho phép chỉnh sửa email
        emailInput.readOnly = false;
        emailInput.style.backgroundColor = '#fff';
        emailInput.style.cursor = 'text';
        emailInput.focus();
        btnChangeEmail.innerHTML = '<i class="bi bi-send-fill me-1"></i>Gửi mã OTP';
        btnChangeEmail.classList.remove('btn-outline-primary');
        btnChangeEmail.classList.add('btn-primary');
        
        // Lưu email ban đầu
        originalEmail = emailInput.value;
      });

      // Xử lý khi nhấn Enter hoặc blur
      emailInput.addEventListener('blur', function() {
        if(this.value !== originalEmail && this.value.trim() !== '') {
          sendEmailChangeOTP();
        }
      });

      emailInput.addEventListener('keypress', function(e) {
        if(e.key === 'Enter') {
          e.preventDefault();
          if(this.value !== originalEmail && this.value.trim() !== '') {
            sendEmailChangeOTP();
          }
        }
      });
    }

    // Gửi OTP đổi email
    function sendEmailChangeOTP() {
      const newEmail = emailInput.value.trim();
      
      if(!newEmail) {
        const msg = window.i18n ? window.i18n.translate('profile.pleaseEnterNewEmail') : 'Vui lòng nhập email mới!';
        if(typeof showToast === 'function') {
          showToast('error', msg, 3000);
        } else {
          alert(msg);
        }
        return;
      }

      if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(newEmail)) {
        const msg = window.i18n ? window.i18n.translate('profile.invalidEmail') : 'Email không hợp lệ!';
        if(typeof showToast === 'function') {
          showToast('error', msg, 3000);
        } else {
          alert(msg);
        }
        return;
      }

      if(newEmail === originalEmail) {
        const msg = window.i18n ? window.i18n.translate('profile.newEmailMustDifferent') : 'Email mới phải khác email hiện tại!';
        if(typeof showToast === 'function') {
          showToast('warning', msg, 3000);
        } else {
          alert(msg);
        }
        return;
      }

      btnChangeEmail.disabled = true;
      const sendingText = window.i18n ? window.i18n.translate('profile.sending') : 'Đang gửi...';
      btnChangeEmail.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>' + sendingText;

      const formData = new FormData();
      formData.append('send_email_change_otp', '');
      formData.append('new_email', newEmail);

      fetch('ajax/profile.php', {
        method: 'POST',
        body: formData
      })
      .then(res => {
        if(!res.ok) {
          throw new Error('Network response was not ok: ' + res.status);
        }
        return res.text();
      })
      .then(res => {
        console.log('Email change OTP response:', res);
        btnChangeEmail.disabled = false;
        
        // Trim response để tránh whitespace
        res = res.trim();
        
        if(res === 'otp_sent') {
          if(typeof showToast === 'function') {
            const msg = window.i18n ? window.i18n.translate('profile.otpSent') : 'Mã xác thực đã được gửi đến email mới của bạn!';
            showToast('success', msg, 3000);
          }
          // Hiển thị modal OTP
          document.getElementById('email-change-new-email').value = newEmail;
          const modal = new bootstrap.Modal(document.getElementById('emailChangeOTPModal'));
          modal.show();
          // Focus vào ô OTP đầu tiên
          setTimeout(() => {
            document.getElementById('email-otp-1').focus();
          }, 300);
        } else if(res === 'email_already') {
          const msg = window.i18n ? window.i18n.translate('profile.emailAlreadyUsed') : 'Email này đã được sử dụng!';
          if(typeof showToast === 'function') {
            showToast('error', msg, 3000);
          } else {
            alert(msg);
          }
          emailInput.value = originalEmail;
        } else if(res === 'same_email') {
          const msg = window.i18n ? window.i18n.translate('profile.newEmailMustDifferent') : 'Email mới phải khác email hiện tại!';
          if(typeof showToast === 'function') {
            showToast('warning', msg, 3000);
          } else {
            alert(msg);
          }
          emailInput.value = originalEmail;
        } else if(res === 'invalid_email') {
          const msg = window.i18n ? window.i18n.translate('profile.invalidEmail') : 'Email không hợp lệ!';
          if(typeof showToast === 'function') {
            showToast('error', msg, 3000);
          } else {
            alert(msg);
          }
          emailInput.value = originalEmail;
        } else if(res === 'session_error') {
          const msg = window.i18n ? window.i18n.translate('profile.sessionExpired') : 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại!';
          if(typeof showToast === 'function') {
            showToast('error', msg, 3000);
          } else {
            alert(msg);
          }
          emailInput.value = originalEmail;
        } else if(res === 'user_not_found') {
          const msg = window.i18n ? window.i18n.translate('profile.userNotFound') : 'Không tìm thấy thông tin người dùng!';
          if(typeof showToast === 'function') {
            showToast('error', msg, 3000);
          } else {
            alert(msg);
          }
          emailInput.value = originalEmail;
        } else if(res === 'otp_send_failed') {
          const msg = window.i18n ? window.i18n.translate('profile.otpSendFailed') : 'Không thể gửi mã xác thực. Vui lòng kiểm tra cấu hình email hoặc thử lại sau!';
          if(typeof showToast === 'function') {
            showToast('error', msg, 3000);
          } else {
            alert(msg);
          }
          emailInput.value = originalEmail;
        } else {
          console.error('Unexpected response:', res);
          const errorMsg = window.i18n ? window.i18n.translate('profile.errorOccurred') : 'Có lỗi xảy ra';
          if(typeof showToast === 'function') {
            showToast('error', errorMsg + ': ' + res.substring(0, 50), 3000);
          } else {
            alert(errorMsg + ': ' + res);
          }
          emailInput.value = originalEmail;
        }
        
        // Reset button
        const changeEmailText = window.i18n ? window.i18n.translate('profile.changeEmail') : 'Đổi email';
        btnChangeEmail.innerHTML = '<i class="bi bi-pencil-fill me-1"></i>' + changeEmailText;
        btnChangeEmail.classList.remove('btn-primary');
        btnChangeEmail.classList.add('btn-outline-primary');
      })
      .catch(err => {
        console.error('Email change OTP error:', err);
        btnChangeEmail.disabled = false;
        const changeEmailText = window.i18n ? window.i18n.translate('profile.changeEmail') : 'Đổi email';
        btnChangeEmail.innerHTML = '<i class="bi bi-pencil-fill me-1"></i>' + changeEmailText;
        btnChangeEmail.classList.remove('btn-primary');
        btnChangeEmail.classList.add('btn-outline-primary');
        const errorMsg = window.i18n ? window.i18n.translate('profile.errorOccurred') : 'Có lỗi xảy ra';
        if(typeof showToast === 'function') {
          showToast('error', errorMsg + ' khi gửi mã xác thực: ' + err.message, 3000);
        } else {
          alert(errorMsg + ' khi gửi mã xác thực: ' + err.message);
        }
        emailInput.value = originalEmail;
      });
    }

    // Setup OTP inputs cho đổi email
    function setupEmailChangeOTPInputs() {
      for (let i = 1; i <= 6; i++) {
        const input = document.getElementById(`email-otp-${i}`);
        if (!input) continue;
        
        input.addEventListener('input', function(e) {
          const value = e.target.value.replace(/[^0-9]/g, '');
          e.target.value = value;
          
          if (value && i < 6) {
            document.getElementById(`email-otp-${i + 1}`).focus();
          }
          
          updateEmailOTPCode();
        });
        
        input.addEventListener('keydown', function(e) {
          if (e.key === 'Backspace' && !e.target.value && i > 1) {
            document.getElementById(`email-otp-${i - 1}`).focus();
          }
        });
      }
    }

    function updateEmailOTPCode() {
      let otpCode = '';
      for (let i = 1; i <= 6; i++) {
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

        const btnText = this.querySelector('.btn-text');
        const btnLoading = this.querySelector('.btn-loading');
        if(btnText && btnLoading) {
          btnText.classList.add('d-none');
          btnLoading.classList.remove('d-none');
          this.disabled = true;
        }

        const formData = new FormData();
        formData.append('verify_email_change_otp', '');
        formData.append('otp', otpCode);

        fetch('ajax/profile.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.text())
        .then(res => {
          if(btnText && btnLoading) {
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
            this.disabled = false;
          }

          if(res === 'email_changed') {
            if(typeof showToast === 'function') {
              const msg = window.i18n ? window.i18n.translate('profile.emailChangedSuccess') : 'Đổi email thành công!';
              showToast('success', msg, 3000);
            }
            // Đóng modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('emailChangeOTPModal'));
            if(modal) modal.hide();
            // Reload trang để cập nhật email mới
            setTimeout(() => {
              location.reload();
            }, 1000);
          } else if(res === 'otp_invalid') {
            if(otpError) {
              otpError.textContent = 'Mã xác thực không đúng!';
              otpError.style.display = 'block';
            }
            // Xóa các ô OTP
            for(let i = 1; i <= 6; i++) {
              const input = document.getElementById(`email-otp-${i}`);
              if(input) input.value = '';
            }
            document.getElementById('email-otp-1').focus();
          } else if(res === 'otp_expired') {
            const expiredMsg = window.i18n ? window.i18n.translate('profile.otpExpired') : 'Mã xác thực đã hết hạn. Vui lòng gửi lại mã!';
            if(otpError) {
              otpError.textContent = expiredMsg;
              otpError.style.display = 'block';
            }
            if(typeof showToast === 'function') {
              showToast('error', expiredMsg, 3000);
            }
          } else if(res === 'otp_not_found') {
            if(otpError) {
              otpError.textContent = 'Không tìm thấy mã xác thực. Vui lòng gửi lại mã!';
              otpError.style.display = 'block';
            }
          } else if(res === 'email_already') {
            if(otpError) {
              otpError.textContent = 'Email này đã được sử dụng!';
              otpError.style.display = 'block';
            }
            if(typeof showToast === 'function') {
              showToast('error', 'Email này đã được sử dụng!', 3000);
            }
          } else if(res === 'session_error') {
            if(typeof showToast === 'function') {
              showToast('error', 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại!', 3000);
            }
            setTimeout(() => {
              location.href = 'logout.php';
            }, 2000);
          } else {
            if(otpError) {
              otpError.textContent = 'Có lỗi xảy ra. Vui lòng thử lại!';
              otpError.style.display = 'block';
            }
            if(typeof showToast === 'function') {
              showToast('error', 'Có lỗi xảy ra. Vui lòng thử lại!', 3000);
            }
          }
        })
        .catch(err => {
          console.error('Error:', err);
          if(btnText && btnLoading) {
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
            this.disabled = false;
          }
          if(typeof showToast === 'function') {
            showToast('error', 'Có lỗi xảy ra. Vui lòng thử lại!', 3000);
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

    let info_form = document.getElementById('info-form');

    info_form.addEventListener('submit',function(e){
      e.preventDefault();
      
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Đang xử lý...';

      let data = new FormData();
      data.append('info_form','');
      data.append('name',info_form.elements['name'].value);
      data.append('phonenum',info_form.elements['phonenum'].value);
      data.append('address',info_form.elements['address'].value);
      data.append('pincode',info_form.elements['pincode'].value);
      data.append('dob',info_form.elements['dob'].value);
      data.append('gender',info_form.elements['gender'].value);

      let xhr = new XMLHttpRequest();
      xhr.open("POST","ajax/profile.php",true);

      xhr.onload = function(){
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        
        if(this.responseText == 'phone_already'){
          const msg = window.i18n ? window.i18n.translate('profile.phoneAlreadyRegistered') : 'Số điện thoại này đã được đăng ký!';
          if(typeof showToast === 'function') {
            showToast('error', msg, 3000);
          } else {
            alert(msg);
          }
        }
        else if(this.responseText == 0){
          const msg = window.i18n ? window.i18n.translate('profile.updateFailed') : 'Không có thay đổi ghi nhận!';
          if(typeof showToast === 'function') {
            showToast('error', msg, 3000);
          } else {
            alert(msg);
          }
        }
        else{
          const msg = window.i18n ? window.i18n.translate('profile.updateSuccess') : 'Cập nhật thành công!';
          if(typeof showToast === 'function') {
            showToast('success', msg, 3000);
          } else {
            alert(msg);
          }
        }
      }
      
      xhr.onerror = function() {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        if(typeof showToast === 'function') {
          showToast('error', 'Có lỗi xảy ra. Vui lòng thử lại!', 3000);
        } else {
          alert('Có lỗi xảy ra. Vui lòng thử lại!');
        }
      }

      xhr.send(data);

    });

    
    let profile_form = document.getElementById('profile-form');

    profile_form.addEventListener('submit',function(e){
      e.preventDefault();
      
      if(!profile_form.elements['profile'].files[0]) {
        if(typeof showToast === 'function') {
          showToast('error', 'Vui lòng chọn ảnh!', 3000);
        } else {
          alert('Vui lòng chọn ảnh!');
        }
        return;
      }
      
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Đang tải lên...';

      let data = new FormData();
      data.append('profile_form','');
      data.append('profile',profile_form.elements['profile'].files[0]);

      let xhr = new XMLHttpRequest();
      xhr.open("POST","ajax/profile.php",true);

      xhr.onload = function()
      {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        
        if(this.responseText == 'inv_img'){
          if(typeof showToast === 'function') {
            showToast('error', "Chỉ hỗ trợ định dạng JPG, WEBP & PNG!", 3000);
          } else {
            alert("Chỉ hỗ trợ định dạng JPG, WEBP & PNG!");
          }
        }
        else if(this.responseText == 'upd_failed'){
          if(typeof showToast === 'function') {
            showToast('error', "Tải hình ảnh thất bại!", 3000);
          } else {
            alert("Tải hình ảnh thất bại!");
          }
        }
        else if(this.responseText == 0){
          const msg = window.i18n ? window.i18n.translate('profile.updateFailed') : 'Cập nhật thất bại!';
          if(typeof showToast === 'function') {
            showToast('error', msg, 3000);
          } else {
            alert(msg);
          }
        }
        else{
          const msg = window.i18n ? window.i18n.translate('profile.avatarUpdateSuccess') : 'Cập nhật ảnh đại diện thành công!';
          if(typeof showToast === 'function') {
            showToast('success', msg, 3000);
          } else {
            alert(msg);
          }
          setTimeout(() => {
            window.location.href=window.location.pathname;
          }, 1000);
        }
      }
      
      xhr.onerror = function() {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        if(typeof showToast === 'function') {
          showToast('error', 'Có lỗi xảy ra. Vui lòng thử lại!', 3000);
        } else {
          alert('Có lỗi xảy ra. Vui lòng thử lại!');
        }
      }

      xhr.send(data);
    });


    let pass_form = document.getElementById('pass-form');

    pass_form.addEventListener('submit',function(e){
      e.preventDefault();

      let old_pass = pass_form.elements['old_pass'].value;
      let new_pass = pass_form.elements['new_pass'].value;
      let confirm_pass = pass_form.elements['confirm_pass'].value;

      // Kiểm tra mật khẩu cũ
      if(!old_pass || old_pass.trim() === '') {
        if(typeof showToast === 'function') {
          showToast('error', '❌ Vui lòng nhập mật khẩu cũ!', 3000);
        } else {
          alert('Vui lòng nhập mật khẩu cũ!');
        }
        return false;
      }

      // Kiểm tra mật khẩu mới
      if(new_pass.length < 6) {
        if(typeof showToast === 'function') {
          showToast('error', '❌ Mật khẩu mới phải có ít nhất 6 ký tự!', 3000);
        } else {
          alert('Mật khẩu mới phải có ít nhất 6 ký tự!');
        }
        return false;
      }

      // Kiểm tra mật khẩu mới không được trùng với mật khẩu cũ
      if(old_pass === new_pass) {
        if(typeof showToast === 'function') {
          showToast('error', '❌ Mật khẩu mới phải khác mật khẩu cũ!', 3000);
        } else {
          alert('Mật khẩu mới phải khác mật khẩu cũ!');
        }
        return false;
      }

      if(new_pass!=confirm_pass){
        if(typeof showToast === 'function') {
          showToast('error', '❌ Mật khẩu xác nhận không khớp!', 3000);
        } else {
          alert('Mật khẩu xác nhận không khớp!');
        }
        return false;
      }
      
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Đang xử lý...';

      let data = new FormData();
      data.append('pass_form','');
      data.append('old_pass',old_pass);
      data.append('new_pass',new_pass);
      data.append('confirm_pass',confirm_pass);

      let xhr = new XMLHttpRequest();
      xhr.open("POST","ajax/profile.php",true);

      xhr.onload = function()
      {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        
        if(this.responseText == 'old_password_incorrect'){
          if(typeof showToast === 'function') {
            showToast('error', '❌ Mật khẩu cũ không đúng! Vui lòng kiểm tra lại.', 3000);
          } else {
            alert('Mật khẩu cũ không đúng! Vui lòng kiểm tra lại.');
          }
        }
        else if(this.responseText == 'same_password'){
          if(typeof showToast === 'function') {
            showToast('error', '❌ Mật khẩu mới phải khác mật khẩu cũ!', 3000);
          } else {
            alert('Mật khẩu mới phải khác mật khẩu cũ!');
          }
        }
        else if(this.responseText == 'mismatch'){
          if(typeof showToast === 'function') {
            showToast('error', '❌ Mật khẩu xác nhận không khớp!', 3000);
          } else {
            alert('Mật khẩu xác nhận không khớp!');
          }
        }
        else if(this.responseText == 'password_too_short'){
          if(typeof showToast === 'function') {
            showToast('error', '❌ Mật khẩu mới phải có ít nhất 6 ký tự!', 3000);
          } else {
            alert('Mật khẩu mới phải có ít nhất 6 ký tự!');
          }
        }
        else if(this.responseText == 'empty_password'){
          const msg = window.i18n ? window.i18n.translate('profile.pleaseFillAllFields') : 'Vui lòng điền đầy đủ thông tin!';
          if(typeof showToast === 'function') {
            showToast('error', '❌ ' + msg, 3000);
          } else {
            alert(msg);
          }
        }
        else if(this.responseText == 'session_error'){
          const msg = window.i18n ? window.i18n.translate('profile.sessionExpired') : 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại!';
          if(typeof showToast === 'function') {
            showToast('error', "❌ " + msg, 3000);
          } else {
            alert(msg);
          }
        }
        else if(this.responseText == 'empty_password'){
          if(typeof showToast === 'function') {
            showToast('error', "Vui lòng nhập mật khẩu!", 3000);
          } else {
            alert("Vui lòng nhập mật khẩu!");
          }
        }
        else if(this.responseText == 'password_too_short'){
          if(typeof showToast === 'function') {
            showToast('error', "Mật khẩu phải có ít nhất 6 ký tự!", 3000);
          } else {
            alert("Mật khẩu phải có ít nhất 6 ký tự!");
          }
        }
        else if(this.responseText == 0){
          const msg = window.i18n ? window.i18n.translate('profile.updateFailed') : 'Cập nhật thất bại!';
          if(typeof showToast === 'function') {
            showToast('error', msg, 3000);
          } else {
            alert(msg);
          }
        }
        else if(this.responseText == 1 || this.responseText == '1'){
          const msg = window.i18n ? window.i18n.translate('profile.updateSuccess') : 'Cập nhật thành công!';
          if(typeof showToast === 'function') {
            showToast('success', msg, 3000);
          } else {
            alert(msg);
          }
          pass_form.reset();
        }
        else{
          // Xử lý các response không mong đợi
          console.error('Unexpected response:', this.responseText);
          if(typeof showToast === 'function') {
            showToast('error', 'Có lỗi xảy ra: ' + this.responseText, 3000);
          } else {
            alert('Có lỗi xảy ra: ' + this.responseText);
          }
        }
      }
      
      xhr.onerror = function() {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        if(typeof showToast === 'function') {
          showToast('error', 'Có lỗi xảy ra. Vui lòng thử lại!', 3000);
        } else {
          alert('Có lỗi xảy ra. Vui lòng thử lại!');
        }
      }

      xhr.send(data);
    });

    // Remove favorite + book buttons
    document.querySelectorAll('.remove-fav').forEach(btn=>{
      btn.addEventListener('click', function(e){
        e.preventDefault();
        const rid = this.dataset.room;
        fetch('ajax/favorites.php', {
          method:'POST',
          headers:{'Content-Type':'application/x-www-form-urlencoded'},
          body:`toggle=1&room_id=${rid}`
        }).then(r=>r.json()).then(resp=>{
          if(resp.status === 'removed' || resp.status === 'added'){
            const card = this.closest('.fav-card');
            if(card){ card.remove(); }
            if(document.querySelectorAll('.fav-card').length===0){
              const noFavoritesText = window.i18n ? window.i18n.translate('profile.noFavorites') : 'Chưa có phòng yêu thích.';
              document.getElementById('fav-list').innerHTML = "<div class='col-12 text-muted'>" + noFavoritesText + "</div>";
            }
          } else if(resp.status === 'login_required'){
            const msg = window.i18n ? window.i18n.translate('profile.loginToUseFavorites') : 'Vui lòng đăng nhập để sử dụng yêu thích';
            if(typeof showToast === 'function') {
              showToast('error', msg, 3000);
            } else {
              alert(msg);
            }
          } else {
            const msg = window.i18n ? window.i18n.translate('profile.favoriteUpdateError') : 'Có lỗi khi cập nhật yêu thích';
            if(typeof showToast === 'function') {
              showToast('error', msg, 3000);
            } else {
              alert(msg);
            }
          }
        }).catch(()=>{
          const msg = window.i18n ? window.i18n.translate('profile.favoriteUpdateError') : 'Có lỗi khi cập nhật yêu thích';
          if(typeof showToast === 'function') {
            showToast('error', msg, 3000);
          } else {
            alert(msg);
          }
        });
      });
    });

    document.querySelectorAll('.book-btn').forEach(btn=>{
      btn.addEventListener('click', function(){
        const rid = this.dataset.room;
        checkLoginToBook(1, rid);
      });
    });

  </script>

  <script>
    // Hàm toggle password cho phần đổi mật khẩu
    function togglePassword(inputId, button) {
      const input = document.getElementById(inputId);
      const icon = button.querySelector('i');
      
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    }
  </script>

  <!-- Modal Quên mật khẩu -->
  <div class="modal fade" id="forgotPasswordModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="background: linear-gradient(135deg, #1f2937 0%, #374151 100%); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px;">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title text-white fw-bold">
            <i class="bi bi-shield-lock-fill me-2"></i>Đặt lại mật khẩu
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pt-3 text-white" style="color: #ffffff !important;">
          <!-- Step 1: Gửi OTP -->
          <div id="forgot-password-step1">
            <div class="alert alert-info bg-info bg-opacity-25 border-info mb-3" style="color: #ffffff !important; background-color: rgba(13, 110, 253, 0.3) !important;">
              <i class="bi bi-info-circle-fill me-2" style="color: #ffffff !important;"></i>
              <strong style="color: #ffffff !important; font-weight: 600;">Mã OTP sẽ được gửi đến email:</strong><br>
              <span id="forgot-password-email-display" class="fw-bold" style="color: #ffffff !important; font-weight: 700; font-size: 1.1rem;"><?php echo htmlspecialchars($u_fetch['email'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <button type="button" class="btn btn-primary w-100" id="btn-send-forgot-otp">
              <i class="bi bi-send-fill me-2"></i>Gửi mã OTP
            </button>
          </div>

          <!-- Step 2: Nhập OTP và mật khẩu mới -->
          <div id="forgot-password-step2" style="display: none;">
            <div class="alert alert-success bg-success bg-opacity-25 border-success mb-3" style="color: #ffffff !important; background-color: rgba(25, 135, 84, 0.3) !important;">
              <i class="bi bi-envelope-check-fill me-2" style="color: #ffffff !important;"></i>
              <strong style="color: #ffffff !important; font-weight: 600;">Mã OTP đã được gửi!</strong> <span style="color: #ffffff !important;">Vui lòng kiểm tra email và nhập mã OTP.</span>
            </div>

            <div class="mb-3">
              <label class="form-label text-white" style="color: #ffffff !important; font-weight: 600;">
                <i class="bi bi-key-fill me-2" style="color: #ffffff !important;"></i>Mã xác thực (6 số)
              </label>
              <div class="d-flex gap-2 justify-content-center mb-2" id="forgot-password-otp-inputs">
                <?php for($i=1; $i<=6; $i++): ?>
                  <input type="text" class="form-control text-center fw-bold" id="forgot-otp-<?php echo $i; ?>" maxlength="1" style="width: 50px; height: 60px; font-size: 24px; background: rgba(255,255,255,0.1); border: 2px solid rgba(255,255,255,0.2); color: #fff;">
                <?php endfor; ?>
              </div>
              <input type="hidden" id="forgot-otp-full-code">
              <div class="error-message text-danger small mt-2" id="forgot-otp-error" style="display: none;"></div>
            </div>

            <div class="mb-3">
              <label class="form-label text-white" style="color: #ffffff !important; font-weight: 600;">
                <i class="bi bi-lock-fill me-2" style="color: #ffffff !important;"></i>Mật khẩu mới
              </label>
              <input type="password" id="forgot-new-password" class="form-control shadow-none" placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)" style="background-color: rgba(255,255,255,0.15); color: #ffffff !important; border: 1px solid rgba(255,255,255,0.4); font-weight: 500;" minlength="6">
            </div>

            <div class="mb-3">
              <label class="form-label text-white" style="color: #ffffff !important; font-weight: 600;">
                <i class="bi bi-lock-fill me-2" style="color: #ffffff !important;"></i>Xác nhận mật khẩu mới
              </label>
              <input type="password" id="forgot-confirm-password" class="form-control shadow-none" placeholder="Nhập lại mật khẩu mới" style="background-color: rgba(255,255,255,0.15); color: #ffffff !important; border: 1px solid rgba(255,255,255,0.4); font-weight: 500;" minlength="6">
            </div>

            <div class="d-flex gap-2">
              <button type="button" class="btn btn-secondary flex-grow-1" data-bs-dismiss="modal">Hủy</button>
              <button type="button" class="btn btn-primary flex-grow-1" id="btn-reset-password">
                <i class="bi bi-check-circle me-1"></i>Đặt lại mật khẩu
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Xử lý quên mật khẩu
    const btnForgotPassword = document.getElementById('btn-forgot-password');
    if(btnForgotPassword) {
      btnForgotPassword.addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'));
        modal.show();
      });
    }

    // Gửi OTP quên mật khẩu
    const btnSendForgotOTP = document.getElementById('btn-send-forgot-otp');
    if(btnSendForgotOTP) {
      btnSendForgotOTP.addEventListener('click', function() {
        btnSendForgotOTP.disabled = true;
        btnSendForgotOTP.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang gửi...';

        const formData = new FormData();
        formData.append('send_forgot_password_otp', '');

        fetch('ajax/profile.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.text())
        .then(res => {
          btnSendForgotOTP.disabled = false;
          btnSendForgotOTP.innerHTML = '<i class="bi bi-send-fill me-2"></i>Gửi mã OTP';

          if(res.trim() === 'otp_sent') {
            // Hiển thị step 2
            document.getElementById('forgot-password-step1').style.display = 'none';
            document.getElementById('forgot-password-step2').style.display = 'block';
            
            // Setup OTP inputs
            setupForgotPasswordOTPInputs();
            
            // Focus vào ô OTP đầu tiên
            setTimeout(() => {
              document.getElementById('forgot-otp-1').focus();
            }, 300);
            
            if(typeof showToast === 'function') {
              showToast('success', '📧 Mã OTP đã được gửi đến email của bạn!', 3000);
            }
          } else if(res.trim() === 'otp_send_failed') {
            if(typeof showToast === 'function') {
              showToast('error', '❌ Không thể gửi mã OTP. Vui lòng thử lại!', 3000);
            } else {
              alert('Không thể gửi mã OTP. Vui lòng thử lại!');
            }
          } else {
            if(typeof showToast === 'function') {
              showToast('error', '❌ Có lỗi xảy ra. Vui lòng thử lại!', 3000);
            } else {
              alert('Có lỗi xảy ra. Vui lòng thử lại!');
            }
          }
        })
        .catch(err => {
          console.error('Send forgot password OTP error:', err);
          btnSendForgotOTP.disabled = false;
          btnSendForgotOTP.innerHTML = '<i class="bi bi-send-fill me-2"></i>Gửi mã OTP';
          if(typeof showToast === 'function') {
            showToast('error', '❌ Có lỗi xảy ra. Vui lòng thử lại!', 3000);
          } else {
            alert('Có lỗi xảy ra. Vui lòng thử lại!');
          }
        });
      });
    }

    // Setup OTP inputs cho quên mật khẩu
    function setupForgotPasswordOTPInputs() {
      for(let i = 1; i <= 6; i++) {
        const input = document.getElementById(`forgot-otp-${i}`);
        if(input) {
          input.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            if(e.target.value.length === 1 && i < 6) {
              document.getElementById(`forgot-otp-${i + 1}`).focus();
            }
            updateForgotOTPCode();
          });
          input.addEventListener('keydown', function(e) {
            if(e.key === 'Backspace' && !e.target.value && i > 1) {
              document.getElementById(`forgot-otp-${i - 1}`).focus();
            }
            updateForgotOTPCode();
          });
          input.addEventListener('focus', function(e) {
            e.target.style.borderColor = 'rgba(255,255,255,0.5)';
            e.target.style.background = 'rgba(255,255,255,0.2)';
          });
          input.addEventListener('blur', function(e) {
            if(!e.target.value) {
              e.target.style.borderColor = 'rgba(255,255,255,0.2)';
              e.target.style.background = 'rgba(255,255,255,0.1)';
            }
          });
        }
      }
    }

    function updateForgotOTPCode() {
      let otpCode = '';
      for(let i = 1; i <= 6; i++) {
        const input = document.getElementById(`forgot-otp-${i}`);
        if (input) otpCode += input.value;
      }
      document.getElementById('forgot-otp-full-code').value = otpCode;
    }

    // Xử lý đặt lại mật khẩu
    const btnResetPassword = document.getElementById('btn-reset-password');
    if(btnResetPassword) {
      btnResetPassword.addEventListener('click', function() {
        const otpCode = document.getElementById('forgot-otp-full-code').value;
        const newPassword = document.getElementById('forgot-new-password').value;
        const confirmPassword = document.getElementById('forgot-confirm-password').value;
        const otpError = document.getElementById('forgot-otp-error');

        // Reset error
        if(otpError) {
          otpError.style.display = 'none';
        }

        // Validate
        if(otpCode.length !== 6) {
          if(otpError) {
            otpError.textContent = '❌ Vui lòng nhập đầy đủ 6 số mã OTP!';
            otpError.style.display = 'block';
          }
          return;
        }

        if(newPassword.length < 6) {
          if(typeof showToast === 'function') {
            showToast('error', '❌ Mật khẩu phải có ít nhất 6 ký tự!', 3000);
          } else {
            alert('Mật khẩu phải có ít nhất 6 ký tự!');
          }
          return;
        }

        if(newPassword !== confirmPassword) {
          if(typeof showToast === 'function') {
            showToast('error', '❌ Mật khẩu xác nhận không khớp!', 3000);
          } else {
            alert('Mật khẩu xác nhận không khớp!');
          }
          return;
        }

        btnResetPassword.disabled = true;
        btnResetPassword.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang xử lý...';

        const formData = new FormData();
        formData.append('verify_forgot_password_otp', '');
        formData.append('otp', otpCode);
        formData.append('new_password', newPassword);

        fetch('ajax/profile.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.text())
        .then(res => {
          btnResetPassword.disabled = false;
          btnResetPassword.innerHTML = '<i class="bi bi-check-circle me-1"></i>Đặt lại mật khẩu';

          if(res.trim() === 'password_reset_success') {
            if(typeof showToast === 'function') {
              showToast('success', '✅ Đặt lại mật khẩu thành công!', 3000);
            } else {
              alert('Đặt lại mật khẩu thành công!');
            }
            const modal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'));
            if(modal) modal.hide();
            // Reload trang sau 1 giây
            setTimeout(() => {
              location.reload();
            }, 1500);
          } else if(res.trim() === 'otp_invalid') {
            if(otpError) {
              otpError.textContent = '❌ Mã xác thực không đúng! Vui lòng kiểm tra lại.';
              otpError.style.display = 'block';
            }
            // Xóa các ô OTP
            for(let i = 1; i <= 6; i++) {
              const input = document.getElementById(`forgot-otp-${i}`);
              if(input) input.value = '';
            }
            document.getElementById('forgot-otp-1').focus();
          } else if(res.trim() === 'otp_expired') {
            if(otpError) {
              otpError.textContent = '⏰ Mã xác thực đã hết hạn. Vui lòng gửi lại mã!';
              otpError.style.display = 'block';
            }
            if(typeof showToast === 'function') {
              showToast('warning', '⏰ Mã xác thực đã hết hạn. Vui lòng gửi lại mã!', 4000);
            }
          } else if(res.trim() === 'password_too_short') {
            if(typeof showToast === 'function') {
              showToast('error', '❌ Mật khẩu phải có ít nhất 6 ký tự!', 3000);
            } else {
              alert('Mật khẩu phải có ít nhất 6 ký tự!');
            }
          } else if(res.trim() === 'update_failed') {
            if(typeof showToast === 'function') {
              showToast('error', '❌ Không thể cập nhật mật khẩu. Vui lòng thử lại!', 3000);
            } else {
              alert('Không thể cập nhật mật khẩu. Vui lòng thử lại!');
            }
          } else {
            if(typeof showToast === 'function') {
              showToast('error', '❌ Có lỗi xảy ra. Vui lòng thử lại!', 3000);
            } else {
              alert('Có lỗi xảy ra. Vui lòng thử lại!');
            }
          }
        })
        .catch(err => {
          console.error('Reset password error:', err);
          btnResetPassword.disabled = false;
          btnResetPassword.innerHTML = '<i class="bi bi-check-circle me-1"></i>Đặt lại mật khẩu';
          if(typeof showToast === 'function') {
            showToast('error', '❌ Có lỗi xảy ra. Vui lòng thử lại!', 3000);
          } else {
            alert('Có lỗi xảy ra. Vui lòng thử lại!');
          }
        });
      });
    }

    // Khởi tạo OTP inputs khi modal được hiển thị
    const forgotPasswordModal = document.getElementById('forgotPasswordModal');
    if(forgotPasswordModal) {
      forgotPasswordModal.addEventListener('shown.bs.modal', function() {
        // Reset form
        document.getElementById('forgot-password-step1').style.display = 'block';
        document.getElementById('forgot-password-step2').style.display = 'none';
        for(let i = 1; i <= 6; i++) {
          const input = document.getElementById(`forgot-otp-${i}`);
          if(input) input.value = '';
        }
        document.getElementById('forgot-new-password').value = '';
        document.getElementById('forgot-confirm-password').value = '';
        const otpError = document.getElementById('forgot-otp-error');
        if(otpError) {
          otpError.style.display = 'none';
          otpError.textContent = '';
        }
      });
    }
  </script>

</body>
</html>
