<?php

require_once("./functions.php");
$result = array();
$result['foo'] = "bar";


if (isset($_POST['tablesList'])) {
    $result['tablesList'] = tableList();
}
if (isset($_POST['devicesList'])) {
    $result['devicesList'] = devicesList();
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

if (isset($_POST['deleteItem'])) {
    if(@mysqli_query($connect, "DELETE FROM `food_items` WHERE `food_items`.`item_id` = '".addslashes($_POST['deleteItem'])."'")){
        $result['deleteItem'] = true;
    }else{
        $result['deleteItem'] = false;
    }    
}

if (isset($_POST['removeDevice'])) {
    if(@mysqli_query($connect, "UPDATE `devices` SET `removed_time` = '$time', `status` = 'REMOVED' WHERE `devices`.`id` = '".addslashes($_POST['removeDevice'])."'")){
        $result['removeDevice'] = true;
    }else{
        $result['removeDevice'] = false;
    }    
}

if (isset($uri[1]) && strtolower($uri[1]) == 'app') {
    $result['connectionResult'] = false;
    $result['connectionUsername'] = "";
    if (isset($_GET['connectorCode'])) {
        $connectorCode = addslashes($_GET['connectorCode']);
        $resultConnection = devicesList($connectorCode);
        if (isset($resultConnection[0])) {
            $resultConnection = $resultConnection[0];
            if(isset($resultConnection['status']) && isset($resultConnection['check_code']) && preg_replace("/[^0-9]/", "", $resultConnection['check_code']) == $connectorCode){
                $result['connectionUsername'] = $resultConnection['username'];
                if ($resultConnection['status'] == "INACTIVE") {
                    if (@mysqli_query($connect, "UPDATE `devices` SET `time` = '$time', `status` = 'ACTIVE' WHERE `devices`.`id` = '".$resultConnection['id']."'")) {
                        $result['connectionResult'] = true;
                    }
                }elseif ($resultConnection['status'] == "ACTIVE") {
                    $result['connectionResult'] = true;
                }
            }
        }
    }
    if (isset($_GET['lists'])) {
        $_GET['lists']=="tables"?($result['lists_table'] = $result['connectionResult']?tableList():array()):null;
    }
    if (isset($_GET['removeOrderedItem'])) {
        $removeOrderedItem = addslashes($_GET['removeOrderedItem']);
        $result['removeOrderedItem'] = @mysqli_query($connect, "DELETE FROM `food_orders_item` WHERE `food_orders_item`.`order_item_id` = '$removeOrderedItem'");
    }
    if(isset($_GET['book_table'])){
        if ($result['connectionResult']) {
            $name = addslashes(strip_tags(urldecode($_GET['customer_name'])));
            $phone = addslashes(strip_tags(urldecode($_GET['customer_phone'])));
            $table_id = addslashes(strip_tags(urldecode($_GET['book_table'])));
            if (@mysqli_query($connect, "INSERT INTO `food_orders_list` (`order_id`, `customer_name`, `customer_phone`, `order_taker_name`, `status`, `table_id`, `vat_when_booked`, `total_when_booked`, `order_time`) VALUES (NULL, '$name', '$phone', '$result[connectionUsername]', 'OPEN', '$table_id', '$info[vat]', '0', '$time')")) {            
                $result['book_table'] = true;
                $result['time'] = date("Y/m/d h:iA", $time);
                $result['order_id'] = @mysqli_insert_id($connect);
                $result['vat'] = $info['vat'];
            }else{
                $result['book_table'] = false;
            }
        }
    }
    if (isset($_GET['ordered_items'])) {
        $order_id = addslashes($_GET['ordered_items']);
        if(isset($_GET['printed']) && $_GET['printed'] == "true"){
            @mysqli_query($connect, "UPDATE `food_orders_item` SET `printed` = 'true' WHERE `food_orders_item`.`order_id` = '$order_id'");
        }
        $result['ordered_items'] = food_orders_item($order_id);
    }
    if(isset($_GET['place_order'])){
        $order_id = addslashes($_GET['place_order']);
        $item_name = addslashes($_GET['item_name']);
        $item_price = addslashes($_GET['item_price']);
        $item_quantity = addslashes($_GET['item_quantity']);
        if (@mysqli_query($connect, "INSERT INTO `food_orders_item` (`order_item_id`, `order_id`, `item_quantity`, `name_then`, `price_then`) VALUES (NULL, '$order_id', '$item_quantity', '$item_name', '$item_price')")) {
            $result['place_order'] = true;
        }else{
            $result['place_order'] = false;
        }
    }


    if (isset($_REQUEST['tableClosed']) ) {
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


echo json_encode($result);