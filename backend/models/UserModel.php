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

    public function getUserStats($userId) {
        try {
            $query = "SELECT 
            COUNT(*) as total_reservations,
            SUM(CASE WHEN status IN ('en_cours', 'reserver') THEN 1 ELSE 0 END) as reservations_actives
            FROM reservations 
            WHERE user_id = :user_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [
                'total_reservations' => 0,
                'reservations_actives' => 0
            ];
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
}