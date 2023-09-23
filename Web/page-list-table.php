<?php

if (isset($_POST['addTable']) && $_POST['addTable'] != "") {
    if (@mysqli_query($connect, "INSERT INTO `table_list` (`table_id`, `table_name`) VALUES (NULL, '".addslashes(strip_tags($_POST['addTable']))."')")) {
        header("Location: /tables");
    }
}

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
<form method="post">
    <h3>Add Table</h3>
    <input type="text" placeholder="Table Name or Number" name="addTable"/>
    <input type="submit" value="Add">
</form>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Table Name / Number</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="tablesList"></tbody>
</table>
<script>
loadLink('/json', [['tablesList','admin']]).then(result=>{
    console.log(result)
    if(Array.isArray(result.tablesList)){
        let tablesList = document.querySelector("tbody.tablesList");
        result.tablesList.forEach(item => {
            const tr = create("tr");
            const td1 = create("td");
            const td2 = create("td");
            const td3 = create("td");
            const del = create("span", "delbtn");
            tr.appendChild(td1);
            tr.appendChild(td2);
            tr.appendChild(td3);
            td3.appendChild(del);

            td1.innerHTML = item.table_id;
            td2.innerHTML = item.table_name;
            del.innerHTML = "Delete";

            del.onclick = e=>{
                if(confirm("Are sure to delete "+ item.table_name)){
                    loadLink('/json', [['deleteTable',item.table_id]]).then(result=>{
                        if (result.deleteTable) {
                            notification("Deleted successfully!", "green");
                            tr.remove();
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