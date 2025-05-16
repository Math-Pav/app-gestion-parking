class PaiementController {
    constructor() {
        this.loadReservationDetails();
        this.initializeEventListeners();
    }

    loadReservationDetails() {
        fetch('/projet_parking/api/reservation/latest')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.displayReservationDetails(data.reservation);
                } else {
                    alert('Aucune réservation active trouvée');
                    setTimeout(() => {
                        window.location.replace('/projet_parking/dashboard');
                    }, 100);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors du chargement de la réservation');
            });
    }

    displayReservationDetails(reservation) {
        document.getElementById('placeNumber').textContent = reservation.place_number;
        document.getElementById('placeType').textContent = reservation.type;

        const startDate = new Date(reservation.start_date);
        const endDate = new Date(reservation.end_date);
        document.getElementById('startDate').textContent = startDate.toLocaleString('fr-FR');
        document.getElementById('endDate').textContent = endDate.toLocaleString('fr-FR');
        document.getElementById('duration').textContent = `${reservation.duration}h`;
        document.getElementById('totalPrice').textContent = `${reservation.price}€`;
    }


    initializeEventListeners() {
        const cancelButton = document.getElementById('cancelReservation');
        if (cancelButton) {
            cancelButton.addEventListener('click', () => this.handleCancelReservation());
        }
    }

    handleCancelReservation() {
        if (confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')) {
            const urlParams = new URLSearchParams(window.location.search);
            const reservationId = urlParams.get('id');

            fetch('/projet_parking/api/reservations/update-status', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    reservation_id: reservationId,
                    status: 'annuler'
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        alert('Réservation annulée avec succès');
                        window.location.href = '/projet_parking/dashboard';
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    this.showError('Erreur lors de l\'annulation');
                });
        }
    }

    showError(message) {
        alert(message);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new PaiementController();
});