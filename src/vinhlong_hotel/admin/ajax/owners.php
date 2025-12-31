<?php
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

/* GET ALL OWNERS */
if(isset($_POST['get_all_owners']))
{
  $con = $GLOBALS['con'];
  // Tự kích hoạt các tài khoản còn trạng thái chờ duyệt (0)
  mysqli_query($con, "UPDATE `hotel_owners` SET `status` = 1 WHERE `status` = 0");
  $search  = filter_var($_POST['search'] ?? '', FILTER_SANITIZE_STRING);
  $status  = $_POST['status'] ?? '';

  $conditions = [];
  $params = [];
  $types  = '';

  if($status === 'active'){
    $conditions[] = "`status` = 1";
  } else if($status === 'inactive'){
    $conditions[] = "`status` = -1";
  }

  if($search !== ''){
    $conditions[] = "(`name` LIKE ? OR `email` LIKE ? OR `hotel_name` LIKE ? OR `phone` LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'ssss';
  }

  $where = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
  $query = "SELECT ho.*, 
            (SELECT COUNT(*) FROM rooms WHERE owner_id = ho.id AND removed=0) AS room_count
            FROM `hotel_owners` ho 
            $where
            ORDER BY ho.created_at DESC";

  if($types){
    $res = select($query, $params, $types);
  } else {
    $res = mysqli_query($con, $query);
  }
  
  $i=1;
  $data = "";

  if(mysqli_num_rows($res) == 0){
    $data = "
      <tr>
        <td colspan='10' class='empty-state'>
          <i class='bi bi-inbox d-block mb-3'></i>
          <p>Không tìm thấy chủ khách sạn nào</p>
        </td>
      </tr>
    ";
    echo $data;
    exit;
  }

  while($row = mysqli_fetch_assoc($res))
  {
    $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($row['phone'] ?? '—', ENT_QUOTES, 'UTF-8');
    $hotel_name = htmlspecialchars($row['hotel_name'] ?? '—', ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars(mb_substr($row['address'] ?? '—', 0, 50), ENT_QUOTES, 'UTF-8');
    $room_count = (int)$row['room_count'];
    $created_at = date('d/m/Y', strtotime($row['created_at']));

    // Status badge
    if($row['status'] == 1){
      $status_badge = "<span class='badge bg-success'>Đã kích hoạt</span>";
      $action_btn = "<button onclick='toggle_status({$row['id']},0)' class='btn btn-warning shadow-none btn-action' title='Khóa tài khoản'>
                      <i class='bi bi-lock'></i>
                    </button>";
    } else {
      $status_badge = "<span class='badge bg-danger'>Đã khóa</span>";
      $action_btn = "<button onclick='toggle_status({$row['id']},1)' class='btn btn-success shadow-none btn-action' title='Kích hoạt tài khoản'>
                      <i class='bi bi-unlock'></i>
                    </button>";
    }

    $data.="
      <tr class='align-middle'>
        <td class='text-center'>$i</td>
        <td><strong>$name</strong></td>
        <td><small>$email</small></td>
        <td>$phone</td>
        <td>$hotel_name</td>
        <td class='address-cell' title='{$row['address']}'>$address</td>
        <td class='text-center'><span class='badge bg-info'>$room_count phòng</span></td>
        <td class='text-center'>$status_badge</td>
        <td class='text-center'><small>$created_at</small></td>
        <td class='text-center'>
          <div class='d-flex gap-1 justify-content-center'>
            $action_btn
            <button onclick='edit_owner({$row['id']})' 
                    class='btn btn-info shadow-none btn-action' 
                    title='Sửa thông tin'>
              <i class='bi bi-pencil'></i>
            </button>
            <button onclick='view_owner_details({$row['id']})' 
                    class='btn btn-primary shadow-none btn-action' 
                    data-bs-toggle='modal' 
                    data-bs-target='#view-owner-modal'
                    title='Xem chi tiết'>
              <i class='bi bi-eye'></i>
            </button>
            <button onclick='delete_owner({$row['id']})' 
                    class='btn btn-danger shadow-none btn-action' 
                    title='Xóa'>
              <i class='bi bi-trash'></i>
            </button>
          </div>
        </td>
      </tr>";
    $i++;
  }

  echo $data;
}

/* TOGGLE STATUS (Duyệt/Khóa/Kích hoạt) */
if(isset($_POST['toggle_status']))
{
  $frm_data = filteration($_POST);
  $owner_id = (int)$frm_data['toggle_status'];
  $new_status = (int)$frm_data['value'];

  $q = "UPDATE `hotel_owners` SET `status`=? WHERE `id`=?";
  $v = [$new_status, $owner_id];

  echo update($q,$v,'ii') ? 1 : 0;
}

/* GET OWNER DETAILS */
if(isset($_POST['get_owner']))
{
  // #region agent log
  file_put_contents(__DIR__ . '/../../.cursor/debug.log', json_encode(['location' => 'admin/ajax/owners.php:get_owner', 'message' => 'get_owner request received', 'data' => ['POST' => $_POST], 'timestamp' => time()]) . "\n", FILE_APPEND);
  // #endregion
  
  $frm_data = filteration($_POST);
  $owner_id = (int)$frm_data['get_owner'];
  
  // #region agent log
  file_put_contents(__DIR__ . '/../../.cursor/debug.log', json_encode(['location' => 'admin/ajax/owners.php:get_owner', 'message' => 'Owner ID extracted', 'data' => ['owner_id' => $owner_id], 'timestamp' => time()]) . "\n", FILE_APPEND);
  // #endregion

  $res = select("SELECT * FROM `hotel_owners` WHERE `id`=?", [$owner_id], 'i');
  if($res && mysqli_num_rows($res) > 0){
    $owner = mysqli_fetch_assoc($res);
    // #region agent log
    file_put_contents(__DIR__ . '/../../.cursor/debug.log', json_encode(['location' => 'admin/ajax/owners.php:get_owner', 'message' => 'Owner found', 'data' => ['id' => $owner['id'], 'name' => $owner['name']], 'timestamp' => time()]) . "\n", FILE_APPEND);
    // #endregion
    echo json_encode($owner);
  } else {
    // #region agent log
    file_put_contents(__DIR__ . '/../../.cursor/debug.log', json_encode(['location' => 'admin/ajax/owners.php:get_owner', 'message' => 'Owner not found', 'data' => ['owner_id' => $owner_id], 'timestamp' => time()]) . "\n", FILE_APPEND);
    // #endregion
    echo json_encode(['error' => 'Không tìm thấy']);
  }
}

/* DELETE OWNER */
if(isset($_POST['delete_owner']))
{
  $frm_data = filteration($_POST);
  $owner_id = (int)$frm_data['delete_owner'];

  // Kiểm tra xem owner có phòng không
  $room_check = select("SELECT COUNT(*) AS c FROM rooms WHERE owner_id=? AND removed=0", [$owner_id], 'i');
  $room_count = mysqli_fetch_assoc($room_check)['c'];

  if($room_count > 0){
    echo json_encode(['error' => "Không thể xóa! Chủ khách sạn này đang có $room_count phòng."]);
    exit;
  }

  // Xóa owner
  if(delete("DELETE FROM `hotel_owners` WHERE `id`=?", [$owner_id], 'i')){
    echo json_encode(['success' => 1]);
  } else {
    echo json_encode(['error' => 'Không thể xóa']);
  }
}

/* ADD OWNER */
if(isset($_POST['add_owner']))
{
  $frm_data = filteration($_POST);
  $name = $frm_data['name'] ?? '';
  $email = $frm_data['email'] ?? '';
  $password = $frm_data['password'] ?? '';
  $phone = $frm_data['phone'] ?? '';
  $hotel_name = $frm_data['hotel_name'] ?? '';
  $address = $frm_data['address'] ?? '';
  $status = (int)($frm_data['status'] ?? 0);

  // Validation
  if(empty($name) || empty($email) || empty($password) || empty($hotel_name)){
    echo json_encode(['error' => 'Vui lòng điền đầy đủ thông tin bắt buộc!']);
    exit;
  }

  // Kiểm tra email đã tồn tại chưa
  $check_email = select("SELECT id FROM hotel_owners WHERE email=?", [$email], 's');
  if($check_email && mysqli_num_rows($check_email) > 0){
    echo json_encode(['error' => 'Email này đã được sử dụng!']);
    exit;
  }

  // Hash password
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  // Insert
  $query = "INSERT INTO hotel_owners (name, email, password, phone, hotel_name, address, status) VALUES (?,?,?,?,?,?,?)";
  $values = [$name, $email, $hashed_password, $phone, $hotel_name, $address, $status];
  
  if(insert($query, $values, 'ssssssi')){
    echo json_encode(['success' => 1]);
  } else {
    echo json_encode(['error' => 'Không thể thêm chủ khách sạn!']);
  }
}

/* UPDATE OWNER */
if(isset($_POST['update_owner']))
{
  // #region agent log
  file_put_contents(__DIR__ . '/../../.cursor/debug.log', json_encode(['location' => 'admin/ajax/owners.php:update_owner', 'message' => 'update_owner request received', 'data' => ['POST_keys' => array_keys($_POST), 'update_owner_value' => $_POST['update_owner'] ?? 'not_set'], 'timestamp' => time()]) . "\n", FILE_APPEND);
  // #endregion
  
  $frm_data = filteration($_POST);
  // Lấy owner_id từ update_owner (giá trị được gửi) hoặc từ owner_id field
  $owner_id = (int)($_POST['update_owner'] ?? $frm_data['owner_id'] ?? 0);
  
  // #region agent log
  file_put_contents(__DIR__ . '/../../.cursor/debug.log', json_encode(['location' => 'admin/ajax/owners.php:update_owner', 'message' => 'Owner ID extracted', 'data' => ['owner_id' => $owner_id, 'owner_id_from_form' => $frm_data['owner_id'] ?? 'not_set'], 'timestamp' => time()]) . "\n", FILE_APPEND);
  // #endregion
  
  $name = $frm_data['name'] ?? '';
  $email = $frm_data['email'] ?? '';
  $password = $frm_data['password'] ?? '';
  $phone = $frm_data['phone'] ?? '';
  $hotel_name = $frm_data['hotel_name'] ?? '';
  $address = $frm_data['address'] ?? '';
  $status = (int)($frm_data['status'] ?? 0);

  // #region agent log
  file_put_contents(__DIR__ . '/../../.cursor/debug.log', json_encode(['location' => 'admin/ajax/owners.php:update_owner', 'message' => 'Form data extracted', 'data' => ['name' => $name, 'email' => $email, 'has_password' => !empty($password), 'status' => $status], 'timestamp' => time()]) . "\n", FILE_APPEND);
  // #endregion

  // Validation
  if(empty($name) || empty($email) || empty($hotel_name)){
    echo json_encode(['error' => 'Vui lòng điền đầy đủ thông tin bắt buộc!']);
    exit;
  }
  
  if($owner_id <= 0){
    echo json_encode(['error' => 'ID chủ khách sạn không hợp lệ!']);
    exit;
  }

  // Kiểm tra email đã tồn tại chưa (trừ chính owner này)
  $check_email = select("SELECT id FROM hotel_owners WHERE email=? AND id!=?", [$email, $owner_id], 'si');
  if($check_email && mysqli_num_rows($check_email) > 0){
    echo json_encode(['error' => 'Email này đã được sử dụng!']);
    exit;
  }

  // Update với hoặc không có password
  if(!empty($password)){
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $query = "UPDATE hotel_owners SET name=?, email=?, password=?, phone=?, hotel_name=?, address=?, status=? WHERE id=?";
    $values = [$name, $email, $hashed_password, $phone, $hotel_name, $address, $status, $owner_id];
    $types = 'ssssssii';
  } else {
    $query = "UPDATE hotel_owners SET name=?, email=?, phone=?, hotel_name=?, address=?, status=? WHERE id=?";
    $values = [$name, $email, $phone, $hotel_name, $address, $status, $owner_id];
    $types = 'sssssii';
  }
  
  // #region agent log
  file_put_contents(__DIR__ . '/../../.cursor/debug.log', json_encode(['location' => 'admin/ajax/owners.php:update_owner', 'message' => 'Executing update query', 'data' => ['has_password' => !empty($password), 'query' => $query], 'timestamp' => time()]) . "\n", FILE_APPEND);
  // #endregion
  
  if(update($query, $values, $types)){
    // #region agent log
    file_put_contents(__DIR__ . '/../../.cursor/debug.log', json_encode(['location' => 'admin/ajax/owners.php:update_owner', 'message' => 'Update successful', 'data' => [], 'timestamp' => time()]) . "\n", FILE_APPEND);
    // #endregion
    echo json_encode(['success' => 1]);
  } else {
    // #region agent log
    global $con;
    $error_msg = isset($con) ? mysqli_error($con) : 'Connection not available';
    file_put_contents(__DIR__ . '/../../.cursor/debug.log', json_encode(['location' => 'admin/ajax/owners.php:update_owner', 'message' => 'Update failed', 'data' => ['error' => $error_msg], 'timestamp' => time()]) . "\n", FILE_APPEND);
    // #endregion
    echo json_encode(['error' => 'Không thể cập nhật thông tin!']);
  }
}
?>

