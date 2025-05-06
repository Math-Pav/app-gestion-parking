class UserLoginController {
    constructor() {
        this.elements = {
            email: document.getElementById('email'),
            password: document.getElementById('password'),
            loginBtn: document.getElementById('loginBtn'),
            message: document.getElementById('message')
        };

        this.api = {
            loginUrl: '/projet_parking/api/login'
        };

        this.bindEvents();
    }

    bindEvents() {
        this.elements.loginBtn.addEventListener('click', () => this.login());
    }

    validateForm() {
        const { email, password } = this.getFormData();
        if (!email || !password) {
            this.displayMessage('Veuillez remplir tous les champs', 'error');
            return false;
        }
        return true;
    }

    getFormData() {
        return {
            email: this.elements.email.value.trim(),
            password: this.elements.password.value.trim()
        };
    }

    async login() {
        if (!this.validateForm()) return;

        try {
            const response = await this.sendLoginRequest();
            this.handleLoginResponse(response);
        } catch (error) {
            this.displayMessage('Erreur de connexion au serveur', 'error');
        }
    }

    async sendLoginRequest() {
        const response = await fetch(this.api.loginUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(this.getFormData())
        });
        return await response.json();
    }

    handleLoginResponse(data) {
        if (data.success) {
            this.displayMessage('Connexion réussie', 'success');
            window.location.href = '/projet_parking/dashboard';
        } else {
            this.displayMessage(data.message, 'error');
        }
    }

    displayMessage(text, type) {
        this.elements.message.textContent = text;
        this.elements.message.className = `message ${type}`;
    }
}

new UserLoginController();