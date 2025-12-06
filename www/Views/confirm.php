<?php
// je me connecte à PostgreSQL avec PDO
$conn = new PDO('pgsql:host=pg-db;dbname=devdb', 'devuser', 'devpass');

// je vérifie si le token est présent dans l'URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // je prépare la requête avec un placeholder nommé
    $stmt = $conn->prepare("UPDATE users SET confirmed = TRUE, confirmation_token = NULL WHERE confirmation_token = :token");

    // je lie la valeur du token
    $stmt->bindValue(':token', $token);

    // j'exécute la requête
    $stmt->execute();
    
}
?>