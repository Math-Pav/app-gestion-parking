<?php
class RegisterModel {
    private $db;
    private $conn;

    public function __construct() {
        try {
            $this->db = new Database();
            $this->conn = $this->db->connect();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function createUser($name, $email, $phone, $password) {
        try {
            if ($this->emailExists($email)) {
                return [
                    'success' => false,
                    'message' => 'Cet email est déjà utilisé'
                ];
            }

            $query = "INSERT INTO user (name, email, phone, password, role, status, registration_date) 
                     VALUES (:name, :email, :phone, :password, :role, :status, :registration_date)";

            $stmt = $this->conn->prepare($query);
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $currentDate = date('Y-m-d H:i:s');

            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'password' => $hashedPassword,
                'role' => 'user',
                'status' => 'active',
                'registration_date' => $currentDate
            ]);

            return [
                'success' => true,
                'user_id' => $this->conn->lastInsertId()
            ];

        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'inscription: ' . $e->getMessage()
            ];
        }
    }

    private function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT id FROM user WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->rowCount() > 0;
    }
}