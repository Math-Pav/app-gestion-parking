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
                        class="btn btn-warning btn-sm me-2"
                        data-action="edit"
                        data-reservation-id="${reservation.id}"
                        onclick="listReservationController.handleEditReservation(${reservation.id})">
                    <i class="bi bi-pencil-square me-1"></i>Modifier
                </button>
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

    handleEditReservation(reservationId) {
        fetch(`${this.api.baseUrl}/reservations/get-reservation?id=${reservationId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.populateEditForm(data.reservation);
                    this.openModal('editReservationModal');
                } else {
                    this.showError(data.message);
                }
            })
            .catch(error => this.showError('Erreur lors du chargement des données'));
    }

    populateEditForm(reservation) {
        document.getElementById('editReservationId').value = reservation.id;
        document.getElementById('editStartDate').value = reservation.start_date;
        document.getElementById('editEndDate').value = reservation.end_date;
        document.getElementById('editStartTime').value = reservation.start_time;
        document.getElementById('editEndTime').value = reservation.end_time;
    }

    openModal(id) {
        const modal = new bootstrap.Modal(document.getElementById(id));
        modal.show();
    }

    handleCancelReservation(reservationId) {
        if (!confirm('Voulez-vous vraiment annuler cette réservation ?')) {
            return;
        }

        fetch(`${this.api.baseUrl}/reservations/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ reservationId: reservationId })
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
            .catch(error => this.showError('Erreur lors de l\'annulation'));
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