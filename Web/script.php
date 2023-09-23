<?php $logo_license= base64_encode(file_get_contents("./ugv-logo-cafe.png"));
if (isset($_GET['img'])) {
    $img = strtolower($_GET['img']);
    header("Content-Type: image/png");
    if($img == 'logo'){
        echo base64_decode($logo_license);
    }else{
        header("HTTP/1.0 403");
    }
}else{
    header("HTTP/1.0 403");
}