<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quem Somos - Pastéis & Bebidas</title>

<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
main {
  max-width: 900px;
  margin: 0 auto;
  padding: 100px 20px 120px;
}
.quem-somos {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  padding: 25px;
}
.quem-somos h2 {
  color: var(--accent);
  margin-bottom: 15px;
  font-size: 1.8rem;
  display: flex;
  align-items: center;
  gap: 10px;
}
.quem-somos p {
  line-height: 1.6;
  color: #444;
  margin-bottom: 15px;
  font-size: 1rem;
}
.info-box {
  background: #fff7f0;
  border-radius: 8px;
  padding: 15px;
  margin-top: 20px;
}
.info-item {
  display: flex;
  align-items: center;
  margin: 8px 0;
}
.info-item i {
  font-size: 1.3rem;
  color: var(--accent);
  margin-right: 10px;
}
.mapa {
  margin-top: 25px;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.mapa iframe {
  width: 100%;
  height: 300px;
  border: 0;
}
@media (max-width: 600px) {
  main { padding: 90px 15px 120px; }
  .quem-somos h2 { font-size: 1.5rem; }
}
</style>
</head>

<body>
<?php include 'header.php'; ?>

<main>
  <section class="quem-somos">
    <h2><i class="bi bi-shop"></i> Quem Somos</h2>

    <p>
      Somos a <strong>Pastéis & Bebidas</strong>, uma empresa apaixonada por sabor, tradição e atendimento de qualidade. 
      Nosso objetivo é oferecer aos clientes os melhores pastéis, recheados com ingredientes frescos, 
      acompanhados de deliciosas bebidas para todos os gostos.
    </p>

    <p>
      Trabalhamos com carinho para garantir que cada pedido chegue quentinho e crocante até você. 
      Valorizamos nossos clientes e buscamos constantemente aprimorar nossos serviços, 
      trazendo novas promoções e sabores irresistíveis.
    </p>

    <div class="info-box">
      <div class="info-item">
        <i class="bi bi-geo-alt-fill"></i>
        <span><strong>Endereço:</strong> Rua das Delícias, 123 - Centro, Fortaleza - CE</span>
      </div>
      <div class="info-item">
        <i class="bi bi-telephone-fill"></i>
        <span><strong>Telefone:</strong> (85) 3333-4444</span>
      </div>
      <div class="info-item">
        <i class="bi bi-whatsapp"></i>
        <span><strong>WhatsApp:</strong> <a href="https://wa.me/5588999999999" target="_blank" style="color:var(--accent);text-decoration:none;">(88) 99999-9999</a></span>
      </div>
      <div class="info-item">
        <i class="bi bi-envelope-fill"></i>
        <span><strong>E-mail:</strong> contato@pasteisebebidas.com.br</span>
      </div>
      <div class="info-item">
        <i class="bi bi-clock-fill"></i>
        <span><strong>Horário de Funcionamento:</strong> Segunda a Domingo - 10h às 22h</span>
      </div>
    </div>

    <div class="mapa">
      <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3975.949059062573!2d-38.54296338573674!3d-3.731860444384607!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x7c748f3f8c92a29%3A0x8e147cf36edb43a7!2sFortaleza%20-%20CE!5e0!3m2!1spt-BR!2sbr!4v1700000000000!5m2!1spt-BR!2sbr" 
        allowfullscreen="" loading="lazy">
      </iframe>
    </div>
  </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
