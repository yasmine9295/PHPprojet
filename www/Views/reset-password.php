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

<!-- <div class="auth-container">
    <h2>Reset Password</h2>
    
    <form method="POST">
        <div class="form-group">
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <button type="submit">Reset Password</button>
    </form>
    
    <p><a href="/login">Back to Login</a></p>
</div> -->
