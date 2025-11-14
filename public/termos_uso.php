<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Termos de Uso - Pastéis & Bebidas</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
main {
  max-width: 900px;
  margin: 100px auto;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 3px 12px rgba(0,0,0,0.1);
  padding: 30px;
  line-height: 1.7;
  color: #333;
}
h1 {
  color: #e53935;
  text-align: center;
  margin-bottom: 20px;
}
h2 {
  color: #e53935;
  margin-top: 25px;
  font-size: 1.2rem;
}
p {
  margin-bottom: 12px;
}
ul { margin-left: 25px; }
</style>
</head>
<body>

<?php include 'header.php'; ?>

<main>
  <h1><i class="bi bi-file-text"></i> Termos de Uso</h1>

  <p>Estes Termos de Uso regulam o acesso e utilização do site <strong>Pastéis & Bebidas</strong>, bem como a compra e entrega de produtos disponíveis na plataforma.</p>

  <h2>1. Aceitação dos Termos</h2>
  <p>Ao acessar o site ou efetuar um pedido, o usuário declara que leu e concorda integralmente com os presentes Termos de Uso e com a <a href="politica_privacidade.php">Política de Privacidade</a>.</p>

  <h2>2. Cadastro e Conta do Usuário</h2>
  <p>Para realizar pedidos, o usuário deve possuir uma conta registrada com informações verdadeiras e atualizadas. É responsabilidade do usuário manter a confidencialidade de seu login e senha.</p>

  <h2>3. Pedidos e Pagamentos</h2>
  <p>Os pedidos são processados conforme a disponibilidade de produtos. Os valores e métodos de pagamento são informados antes da finalização do pedido.</p>
  <p>A loja reserva-se o direito de cancelar pedidos em casos de inconsistências, suspeitas de fraude ou descumprimento dos Termos.</p>

  <h2>4. Entregas</h2>
  <p>As entregas são realizadas no endereço informado pelo cliente no momento da compra. O tempo médio de entrega pode variar conforme a demanda e localização.</p>

  <h2>5. Direitos Autorais</h2>
  <p>Todo o conteúdo do site, incluindo imagens, textos e logotipos, é de propriedade exclusiva da Pastéis & Bebidas, sendo proibida sua reprodução sem autorização.</p>

  <h2>6. Responsabilidade</h2>
  <p>A Pastéis & Bebidas não se responsabiliza por danos decorrentes do uso indevido do site ou de informações incorretas fornecidas pelo usuário.</p>

  <h2>7. Alterações dos Termos</h2>
  <p>Os presentes Termos de Uso podem ser alterados a qualquer momento, sendo a nova versão publicada neste mesmo endereço eletrônico.</p>

  <h2>8. Contato</h2>
  <p>Para dúvidas, sugestões ou reclamações, entre em contato conosco pelo WhatsApp: <strong>(99) 99999-9999</strong> ou e-mail: <strong>contato@pasteisebebidas.com.br</strong>.</p>

  <p style="margin-top:20px;text-align:center;color:#777;">Última atualização: <?= date('d/m/Y') ?></p>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
