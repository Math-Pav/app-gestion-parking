class ReservationController {
    constructor() {
        this.initElements();
        this.bindEvents();
        this.api = {
            baseUrl: '/app-gestion-parking/api/reservations'
        };
    }

    initElements() {
        this.elements = {
            form: document.getElementById('reservationForm'),
            errorMessage: document.getElementById('errorMessage'),
            vehicleTypeInputs: document.querySelectorAll('input[name="vehicleType"]'),
            parkingSpotSelect: document.getElementById('parkingSpot'),
            startDateTime: document.getElementById('startDateTime'),
            endDateTime: document.getElementById('endDateTime'),
            recapType: document.getElementById('recapType'),
            recapPlace: document.getElementById('recapPlace'),
            recapDuree: document.getElementById('recapDuree'),
            recapPrix: document.getElementById('recapPrix'),
            availableSpots: document.getElementById('availableSpots'),
            totalSpots: document.getElementById('totalSpots'),
            cancelBtn: document.getElementById('cancelReservation')
        };
    }

    bindEvents() {
        this.elements.form.addEventListener('submit', (e) => this.handleSubmit(e));

        this.elements.vehicleTypeInputs.forEach(input => {
            input.addEventListener('change', () => {
                this.updateRecap();
                this.loadAvailableSpots();
            });
        });

        this.elements.parkingSpotSelect.addEventListener('change', () => this.updateRecap());

        this.elements.startDateTime.addEventListener('change', () => {
            this.updateRecap();
            this.loadAvailableSpots();
        });

        this.elements.endDateTime.addEventListener('change', () => {
            this.updateRecap();
            this.loadAvailableSpots();
        });

        if (this.elements.cancelBtn) {
            this.elements.cancelBtn.addEventListener('click', () => this.handleCancelReservation());
        }
    }

    async handleCancelReservation() {
        const result = await Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: 'Voulez-vous vraiment annuler cette réservation ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, annuler',
            cancelButtonText: 'Non, garder',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`${this.api.baseUrl}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        reservation_id: this.currentReservationId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        title: 'Annulée !',
                        text: 'Votre réservation a été annulée avec succès',
                        icon: 'success'
                    }).then(() => {
                        window.location.href = '/app-gestion-parking/profile';
                    });
                } else {
                    this.displayMessage(data.message || 'Erreur lors de l\'annulation', 'error');
                }
            } catch (error) {
            }
        }
    }

    updateRecap() {
        const selectedType = document.querySelector('input[name="vehicleType"]:checked')?.value || '--';
        const selectedSpot = this.elements.parkingSpotSelect.value || '--';
        const startDate = this.elements.startDateTime.value;
        const endDate = this.elements.endDateTime.value;

        this.elements.recapType.textContent = selectedType;
        this.elements.recapPlace.textContent = selectedSpot;

        if (startDate && endDate) {
            const duration = this.calculateDuration(startDate, endDate);
            this.elements.recapDuree.textContent = duration;
            const price = this.calculatePrice(duration, selectedType);
            this.elements.recapPrix.textContent = `${price.toFixed(2)}€`;
        }
    }
    calculatePrice(duration, type) {
        const hourlyRates = {
            'moto': 2,
            'voiture': 3,
            'electrique': 4,
            'handicap': 3
        };
        const hours = parseInt(duration.match(/\d+/)[0]);
        return hours * (hourlyRates[type]);
    }

    calculateDuration(start, end) {
        const startDate = new Date(start);
        const endDate = new Date(end);
        const hours = Math.ceil((endDate - startDate) / (1000 * 60 * 60));
        return `${hours} heure(s)`;
    }

    async handleSubmit(e) {
        e.preventDefault();
        this.elements.errorMessage.style.display = 'none';

        const formData = {
            parking_id: this.elements.parkingSpotSelect.value,
            vehicle_type: document.querySelector('input[name="vehicleType"]:checked')?.value,
            start_date: this.elements.startDateTime.value,
            end_date: this.elements.endDateTime.value,
            price: parseFloat(this.elements.recapPrix.textContent.replace('€', ''))
        };

        try {
            const response = await fetch(`${this.api.baseUrl}/create`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    title: 'Réservation en attente',
                    text: 'Votre réservation est en attente de paiement. Voulez-vous procéder au paiement maintenant ?',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Payer maintenant',
                    cancelButtonText: 'Plus tard'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `/app-gestion-parking/payment?reservation_id=${data.reservation_id}`;
                    } else {
                        window.location.href = '/app-gestion-parking/profile';
                    }
                });
            } else {
                this.displayMessage(data.message || 'Une erreur est survenue', 'error');
            }
        } catch (error) {
        }
    }

    async loadAvailableSpots() {
        const vehicleType = document.querySelector('input[name="vehicleType"]:checked')?.value;
        const startDate = this.elements.startDateTime.value;
        const endDate = this.elements.endDateTime.value;

        if (!vehicleType || !startDate || !endDate) return;

        try {
            const response = await fetch(`${this.api.baseUrl}/available-spots?type=${vehicleType}&start=${startDate}&end=${endDate}`);
            const data = await response.json();

            if (data.success) {
                this.updateAvailableSpots(data.spots);
            } else {
                this.displayMessage(data.message, 'error');
            }
        } catch (error) {
            console.error('Erreur lors du chargement des places:', error);
            this.displayMessage('Erreur lors du chargement des places disponibles', 'error');
        }
    }

    updateAvailableSpots(spots) {
        this.elements.parkingSpotSelect.innerHTML = '<option value="">Sélectionnez une place</option>';
        if (spots && spots.length > 0) {
            spots.forEach(spot => {
                const option = document.createElement('option');
                option.value = spot.id;
                option.textContent = `Place ${spot.number_place}`;
                this.elements.parkingSpotSelect.appendChild(option);
            });
            this.elements.parkingSpotSelect.disabled = false;
        } else {
            const option = document.createElement('option');
            option.value = "";
            option.textContent = "Aucune place disponible";
            this.elements.parkingSpotSelect.appendChild(option);
            this.elements.parkingSpotSelect.disabled = true;
        }
    }

    displayMessage(text, type) {
        this.elements.errorMessage.textContent = text;
        this.elements.errorMessage.className = `alert alert-${type}`;
        this.elements.errorMessage.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', () => new ReservationController());