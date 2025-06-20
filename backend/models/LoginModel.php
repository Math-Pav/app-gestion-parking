<?php
class LoginModel {
    private $db;
    private $conn;

    public function __construct() {
        try {
            $this->db = new Database();
            $this->conn = $this->db->connect();

            if ($this->conn === null) {
                throw new Exception("Impossible de se connecter à la base de données");
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function login($email, $password) {
        try {
            $query = "SELECT id, email, password, role FROM user WHERE email = :email AND status = 'active' LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['email' => $email]);

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($password, $user['password'])) {
                    unset($user['password']);
                    return [
                        'success' => true,
                        'user' => $user
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Email ou mot de passe incorrect'
            ];

        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la connexion: ' . $e->getMessage()
            ];
        }
    }
}