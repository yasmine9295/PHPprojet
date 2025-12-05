<?php
$conn = new mysqli("db", "devuser", "devpass", "devdb");
if(isset($_GET['token'])){
    $token = $_GET['token'];
    $stmt = $conn->prepare("UPDATE users SET confirmed=TRUE, confirmation_token=NULL WHERE confirmation_token=?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    
}
?>