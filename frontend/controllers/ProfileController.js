class ProfileController {
    constructor() {
        this.initElements();
        this.loadProfile();
    }

    initElements() {
        this.elements = {
            name: document.querySelector('#name'),
            email: document.querySelector('#email'),
            phone: document.querySelector('#phone'),
            role: document.querySelector('#role'),
            registration_date: document.querySelector('#registration_date'),
            status: document.querySelector('#status'),
            message: document.querySelector('#message'),
            activeReservations: document.querySelector('#activeReservations'),
            totalReservations: document.querySelector('#totalReservations')
        };
    }

    async loadProfile() {
        try {
            const response = await fetch('/projet_parking/api/profile');
            const result = await response.json();
            console.log('Données reçues:', result);

            if (result.success && result.user) {
                this.updateProfileData(result.user);
            } else {
                throw new Error('Données invalides');
            }
        } catch (error) {
            console.error('Erreur:', error);
            this.showError('Erreur lors du chargement du profil');
        }
    }

    updateProfileData(user) {
        if (!user) return;

        this.elements.name.textContent = user.name;
        this.elements.email.textContent = user.email;

        this.elements.phone.textContent = user.phone || 'Non renseigné';

        if (user.role === "admin") {
            this.elements.role.textContent = "Administrateur";
        } else {
            this.elements.role.textContent = "Utilisateur";
        }

        const date = new Date(user.registration_date);
        this.elements.registration_date.textContent = date.toLocaleDateString('fr-FR');

        const isActive = user.status === true || user.status === 1 || user.status === "1";
        if (isActive) {
            this.elements.status.textContent = 'Actif';
            this.elements.status.className = 'badge rounded-pill fs-6 p-2 w-100 bg-success';
        } else {
            this.elements.status.textContent = 'Inactif';
            this.elements.status.className = 'badge rounded-pill fs-6 p-2 w-100 bg-danger';
        }

        this.elements.activeReservations.textContent = '0';
        this.elements.totalReservations.textContent = '0';
    }

    showError(message) {
        this.elements.message.textContent = message;
        this.elements.message.className = 'alert alert-danger';
        this.elements.message.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', () => new ProfileController());