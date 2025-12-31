<?php
require('../inc/db_config.php');
require('../inc/essentials.php');
require('../inc/promos_helper.php');
adminLogin();
ensurePromosTable($con);
seedDefaultPromos($con);

// ========== GET LIST ==========
if(isset($_POST['get_promos'])){
  $res = mysqli_query($con,"SELECT * FROM `promos` ORDER BY `priority` DESC, `id` DESC");
  $html = '';
  $i = 1;

  $cat_map = [
    'hot' => 'Mã giảm giá hot',
    'bank' => 'Ngân hàng',
    'wallet' => 'Ví/QR',
    'destination' => 'Điểm đến hot'
  ];

  if($res && mysqli_num_rows($res)){
    while($row = mysqli_fetch_assoc($res)){
      $code = htmlspecialchars($row['code'], ENT_QUOTES, 'UTF-8');
      $title = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
      $label = htmlspecialchars($row['label'], ENT_QUOTES, 'UTF-8');

      $value_txt = ($row['discount_type']==='flat')
        ? number_format($row['discount_value'],0,'.','.') . ' đ'
        : $row['discount_value'] . '%';

      if(!empty($row['max_discount'])){
        $value_txt .= " • tối đa ".number_format($row['max_discount'],0,'.','.')." đ";
      }

      $min_txt = number_format($row['min_amount'],0,'.','.')." đ";
      $expire_txt = $row['expires_at'] ? date('d/m/Y', strtotime($row['expires_at'])) : 'Không giới hạn';
      $priority = (int)$row['priority'];
      $status_btn = $row['active']
        ? "<span class='badge bg-success'>Đang bật</span>"
        : "<span class='badge bg-secondary'>Tắt</span>";

      $cat_label = $cat_map[$row['category']] ?? ucfirst($row['category']);

      $html .= "<tr>
        <td>{$i}</td>
        <td class='fw-bold'>{$code}</td>
        <td class='text-start'>
          <div>{$title}</div>
          <small class='text-muted'>{$label}</small>
        </td>
        <td>{$cat_label}</td>
        <td>{$value_txt}</td>
        <td>{$min_txt}</td>
        <td>".(!empty($row['max_discount']) ? number_format($row['max_discount'],0,'.','.') . " đ" : 'Không giới hạn')."</td>
        <td>{$expire_txt}</td>
        <td>{$priority}</td>
        <td>{$status_btn}</td>
        <td>
          <button class='btn btn-sm btn-outline-primary mb-1' onclick='editPromo({$row['id']})'><i class=\"bi bi-pencil\"></i></button>
          <button class='btn btn-sm btn-outline-secondary mb-1' onclick='togglePromo({$row['id']}, ".($row['active']?0:1).")'><i class=\"bi ".($row['active']?'bi-slash-circle':'bi-check-circle')."\"></i></button>
          <button class='btn btn-sm btn-outline-danger' onclick='deletePromo({$row['id']})'><i class=\"bi bi-trash\"></i></button>
        </td>
      </tr>";
      $i++;
    }
  } else {
    $html = "<tr><td colspan='11' class='text-center text-muted'>Chưa có mã giảm giá</td></tr>";
  }

  echo $html;
  exit;
}

// ========== SAVE (ADD/EDIT) ==========
if(isset($_POST['save_promo'])){
  $frm = filteration($_POST);
  $id = isset($frm['id']) && $frm['id'] !== '' ? (int)$frm['id'] : 0;

  $label   = $frm['label'] ?? '';
  $title   = $frm['title'] ?? '';
  $desc    = $frm['description'] ?? '';
  $code    = strtoupper(trim($frm['code'] ?? ''));
  $category= $frm['category'] ?? 'hot';
  $dtype   = $frm['discount_type'] ?? 'percent';
  $dvalue  = isset($frm['discount_value']) ? (int)$frm['discount_value'] : 0;
  $min     = isset($frm['min_amount']) ? (int)$frm['min_amount'] : 0;
  $max     = ($frm['max_discount'] === '' || !isset($frm['max_discount'])) ? 0 : (int)$frm['max_discount'];
  $priority= isset($frm['priority']) ? (int)$frm['priority'] : 0;
  $active  = isset($frm['active']) ? 1 : 0;
  $expires = !empty($frm['expires_at']) ? $frm['expires_at'] : null;

  $allow_cat = ['hot','bank','wallet','destination'];
  $allow_type= ['percent','flat'];
  if(!in_array($category, $allow_cat)) $category = 'hot';
  if(!in_array($dtype, $allow_type)) $dtype = 'percent';

  if(empty($code) || empty($title)){
    echo 'missing';
    exit;
  }

  if($id){
    $q = "UPDATE `promos` SET `label`=?,`title`=?,`description`=?,`code`=?,`category`=?,`discount_type`=?,`discount_value`=?,`min_amount`=?,`max_discount`=?,`priority`=?,`active`=?,`expires_at`=? WHERE `id`=?";
    $res = update($q, [
      $label,$title,$desc,$code,$category,$dtype,
      $dvalue,$min,$max,$priority,$active,$expires,$id
    ], 'ssssssiiiiisi');
    echo ($res === false) ? 'error' : 'updated';
  } else {
    $q = "INSERT INTO `promos`(`label`,`title`,`description`,`code`,`category`,`discount_type`,`discount_value`,`min_amount`,`max_discount`,`priority`,`active`,`expires_at`)
          VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
    $res = insert($q, [
      $label,$title,$desc,$code,$category,$dtype,
      $dvalue,$min,$max,$priority,$active,$expires
    ], 'ssssssiiiiis');

    if(!$res && mysqli_errno($con)==1062){
      echo 'duplicate';
    } else {
      echo $res ? 'success' : 'error';
    }
  }
  exit;
}

// ========== GET SINGLE ==========
if(isset($_POST['get_single']) && isset($_POST['id'])){
  $id = (int)$_POST['id'];
  $res = select("SELECT * FROM `promos` WHERE `id`=?", [$id], 'i');
  if($res && mysqli_num_rows($res)){
    echo json_encode(mysqli_fetch_assoc($res));
  } else {
    echo json_encode([]);
  }
  exit;
}

// ========== TOGGLE ==========
if(isset($_POST['toggle_promo']) && isset($_POST['id'])){
  $id = (int)$_POST['id'];
  $status = isset($_POST['status']) ? (int)$_POST['status'] : 0;
  update("UPDATE `promos` SET `active`=? WHERE `id`=?", [$status, $id], 'ii');
  echo 'done';
  exit;
}

// ========== DELETE ==========
if(isset($_POST['delete_promo']) && isset($_POST['id'])){
  $id = (int)$_POST['id'];
  delete("DELETE FROM `promos` WHERE `id`=?", [$id], 'i');
  echo 'deleted';
  exit;
}

echo 'invalid';
?>
