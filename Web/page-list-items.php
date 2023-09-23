<?php

if (isset($_POST['addItem']) && $_POST['itemName'] != "" && $_POST['itemGroupId'] != "" && $_POST['itemPrice'] != "" && isset($_FILES['imageOfItem']['tmp_name'])) {
    if($upload_path = upload($_FILES['imageOfItem']['tmp_name'], "image")){
        $_POST['itemName'] = addslashes(strip_tags($_POST['itemName']));
        $_POST['itemGroupId'] = addslashes(strip_tags($_POST['itemGroupId']));
        $_POST['itemPrice'] = addslashes(strip_tags($_POST['itemPrice']));
        if (@mysqli_query($connect, "INSERT INTO `food_items` (`item_id`, `item_name`, `group_id`, `item_price`, `item_pic`) VALUES (NULL, '$_POST[itemName]', '$_POST[itemGroupId]', '$_POST[itemPrice]', '$upload_path')")) {
            header("Location: /items");
        }
    }
}

require_once('./html-header.php');
?>
<title>
    <?php echo $info['title']; ?> - Tables
</title>
<style>
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    tr {
        background-color: #f9f9f9;
        text-align: center;
    }

    tr:hover {
        background-color: #ffefef;
    }

    th,
    td {
        padding: 8px
    }

    table {
        margin: 16px;
        width: -webkit-fill-available;
    }

    .delbtn {
        color: red;
        cursor: pointer;
        text-decoration: underline;
    }

    form {
        text-align: center;
        margin: 32px;
        display: grid;
        align-content: center;
        justify-items: stretch;
        max-width: 400px;
        margin: 32px auto;
        margin-bottom: 64px;
    }

    form input,
    form select {
        margin: 4px 8px;
        padding: 8px;
        cursor: pointer;
    }

    .imageSelector {
        display: block;
        margin: 4px 8px;
        padding: 16px;
        border: 1px solid gray;
        border-radius: 6px;
    }

    .imageOfItemPreview {
        display: block;
        padding: 16px;
        height: auto;
    }
    td img {
        width: 100px;
        height: auto;
        display: block;
        margin: 0 auto;
    }
</style>
<form method="post" enctype="multipart/form-data">
    <h3>Add Item</h3>
    <input required type="text" placeholder="Item Name" name="itemName" />
    <div>
        <label for="imageOfItem" class="imageSelector">Select item photo</label>
        <input required id="imageOfItem" hidden type="file" name="imageOfItem" accept="image/*"
            onchange="previewInputImage(this, document.querySelector('img.imageOfItemPreview'))">
        <img src="" style="display: none;" class="imageOfItemPreview">
    </div>
    <select required name="itemGroupId">
        <option selected disabled value="0">Select Item Groups</option>
        <?php
            $groupList = groupList();
            foreach ($groupList as $key) {
                echo "<option value='$key[group_id]'>$key[group_name]</option>";
            }
        ?>
    </select>
    <input type="number" min="0.01" minlength="1" step="0.01" required placeholder="Price" name="itemPrice" />
    <input type="submit" name="addItem" value="Add">
</form>
<table>
    <thead>
        <tr>
            <th>Item ID</th>
            <th>Item Image</th>
            <th>Item</th>
            <th>Group Name (id)</th>
            <th>Item Price</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="tablesList"></tbody>
</table>
<script>
    loadLink('/json', [['itemsList', 'admin']]).then(result => {
        console.log(result)
        if (Array.isArray(result.itemsList)) {
            let tablesList = document.querySelector("tbody.tablesList");
            result.itemsList.forEach(item => {
                const tr = create("tr");
                const td1 = create("td");
                const td2 = create("td");
                const td3 = create("td");
                const td4 = create("td");
                const td5 = create("td");
                const td6 = create("td");
                const del = create("span", "delbtn");

                tr.appendChild(td1);
                tr.appendChild(td6);
                tr.appendChild(td2);
                tr.appendChild(td3);
                tr.appendChild(td4);
                tr.appendChild(td5);
                td5.appendChild(del);

                td1.innerHTML = item.item_id;
                td2.innerHTML = item.item_name;
                td3.innerHTML = item.groupDetails ? item.groupDetails.group_name + ` (${item.groupDetails.group_id})` : "Other";
                td4.innerHTML = item.item_price;
                del.innerHTML = "Delete";

                img = create("img");
                img.src = item.item_pic;
                td6.prepend(img);

                del.onclick = e => {
                    if (confirm("Are sure to delete " + item.item_name)) {
                        loadLink('/json', [['deleteItem', item.item_id]]).then(result => {
                            if (result.deleteItem) {
                                notification("Deleted successfully!", "green");
                                tr.remove();
                            } else {
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