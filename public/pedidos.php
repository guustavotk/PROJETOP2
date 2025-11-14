<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Meus Pedidos - Pastéis & Bebidas</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
main {
  max-width: 900px;
  margin: 0 auto;
  padding: 100px 20px 120px;
}
table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  font-size: 0.95rem;
}
th, td {
  padding: 12px 15px;
  text-align: left;
  white-space: nowrap;
}
th {
  background: var(--accent);
  color: #fff;
  font-weight: 600;
}
tr:nth-child(even) { background: #f9f9f9; }
tr:hover { background: #fff5f5; }
.btn-ver {
  background: var(--accent);
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 6px 10px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: background 0.2s;
}
.btn-ver:hover { background: #d32f2f; }

/* ===== RESPONSIVIDADE ===== */
@media (max-width: 768px) {
  table, thead, tbody, th, td, tr {
    display: block;
  }
  thead { display: none; }
  tr {
    background: #fff;
    margin-bottom: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    padding: 10px;
  }
  td {
    padding: 8px 10px;
    text-align: right;
    position: relative;
    border: none;
    border-bottom: 1px solid #eee;
  }
  td::before {
    content: attr(data-label);
    position: absolute;
    left: 10px;
    top: 8px;
    font-weight: 600;
    color: var(--accent);
    text-transform: capitalize;
    font-size: 0.85rem;
  }
  td:last-child { border-bottom: none; }
  .btn-ver { width: 100%; margin-top: 5px; }
}
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
  padding: 20px;
  max-width: 420px;
  width: 90%;
  box-shadow: 0 4px 16px rgba(0,0,0,0.3);
  text-align: center;
}
.modal h3 {
  margin-bottom: 10px;
  color: var(--accent);
}
.modal table {
  width: 100%;
  margin-top: 10px;
  border-collapse: collapse;
}
.modal table td {
  padding: 6px;
  border-bottom: 1px solid #eee;
  font-size: 0.9rem;
}
.close-btn {
  margin-top: 15px;
  background: #777;
  color: #fff;
  border: none;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.2s;
}
.close-btn:hover { background: #555; }
</style>
</head>

<body>
<?php include 'header.php'; ?>

<main>
  <h2 style="margin-bottom: 20px;">📦 Meus Pedidos</h2>
  <section id="pedidosContainer">
    <p style="text-align:center;color:#999;">Carregando seus pedidos...</p>
  </section>
</main>

<?php include 'footer.php'; ?>

<!-- MODAL -->
<div class="modal-overlay" id="modalPedido">
  <div class="modal">
    <h3>Detalhes do Pedido</h3>
    <p><strong>ID:</strong> <span id="modalOrderId"></span></p>
    <p><strong>Total:</strong> R$ <span id="modalOrderTotal"></span></p>
    <table id="modalOrderItems"></table>
    <button class="close-btn" onclick="fecharModal()">Fechar</button>
  </div>
</div>

<script>
async function carregarPedidos() {
  const container = document.getElementById('pedidosContainer');
  try {
    // 🔹 Mantém sessão PHP na requisição
    const res = await fetch('api.php?endpoint=get_orders', {
      credentials: 'include'
    });

    const data = await res.json();

    if (!data.success || !data.orders || data.orders.length === 0) {
      container.innerHTML = `
        <div style="text-align:center;padding:40px;">
          <i class="bi bi-inbox" style="font-size:48px;color:#ccc;"></i>
          <h3>Nenhum pedido encontrado</h3>
          <p>Assim que você fizer um pedido, ele aparecerá aqui 😋</p>
        </div>`;
      return;
    }

    let html = `
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Total</th>
            <th>Forma de Pagamento</th>
            <th>Data</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
    `;

    data.orders.forEach(p => {
      html += `
        <tr>
          <td data-label="ID">#${p.id}</td>
          <td data-label="Total">R$ ${parseFloat(p.total).toFixed(2).replace('.', ',')}</td>
          <td data-label="Pagamento">${p.payment_method}</td>
          <td data-label="Data">${new Date(p.created_at).toLocaleString('pt-BR')}</td>
          <!-- <td data-label="Ações"><button class="btn-ver" onclick="verPedido(${p.id})">Ver Pedido</button></td> -->
        </tr>
      `;
    });

    html += '</tbody></table>';
    container.innerHTML = html;

  } catch (err) {
    container.innerHTML = '<p style="color:red;">Erro ao carregar pedidos.</p>';
    console.error(err);
  }
}

async function verPedido(id) {
  const modal = document.getElementById('modalPedido');
  modal.style.display = 'flex';

  try {
const res = await fetch('api.php?endpoint=get_orders', {
  credentials: 'include'
});
    const data = await res.json();

    document.getElementById('modalOrderId').textContent = id;

    const total = parseFloat(data.total || 0);
    document.getElementById('modalOrderTotal').textContent = total.toFixed(2).replace('.', ',');

    const tbody = document.getElementById('modalOrderItems');
    if (!data.success || !data.items || data.items.length === 0) {
      tbody.innerHTML = '<tr><td colspan="2">Nenhum item encontrado.</td></tr>';
      return;
    }

    tbody.innerHTML = data.items.map(item => `
      <tr>
        <td style="text-align:left;">${item.product_name}</td>
        <td style="text-align:right;">${item.qty}x - R$ ${parseFloat(item.price).toFixed(2).replace('.', ',')}</td>
      </tr>
    `).join('');

  } catch (err) {
    console.error(err);
  }
}

function fecharModal() {
  document.getElementById('modalPedido').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', carregarPedidos);
</script>
</body>
</html>
