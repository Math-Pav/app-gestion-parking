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
            baseUrl: '/projet_parking/api'
        };

        this.bindEvents();
        this.loadDashboardData();
    }

    bindEvents() {
        this.elements.logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.logout();
        });

        this.elements.newReservationBtn.addEventListener('click', () => {
            // À implémenter: logique pour nouvelle réservation
            this.displayMessage('Fonctionnalité à venir', 'info');
        });
    }

    async loadDashboardData() {
        // À implémenter: chargement des données du tableau de bord
        this.elements.availableSpots.textContent = "20";  // Exemple
        this.elements.myReservations.textContent = "0";   // Exemple
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
                window.location.href = '/projet_parking/';
            } else {
                this.displayMessage('Erreur lors de la déconnexion', 'error');
            }
        } catch (error) {
            this.displayMessage('Erreur de connexion au serveur', 'error');
        }
    }

    displayMessage(text, type) {
        this.elements.message.textContent = text;
        this.elements.message.className = `message ${type}`;
    }
}

new DashboardController();