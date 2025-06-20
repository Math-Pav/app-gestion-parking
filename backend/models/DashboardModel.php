<?php
require_once __DIR__ . '/../includes/Database.php';
class DashboardModel
{
    private $conn;

    public function __construct()
    {
        try {
            $database = new Database();
            $this->conn = $database->connect();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getUserReservationsCount($userId)
    {
        $query = "SELECT COUNT(*) as total
                  FROM reservations 
                  WHERE user_id = :user_id
                  AND status != 'annuler'";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'success' => true,
                'total' => $row['total']
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la récupération du nombre de réservations'
            ];
        }
    }

    public function getAvailableSpotsCount()
    {
        $query = "SELECT
          (SELECT COUNT(*) FROM parking) -
          (SELECT COUNT(*) FROM reservations WHERE status = 'actif' OR status = 'attente')
          as available";

        try {
            $stmt = $this->conn->query($query);
            if (!$stmt) {
                error_log("Erreur PDO : " . print_r($this->conn->errorInfo(), true));
                throw new PDOException("Erreur d'exécution de la requête");
            }

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result === false) {
                throw new PDOException("Aucun résultat trouvé");
            }

            return [
                'success' => true,
                'total' => max(0, (int)$result['available'])
            ];
        } catch (PDOException $e) {
            error_log("Erreur SQL complète : " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de la récupération des places disponibles'
            ];
        }
    }

    public function getDashboardStats($userId)
    {
        return [
            'reservations' => $this->getUserReservationsCount($userId),
            'availableSpots' => $this->getAvailableSpotsCount()
        ];
    }
}