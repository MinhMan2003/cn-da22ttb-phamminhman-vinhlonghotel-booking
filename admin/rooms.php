<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trang quản trị - Danh sách phòng</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
          <div>
            <p class="text-uppercase text-secondary small mb-1">Quản lý phòng</p>
            <h3 class="mb-0 fw-bold">Danh sách phòng</h3>
          </div>
        <div class="d-flex align-items-center gap-2">
          <button type="button" class="btn btn-outline-dark btn-sm shadow-none" onclick="presetKeyword('Vĩnh Long')">
            Lọc Vĩnh Long
          </button>
          <button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#add-room">
            <i class="bi bi-plus-square"></i> Thêm phòng
          </button>
        </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
              <div class="flex-grow-1">
                <input type="text" id="filter_keyword" class="form-control shadow-none" placeholder="Tìm theo tên/phần mô tả phòng..." oninput="debounceFetchRooms()">
              </div>
              <select id="filter_owner" class="form-select shadow-none w-auto" onchange="get_all_rooms()">
                <option value="all">Tất cả chủ KS</option>
                <option value="">Chỉ phòng Admin</option>
                <?php
                // Load danh sách owners
                $owners_q = mysqli_query($con, "SELECT id, name, hotel_name FROM hotel_owners WHERE status=1 ORDER BY name");
                while($owner = mysqli_fetch_assoc($owners_q)){
                  $label = htmlspecialchars($owner['hotel_name'] ?: $owner['name'], ENT_QUOTES, 'UTF-8');
                  echo "<option value='{$owner['id']}'>$label</option>";
                }
                ?>
              </select>
              <select id="filter_status" class="form-select shadow-none w-auto" onchange="get_all_rooms()">
                <option value="">Tất cả</option>
                <option value="active">Đang hoạt động</option>
                <option value="inactive">Ngừng hoạt động</option>
                <option value="soldout">Hết phòng</option>
              </select>
              <button class="btn btn-outline-secondary shadow-none" type="button" onclick="clearFilters()">Làm mới</button>
            </div>
            <div class="table-responsive-lg" style="height: 450px; overflow-y: scroll;">
              <table class="table table-hover border text-center">
                <thead>
                  <tr class="bg-dark text-light">
                    <th scope="col">#</th>
                    <th scope="col">Tên phòng</th>
                    <th scope="col">Vị trí</th>
                    <th scope="col">Diện tích</th>
                    <th scope="col">Số khách</th>
                    <th scope="col">Giá</th>
                    <th scope="col">Giảm (%)</th>
                    <th scope="col">Số lượng</th>
                    <th scope="col">Trạng thái</th>
                    <th scope="col">Hành động</th>
                  </tr>
                </thead>
                <tbody id="room-data"></tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  
  <!-- Add room modal -->
  <div class="modal fade" id="add-room" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form id="add_room_form" autocomplete="off">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Thêm phòng</h5>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Tên phòng</label>
                <input type="text" name="name" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Vị trí / Khu vực</label>
                <input type="text" name="location" class="form-control shadow-none" placeholder="VD: Long Hồ, TP. Vĩnh Long" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Diện tích (m²)</label>
                <input type="number" min="1" name="area" class="form-control shadow-none" required>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Giá (VND)</label>
                <input type="number" min="1" name="price" class="form-control shadow-none" required>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Giảm giá (%)</label>
                <input type="number" min="0" max="100" name="discount" class="form-control shadow-none" value="0">
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Số lượng</label>
                <input type="number" min="1" name="quantity" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Người lớn (tối đa)</label>
                <input type="number" min="1" name="adult" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Trẻ em (tối đa)</label>
                <input type="number" min="1" name="children" class="form-control shadow-none" required>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label fw-bold">Không gian</label>
                <div class="row">
                  <?php 
                    $res = selectAll('features');
                    while($opt = mysqli_fetch_assoc($res)){
                      echo"
                        <div class='col-md-3 mb-1'>
                          <label>
                            <input type='checkbox' name='features' value='$opt[id]' class='form-check-input shadow-none'>
                            $opt[name]
                          </label>
                        </div>
                      ";
                    }
                  ?>
                </div>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label fw-bold">Tiện ích</label>
                <div class="row">
                  <?php 
                    $res = selectAll('facilities');
                    while($opt = mysqli_fetch_assoc($res)){
                      echo"
                        <div class='col-md-3 mb-1'>
                          <label>
                            <input type='checkbox' name='facilities' value='$opt[id]' class='form-check-input shadow-none'>
                            $opt[name]
                          </label>
                        </div>
                      ";
                    }
                  ?>
                </div>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label fw-bold">Mô tả</label>
                <textarea name="desc" rows="4" class="form-control shadow-none" required></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="reset" class="btn text-secondary shadow-none" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn custom-bg text-white shadow-none">Tiếp tục</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit room modal -->
  <div class="modal fade" id="edit-room" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form id="edit_room_form" autocomplete="off">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Cập nhật phòng</h5>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Tên phòng</label>
                <input type="text" name="name" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Vị trí / Khu vực</label>
                <input type="text" name="location" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Diện tích (m²)</label>
                <input type="number" min="1" name="area" class="form-control shadow-none" required>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Giá phòng (VND)</label>
                <input type="number" min="1" name="price" class="form-control shadow-none" required>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Giảm giá (%)</label>
                <input type="number" min="0" max="100" name="discount" class="form-control shadow-none" value="0">
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Số lượng</label>
                <input type="number" min="1" name="quantity" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Phòng còn lại</label>
                <div class="input-group">
                    <button type="button" class="btn btn-outline-secondary" onclick="changeRemain(-1)">-</button>
                    <input type="number" min="0" name="remaining" class="form-control shadow-none text-center" required>
                    <button type="button" class="btn btn-outline-secondary" onclick="changeRemain(1)">+</button>
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Người lớn (tối đa)</label>
                <input type="number" min="1" name="adult" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Trẻ em (tối đa)</label>
                <input type="number" min="1" name="children" class="form-control shadow-none" required>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label fw-bold">Không gian</label>
                <div class="row">
                  <?php 
                    $res = selectAll('features');
                    while($opt = mysqli_fetch_assoc($res)){
                      echo"
                        <div class='col-md-3 mb-1'>
                          <label>
                            <input type='checkbox' name='features' value='$opt[id]' class='form-check-input shadow-none'>
                            $opt[name]
                          </label>
                        </div>
                      ";
                    }
                  ?>
                </div>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label fw-bold">Tiện ích</label>
                <div class="row">
                  <?php 
                    $res = selectAll('facilities');
                    while($opt = mysqli_fetch_assoc($res)){
                      echo"
                        <div class='col-md-3 mb-1'>
                          <label>
                            <input type='checkbox' name='facilities' value='$opt[id]' class='form-check-input shadow-none'>
                            $opt[name]
                          </label>
                        </div>
                      ";
                    }
                  ?>
                </div>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label fw-bold">Mô tả</label>
                <textarea name="desc" rows="4" class="form-control shadow-none" required></textarea>
              </div>
              <input type="hidden" name="room_id">
            </div>
          </div>
          <div class="modal-footer">
            <button type="reset" class="btn text-secondary shadow-none" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn custom-bg text-white shadow-none">Lưu</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Manage room images modal -->
  <div class="modal fade" id="room-images" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Room Name</h5>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="image-alert"></div>
          <div class="border-bottom border-3 pb-3 mb-3">
            <form id="add_image_form">
              <label class="form-label fw-bold">Thêm hình ảnh (có thể chọn nhiều ảnh cùng lúc)</label>
              <input type="file" name="image[]" accept=".jpg, .png, .webp, .jpeg" class="form-control shadow-none mb-3" multiple required>
              <small class="text-muted d-block mb-3">Bạn có thể chọn nhiều ảnh cùng lúc bằng cách giữ phím Ctrl (Windows) hoặc Cmd (Mac) khi click chọn file</small>
              <button type="submit" class="btn custom-bg text-white shadow-none">Thêm tất cả ảnh</button>
              <input type="hidden" name="room_id">
            </form>
          </div>
          <div class="table-responsive-lg" style="height: 350px; overflow-y: scroll;">
            <table class="table table-hover border text-center">
              <thead>
                <tr class="bg-dark text-light sticky-top">
                  <th scope="col" width="60%">Hình phòng</th>
                  <th scope="col">Đặt làm ảnh đại diện</th>
                  <th scope="col">Xóa</th>
                </tr>
              </thead>
              <tbody id="room-image-data"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
<style>
/* =============================
   GLOBAL DARK MODE
   ============================= */
body.bg-light{
  background:#0d1117 !important;
  color:#e6e6e6 !important;
}
#main-content{
  background:#0d1117 !important;
}

/* =============================
   NAVBAR & SIDEBAR
   ============================= */
.navbar{
  background:#0f1622 !important;
  border-bottom:1px solid rgba(255,255,255,0.08);
}
.navbar a, .navbar-brand{
  color:#e6e6e6 !important;
}
.navbar a:hover{
  color:#58a6ff !important;
}

#dashboard-menu{
  background:#0f1622 !important;
  border-right:1px solid rgba(255,255,255,0.08);
}
#dashboard-menu a{
  color:#cbd5e1 !important;
}
#dashboard-menu a:hover{
  background:#152033 !important;
  color:#58a6ff !important;
}

/* =============================
   PAGE TITLE
   ============================= */
h3{
  color:#58a6ff !important;
  font-weight:700;
  text-shadow:0 0 20px rgba(88,166,255,0.6);
  letter-spacing:1px;
}

/* =============================
   CARD
   ============================= */
.card{
  background:linear-gradient(145deg,#0a0e14,#141b29) !important;
  border-radius:20px !important;
  border:1px solid rgba(255,255,255,0.08) !important;
  color:#e6e6e6 !important;
}

/* =============================
   SEARCH INPUT
   ============================= */
.form-control{
  background:#0f1622 !important;
  color:#e6e6e6 !important;
  border:1px solid rgba(255,255,255,0.12) !important;
  border-radius:12px !important;
}
.form-control:focus{
  border-color:#58a6ff !important;
  box-shadow:0 0 8px rgba(88,166,255,0.35) !important;
}

/* =============================
   TABLE
   ============================= */
.table{
  color:#e6e6e6 !important;
  border-color:rgba(255,255,255,0.08) !important;
}

thead tr{
  background:#111927 !important;
}
thead th{
  color:#58a6ff !important;
  font-weight:600;
}

/* Hover không làm mất chữ */
tbody tr:hover{
  background:#1b2538 !important;
}
tbody tr:hover td{
  color:#fff !important;
}

/* =============================
   SCROLL AREA
   ============================= */
.table-responsive-lg{
  background:#0f1622;
  border-radius:12px;
  border:1px solid rgba(255,255,255,0.08);
}
.table-responsive-lg::-webkit-scrollbar{
  width:7px;
}
.table-responsive-lg::-webkit-scrollbar-thumb{
  background:#2d3a4f;
  border-radius:10px;
}

/* =============================
   BUTTONS
   ============================= */
.btn-dark{
  background:#1e2737 !important;
  color:#e6e6e6 !important;
  border:none !important;
}
.btn-dark:hover{
  background:#2a3447 !important;
}

.btn-outline-secondary{
  border:1px solid rgba(255,255,255,0.4) !important;
  color:#c5c5c5 !important;
}
.btn-outline-secondary:hover{
  background:#2b3547 !important;
}

.custom-bg{
  background:#58a6ff !important;
  color:#0d1117 !important;
}
.custom-bg:hover{
  background:#7bb8ff !important;
}

/* =============================
   MODAL
   ============================= */
.modal-content{
  background:#141b29 !important;
  border-radius:20px !important;
  border:1px solid rgba(255,255,255,0.08) !important;
  color:#e6e6e6 !important;
}
.modal-header,
.modal-footer{
  border-color:rgba(255,255,255,0.08) !important;
}

.btn-close{
  filter:invert(1);
}

/* Checkbox */
.form-check-input{
  background:#0f1622 !important;
  border:1px solid #58a6ff !important;
}
.form-check-input:checked{
  background:#58a6ff !important;
}
</style>


  <?php require('inc/scripts.php'); ?>

  <script src="scripts/rooms.js"></script>
  
  <script>
  // Đảm bảo hàm approve_room có thể được gọi
  document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, checking approve_room function...');
    console.log('typeof approve_room:', typeof approve_room);
    
    // Nếu hàm chưa được định nghĩa, định nghĩa lại
    if(typeof window.approve_room === 'undefined') {
      console.warn('approve_room not found, defining it...');
      window.approve_room = function(id) {
        console.log('approve_room called with id:', id);
        
        if(!confirm('Bạn có chắc muốn duyệt phòng này? Phòng sẽ hiển thị trên trang chủ sau khi được duyệt.')){
          return;
        }

        let xhr = new XMLHttpRequest();
        xhr.open("POST","ajax/rooms.php",true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function(){
          let response = this.responseText.trim();
          console.log('Approve response:', response);
          
          try {
            let jsonResponse = JSON.parse(response);
            if(jsonResponse.error){
              alert('error', jsonResponse.error);
              return;
            }
          } catch(e) {}
          
          if(response === '1' || response == 1){
            alert('success','Duyệt phòng thành công! Phòng đã hiển thị trên trang chủ.');
            get_all_rooms();
          }
          else{
            alert('error','Không thể duyệt phòng! Lỗi: ' + response);
          }
        }

        xhr.onerror = function(){
          alert('error','Lỗi kết nối! Vui lòng thử lại.');
        }

        xhr.send('approve_room='+id);
      };
      console.log('approve_room function defined');
    }
  });
  </script>

</body>
</html>
