document.addEventListener('DOMContentLoaded', function() {

    const navToggle = document.querySelector('.nav-toggle');
    if (navToggle) {
        navToggle.addEventListener('click', () => {
            document.querySelector('.nav-menu').classList.toggle('active');
        });
    }

    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            try {
                const response = await fetch('/projet_parking/api/logout');
                const data = await response.json();
                if (data.success) {
                    window.location.href = '/projet_parking/';
                }
            } catch (error) {
                console.error('Erreur lors de la déconnexion:', error);
            }
        });
    }
});