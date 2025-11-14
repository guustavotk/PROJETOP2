<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/OrderController.php';
require_once __DIR__ . '/../../core/ErrorHandler.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $orderCtrl = new OrderController();
    $action = $_GET['endpoint'] ?? '';

    switch ($action) {

        // 🔹 CRIAR PEDIDO
        case 'create_order':
            if (empty($_SESSION['user']['id'])) {
                throw new Exception("Usuário não autenticado.");
            }

            $client_id = $orderCtrl->getClientIdByUser($_SESSION['user']['id']);
            if (!$client_id) {
                throw new Exception("Cliente não encontrado.");
            }

            $total          = (float)($_POST['total'] ?? 0);
            $payment_method = trim($_POST['payment_method'] ?? '');
            $address        = trim($_POST['address'] ?? '');
            $items          = $_POST['items'] ?? [];

            if ($total <= 0 || empty($items) || !is_array($items)) {
                throw new Exception("Pedido inválido.");
            }

            $ok = $orderCtrl->create($client_id, $total, $payment_method, $address, $items);

            echo json_encode([
                'success' => $ok,
                'message' => $ok ? 'Pedido criado com sucesso!' : 'Erro ao criar pedido.'
            ]);
            break;

        // 🔹 LISTAR PEDIDOS DO CLIENTE LOGADO
        case 'get_orders':
            if (empty($_SESSION['user']['id'])) {
                throw new Exception("Usuário não autenticado.");
            }

            $client_id = $orderCtrl->getClientIdByUser($_SESSION['user']['id']);
            if (!$client_id) {
                throw new Exception("Cliente não encontrado.");
            }

            $pdo = Database::connect();
            $stmt = $pdo->prepare("
                SELECT id, total, payment_method, address, created_at
                FROM orders
                WHERE client_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$client_id]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'orders' => $orders]);
            break;

        // 🔹 ITENS DE UM PEDIDO
        case 'get_order_items':
            $order_id = (int)($_GET['order_id'] ?? 0);
            if ($order_id <= 0) {
                throw new Exception("ID do pedido inválido.");
            }

            $pdo = Database::connect();
            $stmt = $pdo->prepare("
                SELECT oi.qty, oi.price, p.name AS product_name
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$order_id]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $total = array_reduce($items, fn($t, $i) => $t + ($i['qty'] * $i['price']), 0);

            echo json_encode([
                'success' => true,
                'items'   => $items,
                'total'   => $total
            ]);
            break;

        // 🔹 LISTAR TODOS OS PEDIDOS (modo admin)
        case 'orders':
            if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
                throw new Exception('Acesso negado.');
            }

            $orders = $orderCtrl->getAll();
            echo json_encode(['success' => true, 'orders' => $orders]);
            break;

        // 🔹 ATUALIZAR PEDIDO (modo admin)
        case 'update_order':
            if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
                throw new Exception('Acesso negado.');
            }

            $id             = (int)($_POST['id'] ?? 0);
            $address        = trim($_POST['address'] ?? '');
            $payment_method = trim($_POST['payment_method'] ?? '');
            $items          = json_decode($_POST['items'] ?? '[]', true);

            if ($id <= 0 || empty($items)) {
                throw new Exception('Dados inválidos para atualização.');
            }

            $ok = $orderCtrl->updateOrder([
                'id' => $id,
                'address' => $address,
                'payment_method' => $payment_method,
                'items' => $_POST['items'] // mantém JSON bruto para o model decodificar
            ]);

            echo json_encode([
                'success' => $ok['success'] ?? false,
                'message' => $ok['success'] ? 'Pedido atualizado com sucesso!' : ($ok['message'] ?? 'Erro ao atualizar pedido.')
            ]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação de pedido inválida.']);
    }

} catch (Throwable $e) {
    logError("[Order JSON] " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno no pedido.',
        'error'   => $e->getMessage()
    ]);
}
