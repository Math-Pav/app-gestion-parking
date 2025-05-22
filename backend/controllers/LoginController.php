<?php

require_once __DIR__ . '/../models/LoginModel.php';

class LoginController {
    private $userModel;

    public function __construct() {
        $this->userModel = new LoginModel();
    }

    public function handleLogin() {
        try {
            // Définir les en-têtes CORS et JSON
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: POST, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type');

            // Gestion des requêtes OPTIONS (CORS)
            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                http_response_code(200);
                exit();
            }

            // Vérification de la méthode HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée', 405);
            }

            // Lecture et validation des données
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Format JSON invalide', 400);
            }

            // Validation des champs requis
            if (empty($data['email']) || empty($data['password'])) {
                throw new Exception('Email et mot de passe requis', 400);
            }

            // Validation de l'email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Format d\'email invalide', 400);
            }

            // Validation du mot de passe
            if (strlen($data['password']) < 6) {
                throw new Exception('Le mot de passe doit contenir au moins 6 caractères', 400);
            }

            // Tentative de connexion
            $result = $this->userModel->login($data['email'], $data['password']);

            if ($result['success']) {
                // Créer la session uniquement en cas de succès
                session_regenerate_id(true);
                $_SESSION['user'] = $result['user'];
                $_SESSION['last_activity'] = time();
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