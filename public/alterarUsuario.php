<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../core/core.php';
$pdo = Database::connect();
$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT 
        u.id, u.fullname, u.role, 
        c.phone, c.address_c 
    FROM users u
    LEFT JOIN clients c ON c.user_id = u.id
    WHERE u.id = ?
");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("<h2 style='padding:100px;text-align:center;'>Usuário não encontrado.</h2>");
}

$isAdmin = isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Alterar Usuário - Painel Administrativo</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
main { max-width: 600px; margin: 0 auto; padding: 100px 20px 120px; }
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

.form-group { margin-bottom: 18px; }
label { font-weight: 600; color: #333; display: block; margin-bottom: 6px; }
input, select {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ddd;
  font-size: 15px;
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

/* ---------- Modal ---------- */
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
.btn-ok { background: #4CAF50; color: #fff; width: 100%; }
.btn-voltar:hover { background: #c62828; }
.btn-continuar:hover, .btn-ok:hover { background: #388E3C; }
</style>
</head>
<body>

<?php include 'header.php'; ?>

<main>
  <?php if ($isAdmin): ?>
  <div class="nav-buttons">
    <a href="alterarUsuario.php?id=<?= max($userId - 1, 1) ?>"><i class="bi bi-arrow-left-circle"></i> Anterior</a>
    <a href="alterarUsuario.php?id=<?= $userId + 1 ?>">Próximo <i class="bi bi-arrow-right-circle"></i></a>
  </div>
  <?php endif; ?>

  <div class="form-container">
    <h1 class="form-title"><i class="bi bi-person-lines-fill"></i> Editar Usuário</h1>

    <div id="alertBox" class="hidden"></div>

    <form id="editUserForm">
      <input type="hidden" name="id" value="<?= $user['id'] ?>">

      <div class="form-group">
        <label for="name">Nome completo</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['fullname']) ?>" required>
      </div>

      <div class="form-group">
        <label for="address">Endereço residencial</label>
        <input type="text" id="address" name="address" value="<?= htmlspecialchars($user['address_c'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="phone">Telefone</label>
        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="password">Senha (<b style="color:red">deixe em branco para não alterar</b>)</label>
        <input type="password" id="password" name="password">
      </div>

      <?php if ($isAdmin): ?>
      <div class="form-group">
        <label for="role">Função</label>
        <select id="role" name="role" required>
          <option value="client" <?= $user['role'] === 'client' ? 'selected' : '' ?>>Cliente</option>
          <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
        </select>
      </div>
      <?php else: ?>
      <input type="hidden" name="role" value="<?= htmlspecialchars($user['role']) ?>">
      <?php endif; ?>

      <button type="submit" class="btn-submit"><i class="bi bi-check-lg"></i> Salvar Alterações</button>
    </form>
  </div>
</main>

<div class="modal-overlay" id="modalSucesso">
  <div class="modal">
    <i class="bi bi-check-circle" style="font-size:3rem;color:#4CAF50;"></i>
    <h3>Usuário alterado com sucesso!</h3>
    <p>As informações foram atualizadas com sucesso.</p>

    <?php if ($isAdmin): ?>
    <div class="btns">
      <button class="btn-continuar" onclick="fecharModal()">Continuar alterando</button>
      <button class="btn-voltar" onclick="window.location.href='painel.php'">Voltar ao Painel</button>
    </div>
    <?php else: ?>
    <div class="btns">
      <button class="btn-ok" onclick="fecharModal()">OK</button>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php include 'footer.php'; ?>

<script>
document.getElementById('editUserForm').addEventListener('submit', async e => {
  e.preventDefault();
  const form = e.target;
  const data = Object.fromEntries(new FormData(form));
  const alertBox = document.getElementById('alertBox');
  
  alertBox.className = 'alert';
  alertBox.textContent = 'Salvando...';
  alertBox.style.display = 'block';

  try {
    const res = await fetch('api.php?endpoint=user&action=update', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        id: data.id,
        name: data.name,
        address_c: data.address,
        phone: data.phone,
        password: data.password,
        role: data.role
      }),
      credentials: 'include'
    });

    const response = await res.json();

    if (response.success) {
      alertBox.className = 'alert alert-success';
      alertBox.innerHTML = `<i class="bi bi-check-circle"></i> ${response.message || 'Usuário atualizado com sucesso!'}`;
      document.getElementById('modalSucesso').style.display = 'flex';
    } else {
      throw new Error(response.message || 'Erro ao atualizar usuário.');
    }
  } catch (err) {
    console.error(err);
    alertBox.className = 'alert alert-error';
    alertBox.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${err.message}`;
  }
});

function fecharModal() {
  document.getElementById('modalSucesso').style.display = 'none';
}
</script>
</body>
</html>
