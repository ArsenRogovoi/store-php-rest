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

// Server variables
$method = $_SERVER['REQUEST_METHOD'];
$uri = trim($_SERVER['REQUEST_URI'], '/');

if ($method === 'GET' && $uri === 'products') {
    $productController->getAllProducts();
} elseif ($method === 'POST' && $uri === 'products') {
    $data = json_decode(file_get_contents('php://input'), true);
    $productController->createProduct($data);
} elseif ($method === 'PUT' && preg_match('/products\/(\d+)/', $uri, $matches)) {
    $id = (int)$matches[1];
    $data = json_decode(file_get_contents('php://input'), true);
    $productController->updateProduct($id, $data);
} elseif ($method === 'DELETE' && preg_match('/products\/(\d+)/', $uri, $matches)) {
    $id = (int)$matches[1];
    $productController->deleteProduct($id);
} else {
    http_response_code(404);
    echo "Route not found. $uri";
}
