<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


date_default_timezone_set("Asia/Dhaka");
$time = time();
session_start();
$connect = connect();
if (!addDatabaseAndTables($connect)) {
    header("HTTP/1.0 500 Can't connect to Database Server");
    exit();
}
$info = info();
$ip =  get_client_ip();
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$website = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'];
$current_url = urlencode($_SERVER['REQUEST_URI']);
$user_id = 1;

// function connect(){
// 	$DB_HOST = "localhost";
// 	$DB_USER = "root";
// 	$DB_PASS = '';
// 	$DB_NAME = "eticket";
// 	$CONNECT = @mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
// 	mysqli_set_charset($CONNECT,"utf8");
// 	return $CONNECT;
// }
function connect(){
	$DB_HOST = "localhost";
    $DB_USER = "root";
    $DB_PASS = "";
    $DB_NAME = "jibonpro_ugv_pbl_3rd";
	$CONNECT = @mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
	mysqli_set_charset($CONNECT,"utf8");
	return $CONNECT;
}


function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}



function to_timestamp(string $y, string $m, string $d, string $h, string $i, string $s){
	// echo $y.$m.$d.$h.$i.$s;	
	$createFromFormat = DateTime::createFromFormat(
		'd-m-Y H:i:s',
		"$d-$m-$y $h:$i:$s",
		new DateTimeZone('Asia/Dhaka')
	);
	return $createFromFormat === false?545:$createFromFormat->getTimestamp();
}


function filter_namex($strip = null, $replace = null, $full_name = null){

    $r = "";

    $u = explode($strip, $full_name);

    $i = 0;

    foreach($u as $data){

        if($i != 0){

            $data = $replace.$data;

        }

        $r .= $data;

        $i++;

    }

    return $r;

}




function upload($tmp_file, $type = false){
	$mime_file_type = explode("/", mime_content_type($tmp_file));
	$result = false;
	if($type == false || $type == $mime_file_type[0]){
		$file_path = "uploads/".date("Y/M/");
		if (!file_exists($file_path)) {
			mkdir($file_path, 0777, true);
		}
		$file_name = $file_path.$mime_file_type[0]."-".time()."-".rand().".".$mime_file_type[1];
		if(move_uploaded_file($tmp_file, $file_name)){
			$result = $file_name;
		}
	}
	return $result;
}





function rearrange_files($arr) {
	foreach($arr as $key => $all) {
	    foreach($all as $i => $val) {
	        $new_array[$i][$key] = $val;    
	    }    
	}
		return $new_array;
}
function times($ss) {
	$result = "";
	$s = $ss%60;
	$m = floor(($ss%3600)/60);
	$h = floor(($ss%86400)/3600);
	$d = floor(($ss%((365.25/12)*86400))/86400);
	$M = floor(($ss%(((365.25/12)*86400)*12))/((365.25/12)*86400));
	$Y = floor($ss/(((365.25/12)*86400)*12));

	if ($Y > 0) {
		$result .= $Y."y ";
	}
	if ($M > 0) {
		$result .= $M."m ";
	}
	if ($d > 0) {
		$result .= $d."d ";
	}
	if ($h > 0) {
		$result .= $h."h ";
	}
	if ($m > 0) {
		$result .= $m."m ";
	}/*
	if ($s > 0) {
		$result .= $s."s ";
	}*/

	return $result;
}




function info($admin = false){
    $result = array();
    global $connect;
	$sql = "SELECT * FROM `info` ORDER BY `info`.`id` DESC";
	$query = @mysqli_query($connect, $sql);
	if($query){
		foreach($query as $details){
			$result[$details['name']] = $details['value'];
		}
	}else{
		return false;
	}
		
    return $result;
}




function addDatabaseAndTables($connect){
	$connect == null?exit():null;
	$sql = " CREATE TABLE IF NOT EXISTS `food_items` (`item_id` INT(255) NOT NULL AUTO_INCREMENT , `item_name` VARCHAR(1024) NOT NULL , `group_id` VARCHAR(255) NOT NULL,  `item_pic` VARCHAR(2048) NOT NULL, `item_price` VARCHAR(255) NOT NULL , PRIMARY KEY (`item_id`)) ENGINE = InnoDB; CREATE TABLE IF NOT EXISTS `food_group` (`group_id` INT(255) NOT NULL AUTO_INCREMENT , `group_name` VARCHAR(1024) NOT NULL , PRIMARY KEY (`group_id`)) ENGINE = InnoDB; CREATE TABLE  IF NOT EXISTS `info` (`id` INT(255) NOT NULL AUTO_INCREMENT , `name` VARCHAR(1024) NOT NULL , `value` LONGTEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;CREATE TABLE IF NOT EXISTS `table_list` (`table_id` INT(255) NOT NULL AUTO_INCREMENT , `table_name` VARCHAR(1024) NOT NULL , PRIMARY KEY (`table_id`)) ENGINE = InnoDB;CREATE TABLE IF NOT EXISTS `food_orders_item` (`order_item_id` INT(255) NOT NULL AUTO_INCREMENT , `order_id` VARCHAR(255) NOT NULL , `item_quantity` VARCHAR(255) NOT NULL , `name_then` VARCHAR(1024) NOT NULL , `price_then` VARCHAR(1024) NOT NULL ,`printed` VARCHAR(16) NOT NULL DEFAULT 'false' COMMENT 'true/false' ,PRIMARY KEY (`order_item_id`)) ENGINE = InnoDB; INSERT IGNORE INTO `info` (`id`, `name`, `value`) VALUES (1, '".base64_decode('dGl0bGU=')."', 'UGV Cafeteria Order Manager');"."CREATE TABLE IF NOT EXISTS `devices` (`id` INT(255) NOT NULL AUTO_INCREMENT , `code` VARCHAR(1024) NOT NULL COMMENT 'code where can device connect' , `time` VARCHAR(1024) NOT NULL COMMENT 'device adding time' , `removed_time` VARCHAR(1024) NOT NULL COMMENT 'device removed time' , `username` VARCHAR(1024) NOT NULL COMMENT 'name to identify on admin panel' , `status` VARCHAR(1024) NOT NULL COMMENT 'active: device connected \r\nremoved: device removed\r\ninactive: device isn\'t connected yet' , PRIMARY KEY (`id`)) ENGINE = InnoDB;"."CREATE TABLE IF NOT EXISTS `food_orders_list` (
		`order_id` int(255) NOT NULL AUTO_INCREMENT,
		`customer_name` varchar(1024) NOT NULL,
		`customer_phone` varchar(1024) NOT NULL,
		`order_taker_name` varchar(1024) NOT NULL,
		`status` varchar(1024) NOT NULL DEFAULT 'OPEN' COMMENT 'OPEN : order is not finished yet\r\nCLOSED : same as it''s mean for',
		`table_id` varchar(1024) NOT NULL,
		`order_time` varchar(1024) NOT NULL DEFAULT 0,
		`billed_time` varchar(1024) NOT NULL DEFAULT 0,
		`paid_time` varchar(1024) NOT NULL DEFAULT 0,
		`vat_when_booked` int(255) NOT NULL,
		`total_when_booked` int(255) NOT NULL COMMENT 'without vat',
		PRIMARY KEY (`order_id`)
	  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;INSERT IGNORE INTO  `info` (`id`, `name`, `value`) VALUES (2, 'vat', '25');INSERT IGNORE INTO  `info` (`id`, `name`, `value`) VALUES (3, '4e3b9acd4385b58c539b70445301f400', '');";
	if (@mysqli_multi_query($connect, $sql)) {
		do {
			if ($result = mysqli_store_result($connect)) {
				mysqli_free_result($result);
			}
		} while (mysqli_more_results($connect) && mysqli_next_result($connect));
		return true;
	}
    return false;
}





function tableList($table_id = null){
	$result = array();
	global $connect, $info;
	$extras = $table_id != null?"WHERE `table_id` = '$table_id'":"";
	$sql = "SELECT * FROM `table_list` $extras ORDER BY `table_list`.`table_id` DESC";
	$query = @mysqli_query($connect, $sql);
	if($query){
		foreach($query as $details){
			if ($tableStatus = @mysqli_query($connect, "SELECT * FROM `food_orders_list` WHERE `status` LIKE 'OPEN' AND `table_id` LIKE '$details[table_id]'")) {
				foreach ($tableStatus as $key) {
					$key['order_time'] = date("Y/m/d h:iA", $key['order_time']);
					$key['vat'] = $info['vat'];
					$details['current_status'] = $key;
				}
			}
			$result[] = $details;
		}
	}else{
		return false;
	}
		
	return $result;
}

function groupList($groupID = null){
	$result = array();
	global $connect;
	 $sql = $groupID != null ?"SELECT * FROM `food_group` WHERE  `food_group`.`group_id` = '$groupID' ORDER BY `food_group`.`group_id` DESC LIMIT 1":"SELECT * FROM `food_group` ORDER BY `food_group`.`group_id` DESC";
	$query = @mysqli_query($connect, $sql);
	if($query){
		foreach($query as $details){
			$result[] = $details;
		}
	}else{
		return false;
	}

	
	$others['group_id'] = 0;
	$others['group_name'] = "Others";
	$result[] = $others;
		
	return $result;
}


function food_orders_item($order_id){
	global $connect;
	$result = array();
	if ($ordered_items_query = @mysqli_query($connect, "SELECT * FROM `food_orders_item` WHERE `order_id` = '$order_id';")) {
		foreach ($ordered_items_query as $key ) {
			
			$result[] = $key;
		}
	}
	return $result;
}

function devicesList($code = null){
	$result = array();
	global $connect;
	 $sql = $code != null ?"SELECT * FROM `devices` WHERE  `devices`.`code` = '$code' ORDER BY `devices`.`status` DESC LIMIT 1":"SELECT * FROM `devices` ORDER BY `devices`.`status` ASC";
	$query = @mysqli_query($connect, $sql);
	if($query){
		foreach($query as $details){
			$details['check_code'] = $code?$details['code']:null;
			$details['username'] = preg_replace("/[^a-z0-9]/", "",strtolower($details['username']));
			($details['status'] != "INACTIVE")?$details['code']=substr($details['code'], 0, 2)."****".substr($details['code'], 6, 2):null;
			$details['code'] = substr($details['code'], 0, 4)." ".substr($details['code'], 4, 4).substr($details['code'], 8);
			$details['time'] = ($details['time'] && ($details['status'] == 'ACTIVE' || $details['status'] == "REMOVED"))?date("Y/m/d h:iA", $details['time']):"Not Connected Yet";
			$details['removed_time'] = date("Y/m/d h:iA", $details['removed_time']);
			$result[] = $details;
		}
	}else{
		return false;
	}
		
	return $result;
}


function itemsList($group_id = null){
	$result = array();
	global $connect, $website;
	$extras = $group_id != null?" WHERE `group_id` LIKE '$group_id' ":"";
	$sql = "SELECT * FROM `food_items` $extras ORDER BY `food_items`.`item_id` DESC";
	$query = @mysqli_query($connect, $sql);
	if($query){
		foreach($query as $details){
			if ($group_id == null) {
				$groupDetails = groupList($details['group_id']);
				($details['groupDetails'] = isset($groupDetails[0])?$groupDetails[0]:null);
				unset($details['group_id']);
			}
			$details['item_pic'] = "$website/$details[item_pic]";
			$details['item_name'] = ucwords($details['item_name']);
			$result[] = $details;
		}
	}else{
		return false;
	}
		
	return $result;
}



function showOrdersContainer($query, $wname = false){
	$echo = "<div class='ordersListContainer'>";
	$last_date = "";
	$last_date_price = "";
	$last_price = 0;
    foreach ($query as $key) {
		$new_date = date("M d, Y", $key['billed_time']);

		$last_date_price == ""?$last_date_price = $new_date:null;

		if ($last_date_price != $new_date && $wname) {
			$echo .= "<div class='new_price'>Total Billed: <span>&#2547;$last_price</span></div>";
			$last_date_price = $new_date;
			$last_price = 0;
		}
		$last_price += $key['total_when_booked'];

		
		if($last_date != $new_date && $wname){
			$echo .= "<div class='new_date'>$new_date</div>";
			$last_date = $new_date;
		}




        $echo .= "\n\n<div class='ordersListItem'>";
        $items_ordered = food_orders_item($key['order_id']);
        $tables = tableList($key['table_id']);
        $key['order_time'] = date("Y/m/d h:iA", $key['order_time']);
        $key['billed_time'] = $key['billed_time'] > 0 ? date("Y/m/d h:iA", $key['billed_time']):"OPEN";

        $echo .= "\n<div class='order_id'><span class='name'>Order ID</span>:<span class='value'>$key[order_id]</span></div>";

        foreach($tables as $table){
            $echo .= "\n<div class='table_id'><span class='name'>Table</span>:<span class='value'>$table[table_name] ($table[table_id])</span></div>";
        }        

        $echo .= "\n<div class='customer_name'><span class='name'>Customer Name</span>:<span class='value'>$key[customer_name]</span></div>";

        $echo .= "\n<div class='customer_phone'><span class='name'>Customer Phone Number</span>:<span class='value'>$key[customer_phone]</span></div>";

        $echo .= "\n<div class='order_taker_name'><span class='name'>Served by</span>:<span class='value'>$key[order_taker_name]</span></div>";

        $echo .= "\n<div class='order_time'><span class='name'>Open Time</span>:<span class='value'>$key[order_time]</span></div>";

        $echo .= "\n<div class='billed_time'><span class='name'>Closed Time</span>:<span class='value'>$key[billed_time]</span></div>";


         $echo .= sizeof($items_ordered) >0?'<table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Per Item Price</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                            </tr>
                        </thead><tbody>':"";
        foreach ($items_ordered as $data) {
            $echo .= "\n<tr>";
            $echo .= "\n<td>".$data['name_then']."</td>";
            $echo .= "\n<td>".$data['price_then']."</td>";
            $echo .= "\n<td>".$data['item_quantity']."</td>";
            $echo .= "\n<td>".($data['price_then'] * $data['item_quantity'])."</td>";
            $echo .= "\n</tr>";
        }

        $echo .= sizeof($items_ordered) >0?'<tbody></table>':"";

        
        $echo .= "\n<div class='total_when_booked'><span class='name'>Total</span>:<span class='value'>&#2547;$key[total_when_booked]</span></div>";

        // $echo .= "\n<div class='total_when_booked'><span class='name'>Total (with $key[vat_when_booked]% vat)</span>:<span class='value'>".($key['total_when_booked'] + ($key['total_when_booked']*$key['vat_when_booked']/100))."</span></div>";

        $echo .= "</div>";

        // echo "<pre>";
        // print_r($key);
        // echo "</pre>";
    }
	$wname ? $echo .= "<div class='new_price'>Total Billed: &#2547;$last_price</div>":null;
    $echo .= "</div>";

	// foreach ($totalPrices as $date => $price) {
	// 	$echo .= "Total price for date $date: $price\n";
	// }

    return $echo;
}