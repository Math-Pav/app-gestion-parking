<?php
require_once __DIR__ . '/../includes/Database.php';

class ReservationModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function createReservation($userId, $parkingId, $price, $startDate, $endDate) {
        $query = "INSERT INTO reservations 
                  (user_id, parking_id, price, start_date, end_date, status)
                  VALUES (?, ?, ?, ?, ?, 'reserver')";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $userId,
            $parkingId,
            $price,
            $startDate,
            $endDate
        ]);
    }

    public function getUserReservations($userId) {
        $query = "SELECT * FROM reservations WHERE user_id = ? ORDER BY start_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableSpots($type, $startDate, $endDate)
    {
        $query = "SELECT p.id, p.number_place as spot_number 
              FROM parking p
              WHERE p.id NOT IN (
                  SELECT r.parking_id
                  FROM reservations r
                  WHERE r.status IN ('reserver', 'en_cours')
                  AND ((r.start_date <= ? AND r.end_date >= ?)
                  OR (r.start_date <= ? AND r.end_date >= ?))
              )";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$endDate, $startDate, $startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkSpotAvailability($parkingId, $startDate, $endDate) {
        $query = "SELECT COUNT(*) FROM reservations 
                  WHERE parking_id = ? 
                  AND status IN ('reserver', 'en_cours')
                  AND ((start_date BETWEEN ? AND ?) 
                  OR (end_date BETWEEN ? AND ?))";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            $parkingId,
            $startDate,
            $endDate,
            $startDate,
            $endDate
        ]);

        return $stmt->fetchColumn() == 0;
    }

    public function updateReservationStatus($reservationId, $status) {
        $query = "UPDATE reservations SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$status, $reservationId]);
    }

    public function getReservationById($reservationId) {
        $query = "SELECT * FROM reservations WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$reservationId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}