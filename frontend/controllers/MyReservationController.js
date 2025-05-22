class MyReservationController {
    constructor() {
        this.elements = {
            tableBody: document.getElementById('reservationsTableBody'),
            errorMessage: document.getElementById('errorMessage')
        };

        this.api = {
            baseUrl: '/app-gestion-parking/api/reservations'
        };

        this.loadReservations();
    }

    async loadReservations() {
        try {
            const response = await fetch(`${this.api.baseUrl}/user-reservations`);
            const data = await response.json();

            if (data.success) {
                this.displayReservations(data.reservations);
            } else {
                this.displayMessage(data.message || 'Erreur de chargement', 'danger');
            }
        } catch (error) {
            this.displayMessage('Erreur de connexion au serveur', 'danger');
        }
    }

    displayReservations(reservations) {
        if (!reservations || reservations.length === 0) {
            this.elements.tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center">
                    Aucune réservation trouvée
                </td>
            </tr>`;
            return;
        }

        this.elements.tableBody.innerHTML = reservations.map(reservation => {
            const startDate = new Date(reservation.start_date).toLocaleString('fr-FR');
            const endDate = new Date(reservation.end_date).toLocaleString('fr-FR');

            return `
            <tr>
                <td>Place ${reservation.number_place}</td>
                <td>${this.formatVehicleType(reservation.type_place)}</td>
                <td>${startDate}</td>
                <td>${endDate}</td>
                <td>${reservation.duration}h</td>
                <td>${reservation.price}€</td>
                <td><span class="badge bg-${this.getStatusColor(reservation.status)}">${this.formatStatus(reservation.status)}</span></td>
                <td>
                    ${reservation.status === 'reserver' ?
                `<button class="btn btn-danger btn-sm" onclick="myReservationController.cancelReservation(${reservation.id})">
                            <i class="bi bi-x-circle"></i> Annuler
                        </button>` : ''}
                </td>
            </tr>`;
        }).join('');
    }

    formatVehicleType(type) {
        const types = {
            'voiture': 'Voiture',
            'moto': 'Moto',
            'voiture_electrique': 'Véhicule électrique'
        };
        return types[type] || type;
    }

    formatStatus(status) {
        const statuses = {
            'reserver': 'Réservée',
            'en_cours': 'En cours',
            'terminer': 'Terminée',
            'annuler': 'Annulée'
        };
        return statuses[status] || status;
    }

    getStatusColor(status) {
        const colors = {
            'reserver': 'primary',
            'en_cours': 'success',
            'terminer': 'secondary',
            'annuler': 'danger'
        };
        return colors[status] || 'secondary';
    }

    async cancelReservation(reservationId) {
        if (confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')) {
            try {
                const response = await fetch(`${this.api.baseUrl}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ reservation_id: reservationId })
                });

                const data = await response.json();

                if (data.success) {
                    await this.loadReservations();
                    this.displayMessage('La réservation a été supprimée avec succès', 'success');
                } else {
                    this.displayMessage(data.message || 'Erreur lors de la suppression', 'danger');
                }
            } catch (error) {
                this.displayMessage('Erreur de connexion au serveur', 'danger');
            }
        }
    }

    displayMessage(text, type) {
        this.elements.errorMessage.textContent = text;
        this.elements.errorMessage.className = `alert alert-${type}`;
        this.elements.errorMessage.style.display = 'block';

        setTimeout(() => {
            this.elements.errorMessage.style.display = 'none';
        }, 3000);
    }
}

const myReservationController = new MyReservationController();