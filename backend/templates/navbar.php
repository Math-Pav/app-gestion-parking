<?php
$userRole = $_SESSION['user']['role'] ?? '';
$currentPage = trim($_SERVER['REQUEST_URI'], '/');
$currentPage = str_replace('app-gestion-parking/', '', $currentPage);
?>

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