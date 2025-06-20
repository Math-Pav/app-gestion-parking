<?php

require_once __DIR__ . '/../models/NotificationModel.php';

class NotificationController {
    private $model;

    public function __construct() {
        $this->model = new NotificationModel();
    }

    public function createNotification($userId, $message) {
        return $this->model->createNotification($userId, $message);
    }

    public function getNotifications() {
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
            return;
        }

        try {
            $userId = $_SESSION['user']['id'];
            $notifications = $this->model->getNotificationsByUserId($userId);
            $unreadCount = $this->model->getUnreadCount($userId);

            echo json_encode([
                'success' => true,
                'notifications' => $notifications,
                'unreadCount' => $unreadCount
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
        }
    }

    public function markAllAsRead() {
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
            return;
        }

        try {
            $success = $this->model->markAllAsRead($_SESSION['user']['id']);
            echo json_encode(['success' => $success]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
        }
    }

    public function markAsRead() {
        if (!isset($_SESSION['user']) || !isset($_POST['notificationId'])) {
            echo json_encode(['success' => false, 'message' => 'Paramètres invalides']);
            return;
        }

        try {
            $success = $this->model->markAsRead($_POST['notificationId'], $_SESSION['user']['id']);
            echo json_encode(['success' => $success]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
        }
    }

    public function cleanOldNotifications() {
        try {
            return $this->model->deleteOldNotifications();
        } catch (Exception $e) {
            return false;
        }
    }
}