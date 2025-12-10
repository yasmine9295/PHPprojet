<?php 
$usersData = [];
if (isset($users)) {
    $usersData = is_string($users) ? json_decode($users, true) : $users;
}
?>

<div class="admin-section">
    <h2>User Management</h2>
    
    <a href="/admin/users/create" class="btn btn-primary">Add New User</a>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Confirmed</th>
                <th>Active</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($usersData as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td>
                    <span class="badge badge-<?= $user['role'] === 'admin' ? 'danger' : 'info' ?>">
                        <?= $user['role'] === 'admin' ? 'Admin' : 'User' ?>
                    </span>
                </td>
                <td><?= $user['confirmed'] ? '✓' : '✗' ?></td>
                <td><?= $user['is_active'] ? '✓' : '✗' ?></td>
                <td><?= date('Y-m-d', strtotime($user['date_created'])) ?></td>
                <td>
                    <a href="/admin/users/edit?id=<?= $user['id'] ?>">Edit</a> |
                    <a href="/admin/users/delete?id=<?= $user['id'] ?>" onclick="return confirm('Vous êtes sûr de vouloir supprimer cet user?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>