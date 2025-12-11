<?php
namespace App\Controllers;

use App\Core\Render;
use App\Core\Database;

class User
{
    private function checkAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }
    }

    // Dashboard utilisateur
    public function dashboard(): void
    {
        $this->checkAuth();
        
        $pdo = Database::getInstance()->getPDO();
        
        // Statistiques de l'utilisateur
        $userId = $_SESSION['user_id'];
        $totalPages = $pdo->prepare("SELECT COUNT(*) FROM pages WHERE author_id = ?");
        $totalPages->execute([$userId]);
        $total = $totalPages->fetchColumn();
        
        $publishedPages = $pdo->prepare("SELECT COUNT(*) FROM pages WHERE author_id = ? AND is_published = true");
        $publishedPages->execute([$userId]);
        $published = $publishedPages->fetchColumn();
        
        $stats = json_encode([
            'total_pages' => $total,
            'published_pages' => $published,
            'draft_pages' => $total - $published
        ]);
        
        $render = new Render("user_dashboard", "frontoffice");
        $render->assign("stats", $stats);
        $render->render();
    }

    // Liste des pages de l'utilisateur
    public function myPages(): void
    {
        $this->checkAuth();
        
        $pdo = Database::getInstance()->getPDO();
        $userId = $_SESSION['user_id'];
        
        $stmt = $pdo->prepare("
            SELECT p.* 
            FROM pages p 
            WHERE p.author_id = ?
            ORDER BY p.date_created DESC
        ");
        $stmt->execute([$userId]);
        $pages = $stmt->fetchAll();
        
        $render = new Render("user_pages", "frontoffice");
        $render->assign("pages", json_encode($pages));
        $render->render();
    }

    // Créer une page
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
                // Valider le slug (seulement lettres, chiffres, tirets)
                if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
                    $error = "Slug can only contain lowercase letters, numbers and hyphens.";
                } else {
                    $pdo = Database::getInstance()->getPDO();
                    
                    // Vérifier si le slug existe déjà
                    $stmt = $pdo->prepare("SELECT id FROM pages WHERE slug = ?");
                    $stmt->execute([$slug]);
                    
                    if ($stmt->fetch()) {
                        $error = "This slug already exists. Please choose another one.";
                    } else {
                        $stmt = $pdo->prepare(
                            "INSERT INTO pages (title, slug, content, meta_description, meta_keywords, is_published, author_id, date_created) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
                        );
                        $stmt->execute([
                            $title, 
                            $slug, 
                            $content, 
                            $metaDescription, 
                            $metaKeywords, 
                            $isPublished, 
                            $_SESSION["user_id"]
                        ]);
                        
                        $success = "Page created successfully!";
                        
                        // Rediriger vers la liste après 2 secondes
                        header("refresh:2;url=/my-pages");
                    }
                }
            }
        }

        $render = new Render("user_page_form", "frontoffice");
        $render->assign("error", $error);
        $render->assign("success", $success);
        $render->assign("action", "create");
        $render->assign("page", null);
        $render->render();
    }

    // Modifier une page
    public function editPage(): void
    {
        $this->checkAuth();
        
        $error = null;
        $success = null;
        $page = null;

        if (!isset($_GET["id"])) {
            header("Location: /my-pages");
            exit;
        }

        $pageId = (int)$_GET["id"];
        $pdo = Database::getInstance()->getPDO();

        // Vérifier que l'utilisateur est bien l'auteur de la page
        $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ? AND author_id = ?");
        $stmt->execute([$pageId, $_SESSION["user_id"]]);
        $page = $stmt->fetch();

        if (!$page) {
            header("Location: /my-pages?error=unauthorized");
            exit;
        }

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
                // Valider le slug
                if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
                    $error = "Slug peut seulement contenir lowercase letters, numbers and tiret.";
                } else {
                    // Vérifier si le slug existe déjà (sauf pour cette page)
                    $stmt = $pdo->prepare("SELECT id FROM pages WHERE slug = ? AND id != ?");
                    $stmt->execute([$slug, $pageId]);
                    
                    if ($stmt->fetch()) {
                        $error = "This slug already exists. Please choose another one.";
                    } else {
                        $stmt = $pdo->prepare(
                            "UPDATE pages SET title = ?, slug = ?, content = ?, meta_description = ?, 
                             meta_keywords = ?, is_published = ?, date_updated = NOW() 
                             WHERE id = ? AND author_id = ?"
                        );
                        $stmt->execute([
                            $title, 
                            $slug, 
                            $content, 
                            $metaDescription, 
                            $metaKeywords, 
                            $isPublished, 
                            $pageId,
                            $_SESSION["user_id"]
                        ]);
                        
                        $success = "Page updated successfully!";
                        
                        // Recharger les données de la page
                        $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
                        $stmt->execute([$pageId]);
                        $page = $stmt->fetch();
                    }
                }
            }
        }

        $render = new Render("user_page_form", "frontoffice");
        $render->assign("error", $error);
        $render->assign("success", $success);
        $render->assign("action", "edit");
        $render->assign("page", json_encode($page));
        $render->render();
    }

    // Supprimer une page
    public function deletePage(): void
    {
        $this->checkAuth();
        
        if (!isset($_GET["id"])) {
            header("Location: /my-pages");
            exit;
        }

        $pageId = (int)$_GET["id"];
        $pdo = Database::getInstance()->getPDO();
        
        // Vérifier que l'utilisateur est bien l'auteur avant de supprimer
        $stmt = $pdo->prepare("DELETE FROM pages WHERE id = ? AND author_id = ?");
        $stmt->execute([$pageId, $_SESSION["user_id"]]);

        if ($stmt->rowCount() > 0) {
            header("Location: /my-pages?success=page_deleted");
        } else {
            header("Location: /my-pages?error=unauthorized");
        }
        exit;
    }

    // Voir une de ses pages (preview)
    public function viewPage(): void
    {
        $this->checkAuth();
        
        if (!isset($_GET["id"])) {
            header("Location: /my-pages");
            exit;
        }

        $pageId = (int)$_GET["id"];
        $pdo = Database::getInstance()->getPDO();
        
        // Récupérer la page (même si non publiée, car c'est l'auteur)
        $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ? AND author_id = ?");
        $stmt->execute([$pageId, $_SESSION["user_id"]]);
        $page = $stmt->fetch();

        if (!$page) {
            header("Location: /my-pages?error=unauthorized");
            exit;
        }

        $render = new Render("user_page_preview", "frontoffice");
        $render->assign("title", $page['title']);
        $render->assign("content", $page['content']);
        $render->assign("slug", $page['slug']);
        $render->assign("is_published", $page['is_published']);
        $render->render();
    }
}