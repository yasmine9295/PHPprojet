<?php
$conn = new PDO('pgsql:host=pg-db;dbname=devdb', 'devuser', 'devpass');


$token = $_GET['token'];

// si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? null;
    if (!$token) {
        die("token manquant");
    }

    $password = trim($_POST['password']);
    $hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("UPDATE users SET password = :hash, reset_token = NULL WHERE reset_token = :token");
    $stmt->bindValue(':hash', $hash);
    $stmt->bindValue(':token', $token);
    $stmt->execute();

    echo "mot de passe réinitialisé avec succès";
}

?>

<form method="POST">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

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


