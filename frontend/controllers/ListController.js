class ListController {
    constructor() {
        console.log('Initialisation du ListController');
        this.initializeEventListeners();
        this.loadUsers();
    }

    initializeEventListeners() {
        const usersList = document.getElementById('usersList');
        if (usersList) {
            usersList.addEventListener('click', (e) => {
                const target = e.target.closest('.btn');
                if (target) {
                    const action = target.dataset.action;
                    const userId = target.dataset.userId;

                    switch(action) {
                        case 'edit':
                            this.handleEditUser(userId);
                            break;
                        case 'delete':
                            this.handleDeleteUser(userId);
                            break;
                        case 'activate':
                            this.handleActivateUser(userId);
                            break;
                    }
                }
            });
        }

        const addUserBtn = document.getElementById('addUserBtn');
        if (addUserBtn) {
            addUserBtn.addEventListener('click', () => this.handleAddUser());
        }

        const saveUserBtn = document.getElementById('saveUserBtn');
        if (saveUserBtn) {
            saveUserBtn.addEventListener('click', () => this.handleSaveUser());
        }

        const toggleAddPassword = document.getElementById('toggleAddPassword');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');

        if (toggleAddPassword) {
            toggleAddPassword.addEventListener('click', () => this.togglePasswordVisibility('addPassword', 'toggleAddPassword'));
        }
        if (toggleConfirmPassword) {
            toggleConfirmPassword.addEventListener('click', () => this.togglePasswordVisibility('confirmPassword', 'toggleConfirmPassword'));
        }
    }

    loadUsers() {
        fetch('/app-gestion-parking/api/users/list')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.displayUsers(data.users);
                } else {
                    this.showError(data.message);
                }
            })
            .catch(error => this.showError('Erreur lors du chargement des utilisateurs'));
    }

    validatePhoneNumber(phone) {
        const phoneRegex = /^[0-9]{10}$/;
        return phone === '' || phoneRegex.test(phone);
    }

    displayUsers(users) {
        const usersList = document.getElementById('usersList');
        if (!usersList) return;

        usersList.innerHTML = users.map(user => `
            <tr class="${user.status === 'inactif' ? 'table-secondary' : ''}">
                <td class="px-4">${this.escapeHtml(user.name)}</td>
                <td>${this.escapeHtml(user.email)}</td>
                <td>${this.escapeHtml(user.phone || 'Non renseigné')}</td>
                <td>
                    <span class="badge ${this.getRoleBadgeClass(user.role)}">
                        ${this.formatRole(user.role)}
                    </span>
                </td>
                <td>
                    <span class="badge ${this.getStatusBadgeClass(user.status)}">
                        ${this.formatStatus(user.status)}
                    </span>
                </td>
                <td class="text-end pe-4">
                    <button class="btn btn-warning btn-sm me-2" data-action="edit" data-user-id="${user.id}"
                            ${user.status === 'inactif' ? 'disabled' : ''}>
                        <i class="bi bi-pencil-square me-1"></i>Modifier
                    </button>
                    ${user.status === 'inactif' ?
            `<button class="btn btn-success btn-sm" data-action="activate" data-user-id="${user.id}">
                            <i class="bi bi-check-circle me-1"></i>Activer
                        </button>` :
            `<button class="btn btn-danger btn-sm" data-action="delete" data-user-id="${user.id}">
                            <i class="bi bi-trash me-1"></i>Désactiver
                        </button>`
        }
                </td>
            </tr>
        `).join('');
    }

    handleAddUser() {
        const formData = {
            name: document.getElementById('addName').value,
            email: document.getElementById('addEmail').value,
            phone: document.getElementById('addPhone').value,
            password: document.getElementById('addPassword').value,
            confirmPassword: document.getElementById('confirmPassword').value,
            role: document.getElementById('addRole').value,
            status: 'active'
        };

        if (!this.validateForm(formData)) return;
        if (!this.validatePhoneNumber(formData.phone)) {
            this.showError('Le numéro de téléphone doit contenir 10 chiffres');
            return;
        }

        fetch('/app-gestion-parking/api/users/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.closeModal('addUserModal');
                    document.getElementById('addUserForm').reset();
                    this.loadUsers();
                    this.showSuccess('Utilisateur ajouté avec succès');
                } else {
                    this.showError(data.message);
                }
            })
            .catch(error => this.showError('Erreur lors de l\'ajout de l\'utilisateur'));
    }

    handleEditUser(userId) {
        fetch(`/app-gestion-parking/api/users/get-user?id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.populateEditForm(data.user);
                    this.openModal('editUserModal');
                } else {
                    this.showError(data.message);
                }
            })
            .catch(error => this.showError('Erreur lors du chargement des données'));
    }

    handleActivateUser(userId) {
        if (confirm('Voulez-vous réactiver cet utilisateur ?')) {
            fetch(`/app-gestion-parking/api/users/get-user?id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const userData = {
                            ...data.user,
                            id: userId,
                            status: 'active'
                        };
                        return fetch('/app-gestion-parking/api/users/update', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(userData)
                        });
                    } else {
                        throw new Error(data.message);
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.loadUsers();
                        this.showSuccess('Utilisateur réactivé avec succès');
                    } else {
                        this.showError(data.message);
                    }
                })
                .catch(error => this.showError('Erreur lors de la réactivation'));
        }
    }

    handleSaveUser() {
        const userData = {
            id: document.getElementById('editUserId').value,
            name: document.getElementById('editName').value,
            email: document.getElementById('editEmail').value,
            phone: document.getElementById('editPhone').value,
            role: document.getElementById('editRole').value,
            status: document.getElementById('editStatus').value
        };

        if (!this.validatePhoneNumber(userData.phone)) {
            this.showError('Le numéro de téléphone doit contenir 10 chiffres');
            return;
        }

        fetch('/app-gestion-parking/api/users/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(userData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.closeModal('editUserModal');
                    this.loadUsers();
                    this.showSuccess('Utilisateur modifié avec succès');
                } else {
                    this.showError(data.message);
                }
            })
            .catch(error => this.showError('Erreur lors de la modification'));
    }

    handleDeleteUser(userId) {
        if (confirm('Êtes-vous sûr de vouloir désactiver cet utilisateur ?')) {
            fetch('/app-gestion-parking/api/users/delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: userId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.loadUsers();
                        this.showSuccess('Utilisateur désactivé avec succès');
                    } else {
                        this.showError(data.message);
                    }
                })
                .catch(error => this.showError('Erreur lors de la désactivation'));
        }
    }

    togglePasswordVisibility(inputId, buttonId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(buttonId);

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    }

    validateForm(data) {
        if (!data.name || !data.email || !data.password) {
            this.showError('Veuillez remplir tous les champs obligatoires');
            return false;
        }
        if (data.password.length < 6) {
            this.showError('Le mot de passe doit contenir au moins 6 caractères');
            return false;
        }
        if (data.password !== data.confirmPassword) {
            this.showError('Les mots de passe ne correspondent pas');
            return false;
        }
        return true;
    }

    getRoleBadgeClass(role) {
        return role === 'admin' ? 'bg-danger' : 'bg-primary';
    }

    getStatusBadgeClass(status) {
        return status === 'active' ? 'bg-success' : 'bg-secondary';
    }

    formatRole(role) {
        return role === 'admin' ? 'Administrateur' : 'Utilisateur';
    }

    formatStatus(status) {
        return status === 'active' ? 'Actif' : 'Inactif';
    }

    populateEditForm(user) {
        document.getElementById('editUserId').value = user.id;
        document.getElementById('editName').value = user.name;
        document.getElementById('editEmail').value = user.email;
        document.getElementById('editPhone').value = user.phone || '';
        document.getElementById('editRole').value = user.role;
        document.getElementById('editStatus').value = user.status;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    openModal(id) {
        const modal = new bootstrap.Modal(document.getElementById(id));
        modal.show();
    }

    closeModal(id) {
        const modal = bootstrap.Modal.getInstance(document.getElementById(id));
        if (modal) modal.hide();
    }
}

document.addEventListener('DOMContentLoaded', () => new ListController());