<?php

//

if (isset($_POST['student_id'])) {
    $student_id = preg_replace("/[^0-9]/", "",(strtolower(addslashes(strip_tags($_POST['student_id'])))));
    $code = rand(10000000, 99999999);
    $unique = true;
    foreach (studentsList() as $key) {
        if ($key['student_id'] == $student_id) {
            $unique = false;            
        }
    }
    if($unique){
        $student_name = "";
        if(isset($_POST['student_name'])){
            $student_name = strip_tags(addslashes(removeExtraSpaces($_POST['student_name'])));
        }
        if(@mysqli_query($connect, "INSERT INTO `students` (`student_id`, `student_name`, `code`, `time`, `removed_time`, `status`) VALUES ('$student_id', '$student_name', '$code', '0', '0', 'INACTIVE')")){
            header("Location: /students");
            exit();
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
        <input type="text" style="width: 300px;padding: 8px" placeholder="Enter Student Name" name="student_name" minlength="0" required maxlength="64">
        <input type="text" style="width: 300px;padding: 8px" placeholder="Enter Student ID" pattern="([0-9]*)" name="student_id" minlength="0" required maxlength="64">
        <input style="padding: 8px; cursor: pointer;" type="submit" value="Add student">
    </div>
    <div style="color: red; padding: 8px;"><?php echo (isset($unique))?($unique?"":"student name must be unique,<br> if you have student more than 1 with same name,<br> classify them with adding a number."):"";?></div>
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
            <th>Student Name</th>
            <th>Student Id</th>
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
loadLink('/json', [['studentsList','admin']]).then(result=>{
    console.log(result)
    if(Array.isArray(result.studentsList)){
        let tablesList = document.querySelector("tbody.tablesList");
        result.studentsList.forEach(item => {
            const tr = create("tr");
            const td1 = create("td");
            const td2 = create("td");
            const td3 = create("td");
            const td4 = create("td");
            const td5 = create("td");
            const td6 = create("td");
            const td7 = create("td");
            const td8 = create("td");
            const del = create("span", "delbtn");
            tr.appendChild(td1);
            tr.appendChild(td2);
            tr.appendChild(td3);
            tr.appendChild(td4);
            tr.appendChild(td5);
            tr.appendChild(td6);
            tr.appendChild(td7);
            tr.appendChild(td8);
            td8.appendChild(del);

            td1.innerHTML = item.id;
            td2.innerHTML = item.student_name;
            td3.innerHTML = item.student_id;
            td4.innerHTML = item.code;
            td5.innerHTML = item.time;
            td6.innerHTML = item.removed_time;
            td7.innerHTML = item.status;
            del.innerHTML = "Remove Access";
            if(item.status == "ACTIVE"){
                //td5.innerHTML = "";
            }else if (item.status == "INACTIVE"){
                //td5.innerHTML = "";
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