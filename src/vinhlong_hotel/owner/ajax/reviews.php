<?php
require('../inc/essentials.php');
require('../../admin/inc/db_config.php');
ownerLogin();

$owner_id = getOwnerId();

if(isset($_POST['action']) && $_POST['action'] === 'reply_review') {
  $review_id = (int)($_POST['review_id'] ?? 0);
  $reply_text = trim($_POST['reply_text'] ?? '');
  
  if($review_id <= 0) {
    echo json_encode(['status' => 'error', 'msg' => 'ID đánh giá không hợp lệ!']);
    exit;
  }
  
  // Kiểm tra xem review có thuộc về owner không
  $check_query = select("
    SELECT rv.sr_no 
    FROM rating_review rv
    INNER JOIN rooms r ON rv.room_id = r.id
    WHERE rv.sr_no = ? AND r.owner_id = ?
  ", [$review_id, $owner_id], 'ii');
  
  if(!$check_query || mysqli_num_rows($check_query) === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Không tìm thấy đánh giá hoặc bạn không có quyền phản hồi!']);
    exit;
  }
  
  // Kiểm tra cột admin_reply có tồn tại không
  $cols_check = mysqli_query($con, "SHOW COLUMNS FROM `rating_review`");
  $existing_cols = [];
  while($col = mysqli_fetch_assoc($cols_check)){
    $existing_cols[] = $col['Field'];
  }
  
  $has_admin_reply_col = in_array('admin_reply', $existing_cols);
  $has_admin_reply_date_col = in_array('admin_reply_date', $existing_cols);
  
  if(!$has_admin_reply_col) {
    // Tạo cột nếu chưa có
    mysqli_query($con, "ALTER TABLE `rating_review` ADD COLUMN `admin_reply` TEXT NULL DEFAULT NULL COMMENT 'Phản hồi từ admin/owner' AFTER `seen`");
    $has_admin_reply_col = true;
  }
  
  if(!$has_admin_reply_date_col) {
    // Tạo cột nếu chưa có
    mysqli_query($con, "ALTER TABLE `rating_review` ADD COLUMN `admin_reply_date` DATETIME NULL DEFAULT NULL COMMENT 'Ngày phản hồi' AFTER `admin_reply`");
    $has_admin_reply_date_col = true;
  }
  
  // Cập nhật phản hồi
  if(!empty($reply_text)) {
    $update_query = "UPDATE `rating_review` SET `admin_reply`=?, `admin_reply_date`=NOW() WHERE `sr_no`=?";
    update($update_query, [$reply_text, $review_id], 'si');
    echo json_encode(['status' => 'success', 'msg' => 'Đã gửi phản hồi thành công!']);
  } else {
    // Xóa phản hồi nếu để trống
    $update_query = "UPDATE `rating_review` SET `admin_reply`=NULL, `admin_reply_date`=NULL WHERE `sr_no`=?";
    update($update_query, [$review_id], 'i');
    echo json_encode(['status' => 'success', 'msg' => 'Đã xóa phản hồi!']);
  }
} else {
  echo json_encode(['status' => 'error', 'msg' => 'Hành động không hợp lệ!']);
}
?>

