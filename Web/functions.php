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

$admin_pass = md5(sha1("s7"));
$admin = isset($_SESSION['admin']) && $_SESSION['admin'] && $_SESSION['admin'] == $admin_pass ;

function connect(){
	$localhost = true;
	$DB_HOST = "localhost";
	$DB_USER = $localhost?"root":"jibonpro_ugv";
	$DB_PASS = $localhost?'':"FtjzhT2AjVPvJS5";
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
	return true;
	$connect == null?exit():null;
	$sql = "";
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

function studentsList($student_id = null){
	$result = array();
	global $connect;
	 $sql = $student_id != null ?"SELECT * FROM `students` WHERE  `students`.`student_id` = '$student_id' ORDER BY `students`.`status` DESC LIMIT 1":"SELECT * FROM `students` ORDER BY `students`.`status` ASC";
	$query = @mysqli_query($connect, $sql);
	if($query){
		foreach($query as $details){
			$details['check_code'] = $student_id?$details['code']:null;
			($details['status'] != "INACTIVE")?$details['code']=substr($details['code'], 0, 2)."••••".substr($details['code'], 6, 2):null;
			//$details['code'] = substr($details['code'], 0, 4)." ".substr($details['code'], 4, 4).substr($details['code'], 8);
			$details['time'] = ($details['time'] > 0 && ($details['status'] == 'ACTIVE' || $details['status'] == "REMOVED"))?date("Y/m/d h:iA", $details['time']):"Not Connected Yet";
			$details['removed_time'] = $details['removed_time'] > 0?date("Y/m/d h:iA", $details['removed_time']):"N/A";
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

		$student = studentsList($key["student_id"])[0];

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
        $key['order_time'] = date("Y/m/d h:iA", $key['order_time']);
        $key['billed_time'] = $key['billed_time'] > 0 ? date("Y/m/d h:iA", $key['billed_time']):"OPEN";

        $echo .= "\n<div class='order_id'><span class='name'>Order ID</span>:<span class='value'>$key[order_id]</span></div>";
   

        $echo .= "\n<div class='customer_name'><span class='name'>Student Name</span>:<span class='value'>$student[student_name]</span></div>";

        $echo .= "\n<div class='customer_phone'><span class='name'>Student ID</span>:<span class='value'>$student[student_id]</span></div>";

        $echo .= "\n<div class='order_time'><span class='name'>Open Time</span>:<span class='value'>$key[order_time]</span></div>";

        $echo .= "\n<div class='billed_time'><span class='name'>Closed Time</span>:<span class='value'>$key[billed_time]</span></div>";


         $echo .= sizeof($items_ordered) >0?'<table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Per Item Price</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Remove</th>
                            </tr>
                        </thead><tbody>':"";
        foreach ($items_ordered as $data) {
            $echo .= "\n<tr>";
            $echo .= "\n<td>".$data['name_then']."</td>";
            $echo .= "\n<td>".$data['price_then']."</td>";
            $echo .= "\n<td>".$data['item_quantity']."</td>";
            $echo .= "\n<td>".($data['price_then'] * $data['item_quantity'])."</td>";
            $echo .= $key['status']=="OPEN"?"\n<td><a href='/json?removeOrderedItem=".$data['order_item_id']."'><font color='red'>X</font></a></td>":"<td></td>";
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



function removeExtraSpaces($text) {
    return preg_replace('/\s+/', ' ', $text);
}

function removeSpaces($text) {
    return preg_replace('/\s+/', '', $text);
}