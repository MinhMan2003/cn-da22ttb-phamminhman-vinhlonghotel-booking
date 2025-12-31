<?php
session_start();
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

header('Content-Type: application/json');

// Ensure FAQs table exists
$check_faqs = @mysqli_query($con, "SHOW TABLES LIKE 'faqs'");
if(!$check_faqs || mysqli_num_rows($check_faqs) == 0) {
  $create_faqs = "CREATE TABLE IF NOT EXISTS `faqs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `question` VARCHAR(500) NOT NULL,
    `answer` TEXT NOT NULL,
    `keywords` TEXT,
    `category` VARCHAR(100) DEFAULT 'general',
    `priority` INT(11) DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_category` (`category`),
    KEY `idx_active` (`active`),
    KEY `idx_priority` (`priority`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
  @mysqli_query($con, $create_faqs);
}

// ========== IMPORT 100 FAQs ==========
if(isset($_GET['import_100_faqs'])) {
  // Load FAQs data from separate file
  $faqs_file = __DIR__ . '/import_faqs_data.php';
  if(file_exists($faqs_file)) {
    $faqs = require($faqs_file);
    
    $inserted = 0;
    $skipped = 0;
    
    foreach($faqs as $faq) {
      $question = $faq[0];
      $answer = $faq[1];
      $keywords = $faq[2];
      $category = $faq[3];
      $priority = $faq[4];
      
      // Kiểm tra xem FAQ đã tồn tại chưa (dựa trên question)
      $check_query = "SELECT id FROM `faqs` WHERE `question` = ?";
      $check_stmt = mysqli_prepare($con, $check_query);
      if($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "s", $question);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        if($check_result && mysqli_num_rows($check_result) > 0) {
          $skipped++;
          mysqli_stmt_close($check_stmt);
          continue;
        }
        mysqli_stmt_close($check_stmt);
      }
      
      // Insert FAQ
      $query = "INSERT INTO `faqs` (`question`, `answer`, `keywords`, `category`, `priority`, `active`) 
                VALUES (?, ?, ?, ?, ?, 1)";
      $stmt = mysqli_prepare($con, $query);
      if($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssi", $question, $answer, $keywords, $category, $priority);
        if(mysqli_stmt_execute($stmt)) {
          $inserted++;
        }
        mysqli_stmt_close($stmt);
      }
    }
    
    // Đếm lại sau khi import
    $count_query = mysqli_query($con, "SELECT COUNT(*) as total FROM `faqs`");
    $count_row = mysqli_fetch_assoc($count_query);
    $final_count = (int)$count_row['total'];
    
    echo json_encode([
      'status' => 'success',
      'message' => "Đã thêm {$inserted} FAQ mới. Đã bỏ qua {$skipped} FAQ (đã tồn tại). Tổng số FAQ: {$final_count}",
      'inserted' => $inserted,
      'skipped' => $skipped,
      'total' => $final_count
    ]);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'File import_faqs_data.php không tồn tại']);
  }
  exit;
}

// ========== GET FAQs LIST ==========
if(isset($_GET['get_faqs'])) {
  $res = mysqli_query($con, "SELECT * FROM `faqs` ORDER BY `priority` DESC, `id` ASC");
  $html = '';
  $i = 1;
  
  $cat_map = [
    'general' => '<span class="badge bg-secondary">Chung</span>',
    'booking' => '<span class="badge bg-primary">Đặt phòng</span>',
    'payment' => '<span class="badge bg-success">Thanh toán</span>',
    'room' => '<span class="badge bg-info">Phòng</span>',
    'facilities' => '<span class="badge bg-warning">Tiện ích</span>',
    'destinations' => '<span class="badge bg-danger">Điểm đến</span>'
  ];
  
  if($res && mysqli_num_rows($res) > 0) {
    while($row = mysqli_fetch_assoc($res)) {
      $id = (int)$row['id'];
      $question = htmlspecialchars($row['question'], ENT_QUOTES, 'UTF-8');
      $answer = htmlspecialchars(mb_substr($row['answer'], 0, 100), ENT_QUOTES, 'UTF-8') . (mb_strlen($row['answer']) > 100 ? '...' : '');
      $keywords = htmlspecialchars($row['keywords'] ?? '', ENT_QUOTES, 'UTF-8');
      $category = $row['category'] ?? 'general';
      $priority = (int)$row['priority'];
      $active = (int)$row['active'];
      
      $cat_badge = $cat_map[$category] ?? $cat_map['general'];
      $status_badge = $active 
        ? "<span class='badge bg-success'>Hiển thị</span>"
        : "<span class='badge bg-secondary'>Ẩn</span>";
      
      $html .= "<tr>
        <td>{$i}</td>
        <td class='text-start'><strong>{$question}</strong></td>
        <td class='text-start'><small>{$answer}</small></td>
        <td class='text-start'><small>{$keywords}</small></td>
        <td>{$cat_badge}</td>
        <td>{$priority}</td>
        <td>{$status_badge}</td>
        <td>
          <button class='btn btn-sm btn-outline-primary mb-1' onclick='editFaq({$id})' title='Sửa'>
            <i class='bi bi-pencil'></i>
          </button>
          <button class='btn btn-sm btn-outline-secondary mb-1' onclick='toggleFaq({$id}, ".($active?0:1).")' title='".($active?'Ẩn':'Hiển thị')."'>
            <i class='bi ".($active?'bi-eye-slash':'bi-eye')."'></i>
          </button>
          <button class='btn btn-sm btn-outline-danger' onclick='deleteFaq({$id})' title='Xóa'>
            <i class='bi bi-trash'></i>
          </button>
        </td>
      </tr>";
      $i++;
    }
  } else {
    $html = "<tr><td colspan='8' class='text-center text-muted py-4'>Chưa có FAQ nào</td></tr>";
  }
  
  echo json_encode(['status' => 'success', 'html' => $html]);
  exit;
}

// ========== GET SINGLE FAQ ==========
if(isset($_GET['get_single'])) {
  $id = (int)($_GET['id'] ?? 0);
  if($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
    exit;
  }
  
  $result = select("SELECT * FROM `faqs` WHERE `id` = ?", [$id], 'i');
  if($result && mysqli_num_rows($result) > 0) {
    $faq = mysqli_fetch_assoc($result);
    echo json_encode(['status' => 'success', 'faq' => $faq]);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'FAQ not found']);
  }
  exit;
}

// ========== SAVE FAQ ==========
if(isset($_POST['save_faq'])) {
  $id = (int)($_POST['id'] ?? 0);
  $question = trim($_POST['question'] ?? '');
  $answer = trim($_POST['answer'] ?? '');
  $keywords = trim($_POST['keywords'] ?? '');
  $category = $_POST['category'] ?? 'general';
  $priority = (int)($_POST['priority'] ?? 0);
  $active = (int)($_POST['active'] ?? 1);
  
  if(empty($question) || empty($answer)) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin']);
    exit;
  }
  
  if($id > 0) {
    // Update
    $query = "UPDATE `faqs` SET `question` = ?, `answer` = ?, `keywords` = ?, `category` = ?, `priority` = ?, `active` = ? WHERE `id` = ?";
    $result = update($query, [$question, $answer, $keywords, $category, $priority, $active, $id], 'ssssiii');
  } else {
    // Insert
    $query = "INSERT INTO `faqs` (`question`, `answer`, `keywords`, `category`, `priority`, `active`) VALUES (?, ?, ?, ?, ?, ?)";
    $result = insert($query, [$question, $answer, $keywords, $category, $priority, $active], 'ssssii');
  }
  
  if($result) {
    echo json_encode(['status' => 'success', 'message' => 'Đã lưu FAQ']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi khi lưu']);
  }
  exit;
}

// ========== TOGGLE FAQ ==========
if(isset($_POST['toggle_faq'])) {
  $id = (int)($_POST['id'] ?? 0);
  $active = (int)($_POST['active'] ?? 1);
  
  if($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
    exit;
  }
  
  $result = update("UPDATE `faqs` SET `active` = ? WHERE `id` = ?", [$active, $id], 'ii');
  
  if($result) {
    echo json_encode(['status' => 'success', 'message' => 'Đã cập nhật trạng thái']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi khi cập nhật']);
  }
  exit;
}

// ========== DELETE FAQ ==========
if(isset($_POST['delete_faq'])) {
  $id = (int)($_POST['id'] ?? 0);
  
  if($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
    exit;
  }
  
  $result = delete("DELETE FROM `faqs` WHERE `id` = ?", [$id], 'i');
  
  if($result) {
    echo json_encode(['status' => 'success', 'message' => 'Đã xóa FAQ']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi khi xóa']);
  }
  exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
exit;

