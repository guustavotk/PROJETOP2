<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Política de Privacidade - Pastéis & Bebidas</title>
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
  <h1><i class="bi bi-shield-lock"></i> Política de Privacidade</h1>

  <p>A <strong>Pastéis & Bebidas</strong> respeita sua privacidade e está comprometida com a proteção dos seus dados pessoais, conforme a <strong>Lei nº 13.709/2018 (Lei Geral de Proteção de Dados – LGPD)</strong>.</p>

  <h2>1. Coleta de Informações</h2>
  <p>Coletamos apenas as informações necessárias para o funcionamento do site e dos serviços, incluindo:</p>
  <ul>
    <li>Nome completo</li>
    <li>Telefone e endereço para entrega</li>
    <li>Dados de login e autenticação</li>
    <li>Histórico de pedidos</li>
  </ul>

  <h2>2. Uso das Informações</h2>
  <p>Os dados coletados são utilizados para:</p>
  <ul>
    <li>Processar pedidos e entregas;</li>
    <li>Comunicação com o cliente (via WhatsApp, e-mail ou telefone);</li>
    <li>Melhorar o atendimento e experiência de navegação.</li>
  </ul>

  <h2>3. Compartilhamento de Dados</h2>
  <p>Os dados pessoais não são vendidos nem compartilhados com terceiros, exceto quando necessário para:</p>
  <ul>
    <li>Processar pagamentos (instituições financeiras);</li>
    <li>Efetuar entregas (serviços de entrega ou motoboy);</li>
    <li>Atender a exigências legais.</li>
  </ul>

  <h2>4. Armazenamento e Segurança</h2>
  <p>Utilizamos medidas de segurança físicas e digitais para proteger seus dados contra acessos não autorizados, perda ou destruição. Os dados são armazenados em servidores seguros.</p>

  <h2>5. Direitos do Titular</h2>
  <p>De acordo com a LGPD, você tem direito a:</p>
  <ul>
    <li>Acessar seus dados pessoais;</li>
    <li>Corrigir dados incorretos ou desatualizados;</li>
    <li>Solicitar a exclusão de seus dados pessoais;</li>
    <li>Revogar o consentimento para uso dos dados.</li>
  </ul>

  <h2>6. Contato</h2>
  <p>Para exercer seus direitos ou tirar dúvidas sobre esta política, entre em contato conosco pelo WhatsApp: <strong>(99) 99999-9999</strong> ou e-mail: <strong>contato@pasteisebebidas.com.br</strong>.</p>

  <p style="margin-top:20px;text-align:center;color:#777;">Última atualização: <?= date('d/m/Y') ?></p>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
