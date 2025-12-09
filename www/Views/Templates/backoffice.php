<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backoffice - Admin Panel</title>
    
</head>
<body>
    <div class="header">
        <h1>Back Office</h1>
        <div class="user-info">
            <?php if(isset($_SESSION['username']) && isset($_SESSION['roles'])=='administrateur' )?>
                <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="/logout" style="color: white;">Logout</a>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if(isset($_SESSION['user_id'])): ?>
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