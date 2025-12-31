let carousel_s_form = document.getElementById('carousel_s_form');
let carousel_picture_inp = document.getElementById('carousel_picture_inp');

carousel_s_form.addEventListener('submit', function(e){
    e.preventDefault();
    add_image();
});

function add_image(){
    let data = new FormData();
    data.append('picture', carousel_picture_inp.files[0]);
    data.append('add_image','');

    let xhr = new XMLHttpRequest();
    xhr.open("POST","ajax/carousel_crud.php",true);

    xhr.onload = function(){
        let modal = bootstrap.Modal.getInstance(document.getElementById('carousel-s'));
        modal.hide();

        let resp = this.responseText.trim();

        if(resp === 'inv_img'){
            alert('error','Chỉ cho phép JPG, PNG, WEBP!');
        } else if(resp === 'inv_size'){
            alert('error','Dung lượng tối đa 10MB!');
        } else if(resp === 'upd_failed'){
            alert('error','Tải ảnh thất bại!');
        } else {
            alert('success','Đã thêm hình ảnh!');
            carousel_picture_inp.value='';
            get_carousel();
        }
    };

    xhr.send(data);
}

function get_carousel(){
    let xhr = new XMLHttpRequest();
    xhr.open("POST","ajax/carousel_crud.php",true);
    xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');

    xhr.onload = function(){
        document.getElementById('carousel-data').innerHTML = this.responseText;
    };

    xhr.send('get_carousel');
}

function rem_image(id){
    let xhr = new XMLHttpRequest();
    xhr.open("POST","ajax/carousel_crud.php",true);
    xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');

    xhr.onload = function(){
        if(this.responseText == 1){
            alert('success','Đã xoá ảnh!');
            get_carousel();
        } else {
            alert('error','Không thể xoá ảnh!');
        }
    };

    xhr.send('rem_image='+id);
}

window.onload = function(){
    get_carousel();
};
