<?php
$conn = new mysqli("db", "devuser", "devpass", "devdb");
if(isset($_GET['token']) && $_SERVER['REQUEST_METHOD']==='POST'){
    $password = trim($_POST['password']);
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $token = $_GET['token'];

    $stmt = $conn->prepare("UPDATE users SET password=?, reset_token=NULL WHERE reset_token=?");
    $stmt->bind_param("ss", $hash, $token);
    $stmt->execute();
    echo "reinitialized";
}
?>
