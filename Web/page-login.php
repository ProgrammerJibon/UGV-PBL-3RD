<?php
if(isset($_POST['password'])){
    $passwrod = addslashes($_POST['password']);
    if($passwrod == "123"){
        $_SESSION['admin'] = true;
        header("Location: /");
        exit();
    }
}
?>
<script src="/script.js"></script>
<body></body>
<script>
const form = create("form");
const passInput = create("input");
form.appendChild(passInput);
form.method = "POST";
passInput.type = "password";
passInput.name = "password";
form.style.display = "none";
document.querySelector("body").appendChild(form);
const password = prompt("Enter password!");
passInput.value = password;
form.submit();
</script>