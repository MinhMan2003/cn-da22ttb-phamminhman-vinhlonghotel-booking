<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<script>

  function alert(type,msg,position='body')
  {
    // Xóa alert cũ nếu có để tránh trùng lặp
    const existingAlerts = document.querySelectorAll('.custom-alert');
    existingAlerts.forEach(alert => {
      alert.remove();
    });
    
    let bs_class = (type == 'success') ? 'alert-success' : 'alert-danger';
    let element = document.createElement('div');
    element.innerHTML = `
      <div class="alert ${bs_class} alert-dismissible fade show" role="alert">
        <strong class="me-3">${msg}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    `;

    if(position=='body'){
      document.body.append(element);
      element.classList.add('custom-alert');
    }
    else{
      document.getElementById(position).appendChild(element);
    }
    setTimeout(remAlert, 3000);
  }
  function showAlertBox(type, msg, position='body'){
  alert(type, msg, position);
}


  function remAlert(){
    const ca = document.getElementsByClassName('custom-alert');
    if(ca.length){
      ca[0].remove();
      return;
    }
    const al = document.getElementsByClassName('alert');
    if(al.length){
      al[0].remove();
    }
  }

    
  function setActive()
  {
    let navbar = document.getElementById('dashboard-menu');
    // #region agent log
    fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/inc/scripts.php:setActive',message:'Checking dashboard-menu element',data:{navbarExists:!!navbar,pageUrl:window.location.href},timestamp:Date.now(),sessionId:'debug-session',runId:'run3',hypothesisId:'E'})}).catch(()=>{});
    // #endregion
    if(!navbar) return; // Exit if element doesn't exist (e.g., on user-facing pages)
    
    // Nếu đã có script active-menu riêng trong header, không chạy logic cũ này
    // (owner pages có script riêng trong owner/inc/header.php)
    if (navbar.closest('.col-lg-2') && window.location.pathname.includes('/owner/')) {
      // #region agent log
      fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/inc/scripts.php:setActive',message:'Skipping setActive for owner pages',data:{pathname:window.location.pathname},timestamp:Date.now(),sessionId:'debug-session',runId:'run3',hypothesisId:'E'})}).catch(()=>{});
      // #endregion
      return;
    }
    
    // Xóa tất cả active classes trước
    let a_tags = navbar.getElementsByTagName('a');
    for(i=0; i<a_tags.length; i++) {
      a_tags[i].classList.remove('active');
    }
    
    // #region agent log
    fetch('http://127.0.0.1:7243/ingest/90479396-6b9c-4521-a5fa-cb43f6361cb1',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'admin/inc/scripts.php:setActive',message:'getElementsByTagName called successfully',data:{aTagsCount:a_tags.length},timestamp:Date.now(),sessionId:'debug-session',runId:'run3',hypothesisId:'E'})}).catch(()=>{});
    // #endregion

    // Lấy file name hiện tại (chính xác)
    let currentPath = document.location.pathname;
    let currentFile = currentPath.split('/').pop() || '';
    let currentFileName = currentFile.split('.')[0];

    let foundMatch = null;
    for(i=0; i<a_tags.length; i++)
    {
      try {
        let linkUrl = new URL(a_tags[i].href);
        let linkPath = linkUrl.pathname;
        let linkFile = linkPath.split('/').pop() || '';
        let linkFileName = linkFile.split('.')[0];
        
        // So sánh CHÍNH XÁC file name (không phải substring)
        if (linkFile === currentFile && linkFile !== '' && currentFile !== '') {
          if (!foundMatch) {
            foundMatch = a_tags[i];
          }
        }
      } catch(e) {
        // Fallback cho relative URLs
        let href = a_tags[i].getAttribute('href') || a_tags[i].href || '';
        let linkFile = href.split('/').pop() || '';
        if (linkFile === currentFile && linkFile !== '' && currentFile !== '') {
          if (!foundMatch) {
            foundMatch = a_tags[i];
          }
        }
      }
    }
    
    // Chỉ highlight 1 item duy nhất
    if (foundMatch) {
      foundMatch.classList.add('active');
    }
  }
  setActive();

// Old chat functions - only run if elements exist (for backward compatibility)
function loadChat(){
    // Only run if chat_load.php exists and chatMessages element exists without data-user-id or data-type
    let box = document.getElementById('chatMessages');
    if(box && !box.hasAttribute('data-user-id') && !box.hasAttribute('data-type')) {
        fetch('ajax/chat_load.php')
        .then(r=>{
            if(r.ok) {
                return r.text();
            }
            return Promise.reject('chat_load.php not found');
        })
        .then(d=>{
            if(box) {
                box.innerHTML = d;
                box.scrollTop = box.scrollHeight;
            }
        })
        .catch(()=>{
            // Silently fail if chat_load.php doesn't exist
        });
    }
}

// Only set interval if old chat system elements exist
let chatSend = document.getElementById('chatSend');
let chatText = document.getElementById('chatText');
let chatInterval = null; // Store interval ID to prevent duplicates

if(chatSend && chatText) {
    // Clear any existing interval first
    if(chatInterval) {
        clearInterval(chatInterval);
    }
    chatInterval = setInterval(loadChat, 2000);
    loadChat();
    
    chatSend.onclick = function(){
        let msg = chatText.value.trim();
        if(msg === "") return;

        let f = new FormData();
        f.append('message', msg);

        fetch('ajax/chat_send.php', {method:'POST', body:f})
        .then(r=>r.text())
        .then(()=>{
            chatText.value = "";
            loadChat();
        })
        .catch(()=>{
            // Silently fail if chat_send.php doesn't exist
        });
    };
}


</script>
