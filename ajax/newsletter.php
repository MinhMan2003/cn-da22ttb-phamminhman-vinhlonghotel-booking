<?php
require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

header('Content-Type: text/plain; charset=utf-8');

if(!isset($_POST['newsletter_submit'])){
  echo 0; exit;
}

$frm = filteration($_POST);
$email = trim($frm['email'] ?? '');

if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
  echo 'invalid'; exit;
}

// Tạo bảng nếu chưa có
$create = "CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($con, $create);

// Kiểm tra tồn tại
$check = select("SELECT id FROM `newsletter_subscribers` WHERE `email`=? LIMIT 1", [$email], 's');
if($check && mysqli_num_rows($check) > 0){
  echo 'exists'; exit;
}

$q = "INSERT INTO `newsletter_subscribers`(`email`) VALUES(?)";
$res = insert($q, [$email], 's');
echo $res ? '1' : '0';
