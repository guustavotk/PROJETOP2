<?php
session_start();


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../core/core.php';
$pdo = Database::connect();

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;


$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("<h2 style='padding:100px;text-align:center;'>Produto não encontrado.</h2>");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Alterar Produto - Pastéis & Bebidas</title>
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


.image-preview {
  position: relative;
  width: 100%;
  max-width: 280px;
  height: 220px;
  margin: 0 auto 20px;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.image-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  background: rgba(0,0,0,0.55);
  color: #fff;
  text-align: center;
  padding: 8px 0;
  font-size: 0.95rem;
  font-weight: 600;
  opacity: 0.7;
  transition: opacity 0.2s;
}
.image-preview:hover .image-overlay { opacity: 1; }

.form-group { margin-bottom: 18px; }
label { font-weight: 600; color: #333; display: block; margin-bottom: 6px; }
input, textarea, select {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ddd;
  font-size: 15px;
}
textarea { resize: vertical; min-height: 80px; }

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
    <a href="alterarProduto.php?id=<?= max($productId - 1, 1) ?>"><i class="bi bi-arrow-left-circle"></i> Anterior</a>
    <a href="alterarProduto.php?id=<?= $productId + 1 ?>">Próximo <i class="bi bi-arrow-right-circle"></i></a>
  </div>

  <div class="form-container">
    <h1 class="form-title"><i class="bi bi-pencil-square"></i> Editar Produto</h1>

    <div id="alertBox" class="hidden"></div>

    <form id="editForm" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= $product['id'] ?>">
      <input type="file" id="image" name="image" accept="image/*" class="hidden">

      <div class="image-preview" onclick="document.getElementById('image').click();" id="imagePreview">
        <img src="<?= htmlspecialchars($product['image'] ?: 'imgs/default.png') ?>" alt="Imagem do produto">
        <div class="image-overlay"><i class="bi bi-camera"></i> Alterar foto</div>
      </div>

      <div class="form-group">
        <label for="name">Nome do Produto</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
      </div>

      <div class="form-group">
        <label for="description">Descrição</label>
        <textarea id="description" name="description"><?= htmlspecialchars($product['description']) ?></textarea>
      </div>

      <div class="form-group">
        <label for="price">Preço (R$)</label>
        <input type="text" id="price" name="price" value="<?= number_format($product['price'], 2, ',', '.') ?>" required>
      </div>

      <div class="form-group">
        <label for="price_promo">Preço Promocional (opcional)</label>
        <input type="text" id="price_promo" name="price_promo" value="<?= $product['price_promo'] ? number_format($product['price_promo'], 2, ',', '.') : '' ?>">
      </div>

      <div class="form-group">
        <label for="category_id">Categoria</label>
        <select id="category_id" name="category_id" required>
          <option value="">Carregando categorias...</option>
        </select>
      </div>

      <button type="submit" class="btn-submit"><i class="bi bi-check-lg"></i> Salvar Alterações</button>
    </form>
  </div>
</main>


<div class="modal-overlay" id="modalSucesso">
  <div class="modal">
    <i class="bi bi-check-circle" style="font-size:3rem;color:#4CAF50;"></i>
    <h3>Produto alterado com sucesso!</h3>
    <p>O produto foi atualizado no sistema.</p>
    <div class="btns">
      <button class="btn-continuar" onclick="fecharModal()">Continuar alterando</button>
      <button class="btn-voltar" onclick="window.location.href='painel.php'">Voltar ao Painel</button>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<script>


document.getElementById('image').addEventListener('change', e => {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = ev => {
    document.querySelector('#imagePreview img').src = ev.target.result;
  };
  reader.readAsDataURL(file);
});



document.querySelectorAll('#price, #price_promo').forEach(input => {
  input.addEventListener('input', e => {
    let value = e.target.value.replace(/\D/g, '');
    value = (value / 100).toFixed(2);
    e.target.value = value.replace('.', ',');
  });
});



async function loadCategories() {
  try {
    const res = await fetch('api.php?endpoint=categories');
    const data = await res.json();
    const select = document.getElementById('category_id');
    select.innerHTML = '<option value="">Selecione uma categoria</option>';
    if (data.success && data.categories) {
      data.categories.forEach(cat => {
        const opt = document.createElement('option');
        opt.value = cat.id;
        opt.textContent = cat.name;
        if (cat.id == <?= (int)$product['category_id'] ?>) opt.selected = true;
        select.appendChild(opt);
      });
    }
  } catch (err) {
    console.error('Erro ao carregar categorias:', err);
  }
}



document.getElementById('editForm').addEventListener('submit', async e => {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);
  const alertBox = document.getElementById('alertBox');
  alertBox.className = 'alert';
  alertBox.textContent = 'Salvando...';
  alertBox.style.display = 'block';

  try {
    const res = await fetch('api.php?endpoint=update_product', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();
    if (data.success) {
      alertBox.className = 'alert alert-success';
      alertBox.innerHTML = `<i class="bi bi-check-circle"></i> Produto atualizado com sucesso!`;
      document.getElementById('modalSucesso').style.display = 'flex';
    } else {
      throw new Error(data.message || 'Erro ao atualizar produto.');
    }
  } catch (err) {
    alertBox.className = 'alert alert-error';
    alertBox.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${err.message}`;
  }
});

function fecharModal() {
  document.getElementById('modalSucesso').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', loadCategories);
</script>
</body>
</html>
