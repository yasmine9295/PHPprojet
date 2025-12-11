<div class="page-preview-container">
    <div class="preview-header">
        <h1> Prévisualisation</h1>
        <div class="preview-actions">
            <?php if($is_published): ?>
                <a href="/<?= htmlspecialchars($slug) ?>" target="_blank" class="btn btn-success"> Voir sur le site</a>
            <?php else: ?>
                <span class="badge badge-warning"> Cette page n'est pas encore publiée</span>
            <?php endif; ?>
            <a href="/my-pages/edit?id=<?= $_GET['id'] ?>" class="btn btn-primary"> Modifier</a>
            <a href="/my-pages" class="btn btn-secondary">← Retour à mes pages</a>
        </div>
    </div>
    
    <div class="preview-box">
        <div class="preview-label">Aperçu de votre page</div>
        
        <div class="page-preview-content">
            <h2 class="preview-title"><?= htmlspecialchars($title) ?></h2>
            <div class="preview-slug"> URL: /<?= htmlspecialchars($slug) ?></div>
            
            <div class="preview-content">
                <?= nl2br(htmlspecialchars($content)) ?>
            </div>
        </div>
    </div>
</div>