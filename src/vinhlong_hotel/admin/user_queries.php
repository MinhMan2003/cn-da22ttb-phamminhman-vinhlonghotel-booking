<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();

  if(isset($_GET['seen']))
  {
    $frm_data = filteration($_GET);

    if($frm_data['seen']=='all'){
      $q = "UPDATE `user_queries` SET `seen`=?";
      $values = [1];
      if(update($q,$values,'i')){
        alert('success','Đã xem tất cả!');
      }
      else{
        alert('error','Thao tác thất bại!');
      }
    }
    else{
      $q = "UPDATE `user_queries` SET `seen`=? WHERE `sr_no`=?";
      $values = [1,$frm_data['seen']];
      if(update($q,$values,'ii')){
        alert('success','Đã xem!');
      }
      else{
        alert('error','Thao tác thất bại!');
      }
    }
  }

  if(isset($_GET['del']))
  {
    $frm_data = filteration($_GET);

    if($frm_data['del']=='all'){
      $q = "DELETE FROM `user_queries`";
      if(mysqli_query($con,$q)){
        alert('success','Đã xoá tất cả!');
      }
      else{
        alert('error','Thao tác thất bại!');
      }
    }
    else{
      $q = "DELETE FROM `user_queries` WHERE `sr_no`=?";
      $values = [$frm_data['del']];
      if(delete($q,$values,'i')){
        alert('success','Đã xoá!');
      }
      else{
        alert('error','Thao tác thất bại!');
      }
    }
  }

  // API endpoint để check tin nhắn mới (cho AJAX)
  if(isset($_GET['check_new']))
  {
    header('Content-Type: application/json');
    $count_q = "SELECT COUNT(*) as total, SUM(CASE WHEN `seen`=0 THEN 1 ELSE 0 END) as unread FROM `user_queries`";
    $count_res = mysqli_query($con, $count_q);
    if($count_res && $count_row = mysqli_fetch_assoc($count_res)){
      $total_count = $count_row['total'] ?? 0;
      $unread_count = $count_row['unread'] ?? 0;
      echo json_encode(['total' => $total_count, 'unread' => $unread_count]);
    } else {
      echo json_encode(['total' => 0, 'unread' => 0]);
    }
    exit;
  }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trang quản lý - Liên hệ</title>
  <?php require('inc/links.php'); ?>
  <style>
    /* Highlight animation for new messages */
    @keyframes highlightNew {
      0% { background-color: #ffffff; }
      50% { background-color: #f8f9fa; }
      100% { background-color: #ffffff; }
    }
    .table-warning {
      animation: highlightNew 2s ease-in-out;
    }
    /* Auto-refresh indicator */
    .refresh-indicator {
      position: fixed;
      top: 70px;
      right: 20px;
      background: #28a745;
      color: white;
      padding: 8px 15px;
      border-radius: 20px;
      font-size: 12px;
      z-index: 1000;
      display: none;
    }
    .refresh-indicator.show {
      display: block;
      animation: fadeInOut 1s ease-in-out;
    }
    @keyframes fadeInOut {
      0%, 100% { opacity: 0; }
      50% { opacity: 1; }
    }
  </style>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4">Liên hệ</h3>

        <?php
          // Đếm số tin nhắn chưa đọc
          $unread_count = 0;
          $total_count = 0;
          $count_q = "SELECT COUNT(*) as total, SUM(CASE WHEN `seen`=0 THEN 1 ELSE 0 END) as unread FROM `user_queries`";
          $count_res = mysqli_query($con, $count_q);
          if($count_res && $count_row = mysqli_fetch_assoc($count_res)){
            $total_count = $count_row['total'] ?? 0;
            $unread_count = $count_row['unread'] ?? 0;
          }
        ?>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <!-- Thống kê -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <div>
                <span class="badge bg-primary rounded-pill me-2">Tổng: <?php echo $total_count; ?></span>
                <?php if($unread_count > 0): ?>
                  <span class="badge bg-warning rounded-pill">Chưa đọc: <?php echo $unread_count; ?></span>
                <?php endif; ?>
              </div>
              <div>
                <a href="?seen=all" class="btn btn-dark rounded-pill shadow-none btn-sm">
                  <i class="bi bi-check-all"></i> Đã xem tất cả
                </a>
                <a href="?del=all" class="btn btn-danger rounded-pill shadow-none btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa tất cả tin nhắn?');">
                  <i class="bi bi-trash"></i> Xoá tất cả
                </a>
              </div>
            </div>

            <div class="table-responsive-md" style="height: 450px; overflow-y: scroll;">
              <table class="table table-hover border">
                <thead class="sticky-top">
                  <tr class="bg-dark text-light">
                  <th scope="col">#</th>
                  <th scope="col">Họ và tên</th>
                  <th scope="col">Email</th>
                  <th scope="col" width="20%">Chủ đề</th>
                  <th scope="col" width="30%">Nội dung</th>
                  <th scope="col">Ngày gửi</th>
                  <th scope="col">Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    // Query để lấy tất cả tin nhắn, sắp xếp theo mới nhất
                    $q = "SELECT * FROM `user_queries` ORDER BY `sr_no` DESC";
                    $data = mysqli_query($con,$q);
                    
                    if(!$data){
                      $error = mysqli_error($con);
                      echo "<tr><td colspan='7' class='text-center text-danger py-4'>";
                      echo "<i class='bi bi-exclamation-triangle-fill fs-1 d-block mb-2'></i>";
                      echo "Lỗi truy vấn database: " . htmlspecialchars($error);
                      echo "<br><small class='text-muted'>Vui lòng kiểm tra kết nối database</small>";
                      echo "</td></tr>";
                    }
                    else if(mysqli_num_rows($data) == 0){
                      echo "<tr><td colspan='7' class='text-center text-muted py-5'>";
                      echo "<i class='bi bi-inbox fs-1 d-block mb-3'></i>";
                      echo "<h5 class='mb-2'>Chưa có tin nhắn nào</h5>";
                      echo "<small>Tin nhắn từ khách hàng sẽ hiển thị ở đây</small>";
                      echo "</td></tr>";
                    }
                    else{
                      $i=1;
                      while($row = mysqli_fetch_assoc($data))
                      {
                        // Format ngày giờ
                        $datentime = $row['datentime'] ?? date('Y-m-d H:i:s');
                        $date = date('d-m-Y H:i',strtotime($datentime));
                        
                        // Xử lý trạng thái đã đọc/chưa đọc
                        $seen='';
                        $is_unread = (isset($row['seen']) && $row['seen'] != 1);
                        
                        if($is_unread){
                          $seen = "<a href='?seen=$row[sr_no]' class='btn btn-sm rounded-pill btn-primary'>Đánh dấu là đã đọc</a> <br>";
                        }
                        $seen.="<a href='?del=$row[sr_no]' class='btn btn-sm rounded-pill btn-danger mt-2' onclick='return confirm(\"Bạn có chắc chắn muốn xóa tin nhắn này?\");'>Xóa</a>";

                        // Escape HTML để bảo mật
                        $name = htmlspecialchars($row['name'] ?? 'Chưa có tên', ENT_QUOTES, 'UTF-8');
                        $email = htmlspecialchars($row['email'] ?? 'Chưa có email', ENT_QUOTES, 'UTF-8');
                        $subject = htmlspecialchars($row['subject'] ?? 'Chưa có tiêu đề', ENT_QUOTES, 'UTF-8');
                        $message = htmlspecialchars($row['message'] ?? 'Chưa có nội dung', ENT_QUOTES, 'UTF-8');
                        
                        // Highlight tin nhắn chưa đọc
                        $row_class = $is_unread ? 'table-warning' : '';
                        $unread_badge = $is_unread ? '<span class="badge bg-warning text-dark ms-2">Mới</span>' : '';

                        echo<<<query
                          <tr class="$row_class">
                            <td>$i</td>
                            <td>$name $unread_badge</td>
                            <td><a href="mailto:$email" class="text-decoration-none">$email</a></td>
                            <td>$subject</td>
                            <td>$message</td>
                            <td>$date</td>
                            <td>$seen</td>
                          </tr>
                        query;
                        $i++;
                      }
                    }
                  ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>


      </div>
    </div>
  </div>
  <style>
/* =============================
   GLOBAL DARK MODE
   ============================= */
body.bg-light{
  background:#0d1117 !important;
  color:#e6e6e6 !important;
}

#main-content{
  background:#0d1117 !important;
}

/* =============================
   SIDEBAR & NAVBAR
   ============================= */
.navbar{
  background:#0f1622 !important;
  border-bottom:1px solid rgba(255,255,255,0.08);
}
.navbar a, .navbar-brand{
  color:#e6e6e6 !important;
}
.navbar a:hover{
  color:#58a6ff !important;
}

#dashboard-menu{
  background:#0f1622 !important;
  border-right:1px solid rgba(255,255,255,0.08);
}
#dashboard-menu a{
  color:#cbd5e1 !important;
}
#dashboard-menu a:hover{
  background:#152033 !important;
  color:#58a6ff !important;
}

/* =============================
   PAGE TITLE
   ============================= */
h3{
  color:#58a6ff !important;
  font-weight:700;
  text-shadow:0 0 20px rgba(88,166,255,0.6);
  letter-spacing:1px;
}

/* =============================
   CARD
   ============================= */
.card{
  background:#0f1622 !important;
  border-radius:18px !important;
  border:1px solid rgba(255,255,255,0.08) !important;
  color:#e6e6e6 !important;
}

/* =============================
   BUTTON
   ============================= */
.btn-dark{
  background:#1e2737 !important;
  color:#e6e6e6 !important;
  border:none !important;
}
.btn-dark:hover{
  background:#2a3447 !important;
}

.btn-danger{
  background:#e63946 !important;
  border:none !important;
}
.btn-danger:hover{
  background:#ff5e6c !important;
}

.btn-primary{
  background:#58a6ff !important;
  color:#0d1117 !important;
  border:none !important;
}
.btn-primary:hover{
  background:#7bb8ff !important;
}

/* =============================
   TABLE BASE
   ============================= */
.table{
  color:#e6e6e6 !important;
  border-color:rgba(255,255,255,0.08) !important;
}

thead tr{
  background:#111927 !important;
}

thead th{
  color:#58a6ff !important;
  font-weight:600;
  border-bottom:1px solid rgba(255,255,255,0.08) !important;
}

/* GIỮ CHỮ KHI HOVER – KHÔNG MẤT CHỮ */
tbody tr:hover{
  background:#1b2538 !important;
}
tbody tr:hover td{
  color:#ffffff !important;
}

/* =============================
   SCROLLABLE TABLE BOX
   ============================= */
.table-responsive-md{
  background:#0f1622;
  border-radius:12px;
  padding:0;
  border:1px solid rgba(255,255,255,0.08);
}

.table-responsive-md::-webkit-scrollbar{
  width:7px;
}
.table-responsive-md::-webkit-scrollbar-thumb{
  background:#2d3a4f;
  border-radius:10px;
}

/* =============================
   STICKY HEADER FIX
   ============================= */
thead.sticky-top{
  z-index:5 !important;
}

/* =============================
   UNREAD MESSAGE HIGHLIGHT
   ============================= */
/* Override Bootstrap table-warning với nền trắng */
tr.table-warning,
tbody tr.table-warning,
table tbody tr.table-warning{
  background:#ffffff !important;
  background-color:#ffffff !important;
  border-left:3px solid #0d6efd !important;
}
tr.table-warning:hover,
tbody tr.table-warning:hover,
table tbody tr.table-warning:hover{
  background:#f8f9fa !important;
  background-color:#f8f9fa !important;
}
tr.table-warning td,
tbody tr.table-warning td,
table tbody tr.table-warning td{
  color:#1f2937 !important;
  font-weight:500;
  background-color:transparent !important;
}

/* =============================
   BADGE STYLING
   ============================= */
.badge{
  font-weight:600;
  padding:6px 12px;
}
.bg-warning{
  background:#fbbf24 !important;
  color:#1a1a1a !important;
}
.bg-primary{
  background:#58a6ff !important;
  color:#0d1117 !important;
}

/* =============================
   EMAIL LINK
   ============================= */
a[href^="mailto:"]{
  color:#58a6ff !important;
  text-decoration:none;
}
a[href^="mailto:"]:hover{
  color:#7bb8ff !important;
  text-decoration:underline;
}
</style>


  <?php require('inc/scripts.php'); ?>

<script>
// Auto-refresh để cập nhật tin nhắn mới
(function() {
  let lastUnreadCount = <?php echo $unread_count; ?>;
  let refreshInterval = null;
  
  // Function để check tin nhắn mới
  function checkNewMessages() {
    fetch('user_queries.php?check_new=1', {
      method: 'GET',
      cache: 'no-cache',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      const newUnreadCount = data.unread || 0;
      
      if(newUnreadCount > lastUnreadCount) {
        // Có tin nhắn mới
        const newMessagesCount = newUnreadCount - lastUnreadCount;
        showNotification('Có ' + newMessagesCount + ' tin nhắn mới!');
        lastUnreadCount = newUnreadCount;
        
        // Reload trang để hiển thị tin nhắn mới
        setTimeout(() => {
          window.location.reload();
        }, 2000);
      }
    })
    .catch(error => {
      console.error('Error checking new messages:', error);
    });
  }
  
  // Function hiển thị thông báo
  function showNotification(message) {
    // Tạo notification element
    const notification = document.createElement('div');
    notification.className = 'refresh-indicator show';
    notification.innerHTML = '<i class="bi bi-bell-fill me-2"></i>' + message;
    document.body.appendChild(notification);
    
    // Xóa sau 3 giây
    setTimeout(() => {
      notification.remove();
    }, 3000);
  }
  
  // Auto-refresh mỗi 30 giây
  refreshInterval = setInterval(checkNewMessages, 30000);
  
  // Cleanup khi trang bị unload
  window.addEventListener('beforeunload', () => {
    if(refreshInterval) {
      clearInterval(refreshInterval);
    }
  });
})();
</script>

</body>
</html>