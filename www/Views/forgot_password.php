<?php

$conn = new PDO('pgsql:host=pg-db;dbname=devdb', 'devuser', 'devpass');

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $email = trim($_POST['email']);
        $token = bin2hex(random_bytes(16));

        $stmt = $conn->prepare("UPDATE users SET reset_token=? WHERE email=?");
        $stmt->execute([$token, $email]);
        $stmt->execute();

        $link = "http://localhost:8080/reset.php?token=".$token;
        mail($email, "Password Reset", "Click here to reset your password: $link");

    }
?>
 <div class="auth-container">
    <h2>Forgot Password</h2>
    
        <form method="POST">
            <div class="form-group">
                <label for="email">Enter your email address:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <button type="submit">Send Reset Link</button>
        </form>
    
    <p><a href="/login">Back to Login</a></p>
</div> 