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
}