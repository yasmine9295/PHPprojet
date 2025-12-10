
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
    <button> <a href="/login">connexion</a></button>

</form>


