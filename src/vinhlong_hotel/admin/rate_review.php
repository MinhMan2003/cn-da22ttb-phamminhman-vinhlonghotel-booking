<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();

if(isset($_GET['seen']))
{
    $frm_data = filteration($_GET);

    if($frm_data['seen']=='all'){
        $q = "UPDATE `rating_review` SET `seen`=?";
        $values = [1];
        update($q,$values,'i');
        alert('success','Đã xem tất cả đánh giá!');
    }
    else{
        $q = "UPDATE `rating_review` SET `seen`=? WHERE `sr_no`=?";
        $values = [1,$frm_data['seen']];
        update($q,$values,'ii');
        alert('success','Đã xem đánh giá!');
    }
}

if(isset($_GET['del']))
{
    $frm_data = filteration($_GET);

    if($frm_data['del']=='all'){
        mysqli_query($con,"DELETE FROM `rating_review`");
        alert('success','Đã xoá tất cả đánh giá!');
    }
    else{
        $q = "DELETE FROM `rating_review` WHERE `sr_no`=?";
        $values = [$frm_data['del']];
        delete($q,$values,'i');
        alert('success','Đã xoá đánh giá!');
    }
}

// Xử lý admin reply
if(isset($_POST['admin_reply']))
{
    $frm_data = filteration($_POST);
    $review_id = (int)$frm_data['review_id'];
    $reply_text = trim($frm_data['reply_text']);
    
    if(!empty($reply_text)){
        $q = "UPDATE `rating_review` SET `admin_reply`=?, `admin_reply_date`=NOW() WHERE `sr_no`=?";
        $values = [$reply_text, $review_id];
        update($q,$values,'si');
        alert('success','Đã gửi phản hồi!');
    } else {
        // Xóa reply nếu để trống
        $q = "UPDATE `rating_review` SET `admin_reply`=NULL, `admin_reply_date`=NULL WHERE `sr_no`=?";
        $values = [$review_id];
        update($q,$values,'i');
        alert('success','Đã xóa phản hồi!');
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Trang quản lý - Đánh giá</title>
<?php require('inc/links.php'); ?>

<style>

/* ===================== 🌙 DARK MODE NEON ===================== */

body.bg-light {
    background: #09101c !important;
    color: #d6e6ff !important;
    font-family: 'Segoe UI', sans-serif;
}

#main-content {
    background: #09101c !important;
}

/* ===================== 🔵 TIÊU ĐỀ TRANG ===================== */

#main-content h3 {
    color: #50b4ff !important;
    text-shadow: 0 0 12px rgba(80,180,255,0.55);
    font-weight: 700;
    letter-spacing: .6px;
}

/* ===================== 📦 CARD ===================== */

.card {
    background: linear-gradient(145deg,#0c141f,#112033) !important;
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.08);
    box-shadow:
        0 10px 25px rgba(0,0,0,0.45),
        inset 0 0 10px rgba(80,180,255,0.12);
}

/* ===================== 📊 BẢNG ===================== */

.table {
    color: #dce7ff !important;
}

.table thead {
    background: #122131 !important;
}

.table thead th {
    color: #50b4ff !important;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}

/* ===================== 📊 BẢNG ===================== */

.table tbody tr {
    background: #0b1624 !important;
}

.table tbody tr:hover {
    background: #152845 !important;
}

/* GIỮ CHỮ KHI HOVER – KHÔNG MẤT CHỮ */
.table tbody tr td {
    color: #dce7ff !important;
}

.table tbody tr:hover td {
    color: #ffffff !important;
}


/* ===================== 🔘 BUTTON ===================== */

.btn-dark {
    background: #0e2239 !important;
    border: 1px solid #50b4ff;
    color: #50b4ff !important;
    transition: 0.25s;
}
.btn-dark:hover {
    background: #14395e !important;
}

.btn-danger {
    box-shadow: 0 0 10px rgba(255,80,80,0.4);
}

/* Button trong bảng */
.table .btn-primary {
    background: #006eff;
    border: none;
}
.table .btn-primary:hover {
    background: #2888ff;
}

.table .btn-danger {
    background: #ff4c4c;
    border: none;
}
.table .btn-danger:hover {
    background: #ff6b6b;
}

</style>

</head>
<body class="bg-light">

<?php require('inc/header.php'); ?>

<div class="container-fluid" id="main-content">
  <div class="row">
    <div class="col-lg-10 ms-auto p-4 overflow-hidden">

      <h3 class="mb-4">Đánh giá</h3>

      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

          <!-- Nút thao tác -->
          <div class="text-end mb-4">
              <a href="?seen=all" class="btn btn-dark rounded-pill shadow-none btn-sm me-2">
                <i class="bi bi-check-all"></i> Đã xem tất cả
              </a>
              <a href="?del=all" class="btn btn-danger rounded-pill shadow-none btn-sm">
                <i class="bi bi-trash"></i> Xoá tất cả
              </a>
          </div>

          <!-- Bảng -->
          <div class="table-responsive-md">
            <table class="table table-hover border">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Tên phòng</th>
                  <th>Tên người dùng</th>
                  <th>Đánh giá</th>
                  <th width="25%">Nhận xét</th>
                  <th>Ảnh</th>
                  <th>Phản hồi</th>
                  <th>Ngày</th>
                  <th>Thao tác</th>
                </tr>
              </thead>

              <tbody>
              <?php 
                // Kiểm tra các cột có tồn tại không
                $cols_check = mysqli_query($con, "SHOW COLUMNS FROM `rating_review`");
                $existing_cols = [];
                while($col = mysqli_fetch_assoc($cols_check)){
                  $existing_cols[] = $col['Field'];
                }
                
                $has_images_col = in_array('images', $existing_cols);
                $has_helpful_col = in_array('helpful_count', $existing_cols);
                $has_admin_reply_col = in_array('admin_reply', $existing_cols);
                $has_admin_reply_date_col = in_array('admin_reply_date', $existing_cols);
                
                // Xây dựng SELECT với các cột có sẵn
                $select_cols = ['rr.sr_no', 'rr.booking_id', 'rr.room_id', 'rr.user_id', 'rr.rating', 'rr.review', 'rr.seen', 'rr.datentime'];
                
                if($has_helpful_col) $select_cols[] = 'rr.helpful_count';
                if($has_images_col) $select_cols[] = 'rr.images';
                if($has_admin_reply_col) $select_cols[] = 'rr.admin_reply';
                if($has_admin_reply_date_col) $select_cols[] = 'rr.admin_reply_date';
                
                $select_cols[] = 'uc.name AS uname';
                $select_cols[] = 'r.name AS rname';
                
                $q = "SELECT ".implode(', ', $select_cols)."
                      FROM rating_review rr
                      INNER JOIN user_cred uc ON rr.user_id = uc.id
                      INNER JOIN rooms r ON rr.room_id = r.id
                      ORDER BY rr.sr_no DESC";
                $data = mysqli_query($con,$q);
                $i=1;

                while($row = mysqli_fetch_assoc($data))
                {
                  $date = date('d-m-Y',strtotime($row['datentime']));
                  
                  // Xử lý hiển thị ảnh
                  $images = [];
                  $imagesHtml = '<span class="text-muted">-</span>';
                  
                  if(!$has_images_col){
                    // Chưa có cột images trong database
                    $imagesHtml = '<span class="text-muted small" title="Cần chạy file database/database_updates_reviews.sql">Chưa có cột</span>';
                  } else if(isset($row['images']) && !empty($row['images'])){
                    // Có dữ liệu ảnh
                    $images_raw = $row['images'];
                    $images_data = json_decode($images_raw, true);
                    
                    if(json_last_error() === JSON_ERROR_NONE && is_array($images_data) && !empty($images_data)){
                      $images = $images_data;
                      $imagesHtml = '<div class="d-flex gap-1 flex-wrap align-items-center">';
                      foreach(array_slice($images, 0, 3) as $img){
                        // Đảm bảo đường dẫn đúng
                        $img_path = trim($img);
                        // Loại bỏ ../ nếu có ở đầu
                        $img_path = preg_replace('#^\.\./#', '', $img_path);
                        // Loại bỏ / ở đầu nếu có
                        $img_path = ltrim($img_path, '/');
                        // Tạo đường dẫn đầy đủ
                        $full_path = '../'.$img_path;
                        $imagesHtml .= "<img src='$full_path' style='width:40px;height:40px;object-fit:cover;border-radius:4px;cursor:pointer;border:1px solid #dee2e6;' onclick='window.open(\"$full_path\",\"_blank\")' onerror='this.onerror=null;this.style.display=\"none\"' title='Click để xem ảnh lớn'>";
                      }
                      if(count($images) > 3){
                        $imagesHtml .= "<span class='badge bg-secondary'>+".(count($images)-3)."</span>";
                      }
                      $imagesHtml .= '</div>';
                    }
                  } else {
                    // Có cột nhưng review này không có ảnh (review cũ)
                    $imagesHtml = '<span class="text-muted">-</span>';
                  }
                  
                  $adminReply = ($has_admin_reply_col && !empty($row['admin_reply'])) ? htmlspecialchars($row['admin_reply']) : '';
                  $replyDate = ($has_admin_reply_date_col && !empty($row['admin_reply_date'])) ? date('d/m/Y', strtotime($row['admin_reply_date'])) : '';
                  
                  $replyHtml = '<button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#replyModal'.$row['sr_no'].'">';
                  if($adminReply){
                    $replyHtml .= '<i class="bi bi-check-circle text-success me-1"></i>Đã phản hồi';
                  } else {
                    $replyHtml .= '<i class="bi bi-reply me-1"></i>Phản hồi';
                  }
                  $replyHtml .= '</button>';

                  $actions = "";
                  if($row['seen']!=1){
                      $actions .= "<a href='?seen=$row[sr_no]' class='btn btn-sm rounded-pill btn-primary mb-2'>Đánh dấu đã đọc</a><br>";
                  }
                  $actions .= "<a href='?del=$row[sr_no]' class='btn btn-sm rounded-pill btn-danger'>Xóa</a>";

                  echo "
                  <tr>
                    <td>$i</td>
                    <td>{$row['rname']}</td>
                    <td>{$row['uname']}</td>
                    <td>".str_repeat('⭐', (int)$row['rating'])."</td>
                    <td>{$row['review']}</td>
                    <td>$imagesHtml</td>
                    <td>$replyHtml</td>
                    <td>$date</td>
                    <td>$actions</td>
                  </tr>";
                  
                  // Modal reply
                  echo "
                  <div class='modal fade' id='replyModal{$row['sr_no']}' tabindex='-1'>
                    <div class='modal-dialog'>
                      <div class='modal-content'>
                        <div class='modal-header'>
                          <h5 class='modal-title'>Phản hồi đánh giá</h5>
                          <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                        </div>
                        <form method='POST'>
                          <div class='modal-body'>
                            <div class='mb-3'>
                              <label class='form-label'>Đánh giá từ: <strong>{$row['uname']}</strong></label>
                              <div class='p-2 bg-light rounded'>{$row['review']}</div>
                            </div>
                            <div class='mb-3'>
                              <label class='form-label'>Phản hồi của bạn:</label>
                              <textarea name='reply_text' class='form-control' rows='3' placeholder='Nhập phản hồi...'>{$adminReply}</textarea>
                            </div>
                            ".(!empty($replyDate) ? "<small class='text-muted'>Đã phản hồi: $replyDate</small>" : "")."
                          </div>
                          <div class='modal-footer'>
                            <input type='hidden' name='review_id' value='{$row['sr_no']}'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Đóng</button>
                            <button type='submit' name='admin_reply' class='btn btn-primary'>Lưu phản hồi</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>";
                  
                  $i++;
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
