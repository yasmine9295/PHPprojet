<?php
namespace App\Controllers;

use App\Core\Render;
use App\Core\Database;

class Auth
{
    public function login(): void
    {
        $error = null;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = trim($_POST["username"]);
            $password = trim($_POST["password"]);

            $pdo = Database::getInstance()->getPDO();

            $stmt = $pdo->prepare("SELECT id, password, confirmed FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user["password"])) {

                if ($user["confirmed"]) {
                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["username"] = $username;
                    header("Location: /dashboard");
                    exit;

                } else {
                    $error = "Vous n'avez pas encore confirmé votre compte par mail.";
                }

            } else {
                $error = "Username ou password incorrect.";
            }
        }

        $render = new Render("login", "backoffice");
        $render->assign("error", $error);
        $render->render();
    }

    public function register(): void
    {
        $success = null;
        $error = null;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = trim($_POST["username"]);
            $email = trim($_POST["email"]);
            $password = trim($_POST["password"]);
            $confirmPassword = trim($_POST["confirm_password"]);

            if ($password !== $confirmPassword) {
                $error = "Les passwords ne sont pas les mêmes.";
            } elseif (strlen($password) < 8) {
                $error = "Password doit avoir au moins 8 characters.";
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $pdo = Database::getInstance()->getPDO();

                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $email]);

                if ($stmt->fetch()) {
                    $error = "Cet username ou email est déjà utilisé.";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, confirmed) VALUES (?, ?, ?, false)");
                    $stmt->execute([$username, $email, $passwordHash]);

                    $success = "Utilisateur ajouter avec succès! Veuillez vérifier votre email pour terminer l'inscription.";
                }
            }
        }

        $render = new Render("register", "backoffice");
        $render->assign("error", $error);
        $render->assign("success", $success);
        $render->render();
    }

    public function confirm(): void
    {
        $render = new Render("confirm", "backoffice");
        $render->render();
    }

    public function forgot(): void
    {
        $render = new Render("forgot_password", "backoffice");
        $render->render();
    }

    public function reset(): void
    {
        $render = new Render("reset_password", "backoffice");
        $render->render();
    }

    public function logout(): void
    {
        session_destroy();
        header("Location: /login");
        exit;
    }
}
