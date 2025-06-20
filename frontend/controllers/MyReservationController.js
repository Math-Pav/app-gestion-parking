class MyReservationController {
    constructor() {
        this.elements = {
            tableBody: document.getElementById('reservationsTableBody')
        };

        if (!this.elements.tableBody) {
            console.error('Element tableBody non trouvé');
            return;
        }

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
            console.error('Erreur de chargement:', error);
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

        this.elements.tableBody.innerHTML = reservations.map(reservation => `
        <tr data-reservation-id="${reservation.id}">
            <td>Place ${reservation.number_place}</td>
            <td>${this.formatVehicleType(reservation.type_place)}</td>
            <td>${new Date(reservation.start_date).toLocaleString('fr-FR')}</td>
            <td>${new Date(reservation.end_date).toLocaleString('fr-FR')}</td>
            <td>${reservation.duration}h</td>
            <td>${reservation.price}€</td>
            <td>
                <span class="badge bg-${this.getStatusColor(reservation.status)}">
                    ${this.formatStatus(reservation.status)}
                </span>
            </td>
            <td class="text-center">
                ${this.getActionButtons(reservation)}
            </td>
        </tr>
    `).join('');

        const cancelButtons = document.querySelectorAll('.cancel-reservation');
        cancelButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const reservationId = button.dataset.reservationId;
                this.cancelReservation(reservationId);
            });
        });
    }

    attachRowClickHandlers() {
        const rows = this.elements.tableBody.getElementsByTagName('tr');
        for (const row of rows) {
            row.style.cursor = 'pointer';
            row.addEventListener('click', () => {
                const reservationId = row.dataset.reservationId;
                if (reservationId) {
                    window.location.href = `/app-gestion-parking/paiement?id=${reservationId}`;
                }
            });
        }
    }

    getActionButtons(reservation) {
        let buttons = [];

        if (reservation.status === 'attente') {
            buttons.push(`
        <a href="/app-gestion-parking/paiement?id=${reservation.id}"
           class="btn btn-primary btn-sm me-2">
            <i class="bi bi-credit-card"></i> Payer
        </a>`
            );
        }

        if (reservation.status === 'attente' || reservation.status === 'reserver') {
            buttons.push(`
        <button type="button" 
                class="btn btn-danger btn-sm cancel-reservation" 
                data-reservation-id="${reservation.id}"
                onclick="event.stopPropagation();">
            <i class="bi bi-x-circle"></i> Annuler
        </button>`
            );
        }

        return buttons.join('');
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
            'attente': 'En attente',
            'reserver': 'Réservée',
            'en_cours': 'En cours',
            'terminer': 'Terminée',
            'annuler': 'Annulée'
        };
        return statuses[status] || status;
    }

    getStatusColor(status) {
        const colors = {
            'attente': 'warning',
            'reserver': 'primary',
            'en_cours': 'success',
            'terminer': 'secondary',
            'annuler': 'danger'
        };
        return colors[status] || 'secondary';
    }

    async cancelReservation(reservationId) {
        if (!reservationId) {
            alert('ID de réservation invalide');
            return;
        }

        if (!confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')) {
            return;
        }

        try {
            const response = await fetch('/app-gestion-parking/api/reservations/cancel', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    reservation_id: reservationId
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                window.location.reload();
            } else {
                throw new Error(data.message || 'Erreur lors de l\'annulation');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert(error.message);
        }
    }

    displayMessage(text, type) {
        if (!this.elements.errorMessage) {
            console.error('Element errorMessage non trouvé');
            return;
        }

        this.elements.errorMessage.textContent = text;
        this.elements.errorMessage.className = `alert alert-${type}`;
        this.elements.errorMessage.style.display = 'block';

        setTimeout(() => {
            this.elements.errorMessage.style.display = 'none';
        }, 3000);
    }
}

const myReservationController = new MyReservationController();