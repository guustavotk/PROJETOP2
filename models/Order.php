<?php
require_once __DIR__ . '/../core/core.php';

class Order {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    // 🔹 Criar pedido
    public function create($client_id, $total, $payment_method, $address, $items) {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("
                INSERT INTO orders (client_id, total, payment_method, address, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$client_id, $total, $payment_method, $address]);
            $order_id = $this->pdo->lastInsertId();

            $itemStmt = $this->pdo->prepare("
                INSERT INTO order_items (order_id, product_id, qty, price)
                VALUES (?, ?, ?, ?)
            ");

            foreach ($items as $item) {
                $productId = $item['id'] ?? null;
                $qty       = $item['qtd'] ?? 1;
                $price     = isset($item['price_promo']) && $item['price_promo'] > 0
                    ? $item['price_promo']
                    : ($item['price'] ?? 0);

                if (!$productId || $price <= 0) {
                    throw new Exception("Item inválido no pedido");
                }

                $itemStmt->execute([$order_id, $productId, $qty, $price]);
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Erro ao criar pedido: " . $e->getMessage());
            return false;
        }
    }

    // 🔹 Atualizar pedido
    public function update($id, $address, $payment_method, $items) {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("UPDATE orders SET address = ?, payment_method = ? WHERE id = ?");
            $stmt->execute([$address, $payment_method, $id]);

            // Atualizar ou remover itens
            $idsMantidos = [];
            foreach ($items as $it) {
                $itemId = (int)($it['id'] ?? 0);
                $qty = max(1, (int)($it['qty'] ?? 1));
                if ($itemId > 0) {
                    $idsMantidos[] = $itemId;
                    $stmt = $this->pdo->prepare("UPDATE order_items SET qty = ? WHERE id = ? AND order_id = ?");
                    $stmt->execute([$qty, $itemId, $id]);
                }
            }

            if (!empty($idsMantidos)) {
                $in = implode(',', array_map('intval', $idsMantidos));
                $this->pdo->exec("DELETE FROM order_items WHERE order_id = $id AND id NOT IN ($in)");
            } else {
                $this->pdo->exec("DELETE FROM order_items WHERE order_id = $id");
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Erro ao atualizar pedido: " . $e->getMessage());
            return false;
        }
    }

    // 🔹 Listar todos os pedidos (modo admin)
    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT o.id, c.name AS cliente, o.total, o.payment_method, o.address, o.created_at
            FROM orders o
            LEFT JOIN clients c ON o.client_id = c.id
            ORDER BY o.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Listar pedidos de um usuário específico (modo cliente)
    public function getOrdersByUser($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT o.id, o.total, o.payment_method, o.address, o.created_at
            FROM orders o
            INNER JOIN clients c ON o.client_id = c.id
            WHERE c.user_id = ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Retornar os itens de um pedido
    public function getOrderItems($order_id) {
        $stmt = $this->pdo->prepare("
            SELECT oi.id, p.name, oi.qty, oi.price
            FROM order_items oi
            INNER JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
