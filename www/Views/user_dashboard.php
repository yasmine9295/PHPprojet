<?php $statsData = json_decode($stats ?? '{}', true); ?>

<div class="user-dashboard">
    <div class="welcome-section">
        <h1> Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
        <p>Gérez vos pages et votre contenu depuis votre espace personnel.</p>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Pages</h3>
            <p class="stat-number"><?= $statsData['total_pages'] ?? 0 ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Publiées</h3>
            <p class="stat-number"><?= $statsData['published_pages'] ?? 0 ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Brouillons</h3>
            <p class="stat-number"><?= $statsData['draft_pages'] ?? 0 ?></p>
        </div>
    </div>
    
    <div class="quick-actions">
        <h3>Actions rapides</h3>
        <div class="action-buttons">
            <a href="/my-pages" class="btn btn-primary"> Mes Pages</a>
            <a href="/my-pages/create" class="btn btn-success"> Créer une page</a>
            <a href="/" class="btn btn-secondary"> Voir le site</a>
        </div>
    </div>
</div>