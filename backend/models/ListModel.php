<?php
require_once dirname(__DIR__) . '/includes/Database.php';

class ListModel {
    private $conn;

    public function __construct() {
        try {
            $database = new Database();
            $this->conn = $database->connect();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getAllUsers() {
        try {
            $sql = "SELECT id, name, email, phone, role, status, registration_date 
               FROM user";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception('Erreur lors de la récupération des utilisateurs');
        }
    }

    public function addUser($userData) {
        try {
            $sql = "INSERT INTO user (name, email, phone, password, role, status, registration_date)
                    VALUES (:name, :email, :phone, :password, :role, 'active', CURRENT_TIMESTAMP)";

            $stmt = $this->conn->prepare($sql);
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);

            return $stmt->execute([
                'name' => substr($userData['name'], 0, 15),
                'email' => $userData['email'],
                'phone' => substr($userData['phone'], 0, 10),
                'password' => $hashedPassword,
                'role' => $userData['role'] ?? 'user'
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception('Erreur lors de l\'ajout de l\'utilisateur');
        }
    }

    public function updateUser($userId, $userData) {
        try {
            $sql = "UPDATE user SET
                    name = :name,
                    email = :email,
                    phone = :phone,
                    role = :role,
                    status = :status
                    WHERE id = :id";

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                'id' => $userId,
                'name' => substr($userData['name'], 0, 15),
                'email' => $userData['email'],
                'phone' => substr($userData['phone'], 0, 10),
                'role' => $userData['role'],
                'status' => $userData['status']
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception('Erreur lors de la mise à jour de l\'utilisateur');
        }
    }

    public function deleteUser($userId) {
        try {
            $sql = "UPDATE user SET status = 'inactif' WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute(['id' => $userId]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception('Erreur lors de la suppression de l\'utilisateur');
        }
    }

    public function getUserById($userId) {
        try {
            $sql = "SELECT id, name, email, phone, role, status, registration_date
                FROM user
                WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['id' => $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception('Erreur lors de la récupération de l\'utilisateur');
        }
    }
}