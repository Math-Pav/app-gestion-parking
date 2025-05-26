// frontend/controllers/NotificationsController.js
class NotificationsController {
    constructor() {
        this.baseUrl = '/app-gestion-parking/api';
        this.elements = {
            notificationsList: document.getElementById('notificationsList'),
            notificationCount: document.getElementById('notificationCount'),
            markAllReadBtn: document.getElementById('markAllRead')
        };

        this.initialize();
    }

    initialize() {
        this.loadNotifications();
        this.elements.markAllReadBtn.addEventListener('click', () => this.markAllAsRead());
    }

    async loadNotifications() {
        try {
            const response = await fetch(`${this.baseUrl}/notifications`);
            const data = await response.json();

            if (data.success) {
                this.displayNotifications(data.notifications);
                this.updateNotificationCount(data.unreadCount);
            }
        } catch (error) {
            console.error('Erreur chargement notifications:', error);
        }
    }

    displayNotifications(notifications) {
        if (!notifications.length) {
            this.elements.notificationsList.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-bell text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mt-3">Aucune notification</p>
                </div>`;
            return;
        }

        this.elements.notificationsList.innerHTML = notifications
            .map(notif => `
                <div class="notification-item ${notif.read ? 'read' : ''}" data-id="${notif.id}">
                    <div class="d-flex align-items-center">
                        <span class="notification-dot"></span>
                        <div>
                            <p class="mb-1">${notif.message}</p>
                            <small class="text-muted">
                                ${this.formatDate(notif.created_at)}
                            </small>
                        </div>
                    </div>
                </div>
            `).join('');
    }

    updateNotificationCount(count) {
        if (count > 0) {
            this.elements.notificationCount.style.display = 'inline-block';
            this.elements.notificationCount.textContent = count;
        } else {
            this.elements.notificationCount.style.display = 'none';
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch(`${this.baseUrl}/notifications/mark-all-read`, {
                method: 'POST'
            });
            const data = await response.json();

            if (data.success) {
                this.loadNotifications();
            }
        } catch (error) {
            console.error('Erreur marquer comme lu:', error);
        }
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
}

const notificationsController = new NotificationsController();