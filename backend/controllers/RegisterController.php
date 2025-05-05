<?php
require_once __DIR__ . '/../models/RegisterModel.php';

class RegisterController {
    private $registerModel;

    public function __construct() {
        $this->registerModel = new RegisterModel();
    }

    public function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['name']) || !isset($data['email']) ||
            !isset($data['phone']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes']);
            return;
        }

        $result = $this->registerModel->createUser(
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['password']
        );

        echo json_encode($result);
    }
}