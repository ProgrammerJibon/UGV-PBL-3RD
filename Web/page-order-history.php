<?php

require_once('./html-header.php');
?>
<title><?php echo $info['title']; ?> - Old Orders</title>
<!-- <form action="" method="post" class="search-in-orders">
    <input type="text" value="<?php echo isset($_POST['search'])?$_POST['search']:"" ?>" placeholder="Order id, Customer Name or Customer Number" name="search">
    <button type="submit">Search</button>
</form> -->
<form action="" method="POST" class="search-in-orders">
    <select name="student_id" class="" required onchange="this.parentNode.submit()">
        <option value="">All student</option>
        <?php
            $student_list = studentsList();
            foreach ($student_list as $key) {
                $selected = isset($_POST['student_id']) && ($_POST['student_id'] == $key['id']) ? "selected":"";
                echo "<option $selected value='$key[id]'>$key[student_id] - $key[student_name]</option>";
            }
        ?>
    </select>
</form>
<?php
$time_before = $time - (86400*1);
$search = "(`order_time` BETWEEN '0' AND '$time_before') OR (`status` = 'CLOSED')";
if(isset($_POST['student_id']) && $_POST['student_id'] != ""){
    $ss = addslashes(strip_tags(strtolower($_POST['student_id'])));
    $search = "(`student_id` = '$ss')";
}

$sql = "SELECT * FROM `food_orders_list` WHERE $search  ORDER BY `food_orders_list`.`order_id` DESC LIMIT 99999";

if ($query = @mysqli_query($connect, $sql)) {
    if(mysqli_num_rows($query) > 0){
        echo isset($_POST['student_id']) && $_POST['student_id'] != ""?"<div class='pageTitle'>Search result for ".$_POST['student_id']."</div>": "<div class='pageTitle'>100 orders of before 24 hours</div>";
    }
    echo showOrdersContainer($query, isset($_POST['student_id']) && $_POST['student_id'] != "");
}
?>

<?php require_once('./html-footer.php');?>