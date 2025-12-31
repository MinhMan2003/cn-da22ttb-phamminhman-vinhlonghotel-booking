<?php
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

/* ============ 1. GENERAL ============ */
if (isset($_POST['get_general'])) {
    $res = select("SELECT * FROM `settings` WHERE `sr_no`=?", [1], "i");
    echo json_encode(mysqli_fetch_assoc($res));
}

if (isset($_POST['upd_general'])) {
    
    // Kiểm tra và thêm cột site_logo nếu chưa có
    $check_col = mysqli_query($con, "SHOW COLUMNS FROM `settings` LIKE 'site_logo'");
    if(!$check_col || mysqli_num_rows($check_col) == 0) {
        mysqli_query($con, "ALTER TABLE `settings` ADD COLUMN `site_logo` VARCHAR(255) DEFAULT NULL AFTER `site_about`");
    }
    
    // Lấy logo hiện tại để giữ lại hoặc xóa
    $current_logo = null;
    $old_logo_res = select("SELECT site_logo FROM `settings` WHERE sr_no=?", [1], "i");
    if($old_logo_res && mysqli_num_rows($old_logo_res) > 0) {
        $current_logo = mysqli_fetch_assoc($old_logo_res)['site_logo'];
    }
    $logo_value = $current_logo;
    
    // Xử lý upload logo nếu có file mới
    if(isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] == 0) {
        // Xóa logo cũ nếu có
        if($current_logo && !empty($current_logo)) {
            deleteImage($current_logo, ABOUT_FOLDER);
        }
        
        // Upload logo mới
        $new_logo = uploadImage($_FILES['site_logo'], ABOUT_FOLDER);
        
        if(!in_array($new_logo, ['inv_img','inv_size','upd_failed'])) {
            $logo_value = $new_logo;
        } else {
            // Upload thất bại, giữ logo cũ
            $logo_value = $current_logo;
        }
    }
    
    // Lấy dữ liệu từ POST
    $frm = filteration($_POST);
    $site_title = isset($frm['site_title']) ? trim($frm['site_title']) : '';
    $site_about = isset($frm['site_about']) ? trim($frm['site_about']) : '';
    
    // Lấy dữ liệu hiện tại để so sánh
    $current_data = select("SELECT site_title, site_about, site_logo FROM `settings` WHERE sr_no=?", [1], "i");
    $current_row = null;
    if($current_data && mysqli_num_rows($current_data) > 0) {
        $current_row = mysqli_fetch_assoc($current_data);
    }
    
    // Kiểm tra xem có thay đổi thực sự không
    $has_changes = false;
    if($current_row) {
        $title_changed = ($current_row['site_title'] !== $site_title);
        $about_changed = ($current_row['site_about'] !== $site_about);
        $logo_changed = ($current_row['site_logo'] !== $logo_value);
        
        $has_changes = $title_changed || $about_changed || $logo_changed;
    } else {
        // Không có dữ liệu cũ, luôn cập nhật
        $has_changes = true;
    }
    
    // Cập nhật database
    if($logo_value !== null && !empty($logo_value)) {
        $sql = "UPDATE `settings` SET site_title=?, site_about=?, site_logo=? WHERE sr_no=?";
        $params = [$site_title, $site_about, $logo_value, 1];
        $types = "sssi";
    } else {
        $sql = "UPDATE `settings` SET site_title=?, site_about=? WHERE sr_no=?";
        $params = [$site_title, $site_about, 1];
        $types = "ssi";
    }
    
    $result = update($sql, $params, $types);
    
    // Nếu không có thay đổi thực sự, trả về -1 để JavaScript biết
    if(!$has_changes && $result == 0) {
        $result = -1;
    }
    
    echo $result;
}

/* ============ 2. SHUTDOWN ============ */
if (isset($_POST['upd_shutdown'])) {
    echo update(
        "UPDATE `settings` SET shutdown=? WHERE sr_no=?",
        [$_POST['upd_shutdown'], 1],
        "ii"
    );
}

/* ============ 3. CONTACTS ============ */
if (isset($_POST['get_contacts'])) {
    $q = "SELECT * FROM `contact_details` WHERE sr_no=1";
    echo json_encode(mysqli_fetch_assoc(mysqli_query($con, $q)));
}

if (isset($_POST['upd_contacts'])) {
    $frm = filteration($_POST);

    $q = "UPDATE contact_details SET 
            address=?, gmap=?, pn1=?, email=?, fb=?, insta=?, tw=?, iframe=?
          WHERE sr_no=?";

    $values = [
        $frm['address'], $frm['gmap'], $frm['pn1'],
        $frm['email'], $frm['fb'], $frm['insta'],
        $frm['tw'], $frm['iframe'], 1
    ];

    echo update($q, $values, "ssssssssi");
}

/* ============ 4. TEAM ADD ============ */
if (isset($_POST['add_member'])) {

    $name = filteration($_POST)['name'];

    $img = uploadImage($_FILES['picture'], TEAM_FOLDER);
    if (in_array($img, ['inv_img','inv_size','upd_failed'])) {
        echo $img;
        exit;
    }

    echo insert(
        "INSERT INTO team_details (name,picture) VALUES (?,?)",
        [$name, $img],
        "ss"
    );
}

/* ============ 5. TEAM GET ============ */
if (isset($_POST['get_members'])) {

    $res = mysqli_query($con, "SELECT * FROM team_details ORDER BY sr_no DESC");
    $html = "";

    while ($row = mysqli_fetch_assoc($res)) {

        $html .= "
        <div class='col-md-3 mb-3'>
            <div class='card shadow'>
                <img src='../images/team/$row[picture]' class='card-img-top'>
                <div class='card-body p-2'>
                    <h6 class='card-title'>$row[name]</h6>
                    <button onclick='rem_member($row[sr_no])' 
                            class='btn btn-danger btn-sm w-100'>Xóa</button>
                </div>
            </div>
        </div>";
    }

    echo $html;
}

/* ============ 6. TEAM REMOVE ============ */
if (isset($_POST['rem_member'])) {

    $id = filteration($_POST)['rem_member'];

    $res = select("SELECT picture FROM team_details WHERE sr_no=?", [$id], "i");
    $img = mysqli_fetch_assoc($res)['picture'];

    deleteImage($img, TEAM_FOLDER);

    echo delete("DELETE FROM team_details WHERE sr_no=?", [$id], "i");
}

?>
