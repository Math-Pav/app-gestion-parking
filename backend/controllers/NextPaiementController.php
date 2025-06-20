<?php
require_once __DIR__ . '/../models/NextPaiementModel.php';

class NextPaiementController {
    private $model;

    public function __construct() {
        $this->model = new NextPaiementModel();
    }

    public function getReservationById() {
        try {
            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                throw new Exception('Méthode non autorisée', 405);
            }

            if (!isset($_GET['id'])) {
                throw new Exception('ID de réservation manquant', 400);
            }

            $reservationId = $_GET['id'];
            $result = $this->model->getReservationById($reservationId);

            if ($result['success']) {
                echo json_encode($result);
            } else {
                throw new Exception($result['message'], 404);
            }

        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function handlePayment() {
        try {
            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée', 405);
            }

            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['reservation_id'])) {
                throw new Exception('ID de réservation manquant', 400);
            }

            $result = $this->model->updateReservationStatus(
                $data['reservation_id'],
                'reserver'
            );

            echo json_encode($result);

        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}