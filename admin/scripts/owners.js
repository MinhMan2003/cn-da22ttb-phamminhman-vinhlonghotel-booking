/* ======================= LẤY DANH SÁCH OWNERS ======================= */

function get_all_owners()
{
  const keyword = document.getElementById('filter_keyword')?.value || '';
  const status = document.getElementById('filter_status')?.value || '';

  let xhr = new XMLHttpRequest();
  xhr.open("POST","ajax/owners.php",true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function(){
    document.getElementById('owner-data').innerHTML = this.responseText;
  }

  xhr.send(`get_all_owners=1&search=${encodeURIComponent(keyword)}&status=${encodeURIComponent(status)}`);
}

/* ======================= CHUYỂN TRẠNG THÁI (Duyệt/Khóa) ======================= */

function toggle_status(id,val)
{
  let confirm_msg = val == 1 ? 'Kích hoạt tài khoản này?' : 'Bạn có chắc muốn khóa tài khoản này?';
  
  if(!confirm(confirm_msg)){
    return;
  }

  let xhr = new XMLHttpRequest();
  xhr.open("POST","ajax/owners.php",true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function(){
    if(this.responseText == 1){
      alert('success','Thay đổi trạng thái thành công!');
      get_all_owners();
    }
    else{
      alert('error','Không thể thay đổi trạng thái!');
    }
  }

  xhr.send('toggle_status='+id+'&value='+val);
}

/* ======================= XEM CHI TIẾT OWNER ======================= */

function view_owner_details(id)
{
  let contentDiv = document.getElementById('owner-details-content');
  contentDiv.innerHTML = `
    <div class="text-center py-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Đang tải...</span>
      </div>
    </div>
  `;

  let xhr = new XMLHttpRequest();
  xhr.open("POST","ajax/owners.php",true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function(){
    try{
      let data = JSON.parse(this.responseText);
      if(data.error){
        contentDiv.innerHTML = `
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>${data.error}
          </div>
        `;
        return;
      }

      let statusText = data.status == 1 ? '<span class="badge bg-success">Đã kích hoạt</span>' : 
                      '<span class="badge bg-danger">Đã khóa</span>';
      
      let createdDate = new Date(data.created_at).toLocaleDateString('vi-VN', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });

      contentDiv.innerHTML = `
        <div class="row g-3">
          <div class="col-md-6">
            <div class="border rounded p-3">
              <label class="form-label small text-muted mb-1">Tên chủ khách sạn</label>
              <p class="mb-0 fw-semibold">${data.name || '—'}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3">
              <label class="form-label small text-muted mb-1">Email</label>
              <p class="mb-0">${data.email || '—'}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3">
              <label class="form-label small text-muted mb-1">Số điện thoại</label>
              <p class="mb-0">${data.phone || '—'}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3">
              <label class="form-label small text-muted mb-1">Tên khách sạn</label>
              <p class="mb-0 fw-semibold">${data.hotel_name || '—'}</p>
            </div>
          </div>
          <div class="col-12">
            <div class="border rounded p-3">
              <label class="form-label small text-muted mb-1">Địa chỉ</label>
              <p class="mb-0">${data.address || '—'}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3">
              <label class="form-label small text-muted mb-1">Trạng thái</label>
              <p class="mb-0">${statusText}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3">
              <label class="form-label small text-muted mb-1">Ngày đăng ký</label>
              <p class="mb-0">${createdDate}</p>
            </div>
          </div>
        </div>
      `;
    } catch(e){
      contentDiv.innerHTML = `
        <div class="alert alert-danger">
          <i class="bi bi-exclamation-triangle me-2"></i>Lỗi khi tải dữ liệu!
        </div>
      `;
    }
  }

  xhr.send('get_owner='+id);
}

/* ======================= XÓA OWNER ======================= */

function delete_owner(id)
{
  if(!confirm('Bạn có chắc muốn xóa chủ khách sạn này? Hành động này không thể hoàn tác!')){
    return;
  }

  let xhr = new XMLHttpRequest();
  xhr.open("POST","ajax/owners.php",true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function(){
    try{
      let data = JSON.parse(this.responseText);
      if(data.success){
        alert('success','Xóa thành công!');
        get_all_owners();
      } else {
        alert('error', data.error || 'Không thể xóa!');
      }
    } catch(e){
      alert('error', 'Lỗi khi xóa!');
    }
  }

  xhr.send('delete_owner='+id);
}

/* ======================= FILTER HELPERS ======================= */

let debounceTimer;
function debounceFetchOwners(){
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => get_all_owners(), 350);
}

function clearFilters(){
  const kw = document.getElementById('filter_keyword');
  const st = document.getElementById('filter_status');
  if(kw) kw.value = '';
  if(st) st.value = '';
  get_all_owners();
}

/* ======================= THÊM/SỬA OWNER ======================= */

function openAddOwnerModal() {
  document.getElementById('add-edit-modal-title').innerHTML = '<i class="bi bi-person-plus me-2"></i>Thêm Chủ khách sạn';
  document.getElementById('owner-form').reset();
  document.getElementById('owner_id').value = '';
  document.getElementById('owner_password').required = true;
  document.getElementById('password-label').innerHTML = 'Mật khẩu <span class="text-danger">*</span>';
  document.getElementById('password-hint').style.display = 'none';
  document.getElementById('owner_status').value = '1';
  
  let modal = new bootstrap.Modal(document.getElementById('add-edit-owner-modal'));
  modal.show();
}

function edit_owner(id) {
  // #region agent log
  fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/scripts/owners.js:edit_owner',message:'edit_owner called',data:{id:id},timestamp:Date.now(),sessionId:'debug-session',runId:'run5',hypothesisId:'A'})}).catch(()=>{});
  // #endregion
  
  // Kiểm tra modal có tồn tại không
  let modalElement = document.getElementById('add-edit-owner-modal');
  if(!modalElement){
    // #region agent log
    fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/scripts/owners.js:edit_owner',message:'Modal element not found',data:{},timestamp:Date.now(),sessionId:'debug-session',runId:'run5',hypothesisId:'B'})}).catch(()=>{});
    // #endregion
    alert('error', 'Modal không tồn tại!');
    return;
  }
  
  // Lấy thông tin owner
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "ajax/owners.php", true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  
  xhr.onload = function(){
    // #region agent log
    fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/scripts/owners.js:edit_owner',message:'Response received',data:{status:xhr.status,responseText:xhr.responseText.substring(0,200)},timestamp:Date.now(),sessionId:'debug-session',runId:'run5',hypothesisId:'A'})}).catch(()=>{});
    // #endregion
    
    try{
      let data = JSON.parse(this.responseText);
      if(data.error){
        alert('error', data.error);
        return;
      }
      
      // #region agent log
      fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/scripts/owners.js:edit_owner',message:'Filling form with data',data:{ownerId:data.id,name:data.name},timestamp:Date.now(),sessionId:'debug-session',runId:'run5',hypothesisId:'A'})}).catch(()=>{});
      // #endregion
      
      // Điền form
      document.getElementById('add-edit-modal-title').innerHTML = '<i class="bi bi-pencil me-2"></i>Sửa Chủ khách sạn';
      document.getElementById('owner_id').value = data.id;
      document.getElementById('owner_name').value = data.name || '';
      document.getElementById('owner_email').value = data.email || '';
      document.getElementById('owner_phone').value = data.phone || '';
      document.getElementById('owner_hotel_name').value = data.hotel_name || '';
      document.getElementById('owner_address').value = data.address || '';
      document.getElementById('owner_status').value = data.status || '0';
      document.getElementById('owner_password').value = '';
      document.getElementById('owner_password').required = false;
      document.getElementById('password-label').innerHTML = 'Mật khẩu mới (để trống nếu không đổi)';
      document.getElementById('password-hint').style.display = 'block';
      
      let modal = new bootstrap.Modal(modalElement);
      modal.show();
      
      // #region agent log
      fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/scripts/owners.js:edit_owner',message:'Modal shown',data:{},timestamp:Date.now(),sessionId:'debug-session',runId:'run5',hypothesisId:'A'})}).catch(()=>{});
      // #endregion
    } catch(e){
      // #region agent log
      fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/scripts/owners.js:edit_owner',message:'Error parsing response',data:{error:e.message,responseText:xhr.responseText.substring(0,200)},timestamp:Date.now(),sessionId:'debug-session',runId:'run5',hypothesisId:'C'})}).catch(()=>{});
      // #endregion
      alert('error', 'Lỗi khi tải dữ liệu!');
    }
  }
  
  xhr.onerror = function(){
    // #region agent log
    fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/scripts/owners.js:edit_owner',message:'XHR error',data:{},timestamp:Date.now(),sessionId:'debug-session',runId:'run5',hypothesisId:'D'})}).catch(()=>{});
    // #endregion
    alert('error', 'Lỗi kết nối!');
  }
  
  xhr.send('get_owner=' + id);
}

function saveOwner() {
  // #region agent log
  fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/scripts/owners.js:saveOwner',message:'saveOwner called',data:{},timestamp:Date.now(),sessionId:'debug-session',runId:'run5',hypothesisId:'E'})}).catch(()=>{});
  // #endregion
  
  let form = document.getElementById('owner-form');
  if(!form){
    // #region agent log
    fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/scripts/owners.js:saveOwner',message:'Form element not found',data:{},timestamp:Date.now(),sessionId:'debug-session',runId:'run5',hypothesisId:'F'})}).catch(()=>{});
    // #endregion
    alert('error', 'Form không tồn tại!');
    return;
  }
  
  if(!form.checkValidity()){
    form.reportValidity();
    return;
  }
  
  let ownerId = document.getElementById('owner_id').value;
  
  // #region agent log
  fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/scripts/owners.js:saveOwner',message:'Form validation passed',data:{ownerId:ownerId},timestamp:Date.now(),sessionId:'debug-session',runId:'run5',hypothesisId:'E'})}).catch(()=>{});
  // #endregion
  
  let formData = new FormData(form);
  
  // Đảm bảo owner_id được gửi trong form data (không xóa)
  // Thêm action flag
  if(ownerId){
    formData.append('update_owner', ownerId);
  } else {
    formData.append('add_owner', '1');
  }
  
  // #region agent log
  let formDataEntries = {};
  for(let pair of formData.entries()) {
    formDataEntries[pair[0]] = pair[1];
  }
  fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/scripts/owners.js:saveOwner',message:'Sending request',data:{formData:formDataEntries,isUpdate:!!ownerId},timestamp:Date.now(),sessionId:'debug-session',runId:'run5',hypothesisId:'E'})}).catch(()=>{});
  // #endregion
  
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "ajax/owners.php", true);
  
  xhr.onload = function(){
    // #region agent log
    fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/scripts/owners.js:saveOwner',message:'Response received',data:{status:xhr.status,responseText:xhr.responseText.substring(0,300)},timestamp:Date.now(),sessionId:'debug-session',runId:'run5',hypothesisId:'E'})}).catch(()=>{});
    // #endregion
    
    try{
      let data = JSON.parse(this.responseText);
      if(data.success){
        alert('success', ownerId ? 'Cập nhật thành công!' : 'Thêm thành công!');
        bootstrap.Modal.getInstance(document.getElementById('add-edit-owner-modal')).hide();
        get_all_owners();
      } else {
        alert('error', data.error || 'Có lỗi xảy ra!');
      }
    } catch(e){
      // #region agent log
      fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/scripts/owners.js:saveOwner',message:'JSON parse error',data:{error:e.message,responseText:xhr.responseText.substring(0,300)},timestamp:Date.now(),sessionId:'debug-session',runId:'run5',hypothesisId:'G'})}).catch(()=>{});
      // #endregion
      console.error('Error:', e, this.responseText);
      alert('error', 'Lỗi khi lưu dữ liệu!');
    }
  }
  
  xhr.onerror = function(){
    // #region agent log
    fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/scripts/owners.js:saveOwner',message:'XHR error',data:{},timestamp:Date.now(),sessionId:'debug-session',runId:'run5',hypothesisId:'H'})}).catch(()=>{});
    // #endregion
    alert('error', 'Lỗi kết nối!');
  }
  
  xhr.send(formData);
}

// Load khi trang được mở
window.onload = get_all_owners;

