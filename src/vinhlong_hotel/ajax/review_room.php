<?php 

  require('../admin/inc/db_config.php');
  require('../admin/inc/essentials.php');

  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }


  if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
    redirect('index.php');
  }

  if(isset($_POST['review_form']))
  {
    try {
      $frm_data = filteration($_POST);

      // Kiểm tra dữ liệu bắt buộc
      if(empty($frm_data['booking_id']) || empty($frm_data['room_id']) || empty($frm_data['rating']) || empty($frm_data['review'])){
        echo json_encode(['status' => 'error', 'msg' => 'Vui lòng điền đầy đủ thông tin']);
        exit;
      }

      $upd_query = "UPDATE `booking_order` SET `rate_review`=? WHERE `booking_id`=? AND `user_id`=?";
      $upd_values = [1,$frm_data['booking_id'],$_SESSION['uId']];
      $upd_result = update($upd_query,$upd_values,'iii');

      // Xử lý upload ảnh
      $images = [];
      // Kiểm tra cả 2 cách: review_images và review_images[]
      $files_to_process = [];
      
      if(isset($_FILES['review_images']) && is_array($_FILES['review_images']['name'])){
        $files_to_process = $_FILES['review_images'];
      } else if(isset($_FILES['review_images[]']) && is_array($_FILES['review_images[]']['name'])){
        $files_to_process = $_FILES['review_images[]'];
      }
      
      if(!empty($files_to_process) && is_array($files_to_process['name'])){
        // Giới hạn tối đa 5 ảnh
        $file_count = count(array_filter($files_to_process['name'], function($name) {
          return !empty($name);
        }));
        
        if($file_count > 5){
          echo json_encode(['status' => 'error', 'msg' => 'Chỉ được upload tối đa 5 ảnh!']);
          exit;
        }
        
        $upload_dir = '../images/reviews/';
        if(!is_dir($upload_dir)){
          if(!mkdir($upload_dir, 0777, true)){
            echo json_encode(['status' => 'error', 'msg' => 'Không thể tạo thư mục upload']);
            exit;
          }
        }
        
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        $max_images = 5; // Tối đa 5 ảnh
        
        // Loại bỏ duplicate - chỉ lấy unique files
        $processed_files = [];
        
        foreach($files_to_process['name'] as $key => $name){
          // Kiểm tra đã đạt giới hạn 5 ảnh chưa
          if(count($images) >= $max_images){
            break;
          }
          
          if(!empty($name) && isset($files_to_process['error'][$key]) && $files_to_process['error'][$key] === UPLOAD_ERR_OK){
            $tmp_name = $files_to_process['tmp_name'][$key];
            $type = $files_to_process['type'][$key];
            $size = $files_to_process['size'][$key];
            
            // Kiểm tra duplicate bằng cách so sánh size và name
            $file_key = md5($name.$size);
            if(isset($processed_files[$file_key])){
              continue; // Bỏ qua file trùng
            }
            $processed_files[$file_key] = true;
            
            if(in_array($type, $allowed_types) && $size <= $max_size){
              $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
              $new_name = 'review_'.$_SESSION['uId'].'_'.time().'_'.$key.'.'.$ext;
              $upload_path = $upload_dir.$new_name;
              
              if(move_uploaded_file($tmp_name, $upload_path)){
                $images[] = 'images/reviews/'.$new_name;
              }
            }
          }
        }
      }
      
      $images_json = !empty($images) ? json_encode($images, JSON_UNESCAPED_UNICODE) : NULL;

      // Kiểm tra xem cột images có tồn tại không
      $check_col = mysqli_query($con, "SHOW COLUMNS FROM `rating_review` LIKE 'images'");
      if(mysqli_num_rows($check_col) > 0){
        // Có cột images
        $ins_query = "INSERT INTO `rating_review`(`booking_id`, `room_id`, `user_id`, `rating`, `review`, `images`)
          VALUES (?,?,?,?,?,?)";
        $ins_values = [$frm_data['booking_id'],$frm_data['room_id'],$_SESSION['uId'],
          $frm_data['rating'],$frm_data['review'],$images_json];
        $ins_result = insert($ins_query,$ins_values,'iiiiss');
      } else {
        // Không có cột images, insert không có images
        $ins_query = "INSERT INTO `rating_review`(`booking_id`, `room_id`, `user_id`, `rating`, `review`)
          VALUES (?,?,?,?,?)";
        $ins_values = [$frm_data['booking_id'],$frm_data['room_id'],$_SESSION['uId'],
          $frm_data['rating'],$frm_data['review']];
        $ins_result = insert($ins_query,$ins_values,'iiiis');
      }

      if($ins_result){
        echo json_encode(['status' => 'success', 'msg' => 'Đánh giá đã được gửi thành công!']);
      } else {
        echo json_encode(['status' => 'error', 'msg' => 'Không thể lưu đánh giá. Vui lòng thử lại.']);
      }
    } catch(Exception $e){
      echo json_encode(['status' => 'error', 'msg' => 'Lỗi: '.$e->getMessage()]);
    }
  }

?>