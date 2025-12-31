<?php
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
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link  rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css">
  <?php require('inc/links.php'); ?>
  <?php
    function t_about($key, $lang = 'vi') {
      $translations = [
        'vi' => [
          'about.pageTitle' => 'Về chúng tôi',
        ],
        'en' => [
          'about.pageTitle' => 'About Us',
        ]
      ];
      return $translations[$lang][$key] ?? $key;
    }
  ?>
  <title><?php echo $settings_r['site_title'] . " - " . t_about('about.pageTitle', $current_lang); ?></title>
  
  <style>
    /* Modern About Page */
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
    }
    
    /* Header Section */
    .about-header {
      margin-bottom: 4rem;
      padding: 3rem 0;
      text-align: center;
      animation: fadeInDown 0.6s ease-out;
    }
    
    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translateY(-30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .about-header-content {
      position: relative;
    }
    
    .about-header h2 {
      font-size: 3rem;
      font-weight: 800;
      color: #1a202c;
      margin-bottom: 1rem;
      letter-spacing: 0.02em;
      text-transform: uppercase;
    }
    
    .about-header .h-line {
      width: 100px;
      height: 4px;
      background: linear-gradient(90deg, #a78bfa 0%, #ec4899 100%);
      margin: 1.5rem auto;
      border-radius: 2px;
    }
    
    .about-header p {
      max-width: 900px;
      margin: 0 auto;
      line-height: 1.9;
      color: #4a5568;
      font-size: 1.1rem;
      font-weight: 400;
      text-align: center;
      margin-top: 1.5rem;
    }
    
    .about-header .text-uppercase.text-muted {
      color: #6b7280 !important;
      font-weight: 500;
      letter-spacing: 2px;
    }
    
    /* Introduction Section */
    .intro-section {
      background: #ffffff;
      border-radius: 24px;
      padding: 3rem;
      box-shadow: 0 10px 40px rgba(0,0,0,0.08);
      margin-bottom: 3rem;
      border: 1px solid rgba(229,231,235,0.5);
    }
    
    .intro-section h3 {
      font-size: 2rem;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 1.5rem;
      position: relative;
      padding-bottom: 1rem;
    }
    
    .intro-section h3::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 60px;
      height: 4px;
      background: linear-gradient(90deg, #1f2937, #374151);
      border-radius: 2px;
    }
    
    .intro-section .indent {
      text-indent: 30px;
      text-align: justify;
      line-height: 1.8;
      color: #4b5563;
      font-size: 1.05rem;
      margin-bottom: 1.5rem;
    }
    
    .intro-section strong {
      color: #1f2937;
      font-size: 1.15rem;
    }
    
    .logo-container {
      background: #ffffff;
      border-radius: 20px;
      padding: 2rem;
      box-shadow: 0 10px 40px rgba(0,0,0,0.08);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
    }
    
    .logo-container:hover {
      transform: translateY(-4px);
      box-shadow: 0 16px 50px rgba(0,0,0,0.12);
    }
    
    .logo-container img {
      max-width: 100%;
      height: auto;
      filter: drop-shadow(0 4px 12px rgba(0,0,0,0.1));
    }
    
    /* Statistics Cards */
    .stat-card {
      background: #ffffff;
      border-radius: 20px;
      padding: 2.5rem 2rem;
      box-shadow: 0 10px 40px rgba(0,0,0,0.08);
      border: 1px solid rgba(229,231,235,0.5);
      text-align: center;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      height: 100%;
    }
    
    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #1f2937, #374151);
      transform: scaleX(0);
      transition: transform 0.3s ease;
    }
    
    .stat-card:hover::before {
      transform: scaleX(1);
    }
    
    .stat-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 16px 50px rgba(0,0,0,0.15);
    }
    
    .stat-card img {
      width: 80px;
      height: 80px;
      object-fit: contain;
      margin-bottom: 1.5rem;
      transition: all 0.3s ease;
      filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
    }
    
    .stat-card:hover img {
      transform: scale(1.1) rotate(5deg);
    }
    
    .stat-card h4 {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1f2937;
      margin-top: 1rem;
    }
    
    /* Team Section */
    .team-section-title {
      font-size: 2.5rem;
      font-weight: 800;
      color: #1f2937;
      text-align: center;
      margin: 4rem 0 3rem;
      position: relative;
      padding-bottom: 1rem;
    }
    
    .team-section-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 4px;
      background: linear-gradient(90deg, #1f2937, #374151);
      border-radius: 2px;
    }
    
    .team-card {
      background: #ffffff;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      border: 1px solid rgba(229,231,235,0.5);
      position: relative;
    }
    
    .team-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(31,41,55,0.05), rgba(55,65,81,0.05));
      opacity: 0;
      transition: opacity 0.3s ease;
      z-index: 1;
    }
    
    .team-card:hover::before {
      opacity: 1;
    }
    
    .team-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    
    .team-img {
      width: 100%;
      height: 500px;
      object-fit: cover;
      object-position: center center !important;
      display: block;
      margin: 0 auto;
      transition: transform 0.4s ease;
      position: relative;
      z-index: 0;
    }
    
    .team-card:hover .team-img {
      transform: scale(1.05);
    }
    
    .team-name {
      font-size: 1.25rem;
      font-weight: 700;
      color: #1f2937;
      margin-top: 1rem;
      padding: 0 1rem 1.5rem;
      text-align: center;
      position: relative;
      z-index: 1;
    }
    
    /* Swiper Customization - Center alignment */
    .mySwiper {
      padding-bottom: 60px;
      margin: 0 auto;
      width: 100%;
      overflow: visible;
    }
    
    .swiper-wrapper {
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      width: 100% !important;
      margin: 0 auto !important;
    }
    
    .swiper-slide {
      display: flex !important;
      justify-content: center !important;
      align-items: center !important;
      height: auto;
      width: auto !important;
      margin: 0 auto !important;
      flex-shrink: 0;
    }
    
    .team-card {
      width: 100%;
      max-width: 400px;
      min-width: 300px;
      margin: 0 auto !important;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    
    /* Ensure slide width matches card */
    .swiper-slide {
      width: auto !important;
      max-width: 400px;
      min-width: 300px;
    }
    
    /* Force center alignment for multiple slides */
    .swiper-wrapper .swiper-slide {
      margin-left: auto !important;
      margin-right: auto !important;
    }
    
    /* Ensure image container centers the image */
    .team-card img {
      object-position: center center !important;
      object-fit: cover !important;
      width: 100%;
      height: 500px;
      display: block;
      margin: 0 auto;
    }
    
    /* Center Swiper when only 1 slide */
    .swiper-slide:only-child {
      display: flex !important;
      justify-content: center !important;
      align-items: center !important;
      margin: 0 auto;
    }
    
    /* Center container wrapper */
    .container.px-4.mb-5 {
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
    }
    
    /* Ensure pagination is centered */
    .swiper-pagination {
      position: relative;
      margin-top: 2rem;
      text-align: center;
    }
    
    /* Force center when single slide or few slides */
    .swiper-wrapper:has(.swiper-slide:only-child) {
      justify-content: center !important;
    }
    
    .swiper-wrapper .swiper-slide:only-child {
      margin-left: auto !important;
      margin-right: auto !important;
    }
    
    /* Always center wrapper content when possible */
    .swiper-wrapper {
      transform: translateX(0) !important;
    }
    
    /* Force center alignment for all cases */
    .mySwiper .swiper-wrapper {
      display: flex !important;
      align-items: center !important;
    }
    
    /* Center slides when total width is less than container */
    @media (min-width: 768px) {
      .swiper-wrapper {
        justify-content: center !important;
      }
    }
    
    /* Additional center alignment */
    .swiper-container {
      display: flex;
      justify-content: center;
      align-items: center;
    }
    
    .swiper-pagination-bullet {
      width: 12px;
      height: 12px;
      background: #1f2937;
      opacity: 0.3;
      transition: all 0.3s ease;
    }
    
    .swiper-pagination-bullet-active {
      opacity: 1;
      background: #1f2937;
      transform: scale(1.2);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .about-header {
        padding: 2rem 0;
      }
      
      .about-header h2 {
        font-size: 2rem;
      }
      
      .intro-section {
        padding: 2rem 1.5rem;
      }
      
      .stat-card {
        padding: 2rem 1.5rem;
      }
      
      .team-img {
        height: 400px;
      }
    }
    
    /* Animation on scroll */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .fade-in-up {
      animation: fadeInUp 0.6s ease-out;
    }
  </style>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>
  
  <!-- Header Section -->
  <div class="container my-5 px-4">
    <div class="about-header">
      <div class="about-header-content">
        <p class="text-uppercase text-muted small mb-2" style="letter-spacing: 2px;" data-i18n="about.subtitle">GIỚI THIỆU</p>
        <h2 class="fw-bold text-center" data-i18n="about.heading">VỀ CHÚNG TÔI</h2>
        <div class="h-line"></div>
        <p class="text-center mt-3" data-i18n="about.studentInfo">
          <i class="bi bi-mortarboard-fill me-2"></i>
          Sinh viên K22 - Đại học Trà Vinh - Khoa Công nghệ Thông tin 
        </p>
        <p class="text-center mt-3">
          <?php
            $about_text = $settings_r['site_about'] ?? '';
            if ($current_lang === 'en') {
              $about_text = 'Enjoy fast, convenient online hotel booking with a wide range of options at famous destinations across Vietnam. Let your journey begin with just a few clicks!';
            }
            echo nl2br(htmlspecialchars($about_text, ENT_QUOTES, 'UTF-8'));
          ?>
        </p>
      </div>
    </div>
  </div>

  <!-- Introduction Section -->
  <div class="container">
    <div class="row justify-content-between align-items-center">
      <div class="col-lg-6 col-md-5 mb-4 order-lg-1 order-md-1 order-2">
        <div class="intro-section fade-in-up">
          <h3 class="mb-3" data-i18n="about.introTitle">
            <i class="bi bi-info-circle-fill me-2 text-primary"></i>
            Lời Giới Thiệu
          </h3>

          <p class="indent" data-i18n="about.intro1">
            Chúng tôi là nhóm sinh viên K22 – Đại học Trà Vinh, Khoa Công nghệ Thông Tin, 
            những người trẻ đầy nhiệt huyết và quyết tâm mang đến các sản phẩm phần mềm chất lượng, 
            sáng tạo và hữu ích. Mỗi thành viên đều có thế mạnh riêng, nhưng chúng tôi cùng chung một 
            mục tiêu: học hỏi, phát triển và tạo ra giá trị thực tế thông qua công nghệ.
          </p>

          <p class="indent" data-i18n="about.intro2">
            Với tinh thần trách nhiệm, sự nghiêm túc trong học tập và niềm đam mê với lập trình, 
            nhóm luôn cố gắng hoàn thiện bản thân từng ngày và mang đến những sản phẩm sáng tạo, 
            tối ưu và thân thiện với người dùng.
          </p>

          <p class="indent">
            <span data-i18n="about.intro3">Chúng tôi tin rằng:</span><br>
            <strong data-i18n="about.introQuote">"Công nghệ là công cụ – con người mới là giá trị cốt lõi."</strong>
          </p>
        </div>
      </div>

      <div class="col-lg-5 col-md-5 mb-4 order-lg-2 order-md-2 order-1">
        <div class="logo-container fade-in-up">
          <img src="images/about/logotvu.png" alt="Logo Đại học Trà Vinh" data-i18n-alt="about.logoAlt">
        </div>
      </div>
    </div>
  </div>



  <!-- Statistics Section -->
  <div class="container mt-5">
    <div class="row g-4">
      <div class="col-lg-3 col-md-6 mb-4 px-4">
        <div class="stat-card fade-in-up">
          <img src="images/about/hotel.svg" alt="Phòng" data-i18n-alt="about.statRooms">
          <h4 class="mt-3" data-i18n="about.statRooms">100+ PHÒNG</h4>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4 px-4">
        <div class="stat-card fade-in-up" style="animation-delay: 0.1s;">
          <img src="images/about/customers.svg" alt="Khách hàng" data-i18n-alt="about.statCustomers">
          <h4 class="mt-3" data-i18n="about.statCustomers">200+ KHÁCH HÀNG</h4>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4 px-4">
        <div class="stat-card fade-in-up" style="animation-delay: 0.2s;">
          <img src="images/about/rating.svg" alt="Đánh giá" data-i18n-alt="about.statReviews">
          <h4 class="mt-3" data-i18n="about.statReviews">150+ ĐÁNH GIÁ</h4>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4 px-4">
        <div class="stat-card fade-in-up" style="animation-delay: 0.3s;">
          <img src="images/about/staff.svg" alt="Nhân sự" data-i18n-alt="about.statStaff">
          <h4 class="mt-3" data-i18n="about.statStaff">50+ NHÂN SỰ</h4>
        </div>
      </div>
    </div>
  </div>

  <!-- Team Section -->
  <h3 class="team-section-title" data-i18n="about.teamTitle">
    <i class="bi bi-people-fill me-2 text-primary"></i>
    Admin VĩnhLongHotel
  </h3>

  <div class="container px-4 mb-5 d-flex justify-content-center">
    <div class="swiper mySwiper" style="width: 100%; max-width: 1200px;">
      <div class="swiper-wrapper">
        <?php 
          $about_r = selectAll('team_details');
          $path = TEAM_IMG_PATH;
          while($row = mysqli_fetch_assoc($about_r)){
            $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
            $picture = htmlspecialchars($path.$row['picture'], ENT_QUOTES, 'UTF-8');
           echo<<<data
            <div class="swiper-slide d-flex justify-content-center">
              <div class="team-card text-center">
                <img src="$picture" class="team-img" alt="$name">
                <h5 class="team-name">$name</h5>
              </div>
            </div>
          data;
          }
        ?>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </div>

  <?php require('inc/footer.php'); ?>
  <?php require('inc/modals.php'); ?>

  <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

  <script>
    var swiper = new Swiper(".mySwiper", {
      loop: true,
      spaceBetween: 30,
      centeredSlides: true,
      slidesPerView: 'auto',
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      autoplay: {
        delay: 3000,
        disableOnInteraction: false,
      },
      breakpoints: {
        320: {
          slidesPerView: 1,
          centeredSlides: true,
          spaceBetween: 20,
        },
        640: {
          slidesPerView: 1,
          centeredSlides: true,
          spaceBetween: 20,
        },
        768: {
          slidesPerView: 2,
          spaceBetween: 20,
          centeredSlides: true,
        },
        1024: {
          slidesPerView: 3,
          spaceBetween: 30,
          centeredSlides: true,
        },
      },
      on: {
        init: function() {
          // Force center alignment on init
          this.update();
          centerSlides();
        },
        update: function() {
          centerSlides();
        }
      }
    });
    
    // Function to center slides
    function centerSlides() {
      const wrapper = document.querySelector('.swiper-wrapper');
      const container = document.querySelector('.mySwiper');
      if (wrapper && container) {
        const slides = wrapper.querySelectorAll('.swiper-slide');
        let totalWidth = 0;
        let maxGap = 0;
        
        slides.forEach((slide, index) => {
          totalWidth += slide.offsetWidth;
          if (index < slides.length - 1) {
            const style = window.getComputedStyle(slide);
            const marginRight = parseInt(style.marginRight) || 0;
            maxGap = Math.max(maxGap, marginRight);
          }
        });
        
        totalWidth += (slides.length - 1) * 30; // spaceBetween
        const containerWidth = container.offsetWidth;
        
        // Always try to center
        if (totalWidth < containerWidth) {
          wrapper.style.justifyContent = 'center';
          wrapper.style.marginLeft = 'auto';
          wrapper.style.marginRight = 'auto';
        } else {
          // Even when slides overflow, try to center the first visible slide
          wrapper.style.justifyContent = 'flex-start';
        }
      }
    }
    
    // Call on resize
    window.addEventListener('resize', function() {
      if (swiper) {
        swiper.update();
        setTimeout(centerSlides, 100);
      }
    });
    
    // Intersection Observer for animations
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('fade-in-up');
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);
    
    document.querySelectorAll('.stat-card, .intro-section, .logo-container').forEach(el => {
      observer.observe(el);
    });
  </script>
</body>
</html>
