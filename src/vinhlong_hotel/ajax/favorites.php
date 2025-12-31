<?php
require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');
ensureFavoritesTable();

if (session_status() === PHP_SESSION_NONE) session_start();

function json_resp($data){
  header('Content-Type: application/json');
  echo json_encode($data);
  exit;
}

if(!isset($_SESSION['login']) || $_SESSION['login'] !== true){
  json_resp(['status'=>'login_required']);
}

$uid = $_SESSION['uId'] ?? 0;
if(!$uid){
  json_resp(['status'=>'login_required']);
}

// Toggle favorite
if(isset($_POST['toggle']) && isset($_POST['room_id'])){
  $room_id = (int)$_POST['room_id'];
  if($room_id <= 0){
    json_resp(['status'=>'error','msg'=>'invalid_room']);
  }

  // Check exists
  $check = select("SELECT id FROM favorites WHERE user_id=? AND room_id=? LIMIT 1", [$uid,$room_id], 'ii');
  if($check && mysqli_num_rows($check)){
    delete("DELETE FROM favorites WHERE user_id=? AND room_id=?", [$uid,$room_id], 'ii');
    json_resp(['status'=>'removed']);
  } else {
    insert("INSERT INTO favorites(user_id, room_id) VALUES(?,?)", [$uid,$room_id], 'ii');
    json_resp(['status'=>'added']);
  }
}

// Get favorites
if(isset($_GET['list'])){
  $res = select("SELECT room_id FROM favorites WHERE user_id=?", [$uid], 'i');
  $ids = [];
  if($res){
    while($row = mysqli_fetch_assoc($res)){
      $ids[] = (int)$row['room_id'];
    }
  }
  json_resp(['status'=>'ok','rooms'=>$ids]);
}

json_resp(['status'=>'error','msg'=>'invalid_request']);
