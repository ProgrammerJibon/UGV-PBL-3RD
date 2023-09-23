<?php

require_once('./html-header.php');
?>
<title><?php echo $info['title']; ?> - Old Orders</title>
<form action="" method="post" class="search-in-orders">
    <input type="text" value="<?php echo isset($_POST['search'])?$_POST['search']:"" ?>" placeholder="Order id, Customer Name or Customer Number" name="search">
    <button type="submit">Search</button>
</form>
<form action="" method="POST" class="search-in-orders">
    <select name="waiter" class="" required onchange="this.parentNode.submit()">
        <option value="" selected>All Waiter</option>
        <?php
            $waiter_list = devicesList();
            foreach ($waiter_list as $key) {
                $selected = isset($_POST['waiter']) && ($_POST['waiter'] == $key['username']) ? "selected":"";
                echo "<option $selected value='$key[username]'>$key[username]</option>";
            }
        ?>
    </select>
</form>
<?php
$time_before = $time - (86400*1);
$search = "(`order_time` BETWEEN '0' AND '$time_before') OR (`status` = 'CLOSED')";
if(isset($_POST['waiter']) && $_POST['waiter'] == ""){
    unset($_POST['waiter']);
}
if(isset($_POST['waiter'])){
    $ss = addslashes(strip_tags(strtolower($_POST['waiter'])));
    $search = "(`order_taker_name` = '$ss')";
}else if (isset($_POST['search']) && $_POST['search'] != "") {
    $ss = addslashes(strip_tags(strtolower($_POST['search'])));
    $search = "(`order_id` = '$ss') OR (`customer_name` LIKE '%$ss%') OR (`customer_phone` = '$ss')  OR (`order_taker_name` LIKE '%$ss%')";
}

$sql = "SELECT * FROM `food_orders_list` WHERE $search  ORDER BY `food_orders_list`.`order_id` DESC LIMIT 99999";

if ($query = @mysqli_query($connect, $sql)) {
    if(mysqli_num_rows($query) > 0){
        echo isset($_POST['search'])?"<div class='pageTitle'>Search result for ".$_POST['search']."</div>": "<div class='pageTitle'>100 orders of before 24 hours</div>";
    }
    echo showOrdersContainer($query, isset($_POST['waiter']));
}
?>

<?php require_once('./html-footer.php');?>