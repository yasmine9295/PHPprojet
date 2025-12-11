<?php $pagesData = json_decode($pages ?? '[]', true); ?>

<div class="user-pages">
    <div class="page-header">
        <h1> Mes Pages</h1>
        <a href="/my-pages/create" class="btn btn-success"> Créer une nouvelle page</a>
    </div>
    
    <?php if(empty($pagesData)): ?>
        <div class="empty-state">
            <h2>Aucune page pour le moment</h2>
            <p>Commencez par créer votre première page !</p>
            <a href="/my-pages/create" class="btn btn-primary">Créer ma première page</a>
        </div>
    <?php else: ?>
        <div class="pages-grid">
            <?php foreach($pagesData as $page): ?>
            <div class="page-card">
                <div class="page-card-header">
                    <h3><?= htmlspecialchars($page['title']) ?></h3>
                    <span class="badge badge-<?= $page['is_published'] ? 'success' : 'warning' ?>">
                        <?= $page['is_published'] ? ' Publiée' : ' Brouillon' ?>
                    </span>
                </div>
                
                <div class="page-card-body">
                    <p class="page-slug"> /{<?= htmlspecialchars($page['slug']) ?>}</p>
                    <p class="page-date"> Créée le <?= date('d/m/Y', strtotime($page['date_created'])) ?></p>
                    <?php if(!empty($page['content'])): ?>
                        <p class="page-excerpt"><?= htmlspecialchars(substr($page['content'], 0, 100)) ?>...</p>
                    <?php endif; ?>
                </div>
                
                <div class="page-card-actions">
                    <a href="/my-pages/view?id=<?= $page['id'] ?>" title="Prévisualiser">Prévisualiser</a>
                    <a href="/my-pages/edit?id=<?= $page['id'] ?>" title="Modifier">Modifier</a>
                    <a href="/my-pages/delete?id=<?= $page['id'] ?>" class="btn-icon btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette page ?')">supprimer</a>
                    <?php if($page['is_published']): ?>
                        <a href="/<?= htmlspecialchars($page['slug']) ?>" target="_blank" class="btn-icon" title="Voir sur le site">Retour vers home</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
