<?php
namespace App\Controllers;

use PDO;
use App\Core\Render;
use App\Core\Database;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Auth
{
    // connexion de l'utilisateur
    public function login(): void
    {
        $error = null;

        // si le formulaire est envoyé
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = ($_POST['password']);

            $pdo = Database::getInstance()->getPDO();

            // on récupère l'utilisateur correspondant au username
            $stmt = $pdo->prepare("SELECT id, password, role, confirmed FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            // on vérifie si l'utilisateur existe et le mot de passe
            if ($user && password_verify($password, $user['password'])) {
                if (!$user['confirmed']) {
                    $error = "Votre compte n'est pas encore confirmé. Vérifiez vos emails.";
                    // die("Votre compte n'est pas encore confirmé. Vérifiez vos emails.");
                } else {
                    // connexion réussie
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $username;
                    $_SESSION["role"] = $user["role"];

                    if ($user["role"] === "admin") {
                        header("Location: /dashboard");
                    } else {
                        header("Location: /");
                    }
                    exit;
                }
            } else {
                $error = "nom d'utilisateur ou mot de passe incorrect.";
            }
        }

        // on affiche la vue login
        $render = new Render("login", "backoffice");
        $render->assign("error", $error);
        $render->render();
    }


public function register(): void
{
    $error = null;
    $success = null;

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Vérification des champs obligatoires
        if (!$username || !$email || !$password || !$confirmPassword) {
            $error = "Tous les champs sont requis.";
        }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Email invalide.";
        }
        
        elseif ($password !== $confirmPassword) {
            $error = "Les mots de passe ne correspondent pas.";
        }
        // Conditions du mot de passe
        elseif (strlen($password) < 8) {
            $error = "Le mot de passe doit faire au moins 8 caractères.";
        }
        elseif (!preg_match('#[A-Z]#', $password)) {
            $error = "Le mot de passe doit contenir au moins une majuscule.";
        }
        elseif (!preg_match('#[a-z]#', $password)) {
            $error = "Le mot de passe doit contenir au moins une minuscule.";
        }
        elseif (!preg_match('#[0-9]#', $password)) {
            $error = "Le mot de passe doit contenir au moins un chiffre.";
        }
        elseif (!preg_match('#[^a-zA-Z0-9]#', $password)) {
            $error = "Le mot de passe doit contenir au moins un caractère spécial.";
        }
        else {

            // Connexion BDD
            $pdo = Database::getInstance()->getPDO();

            // Vérification de l'existence de l'utilisateur
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$email, $username]);

            if ($stmt->fetch()) {
                $error = "Email ou nom d'utilisateur déjà utilisé.";
            } else {

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $token = bin2hex(random_bytes(32));

                // Insertion
                $stmt = $pdo->prepare(
                    "INSERT INTO users (username, email, role, password, confirmed, confirmation_token) 
                     VALUES (?, ?, 'user', ?, false, ?)"
                );
                $stmt->execute([$username, $email, $passwordHash, $token]);

                // Envoi du mail
                try {
                    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = 'mailhog';
                    $mail->Port = 1025;
                    $mail->SMTPAuth = false;

                    $mail->setFrom('test@gmail.com', 'phpprojet');
                    $mail->addAddress($email, $username);
                    $mail->Subject = 'Confirmation de votre compte';
                    $mail->Body = "Cliquez ici pour confirmer votre compte : http://localhost:8080/confirm?token=$token";
                    $mail->send();

                    $success = "Utilisateur créé avec succès. Vérifiez vos emails pour confirmer votre compte.";
                } catch (\Exception $e) {
                    $error = "Erreur lors de l'envoi du mail : " . $mail->ErrorInfo;
                }
            }
        }
    }

    // Affichage vue
    $render = new Render("register", "backoffice");
    $render->assign("error", $error);
    $render->assign("success", $success);
    $render->render();
}



    // confirmation de compte
    public function confirm(): void
{
    $token = $_GET['token'] ?? null;

    if ($token) {
        $pdo = Database::getInstance()->getPDO();

        // On récupère l'utilisateur correspondant au token
        $stmt = $pdo->prepare("SELECT id FROM users WHERE confirmation_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {
            // On confirme le compte
            $stmt = $pdo->prepare("UPDATE users SET confirmed = true, confirmation_token = NULL WHERE id = ?");
            $stmt->execute([$user['id']]);

            header("Location: /login" );
            exit;

        } else {
            header("Location: /login");
            exit;
        }
    }

    // Aucun token fourni
    header("Location: /home?error=aucun_token");
    exit;



        $render = new Render("home", "frontoffice");
        $render->assign("error", $error);
        $render->assign("success", $success);
        $render->render();
    }

    // mot de passe oublié
    public function forgot(): void
    {
        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
            $email = trim($_POST['email']);
            $pdo = Database::getInstance()->getPDO();

            // on vérifie si l'email existe
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // on génère un token pour reset
                $token = bin2hex(random_bytes(32));

                // on met à jour l'utilisateur avec le token
                $stmt = $pdo->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
                $stmt->execute([$token, $email]);

                // on envoie le mail de reset
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'mailhog';
                    $mail->Port = 1025;
                    $mail->SMTPAuth = false;

                    $mail->setFrom('test@gmail.com', 'phpprojet');
                    $mail->addAddress($email);
                    $mail->Subject = 'reinitialisation du mot de passe';
                    $mail->Body = "cliquez sur ce lien pour réinitialiser votre mot de passe : http://localhost:8080/reset-password?token=".$token;

                    $mail->send();
                } catch (Exception $e) {
                    $error = "erreur  " . $mail->ErrorInfo;
                }
            }

        }
        $render = new Render("forgot_password", "backoffice");
        $render->assign("error", $error);
        $render->assign("success", $success);
        $render->render();
    }

    public function reset(): void
{
    $token = $_GET['token'] ?? null;
    $error = null;
    $success = null;
    $pdo = Database::getInstance()->getPDO();

    // Si le formulaire est envoyé
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $password = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');
        $tokenPost = $_POST['token'] ?? null;

        // Vérification du token
        if (!$tokenPost) {
            $error = "Token manquant.";
        }
        // Vérification des champs
        elseif (!$password || !$confirmPassword) {
            $error = "Veuillez remplir les deux champs.";
        }
        // Vérification correspondance mots de passe
        elseif ($password !== $confirmPassword) {
            $error = "Les mots de passe ne correspondent pas.";
        }
        // Conditions du mot de passe
        elseif (strlen($password) < 8) {
            $error = "Le mot de passe doit faire au moins 8 caractères.";
        }
        elseif (!preg_match('#[A-Z]#', $password)) {
            $error = "Le mot de passe doit contenir au moins une majuscule.";
        }
        elseif (!preg_match('#[a-z]#', $password)) {
            $error = "Le mot de passe doit contenir au moins une minuscule.";
        }
        elseif (!preg_match('#[0-9]#', $password)) {
            $error = "Le mot de passe doit contenir au moins un chiffre.";
        }
        elseif (!preg_match('#[^a-zA-Z0-9]#', $password)) {
            $error = "Le mot de passe doit contenir un caractère spécial.";
        }
        else {

            // Vérification du token existant
            $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ?");
            $stmt->execute([$tokenPost]);
            $user = $stmt->fetch();

            if ($user) {

                // Mise à jour du mot de passe
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare(
                    "UPDATE users SET password = ?, reset_token = NULL WHERE id = ?"
                );
                $stmt->execute([$hash, $user['id']]);

                $success = "Mot de passe réinitialisé avec succès.";
            } else {
                $error = "Token invalide ou expiré.";
            }
        }
    }

    // Affichage
    $render = new Render("reset_password", "backoffice");
    $render->assign("token", $token);
    $render->assign("error", $error);
    $render->assign("success", $success);
    $render->render();
}


    // déconnexion
    public function logout(): void
    {
        session_destroy();
        header("Location: /login");
        exit;
    }
}