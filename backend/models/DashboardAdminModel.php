<?php
require_once __DIR__ . '/../includes/Database.php';

class DashboardAdminModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getGeneralStats() {
        try {
            $stats = [
                'totalReservations' => $this->getTotalReservations(),
                'availableSpots' => $this->getAvailableSpots(),
                'activeUsers' => $this->getActiveUsers(),
                'pendingReservations' => $this->getPendingReservations()
            ];
            return $stats;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function getTotalReservations() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM reservations");
        return $stmt->fetchColumn();
    }

    private function getAvailableSpots() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM parking WHERE status = 'disponible'");
        return $stmt->fetchColumn();
    }

    private function getActiveUsers() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM users WHERE status = 'actif'");
        return $stmt->fetchColumn();
    }

    private function getPendingReservations() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM reservations WHERE status = 'attente'");
        return $stmt->fetchColumn();
    }

    public function getChartData() {
        try {
            return [
                'types' => $this->getParkingTypeStats()
            ];
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function getParkingTypeStats() {
        $query = "SELECT p.type_place, COUNT(*) as count 
                 FROM parking p 
                 GROUP BY p.type_place";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}