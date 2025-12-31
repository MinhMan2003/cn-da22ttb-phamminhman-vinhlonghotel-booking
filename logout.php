<?php 

  require('admin/inc/essentials.php');

  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  
  // Clear all session data
  $_SESSION = array();
  
  // Destroy session cookie
  if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
  }
  
  session_destroy();
  
  // Output HTML with JavaScript to clear localStorage before redirect
  ?>
  <!DOCTYPE html>
  <html lang="vi">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đang đăng xuất...</title>
  </head>
  <body>
    <script>
      // Clear all localStorage data related to chat and user
      try {
        localStorage.removeItem('chat_session_id');
        localStorage.removeItem('chat_history');
        localStorage.removeItem('chat_user_profile');
        localStorage.removeItem('lastMessageId');
        
        // Clear all localStorage items that start with 'chat_'
        const keysToRemove = [];
        for (let i = 0; i < localStorage.length; i++) {
          const key = localStorage.key(i);
          if (key && key.startsWith('chat_')) {
            keysToRemove.push(key);
          }
        }
        keysToRemove.forEach(key => localStorage.removeItem(key));
      } catch (e) {
        console.error('Error clearing localStorage:', e);
      }
      
      // Redirect to index page
      window.location.href = 'index.php';
    </script>
    <div style="text-align: center; padding: 50px; font-family: Arial, sans-serif;">
      <p>Đang đăng xuất...</p>
      <p>Nếu không tự động chuyển hướng, <a href="index.php">click vào đây</a></p>
    </div>
  </body>
  </html>
  <?php
  exit;

?>