<?php
session_start();

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>

<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body>
<?php include 'header.php'; ?>

<main>
  <div class="login-box" style="margin-top:10px">
    <h2><i class="bi bi-person-circle"></i> Login</h2>
    
    <form id="loginForm">
      <input type="text" id="username" name="username" placeholder="Usuário" required>
      <input type="password" id="password" name="password" placeholder="Senha" required>
      <button type="submit">Entrar</button>
    </form>

    <div id="message"></div>

    <a href="cadastroCliente.php" class="btn-secondary">Cadastro Rápido</a>
  </div>
</main>

<?php include 'footer.php'; ?> 

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const message  = document.getElementById('message');
    message.innerHTML = '';

    if (!username || !password) {
        message.innerHTML = `<div class="alert alert-error">Preencha todos os campos.</div>`;
        return;
    }

    try {
        const response = await fetch('api.php?endpoint=user&action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password }),
            credentials: 'include' // mantém sessão ativa
        });

        const data = await response.json();

        if (data.success) {
            message.innerHTML = `<div class="alert alert-success">${data.message || 'Login realizado com sucesso.'}</div>`;
            
            // redireciona após login
            setTimeout(() => {
                window.location.href = data.redirect || 'index.php';
            }, 1000);
        } else {
            message.innerHTML = `<div class="alert alert-error">${data.message || 'Usuário ou senha inválidos.'}</div>`;
        }

    } catch (error) {
        console.error(error);
        message.innerHTML = `<div class="alert alert-error">Erro ao conectar com o servidor.</div>`;
    }
});
</script>

</body>
</html>
