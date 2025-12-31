<?php
// File essentials riêng cho Owner
// Kế thừa từ admin/inc/essentials.php nhưng có thêm check ownerLogin

require_once __DIR__ . '/../../admin/inc/essentials.php';

// ======================
// Check owner login
// ======================
function ownerLogin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!(isset($_SESSION['ownerLogin']) && $_SESSION['ownerLogin'] == true)) {
        echo "<script>window.location.href='index.php'</script>";
        exit;
    }
    
    // Kiểm tra status của owner - nếu bị khóa thì tự động đăng xuất
    $owner_id = isset($_SESSION['ownerId']) ? (int)$_SESSION['ownerId'] : 0;
    if ($owner_id > 0) {
        $con = $GLOBALS['con'] ?? null;
        if ($con) {
            $status_check = select("SELECT status FROM hotel_owners WHERE id=?", [$owner_id], 'i');
            if ($status_check && mysqli_num_rows($status_check) > 0) {
                $owner_data = mysqli_fetch_assoc($status_check);
                // Nếu status != 1 (không phải đã kích hoạt), đăng xuất
                if ($owner_data['status'] != 1) {
                    // Xóa session
                    session_unset();
                    session_destroy();
                    // Redirect về trang login với thông báo
                    echo "<script>
                        alert('Tài khoản của bạn đã bị khóa hoặc chưa được duyệt. Vui lòng liên hệ quản trị viên.');
                        window.location.href='index.php';
                    </script>";
                    exit;
                }
            } else {
                // Owner không tồn tại trong database, đăng xuất
                session_unset();
                session_destroy();
                echo "<script>window.location.href='index.php'</script>";
                exit;
            }
        }
    }
}

// ======================
// Get current owner ID
// ======================
function getOwnerId() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['ownerId']) ? (int)$_SESSION['ownerId'] : 0;
}

// ======================
// Check if room belongs to owner
// ======================
function isOwnerRoom($room_id, $owner_id) {
    $con = $GLOBALS['con'] ?? null;
    if (!$con || !$room_id || !$owner_id) return false;
    
    $res = select("SELECT owner_id FROM rooms WHERE id=? AND owner_id=?", [$room_id, $owner_id], "ii");
    return $res && mysqli_num_rows($res) > 0;
}

