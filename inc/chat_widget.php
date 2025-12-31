<?php
// Chat Widget - Floating Live Chat
// Include this file in footer.php or header.php

// Ẩn chat widget khi đang ở trang messages.php
$current_page = basename($_SERVER['PHP_SELF']);
$hide_chat_on_messages = ($current_page === 'messages.php');
?>
<!-- Live Chat Widget -->
<div id="live-chat-widget" class="live-chat-widget" <?php echo $hide_chat_on_messages ? 'style="display: none !important;"' : ''; ?>>
  <!-- Chat Button -->
  <button id="chat-toggle-btn" class="chat-toggle-btn" data-i18n-aria="chat.openChat" aria-label="Mở chat">
    <i class="bi bi-chat-dots-fill"></i>
    <span class="chat-badge" id="chat-unread-badge" style="display: none;">0</span>
  </button>

  <!-- Chat Window -->
  <div id="chat-window" class="chat-window" style="display: none;">
    <div class="chat-header">
      <div class="d-flex align-items-center gap-2">
        <div class="chat-avatar">
          <i class="bi bi-headset"></i>
        </div>
        <div class="flex-grow-1">
          <h6 class="mb-0 fw-bold" data-i18n="chat.onlineSupport">Hỗ trợ trực tuyến</h6>
          <small class="text-muted" id="chat-status" data-i18n="chat.connecting">Đang kết nối...</small>
        </div>
      </div>
      <button id="chat-minimize-btn" class="chat-minimize-btn" data-i18n-aria="chat.minimize" aria-label="Thu nhỏ">
        <i class="bi bi-dash"></i>
      </button>
      <button id="chat-close-btn" class="chat-close-btn" data-i18n-aria="chat.close" aria-label="Đóng">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    <!-- Admin Access Banner -->
    <div id="admin-access-banner" class="admin-access-banner" style="display: none;">
      <div class="admin-banner-content">
        <i class="bi bi-shield-check"></i>
        <span data-i18n="chat.adminAccess">Admin đã truy cập cuộc trò chuyện này</span>
      </div>
      <button class="admin-banner-close" onclick="document.getElementById('admin-access-banner').style.display='none'">
        <i class="bi bi-x"></i>
      </button>
    </div>

    <div class="chat-messages" id="chat-messages-container">
      <div class="chat-welcome">
        <div class="chat-welcome-icon">
          <i class="bi bi-chat-heart"></i>
        </div>
        <h6 data-i18n="chat.hello">Xin chào! 👋</h6>
        <p data-i18n="chat.assistantIntro">Tôi là trợ lý ảo của Vĩnh Long Hotel. Tôi có thể giúp bạn:</p>
        <div class="chat-quick-actions">
          <button class="quick-action-btn" data-action="booking">
            <i class="bi bi-calendar-check"></i> <span data-i18n="chat.quickBooking">Đặt phòng</span>
          </button>
          <button class="quick-action-btn" data-action="pricing">
            <i class="bi bi-currency-dollar"></i> <span data-i18n="chat.quickPricing">Giá cả</span>
          </button>
          <button class="quick-action-btn" data-action="facilities">
            <i class="bi bi-star"></i> <span data-i18n="chat.quickFacilities">Tiện ích</span>
          </button>
          <button class="quick-action-btn" data-action="destinations">
            <i class="bi bi-geo-alt"></i> <span data-i18n="chat.quickDestinations">Điểm đến</span>
          </button>
        </div>
      </div>
    </div>

    <div class="chat-input-area">
      <div class="chat-typing-indicator" id="chat-typing" style="display: none;">
        <span></span><span></span><span></span>
      </div>
      <form id="chat-form" class="chat-form">
        <input 
          type="text" 
          id="chat-input" 
          class="chat-input" 
          data-i18n-placeholder="chat.messagePlaceholder"
          placeholder="Nhập tin nhắn của bạn..." 
          autocomplete="off"
          <?php if(!isset($_SESSION['login']) || $_SESSION['login'] != true): ?>
          disabled
          <?php endif; ?>
        >
        <button type="submit" class="chat-send-btn" id="chat-send-btn" data-i18n-aria="chat.send" aria-label="Gửi">
          <i class="bi bi-send-fill"></i>
        </button>
      </form>
      <?php if(!isset($_SESSION['login']) || $_SESSION['login'] != true): ?>
      <div class="chat-login-prompt">
        <small class="text-muted">
          <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-i18n="auth.login">Đăng nhập</a> <span data-i18n="chat.loginToUse">để sử dụng chat</span>
        </small>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<style>
/* Live Chat Widget Styles */
.live-chat-widget {
  position: fixed;
  bottom: 100px; /* Đưa lên trên để tránh nút scroll to top */
  right: 20px;
  z-index: 10000; /* Tăng z-index để nằm trên nút scroll to top (z-index: 9999) */
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

/* Chat Toggle Button - Water Wave Style */
.chat-toggle-btn {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, #0d6efd 0%, #0ea5e9 50%, #0d6efd 100%);
  background-size: 400% 400%;
  border: 2px solid rgba(255, 255, 255, 0.3);
  color: white;
  font-size: 24px;
  cursor: pointer;
  box-shadow: 
    0 8px 32px rgba(13, 110, 253, 0.5),
    0 4px 16px rgba(13, 110, 253, 0.4),
    inset 0 2px 4px rgba(255, 255, 255, 0.3),
    inset 0 -2px 4px rgba(0, 0, 0, 0.2);
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: visible;
  animation: chatGradient 3s ease infinite,
             chatWaterWave 3s ease-in-out infinite,
             chatFloat 3s ease-in-out infinite;
  z-index: 1;
}

.chat-toggle-btn::before {
  content: '';
  position: absolute;
  inset: -12px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(13, 110, 253, 0.4) 0%, rgba(14, 165, 233, 0.3) 40%, transparent 70%);
  z-index: -1;
  animation: chatWaterRipple1 2.5s ease-out infinite;
  opacity: 0;
}

.chat-toggle-btn::after {
  content: '';
  position: absolute;
  inset: -16px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(14, 165, 233, 0.3) 0%, rgba(13, 110, 253, 0.2) 40%, transparent 70%);
  z-index: -2;
  animation: chatWaterRipple2 2.5s ease-out infinite 0.8s;
  opacity: 0;
}



@keyframes chatWaterWave {
  0%, 100% {
    transform: scale(1);
    box-shadow: 
      0 8px 32px rgba(13, 110, 253, 0.5),
      0 4px 16px rgba(13, 110, 253, 0.4),
      inset 0 2px 4px rgba(255, 255, 255, 0.3),
      inset 0 -2px 4px rgba(0, 0, 0, 0.2);
  }
  25% {
    transform: scale(1.03);
    box-shadow: 
      0 9px 36px rgba(13, 110, 253, 0.55),
      0 4.5px 18px rgba(13, 110, 253, 0.45),
      inset 0 2px 4px rgba(255, 255, 255, 0.35),
      inset 0 -2px 4px rgba(0, 0, 0, 0.22);
  }
  50% {
    transform: scale(1.06);
    box-shadow: 
      0 10px 40px rgba(13, 110, 253, 0.6),
      0 5px 20px rgba(13, 110, 253, 0.5),
      inset 0 2.5px 5px rgba(255, 255, 255, 0.4),
      inset 0 -2.5px 5px rgba(0, 0, 0, 0.25);
  }
  75% {
    transform: scale(1.03);
    box-shadow: 
      0 9px 36px rgba(13, 110, 253, 0.55),
      0 4.5px 18px rgba(13, 110, 253, 0.45),
      inset 0 2px 4px rgba(255, 255, 255, 0.35),
      inset 0 -2px 4px rgba(0, 0, 0, 0.22);
  }
}

@keyframes chatWaterRipple1 {
  0% {
    transform: scale(0.8);
    opacity: 0.8;
  }
  50% {
    opacity: 0.4;
  }
  100% {
    transform: scale(2.2);
    opacity: 0;
  }
}

@keyframes chatWaterRipple2 {
  0% {
    transform: scale(0.8);
    opacity: 0.6;
  }
  50% {
    opacity: 0.3;
  }
  100% {
    transform: scale(2.5);
    opacity: 0;
  }
}

@keyframes chatFloat {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-8px);
  }
}

@keyframes chatGradient {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

.chat-toggle-btn:hover {
  transform: scale(1.3) translateY(-12px);
  box-shadow: 
    0 20px 80px rgba(13, 110, 253, 0.7),
    0 10px 40px rgba(13, 110, 253, 0.6),
    0 0 0 4px rgba(255, 255, 255, 0.4),
    0 0 40px rgba(14, 165, 233, 0.8),
    0 0 60px rgba(13, 110, 253, 0.6),
    inset 0 4px 8px rgba(255, 255, 255, 0.4),
    inset 0 -4px 8px rgba(0, 0, 0, 0.3);
  animation: chatGradient 1.5s ease infinite,
             chatWaterWave 1.5s ease-in-out infinite,
             chatFloat 1.5s ease-in-out infinite;
  border-radius: 50%;
  background: linear-gradient(135deg, #0ea5e9 0%, #0d6efd 50%, #0ea5e9 100%);
  border-color: rgba(255, 255, 255, 0.5);
}

.chat-toggle-btn:hover::before {
  animation: chatWaterRipple1 1.2s ease-out infinite;
  background: radial-gradient(circle, rgba(13, 110, 253, 0.6) 0%, rgba(14, 165, 233, 0.5) 40%, transparent 70%);
}

.chat-toggle-btn:hover::after {
  animation: chatWaterRipple2 1.2s ease-out infinite 0.4s;
  background: radial-gradient(circle, rgba(14, 165, 233, 0.5) 0%, rgba(13, 110, 253, 0.4) 40%, transparent 70%);
}

.chat-toggle-btn:active {
  transform: scale(0.9) translateY(-2px);
  box-shadow: 
    0 4px 16px rgba(13, 110, 253, 0.6),
    0 2px 8px rgba(13, 110, 253, 0.5),
    0 0 0 2px rgba(255, 255, 255, 0.4),
    0 0 25px rgba(14, 165, 233, 0.7),
    inset 0 2px 4px rgba(255, 255, 255, 0.4),
    inset 0 -2px 4px rgba(0, 0, 0, 0.3);
  border-radius: 50%;
  animation: chatGradient 0.8s ease infinite,
             chatWaterWave 0.6s ease-in-out infinite;
  background: linear-gradient(135deg, #0d6efd 0%, #0ea5e9 50%, #0d6efd 100%);
}

.chat-toggle-btn:active::before {
  animation: chatWaterRipple1 0.6s ease-out infinite;
  background: radial-gradient(circle, rgba(13, 110, 253, 0.7) 0%, rgba(14, 165, 233, 0.6) 40%, transparent 70%);
}

.chat-toggle-btn:active::after {
  animation: chatWaterRipple2 0.6s ease-out infinite 0.2s;
  background: radial-gradient(circle, rgba(14, 165, 233, 0.6) 0%, rgba(13, 110, 253, 0.5) 40%, transparent 70%);
}

.chat-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
  color: white;
  border-radius: 50%;
  min-width: 20px;
  height: 20px;
  padding: 0 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 700;
  border: 2px solid white;
  box-shadow: 
    0 4px 12px rgba(220, 53, 69, 0.6),
    0 2px 6px rgba(220, 53, 69, 0.5),
    0 0 0 2px rgba(255, 255, 255, 0.8),
    inset 0 1px 0 rgba(255, 255, 255, 0.3);
  z-index: 10001;
  animation: pulse-badge 2s infinite;
  line-height: 1;
}

@keyframes pulse-badge {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.15); }
}

/* Chat Window */
.chat-window {
  position: absolute;
  bottom: 80px;
  right: 0;
  width: 380px;
  max-width: calc(100vw - 40px);
  height: 600px;
  max-height: calc(100vh - 100px);
  background: white;
  border-radius: 20px;
  box-shadow: 
    0 20px 60px rgba(0, 0, 0, 0.25),
    0 10px 30px rgba(0, 0, 0, 0.2),
    0 5px 15px rgba(0, 0, 0, 0.15),
    0 0 0 1px rgba(0, 0, 0, 0.05),
    inset 0 1px 0 rgba(255, 255, 255, 0.9);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  animation: slideUp 0.3s ease-out;
  transition: box-shadow 0.3s ease;
}

.chat-window:hover {
  box-shadow: 
    0 24px 72px rgba(0, 0, 0, 0.3),
    0 12px 36px rgba(0, 0, 0, 0.25),
    0 6px 18px rgba(0, 0, 0, 0.2),
    0 0 0 1px rgba(0, 0, 0, 0.08),
    inset 0 1px 0 rgba(255, 255, 255, 0.95);
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* Chat Header */
.chat-header {
  background: linear-gradient(135deg, #0d6efd 0%, #0ea5e9 100%);
  color: white;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-shrink: 0;
}

.chat-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}

.chat-minimize-btn,
.chat-close-btn {
  background: none;
  border: none;
  color: white;
  font-size: 18px;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 6px;
  transition: background 0.2s;
}

.chat-minimize-btn:hover,
.chat-close-btn:hover {
  background: rgba(255, 255, 255, 0.2);
}

/* Admin Access Banner */
.admin-access-banner {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  padding: 10px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  border-bottom: 2px solid #047857;
  animation: slideDown 0.3s ease-out;
  box-shadow: 
    0 4px 12px rgba(16, 185, 129, 0.4),
    0 2px 6px rgba(16, 185, 129, 0.3),
    0 0 0 1px rgba(5, 150, 105, 0.2),
    inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.admin-banner-content {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  font-weight: 500;
  flex: 1;
}

.admin-banner-content i {
  font-size: 16px;
  color: white;
}

.admin-banner-close {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s;
  flex-shrink: 0;
  padding: 0;
}

.admin-banner-close:hover {
  background: rgba(255, 255, 255, 0.3);
}

.admin-banner-close i {
  font-size: 14px;
}

/* Chat Messages Container */
.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
  background: #f8f9fa;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.chat-messages::-webkit-scrollbar {
  width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
  background: transparent;
}

.chat-messages::-webkit-scrollbar-thumb {
  background: #dee2e6;
  border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
  background: #adb5bd;
}

/* Welcome Message */
.chat-welcome {
  text-align: center;
  padding: 20px;
  background: white;
  border-radius: 16px;
  box-shadow: 
    0 4px 16px rgba(0, 0, 0, 0.08),
    0 2px 8px rgba(0, 0, 0, 0.06),
    0 0 0 1px rgba(0, 0, 0, 0.04),
    inset 0 1px 0 rgba(255, 255, 255, 0.9);
  transition: box-shadow 0.3s ease;
}

.chat-welcome:hover {
  box-shadow: 
    0 6px 20px rgba(0, 0, 0, 0.1),
    0 3px 10px rgba(0, 0, 0, 0.08),
    0 0 0 1px rgba(0, 0, 0, 0.06),
    inset 0 1px 0 rgba(255, 255, 255, 1);
}

.chat-welcome-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, #0d6efd 0%, #0ea5e9 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  margin: 0 auto 16px;
}

.chat-welcome h6 {
  color: #1a202c;
  margin-bottom: 8px;
}

.chat-welcome p {
  color: #6b7280;
  font-size: 14px;
  margin-bottom: 16px;
}

/* Quick Actions */
.chat-quick-actions {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
  margin-top: 16px;
}

.quick-action-btn {
  padding: 10px 12px;
  background: #f8f9fa;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  font-size: 13px;
  color: #4a5568;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 6px;
  justify-content: center;
}

.quick-action-btn:hover {
  background: #e9ecef;
  border-color: #0d6efd;
  color: #0d6efd;
  transform: translateY(-2px);
}

.quick-action-btn i {
  font-size: 16px;
}

/* Message Bubbles */
.chat-message {
  display: flex;
  gap: 10px;
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.chat-message.user {
  flex-direction: row-reverse;
}

.chat-message-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: linear-gradient(135deg, #0d6efd 0%, #0ea5e9 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  flex-shrink: 0;
  overflow: hidden;
  position: relative;
}

.chat-message.user .chat-message-avatar {
  background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
}

.chat-message-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}

.chat-message-avatar span:not([style*="display: none"]) {
  font-weight: 600;
  font-size: 13px;
}

.chat-message-content {
  max-width: 75%;
  background: white;
  padding: 12px 16px;
  border-radius: 18px;
  box-shadow: 
    0 4px 12px rgba(0, 0, 0, 0.1),
    0 2px 6px rgba(0, 0, 0, 0.08),
    0 0 0 1px rgba(0, 0, 0, 0.04),
    inset 0 1px 0 rgba(255, 255, 255, 0.9);
  word-wrap: break-word;
  transition: box-shadow 0.2s ease;
}

.chat-message-content:hover {
  box-shadow: 
    0 6px 16px rgba(0, 0, 0, 0.12),
    0 3px 8px rgba(0, 0, 0, 0.1),
    0 0 0 1px rgba(0, 0, 0, 0.06),
    inset 0 1px 0 rgba(255, 255, 255, 1);
}

.chat-message.user .chat-message-content {
  background: linear-gradient(135deg, #0d6efd 0%, #0ea5e9 100%);
  color: white;
  box-shadow: 
    0 4px 12px rgba(13, 110, 253, 0.3),
    0 2px 6px rgba(13, 110, 253, 0.25),
    0 0 0 1px rgba(13, 110, 253, 0.2),
    inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.chat-message.user .chat-message-content:hover {
  box-shadow: 
    0 6px 16px rgba(13, 110, 253, 0.4),
    0 3px 8px rgba(13, 110, 253, 0.3),
    0 0 0 1px rgba(13, 110, 253, 0.25),
    inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.chat-message-text {
  margin: 0;
  font-size: 14px;
  line-height: 1.5;
  color: #1a202c;
  word-wrap: break-word;
}

.chat-message.user .chat-message-text {
  color: white;
}

.chat-message-text a {
  color: #0d6efd;
  text-decoration: underline;
  font-weight: 600;
}

.chat-message.user .chat-message-text a {
  color: rgba(255, 255, 255, 0.9);
  text-decoration: underline;
}

.chat-message-text strong {
  font-weight: 700;
}

.chat-message-time {
  font-size: 11px;
  color: #9ca3af;
  margin-top: 4px;
}

.chat-message.user .chat-message-time {
  color: rgba(255, 255, 255, 0.8);
}

/* Message Label (Bot or Admin) */
.chat-message-auto-label {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 10px;
  color: #6b7280;
  margin-bottom: 4px;
  font-weight: 500;
  opacity: 0.7;
  white-space: nowrap;
}

.chat-message-auto-label i {
  font-size: 11px;
  color: #9ca3af;
  flex-shrink: 0;
}

/* Admin Label (Real person) */
.chat-message-auto-label.admin-label {
  color: #059669;
  opacity: 0.9;
}

.chat-message-auto-label.admin-label i {
  color: #10b981;
}

/* System Message */
.chat-message.system-message {
  justify-content: center;
  margin: 12px 0;
}

.system-message-content {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background: #e3f2fd;
  border: 1px solid #90caf9;
  border-radius: 20px;
  color: #1565c0;
  font-size: 13px;
  font-weight: 500;
}

.system-message-content i {
  font-size: 16px;
  color: #1976d2;
}

/* Typing Indicator */
.chat-typing-indicator {
  padding: 0 20px 8px;
  display: flex;
  gap: 4px;
}

.chat-typing-indicator span {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #9ca3af;
  animation: typing 1.4s infinite;
}

.chat-typing-indicator span:nth-child(2) {
  animation-delay: 0.2s;
}

.chat-typing-indicator span:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes typing {
  0%, 60%, 100% {
    transform: translateY(0);
    opacity: 0.7;
  }
  30% {
    transform: translateY(-10px);
    opacity: 1;
  }
}

/* Chat Input Area */
.chat-input-area {
  border-top: 1px solid #e5e7eb;
  background: white;
  padding: 16px;
  flex-shrink: 0;
}

.chat-form {
  display: flex;
  gap: 8px;
  align-items: center;
}

.chat-input {
  flex: 1;
  border: 2px solid #e5e7eb;
  border-radius: 24px;
  padding: 10px 16px;
  font-size: 14px;
  outline: none;
  transition: all 0.2s;
}

.chat-input:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

.chat-input:disabled {
  background: #f8f9fa;
  cursor: not-allowed;
}

.chat-send-btn {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: linear-gradient(135deg, #0d6efd 0%, #0ea5e9 100%);
  border: none;
  color: white;
  font-size: 18px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
  flex-shrink: 0;
}

.chat-send-btn:hover:not(:disabled) {
  transform: scale(1.1);
  box-shadow: 
    0 6px 20px rgba(13, 110, 253, 0.5),
    0 3px 10px rgba(13, 110, 253, 0.4),
    0 0 0 2px rgba(13, 110, 253, 0.2),
    inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.chat-send-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.chat-login-prompt {
  text-align: center;
  margin-top: 8px;
}

.chat-login-prompt a {
  color: #0d6efd;
  text-decoration: none;
  font-weight: 600;
}

.chat-login-prompt a:hover {
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 576px) {
  .live-chat-widget {
    bottom: 15px;
    right: 15px;
  }

  .chat-toggle-btn {
    width: 56px;
    height: 56px;
    font-size: 22px;
  }

  .chat-window {
    width: calc(100vw - 30px);
    height: calc(100vh - 80px);
    bottom: 76px;
    right: 0;
    border-radius: 16px 16px 0 0;
  }
}

/* Minimized State */
.chat-window.minimized {
  height: 60px;
  overflow: hidden;
}

.chat-window.minimized .chat-messages,
.chat-window.minimized .chat-input-area {
  display: none;
}
</style>

<script>
// Live Chat Widget JavaScript
(function() {
  'use strict';
  
  // Ẩn chat widget khi đang ở trang messages.php
  const currentPage = window.location.pathname;
  if (currentPage.includes('messages.php')) {
    const chatWidget = document.getElementById('live-chat-widget');
    if (chatWidget) {
      chatWidget.style.display = 'none';
    }
    return; // Không khởi tạo chat widget
  }
  
  const chatWidget = document.getElementById('live-chat-widget');
  const chatToggleBtn = document.getElementById('chat-toggle-btn');
  const chatWindow = document.getElementById('chat-window');
  const chatMinimizeBtn = document.getElementById('chat-minimize-btn');
  const chatCloseBtn = document.getElementById('chat-close-btn');
  const chatForm = document.getElementById('chat-form');
  const chatInput = document.getElementById('chat-input');
  const chatMessagesContainer = document.getElementById('chat-messages-container');
  const chatSendBtn = document.getElementById('chat-send-btn');
  const chatTyping = document.getElementById('chat-typing');
  const chatUnreadBadge = document.getElementById('chat-unread-badge');
  const chatStatus = document.getElementById('chat-status');
  
  let isOpen = false;
  let isMinimized = false;
  let sessionId = '';
  let lastMessageId = 0;
  let lastReadMessageId = 0; // Track last read message ID to count unread
  let pollInterval = null;
  let displayedMessageIds = new Set(); // Track displayed message IDs to prevent duplicates
  let adminBannerShownFor = new Set(); // Track admin message IDs that already showed banner
  let userProfile = '';
  let userInitial = 'U';
  let userName = 'Người dùng';
  
  // Initialize session
  function initSession() {
    // Always try to load from localStorage first
    const savedSessionId = localStorage.getItem('chat_session_id');
    if (savedSessionId) {
      sessionId = savedSessionId;
    } else if (!sessionId) {
      // Only create new session if no saved session exists
      sessionId = 'chat_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
      localStorage.setItem('chat_session_id', sessionId);
    }
  }
  
  // Save message to localStorage
  function saveMessageToLocal(messageData) {
    try {
      const key = `chat_history_${sessionId}`;
      let history = JSON.parse(localStorage.getItem(key) || '[]');
      
      // Check if message already exists
      const exists = history.some(msg => msg.id === messageData.id);
      if (!exists) {
        history.push(messageData);
        // Keep only last 100 messages
        if (history.length > 100) {
          history = history.slice(-100);
        }
        localStorage.setItem(key, JSON.stringify(history));
      }
    } catch (e) {
      console.error('Error saving to localStorage:', e);
    }
  }
  
  // Load messages from localStorage
  function loadMessagesFromLocal() {
    try {
      const key = `chat_history_${sessionId}`;
      const history = JSON.parse(localStorage.getItem(key) || '[]');
      return history;
    } catch (e) {
      console.error('Error loading from localStorage:', e);
      return [];
    }
  }
  
  // Clear localStorage history (optional, for testing)
  function clearLocalHistory() {
    const key = `chat_history_${sessionId}`;
    localStorage.removeItem(key);
  }
  
  // Toggle chat window
  function toggleChat() {
    if (isMinimized) {
      chatWindow.classList.remove('minimized');
      isMinimized = false;
      chatInput.focus();
    } else {
      isOpen = !isOpen;
      if (isOpen) {
        chatWindow.style.display = 'flex';
        chatInput.focus();
        loadChatHistory();
        startPolling();
        // Note: markMessagesAsRead() will be called after messages are loaded in loadChatHistory()
      } else {
        chatWindow.style.display = 'none';
        stopPolling();
      }
    }
  }
  
  // Mark messages as read
  function markMessagesAsRead() {
    // Update lastReadMessageId to the highest message ID (all messages are now read)
    if (lastMessageId > lastReadMessageId) {
      lastReadMessageId = lastMessageId;
      // Save to localStorage
      try {
        localStorage.setItem('chat_last_read_message_id', lastReadMessageId.toString());
      } catch (e) {
        console.error('Error saving last read message ID:', e);
      }
    }
    // Hide badge
    updateUnreadBadge(0);
  }
  
  // Update unread badge
  function updateUnreadBadge(count) {
    if (chatUnreadBadge) {
      if (count > 0 && !isOpen) {
        chatUnreadBadge.textContent = count > 99 ? '99+' : count.toString();
        chatUnreadBadge.style.display = 'flex';
      } else {
        chatUnreadBadge.style.display = 'none';
      }
    }
  }
  
  // Count unread messages from admin (is_bot = 0) - only messages that customer hasn't seen AND hasn't replied to
  function countUnreadMessages(messages) {
    if (!messages || messages.length === 0) return 0;
    
    let count = 0;
    
    // Sort messages by id to process in order
    const sortedMessages = [...messages].sort((a, b) => a.id - b.id);
    
    sortedMessages.forEach((adminMsg, index) => {
      // Only check admin messages (sender_type === 'admin' and is_bot === 0) 
      // that are after lastReadMessageId (messages customer hasn't seen)
      if (adminMsg.sender_type === 'admin' && 
          (adminMsg.is_bot === 0 || adminMsg.is_bot === null) && 
          adminMsg.id > lastReadMessageId) {
        
        // Check if there's any user message after this admin message
        // If user has replied, don't count this admin message
        let hasUserReply = false;
        for (let i = index + 1; i < sortedMessages.length; i++) {
          if (sortedMessages[i].sender_type === 'user' && sortedMessages[i].id > adminMsg.id) {
            hasUserReply = true;
            break;
          }
        }
        
        // Only count if customer hasn't replied to this admin message
        if (!hasUserReply) {
          count++;
        }
      }
    });
    
    return count;
  }
  
  // Minimize chat
  function minimizeChat() {
    chatWindow.classList.add('minimized');
    isMinimized = true;
  }
  
  // Close chat
  function closeChat() {
    isOpen = false;
    chatWindow.style.display = 'none';
    stopPolling();
  }
  
  // Send message
  function sendMessage(message) {
    if (!message || !message.trim()) return;
    
    const userLoggedIn = <?php echo (isset($_SESSION['login']) && $_SESSION['login'] == true) ? 'true' : 'false'; ?>;
    if (!userLoggedIn) {
      const loginMsg = window.i18n ? window.i18n.translate('chat.loginToUse') : 'Vui lòng đăng nhập để sử dụng chat';
      alert(loginMsg);
      return;
    }
    
    // Store original message before clearing input
    const originalMessage = message.trim();
    
    // Clear input first
    chatInput.value = '';
    
    // Show typing indicator
    showTyping();
    
    // Send to server
    const formData = new FormData();
    formData.append('action', 'send');
    formData.append('message', originalMessage);
    formData.append('session_id', sessionId);
    
    fetch('ajax/live_chat.php', {
      method: 'POST',
      body: formData
    })
    .then(res => {
      if (!res.ok) {
        throw new Error('HTTP error: ' + res.status);
      }
      return res.json();
    })
    .then(data => {
      hideTyping();
      
      // Debug: Log full response
      console.log('Chat response received:', data);
      
      if (data.status === 'success') {
        // Add user message to UI immediately
        if (data.message_id) {
          addMessageToUI('user', originalMessage, null, data.message_id);
          lastMessageId = Math.max(lastMessageId, data.message_id);
          displayedMessageIds.add(data.message_id);
        } else {
          // Fallback: add user message even without message_id
          addMessageToUI('user', originalMessage);
        }
        
        // Add bot response immediately if available
        if (data.bot_response && data.bot_response.trim() !== '') {
          console.log('Adding bot response:', data.bot_response.substring(0, 50) + '...');
          setTimeout(() => {
            // Chatbot message - is_bot = 1 (automatic message)
            const botProfileData = {
              is_bot: 1, // Chatbot is always automatic
              message_type: 'bot'
            };
            if (data.bot_message_id) {
              addMessageToUI('bot', data.bot_response, null, data.bot_message_id, botProfileData);
              lastMessageId = Math.max(lastMessageId, data.bot_message_id);
              displayedMessageIds.add(data.bot_message_id);
            } else {
              // Fallback if no bot_message_id
              addMessageToUI('bot', data.bot_response, null, null, botProfileData);
            }
            // Scroll to bottom after adding message
            const chatMessages = document.getElementById('chat-messages-container');
            if (chatMessages) {
              chatMessages.scrollTop = chatMessages.scrollHeight;
            }
          }, 500);
        } else {
          // Debug: log if bot_response is missing
          console.error('❌ No bot_response in server response!');
          console.error('Full response:', data);
          console.error('Admin online:', data.admin_online);
          console.error('Session ID:', data.session_id);
          console.error('Debug message:', data.debug_message);
          
          // Show a message to user if bot didn't respond
          if (!data.admin_online) {
            const botProfileData = {
              is_bot: 1, // Chatbot is always automatic
              message_type: 'bot'
            };
            const errorIntro = window.i18n ? window.i18n.translate('chat.hello') + ' ' + window.i18n.translate('chat.assistantIntro') : 'Xin chào! 👋 Tôi là trợ lý ảo của Vĩnh Long Hotel.';
            const errorText = window.i18n ? (errorIntro + ' Có vẻ như tôi gặp sự cố. Vui lòng thử lại!') : 'Xin chào! 👋 Tôi là trợ lý ảo của Vĩnh Long Hotel. Có vẻ như tôi gặp sự cố. Vui lòng thử lại!';
            addMessageToUI('bot', errorText, null, null, botProfileData);
          }
        }
      } else {
        const errorMsg = data.message || (window.i18n ? window.i18n.translate('common.error') : 'Có lỗi xảy ra. Vui lòng thử lại.');
        const botProfileData = {
          is_bot: 1, // Chatbot is always automatic
          message_type: 'bot'
        };
        const sorryText = window.i18n ? window.i18n.translate('common.sorry') || 'Xin lỗi' : 'Xin lỗi';
        addMessageToUI('bot', sorryText + ', ' + errorMsg, null, null, botProfileData);
        console.error('Chat error:', data);
      }
    })
    .catch(err => {
      hideTyping();
      console.error('Chat fetch error:', err);
      // Show more detailed error for debugging
      const errorMsg = err.message || (window.i18n ? window.i18n.translate('common.connectionError') || 'Không thể kết nối đến server' : 'Không thể kết nối đến server');
      console.error('Full error:', err);
      const sorryText = window.i18n ? window.i18n.translate('common.sorry') || 'Xin lỗi' : 'Xin lỗi';
      const tryAgainText = window.i18n ? window.i18n.translate('common.tryAgain') || 'Vui lòng thử lại' : 'Vui lòng thử lại';
      addMessageToUI('bot', sorryText + ', ' + errorMsg + '. ' + tryAgainText + '.');
    });
  }
  
  // Add message to UI
  function addMessageToUI(type, text, timestamp = null, messageId = null, profileData = null) {
    // Prevent duplicate messages
    if (messageId && displayedMessageIds.has(messageId)) {
      return;
    }
    
    if (messageId) {
      displayedMessageIds.add(messageId);
    }
    
    // Update user profile data if provided
    if (profileData) {
      if (profileData.user_profile) userProfile = profileData.user_profile;
      if (profileData.user_initial) userInitial = profileData.user_initial;
      if (profileData.user_name) userName = profileData.user_name;
      
      // Save profile to localStorage
      try {
        localStorage.setItem('chat_user_profile', JSON.stringify({
          user_profile: userProfile,
          user_initial: userInitial,
          user_name: userName
        }));
      } catch (e) {
        console.error('Error saving profile to localStorage:', e);
      }
    }
    
    // Determine if this is a bot message or real admin message
    // Chatbot messages (type === 'bot') are ALWAYS automatic (is_bot = 1 by default)
    // Only admin messages (type === 'bot' but is_bot === 0) are from real staff
    const isBotMessage = type === 'bot' && (!profileData || profileData.is_bot === undefined || profileData.is_bot === 1);
    
    // Save message to localStorage (only if we have a valid sessionId)
    if (sessionId) {
      const messageData = {
        id: messageId || Date.now(),
        sender_type: type === 'user' ? 'user' : 'admin',
        message: text,
        created_at: timestamp || new Date().toISOString(),
        message_type: (profileData && profileData.message_type) ? profileData.message_type : (type === 'bot' ? 'bot' : 'text'),
        is_bot: (type === 'bot' && (!profileData || profileData.is_bot === undefined || profileData.is_bot === 1)) ? 1 : ((type === 'bot' && profileData && profileData.is_bot === 0) ? 0 : (type === 'user' ? 0 : 1)),
        user_profile: userProfile || '',
        user_initial: userInitial || 'U',
        user_name: userName || 'Người dùng'
      };
      saveMessageToLocal(messageData);
    }
    
    // Check if this is a system message
    const isSystemMessage = profileData && profileData.message_type === 'system';
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `chat-message ${type}${isSystemMessage ? ' system-message' : ''}`;
    if (messageId) {
      messageDiv.setAttribute('data-message-id', messageId);
    }
    
    // System messages have special styling (centered, no avatar)
    if (isSystemMessage) {
      // Process text: convert \n to <br> and handle markdown formatting
      let processedText = String(text || '');
      processedText = processedText.replace(/\\n/g, '\n');
      processedText = processedText.replace(/\\r\\n/g, '\n');
      processedText = processedText.replace(/\\r/g, '\n');
      processedText = processedText.replace(/\r\n/g, '<br>').replace(/\r/g, '<br>').replace(/\n/g, '<br>');
      processedText = processedText.replace(/\*\*([^*<]+?)\*\*/g, '<strong>$1</strong>');
      
      messageDiv.innerHTML = `
        <div class="system-message-content">
          <i class="bi bi-info-circle"></i>
          <span>${processedText}</span>
        </div>
      `;
      chatMessagesContainer.appendChild(messageDiv);
      chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
      return;
    }
    
    const avatar = document.createElement('div');
    avatar.className = 'chat-message-avatar';
    
    if (type === 'user') {
      // Show user avatar or initial
      if (userProfile) {
        const img = document.createElement('img');
        img.src = userProfile;
        img.alt = userName;
        img.style.cssText = 'width:100%;height:100%;object-fit:cover;border-radius:50%;';
        img.onerror = function() {
          this.style.display = 'none';
          const span = document.createElement('span');
          span.textContent = userInitial;
          span.style.cssText = 'display:flex;align-items:center;justify-content:center;width:100%;height:100%;';
          avatar.appendChild(span);
        };
        avatar.appendChild(img);
      } else {
        const span = document.createElement('span');
        span.textContent = userInitial;
        avatar.appendChild(span);
      }
    } else {
      // Bot or Admin avatar - check if it's a real admin or bot
      // Chatbot messages (type === 'bot') are ALWAYS automatic (is_bot = 1 by default)
      // Only admin messages (type === 'bot' but is_bot === 0) are from real staff
      const isBotMessage = !profileData || profileData.is_bot === undefined || profileData.is_bot === 1;
      if (isBotMessage) {
        // Chatbot avatar (automatic message)
        avatar.innerHTML = '<i class="bi bi-robot"></i>';
      } else {
        // Real admin avatar
        avatar.innerHTML = '<i class="bi bi-person-badge"></i>';
        avatar.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
      }
    }
    
    const content = document.createElement('div');
    content.className = 'chat-message-content';
    
    const textDiv = document.createElement('div');
    textDiv.className = 'chat-message-text';
    
    // Process text: convert \n to <br> and handle markdown formatting
    let processedText = String(text || '');
    
    // Handle escaped newlines from JSON (\\n -> \n)
    processedText = processedText.replace(/\\n/g, '\n');
    processedText = processedText.replace(/\\r\\n/g, '\n');
    processedText = processedText.replace(/\\r/g, '\n');
    
    // Convert all types of newlines to <br> (handle \r\n, \n, \r)
    processedText = processedText.replace(/\r\n/g, '<br>').replace(/\r/g, '<br>').replace(/\n/g, '<br>');
    
    // Convert markdown **text** to <strong>text</strong> (after converting \n to <br>)
    processedText = processedText.replace(/\*\*([^*<]+?)\*\*/g, '<strong>$1</strong>');
    
    // Allow HTML for bot messages (links, formatting)
    if (type === 'bot') {
      textDiv.innerHTML = processedText;
      // Make links open in new tab
      textDiv.querySelectorAll('a').forEach(link => {
        link.target = '_blank';
        link.rel = 'noopener noreferrer';
      });
    } else {
      // For user messages, also use innerHTML to support line breaks
      textDiv.innerHTML = processedText;
    }
    
    const timeDiv = document.createElement('div');
    timeDiv.className = 'chat-message-time';
    timeDiv.textContent = timestamp || new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    
    // Add label to distinguish bot messages from real admin messages
    // Bot messages (type === 'bot') are ALWAYS automatic messages
    // Only admin messages (type === 'bot' but is_bot === 0) are from real staff
    if (type === 'bot') {
      // Default: bot messages are automatic (is_bot = 1)
      // Only if explicitly set is_bot = 0, it's from real admin
      const isBotMessage = !profileData || profileData.is_bot === undefined || profileData.is_bot === 1;
      const label = document.createElement('div');
      label.className = 'chat-message-auto-label';
      
      if (isBotMessage) {
        // Bot/Chatbot message - show "Tin nhắn tự động"
        const autoMsgText = window.i18n ? window.i18n.translate('chat.autoMessage') : 'Tin nhắn tự động';
        label.innerHTML = '<i class="bi bi-robot"></i> ' + autoMsgText;
      } else {
        // Real admin message - show "Nhân viên hỗ trợ"
        const staffMsgText = window.i18n ? window.i18n.translate('chat.staffMessage') : 'Nhân viên hỗ trợ';
        label.innerHTML = '<i class="bi bi-person-check"></i> ' + staffMsgText;
        label.classList.add('admin-label');
      }
      
      content.appendChild(label);
    }
    
    content.appendChild(textDiv);
    content.appendChild(timeDiv);
    
    messageDiv.appendChild(avatar);
    messageDiv.appendChild(content);
    
    // Remove welcome message if exists
    const welcome = chatMessagesContainer.querySelector('.chat-welcome');
    if (welcome && type === 'user') {
      welcome.remove();
    }
    
    chatMessagesContainer.appendChild(messageDiv);
    
    // Show admin access banner for NEW admin messages (not bot)
    // Only show if this message ID hasn't shown banner before (to prevent duplicate)
    if (type === 'bot' && profileData && profileData.is_bot === 0 && messageId && !adminBannerShownFor.has(messageId)) {
      showAdminAccessBanner();
      adminBannerShownFor.add(messageId);
    }
    
    scrollToBottom();
  }
  
  // Show admin access banner
  function showAdminAccessBanner() {
    const banner = document.getElementById('admin-access-banner');
    if (banner && banner.style.display === 'none') {
      banner.style.display = 'flex';
      // Auto-hide after 5 seconds
      setTimeout(() => {
        if (banner && banner.style.display !== 'none') {
          banner.style.display = 'none';
        }
      }, 5000);
    }
  }
  
  // Load chat history
  function loadChatHistory() {
    const userLoggedIn = <?php echo (isset($_SESSION['login']) && $_SESSION['login'] == true) ? 'true' : 'false'; ?>;
    if (!userLoggedIn) return;
    
    // Ensure session is initialized
    if (!sessionId) {
      initSession();
    }
    
    // First, load from localStorage for instant display
    const localMessages = loadMessagesFromLocal();
    
    // Only show localStorage messages if container is empty (first load)
    if (localMessages.length > 0 && chatMessagesContainer.children.length === 0) {
      chatMessagesContainer.innerHTML = '';
      displayedMessageIds.clear();
      
      // Restore user profile from messages
      const firstUserMsg = localMessages.find(msg => msg.sender_type === 'user');
      if (firstUserMsg) {
        if (firstUserMsg.user_profile) userProfile = firstUserMsg.user_profile;
        if (firstUserMsg.user_initial) userInitial = firstUserMsg.user_initial;
        if (firstUserMsg.user_name) userName = firstUserMsg.user_name;
      }
      
      localMessages.forEach(msg => {
        const profileData = {
          user_profile: msg.user_profile || userProfile || '',
          user_initial: msg.user_initial || userInitial || 'U',
          user_name: msg.user_name || userName || 'Người dùng',
          message_type: msg.message_type || 'text',
          is_bot: msg.is_bot !== undefined ? msg.is_bot : (msg.sender_type === 'user' ? 0 : 1)
        };
        // Check if this is a system message
        const msgType = (msg.message_type === 'system') ? 'bot' : (msg.sender_type === 'user' ? 'user' : 'bot');
        addMessageToUI(msgType, msg.message, msg.created_at, msg.id, profileData);
        
        // Don't show banner for old messages when loading from localStorage (first load)
        // Just update lastMessageId, but DON'T mark as shown (so new messages will show banner)
        if (msg.id > lastMessageId) {
          lastMessageId = msg.id;
        }
        // Don't mark old messages as shown - only mark when actually showing banner for new messages
      });
    }
    
    // Then, sync with server to get latest messages
    fetch('ajax/live_chat.php?action=get_history&session_id=' + sessionId)
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          // Update session_id if provided
          if (data.session_id) {
            sessionId = data.session_id;
            localStorage.setItem('chat_session_id', sessionId);
          }
          
          // Update user profile data from response
          if (data.user_profile) userProfile = data.user_profile;
          if (data.user_initial) userInitial = data.user_initial;
          if (data.user_name) userName = data.user_name;
          
          if (data.messages && Array.isArray(data.messages)) {
            // Merge server messages with localStorage
            // Server is source of truth, but keep localStorage as backup
            const serverMessageIds = new Set(data.messages.map(m => m.id));
            
            // Update localStorage with server messages
            if (sessionId && data.messages.length > 0) {
              try {
                const key = `chat_history_${sessionId}`;
                const serverHistory = data.messages.map(msg => ({
                  id: msg.id,
                  sender_type: msg.sender_type,
                  message: msg.message,
                  created_at: msg.created_at,
                  message_type: msg.message_type || 'text',
                  is_bot: msg.is_bot || 0,
                  user_profile: msg.user_profile || data.user_profile || '',
                  user_initial: msg.user_initial || data.user_initial || 'U',
                  user_name: msg.user_name || data.user_name || 'Người dùng'
                }));
                localStorage.setItem(key, JSON.stringify(serverHistory));
              } catch (e) {
                console.error('Error updating localStorage with server data:', e);
              }
            }
            
            // Only clear and reload if we have messages from server OR if localStorage was empty
            if (data.messages.length > 0 || localMessages.length === 0) {
              chatMessagesContainer.innerHTML = '';
              displayedMessageIds.clear();
              
              data.messages.forEach(msg => {
                const profileData = {
                  user_profile: msg.user_profile || data.user_profile || '',
                  user_initial: msg.user_initial || data.user_initial || 'U',
                  user_name: msg.user_name || data.user_name || 'Người dùng',
                  message_type: msg.message_type || 'text',
                  is_bot: msg.is_bot !== undefined ? msg.is_bot : (msg.sender_type === 'user' ? 0 : 1)
                };
                // Check if this is a system message
                const msgType = (msg.message_type === 'system') ? 'bot' : (msg.sender_type === 'user' ? 'user' : 'bot');
                addMessageToUI(msgType, msg.message, msg.created_at, msg.id, profileData);
                
                // Don't show banner for old messages when loading from server (first load)
                // Just update lastMessageId, but DON'T mark as shown (so new messages will show banner)
                if (msg.id > lastMessageId) {
                  lastMessageId = msg.id;
                }
                // Don't mark old messages as shown - only mark when actually showing banner for new messages
              });
              
              // Mark all messages as read when chat is opened and messages are loaded
              if (isOpen) {
                markMessagesAsRead();
              }
            }
            
            // Show welcome if no messages at all
            if (data.messages.length === 0 && localMessages.length === 0) {
              // Show welcome if no messages - clone from template
              const welcomeTemplate = document.getElementById('chat-welcome-template');
              if (welcomeTemplate) {
                const welcome = welcomeTemplate.cloneNode(true);
                welcome.id = ''; // Remove ID to avoid duplicates
                chatMessagesContainer.appendChild(welcome);
              } else {
                // Fallback: create welcome manually
                const welcome = document.createElement('div');
                welcome.className = 'chat-welcome';
                const helloText = window.i18n ? window.i18n.translate('chat.hello') : 'Xin chào! 👋';
                const introText = window.i18n ? window.i18n.translate('chat.assistantIntro') : 'Tôi là trợ lý ảo của Vĩnh Long Hotel. Tôi có thể giúp bạn:';
                welcome.innerHTML = '<div class="chat-welcome-icon"><i class="bi bi-chat-heart"></i></div><h6 data-i18n="chat.hello">' + helloText + '</h6><p data-i18n="chat.assistantIntro">' + introText + '</p>';
                // Add quick actions
                const quickActions = document.createElement('div');
                quickActions.className = 'chat-quick-actions';
                const bookingText = window.i18n ? window.i18n.translate('chat.quickBooking') : 'Đặt phòng';
                const pricingText = window.i18n ? window.i18n.translate('chat.quickPricing') : 'Giá cả';
                const facilitiesText = window.i18n ? window.i18n.translate('chat.quickFacilities') : 'Tiện ích';
                const destinationsText = window.i18n ? window.i18n.translate('chat.quickDestinations') : 'Điểm đến';
                quickActions.innerHTML = `
                  <button class="quick-action-btn" data-action="booking">
                    <i class="bi bi-calendar-check"></i> <span data-i18n="chat.quickBooking">${bookingText}</span>
                  </button>
                  <button class="quick-action-btn" data-action="pricing">
                    <i class="bi bi-currency-dollar"></i> <span data-i18n="chat.quickPricing">${pricingText}</span>
                  </button>
                  <button class="quick-action-btn" data-action="facilities">
                    <i class="bi bi-star"></i> <span data-i18n="chat.quickFacilities">${facilitiesText}</span>
                  </button>
                  <button class="quick-action-btn" data-action="destinations">
                    <i class="bi bi-geo-alt"></i> <span data-i18n="chat.quickDestinations">${destinationsText}</span>
                  </button>
                `;
                welcome.appendChild(quickActions);
                chatMessagesContainer.appendChild(welcome);
                // Trigger i18n update
                if (window.i18n) {
                  window.i18n.updateTranslations();
                }
              }
            }
          }
        } else {
          console.error('Error loading chat history:', data.message || 'Unknown error');
          // If server fails, at least show localStorage messages
          if (localMessages.length === 0) {
            const welcomeTemplate = document.getElementById('chat-welcome-template');
            if (welcomeTemplate) {
              const welcome = welcomeTemplate.cloneNode(true);
              welcome.id = '';
              chatMessagesContainer.appendChild(welcome);
            }
          }
        }
      })
      .catch(err => {
        console.error('Error loading chat history:', err);
        // If server fails, at least show localStorage messages
        if (localMessages.length === 0) {
          const welcomeTemplate = document.getElementById('chat-welcome-template');
          if (welcomeTemplate) {
            const welcome = welcomeTemplate.cloneNode(true);
            welcome.id = '';
            chatMessagesContainer.appendChild(welcome);
          }
        }
      });
  }
  
  // Start polling for new messages
  function startPolling() {
    if (pollInterval) return;
    pollInterval = setInterval(() => {
      checkNewMessages();
    }, 2000);
  }
  
  // Stop polling
  function stopPolling() {
    if (pollInterval) {
      clearInterval(pollInterval);
      pollInterval = null;
    }
  }
  
  // Check for new messages
  function checkNewMessages() {
    const userLoggedIn = <?php echo (isset($_SESSION['login']) && $_SESSION['login'] == true) ? 'true' : 'false'; ?>;
    if (!userLoggedIn) return;
    
    fetch('ajax/live_chat.php?action=check_new&session_id=' + sessionId + '&last_id=' + lastMessageId)
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success' && data.messages) {
          let hasUnreadAdminMessage = false;
          
          data.messages.forEach(msg => {
            if (msg.id > lastMessageId && !displayedMessageIds.has(msg.id)) {
              const profileData = {
                user_profile: msg.user_profile || '',
                user_initial: msg.user_initial || 'U',
                user_name: msg.user_name || 'Người dùng',
                message_type: msg.message_type || 'text',
                is_bot: msg.is_bot !== undefined ? msg.is_bot : (msg.sender_type === 'user' ? 0 : 1)
              };
              
              // Check if this is a system message
              const msgType = (msg.message_type === 'system') ? 'bot' : (msg.sender_type === 'user' ? 'user' : 'bot');
              
              // Only add to UI if chat is open
              if (isOpen) {
                addMessageToUI(msgType, msg.message, msg.created_at, msg.id, profileData);
                
                // Show admin access banner for NEW admin messages (not bot)
                // This is a new message (id > lastMessageId) from admin, so show banner
                if (msg.sender_type === 'admin' && profileData.is_bot === 0 && msg.id > lastMessageId) {
                  showAdminAccessBanner();
                  // Mark this message ID as shown to prevent duplicate banner for same message
                  adminBannerShownFor.add(msg.id);
                }
              } else {
                // If chat is closed, check if this is an unread admin message
                if (msg.sender_type === 'admin' && profileData.is_bot === 0 && msg.id > lastReadMessageId) {
                  hasUnreadAdminMessage = true;
                }
              }
              
              lastMessageId = msg.id;
            }
          });
          
          // Update badge if chat is closed - fetch all messages to count accurately
          if (!isOpen) {
            fetch('ajax/live_chat.php?action=get_history&session_id=' + sessionId)
              .then(res => res.json())
              .then(historyData => {
                if (historyData.status === 'success' && historyData.messages) {
                  const unreadCount = countUnreadMessages(historyData.messages);
                  updateUnreadBadge(unreadCount);
                }
              })
              .catch(err => console.error('Error fetching history for badge:', err));
          }
        }
      })
      .catch(err => console.error('Error checking messages:', err));
  }
  
  // Show typing indicator
  function showTyping() {
    chatTyping.style.display = 'flex';
    scrollToBottom();
  }
  
  // Hide typing indicator
  function hideTyping() {
    chatTyping.style.display = 'none';
  }
  
  // Scroll to bottom
  function scrollToBottom() {
    chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
  }
  
  // Quick action buttons
  document.addEventListener('click', function(e) {
    if (e.target.closest('.quick-action-btn')) {
      const btn = e.target.closest('.quick-action-btn');
      const action = btn.dataset.action;
      let message = '';
      
      const currentLang = window.i18n ? window.i18n.getCurrentLanguage() : 'vi';
      switch(action) {
        case 'booking':
          message = window.i18n ? window.i18n.translate('chat.bookingMessage') : 'Tôi muốn đặt phòng';
          break;
        case 'pricing':
          message = window.i18n ? window.i18n.translate('chat.pricingMessage') : 'Giá phòng như thế nào?';
          break;
        case 'facilities':
          message = window.i18n ? window.i18n.translate('chat.facilitiesMessage') : 'Có những tiện ích gì?';
          break;
        case 'destinations':
          message = window.i18n ? window.i18n.translate('chat.destinationsMessage') : 'Có địa điểm du lịch nào gần đây không?';
          break;
      }
      
      if (message) {
        chatInput.value = message;
        sendMessage(message);
      }
    }
  });
  
  // Event listeners
  if (chatToggleBtn) {
    chatToggleBtn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      console.log('Chat button clicked');
      toggleChat();
    });
    console.log('Chat toggle button event listener attached');
  } else {
    console.error('Chat toggle button not found!');
  }
  
  if (chatMinimizeBtn) {
    chatMinimizeBtn.addEventListener('click', minimizeChat);
  }
  
  if (chatCloseBtn) {
    chatCloseBtn.addEventListener('click', closeChat);
  }
  
  if (chatForm) {
    chatForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const message = chatInput.value.trim();
      if (message) {
        sendMessage(message);
      }
    });
  }
  
  // Initialize session immediately
  initSession();
  
  // Function to update badge count (defined globally)
  function updateBadgeCount() {
    const userLoggedInCheck = <?php echo (isset($_SESSION['login']) && $_SESSION['login'] == true) ? 'true' : 'false'; ?>;
    if (!userLoggedInCheck || !sessionId || isOpen) return;
    
    fetch('ajax/live_chat.php?action=get_history&session_id=' + sessionId)
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success' && data.messages) {
          const unreadCount = countUnreadMessages(data.messages);
          updateUnreadBadge(unreadCount);
        }
      })
      .catch(err => console.error('Error loading unread count:', err));
  }
  
  // Check for unread messages on page load
  const userLoggedIn = <?php echo (isset($_SESSION['login']) && $_SESSION['login'] == true) ? 'true' : 'false'; ?>;
  if (userLoggedIn && sessionId) {
    // Load chat history to count unread messages on page load
    setTimeout(updateBadgeCount, 1000); // Wait a bit for session to be ready
    
    // Check for unread messages periodically when chat is closed
    setInterval(() => {
      if (!isOpen && sessionId) {
        // First check for new messages
        checkNewMessages();
        // Then update badge count (checkNewMessages will also update badge, but this ensures it's always accurate)
        setTimeout(updateBadgeCount, 1000);
      }
    }, 5000); // Check every 5 seconds
  }
  
  // Load user profile from localStorage if available
  const savedProfile = localStorage.getItem('chat_user_profile');
  if (savedProfile) {
    try {
      const profile = JSON.parse(savedProfile);
      if (profile.user_profile) userProfile = profile.user_profile;
      if (profile.user_initial) userInitial = profile.user_initial;
      if (profile.user_name) userName = profile.user_name;
    } catch (e) {
      console.error('Error loading profile from localStorage:', e);
    }
  }
  
  // Pre-load chat history from localStorage when page loads
  // This ensures messages are available even before opening chat
  if (userLoggedIn && sessionId) {
    const preloadMessages = loadMessagesFromLocal();
    if (preloadMessages.length > 0) {
      // Update lastMessageId from localStorage
      preloadMessages.forEach(msg => {
        if (msg.id > lastMessageId) {
          lastMessageId = msg.id;
        }
      });
    }
  }
  
  // Update status
  if (chatStatus) {
    const readyText = window.i18n ? window.i18n.translate('chat.readyToSupport') : 'Sẵn sàng hỗ trợ';
    chatStatus.textContent = readyText;
    chatStatus.setAttribute('data-i18n', 'chat.readyToSupport');
  }
  
  // Update chat status when language changes
  document.addEventListener('languageChanged', function() {
    if (chatStatus) {
      const readyText = window.i18n ? window.i18n.translate('chat.readyToSupport') : 'Sẵn sàng hỗ trợ';
      chatStatus.textContent = readyText;
    }
    // Trigger i18n update for all chat elements
    if (window.i18n) {
      window.i18n.updateTranslations();
    }
  });
  
})();
</script>

