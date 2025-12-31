<?php 

require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

/* -------------------------------
      ADD ROOM
--------------------------------*/
if(isset($_POST['add_room']))
{
  $features = filteration(json_decode($_POST['features']));
  $facilities = filteration(json_decode($_POST['facilities']));
  $frm_data = filteration($_POST);
  $flag = 0;
  $con = $GLOBALS['con'];

  $q1 = "INSERT INTO `rooms` 
        (`name`, `location`, `area`, `price`, `discount`, `quantity`, `adult`, `children`, `description`, `remaining`) 
        VALUES (?,?,?,?,?,?,?,?,?,?)";

  $discount = isset($frm_data['discount']) ? (int)$frm_data['discount'] : 0;
  if($discount < 0) { $discount = 0; }
  if($discount > 100) { $discount = 100; }

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
    (int)$frm_data['quantity']
  ];

  // types: name(s), location(s), area(i), price(i), discount(i), quantity(i), adult(i), children(i), desc(s), remaining(i)
  if(insert($q1,$values,'ssiiiiiisi')){
    $flag = 1;
  } else {
    echo mysqli_error($con);
    exit;
  }

  $room_id = mysqli_insert_id($con);

  /* insert facilities */
  $q2 = "INSERT INTO `room_facilities`(`room_id`, `facilities_id`) VALUES (?,?)";
  if($stmt = mysqli_prepare($con,$q2))
  {
    foreach($facilities as $f){
      mysqli_stmt_bind_param($stmt,'ii',$room_id,$f);
      mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);
  }
  else{
    $flag = 0;
  }

  /* insert features */
  $q3 = "INSERT INTO `room_features`(`room_id`, `features_id`) VALUES (?,?)";
  if($stmt = mysqli_prepare($con,$q3))
  {
    foreach($features as $f){
      mysqli_stmt_bind_param($stmt,'ii',$room_id,$f);
      mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);
  }
  else{
    $flag = 0;
  }
  
  echo $flag ? 1 : 0;
}


/* -------------------------------
      GET ALL ROOMS
--------------------------------*/
if(isset($_POST['get_all_rooms']))
{
  $con = $GLOBALS['con'];
  $search  = filter_var($_POST['search'] ?? '', FILTER_SANITIZE_STRING);
  $status  = $_POST['status'] ?? '';

  $conditions = ["`removed` = 0"];
  $params = [];
  $types  = '';

  // Filter theo owner_id nếu có (admin có thể xem tất cả hoặc filter theo owner)
  $owner_filter = $_POST['owner_filter'] ?? '';
  if($owner_filter !== '' && $owner_filter !== 'all'){
    $conditions[] = "`owner_id` = ?";
    $params[] = (int)$owner_filter;
    $types .= 'i';
  }

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

  // Kiểm tra và tự động tạo cột approved nếu chưa có
  $check_approved = mysqli_query($con, "SHOW COLUMNS FROM `rooms` LIKE 'approved'");
  $has_approved_column = $check_approved && mysqli_num_rows($check_approved) > 0;
  
  if(!$has_approved_column){
    // Tự động tạo cột approved
    $alter_sql = "ALTER TABLE `rooms` 
                  ADD COLUMN `approved` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0=chờ duyệt, 1=đã duyệt' 
                  AFTER `status`";
    @mysqli_query($con, $alter_sql);
    // Cập nhật lại flag sau khi tạo
    $check_approved = mysqli_query($con, "SHOW COLUMNS FROM `rooms` LIKE 'approved'");
    $has_approved_column = $check_approved && mysqli_num_rows($check_approved) > 0;
  }

  $where = implode(' AND ', $conditions);
  $query = "SELECT r.*, ho.name AS owner_name, ho.hotel_name
            FROM `rooms` r 
            LEFT JOIN hotel_owners ho ON r.owner_id = ho.id 
            WHERE $where";

  if($types){
    $res = select($query, $params, $types);
  } else {
    $res = mysqli_query($con, $query);
  }
  $i=1;
  $data = "";

  while($row = mysqli_fetch_assoc($res))
  {
    $location_val = isset($row['location']) ? htmlspecialchars($row['location'], ENT_QUOTES, 'UTF-8') : 'Đang cập nhật';
    $owner_badge = '';
    if(!empty($row['owner_name'])){
      $owner_label = htmlspecialchars($row['hotel_name'] ?: $row['owner_name'], ENT_QUOTES, 'UTF-8');
      $owner_badge = "<br><small class='text-info'><i class='bi bi-building'></i> $owner_label</small>";
    } else {
      $owner_badge = "<br><small class='text-muted'><i class='bi bi-shield-check'></i> Admin</small>";
    }

    /* APPROVAL STATUS */
    // Kiểm tra giá trị approved: nếu cột tồn tại và có giá trị, lấy giá trị đó; nếu không, mặc định là 1 (đã duyệt cho phòng cũ)
    if($has_approved_column && array_key_exists('approved', $row)){
      $approved = (int)$row['approved'];
    } else {
      // Nếu cột chưa tồn tại hoặc không có giá trị, mặc định là 1 (đã duyệt)
      $approved = 1;
    }
    
    $approval_badge = '';
    $approval_btn = '';
    if($approved == 0){
      $approval_badge = "<span class='badge bg-warning text-dark mb-1'>Chờ duyệt</span><br>";
      $room_id_escaped = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
      $approval_btn = "<button type='button' onclick=\"if(typeof approve_room === 'function') { approve_room($room_id_escaped); } else { console.error('approve_room function not found'); alert('Lỗi: Hàm duyệt phòng chưa được tải. Vui lòng refresh trang.'); }\" class='btn btn-success btn-sm shadow-none mb-1' title='Duyệt phòng'>
                        <i class='bi bi-check-circle'></i> Duyệt
                      </button><br>";
    } else {
      $approval_badge = "<span class='badge bg-success mb-1'>Đã duyệt</span><br>";
    }

    /* STATUS BUTTON */
    if($row['status']==1){
      $status_btn = "<button onclick='toggle_status($row[id],0)' class='btn btn-dark btn-sm shadow-none'>Đang hoạt động</button>";
    } else {
      $status_btn = "<button onclick='toggle_status($row[id],1)' class='btn btn-warning btn-sm shadow-none'>Ngưng hoạt động</button>";
    }

    /* AVAILABILITY */
    if($row['remaining'] > 0){
      $availability = "<span class='badge bg-success'>Còn $row[remaining] phòng</span>";
    } else {
      $availability = "<span class='badge bg-danger'>Hết phòng</span>";
    }

    $discount = isset($row['discount']) ? (int)$row['discount'] : 0;
    $final_price = $row['price'];
    if($discount > 0 && $discount <= 100){
      $final_price = max(0, $row['price'] - ($row['price'] * $discount / 100));
    }
    $price_fmt = number_format($row['price'],0,',','.');
    $final_fmt = number_format($final_price,0,',','.');

    if($discount > 0){
      $price_html = "<div class='fw-semibold text-info'>$final_fmt VND</div><div class='text-muted text-decoration-line-through small'>$price_fmt VND</div>";
      $discount_display = "<span class='badge bg-danger text-white'>$discount%</span>";
    } else {
      $price_html = "<div class='fw-semibold'>$price_fmt VND</div>";
      $discount_display = "0%";
    }

    $data.="
      <tr class='align-middle'>
        <td>$i</td>
        <td>$row[name]$owner_badge</td>
        <td>$location_val</td>
        <td>$row[area] m2</td>
        <td>
          <span class='badge rounded-pill bg-light text-dark'>Adult: $row[adult]</span><br>
          <span class='badge rounded-pill bg-light text-dark'>Children: $row[children]</span>
        </td>
        <td>$price_html</td>
        <td>$discount_display</td>
        <td>$row[quantity]</td>
        <td>
          $approval_badge
          $approval_btn
          $status_btn <br> $availability
        </td>
        <td>
          <button type='button' onclick='edit_details($row[id])' 
            class='btn btn-primary shadow-none btn-sm' 
            data-bs-toggle='modal' data-bs-target='#edit-room'>
            <i class='bi bi-pencil-square'></i>
          </button>

          <button type='button' onclick=\"room_images($row[id],'$row[name]')\" 
            class='btn btn-info shadow-none btn-sm' 
            data-bs-toggle='modal' data-bs-target='#room-images'>
            <i class='bi bi-images'></i>
          </button>

          <button type='button' onclick='remove_room($row[id])' 
            class='btn btn-danger shadow-none btn-sm'>
            <i class='bi bi-trash'></i>
          </button>
        </td>
      </tr>";
    $i++;
  }

  echo $data;
}


/* -------------------------------
      GET ROOM DETAILS
--------------------------------*/
if(isset($_POST['get_room']))
{
  $frm_data = filteration($_POST);

  $res1 = select("SELECT * FROM `rooms` WHERE `id`=?",[$frm_data['get_room']],'i');
  $res2 = select("SELECT * FROM `room_features` WHERE `room_id`=?",[$frm_data['get_room']],'i');
  $res3 = select("SELECT * FROM `room_facilities` WHERE `room_id`=?",[$frm_data['get_room']],'i');

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


/* -------------------------------
      EDIT ROOM - Admin có thể edit tất cả phòng
--------------------------------*/
if(isset($_POST['edit_room']))
{
  $features = filteration(json_decode($_POST['features']));
  $facilities = filteration(json_decode($_POST['facilities']));
  $frm_data = filteration($_POST);
  $con = $GLOBALS['con'];
  $room_id = (int)$frm_data['room_id'];

  // Kiểm tra phòng có tồn tại không
  $check_room = select("SELECT id FROM rooms WHERE id=?", [$room_id], 'i');
  if(!$check_room || mysqli_num_rows($check_room) == 0){
    echo json_encode(['error' => 'Không tìm thấy phòng!']);
    exit;
  }

  $remaining = $frm_data['remaining'];

  $q1 = "UPDATE `rooms`
        SET `name`=?,`location`=?,`area`=?,`price`=?,`discount`=?,`quantity`=?,`adult`=?,`children`=?,`description`=?,`remaining`=? 
        WHERE `id`=?";

  $discount = isset($frm_data['discount']) ? (int)$frm_data['discount'] : 0;
  if($discount < 0) { $discount = 0; }
  if($discount > 100) { $discount = 100; }

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
    (int)$remaining,
    $room_id
  ];

  // types: name(s), location(s), area(i), price(i), discount(i), quantity(i), adult(i), children(i), desc(s), remaining(i), id(i)
  $upd = update($q1, $values, 'ssiiiiiisii');

  delete("DELETE FROM `room_features` WHERE `room_id`=?", [$frm_data['room_id']], 'i');
  delete("DELETE FROM `room_facilities` WHERE `room_id`=?", [$frm_data['room_id']], 'i');

  /* Re-insert facilities */
  $q2 = "INSERT INTO `room_facilities`(`room_id`, `facilities_id`) VALUES (?,?)";
  if($stmt = mysqli_prepare($con,$q2)){
    foreach($facilities as $f){
      mysqli_stmt_bind_param($stmt,'ii',$frm_data['room_id'],$f);
      mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);
  }

  /* Re-insert features */
  $q3 = "INSERT INTO `room_features`(`room_id`, `features_id`) VALUES (?,?)";
  if($stmt = mysqli_prepare($con,$q3)){
    foreach($features as $f){
      mysqli_stmt_bind_param($stmt,'ii',$frm_data['room_id'],$f);
      mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);
  }

  if($upd === false){
    echo mysqli_error($con);
  } else {
    echo 1;
  }
}


/* -------------------------------
      TOGGLE STATUS - Admin có thể toggle tất cả phòng
--------------------------------*/
if(isset($_POST['toggle_status']))
{
  $frm_data = filteration($_POST);
  $room_id = (int)$frm_data['toggle_status'];

  // Kiểm tra phòng có tồn tại không
  $check_room = select("SELECT id FROM rooms WHERE id=?", [$room_id], 'i');
  if(!$check_room || mysqli_num_rows($check_room) == 0){
    echo 0;
    exit;
  }

  $q = "UPDATE `rooms` SET `status`=? WHERE `id`=?";
  $v = [$frm_data['value'], $room_id];

  echo update($q,$v,'ii') ? 1 : 0;
}


/* -------------------------------
      ADD IMAGE
--------------------------------*/
if(isset($_POST['add_image']))
{
  $frm_data = filteration($_POST);

  $img_r = uploadImage($_FILES['image'],ROOMS_FOLDER);

  if(in_array($img_r,['inv_img','inv_size','upd_failed'])){
    echo $img_r;
    exit;
  }

  $q = "INSERT INTO `room_images`(`room_id`, `image`) VALUES (?,?)";
  echo insert($q,[$frm_data['room_id'],$img_r],'is');
}


/* -------------------------------
      GET ROOM IMAGES
--------------------------------*/
if(isset($_POST['get_room_images']))
{
  $frm_data = filteration($_POST);
  $res = select("SELECT * FROM `room_images` WHERE `room_id`=?",[$frm_data['get_room_images']],'i');
  $path = ROOMS_IMG_PATH;

  while($row = mysqli_fetch_assoc($res))
  {
    $thumb_btn = ($row['thumb']==1)
    ? "<i class='bi bi-check-lg text-light bg-success px-2 py-1 rounded fs-5'></i>"
    : "<button onclick='thumb_image($row[sr_no],$row[room_id])' class='btn btn-secondary shadow-none'>
         <i class='bi bi-check-lg'></i>
       </button>";

    echo "
      <tr class='align-middle'>
        <td><img src='$path$row[image]' class='img-fluid'></td>
        <td>$thumb_btn</td>
        <td>
          <button onclick='rem_image($row[sr_no],$row[room_id])' class='btn btn-danger shadow-none'>
            <i class='bi bi-trash'></i>
          </button>
        </td>
      </tr>
    ";
  }
}


/* -------------------------------
      REMOVE IMAGE
--------------------------------*/
if(isset($_POST['rem_image']))
{
  $frm_data = filteration($_POST);

  $pre = select("SELECT * FROM `room_images` WHERE `sr_no`=? AND `room_id`=?",
                [$frm_data['image_id'],$frm_data['room_id']], 'ii');

  $img = mysqli_fetch_assoc($pre);

  if(deleteImage($img['image'],ROOMS_FOLDER)){
    echo delete("DELETE FROM `room_images` WHERE `sr_no`=? AND `room_id`=?",
                [$frm_data['image_id'],$frm_data['room_id']], 'ii');
  } else {
    echo 0;
  }
}


/* -------------------------------
      SET THUMBNAIL
--------------------------------*/
if(isset($_POST['thumb_image']))
{
  $frm_data = filteration($_POST);

  update("UPDATE `room_images` SET `thumb`=0 WHERE `room_id`=?", [$frm_data['room_id']], 'i');

  echo update("UPDATE `room_images` SET `thumb`=1 WHERE `sr_no`=? AND `room_id`=?",
              [$frm_data['image_id'],$frm_data['room_id']], 'ii');
}


/* -------------------------------
      APPROVE ROOM
--------------------------------*/
if(isset($_POST['approve_room']))
{
  $frm_data = filteration($_POST);
  $room_id = (int)$frm_data['approve_room'];
  $con = $GLOBALS['con'];

  // Kiểm tra và tự động tạo cột approved nếu chưa có
  $check_approved = mysqli_query($con, "SHOW COLUMNS FROM `rooms` LIKE 'approved'");
  $has_approved_column = $check_approved && mysqli_num_rows($check_approved) > 0;
  
  if(!$has_approved_column){
    // Tự động tạo cột approved
    $alter_sql = "ALTER TABLE `rooms` 
                  ADD COLUMN `approved` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0=chờ duyệt, 1=đã duyệt' 
                  AFTER `status`";
    @mysqli_query($con, $alter_sql);
  }

  // Kiểm tra phòng có tồn tại không
  $check_room = select("SELECT id, approved FROM rooms WHERE id=?", [$room_id], 'i');
  if(!$check_room || mysqli_num_rows($check_room) == 0){
    echo json_encode(['error' => 'Không tìm thấy phòng!']);
    exit;
  }

  $room_data = mysqli_fetch_assoc($check_room);
  
  // Nếu đã duyệt rồi thì không cần update
  if(isset($room_data['approved']) && (int)$room_data['approved'] == 1){
    echo '1'; // Đã duyệt rồi
    exit;
  }

  $q = "UPDATE `rooms` SET `approved`=1 WHERE `id`=?";
  $v = [$room_id];

  $result = update($q,$v,'i');
  
  // Log để debug
  error_log("Approve room $room_id - Result: " . var_export($result, true));
  error_log("MySQL Error: " . mysqli_error($con));
  
  if($result !== false){
    // Kiểm tra xem có dòng nào được cập nhật không
    if($result >= 0){ // >= 0 vì có thể là 0 nếu giá trị không đổi
      echo '1';
    } else {
      $error = mysqli_error($con);
      echo json_encode(['error' => $error ?: 'Không thể cập nhật trạng thái phê duyệt!']);
    }
  } else {
    $error = mysqli_error($con);
    echo json_encode(['error' => $error ?: 'Không thể cập nhật trạng thái phê duyệt!']);
  }
}

/* -------------------------------
      REMOVE ROOM - Admin có thể remove tất cả phòng
--------------------------------*/
if(isset($_POST['remove_room']))
{
  $frm_data = filteration($_POST);
  $room_id = (int)$frm_data['room_id'];

  // Kiểm tra phòng có tồn tại không
  $check_room = select("SELECT id FROM rooms WHERE id=?", [$room_id], 'i');
  if(!$check_room || mysqli_num_rows($check_room) == 0){
    echo 0;
    exit;
  }

  $imgs = select("SELECT * FROM `room_images` WHERE `room_id`=?",[$room_id],'i');
  while($row = mysqli_fetch_assoc($imgs)){
    deleteImage($row['image'],ROOMS_FOLDER);
  }

  delete("DELETE FROM `room_images` WHERE `room_id`=?",[$room_id],'i');
  delete("DELETE FROM `room_features` WHERE `room_id`=?",[$room_id],'i');
  delete("DELETE FROM `room_facilities` WHERE `room_id`=?",[$room_id],'i');

  echo update("UPDATE `rooms` SET `removed`=1 WHERE `id`=?",[$room_id],'i');
}

?>
