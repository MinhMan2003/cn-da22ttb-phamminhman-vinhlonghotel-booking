// ============================================
// DESTINATIONS MANAGEMENT
// ============================================

function showResponseAlert(res, fallbackMessage) {
  const type = res && res.status === 'success' ? 'success' : 'error';
  const message = res && res.message ? res.message : (fallbackMessage || 'Co loi xay ra');
  alert(type, message);
}

function debounce(callback, wait) {
  let timerId;
  return function(...args) {
    clearTimeout(timerId);
    timerId = setTimeout(() => callback.apply(this, args), wait);
  };
}

function getDataRows(body) {
  if (!body) return [];
  return Array.from(body.querySelectorAll('tr')).filter((row) => row.children.length > 1);
}

function setEmptyRow(body, colSpan, message, show) {
  if (!body) return;
  let row = body.querySelector('tr.empty-row');
  if (!row) {
    row = document.createElement('tr');
    row.className = 'empty-row';
    row.innerHTML = `<td colspan="${colSpan}" class="empty-state">${message}</td>`;
    body.appendChild(row);
  }
  row.style.display = show ? '' : 'none';
}

function applyImageFallback(container) {
  if (!container) return;
  const images = container.querySelectorAll('img');
  images.forEach((img) => {
    img.onerror = null;
    img.onerror = function() {
      this.onerror = null;
      const src = this.src || '';
      if (src && !src.includes('default.jpg')) {
        const basePath = src.substring(0, src.lastIndexOf('/') + 1);
        this.src = basePath + 'default.jpg';
        this.onerror = function() {
          this.style.display = 'none';
          this.onerror = null;
        };
      } else {
        this.style.display = 'none';
      }
    };
  });
}

function getImagesCount(cell) {
  if (!cell) return 0;
  const small = cell.querySelector('small');
  if (small) {
    const value = parseInt(small.textContent, 10);
    if (!Number.isNaN(value)) return value;
  }
  return cell.querySelector('img') ? 1 : 0;
}

function isActiveRow(row) {
  if (!row) return false;
  return !!row.querySelector('.badge.bg-success');
}

function applyDestinationFilters() {
  const body = document.getElementById('destination-data');
  if (!body) return;
  Array.from(body.querySelectorAll('tr')).forEach((row) => {
    if (row.children.length <= 1) {
      row.style.display = 'none';
    }
  });
  const rows = getDataRows(body);
  const search = (document.getElementById('destinations-search')?.value || '').toLowerCase().trim();
  const category = document.getElementById('destinations-category')?.value || '';
  const status = document.getElementById('destinations-status')?.value || '';

  let visible = 0;
  let active = 0;
  let images = 0;

  rows.forEach((row) => {
    const rowText = row.textContent.toLowerCase();
    const rowCategory = row.getAttribute('data-category') || '';
    const matchesSearch = !search || rowText.includes(search);
    const matchesCategory = !category || rowCategory === category;
    const rowActive = row.getAttribute('data-active') === '1';
    const matchesStatus = !status || (status === 'active' ? rowActive : !rowActive);
    const matches = matchesSearch && matchesCategory && matchesStatus;

    row.classList.toggle('is-hidden', !matches);

    if (matches) {
      visible += 1;
      if (rowActive || isActiveRow(row)) active += 1;
      images += getImagesCount(row.children[1]);
    }
  });

  setEmptyRow(body, 9, 'Khong co du lieu phu hop', visible === 0);
  const totalEl = document.getElementById('destinations-total');
  const activeEl = document.getElementById('destinations-active');
  const imagesEl = document.getElementById('destinations-images');
  if (totalEl) totalEl.textContent = visible;
  if (activeEl) activeEl.textContent = active;
  if (imagesEl) imagesEl.textContent = images;
}

function applySpecialtyFilters() {
  const body = document.getElementById('specialty-data');
  if (!body) return;
  Array.from(body.querySelectorAll('tr')).forEach((row) => {
    if (row.children.length <= 1) {
      row.style.display = 'none';
    }
  });
  const rows = getDataRows(body);
  const search = (document.getElementById('specialties-search')?.value || '').toLowerCase().trim();
  const category = document.getElementById('specialties-category')?.value || '';
  const status = document.getElementById('specialties-status')?.value || '';

  let visible = 0;
  let active = 0;
  let images = 0;

  rows.forEach((row) => {
    const rowText = row.textContent.toLowerCase();
    const rowCategory = row.getAttribute('data-category') || '';
    const matchesSearch = !search || rowText.includes(search);
    const matchesCategory = !category || rowCategory === category;
    const rowActive = row.getAttribute('data-active') === '1';
    const matchesStatus = !status || (status === 'active' ? rowActive : !rowActive);
    const matches = matchesSearch && matchesCategory && matchesStatus;

    row.classList.toggle('is-hidden', !matches);

    if (matches) {
      visible += 1;
      if (rowActive || isActiveRow(row)) active += 1;
      images += getImagesCount(row.children[1]);
    }
  });

  setEmptyRow(body, 9, 'Khong co du lieu phu hop', visible === 0);
  const totalEl = document.getElementById('specialties-total');
  const activeEl = document.getElementById('specialties-active');
  const imagesEl = document.getElementById('specialties-images');
  if (totalEl) totalEl.textContent = visible;
  if (activeEl) activeEl.textContent = active;
  if (imagesEl) imagesEl.textContent = images;
}

function bindFilters() {
  const destSearch = document.getElementById('destinations-search');
  const destCategory = document.getElementById('destinations-category');
  const destStatus = document.getElementById('destinations-status');
  const destReset = document.getElementById('destinations-reset');
  if (destSearch) destSearch.addEventListener('input', debounce(applyDestinationFilters, 200));
  if (destCategory) destCategory.addEventListener('change', applyDestinationFilters);
  if (destStatus) destStatus.addEventListener('change', applyDestinationFilters);
  if (destReset) {
    destReset.addEventListener('click', () => {
      if (destSearch) destSearch.value = '';
      if (destCategory) destCategory.value = '';
      if (destStatus) destStatus.value = '';
      applyDestinationFilters();
    });
  }

  const specSearch = document.getElementById('specialties-search');
  const specCategory = document.getElementById('specialties-category');
  const specStatus = document.getElementById('specialties-status');
  const specReset = document.getElementById('specialties-reset');
  if (specSearch) specSearch.addEventListener('input', debounce(applySpecialtyFilters, 200));
  if (specCategory) specCategory.addEventListener('change', applySpecialtyFilters);
  if (specStatus) specStatus.addEventListener('change', applySpecialtyFilters);
  if (specReset) {
    specReset.addEventListener('click', () => {
      if (specSearch) specSearch.value = '';
      if (specCategory) specCategory.value = '';
      if (specStatus) specStatus.value = '';
      applySpecialtyFilters();
    });
  }
}

// Prevent multiple initializations
if(typeof window.destinationsInitialized === 'undefined') {
  window.destinationsInitialized = true;
  
  let destinationModal;
  let isLoading = false; // Prevent multiple simultaneous requests
  let isInitialized = false;

  // Get all destinations - define first
  window.get_destinations = function(){
    if(isLoading) {
      return; // Prevent multiple calls
    }
    isLoading = true;
    
    const dataEl = document.getElementById('destination-data');
    if(!dataEl) {
      isLoading = false;
      return;
    }
    
    dataEl.innerHTML = '<tr><td colspan=\"9\" class=\"text-center py-4\"><div class=\"spinner-border spinner-border-sm me-2\"></div>Dang tai...</td></tr>';

    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/destinations.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function(){
      isLoading = false;
      if(xhr.status === 200){
        dataEl.innerHTML = xhr.responseText;
        applyImageFallback(dataEl);
        applyDestinationFilters();
      } else {
        dataEl.innerHTML = '<tr><td colspan=\"9\" class=\"text-center py-4 text-danger\">Loi tai du lieu</td></tr>';
      }
    };
    
    xhr.onerror = function(){
      isLoading = false;
      dataEl.innerHTML = '<tr><td colspan=\"9\" class=\"text-center py-4 text-danger\">Loi ket noi</td></tr>';
    };

    xhr.ontimeout = function(){
      isLoading = false;
      dataEl.innerHTML = '<tr><td colspan=\"9\" class=\"text-center py-4 text-danger\">Qua thoi gian cho</td></tr>';
    };
    
    xhr.timeout = 10000; // 10 seconds timeout
    
    xhr.send('get_destinations=1');
  };

  // Initialize only once
  function initDestinations() {
    if(isInitialized) {
      return;
    }
    isInitialized = true;
    
    const modalEl = document.getElementById('destinationModal');
    if(modalEl) {
      destinationModal = new bootstrap.Modal(modalEl);
    }
    
    // Only call get_destinations if table exists
    setTimeout(function(){
      const dataEl = document.getElementById('destination-data');
      if(dataEl && window.get_destinations) {
        window.get_destinations();
      }
    }, 100);

    bindFilters();
  }

  // Wait for DOM to be ready - but only initialize once
  if(document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDestinations);
  } else {
    // DOM already loaded
    initDestinations();
  }

  // Add new destination
  const destinationForm = document.getElementById('destination-form');
  if(destinationForm) {
    // Remove existing listeners to prevent duplicates
    const newForm = destinationForm.cloneNode(true);
    destinationForm.parentNode.replaceChild(newForm, destinationForm);
    
    newForm.addEventListener('submit', function(e){
      e.preventDefault();
      e.stopPropagation();
      
      let formData = new FormData(this);
      formData.append('save_destination', '1');
      
      let xhr = new XMLHttpRequest();
      xhr.open('POST', 'ajax/destinations.php', true);
      
      xhr.onload = function(){
        if(xhr.status === 200){
          try {
            let res = JSON.parse(xhr.responseText);
            showResponseAlert(res, 'Co loi xay ra');
            if(res.status === 'success'){
              if(destinationModal) destinationModal.hide();
              const form = document.getElementById('destination-form');
              const preview = document.getElementById('images-preview');
              if(form) form.reset();
              if(preview) preview.innerHTML = '';
              if(window.get_destinations) window.get_destinations();
            }
          } catch(e) {
            alert('error', 'Loi: ' + xhr.responseText);
          }
        }
      };
      
      xhr.send(formData);
    });
  }

  // Edit destination
  window.editDestination = function(id){
  let xhr = new XMLHttpRequest();
  xhr.open('POST', 'ajax/destinations.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  
  xhr.onload = function(){
    if(xhr.status === 200){
      try {
        let res = JSON.parse(xhr.responseText);
        if(res.status === 'success'){
          let data = res.data;
          
          document.getElementById('destination_id').value = data.id;
          document.querySelector('[name="name"]').value = data.name || '';
          document.querySelector('[name="description"]').value = data.description || '';
          document.querySelector('[name="short_description"]').value = data.short_description || '';
          document.querySelector('[name="location"]').value = data.location || '';
          document.querySelector('[name="latitude"]').value = data.latitude || '';
          document.querySelector('[name="longitude"]').value = data.longitude || '';
          document.querySelector('[name="category"]').value = data.category || 'other';
          document.querySelector('[name="rating"]').value = data.rating || 0;
          document.querySelector('[name="review_count"]').value = data.review_count || 0;
          document.querySelector('[name="active"]').value = data.active || 1;
          
          // Show existing images
          const existingImagesEl = document.getElementById('existing-images');
          const imagesPreviewEl = document.getElementById('images-preview');
          
          if(existingImagesEl) existingImagesEl.innerHTML = '';
          if(imagesPreviewEl) imagesPreviewEl.innerHTML = '';
          
          if(data.images && data.images.length > 0){
            if(existingImagesEl){
              existingImagesEl.innerHTML = '<label class="form-label fw-semibold mt-3">Ảnh hiện có:</label><div class="row g-2" id="existing-images-list"></div>';
              const listEl = document.getElementById('existing-images-list');
              
              data.images.forEach(function(img){
                const col = document.createElement('div');
                col.className = 'col-md-3 col-6';
                const imgPath = '../images/destinations/' + img.image;
                const isPrimary = img.is_primary == 1;
                col.innerHTML = `
                  <div class="position-relative">
                    <img src="${imgPath}" class="img-thumbnail ${isPrimary ? 'border border-primary border-2' : ''}" style="width:100%;height:120px;object-fit:cover;" onerror="this.style.display='none'">
                    ${isPrimary ? '<span class="badge bg-primary position-absolute top-0 start-0 m-1">Ảnh chính</span>' : ''}
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="deleteDestinationImage(${img.id}, ${data.id})" style="padding:2px 6px;" title="Xóa ảnh">
                      <i class="bi bi-trash"></i>
                    </button>
                    ${!isPrimary ? `<button type="button" class="btn btn-sm btn-primary position-absolute bottom-0 start-0 m-1" onclick="setPrimaryImage(${img.id}, ${data.id})" style="padding:2px 6px;" title="Đặt làm ảnh chính">
                      <i class="bi bi-star"></i>
                    </button>` : ''}
                  </div>
                `;
                listEl.appendChild(col);
              });
            }
          }
          
          if(destinationModal) destinationModal.show();
        } else {
          showResponseAlert(res, 'Co loi xay ra');
        }
      } catch(e) {
        alert('error', 'Loi: ' + xhr.responseText);
      }
    }
  };
  
  xhr.send('get_destination=1&id=' + id);
}

  // Toggle active
  window.toggleDestination = function(id, active){
  if(!confirm('Bạn có chắc muốn thay đổi trạng thái?')) return;
  
  let xhr = new XMLHttpRequest();
  xhr.open('POST', 'ajax/destinations.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  
  xhr.onload = function(){
    if(xhr.status === 200){
      try {
        let res = JSON.parse(xhr.responseText);
        showResponseAlert(res, 'Co loi xay ra');
        if(res.status === 'success'){
          if(window.get_destinations) window.get_destinations();
        }
      } catch(e) {
        alert('error', 'Loi: ' + xhr.responseText);
      }
    }
  };
  
  xhr.send('toggle_destination=1&id=' + id + '&active=' + active);
}

  // Delete destination
  window.deleteDestination = function(id){
  if(!confirm('Bạn có chắc muốn xóa điểm du lịch này? Hành động này không thể hoàn tác!')) return;
  
  let xhr = new XMLHttpRequest();
  xhr.open('POST', 'ajax/destinations.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  
  xhr.onload = function(){
    if(xhr.status === 200){
      try {
        let res = JSON.parse(xhr.responseText);
        showResponseAlert(res, 'Co loi xay ra');
        if(res.status === 'success'){
          if(window.get_destinations) window.get_destinations();
        }
      } catch(e) {
        alert('error', 'Loi: ' + xhr.responseText);
      }
    }
  };
  
  xhr.send('delete_destination=1&id=' + id);
}

  // Multiple images preview - use event delegation
  document.addEventListener('change', function(e){
    if(e.target && e.target.id === 'destination_images') {
      const files = e.target.files;
      const previewEl = document.getElementById('images-preview');
      if(previewEl && files.length > 0) {
        previewEl.innerHTML = ''; // Clear previous previews
        
        Array.from(files).forEach(function(file, index){
          if(file.type.startsWith('image/')){
            let reader = new FileReader();
            reader.onload = function(e){
              const col = document.createElement('div');
              col.className = 'col-md-3 col-6';
              col.innerHTML = `
                <div class="position-relative">
                  <img src="${e.target.result}" class="img-thumbnail" style="width:100%;height:120px;object-fit:cover;">
                  <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="removeImagePreview(this)" style="padding:2px 6px;">
                    <i class="bi bi-x"></i>
                  </button>
                </div>
              `;
              previewEl.appendChild(col);
            };
            reader.readAsDataURL(file);
          }
        });
      }
    }
  });
  
  // Remove image from preview
  window.removeImagePreview = function(btn){
    const col = btn.closest('.col-md-3');
    if(col) col.remove();
    
    // Update file input
    const input = document.getElementById('destination_images');
    if(input){
      const dt = new DataTransfer();
      const files = Array.from(input.files);
      const previewImages = document.querySelectorAll('#images-preview img');
      previewImages.forEach(function(img, index){
        if(img.closest('.col-md-3').contains(btn)) return; // Skip removed image
        if(files[index]) dt.items.add(files[index]);
      });
      input.files = dt.files;
    }
  };

  // Reset form when modal is closed - use event delegation
  document.addEventListener('hidden.bs.modal', function(e){
    if(e.target && e.target.id === 'destinationModal') {
      const form = document.getElementById('destination-form');
      const imagesPreview = document.getElementById('images-preview');
      const existingImages = document.getElementById('existing-images');
      const idInput = document.getElementById('destination_id');
      
      if(form) form.reset();
      if(imagesPreview) imagesPreview.innerHTML = '';
      if(existingImages) existingImages.innerHTML = '';
      if(idInput) idInput.value = '';
    }
  });
  
  // Delete destination image
  window.deleteDestinationImage = function(imageId, destinationId){
    if(!confirm('Bạn có chắc muốn xóa ảnh này?')) return;
    
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/destinations.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function(){
      if(xhr.status === 200){
        try {
          let res = JSON.parse(xhr.responseText);
          showResponseAlert(res, 'Co loi xay ra');
          if(res.status === 'success'){
            // Reload destination data
            if(window.editDestination) window.editDestination(destinationId);
          }
        } catch(e) {
          alert('error', 'Loi: ' + xhr.responseText);
        }
      }
    };
    
    xhr.send('delete_image=1&image_id=' + imageId);
  };
  
  // Set primary image
  window.setPrimaryImage = function(imageId, destinationId){
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/destinations.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function(){
      if(xhr.status === 200){
        try {
          let res = JSON.parse(xhr.responseText);
          showResponseAlert(res, 'Co loi xay ra');
          if(res.status === 'success'){
            // Reload destination data
            if(window.editDestination) window.editDestination(destinationId);
          }
        } catch(e) {
          alert('error', 'Loi: ' + xhr.responseText);
        }
      }
    };
    
    xhr.send('set_primary_image=1&image_id=' + imageId + '&destination_id=' + destinationId);
  };
}

// ============================================
// SPECIALTIES MANAGEMENT
// ============================================

// Define get_specialties function FIRST, before any if blocks
window.get_specialties = function(){
  if(typeof window.specialtiesLoading === 'undefined') {
    window.specialtiesLoading = false;
  }

  if(window.specialtiesLoading) {
    return;
  }
  window.specialtiesLoading = true;

  const dataEl = document.getElementById('specialty-data');
  if(!dataEl) {
    window.specialtiesLoading = false;
    return;
  }

  dataEl.innerHTML = '<tr><td colspan="9" class="text-center py-4"><div class="spinner-border spinner-border-sm me-2"></div>Dang tai...</td></tr>';

  let xhr = new XMLHttpRequest();
  xhr.open('POST', 'ajax/destinations.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function(){
    window.specialtiesLoading = false;
    if(xhr.status === 200){
      dataEl.innerHTML = xhr.responseText;
      applyImageFallback(dataEl);
      applySpecialtyFilters();
    } else {
      dataEl.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-danger">Loi tai du lieu</td></tr>';
    }
  };

  xhr.onerror = function(){
    window.specialtiesLoading = false;
    dataEl.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-danger">Loi ket noi</td></tr>';
  };

  xhr.ontimeout = function(){
    window.specialtiesLoading = false;
    dataEl.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-danger">Qua thoi gian cho</td></tr>';
  };

  xhr.timeout = 10000;
  xhr.send('get_specialties=1');
};

if(typeof window.specialtiesInitialized === 'undefined') {
  window.specialtiesInitialized = true;
  
  let specialtyModal;
  let shopModal;
  let isInitializedSpecialties = false;

  // Initialize specialties
  function initSpecialties() {
    if(isInitializedSpecialties) return;
    isInitializedSpecialties = true;
    
    const modalEl = document.getElementById('specialtyModal');
    if(modalEl) {
      specialtyModal = new bootstrap.Modal(modalEl);
    }
    
    const shopModalEl = document.getElementById('shopModal');
    if(shopModalEl) {
      shopModal = new bootstrap.Modal(shopModalEl);
    }
    
        // Load specialties when tab is shown
    const specialtiesTab = document.getElementById('specialties-tab');
    if(specialtiesTab) {
      specialtiesTab.addEventListener('shown.bs.tab', function(){
        if(typeof window.get_specialties === 'function') {
          window.get_specialties();
        }
      });
    }

    // Load on page load if tab is active
    setTimeout(function(){
      const activePane = document.querySelector('#specialties-pane.active.show');
      if(activePane && typeof window.get_specialties === 'function') {
        window.get_specialties();
      }
    }, 200);
  }

  // Edit specialty
  window.editSpecialty = function(id){
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/destinations.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function(){
      if(xhr.status === 200){
        try {
          let res = JSON.parse(xhr.responseText);
          if(res.status === 'success'){
            let data = res.data;
            
            document.getElementById('specialty_id').value = data.id;
            document.querySelector('#specialty-form [name="name"]').value = data.name || '';
            document.querySelector('#specialty-form [name="description"]').value = data.description || '';
            document.querySelector('#specialty-form [name="short_description"]').value = data.short_description || '';
            document.querySelector('#specialty-form [name="category"]').value = data.category || 'food';
            document.querySelector('#specialty-form [name="price_range"]').value = data.price_range || '';
            document.querySelector('#specialty-form [name="best_season"]').value = data.best_season || '';
            document.querySelector('#specialty-form [name="location"]').value = data.location || '';
            document.querySelector('#specialty-form [name="latitude"]').value = data.latitude || '';
            document.querySelector('#specialty-form [name="longitude"]').value = data.longitude || '';
            document.querySelector('#specialty-form [name="rating"]').value = data.rating || 0;
            document.querySelector('#specialty-form [name="review_count"]').value = data.review_count || 0;
            document.querySelector('#specialty-form [name="active"]').value = data.active || 1;
            
            // Show existing images
            let existingImagesDiv = document.getElementById('specialty-existing-images');
            if(existingImagesDiv && data.images && data.images.length > 0){
              let html = '<div class="mb-2"><strong>Ảnh hiện có:</strong></div><div class="row g-2">';
              data.images.forEach(function(img){
                html += `<div class="col-3">
                  <div class="position-relative">
                    <img src="${img.image}" class="img-thumbnail" style="width:100%;height:80px;object-fit:cover;">
                    ${img.is_primary ? '<span class="badge bg-primary position-absolute top-0 start-0">Chính</span>' : ''}
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="deleteSpecialtyImage(${img.id}, ${data.id})">
                      <i class="bi bi-x"></i>
                    </button>
                  </div>
                </div>`;
              });
              html += '</div>';
              existingImagesDiv.innerHTML = html;
            } else {
              existingImagesDiv.innerHTML = '';
            }
            
            specialtyModal.show();
          } else {
            showResponseAlert(res, 'Co loi xay ra');
          }
        } catch(e) {
          alert('error', 'Loi: ' + xhr.responseText);
        }
      }
    };
    
    xhr.send('get_single_specialty=' + id);
  };

  // Toggle specialty
  window.toggleSpecialty = function(id, val){
    if(confirm('Bạn có chắc muốn thay đổi trạng thái?')){
      let xhr = new XMLHttpRequest();
      xhr.open('POST', 'ajax/destinations.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      
      xhr.onload = function(){
        if(xhr.status === 200){
          try {
            let res = JSON.parse(xhr.responseText);
            showResponseAlert(res, 'Co loi xay ra');
            if(res.status === 'success'){
              window.get_specialties();
            }
          } catch(e) {
            alert('error', 'Loi: ' + xhr.responseText);
          }
        }
      };
      
      xhr.send('toggle_specialty=' + id + '&value=' + val);
    }
  };

  // Delete specialty
  window.deleteSpecialty = function(id){
    if(confirm('Bạn có chắc muốn xóa đặc sản này?')){
      let xhr = new XMLHttpRequest();
      xhr.open('POST', 'ajax/destinations.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      
      xhr.onload = function(){
        if(xhr.status === 200){
          try {
            let res = JSON.parse(xhr.responseText);
            showResponseAlert(res, 'Co loi xay ra');
            if(res.status === 'success'){
              window.get_specialties();
            }
          } catch(e) {
            alert('error', 'Loi: ' + xhr.responseText);
          }
        }
      };
      
      xhr.send('delete_specialty=' + id);
    }
  };

  // Save specialty form
  const specialtyForm = document.getElementById('specialty-form');
  if(specialtyForm) {
    specialtyForm.addEventListener('submit', function(e){
      e.preventDefault();
      
      let data = new FormData(this);
      data.append('save_specialty', '1');
      
      let xhr = new XMLHttpRequest();
      xhr.open('POST', 'ajax/destinations.php', true);
      
      xhr.onload = function(){
        if(xhr.status === 200){
          try {
            let res = JSON.parse(xhr.responseText);
            if(res.message){
              showResponseAlert(res, 'Co loi xay ra');
            } else if(res.status){
              alert(res.status === 'success' ? 'success' : 'error', res.status === 'success' ? 'Luu thanh cong' : 'Co loi xay ra');
            } else {
              alert('error', 'Phan hoi khong hop le tu server');
            }
            if(res.status === 'success'){
              specialtyModal.hide();
              specialtyForm.reset();
              document.getElementById('specialty-existing-images').innerHTML = '';
              document.getElementById('specialty-images-preview').innerHTML = '';
              window.get_specialties();
            }
          } catch(e) {
            console.error('Parse error:', e);
            console.error('Response:', xhr.responseText);
            alert('error', 'Loi khi xu ly phan hoi: ' + e.message);
          }
        } else {
          alert('error', 'Loi server: ' + xhr.status);
        }
      };
      
      xhr.onerror = function(){
        alert('error', 'Loi ket noi den server');
      };
      
      xhr.send(data);
    });
  }

  // Preview specialty images
  const specialtyImagesInput = document.getElementById('specialty_images');
  if(specialtyImagesInput) {
    specialtyImagesInput.addEventListener('change', function(e){
      let previewDiv = document.getElementById('specialty-images-preview');
      previewDiv.innerHTML = '';
      
      if(this.files && this.files.length > 0){
        Array.from(this.files).forEach(function(file){
          if(file.type.startsWith('image/')){
            let reader = new FileReader();
            reader.onload = function(e){
              let div = document.createElement('div');
              div.className = 'col-3';
              div.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="width:100%;height:80px;object-fit:cover;">`;
              previewDiv.appendChild(div);
            };
            reader.readAsDataURL(file);
          }
        });
      }
    });
  }

  // Manage shops
  window.manageShops = function(specialtyId){
    document.getElementById('shop_specialty_id').value = specialtyId;
    document.getElementById('shop_id').value = '';
    document.getElementById('shop-form').reset();
    document.getElementById('shop_specialty_id').value = specialtyId;
    
    // Load shops
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/destinations.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function(){
      if(xhr.status === 200){
        try {
          let res = JSON.parse(xhr.responseText);
          // TODO: Display shops list in modal
          shopModal.show();
        } catch(e) {
          shopModal.show();
        }
      }
    };
    
    xhr.send('get_specialty_shops=' + specialtyId);
  };

  // Delete specialty image
  window.deleteSpecialtyImage = function(imageId, specialtyId){
    if(confirm('Bạn có chắc muốn xóa ảnh này?')){
      let xhr = new XMLHttpRequest();
      xhr.open('POST', 'ajax/destinations.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      
      xhr.onload = function(){
        if(xhr.status === 200){
          try {
            let res = JSON.parse(xhr.responseText);
            showResponseAlert(res, 'Co loi xay ra');
            if(res.status === 'success'){
              window.editSpecialty(specialtyId);
            }
          } catch(e) {
            alert('error', 'Loi: ' + xhr.responseText);
          }
        }
      };
      
      xhr.send('delete_specialty_image=1&image_id=' + imageId);
    }
  };

  // Initialize on DOM ready
  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', function(){
      initSpecialties();
    });
  } else {
    // DOM already loaded
    initSpecialties();
  }
  
}






