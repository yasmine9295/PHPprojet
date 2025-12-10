<?php

namespace App\Controllers;

use App\Core\Render;
use App\Core\Database;

class Base
{

    public function index(): void
    {

        // if (!isset($_SESSION['username'])) {
        //     header('Location: /login');
        //     exit();
        // } else {
        //     header('Location: /dashboard');
        //     exit();

        // }

        $render = new Render("home", "frontoffice");
        $render->assign("pseudo", $_SESSION['username'] ?? "Visiteur");
        $render->render();
    }

    public function contact(): void
    {
        $render = new Render("contact", "frontoffice");
        $render->render();
    }


    public function portfolio(): void
    {
        $render = new Render("portfolio", "frontoffice");
        $render->render();
    }

    public function dynamicPage(string $slug): void
    {
        $pdo = Database::getInstance()->getPDO();
        $request = $pdo->prepare("SELECT * FROM pages WHERE slug = ? AND is_published = true");
        $request->execute([$slug]);
        $page = $request->fetch();

        if (!$page) {
            http_response_code(404);
            $render = new Render("404", "frontoffice");
            $render->render();
            return;
        }

        $render = new Render("dynamic_page", "frontoffice");
        $render->assign("title", $page['title']);
        $render->assign("content", $page['content']);
        $render->assign("meta_description", $page['meta_description']);
        $render->assign("meta_keywords", $page['meta_keywords']);
        $render->render();
    }

}