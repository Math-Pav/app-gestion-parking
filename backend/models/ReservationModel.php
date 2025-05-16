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
    public function getAvailableSpots($type, $startDate, $endDate)
    {
        $typeMapping = [
            'voiture' => 'voiture',
            'moto' => 'moto',
            'electrique' => 'voiture_electrique'
        ];

        $placeType = $typeMapping[$type] ?? $type;

        $query = "SELECT p.id, p.number_place as spot_number, p.type_place
              FROM parking p
              WHERE p.type_place = ?
              AND p.id NOT IN (
                  SELECT r.parking_id
                  FROM reservations r
                  WHERE r.status IN ('reserver', 'en_cours')
                  AND ((r.start_date <= ? AND r.end_date >= ?)
                  OR (r.start_date <= ? AND r.end_date >= ?))
              )
              ORDER BY p.number_place";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$placeType, $endDate, $startDate, $startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkSpotAvailability($parkingId, $type, $startDate, $endDate) {
        $query = "SELECT COUNT(*) FROM reservations r
              JOIN parking p ON p.id = r.parking_id
              WHERE r.parking_id = ?
              AND p.type_place = ?
              AND r.status IN ('reserver', 'en_cours')
              AND ((r.start_date BETWEEN ? AND ?)
              OR (r.end_date BETWEEN ? AND ?))";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            $parkingId,
            $type,
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

    public function getReservationById($reservationId, $userId) {
        $query = "SELECT r.*,
              p.number_place,
              p.type_place,
              TIMESTAMPDIFF(HOUR, r.start_date, r.end_date) as duration
              FROM reservations r
              JOIN parking p ON r.parking_id = p.id
              WHERE r.id = ? AND r.user_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$reservationId, $userId]);
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($reservation) {
            return [
                'success' => true,
                'reservation' => [
                    'id' => $reservation['id'],
                    'place_number' => $reservation['number_place'],
                    'type' => $reservation['type_place'],
                    'price' => $reservation['price'],
                    'start_date' => $reservation['start_date'],
                    'end_date' => $reservation['end_date'],
                    'duration' => $reservation['duration'],
                    'status' => $reservation['status']
                ]
            ];
        }

        return [
            'success' => false,
            'message' => 'Réservation non trouvée'
        ];
    }
    public function getActiveReservation($userId) {
        $query = "SELECT id FROM reservations 
              WHERE user_id = ? AND status = 'reserver' 
              ORDER BY start_date DESC LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($reservation) {
            return $this->getReservationById($reservation['id'], $userId);
        }

        return [
            'success' => false,
            'message' => 'Aucune réservation active trouvée'
        ];
    }
}