<?php
session_start();

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
<title>Cadastrar Produto - Pastéis & Bebidas</title>
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

.form-title {
  text-align: center;
  font-weight: 800;
  margin-bottom: 25px;
  color: #222;
}

.image-preview {
  width: 100%;
  max-width: 260px;
  height: 200px;
  margin: 0 auto 20px;
  border: 2px dashed #ccc;
  border-radius: 10px;
  overflow: hidden;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fafafa;
  transition: border-color .2s;
}
.image-preview:hover { border-color: #e53935; }
.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

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

.btn-ai {
  background: #4CAF50;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 10px;
  font-weight: 600;
  cursor: pointer;
  margin-top: 6px;
}
.btn-ai:hover { background: #388E3C; }

.alert {
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 15px;
  text-align: center;
}
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

.hidden { display: none; }
</style>
</head>
<body>

<?php include 'header.php'; ?>

<main>
  <div class="form-container">
    <h1 class="form-title"><i class="bi bi-plus-circle"></i> Cadastrar Novo Produto</h1>

    <div id="alertBox" class="hidden"></div>

    <form id="productForm" enctype="multipart/form-data">
      <input type="file" id="image" name="image" accept="image/*" class="hidden">
      
      <div class="image-preview" id="imagePreview" onclick="document.getElementById('image').click();">
        Escolher Imagem
      </div>

      <div class="form-group">
        <label for="name" class="required">Nome do Produto</label>
        <input type="text" id="name" name="name" placeholder="Ex: Pastel de Carne" required>
        <button type="button" class="btn-ai" id="gerarDescricao">
          <i class="bi bi-stars"></i> Gerar Descrição Automática
        </button>
      </div>

      <div class="form-group">
        <label for="description">Descrição</label>
        <textarea id="description" name="description" placeholder="Descreva o produto..."></textarea>
      </div>

      <div class="form-group">
        <label for="price" class="required">Preço (R$)</label>
        <input type="text" id="price" name="price" placeholder="Ex: 12,90" required>
      </div>

      <div class="form-group">
        <label for="category_id" class="required">Categoria</label>
        <select id="category_id" name="category_id" required>
          <option value="">Carregando categorias...</option>
        </select>
      </div>

      <button type="submit" class="btn-submit"><i class="bi bi-check-lg"></i> Cadastrar Produto</button>
    </form>
  </div>
</main>

<?php include 'footer.php'; ?>

<script>
// Preview da imagem
const imageInput = document.getElementById('image');
const imagePreview = document.getElementById('imagePreview');
imageInput.addEventListener('change', e => {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = ev => {
    imagePreview.innerHTML = `<img src="${ev.target.result}" alt="Preview">`;
  };
  reader.readAsDataURL(file);
});

// Formatação de preço
document.getElementById('price').addEventListener('input', e => {
  let value = e.target.value.replace(/\D/g, '');
  value = (value / 100).toFixed(2);
  value = value.replace('.', ',');
  e.target.value = value;
});

// Carregar categorias
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
        select.appendChild(opt);
      });
    }
  } catch (err) {
    console.error('Erro ao carregar categorias:', err);
  }
}

// Enviar formulário
document.getElementById('productForm').addEventListener('submit', async e => {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);
  const alertBox = document.getElementById('alertBox');
  alertBox.className = 'alert';
  alertBox.textContent = 'Enviando...';
  alertBox.style.display = 'block';

  try {
    const response = await fetch('api.php?endpoint=create_product', {
      method: 'POST',
      body: formData
    });

    const data = await response.json();

    if (data.success) {
      alertBox.className = 'alert alert-success';
      alertBox.innerHTML = `<i class="bi bi-check-circle"></i> Produto cadastrado com sucesso!`;
      form.reset();
      imagePreview.innerHTML = 'Escolher Imagem';
    } else {
      throw new Error(data.message || 'Erro ao cadastrar produto.');
    }
  } catch (err) {
    alertBox.className = 'alert alert-error';
    alertBox.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${err.message}`;
  }
});

// Integração com Gemini API
document.getElementById('gerarDescricao').addEventListener('click', async () => {
  const nome = document.getElementById('name').value.trim();
  const desc = document.getElementById('description');
  if (!nome) {
    alert('Digite o nome do produto antes de gerar a descrição.');
    return;
  }

  desc.value = 'Gerando descrição automática... ⏳';

  try {
    const res = await fetch('./api_gemini.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ product_name: nome })
    });
    const data = await res.json();
    if (data.description) {
      desc.value = data.description;
    } else {
      desc.value = 'Não foi possível gerar a descrição.';
    }
  } catch (err) {
    desc.value = 'Erro ao gerar descrição.';
    console.error(err);
  }
});

document.addEventListener('DOMContentLoaded', loadCategories);
</script>
</body>
</html>
