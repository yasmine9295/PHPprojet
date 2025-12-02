<?php
$conn = new mysqli("db", "devuser", "devpass", "devdb");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['email']);
    $token = bin2hex(random_bytes(16));

    $stmt = $conn->prepare("UPDATE users SET reset_token=? WHERE email=?");
    $stmt->bind_param("ss", $token, $email);
    $stmt->execute();

    $link = "http://localhost:8080/reset.php?token=".$token;
    mail($email, "Password Reset", "Click here to reset your password: $link");

}
?>
