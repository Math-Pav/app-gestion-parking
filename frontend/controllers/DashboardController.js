class DashboardController {
    constructor() {
        this.elements = {
            logoutBtn: document.getElementById('logoutBtn'),
            message: document.getElementById('message'),
            availableSpots: document.getElementById('availableSpots'),
            myReservations: document.getElementById('myReservations'),
            newReservationBtn: document.getElementById('newReservationBtn')
        };

        this.api = {
            baseUrl: '/app-gestion-parking/api'
        };

        this.bindEvents();
        this.loadDashboardStats();
    }

    async loadDashboardStats() {
        try {
            const response = await fetch(`${this.api.baseUrl}/dashboard/stats`);

            if (!response.ok) {
                throw new Error('Erreur réseau');
            }

            const data = await response.json();

            if (data.success) {
                this.elements.availableSpots.textContent = data.data.availableSpots.total;
                this.elements.myReservations.textContent = data.data.reservations.total;
            } else {
                this.displayMessage('Erreur lors du chargement des données', 'danger');
            }
        } catch (error) {
            this.displayMessage('Erreur de connexion au serveur', 'danger');
        }
    }

    bindEvents() {
        this.elements.logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.logout();
        });

        this.elements.newReservationBtn.addEventListener('click', () => {
            window.location.href = '/app-gestion-parking/reservation';
        });
    }


    async logout() {
        try {
            const response = await fetch(`${this.api.baseUrl}/logout`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                window.location.href = '/app-gestion-parking/';
            } else {
                this.displayMessage('Erreur lors de la déconnexion', 'error');
            }
        } catch (error) {
            this.displayMessage('Erreur de connexion au serveur', 'error');
        }
    }

    displayMessage(text, type) {
        const messageElement = this.elements.message;
        messageElement.textContent = text;
        messageElement.style.display = 'block';
        messageElement.className = `alert alert-${type}`;

        setTimeout(() => {
            messageElement.style.display = 'none';
        }, 3000);
    }
}
document.addEventListener('DOMContentLoaded', () => new DashboardController());
