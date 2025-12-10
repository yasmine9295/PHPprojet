<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backoffice - Admin Panel</title>
    
</head>
<body>
    <div class="header">
        <?php if(isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <h1>Back Office</h1>
        <div class="user-info">
            <?php if(isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="/logout" style="color: white;">Logout</a>
            <?php endif; ?>
        </div>
        <?php else: ?>
            <h1>Front Office</h1>
        <?php endif; ?>

    </div>
    
    <?php if(isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
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