<?php

//

if (isset($_POST['fname'])) {
    $fname = preg_replace("/[^a-zA-Z0-9]/", "",(strtolower(addslashes(strip_tags($_POST['fname'])))));
    $code = rand(10000000, 99999999);
    $unique = true;
    foreach (devicesList() as $key) {
        if ($key['username'] == $fname) {
            $unique = false;            
        }
    }
    if($unique){
        if(@mysqli_query($connect, "INSERT INTO `devices` (`id`, `code`, `time`, `removed_time`, `username`, `status`) VALUES (NULL, '$code', '$time', '0', '$fname', 'INACTIVE')")){
            header("Location: /devices");
        }
    }
}

$localIP = $_SERVER['SERVER_NAME'] == "localhost" ||  $_SERVER['SERVER_NAME'] == "127.0.0.1" ?getHostByName(getHostName()):$_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'];
// echo "Local IP address: " . $localIP;
require_once('./html-header.php');

?>
<title><?php echo $info['title']; ?> - Tables</title>
<style>
    tr:nth-child(even) {background-color: #f2f2f2;}
    tr{background-color:#f9f9f9;text-align: center;}
    tr:hover{background-color: #ffefef; }
    th,td{padding: 8px}
    table{margin: 16px; width: -webkit-fill-available;}
    .delbtn{color:red;cursor: pointer; text-decoration: underline;}
    form{text-align: center;margin: 32px;}
</style>

<form action="" method="POST">
    <div>
        <input type="text" style="width: 300px;padding: 8px" placeholder="Enter Waiter Name (must be unique)" pattern="([a-z][a-z0-9]*)" name="fname" minlength="0" required maxlength="64">
        <input style="padding: 8px; cursor: pointer;" type="submit" value="Generate Code">
    </div>
    <div style="color: red; padding: 8px;"><?php echo (isset($unique))?($unique?"":"Waiter name must be unique,<br> if you have waiter more than 1 with same name,<br> classify them with adding a number."):"";?></div>
    <br>
    <div>
        <span>Current Server IP: </span>
        <span style="color: green; font-weight: bold;"><?php echo $localIP;?></span>
    </div>
</form>

<table>
    <thead>
        <tr>
            <th>id</th>
            <th>Waiter Name</th>
            <th>code</th>
            <th>connected time</th>
            <th>removed time</th>
            <th>status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="tablesList"></tbody>
</table>
<script>
loadLink('/json', [['devicesList','admin']]).then(result=>{
    console.log(result)
    if(Array.isArray(result.devicesList)){
        let tablesList = document.querySelector("tbody.tablesList");
        result.devicesList.forEach(item => {
            const tr = create("tr");
            const td1 = create("td");
            const td2 = create("td");
            const td3 = create("td");
            const td4 = create("td");
            const td5 = create("td");
            const td6 = create("td");
            const td7 = create("td");
            const del = create("span", "delbtn");
            tr.appendChild(td1);
            tr.appendChild(td2);
            tr.appendChild(td3);
            tr.appendChild(td4);
            tr.appendChild(td5);
            tr.appendChild(td6);
            tr.appendChild(td7);
            td7.appendChild(del);

            td1.innerHTML = item.id;
            td2.innerHTML = item.username;
            td3.innerHTML = item.code;
            td4.innerHTML = item.time;
            td5.innerHTML = item.removed_time;
            td6.innerHTML = item.status;
            del.innerHTML = "Remove Access";
            if(item.status == "ACTIVE"){
                td5.innerHTML = "";
            }else if (item.status == "INACTIVE"){
                td5.innerHTML = "";
                del.remove();
            }else{
                del.remove();
            }


            del.onclick = e=>{
                if(confirm("Are sure to remove access of "+ item.username)){
                    loadLink('/json', [['removeDevice',item.id]]).then(result=>{
                        if (result.removeDevice) {
                            notification("Removed Access Successfully!", "green");
                            window.location.reload();
                        }else{
                            notification("Something went wrong!", "red");
                        }
                    })
                }
            }

            tablesList.appendChild(tr);
        });
    }
});
</script>
<?php require_once('./html-footer.php');?>