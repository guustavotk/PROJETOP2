<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

try {
    // 🔒 Verifica autenticação e permissão
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $userCtrl = new UserController();
    $productCtrl = new ProductController();
    $pdo = Database::connect();

    // 🔹 Contagem de usuários
    $clients  = method_exists($userCtrl, 'countByRole')
        ? $userCtrl->countByRole('client')
        : $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'client'")->fetchColumn();

    $users = method_exists($userCtrl, 'countAll')
        ? $userCtrl->countAll()
        : $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    // 🔹 Contagem de produtos
    $products = method_exists($productCtrl, 'countAll')
        ? $productCtrl->countAll()
        : $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

    // 🔹 Contagem de pedidos
    $stmtOrders = $pdo->query("SELECT COUNT(*) FROM orders");
    $orders = (int)$stmtOrders->fetchColumn();

    // 🔹 Pedidos recentes
    $stmtRecent = $pdo->query("
        SELECT o.id, o.total, o.payment_method, o.created_at,
               COALESCE(u.fullname, 'Cliente não identificado') AS cliente
        FROM orders o
        LEFT JOIN clients c ON c.id = o.client_id
        LEFT JOIN users u ON u.id = c.user_id
        ORDER BY o.id DESC
        LIMIT 5
    ");
    $recentOrders = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'dashboard' => [
            'clients'       => (int)$clients,
            'products'      => (int)$products,
            'users'         => (int)$users,
            'orders'        => (int)$orders,
            'recentOrders'  => $recentOrders
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    logError("[getDashboard] " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno ao carregar o dashboard.',
        'error' => $e->getMessage()
    ]);
}
