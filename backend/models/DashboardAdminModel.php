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

    public function getNumberOfUsers() {
        try {
            $query = "SELECT COUNT(id) as total FROM user WHERE status = 'active'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'success' => true,
                'total' => $result['total']
            ];
        } catch (PDOException $e) {
            error_log("Erreur SQL: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors du comptage des utilisateurs'
            ];
        }
    }

    public function getNumberOfReservations() {
        try {
            $query = "SELECT COUNT(*) as total FROM reservations";
            $stmt = $this->conn->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'success' => true,
                'total' => $result['total']
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors du comptage des réservations'
            ];
        }
    }
}