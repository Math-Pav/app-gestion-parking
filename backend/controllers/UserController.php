<?php
require_once dirname(__DIR__) . '/models/UserModel.php';

class UserController {
    private $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->userModel = new UserModel();
    }

    public function getProfile() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $userId = $_SESSION['user']['id'];
        $result = $this->userModel->getUserProfile($userId);

        if ($result['success']) {
            $_SESSION['user']['name'] = $result['user']['name'] ?? '';
        }

        http_response_code($result['success'] ? 200 : 404);
        echo json_encode($result);
    }

}