let general_data, contacts_data;

let general_s_form, site_title_inp, site_about_inp, site_logo_inp;
let contacts_s_form;
let team_s_form, member_name_inp, member_picture_inp;

// Đảm bảo DOM đã load trước khi lấy elements
function initElements() {
    general_s_form = document.getElementById('general_s_form');
    site_title_inp = document.getElementById('site_title_inp');
    site_about_inp = document.getElementById('site_about_inp');
    site_logo_inp = document.getElementById('site_logo_inp');
    contacts_s_form = document.getElementById('contacts_s_form');
    team_s_form = document.getElementById('team_s_form');
    member_name_inp = document.getElementById('member_name_inp');
    member_picture_inp = document.getElementById('member_picture_inp');
    
    // Contacts form inputs (cần cho contacts form handler)
    window.address_inp = document.getElementById('address_inp');
    window.gmap_inp = document.getElementById('gmap_inp');
    window.pn1_inp = document.getElementById('pn1_inp');
    window.email_inp = document.getElementById('email_inp');
    window.fb_inp = document.getElementById('fb_inp');
    window.insta_inp = document.getElementById('insta_inp');
    window.tw_inp = document.getElementById('tw_inp');
    window.iframe_inp = document.getElementById('iframe_inp');
    
    console.log('Elements initialized:', {
        general_s_form: !!general_s_form,
        site_title_inp: !!site_title_inp,
        site_about_inp: !!site_about_inp,
        contacts_s_form: !!contacts_s_form,
        team_s_form: !!team_s_form
    });
}

/* ======================= GENERAL ======================= */
function get_general() {

  let xhr = new XMLHttpRequest();
  xhr.open("POST","ajax/settings_crud.php",true);
  xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");

  xhr.onload = function(){
      general_data = JSON.parse(this.responseText);
      document.getElementById('site_title').innerText = general_data.site_title;
      document.getElementById('site_about').innerText = general_data.site_about;

      site_title_inp.value = general_data.site_title;
      site_about_inp.value = general_data.site_about;
      
      // Hiển thị logo
      const logoImg = document.getElementById('site_logo_img');
      const logoText = document.getElementById('site_logo_text');
      const logoPreviewImg = document.getElementById('logo_preview_img');
      
      if(general_data.site_logo && general_data.site_logo.trim() !== '') {
        const logoPath = '../images/about/' + general_data.site_logo;
        logoImg.src = logoPath;
        logoImg.style.display = 'block';
        logoText.style.display = 'none';
        logoPreviewImg.src = logoPath;
        logoPreviewImg.style.display = 'block';
      } else {
        // Kiểm tra logo mặc định - thử từng logo
        const defaultLogos = ['../logo/Vĩnh Long Hotel.png', '../logo/logo.png', '../images/logo.png'];
        let logoFound = false;
        
        defaultLogos.forEach(function(logoPath, index) {
          if(logoFound) return;
          
          const testImg = new Image();
          testImg.onload = function() {
            if(!logoFound) {
              logoImg.src = logoPath;
              logoImg.style.display = 'block';
              logoText.style.display = 'none';
              logoPreviewImg.src = logoPath;
              logoPreviewImg.style.display = 'block';
              logoFound = true;
            }
          };
          testImg.onerror = function() {
            // Nếu là logo cuối cùng và không tìm thấy, ẩn logo
            if(index === defaultLogos.length - 1 && !logoFound) {
              logoImg.style.display = 'none';
              logoText.style.display = 'block';
              logoPreviewImg.style.display = 'none';
            }
          };
          testImg.src = logoPath;
        });
      }

      let shutdownToggle = document.getElementById('shutdown-toggle');
let shutdownLabel = document.getElementById('shutdown-status');

shutdownToggle.checked = (general_data.shutdown == 1);

// Cập nhật label
if (shutdownToggle.checked) {
    shutdownLabel.innerHTML = "⚠️ Hệ thống Đang Bảo Trì";
    shutdownLabel.classList.add("active-danger");
} else {
    shutdownLabel.innerHTML = "Hệ thống Bình Thường";
    shutdownLabel.classList.remove("active-danger");
}

  }

  xhr.send("get_general");
}


// Khởi tạo form submit handler - đợi DOM sẵn sàng
function initFormHandlers() {
    initElements();
    
    console.log('Initializing form handlers...');
    console.log('general_s_form:', general_s_form);
    console.log('contacts_s_form:', contacts_s_form);
    console.log('team_s_form:', team_s_form);
    
    // Preview logo khi chọn file
    if(site_logo_inp) {
        site_logo_inp.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImg = document.getElementById('logo_preview_img');
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // General form handler
    if(general_s_form) {
        console.log('Adding submit listener to general_s_form');
        general_s_form.addEventListener('submit', e=>{
    e.preventDefault();
    
    try {
        // Kiểm tra xem có file logo được chọn không
        const hasLogoFile = site_logo_inp && site_logo_inp.files && site_logo_inp.files.length > 0;
        
        if(hasLogoFile) {
            // Sử dụng FormData để upload file
            let data = new FormData();
            data.append("upd_general", "1");
            data.append("site_title", site_title_inp.value);
            data.append("site_about", site_about_inp.value);
            data.append("site_logo", site_logo_inp.files[0]);
            
            let xhr = new XMLHttpRequest();
            xhr.open("POST","ajax/settings_crud.php",true);

            xhr.onload = function(){
                bootstrap.Modal.getInstance(
                    document.getElementById('general-s')
                ).hide();

                const response = this.responseText.trim();
                const responseNum = parseInt(response);
                const isNumeric = !isNaN(responseNum);
                
                if (isNumeric && responseNum >= 0){
                    if (responseNum === 0) {
                        alert("warning", "Không có thay đổi nào được thực hiện.");
                    } else {
                        alert("success","Lưu thành công!");
                        get_general();
                        if(site_logo_inp) site_logo_inp.value = '';
                    }
                } else if (isNumeric && responseNum === -1) {
                    alert("warning", "Không có thay đổi nào được thực hiện. Dữ liệu mới giống với dữ liệu cũ.");
                    get_general();
                } else {
                    alert("error","Lỗi: " + response);
                }
            }
            
            xhr.onerror = function(){
                alert("error","Lỗi kết nối!");
            }

            xhr.send(data);
        } else {
            // Không có file, gửi như cũ
            let send = "upd_general=1&site_title=" + encodeURIComponent(site_title_inp.value) + 
                       "&site_about=" + encodeURIComponent(site_about_inp.value);
            
            let xhr = new XMLHttpRequest();
            xhr.open("POST","ajax/settings_crud.php",true);
            xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
            
            xhr.onload = function(){
                bootstrap.Modal.getInstance(
                    document.getElementById('general-s')
                ).hide();

                const response = this.responseText.trim();
                const responseNum = parseInt(response);
                const isNumeric = !isNaN(responseNum);
                
                if (isNumeric && responseNum >= 0){
                    if (responseNum === 0) {
                        alert("warning", "Không có thay đổi nào được thực hiện.");
                    } else {
                        alert("success","Lưu thành công!");
                        get_general();
                    }
                } else if (isNumeric && responseNum === -1) {
                    alert("warning", "Không có thay đổi nào được thực hiện. Dữ liệu mới giống với dữ liệu cũ.");
                    get_general();
                } else {
                    alert("error","Lỗi: " + response);
                }
            }
            
            xhr.onerror = function(){
                alert("error","Lỗi kết nối!");
            }

            xhr.send(send);
        }
    } catch(error) {
        console.error('ERROR in form submit:', error);
        alert("error", "Lỗi: " + error.message);
    }
        });
    } else {
        console.error('ERROR: general_s_form not found!');
    }
    
    // Contacts form handler
    if(contacts_s_form) {
        console.log('Adding submit listener to contacts_s_form');
        contacts_s_form.addEventListener('submit', e=>{
            e.preventDefault();

            let send = 
                "upd_contacts&address="+address_inp.value+
                "&gmap="+gmap_inp.value+
                "&pn1="+pn1_inp.value+
                "&email="+email_inp.value+
                "&fb="+fb_inp.value+
                "&insta="+insta_inp.value+
                "&tw="+tw_inp.value+
                "&iframe="+iframe_inp.value;

            let xhr = new XMLHttpRequest();
            xhr.open("POST","ajax/settings_crud.php",true);
            xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");

            xhr.onload = function(){
                bootstrap.Modal.getInstance(
                    document.getElementById('contacts-s')
                ).hide();

                if (this.responseText == 1){
                    alert("success","Cập nhật thành công!");
                    get_contacts();
                } else {
                    alert("error","Không thay đổi!");
                }
            }

            xhr.send(send);
        });
    } else {
        console.error('ERROR: contacts_s_form not found!');
    }
    
    // Team form handler
    if(team_s_form) {
        console.log('Adding submit listener to team_s_form');
        team_s_form.addEventListener('submit', e=>{
            e.preventDefault();
            let data = new FormData();
            data.append("add_member","");
            data.append("name",member_name_inp.value);
            data.append("picture",member_picture_inp.files[0]);

            let xhr = new XMLHttpRequest();
            xhr.open("POST","ajax/settings_crud.php",true);

            xhr.onload = function(){
                bootstrap.Modal.getInstance(
                    document.getElementById('team-s')
                ).hide();

                if (['inv_img','inv_size','upd_failed'].includes(this.responseText)){
                    alert("error", this.responseText);
                } else {
                    alert("success","Đã thêm thành viên!");
                    member_name_inp.value="";
                    member_picture_inp.value="";
                    get_members();
                }
            }

            xhr.send(data);
        });
    } else {
        console.error('ERROR: team_s_form not found!');
    }
}

/* ======================= SHUTDOWN ======================= */
function upd_shutdown() {
    let toggle = document.getElementById('shutdown-toggle');
    let status = document.getElementById('shutdown-status');
    let val = toggle.checked ? 1 : 0;

    // Cập nhật giao diện trước
    if(toggle.checked){
        status.innerHTML = "⚠️ Hệ thống Đang Bảo Trì";
        status.classList.add('active-danger');
    } else {
        status.innerHTML = "Hệ thống Đang Tắt Bảo Trì";
        status.classList.remove('active-danger');
    }

    let xhr = new XMLHttpRequest();
    xhr.open("POST","ajax/settings_crud.php",true);
    xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");

    xhr.onload = function(){
        if (this.responseText == 1){
            toast("success", toggle.checked ? 
                "Chế độ bảo trì đã BẬT!" :
                "Chế độ bảo trì đã TẮT!"
            );
        } else {
            toast("error","Không thể cập nhật trạng thái!");
        }
    }

    xhr.send("upd_shutdown="+val);
}


/* ======================= CONTACTS ======================= */

function get_contacts() {

    let xhr = new XMLHttpRequest();
    xhr.open("POST","ajax/settings_crud.php",true);
    xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");

    xhr.onload = function(){
        contacts_data = JSON.parse(this.responseText);

        document.getElementById('address').innerText = contacts_data.address;
        document.getElementById('gmap').innerText = contacts_data.gmap;
        document.getElementById('pn1').innerText = contacts_data.pn1;
        document.getElementById('email').innerText = contacts_data.email;
        document.getElementById('fb').innerText = contacts_data.fb;
        document.getElementById('insta').innerText = contacts_data.insta;
        document.getElementById('tw').innerText = contacts_data.tw;

        document.getElementById('iframe').src = contacts_data.iframe;

        // Đổ vào form
        document.getElementById('address_inp').value = contacts_data.address;
        document.getElementById('gmap_inp').value = contacts_data.gmap;
        document.getElementById('pn1_inp').value = contacts_data.pn1;
        document.getElementById('email_inp').value = contacts_data.email;
        document.getElementById('fb_inp').value = contacts_data.fb;
        document.getElementById('insta_inp').value = contacts_data.insta;
        document.getElementById('tw_inp').value = contacts_data.tw;
        document.getElementById('iframe_inp').value = contacts_data.iframe;
    }

    xhr.send("get_contacts");
}

function get_members(){
    let xhr = new XMLHttpRequest();
    xhr.open("POST","ajax/settings_crud.php",true);
    xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");

    xhr.onload = function(){
        document.getElementById('team-data').innerHTML = this.responseText;
    }

    xhr.send("get_members");
}

function rem_member(id){
    let xhr = new XMLHttpRequest();
    xhr.open("POST","ajax/settings_crud.php",true);
    xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");

    xhr.onload = function(){
        if (this.responseText == 1){
            alert("success","Đã xóa!");
            get_members();
        } else {
            alert("error","Không thể xóa!");
        }
    }

    xhr.send("rem_member="+id);
}

/* ======================= INIT ======================= */
window.onload = function(){
    initElements();
    initFormHandlers();
    get_general();
    get_contacts();
    get_members();
}
