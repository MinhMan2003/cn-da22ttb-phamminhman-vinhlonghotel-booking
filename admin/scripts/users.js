// ...existing code...
let currentPage = 1;
let currentQuery = '';
let perPage = 25;
let debounceTimer = null;

document.addEventListener('DOMContentLoaded', ()=>{
  const perPageEl = document.getElementById('per-page');
  if(perPageEl){
    perPage = parseInt(perPageEl.value);
    perPageEl.addEventListener('change', ()=>{ perPage = parseInt(perPageEl.value); currentPage=1; loadUsers(); });
  }

  document.getElementById('users-data')?.addEventListener('click', (e)=>{
    // toggle status (Vô hiệu hóa / Khôi phục)
    const btnToggle = e.target.closest('.btn-toggle-status');
    if(btnToggle){
      const id = btnToggle.dataset.id;
      const val = parseInt(btnToggle.dataset.value);
      if(!id) return;
      const confirmMsg = val === 1 ? `Bạn có chắc muốn KHÔI PHỤC người dùng #${id} ?` : `Bạn có chắc muốn VÔ HIỆU HÓA người dùng #${id} ?`;
      if(!confirm(confirmMsg)) return;
      toggleStatus(id, val, btnToggle);
      return;
    }

    // Removed verify button handler
  });

  loadUsers();
});

function search_user(q){
  currentQuery = q.trim();
  currentPage = 1;
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(()=> loadUsers(), 300);
}

function loadUsers(){
  const params = new URLSearchParams({ action:'list', q: currentQuery, page: currentPage, per_page: perPage });
  fetch('./ajax/users.php?' + params.toString(), { credentials:'same-origin' })
    .then(r=>r.json())
    .then(resp=>{
      if(resp.status !== 'success'){ alert('Lỗi: ' + (resp.msg||'')); return; }
      renderUsers(resp.data);
      renderPagination(resp.page, resp.per_page, resp.total);
      const info = document.getElementById('users-info');
      if(info) info.textContent = `Hiển thị ${resp.data.length} / ${resp.total} người dùng`;
    })
    .catch(err=>{ console.error(err); alert('Lỗi mạng khi tải dữ liệu.'); });
}

function renderUsers(users){
  const tbody = document.getElementById('users-data');
  if(!tbody) return;
  tbody.innerHTML = '';
  if(!users.length){ tbody.innerHTML = '<tr><td colspan="10">Không có kết quả</td></tr>'; return; }
  users.forEach((u, idx)=>{
    const toggleBtn = u.status==1
      ? `<button class="btn btn-sm btn-danger btn-toggle-status" data-id="${u.id}" data-value="0">Vô hiệu hóa</button>`
      : `<button class="btn btn-sm btn-success btn-toggle-status" data-id="${u.id}" data-value="1">Khôi phục</button>`;
    
    // Hiển thị giới tính - CHỈ hiển thị thông tin Nam/Nữ, KHÔNG có nút xác minh
    let genderDisplay = '';
    const gender = (u.gender || '').toString().toLowerCase().trim();
    
    // Hiển thị giới tính - CHỈ hiển thị Nam/Nữ, KHÔNG có nút xác minh
    // Debug: log để kiểm tra dữ liệu
    if(idx === 0) {
      console.log('User data:', u);
      console.log('Gender value:', u.gender, 'Type:', typeof u.gender);
    }
    
    // Xử lý hiển thị giới tính
    const genderValue = String(u.gender || '').toLowerCase().trim();
    
    if(genderValue === 'male' || genderValue === 'nam'){
      genderDisplay = '<span class="badge bg-primary"><i class="bi bi-gender-male me-1"></i>Nam</span>';
    } else if(genderValue === 'female' || genderValue === 'nữ'){
      genderDisplay = '<span class="badge" style="background: #e91e63; color: #fff;"><i class="bi bi-gender-female me-1"></i>Nữ</span>';
    } else {
      genderDisplay = '<span class="text-muted">-</span>';
    }

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${((currentPage-1)*perPage)+idx+1}</td>
      <td>${escapeHtml(u.name)}</td>
      <td>${escapeHtml(u.email)}</td>
      <td>${escapeHtml(u.phone)}</td>
      <td>${escapeHtml(u.address)}</td>
      <td>${escapeHtml(u.dob)}</td>
      <td>${genderDisplay}</td>
      <td>${u.status==1?'<span class="badge bg-success">Hoạt động</span>':'<span class="badge bg-secondary">Không hoạt động</span>'}</td>
      <td>${escapeHtml(u.created_at)}</td>
      <td>
        <a href="edit_user.php?id=${u.id}" class="btn btn-sm btn-primary">Sửa</a>
        ${toggleBtn}
      </td>`;
    tbody.appendChild(tr);
  });
}

function toggleStatus(id, value, btn){
  btn.disabled = true;
  const fd = new FormData();
  fd.append('action','toggle_status');
  fd.append('id', id);
  fd.append('value', value);
  fd.append('csrf_token', typeof CSRF_TOKEN !== 'undefined' ? CSRF_TOKEN : '');
  fetch('./ajax/users.php', { method:'POST', body: fd, credentials:'same-origin' })
    .then(r=>r.json())
    .then(resp=>{
      btn.disabled = false;
      if(resp.status==='success'){ alert(resp.msg || 'Cập nhật trạng thái thành công'); loadUsers(); }
      else alert('Lỗi: '+(resp.msg||''));
    })
    .catch(err=>{ btn.disabled=false; console.error(err); alert('Lỗi mạng'); });
}

// Removed verifyUser function

function renderPagination(page, per_page, total){
  const totalPages = Math.max(1, Math.ceil(total / per_page));
  const ul = document.getElementById('users-pagination');
  if(!ul) return;
  ul.innerHTML = '';
  const pushPage = (p, label, disabled, active)=>{
    const li = document.createElement('li');
    li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
    li.innerHTML = `<a class="page-link" href="#" data-page="${p}">${label}</a>`;
    ul.appendChild(li);
  };
  pushPage(page-1, 'Trước', page<=1, false);
  let start = Math.max(1, page-3), end = Math.min(totalPages, page+3);
  if(end-start<6){ start = Math.max(1, end-6); end = Math.min(totalPages, start+6); }
  for(let p=start;p<=end;p++) pushPage(p,p,false,p===page);
  pushPage(page+1, 'Tiếp', page>=totalPages, false);

  ul.querySelectorAll('a.page-link').forEach(a=>{
    a.addEventListener('click', (e)=>{
      e.preventDefault();
      const p = parseInt(a.dataset.page);
      if(!p || p===currentPage) return;
      currentPage = p; loadUsers(); window.scrollTo({top:0, behavior:'smooth'});
    });
  });
}

function escapeHtml(s){ return (s||'').toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }
// ...existing code...