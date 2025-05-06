<?php
require_once dirname(__DIR__) . '/includes/Database.php';

class UserModel {
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

    public function getUserProfile($userId) {
        try {
            $query = "SELECT name, email, phone, role, registration_date, status FROM user WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return [
                    'success' => true,
                    'user' => [
                        'name' => $result['name'],
                        'email' => $result['email'],
                        'phone' => $result['phone'] ?? 'Non renseigné',
                        'role' => $result['role'],
                        'registration_date' => $result['registration_date'],
                        'status' => $result['status']
                    ]
                ];
            }

            return ['success' => false, 'message' => 'Utilisateur non trouvé'];

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la récupération du profil'];
        }
    }

    public function updateUserProfile($userId, $data) {
        try {
            $query = "UPDATE user 
                 SET name = :name, 
                     email = :email, 
                     phone = :phone 
                 WHERE id = :id";

            $stmt = $this->conn->prepare($query);

            $result = $stmt->execute([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?: null,
                'id' => $userId
            ]);

            if ($result) {
                $updatedUser = $this->getUserProfile($userId);

                return [
                    'success' => true,
                    'message' => 'Profil mis à jour avec succès',
                    'user' => $updatedUser['user']
                ];
            }

            return [
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du profil'
            ];

        } catch (PDOException $e) {
            error_log($e->getMessage());

            if ($e->getCode() == '23000') {
                return [
                    'success' => false,
                    'message' => 'Cet email est déjà utilisé'
                ];
            }

            return [
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du profil'
            ];
        }
    }
}