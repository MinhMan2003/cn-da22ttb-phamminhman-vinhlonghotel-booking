let add_room_form = document.getElementById('add_room_form');
    
add_room_form.addEventListener('submit',function(e){
  e.preventDefault();
  add_room();
});

/* ======================= THÊM PHÒNG ======================= */
function add_room()
{
  let data = new FormData();
  data.append('add_room','');
  data.append('name',add_room_form.elements['name'].value);
  data.append('location',add_room_form.elements['location'].value);
  data.append('area',add_room_form.elements['area'].value);
  data.append('price',add_room_form.elements['price'].value);
  data.append('discount',add_room_form.elements['discount'].value || 0);
  data.append('quantity',add_room_form.elements['quantity'].value);
  data.append('adult',add_room_form.elements['adult'].value);
  data.append('children',add_room_form.elements['children'].value);
  data.append('desc',add_room_form.elements['desc'].value);

  let features = [];
  add_room_form.elements['features'].forEach(el =>{
    if(el.checked){
      features.push(el.value);
    }
  });

  let facilities = [];
  add_room_form.elements['facilities'].forEach(el =>{
    if(el.checked){
      facilities.push(el.value);
    }
  });

  data.append('features',JSON.stringify(features));
  data.append('facilities',JSON.stringify(facilities));

  let xhr = new XMLHttpRequest();
  xhr.open("POST","ajax/rooms.php",true);

  xhr.onload = function(){
    var myModal = document.getElementById('add-room');
    var modal = bootstrap.Modal.getInstance(myModal);
    modal.hide();

    if(this.responseText == 1){
      alert('success','Thêm phòng mới thành công!');
      add_room_form.reset();
      get_all_rooms();
    }
    else{
      alert('error','Máy chủ đang gặp sự cố!');
    }
  }

  xhr.send(data);
}

/* ======================= LẤY DANH SÁCH PHÒNG ======================= */

function get_all_rooms()
{
  const keyword = document.getElementById('filter_keyword')?.value || '';
  const status = document.getElementById('filter_status')?.value || '';
  const owner_filter = document.getElementById('filter_owner')?.value || 'all';

  let xhr = new XMLHttpRequest();
  xhr.open("POST","ajax/rooms.php",true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function(){
    document.getElementById('room-data').innerHTML = this.responseText;
  }

  xhr.send(`get_all_rooms=1&search=${encodeURIComponent(keyword)}&status=${encodeURIComponent(status)}&owner_filter=${encodeURIComponent(owner_filter)}`);
}

/* ======================= LOAD THÔNG TIN PHÒNG ======================= */

let edit_room_form = document.getElementById('edit_room_form');

function edit_details(id)
{
  let xhr = new XMLHttpRequest();
  xhr.open("POST","ajax/rooms.php",true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function(){
    let data = JSON.parse(this.responseText);

    edit_room_form.elements['name'].value = data.roomdata.name;
    edit_room_form.elements['location'].value = data.roomdata.location || '';
    edit_room_form.elements['area'].value = data.roomdata.area;
    edit_room_form.elements['price'].value = data.roomdata.price;
    if(edit_room_form.elements['discount']){
      edit_room_form.elements['discount'].value = data.roomdata.discount ?? 0;
    }
    edit_room_form.elements['quantity'].value = data.roomdata.quantity;
    edit_room_form.elements['remaining'].value = data.roomdata.remaining;
    edit_room_form.elements['adult'].value = data.roomdata.adult;
    edit_room_form.elements['children'].value = data.roomdata.children;
    edit_room_form.elements['desc'].value = data.roomdata.description;
    edit_room_form.elements['room_id'].value = data.roomdata.id;

    edit_room_form.elements['features'].forEach(el =>{
      if(data.features.includes(Number(el.value))){
        el.checked = true;
      }
    });

    edit_room_form.elements['facilities'].forEach(el =>{
      if(data.facilities.includes(Number(el.value))){
        el.checked = true;
      }
    });
  }

  xhr.send('get_room='+id);
}

/* ======================= GỬI FORM CHỈNH SỬA ======================= */

edit_room_form.addEventListener('submit',function(e){
  e.preventDefault();
  submit_edit_room();
});

function submit_edit_room()
{
  let data = new FormData();
  data.append('edit_room','');
  data.append('room_id',edit_room_form.elements['room_id'].value);
  data.append('name',edit_room_form.elements['name'].value);
  data.append('location',edit_room_form.elements['location'].value);
  data.append('area',edit_room_form.elements['area'].value);
  data.append('price',edit_room_form.elements['price'].value);
  data.append('discount',edit_room_form.elements['discount'].value || 0);
  data.append('quantity',edit_room_form.elements['quantity'].value);
  data.append('remaining',edit_room_form.elements['remaining'].value);
  data.append('adult',edit_room_form.elements['adult'].value);
  data.append('children',edit_room_form.elements['children'].value);
  data.append('desc',edit_room_form.elements['desc'].value);

  let features = [];
  edit_room_form.elements['features'].forEach(el =>{
    if(el.checked){
      features.push(el.value);
    }
  });

  let facilities = [];
  edit_room_form.elements['facilities'].forEach(el =>{
    if(el.checked){
      facilities.push(el.value);
    }
  });

  data.append('features',JSON.stringify(features));
  data.append('facilities',JSON.stringify(facilities));

  let xhr = new XMLHttpRequest();
  xhr.open("POST","ajax/rooms.php",true);

  xhr.onload = function(){
    var myModal = document.getElementById('edit-room');
    var modal = bootstrap.Modal.getInstance(myModal);
    modal.hide();

    if(this.responseText.trim() == 1){
      alert('success','Đã cập nhật thông tin phòng!');
      edit_room_form.reset();
      get_all_rooms();
    }
    else{
      alert('error','Máy chủ đang gặp sự cố! '+this.responseText);
    }
  }

  xhr.send(data);
}

/* ======================= CHUYỂN TRẠNG THÁI ======================= */

function toggle_status(id,val)
{
  let xhr = new XMLHttpRequest();
  xhr.open("POST","ajax/rooms.php",true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function(){
    if(this.responseText==1){
      alert('success','Trạng thái phòng đã được thay đổi!');
      get_all_rooms();
    }
    else{
      alert('error','Không thể thay đổi trạng thái!');
    }
  }

  xhr.send('toggle_status='+id+'&value='+val);
}

/* ======================= DUYỆT PHÒNG ======================= */

function approve_room(id)
{
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
    console.log('Response length:', response.length);
    console.log('Response type:', typeof response);
    
    try {
      // Thử parse JSON nếu có
      let jsonResponse = JSON.parse(response);
      if(jsonResponse.error){
        console.error('Server error:', jsonResponse.error);
        if(typeof alert === 'function'){
          alert('error', jsonResponse.error);
        } else {
          alert('Lỗi: ' + jsonResponse.error);
        }
        return;
      }
    } catch(e) {
      // Không phải JSON, xử lý như bình thường
      console.log('Response is not JSON, treating as plain text');
    }
    
    if(response === '1' || response == 1){
      console.log('Approve successful!');
      if(typeof alert === 'function'){
        alert('success','Duyệt phòng thành công! Phòng đã hiển thị trên trang chủ.');
      } else {
        alert('Duyệt phòng thành công! Phòng đã hiển thị trên trang chủ.');
      }
      get_all_rooms();
    }
    else{
      console.error('Approve failed. Response:', response);
      if(typeof alert === 'function'){
        alert('error','Không thể duyệt phòng! Lỗi: ' + response);
      } else {
        alert('Không thể duyệt phòng! Lỗi: ' + response);
      }
    }
  }

  xhr.onerror = function(){
    console.error('XHR Error:', this);
    console.error('Status:', this.status);
    console.error('StatusText:', this.statusText);
    if(typeof alert === 'function'){
      alert('error','Lỗi kết nối! Vui lòng thử lại.');
    } else {
      alert('Lỗi kết nối! Vui lòng thử lại.');
    }
  }

  xhr.onloadend = function(){
    console.log('Request completed. Status:', this.status);
  }

  console.log('Sending request: approve_room=' + id);
  xhr.send('approve_room='+id);
}

// Đảm bảo hàm có thể được gọi từ inline onclick
window.approve_room = approve_room;

/* ======================= THÊM ẢNH PHÒNG ======================= */

let add_image_form = document.getElementById('add_image_form');

add_image_form.addEventListener('submit',function(e){
  e.preventDefault();
  add_image();
});

function add_image()
{
  // Truy cập input file bằng querySelector vì name có dấu ngoặc vuông
  let fileInput = add_image_form.querySelector('input[type="file"]');
  let files = fileInput ? fileInput.files : null;
  let room_id = add_image_form.elements['room_id'].value;
  
  if(!files || files.length === 0){
    alert('error','Vui lòng chọn ít nhất một ảnh!','image-alert');
    return;
  }

  // Disable button và hiển thị loading
  let submitBtn = add_image_form.querySelector('button[type="submit"]');
  if(!submitBtn){
    alert('error','Không tìm thấy nút submit!','image-alert');
    return;
  }
  let originalBtnText = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang tải...';

  let successCount = 0;
  let errorCount = 0;
  let totalFiles = files.length;
  let processedFiles = 0;

  // Upload từng file một
  Array.from(files).forEach((file, index) => {
    let data = new FormData();
    data.append('image', file);
    data.append('room_id', room_id);
    data.append('add_image', '');

    let xhr = new XMLHttpRequest();
    xhr.open("POST","ajax/rooms.php",true);

    xhr.onload = function()
    {
      processedFiles++;
      
      if(this.responseText == 'inv_img'){
        errorCount++;
      }
      else if(this.responseText == 'inv_size'){
        errorCount++;
      }
      else if(this.responseText == 'upd_failed'){
        errorCount++;
      }
      else if(this.responseText == '1' || this.responseText == 1){
        successCount++;
      }
      else{
        errorCount++;
      }

      // Khi đã xử lý xong tất cả file
      if(processedFiles === totalFiles){
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
        
        if(successCount > 0){
          let message = `Đã thêm thành công ${successCount} ảnh`;
          if(errorCount > 0){
            message += `, ${errorCount} ảnh lỗi`;
          }
          alert('success', message, 'image-alert');
          room_images(room_id, document.querySelector("#room-images .modal-title").innerText);
          add_image_form.reset();
        }
        else{
          let errorMsg = 'Không thể tải ảnh lên!';
          if(this.responseText == 'inv_img'){
            errorMsg = 'Chỉ hỗ trợ JPG, PNG, WEBP!';
          }
          else if(this.responseText == 'inv_size'){
            errorMsg = 'Ảnh phải nhỏ hơn 10MB!';
          }
          alert('error', errorMsg, 'image-alert');
        }
      }
    }

    xhr.onerror = function(){
      processedFiles++;
      errorCount++;
      
      if(processedFiles === totalFiles){
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
        
        if(successCount > 0){
          alert('success', `Đã thêm thành công ${successCount} ảnh, ${errorCount} ảnh lỗi`, 'image-alert');
          room_images(room_id, document.querySelector("#room-images .modal-title").innerText);
          add_image_form.reset();
        }
        else{
          alert('error', 'Lỗi kết nối! Vui lòng thử lại.', 'image-alert');
        }
      }
    }

    xhr.send(data);
  });
}

/* ======================= LOAD ẢNH PHÒNG ======================= */

function room_images(id,rname)
{
  document.querySelector("#room-images .modal-title").innerText = rname;
  add_image_form.elements['room_id'].value = id;
  // Reset file input - sử dụng querySelector vì name có dấu ngoặc vuông
  let fileInput = add_image_form.querySelector('input[type="file"]');
  if(fileInput){
    fileInput.value = '';
  }

  let xhr = new XMLHttpRequest();
  xhr.open("POST","ajax/rooms.php",true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function(){
    document.getElementById('room-image-data').innerHTML = this.responseText;
  }

  xhr.send('get_room_images='+id);
}

/* ======================= XÓA ẢNH ======================= */

function rem_image(img_id,room_id)
{
  let data = new FormData();
  data.append('image_id',img_id);
  data.append('room_id',room_id);
  data.append('rem_image','');

  let xhr = new XMLHttpRequest();
  xhr.open("POST","ajax/rooms.php",true);

  xhr.onload = function()
  {
    if(this.responseText == 1){
      alert('success','Đã xoá hình ảnh!','image-alert');
      room_images(room_id,document.querySelector("#room-images .modal-title").innerText);
    }
    else{
      alert('error','Không thể xoá ảnh!','image-alert');
    }
  }
  xhr.send(data);  
}

/* ======================= ĐẶT ẢNH ĐẠI DIỆN ======================= */

function thumb_image(img_id,room_id)
{
  let data = new FormData();
  data.append('image_id',img_id);
  data.append('room_id',room_id);
  data.append('thumb_image','');

  let xhr = new XMLHttpRequest();
  xhr.open("POST","ajax/rooms.php",true);

  xhr.onload = function()
  {
    if(this.responseText == 1){
      alert('success','Đã đặt ảnh làm hình đại diện!','image-alert');
      room_images(room_id,document.querySelector("#room-images .modal-title").innerText);
    }
    else{
      alert('error','Không thể cập nhật ảnh đại diện!','image-alert');
    }
  }
  xhr.send(data);  
}

/* ======================= XOÁ PHÒNG ======================= */

function remove_room(room_id)
{
  if(confirm("Bạn có chắc chắn muốn xoá phòng này?"))
  {
    let data = new FormData();
    data.append('room_id',room_id);
    data.append('remove_room','');

    let xhr = new XMLHttpRequest();
    xhr.open("POST","ajax/rooms.php",true);

    xhr.onload = function()
    {
      if(this.responseText == 1){
        alert('success','Đã xoá phòng!');
        get_all_rooms();
      }
      else{
        alert('error','Không thể xoá phòng!');
      }
    }
    xhr.send(data);
  }
}

/* ======================= THAY ĐỔI SỐ PHÒNG CÒN LẠI ======================= */

function changeRemain(value) {
    let input = edit_room_form.elements['remaining'];
    let current = parseInt(input.value) || 0;
    current += value;
    if (current < 0) current = 0;
    input.value = current;
}

/* ======================= KHỞI TẠO ======================= */

// Đảm bảo approve_room được định nghĩa trước khi sử dụng
if(typeof window.approve_room === 'undefined') {
  window.approve_room = function(id) {
    console.log('approve_room (fallback) called with id:', id);
    approve_room(id);
  };
}

window.onload = function(){
  get_all_rooms();
  
  // Kiểm tra lại sau khi load
  console.log('After page load - typeof approve_room:', typeof approve_room);
  console.log('After page load - typeof window.approve_room:', typeof window.approve_room);
}

/* ======================= FILTER HELPERS ======================= */
let debounceTimer;
function debounceFetchRooms(){
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => get_all_rooms(), 350);
}
function clearFilters(){
  const kw = document.getElementById('filter_keyword');
  const st = document.getElementById('filter_status');
  const owner = document.getElementById('filter_owner');
  if(kw) kw.value = '';
  if(st) st.value = '';
  if(owner) owner.value = 'all';
  get_all_rooms();
}

// preset keyword quick filter
function presetKeyword(text){
  const kw = document.getElementById('filter_keyword');
  if(kw){
    kw.value = text;
    get_all_rooms();
  }
}
