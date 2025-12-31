<?php
require('../../admin/inc/db_config.php');
require('../../admin/inc/essentials.php');

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Check owner login
if (!isset($_SESSION['ownerLogin']) || $_SESSION['ownerLogin'] != true) {
    echo "Unauthorized";
    exit;
}

$owner_id = (int)$_SESSION['ownerId'];

/* GET ALL ROOMS - chỉ phòng của owner */
if(isset($_POST['get_all_rooms']))
{
  $con = $GLOBALS['con'];
  $search  = filter_var($_POST['search'] ?? '', FILTER_SANITIZE_STRING);
  $status  = $_POST['status'] ?? '';

  $conditions = ["`removed` = 0", "`owner_id` = $owner_id"];
  $params = [];
  $types  = '';

  if($status === 'active'){
    $conditions[] = "`status` = 1";
  } else if($status === 'inactive'){
    $conditions[] = "`status` = 0";
  } else if($status === 'soldout'){
    $conditions[] = "`remaining` = 0";
  }

  if($search !== ''){
    $conditions[] = "(`name` LIKE ? OR `description` LIKE ? OR `location` LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'sss';
  }

  $where = implode(' AND ', $conditions);
  $query = "SELECT * FROM `rooms` WHERE $where";

  if($types){
    $res = select($query, $params, $types);
  } else {
    $res = mysqli_query($con, $query);
  }
  
  // Tính toán stats trước khi render
  $total_count = 0;
  $active_count = 0;
  $inactive_count = 0;
  
  // Đếm lại từ kết quả query (không bị ảnh hưởng bởi filter status)
  $stats_query = "SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS active,
    SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) AS inactive
    FROM `rooms` 
    WHERE `removed` = 0 AND `owner_id` = $owner_id";
  $stats_res = mysqli_query($con, $stats_query);
  if($stats_res && mysqli_num_rows($stats_res) > 0){
    $stats_row = mysqli_fetch_assoc($stats_res);
    $total_count = (int)$stats_row['total'];
    $active_count = (int)$stats_row['active'];
    $inactive_count = (int)$stats_row['inactive'];
  }
  
  $i=1;
  $data = "";

  if(mysqli_num_rows($res) == 0){
    $data = "
      <tr>
        <td colspan='8' class='empty-state'>
          <i class='bi bi-inbox d-block mb-3'></i>
          <p>Chưa có phòng nào. Hãy thêm phòng mới!</p>
        </td>
      </tr>
    ";
  } else {
    while($row = mysqli_fetch_assoc($res))
    {
    $location_val = isset($row['location']) ? htmlspecialchars($row['location'], ENT_QUOTES, 'UTF-8') : 'Đang cập nhật';
    $name_escaped = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
    
    // Trạng thái phê duyệt
    $approved = isset($row['approved']) ? (int)$row['approved'] : 0;
    $approval_badge = $approved == 1 
      ? "<span class='badge bg-success mb-1 d-block'>Đã duyệt</span>" 
      : "<span class='badge bg-warning text-dark mb-1 d-block'>Chờ duyệt</span>";

    if($row['status']==1){
      $status_btn = "<button onclick='toggle_status($row[id],0)' class='btn btn-dark btn-sm shadow-none'>Đang hoạt động</button>";
    } else {
      $status_btn = "<button onclick='toggle_status($row[id],1)' class='btn btn-warning btn-sm shadow-none'>Ngưng hoạt động</button>";
    }

    if($row['remaining'] > 0){
      $availability = "<span class='badge bg-success'>Còn $row[remaining] phòng</span>";
    } else {
      $availability = "<span class='badge bg-danger'>Hết phòng</span>";
    }

      $price_fmt = number_format($row['price'],0,',','.');
      $discount_html = '';
      if(isset($row['discount']) && $row['discount'] > 0){
        $discount_html = "<small class='text-danger d-block'>-{$row['discount']}%</small>";
      }

      $data.="
        <tr class='align-middle'>
          <td class='text-center'>$i</td>
          <td>
            <strong>$name_escaped</strong>
            $discount_html
          </td>
          <td>$location_val</td>
          <td class='text-center'>$row[area] m²</td>
          <td class='text-end'>
            <strong>$price_fmt VND</strong>
          </td>
          <td class='text-center'>$row[quantity]</td>
          <td class='text-center'>
            <div class='d-flex flex-column gap-1 align-items-center'>
              $approval_badge
              $status_btn
              $availability
            </div>
          </td>
          <td class='text-center'>
            <div class='d-flex gap-1 justify-content-center'>
              <button type='button' onclick='edit_details($row[id])' 
                class='btn btn-primary shadow-none btn-action' 
                data-bs-toggle='modal' data-bs-target='#edit-room'
                title='Chỉnh sửa'>
                <i class='bi bi-pencil-square'></i>
              </button>
              <button type='button' onclick=\"room_images($row[id],'$name_escaped')\" 
                class='btn btn-info shadow-none btn-action' 
                data-bs-toggle='modal' data-bs-target='#room-images'
                title='Quản lý ảnh'>
                <i class='bi bi-images'></i>
              </button>
            </div>
          </td>
        </tr>";
      $i++;
    }
  }

  // Thêm stats vào đầu output dưới dạng comment để JavaScript có thể đọc
  $data = "<!--STATS:{\"total\":$total_count,\"active\":$active_count,\"inactive\":$inactive_count}-->" . $data;
  
  echo $data;
}

/* ADD ROOM - tự động gán owner_id */
if(isset($_POST['add_room']) && $_POST['add_room'] == '1')
{
  $frm_data = filteration($_POST);
  $flag = 0;
  $con = $GLOBALS['con'];

  // Validate required fields
  if(empty($frm_data['name']) || empty($frm_data['location']) || empty($frm_data['area']) || 
     empty($frm_data['price']) || empty($frm_data['quantity']) || empty($frm_data['adult']) || 
     empty($frm_data['children']) || empty($frm_data['desc'])){
    echo json_encode(['error' => 'Vui lòng điền đầy đủ thông tin!']);
    exit;
  }

  // Kiểm tra xem cột approved có tồn tại không
  $has_approved = false;
  $check_approved = mysqli_query($con, "SHOW COLUMNS FROM `rooms` LIKE 'approved'");
  if($check_approved && mysqli_num_rows($check_approved) > 0){
    $has_approved = true;
  }

  $discount = isset($frm_data['discount']) ? (int)$frm_data['discount'] : 0;
  if($discount < 0) { $discount = 0; }
  if($discount > 100) { $discount = 100; }

  // Xây dựng query và values động dựa trên việc cột approved có tồn tại
  if($has_approved){
    $q1 = "INSERT INTO `rooms` 
          (`name`, `location`, `area`, `price`, `discount`, `quantity`, `adult`, `children`, `description`, `remaining`, `owner_id`, `approved`) 
          VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
    $values = [
      $frm_data['name'],
      $frm_data['location'],
      (int)$frm_data['area'],
      (int)$frm_data['price'],
      $discount,
      (int)$frm_data['quantity'],
      (int)$frm_data['adult'],
      (int)$frm_data['children'],
      $frm_data['desc'],
      (int)$frm_data['quantity'],
      $owner_id,  // Tự động gán owner_id
      0  // Mặc định chờ duyệt (approved = 0)
    ];
    $types = 'ssiiiiiisiii';
  } else {
    $q1 = "INSERT INTO `rooms` 
          (`name`, `location`, `area`, `price`, `discount`, `quantity`, `adult`, `children`, `description`, `remaining`, `owner_id`) 
          VALUES (?,?,?,?,?,?,?,?,?,?,?)";
    $values = [
      $frm_data['name'],
      $frm_data['location'],
      (int)$frm_data['area'],
      (int)$frm_data['price'],
      $discount,
      (int)$frm_data['quantity'],
      (int)$frm_data['adult'],
      (int)$frm_data['children'],
      $frm_data['desc'],
      (int)$frm_data['quantity'],
      $owner_id  // Tự động gán owner_id
    ];
    $types = 'ssiiiiiisii';
  }

  if(insert($q1,$values,$types)){
    $room_id = mysqli_insert_id($con);
    $flag = 1;

    // Xử lý features
    if(isset($_POST['features'])){
      $features = json_decode($_POST['features'], true);
      if(is_array($features) && count($features) > 0){
        $q2 = "INSERT INTO `room_features`(`room_id`, `features_id`) VALUES (?,?)";
        if($stmt = mysqli_prepare($con, $q2)){
          foreach($features as $fid){
            $fid = (int)$fid;
            if($fid > 0){
              mysqli_stmt_bind_param($stmt, 'ii', $room_id, $fid);
              mysqli_stmt_execute($stmt);
            }
          }
          mysqli_stmt_close($stmt);
        }
      }
    }

    // Xử lý facilities
    if(isset($_POST['facilities'])){
      $facilities = json_decode($_POST['facilities'], true);
      if(is_array($facilities) && count($facilities) > 0){
        $q3 = "INSERT INTO `room_facilities`(`room_id`, `facilities_id`) VALUES (?,?)";
        if($stmt = mysqli_prepare($con, $q3)){
          foreach($facilities as $fid){
            $fid = (int)$fid;
            if($fid > 0){
              mysqli_stmt_bind_param($stmt, 'ii', $room_id, $fid);
              mysqli_stmt_execute($stmt);
            }
          }
          mysqli_stmt_close($stmt);
        }
      }
    }
  } else {
    $error = mysqli_error($con);
    echo json_encode(['error' => $error ?: 'Lỗi khi thêm phòng!']);
    exit;
  }

  echo $flag ? 1 : 0;
}

/* GET ROOM - lấy thông tin phòng để edit */
if(isset($_POST['get_room']))
{
  $frm_data = filteration($_POST);
  $room_id = (int)$frm_data['get_room'];
  
  // Kiểm tra phòng thuộc về owner
  $check = select("SELECT id FROM rooms WHERE id=? AND owner_id=?", [$room_id, $owner_id], 'ii');
  if(!$check || mysqli_num_rows($check) == 0){
    echo json_encode(['error' => 'Không tìm thấy phòng!']);
    exit;
  }

  $res1 = select("SELECT * FROM `rooms` WHERE `id`=? AND `owner_id`=?", [$room_id, $owner_id], 'ii');
  $res2 = select("SELECT * FROM `room_features` WHERE `room_id`=?", [$room_id], 'i');
  $res3 = select("SELECT * FROM `room_facilities` WHERE `room_id`=?", [$room_id], 'i');

  $roomdata = mysqli_fetch_assoc($res1);
  $features = [];
  $facilities = [];

  while($row = mysqli_fetch_assoc($res2)){ $features[] = $row['features_id']; }
  while($row = mysqli_fetch_assoc($res3)){ $facilities[] = $row['facilities_id']; }

  echo json_encode([
    "roomdata" => $roomdata,
    "features" => $features,
    "facilities" => $facilities
  ]);
}

/* EDIT ROOM - chỉ phòng của owner */
if(isset($_POST['edit_room']) && $_POST['edit_room'] == '1')
{
  $frm_data = filteration($_POST);
  
  // Kiểm tra room_id có tồn tại không
  if(!isset($frm_data['room_id']) || empty($frm_data['room_id'])){
    echo json_encode(['error' => 'Thiếu thông tin phòng!']);
    exit;
  }
  
  $room_id = (int)$frm_data['room_id'];
  
  // Kiểm tra phòng thuộc về owner
  $check = select("SELECT id FROM rooms WHERE id=? AND owner_id=?", [$room_id, $owner_id], 'ii');
  if(!$check || mysqli_num_rows($check) == 0){
    echo json_encode(['error' => 'Không tìm thấy phòng hoặc bạn không có quyền chỉnh sửa phòng này!']);
    exit;
  }

  // Xử lý features và facilities từ JSON
  $features = [];
  $facilities = [];
  
  if(isset($_POST['features']) && !empty($_POST['features'])){
    $features_json = json_decode($_POST['features'], true);
    if(is_array($features_json)){
      $features = array_map('intval', $features_json);
    }
  }
  
  if(isset($_POST['facilities']) && !empty($_POST['facilities'])){
    $facilities_json = json_decode($_POST['facilities'], true);
    if(is_array($facilities_json)){
      $facilities = array_map('intval', $facilities_json);
    }
  }
  
  $con = $GLOBALS['con'];

  $remaining = (int)$frm_data['remaining'];

  $q1 = "UPDATE `rooms`
        SET `name`=?,`location`=?,`area`=?,`price`=?,`discount`=?,`quantity`=?,`adult`=?,`children`=?,`description`=?,`remaining`=? 
        WHERE `id`=? AND `owner_id`=?";

  $discount = isset($frm_data['discount']) ? (int)$frm_data['discount'] : 0;
  if($discount < 0) { $discount = 0; }
  if($discount > 100) { $discount = 100; }

  // Validate required fields
  if(empty($frm_data['name']) || empty($frm_data['location']) || empty($frm_data['area']) || 
     empty($frm_data['price']) || empty($frm_data['quantity']) || empty($frm_data['adult']) || 
     empty($frm_data['children']) || empty($frm_data['desc'])){
    echo json_encode(['error' => 'Vui lòng điền đầy đủ thông tin bắt buộc!']);
    exit;
  }

  $values = [
    $frm_data['name'],           // s
    $frm_data['location'],       // s
    (int)$frm_data['area'],      // i
    (int)$frm_data['price'],     // i
    $discount,                    // i
    (int)$frm_data['quantity'],  // i
    (int)$frm_data['adult'],     // i
    (int)$frm_data['children'],  // i
    $frm_data['desc'],           // s
    $remaining,                   // i
    $room_id,                     // i
    $owner_id                     // i
  ];
  // Tổng: 12 giá trị = ssiiiiiisiii (12 ký tự)

  $upd = update($q1, $values, 'ssiiiiiisiii');
  
  // Kiểm tra lỗi update
  if($upd === false){
    $error = mysqli_error($con);
    echo json_encode(['error' => $error ?: 'Lỗi khi cập nhật phòng!']);
    exit;
  }
  
  // Nếu không có dòng nào được cập nhật (có thể do dữ liệu không thay đổi)
  if($upd == 0){
    // Vẫn coi là thành công nếu không có lỗi
    $upd = 1;
  }

  // Xóa features và facilities cũ
  delete("DELETE FROM `room_features` WHERE `room_id`=?", [$room_id], 'i');
  delete("DELETE FROM `room_facilities` WHERE `room_id`=?", [$room_id], 'i');

  // Thêm lại facilities
  if(is_array($facilities) && count($facilities) > 0){
    $q2 = "INSERT INTO `room_facilities`(`room_id`, `facilities_id`) VALUES (?,?)";
    if($stmt = mysqli_prepare($con, $q2)){
      foreach($facilities as $f){
        if($f > 0){
          mysqli_stmt_bind_param($stmt, 'ii', $room_id, $f);
          mysqli_stmt_execute($stmt);
        }
      }
      mysqli_stmt_close($stmt);
    }
  }

  // Thêm lại features
  if(is_array($features) && count($features) > 0){
    $q3 = "INSERT INTO `room_features`(`room_id`, `features_id`) VALUES (?,?)";
    if($stmt = mysqli_prepare($con, $q3)){
      foreach($features as $f){
        if($f > 0){
          mysqli_stmt_bind_param($stmt, 'ii', $room_id, $f);
          mysqli_stmt_execute($stmt);
        }
      }
      mysqli_stmt_close($stmt);
    }
  }

  echo $upd >= 0 ? 1 : 0;
}

/* TOGGLE STATUS - chỉ phòng của owner */
if(isset($_POST['toggle_status']))
{
  $frm_data = filteration($_POST);
  $room_id = (int)$frm_data['toggle_status'];
  
  // Kiểm tra phòng thuộc về owner
  $check = select("SELECT id FROM rooms WHERE id=? AND owner_id=?", [$room_id, $owner_id], 'ii');
  if(!$check || mysqli_num_rows($check) == 0){
    echo 0;
    exit;
  }

  $q = "UPDATE `rooms` SET `status`=? WHERE `id`=? AND `owner_id`=?";
  $v = [$frm_data['value'], $room_id, $owner_id];

  echo update($q,$v,'iii') ? 1 : 0;
}

/* ADD IMAGE - chỉ phòng của owner - hỗ trợ nhiều ảnh cùng lúc */
if(isset($_POST['add_image']))
{
  $frm_data = filteration($_POST);
  $room_id = (int)$frm_data['room_id'];
  
  // Kiểm tra phòng thuộc về owner
  $check = select("SELECT id FROM rooms WHERE id=? AND owner_id=?", [$room_id, $owner_id], 'ii');
  if(!$check || mysqli_num_rows($check) == 0){
    echo json_encode(['error' => 'Không tìm thấy phòng hoặc bạn không có quyền!']);
    exit;
  }

  // Kiểm tra có file được upload không
  if(!isset($_FILES['image']) || empty($_FILES['image']['name'][0])){
    echo json_encode(['error' => 'Vui lòng chọn ít nhất một ảnh!']);
    exit;
  }

  $files = $_FILES['image'];
  $con = $GLOBALS['con'];
  $success_count = 0;
  $error_count = 0;
  $errors = [];

  // Xử lý từng file
  $file_count = is_array($files['name']) ? count($files['name']) : 1;
  
  for($i = 0; $i < $file_count; $i++){
    // Tạo $_FILES tạm cho từng file
    $file = [
      'name' => is_array($files['name']) ? $files['name'][$i] : $files['name'],
      'type' => is_array($files['type']) ? $files['type'][$i] : $files['type'],
      'tmp_name' => is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'],
      'error' => is_array($files['error']) ? $files['error'][$i] : $files['error'],
      'size' => is_array($files['size']) ? $files['size'][$i] : $files['size']
    ];
    
    // Bỏ qua nếu file này rỗng (khi chỉ upload 1 file, các index khác sẽ rỗng)
    if($file['error'] == UPLOAD_ERR_NO_FILE){
      continue;
    }
    
    $img_r = uploadImage($file, ROOMS_FOLDER);

    if(in_array($img_r, ['inv_img', 'inv_size', 'upd_failed'])){
      $error_count++;
      $errors[] = "File " . ($i + 1) . ": " . $img_r;
      continue;
    }

    $q = "INSERT INTO `room_images`(`room_id`, `image`) VALUES (?,?)";
    if(insert($q, [$room_id, $img_r], 'is')){
      $success_count++;
    } else {
      $error_count++;
      $errors[] = "File " . ($i + 1) . ": Lỗi khi lưu vào database";
      // Xóa file đã upload nếu insert thất bại
      if(file_exists(ROOMS_FOLDER . $img_r)){
        @unlink(ROOMS_FOLDER . $img_r);
      }
    }
  }

  // Trả về kết quả
  if($success_count > 0 && $error_count == 0){
    echo json_encode([
      'success' => true,
      'message' => "Đã thêm thành công $success_count ảnh!"
    ]);
  } else if($success_count > 0 && $error_count > 0){
    echo json_encode([
      'success' => true,
      'message' => "Đã thêm thành công $success_count ảnh. Có $error_count ảnh lỗi: " . implode(', ', $errors)
    ]);
  } else {
    echo json_encode([
      'error' => "Không thể thêm ảnh. Lỗi: " . implode(', ', $errors)
    ]);
  }
}

/* GET ROOM IMAGES - chỉ phòng của owner */
if(isset($_POST['get_room_images']))
{
  $frm_data = filteration($_POST);
  $room_id = (int)$frm_data['get_room_images'];
  
  // Kiểm tra phòng thuộc về owner
  $check = select("SELECT id FROM rooms WHERE id=? AND owner_id=?", [$room_id, $owner_id], 'ii');
  if(!$check || mysqli_num_rows($check) == 0){
    echo "<tr><td colspan='3' class='text-center'>Không tìm thấy phòng!</td></tr>";
    exit;
  }

  $res = select("SELECT * FROM `room_images` WHERE `room_id`=?", [$room_id], 'i');
  $path = ROOMS_IMG_PATH;

  if(mysqli_num_rows($res) == 0){
    echo "<tr><td colspan='3' class='text-center text-muted'>Chưa có ảnh nào</td></tr>";
    exit;
  }

  while($row = mysqli_fetch_assoc($res))
  {
    $thumb_btn = ($row['thumb'] == 1)
    ? "<i class='bi bi-check-lg text-light bg-success px-2 py-1 rounded fs-5' data-thumb='1' data-image-id='$row[sr_no]' data-room-id='$row[room_id]'></i>"
    : "<button onclick='thumb_image($row[sr_no],$row[room_id], this)' class='btn btn-secondary shadow-none btn-sm' data-image-id='$row[sr_no]' data-room-id='$row[room_id]'>
         <i class='bi bi-check-lg'></i>
       </button>";

    echo "
      <tr class='align-middle'>
        <td><img src='$path$row[image]' class='img-fluid' style='max-width: 200px; max-height: 150px; object-fit: cover;'></td>
        <td>$thumb_btn</td>
        <td>
          <button onclick='rem_image($row[sr_no],$row[room_id])' class='btn btn-danger shadow-none btn-sm'>
            <i class='bi bi-trash'></i>
          </button>
        </td>
      </tr>
    ";
  }
}

/* REMOVE IMAGE - chỉ phòng của owner */
if(isset($_POST['rem_image']))
{
  $frm_data = filteration($_POST);
  $image_id = (int)$frm_data['image_id'];
  $room_id = (int)$frm_data['room_id'];
  
  // Kiểm tra phòng thuộc về owner
  $check = select("SELECT id FROM rooms WHERE id=? AND owner_id=?", [$room_id, $owner_id], 'ii');
  if(!$check || mysqli_num_rows($check) == 0){
    echo 0;
    exit;
  }

  $pre = select("SELECT * FROM `room_images` WHERE `sr_no`=? AND `room_id`=?",
                [$image_id, $room_id], 'ii');

  if(!$pre || mysqli_num_rows($pre) == 0){
    echo 0;
    exit;
  }

  $img = mysqli_fetch_assoc($pre);

  if(deleteImage($img['image'], ROOMS_FOLDER)){
    echo delete("DELETE FROM `room_images` WHERE `sr_no`=? AND `room_id`=?",
                [$image_id, $room_id], 'ii');
  } else {
    echo 0;
  }
}

/* SET THUMBNAIL - chỉ phòng của owner */
if(isset($_POST['thumb_image']))
{
  $frm_data = filteration($_POST);
  $image_id = (int)$frm_data['image_id'];
  $room_id = (int)$frm_data['room_id'];
  
  // Kiểm tra phòng thuộc về owner
  $check = select("SELECT id FROM rooms WHERE id=? AND owner_id=?", [$room_id, $owner_id], 'ii');
  if(!$check || mysqli_num_rows($check) == 0){
    echo 0;
    exit;
  }

  update("UPDATE `room_images` SET `thumb`=0 WHERE `room_id`=?", [$room_id], 'i');

  echo update("UPDATE `room_images` SET `thumb`=1 WHERE `sr_no`=? AND `room_id`=?",
              [$image_id, $room_id], 'ii');
}

?>

