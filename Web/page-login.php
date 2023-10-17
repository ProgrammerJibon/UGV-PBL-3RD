<?php
$errorText = "";
if(strtoupper($_SERVER['REQUEST_METHOD']) == "POST"){
    if(isset($_POST[$_SESSION['password_input']])){
        $passwrod = addslashes($_POST[$_SESSION['password_input']]);
        if(!empty($passwrod)){
            if(md5(sha1($passwrod)) == $admin_pass){
                $_SESSION['admin'] = $admin_pass;
                header("Location: /");
                exit();
            }else{
                $errorText = "Wrong password!";
            }
        }else{
            $errorText = "Invalid password!";
        }
    }else{
        $errorText = "Invalid action!";
    }
}

if(isset($_SESSION['admin']) && $_SESSION['admin'] && $_SESSION['admin'] == $admin_pass ){
    header("Location: /");
    exit();
}
$_SESSION['password_input'] = md5($time);
?>
<script src="/script.js"></script>
<form action="" method="post">
    <div>
        <h1>Welcome back Admin</h1>
    </div>
    <div>
        <font><?php echo $errorText;?></font>
    </div>
    <label>
        <div>
            <span>Enter your admin password: </span>
        </div>
        <input type="password" autofocus placeholder="•_•" name="<?php echo $_SESSION['password_input']?>">
    </label>
    <div>
        <button type="submit">Login</button>
    </div>
</form>
<style>
*{
    box-sizing: border-box;
}
body{
    background-color: #0e0e0e;
    color: #00a7cd;
}
form {
    min-width: 450px;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 32px;
    border-radius: 6px;
    box-shadow: 5px 5px 15px 5px #00dcff;
    border: 2px solid #00d0ff;
}
h1 {
    text-align: center;
}
input {
    width: 100%;
    padding: 8px;
    text-align: center;
    font-size: 16px;
    border: 1px solid #00d0ff;
    color: #00d0ff;
    margin: 8px 0;
    outline-color: #00a1ff;
    border-radius: 3px;
}
button{
    width: 100%;
    padding: 12px;
    background: #00d0ff;
    border: 2px solid #00d0ff;
    color: white;
    text-transform: uppercase;
    margin: 8px 0;
    border-radius: 3px;
    cursor: pointer;
}
button:hover {
    background: #005c70;
    color: #00d0ff;
}
font {
    color: red;
    display: inline-block;
    text-align: center;
    width: 100%;
    height: 20px;
}
</style>