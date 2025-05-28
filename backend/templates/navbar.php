<?php
$name = isset($_SESSION['user']['name']) && !empty($_SESSION['user']['name'])
    ? htmlspecialchars($_SESSION['user']['name'])
    : 'Utilisateur';
?>
<nav class="navbar navbar-expand-lg custom-navbar mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">
            <i class="bi bi-p-circle-fill me-2"></i>Parking App
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_PATH; ?>/dashboard">
                        <i class="bi bi-speedometer2 me-1"></i>Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_PATH; ?>/reservation">
                        <i class="bi bi-calendar-plus me-1"></i>Réserver
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_PATH; ?>/notifications">
                        <i class="bi bi-bell me-1"></i>Notifications
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_PATH; ?>/mes-reservations">
                        <i class="bi bi-bookmark me-1"></i>Mes réservations
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_PATH; ?>/list">
                        <i class="bi bi-bookmark me-1"></i>Liste des utilisateurs
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <div class="avatar-circle me-2">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <span><?php echo $name; ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li>
                            <a class="dropdown-item py-2" href="<?php echo BASE_PATH; ?>/profile">
                                <i class="bi bi-person me-2"></i>Mon profil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2 text-danger" href="#" id="logoutBtn">
                                <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
    .custom-navbar {
        background-color: #ffffff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        padding: 1rem 0;
    }


    .nav-link {
        color: #495057;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .avatar-circle {
        width: 32px;
        height: 32px;
        background-color: rgba(13, 110, 253, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dropdown-menu {
        border-radius: 0.5rem;
        margin-top: 0.5rem;
    }

    .dropdown-item {
        border-radius: 0.3rem;
    }

    .dropdown-item:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }

    .dropdown-item.text-danger:hover {
        background-color: rgba(220, 53, 69, 0.05);
    }

    @media (max-width: 991.98px) {
        .navbar-collapse {
            padding: 1rem 0;
        }

        .nav-link {
            padding: 0.75rem 1rem;
        }

        .dropdown-menu {
            border: none;
            box-shadow: none !important;
            padding: 0;
        }
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">