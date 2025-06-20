<?php

require_once __DIR__ . '/../models/DashboardModel.php';

class DashboardController
{
    private $model;

    public function __construct()
    {
        $this->model = new DashboardModel();
    }

    public function getDashboardStats()
    {
        if (!isset($_SESSION['user'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            return;
        }

        header('Content-Type: application/json');
        try {
            $result = $this->model->getDashboardStats($_SESSION['user']['id']);
            echo json_encode([
                'success' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur'
            ]);
        }
    }
}