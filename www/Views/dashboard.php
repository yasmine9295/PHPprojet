<?php $statsData = json_decode($stats ?? '{}', true); ?>
<div class="dashboard">
    <h2>Admin Dashboard</h2>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Users</h3>
            <p class="stat-number"><?= $statsData['total_users'] ?? 0 ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Total Pages</h3>
            <p class="stat-number"><?= $statsData['total_pages'] ?? 0 ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Pages publier</h3>
            <p class="stat-number"><?= $statsData['published_pages'] ?? 0 ?></p>
        </div>
    </div>
    
    <div class="quick-actions">
        <h3>Actions Gestions</h3>
        <a href="/admin/users" class="btn">Gestion Users</a>
        <a href="/admin/pages" class="btn">Gestion Pages</a>
        <a href="/admin/pages/create" class="btn">Créer New Page</a>
    </div>
</div>