<?php
require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');
require('../admin/inc/promos_helper.php');

if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['login']) || $_SESSION['login']!==true){
  http_response_code(401);
  echo json_encode(['status'=>'login_required']);
  exit;
}

ensureSavedPromoTable($con);

$uid = $_SESSION['uId'] ?? 0;
header('Content-Type: application/json');

function resp($arr){ echo json_encode($arr); exit; }

if(isset($_GET['list'])){
  $res = select("SELECT promo_code FROM promo_saved WHERE user_id=? ORDER BY created_at DESC", [$uid], 'i');
  $codes = [];
  if($res){
    while($row = mysqli_fetch_assoc($res)){ $codes[] = strtoupper($row['promo_code']); }
  }
  resp(['status'=>'ok','codes'=>$codes]);
}

if(isset($_POST['save']) && isset($_POST['code'])){
  $code = strtoupper(trim($_POST['code']));
  if($code === ''){
    resp(['status'=>'error','msg'=>'empty_code']);
  }
  $ins = insert("INSERT IGNORE INTO promo_saved(user_id, promo_code) VALUES(?,?)", [$uid,$code], 'is');
  if($ins !== false){
    resp(['status'=>'ok','code'=>$code]);
  }
  resp(['status'=>'error','msg'=>'db']);
}

resp(['status'=>'error','msg'=>'invalid']);
