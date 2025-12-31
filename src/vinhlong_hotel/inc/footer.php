<!-- ============ FOOTER ============ -->
<div class="footer-hero text-white">
  <div class="container">
    <div class="footer-hero-inner d-flex flex-column flex-lg-row align-items-center gap-4">
      <div class="flex-grow-1 text-center text-lg-start">
        <div class="d-flex align-items-center gap-2 mb-2 justify-content-center justify-content-lg-start">
          <div class="newsletter-icon-wrapper">
            <i class="bi bi-envelope-heart-fill"></i>
          </div>
          <h4 class="fw-bold mb-0 text-uppercase" data-i18n="footer.newsletter.title">Luôn được cập nhật các gợi ý & khuyến mãi mới nhất</h4>
        </div>
        <p class="mb-3 text-white-50" data-i18n="footer.newsletter.description">Nhập email để nhận ưu đãi mới nhất từ <?php echo $settings_r['site_title']; ?>.</p>
        <form id="newsletter-form" class="footer-newsletter d-flex flex-column flex-md-row gap-2">
          <div class="newsletter-input-wrapper position-relative flex-grow-1">
            <i class="bi bi-envelope-fill newsletter-input-icon"></i>
            <input type="email" name="email" class="form-control shadow-none newsletter-input" placeholder="Nhập email của bạn" data-i18n-placeholder="footer.newsletter.placeholder" required>
          </div>
          <button class="btn btn-primary px-4 newsletter-btn" type="submit">
            <span class="btn-text" data-i18n="footer.newsletter.submit">Đăng ký</span>
            <span class="btn-loading d-none">
              <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
              <span data-i18n="footer.newsletter.sending">Đang gửi...</span>
            </span>
          </button>
        </form>
        <small id="newsletter-msg" class="text-white-50 d-block mt-2"></small>
      </div>
      <div class="d-flex flex-column align-items-center gap-2">
        <div class="app-badges text-center">
          <div class="d-flex align-items-center gap-2 mb-2 justify-content-center">
            <i class="bi bi-phone-fill app-icon"></i>
            <p class="app-badges-text text-uppercase fw-bold mb-0" data-i18n="footer.app.title">Có chuyến đi mơ ước của bạn trong tầm tay</p>
          </div>
          <p class="text-white-50 small mb-3" data-i18n="footer.app.description">Tải ứng dụng và nhận ưu đãi từ <?php echo $settings_r['site_title']; ?>.</p>
          <div class="d-flex justify-content-center gap-2">
            <a href="#" class="store-badge-link">
              <img src="images/app/app.svg" class="store-badge" alt="App Store">
            </a>
            <a href="#" class="store-badge-link">
              <img src="images/app/gg.svg" class="store-badge" alt="Google Play">
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<footer class="footer-wrapper text-white pt-5 pb-4">
  <div class="container">
    <div class="row g-4 align-items-start">

      <!-- Brand & payment -->
      <div class="col-lg-4 col-md-12">
        <div class="d-flex align-items-center gap-3 mb-3">
          <div>
            <h4 class="m-0"><?php echo $settings_r['site_title']; ?></h4>
            <small class="text-white-50" data-i18n="footer.exploreBook">Khám phá & đặt phòng Vĩnh Long</small>
          </div>
        </div>
        <div class="d-flex flex-wrap gap-2 mb-3">
          <span class="trust-pill"><i class="bi bi-shield-lock me-1"></i><span data-i18n="footer.trust.security">Bảo mật</span></span>
          <span class="trust-pill"><i class="bi bi-headset me-1"></i><span data-i18n="footer.trust.support">Hỗ trợ 24/7</span></span>
          <span class="trust-pill"><i class="bi bi-star me-1"></i><span data-i18n="footer.trust.price">Giá tốt</span></span>
        </div>
        <div class="d-flex align-items-center gap-2 mb-3 partner-pill">
          <span class="partner-icon" aria-hidden="true">
            <img src="images/Battay.png" alt="Hợp tác" class="partner-img">
          </span>
          <span data-i18n="footer.partner">Hợp tác với <?php echo $settings_r['site_title']; ?></span>
        </div>
        <div class="cert-row d-flex flex-wrap align-items-center gap-2 mb-3">
          <img src="images/The%20public/IATA.png" alt="IATA" class="cert-badge">
          <img src="images/The%20public/BSI.jpg" alt="BSI" class="cert-badge">
          <img src="images/The%20public/dadangky.png" alt="Đã đăng ký" class="cert-badge">
        </div>
        <h6 class="footer-heading mt-3 mb-2" data-i18n="footer.payment.title">Đối tác thanh toán</h6>
        <div class="payment-grid mb-3">
          <div class="pay-box"><img src="images/pay/VISA.webp" alt="Visa"></div>
          <div class="pay-box"><img src="images/pay/Mastercard.webp" alt="Mastercard"></div>
          <div class="pay-box"><img src="images/pay/AMEX.webp" alt="American Express"></div>
          <div class="pay-box"><img src="images/pay/JCB.webp" alt="JCB"></div>
          <div class="pay-box"><img src="images/pay/MoMo.webp" alt="MoMo"></div>
          <div class="pay-box"><img src="images/pay/VietQR.webp" alt="VietQR"></div>
          <div class="pay-box"><img src="images/pay/AlePay.webp" alt="AlePay"></div>
          <div class="pay-box"><img src="images/pay/OnePay.webp" alt="OnePay"></div>
          <div class="pay-box"><img src="images/pay/Vietcombank.webp" alt="Vietcombank"></div>
          <div class="pay-box"><img src="images/pay/VietinBank.webp" alt="VietinBank"></div>
          <div class="pay-box"><img src="images/pay/Techcombank.webp" alt="Techcombank"></div>
          <div class="pay-box"><img src="images/pay/MB Bank.webp" alt="MB Bank"></div>
          <div class="pay-box"><img src="images/pay/TPBank.webp" alt="TPBank"></div>
          <div class="pay-box"><img src="images/pay/VPBank.webp" alt="VPBank"></div>
          <div class="pay-box"><img src="images/pay/ACB.webp" alt="ACB"></div>
          <div class="pay-box"><img src="images/pay/BIDV.webp" alt="BIDV"></div>
          <div class="pay-box"><img src="images/pay/VIB.webp" alt="VIB"></div>
          <div class="pay-box"><img src="images/pay/Sacombank.webp" alt="Sacombank"></div>
          <div class="pay-box"><img src="images/pay/HSBC.webp" alt="HSBC"></div>
          <div class="pay-box"><img src="images/pay/Citibank.webp" alt="Citibank"></div>
        </div>
      </div>

      <!-- Links -->
      <div class="col-lg-2 col-md-4 col-sm-6">
        <h6 class="footer-heading" data-i18n="footer.about">Về Vĩnh Long Hotel</h6>
        <ul class="footer-links">
          <li><a href="rooms.php" data-i18n="footer.howToBook">Cách đặt chỗ</a></li>
          <li><a href="contact.php" data-i18n="footer.contactUs">Liên hệ chúng tôi</a></li>
          <li><a href="about.php" data-i18n="nav.about">Về chúng tôi</a></li>
        </ul>
      </div>

      <div class="col-lg-2 col-md-4 col-sm-6">
        <h6 class="footer-heading" data-i18n="footer.products">Sản phẩm</h6>
        <ul class="footer-links">
          <li><a href="rooms.php" data-i18n="footer.hotels">Khách sạn</a></li>
          <li><a href="#" data-i18n="footer.flights">Vé máy bay</a></li>
          <li><a href="#" data-i18n="footer.bus">Vé xe khách</a></li>
          <li><a href="#" data-i18n="footer.cruises">Du thuyền</a></li>
          <li><a href="#" data-i18n="footer.villas">Biệt thự</a></li>
          <li><a href="#" data-i18n="footer.apartments">Căn hộ</a></li>
        </ul>
      </div>

      <div class="col-lg-2 col-md-4 col-sm-6">
        <h6 class="footer-heading" data-i18n="footer.forOwners">Dành cho Chủ khách sạn</h6>
        <ul class="footer-links">
          <li><a href="owner/register.php"><i class="bi bi-person-plus me-1"></i><span data-i18n="footer.registerAccount">Đăng ký tài khoản</span></a></li>
          <li><a href="owner/index.php"><i class="bi bi-box-arrow-in-right me-1"></i><span data-i18n="footer.ownerLogin">Đăng nhập quản lý</span></a></li>
          <li><a href="owner/register.php" data-i18n="footer.manageHotel">Quản lý khách sạn của bạn</a></li>
          <li><a href="contact.php" data-i18n="footer.contactSupport">Liên hệ hỗ trợ</a></li>
        </ul>
        <h6 class="footer-heading mt-3" data-i18n="footer.others">Khác</h6>
        <ul class="footer-links">
          <li><a href="#" data-i18n="footer.affiliate">Vĩnh Long Hotel Affiliate</a></li>
          <li><a href="#" data-i18n="footer.referFriends">Giới thiệu bạn bè</a></li>
          <li><a href="#" data-i18n="footer.blog">Vĩnh Long Hotel Blog</a></li>
          <li><a href="#" data-i18n="footer.privacy">Chính Sách Quyền Riêng</a></li>
          <li><a href="#" data-i18n="footer.terms">Điều khoản & Điều kiện</a></li>
          <li><a href="#" data-i18n="footer.press">Khu vực báo chí</a></li>
          <li><a href="#" data-i18n="footer.regulations">Quy chế hoạt động</a></li>
        </ul>
      </div>

      <div class="col-lg-2 col-md-4 col-sm-6">
        <h6 class="footer-heading" data-i18n="footer.followUs">Theo dõi chúng tôi</h6>
        <div class="footer-social-list">
          <?php if($contact_r['fb']) echo "<a href='$contact_r[fb]' class='social-link' target='_blank' rel='noopener'><span class='social-icon-wrapper social-fb'><i class='bi bi-facebook'></i></span><span>Facebook</span></a>"; ?>
          <?php if($contact_r['insta']) echo "<a href='$contact_r[insta]' class='social-link' target='_blank' rel='noopener'><span class='social-icon-wrapper social-insta'><i class='bi bi-instagram'></i></span><span>Instagram</span></a>"; ?>
          <?php if($contact_r['tw']) echo "<a href='$contact_r[tw]' class='social-link' target='_blank' rel='noopener'><span class='social-icon-wrapper social-twitter'><i class='bi bi-twitter-x'></i></span><span>Twitter</span></a>"; ?>
          <a href="#" class="social-link" target="_blank" rel="noopener"><span class="social-icon-wrapper social-tiktok"><i class="bi bi-tiktok"></i></span><span>TikTok</span></a>
          <a href="#" class="social-link" target="_blank" rel="noopener"><span class="social-icon-wrapper social-youtube"><i class="bi bi-youtube"></i></span><span>Youtube</span></a>
          <a href="#" class="social-link" target="_blank" rel="noopener"><span class="social-icon-wrapper social-telegram"><i class="bi bi-telegram"></i></span><span>Telegram</span></a>
        </div>
        <div class="mt-4">
          <h6 class="footer-heading" data-i18n="footer.downloadApp">Tải ứng dụng <?php echo $settings_r['site_title']; ?></h6>
          <div class="store-row">
            <a href="#" class="store-badge-link">
              <img src="images/app/app.svg" class="store-badge" alt="App Store">
            </a>
            <a href="#" class="store-badge-link">
              <img src="images/app/gg.svg" class="store-badge" alt="Google Play">
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</footer>

<!-- Back to top button -->
<button type="button" id="btn-back-top-global" class="back-to-top" aria-label="Lên đầu trang">
  <i class="bi bi-arrow-up fs-5"></i>
</button>

<div class="footer-copy text-center py-3">
  © 2025 <?php echo $settings_r['site_title']; ?> - Web Developer PhamMinhMan
</div>

<style>
.footer-hero{
  background:linear-gradient(135deg,#0b1e37 0%,#0d2f58 50%,#0f5fa6 100%);
  padding:40px 0 44px;
  box-shadow:0 16px 40px rgba(0,0,0,0.25);
  position:relative;
  overflow:hidden;
}
.footer-hero::before{
  content:'';
  position:absolute;
  top:0;
  left:0;
  right:0;
  bottom:0;
  background:url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
  opacity:0.5;
  pointer-events:none;
}
.footer-hero-inner{
  background:rgba(255,255,255,0.06);
  border-radius:20px;
  padding:24px 28px;
  border:1px solid rgba(255,255,255,0.25);
  backdrop-filter:blur(10px);
  position:relative;
  z-index:1;
  box-shadow:0 8px 32px rgba(0,0,0,0.2);
}
.footer-hero-text{font-size:16px; letter-spacing:.2px; color:#f2c94c;}

/* Newsletter Icon */
.newsletter-icon-wrapper{
  width:48px;
  height:48px;
  border-radius:12px;
  background:linear-gradient(135deg, rgba(242,201,76,0.2), rgba(242,153,74,0.2));
  border:1px solid rgba(242,201,76,0.4);
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:20px;
  color:#f2c94c;
  flex-shrink:0;
  box-shadow:0 4px 12px rgba(242,201,76,0.2);
  animation: pulse 2s infinite;
}

/* Newsletter Input */
.newsletter-input-wrapper{
  position:relative;
  max-width:100%;
}
.newsletter-input-icon{
  position:absolute;
  left:16px;
  top:50%;
  transform:translateY(-50%);
  color:rgba(255,255,255,0.7);
  font-size:18px;
  z-index:2;
  pointer-events:none;
  transition:color 0.3s ease;
}
.newsletter-input{
  padding-left:48px !important;
  border-radius:14px !important;
  background:rgba(255,255,255,0.12) !important;
  border:2px solid rgba(255,255,255,0.25) !important;
  color:#fff !important;
  font-weight:500;
  transition:all 0.3s ease;
  height:52px;
}
.newsletter-input:focus{
  background:rgba(255,255,255,0.18) !important;
  border-color:rgba(242,201,76,0.6) !important;
  box-shadow:0 0 0 4px rgba(242,201,76,0.15) !important;
  outline:none;
}
.newsletter-input:focus + .newsletter-input-icon,
.newsletter-input:not(:placeholder-shown) + .newsletter-input-icon{
  color:#f2c94c;
}
.newsletter-input::placeholder{
  color:rgba(255,255,255,0.6) !important;
}
.newsletter-btn{
  background:linear-gradient(135deg,#f2c94c,#f2994a) !important;
  border:none !important;
  color:#0b1726 !important;
  font-weight:700 !important;
  border-radius:14px !important;
  height:52px;
  padding:0 28px !important;
  transition:all 0.3s ease;
  box-shadow:0 4px 16px rgba(242,201,76,0.3);
  position:relative;
  overflow:hidden;
}
.newsletter-btn::before{
  content:'';
  position:absolute;
  top:0;
  left:-100%;
  width:100%;
  height:100%;
  background:linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
  transition:left 0.5s ease;
}
.newsletter-btn:hover::before{
  left:100%;
}
.newsletter-btn:hover{
  transform:translateY(-2px);
  box-shadow:0 6px 20px rgba(242,201,76,0.4);
  background:linear-gradient(135deg,#f5d05a,#f5a85a) !important;
}
.newsletter-btn:active{
  transform:translateY(0);
}
.newsletter-btn .btn-loading{
  display:flex;
  align-items:center;
  justify-content:center;
}
.newsletter-btn.loading .btn-text{
  display:none;
}
.newsletter-btn.loading .btn-loading{
  display:flex !important;
}

/* App Icon */
.app-icon{
  font-size:24px;
  color:#f2c94c;
  animation: bounce 2s infinite;
}
.footer-wrapper{
    background:#0a1525;
    color:#dce6f5;
}
.footer-heading{
    font-size:15px;
    font-weight:800;
    margin-bottom:12px;
    text-transform:uppercase;
    letter-spacing:.6px;
    color:#f2c94c;
}
.footer-links{
    list-style:none;
    padding:0;
    margin:0;
}
.footer-links li{margin-bottom:8px;}
.footer-links a{
    color:#c3d4ee;
    text-decoration:none;
    transition:.2s;
}
.footer-links a:hover{
    color:#f2c94c;
    padding-left:4px;
}
/* Payment Grid */
.payment-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(70px, 1fr));
    gap:12px;
}
@media (min-width:576px){
  .payment-grid{
    grid-template-columns:repeat(4,1fr);
  }
}
.pay-box{
    background:#ffffff;
    border:2px solid #e5e7eb;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:12px;
    height:60px;
    transition:all 0.3s ease;
    box-shadow:0 2px 8px rgba(0,0,0,0.08), 0 1px 3px rgba(0,0,0,0.05);
    position:relative;
    overflow:hidden;
}
.pay-box::before{
  content:'';
  position:absolute;
  top:0;
  left:0;
  right:0;
  bottom:0;
  background:linear-gradient(135deg, rgba(242,201,76,0.08), rgba(242,153,74,0.08));
  opacity:0;
  transition:opacity 0.3s ease;
}
.pay-box:hover::before{
  opacity:1;
}
.pay-box img{
  width:36px;
  height:auto;
  filter:brightness(1);
  transition:all 0.3s ease;
  position:relative;
  z-index:1;
}
.pay-box:hover{
  border-color:#f2c94c;
  background:#fefefe;
  transform:translateY(-3px) scale(1.05);
  box-shadow:0 8px 24px rgba(242,201,76,0.25), 0 4px 12px rgba(0,0,0,0.1);
}
.pay-box:hover img{
  filter:brightness(1);
  transform:scale(1.1);
}

/* Store Badges */
.store-row{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
    justify-content:center;
}
.store-badge-link{
  display:inline-block;
  transition:all 0.3s ease;
}
.store-badge{
    width:140px;
    border-radius:12px;
    border:2px solid #1f3a5a;
    background:linear-gradient(135deg, #0f1f32, #152a42);
    padding:8px 12px;
    transition:all 0.3s ease;
    box-shadow:0 4px 12px rgba(0,0,0,0.2);
    display:block;
}
.store-badge-link:hover .store-badge{
    border-color:#f2c94c;
    transform:translateY(-3px);
    box-shadow:0 8px 24px rgba(242,201,76,0.3);
    background:linear-gradient(135deg, #152a42, #1a3552);
}

/* Social Links */
.footer-social-list{
  display:flex;
  flex-direction:column;
  gap:10px;
}
.footer-social-list .social-link{
    display:flex;
    align-items:center;
    gap:12px;
    color:#c3d4ee;
    padding:10px 12px;
    text-decoration:none;
    transition:all 0.3s ease;
    border-radius:10px;
    background:rgba(15,31,50,0.3);
    border:1px solid rgba(31,58,90,0.5);
}
.social-icon-wrapper{
  width:36px;
  height:36px;
  border-radius:10px;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:18px;
  transition:all 0.3s ease;
  flex-shrink:0;
}
.social-fb{
  background:linear-gradient(135deg, rgba(24,119,242,0.2), rgba(24,119,242,0.1));
  color:#1877f2;
  border:1px solid rgba(24,119,242,0.3);
}
.social-insta{
  background:linear-gradient(135deg, rgba(225,48,108,0.2), rgba(225,48,108,0.1));
  color:#e1306c;
  border:1px solid rgba(225,48,108,0.3);
}
.social-twitter{
  background:linear-gradient(135deg, rgba(0,0,0,0.2), rgba(0,0,0,0.1));
  color:#000;
  border:1px solid rgba(0,0,0,0.3);
}
.social-tiktok{
  background:linear-gradient(135deg, rgba(0,242,234,0.2), rgba(255,0,80,0.1));
  color:#00f2ea;
  border:1px solid rgba(0,242,234,0.3);
}
.social-youtube{
  background:linear-gradient(135deg, rgba(255,0,0,0.2), rgba(255,0,0,0.1));
  color:#ff0000;
  border:1px solid rgba(255,0,0,0.3);
}
.social-telegram{
  background:linear-gradient(135deg, rgba(37,150,190,0.2), rgba(37,150,190,0.1));
  color:#2596be;
  border:1px solid rgba(37,150,190,0.3);
}
.footer-social-list .social-link:hover{
  color:#f2c94c;
  background:rgba(15,31,50,0.5);
  border-color:#f2c94c;
  transform:translateX(4px);
  box-shadow:0 4px 12px rgba(242,201,76,0.2);
}
.footer-social-list .social-link:hover .social-icon-wrapper{
  transform:scale(1.15) rotate(5deg);
  box-shadow:0 4px 12px rgba(242,201,76,0.3);
}
.footer-social-list .social-link:hover .social-fb{
  background:linear-gradient(135deg, rgba(24,119,242,0.4), rgba(24,119,242,0.3));
  border-color:#1877f2;
}
.footer-social-list .social-link:hover .social-insta{
  background:linear-gradient(135deg, rgba(225,48,108,0.4), rgba(225,48,108,0.3));
  border-color:#e1306c;
}
.footer-social-list .social-link:hover .social-twitter{
  background:linear-gradient(135deg, rgba(0,0,0,0.4), rgba(0,0,0,0.3));
  border-color:#000;
}
.footer-social-list .social-link:hover .social-tiktok{
  background:linear-gradient(135deg, rgba(0,242,234,0.4), rgba(255,0,80,0.3));
  border-color:#00f2ea;
}
.footer-social-list .social-link:hover .social-youtube{
  background:linear-gradient(135deg, rgba(255,0,0,0.4), rgba(255,0,0,0.3));
  border-color:#ff0000;
}
.footer-social-list .social-link:hover .social-telegram{
  background:linear-gradient(135deg, rgba(37,150,190,0.4), rgba(37,150,190,0.3));
  border-color:#2596be;
}
.app-badges-text{color:#f2c94c;font-size:13px;letter-spacing:.2px;}

/* Animations */
@keyframes pulse{
  0%, 100%{ transform:scale(1); }
  50%{ transform:scale(1.05); }
}
@keyframes bounce{
  0%, 100%{ transform:translateY(0); }
  50%{ transform:translateY(-5px); }
}

/* Responsive */
@media (max-width:991px){
  .footer-hero-inner{
    padding:20px 24px;
  }
  .newsletter-icon-wrapper{
    width:40px;
    height:40px;
    font-size:18px;
  }
  .payment-grid{
    grid-template-columns:repeat(3,1fr);
  }
}
@media (max-width:767px){
  .footer-hero{
    padding:32px 0 36px;
  }
  .footer-hero-inner{
    padding:20px;
  }
  .payment-grid{
    grid-template-columns:repeat(2,1fr);
    gap:10px;
  }
  .pay-box{
    height:56px;
    padding:10px;
  }
  .pay-box img{
    width:32px;
  }
  .store-badge{
    width:120px;
  }
}
.trust-pill{
    background:rgba(255,255,255,0.08);
    border:1px solid rgba(242,201,76,0.4);
    color:#f2c94c;
    border-radius:999px;
    padding:6px 12px;
    font-size:12px;
    display:inline-flex;
    align-items:center;
}
.partner-pill{
    background:#0f1f32;
    border:1px solid #f2c94c;
    color:#f2c94c;
    border-radius:10px;
    padding:8px 12px;
    font-weight:700;
    box-shadow:0 8px 18px rgba(0,0,0,0.25);
}
.partner-pill .partner-icon{display:inline-flex;}
.partner-img{width:20px;height:20px;object-fit:contain;}
.cert-badge{
    height:34px;
    border-radius:6px;
    background:#0f1f32;
    border:1px solid #1f3a5a;
    padding:4px 8px;
    box-shadow:0 6px 12px rgba(0,0,0,0.18);
}
.footer-copy{
    background:#070f1c;
    color:#8c97ad;
    font-size:14px;
    border-top:1px solid #11243c;
}
/* Back to top (global) - Matching chat button style (no animations) */
.back-to-top{
    position:fixed !important;
    right:20px !important;
    bottom:10px !important;
    z-index:9998 !important;
    width:60px !important;
    height:60px !important;
    border-radius:50% !important;
    border:2px solid rgba(255, 255, 255, 0.3) !important;
    display:none !important;
    align-items:center !important;
    justify-content:center !important;
    background:linear-gradient(135deg, #0d6efd 0%, #0ea5e9 50%, #0d6efd 100%) !important;
    color:#fff !important;
    font-size:24px !important;
    cursor:pointer !important;
    box-shadow: 
      0 8px 32px rgba(13, 110, 253, 0.5),
      0 4px 16px rgba(13, 110, 253, 0.4),
      inset 0 2px 4px rgba(255, 255, 255, 0.3),
      inset 0 -2px 4px rgba(0, 0, 0, 0.2) !important;
    transition:all 0.3s ease !important;
    opacity:1 !important;
    visibility:visible !important;
}

.back-to-top.show{
  display:flex !important;
}

.back-to-top:hover{
  transform: translateY(-4px);
  box-shadow: 
    0 12px 40px rgba(13, 110, 253, 0.6),
    0 6px 20px rgba(13, 110, 253, 0.5),
    inset 0 2px 4px rgba(255, 255, 255, 0.4),
    inset 0 -2px 4px rgba(0, 0, 0, 0.25);
  background: linear-gradient(135deg, #0ea5e9 0%, #0d6efd 50%, #0ea5e9 100%);
  border-color: rgba(255, 255, 255, 0.5);
}

.back-to-top:active {
  transform: translateY(-2px);
  box-shadow: 
    0 4px 16px rgba(13, 110, 253, 0.5),
    0 2px 8px rgba(13, 110, 253, 0.4),
    inset 0 1px 2px rgba(255, 255, 255, 0.4),
    inset 0 -1px 2px rgba(0, 0, 0, 0.25);
}
</style>

<!-- Bootstrap bundle (JS + Popper) for dropdowns/collapse -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script>
// Newsletter submit
const newsletterForm = document.getElementById('newsletter-form');
const newsletterMsg  = document.getElementById('newsletter-msg');

if(newsletterForm){
  const newsletterBtn = newsletterForm.querySelector('.newsletter-btn');
  const newsletterInput = newsletterForm.querySelector('.newsletter-input');
  
  newsletterForm.addEventListener('submit', function(e){
    e.preventDefault();
    const email = this.email.value.trim();
    
    if(!email){
      newsletterMsg.textContent = 'Vui lòng nhập email.';
      newsletterMsg.classList.add('text-warning');
      newsletterInput.focus();
      return;
    }
    
    // Loading state
    if(newsletterBtn){
      newsletterBtn.classList.add('loading');
      newsletterBtn.disabled = true;
    }
    newsletterMsg.textContent = '';
    newsletterMsg.classList.remove('text-success', 'text-warning', 'text-danger');
    
    const fd = new FormData();
    fd.append('newsletter_submit','1');
    fd.append('email', email);

    fetch('ajax/newsletter.php', { method:'POST', body: fd })
      .then(res => res.text())
      .then(res => {
        res = res.trim();
        if(newsletterBtn){
          newsletterBtn.classList.remove('loading');
          newsletterBtn.disabled = false;
        }
        
        if(res === '1'){
          newsletterMsg.textContent = '✓ Đăng ký thành công! Cảm ơn bạn đã đăng ký.';
          newsletterMsg.classList.add('text-success');
          this.reset();
          setTimeout(() => {
            newsletterMsg.textContent = '';
            newsletterMsg.classList.remove('text-success');
          }, 5000);
        } else if(res === 'exists'){
          newsletterMsg.textContent = 'Email này đã được đăng ký trước đó.';
          newsletterMsg.classList.add('text-warning');
        } else {
          newsletterMsg.textContent = 'Có lỗi xảy ra, vui lòng thử lại.';
          newsletterMsg.classList.add('text-danger');
        }
      })
      .catch(() => {
        if(newsletterBtn){
          newsletterBtn.classList.remove('loading');
          newsletterBtn.disabled = false;
        }
        newsletterMsg.textContent = 'Có lỗi xảy ra, vui lòng thử lại.';
        newsletterMsg.classList.add('text-danger');
      });
  });
}
</script>

<script>
// Đăng nhập / Đăng ký dùng chung mọi trang (chạy sau khi DOM load để có modals)
function bindAuthForms(){
  if(window.__authBound) return;
  const login_form = document.getElementById('login-form');
  const register_form = document.getElementById('register-form');

  if(login_form){
    login_form.addEventListener('submit', (e)=>{  
      e.preventDefault();
      let data = new FormData(login_form);
      data.append('login', '');

      fetch('ajax/login_register.php', { method: 'POST', body: data })
      .then(res => res.text())
      .then(res => {
        const errorAlert = document.getElementById('login-error-alert');
        const errorText = document.getElementById('login-error-text');
        const loginBtn = document.getElementById('login-submit-btn');
        const btnText = loginBtn ? loginBtn.querySelector('.btn-text') : null;
        const btnLoading = loginBtn ? loginBtn.querySelector('.btn-loading') : null;
        
        // Reset loading state
        if (btnText && btnLoading) {
          btnText.classList.remove('d-none');
          btnLoading.classList.add('d-none');
          if (loginBtn) loginBtn.disabled = false;
        }
        
        if(res === 'login_success'){
            // Lưu tài khoản vào localStorage
            try {
                const email_mob = login_form.elements['email_mob']?.value;
                const password = login_form.elements['pass']?.value;
                
                if (email_mob && password && typeof saveAccountToLocal === 'function') {
                    // Lưu tài khoản với mật khẩu (name và profile sẽ được cập nhật sau khi reload)
                    saveAccountToLocal(email_mob, password, '', '');
                }
            } catch (e) {
                console.error('Lỗi khi lưu tài khoản:', e);
            }
            
            // Hiển thị thông báo trước khi reload
            if(typeof showToast === 'function') {
                showToast('success', 'Đăng nhập thành công!', 2000);
                setTimeout(function() {
                    location.reload();
                }, 500);
            } else {
                // Nếu showToast chưa có, reload ngay
                location.reload();
            }
        }
        else {
          let errorMsg = '';
          if(res === 'invalid_email_mob'){
            errorMsg = 'Email hoặc số điện thoại không tồn tại!';
          }
          else if(res === 'invalid_password'){
            errorMsg = 'Sai mật khẩu!';
          }
          else if(res === 'not_verified'){
            errorMsg = 'Email chưa được xác thực! Vui lòng xác thực email trước khi đăng nhập.';
          }
          else if(res === 'inactive'){
            errorMsg = 'Tài khoản đã bị khóa!';
          }
          else{
            errorMsg = 'Lỗi không xác định: ' + res;
          }
          
          // Chỉ hiển thị 1 thông báo - alert trong modal
          if (errorAlert && errorText) {
            errorText.textContent = errorMsg;
            errorAlert.classList.remove('d-none');
            errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
          }
          // Đã xóa showToast để chỉ hiển thị 1 thông báo
        }
      })
      .catch(err => {
        const errorAlert = document.getElementById('login-error-alert');
        const errorText = document.getElementById('login-error-text');
        const loginBtn = document.getElementById('login-submit-btn');
        const btnText = loginBtn ? loginBtn.querySelector('.btn-text') : null;
        const btnLoading = loginBtn ? loginBtn.querySelector('.btn-loading') : null;
        
        if (btnText && btnLoading) {
          btnText.classList.remove('d-none');
          btnLoading.classList.add('d-none');
          if (loginBtn) loginBtn.disabled = false;
        }
        
        // Chỉ hiển thị 1 thông báo - alert trong modal
        if (errorAlert && errorText) {
          errorText.textContent = 'Có lỗi xảy ra. Vui lòng thử lại!';
          errorAlert.classList.remove('d-none');
          errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
        // Đã xóa showToast để chỉ hiển thị 1 thông báo
      });
    });
  }

  if(register_form){
    register_form.addEventListener('submit', (e)=>{
      e.preventDefault();
      
      const emailInput = document.getElementById('register_email');
      const errorAlert = document.getElementById('register-error-alert');
      const errorText = document.getElementById('register-error-text');
      const registerBtn = document.getElementById('register-submit-btn');
      const btnText = registerBtn ? registerBtn.querySelector('.btn-text') : null;
      const btnLoading = registerBtn ? registerBtn.querySelector('.btn-loading') : null;
      
      // Validate form trước
      const nameInput = register_form.querySelector('input[name="name"]');
      const phoneInput = register_form.querySelector('input[name="phonenum"]');
      const addressInput = register_form.querySelector('input[name="address"]');
      const dobInput = register_form.querySelector('input[name="dob"]');
      const passInput = register_form.querySelector('input[name="pass"]');
      const cpassInput = register_form.querySelector('input[name="cpass"]');
      
      // Kiểm tra các trường bắt buộc
      if (!nameInput || !nameInput.value.trim()) {
        if (typeof showToast === 'function') {
          showToast('warning', 'Vui lòng nhập họ và tên!', 3000);
        }
        if (nameInput) nameInput.focus();
        return;
      }
      
      if (!emailInput || !emailInput.value.trim()) {
        if (errorAlert && errorText) {
          errorText.textContent = 'Vui lòng nhập email!';
          errorAlert.classList.remove('d-none');
        }
        if (typeof showToast === 'function') {
          showToast('warning', 'Vui lòng nhập email!', 3000);
        }
        if (emailInput) emailInput.focus();
        return;
      }
      
      if (!phoneInput || !phoneInput.value.trim()) {
        if (typeof showToast === 'function') {
          showToast('warning', 'Vui lòng nhập số điện thoại!', 3000);
        }
        if (phoneInput) phoneInput.focus();
        return;
      }
      
      if (!addressInput || !addressInput.value.trim()) {
        if (typeof showToast === 'function') {
          showToast('warning', 'Vui lòng nhập địa chỉ!', 3000);
        }
        if (addressInput) addressInput.focus();
        return;
      }
      
      if (!dobInput || !dobInput.value) {
        if (typeof showToast === 'function') {
          showToast('warning', 'Vui lòng nhập ngày sinh!', 3000);
        }
        if (dobInput) dobInput.focus();
        return;
      }
      
      if (!passInput || !passInput.value.trim()) {
        if (typeof showToast === 'function') {
          showToast('warning', 'Vui lòng nhập mật khẩu!', 3000);
        }
        if (passInput) passInput.focus();
        return;
      }
      
      if (passInput.value !== cpassInput.value) {
        if (typeof showToast === 'function') {
          showToast('warning', 'Mật khẩu không trùng khớp!', 3000);
        }
        if (cpassInput) cpassInput.focus();
        return;
      }
      
      const email = emailInput.value.trim();
      
      // ⚠️ BƯỚC 1: TỰ ĐỘNG GỬI OTP KHI ẤN ĐĂNG KÝ
      // Hiển thị loading
      if (btnText && btnLoading) {
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
        if (registerBtn) registerBtn.disabled = true;
      }
      
      // Lấy tất cả dữ liệu từ form để gửi kèm OTP
      const formDataObj = new FormData(register_form);
      const otpData = new FormData();
      otpData.append('send_otp', '1');
      otpData.append('email', email);
      otpData.append('name', formDataObj.get('name') || '');
      otpData.append('phonenum', formDataObj.get('phonenum') || '');
      otpData.append('address', formDataObj.get('address') || '');
      otpData.append('pincode', formDataObj.get('pincode') || '');
      otpData.append('dob', formDataObj.get('dob') || '');
      otpData.append('pass', formDataObj.get('pass') || '');
      otpData.append('cpass', formDataObj.get('cpass') || '');
      otpData.append('gender', formDataObj.get('gender') || '');
      
      // Gửi OTP với toàn bộ thông tin form
      fetch('ajax/login_register.php', { method: 'POST', body: otpData })
      .then(res => res.text())
      .then(otpRes => {
        const cleanOtpRes = otpRes.trim();
        console.log('Send OTP response:', cleanOtpRes);
        
        if (cleanOtpRes === 'otp_sent') {
          // ✅ OTP đã được gửi - Mở modal nhập mã OTP
          if (typeof showToast === 'function') {
            showToast('success', 'Mã xác thực đã được gửi đến email của bạn!', 3000);
          }
          
          // Reset loading
          if (btnText && btnLoading) {
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
            if (registerBtn) registerBtn.disabled = false;
          }
          
          // Đóng modal đăng ký và mở modal OTP
          const registerModal = document.getElementById('registerModal');
          if (registerModal) {
            const registerBsModal = bootstrap.Modal.getInstance(registerModal);
            if (registerBsModal) registerBsModal.hide();
          }
          
          // Mở modal OTP
          setTimeout(() => {
            if (typeof openOTPModal === 'function') {
              openOTPModal(email);
            } else {
              const otpModal = document.getElementById('otpVerificationModal');
              if (otpModal) {
                const otpBsModal = bootstrap.Modal.getInstance(otpModal) || new bootstrap.Modal(otpModal);
                otpBsModal.show();
              }
            }
          }, 300);
          
        } else if (cleanOtpRes === 'email_already') {
          // Email đã tồn tại - chỉ hiển thị 1 thông báo
          if (errorAlert && errorText) {
            errorText.textContent = 'Email này đã được sử dụng!';
            errorAlert.classList.remove('d-none');
            errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
          }
          
          // Reset loading
          if (btnText && btnLoading) {
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
            if (registerBtn) registerBtn.disabled = false;
          }
        } else if (cleanOtpRes === 'invalid_email') {
          // Email không hợp lệ - chỉ hiển thị 1 thông báo
          if (errorAlert && errorText) {
            errorText.textContent = 'Email không hợp lệ!';
            errorAlert.classList.remove('d-none');
            errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
          }
          
          // Reset loading
          if (btnText && btnLoading) {
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
            if (registerBtn) registerBtn.disabled = false;
          }
        } else {
          // Lỗi khác - chỉ hiển thị 1 thông báo
          if (errorAlert && errorText) {
            errorText.textContent = 'Không thể gửi mã xác thực. Vui lòng thử lại!';
            errorAlert.classList.remove('d-none');
            errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
          }
          
          // Reset loading
          if (btnText && btnLoading) {
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
            if (registerBtn) registerBtn.disabled = false;
          }
        }
      })
      .catch(err => {
        console.error('Error sending OTP:', err);
        
        // Reset loading
        if (btnText && btnLoading) {
          btnText.classList.remove('d-none');
          btnLoading.classList.add('d-none');
          if (registerBtn) registerBtn.disabled = false;
        }
        
        // Chỉ hiển thị 1 thông báo - alert trong modal
        if (errorAlert && errorText) {
          errorText.textContent = 'Có lỗi xảy ra. Vui lòng thử lại!';
          errorAlert.classList.remove('d-none');
          errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
      });
    });
  }
  window.__authBound = true;
}

// Profile preview functions removed - using auto-generated avatars

document.addEventListener('DOMContentLoaded', bindAuthForms);
window.addEventListener('load', bindAuthForms);

// Back to top global
(function(){
  function initBackToTop() {
    const btn = document.getElementById('btn-back-top-global');
    if(!btn) {
      console.log('Back to top button not found');
      return;
    }
    
    // Function to check scroll position
    function checkScroll() {
      const scrollY = window.scrollY || window.pageYOffset || document.documentElement.scrollTop;
      if(scrollY > 320){
        btn.classList.add('show');
        btn.style.display = 'flex';
      } else {
        btn.classList.remove('show');
        btn.style.display = 'none';
      }
    }
    
    // Check on scroll
    window.addEventListener('scroll', checkScroll, { passive: true });
    
    // Check on load with delay
    setTimeout(checkScroll, 100);
    checkScroll();
    
    // Click handler
    btn.addEventListener('click', ()=> {
      window.scrollTo({top:0, behavior:'smooth'});
    });
  }
  
  // Try multiple times to ensure DOM is ready
  if(document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBackToTop);
  } else {
    initBackToTop();
  }
  
  // Also try on window load
  window.addEventListener('load', initBackToTop);
})();
</script>

<!-- ============================
     I18N SCRIPTS
============================= -->
<script src="js/languages.js?v=<?php echo time(); ?>"></script>
<script>
  // Set translations globally để i18n.js có thể sử dụng
  if (typeof translations !== 'undefined') {
    window.translations = translations;
  } else {
    // Fallback nếu chưa load được
    console.warn('Translations not loaded, using fallback');
    window.translations = window.translations || {};
  }
</script>
<script src="js/i18n.js?v=<?php echo time(); ?>"></script>

<?php require('inc/chat_widget.php'); ?>

</body>
</html>
