document.addEventListener('DOMContentLoaded', function() {
    const registerBtn = document.getElementById('registerBtn');
    const messageDiv = document.getElementById('message');
    const phoneInput = document.getElementById('phone');
    const emailInput = document.getElementById('email');

    phoneInput.addEventListener('input', function() {
        if (this.value.length > 10) {
            this.value = this.value.slice(0, 10);
        }
    });

    emailInput.addEventListener('input', function() {
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailRegex.test(this.value)) {
            emailInput.setCustomValidity('Format invalide. Exemple: exemple@exemple.com');
        } else {
            emailInput.setCustomValidity('');
        }
    });

    registerBtn.addEventListener('click', async function() {
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (!name || !email || !phone || !password || !confirmPassword) {
            showMessage('Tous les champs sont requis', 'danger');
            return;
        }

        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailRegex.test(email)) {
            showMessage('Format d\'email invalide. Exemple: exemple@exemple.com', 'danger');
            return;
        }

        if (phone.length !== 10 || !/^\d+$/.test(phone)) {
            showMessage('Le numéro de téléphone doit contenir exactement 10 chiffres', 'danger');
            return;
        }

        if (password !== confirmPassword) {
            showMessage('Les mots de passe ne correspondent pas', 'danger');
            return;
        }

        try {
            const response = await fetch('/app-gestion-parking/api/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name,
                    email,
                    phone,
                    password
                })
            });

            const data = await response.json();

            if (data.success) {
                showMessage(`Vous allez être redirigé vers la page de connexion dans 5 secondes...`, 'success');
                setTimeout(() => {
                    window.location.href = '/app-gestion-parking/';
                }, 5000);
            } else {
                showMessage(data.message, 'danger');
            }
        } catch (error) {
            showMessage('Erreur lors de l\'inscription', 'danger');
        }
    });

    function showMessage(message, type) {
        messageDiv.className = `alert alert-${type}`;
        messageDiv.style.display = 'block';
        messageDiv.textContent = message;
    }
});