class PaiementController {
    constructor() {
        console.log('Initialisation du contrôleur de paiement');
        this.urlParams = new URLSearchParams(window.location.search);
        this.reservationId = this.urlParams.get('id');
        console.log('ID de réservation:', this.reservationId);

        if (this.reservationId) {
            this.loadReservationById();
        } else {
            console.log('Pas d\'ID de réservation, redirection...');
            window.location.href = '/app-gestion-parking/mes-reservations';
        }

        this.initializeEventListeners();
    }

    loadReservationById() {
        console.log('Chargement de la réservation...');
        fetch(`/app-gestion-parking/api/reservations/get-reservation?id=${this.reservationId}`)
            .then(response => response.json())
            .then(data => {
                console.log('Données reçues:', data);
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
        console.log('Affichage des détails...');
        const elements = {
            'placeNumber': `Place ${reservation.place_number}`,
            'placeType': this.formatVehicleType(reservation.type),
            'startDate': new Date(reservation.start_date).toLocaleString('fr-FR'),
            'endDate': new Date(reservation.end_date).toLocaleString('fr-FR'),
            'duration': `${reservation.duration}h`,
            'totalPrice': `${reservation.price}€`
        };

        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            } else {
                console.error(`Élément non trouvé: ${id}`);
            }
        });
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
        console.log('Initialisation des écouteurs d\'événements...');
        const cancelButton = document.getElementById('cancelReservation');
        console.log('Bouton d\'annulation trouvé:', cancelButton);

        if (cancelButton) {
            cancelButton.addEventListener('click', () => {
                console.log('Clic sur le bouton d\'annulation');
                this.handleCancelReservation();
            });
        } else {
            console.error('Bouton d\'annulation non trouvé');
        }
    }

    handleCancelReservation() {
        console.log('Gestion de l\'annulation...');

        if (!this.reservationId) {
            this.showError('ID de réservation non trouvé');
            return;
        }

        fetch('/app-gestion-parking/api/reservations/cancel', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                reservation_id: this.reservationId
            })
        })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Erreur serveur');
                }
                return data;
            })
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
                this.showError(error.message || 'Erreur lors de l\'annulation');
            });
    }

    showError(message) {
        console.error('Affichage erreur:', message);
        alert(message);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM chargé, initialisation du contrôleur...');
    new PaiementController();
});