let promoTableBody = document.getElementById('promo-data');
let promoForm = document.getElementById('promo-form');
let promoModalEl = document.getElementById('promoModal');
let promoModal = new bootstrap.Modal(promoModalEl);
let promoAlert = document.getElementById('promo-alert');
let editId = '';

const showAlert = (type, msg) => {
  const palette = {
    success: 'background:#d1f7d5;color:#0f5132;border:1px solid #9adba3;',
    danger: 'background:#f8d7da;color:#842029;border:1px solid #f5c2c7;',
    warning: 'background:#fff3cd;color:#664d03;border:1px solid #ffecb5;'
  };
  const style = palette[type] || '';
  promoAlert.innerHTML = `
    <div class="alert alert-${type} alert-dismissible fade show" role="alert" style="${style}">
      ${msg}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  `;
  setTimeout(() => { promoAlert.innerHTML = ''; }, 2500);
};

function fetchPromos(){
  let xhr = new XMLHttpRequest();
  xhr.open("POST","ajax/promos.php",true);
  xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
  xhr.onload = function(){
    promoTableBody.innerHTML = this.responseText;
  };
  xhr.send('get_promos=1');
}

promoForm.addEventListener('submit', function(e){
  e.preventDefault();
  let fd = new FormData(promoForm);
  fd.append('save_promo','1');
  if(editId) fd.append('id', editId);

  let xhr = new XMLHttpRequest();
  xhr.open("POST","ajax/promos.php",true);
  xhr.onload = function(){
    const res = this.responseText.trim();
    if(res === 'success' || res === 'updated'){
      showAlert('success', res === 'updated' ? 'Đã cập nhật mã' : 'Đã thêm mã mới');
      promoForm.reset();
      editId = '';
      promoModal.hide();
      fetchPromos();
    } else if(res === 'duplicate'){
      showAlert('warning','Mã code đã tồn tại, vui lòng dùng mã khác');
    } else {
      showAlert('danger','Lưu thất bại: ' + res);
    }
  };
  xhr.send(fd);
});

function editPromo(id){
  let fd = new FormData();
  fd.append('get_single',1);
  fd.append('id', id);

  let xhr = new XMLHttpRequest();
  xhr.open("POST","ajax/promos.php",true);
  xhr.onload = function(){
    let data = {};
    try { data = JSON.parse(this.responseText); } catch (err) {
      showAlert('danger','Không đọc được dữ liệu mã');
      return;
    }
    if(!data || !data.id){
      showAlert('warning','Không tìm thấy mã');
      return;
    }

    document.getElementById('promo_id').value = data.id || '';
    promoForm.label.value = data.label || '';
    promoForm.code.value = data.code || '';
    promoForm.title.value = data.title || '';
    promoForm.description.value = data.description || '';
    promoForm.category.value = data.category || 'hot';
    promoForm.discount_type.value = data.discount_type || 'percent';
    promoForm.discount_value.value = data.discount_value || 0;
    promoForm.min_amount.value = data.min_amount || 0;
    promoForm.max_discount.value = data.max_discount || '';
    promoForm.priority.value = data.priority || 0;
    promoForm.expires_at.value = data.expires_at && data.expires_at !== '0000-00-00' ? data.expires_at : '';
    promoForm.active.checked = data.active == 1;

    editId = data.id;
    promoModal.show();
  };
  xhr.send(fd);
}

function togglePromo(id, status){
  let fd = new FormData();
  fd.append('toggle_promo',1);
  fd.append('id', id);
  fd.append('status', status);

  let xhr = new XMLHttpRequest();
  xhr.open("POST","ajax/promos.php",true);
  xhr.onload = function(){
    fetchPromos();
    showAlert('success','Đã cập nhật trạng thái');
  };
  xhr.send(fd);
}

function deletePromo(id){
  if(!confirm('Xóa mã giảm giá này?')) return;
  let fd = new FormData();
  fd.append('delete_promo',1);
  fd.append('id', id);

  let xhr = new XMLHttpRequest();
  xhr.open("POST","ajax/promos.php",true);
  xhr.onload = function(){
    fetchPromos();
    showAlert('success','Đã xóa mã');
  };
  xhr.send(fd);
}

promoModalEl.addEventListener('hidden.bs.modal', () => {
  promoForm.reset();
  document.getElementById('promo_id').value = '';
  promoForm.active.checked = true;
  editId = '';
});

document.addEventListener('DOMContentLoaded', fetchPromos);
