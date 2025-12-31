/**
 * Internationalization (i18n) System
 * Hỗ trợ đa ngôn ngữ - Mặc định tiếng Việt, chỉ chuyển sang tiếng Anh khi người dùng chủ động bật
 * KHÔNG tự động phát hiện ngôn ngữ trình duyệt
 */

(function() {
  'use strict';

  let currentLang = 'vi';

  // Load translations từ languages.js
  function loadTranslations() {
    // Ưu tiên sử dụng window.translations từ languages.js
    if (window.translations && window.translations.vi && window.translations.en) {
      return window.translations;
    }
    // Fallback nếu chưa load được
    return window.translations || {};
  }

  // Lấy ngôn ngữ từ cookie hoặc localStorage
  // Mặc định là tiếng Việt, chỉ chuyển sang tiếng Anh khi người dùng chọn
  function getSavedLanguage() {
    // Ưu tiên kiểm tra cookie trước (từ PHP hoặc đã set trước đó)
    const cookies = document.cookie.split(';');
    for (let cookie of cookies) {
      const [name, value] = cookie.trim().split('=');
      if (name === 'lang' && value && (value === 'vi' || value === 'en')) {
        // Đồng bộ với localStorage
        if (value !== localStorage.getItem('language')) {
          localStorage.setItem('language', value);
        }
        return value;
      }
    }
    // Kiểm tra localStorage (fallback)
    const saved = localStorage.getItem('language');
    if (saved && (saved === 'vi' || saved === 'en')) {
      // Đồng bộ với cookie nếu chưa có
      if (!document.cookie.includes('lang=')) {
        saveLanguage(saved);
      }
      return saved;
    }
    // Mặc định là tiếng Việt (không tự động phát hiện ngôn ngữ trình duyệt)
    // Chỉ chuyển sang tiếng Anh khi người dùng click nút chuyển ngôn ngữ
    const defaultLang = 'vi';
    // Lưu ngôn ngữ mặc định
    saveLanguage(defaultLang);
    return defaultLang;
  }

  // Lưu ngôn ngữ vào cookie và localStorage
  function saveLanguage(lang) {
    localStorage.setItem('language', lang);
    
    // Set cookie với nhiều cách để đảm bảo hoạt động trên mọi trình duyệt
    const expires = new Date();
    expires.setFullYear(expires.getFullYear() + 1);
    
    // Cách 1: Set với expires
    document.cookie = `lang=${lang}; expires=${expires.toUTCString()}; path=/; SameSite=Lax`;
    
    // Cách 2: Set với max-age (backup)
    document.cookie = `lang=${lang}; path=/; max-age=31536000; SameSite=Lax`;
    
    // Verify cookie was set
    const cookies = document.cookie.split(';');
    let cookieVerified = false;
    for (let cookie of cookies) {
      const [name, value] = cookie.trim().split('=');
      if (name === 'lang' && value === lang) {
        cookieVerified = true;
        break;
      }
    }
    
    if (!cookieVerified) {
      console.warn('Cookie not set, retrying...');
      // Retry với cách đơn giản nhất
      document.cookie = `lang=${lang}; path=/`;
    }
  }

  // Dịch text
  function translate(key, lang = null) {
    const useLang = lang || currentLang;
    const trans = loadTranslations();
    if (trans[useLang] && trans[useLang][key]) {
      return trans[useLang][key];
    }
    // Fallback về tiếng Việt nếu không tìm thấy
    if (useLang !== 'vi' && trans.vi && trans.vi[key]) {
      return trans.vi[key];
    }
    // Trả về key nếu không tìm thấy translation
    return key;
  }

  // Cập nhật tất cả elements có data-i18n
  function updateTranslations() {
    const elements = document.querySelectorAll('[data-i18n]');
    elements.forEach(element => {
      const key = element.getAttribute('data-i18n');
      const translated = translate(key);
      
      // Xử lý placeholder cho input
      if (element.tagName === 'INPUT' && element.type !== 'submit' && element.type !== 'button') {
        element.placeholder = translated;
      }
      // Xử lý title và aria-label
      else if (element.hasAttribute('data-i18n-title')) {
        element.title = translated;
      }
      else if (element.hasAttribute('data-i18n-aria')) {
        element.setAttribute('aria-label', translated);
      }
      // Xử lý text content
      else {
        // Kiểm tra xem element có chứa HTML tags không (như <span> bên trong)
        const html = element.getAttribute('data-i18n-html');
        if (html === 'true') {
          element.innerHTML = translated;
        } else {
          // Kiểm tra xem element có children với data-i18n không
          // Nếu có, không update element này (để children tự xử lý)
          const hasI18nChildren = Array.from(element.children).some(child => child.hasAttribute('data-i18n'));
          if (hasI18nChildren) {
            // Element có children với data-i18n, không update element này
            return;
          }
          // Kiểm tra xem element có attribute data-i18n-skip không
          // Nếu có, PHP đã xử lý translation, không cần JavaScript update
          if (element.hasAttribute('data-i18n-skip')) {
            return;
          }
          
          // Update text content
          element.textContent = translated;
        }
      }
    });

    // Xử lý data-i18n-placeholder riêng
    const placeholderElements = document.querySelectorAll('[data-i18n-placeholder]');
    placeholderElements.forEach(element => {
      const key = element.getAttribute('data-i18n-placeholder');
      const translated = translate(key);
      element.placeholder = translated;
    });

    // Xử lý aria-label từ data-i18n-aria
    const ariaElements = document.querySelectorAll('[data-i18n-aria]');
    ariaElements.forEach(element => {
      const key = element.getAttribute('data-i18n-aria');
      const translated = translate(key);
      element.setAttribute('aria-label', translated);
    });

    // Xử lý alt attribute từ data-i18n-alt
    const altElements = document.querySelectorAll('[data-i18n-alt]');
    altElements.forEach(element => {
      const key = element.getAttribute('data-i18n-alt');
      const translated = translate(key);
      element.setAttribute('alt', translated);
    });

    // Xử lý option elements trong select
    const optionElements = document.querySelectorAll('option[data-i18n]');
    optionElements.forEach(option => {
      const key = option.getAttribute('data-i18n');
      const translated = translate(key);
      option.textContent = translated;
    });

    // Cập nhật lang attribute của html tag
    document.documentElement.setAttribute('lang', currentLang);
    
    // Cập nhật nút chuyển đổi ngôn ngữ
    updateLanguageToggle();
  }

  // Cập nhật nút chuyển đổi ngôn ngữ
  function updateLanguageToggle() {
    const toggleBtn = document.getElementById('language-toggle');
    if (toggleBtn) {
      const icon = toggleBtn.querySelector('i');
      const text = toggleBtn.querySelector('.lang-text');
      if (icon) {
        icon.className = currentLang === 'vi' ? 'bi bi-translate' : 'bi bi-translate';
      }
      if (text) {
        text.textContent = currentLang === 'vi' ? 'EN' : 'VI';
      }
      toggleBtn.setAttribute('aria-label', translate('language.toggle'));
      toggleBtn.setAttribute('title', translate('language.toggle'));
    }
  }

  // Thay đổi ngôn ngữ
  function changeLanguage(lang) {
    if (lang !== 'vi' && lang !== 'en') {
      lang = 'vi'; // Mặc định
    }
    currentLang = lang;
    saveLanguage(lang);
    updateTranslations();
    
    // Trigger custom event
    const event = new CustomEvent('languageChanged', { detail: { lang } });
    document.dispatchEvent(event);
  }

  // Toggle ngôn ngữ (vi <-> en)
  function toggleLanguage() {
    const newLang = currentLang === 'vi' ? 'en' : 'vi';
    
    console.log('Toggling language to:', newLang);
    
    // Set cookie trước để đảm bảo PHP đọc được khi reload
    saveLanguage(newLang);
    
    // Verify cookie was set
    const cookies = document.cookie.split(';');
    let cookieVerified = false;
    for (let cookie of cookies) {
      const [name, value] = cookie.trim().split('=');
      if (name === 'lang' && value === newLang) {
        cookieVerified = true;
        console.log('Cookie verified:', name, '=', value);
        break;
      }
    }
    
    if (!cookieVerified) {
      console.warn('Cookie not verified, retrying...');
      // Retry setting cookie with different method
      document.cookie = `lang=${newLang}; path=/; max-age=31536000; SameSite=Lax`;
    }
    
    // Update current language
    currentLang = newLang;
    updateTranslations();
    
    // Animation effect
    const toggleBtn = document.getElementById('language-toggle');
    if (toggleBtn) {
      toggleBtn.classList.add('toggle-animate');
      setTimeout(() => {
        toggleBtn.classList.remove('toggle-animate');
      }, 300);
    }
    
    // Reload trang để áp dụng ngôn ngữ cho tất cả các phần tử (bao gồm cả PHP-generated content)
    // Điều này đảm bảo khi chuyển sang trang khác, ngôn ngữ đã được áp dụng
    // Tăng delay để đảm bảo cookie được set trước khi reload
    setTimeout(() => {
      console.log('Reloading page with language:', newLang);
      // Force reload from server với cache-busting
      const url = new URL(window.location.href);
      // Remove old _lang and _t params if exist
      url.searchParams.delete('_lang');
      url.searchParams.delete('_t');
      // Add new params
      url.searchParams.set('_lang', newLang);
      url.searchParams.set('_t', Date.now());
      window.location.href = url.toString();
    }, 500);
  }

  // Khởi tạo
  function initI18n() {
    // Lấy ngôn ngữ đã lưu (mặc định là tiếng Việt)
    // Ưu tiên đọc từ URL parameter _lang nếu có (từ PHP reload)
    const urlParams = new URLSearchParams(window.location.search);
    const langFromUrl = urlParams.get('_lang');
    if (langFromUrl === 'en' || langFromUrl === 'vi') {
      currentLang = langFromUrl;
      // Đồng bộ với cookie và localStorage
      saveLanguage(langFromUrl);
    } else {
      currentLang = getSavedLanguage();
    }
    
    console.log('Initializing i18n with language:', currentLang);
    
    // Cập nhật lang attribute của html tag ngay lập tức
    document.documentElement.setAttribute('lang', currentLang);
    
    // Đợi một chút để đảm bảo translations đã load
    setTimeout(() => {
      // Áp dụng ngôn ngữ
      updateTranslations();
    }, 100);
    
    // Lắng nghe click trên nút toggle
    document.addEventListener('click', (e) => {
      if (e.target.closest('#language-toggle')) {
        e.preventDefault();
        toggleLanguage();
      }
    });
    
    // Lắng nghe sự kiện storage để đồng bộ giữa các tab
    window.addEventListener('storage', (e) => {
      if (e.key === 'language') {
        const newLang = e.newValue;
        if (newLang === 'vi' || newLang === 'en') {
          currentLang = newLang;
          updateTranslations();
        }
      }
    });
    
    // Lắng nghe sự kiện languageChanged để cập nhật guests input
    document.addEventListener('languageChanged', function() {
      // Cập nhật guests input nếu có
      if(typeof updateGuestsInput === 'function') {
        setTimeout(() => {
          updateGuestsInput();
        }, 50);
      }
    });
  }

  // Khởi tạo khi DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initI18n);
  } else {
    initI18n();
  }

  // Expose functions globally
  window.i18n = {
    translate,
    changeLanguage,
    toggleLanguage,
    getCurrentLanguage: () => currentLang,
    updateTranslations
  };

  // Load translations từ languages.js nếu có
  if (typeof translations !== 'undefined' && translations.vi) {
    window.translations = translations;
  }
})();

