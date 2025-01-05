<?php
// connecting config.php
$config = require_once __DIR__ . '/../config/config.php';

// config variables
$projectRoot = $config['project_root'];
$databasePath =  $config['database_path'];

// Connecting the controllers
require_once $projectRoot . '/src/Controllers/ProductController.php';

use App\Controllers\ProductController;

// Connecting to DB:
try {
    $pdo = new PDO('sqlite:' . $databasePath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to connect to the database: '
            . $e->getMessage()
    ]);
    exit;
}

// Creating product controller
$productController = new ProductController($pdo);

// Routes array
$routes = [
    'GET' => [
        'products' => [$productController, 'getAllProducts'],
    ],
    'POST' => [
        'products' => [$productController, 'createProduct'],
    ],
    'PUT' => [
        'products/(\d+)' => [$productController, 'updateProduct'],
    ],
    'DELETE' => [
        'products/(\d+)' => [$productController, 'deleteProduct'],
    ],
];

// Server variables
$method = $_SERVER['REQUEST_METHOD'];
$uri = trim($_SERVER['REQUEST_URI'], '/');

// Checking if route exists
$routeFound = false;

foreach ($routes[$method] ?? [] as $routePattern => $handler) {
    if (preg_match("#^{$routePattern}$#", $uri, $matches)) {
        $routeFound = true;
        array_shift($matches);
        call_user_func_array($handler, $matches);
        break;
    }
}

if (!$routeFound) {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found.']);
}
