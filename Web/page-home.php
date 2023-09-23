<?php

require_once('./html-header.php');
?>
<title><?php echo $info['title']; ?> - Current Order List</title>
<form action="/order-history" method="post" class="search-in-orders">
    <input type="text" value="<?php echo isset($_POST['search'])?$_POST['search']:"" ?>" placeholder="Order id, Customer Name or Customer Number" name="search">
    <button type="submit">Search</button>
</form>
<?php
$sql = "SELECT * FROM `food_orders_list` WHERE (`status` = 'OPEN')  ORDER BY `food_orders_list`.`order_id` DESC";

if ($query = @mysqli_query($connect, $sql)) {
    if(mysqli_num_rows($query) > 0){
        echo "<div class='pageTitle'>Orders Open right now</div>";
    }
    echo showOrdersContainer($query);
}

$time_before = $time - (86400*1);
$sql = "SELECT * FROM `food_orders_list` WHERE (`order_time` BETWEEN '$time_before' AND '$time') AND (`status` = 'CLOSED')  ORDER BY `food_orders_list`.`order_id` DESC";

if ($query = @mysqli_query($connect, $sql)) {
    if(mysqli_num_rows($query) > 0){
        echo "<div class='pageTitle'>Orders of last 24 hours</div>";
    }
    echo showOrdersContainer($query);
}
?>

<?php require_once('./html-footer.php');?>