<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel Administrativo - Pastéis & Bebidas</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.dashboard {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 15px;
  padding: 0 15px;
}
.card {
  background: #fff;
  padding: 20px;
  text-align: center;
  border-radius: 12px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}
.card h3 { color: #e53935; font-size: 1.6rem; margin: 0; }
.card span { color: #555; font-weight: 600; }

.chart-box {
  margin: 40px auto;
  max-width: 600px;
}

table {
  width: 95%;
  margin: 20px auto;
  border-collapse: collapse;
  background: #fff;
  border-radius: 10px;
  overflow: hidden;
}
th, td {
  padding: 10px;
  text-align: center;
  border-bottom: 1px solid #eee;
}
th {
  background: #f8f8f8;
  font-weight: bold;
}
tr:hover { background: #fafafa; }

.btn-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 10px;
  margin: 20px;
}


#notificationContainer {
  position: fixed;
  top: 20px;
  right: 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  z-index: 9999;
}

.notification {
  background: #4CAF50;
  color: #fff;
  padding: 15px 20px;
  border-radius: 10px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.2);
  font-weight: 600;
  display: flex;
  justify-content: space-between;
  align-items: center;
  animation: fadeIn 0.4s ease;
}

.notification button {
  background: #fff;
  border: none;
  border-radius: 5px;
  padding: 6px 12px;
  color: #4CAF50;
  font-weight: bold;
  cursor: pointer;
}

.notification button:hover {
  background: #f1f1f1;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>
<?php include 'header.php'; ?>


<div id="notificationContainer"></div>

<main style="margin-top:30px">
  <h1 style="text-align:center;color:#e53935;"><i class="bi bi-speedometer2"></i> Painel Administrativo</h1>
  <p style="text-align:center;margin-bottom:20px;">Bem-vindo, <?= htmlspecialchars($_SESSION['user']['username']) ?>.</p>

  <section class="dashboard" id="dashboard">
    <div class="card"><h3 id="clientsCount">0</h3><span>Clientes</span></div>
    <div class="card"><h3 id="productsCount">0</h3><span>Produtos</span></div>
    <div class="card"><h3 id="usersCount">0</h3><span>Usuários</span></div>
    <div class="card"><h3 id="ordersCount">0</h3><span>Pedidos</span></div>
  </section>

  <div class="chart-box">
    <canvas id="dashboardChart"></canvas>
  </div>

  <h2 style="text-align:center;margin-top:40px;">Últimos Pedidos</h2>
  <table id="ordersTable">
    <thead>
      <tr>
        <th>ID</th>
        <th>Cliente</th>
        <th>Forma de Pagamento</th>
        <th>Total</th>
        <th>Data</th>
      </tr>
    </thead>
    <tbody>
      <tr><td colspan="5">Carregando...</td></tr>
    </tbody>
  </table>

  <h2 style="margin-top:40px;text-align:center;">Ações Rápidas</h2>
  <section class="btn-grid">
    <button class="btn-action" onclick="window.location='cadastroProduto.php'"><i class="bi bi-plus-circle"></i> Criar Produto</button>
    <button class="btn-action" onclick="window.location='cadastroCliente.php'"><i class="bi bi-person-plus"></i> Criar Usuário</button>
    <button class="btn-action" onclick="window.location='alterarProduto.php?id=1'"><i class="bi bi-box-seam"></i> Ver / Alterar Produtos</button>
    <button class="btn-action" onclick="window.location='alterarUsuario.php?id=1'"><i class="bi bi-person-gear"></i> Ver / Alterar Usuários</button>
    <!--<button class="btn-action" onclick="window.location='alterarPedidos.php?id=1'"><i class="bi bi-receipt"></i> Ver / Alterar Pedidos</button> -->
  </section>
</main>

<?php include 'footer.php'; ?>

<script>
let lastOrderCount = 0;
let chartInstance = null;
let audioLiberado = false;


document.body.addEventListener('click', () => {
  if (!audioLiberado) {
    const audio = new Audio('bell-notification-337658.mp3');
    audio.play().then(() => {
      audio.pause();
      audio.currentTime = 0;
      audioLiberado = true;
      console.log('🔊 Som liberado após interação do usuário.');
    }).catch(() => {});
  }
});

async function atualizarPainel(showNotification = false) {
  try {
    const res = await fetch('api.php?endpoint=getDashboard');
    const data = await res.json();

    if (!data.success) return;

    const { clients, products, users, orders, recentOrders } = data.dashboard;


    if (showNotification && orders > lastOrderCount) {
      const novos = orders - lastOrderCount;
      for (let i = 0; i < novos; i++) {
        exibirNotificacao(`🛒 Novo pedido recebido! (#${orders - i})`);
      }
    }
    lastOrderCount = orders;


    document.getElementById('clientsCount').textContent = clients ?? 0;
    document.getElementById('productsCount').textContent = products ?? 0;
    document.getElementById('usersCount').textContent = users ?? 0;
    document.getElementById('ordersCount').textContent = orders ?? 0;


    const ctx = document.getElementById('dashboardChart').getContext('2d');
    const chartData = {
      labels: ['Clientes', 'Produtos', 'Usuários', 'Pedidos'],
      datasets: [{
        data: [clients, products, users, orders],
        backgroundColor: ['#42a5f5','#66bb6a','#ffca28','#ef5350'],
        borderWidth: 1
      }]
    };

    if (chartInstance) {
      chartInstance.data = chartData;
      chartInstance.update();
    } else {
      chartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: chartData,
        options: {
          responsive: true,
          plugins: { legend: { position: 'bottom' } }
        }
      });
    }


    const tbody = document.querySelector('#ordersTable tbody');
    tbody.innerHTML = '';
    if (recentOrders && recentOrders.length > 0) {
      recentOrders.forEach(o => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${o.id}</td>
          <td>${o.cliente ?? 'N/A'}</td>
          <td>${o.payment_method}</td>
          <td>R$ ${parseFloat(o.total).toFixed(2).replace('.', ',')}</td>
          <td>${new Date(o.created_at).toLocaleString('pt-BR')}</td>
        `;
        tbody.appendChild(tr);
      });
    } else {
      tbody.innerHTML = '<tr><td colspan="5">Nenhum pedido encontrado.</td></tr>';
    }

  } catch (err) {
    console.error('❌ Erro ao atualizar painel:', err);
  }
}


function exibirNotificacao(texto) {
  const container = document.getElementById('notificationContainer');
  const box = document.createElement('div');
  box.className = 'notification';
  box.innerHTML = `
    <span>${texto}</span>
    <button onclick="this.parentElement.remove()">OK</button>
  `;
  container.appendChild(box);


  const audio = new Audio('bell-notification-337658.mp3');
  audio.play().catch(() => console.warn('🔇 Som bloqueado pelo navegador.'));
}

document.addEventListener('DOMContentLoaded', async () => {
  await atualizarPainel(false);
  setInterval(() => atualizarPainel(true), 20000);
});
</script>
</body>
</html>
