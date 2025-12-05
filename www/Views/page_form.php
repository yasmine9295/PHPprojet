<?php $pageData = json_decode($page ?? '{}', true); ?>
<div class="admin-section">
    <h2><?= $action === 'create' ? 'Create New Page' : 'Edit Page' ?></h2>
    
    <?php if(!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if(!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($pageData['title'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="slug">Slug (URL):</label>
            <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($pageData['slug'] ?? '') ?>" required>
            <small>Example: about-us, contact, services</small>
        </div>
        
        <div class="form-group">
            <label for="content">Content:</label>
            <textarea id="content" name="content" rows="10"><?= htmlspecialchars($pageData['content'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="meta_description">Meta Description:</label>
            <textarea id="meta_description" name="meta_description" rows="3"><?= htmlspecialchars($pageData['meta_description'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="meta_keywords">Meta Keywords:</label>
            <input type="text" id="meta_keywords" name="meta_keywords" value="<?= htmlspecialchars($pageData['meta_keywords'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label>
                <input type="checkbox" name="is_published" <?= ($pageData['is_published'] ?? false) ? 'checked' : '' ?>>
                Published
            </label>
        </div>
        
        <button type="submit"><?= $action === 'create' ? 'Create Page' : 'Update Page' ?></button>
        <a href="/admin/pages" class="btn btn-secondary">Cancel</a>
    </form>