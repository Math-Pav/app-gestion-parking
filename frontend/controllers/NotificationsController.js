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
                <div class="d-flex align-items-center flex-grow-1">
                    <span class="notification-dot"></span>
                    <div class="notification-content flex-grow-1">
                        <p class="mb-1">${notif.message}</p>
                        <small class="text-muted">
                            ${this.formatDate(notif.created_at)}
                        </small>
                    </div>
                    <button class="btn btn-light btn-sm mark-as-read">
                        <i class="bi bi-check2"></i> Marquer comme lu
                    </button>
                </div>
            </div>
        `).join('');

        this.elements.notificationsList.querySelectorAll('.mark-as-read').forEach(button => {
            button.addEventListener('click', (event) => this.markAsRead(event));
        });
    }

    updateNotificationCount(count) {
        if (count > 0) {
            this.elements.notificationCount.style.display = 'inline-block';
            this.elements.notificationCount.textContent = count;
        } else {
            this.elements.notificationCount.style.display = 'none';
        }
    }

    async markAsRead(event) {
        const button = event.target.closest('.mark-as-read');
        const notificationItem = button.closest('.notification-item');
        const notificationId = notificationItem.dataset.id;

        try {
            const response = await fetch(`${this.baseUrl}/notifications/mark-as-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ notificationId })
            });

            if (response.ok) {
                notificationItem.classList.add('read');
                const dot = notificationItem.querySelector('.notification-dot');
                if (dot) {
                    dot.style.backgroundColor = 'transparent';
                }
                button.style.display = 'none';

                const count = parseInt(this.elements.notificationCount.textContent, 10);
                this.updateNotificationCount(Math.max(0, count - 1));
            } else {
                console.error('Erreur lors du marquage de la notification comme lue');
            }
        } catch (error) {
            console.error('Erreur réseau:', error);
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