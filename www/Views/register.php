<?php
$conn = new mysqli("db", "devuser", "devpass", "devdb");
if ($conn->connect_error) die("Connection failed: ".$conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $token = bin2hex(random_bytes(16));


    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? OR username=?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows > 0){
        die("Email or username already in use");
    }


    $stmt = $conn->prepare("INSERT INTO users (username, email, password, confirmation_token) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hash, $token);
    $stmt->execute();


    $link = "http://localhost:8080/confirm.php?token=".$token;
    mail($email, "Confirm Your Account", "Click here to confirm your account: $link");

    echo "Registration successful, check your email!";
}
?>
