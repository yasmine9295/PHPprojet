<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $host = 'db';
        $dbname = 'devdb';
        $username = 'devuser';
        $password = 'devpass';
//connection bdd
        try {
            $this->pdo = new PDO(
                "pgsql:host=$host;dbname=$dbname",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Echec de la connexion à la base de donnée: " . $e->getMessage());
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getPDO(): PDO
    {
        return $this->pdo;
    }

    // empeche le clonage de l'instance
    private function __clone() {}

    // empeche la désérialisation
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}