<?php
require_once('./html-header.php');
?>
<title><?php echo $info['title']; ?> - Current Order List</title>
<form action="/order-history" method="post" class="search-in-orders">
    <input type="text" value="<?php echo isset($_POST['search'])?$_POST['search']:"" ?>" placeholder="Order id, Customer Name or Customer Number" name="search">
    <button type="submit">Search</button>
</form>
<div class="currentOrders"></div>
<?php
$time_before = $time - (86400*1);
$sql = "SELECT * FROM `food_orders_list` WHERE (`order_time` BETWEEN '$time_before' AND '$time') AND (`status` = 'CLOSED')  ORDER BY `food_orders_list`.`order_id` DESC";

if ($query = @mysqli_query($connect, $sql)) {
    if(mysqli_num_rows($query) > 0){
        echo "<div class='pageTitle'>Orders of last 24 hours</div>";
    }
    echo showOrdersContainer($query);
}
?>
<script>
(loadOpenOrders=e=>{
    const open_orders_div =document.querySelector(".currentOrders");
    if(open_orders_div){
        loadLink('/json', [['open_orders','1']]).then(result=>{
            open_orders_div.innerHTML = "";
            if(result.open_orders){
                const newData = create("div");
                newData.innerHTML = result.open_orders;
                open_orders_div.appendChild(newData);
            }
            setTimeout(()=>{
                loadOpenOrders();
            }, 1000);
        });
    }
})();
</script>
<?php require_once('./html-footer.php');?>