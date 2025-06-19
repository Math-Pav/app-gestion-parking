<?php
require_once __DIR__ . '/../models/DashboardAdminModel.php';

class DashboardAdminController {
    private $model;

    public function __construct() {
        $this->model = new DashboardAdminModel();
    }

    public function getChartData() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            return;
        }

        try {
            $data = $this->model->getChartData();
            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
        }
    }
}