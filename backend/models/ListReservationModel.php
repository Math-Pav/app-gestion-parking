<?php
require_once __DIR__ . '/../includes/Database.php';

class ListReservationModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getUserReservations($userId) {
        $query = "SELECT r.id,
              r.user_id,
              r.start_date,
              r.end_date,
              r.price,
              r.status,
              p.number_place,
              p.type_place,
              DATE_FORMAT(r.start_date, '%d/%m/%Y') as formatted_date,
              DATE_FORMAT(r.end_date, '%d/%m/%Y') as formatted_end_date,
              DATE_FORMAT(r.start_date, '%H:%i') as start_time,
              DATE_FORMAT(r.end_date, '%H:%i') as end_time
              FROM reservations r
              JOIN parking p ON r.parking_id = p.id
              ORDER BY r.start_date DESC";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return [
                'success' => true,
                'reservations' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de la récupération des réservations'
            ];
        }
    }

    public function cancelReservation($reservationId, $userId) {
        $query = "UPDATE reservations 
                  SET status = 'annuler' 
                  WHERE id = ? 
                  AND user_id = ? 
                  AND status IN ('reserver', 'en_cours', 'attente')";

        try {
            $stmt = $this->conn->prepare($query);
            $success = $stmt->execute([$reservationId, $userId]);

            return [
                'success' => $success,
                'message' => $success ? 'Réservation annulée' : 'Échec de l\'annulation'
            ];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'annulation'
            ];
        }
    }
}