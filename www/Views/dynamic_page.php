<div class="page-content">
    <h1><?= htmlspecialchars($title ?? 'Page') ?></h1>
    <div class="content">
        <?= nl2br(htmlspecialchars($content ?? '')) ?>
    </div>
</div>