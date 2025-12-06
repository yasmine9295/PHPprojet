<?php
namespace App\Controllers;

use App\Core\Render;
use App\Core\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Auth
{
    // je gère la connexion de l'utilisateur
    public function login(): void
    {
        $error = null;

        // je vérifie si le formulaire a été soumis
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = trim($_POST["username"]);
            $password = trim($_POST["password"]);

            // je récupère la connexion à la base de données
            $pdo = Database::getInstance()->getPDO();

            // je cherche l'utilisateur correspondant au username
            $stmt = $pdo->prepare("SELECT id, password, confirmed FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            // je vérifie que l'utilisateur existe et que le mot de passe est correct
            if ($user && password_verify($password, $user["password"])) {

                // je vérifie que le compte est confirmé
                if ($user["confirmed"]) {
                    // je crée la session
                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["username"] = $username;
                    // je redirige vers le dashboard
                    header("Location: /dashboard");
                    exit;

                } else {
                    $error = "vous n'avez pas encore confirmé votre compte par mail.";
                }

            } else {
                $error = "username ou password incorrect.";
            }
        }

        // je charge la vue login avec les messages d'erreur
        $render = new Render("login", "backoffice");
        $render->assign("error", $error);
        $render->render();
    }

    // je gère l'inscription de l'utilisateur
    public function register(): void
    {
        $success = null;
        $error = null;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = trim($_POST["username"]);
            $email = trim($_POST["email"]);
            $password = trim($_POST["password"]);
            $confirmPassword = trim($_POST["confirm_password"]);

            // je vérifie que les mots de passe correspondent
            if ($password !== $confirmPassword) {
                $error = "les passwords ne sont pas les mêmes.";
            } 
            // je vérifie la longueur du mot de passe
            elseif (strlen($password) < 8) {
                $error = "password doit avoir au moins 8 caractères.";
            } 
            else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $pdo = Database::getInstance()->getPDO();

                // je vérifie si le username ou l'email existe déjà
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $email]);

                if ($stmt->fetch()) {
                    $error = "cet username ou email est déjà utilisé.";
                } else {
                    // je génère un token aléatoire pour confirmer le compte
                    $token = bin2hex(random_bytes(32));

                    // je crée l'utilisateur avec le token
                    $stmt = $pdo->prepare(
                        "INSERT INTO users (username, email, password, confirmed, confirmation_token) 
                        VALUES (?, ?, ?, false, ?)"
                    );
                    $stmt->execute([$username, $email, $passwordHash, $token]);

                    // je prépare l'email de confirmation
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'mailhog'; // je me connecte au container mailhog
                        $mail->Port = 1025; // port smtp de mailhog
                        $mail->SMTPAuth = false; // pas besoin d'authentification

                        $mail->setFrom('noreply@tonsite.com', 'tonsite');
                        $mail->addAddress($email, $username);

                        $mail->Subject = 'confirmation de votre compte';
                        // je crée le corps du mail avec le lien de confirmation
                        $mail->Body = "bonjour $username,\n\ncliquez sur ce lien pour confirmer votre compte : http://localhost:8080/confirm?token=$token";

                        $mail->send();
                        $success = "utilisateur ajouté avec succès ! veuillez vérifier votre email pour terminer l'inscription.";

                    } catch (Exception $e) {
                        // si l'email ne s'envoie pas, je récupère l'erreur
                        $error = "erreur lors de l'envoi de l'email : " . $mail->ErrorInfo;
                    }
                }
            }
        }

        // je charge la vue register avec les messages
        $render = new Render("register", "backoffice");
        $render->assign("error", $error);
        $render->assign("success", $success);
        $render->render();
    }

    // je gère la confirmation du compte
    public function confirm(): void
    {
        $token = $_GET['token'] ?? null;
        $error = null;
        $success = null;

        if ($token) {
            $pdo = Database::getInstance()->getPDO();
            // je cherche l'utilisateur correspondant au token
            $stmt = $pdo->prepare("SELECT id FROM users WHERE confirmation_token = ?");
            $stmt->execute([$token]);
            $user = $stmt->fetch();

            if ($user) {
                // je confirme le compte et supprime le token
                $stmt = $pdo->prepare("UPDATE users SET confirmed = true, confirmation_token = NULL WHERE id = ?");
                $stmt->execute([$user['id']]);
                $success = "votre compte est maintenant confirmé !";
            } else {
                $error = "token invalide.";
            }
        } else {
            $error = "aucun token fourni.";
        }

        // je charge la vue confirm avec les messages
        $render = new Render("confirm", "backoffice");
        $render->assign("error", $error);
        $render->assign("success", $success);
        $render->render();
    }

    // je charge la page mot de passe oublié
    public function forgot(): void
    {
        $render = new Render("forgot_password", "backoffice");
        $render->render();
    }

    // je charge la page de reset du mot de passe
    public function reset(): void
    {
        $render = new Render("reset_password", "backoffice");
        $render->render();
    }

    // je déconnecte l'utilisateur
    public function logout(): void
    {
        session_destroy();
        header("Location: /login");
        exit;
    }
}
