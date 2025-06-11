<?php
require_once __DIR__ . '/../models/ListReservationModel.php';

class ListReservationController {
    private $model;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->model = new ListReservationModel();
    }

    private function checkAuth() {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            return false;
        }
        return true;
    }

    public function getUserReservations() {
        header('Content-Type: application/json');

        if (!$this->checkAuth()) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Non autorisé'
            ]);
            return;
        }

        try {
            $userId = $_SESSION['user']['id'];
            $result = $this->model->getUserReservations($userId);
            echo json_encode($result);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur'
            ]);
        }
    }

    public function cancelReservation() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Méthode non autorisée'
            ]);
            return;
        }

        if (!$this->checkAuth()) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Non autorisé'
            ]);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['reservationId'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ID de réservation manquant'
            ]);
            return;
        }

        try {
            $userId = $_SESSION['user']['id'];
            $reservationId = intval($data['reservationId']);

            $result = $this->model->cancelReservation($reservationId, $userId);
            echo json_encode($result);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur'
            ]);
        }
    }
}