<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();

// Kiểm tra và tạo cột seen nếu chưa có
$check_seen = mysqli_query($con, "SHOW COLUMNS FROM `newsletter_subscribers` LIKE 'seen'");
if(!$check_seen || mysqli_num_rows($check_seen) == 0){
  mysqli_query($con, "ALTER TABLE `newsletter_subscribers` ADD COLUMN `seen` TINYINT(1) NOT NULL DEFAULT 0 AFTER `created_at`");
}

// Xử lý đánh dấu đã xem
if(isset($_GET['seen'])){
  $frm_data = filteration($_GET);
  
  if($frm_data['seen'] == 'all'){
    $q = "UPDATE `newsletter_subscribers` SET `seen` = 1";
    if(mysqli_query($con, $q)){
      alert('success', 'Đã đánh dấu tất cả là đã xem!');
    } else {
      alert('error', 'Thao tác thất bại!');
    }
  } else {
    $q = "UPDATE `newsletter_subscribers` SET `seen` = 1 WHERE `id` = ?";
    $values = [(int)$frm_data['seen']];
    if(update($q, $values, 'i')){
      alert('success', 'Đã đánh dấu là đã xem!');
    } else {
      alert('error', 'Thao tác thất bại!');
    }
  }
  redirect('newsletter.php');
}

// Đếm số email chưa xem
$unread_count = 0;
$total_count = 0;
$count_q = "SELECT COUNT(*) as total, SUM(CASE WHEN `seen`=0 THEN 1 ELSE 0 END) as unread FROM `newsletter_subscribers`";
$count_res = mysqli_query($con, $count_q);
if($count_res && $count_row = mysqli_fetch_assoc($count_res)){
  $total_count = $count_row['total'] ?? 0;
  $unread_count = $count_row['unread'] ?? 0;
}

$subs = mysqli_query($con, "SELECT * FROM `newsletter_subscribers` ORDER BY `seen` ASC, `id` DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Trang quản lý - Newsletter</title>
  <?php require('inc/links.php'); ?>
  <style>
    /* Highlight email chưa xem - Nền trắng giống user_queries.php */
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
    tr.table-warning .badge {
      font-weight: 600;
    }
  </style>
</head>
<body class="bg-light">
<?php require('inc/header.php'); ?>

<div class="container-fluid" id="main-content">
  <div class="row">
    <div class="col-lg-10 ms-auto p-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Danh sách đăng ký nhận email</h3>
        <div>
          <span class="badge bg-primary rounded-pill me-2">Tổng: <?php echo $total_count; ?></span>
          <?php if($unread_count > 0): ?>
            <span class="badge bg-warning rounded-pill me-2">Chưa xem: <?php echo $unread_count; ?></span>
          <?php endif; ?>
          <?php if($unread_count > 0): ?>
            <a href="?seen=all" class="btn btn-dark rounded-pill shadow-none btn-sm">
              <i class="bi bi-check-all"></i> Đã xem tất cả
            </a>
          <?php endif; ?>
        </div>
      </div>

      <div class="card shadow border-0">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-dark">
                <tr>
                  <th>#</th>
                  <th>Email</th>
                  <th>Ngày đăng ký</th>
                  <th>Trạng thái</th>
                  <th>Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $i=1;
                  if(mysqli_num_rows($subs)){
                    while($row = mysqli_fetch_assoc($subs)){
                      $seen = isset($row['seen']) ? (int)$row['seen'] : 0;
                      $is_unread = ($seen == 0);
                      $row_class = $is_unread ? 'table-warning' : '';
                      $status_badge = $is_unread 
                        ? '<span class="badge bg-warning text-dark">Chưa xem</span>' 
                        : '<span class="badge bg-success">Đã xem</span>';
                      $seen_btn = $is_unread 
                        ? "<a href='?seen={$row['id']}' class='btn btn-sm btn-primary rounded-pill'>Đánh dấu đã xem</a>"
                        : "<span class='text-muted small'>Đã xem</span>";
                      
                      echo "<tr class='{$row_class}'>
                              <td>{$i}</td>
                              <td>".htmlspecialchars($row['email'])."</td>
                              <td>".date('d/m/Y H:i', strtotime($row['created_at']))."</td>
                              <td>{$status_badge}</td>
                              <td>{$seen_btn}</td>
                            </tr>";
                      $i++;
                    }
                  } else {
                    echo "<tr><td colspan='5' class='text-center text-muted'>Chưa có đăng ký nào</td></tr>";
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

<?php require('inc/scripts.php'); ?>
</body>
</html>
