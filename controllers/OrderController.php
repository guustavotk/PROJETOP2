<?php
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../core/core.php';

class OrderController {
    private $orderModel;

    public function __construct() {
        $this->orderModel = new Order();
    }

    //  Retorna o ID do cliente a partir do ID do usuário logado
    public function getClientIdByUser($user_id) {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT id FROM clients WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        return $client['id'] ?? null;
    }

    //  Cria um novo pedido
    public function create($client_id, $total, $payment_method, $address, $items) {
        return $this->orderModel->create($client_id, $total, $payment_method, $address, $items);
    }



    //  Retorna todos os pedidos (modo admin)
    public function getAll() {
        return $this->orderModel->getAll();
    }
  //  Busca pedido + itens (usado pela view alterarPedidos.php)
    public function getOrderById($id) {
        if (!$id) return null;
        $order = $this->orderModel->getById($id);
        $items = $this->orderModel->getItems($id);

        if (!$order) return null;
        return ['order' => $order, 'items' => $items];
    }
    //  Retorna pedidos de um usuário específico (modo cliente)
    public function getOrdersByUser($user_id) {
        return $this->orderModel->getOrdersByUser($user_id);
    }

    //  Retorna os itens de um pedido
    public function getOrderItems($order_id) {
        return $this->orderModel->getOrderItems($order_id);
    }

    //  Atualiza pedido (usado pelo endpoint update_order)
    public function updateOrder($data) {
        return $this->orderModel->update($data);
    }

    
}
