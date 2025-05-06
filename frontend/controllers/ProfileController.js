class ProfileController {
    constructor() {
        this.initElements();
        this.loadProfile();
        this.bindEvents();
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
            totalReservations: document.querySelector('#totalReservations'),
            editProfileBtn: document.querySelector('#editProfileBtn'),
            editProfileModal: new bootstrap.Modal(document.querySelector('#editProfileModal')),
            editName: document.querySelector('#editName'),
            editEmail: document.querySelector('#editEmail'),
            editPhone: document.querySelector('#editPhone'),
            saveProfileBtn: document.querySelector('#saveProfileBtn')
        };
    }

    bindEvents() {
        this.elements.editProfileBtn.addEventListener('click', () => this.showEditModal());
        this.elements.saveProfileBtn.addEventListener('click', () => this.saveProfile());
    }

    async loadProfile() {
        try {
            const response = await fetch('/projet_parking/api/profile');
            const result = await response.json();

            if (result.success && result.user) {
                this.updateProfileData(result.user);
            } else {
                throw new Error('Données invalides');
            }
        } catch (error) {
            console.error('Erreur:', error);
            this.showMessage('Erreur lors du chargement du profil', 'danger');
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

    showEditModal() {
        this.elements.editName.value = this.elements.name.textContent;
        this.elements.editEmail.value = this.elements.email.textContent;
        this.elements.editPhone.value = this.elements.phone.textContent === 'Non renseigné' ? '' : this.elements.phone.textContent;
        this.elements.editProfileModal.show();
    }

    async saveProfile() {
        try {
            const response = await fetch('/projet_parking/api/profile/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name: this.elements.editName.value,
                    email: this.elements.editEmail.value,
                    phone: this.elements.editPhone.value
                })
            });

            const result = await response.json();

            if (result.success) {
                this.elements.editProfileModal.hide();
                await this.loadProfile();
                this.showMessage('Profil mis à jour avec succès', 'success');
            } else {
                throw new Error(result.message || 'Erreur lors de la mise à jour');
            }
        } catch (error) {
            this.showMessage(error.message, 'danger');
        }
    }

    showMessage(message, type) {
        this.elements.message.textContent = message;
        this.elements.message.className = `alert alert-${type}`;
        this.elements.message.style.display = 'block';
        setTimeout(() => {
            this.elements.message.style.display = 'none';
        }, 3000);
    }
}

document.addEventListener('DOMContentLoaded', () => new ProfileController());