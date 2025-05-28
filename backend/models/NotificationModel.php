<?php
require_once __DIR__ . '/../includes/Database.php';

class NotificationModel {
    private $db;
    private $conn;

    public function __construct() {
        try {
            $this->db = new Database();
            $this->conn = $this->db->connect();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function createNotification($userId, $message) {
        try {
            $sql = "INSERT INTO notifications (user_id, message, send_date, status) VALUES (:userId, :message, NOW(), 'en_cours')";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                'userId' => $userId,
                'message' => $message
            ]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getNotificationsByUserId($userId) {
        try {
            $sql = "
            SELECT
                id,
                message,
                DATE_FORMAT(send_date, '%d/%m/%Y %H:%i') as formatted_date,
                send_date as created_at,
                status,
                CASE WHEN status = 'lu' THEN 1 ELSE 0 END as `read`
            FROM notifications
            WHERE user_id = :userId AND status != 'supprimer'
            ORDER BY send_date DESC
        ";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getUnreadCount($userId) {
        try {
            $sql = "SELECT COUNT(*) FROM notifications WHERE user_id = :userId AND status = 'en_cours'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function markAllAsRead($userId) {
        try {
            $sql = "UPDATE notifications SET status = 'supprimer' WHERE user_id = :userId AND status = 'en_cours'";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute(['userId' => $userId]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function markAsRead($notificationId, $userId) {
        try {
            $sql = "UPDATE notifications SET status = 'lu' WHERE id = :notificationId AND user_id = :userId";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                'notificationId' => $notificationId,
                'userId' => $userId
            ]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function deleteOldNotifications($days = 30) {
        try {
            $sql = "DELETE FROM notifications WHERE send_date < DATE_SUB(NOW(), INTERVAL :days DAY)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute(['days' => $days]);
        } catch (PDOException $e) {
            throw $e;
        }
    }
}