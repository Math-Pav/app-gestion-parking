<?php
require_once __DIR__ . '/../models/LoginModel.php';

class LoginController {
    private $userModel;

    public function __construct() {
        $this->userModel = new LoginModel();
    }

    public function handleLogin() {
        try {
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: POST, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type');

            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                http_response_code(200);
                exit();
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée', 405);
            }

            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Format JSON invalide', 400);
            }

            if (empty($data['email']) || empty($data['password'])) {
                throw new Exception('Email et mot de passe requis', 400);
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Format d\'email invalide', 400);
            }

            if (strlen($data['password']) < 6) {
                throw new Exception('Le mot de passe doit contenir au moins 6 caractères', 400);
            }

            $result = $this->userModel->login($data['email'], $data['password']);

            if ($result['success']) {
                session_regenerate_id(true);
                $_SESSION['user'] = $result['user'];
                $_SESSION['last_activity'] = time();

                $redirectUrl = $result['user']['role'] === 'admin'
                    ? '/app-gestion-parking/dashboard-admin'
                    : '/app-gestion-parking/dashboard';

                $result['redirect'] = $redirectUrl;
            }

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