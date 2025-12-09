<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'My Website') ?></title>
    <?php if(!empty($meta_description)): ?>
        <meta name="description" content="<?= htmlspecialchars($meta_description) ?>">
    <?php endif; ?>
    <?php if(!empty($meta_keywords)): ?>
        <meta name="keywords" content="<?= htmlspecialchars($meta_keywords) ?>">
    <?php endif; ?>
    
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Front Office</h1>
            <nav>
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/contact">Contact</a></li>
                    <li><a href="/portfolio">Portfolio</a></li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li><a href="/dashboard">Dashboard</a></li>
                        <li><a href="/logout">Logout</a></li>
                    <?php else: ?>
                        <li><a href="/login">Login</a></li>
                        <li><a href="/register">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="container">
        <?php include $this->viewPath; ?>
    </div>
    
    <footer>
        <p>&copy; <?= date('Y') ?> My Website. All rights reserved.</p>
    </footer>
</body>
</html>