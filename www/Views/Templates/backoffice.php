<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backoffice - Admin Panel</title>
    
</head>

<?php
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$username = $_SESSION['username'] ?? null;
?>

<body>
    <div class="header">
        <?php if ($isAdmin): ?>
            <h1>Back Office</h1>
            <div class="user-info">
                <span>Welcome, <?= htmlspecialchars($username) ?></span>
                <a href="/logout" style="color: white;">Logout</a>
            </div>
        <?php else: ?>
            <h1>Front Office</h1>
        <?php endif; ?>
    </div>
        <?php if ($isAdmin): ?>
        <nav class="nav-menu">
            <ul>
                <li><a href="/dashboard">Dashboard</a></li>
                <li><a href="/admin/users">Users</a></li>
                <li><a href="/admin/pages">Pages</a></li>
                <li><a href="/">View Site</a></li>
            </ul>
        </nav>
        <?php endif; ?>
    <div class="container">
        <?php include $this->viewPath; ?>
    </div>
</body>
</html>