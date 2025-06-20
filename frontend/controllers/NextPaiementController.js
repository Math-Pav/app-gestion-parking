class NextPaiementController {
    constructor() {
        this.form = document.getElementById('paymentForm');
        this.validateButton = document.getElementById('validatePayment');
        this.cancelButton = document.getElementById('cancelPayment');
        this.acceptTerms = document.getElementById('acceptTerms');
        this.urlParams = new URLSearchParams(window.location.search);
        this.reservationId = this.urlParams.get('id');

        if (!this.reservationId) {
            alert('ID de réservation manquant');
            window.location.href = '/app-gestion-parking/mes-reservations';
            return;
        }

        this.initializeEventListeners();
        this.formatInputs();
        this.loadReservationDetails();
    }

    async loadReservationDetails() {
        try {
            const response = await fetch(`/app-gestion-parking/api/reservations/get-reservation?id=${this.reservationId}`);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Erreur lors de la récupération des détails');
            }

            if (data.success && data.reservation) {
                console.log('Détails de la réservation chargés:', data.reservation);
            } else {
                throw new Error('Données de réservation non trouvées');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert(error.message);
            window.location.href = '/app-gestion-parking/mes-reservations';
        }
    }

    initializeEventListeners() {
        if (this.form) {
            this.form.addEventListener('submit', (e) => this.handlePayment(e));
        }

        if (this.cancelButton) {
            this.cancelButton.addEventListener('click', () => this.handleCancel());
        }

        if (this.acceptTerms) {
            this.acceptTerms.addEventListener('change', () => this.toggleButton());
        }

        this.toggleButton();
    }

    formatInputs() {
        const cardNumber = document.getElementById('cardNumber');
        if (cardNumber) {
            cardNumber.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                value = value.replace(/(\d{4})/g, '$1 ').trim();
                e.target.value = value.substring(0, 19);
            });
        }

        const expiryDate = document.getElementById('expiryDate');
        if (expiryDate) {
            expiryDate.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2);
                }
                e.target.value = value.substring(0, 5);
            });
        }

        const cvc = document.getElementById('cvc');
        if (cvc) {
            cvc.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/\D/g, '').substring(0, 3);
            });
        }

        const cardName = document.getElementById('cardName');
        if (cardName) {
            cardName.addEventListener('input', (e) => {
                e.target.value = e.target.value.toUpperCase();
            });
        }
    }

    toggleButton() {
        if (this.validateButton && this.acceptTerms) {
            this.validateButton.disabled = !this.acceptTerms.checked;
        }
    }

    async handlePayment(event) {
        event.preventDefault();

        if (!this.validateForm()) {
            return;
        }

        this.validateButton.disabled = true;
        this.validateButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Traitement...';

        try {
            await new Promise(resolve => setTimeout(resolve, 2000));

            const response = await fetch('/app-gestion-parking/api/payment/process', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    reservation_id: this.reservationId
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                alert('Paiement effectué avec succès !');
                window.location.href = '/app-gestion-parking/mes-reservations';
            } else {
                throw new Error(data.message || 'Erreur lors du traitement du paiement');
            }
        } catch (error) {
            alert('Erreur : ' + error.message);
        } finally {
            this.validateButton.disabled = false;
            this.validateButton.innerHTML = 'Valider le paiement';
        }
    }

    validateForm() {
        const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
        const expiryDate = document.getElementById('expiryDate').value;
        const cvc = document.getElementById('cvc').value;
        const cardName = document.getElementById('cardName').value;

        if (!cardNumber || cardNumber.length !== 16) {
            alert('Numéro de carte invalide');
            return false;
        }

        if (!expiryDate || !expiryDate.match(/^(0[1-9]|1[0-2])\/([0-9]{2})$/)) {
            alert('Date d\'expiration invalide');
            return false;
        }

        if (!cvc || cvc.length !== 3) {
            alert('Code CVC invalide');
            return false;
        }

        if (!cardName || cardName.length < 3) {
            alert('Nom sur la carte invalide');
            return false;
        }

        return true;
    }

    handleCancel() {
        if (confirm('Êtes-vous sûr de vouloir annuler le paiement ?')) {
            window.location.href = '/app-gestion-parking/mes-reservations';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => new NextPaiementController());