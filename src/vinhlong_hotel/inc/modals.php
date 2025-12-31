<!-- ========================================================= -->

<!-- ===================  MODAL QUÊN MẬT KHẨU  =================== -->

<!-- ========================================================= -->

<div class="modal fade" id="forgotPasswordLoginModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modern-modal">
      <form id="forgot-password-form">
        <div class="modal-header border-0 justify-content-center">
          <h4 class="fw-bold text-white">
            <i class="bi bi-shield-lock-fill me-2 text-gold"></i> Quên mật khẩu
          </h4>
          <button type="button" class="btn-close position-absolute end-0 me-3 mt-3" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <!-- Step 1: Nhập email -->
          <div id="forgot-password-step1">
            <div class="alert alert-info bg-info bg-opacity-25 border-info mb-3" style="color: #ffffff !important; background-color: rgba(13, 110, 253, 0.3) !important;">
              <i class="bi bi-info-circle-fill me-2" style="color: #ffffff !important;"></i>
              <strong style="color: #ffffff !important; font-weight: 600;">Nhập email hoặc số điện thoại của bạn</strong><br>
              <span style="color: #ffffff !important;">Chúng tôi sẽ gửi mã OTP để đặt lại mật khẩu.</span>
            </div>

            <div class="form-group-modern mb-3">
              <label class="form-label-modern text-white d-flex align-items-center gap-2">
                <i class="bi bi-envelope-fill"></i>
                <span>Email hoặc số điện thoại</span>
              </label>
              <div class="input-wrapper-modern">
                <input type="text" id="forgot-email-mob" name="email_mob" class="form-control-modern" placeholder="Nhập email hoặc số điện thoại" required>
                <i class="input-icon bi bi-envelope"></i>
              </div>
              <div class="error-message" id="forgot-email-error"></div>
            </div>

            <div class="text-center mb-3">
              <button type="button" class="btn btn-gold px-5 py-3 rounded-pill fw-semibold" id="btn-send-forgot-otp-login">
                <i class="bi bi-send-fill me-2"></i>Gửi mã OTP
              </button>
            </div>
          </div>

          <!-- Step 2: Nhập OTP và mật khẩu mới -->
          <div id="forgot-password-step2" style="display: none;">
            <div class="alert alert-success bg-success bg-opacity-25 border-success mb-3" style="color: #ffffff !important; background-color: rgba(25, 135, 84, 0.3) !important;">
              <i class="bi bi-envelope-check-fill me-2" style="color: #ffffff !important;"></i>
              <strong style="color: #ffffff !important; font-weight: 600;">Mã OTP đã được gửi!</strong> <span style="color: #ffffff !important;">Vui lòng kiểm tra email và nhập mã OTP.</span>
            </div>

            <div class="mb-3">
              <label class="form-label-modern text-white d-flex align-items-center gap-2 mb-2" style="color: #ffffff !important; font-weight: 600;">
                <i class="bi bi-key-fill" style="color: #ffffff !important;"></i>
                <span>Mã xác thực (6 số)</span>
              </label>
              <div class="d-flex gap-2 justify-content-center mb-2" id="forgot-password-otp-inputs-login">
                <?php for($i=1; $i<=6; $i++): ?>
                  <input type="text" class="form-control text-center fw-bold" id="forgot-otp-login-<?php echo $i; ?>" maxlength="1" style="width: 50px; height: 60px; font-size: 24px; background: rgba(255,255,255,0.1); border: 2px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px;">
                <?php endfor; ?>
              </div>
              <input type="hidden" id="forgot-otp-full-code-login">
              <div class="error-message text-danger small mt-2" id="forgot-otp-error-login" style="display: none;"></div>
            </div>

            <div class="form-group-modern mb-3">
              <label class="form-label-modern text-white d-flex align-items-center gap-2" style="color: #ffffff !important; font-weight: 600;">
                <i class="bi bi-lock-fill" style="color: #ffffff !important;"></i>
                <span>Mật khẩu mới</span>
              </label>
              <div class="input-wrapper-modern">
                <input type="password" id="forgot-new-password-login" class="form-control-modern" placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)" minlength="6" required>
                <i class="input-icon bi bi-lock"></i>
                <button type="button" class="password-toggle" onclick="togglePassword('forgot-new-password-login', this)">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>

            <div class="form-group-modern mb-3">
              <label class="form-label-modern text-white d-flex align-items-center gap-2" style="color: #ffffff !important; font-weight: 600;">
                <i class="bi bi-lock-fill" style="color: #ffffff !important;"></i>
                <span>Xác nhận mật khẩu mới</span>
              </label>
              <div class="input-wrapper-modern">
                <input type="password" id="forgot-confirm-password-login" class="form-control-modern" placeholder="Nhập lại mật khẩu mới" minlength="6" required>
                <i class="input-icon bi bi-lock"></i>
                <button type="button" class="password-toggle" onclick="togglePassword('forgot-confirm-password-login', this)">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>

            <div class="d-flex gap-2">
              <button type="button" class="btn btn-secondary flex-grow-1" data-bs-dismiss="modal">Hủy</button>
              <button type="button" class="btn btn-gold flex-grow-1" id="btn-reset-password-login">
                <i class="bi bi-check-circle me-1"></i>Đặt lại mật khẩu
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ========================================================= -->

<!-- ===================  MODAL ĐĂNG NHẬP  =================== -->

<!-- ========================================================= -->



<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">

  <div class="modal-dialog modal-dialog-centered">

    <div class="modal-content modern-modal">



      <form id="login-form">



        <div class="modal-header border-0 justify-content-center">

          <h4 class="fw-bold text-white">

            <i class="bi bi-person-circle me-2 text-gold"></i> <span data-i18n="auth.loginTitle">Đăng nhập tài khoản</span>

          </h4>

          <button type="button" class="btn-close position-absolute end-0 me-3 mt-3" data-bs-dismiss="modal"></button>

        </div>



        <div class="modal-body">

          <!-- Email -->
          <div class="form-group-modern mb-3">
            <label class="form-label-modern text-white d-flex align-items-center gap-2">
              <i class="bi bi-envelope-fill"></i>
              <span data-i18n="auth.emailOrPhone">Email hoặc số điện thoại</span>
            </label>
            <div class="input-wrapper-modern">
              <input type="text" id="email_mob" name="email_mob" class="form-control-modern" data-i18n-placeholder="auth.emailOrPhonePlaceholder" placeholder="Nhập email hoặc số điện thoại" required>
              <i class="input-icon bi bi-envelope"></i>
            </div>
            <div class="error-message" id="email_mob_error"></div>
          </div>

          <!-- Pass -->
          <div class="form-group-modern mb-4">
            <label class="form-label-modern text-white d-flex align-items-center gap-2">
              <i class="bi bi-lock-fill"></i>
              <span data-i18n="auth.password">Mật khẩu</span>
            </label>
            <div class="input-wrapper-modern">
              <input type="password" id="login_pass" name="pass" class="form-control-modern" data-i18n-placeholder="auth.passwordPlaceholder" placeholder="Nhập mật khẩu" required>
              <i class="input-icon bi bi-lock"></i>
              <button type="button" class="password-toggle" onclick="togglePassword('login_pass', this)">
                <i class="bi bi-eye"></i>
              </button>
            </div>
            <div class="error-message" id="login_pass_error"></div>
          </div>

          <!-- Quên mật khẩu -->
          <div class="text-end mb-3">
            <a href="#" class="text-gold text-decoration-none small fw-semibold" id="forgot-password-link" style="font-size: 13px;">
              <i class="bi bi-question-circle me-1"></i><span data-i18n="auth.forgotPassword">Quên mật khẩu?</span>
            </a>
          </div>

          <!-- HOẶC -->

          <div class="separator text-center mb-3">

            <span data-i18n="auth.or">Hoặc</span>

          </div>



          <!-- Google icon -->

          <div class="text-center mb-2">

            <a href="google-login.php" class="btn-google-icon">

              <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"

                  alt="Google Login" class="google-logo">

            </a>

          </div>



          <!-- Danh sách tài khoản đã lưu -->

          <div id="saved-accounts-section" class="saved-accounts-container mb-3" style="display: none;">

            <div class="d-flex align-items-center justify-content-between mb-2">

              <small class="text-muted d-flex align-items-center gap-1">

                <i class="bi bi-clock-history"></i>

                <span data-i18n="auth.savedAccounts">Tài khoản đã lưu</span>

              </small>

            </div>

            <div id="saved-accounts-list" class="saved-accounts-list">

              <!-- Danh sách tài khoản sẽ được load bằng JavaScript -->

            </div>

          </div>



          <!-- Error Alert -->
          <div class="alert alert-danger d-none mb-3" id="login-error-alert" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <span id="login-error-text"></span>
          </div>

          <!-- Login button -->
          <div class="text-center mb-3">
            <button type="submit" class="btn btn-gold px-5 py-3 rounded-pill fw-semibold" id="login-submit-btn">
              <span class="btn-text">
                <i class="bi bi-box-arrow-in-right me-2"></i><span data-i18n="auth.login">Đăng nhập</span>
              </span>
              <span class="btn-loading d-none">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                <span data-i18n="auth.processing">Đang xử lý...</span>
              </span>
            </button>
          </div>



          <!-- Register link -->

          <p class="mt-3 text-center small text-muted">

            <span data-i18n="auth.noAccount">Chưa có tài khoản?</span>

            <a href="#" class="text-gold fw-semibold" data-bs-dismiss="modal"

               data-bs-toggle="modal" data-bs-target="#registerModal">

              <span data-i18n="auth.registerNow">Đăng ký ngay</span>

            </a>

          </p>



        </div>



      </form>



    </div>

  </div>

</div>





<!-- ========================================================= -->

<!-- =====================  MODAL ĐĂNG KÝ  ==================== -->

<!-- ========================================================= -->



<div class="modal fade" id="registerModal" tabindex="-1">

  <div class="modal-dialog modal-lg modal-dialog-centered">

    <div class="modal-content modern-modal">



      <form id="register-form" enctype="multipart/form-data">



        <div class="modal-header border-0 justify-content-center">

          <h4 class="fw-bold text-white">

            <i class="bi bi-person-plus-fill me-2 text-gold"></i> <span data-i18n="auth.registerTitle">Tạo tài khoản mới</span>

          </h4>

          <button type="button" class="btn-close position-absolute end-0 me-3 mt-3" data-bs-dismiss="modal"></button>

        </div>



        <div class="modal-body">

          <!-- Error Alert -->
          <div class="alert alert-danger d-none mb-3" id="register-error-alert" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <span id="register-error-text"></span>
          </div>

          <div class="row g-4">
            <div class="col-md-6">
              <div class="form-group-modern">
                <label class="form-label-modern text-white d-flex align-items-center gap-2">
                  <i class="bi bi-person-fill"></i>
                  <span data-i18n="auth.fullName">Họ và tên</span>
                </label>
                <div class="input-wrapper-modern">
                  <input type="text" name="name" class="form-control-modern" data-i18n-placeholder="auth.fullNamePlaceholder" placeholder="Nhập họ và tên" required>
                  <i class="input-icon bi bi-person"></i>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group-modern">
                <label class="form-label-modern text-white d-flex align-items-center gap-2">
                  <i class="bi bi-envelope-fill"></i>
                  <span data-i18n="auth.email">Email</span>
                </label>
                <div class="input-wrapper-modern">
                  <input type="email" name="email" id="register_email" class="form-control-modern" data-i18n-placeholder="auth.emailPlaceholder" placeholder="Nhập email" required>
                  <i class="input-icon bi bi-envelope"></i>
                  <!-- Nút gửi OTP đã được ẩn - OTP sẽ tự động gửi khi ấn "Đăng ký" -->
                  <button type="button" class="btn-send-otp d-none" id="btn-send-otp" title="Gửi mã xác thực" style="display: none !important;">
                    <i class="bi bi-send-fill"></i>
                  </button>
                </div>
                <div class="error-message" id="email_error"></div>
                <div id="email-verified-badge" class="d-none mt-2">
                  <small class="text-success d-flex align-items-center gap-1">
                    <i class="bi bi-check-circle-fill"></i>
                    <span data-i18n="auth.emailVerified">Email đã được xác thực</span>
                  </small>
                </div>
                <small class="text-info d-block mt-1" id="email-verification-reminder">
                  <i class="bi bi-info-circle-fill"></i>
                  <strong data-i18n="auth.otpNote">Lưu ý: Mã xác thực sẽ được gửi tự động khi bạn ấn nút "Đăng ký"</strong>
                </small>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group-modern">
                <label class="form-label-modern text-white d-flex align-items-center gap-2">
                  <i class="bi bi-telephone-fill"></i>
                  <span data-i18n="auth.phone">Số điện thoại</span>
                </label>
                <div class="input-wrapper-modern">
                  <input type="tel" name="phonenum" id="register_phonenum" class="form-control-modern" data-i18n-placeholder="auth.phonePlaceholder" placeholder="Nhập số điện thoại (VD: 0987654321)" required pattern="[0][0-9]{9}" maxlength="10">
                  <i class="input-icon bi bi-telephone"></i>
                </div>
                <div class="error-message" id="phonenum_error"></div>
                <small class="text-white-50 d-block mt-1">
                  <i class="bi bi-info-circle"></i> <span data-i18n="auth.phoneInfo">Số điện thoại Việt Nam (10 số, bắt đầu bằng 0)</span>
                </small>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group-modern">
                <label class="form-label-modern text-white d-flex align-items-center gap-2">
                  <i class="bi bi-calendar-event-fill"></i>
                  <span data-i18n="auth.dateOfBirth">Ngày sinh</span>
                </label>
                <div class="input-wrapper-modern">
                  <input type="date" name="dob" id="register_dob" class="form-control-modern" required max="">
                  <i class="input-icon bi bi-calendar"></i>
                </div>
                <div class="error-message" id="dob_error"></div>
                <small class="text-white-50 d-block mt-1">
                  <i class="bi bi-info-circle"></i> <span data-i18n="auth.ageRequirement">Phải từ 18 tuổi trở lên</span>
                </small>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group-modern">
                <label class="form-label-modern text-white d-flex align-items-center gap-2">
                  <i class="bi bi-geo-alt-fill"></i>
                  <span data-i18n="auth.address">Địa chỉ</span>
                </label>
                <div class="input-wrapper-modern">
                  <input type="text" name="address" id="register_address" class="form-control-modern" data-i18n-placeholder="auth.addressPlaceholder" placeholder="Nhập địa chỉ hoặc click icon bên phải để lấy vị trí">
                  <i class="input-icon bi bi-geo-alt"></i>
                  <button type="button" class="btn-location" id="btn-get-location" title="Lấy vị trí hiện tại" onclick="getCurrentLocation()">
                    <i class="bi bi-geo-alt-fill"></i>
                  </button>
                </div>
                <small class="text-white-50 d-block mt-1">
                  <i class="bi bi-info-circle"></i> Nhấp vào biểu tượng <i class="bi bi-geo-alt-fill" style="font-size: 12px;"></i> bên phải để lấy vị trí hiện tại
                </small>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group-modern">
                <label class="form-label-modern text-white d-flex align-items-center gap-2">
                  <i class="bi bi-shield-check-fill"></i>
                  <span>Mã bưu điện</span>
                </label>
                <div class="input-wrapper-modern">
                  <input type="text" name="pincode" class="form-control-modern" placeholder="Nhập mã bưu điện">
                  <i class="input-icon bi bi-shield-check"></i>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group-modern">
                <label class="form-label-modern text-white d-flex align-items-center gap-2">
                  <i class="bi bi-lock-fill"></i>
                  <span data-i18n="auth.password">Mật khẩu</span>
                </label>
                <div class="input-wrapper-modern">
                  <input type="password" name="pass" id="register_pass" class="form-control-modern" data-i18n-placeholder="auth.passwordPlaceholder" placeholder="Nhập mật khẩu" required>
                  <i class="input-icon bi bi-lock"></i>
                  <button type="button" class="password-toggle" onclick="togglePassword('register_pass', this)">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
                <small class="text-white-50 d-block mt-1">Tối thiểu 6 ký tự</small>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group-modern">
                <label class="form-label-modern text-white d-flex align-items-center gap-2">
                  <i class="bi bi-lock-fill"></i>
                  <span data-i18n="auth.confirmPassword">Nhập lại mật khẩu</span>
                </label>
                <div class="input-wrapper-modern">
                  <input type="password" name="cpass" id="register_cpass" class="form-control-modern" data-i18n-placeholder="auth.confirmPasswordPlaceholder" placeholder="Nhập lại mật khẩu" required>
                  <i class="input-icon bi bi-lock"></i>
                  <button type="button" class="password-toggle" onclick="togglePassword('register_cpass', this)">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
                <div class="error-message" id="password-match-error"></div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group-modern">
                <label class="form-label-modern text-white d-flex align-items-center gap-2">
                  <i class="bi bi-gender-ambiguous"></i>
                  <span data-i18n="auth.gender">Giới tính</span>
                </label>
                <div class="input-wrapper-modern">
                  <select name="gender" id="register_gender" class="form-control-modern" required>
                    <option value="" data-i18n="profile.selectGender">-- Chọn giới tính --</option>
                    <option value="male" data-i18n="profile.male">Nam</option>
                    <option value="female" data-i18n="profile.female">Nữ</option>
                  </select>
                  <i class="input-icon bi bi-gender-ambiguous"></i>
                </div>
              </div>
            </div>
          </div>



          <div class="text-center mt-4">
            <button type="submit" class="btn btn-gold px-5 py-3 rounded-pill fw-semibold" id="register-submit-btn">
              <span class="btn-text">
                <i class="bi bi-person-plus-fill me-2"></i><span data-i18n="auth.register">Đăng ký</span>
              </span>
              <span class="btn-loading d-none">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                <span data-i18n="auth.processing">Đang xử lý...</span>
              </span>
            </button>
          </div>



          <p class="mt-3 text-center small text-muted">

            <span data-i18n="auth.haveAccount">Đã có tài khoản?</span>

            <a href="#" class="text-gold fw-semibold" data-bs-dismiss="modal"

               data-bs-toggle="modal" data-bs-target="#loginModal">

              <span data-i18n="auth.loginNow">Đăng nhập ngay</span>

            </a>

          </p>



        </div>



      </form>



    </div>

  </div>

</div>

<!-- ========================================================= -->
<!-- MODAL XÁC THỰC OTP -->
<!-- ========================================================= -->

<div class="modal fade" id="otpVerificationModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modern-modal">
      <div class="modal-header border-0 justify-content-center">
        <h4 class="fw-bold text-white">
          <i class="bi bi-shield-check-fill me-2 text-gold"></i> Xác thực email
        </h4>
        <button type="button" class="btn-close position-absolute end-0 me-3 mt-3" data-bs-dismiss="modal" id="otp-modal-close"></button>
      </div>
      
      <div class="modal-body">
        <div class="alert alert-info d-flex align-items-center gap-2 mb-3">
          <i class="bi bi-info-circle-fill"></i>
          <div>
            <strong>Mã xác thực đã được gửi đến email của bạn</strong>
            <div class="small mt-1" id="otp-email-display"></div>
          </div>
        </div>
        
        <form id="otp-verification-form">
          <div class="form-group-modern mb-4">
            <label class="form-label-modern text-white d-flex align-items-center gap-2 mb-3">
              <i class="bi bi-key-fill"></i>
              <span>Nhập mã xác thực 6 số</span>
            </label>
            <div class="otp-input-container">
              <input type="text" id="otp-input-1" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off">
              <input type="text" id="otp-input-2" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off">
              <input type="text" id="otp-input-3" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off">
              <input type="text" id="otp-input-4" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off">
              <input type="text" id="otp-input-5" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off">
              <input type="text" id="otp-input-6" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off">
            </div>
            <input type="hidden" id="otp-full-code" name="otp">
            <input type="hidden" id="otp-email" name="email">
            <div class="error-message mt-2" id="otp_error"></div>
          </div>
          
          <div class="text-center">
            <button type="submit" class="btn btn-gold px-5 py-3 rounded-pill fw-semibold" id="otp-submit-btn">
              <span class="btn-text">
                <i class="bi bi-check-circle-fill me-2"></i>Xác thực
              </span>
              <span class="btn-loading d-none">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Đang xác thực...
              </span>
            </button>
          </div>
        </form>
        
        <div class="text-center mt-3">
          <button type="button" class="btn btn-link text-white-50 p-0" id="btn-resend-otp">
            <i class="bi bi-arrow-clockwise me-1"></i>Gửi lại mã
          </button>
        </div>
        
        <div class="text-center mt-2">
          <small class="text-white-50">Mã có hiệu lực trong <strong id="otp-timer" class="text-gold">10:00</strong></small>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ========================================================= -->

<!-- ==========================  CSS  ========================== -->

<!-- ========================================================= -->



<style>

/* ========================= BACKDROP ========================= */

.modal-backdrop.show {
    opacity: 0.4 !important;
    backdrop-filter: blur(4px);
    z-index: 10050 !important;
}

.modal {
    z-index: 10060 !important;
}

.modal.show {
    z-index: 10060 !important;
}



/* ========================= KHUNG MODAL ========================= */

.modern-modal {
    background: rgba(0, 0, 0, 0.60) !important;
    border: 1px solid rgba(255,255,255,0.15);
    color: #ffffff !important;
    border-radius: 20px !important;
    padding: 15px 20px;
    backdrop-filter: blur(18px);
    box-shadow: 0 0 30px rgba(0,0,0,0.6);
    animation: showModal 0.3s ease;
    z-index: 10065 !important;
    position: relative;
}

.modal-dialog {
    z-index: 10065 !important;
}

#loginModal,
#registerModal {
    z-index: 10060 !important;
}

#otpVerificationModal {
    z-index: 10070 !important;
}

#otpVerificationModal .modal-dialog {
    z-index: 10071 !important;
}



@keyframes showModal {

    from { transform: scale(0.95); opacity: 0; }

    to   { transform: scale(1); opacity: 1; }

}



/* ========================= TIÊU ĐỀ ========================= */

.modern-modal .modal-header h4 {

    color: #fff !important;

    font-weight: 700;

    font-size: 20px;

}



/* ========================= BUTTON CLOSE ========================= */

.modern-modal .btn-close {

    filter: invert(1) brightness(2);

}



/* ========================= INPUT ========================= */

.modern-modal input.form-control {

    background: rgba(255,255,255,0.08) !important;

    border: 1px solid rgba(255,255,255,0.25);

    color: #fff !important;

    border-radius: 10px;

    height: 46px;

}



.modern-modal input.form-control:focus {

    border-color: #00ffbf;

    box-shadow: 0 0 10px rgba(0,255,200,0.5);

}



/* ========================= NÚT CHÍNH ========================= */

.btn-gold {
    background: linear-gradient(135deg, #00ffbf 0%, #00d4aa 100%) !important;
    color: #000 !important;
    border-radius: 50px !important;
    padding: 14px 35px !important;
    font-weight: 700 !important;
    box-shadow: 0 6px 20px rgba(0,255,191,0.4) !important;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
    position: relative;
    overflow: hidden;
    transform: translateY(0);
    letter-spacing: 0.5px;
    border: none !important;
}

.btn-gold::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.6s;
}

.btn-gold:hover::before {
    left: 100%;
}

.btn-gold:hover {
    background: linear-gradient(135deg, #00e6ad 0%, #00c99a 100%) !important;
    box-shadow: 0 8px 30px rgba(0,255,200,0.6) !important;
    transform: translateY(-3px) scale(1.02) !important;
}

.btn-gold:active {
    transform: translateY(-1px) scale(0.98) !important;
    box-shadow: 0 4px 15px rgba(0,255,191,0.5) !important;
}



/* ========================= TEXT ========================= */

.text-gold {

    color: #00ffbf !important;

}

.text-gold:hover {

    color: #8affee !important;

}



.modern-modal small,

.modern-modal p {

    color: #fff !important;

}



/* ========================= SEPARATOR HOẶC ========================= */

.separator {

    position: relative;

    color: #ffffffcc;

    font-size: 14px;

}

.separator span {

    background: rgba(0,0,0,0.6);

    padding: 0 10px;

    z-index: 2;

    position: relative;

}

.separator::before {

    content: "";

    position: absolute;

    top: 50%;

    left: 0;

    width: 100%;

    height: 1px;

    background: rgba(255,255,255,0.25);

    z-index: 1;

}



/* ========================= GOOGLE ICON ========================= */

.btn-google-icon {

    width: 52px;

    height: 52px;

    border-radius: 50%;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    background: rgba(255,255,255,0.15);

    border: 1px solid rgba(255,255,255,0.25);

    transition: 0.25s;

    cursor: pointer;

}

.btn-google-icon:hover {

    background: #fff;

    transform: scale(1.1);

    box-shadow: 0 0 15px rgba(255,255,255,0.5);

}

.google-logo {

    width: 26px;

    height: 26px;

}

/* ========================= DANH SÁCH TÀI KHOẢN ĐÃ LƯU ========================= */

.saved-accounts-container {

    margin-top: 15px;

    padding-top: 15px;

    border-top: 1px solid rgba(255,255,255,0.1);

}

.saved-accounts-list {

    display: flex;

    flex-direction: column;

    gap: 8px;

}

.saved-account-item {

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 10px 12px;

    background: rgba(255,255,255,0.08);

    border: 1px solid rgba(255,255,255,0.15);

    border-radius: 10px;

    cursor: pointer;

    transition: all 0.3s ease;

}

.saved-account-item:hover {

    background: rgba(255,255,255,0.12);

    border-color: rgba(0,255,200,0.5);

    transform: translateX(3px);

}

.saved-account-info {

    display: flex;

    align-items: center;

    gap: 10px;

    flex: 1;

}

.saved-account-avatar {

    width: 36px;

    height: 36px;

    border-radius: 50%;

    background: linear-gradient(135deg, #00ffbf, #00d4aa);

    display: flex;

    align-items: center;

    justify-content: center;

    color: #000;

    font-weight: 700;

    font-size: 14px;

    flex-shrink: 0;

}

.saved-account-details {

    flex: 1;

    min-width: 0;

}

.saved-account-name {

    color: #fff;

    font-weight: 600;

    font-size: 14px;

    margin: 0;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

}

.saved-account-email {

    color: rgba(255,255,255,0.7);

    font-size: 12px;

    margin: 0;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

}

.saved-account-actions {

    display: flex;

    align-items: center;

    gap: 5px;

}

.btn-remove-account {

    background: transparent;

    border: none;

    color: rgba(255,255,255,0.6);

    padding: 5px 8px;

    border-radius: 6px;

    cursor: pointer;

    transition: all 0.2s ease;

    display: flex;

    align-items: center;

    justify-content: center;

}

.btn-remove-account:hover {

    background: rgba(255,0,0,0.2);

    color: #ff4444;

}

    width: 26px;

    height: 26px;

}

/* ========================= FORM GROUP MODERN ========================= */
.form-group-modern {
  margin-bottom: 1.25rem;
  animation: fadeInUp 0.4s ease-out backwards;
}

.form-group-modern:nth-child(1) { animation-delay: 0.1s; }
.form-group-modern:nth-child(2) { animation-delay: 0.15s; }
.form-group-modern:nth-child(3) { animation-delay: 0.2s; }
.form-group-modern:nth-child(4) { animation-delay: 0.25s; }
.form-group-modern:nth-child(5) { animation-delay: 0.3s; }
.form-group-modern:nth-child(6) { animation-delay: 0.35s; }
.form-group-modern:nth-child(7) { animation-delay: 0.4s; }
.form-group-modern:nth-child(8) { animation-delay: 0.45s; }
.form-group-modern:nth-child(9) { animation-delay: 0.5s; }

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(15px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.form-label-modern {
  font-weight: 600;
  font-size: 14px;
  margin-bottom: 10px;
  display: block;
  transition: all 0.3s ease;
}

.form-label-modern:hover {
  transform: translateX(3px);
}

.form-label-modern i {
  font-size: 16px;
  color: #00ffbf;
  transition: all 0.3s ease;
}

.form-label-modern:hover i {
  transform: scale(1.1);
  color: #8affee;
}

/* ========================= INPUT WRAPPER ========================= */
.input-wrapper-modern {
  position: relative;
  display: flex;
  align-items: center;
}

.form-control-modern {
  background: rgba(255,255,255,0.08) !important;
  border: 1px solid rgba(255,255,255,0.25);
  color: #fff !important;
  border-radius: 12px;
  height: 48px;
  padding: 12px 16px 12px 45px;
  width: 100%;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  font-size: 15px;
  transform: translateY(0);
}

.form-control-modern:hover {
  background: rgba(255,255,255,0.1) !important;
  border-color: rgba(255,255,255,0.35);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,255,191,0.1);
}

/* Select dropdown - màu xám để chữ dễ đọc */
select.form-control-modern {
  background: rgba(100,100,100,0.4) !important;
  color: #fff !important;
}

select.form-control-modern option {
  background: rgba(60,60,60,0.95) !important;
  color: #fff !important;
  padding: 10px;
}

select.form-control-modern:focus {
  background: rgba(120,120,120,0.5) !important;
}

/* Nếu có nút location, thêm padding bên phải */
.input-wrapper-modern:has(.btn-location) .form-control-modern {
  padding-right: 55px;
}

.form-control-modern::placeholder {
  color: rgba(255,255,255,0.5);
}

.form-control-modern:focus {
  background: rgba(255,255,255,0.12) !important;
  border-color: #00ffbf;
  box-shadow: 0 0 0 4px rgba(0,255,191,0.2), 0 8px 20px rgba(0,255,191,0.15);
  outline: none;
  transform: translateY(-2px) scale(1.01);
}

/* Xử lý autofill - tránh màu trắng làm mất form */
.form-control-modern:-webkit-autofill,
.form-control-modern:-webkit-autofill:hover,
.form-control-modern:-webkit-autofill:focus,
.form-control-modern:-webkit-autofill:active {
  -webkit-box-shadow: 0 0 0 30px rgba(100,100,100,0.3) inset !important;
  -webkit-text-fill-color: #fff !important;
  background: rgba(100,100,100,0.3) !important;
  border-color: rgba(255,255,255,0.25) !important;
  transition: background-color 5000s ease-in-out 0s;
}

/* Select khi focus cũng dùng màu xám */
select.form-control-modern:focus {
  background: rgba(120,120,120,0.5) !important;
}

.form-control-modern.is-invalid {
  border-color: #ff4444;
  box-shadow: 0 0 0 4px rgba(255,68,68,0.2);
}

.input-icon {
  position: absolute;
  left: 16px;
  color: rgba(255,255,255,0.6);
  font-size: 18px;
  pointer-events: none;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 1;
}

.input-wrapper-modern:hover .input-icon {
  color: rgba(255,255,255,0.8);
  transform: scale(1.1);
}

.form-control-modern:focus + .input-icon,
.input-wrapper-modern:has(.form-control-modern:focus) .input-icon {
  color: #00ffbf;
  transform: scale(1.15);
  text-shadow: 0 0 8px rgba(0,255,191,0.5);
}

/* ========================= PASSWORD TOGGLE ========================= */
.password-toggle {
  position: absolute;
  right: 16px;
  background: none;
  border: none;
  color: rgba(255,255,255,0.6);
  font-size: 18px;
  cursor: pointer;
  padding: 0;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.3s ease;
  z-index: 1;
}

.password-toggle:hover {
  color: #00ffbf;
}

/* ========================= LOCATION BUTTON ========================= */
.btn-location {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0,255,191,0.1);
  border: 1px solid rgba(0,255,191,0.2);
  border-radius: 8px;
  color: rgba(255,255,255,0.7);
  font-size: 18px;
  cursor: pointer;
  padding: 6px;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 2;
}

.btn-location:hover {
  background: rgba(0,255,191,0.2);
  border-color: #00ffbf;
  color: #00ffbf;
  transform: translateY(-50%) scale(1.1);
  box-shadow: 0 4px 12px rgba(0,255,191,0.3);
}

.btn-location:active {
  transform: translateY(-50%) scale(0.95);
}

/* ========================= SEND OTP BUTTON ========================= */
.btn-send-otp {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: rgba(255,255,255,0.6);
  font-size: 16px;
  cursor: pointer;
  padding: 4px 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  z-index: 2;
  border-radius: 4px;
}

.btn-send-otp:hover {
  color: #00ffbf;
  background: rgba(0,255,191,0.1);
}

.btn-send-otp:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-send-otp.loading {
  pointer-events: none;
}

.input-wrapper-modern:has(.btn-send-otp) .form-control-modern {
  padding-right: 80px;
}

/* ========================= OTP INPUT CONTAINER ========================= */
.otp-input-container {
  display: flex;
  gap: 10px;
  justify-content: center;
  margin-bottom: 10px;
}

.otp-input {
  width: 50px;
  height: 60px;
  text-align: center;
  font-size: 24px;
  font-weight: bold;
  border: 2px solid rgba(255,255,255,0.3);
  background: rgba(255,255,255,0.1);
  color: white;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.otp-input:focus {
  outline: none;
  border-color: #00ffbf;
  background: rgba(255,255,255,0.15);
  box-shadow: 0 0 0 4px rgba(0,255,191,0.2);
}

.otp-input:invalid {
  border-color: #ff4444;
}

.btn-location:hover {
  color: #00ffbf;
  transform: translateY(-50%) scale(1.15);
}

.btn-location:active {
  transform: translateY(-50%) scale(0.95);
}

.btn-location.loading {
  pointer-events: none;
  opacity: 0.6;
}

.btn-location.loading i {
  animation: spin 1s linear infinite;
}

/* ========================= SEND OTP BUTTON ========================= */
.btn-send-otp {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: rgba(255,255,255,0.6);
  font-size: 16px;
  cursor: pointer;
  padding: 4px 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  z-index: 2;
  border-radius: 4px;
}

.btn-send-otp:hover {
  color: #00ffbf;
  background: rgba(0,255,191,0.1);
}

.btn-send-otp:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-send-otp.loading {
  pointer-events: none;
}

.input-wrapper-modern:has(.btn-send-otp) .form-control-modern {
  padding-right: 80px;
}

/* ========================= OTP INPUT CONTAINER ========================= */
.otp-input-container {
  display: flex;
  gap: 10px;
  justify-content: center;
  margin-bottom: 10px;
}

.otp-input {
  width: 50px;
  height: 60px;
  text-align: center;
  font-size: 24px;
  font-weight: bold;
  border: 2px solid rgba(255,255,255,0.3);
  background: rgba(255,255,255,0.1);
  color: white;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.otp-input:focus {
  outline: none;
  border-color: #00ffbf;
  background: rgba(255,255,255,0.15);
  box-shadow: 0 0 0 4px rgba(0,255,191,0.2);
}

.otp-input:invalid {
  border-color: #ff4444;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* ========================= FILE INPUT ========================= */
.file-input-wrapper-modern {
  position: relative;
}

.file-input-wrapper-modern input[type="file"] {
  position: absolute;
  opacity: 0;
  width: 0;
  height: 0;
}

.file-input-label-modern {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 20px;
  background: rgba(255,255,255,0.1);
  border: 2px dashed rgba(255,255,255,0.3);
  border-radius: 12px;
  color: #fff;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 600;
  font-size: 14px;
}

.file-input-label-modern:hover {
  background: rgba(255,255,255,0.15);
  border-color: #00ffbf;
  color: #00ffbf;
}

.profile-preview {
  margin-top: 12px;
  position: relative;
  display: inline-block;
}

.profile-preview img {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 12px;
  border: 2px solid rgba(255,255,255,0.3);
}

.remove-preview {
  position: absolute;
  top: -8px;
  right: -8px;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #ff4444;
  border: none;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 12px;
  transition: all 0.3s ease;
}

.remove-preview:hover {
  transform: scale(1.1);
  background: #cc0000;
}

/* ========================= ERROR MESSAGE ========================= */
.error-message {
  color: #ff6b6b;
  font-size: 13px;
  margin-top: 6px;
  display: none;
  padding: 6px 10px;
  background: rgba(255, 107, 107, 0.1);
  border-left: 3px solid #ff6b6b;
  border-radius: 6px;
  animation: slideDown 0.3s ease-out;
  font-weight: 500;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
    max-height: 0;
  }
  to {
    opacity: 1;
    transform: translateY(0);
    max-height: 100px;
  }
}

.error-message.show {
  display: block;
}

.alert-danger {
  background: rgba(255,68,68,0.15);
  border: 1px solid rgba(255,68,68,0.5);
  color: #ffcccc;
  border-radius: 12px;
  padding: 12px 16px;
}

.alert-danger i {
  color: #ff4444;
}

/* ========================= BUTTON LOADING ========================= */
.btn-loading {
  display: inline-flex;
  align-items: center;
}

.btn-loading .spinner-border-sm {
  width: 16px;
  height: 16px;
  border-width: 2px;
}

/* ========================= MODAL ANIMATIONS ========================= */
.modal.fade .modal-dialog {
  transition: transform 0.3s ease-out, opacity 0.3s ease-out;
  transform: translateY(-50px);
  opacity: 0;
}

.modal.show .modal-dialog {
  transform: translateY(0);
  opacity: 1;
}

.modal-backdrop {
  transition: opacity 0.3s ease;
}

/* ========================= RESPONSIVE ========================= */
@media (max-width: 768px) {
  .form-control-modern {
    height: 44px;
    padding: 10px 14px 10px 40px;
    font-size: 14px;
  }
  
  .input-icon {
    font-size: 16px;
    left: 14px;
  }
}

</style>

<script>
// Password toggle function
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

// Profile image preview
// Profile image functions removed - using auto-generated avatars from first letter

// Password match validation và các validation khác
document.addEventListener('DOMContentLoaded', function() {
  const registerPass = document.getElementById('register_pass');
  const registerCpass = document.getElementById('register_cpass');
  const matchError = document.getElementById('password-match-error');
  
  if (registerPass && registerCpass && matchError) {
    function checkPasswordMatch() {
      if (registerCpass.value && registerPass.value !== registerCpass.value) {
        matchError.textContent = 'Mật khẩu không khớp';
        matchError.classList.add('show');
        registerCpass.classList.add('is-invalid');
      } else {
        matchError.classList.remove('show');
        registerCpass.classList.remove('is-invalid');
      }
    }
    
    registerCpass.addEventListener('input', checkPasswordMatch);
    registerPass.addEventListener('input', checkPasswordMatch);
  }
  
  // Validation số điện thoại Việt Nam
  const phoneInput = document.getElementById('register_phonenum');
  const phoneError = document.getElementById('phonenum_error');
  
  if (phoneInput && phoneError) {
    function validatePhone() {
      const phone = phoneInput.value.replace(/[^0-9]/g, ''); // Loại bỏ ký tự không phải số
      
      if (phone && !/^0[0-9]{9}$/.test(phone)) {
        phoneError.textContent = 'Số điện thoại phải bắt đầu bằng 0 và có 10 số';
        phoneError.classList.add('show');
        phoneInput.classList.add('is-invalid');
        return false;
      } else {
        phoneError.classList.remove('show');
        phoneInput.classList.remove('is-invalid');
        // Tự động format số điện thoại
        if (phone && phone.length === 10) {
          phoneInput.value = phone;
        }
        return true;
      }
    }
    
    phoneInput.addEventListener('input', function() {
      // Chỉ cho phép nhập số
      this.value = this.value.replace(/[^0-9]/g, '');
      validatePhone();
    });
    
    phoneInput.addEventListener('blur', validatePhone);
  }
  
  // Validation ngày sinh (phải ít nhất 18 tuổi)
  const dobInput = document.getElementById('register_dob');
  const dobError = document.getElementById('dob_error');
  
  if (dobInput && dobError) {
    // Set max date là 18 năm trước
    const today = new Date();
    const maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
    dobInput.setAttribute('max', maxDate.toISOString().split('T')[0]);
    
    function validateAge() {
      if (dobInput.value) {
        const dob = new Date(dobInput.value);
        const today = new Date();
        const age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();
        const dayDiff = today.getDate() - dob.getDate();
        
        const actualAge = (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) ? age - 1 : age;
        
        if (actualAge < 18) {
          dobError.textContent = 'Bạn phải ít nhất 18 tuổi để đăng ký';
          dobError.classList.add('show');
          dobInput.classList.add('is-invalid');
          return false;
        } else {
          dobError.classList.remove('show');
          dobInput.classList.remove('is-invalid');
          return true;
        }
      }
      return true;
    }
    
    dobInput.addEventListener('change', validateAge);
    dobInput.addEventListener('blur', validateAge);
  }
  
  // Loading states for forms
  const loginForm = document.getElementById('login-form');
  const registerForm = document.getElementById('register-form');
  const loginBtn = document.getElementById('login-submit-btn');
  const registerBtn = document.getElementById('register-submit-btn');
  
  if (loginForm && loginBtn) {
    loginForm.addEventListener('submit', function() {
      const btnText = loginBtn.querySelector('.btn-text');
      const btnLoading = loginBtn.querySelector('.btn-loading');
      if (btnText && btnLoading) {
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
        loginBtn.disabled = true;
      }
    });
  }
  
  if (registerForm && registerBtn) {
    registerForm.addEventListener('submit', function() {
      const btnText = registerBtn.querySelector('.btn-text');
      const btnLoading = registerBtn.querySelector('.btn-loading');
      if (btnText && btnLoading) {
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
        registerBtn.disabled = true;
      }
    });
  }
  
  // Reset loading state when modal is closed
  const loginModal = document.getElementById('loginModal');
  const registerModal = document.getElementById('registerModal');
  
  if (loginModal) {
    loginModal.addEventListener('hidden.bs.modal', function() {
      if (loginBtn) {
        const btnText = loginBtn.querySelector('.btn-text');
        const btnLoading = loginBtn.querySelector('.btn-loading');
        if (btnText && btnLoading) {
          btnText.classList.remove('d-none');
          btnLoading.classList.add('d-none');
          loginBtn.disabled = false;
        }
      }
      // Clear form
      if (loginForm) loginForm.reset();
      // Clear errors
      const errorAlert = document.getElementById('login-error-alert');
      if (errorAlert) errorAlert.classList.add('d-none');
    });
  }
  
  if (registerModal) {
    registerModal.addEventListener('hidden.bs.modal', function() {
      if (registerBtn) {
        const btnText = registerBtn.querySelector('.btn-text');
        const btnLoading = registerBtn.querySelector('.btn-loading');
        if (btnText && btnLoading) {
          btnText.classList.remove('d-none');
          btnLoading.classList.add('d-none');
          registerBtn.disabled = false;
        }
      }
      // Clear form
      if (registerForm) registerForm.reset();
      // Clear preview
      // Profile preview removed - using auto-generated avatars
      // Clear errors
      const errorAlert = document.getElementById('register-error-alert');
      if (errorAlert) errorAlert.classList.add('d-none');
    });
  }
});

// ========================= QUẢN LÝ TÀI KHOẢN ĐÃ LƯU =========================

// Mã hóa đơn giản mật khẩu (Base64 - chỉ để che mắt, không phải bảo mật thực sự)
function simpleEncode(str) {
  try {
    return btoa(unescape(encodeURIComponent(str)));
  } catch (e) {
    return str;
  }
}

function simpleDecode(str) {
  try {
    return decodeURIComponent(escape(atob(str)));
  } catch (e) {
    return str;
  }
}

// Lưu tài khoản vào localStorage (bao gồm mật khẩu đã mã hóa)
function saveAccountToLocal(email_mob, password, name, profile, isGoogle) {
  try {
    let savedAccounts = JSON.parse(localStorage.getItem('saved_accounts') || '[]');
    
    // Kiểm tra xem tài khoản đã tồn tại chưa
    const existingIndex = savedAccounts.findIndex(acc => acc.email_mob === email_mob);
    
    const accountData = {
      email_mob: email_mob,
      password: (password && !isGoogle) ? simpleEncode(password) : '', // Mã hóa mật khẩu (không lưu cho Google)
      name: name || email_mob.split('@')[0],
      profile: profile || '',
      isGoogle: isGoogle || false, // Đánh dấu tài khoản Google
      savedAt: new Date().toISOString()
    };
    
    if (existingIndex >= 0) {
      // Cập nhật tài khoản đã tồn tại
      savedAccounts[existingIndex] = accountData;
    } else {
      // Thêm tài khoản mới (giới hạn tối đa 5 tài khoản)
      if (savedAccounts.length >= 5) {
        savedAccounts.shift(); // Xóa tài khoản cũ nhất
      }
      savedAccounts.push(accountData);
    }
    
    localStorage.setItem('saved_accounts', JSON.stringify(savedAccounts));
    return true;
  } catch (e) {
    console.error('Lỗi khi lưu tài khoản:', e);
    return false;
  }
}

// Load danh sách tài khoản đã lưu
function loadSavedAccounts() {
  try {
    const savedAccounts = JSON.parse(localStorage.getItem('saved_accounts') || '[]');
    const container = document.getElementById('saved-accounts-section');
    const list = document.getElementById('saved-accounts-list');
    
    if (!container || !list) return;
    
    if (savedAccounts.length === 0) {
      container.style.display = 'none';
      return;
    }
    
    container.style.display = 'block';
    list.innerHTML = '';
    
    savedAccounts.forEach((account, index) => {
      const item = document.createElement('div');
      item.className = 'saved-account-item';
      item.setAttribute('data-email-mob', account.email_mob);
      
      // Lấy chữ cái đầu để hiển thị avatar
      const initial = account.name ? account.name.charAt(0).toUpperCase() : account.email_mob.charAt(0).toUpperCase();
      
      // Xử lý đường dẫn ảnh profile
      let profileImg = '';
      if (account.profile) {
        // Nếu profile đã có đường dẫn đầy đủ (bắt đầu bằng http hoặc /)
        if (account.profile.startsWith('http') || account.profile.startsWith('/') || account.profile.startsWith('images/')) {
          profileImg = account.profile;
        } else {
          // Nếu chỉ là tên file, thêm đường dẫn
          profileImg = 'images/users/' + account.profile;
        }
      }
      
      // Hiển thị badge Google nếu là tài khoản Google
      const googleBadge = account.isGoogle ? '<span class="badge bg-danger ms-1" style="font-size:9px;padding:2px 4px;">G</span>' : '';
      
      // Tạo event handler để đăng nhập khi click vào item (trừ nút xóa)
      item.addEventListener('click', function(e) {
        // Nếu click vào nút xóa, không đăng nhập
        if (e.target.closest('.btn-remove-account')) {
          return;
        }
        // Đăng nhập tự động
        loginWithSavedAccount(account.email_mob);
      });
      
      item.innerHTML = `
        <div class="saved-account-info">
          <div class="saved-account-avatar">
            ${profileImg ? `<img src="${profileImg}" alt="${account.name}" onerror="this.parentElement.innerHTML='${initial}'" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">` : initial}
          </div>
          <div class="saved-account-details">
            <div class="saved-account-name">${account.name || account.email_mob}${googleBadge}</div>
            <div class="saved-account-email">${account.email_mob}</div>
          </div>
        </div>
        <div class="saved-account-actions">
          <button type="button" class="btn-remove-account" onclick="removeSavedAccount('${account.email_mob.replace(/'/g, "\\'")}', event)" title="Xóa tài khoản">
            <i class="bi bi-x-circle"></i>
          </button>
        </div>
      `;
      
      list.appendChild(item);
    });
  } catch (e) {
    console.error('Lỗi khi load tài khoản đã lưu:', e);
  }
}

// Đăng nhập với tài khoản đã lưu - tự động đăng nhập không cần nhập mật khẩu
function loginWithSavedAccount(email_mob) {
  const loginForm = document.getElementById('login-form');
  if (!loginForm) return;
  
  // Tìm tài khoản trong localStorage
  try {
    const savedAccounts = JSON.parse(localStorage.getItem('saved_accounts') || '[]');
    const account = savedAccounts.find(acc => acc.email_mob === email_mob);
    
    if (!account) {
      if (typeof showToast === 'function') {
        showToast('error', 'Không tìm thấy tài khoản!', 2000);
      }
      return;
    }
    
    // Nếu là tài khoản Google, redirect đến Google login
    if (account.isGoogle) {
      if (typeof showToast === 'function') {
        showToast('info', 'Đang chuyển đến Google...', 1500);
      }
      setTimeout(() => {
        window.location.href = 'google-login.php';
      }, 300);
      return;
    }
    
    // Nếu không có mật khẩu đã lưu, yêu cầu nhập mật khẩu
    if (!account.password || account.password === '') {
      const emailInput = document.getElementById('email_mob');
      const passwordInput = document.getElementById('login_pass');
      
      if (emailInput) {
        emailInput.value = email_mob;
      }
      
      if (passwordInput) {
        setTimeout(() => {
          passwordInput.focus();
        }, 100);
      }
      
      if (typeof showToast === 'function') {
        showToast('info', 'Vui lòng nhập mật khẩu', 2000);
      }
      return;
    }
    
    // Giải mã mật khẩu
    const password = simpleDecode(account.password);
    
    if (!password || password === '') {
      if (typeof showToast === 'function') {
        showToast('error', 'Không thể lấy mật khẩu đã lưu. Vui lòng nhập lại mật khẩu.', 3000);
      }
      const passwordInput = document.getElementById('login_pass');
      if (passwordInput) {
        passwordInput.focus();
      }
      return;
    }
    
    // Hiển thị thông báo đang đăng nhập
    if (typeof showToast === 'function') {
      showToast('info', 'Đang đăng nhập...', 2000);
    }
    
    // Tự động submit form đăng nhập
    const data = new FormData();
    data.append('email_mob', email_mob);
    data.append('pass', password);
    data.append('login', '');
    
    // Hiển thị loading
    const loginBtn = document.getElementById('login-submit-btn');
    if (loginBtn) {
      const btnText = loginBtn.querySelector('.btn-text');
      const btnLoading = loginBtn.querySelector('.btn-loading');
      if (btnText && btnLoading) {
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
        loginBtn.disabled = true;
      }
    }
    
    fetch('ajax/login_register.php', { method: 'POST', body: data })
      .then(res => res.text())
      .then(res => {
        if (res === 'login_success') {
          if (typeof showToast === 'function') {
            showToast('success', 'Đăng nhập thành công!', 2000);
          }
          // Đóng modal trước khi reload
          const loginModalEl = document.getElementById('loginModal');
          if (loginModalEl) {
            const loginModal = bootstrap.Modal.getInstance(loginModalEl);
            if (loginModal) {
              loginModal.hide();
            }
          }
          setTimeout(() => {
            location.reload();
          }, 500);
        } else {
          // Nếu đăng nhập thất bại, yêu cầu nhập lại mật khẩu
          const emailInput = document.getElementById('email_mob');
          const passwordInput = document.getElementById('login_pass');
          
          if (emailInput) {
            emailInput.value = email_mob;
          }
          
          if (passwordInput) {
            passwordInput.value = '';
            passwordInput.focus();
            passwordInput.required = true;
          }
          
          if (typeof showToast === 'function') {
            let errorMsg = 'Mật khẩu đã thay đổi, vui lòng nhập lại';
            if (res === 'invalid_password') {
              errorMsg = 'Mật khẩu không đúng, vui lòng nhập lại';
            } else if (res === 'invalid_email_mob') {
              errorMsg = 'Email không tồn tại';
            }
            // Chỉ hiển thị alert trong modal, không dùng toast
            const errorAlert = document.getElementById('login-error-alert');
            const errorText = document.getElementById('login-error-text');
            if (errorAlert && errorText) {
              errorText.textContent = errorMsg;
              errorAlert.classList.remove('d-none');
              errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
          }
        }
        
        // Reset loading
        if (loginBtn) {
          const btnText = loginBtn.querySelector('.btn-text');
          const btnLoading = loginBtn.querySelector('.btn-loading');
          if (btnText && btnLoading) {
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
            loginBtn.disabled = false;
          }
        }
      })
      .catch(err => {
        console.error('Lỗi đăng nhập:', err);
        
        // Chỉ hiển thị 1 thông báo - alert trong modal
        const errorAlert = document.getElementById('login-error-alert');
        const errorText = document.getElementById('login-error-text');
        if (errorAlert && errorText) {
          errorText.textContent = 'Có lỗi xảy ra khi đăng nhập. Vui lòng thử lại!';
          errorAlert.classList.remove('d-none');
          errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
        
        // Reset loading
        if (loginBtn) {
          const btnText = loginBtn.querySelector('.btn-text');
          const btnLoading = loginBtn.querySelector('.btn-loading');
          if (btnText && btnLoading) {
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
            loginBtn.disabled = false;
          }
        }
      });
  } catch (e) {
    console.error('Lỗi:', e);
    if (typeof showToast === 'function') {
      showToast('error', 'Có lỗi xảy ra', 3000);
    }
  }
}

// Xóa tài khoản đã lưu
function removeSavedAccount(email_mob, event) {
  if (event) {
    event.stopPropagation(); // Ngăn click vào item
  }
  
  if (!confirm('Bạn có chắc chắn muốn xóa tài khoản này khỏi danh sách đã lưu?')) {
    return;
  }
  
  try {
    let savedAccounts = JSON.parse(localStorage.getItem('saved_accounts') || '[]');
    savedAccounts = savedAccounts.filter(acc => acc.email_mob !== email_mob);
    localStorage.setItem('saved_accounts', JSON.stringify(savedAccounts));
    
    // Reload danh sách
    loadSavedAccounts();
    
    if (typeof showToast === 'function') {
      showToast('success', 'Đã xóa tài khoản khỏi danh sách', 2000);
    }
  } catch (e) {
    console.error('Lỗi khi xóa tài khoản:', e);
  }
}

// Load danh sách khi mở modal
document.addEventListener('DOMContentLoaded', function() {
  const loginModal = document.getElementById('loginModal');
  if (loginModal) {
    loginModal.addEventListener('shown.bs.modal', function() {
      loadSavedAccounts();
    });
  }
});

// ========================= LẤY VỊ TRÍ HIỆN TẠI =========================

function getCurrentLocation() {
  const addressInput = document.getElementById('register_address');
  const btnLocation = document.getElementById('btn-get-location');
  
  if (!addressInput || !btnLocation) return;
  
  // Hiển thị loading
  btnLocation.classList.add('loading');
  btnLocation.disabled = true;
  
  // Kiểm tra xem browser có hỗ trợ Geolocation không
  if (!navigator.geolocation) {
    if (typeof showToast === 'function') {
      showToast('error', 'Trình duyệt không hỗ trợ lấy vị trí', 3000);
    } else {
      alert('Trình duyệt không hỗ trợ lấy vị trí');
    }
    btnLocation.classList.remove('loading');
    btnLocation.disabled = false;
    return;
  }
  
  // Lấy vị trí hiện tại
  navigator.geolocation.getCurrentPosition(
    function(position) {
      const lat = position.coords.latitude;
      const lng = position.coords.longitude;
      
      // Sử dụng reverse geocoding để lấy địa chỉ
      // Sử dụng Nominatim (OpenStreetMap) - miễn phí, không cần API key
      // Thêm User-Agent header (yêu cầu của Nominatim)
      fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language=vi`, {
        headers: {
          'User-Agent': 'VinhLongHotel/1.0'
        }
      })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          let fullAddress = '';
          
          // Ưu tiên sử dụng display_name (thường có địa chỉ đầy đủ nhất)
          if (data && data.display_name) {
            fullAddress = data.display_name;
          } else if (data && data.address) {
            // Tạo địa chỉ từ các thành phần
            const addressParts = [];
            
            // Số nhà
            if (data.address.house_number) {
              addressParts.push(data.address.house_number);
            }
            
            // Tên đường
            if (data.address.road) {
              addressParts.push(data.address.road);
            } else if (data.address.street) {
              addressParts.push(data.address.street);
            }
            
            // Phường/Xã
            if (data.address.suburb) {
              addressParts.push(data.address.suburb);
            } else if (data.address.village) {
              addressParts.push(data.address.village);
            } else if (data.address.town) {
              addressParts.push(data.address.town);
            } else if (data.address.neighbourhood) {
              addressParts.push(data.address.neighbourhood);
            }
            
            // Quận/Huyện
            if (data.address.city_district) {
              addressParts.push(data.address.city_district);
            } else if (data.address.district) {
              addressParts.push(data.address.district);
            } else if (data.address.county) {
              addressParts.push(data.address.county);
            }
            
            // Tỉnh/Thành phố
            if (data.address.state) {
              addressParts.push(data.address.state);
            } else if (data.address.city) {
              addressParts.push(data.address.city);
            } else if (data.address.province) {
              addressParts.push(data.address.province);
            }
            
            // Quốc gia (chỉ thêm nếu không phải Việt Nam hoặc để trống)
            // if (data.address.country && data.address.country !== 'Việt Nam' && data.address.country !== 'Vietnam') {
            //   addressParts.push(data.address.country);
            // }
            
            fullAddress = addressParts.join(', ');
          }
          
          // Nếu vẫn không có địa chỉ, thử dùng API khác hoặc yêu cầu nhập thủ công
          if (!fullAddress || fullAddress.trim() === '') {
            // Thử dùng Google Geocoding API (nếu có) hoặc yêu cầu nhập thủ công
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
          
          btnLocation.classList.remove('loading');
          btnLocation.disabled = false;
        })
        .catch(error => {
          console.error('Lỗi khi lấy địa chỉ:', error);
          
          // Thử dùng API dự phòng hoặc yêu cầu nhập thủ công
          if (typeof showToast === 'function') {
            showToast('error', 'Không thể lấy địa chỉ tự động. Vui lòng nhập địa chỉ thủ công.', 4000);
          }
          addressInput.value = '';
          addressInput.focus();
          
          btnLocation.classList.remove('loading');
          btnLocation.disabled = false;
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
      
      btnLocation.classList.remove('loading');
      btnLocation.disabled = false;
    },
    {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 0
    }
  );
}

// ========================= XỬ LÝ OTP =========================

let otpTimer = null;
let otpTimeLeft = 600; // 10 phút = 600 giây

// Gửi mã OTP
function sendOTP() {
  const emailInput = document.getElementById('register_email');
  const nameInput = document.querySelector('input[name="name"]');
  const btnSendOTP = document.getElementById('btn-send-otp');
  const emailError = document.getElementById('email_error');
  const emailVerifiedBadge = document.getElementById('email-verified-badge');
  
  if (!emailInput || !btnSendOTP) return;
  
  const email = emailInput.value.trim();
  const name = nameInput ? nameInput.value.trim() : '';
  
  // Validate email
  if (!email || !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
    if (emailError) {
      emailError.textContent = 'Vui lòng nhập email hợp lệ';
      emailError.classList.add('show');
    }
    emailInput.focus();
    return;
  }
  
  // Hiển thị loading
  btnSendOTP.classList.add('loading');
  btnSendOTP.disabled = true;
  if (emailError) {
    emailError.classList.remove('show');
  }
  if (emailVerifiedBadge) {
    emailVerifiedBadge.classList.add('d-none');
  }
  // Hiển thị lại thông báo nhắc nhở khi reset
  const emailReminder = document.getElementById('email-verification-reminder');
  if (emailReminder) {
    emailReminder.classList.remove('d-none');
  }
  
  // ⚠️ LẤY TẤT CẢ THÔNG TIN FORM ĐỂ GỬI CÙNG OTP
  const registerForm = document.getElementById('register-form');
  if (!registerForm) {
    if (typeof showToast === 'function') {
      showToast('error', 'Không tìm thấy form đăng ký!', 3000);
    }
    btnSendOTP.classList.remove('loading');
    btnSendOTP.disabled = false;
    return;
  }
  
  // Lấy tất cả dữ liệu từ form
  const formDataObj = new FormData(registerForm);
  const formData = new FormData();
  formData.append('send_otp', '1');
  formData.append('email', email);
  formData.append('name', formDataObj.get('name') || name);
  formData.append('phonenum', formDataObj.get('phonenum') || '');
  formData.append('address', formDataObj.get('address') || '');
  formData.append('pincode', formDataObj.get('pincode') || '');
  formData.append('dob', formDataObj.get('dob') || '');
  formData.append('pass', formDataObj.get('pass') || '');
  
  fetch('ajax/login_register.php', {
    method: 'POST',
    body: formData
  })
    .then(res => {
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }
      return res.text();
    })
    .then(res => {
      const cleanRes = res.trim();
      console.log('SEND OTP Response:', cleanRes);
      
      if (cleanRes === 'otp_sent') {
        if (typeof showToast === 'function') {
          showToast('success', 'Mã xác thực đã được gửi đến email của bạn!', 3000);
        }
        // Mở modal OTP
        openOTPModal(email);
      } else if (cleanRes === 'email_already') {
        if (emailError) {
          emailError.textContent = 'Email này đã được sử dụng';
          emailError.classList.add('show');
        }
        if (typeof showToast === 'function') {
          showToast('error', 'Email này đã được sử dụng!', 3000);
        }
      } else if (cleanRes === 'invalid_email') {
        if (emailError) {
          emailError.textContent = 'Email không hợp lệ';
          emailError.classList.add('show');
        }
        if (typeof showToast === 'function') {
          showToast('error', 'Email không hợp lệ!', 3000);
        }
      } else if (cleanRes === 'otp_send_failed') {
        if (emailError) {
          emailError.textContent = 'Không thể gửi email. Vui lòng kiểm tra cấu hình email hoặc thử lại sau!';
          emailError.classList.add('show');
        }
        if (typeof showToast === 'function') {
          showToast('error', 'Không thể gửi email. Vui lòng thử lại sau!', 3000);
        }
      } else {
        console.error('Unexpected response:', cleanRes);
        if (emailError) {
          emailError.textContent = 'Có lỗi xảy ra: ' + cleanRes.substring(0, 100);
          emailError.classList.add('show');
        }
        if (typeof showToast === 'function') {
          showToast('error', 'Có lỗi xảy ra. Vui lòng thử lại!', 3000);
        }
      }
      
      btnSendOTP.classList.remove('loading');
      btnSendOTP.disabled = false;
    })
    .catch(err => {
      console.error('Lỗi khi gửi OTP:', err);
      if (emailError) {
        emailError.textContent = 'Lỗi kết nối: ' + err.message;
        emailError.classList.add('show');
      }
      if (typeof showToast === 'function') {
        showToast('error', 'Có lỗi xảy ra. Vui lòng thử lại!', 3000);
      }
      btnSendOTP.classList.remove('loading');
      btnSendOTP.disabled = false;
    });
}

// Mở modal OTP
function openOTPModal(email) {
  console.log('🔵 openOTPModal called with email:', email);
  const otpModal = document.getElementById('otpVerificationModal');
  const otpEmailDisplay = document.getElementById('otp-email-display');
  const otpEmailInput = document.getElementById('otp-email');
  
  if (!otpModal) {
    console.error('❌ OTP Modal not found!');
    if (typeof showToast === 'function') {
      showToast('error', 'Không tìm thấy modal xác thực OTP!', 3000);
    }
    return;
  }
  
  console.log('✅ OTP Modal found, preparing to open...');
  
  if (otpEmailDisplay) {
    otpEmailDisplay.textContent = email;
  }
  if (otpEmailInput) {
    otpEmailInput.value = email;
  }
  
  // Reset OTP inputs
  for (let i = 1; i <= 6; i++) {
    const input = document.getElementById(`otp-input-${i}`);
    if (input) input.value = '';
  }
  
  // Reset timer
  otpTimeLeft = 600;
  updateOTPTimer();
  startOTPTimer();
  
  // Đóng modal đăng ký trước (nếu đang mở)
  const registerModal = document.getElementById('registerModal');
  if (registerModal) {
    const registerBsModal = bootstrap.Modal.getInstance(registerModal);
    if (registerBsModal) {
      registerBsModal.hide();
    }
  }
  
  // Đợi một chút để modal đăng ký đóng hoàn toàn
  setTimeout(() => {
    // Mở modal OTP
    try {
      console.log('🔵 Attempting to open OTP modal...');
      const bsModal = new bootstrap.Modal(otpModal, {
        backdrop: 'static',
        keyboard: false
      });
      bsModal.show();
      console.log('✅ OTP Modal opened successfully');
      
      // Focus vào ô đầu tiên
      setTimeout(() => {
        const firstInput = document.getElementById('otp-input-1');
        if (firstInput) {
          firstInput.focus();
          console.log('✅ Focused on first OTP input');
        } else {
          console.error('❌ First OTP input not found!');
        }
      }, 300);
    } catch (err) {
      console.error('❌ Error opening OTP modal:', err);
      if (typeof showToast === 'function') {
        showToast('error', 'Không thể mở modal xác thực OTP! Vui lòng thử lại.', 3000);
      }
    }
  }, 300);
}

// Xử lý nhập OTP (tự động chuyển ô)
function setupOTPInputs() {
  for (let i = 1; i <= 6; i++) {
    const input = document.getElementById(`otp-input-${i}`);
    if (!input) continue;
    
    input.addEventListener('input', function(e) {
      const value = e.target.value.replace(/[^0-9]/g, '');
      e.target.value = value;
      
      // Chuyển sang ô tiếp theo nếu đã nhập
      if (value && i < 6) {
        const nextInput = document.getElementById(`otp-input-${i + 1}`);
        if (nextInput) nextInput.focus();
      }
      
      // Cập nhật mã OTP đầy đủ
      updateOTPCode();
    });
    
    input.addEventListener('keydown', function(e) {
      // Xóa và quay lại ô trước nếu nhấn Backspace
      if (e.key === 'Backspace' && !e.target.value && i > 1) {
        const prevInput = document.getElementById(`otp-input-${i - 1}`);
        if (prevInput) prevInput.focus();
      }
    });
    
    input.addEventListener('paste', function(e) {
      e.preventDefault();
      const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').substring(0, 6);
      
      for (let j = 0; j < pastedData.length && (i + j) <= 6; j++) {
        const targetInput = document.getElementById(`otp-input-${i + j}`);
        if (targetInput) {
          targetInput.value = pastedData[j];
        }
      }
      
      // Focus vào ô cuối cùng đã điền
      const lastFilled = Math.min(i + pastedData.length - 1, 6);
      const lastInput = document.getElementById(`otp-input-${lastFilled}`);
      if (lastInput) lastInput.focus();
      
      updateOTPCode();
    });
  }
}

// Cập nhật mã OTP đầy đủ
function updateOTPCode() {
  let otpCode = '';
  for (let i = 1; i <= 6; i++) {
    const input = document.getElementById(`otp-input-${i}`);
    if (input) otpCode += input.value;
  }
  
  const otpFullCode = document.getElementById('otp-full-code');
  if (otpFullCode) otpFullCode.value = otpCode;
}

// Xác thực OTP
function verifyOTP() {
  const otpForm = document.getElementById('otp-verification-form');
  const otpSubmitBtn = document.getElementById('otp-submit-btn');
  const otpError = document.getElementById('otp_error');
  const otpFullCode = document.getElementById('otp-full-code');
  const otpEmail = document.getElementById('otp-email');
  
  if (!otpForm || !otpFullCode || !otpEmail) return;
  
  const otp = otpFullCode.value;
  const email = otpEmail.value;
  
  if (otp.length !== 6) {
    if (otpError) {
      otpError.textContent = 'Vui lòng nhập đầy đủ 6 số';
      otpError.classList.add('show');
    }
    return;
  }
  
  // Hiển thị loading
  const btnText = otpSubmitBtn.querySelector('.btn-text');
  const btnLoading = otpSubmitBtn.querySelector('.btn-loading');
  if (btnText && btnLoading) {
    btnText.classList.add('d-none');
    btnLoading.classList.remove('d-none');
    otpSubmitBtn.disabled = true;
  }
  if (otpError) {
    otpError.classList.remove('show');
  }
  
  // Gửi request
  const formData = new FormData();
  formData.append('verify_otp', '1');
  formData.append('otp', otp);
  formData.append('email', email);
  
  // Avatar will be auto-generated from first letter of name on backend
  
  fetch('ajax/login_register.php', {
    method: 'POST',
    body: formData
  })
    .then(res => {
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }
      return res.text();
    })
    .then(res => {
      const cleanRes = res.trim();
      console.log('OTP Verification Response:', cleanRes);
      
      if (cleanRes === 'registration_success') {
        console.log('✅ Registration successful for:', email);
        
        if (typeof showToast === 'function') {
          showToast('success', 'Đăng ký thành công! Đang tự động đăng nhập...', 3000);
        }
        
        // Dừng timer
        stopOTPTimer();
        
        // Đóng modal OTP
        const otpModal = document.getElementById('otpVerificationModal');
        if (otpModal) {
          const bsModal = bootstrap.Modal.getInstance(otpModal);
          if (bsModal) {
            bsModal.hide();
          }
        }
        
        // Đóng modal đăng ký
        const registerModal = document.getElementById('registerModal');
        if (registerModal) {
          const registerBsModal = bootstrap.Modal.getInstance(registerModal);
          if (registerBsModal) {
            registerBsModal.hide();
          }
        }
        
        // Reload trang sau 1 giây để cập nhật trạng thái đăng nhập
        setTimeout(() => {
          location.reload();
        }, 1000);
        
      } else if (cleanRes === 'otp_verified') {
        // Fallback - không nên xảy ra nữa vì đã tự động insert
        console.log('OTP verified but registration not completed');
        if (typeof showToast === 'function') {
          showToast('error', 'Có lỗi xảy ra khi đăng ký. Vui lòng thử lại!', 3000);
        }
        
        // Reset loading
        if (btnText && btnLoading) {
          btnText.classList.remove('d-none');
          btnLoading.classList.add('d-none');
          otpSubmitBtn.disabled = false;
        }
      } else if (cleanRes === 'otp_invalid') {
        if (otpError) {
          otpError.textContent = 'Mã xác thực không đúng';
          otpError.classList.add('show');
        }
        // Xóa tất cả input
        for (let i = 1; i <= 6; i++) {
          const input = document.getElementById(`otp-input-${i}`);
          if (input) input.value = '';
        }
        const firstInput = document.getElementById('otp-input-1');
        if (firstInput) firstInput.focus();
        updateOTPCode();
      } else if (cleanRes === 'otp_expired') {
        if (otpError) {
          otpError.textContent = 'Mã xác thực đã hết hạn. Vui lòng gửi lại mã';
          otpError.classList.add('show');
        }
        if (typeof showToast === 'function') {
          showToast('error', 'Mã xác thực đã hết hạn!', 3000);
        }
      } else if (cleanRes === 'otp_not_found') {
        if (otpError) {
          otpError.textContent = 'Không tìm thấy mã xác thực. Vui lòng gửi lại mã!';
          otpError.classList.add('show');
        }
        if (typeof showToast === 'function') {
          showToast('error', 'Không tìm thấy mã xác thực. Vui lòng gửi lại mã!', 3000);
        }
        
        // Reset loading
        if (btnText && btnLoading) {
          btnText.classList.remove('d-none');
          btnLoading.classList.add('d-none');
          otpSubmitBtn.disabled = false;
        }
      } else if (cleanRes === 'otp_verify_failed') {
        console.error('OTP verification failed - check server logs');
        if (otpError) {
          otpError.textContent = 'Có lỗi xảy ra khi xác thực. Vui lòng thử lại!';
          otpError.classList.add('show');
        }
        if (typeof showToast === 'function') {
          showToast('error', 'Có lỗi xảy ra khi xác thực. Vui lòng thử lại!', 3000);
        }
        
        // Reset loading
        if (btnText && btnLoading) {
          btnText.classList.remove('d-none');
          btnLoading.classList.add('d-none');
          otpSubmitBtn.disabled = false;
        }
      } else if (cleanRes === 'registration_failed') {
        console.error('Registration failed - check server logs');
        if (otpError) {
          otpError.textContent = 'Đăng ký thất bại. Vui lòng thử lại!';
          otpError.classList.add('show');
        }
        if (typeof showToast === 'function') {
          showToast('error', 'Đăng ký thất bại. Vui lòng thử lại!', 3000);
        }
        
        // Reset loading
        if (btnText && btnLoading) {
          btnText.classList.remove('d-none');
          btnLoading.classList.add('d-none');
          otpSubmitBtn.disabled = false;
        }
      } else if (cleanRes === 'missing_required_fields' || cleanRes === 'invalid_phone' || cleanRes === 'age_under_18') {
        console.error('Validation failed:', cleanRes);
        if (otpError) {
          otpError.textContent = 'Thông tin không hợp lệ. Vui lòng kiểm tra lại!';
          otpError.classList.add('show');
        }
        if (typeof showToast === 'function') {
          showToast('error', 'Thông tin không hợp lệ. Vui lòng kiểm tra lại!', 3000);
        }
        
        // Reset loading
        if (btnText && btnLoading) {
          btnText.classList.remove('d-none');
          btnLoading.classList.add('d-none');
          otpSubmitBtn.disabled = false;
        }
      } else if (cleanRes === 'user_already_exists') {
        console.error('User already exists - email or phone already registered');
        if (otpError) {
          otpError.textContent = 'Email hoặc số điện thoại này đã được sử dụng! Vui lòng đăng nhập hoặc dùng email/số điện thoại khác.';
          otpError.classList.add('show');
        }
        if (typeof showToast === 'function') {
          showToast('error', 'Email hoặc số điện thoại này đã được sử dụng!', 5000);
        }
        
        // Đóng modal OTP và mở lại modal đăng ký
        const otpModal = document.getElementById('otpVerificationModal');
        if (otpModal) {
          const bsModal = bootstrap.Modal.getInstance(otpModal);
          if (bsModal) bsModal.hide();
        }
        
        setTimeout(() => {
          const registerModal = document.getElementById('registerModal');
          if (registerModal) {
            const registerBsModal = bootstrap.Modal.getInstance(registerModal) || new bootstrap.Modal(registerModal);
            registerBsModal.show();
          }
        }, 300);
        
        // Reset loading
        if (btnText && btnLoading) {
          btnText.classList.remove('d-none');
          btnLoading.classList.add('d-none');
          otpSubmitBtn.disabled = false;
        }
      } else if (cleanRes === 'register_data_missing') {
        console.error('Register data missing from session - session may have expired');
        if (otpError) {
          otpError.textContent = 'Phiên làm việc đã hết hạn. Vui lòng gửi lại mã OTP!';
          otpError.classList.add('show');
        }
        if (typeof showToast === 'function') {
          showToast('error', 'Phiên làm việc đã hết hạn. Vui lòng gửi lại mã OTP!', 5000);
        }
        
        // Đóng modal OTP và mở lại modal đăng ký
        const otpModal = document.getElementById('otpVerificationModal');
        if (otpModal) {
          const bsModal = bootstrap.Modal.getInstance(otpModal);
          if (bsModal) bsModal.hide();
        }
        
        setTimeout(() => {
          const registerModal = document.getElementById('registerModal');
          if (registerModal) {
            const registerBsModal = bootstrap.Modal.getInstance(registerModal) || new bootstrap.Modal(registerModal);
            registerBsModal.show();
          }
        }, 300);
        
        // Reset loading
        if (btnText && btnLoading) {
          btnText.classList.remove('d-none');
          btnLoading.classList.add('d-none');
          otpSubmitBtn.disabled = false;
        }
      } else {
        console.error('Unexpected response:', cleanRes);
        if (otpError) {
          otpError.textContent = 'Có lỗi xảy ra: ' + cleanRes.substring(0, 100);
          otpError.classList.add('show');
        }
        if (typeof showToast === 'function') {
          showToast('error', 'Có lỗi xảy ra. Vui lòng thử lại!', 3000);
        }
        
        // Reset loading
        if (btnText && btnLoading) {
          btnText.classList.remove('d-none');
          btnLoading.classList.add('d-none');
          otpSubmitBtn.disabled = false;
        }
      }
      
      // Reset loading
      if (btnText && btnLoading) {
        btnText.classList.remove('d-none');
        btnLoading.classList.add('d-none');
        otpSubmitBtn.disabled = false;
      }
    })
    .catch(err => {
      console.error('Lỗi khi xác thực OTP:', err);
      if (typeof showToast === 'function') {
        showToast('error', 'Có lỗi xảy ra. Vui lòng thử lại!', 3000);
      }
      // Reset loading
      if (btnText && btnLoading) {
        btnText.classList.remove('d-none');
        btnLoading.classList.add('d-none');
        otpSubmitBtn.disabled = false;
      }
    });
}

// Timer đếm ngược
function startOTPTimer() {
  stopOTPTimer();
  otpTimer = setInterval(() => {
    otpTimeLeft--;
    updateOTPTimer();
    
    if (otpTimeLeft <= 0) {
      stopOTPTimer();
      const otpError = document.getElementById('otp_error');
      if (otpError) {
        otpError.textContent = 'Mã xác thực đã hết hạn. Vui lòng gửi lại mã';
        otpError.classList.add('show');
      }
    }
  }, 1000);
}

function stopOTPTimer() {
  if (otpTimer) {
    clearInterval(otpTimer);
    otpTimer = null;
  }
}

function updateOTPTimer() {
  const timerEl = document.getElementById('otp-timer');
  if (timerEl) {
    const minutes = Math.floor(otpTimeLeft / 60);
    const seconds = otpTimeLeft % 60;
    timerEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
  }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
  // Nút gửi OTP
  const btnSendOTP = document.getElementById('btn-send-otp');
  if (btnSendOTP) {
    btnSendOTP.addEventListener('click', sendOTP);
  }
  
  // Form xác thực OTP
  const otpForm = document.getElementById('otp-verification-form');
  if (otpForm) {
    otpForm.addEventListener('submit', function(e) {
      e.preventDefault();
      verifyOTP();
    });
  }
  
  // Nút gửi lại OTP
  const btnResendOTP = document.getElementById('btn-resend-otp');
  if (btnResendOTP) {
    btnResendOTP.addEventListener('click', function() {
      const otpEmail = document.getElementById('otp-email');
      if (otpEmail && otpEmail.value) {
        const emailInput = document.getElementById('register_email');
        if (emailInput) {
          emailInput.value = otpEmail.value;
        }
        sendOTP();
      }
    });
  }
  
  // Setup OTP inputs
  setupOTPInputs();
  
  // Reset timer khi đóng modal
  const otpModal = document.getElementById('otpVerificationModal');
  if (otpModal) {
    otpModal.addEventListener('hidden.bs.modal', function() {
      stopOTPTimer();
    });
  }
});

  // JavaScript cho modal quên mật khẩu
  // Mở modal quên mật khẩu
  document.addEventListener('DOMContentLoaded', function() {
    const forgotPasswordLink = document.getElementById('forgot-password-link');
    if(forgotPasswordLink) {
      forgotPasswordLink.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Forgot password link clicked');
        const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
        if(loginModal) {
          loginModal.hide();
        }
        setTimeout(() => {
          const forgotModalEl = document.getElementById('forgotPasswordLoginModal');
          if(forgotModalEl) {
            console.log('Opening forgot password modal');
            const forgotModal = new bootstrap.Modal(forgotModalEl);
            forgotModal.show();
          } else {
            console.error('Forgot password modal not found!');
            alert('Lỗi: Không tìm thấy modal quên mật khẩu. Vui lòng refresh trang và thử lại.');
          }
        }, 300);
      });
    } else {
      console.warn('Forgot password link not found!');
    }
  });

  // Setup OTP inputs cho quên mật khẩu
  function setupForgotPasswordOTPInputsLogin() {
    for(let i = 1; i <= 6; i++) {
      const input = document.getElementById(`forgot-otp-login-${i}`);
      if(input) {
        input.addEventListener('input', function(e) {
          e.target.value = e.target.value.replace(/[^0-9]/g, '');
          if(e.target.value.length === 1 && i < 6) {
            document.getElementById(`forgot-otp-login-${i + 1}`).focus();
          }
          updateForgotOTPCodeLogin();
        });
        input.addEventListener('keydown', function(e) {
          if(e.key === 'Backspace' && !e.target.value && i > 1) {
            document.getElementById(`forgot-otp-login-${i - 1}`).focus();
          }
          updateForgotOTPCodeLogin();
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

  function updateForgotOTPCodeLogin() {
    let otpCode = '';
    for(let i = 1; i <= 6; i++) {
      const input = document.getElementById(`forgot-otp-login-${i}`);
      if (input) otpCode += input.value;
    }
    document.getElementById('forgot-otp-full-code-login').value = otpCode;
  }

  // Gửi OTP quên mật khẩu
  const btnSendForgotOTPLogin = document.getElementById('btn-send-forgot-otp-login');
  if(btnSendForgotOTPLogin) {
    btnSendForgotOTPLogin.addEventListener('click', function() {
      const emailMob = document.getElementById('forgot-email-mob').value.trim();
      const emailError = document.getElementById('forgot-email-error');

      if(!emailMob) {
        if(emailError) {
          emailError.textContent = 'Vui lòng nhập email hoặc số điện thoại!';
          emailError.classList.add('show');
        }
        return;
      }

      btnSendForgotOTPLogin.disabled = true;
      btnSendForgotOTPLogin.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang gửi...';

      const formData = new FormData();
      formData.append('send_forgot_password_otp_login', '');
      formData.append('email_mob', emailMob);

      fetch('ajax/login_register.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.text())
      .then(res => {
        btnSendForgotOTPLogin.disabled = false;
        btnSendForgotOTPLogin.innerHTML = '<i class="bi bi-send-fill me-2"></i>Gửi mã OTP';

        if(res.trim() === 'otp_sent') {
          document.getElementById('forgot-password-step1').style.display = 'none';
          document.getElementById('forgot-password-step2').style.display = 'block';
          setupForgotPasswordOTPInputsLogin();
          setTimeout(() => {
            document.getElementById('forgot-otp-login-1').focus();
          }, 300);
          if(typeof showToast === 'function') {
            showToast('success', '📧 Mã OTP đã được gửi đến email của bạn!', 3000);
          }
        } else if(res.trim() === 'email_not_found') {
          if(emailError) {
            emailError.textContent = 'Email hoặc số điện thoại không tồn tại!';
            emailError.classList.add('show');
          }
        } else {
          if(typeof showToast === 'function') {
            showToast('error', '❌ Không thể gửi mã OTP. Vui lòng thử lại!', 3000);
          }
        }
      })
      .catch(err => {
        console.error('Send forgot password OTP error:', err);
        btnSendForgotOTPLogin.disabled = false;
        btnSendForgotOTPLogin.innerHTML = '<i class="bi bi-send-fill me-2"></i>Gửi mã OTP';
        if(typeof showToast === 'function') {
          showToast('error', '❌ Có lỗi xảy ra. Vui lòng thử lại!', 3000);
        }
      });
    });
  }

  // Xử lý đặt lại mật khẩu
  const btnResetPasswordLogin = document.getElementById('btn-reset-password-login');
  if(btnResetPasswordLogin) {
    btnResetPasswordLogin.addEventListener('click', function() {
      const otpCode = document.getElementById('forgot-otp-full-code-login').value;
      const newPassword = document.getElementById('forgot-new-password-login').value;
      const confirmPassword = document.getElementById('forgot-confirm-password-login').value;
      const emailMob = document.getElementById('forgot-email-mob').value.trim();
      const otpError = document.getElementById('forgot-otp-error-login');

      if(otpError) {
        otpError.style.display = 'none';
      }

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
        }
        return;
      }

      if(newPassword !== confirmPassword) {
        if(typeof showToast === 'function') {
          showToast('error', '❌ Mật khẩu xác nhận không khớp!', 3000);
        }
        return;
      }

      btnResetPasswordLogin.disabled = true;
      btnResetPasswordLogin.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang xử lý...';

      const formData = new FormData();
      formData.append('verify_forgot_password_otp_login', '');
      formData.append('email_mob', emailMob);
      formData.append('otp', otpCode);
      formData.append('new_password', newPassword);

      fetch('ajax/login_register.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.text())
      .then(res => {
        btnResetPasswordLogin.disabled = false;
        btnResetPasswordLogin.innerHTML = '<i class="bi bi-check-circle me-1"></i>Đặt lại mật khẩu';

        if(res.trim() === 'password_reset_success') {
          if(typeof showToast === 'function') {
            showToast('success', '✅ Đặt lại mật khẩu thành công! Vui lòng đăng nhập lại.', 3000);
          }
          const modal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordLoginModal'));
          if(modal) modal.hide();
          setTimeout(() => {
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
          }, 500);
        } else if(res.trim() === 'otp_invalid') {
          if(otpError) {
            otpError.textContent = '❌ Mã xác thực không đúng! Vui lòng kiểm tra lại.';
            otpError.style.display = 'block';
          }
          for(let i = 1; i <= 6; i++) {
            const input = document.getElementById(`forgot-otp-login-${i}`);
            if(input) input.value = '';
          }
          document.getElementById('forgot-otp-login-1').focus();
        } else if(res.trim() === 'otp_expired') {
          if(otpError) {
            otpError.textContent = '⏰ Mã xác thực đã hết hạn. Vui lòng gửi lại mã!';
            otpError.style.display = 'block';
          }
          if(typeof showToast === 'function') {
            showToast('warning', '⏰ Mã xác thực đã hết hạn. Vui lòng gửi lại mã!', 4000);
          }
        } else {
          if(typeof showToast === 'function') {
            showToast('error', '❌ Có lỗi xảy ra. Vui lòng thử lại!', 3000);
          }
        }
      })
      .catch(err => {
        console.error('Reset password error:', err);
        btnResetPasswordLogin.disabled = false;
        btnResetPasswordLogin.innerHTML = '<i class="bi bi-check-circle me-1"></i>Đặt lại mật khẩu';
        if(typeof showToast === 'function') {
          showToast('error', '❌ Có lỗi xảy ra. Vui lòng thử lại!', 3000);
        }
      });
    });
  }

  // Reset form khi modal được hiển thị
  const forgotPasswordLoginModal = document.getElementById('forgotPasswordLoginModal');
  if(forgotPasswordLoginModal) {
    forgotPasswordLoginModal.addEventListener('shown.bs.modal', function() {
      document.getElementById('forgot-password-step1').style.display = 'block';
      document.getElementById('forgot-password-step2').style.display = 'none';
      document.getElementById('forgot-email-mob').value = '';
      for(let i = 1; i <= 6; i++) {
        const input = document.getElementById(`forgot-otp-login-${i}`);
        if(input) input.value = '';
      }
      document.getElementById('forgot-new-password-login').value = '';
      document.getElementById('forgot-confirm-password-login').value = '';
      const emailError = document.getElementById('forgot-email-error');
      if(emailError) {
        emailError.textContent = '';
        emailError.classList.remove('show');
      }
      const otpError = document.getElementById('forgot-otp-error-login');
      if(otpError) {
        otpError.style.display = 'none';
        otpError.textContent = '';
      }
    });
  }

</script>

