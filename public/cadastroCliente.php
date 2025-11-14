<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastro Rápido</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body>
<?php include 'header.php'; ?> 

<main style="margin-top:30px">
  <div class="cadastro-box">
    <h2><i class="bi bi-person-plus"></i> Cadastro Rápido</h2>
    <form id="formCadastro">
      <input type="text" name="username" placeholder="Usuário" required>
      <input type="password" name="password" placeholder="Senha" required>
      <input type="text" name="name" placeholder="Nome completo" required>
      <input type="text" name="phone" placeholder="Telefone" required>
      <input type="text" name="address" placeholder="Endereço" required>
      <input type="number" name="number" placeholder="Número" required>
      <!--<input type="email" name="email" placeholder="E-mail" hidden> -->
      <button type="submit">Cadastrar</button>
      <div id="erro" class="alert" style="display:none;"></div>
    </form>
  </div>
</main>

<div id="modalOverlay" class="modal-overlay">
  <div class="modal">
    <h3>Cadastro realizado com sucesso!</h3>
    <p>Bem-vindo! Você já pode começar a comprar.</p>
    <button id="btnOk">OK</button>
  </div>
</div>

<?php include 'footer.php'; ?> 

<script>
document.getElementById('formCadastro').addEventListener('submit', async e => {
  e.preventDefault();

  const form = e.target;
  const data = Object.fromEntries(new FormData(form));
  const erroBox = document.getElementById('erro');

  erroBox.style.display = 'none';
  erroBox.textContent = '';

  try {
    const res = await fetch('api.php?endpoint=user&action=register', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({
        username: data.username,
        password: data.password,
        name: data.name,
        phone: data.phone,
        address_c: data.address,  // compatível com AuthController
        number_address: data.number,
        email: data.email
      })
    });

    const result = await res.json();

    if (result.success) {
      document.getElementById('modalOverlay').style.display = 'flex';
      document.getElementById('btnOk').onclick = () => {
        window.location.href = result.redirect || 'index.php';
      };
    } else {
      erroBox.textContent = result.message || 'Erro ao cadastrar.';
      erroBox.style.display = 'block';
    }

  } catch (err) {
    console.error(err);
    erroBox.textContent = 'Erro na conexão com o servidor.';
    erroBox.style.display = 'block';
  }
});
</script>

</body>
</html>
