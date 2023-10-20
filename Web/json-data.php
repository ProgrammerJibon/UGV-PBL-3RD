<?php

require_once("./functions.php");
header('Content-Type: application/json');

$result = array();
$result['foo'] = "bar";


if (isset($_REQUEST['studentsList'])) {
    $resultLists = array();
    foreach(studentsList() as $key){
        if(!$admin){
            $key['code'] = "••••••••";
        }
        $resultLists[] = $key;
    }
    $result['studentsList'] = $resultLists;

}

if(isset($_POST['open_orders'])){
    $sql = "SELECT * FROM `food_orders_list` WHERE (`status` = 'OPEN')  ORDER BY `food_orders_list`.`order_id` DESC";
    $result['open_orders'] = "";
    if ($query = @mysqli_query($connect, $sql)) {
        if(mysqli_num_rows($query) > 0){
            $result['open_orders'] .= "<div class='pageTitle'>Orders Open right now</div>";
        }
        $result['open_orders'] .= showOrdersContainer($query);
    }
}

if (isset($_REQUEST['groupList'])) {
    $result['groupList'] = groupList();
}

if (isset($_REQUEST['itemsList'])) {
    if(isset($_REQUEST['group_id'])){
        $result['itemsList'] = itemsList(addslashes($_REQUEST['group_id']));
    }else{
        $result['itemsList'] = itemsList();
    }
}
if (isset($_POST['deleteTable'])) {
    if(@mysqli_query($connect, "DELETE FROM `table_list` WHERE `table_list`.`table_id` = '".addslashes($_POST['deleteTable'])."'")){
        $result['deleteTable'] = true;
    }else{
        $result['deleteTable'] = false;
    }    
}

if (isset($_POST['deleteGroup'])) {
    @mysqli_query($connect, "UPDATE `food_items` SET `group_id` = '0' WHERE `food_items`.`group_id` = '".addslashes($_POST['deleteGroup'])."'");
    if(@mysqli_query($connect, "DELETE FROM `food_group` WHERE `food_group`.`group_id` = '".addslashes($_POST['deleteGroup'])."'")){
        $result['deleteGroup'] = true;
    }else{
        $result['deleteGroup'] = false;
    }    
}

if (isset($_REQUEST['removeOrderedItem'])) {
    $removeOrderedItem = addslashes($_REQUEST['removeOrderedItem']);
    if(@mysqli_query($connect, "DELETE FROM `food_orders_item` WHERE `food_orders_item`.`order_item_id` = '$removeOrderedItem'")){
        header("Location: /home");
    }    
}

if (isset($_POST['deleteItem'])) {
    if(@mysqli_query($connect, "DELETE FROM `food_items` WHERE `food_items`.`item_id` = '".addslashes($_POST['deleteItem'])."'")){
        $result['deleteItem'] = true;
    }else{
        $result['deleteItem'] = false;
    }    
}

if (isset($_POST['removeDevice'])) {
    if(@mysqli_query($connect, "UPDATE `students` SET `status` = 'BANNED' WHERE `students`.`id` = '".addslashes($_POST['removeDevice'])."'")){
        $result['removeDevice'] = true;
    }else{
        $result['removeDevice'] = false;
    }    
}

if (isset($uri[1]) && strtolower($uri[1]) == 'app') {
    $result['connectionResult'] = false;
    $result['student_name'] = "";
    $student_id = "";
    if (isset($_POST['student_phone'], $_POST['student_password'])) {
        $student_phone = addslashes($_POST['student_phone']);
        $student_password = addslashes(md5(sha1($_POST['student_password'])));
        $studentId = isset($_POST['studentId'])?sanitizeInput($_POST['studentId'], "NUMBER_ONLY"):"";
        $studentName = isset($_POST['studentName'])?sanitizeInput($_POST['studentName'], "TEXT_ONLY"):"";
        if($studentId != "" && $studentName != ""){
            @mysqli_query($connect, "INSERT INTO `students` (`id`, `student_id`, `student_phone`, `student_name`, `password`, `time`, `removed_time`, `status`) VALUES (NULL, '$studentId', '$student_phone', '$studentName', '$student_password', '$time', '0', 'VERIFIED')");
        }
        $resultConnection = studentsList($student_phone);
        if (isset($resultConnection[0]['student_phone'])) {
            $resultConnection = $resultConnection[0];
            if(isset($resultConnection['status']) && $resultConnection['status'] == "VERIFIED"){
                $result['student_name'] = $resultConnection['student_name'];
                $student_id =  $resultConnection['id'];
                if($resultConnection['password'] == $student_password){
                    $result['connectionResult'] = true;
                }
            }else if(isset($resultConnection['status']) && $resultConnection['status'] == "UNVERIFIED"){
                $result['new_student'] = true;
            }
        }else{
            $result['new_student'] = true;
        }
    }


    if (isset($_POST["syncTable"])) {
        $result['book_table'] = false;
        if ($student_id != "") {
            $checkBackOrder = mysqli_query($connect, "SELECT * FROM `food_orders_list` WHERE `status` = 'OPEN' AND `student_id` = '$student_id' LIMIT 1");
            if (mysqli_num_rows($checkBackOrder) > 0){
                foreach ($checkBackOrder as $key) {
                    $result['book_table'] = true;
                    $result['time'] = date("Y/m/d h:iA", $key['order_time']);
                    $result['order_id'] = $key['order_id'];
                }
            }
        }
    }

    if(isset($_POST['book_table'])){
        $result['book_table'] = false;
        if ($student_id != "") {
            $checkBackOrder = mysqli_query($connect, "SELECT * FROM `food_orders_list` WHERE `status` = 'OPEN' AND `student_id` = '$student_id' LIMIT 1");
            if (mysqli_num_rows($checkBackOrder) > 0){
                foreach ($checkBackOrder as $key) {
                    $result['book_table'] = true;
                    $result['time'] = date("Y/m/d h:iA", $key['order_time']);
                    $result['order_id'] = $key['order_id'];
                }
            }else{
                if (@mysqli_query($connect, "INSERT INTO `food_orders_list` (`status`, `student_id`, `order_time`, `billed_time`, `paid_time`, `total_when_booked`) VALUES ('OPEN', '$student_id', '$time', '0', '0', '0')")) {            
                    $result['book_table'] = true;
                    $result['time'] = date("Y/m/d h:iA", $time);
                    $result['order_id'] = @mysqli_insert_id($connect);
                }
            }
            
        }
    }
    if (isset($_POST['ordered_items'])) {
        $order_id = addslashes($_REQUEST['ordered_items']);
        $result['ordered_items'] = food_orders_item($order_id);
    }
    if(isset($_REQUEST['place_order'])){
        $order_id = addslashes($_REQUEST['place_order']);
        $item_name = addslashes($_REQUEST['item_name']);
        $item_price = addslashes($_REQUEST['item_price']);
        $item_quantity = addslashes($_REQUEST['item_quantity']);
        if (@mysqli_query($connect, "INSERT INTO `food_orders_item` (`order_item_id`, `order_id`, `item_quantity`, `name_then`, `price_then`) VALUES (NULL, '$order_id', '$item_quantity', '$item_name', '$item_price')")) {
            $result['place_order'] = true;
        }else{
            $result['place_order'] = false;
        }
    }


    if (isset($_POST['tableClosed']) ) {
        $result['closeStatus'] = false;
        $result['tableClosed'] = null;
        $order_id = addslashes($_REQUEST['tableClosed']);
        $result['ordered_items'] = food_orders_item($order_id);
        $totalPrice = 0;
        foreach ($result['ordered_items'] as $key ) {
            $totalPrice += $key['item_quantity'] * $key['price_then'];
        }
        if(@mysqli_query($connect, "UPDATE `food_orders_list` SET `status` = 'CLOSED', `billed_time` = '$time', `total_when_booked` = '$totalPrice' WHERE `food_orders_list`.`order_id` = '$order_id'")){
            $result['closeStatus'] = true;
            $result['tableClosed'] = array();
            if ($order = @mysqli_query($connect, "SELECT * FROM `food_orders_list` WHERE `status` LIKE 'CLOSED' AND `order_id` LIKE '$order_id'")) {
				foreach ($order as $key) {
					$key['order_time'] = date("Y/m/d h:iA", $key['order_time']);
					$key['billed_time'] = date("Y/m/d h:iA", $key['billed_time']);
					$key['vat'] = $info['vat'];
					$result['tableClosed'] = $key;
				}
			}
        }
    }
}


    $result['errnos'] = $_POST;
echo json_encode($result);