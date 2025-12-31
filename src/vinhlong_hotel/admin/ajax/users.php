<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../inc/essentials.php';
require __DIR__ . '/../inc/db_config.php';
adminLogin();

if(session_status() !== PHP_SESSION_ACTIVE) session_start();
$action = $_REQUEST['action'] ?? 'list';
$mysqli = $con;

// build available columns list to be robust
$cols = [];
if($cr = $mysqli->query("SHOW COLUMNS FROM `user_cred`")){
    while($r = $cr->fetch_assoc()) $cols[] = $r['Field'];
    $cr->free();
}

// search fields to use
$search_candidates = ['name','email','phonenum','phone','mobile','address'];
$search_fields = array_values(array_intersect($search_candidates, $cols));
$concat_search = $search_fields ? "CONCAT_WS(' ', " . implode(', ', array_map(function($f){ return "`$f`"; }, $search_fields)) . ")" : "`name`";

if($action === 'list'){
    $q = trim($_GET['q'] ?? '');
    $page = max(1, (int)($_GET['page'] ?? 1));
    $per_page = max(1, min(500, (int)($_GET['per_page'] ?? 25)));
    $offset = ($page - 1) * $per_page;

    // total
    if($q !== ''){
        $like = "%{$q}%";
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM `user_cred` WHERE {$concat_search} LIKE ?");
        $stmt->bind_param("s", $like);
    } else {
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM `user_cred`");
    }
    $stmt->execute();
    $stmt->bind_result($total);
    $stmt->fetch();
    $stmt->close();

    // data - đảm bảo lấy đúng gender từ database
    if($q !== ''){
        $stmt = $mysqli->prepare("SELECT * FROM `user_cred` WHERE {$concat_search} LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("sii", $like, $per_page, $offset);
    } else {
        $stmt = $mysqli->prepare("SELECT * FROM `user_cred` ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $per_page, $offset);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $data = [];
    while($row = $res->fetch_assoc()){
        // Lấy gender từ database, đảm bảo không lấy nhầm is_verified
        $gender = '';
        if(isset($row['gender']) && !empty($row['gender'])){
            $gender = trim($row['gender']);
        }
        
        $data[] = [
            'id' => isset($row['id']) ? (int)$row['id'] : null,
            'name' => $row['name'] ?? '',
            'email' => $row['email'] ?? '',
            'phone' => $row['phonenum'] ?? ($row['phone'] ?? ($row['mobile'] ?? '')),
            'address' => $row['address'] ?? '',
            'dob' => $row['dob'] ?? '',
            'gender' => $gender, // CHỈ trả về gender, KHÔNG phải is_verified
            'status' => isset($row['status']) ? (int)$row['status'] : 0,
            'created_at' => $row['datentime'] ?? ($row['created_at'] ?? '')
        ];
    }
    $stmt->close();

    echo json_encode(['status'=>'success','data'=>$data,'total'=>(int)$total,'page'=>$page,'per_page'=>$per_page]);
    exit;
}

if($action === 'deactivate' || $action === 'delete'){
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){ echo json_encode(['status'=>'error','msg'=>'Yêu cầu không hợp lệ']); exit; }
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $token = $_POST['csrf_token'] ?? '';
    if(!$id){ echo json_encode(['status'=>'error','msg'=>'ID không hợp lệ']); exit; }
    if(!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)){ echo json_encode(['status'=>'error','msg'=>'CSRF token không hợp lệ']); exit; }

    if(in_array('status',$cols)){
        $stmt = $mysqli->prepare("UPDATE `user_cred` SET `status` = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        if($stmt->execute() && $stmt->affected_rows>0){
            echo json_encode(['status'=>'success','msg'=>'Vô hiệu hóa thành công']);
        } else {
            echo json_encode(['status'=>'error','msg'=>'Không tìm thấy hoặc không thay đổi']);
        }
        $stmt->close();
        exit;
    } else {
        echo json_encode(['status'=>'error','msg'=>'Bảng không hỗ trợ trường status']); exit;
    }
}

if($action === 'toggle_status'){
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){ echo json_encode(['status'=>'error']); exit; }
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $val = isset($_POST['value']) ? (int)$_POST['value'] : 0;
    if(!$id){ echo json_encode(['status'=>'error']); exit; }
    if(in_array('status',$cols)){
        $stmt = $mysqli->prepare("UPDATE `user_cred` SET `status` = ? WHERE id = ?");
        $stmt->bind_param("ii",$val,$id);
        echo ($stmt->execute() && $stmt->affected_rows>0) ? json_encode(['status'=>'success']) : json_encode(['status'=>'error']);
        $stmt->close();
        exit;
    }
    echo json_encode(['status'=>'error']); exit;
}

// Removed verify action - no longer needed

echo json_encode(['status'=>'error','msg'=>'Hành động không xác định']);
exit;
?>