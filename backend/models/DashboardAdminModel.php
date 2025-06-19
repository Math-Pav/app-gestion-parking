<?php
require_once __DIR__ . '/../includes/Database.php';
class DashboardAdminModel {
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

    public function getGeneralStats() {
        try {
            error_log("Début getGeneralStats");
            $stats = [
                'totalReservations' => $this->getTotalReservations(),
                'availableSpots' => $this->getAvailableSpots(),
                'activeUsers' => $this->getActiveUsers()
            ];
            error_log("Stats récupérées : " . json_encode($stats));
            return $stats;
        } catch (PDOException $e) {
            error_log("Erreur PDO : " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération des statistiques");
        }
    }

    private function getTotalReservations() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM reservations");
            $result = $stmt->fetchColumn();
            error_log("Total réservations: " . $result);
            return intval($result);
        } catch (PDOException $e) {
            error_log("Erreur getTotalReservations: " . $e->getMessage());
            throw $e;
        }
    }

    private function getAvailableSpots() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM parking WHERE status = 'disponible'");
            $result = $stmt->fetchColumn();
            error_log("Places disponibles: " . $result);
            return intval($result);
        } catch (PDOException $e) {
            error_log("Erreur getAvailableSpots: " . $e->getMessage());
            throw $e;
        }
    }

    private function getActiveUsers() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM users WHERE status = 'actif'");
            $result = $stmt->fetchColumn();
            error_log("Utilisateurs actifs: " . $result);
            return intval($result);
        } catch (PDOException $e) {
            error_log("Erreur getActiveUsers: " . $e->getMessage());
            throw $e;
        }
    }

    public function getChartData() {
        try {
            $data = $this->getParkingTypeStats();
            return ['types' => $data];
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des données du graphique: " . $e->getMessage());
        }
    }

    private function getParkingTypeStats() {
        $query = "SELECT 
            type_place,
            COUNT(*) as count
            FROM parking 
            GROUP BY type_place 
            ORDER BY type_place";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}