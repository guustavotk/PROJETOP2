<?php
session_start();


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../core/core.php';
$pdo = Database::connect();
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;


$stmt = $pdo->prepare("
    SELECT o.*, c.phone, c.address_c, u.fullname AS client_name
    FROM orders o
    LEFT JOIN clients c ON c.id = o.client_id
    LEFT JOIN users u ON u.id = c.user_id
    WHERE o.id = ?
");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("<h2 style='padding:100px;text-align:center;'>Pedido não encontrado.</h2>");
}


$stmtItems = $pdo->prepare("
    SELECT oi.*, p.name AS product_name
    FROM order_items oi
    LEFT JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = ?
");
$stmtItems->execute([$orderId]);
$items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Alterar Pedido - Painel Administrativo</title>
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
main { max-width: 700px; margin: 0 auto; padding: 100px 20px 120px; }
.form-container {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  padding: 25px;
}
.nav-buttons {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}
.nav-buttons a {
  color: var(--accent);
  font-weight: bold;
  text-decoration: none;
}
.nav-buttons a:hover { text-decoration: underline; }


table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}
th, td {
  padding: 8px;
  border-bottom: 1px solid #eee;
  text-align: left;
  font-size: 0.9rem;
  vertical-align: middle;
}
th {
  background: #f9f9f9;
  font-weight: 600;
}
.qty-input {
  width: 60px;
  text-align: center;
  border: 1px solid #ccc;
  border-radius: 6px;
  padding: 6px;
}
.btn-remove {
  background: #f44336;
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 6px 10px;
  cursor: pointer;
  font-size: 0.9rem;
}
.btn-remove:hover { background: #d32f2f; }


@media (max-width: 650px) {
  table, thead, tbody, th, td, tr {
    display: block;
    width: 100%;
  }

  thead { display: none; }

  tbody tr {
    background: #fff;
    margin-bottom: 14px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    padding: 10px 12px;
  }

  tbody td {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    border: none;
    font-size: 0.95rem;
  }

  tbody td::before {
    content: attr(data-label);
    font-weight: 600;
    color: #333;
    margin-right: 10px;
    flex-shrink: 0;
  }

  .qty-input {
    width: 70px;
    padding: 6px;
  }

  .btn-remove {
    width: 100%;
    margin-top: 6px;
    padding: 10px;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 8px;
  }

  .total {
    text-align: center;
    margin-top: 20px;
    font-size: 1.2rem;
  }
}

.total {
  text-align: right;
  font-size: 1.1rem;
  margin-top: 10px;
  font-weight: bold;
}

.btn-submit {
  background: #e53935;
  color: #fff;
  font-weight: 600;
  border: none;
  border-radius: 8px;
  padding: 12px;
  cursor: pointer;
  font-size: 1rem;
  width: 100%;
}
.btn-submit:hover { background: #c62828; }

.alert {
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 15px;
  text-align: center;
}
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.hidden { display: none; }

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.6);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}
.modal {
  background: #fff;
  border-radius: 12px;
  padding: 25px;
  text-align: center;
  max-width: 360px;
  width: 90%;
  box-shadow: 0 4px 16px rgba(0,0,0,0.2);
}
.modal h3 {
  color: #4CAF50;
  margin-bottom: 10px;
}
.modal .btns {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
}
.modal button {
  flex: 1;
  margin: 0 5px;
  padding: 10px 0;
  font-weight: 600;
  border-radius: 8px;
  cursor: pointer;
  border: none;
}
.btn-voltar { background: #e53935; color: #fff; }
.btn-continuar { background: #4CAF50; color: #fff; }
.btn-voltar:hover { background: #c62828; }
.btn-continuar:hover { background: #388E3C; }
</style>
</head>
<body>

<?php include 'header.php'; ?>

<main>
  <div class="nav-buttons">
    <a href="alterarPedidos.php?id=<?= max($orderId - 1, 1) ?>"><i class="bi bi-arrow-left-circle"></i> Anterior</a>
    <a href="alterarPedidos.php?id=<?= $orderId + 1 ?>">Próximo <i class="bi bi-arrow-right-circle"></i></a>
  </div>

  <div class="form-container">
    <h1 class="form-title"><i class="bi bi-receipt"></i> Editar Pedido #<?= $orderId ?></h1>

    <div id="alertBox" class="hidden"></div>

    <form id="editOrderForm">
      <input type="hidden" name="id" value="<?= $order['id'] ?>">

      <div class="form-group">
        <label><strong>Cliente:</strong></label>
        <p><?= htmlspecialchars($order['client_name'] ?? 'Não informado') ?></p>
      </div>

      <div class="form-group">
        <label for="address">Endereço de Entrega</label>
        <input type="text" id="address" name="address" value="<?= htmlspecialchars($order['address_c'] ?? $order['address']) ?>" required>
      </div>

      <div class="form-group">
        <label for="payment_method">Forma de Pagamento</label>
        <input type="text" id="payment_method" name="payment_method" value="<?= htmlspecialchars($order['payment_method']) ?>" required>
      </div>

      <h3 style="margin-top:20px;">Itens do Pedido</h3>
      <table id="itemsTable">
        <thead>
          <tr><th>Produto</th><th>Qtd</th><th>Preço</th><th>Subtotal</th><th></th></tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
          <tr data-id="<?= $item['id'] ?>">
            <td data-label="Produto"><?= htmlspecialchars($item['product_name']) ?></td>
            <td data-label="Qtd"><input type="number" min="1" class="qty-input" value="<?= $item['qty'] ?>"></td>
            <td data-label="Preço" class="price" data-value="<?= $item['price'] ?>">R$ <?= number_format($item['price'], 2, ',', '.') ?></td>
            <td data-label="Subtotal" class="subtotal">R$ <?= number_format($item['qty'] * $item['price'], 2, ',', '.') ?></td>
            <td data-label="Remover"><button type="button" class="btn-remove">Remover</button></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div class="total">Total: R$ <span id="totalValue">0,00</span></div>

      <button type="submit" class="btn-submit"><i class="bi bi-check-lg"></i> Salvar Alterações</button>
    </form>
  </div>
</main>


<div class="modal-overlay" id="modalSucesso">
  <div class="modal">
    <i class="bi bi-check-circle" style="font-size:3rem;color:#4CAF50;"></i>
    <h3>Pedido atualizado com sucesso!</h3>
    <p>As informações foram salvas corretamente.</p>
    <div class="btns">
      <button class="btn-continuar" onclick="fecharModal()">Continuar alterando</button>
      <button class="btn-voltar" onclick="window.location.href='painel.php'">Voltar ao Painel</button>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<script>

function atualizarTotal() {
  let total = 0;
  document.querySelectorAll('#itemsTable tbody tr').forEach(tr => {
    const qty = parseFloat(tr.querySelector('.qty-input')?.value || 0);
    const price = parseFloat(tr.querySelector('.price')?.dataset.value || 0);
    const subtotal = qty * price;
    tr.querySelector('.subtotal').textContent = "R$ " + subtotal.toFixed(2).replace('.', ',');
    total += subtotal;
  });
  document.getElementById('totalValue').textContent = total.toFixed(2).replace('.', ',');
}
atualizarTotal();


document.querySelectorAll('.qty-input').forEach(input => {
  input.addEventListener('input', atualizarTotal);
});


document.querySelectorAll('.btn-remove').forEach(btn => {
  btn.addEventListener('click', e => {
    e.target.closest('tr').remove();
    atualizarTotal();
  });
});


document.getElementById('editOrderForm').addEventListener('submit', async e => {
  e.preventDefault();
  const formData = new FormData(e.target);


  const items = [];
  document.querySelectorAll('#itemsTable tbody tr').forEach(tr => {
    items.push({
      id: tr.dataset.id,
      qty: tr.querySelector('.qty-input').value
    });
  });
  formData.append('items', JSON.stringify(items));

  const alertBox = document.getElementById('alertBox');
  alertBox.className = 'alert';
  alertBox.textContent = 'Salvando...';
  alertBox.style.display = 'block';

  try {
   fetch('api.php?endpoint=update_order', {
  method: 'POST',
  body: formData,
  credentials: 'include'
});
    const data = await res.json();
    if (data.success) {
      alertBox.className = 'alert alert-success';
      alertBox.innerHTML = `<i class="bi bi-check-circle"></i> Pedido atualizado com sucesso!`;
      document.getElementById('modalSucesso').style.display = 'flex';
    } else throw new Error(data.message);
  } catch (err) {
    alertBox.className = 'alert alert-error';
    alertBox.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${err.message}`;
  }
});

function fecharModal() { document.getElementById('modalSucesso').style.display = 'none'; }
</script>
</body>
</html>
