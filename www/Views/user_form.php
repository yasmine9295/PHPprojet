<?php $userData = json_decode($user ?? '{}', true); ?>
<div class="admin-section">
    <h2><?= $action === 'create' ? 'Create New User' : 'Edit User' ?></h2>
    
    <?php if(!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if(!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($userData['username'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($userData['email'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="role">Rôle: </label>
            <select id="role" name="role" required>
                <option value="user" <?= ($userData && $userData['role'] === 'user') ? 'selected' : '' ?>>Utilisateur</option>
                <option value="admin" <?= ($userData && $userData['role'] === 'admin') ? 'selected' : '' ?>>Administrateur</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="password">Password <?= $action === 'edit' ? '(leave empty to keep current)' : '' ?>:</label>
            <input type="password" id="password" name="password" <?= $action === 'create' ? 'required' : '' ?>>
        </div>
        
        <div class="form-group">
            <label>
                <input type="checkbox" name="is_active" <?= ($userData['is_active'] ?? true) ? 'checked' : '' ?>>
                Active
            </label>
        </div>
        
        <div class="form-group">
            <label>
                <input type="checkbox" name="confirmed" <?= ($userData['confirmed'] ?? false) ? 'checked' : '' ?>>
                Confirmed
            </label>
        </div>
        
        <button type="submit"><?= $action === 'create' ? 'Create User' : 'Update User' ?></button>
        <a href="/admin/users" class="btn btn-secondary">Cancel</a>
    </form>
</div>