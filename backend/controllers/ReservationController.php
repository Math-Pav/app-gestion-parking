<?php

require_once __DIR__ . '/NotificationController.php';
require_once __DIR__ . '/../models/ReservationModel.php';

class ReservationController
{
    private $reservationModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->reservationModel = new ReservationModel();
    }

    public function createReservation()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            return;
        }

        $this->reservationModel->updateExpiredReservations();

        $data = json_decode(file_get_contents('php://input'), true);
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            http_response_code(401);
            echo json_encode(['error' => 'Utilisateur non connecté']);
            return;
        }

        if (!$this->validateReservationData($data)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Données invalides: vérifiez le type de véhicule, les dates et le prix'
            ]);
            return;
        }

        if (!$this->reservationModel->checkSpotAvailability(
            $data['parking_id'],
            $data['start_date'],
            $data['end_date']
        )) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Place déjà réservée']);
            return;
        }

        $initialStatus = 'attente';

        $success = $this->reservationModel->createReservation(
            $userId,
            $data['parking_id'],
            $data['price'],
            $data['start_date'],
            $data['end_date'],
            $initialStatus
        );

        if ($success) {
            $notificationController = new NotificationController();
            $message = "Nouvelle réservation pour la place n°{$data['parking_id']} du {$data['start_date']} au {$data['end_date']}";
            $notificationController->createNotification($userId, $message);

            $reservationId = $this->reservationModel->getLastInsertId();
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Réservation en attente de paiement',
                'status' => $initialStatus,
                'reservation_id' => $reservationId
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la création de la réservation'
            ]);
        }
    }

    public function cancelReservation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            http_response_code(401);
            echo json_encode(['error' => 'Utilisateur non connecté']);
            return;
        }

        if (!isset($data['reservation_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de réservation manquant']);
            return;
        }

        $success = $this->reservationModel->cancelReservation(
            intval($data['reservation_id']),
            $userId
        );

        if ($success) {
            $notificationController = new NotificationController();
            $message = "Votre réservation n°{$data['reservation_id']} a été annulée avec succès.";
            $notificationController->createNotification($userId, $message);

            echo json_encode([
                'success' => true,
                'message' => 'Réservation annulée avec succès'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation'
            ]);
        }
    }

    public function getAvailableSpots()
    {
        $type = $_GET['type'] ?? null;
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;

        if (!$type || !$start || !$end) {
            echo json_encode([
                'success' => false,
                'message' => 'Paramètres manquants'
            ]);
            return;
        }

        $spots = $this->reservationModel->getAvailableSpots($type, $start, $end);

        echo json_encode([
            'success' => true,
            'spots' => $spots
        ]);
    }

    public function getReservationById() {
        if (!isset($_GET['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID de réservation manquant']);
            return;
        }

        $reservationId = $_GET['id'];
        $userId = $_SESSION['user']['id'];

        $result = $this->reservationModel->getReservationById($reservationId, $userId);

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function updateReservationStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId || !isset($data['reservation_id']) || !isset($data['status'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes']);
            return;
        }

        if (!in_array($data['status'], ['en_cours', 'terminer', 'reserver'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Statut invalide']);
            return;
        }

        $success = $this->reservationModel->updateReservationStatus(
            $data['reservation_id'],
            $data['status']
        );

        if ($success) {
            echo json_encode(['message' => 'Statut mis à jour avec succès']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors de la mise à jour du statut']);
        }
    }

    private function validateReservationData($data)
    {
        $validTypes = ['voiture', 'moto', 'electrique','handicap'];

        if (!isset($data['parking_id']) ||
            !isset($data['vehicle_type']) ||
            !isset($data['price']) ||
            !isset($data['start_date']) ||
            !isset($data['end_date'])) {
            return false;
        }

        if (!in_array($data['vehicle_type'], $validTypes)) {
            return false;
        }

        if (!strtotime($data['start_date']) ||
            !strtotime($data['end_date']) ||
            strtotime($data['start_date']) >= strtotime($data['end_date'])) {
            return false;
        }

        if (!is_numeric($data['price']) || $data['price'] < 0) {
            return false;
        }

        return true;
    }


    public function getLatestReservation() {
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Utilisateur non connecté'
            ]);
            return;
        }

        $result = $this->reservationModel->getActiveReservation($userId);

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function getUserReservations() {
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Utilisateur non connecté'
            ]);
            return;
        }

        $reservations = $this->reservationModel->getUserReservations($userId);

        echo json_encode([
            'success' => true,
            'reservations' => $reservations
        ]);
    }
}