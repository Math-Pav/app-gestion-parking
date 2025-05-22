class PaiementController {
    constructor() {
        this.urlParams = new URLSearchParams(window.location.search);
        this.reservationId = this.urlParams.get('id');

        if (this.reservationId) {
            this.loadReservationById();
        } else {
            window.location.href = '/app-gestion-parking/mes-reservations';
        }

        this.initializeEventListeners();
    }

    loadReservationById() {
        fetch(`/app-gestion-parking/api/reservations/get-reservation?id=${this.reservationId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.reservation) {
                    this.displayReservationDetails(data.reservation);
                } else {
                    console.error('Erreur:', data.message);
                    window.location.href = '/app-gestion-parking/mes-reservations';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                window.location.href = '/app-gestion-parking/mes-reservations';
            });
    }

    displayReservationDetails(reservation) {
        document.getElementById('placeNumber').textContent = `Place ${reservation.place_number}`;
        document.getElementById('placeType').textContent = this.formatVehicleType(reservation.type);
        document.getElementById('startDate').textContent = new Date(reservation.start_date).toLocaleString('fr-FR');
        document.getElementById('endDate').textContent = new Date(reservation.end_date).toLocaleString('fr-FR');
        document.getElementById('duration').textContent = `${reservation.duration}h`;
        document.getElementById('totalPrice').textContent = `${reservation.price}€`;
    }

    formatVehicleType(type) {
        const types = {
            'voiture': 'Voiture',
            'moto': 'Moto',
            'voiture_electrique': 'Véhicule électrique'
        };
        return types[type] || type;
    }

    initializeEventListeners() {
        const cancelButton = document.getElementById('cancelReservation');
        if (cancelButton) {
            cancelButton.addEventListener('click', () => this.handleCancelReservation());
        }
    }

    handleCancelReservation() {
        if (!confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')) {
            return;
        }

        fetch('/app-gestion-parking/api/reservations/update-status', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                reservation_id: this.reservationId,
                status: 'annuler'
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Réservation annulée avec succès');
                    window.location.href = '/app-gestion-parking/mes-reservations';
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
}

document.addEventListener('DOMContentLoaded', () => {
    new PaiementController();
});