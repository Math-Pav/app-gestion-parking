<?php
require_once __DIR__ . '/Config.php';

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        $this->host = Config::get('DB_HOST') ?? 'localhost';
        $this->db_name = Config::get('DB_NAME');
        $this->username = Config::get('DB_USER');
        $this->password = Config::get('DB_PASS');

        if (!$this->db_name || !$this->username) {
            throw new Exception("Configuration de base de données manquante");
        }
    }

    public function connect() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            return $this->conn;
        } catch(PDOException $e) {
            error_log("Erreur de connexion : " . $e->getMessage());
            return null;
        }
    }
}