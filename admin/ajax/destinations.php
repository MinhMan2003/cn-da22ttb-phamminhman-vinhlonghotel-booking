<?php
require('../inc/db_config.php');
require('../inc/essentials.php');
require('../inc/destinations_helper.php');
adminLogin();

// Ensure destination_images table exists
ensureDestinationImagesTable($con);

// ========== GET LIST ==========
if(isset($_POST['get_destinations'])){
  $res = mysqli_query($con, "SELECT * FROM `destinations` ORDER BY `id` DESC");
  $html = '';
  $i = 1;

  $cat_map = [
    'temple' => '<span class="badge bg-purple">Chùa, Đình</span>',
    'nature' => '<span class="badge bg-success">Thiên nhiên</span>',
    'market' => '<span class="badge bg-warning">Chợ nổi</span>',
    'culture' => '<span class="badge bg-danger">Văn hóa</span>',
    'other' => '<span class="badge bg-secondary">Khác</span>'
  ];

  if($res && mysqli_num_rows($res)){
    while($row = mysqli_fetch_assoc($res)){
      $id = (int)$row['id'];
      $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
      $location = htmlspecialchars($row['location'], ENT_QUOTES, 'UTF-8');
      $category = $row['category'];
      $rating = number_format((float)$row['rating'], 1);
      $review_count = (int)$row['review_count'];
      $active = (int)$row['active'];
      
      // Lấy ảnh chính từ destination_images hoặc fallback về destinations.image
      $primary_image = '';
      $images_count = 0;
      
      // Check if destination_images table exists
      $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'destination_images'");
      if($table_check && mysqli_num_rows($table_check) > 0){
        $primary_image_query = "SELECT image FROM `destination_images` WHERE `destination_id` = ? AND `is_primary` = 1 ORDER BY `sort_order` ASC LIMIT 1";
        $primary_image_result = @select($primary_image_query, [$id], 'i');
        if($primary_image_result && mysqli_num_rows($primary_image_result) > 0){
          $primary_image_data = mysqli_fetch_assoc($primary_image_result);
          $primary_image = $primary_image_data['image'];
        }
        
        // Đếm số ảnh
        $images_count_query = "SELECT COUNT(*) as count FROM `destination_images` WHERE `destination_id` = ?";
        $images_count_result = @select($images_count_query, [$id], 'i');
        if($images_count_result && mysqli_num_rows($images_count_result) > 0){
          $images_count_data = mysqli_fetch_assoc($images_count_result);
          $images_count = (int)$images_count_data['count'];
        }
      }
      
      // Fallback to old image field if no primary image in destination_images
      if(empty($primary_image)){
        $primary_image = $row['image'] ? $row['image'] : 'default.jpg';
      }
      
      $primary_image_name = $primary_image;
      if(strpos($primary_image_name, '/') !== false || strpos($primary_image_name, '\\') !== false){
        $primary_image_name = basename($primary_image_name);
      }
      if(preg_match('~^https?://~', $primary_image)){
        $image_path = $primary_image;
      } else {
        $image_path = DESTINATIONS_IMG_PATH . $primary_image_name;
      }
      $default_image_path = DESTINATIONS_IMG_PATH . 'default.jpg';
      // If primary image is placeholder, treat as no image (don't show default)
      if(empty($primary_image_name) || $primary_image_name === 'default.jpg'){
        $image_html = "<div style='width:60px;height:60px;display:flex;align-items:center;justify-content:center;background:#f3f4f6;border-radius:8px;color:#9ca3af;'>No Image</div>";
      } else {
        $image_html = "<img src='{$image_path}' alt='{$name}' style='width:60px;height:60px;object-fit:cover;border-radius:8px;' onerror=\"this.onerror=null; this.src='{$default_image_path}'; this.onerror=function(){this.style.display='none';};\">";
      }
      
      // Đếm số phòng liên kết
      $rooms_count_query = "SELECT COUNT(*) as count FROM `room_destinations` WHERE `destination_id` = ?";
      $rooms_count_result = select($rooms_count_query, [$id], 'i');
      $rooms_count = 0;
      if($rooms_count_result && mysqli_num_rows($rooms_count_result) > 0) {
        $rooms_count_data = mysqli_fetch_assoc($rooms_count_result);
        $rooms_count = (int)$rooms_count_data['count'];
      }
      
      $cat_badge = $cat_map[$category] ?? $cat_map['other'];
      $status_badge = $active 
        ? "<span class='badge bg-success'>Hiển thị</span>"
        : "<span class='badge bg-secondary'>Ẩn</span>";
      
      $html .= "<tr data-category='{$category}' data-active='{$active}' data-images='{$images_count}'>
        <td>{$i}</td>
        <td>
          {$image_html}
          " . ($images_count > 1 ? "<small class='d-block text-muted mt-1'>{$images_count} anh</small>" : "") . "
        </td>
        <td class='text-start'><strong>{$name}</strong></td>
        <td class='text-start'><small>{$location}</small></td>
        <td>{$cat_badge}</td>
        <td>
          <div><i class='bi bi-star-fill text-warning'></i> {$rating}</div>
          <small class='text-muted'>({$review_count} danh gia)</small>
        </td>
        <td><span class='badge bg-info'>{$rooms_count} phong</span></td>
        <td>{$status_badge}</td>
        <td>
          <button class='btn btn-sm btn-outline-primary mb-1' onclick='editDestination({$id})' title='Sua'>
            <i class='bi bi-pencil'></i>
          </button>
          <button class='btn btn-sm btn-outline-secondary mb-1' onclick='toggleDestination({$id}, ".($active?0:1).")' title='".($active?'An':'Hien thi')."'>
            <i class='bi ".($active?'bi-eye-slash':'bi-eye')."'></i>
          </button>
          <button class='btn btn-sm btn-outline-danger' onclick='deleteDestination({$id})' title='Xoa'>
            <i class='bi bi-trash'></i>
          </button>
        </td>
      </tr>";
      $i++;
    }
  } else {
    $html = "<tr><td colspan='9' class='text-center text-muted py-4'>Chưa có điểm du lịch nào</td></tr>";
  }

  echo $html;
  exit;
}

// ========== SAVE (ADD/EDIT) ==========
if(isset($_POST['save_destination'])){
  $frm = filteration($_POST);
  $id = isset($frm['id']) && $frm['id'] !== '' ? (int)$frm['id'] : 0;

  $name = $frm['name'] ?? '';
  $description = $frm['description'] ?? '';
  $short_description = $frm['short_description'] ?? '';
  $location = $frm['location'] ?? '';
  $latitude = !empty($frm['latitude']) ? (float)$frm['latitude'] : null;
  $longitude = !empty($frm['longitude']) ? (float)$frm['longitude'] : null;
  $category = $frm['category'] ?? 'other';
  $rating = isset($frm['rating']) ? (float)$frm['rating'] : 0.0;
  $review_count = isset($frm['review_count']) ? (int)$frm['review_count'] : 0;
  $active = isset($frm['active']) ? (int)$frm['active'] : 1;

  // Validate
  if(empty($name) || empty($description) || empty($location)){
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc']);
    exit;
  }

  // Validate rating
  if($rating < 0) $rating = 0;
  if($rating > 5) $rating = 5;

  // Handle multiple images upload
  $uploaded_images = [];
  if(isset($_FILES['images']) && !empty($_FILES['images']['name'][0])){
    $files = $_FILES['images'];
    $file_count = count($files['name']);
    
    for($i = 0; $i < $file_count; $i++){
      if($files['error'][$i] === UPLOAD_ERR_OK){
        $img_name = $files['name'][$i];
        $img_tmp = $files['tmp_name'][$i];
        $img_size = $files['size'][$i];
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

        if(!in_array($img_ext, $allowed_ext)){
          continue; // Skip invalid files
        }

        if($img_size > 5 * 1024 * 1024){ // 5MB
          continue; // Skip oversized files
        }

        $image_name = 'dest_' . time() . '_' . rand(1000, 9999) . '_' . $i . '.' . $img_ext;
        $upload_path = UPLOAD_IMAGE_PATH . DESTINATIONS_FOLDER . $image_name;

        if(!is_dir(UPLOAD_IMAGE_PATH . DESTINATIONS_FOLDER)){
          mkdir(UPLOAD_IMAGE_PATH . DESTINATIONS_FOLDER, 0777, true);
        }

        if(move_uploaded_file($img_tmp, $upload_path)){
          $uploaded_images[] = $image_name;
        }
      }
    }
  }
  
  // Keep old image handling for backward compatibility (single image)
  $image_name = '';
  if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK && empty($uploaded_images)){
    $img = $_FILES['image'];
    $img_name = $img['name'];
    $img_tmp = $img['tmp_name'];
    $img_size = $img['size'];
    $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

    if(!in_array($img_ext, $allowed_ext)){
      echo json_encode(['status' => 'error', 'message' => 'Định dạng ảnh không hợp lệ']);
      exit;
    }

    if($img_size > 5 * 1024 * 1024){ // 5MB
      echo json_encode(['status' => 'error', 'message' => 'Kích thước ảnh quá lớn (tối đa 5MB)']);
      exit;
    }

    $image_name = 'dest_' . time() . '_' . rand(1000, 9999) . '.' . $img_ext;
    $upload_path = UPLOAD_IMAGE_PATH . DESTINATIONS_FOLDER . $image_name;

    if(!is_dir(UPLOAD_IMAGE_PATH . DESTINATIONS_FOLDER)){
      mkdir(UPLOAD_IMAGE_PATH . DESTINATIONS_FOLDER, 0777, true);
    }

    if(!move_uploaded_file($img_tmp, $upload_path)){
      echo json_encode(['status' => 'error', 'message' => 'Lỗi khi upload ảnh']);
      exit;
    }

    // Delete old image if editing
    if($id > 0){
      $old_img_query = "SELECT image FROM `destinations` WHERE `id` = ?";
      $old_img_result = select($old_img_query, [$id], 'i');
      if($old_img_result && mysqli_num_rows($old_img_result) > 0){
        $old_img_data = mysqli_fetch_assoc($old_img_result);
        if(!empty($old_img_data['image'])){
          $old_img_path = UPLOAD_IMAGE_PATH . DESTINATIONS_FOLDER . $old_img_data['image'];
          if(file_exists($old_img_path)){
            @unlink($old_img_path);
          }
        }
      }
    }
  } else if($id > 0 && empty($uploaded_images)) {
    // Keep existing single image if not uploading new one (backward compatibility)
    $old_img_query = "SELECT image FROM `destinations` WHERE `id` = ?";
    $old_img_result = select($old_img_query, [$id], 'i');
    if($old_img_result && mysqli_num_rows($old_img_result) > 0){
      $old_img_data = mysqli_fetch_assoc($old_img_result);
      $image_name = $old_img_data['image'] ?? '';
    }
  }
  
  // Use first uploaded image as primary if multiple images uploaded
  if(!empty($uploaded_images)){
    $image_name = $uploaded_images[0]; // Set first as primary for backward compatibility
  }

  if($id > 0){
    // UPDATE
    if(!empty($image_name)){
      $update_query = "UPDATE `destinations` SET 
                      `name` = ?, `description` = ?, `short_description` = ?, `location` = ?, 
                      `latitude` = ?, `longitude` = ?, `category` = ?, `rating` = ?, 
                      `review_count` = ?, `active` = ?, `image` = ?
                      WHERE `id` = ?";
      $update_values = [$name, $description, $short_description, $location, $latitude, $longitude, 
                       $category, $rating, $review_count, $active, $image_name, $id];
      $update_types = 'ssssddsdiisi';
    } else {
      $update_query = "UPDATE `destinations` SET 
                      `name` = ?, `description` = ?, `short_description` = ?, `location` = ?, 
                      `latitude` = ?, `longitude` = ?, `category` = ?, `rating` = ?, 
                      `review_count` = ?, `active` = ?
                      WHERE `id` = ?";
      $update_values = [$name, $description, $short_description, $location, $latitude, $longitude, 
                       $category, $rating, $review_count, $active, $id];
      $update_types = 'ssssddsdiii';
    }
    
    if(update($update_query, $update_values, $update_types)){
      // Save multiple images to destination_images table
      if(!empty($uploaded_images)){
        // Check if table exists
        $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'destination_images'");
        if($table_check && mysqli_num_rows($table_check) > 0){
          // Get current max sort_order
          $max_order_query = "SELECT MAX(sort_order) as max_order FROM `destination_images` WHERE `destination_id` = ?";
          $max_order_result = @select($max_order_query, [$id], 'i');
          $max_order = 0;
          if($max_order_result && mysqli_num_rows($max_order_result) > 0){
            $max_order_data = mysqli_fetch_assoc($max_order_result);
            $max_order = (int)($max_order_data['max_order'] ?? 0);
          }
          
          $img_insert_query = "INSERT INTO `destination_images` (`destination_id`, `image`, `is_primary`, `sort_order`) VALUES (?, ?, ?, ?)";
          $sort_order = $max_order + 1;
          foreach($uploaded_images as $img){
            $is_primary = ($sort_order == ($max_order + 1)) ? 1 : 0;
            @insert($img_insert_query, [$id, $img, $is_primary, $sort_order], 'isii');
            $sort_order++;
          }
        }
      }
      
      echo json_encode(['status' => 'success', 'message' => 'Cập nhật thành công']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Lỗi khi cập nhật']);
    }
  } else {
    // INSERT
    $insert_query = "INSERT INTO `destinations` 
                    (`name`, `description`, `short_description`, `location`, `latitude`, `longitude`, 
                     `category`, `rating`, `review_count`, `active`, `image`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insert_values = [$name, $description, $short_description, $location, $latitude, $longitude, 
                     $category, $rating, $review_count, $active, $image_name];
    $insert_types = 'ssssddsdiis';
    
    if(insert($insert_query, $insert_values, $insert_types)){
      $new_destination_id = mysqli_insert_id($con);
      
      // Save multiple images to destination_images table
      if(!empty($uploaded_images)){
        // Check if table exists
        $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'destination_images'");
        if($table_check && mysqli_num_rows($table_check) > 0){
          $img_insert_query = "INSERT INTO `destination_images` (`destination_id`, `image`, `is_primary`, `sort_order`) VALUES (?, ?, ?, ?)";
          $sort_order = 1;
          foreach($uploaded_images as $img){
            $is_primary = ($sort_order == 1) ? 1 : 0;
            @insert($img_insert_query, [$new_destination_id, $img, $is_primary, $sort_order], 'isii');
            $sort_order++;
          }
        }
      }
      
      echo json_encode(['status' => 'success', 'message' => 'Thêm thành công']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Lỗi khi thêm']);
    }
  }
  exit;
}

// ========== TOGGLE ACTIVE ==========
if(isset($_POST['toggle_destination'])){
  $id = (int)$_POST['id'];
  $active = (int)$_POST['active'];
  
  $update_query = "UPDATE `destinations` SET `active` = ? WHERE `id` = ?";
  if(update($update_query, [$active, $id], 'ii')){
    echo json_encode(['status' => 'success', 'message' => 'Cập nhật trạng thái thành công']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi khi cập nhật']);
  }
  exit;
}

// ========== DELETE ==========
if(isset($_POST['delete_destination'])){
  $id = (int)$_POST['id'];
  
  // Get image to delete
  $img_query = "SELECT image FROM `destinations` WHERE `id` = ?";
  $img_result = select($img_query, [$id], 'i');
  if($img_result && mysqli_num_rows($img_result) > 0){
    $img_data = mysqli_fetch_assoc($img_result);
    if(!empty($img_data['image'])){
      $img_path = UPLOAD_IMAGE_PATH . DESTINATIONS_FOLDER . $img_data['image'];
      if(file_exists($img_path)){
        @unlink($img_path);
      }
    }
  }
  
  $delete_query = "DELETE FROM `destinations` WHERE `id` = ?";
  if(delete($delete_query, [$id], 'i')){
    echo json_encode(['status' => 'success', 'message' => 'Xóa thành công']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi khi xóa']);
  }
  exit;
}

// ========== GET SINGLE ==========
if(isset($_POST['get_destination'])){
  $id = (int)$_POST['id'];
  $query = "SELECT * FROM `destinations` WHERE `id` = ?";
  $result = select($query, [$id], 'i');
  
  if($result && mysqli_num_rows($result) > 0){
    $data = mysqli_fetch_assoc($result);
    
    // Get all images for this destination
    $images = [];
    $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'destination_images'");
    if($table_check && mysqli_num_rows($table_check) > 0){
      $images_query = "SELECT * FROM `destination_images` WHERE `destination_id` = ? ORDER BY `is_primary` DESC, `sort_order` ASC";
      $images_result = @select($images_query, [$id], 'i');
      if($images_result && mysqli_num_rows($images_result) > 0){
        while($img_row = mysqli_fetch_assoc($images_result)){
          $images[] = $img_row;
        }
      }
    }
    // If no images in destination_images, use old image field
    if(empty($images) && !empty($data['image'])){
      $images[] = [
        'id' => 0,
        'destination_id' => $id,
        'image' => $data['image'],
        'is_primary' => 1,
        'sort_order' => 1
      ];
    }
    $data['images'] = $images;
    
    echo json_encode(['status' => 'success', 'data' => $data]);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy']);
  }
  exit;
}

// ========== DELETE IMAGE ==========
if(isset($_POST['delete_image'])){
  $image_id = (int)$_POST['image_id'];
  
  // Check if table exists
  $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'destination_images'");
  if(!$table_check || mysqli_num_rows($table_check) == 0){
    echo json_encode(['status' => 'error', 'message' => 'Bảng destination_images chưa tồn tại']);
    exit;
  }
  
  // Get image info
  $img_query = "SELECT image, destination_id FROM `destination_images` WHERE `id` = ?";
  $img_result = @select($img_query, [$image_id], 'i');
  
  if($img_result && mysqli_num_rows($img_result) > 0){
    $img_data = mysqli_fetch_assoc($img_result);
    $img_file = $img_data['image'];
    
    // Delete file
    $img_path = UPLOAD_IMAGE_PATH . DESTINATIONS_FOLDER . $img_file;
    if(file_exists($img_path)){
      @unlink($img_path);
    }
    
    // Delete from database
    $delete_query = "DELETE FROM `destination_images` WHERE `id` = ?";
    if(@delete($delete_query, [$image_id], 'i')){
      echo json_encode(['status' => 'success', 'message' => 'Xóa ảnh thành công']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Lỗi khi xóa ảnh']);
    }
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy ảnh']);
  }
  exit;
}

// ========== SET PRIMARY IMAGE ==========
if(isset($_POST['set_primary_image'])){
  $image_id = (int)$_POST['image_id'];
  $destination_id = (int)$_POST['destination_id'];
  
  // Check if table exists
  $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'destination_images'");
  if(!$table_check || mysqli_num_rows($table_check) == 0){
    echo json_encode(['status' => 'error', 'message' => 'Bảng destination_images chưa tồn tại']);
    exit;
  }
  
  // Unset all primary images for this destination
  $unset_query = "UPDATE `destination_images` SET `is_primary` = 0 WHERE `destination_id` = ?";
  @update($unset_query, [$destination_id], 'i');
  
  // Set this image as primary
  $set_query = "UPDATE `destination_images` SET `is_primary` = 1 WHERE `id` = ?";
  if(@update($set_query, [$image_id], 'i')){
    echo json_encode(['status' => 'success', 'message' => 'Đặt ảnh chính thành công']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi khi đặt ảnh chính']);
  }
  exit;
}

// ============================================
// SPECIALTIES MANAGEMENT
// ============================================

// ========== GET SPECIALTIES LIST ==========
if(isset($_POST['get_specialties'])){
  // Check if table exists
  $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'specialties'");
  if(!$table_check || mysqli_num_rows($table_check) == 0){
    echo '<tr><td colspan="9" class="text-center py-4"><p class="text-muted">Bảng specialties chưa tồn tại. Vui lòng chạy file SQL để tạo bảng.</p></td></tr>';
    exit;
  }
  
  $res = @mysqli_query($con, "SELECT * FROM `specialties` ORDER BY `id` DESC");
  
  // Debug: Check if query failed
  if(!$res){
    echo '<tr><td colspan="9" class="text-center py-4 text-danger">Lỗi SQL: ' . mysqli_error($con) . '</td></tr>';
    exit;
  }
  $html = '';
  $i = 1;

  $cat_map = [
    'food' => '<span class="badge bg-danger">Món ăn</span>',
    'fruit' => '<span class="badge bg-success">Trái cây</span>',
    'drink' => '<span class="badge bg-info">Đồ uống</span>',
    'souvenir' => '<span class="badge bg-warning">Quà lưu niệm</span>'
  ];

  if($res && mysqli_num_rows($res)){
    while($row = mysqli_fetch_assoc($res)){
      $id = (int)$row['id'];
      $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
      $category = $row['category'] ?? 'food';
      $price_range = htmlspecialchars($row['price_range'] ?? 'Liên hệ', ENT_QUOTES, 'UTF-8');
      $location = htmlspecialchars($row['location'] ?? '', ENT_QUOTES, 'UTF-8');
      $rating = number_format((float)$row['rating'], 1);
      $review_count = (int)$row['review_count'];
      $active = (int)$row['active'];
      
      // Lấy ảnh chính từ specialty_images hoặc fallback
      $primary_image = '';
      $images_count = 0;
      
      $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'specialty_images'");
      if($table_check && mysqli_num_rows($table_check) > 0){
        $primary_image_query = "SELECT image FROM `specialty_images` WHERE `specialty_id` = ? AND `is_primary` = 1 ORDER BY `sort_order` ASC LIMIT 1";
        $primary_image_result = @select($primary_image_query, [$id], 'i');
        if($primary_image_result && mysqli_num_rows($primary_image_result) > 0){
          $primary_image_data = mysqli_fetch_assoc($primary_image_result);
          $primary_image = $primary_image_data['image'];
        }
        
        $images_count_query = "SELECT COUNT(*) as count FROM `specialty_images` WHERE `specialty_id` = ?";
        $images_count_result = @select($images_count_query, [$id], 'i');
        if($images_count_result && mysqli_num_rows($images_count_result) > 0){
          $images_count_data = mysqli_fetch_assoc($images_count_result);
          $images_count = (int)$images_count_data['count'];
        }
      }
      
      if(empty($primary_image)){
        $primary_image = !empty($row['image']) ? $row['image'] : 'default.jpg';
      }
      
      // Loại bỏ đường dẫn nếu có trong tên ảnh
      if(strpos($primary_image, 'specialties/') !== false){
        $primary_image = basename($primary_image);
      } else if(strpos($primary_image, '/') !== false && strpos($primary_image, 'http') === false){
        $primary_image = basename($primary_image);
      }
      
      $primary_image_name = $primary_image;
      if(strpos($primary_image_name, '/') !== false || strpos($primary_image_name, '\\') !== false){
        $primary_image_name = basename($primary_image_name);
      }
      if(preg_match('~^https?://~', $primary_image)){
        $image_path = $primary_image;
      } else {
        $image_path = SPECIALTIES_IMG_PATH . $primary_image_name;
      }
      $default_image_path = SPECIALTIES_IMG_PATH . 'default.jpg';
      // If primary image is placeholder, treat as no image
      if(empty($primary_image_name) || $primary_image_name === 'default.jpg'){
        $spec_image_html = "<div style='width:60px;height:60px;display:flex;align-items:center;justify-content:center;background:#f3f4f6;border-radius:8px;color:#9ca3af;'>No Image</div>";
      } else {
        $spec_image_html = "<img src='{$image_path}' alt='{$name}' style='width:60px;height:60px;object-fit:cover;border-radius:8px;' onerror=\"this.onerror=null; this.src='{$default_image_path}'; this.onerror=function(){this.style.display='none';};\">";
      }
      
      $cat_badge = $cat_map[$category] ?? $cat_map['food'];
      $status_badge = $active 
        ? "<span class='badge bg-success'>Hiển thị</span>"
        : "<span class='badge bg-secondary'>Ẩn</span>";
      
      // Đếm số địa điểm mua
      $shops_count = 0;
      $shops_table_check = @mysqli_query($con, "SHOW TABLES LIKE 'specialty_shops'");
      if($shops_table_check && mysqli_num_rows($shops_table_check) > 0){
        $shops_count_query = "SELECT COUNT(*) as count FROM `specialty_shops` WHERE `specialty_id` = ?";
        $shops_count_result = @select($shops_count_query, [$id], 'i');
        if($shops_count_result && mysqli_num_rows($shops_count_result) > 0){
          $shops_count_data = mysqli_fetch_assoc($shops_count_result);
          $shops_count = (int)$shops_count_data['count'];
        }
      }
      
      $html .= "<tr data-category='{$category}' data-active='{$active}' data-images='{$images_count}'>
        <td>{$i}</td>
        <td>
          {$spec_image_html}
          " . ($images_count > 1 ? "<small class='d-block text-muted mt-1'>{$images_count} anh</small>" : "") . "
        </td>
        <td class='text-start'><strong>{$name}</strong></td>
        <td>{$cat_badge}</td>
        <td><small>{$price_range}</small></td>
        <td class='text-start'><small>{$location}</small></td>
        <td>
          <div><i class='bi bi-star-fill text-warning'></i> {$rating}</div>
          <small class='text-muted'>({$review_count} danh gia)</small>
        </td>
        <td>{$status_badge}</td>
        <td>
          <button class='btn btn-sm btn-outline-primary mb-1' onclick='editSpecialty({$id})' title='Sua'>
            <i class='bi bi-pencil'></i>
          </button>
          <button class='btn btn-sm btn-outline-info mb-1' onclick='manageShops({$id})' title='Quan ly dia diem mua'>
            <i class='bi bi-shop'></i> ({$shops_count})
          </button>
          <button class='btn btn-sm btn-outline-secondary mb-1' onclick='toggleSpecialty({$id}, ".($active?0:1).")' title='".($active?'An':'Hien thi')."'>
            <i class='bi bi-eye".($active?'-slash':'')."'></i>
          </button>
          <button class='btn btn-sm btn-outline-danger' onclick='deleteSpecialty({$id})' title='Xoa'>
            <i class='bi bi-trash'></i>
          </button>
        </td>
      </tr>";
      $i++;
    }
  } else {
    $html = '<tr><td colspan="9" class="text-center py-4"><p class="text-muted">Chưa có đặc sản nào</p></td></tr>';
  }
  
  echo $html;
  exit;
}

// ========== SAVE SPECIALTY ==========
if(isset($_POST['save_specialty'])){
  $frm_data = filteration($_POST);
  
  // Check if table exists
  $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'specialties'");
  if(!$table_check || mysqli_num_rows($table_check) == 0){
    echo json_encode(['status' => 'error', 'message' => 'Bảng specialties chưa tồn tại. Vui lòng chạy file SQL để tạo bảng.']);
    exit;
  }
  
  $flag = 0;
  $q = "SELECT * FROM `specialties` WHERE `name` = ? AND `id` != ?";
  $values = [$frm_data['name'], $frm_data['id'] ?? 0];
  $res = select($q, $values, 'si');
  
  if(mysqli_num_rows($res) == 0){
    $img_r = 'no_img';
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){
      $img_r = uploadImage($_FILES['image'], SPECIALTIES_FOLDER);
      
      if($img_r == 'inv_img'){
        echo json_encode(['status' => 'error', 'message' => 'Ảnh không hợp lệ']);
        exit;
      } else if($img_r == 'inv_size'){
        echo json_encode(['status' => 'error', 'message' => 'Kích thước ảnh quá lớn (tối đa 5MB)']);
        exit;
      } else if($img_r == 'upd_failed'){
        echo json_encode(['status' => 'error', 'message' => 'Lỗi khi upload ảnh']);
        exit;
      }
    }
    
    if($frm_data['id'] != 0){
      // Update
      if($img_r != 'no_img'){
        $u_q = "UPDATE `specialties` SET `name`=?, `description`=?, `short_description`=?, `category`=?, `price_range`=?, `best_season`=?, `location`=?, `latitude`=?, `longitude`=?, `rating`=?, `review_count`=?, `active`=?, `image`=? WHERE `id`=?";
        $u_values = [
          $frm_data['name'],
          $frm_data['description'] ?? '',
          $frm_data['short_description'] ?? '',
          $frm_data['category'],
          $frm_data['price_range'] ?? '',
          $frm_data['best_season'] ?? '',
          $frm_data['location'] ?? '',
          !empty($frm_data['latitude']) ? $frm_data['latitude'] : null,
          !empty($frm_data['longitude']) ? $frm_data['longitude'] : null,
          $frm_data['rating'] ?? 0,
          $frm_data['review_count'] ?? 0,
          $frm_data['active'] ?? 1,
          $img_r,
          $frm_data['id']
        ];
        $datatypes = 'sssssssdddiisi';
      } else {
        $u_q = "UPDATE `specialties` SET `name`=?, `description`=?, `short_description`=?, `category`=?, `price_range`=?, `best_season`=?, `location`=?, `latitude`=?, `longitude`=?, `rating`=?, `review_count`=?, `active`=? WHERE `id`=?";
        $u_values = [
          $frm_data['name'],
          $frm_data['description'] ?? '',
          $frm_data['short_description'] ?? '',
          $frm_data['category'],
          $frm_data['price_range'] ?? '',
          $frm_data['best_season'] ?? '',
          $frm_data['location'] ?? '',
          !empty($frm_data['latitude']) ? $frm_data['latitude'] : null,
          !empty($frm_data['longitude']) ? $frm_data['longitude'] : null,
          $frm_data['rating'] ?? 0,
          $frm_data['review_count'] ?? 0,
          $frm_data['active'] ?? 1,
          $frm_data['id']
        ];
        $datatypes = 'sssssssdddiii';
      }
      
      $update_result = update($u_q, $u_values, $datatypes);
      if($update_result !== false){
        $flag = 1;
      } else {
        $db_error = mysqli_error($con);
        error_log("Specialties update failed: " . $db_error);
        echo json_encode(['status' => 'error', 'message' => 'Loi khi luu: ' . ($db_error ?: 'Unknown error')]);
        exit;
      }
    } else {
      // Insert
      $q = "INSERT INTO `specialties` (`name`, `description`, `short_description`, `category`, `price_range`, `best_season`, `location`, `latitude`, `longitude`, `rating`, `review_count`, `active`, `image`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
      $values = [
        $frm_data['name'],
        $frm_data['description'] ?? '',
        $frm_data['short_description'] ?? '',
        $frm_data['category'],
        $frm_data['price_range'] ?? '',
        $frm_data['best_season'] ?? '',
        $frm_data['location'] ?? '',
        !empty($frm_data['latitude']) ? $frm_data['latitude'] : null,
        !empty($frm_data['longitude']) ? $frm_data['longitude'] : null,
        $frm_data['rating'] ?? 0,
        $frm_data['review_count'] ?? 0,
        $frm_data['active'] ?? 1,
        $img_r != 'no_img' ? $img_r : ''
      ];
      $datatypes = 'sssssssdddiis';
      
      if(insert($q, $values, $datatypes)){
        $flag = 1;
      } else {
        $db_error = mysqli_error($con);
        error_log("Specialties insert failed: " . $db_error);
        echo json_encode(['status' => 'error', 'message' => 'Loi khi luu: ' . ($db_error ?: 'Unknown error')]);
        exit;
      }
    }
    
    // Handle multiple images
    if($flag == 1 && isset($_FILES['images']) && !empty($_FILES['images']['name'][0])){
      $specialty_id = $frm_data['id'] != 0 ? $frm_data['id'] : mysqli_insert_id($con);
      
      // Debug: Log specialty_id
      error_log("Specialty ID for image upload: " . $specialty_id);
      error_log("Files count: " . count($_FILES['images']['name']));
      
      // Ensure specialty_images table exists
      $img_table_check = @mysqli_query($con, "SHOW TABLES LIKE 'specialty_images'");
      if(!$img_table_check || mysqli_num_rows($img_table_check) == 0){
        $create_table = "CREATE TABLE IF NOT EXISTS `specialty_images` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `specialty_id` INT(11) NOT NULL,
          `image` VARCHAR(255) NOT NULL,
          `is_primary` TINYINT(1) DEFAULT 0,
          `sort_order` INT(11) DEFAULT 0,
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `specialty_id` (`specialty_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        if(!@mysqli_query($con, $create_table)){
          error_log("Failed to create specialty_images table: " . mysqli_error($con));
        }
      }
      
      $uploaded_count = 0;
      $upload_errors = [];
      $image_count = count($_FILES['images']['name']);
      
      for($i = 0; $i < $image_count; $i++){
        if(!empty($_FILES['images']['name'][$i])){
          // Check for upload errors
          if($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK){
            $upload_errors[] = "File " . ($i + 1) . ": Lỗi upload (code: " . $_FILES['images']['error'][$i] . ")";
            continue;
          }
          
          $file = [
            'name' => $_FILES['images']['name'][$i],
            'type' => $_FILES['images']['type'][$i],
            'tmp_name' => $_FILES['images']['tmp_name'][$i],
            'error' => $_FILES['images']['error'][$i],
            'size' => $_FILES['images']['size'][$i]
          ];
          
          $uploaded_img = uploadImage($file, SPECIALTIES_FOLDER);
          error_log("Upload result for file " . ($i + 1) . " (" . $file['name'] . "): " . $uploaded_img);
          
          if($uploaded_img == 'inv_img'){
            $upload_errors[] = "File " . ($i + 1) . ": Định dạng không hợp lệ";
          } else if($uploaded_img == 'inv_size'){
            $upload_errors[] = "File " . ($i + 1) . ": Kích thước quá lớn (tối đa 10MB)";
          } else if($uploaded_img == 'upd_failed'){
            $upload_errors[] = "File " . ($i + 1) . ": Lỗi khi upload";
            error_log("Upload failed for file: " . $file['name'] . " | Error code: " . $file['error'] . " | Size: " . $file['size']);
          } else {
            // Upload thành công, lưu vào database
            error_log("Upload successful: " . $uploaded_img . " | Full path: " . UPLOAD_IMAGE_PATH . SPECIALTIES_FOLDER . $uploaded_img);
            $is_primary = ($i == 0 && $uploaded_count == 0) ? 1 : 0;
            $img_q = "INSERT INTO `specialty_images` (`specialty_id`, `image`, `is_primary`, `sort_order`) VALUES (?,?,?,?)";
            $img_values = [$specialty_id, $uploaded_img, $is_primary, $uploaded_count + 1];
            error_log("Attempting to insert image into DB: specialty_id=" . $specialty_id . ", image=" . $uploaded_img);
            $insert_result = @insert($img_q, $img_values, 'isii');
            if($insert_result){
              $uploaded_count++;
              error_log("Image inserted successfully into database");
            } else {
              // Xóa file đã upload nếu insert DB thất bại
              $img_path = UPLOAD_IMAGE_PATH . SPECIALTIES_FOLDER . $uploaded_img;
              if(file_exists($img_path)){
                @unlink($img_path);
              }
              $db_error = mysqli_error($con);
              $upload_errors[] = "File " . ($i + 1) . ": Lỗi khi lưu vào database" . ($db_error ? " - " . $db_error : "");
              error_log("Failed to insert specialty image: " . $db_error . " | Query: " . $img_q . " | Values: " . print_r($img_values, true));
            }
          }
        }
      }
      
      // Log errors if any
      if(!empty($upload_errors)){
        error_log("Specialty image upload errors: " . implode(", ", $upload_errors));
      }
      
      // Thêm thông tin về upload vào response message
      $upload_info = '';
      if($uploaded_count > 0){
        $upload_info = " Đã upload {$uploaded_count} ảnh thành công.";
      }
      if(!empty($upload_errors)){
        $upload_info .= " " . count($upload_errors) . " ảnh lỗi: " . implode(", ", array_slice($upload_errors, 0, 3));
        if(count($upload_errors) > 3){
          $upload_info .= "...";
        }
      }
    }
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Tên đặc sản đã tồn tại']);
    exit;
  }
  
  if($flag){
    $message = 'Lưu thành công';
    if(isset($upload_info)){
      $message .= $upload_info;
    }
    echo json_encode(['status' => 'success', 'message' => $message, 'uploaded_count' => isset($uploaded_count) ? $uploaded_count : 0]);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi khi lưu']);
  }
  exit;
}

// ========== GET SINGLE SPECIALTY ==========
if(isset($_POST['get_single_specialty'])){
  $frm_data = filteration($_POST);
  $values = [$frm_data['get_single_specialty']];
  
  $res = select("SELECT * FROM `specialties` WHERE `id`=?", $values, 'i');
  
  if(mysqli_num_rows($res) == 1){
    $row = mysqli_fetch_assoc($res);
    
    // Get images
    $images = [];
    $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'specialty_images'");
    if($table_check && mysqli_num_rows($table_check) > 0){
      $img_res = select("SELECT * FROM `specialty_images` WHERE `specialty_id`=? ORDER BY `is_primary` DESC, `sort_order` ASC", $values, 'i');
      if($img_res && mysqli_num_rows($img_res) > 0){
        while($img_row = mysqli_fetch_assoc($img_res)){
          // Đảm bảo đường dẫn ảnh đúng
          $img_name = $img_row['image'];
          // Loại bỏ đường dẫn nếu có trong tên ảnh
          if(strpos($img_name, 'specialties/') !== false){
            $img_name = basename($img_name);
          } else if(strpos($img_name, '/') !== false && strpos($img_name, 'http') === false){
            $img_name = basename($img_name);
          }
          $images[] = [
            'id' => $img_row['id'],
            'image' => SPECIALTIES_IMG_PATH . $img_name,
            'is_primary' => $img_row['is_primary']
          ];
        }
      }
    }
    
    $data = $row;
    $data['images'] = $images;
    
    echo json_encode(['status' => 'success', 'data' => $data]);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy']);
  }
  exit;
}

// ========== TOGGLE SPECIALTY ==========
if(isset($_POST['toggle_specialty'])){
  $frm_data = filteration($_POST);
  $values = [$frm_data['toggle_specialty'], $frm_data['value']];
  
  if(update("UPDATE `specialties` SET `active`=? WHERE `id`=?", $values, 'ii')){
    echo json_encode(['status' => 'success', 'message' => 'Cập nhật thành công']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi khi cập nhật']);
  }
  exit;
}

// ========== DELETE SPECIALTY ==========
if(isset($_POST['delete_specialty'])){
  $frm_data = filteration($_POST);
  $values = [$frm_data['delete_specialty']];
  
  // Delete images
  $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'specialty_images'");
  if($table_check && mysqli_num_rows($table_check) > 0){
    $img_res = select("SELECT image FROM `specialty_images` WHERE `specialty_id`=?", $values, 'i');
    if($img_res && mysqli_num_rows($img_res) > 0){
      while($img_row = mysqli_fetch_assoc($img_res)){
        $img_path = UPLOAD_IMAGE_PATH . SPECIALTIES_FOLDER . $img_row['image'];
        if(file_exists($img_path)){
          @unlink($img_path);
        }
      }
      @delete("DELETE FROM `specialty_images` WHERE `specialty_id`=?", $values, 'i');
    }
  }
  
  // Delete main image
  $spec_res = select("SELECT image FROM `specialties` WHERE `id`=?", $values, 'i');
  if($spec_res && mysqli_num_rows($spec_res) > 0){
    $spec_row = mysqli_fetch_assoc($spec_res);
    if(!empty($spec_row['image'])){
      $img_path = UPLOAD_IMAGE_PATH . SPECIALTIES_FOLDER . $spec_row['image'];
      if(file_exists($img_path)){
        @unlink($img_path);
      }
    }
  }
  
  // Delete shops
  $shops_table_check = @mysqli_query($con, "SHOW TABLES LIKE 'specialty_shops'");
  if($shops_table_check && mysqli_num_rows($shops_table_check) > 0){
    @delete("DELETE FROM `specialty_shops` WHERE `specialty_id`=?", $values, 'i');
  }
  
  // Delete specialty
  if(delete("DELETE FROM `specialties` WHERE `id`=?", $values, 'i')){
    echo json_encode(['status' => 'success', 'message' => 'Xóa thành công']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi khi xóa']);
  }
  exit;
}

// ========== GET SPECIALTY SHOPS ==========
if(isset($_POST['get_specialty_shops'])){
  $specialty_id = (int)$_POST['get_specialty_shops'];
  
  $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'specialty_shops'");
  if(!$table_check || mysqli_num_rows($table_check) == 0){
    echo json_encode(['status' => 'error', 'message' => 'Bảng specialty_shops chưa tồn tại', 'shops' => []]);
    exit;
  }
  
  $res = select("SELECT * FROM `specialty_shops` WHERE `specialty_id`=? ORDER BY `rating` DESC", [$specialty_id], 'i');
  $shops = [];
  
  if($res && mysqli_num_rows($res) > 0){
    while($row = mysqli_fetch_assoc($res)){
      $shops[] = $row;
    }
  }
  
  echo json_encode(['status' => 'success', 'shops' => $shops]);
  exit;
}

// ========== SAVE SPECIALTY SHOP ==========
if(isset($_POST['save_specialty_shop'])){
  $frm_data = filteration($_POST);
  
  // Ensure table exists
  $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'specialty_shops'");
  if(!$table_check || mysqli_num_rows($table_check) == 0){
    $create_table = "CREATE TABLE IF NOT EXISTS `specialty_shops` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `specialty_id` INT(11) NOT NULL,
      `shop_name` VARCHAR(255) NOT NULL,
      `address` VARCHAR(500),
      `phone` VARCHAR(20),
      `latitude` DECIMAL(10,8),
      `longitude` DECIMAL(11,8),
      `opening_hours` VARCHAR(200),
      `rating` DECIMAL(3,2) DEFAULT 0,
      `active` TINYINT(1) DEFAULT 1,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `specialty_id` (`specialty_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    @mysqli_query($con, $create_table);
  }
  
  if(!empty($frm_data['shop_id'])){
    // Update
    $q = "UPDATE `specialty_shops` SET `shop_name`=?, `address`=?, `phone`=?, `latitude`=?, `longitude`=?, `opening_hours`=?, `rating`=?, `active`=? WHERE `id`=?";
    $values = [
      $frm_data['shop_name'],
      $frm_data['address'] ?? '',
      $frm_data['phone'] ?? '',
      !empty($frm_data['latitude']) ? $frm_data['latitude'] : null,
      !empty($frm_data['longitude']) ? $frm_data['longitude'] : null,
      $frm_data['opening_hours'] ?? '',
      $frm_data['rating'] ?? 0,
      $frm_data['active'] ?? 1,
      $frm_data['shop_id']
    ];
    $datatypes = 'issssddi';
    if(update($q, $values, $datatypes)){
      echo json_encode(['status' => 'success', 'message' => 'Cập nhật địa điểm mua thành công']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Lỗi khi cập nhật']);
    }
  } else {
    // Insert
    $q = "INSERT INTO `specialty_shops` (`specialty_id`, `shop_name`, `address`, `phone`, `latitude`, `longitude`, `opening_hours`, `rating`, `active`) VALUES (?,?,?,?,?,?,?,?,?)";
    $values = [
      $frm_data['specialty_id'],
      $frm_data['shop_name'],
      $frm_data['address'] ?? '',
      $frm_data['phone'] ?? '',
      !empty($frm_data['latitude']) ? $frm_data['latitude'] : null,
      !empty($frm_data['longitude']) ? $frm_data['longitude'] : null,
      $frm_data['opening_hours'] ?? '',
      $frm_data['rating'] ?? 0,
      $frm_data['active'] ?? 1
    ];
    $datatypes = 'issssddi';
    if(insert($q, $values, $datatypes)){
      echo json_encode(['status' => 'success', 'message' => 'Thêm địa điểm mua thành công']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Lỗi khi thêm']);
    }
  }
  exit;
}

// ========== DELETE SPECIALTY SHOP ==========
if(isset($_POST['delete_specialty_shop'])){
  $shop_id = (int)$_POST['delete_specialty_shop'];
  
  if(delete("DELETE FROM `specialty_shops` WHERE `id`=?", [$shop_id], 'i')){
    echo json_encode(['status' => 'success', 'message' => 'Xóa địa điểm mua thành công']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi khi xóa']);
  }
  exit;
}

// ========== DELETE SPECIALTY IMAGE ==========
if(isset($_POST['delete_specialty_image'])){
  $image_id = (int)$_POST['image_id'];
  
  $table_check = @mysqli_query($con, "SHOW TABLES LIKE 'specialty_images'");
  if(!$table_check || mysqli_num_rows($table_check) == 0){
    echo json_encode(['status' => 'error', 'message' => 'Bảng specialty_images chưa tồn tại']);
    exit;
  }
  
  $img_query = "SELECT image FROM `specialty_images` WHERE `id` = ?";
  $img_result = @select($img_query, [$image_id], 'i');
  
  if($img_result && mysqli_num_rows($img_result) > 0){
    $img_data = mysqli_fetch_assoc($img_result);
    $img_file = $img_data['image'];
    
    $img_path = UPLOAD_IMAGE_PATH . SPECIALTIES_FOLDER . $img_file;
    if(file_exists($img_path)){
      @unlink($img_path);
    }
    
    $delete_query = "DELETE FROM `specialty_images` WHERE `id` = ?";
    if(@delete($delete_query, [$image_id], 'i')){
      echo json_encode(['status' => 'success', 'message' => 'Xóa ảnh thành công']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Lỗi khi xóa ảnh']);
    }
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy ảnh']);
  }
  exit;
}

// ========== SET PRIMARY SPECIALTY IMAGE ==========
if(isset($_POST['set_primary_specialty_image'])){
  $image_id = (int)$_POST['image_id'];
  $specialty_id = (int)$_POST['specialty_id'];
  
  // Unset all primary images for this specialty
  $unset_query = "UPDATE `specialty_images` SET `is_primary` = 0 WHERE `specialty_id` = ?";
  @update($unset_query, [$specialty_id], 'i');
  
  // Set this image as primary
  $set_query = "UPDATE `specialty_images` SET `is_primary` = 1 WHERE `id` = ?";
  if(@update($set_query, [$image_id], 'i')){
    echo json_encode(['status' => 'success', 'message' => 'Đặt ảnh chính thành công']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi khi đặt ảnh chính']);
  }
  exit;
}



