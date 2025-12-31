<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();

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
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý FAQ - Chatbot - Admin</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">
  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
          <div>
            <p class="text-uppercase text-secondary small mb-1">Chatbot & Hỗ trợ</p>
            <h3 class="mb-0 fw-bold">Quản lý FAQ (Câu hỏi thường gặp)</h3>
          </div>
          <div>
            <button class="btn btn-success btn-sm shadow-none me-2" onclick="import100Faqs()" id="importBtn">
              <i class="bi bi-download"></i> Import 100 FAQs
            </button>
            <button class="btn btn-dark btn-sm shadow-none" data-bs-toggle="modal" data-bs-target="#faqModal">
              <i class="bi bi-plus-circle"></i> Thêm FAQ
            </button>
          </div>
        </div>

        <div id="faq-alert"></div>

        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="table-responsive" style="max-height:600px; overflow-y:auto;">
              <table class="table table-hover text-center align-middle">
                <thead class="table-dark">
                  <tr>
                    <th>#</th>
                    <th>Câu hỏi</th>
                    <th>Trả lời</th>
                    <th>Từ khóa</th>
                    <th>Danh mục</th>
                    <th>Độ ưu tiên</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                  </tr>
                </thead>
                <tbody id="faq-data"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Add/Edit FAQ -->
  <div class="modal fade" id="faqModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold"><i class="bi bi-question-circle me-2"></i> Thêm / Sửa FAQ</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="faq-form">
          <div class="modal-body">
            <input type="hidden" name="id" id="faq_id">
            <div class="row g-3">
              <div class="col-md-12">
                <label class="form-label fw-semibold">Câu hỏi <span class="text-danger">*</span></label>
                <input type="text" name="question" class="form-control shadow-none" required placeholder="VD: Làm sao để đặt phòng?">
              </div>
              <div class="col-md-12">
                <label class="form-label fw-semibold">Câu trả lời <span class="text-danger">*</span></label>
                <textarea name="answer" class="form-control shadow-none" rows="5" required placeholder="Nhập câu trả lời chi tiết..."></textarea>
                <small class="text-muted">Có thể sử dụng HTML cơ bản: &lt;a&gt;, &lt;strong&gt;, &lt;br&gt;</small>
              </div>
              <div class="col-md-12">
                <label class="form-label fw-semibold">Từ khóa</label>
                <input type="text" name="keywords" class="form-control shadow-none" placeholder="VD: đặt phòng, booking, book (phân cách bằng dấu phẩy)">
                <small class="text-muted">Các từ khóa giúp chatbot nhận diện câu hỏi (phân cách bằng dấu phẩy)</small>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Danh mục</label>
                <select name="category" class="form-select shadow-none">
                  <option value="general">Chung</option>
                  <option value="booking">Đặt phòng</option>
                  <option value="payment">Thanh toán</option>
                  <option value="room">Phòng</option>
                  <option value="facilities">Tiện ích</option>
                  <option value="destinations">Điểm đến</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-semibold">Độ ưu tiên</label>
                <input type="number" name="priority" class="form-control shadow-none" min="0" max="100" value="0">
                <small class="text-muted">Số càng cao càng ưu tiên</small>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-semibold">Trạng thái</label>
                <select name="active" class="form-select shadow-none">
                  <option value="1">Hiển thị</option>
                  <option value="0">Ẩn</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-primary">Lưu</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>
  <script src="scripts/faqs.js"></script>
</body>
</html>

