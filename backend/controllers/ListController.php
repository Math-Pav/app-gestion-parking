<?php
require_once dirname(__DIR__) . '/models/ListModel.php';

class ListController {
    private $model;

    public function __construct() {
        $this->model = new ListModel();
    }

    public function getUsers() {
        try {
            $users = $this->model->getAllUsers();
            echo json_encode([
                'success' => true,
                'users' => $users
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function addUser() {
        try {
            $userData = json_decode(file_get_contents('php://input'), true);

            if (!$userData) {
                throw new Exception('Données utilisateur invalides');
            }

            $result = $this->model->addUser($userData);
            echo json_encode([
                'success' => true,
                'message' => 'Utilisateur ajouté avec succès'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateUser() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['id'])) {
                throw new Exception('ID utilisateur manquant');
            }

            $result = $this->model->updateUser($data['id'], $data);
            echo json_encode([
                'success' => true,
                'message' => 'Utilisateur mis à jour avec succès'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteUser() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['id'])) {
                throw new Exception('ID utilisateur manquant');
            }

            $result = $this->model->deleteUser($data['id']);
            echo json_encode([
                'success' => true,
                'message' => 'Utilisateur supprimé avec succès'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getUserById() {
        try {
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

            if (!$id) {
                throw new Exception('ID utilisateur invalide');
            }

            $user = $this->model->getUserById($id);
            echo json_encode([
                'success' => true,
                'user' => $user
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}