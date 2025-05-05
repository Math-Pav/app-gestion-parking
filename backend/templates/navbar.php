<!-- backend/templates/navbar.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo BASE_PATH; ?>/dashboard">Parking App</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page === 'dashboard') ? 'active' : ''; ?>"
                       href="<?php echo BASE_PATH; ?>/dashboard">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page === 'reservations') ? 'active' : ''; ?>"
                       href="<?php echo BASE_PATH; ?>/reservations">Réservations</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page === 'profile') ? 'active' : ''; ?>"
                       href="<?php echo BASE_PATH; ?>/profile">Profil</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#" id="logoutBtn">Déconnexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>