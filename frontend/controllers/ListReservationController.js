class ListReservationController {
    constructor() {
        console.log('Initialisation du ListReservationController');
        this.elements = {
            reservationsList: document.getElementById('reservationsList')
        };
        this.api = {
            baseUrl: '/app-gestion-parking/api'
        };
        this.loadReservations();
    }

    loadReservations() {
        fetch(`${this.api.baseUrl}/reservations/list`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.displayReservations(data.reservations);
                } else {
                    this.showError(data.message || 'Erreur de chargement');
                }
            })
            .catch(error => this.showError('Erreur de connexion au serveur'));
    }

    displayReservations(reservations) {
        if (!reservations || reservations.length === 0) {
            this.elements.reservationsList.innerHTML = `
                <tr><td colspan="6" class="text-center py-3">Aucune réservation trouvée</td></tr>`;
            return;
        }

        this.elements.reservationsList.innerHTML = reservations.map(reservation => `
            <tr>
                <td class="px-4">
                    ${reservation.formatted_date}
                    ${reservation.formatted_date !== reservation.formatted_end_date ?
            ` - ${reservation.formatted_end_date}` : ''}
                </td>
                <td>${reservation.number_place} (${reservation.type_place})</td>
                <td>${reservation.start_time}</td>
                <td>${reservation.end_time}</td>
                <td>
                    <span class="badge ${this.getStatusBadgeClass(reservation.status)}">
                        ${this.getStatusText(reservation.status)}
                    </span>
                </td>
                <td class="text-end pe-4">
                    ${this.getActionButton(reservation)}
                </td>
            </tr>
        `).join('');
    }

    getStatusBadgeClass(status) {
        const classes = {
            'reserver': 'bg-success',
            'en_cours': 'bg-primary',
            'terminer': 'bg-info',
            'annuler': 'bg-danger',
            'attente': 'bg-warning'
        };
        return classes[status] || 'bg-secondary';
    }

    getStatusText(status) {
        const texts = {
            'reserver': 'Réservé',
            'en_cours': 'En cours',
            'terminer': 'Terminé',
            'annuler': 'Annulé',
            'attente': 'En attente'
        };
        return texts[status] || status;
    }

    getActionButton(reservation) {
        if (['reserver', 'en_cours', 'attente'].includes(reservation.status)) {
            return `
                <button type="button"
                        class="btn btn-danger btn-sm"
                        data-action="cancel"
                        data-reservation-id="${reservation.id}"
                        onclick="listReservationController.handleCancelReservation(${reservation.id})">
                    <i class="bi bi-x-circle me-1"></i>Annuler
                </button>`;
        }
        return '';
    }
    handleCancelReservation(reservationId) {
        if (!confirm('Voulez-vous vraiment annuler cette réservation ?')) {
            return;
        }

        fetch(`${this.api.baseUrl}/reservations/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                reservationId: parseInt(reservationId) // Assurez-vous que l'ID est un nombre
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.loadReservations();
                    this.showSuccess('Réservation annulée avec succès');
                } else {
                    this.showError(data.message || 'Erreur lors de l\'annulation');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                this.showError('Erreur lors de l\'annulation');
            });
    }

    showError(message) {
        alert(message);
    }

    showSuccess(message) {
        alert(message);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.listReservationController = new ListReservationController();
});