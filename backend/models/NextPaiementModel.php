<?php
require_once __DIR__ . '/../includes/Database.php';

class NextPaiementModel {
    private $db;

    private $conn;

    public function __construct() {
        try {
            $this->db = new Database();
            $this->conn = $this->db->connect();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateReservationStatus($reservationId, $status) {
        try {
            $query = "UPDATE reservations SET status = :status WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':status' => $status,
                ':id' => $reservationId
            ]);

            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Statut de réservation mis à jour avec succès'
                ];
            } else {
                throw new Exception('Aucune réservation trouvée avec cet ID');
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la mise à jour : ' . $e->getMessage()
            ];
        }
    }
    public function getReservationById($reservationId) {
        try {
            $query = "SELECT * FROM reservations WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $reservationId]);

            $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($reservation) {
                return [
                    'success' => true,
                    'reservation' => $reservation
                ];
            } else {
                throw new Exception('Réservation non trouvée');
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la récupération : ' . $e->getMessage()
            ];
        }
    }
}