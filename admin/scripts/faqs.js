// FAQs Management JavaScript
(function() {
  'use strict';
  
  let faqModal;
  let isInitialized = false;
  
  function init() {
    if(isInitialized) return;
    isInitialized = true;
    
    const modalEl = document.getElementById('faqModal');
    if(modalEl) {
      faqModal = new bootstrap.Modal(modalEl);
    }
    
    // Load FAQs on page load
    get_faqs();
    
    // Form submit
    const faqForm = document.getElementById('faq-form');
    if(faqForm) {
      faqForm.addEventListener('submit', function(e) {
        e.preventDefault();
        save_faq();
      });
    }
    
    // Reset form when modal is closed
    if(modalEl) {
      modalEl.addEventListener('hidden.bs.modal', function() {
        resetForm();
      });
    }
  }
  
  window.get_faqs = function() {
    const dataEl = document.getElementById('faq-data');
    if(!dataEl) return;
    
    dataEl.innerHTML = '<tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm me-2"></div>Đang tải...</td></tr>';
    
    fetch('ajax/faqs.php?get_faqs=1')
      .then(res => res.json())
      .then(data => {
        if(data.status === 'success') {
          dataEl.innerHTML = data.html || '<tr><td colspan="8" class="text-center text-muted py-4">Chưa có FAQ nào</td></tr>';
        } else {
          dataEl.innerHTML = '<tr><td colspan="8" class="text-center text-danger py-4">Lỗi: ' + (data.message || 'Không thể tải dữ liệu') + '</td></tr>';
        }
      })
      .catch(err => {
        console.error('Error loading FAQs:', err);
        dataEl.innerHTML = '<tr><td colspan="8" class="text-center text-danger py-4">Lỗi kết nối</td></tr>';
      });
  };
  
  window.editFaq = function(id) {
    fetch('ajax/faqs.php?get_single=1&id=' + id)
      .then(res => res.json())
      .then(data => {
        if(data.status === 'success' && data.faq) {
          const f = data.faq;
          document.getElementById('faq_id').value = f.id;
          document.getElementById('faq-form').querySelector('[name="question"]').value = f.question || '';
          document.getElementById('faq-form').querySelector('[name="answer"]').value = f.answer || '';
          document.getElementById('faq-form').querySelector('[name="keywords"]').value = f.keywords || '';
          document.getElementById('faq-form').querySelector('[name="category"]').value = f.category || 'general';
          document.getElementById('faq-form').querySelector('[name="priority"]').value = f.priority || 0;
          document.getElementById('faq-form').querySelector('[name="active"]').value = f.active || 1;
          
          if(faqModal) faqModal.show();
        } else {
          alert('Không tìm thấy FAQ');
        }
      })
      .catch(err => {
        console.error('Error loading FAQ:', err);
        alert('Lỗi khi tải dữ liệu');
      });
  };
  
  window.toggleFaq = function(id, newStatus) {
    if(!confirm('Bạn có chắc muốn ' + (newStatus == 1 ? 'hiển thị' : 'ẩn') + ' FAQ này?')) return;
    
    const formData = new FormData();
    formData.append('toggle_faq', '1');
    formData.append('id', id);
    formData.append('active', newStatus);
    
    fetch('ajax/faqs.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if(data.status === 'success') {
        get_faqs();
        showAlert('success', 'Đã cập nhật trạng thái');
      } else {
        showAlert('danger', data.message || 'Lỗi khi cập nhật');
      }
    })
    .catch(err => {
      console.error('Error toggling FAQ:', err);
      showAlert('danger', 'Lỗi kết nối');
    });
  };
  
  window.deleteFaq = function(id) {
    if(!confirm('Bạn có chắc muốn xóa FAQ này?')) return;
    
    const formData = new FormData();
    formData.append('delete_faq', '1');
    formData.append('id', id);
    
    fetch('ajax/faqs.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if(data.status === 'success') {
        get_faqs();
        showAlert('success', 'Đã xóa FAQ');
      } else {
        showAlert('danger', data.message || 'Lỗi khi xóa');
      }
    })
    .catch(err => {
      console.error('Error deleting FAQ:', err);
      showAlert('danger', 'Lỗi kết nối');
    });
  };
  
  function save_faq() {
    const form = document.getElementById('faq-form');
    if(!form) return;
    
    const formData = new FormData(form);
    formData.append('save_faq', '1');
    
    const submitBtn = form.querySelector('button[type="submit"]');
    if(submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';
    }
    
    fetch('ajax/faqs.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if(submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Lưu';
      }
      
      if(data.status === 'success') {
        if(faqModal) faqModal.hide();
        get_faqs();
        showAlert('success', 'Đã lưu FAQ');
        resetForm();
      } else {
        showAlert('danger', data.message || 'Lỗi khi lưu');
      }
    })
    .catch(err => {
      console.error('Error saving FAQ:', err);
      if(submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Lưu';
      }
      showAlert('danger', 'Lỗi kết nối');
    });
  }
  
  function resetForm() {
    const form = document.getElementById('faq-form');
    if(form) {
      form.reset();
      document.getElementById('faq_id').value = '';
      document.getElementById('faq-form').querySelector('[name="category"]').value = 'general';
      document.getElementById('faq-form').querySelector('[name="priority"]').value = 0;
      document.getElementById('faq-form').querySelector('[name="active"]').value = 1;
    }
  }
  
  function showAlert(type, message) {
    const alertEl = document.getElementById('faq-alert');
    if(alertEl) {
      alertEl.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>`;
      setTimeout(() => {
        const alert = alertEl.querySelector('.alert');
        if(alert) {
          bootstrap.Alert.getOrCreateInstance(alert).close();
        }
      }, 3000);
    }
  }
  
  window.import100Faqs = function() {
    if(!confirm('Bạn có chắc muốn import 100 FAQs? Các FAQ đã tồn tại sẽ được bỏ qua.')) return;
    
    const importBtn = document.getElementById('importBtn');
    if(importBtn) {
      importBtn.disabled = true;
      importBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang import...';
    }
    
    fetch('ajax/faqs.php?import_100_faqs=1')
      .then(res => res.json())
      .then(data => {
        if(importBtn) {
          importBtn.disabled = false;
          importBtn.innerHTML = '<i class="bi bi-download"></i> Import 100 FAQs';
        }
        
        if(data.status === 'success') {
          showAlert('success', data.message || 'Import thành công!');
          get_faqs(); // Reload FAQs list
        } else {
          showAlert('danger', data.message || 'Lỗi khi import');
        }
      })
      .catch(err => {
        console.error('Error importing FAQs:', err);
        if(importBtn) {
          importBtn.disabled = false;
          importBtn.innerHTML = '<i class="bi bi-download"></i> Import 100 FAQs';
        }
        showAlert('danger', 'Lỗi kết nối khi import');
      });
  };
  
  // Initialize on DOM ready
  if(document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

