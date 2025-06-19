<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/backend/controllers/LoginController.php';
require_once __DIR__ . '/backend/controllers/RegisterController.php';
require_once __DIR__ . '/backend/controllers/UserController.php';
require_once __DIR__ . '/backend/controllers/ReservationController.php';
require_once __DIR__ . '/backend/controllers/NotificationController.php';
require_once __DIR__ . '/backend/controllers/ListController.php';
require_once __DIR__ . '/backend/controllers/ListReservationController.php';
require_once __DIR__ . '/backend/controllers/DashboardAdminController.php';
require_once __DIR__ . '/backend/controllers/NextPaiementController.php';

define('BASE_PATH', '/app-gestion-parking');

$staticExtensions = ['css', 'js'];
$requestUri = str_replace(BASE_PATH, '', $_SERVER['REQUEST_URI']);
$extension = pathinfo($requestUri, PATHINFO_EXTENSION);
if (in_array($extension, $staticExtensions)) {
    $filePath = __DIR__ . $requestUri;
    if (file_exists($filePath)) {
        $contentType = $extension === 'css' ? 'text/css' : 'application/javascript';
        header("Content-Type: $contentType");
        readfile($filePath);
        exit;
    }
}

$routes = [
    '/' => [
        'view' => 'frontend/views/login.html',
        'auth' => false,
        'js' => ['frontend/controllers/UserLoginController.js']
    ],
    '/dashboard' => [
        'view' => 'frontend/views/dashboard.html',
        'auth' => true,
        'js' => ['frontend/controllers/DashboardController.js'],
        'roles' => ['user']
    ],
    '/register' => [
        'view' => 'frontend/views/register.html',
        'auth' => false,
        'js' => ['frontend/controllers/RegisterController.js']
    ],
    '/list-reservation' => [
        'view' => 'frontend/views/list-reservation.html',
        'auth' => true,
        'js' => ['frontend/controllers/ListReservationController.js'],
        'roles' => ['admin', 'user']
    ],
    '/reservation' => [
        'view' => 'frontend/views/reservation.html',
        'auth' => true,
        'js' => ['frontend/controllers/ReservationController.js'],
        'roles' => ['user']
    ],
    '/list' => [
        'view' => 'frontend/views/list.html',
        'auth' => true,
        'js' => ['frontend/controllers/ListController.js'],
        'roles' => ['admin', 'user']
    ],
    '/paiement' => [
        'view' => 'frontend/views/paiement.html',
        'auth' => true,
        'js' => ['frontend/controllers/PaiementController.js', 'frontend/controllers/NextPaiementController.js'],
        'roles' => ['user']
    ],
    '/notifications' => [
        'view' => 'frontend/views/notifications.html',
        'auth' => true,
        'js' => ['frontend/controllers/NotificationsController.js'],
        'roles' => ['user']
    ],
    '/profile' => [
        'view' => 'frontend/views/profile.html',
        'auth' => true,
        'js' => ['frontend/controllers/ProfileController.js'],
        'roles' => ['user','admin']
    ],
    '/mes-reservations' => [
        'view' => 'frontend/views/my-reservation.html',
        'auth' => true,
        'js' => ['frontend/controllers/MyReservationController.js'],
        'roles' => ['user']
    ],
    '/dashboard-admin' => [
        'view' => 'frontend/views/dashboard-admin.html',
        'auth' => true,
        'js' => ['frontend/controllers/DashboardAdminController.js'],
        'roles' => ['admin']
    ],
    '/next-paiement' => [
        'view' => 'frontend/views/next-paiement.html',
        'auth' => true,
        'js' => ['frontend/controllers/NextPaiementController.js'],
        'roles' => ['user']
    ],

];

$request_uri = $_SERVER['REQUEST_URI'];
$request_uri = str_replace(BASE_PATH, '', $request_uri);
$path = parse_url($request_uri, PHP_URL_PATH);
$path = rtrim($path, '/');

if ($path === '') {
    $path = '/';
}

if (strpos($path, '/api/') === 0) {
    if ($path === '/api/login') {
        $controller = new LoginController();
        $controller->handleLogin();
    } elseif ($path === '/api/logout') {
        session_destroy();
        echo json_encode(['success' => true]);
        exit;
    } elseif ($path === '/api/register') {
        $controller = new RegisterController();
        $controller->handleRegister();
    }  elseif ($path === '/api/profile') {
        $controller = new UserController();
        $controller->getProfile();
    }  elseif ($path === '/api/reservations/create') {
        $controller = new ReservationController();
        $data = json_decode(file_get_contents('php://input'), true);
        $controller->createReservation();
    }  elseif ($path === '/api/reservations/available-spots') {
        $controller = new ReservationController();
        $controller->getAvailableSpots();
    }  elseif ($path === '/api/reservations/update-status') {
        $controller = new ReservationController();
        $controller->updateReservationStatus();
    }  elseif ($path === '/api/reservation/latest' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $reservationController = new ReservationController();
        $reservationController->getLatestReservation();
    }  elseif ($path === '/api/reservations/user-reservations') {
        $controller = new ReservationController();
        $controller->getUserReservations();
    } elseif ($path === '/api/notifications') {
        $controller = new NotificationController();
        $controller->getNotifications();
    } elseif ($path === '/api/notifications/mark-all-read') {
        $controller = new NotificationController();
        $controller->markAllAsRead();
    } elseif ($path === '/api/notifications/mark-as-read' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $notificationController = new NotificationController();
        $notificationController->markAsRead();
    } elseif ($path === '/api/reservations/list') {
        $controller = new ListReservationController();
        $controller->getUserReservations();
    } elseif ($path === '/api/reservations/cancel') {
        $controller = new ListReservationController();
        $controller->cancelReservation();
    } elseif ($path === '/api/users/list') {
        $controller = new ListController();
        $controller->getUsers();
    } elseif ($path === '/api/users/add') {
        $controller = new ListController();
        $controller->addUser();
    } elseif ($path === '/api/users/update') {
        $controller = new ListController();
        $controller->updateUser();
    } elseif ($path === '/api/users/delete') {
        $controller = new ListController();
        $controller->deleteUser();
    } elseif ($path === '/api/users/get-user') {
        $controller = new ListController();
        $controller->getUserById();
    } elseif ($path === '/api/admin/stats') {
        $controller = new DashboardAdminController();
        $controller->getStats();
    } elseif ($path === '/api/admin/chart-data') {
        $controller = new DashboardAdminController();
        $controller->getChartData();
    } elseif ($path === '/api/payment/process') {
        $controller = new NextPaiementController();
        $controller->handlePayment();
    } elseif ($path === '/api/reservations/get-reservation') {
        $controller = new ReservationController();
        $controller->getReservationById();
    }  else {
        header("HTTP/1.0 404 Not Found");
        echo json_encode(['error' => 'API endpoint non trouvé']);
    }
    exit;
}

if (isset($routes[$path])) {
    if ($routes[$path]['auth'] && !isset($_SESSION['user'])) {
        header('Location: ' . BASE_PATH);
        exit;
    }

    if (isset($routes[$path]['roles']) && isset($_SESSION['user'])) {
        if (!in_array($_SESSION['user']['role'], $routes[$path]['roles'])) {
            $redirectPath = $_SESSION['user']['role'] === 'admin'
                ? '/dashboard-admin'
                : '/dashboard';
            header('Location: ' . BASE_PATH . $redirectPath);
            exit;
        }
    }

    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Parking App</title>
        <style></style>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    <?php
    if ($path !== '/' && $path !== '/register') {
        $page = trim($path, '/');
        include __DIR__ . '/backend/templates/navbar.php';
    }

    include __DIR__ . '/' . $routes[$path]['view'];
    ?>

    <?php foreach ($routes[$path]['js'] as $script): ?>
        <script src="<?php echo BASE_PATH; ?>/<?php echo $script; ?>"></script>
    <?php endforeach; ?>

    <?php if ($path !== '/'): ?>
        <script src="<?php echo BASE_PATH; ?>/frontend/assets/js/navbar.js" defer></script>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Page non trouvée";
}