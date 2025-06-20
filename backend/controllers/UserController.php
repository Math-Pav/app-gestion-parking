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
        $profileResult = $this->userModel->getUserProfile($userId);
        $statsResult = $this->userModel->getUserStats($userId);

        if ($profileResult['success']) {
            $_SESSION['user']['name'] = $profileResult['user']['name'] ?? '';
            $profileResult['user']['reservations_actives'] = $statsResult['reservations_actives'];
            $profileResult['user']['total_reservations'] = $statsResult['total_reservations'];
        }

        http_response_code($profileResult['success'] ? 200 : 404);
        echo json_encode($profileResult);
    }

}