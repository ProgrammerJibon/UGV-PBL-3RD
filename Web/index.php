<?php
require_once("./functions.php");
// $localIP = getHostByName(getHostName());
// echo "Local IP address: " . $localIP;

// $domainName = $_SERVER['SERVER_NAME'];
// echo "<br>Domain name: " . $domainName;

// start - test purpose
// if (!isset($_SESSION['changeIP'])) {
//     if(@file_get_contents("https://www.jibon.io/verify_license.php?username=jhon1234&last_ip=$localIP")){
//         $_SESSION['changeIP'] = $localIP;
//     }else{
//         exit("No internet!");
//     }    
// }elseif($_SESSION['changeIP'] != $localIP){
//     if(@file_get_contents("https://www.jibon.io/verify_license.php?username=jhon1234&last_ip=$localIP")){
//         $_SESSION['changeIP'] = $localIP;
//     }else{
//         exit("No internet!");
//     }
// }
// end - test purpose




$user_id = 1;
if(isset($_GET['page'])){
    $uri = explode("/",strtolower($_GET['page']));
    $page = $uri[0];
    isset($uri[1]) ? $path = $uri[1] : $path = NULL;
    if($page == 'home'){
        $admin?require_once('./page-home.php'):header("Location: /login");
    }else if($page == 'test'){
        require_once('./page-test.php');
    }else if($page == 'groups'){
        $admin?require_once('./page-list-groups.php'):header("HTTP/1.0 403");
    }else if($page == 'items'){
        $admin?require_once('./page-list-items.php'):header("HTTP/1.0 403");
    }else if($page == 'order-history'){
        $admin?require_once('./page-order-history.php'):header("HTTP/1.0 403");
    }else if($page == 'settings'){
        require_once('./page-settings.php');
    }else if($page == 'json'){
        require_once('./json-data.php');
    }else if($page == 'login'){
        require_once "page-login.php";
    }else if($page == 'students'){
        $admin?require_once('./page-list-students.php'):header("HTTP/1.0 403");
    }else if($page == 'add-event'){
        $admin?($user_id?require_once('./page-add-event.php'):header("Location: /auth")):header("HTTP/1.0 403");
    }else if($page == 'logout'){
        $_SESSION['admin'] = false;
        header("Location: /");
        exit();
    }else{
        echo date("Y-m-d-", 1688474141);
        echo time()+(86400*60);
    }
}else{
    header("location: /home");
}