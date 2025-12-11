<?php $pageData = $page ? json_decode($page, true) : null; ?>

<div class="user-page-form">
    <div class="form-header">
        <h1><?= $action === 'create' ? ' Créer une nouvelle page' : ' Modifier la page' ?></h1>
        <a href="/my-pages" class="btn-back">← Retour à mes pages</a>
    </div>
    
    <?php if(!empty($error)): ?>
        <div class="alert alert-error">
            <strong> Erreur</strong><br>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <?php if(!empty($success)): ?>
        <div class="alert alert-success">
            <strong> Succès</strong><br>
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" class="page-form">
        <div class="form-grid">
            <div class="form-section">
                <h3> Contenu Principal</h3>
                
                <div class="form-group">
                    <label for="title">Titre de la page *</label>
                    <input type="text" id="title" name="title" required
                           value="<?= $pageData ? htmlspecialchars($pageData['title']) : '' ?>"
                           placeholder="Ex: À propos de nous">
                    <small>Le titre qui apparaîtra en haut de votre page</small>
                </div>
                
                <div class="form-group">
                    <label for="slug">URL (Slug) *</label>
                    <input type="text" id="slug" name="slug" required pattern="[a-z0-9\-]+"
                           value="<?= $pageData ? htmlspecialchars($pageData['slug']) : '' ?>"
                           placeholder="a-propos">
                    <small>Uniquement lettres minuscules, chiffres et tirets (ex: mon-article-123)</small>
                </div>
                
                <div class="form-group">
                    <label for="content">Contenu</label>
                    <textarea id="content" name="content" rows="12" 
                              placeholder="Rédigez le contenu de votre page ici..."><?= $pageData ? htmlspecialchars($pageData['content']) : '' ?></textarea>
                    <small>Le contenu principal de votre page</small>
                </div>
            </div>
            
            <div class="form-section">
                <h3> SEO & Options</h3>
                
                <div class="form-group">
                    <label for="meta_description">Description SEO</label>
                    <textarea id="meta_description" name="meta_description" rows="3"
                              placeholder="Description courte pour les moteurs de recherche..."><?= $pageData ? htmlspecialchars($pageData['meta_description']) : '' ?></textarea>
                    <small>150-160 caractères recommandés</small>
                </div>
                
                <div class="form-group">
                    <label for="meta_keywords">Mots-clés SEO</label>
                    <input type="text" id="meta_keywords" name="meta_keywords"
                           value="<?= $pageData ? htmlspecialchars($pageData['meta_keywords']) : '' ?>"
                           placeholder="web, design, développement">
                    <small>Séparez les mots-clés par des virgules</small>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_published" 
                               <?= ($pageData && $pageData['is_published']) ? 'checked' : '' ?>>
                        <span> Publier cette page</span>
                    </label>
                                </div>
                
                <div class="form-status">
                    <?php if($action === 'edit'): ?>
                        <p><strong>Statut actuel:</strong> 
                            <span class="badge badge-<?= ($pageData && $pageData['is_published']) ? 'success' : 'warning' ?>">
                                <?= ($pageData && $pageData['is_published']) ? ' Publiée' : ' Brouillon' ?>
                            </span>
                        </p>
                        <p><strong>Créée le:</strong> <?= date('d/m/Y à H:i', strtotime($pageData['date_created'])) ?></p>
                        <?php if(!empty($pageData['date_updated'])): ?>
                            <p><strong>Modifiée le:</strong> <?= date('d/m/Y à H:i', strtotime($pageData['date_updated'])) ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?= $action === 'create' ? ' Créer la page' : ' Enregistrer les modifications' ?>
            </button>
            <a href="/my-pages" class="btn btn-secondary"> Annuler</a>
            <?php if($action === 'edit' && $pageData): ?>
                <a href="/my-pages/view?id=<?= $pageData['id'] ?>" class="btn btn-info"> Prévisualiser</a>
            <?php endif; ?>
        </div>
    </form>
</div>