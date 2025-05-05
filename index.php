<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/backend/controllers/UserController.php';

// Définir le chemin de base
define('BASE_PATH', '/projet_parking');

// Gestion des fichiers statiques
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
    ]
];

// Traitement de l'URL
$request_uri = $_SERVER['REQUEST_URI'];
$request_uri = str_replace(BASE_PATH, '', $request_uri);
$path = parse_url($request_uri, PHP_URL_PATH);
$path = rtrim($path, '/');

if ($path === '') {
    $path = '/';
}

// Gestion des routes API
if (strpos($path, '/api/') === 0) {
    if ($path === '/api/login') {
        $controller = new UserController();
        $controller->handleLogin();
    } else {
        header("HTTP/1.0 404 Not Found");
        echo json_encode(['error' => 'API endpoint non trouvé']);
    }
    exit;
}

// Gestion des routes normales
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
        <title>Mon Application</title>
        <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/frontend/assets/css/style.css">
    </head>
    <body>
    <?php include __DIR__ . '/' . $routes[$path]['view']; ?>
    <?php foreach ($routes[$path]['js'] as $script): ?>
        <script src="<?php echo BASE_PATH; ?>/<?php echo $script; ?>"></script>
    <?php endforeach; ?>
    </body>
    </html>
    <?php
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Page non trouvée";
}