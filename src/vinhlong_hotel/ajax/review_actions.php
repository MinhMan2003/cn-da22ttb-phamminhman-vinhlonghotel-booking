<?php 
  require('../admin/inc/db_config.php');
  require('../admin/inc/essentials.php');

  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }

  // Đánh dấu review hữu ích
  if(isset($_POST['mark_helpful']))
  {
    if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
      echo json_encode(['status' => 'error', 'msg' => 'Vui lòng đăng nhập']);
      exit;
    }

    $frm_data = filteration($_POST);
    $review_id = (int)$frm_data['review_id'];
    $user_id = $_SESSION['uId'];

    // Kiểm tra đã đánh dấu chưa
    $check = select("SELECT id FROM review_helpful WHERE review_id=? AND user_id=?", [$review_id, $user_id], 'ii');
    
    if(mysqli_num_rows($check) > 0){
      // Bỏ đánh dấu
      $del = update("DELETE FROM review_helpful WHERE review_id=? AND user_id=?", [$review_id, $user_id], 'ii');
      if($del){
        // Cập nhật helpful_count
        update("UPDATE rating_review SET helpful_count = GREATEST(helpful_count - 1, 0) WHERE sr_no=?", [$review_id], 'i');
        $new_count = mysqli_fetch_assoc(select("SELECT helpful_count FROM rating_review WHERE sr_no=?", [$review_id], 'i'))['helpful_count'];
        echo json_encode(['status' => 'removed', 'count' => (int)$new_count]);
      } else {
        echo json_encode(['status' => 'error']);
      }
    } else {
      // Đánh dấu
      $ins = insert("INSERT INTO review_helpful (review_id, user_id) VALUES (?,?)", [$review_id, $user_id], 'ii');
      if($ins){
        // Cập nhật helpful_count
        update("UPDATE rating_review SET helpful_count = helpful_count + 1 WHERE sr_no=?", [$review_id], 'i');
        $new_count = mysqli_fetch_assoc(select("SELECT helpful_count FROM rating_review WHERE sr_no=?", [$review_id], 'i'))['helpful_count'];
        echo json_encode(['status' => 'added', 'count' => (int)$new_count]);
      } else {
        echo json_encode(['status' => 'error']);
      }
    }
    exit;
  }

  // Lấy reviews với filter
  if(isset($_POST['get_reviews']))
  {
    header('Content-Type: application/json; charset=utf-8');
    
    $frm_data = filteration($_POST);
    $room_id = (int)$frm_data['room_id'];
    $filter_rating = isset($frm_data['rating']) ? (int)$frm_data['rating'] : 0;
    $sort_by = isset($frm_data['sort_by']) ? $frm_data['sort_by'] : 'newest'; // helpful, newest, oldest
    $user_id = isset($_SESSION['uId']) ? $_SESSION['uId'] : 0;

    $where = "rr.room_id = ?";
    $params = [$room_id];
    $types = 'i';

    if($filter_rating > 0){
      $where .= " AND rr.rating = ?";
      $params[] = $filter_rating;
      $types .= 'i';
    }

    // Kiểm tra cột helpful_count có tồn tại không
    $check_helpful_col = @mysqli_query($con, "SHOW COLUMNS FROM rating_review LIKE 'helpful_count'");
    $has_helpful_col = $check_helpful_col && mysqli_num_rows($check_helpful_col) > 0;
    
    // Kiểm tra cột images có tồn tại không
    $check_images_col = @mysqli_query($con, "SHOW COLUMNS FROM rating_review LIKE 'images'");
    $has_images_col = $check_images_col && mysqli_num_rows($check_images_col) > 0;
    
    // Kiểm tra cột admin_reply có tồn tại không
    $check_admin_reply_col = @mysqli_query($con, "SHOW COLUMNS FROM rating_review LIKE 'admin_reply'");
    $has_admin_reply_col = $check_admin_reply_col && mysqli_num_rows($check_admin_reply_col) > 0;

    // Sắp xếp
    if($has_helpful_col && $sort_by === 'helpful'){
      $order_by = "COALESCE(rr.helpful_count, 0) DESC, rr.sr_no DESC";
    } else if($sort_by === 'newest'){
      $order_by = "rr.sr_no DESC";
    } else if($sort_by === 'oldest'){
      $order_by = "rr.sr_no ASC";
    } else {
      $order_by = "rr.sr_no DESC";
    }

    // Kiểm tra xem bảng review_helpful có tồn tại không
    $check_table = @mysqli_query($con, "SHOW TABLES LIKE 'review_helpful'");
    $has_helpful_table = $check_table && mysqli_num_rows($check_table) > 0;
    
    // Xây dựng query với user_helpful - đơn giản hóa
    try {
      if($has_helpful_table && $user_id > 0){
        $user_helpful_subquery = "(SELECT COUNT(*) FROM review_helpful WHERE review_id = rr.sr_no AND user_id = ?)";
        $params[] = $user_id;
        $types .= 'i';
      } else {
        $user_helpful_subquery = "0";
      }
      
      // Xây dựng SELECT với các cột có thể không tồn tại
      $select_cols = [
        'rr.sr_no',
        'rr.rating',
        'rr.review',
        'rr.datentime',
        'rr.user_id',
        "COALESCE(uc.name, 'Khách hàng') AS uname",
        "COALESCE(uc.profile, '') AS profile",
        "$user_helpful_subquery AS user_helpful"
      ];
      
      if($has_images_col){
        $select_cols[] = 'rr.images';
      } else {
        $select_cols[] = 'NULL AS images';
      }
      
      if($has_helpful_col){
        $select_cols[] = 'COALESCE(rr.helpful_count, 0) AS helpful_count';
      } else {
        $select_cols[] = '0 AS helpful_count';
      }
      
      if($has_admin_reply_col){
        $select_cols[] = 'rr.admin_reply';
        $select_cols[] = 'rr.admin_reply_date';
      } else {
        $select_cols[] = 'NULL AS admin_reply';
        $select_cols[] = 'NULL AS admin_reply_date';
      }
      
      $query = "SELECT " . implode(', ', $select_cols) . "
                FROM rating_review rr 
                LEFT JOIN user_cred uc ON rr.user_id = uc.id
                WHERE $where
                ORDER BY $order_by
                LIMIT 20";
    } catch(Exception $e) {
      echo json_encode(['status' => 'error', 'msg' => 'Query build error: ' . $e->getMessage(), 'reviews' => []]);
      exit;
    }

    $res = select($query, $params, $types);
    $reviews = [];
    
    if(!$res){
      $error_msg = mysqli_error($con);
      echo json_encode(['status' => 'error', 'msg' => 'Query error: ' . $error_msg, 'reviews' => []]);
      exit;
    }
    
    $num_rows = mysqli_num_rows($res);
    
    while($row = mysqli_fetch_assoc($res)){
      $images = [];
      if(!empty($row['images'])){
        $images_raw = json_decode($row['images'], true);
        if(json_last_error() === JSON_ERROR_NONE && is_array($images_raw)){
          foreach($images_raw as $img){
            // Xử lý đường dẫn ảnh
            $img_path = trim($img);
            // Loại bỏ ../ nếu có ở đầu
            $img_path = preg_replace('#^\.\./#', '', $img_path);
            // Loại bỏ / ở đầu nếu có
            $img_path = ltrim($img_path, '/');
            // Đảm bảo có prefix images/ nếu chưa có
            if(!empty($img_path) && strpos($img_path, 'images/') !== 0 && !filter_var($img_path, FILTER_VALIDATE_URL)){
              if(strpos($img_path, 'images/') !== 0){
                $img_path = 'images/' . ltrim($img_path, '/');
              }
            }
            $images[] = $img_path;
          }
        }
      }
      
      $reviews[] = [
        'id' => (int)$row['sr_no'],
        'rating' => (int)$row['rating'],
        'review' => htmlspecialchars($row['review']),
        'images' => $images,
        'helpful_count' => (int)$row['helpful_count'],
        'admin_reply' => $row['admin_reply'],
        'admin_reply_date' => $row['admin_reply_date'],
        'date' => date('d/m/Y', strtotime($row['datentime'])),
        'user_name' => htmlspecialchars($row['uname']),
        'user_avatar' => $row['profile'] ? (filter_var($row['profile'], FILTER_VALIDATE_URL) ? $row['profile'] : '../'.USERS_IMG_PATH.$row['profile']) : '',
        'user_helpful' => (int)$row['user_helpful'] > 0
      ];
    }

    echo json_encode([
      'status' => 'success', 
      'reviews' => $reviews,
      'debug' => [
        'room_id' => $room_id,
        'num_rows' => $num_rows,
        'has_helpful_table' => isset($has_helpful_table) ? $has_helpful_table : false,
        'query' => $query
      ]
    ], JSON_UNESCAPED_UNICODE);
    exit;
  }

?>

