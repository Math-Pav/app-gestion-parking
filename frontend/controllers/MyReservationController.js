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
            <td>
                <span class="badge bg-${this.getStatusColor(reservation.status)}">
                    ${this.formatStatus(reservation.status)}
                </span>
            </td>
            <td class="text-center">
                ${this.getActionButtons(reservation)}
            </td>
        </tr>`;
        }).join('');
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
            <button class="btn btn-danger btn-sm"
                    onclick="event.stopPropagation(); myReservationController.cancelReservation(${reservation.id})">
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
        if (!confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')) {
            return;
        }

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
                this.displayMessage('La réservation a été annulée avec succès', 'success');
            } else {
                this.displayMessage(data.message || 'Erreur lors de l\'annulation', 'danger');
            }
        } catch (error) {
            console.error('Erreur annulation:', error);
            this.displayMessage('Erreur de connexion au serveur', 'danger');
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