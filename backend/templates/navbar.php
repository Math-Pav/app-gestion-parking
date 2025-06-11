<?php
$userRole = $_SESSION['user']['role'] ?? '';
$currentPage = trim($_SERVER['REQUEST_URI'], '/');
$currentPage = str_replace('app-gestion-parking/', '', $currentPage);
?>

<style>
    .custom-navbar {
        background: #ffffff;
        padding: 0.8rem 0;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.04);
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .custom-navbar .navbar-brand {
        color: #2563eb;
        font-weight: 700;
        font-size: 1.3rem;
        letter-spacing: -0.5px;
        padding: 0.5rem 1rem;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .custom-navbar .navbar-brand:hover {
        background: rgba(37, 99, 235, 0.08);
        color: #1e40af;
    }

    .custom-navbar .nav-link {
        color: #64748b;
        padding: 0.7rem 1rem;
        margin: 0 0.2rem;
        border-radius: 12px;
        font-weight: 500;
        transition: all 0.2s ease;
        position: relative;
    }

    .custom-navbar .nav-link:hover {
        color: #2563eb;
        background: rgba(37, 99, 235, 0.08);
    }

    .custom-navbar .nav-link.active {
        color: #2563eb;
        background: rgba(37, 99, 235, 0.12);
    }

    .custom-navbar .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: #2563eb;
    }

    .navbar-toggler {
        border: none;
        padding: 0.5rem;
        border-radius: 10px;
        background: rgba(37, 99, 235, 0.08);
    }

    .navbar-toggler:focus {
        box-shadow: none;
        outline: none;
    }

    .notification-badge {
        background: #ef4444;
        color: white;
        font-size: 0.65rem;
        padding: 0.2rem 0.45rem;
        border-radius: 20px;
        position: absolute;
        top: 2px;
        right: 2px;
        border: 2px solid #ffffff;
    }

    .dropdown-menu {
        padding: 0.5rem;
        border: none;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border-radius: 16px;
        min-width: 220px;
    }

    .dropdown-item {
        color: #64748b;
        padding: 0.8rem 1rem;
        border-radius: 10px;
        font-weight: 500;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        color: #2563eb;
        background: rgba(37, 99, 235, 0.08);
    }

    .dropdown-item i {
        margin-right: 0.8rem;
        font-size: 1.1rem;
        color: #94a3b8;
    }

    .dropdown-item:hover i {
        color: #2563eb;
    }

    @media (max-width: 991.98px) {
        .navbar-collapse {
            background: #ffffff;
            padding: 1rem;
            border-radius: 16px;
            margin-top: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .custom-navbar .nav-link.active::after {
            display: none;
        }
    }
</style>

<nav class="navbar navbar-expand-lg custom-navbar">
    <div class="container">
        <a class="navbar-brand" href="<?php echo BASE_PATH; ?>/dashboard">
            <i class="bi bi-p-circle-fill me-2"></i>Parking App
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>"
                       href="<?php echo BASE_PATH; ?>/dashboard">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                </li>

                <?php if ($userRole === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'list' ? 'active' : ''; ?>"
                           href="<?php echo BASE_PATH; ?>/list">
                            <i class="bi bi-people me-2"></i>Utilisateurs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'list-reservation' ? 'active' : ''; ?>"
                           href="<?php echo BASE_PATH; ?>/list-reservation">
                            <i class="bi bi-calendar-check me-2"></i>Réservations
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($userRole === 'user'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'reservation' ? 'active' : ''; ?>"
                           href="<?php echo BASE_PATH; ?>/reservation">
                            <i class="bi bi-calendar-plus me-2"></i>Réserver
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'mes-reservations' ? 'active' : ''; ?>"
                           href="<?php echo BASE_PATH; ?>/mes-reservations">
                            <i class="bi bi-calendar-event me-2"></i>Mes réservations
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav">
                <?php if ($userRole === 'user'): ?>
                    <li class="nav-item">
                        <a class="nav-link position-relative <?php echo $currentPage === 'notifications' ? 'active' : ''; ?>"
                           href="<?php echo BASE_PATH; ?>/notifications">
                            <i class="bi bi-bell me-2"></i>Notifications
                            <span class="notification-badge" id="notificationBadge"></span>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-2"></i><?php echo $_SESSION['user']['name'] ?? 'Mon compte'; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="<?php echo BASE_PATH; ?>/profile">
                                <i class="bi bi-person me-2"></i>Mon profil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" id="logoutBtn">
                                <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">