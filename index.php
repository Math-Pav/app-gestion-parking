<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/backend/controllers/LoginController.php';
require_once __DIR__ . '/backend/controllers/RegisterController.php';
require_once __DIR__ . '/backend/controllers/UserController.php';
require_once __DIR__ . '/backend/controllers/ReservationController.php';


define('BASE_PATH', '/projet_parking');

$staticExtensions = ['css', 'js'];
$extension = pathinfo($_SERVER['REQUEST_URI'], PATHINFO_EXTENSION);
if (in_array($extension, $staticExtensions)) {
    $filePath = __DIR__ . str_replace(BASE_PATH, '', $_SERVER['REQUEST_URI']);
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
        'js' => ['frontend/controllers/DashboardController.js']
    ],
    '/register' => [
        'view' => 'frontend/views/register.html',
        'auth' => false,
        'js' => ['frontend/controllers/RegisterController.js']
    ],
    '/reservation' => [
        'view' => 'frontend/views/reservation.html',
        'auth' => true,
        'js' => ['frontend/controllers/ReservationController.js']
    ],
    '/next-paiement' => [
        'view' => 'frontend/views/next-paiement.html',
        'auth' => true,
        'js' => ['frontend/controllers/NextPaiementController.js']
    ],
    '/paiement' => [
        'view' => 'frontend/views/paiement.html',
        'auth' => true,
        'js' => ['frontend/controllers/PaiementController.js']
    ],
    '/notifications' => [
        'view' => 'frontend/views/notifications.html',
        'auth' => true,
        'js' => ['frontend/controllers/NotificationsController.js']
    ],
    '/profile' => [
        'view' => 'frontend/views/profile.html',
        'auth' => true,
        'js' => ['frontend/controllers/ProfileController.js']
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
        exit;
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

    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Parking App</title>
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
    </body>
    </html>
    <?php
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Page non trouvée";
}