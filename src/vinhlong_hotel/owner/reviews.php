<?php
require('inc/essentials.php');
require('../admin/inc/db_config.php');
ownerLogin();

$owner_id = getOwnerId();

// Kiểm tra các cột có tồn tại không
$cols_check = mysqli_query($con, "SHOW COLUMNS FROM `rating_review`");
$existing_cols = [];
while($col = mysqli_fetch_assoc($cols_check)){
  $existing_cols[] = $col['Field'];
}

$has_images_col = in_array('images', $existing_cols);
$has_admin_reply_col = in_array('admin_reply', $existing_cols);
$has_admin_reply_date_col = in_array('admin_reply_date', $existing_cols);

// Xây dựng SELECT với các cột có sẵn
$select_cols = [
  'rv.sr_no', 
  'rv.booking_id', 
  'rv.room_id', 
  'rv.user_id', 
  'rv.rating', 
  'rv.review', 
  'rv.seen', 
  'rv.datentime'
];

if($has_images_col) $select_cols[] = 'rv.images';
if($has_admin_reply_col) $select_cols[] = 'rv.admin_reply';
if($has_admin_reply_date_col) $select_cols[] = 'rv.admin_reply_date';

$select_cols[] = 'r.name AS room_name';
$select_cols[] = 'r.id AS room_id';
$select_cols[] = 'uc.name AS user_name';
$select_cols[] = 'uc.email AS user_email';
$select_cols[] = 'uc.phonenum AS user_phone';
$select_cols[] = 'bo.booking_id AS booking_order_id';
$select_cols[] = 'bo.check_in AS checkin_date';
$select_cols[] = 'bo.check_out AS checkout_date';

$q = "SELECT ".implode(', ', $select_cols)."
      FROM rating_review rv
      INNER JOIN rooms r ON rv.room_id = r.id
      LEFT JOIN user_cred uc ON rv.user_id = uc.id
      LEFT JOIN booking_order bo ON rv.booking_id = bo.booking_id
      WHERE r.owner_id=?
      ORDER BY rv.datentime DESC";

$reviews_query = select($q, [$owner_id], 'i');

// Thống kê đánh giá
$avg_rating = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews
    FROM rating_review rv
    INNER JOIN rooms r ON rv.room_id = r.id
    WHERE r.owner_id=$owner_id
"))['avg_rating'] ?? 0;

$total_reviews = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT COUNT(*) AS c
    FROM rating_review rv
    INNER JOIN rooms r ON rv.room_id = r.id
    WHERE r.owner_id=$owner_id
"))['c'] ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đánh giá - Chủ khách sạn</title>
  <?php require('../admin/inc/links.php'); ?>
  <style>
    /* Page Header */
    .page-header {
      background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
      color: white;
      border-radius: 16px;
      padding: 2rem 2.5rem;
      margin-bottom: 2rem;
      box-shadow: 0 10px 26px rgba(0, 0, 0, 0.05);
      position: relative;
      overflow: hidden;
    }
    
    .page-header h4 {
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      text-shadow: 0 2px 4px rgba(0,0,0,0.1);
      color: white;
    }
    
    .page-header p {
      font-size: 1rem;
      opacity: 0.95;
      margin-bottom: 0;
      color: rgba(255, 255, 255, 0.9);
    }
    
    /* Cards */
    .card {
      border: 1px solid #e5e7eb;
      border-radius: 15px;
      box-shadow: 0 10px 26px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
    }
    
    .card:hover {
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
      transform: translateY(-2px);
    }
    
    .card-title {
      color: #0f172a;
      font-weight: 600;
    }
    
    /* Stats Cards */
    .card-body h3 {
      color: #0f172a;
      font-weight: 700;
    }
    
    /* Review Cards */
    .card.border {
      border: 1px solid #e5e7eb !important;
    }
    
    .card-body h6 {
      color: #0f172a;
    }
    
    /* Modal */
    .modal-content {
      border-radius: 16px;
      border: 1px solid #e5e7eb;
      box-shadow: 0 10px 26px rgba(0, 0, 0, 0.05);
    }
    
    .modal-header {
      background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
      color: white;
      border-bottom: none;
      border-radius: 16px 16px 0 0;
    }
    
    .modal-header .btn-close {
      filter: invert(1);
    }
    
    .modal-header h5 {
      color: white;
    }
    
    /* Form Controls */
    .form-control {
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      color: #0f172a;
    }
    
    .form-control:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    /* Buttons */
    .btn-primary {
      background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
      border: none;
      transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }
    
    .btn-outline-primary {
      border-color: #0d6efd;
      color: #0d6efd;
    }
    
    .btn-outline-primary:hover {
      background: linear-gradient(135deg, #0f172a 0%, #0d6efd 60%, #0ea5e9 100%);
      border-color: #0d6efd;
      color: white;
    }
    
    /* Text Colors */
    .text-muted {
      color: #6c757d !important;
    }
    
    /* Empty State */
    .text-center.py-5 {
      color: #0f172a;
    }
    
    .text-center.py-5 i {
      color: #0f172a;
      opacity: 0.3;
    }
  </style>
</head>
<body class="bg-light">
  <div class="container-fluid p-0">
    <div class="row g-0">
      <?php require('inc/header.php'); ?>

      <div class="col-lg-10 p-4" id="main-content">
        
        <!-- Page Header -->
        <div class="page-header mb-4">
          <div>
            <h4 class="mb-2">
              <i class="bi bi-star-half me-2"></i>Đánh giá
            </h4>
            <p class="mb-0 opacity-90">Xem và quản lý đánh giá từ khách hàng</p>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
          <div class="col-md-6">
            <div class="card">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <p class="text-muted small mb-1">Đánh giá trung bình</p>
                    <h3 class="mb-0">
                      <?php echo number_format($avg_rating, 1); ?>
                      <small class="text-muted fs-6">/ 5.0</small>
                    </h3>
                  </div>
                  <div class="text-warning fs-1">
                    <i class="bi bi-star-fill"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <p class="text-muted small mb-1">Tổng số đánh giá</p>
                    <h3 class="mb-0"><?php echo $total_reviews; ?></h3>
                  </div>
                  <div class="text-info fs-1">
                    <i class="bi bi-chat-left-text"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Reviews List -->
        <div class="card">
          <div class="card-body">
            <h5 class="card-title mb-4">Tất cả đánh giá</h5>
            
            <?php if ($reviews_query && mysqli_num_rows($reviews_query) > 0): ?>
              <div class="row g-4">
                <?php while ($review = mysqli_fetch_assoc($reviews_query)): 
                  $rating = (int)$review['rating'];
                  $review_id = (int)$review['sr_no'];
                  
                  // Xử lý ảnh
                  $images = [];
                  if($has_images_col && !empty($review['images'])){
                    $images_data = json_decode($review['images'], true);
                    if(json_last_error() === JSON_ERROR_NONE && is_array($images_data)){
                      $images = $images_data;
                    }
                  }
                  
                  // Phản hồi
                  $adminReply = ($has_admin_reply_col && !empty($review['admin_reply'])) ? htmlspecialchars($review['admin_reply']) : '';
                  $replyDate = ($has_admin_reply_date_col && !empty($review['admin_reply_date'])) ? date('d/m/Y H:i', strtotime($review['admin_reply_date'])) : '';
                  
                  $user_name = htmlspecialchars($review['user_name'] ?? 'Khách', ENT_QUOTES, 'UTF-8');
                  $user_email = htmlspecialchars($review['user_email'] ?? '', ENT_QUOTES, 'UTF-8');
                  $user_phone = htmlspecialchars($review['user_phone'] ?? '', ENT_QUOTES, 'UTF-8');
                  $room_name = htmlspecialchars($review['room_name'], ENT_QUOTES, 'UTF-8');
                  $review_text = htmlspecialchars($review['review'] ?? '', ENT_QUOTES, 'UTF-8');
                  $review_date = date('d/m/Y H:i', strtotime($review['datentime']));
                  $checkin = !empty($review['checkin_date']) ? date('d/m/Y', strtotime($review['checkin_date'])) : '';
                  $checkout = !empty($review['checkout_date']) ? date('d/m/Y', strtotime($review['checkout_date'])) : '';
                ?>
                  <div class="col-12">
                    <div class="card border shadow-sm">
                      <div class="card-body">
                        <div class="row">
                          <!-- Thông tin khách hàng -->
                          <div class="col-md-4 border-end">
                            <h6 class="fw-bold mb-3">
                              <i class="bi bi-person-circle me-2"></i>Thông tin khách hàng
                            </h6>
                            <div class="mb-2">
                              <strong>Tên:</strong> <?php echo $user_name; ?>
                            </div>
                            <?php if($user_email): ?>
                            <div class="mb-2">
                              <strong>Email:</strong> 
                              <a href="mailto:<?php echo $user_email; ?>" class="text-decoration-none">
                                <?php echo $user_email; ?>
                              </a>
                            </div>
                            <?php endif; ?>
                            <?php if($user_phone): ?>
                            <div class="mb-2">
                              <strong>Điện thoại:</strong> 
                              <a href="tel:<?php echo $user_phone; ?>" class="text-decoration-none">
                                <?php echo $user_phone; ?>
                              </a>
                            </div>
                            <?php endif; ?>
                            <?php if($checkin && $checkout): ?>
                            <div class="mt-3 p-2 bg-light rounded">
                              <small class="text-muted d-block">Check-in: <?php echo $checkin; ?></small>
                              <small class="text-muted d-block">Check-out: <?php echo $checkout; ?></small>
                            </div>
                            <?php endif; ?>
                          </div>
                          
                          <!-- Đánh giá -->
                          <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                              <div>
                                <h6 class="fw-bold mb-1">
                                  <i class="bi bi-door-open me-2"></i><?php echo $room_name; ?>
                                </h6>
                                <div class="mb-2">
                                  <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?php echo $i <= $rating ? '-fill' : ''; ?> text-warning"></i>
                                  <?php endfor; ?>
                                  <span class="ms-2 fw-bold"><?php echo $rating; ?>/5</span>
                                </div>
                              </div>
                              <small class="text-muted"><?php echo $review_date; ?></small>
                            </div>
                            
                            <div class="mb-3">
                              <p class="mb-0"><?php echo nl2br($review_text); ?></p>
                            </div>
                            
                            <!-- Ảnh đánh giá -->
                            <?php if(!empty($images)): ?>
                            <div class="mb-3">
                              <div class="d-flex gap-2 flex-wrap">
                                <?php foreach($images as $img): 
                                  $img_path = trim($img);
                                  $img_path = preg_replace('#^\.\./#', '', $img_path);
                                  $img_path = ltrim($img_path, '/');
                                  $full_path = '../'.$img_path;
                                ?>
                                  <img src="<?php echo $full_path; ?>" 
                                       class="rounded" 
                                       style="width:80px;height:80px;object-fit:cover;cursor:pointer;border:1px solid #dee2e6;"
                                       onclick="window.open('<?php echo $full_path; ?>','_blank')"
                                       onerror="this.style.display='none'"
                                       title="Click để xem ảnh lớn">
                                <?php endforeach; ?>
                              </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Phản hồi của chủ khách sạn -->
                            <?php if($adminReply): ?>
                            <div class="alert alert-info mb-3">
                              <div class="d-flex justify-content-between align-items-start mb-2">
                                <strong>
                                  <i class="bi bi-reply-fill me-1"></i>Phản hồi của bạn:
                                </strong>
                                <small class="text-muted"><?php echo $replyDate; ?></small>
                              </div>
                              <p class="mb-0"><?php echo nl2br($adminReply); ?></p>
                              <button type="button" 
                                      class="btn btn-sm btn-outline-primary mt-2" 
                                      onclick="openReplyModal(<?php echo $review_id; ?>, '<?php echo addslashes($adminReply); ?>')">
                                <i class="bi bi-pencil me-1"></i>Sửa phản hồi
                              </button>
                            </div>
                            <?php else: ?>
                            <div class="text-end">
                              <button type="button" 
                                      class="btn btn-primary btn-sm" 
                                      onclick="openReplyModal(<?php echo $review_id; ?>, '')">
                                <i class="bi bi-reply me-1"></i>Phản hồi khách hàng
                              </button>
                            </div>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endwhile; ?>
              </div>
            <?php else: ?>
              <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="text-muted mt-3">Chưa có đánh giá nào</p>
              </div>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Modal Phản hồi -->
  <div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Phản hồi đánh giá</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="replyForm">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label fw-bold">Nội dung phản hồi:</label>
              <textarea name="reply_text" 
                        id="reply_text" 
                        class="form-control shadow-none" 
                        rows="4" 
                        placeholder="Nhập phản hồi của bạn cho khách hàng..."></textarea>
            </div>
            <div class="alert alert-info mb-0">
              <i class="bi bi-info-circle me-2"></i>
              <small>Phản hồi của bạn sẽ được hiển thị công khai dưới đánh giá của khách hàng.</small>
            </div>
            <input type="hidden" name="review_id" id="reply_review_id">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-send me-1"></i>Gửi phản hồi
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php require('../admin/inc/scripts.php'); ?>
  
  <script>
  // Hàm hiển thị toast notification
  function showToast(message, type) {
    let toast = document.createElement('div');
    toast.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show position-fixed';
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    document.body.appendChild(toast);
    setTimeout(() => {
      toast.remove();
    }, 3000);
  }

  function openReplyModal(reviewId, currentReply) {
    document.getElementById('reply_review_id').value = reviewId;
    document.getElementById('reply_text').value = currentReply;
    new bootstrap.Modal(document.getElementById('replyModal')).show();
  }

  document.getElementById('replyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let formData = new FormData(this);
    formData.append('action', 'reply_review');
    
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/reviews.php', true);
    xhr.onload = function() {
      if(this.status === 200) {
        try {
          let res = JSON.parse(this.responseText);
          if(res.status === 'success') {
            showToast(res.msg || 'Đã gửi phản hồi thành công!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('replyModal')).hide();
            setTimeout(() => location.reload(), 1000);
          } else {
            showToast(res.msg || 'Có lỗi xảy ra!', 'error');
          }
        } catch(e) {
          console.error('Parse error:', e);
          showToast('Có lỗi xảy ra khi xử lý phản hồi!', 'error');
        }
      } else {
        showToast('Có lỗi xảy ra!', 'error');
      }
    };
    xhr.send(formData);
  });
  </script>
</body>
</html>
