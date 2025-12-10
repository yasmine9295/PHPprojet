<?php
namespace App\Controllers;

use App\Core\Render;
use App\Core\Database;

class Admin
{
    private function checkAuth(): void
    {
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: /login");
            exit;
        }
    }

    public function index(): void
    {
        $this->checkAuth();
        
        $pdo = Database::getInstance()->getPDO();
        
        // Get stats
        $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $totalPages = $pdo->query("SELECT COUNT(*) FROM pages")->fetchColumn();
        $publishedPages = $pdo->query("SELECT COUNT(*) FROM pages WHERE is_published = true")->fetchColumn();
        
        $stats = json_encode([
            'total_users' => $totalUsers,
            'total_pages' => $totalPages,
            'published_pages' => $publishedPages
        ]);
        
        $render = new Render("dashboard", "backoffice");
        $render->assign("stats", $stats);
        $render->render();
    }

    public function users(): void
    {
        $this->checkAuth();
        
        $pdo = Database::getInstance()->getPDO();
        $request = $pdo->query("SELECT * FROM users ORDER BY date_created DESC");
        $users = $request->fetchAll();
        
        $render = new Render("users", "backoffice");
        $render->assign("users", $users);
        $render->render();
    }

    public function createUser(): void
    {
        $this->checkAuth();
        
        $error = null;
        $success = null;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = trim($_POST["username"] ?? "");
            $email = trim($_POST["email"] ?? "");
            $role = $_POST["role"] ?? "user";
            $password = $_POST["password"] ?? "";
            $isActive = isset($_POST["is_active"]) ? true : false;
            $confirmed = isset($_POST["confirmed"]) ? true : false;

            if (empty($username) || empty($email) || empty($password)) {
                 $error = "Tous les champs sont requis.";
            } elseif (!in_array($role, ['admin', 'user'])) {
                $error = "Rôle invalide.";
            } else {
                $pdo = Database::getInstance()->getPDO();
                
                // Vérifier si l'utilisateur existe déjà
                $request = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                $request->execute([$username, $email]);
                
                if ($request->fetch()) {
                    $error = "Username or email already exists.";
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    
                    $request = $pdo->prepare(
                        "INSERT INTO users (username, email, role, password, is_active, confirmed, date_created) 
                         VALUES (?, ?, ?, ?, ?, ?, NOW())"
                    );
                    $request->execute([$username, $email, $hashedPassword, $isActive, $confirmed]);
                    
                    $success = "User created successfully!";
                }
            }
        }

        $render = new Render("user_form", "backoffice");
        $render->assign("error", $error);
        $render->assign("success", $success);
        $render->assign("action", "create");
        $render->assign("user", null);
        $render->render();
    }

    public function editUser(): void
    {
        $this->checkAuth();
        
        $error = null;
        $success = null;
        $user = null;

        if (!isset($_GET["id"])) {
            header("Location: /admin/users");
            exit;
        }

        $userId = (int)$_GET["id"];
        $pdo = Database::getInstance()->getPDO();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = trim($_POST["username"] ?? "");
            $email = trim($_POST["email"] ?? "");
            $role = $_POST["role"] ?? "user";
            $isActive = isset($_POST["is_active"]) ? true : false;
            $confirmed = isset($_POST["confirmed"]) ? true : false;

            if (empty($username) || empty($email)) {
                $error = "Username et email sont requis.";
            } elseif (!in_array($role, ['admin', 'user'])) {
                $error = "Rôle invalide.";
            } else {
                // Vérifier si username/email existe déjà (sauf pour cet utilisateur)
                $request = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
                $request->execute([$username, $email, $userId]);
                
                if ($request->fetch()) {
                    $error = "Username or email already exists.";
                } else {
                    $updateQuery = "UPDATE users SET username = ?, email = ?, role = ?, is_active = ?, confirmed = ?, date_updated = NOW()";
                    $params = [$username, $email, $role, $isActive, $confirmed];
                    
                    // Si un nouveau mot de passe est fourni
                    if (!empty($_POST["password"])) {
                        $updateQuery .= ", password = ?";
                        $params[] = password_hash($_POST["password"], PASSWORD_DEFAULT);
                    }
                    
                    $updateQuery .= " WHERE id = ?";
                    $params[] = $userId;
                    
                    $request = $pdo->prepare($updateQuery);
                    $request->execute($params);
                    
                    $success = "User updated successfully!";
                }
            }
        }

        // Récupérer les données de l'utilisateur
        $request = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $request->execute([$userId]);
        $user = $request->fetch();

        if (!$user) {
            header("Location: /admin/users");
            exit;
        }

        $render = new Render("user_form", "backoffice");
        $render->assign("error", $error);
        $render->assign("success", $success);
        $render->assign("action", "edit");
        $render->assign("user", json_encode($user));
        $render->render();
    }

    public function deleteUser(): void
    {
        $this->checkAuth();
        
        if (!isset($_GET["id"])) {
            header("Location: /admin/users");
            exit;
        }

        $userId = (int)$_GET["id"];
        
        // Empêcher de supprimer son propre compte
        if ($userId === $_SESSION["user_id"]) {
            header("Location: /admin/users?error=cannot_delete_self");
            exit;
        }

        $pdo = Database::getInstance()->getPDO();
        $request = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $request->execute([$userId]);

        header("Location: /admin/users?success=user_deleted");
        exit;
    }

    public function pages(): void
    {
        $this->checkAuth();
        
        $pdo = Database::getInstance()->getPDO();
        $request = $pdo->query("
            SELECT p.*, u.username as author_name 
            FROM pages p 
            LEFT JOIN users u ON p.author_id = u.id 
            ORDER BY p.date_created DESC
        ");
        $pages = $request->fetchAll();
        
        $render = new Render("pages", "backoffice");
        $render->assign("pages", json_encode($pages));
        $render->render();
    }

    public function createPage(): void
    {
        $this->checkAuth();
        
        $error = null;
        $success = null;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $title = trim($_POST["title"] ?? "");
            $slug = trim($_POST["slug"] ?? "");
            $content = trim($_POST["content"] ?? "");
            $metaDescription = trim($_POST["meta_description"] ?? "");
            $metaKeywords = trim($_POST["meta_keywords"] ?? "");
            $isPublished = isset($_POST["is_published"]) ? true : false;

            if (empty($title) || empty($slug)) {
                $error = "Title and slug are required.";
            } else {
                $pdo = Database::getInstance()->getPDO();
                
                // Vérifier si le slug existe déjà
                $request = $pdo->prepare("SELECT id FROM pages WHERE slug = ?");
                $request->execute([$slug]);
                
                if ($request->fetch()) {
                    $error = "This slug already exists.";
                } else {
                    $request = $pdo->prepare(
                        "INSERT INTO pages (title, slug, content, meta_description, meta_keywords, is_published, author_id, date_created) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
                    );
                    $request->execute([$title, $slug, $content, $metaDescription, $metaKeywords, $isPublished, $_SESSION["user_id"]]);
                    
                    $success = "Page created successfully!";
                }
            }
        }

        $render = new Render("page_form", "backoffice");
        $render->assign("error", $error);
        $render->assign("success", $success);
        $render->assign("action", "create");
        $render->assign("page", null);
        $render->render();
    }

    public function editPage(): void
    {
        $this->checkAuth();
        
        $error = null;
        $success = null;
        $page = null;

        if (!isset($_GET["id"])) {
            header("Location: /admin/pages");
            exit;
        }

        $pageId = (int)$_GET["id"];
        $pdo = Database::getInstance()->getPDO();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $title = trim($_POST["title"] ?? "");
            $slug = trim($_POST["slug"] ?? "");
            $content = trim($_POST["content"] ?? "");
            $metaDescription = trim($_POST["meta_description"] ?? "");
            $metaKeywords = trim($_POST["meta_keywords"] ?? "");
            $isPublished = isset($_POST["is_published"]) ? true : false;

            if (empty($title) || empty($slug)) {
                $error = "Title and slug are required.";
            } else {
                // Vérifier si le slug existe déjà (sauf pour cette page)
                $request = $pdo->prepare("SELECT id FROM pages WHERE slug = ? AND id != ?");
                $request->execute([$slug, $pageId]);
                
                if ($request->fetch()) {
                    $error = "This slug already exists.";
                } else {
                    $request = $pdo->prepare(
                        "UPDATE pages SET title = ?, slug = ?, content = ?, meta_description = ?, 
                         meta_keywords = ?, is_published = ?, date_updated = NOW() WHERE id = ?"
                    );
                    $request->execute([$title, $slug, $content, $metaDescription, $metaKeywords, $isPublished, $pageId]);
                    
                    $success = "Page updated successfully!";
                }
            }
        }

        // Récupérer les données de la page
        $request = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
        $request->execute([$pageId]);
        $page = $request->fetch();

        if (!$page) {
            header("Location: /admin/pages");
            exit;
        }

        $render = new Render("page_form", "backoffice");
        $render->assign("error", $error);
        $render->assign("success", $success);
        $render->assign("action", "edit");
        $render->assign("page", json_encode($page));
        $render->render();
    }

    public function deletePage(): void
    {
        $this->checkAuth();
        
        if (!isset($_GET["id"])) {
            header("Location: /admin/pages");
            exit;
        }

        $pageId = (int)$_GET["id"];
        $pdo = Database::getInstance()->getPDO();
        
        $request = $pdo->prepare("DELETE FROM pages WHERE id = ?");
        $request->execute([$pageId]);

        header("Location: /admin/pages?success=page_deleted");
        exit;
    }
}