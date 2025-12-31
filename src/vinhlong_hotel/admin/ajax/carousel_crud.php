<?php
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

/* ================== ADD IMAGE ================== */
if(isset($_POST['add_image']))
{
    $img_r = uploadImage($_FILES['picture'], CAROUSEL_FOLDER);

    if($img_r == 'inv_img'){
        echo 'inv_img';
    }
    else if($img_r == 'inv_size'){
        echo 'inv_size';
    }
    else if($img_r == 'upd_failed'){
        echo 'upd_failed';
    }
    else{
        $q = "INSERT INTO carousel (image) VALUES (?)";
        $values = [$img_r];
        echo insert($q,$values,'s');
    }
}

/* ================== GET LIST ================== */
if(isset($_POST['get_carousel']))
{
    $res = selectAll('carousel');
    $path = CAROUSEL_IMG_PATH;

    $html = "";

    while($row = mysqli_fetch_assoc($res))
    {
        $html .= <<<item
        <div class="col-md-4">
            <div class="card shadow-sm">

                <div class="carousel-box">
                    <img src="$path$row[image]" alt="">
                </div>

                <div class="card-body text-end">
                    <button class="btn btn-danger btn-sm" onclick="rem_image($row[sr_no])">
                        <i class="bi bi-trash-fill"></i> Xoá
                    </button>
                </div>

            </div>
        </div>
        item;
    }

    echo $html;
}

/* ================== DELETE IMAGE ================== */
if(isset($_POST['rem_image']))
{
    $id = filteration($_POST)['rem_image'];

    $pre = select("SELECT image FROM carousel WHERE sr_no=?", [$id], "i");
    $img = mysqli_fetch_assoc($pre)['image'];

    if(deleteImage($img, CAROUSEL_FOLDER)){
        echo delete("DELETE FROM carousel WHERE sr_no=?", [$id], "i");
    } else {
        echo 0;
    }
}
?>
