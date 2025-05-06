<?php

require_once __DIR__ . '/../models/LoginModel.php';

class LoginController {
    private $userModel;

    public function __construct() {
        $this->userModel = new LoginModel();
    }

    public function handleLogin() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Méthode non autorisée'
            ]);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Email et mot de passe requis'
            ]);
            return;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'success' => false,
                'message' => 'Email invalide'
            ]);
            return;
        }

        if (strlen($data['password']) < 6) {
            echo json_encode([
                'success' => false,
                'message' => 'Le mot de passe doit contenir au moins 6 caractères'
            ]);
            return;
        }

        $result = $this->userModel->login($data['email'], $data['password']);

        if ($result['success']) {
            $_SESSION['user'] = $result['user'];
        }

        echo json_encode($result);
    }
}