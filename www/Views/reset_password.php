<div class="auth-container">
    <h2>Reinitialiser le mot de passe</h2>
    
    <?php if(!empty($error)): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if(!empty($success)): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
        <a href="/login" class="btn-login">
           Se connecter
        </a>

    <?php else: ?>

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

            <a href="/login" class="btn-login"
               style="margin-left: 10px; text-decoration:none;">Connexion</a>
        </form>

    <?php endif; ?>
</div>
