<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

// Permite JSON no corpo (para POST via fetch)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST)) {
    $input = file_get_contents("php://input");
    if (!empty($input)) {
        $jsonData = json_decode($input, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
            $_POST = $jsonData;
        }
    }
}

// Caminho base
define('APP_ROOT', dirname(__DIR__));

// Imports principais
require_once APP_ROOT . '/core/core.php';
require_once APP_ROOT . '/models/Product.php';
require_once APP_ROOT . '/models/Category.php';
require_once APP_ROOT . '/models/Order.php';
require_once APP_ROOT . '/models/User.php';
require_once APP_ROOT . '/controllers/OrderController.php';
require_once APP_ROOT . '/controllers/ProductController.php';
require_once APP_ROOT . '/controllers/UserController.php';
require_once APP_ROOT . '/controllers/AuthController.php';

$endpoint = $_GET['endpoint'] ?? '';

try {
    $productController = new ProductController();
    $orderController   = new OrderController();
    $userController    = new UserController();
    $authController    = new AuthController();

    switch ($endpoint) {
        case 'products':
            require APP_ROOT . '/views/json/products.php';
            break;

        case 'categories':
            require APP_ROOT . '/views/json/categories.php';
            break;

        case 'user':
            require APP_ROOT . '/views/json/user.php';
            break;

        case 'getDashboard':
            require APP_ROOT . '/views/json/dashboard.php';
            break;

        // 🔹 Todos os endpoints de pedidos passam por order.php
        case 'create_order':
        case 'update_order':
               require APP_ROOT . '/views/json/order.php';
        break;
        case 'orders':
        case 'get_orders':
        case 'get_order_items':
            require APP_ROOT . '/views/json/order.php';
            break;

        case 'create_product':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $productController->createProduct();
            } else {
                Response::error("Método não permitido", 405);
            }
            break;

        case 'update_product':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $productController->updateProduct();
            } else {
                Response::error("Método não permitido", 405);
            }
            break;

        default:
            http_response_code(404);
            echo json_encode([
                'success'  => false,
                'message'  => 'Endpoint não encontrado.',
                'endpoint' => $endpoint
            ]);
    }

} catch (Throwable $e) {
    logError("[API] " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno na API.',
        'error'   => $e->getMessage()
    ]);
}
