<?php $pagesData = json_decode($pages ?? '[]', true); ?>
<div class="admin-section">
    <h2>Page Management</h2>
    
    <a href="/admin/pages/create" class="btn btn-primary">Ajouter New Page</a>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Slug</th>
                <th>Author</th>
                <th>Published</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($pagesData as $page): ?>
            <tr>
                <td><?= $page['id'] ?></td>
                <td><?= htmlspecialchars($page['title']) ?></td>
                <td><?= htmlspecialchars($page['slug']) ?></td>
                <td><?= htmlspecialchars($page['author_name'] ?? 'N/A') ?></td>
                <td><?= $page['is_published'] ? '✓' : '✗' ?></td>
                <td><?= date('Y-m-d', strtotime($page['date_created'])) ?></td>
                <td>
                    <a href="/<?= htmlspecialchars($page['slug']) ?>" target="_blank">View</a> |
                    <a href="/admin/pages/edit?id=<?= $page['id'] ?>">Edit</a> |
                    <a href="/admin/pages/delete?id=<?= $page['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>