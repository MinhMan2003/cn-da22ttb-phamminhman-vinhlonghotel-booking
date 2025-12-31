<?php
require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['login'])) {
    echo "not_logged";
    exit;
}

$user_id = $_SESSION['uId'];
$msg = trim($_POST['message']);

if ($msg == "") {
    echo "empty";
    exit;
}

$q = "INSERT INTO chat_messages (user_id, sender, message) VALUES (?, 'user', ?)";
insert($q, [$user_id, $msg], "is");

echo "sent";
