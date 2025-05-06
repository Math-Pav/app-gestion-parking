<?php
require_once dirname(__DIR__) . '/models/UserModel.php';

class UserController {
    private $userModel;

    public function __construct() {
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

        http_response_code($result['success'] ? 200 : 404);
        echo json_encode($result);
    }

    public function updateProfile() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $userId = $_SESSION['user']['id'];
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$this->validateProfileData($data)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
            return;
        }

        $result = $this->userModel->updateUserProfile($userId, $data);

        http_response_code($result['success'] ? 200 : 400);
        echo json_encode($result);
    }

    private function validateProfileData($data) {
        if (!isset($data['name']) || !isset($data['email'])) {
            return false;
        }

        if (strlen($data['email']) > 255 || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (!empty($data['phone'])) {
            if (!preg_match('/^(?:(?:\+|00)33[\s.-]{0,3}(?:\(0\)[\s.-]{0,3})?|0)[1-9](?:(?:[\s.-]?\d{2}){4})$/', $data['phone'])) {
                return false;
            }
        }

        return true;
    }
}