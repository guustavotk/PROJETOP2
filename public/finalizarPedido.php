<?php 
session_start(); 
require_once __DIR__ . '/../core/core.php'; 

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) { 
    $_SESSION['msg'] = "⚠️ Você precisa estar logado para finalizar o pedido."; 
    header('Location: login.php'); 
    exit; 
} 

$userId = (int) $_SESSION['user']['id']; 
$pdo = Database::connect(); 
$nomeUsuario = $_SESSION['user']['name']; 
$telefoneUsuario = ''; 
$enderecoUsuario = ''; 
$numeroUsuario = ''; 

try { 
    $stmtClient = $pdo->prepare("SELECT phone, address_c, number_address FROM clients WHERE user_id = ? LIMIT 1"); 
    $stmtClient->execute([$userId]); 
    $clientRow = $stmtClient->fetch(PDO::FETCH_ASSOC); 
    if ($clientRow) { 
        $telefoneUsuario = $clientRow['phone'] ?? ''; 
        $enderecoUsuario = $clientRow['address_c'] ?? ''; 
        $numeroUsuario = $clientRow['number_address'] ?? ''; 
    } 
} catch (Exception $e) { 
    echo '<br><br><br> erro: '. $e->getMessage(); 
} 
?> 

<!DOCTYPE html> 
<html lang="pt-BR"> 
<head> 
<meta charset="UTF-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<title>Finalizar Pedido - Pastéis & Bebidas</title> 
<link rel="stylesheet" href="css/style.css"> 
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"> 
<style> 
main { max-width: 600px; margin: 0 auto; padding: 100px 20px 120px; }
.resumo { background: #fff; border-radius: 12px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 20px; }
.resumo h3 { color: var(--accent); text-align: center; margin-bottom: 15px; }
.resumo p { font-size: 1rem; margin: 5px 0; }
.form-group { margin-bottom: 20px; }
label { font-weight: 600; display: block; margin-bottom: 6px; }
select, input { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 1rem; }
.btn-finalizar { width: 100%; padding: 14px; font-size: 1.1rem; font-weight: 600; border: none; border-radius: 10px; background: linear-gradient(135deg, #e53935, #ff7043); color: #fff; cursor: pointer; transition: 0.2s; }
.btn-finalizar:hover { background: linear-gradient(135deg, #d32f2f, #f4511e); transform: translateY(-2px); }
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 9999; }
.modal { background: #fff; border-radius: 14px; padding: 25px; text-align: center; max-width: 340px; box-shadow: 0 4px 16px rgba(0,0,0,0.2); }
</style> 
</head> 

<body> 
<?php include 'header.php'; ?> 

<main> 
<div class="resumo"> 
<h3><i class="bi bi-receipt"></i> Resumo do Pedido</h3> 
<p><strong>Nº do Pedido:</strong> <span id="orderNumber">#---</span></p> 
<p><strong>Total:</strong> R$ <span id="totalPedido">0,00</span></p> 
</div> 

<form id="formPedido"> 
<div class="form-group"> 
<label for="payment_method">Forma de Pagamento</label> 
<select id="payment_method" required> 
<option value="">Selecione...</option> 
<option value="Cartão">Cartão</option> 
<option value="Pix">Pix</option> 
<option value="Dinheiro">Dinheiro</option> 
</select> 
</div> 

<div class="form-group"> 
<label>Momento do Pagamento</label> 
<select id="payment_time" required> 
<option value="entrega">Pagar na Entrega</option> 
<option value="agora">Pagar Agora</option> 
</select> 
</div> 

<div class="form-group"> 
<label for="address">Endereço de Entrega</label> 
<input type="text" id="address" name="address" placeholder="Rua, número, bairro" required 
value="<?= htmlspecialchars(trim($enderecoUsuario . ' N:' . $numeroUsuario)) ?>"> 

<hr style="margin:15px 0;border:none;border-top:1px solid #eee;"> 
<h4 style="margin-bottom:8px;color:#333;">Dados para Contato</h4> 
<label for="name">Nome</label> 
<input type="text" id="name" name="name" placeholder="Seu nome" required value="<?= htmlspecialchars($nomeUsuario) ?>"> 
<label for="phone">Telefone</label> 
<input type="text" id="phone" name="phone" placeholder="Telefone para contato" required value="<?= htmlspecialchars($telefoneUsuario) ?>"> 
</div> 

<button type="submit" class="btn-finalizar"><i class="bi bi-check2-circle"></i> Finalizar Pedido</button> 
</form> 
</main> 

<?php include 'footer.php'; ?> 

<div id="modalSuccess" class="modal-overlay" style="display:none;"> 
<div class="modal"> 
<i class="bi bi-check-circle" style="font-size:3rem;color:#4CAF50;"></i> 
<h3>Pedido finalizado com sucesso!</h3> 
<p>Estamos te redirecionando para o WhatsApp da loja...</p> 
</div> 
</div> 

<script>
function getCart() { return JSON.parse(localStorage.getItem('carrinho')) || []; }
function calcTotal(cart) { return cart.reduce((acc, p) => acc + (parseFloat(p.price_promo || p.price) * p.qtd), 0); }

document.addEventListener('DOMContentLoaded', () => {
  const cart = getCart();
  const total = calcTotal(cart);
  document.getElementById('totalPedido').textContent = total.toFixed(2).replace('.', ',');
  document.getElementById('orderNumber').textContent = '#' + Math.floor(Math.random() * 9000 + 1000);
});

document.getElementById('formPedido').addEventListener('submit', async e => {
  e.preventDefault();

  const cart = getCart();
  if (cart.length === 0) {
    alert('Seu carrinho está vazio!');
    return;
  }

  const total = calcTotal(cart);
  const orderNumber = document.getElementById('orderNumber').textContent;
  const payment_method = document.getElementById('payment_method').value;
  const payment_time = document.getElementById('payment_time').value;
  const address = document.getElementById('address').value.trim();
  const name = document.getElementById('name').value.trim();
  
  // 🔥 CORREÇÃO: Limpar telefone (remover caracteres especiais)
  let phone = document.getElementById('phone').value.trim();
  phone = phone.replace(/\D/g, ''); // Remove tudo que não é número

  // 🔥 CORREÇÃO: Validação melhorada
  if (!payment_method || !address || !name || !phone) {
    alert('Preencha todos os campos obrigatórios!');
    return;
  }

  if (phone.length < 10 || phone.length > 11) {
    alert('Digite um telefone válido com DDD (10 ou 11 dígitos)!');
    return;
  }

  try {
    const response = await fetch('api.php?endpoint=create_order', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        total: total,
        payment_method: payment_method + ' (' + payment_time + ')',
        address: address,
        name: name,
        phone: phone,
        items: cart
      })
    });

    const data = await response.json();
    console.log("📦 Resposta da API:", data);

    if (data.success) {
      document.getElementById('modalSuccess').style.display = 'flex';

      let msg = `*Novo Pedido Recebido!* 🍽️%0A%0A`;
      msg += `*Número do Pedido:* ${orderNumber}%0A`;
      msg += `*Cliente:* ${encodeURIComponent(name)}%0A`;
      msg += `*Telefone:* ${encodeURIComponent(phone)}%0A`;
      msg += `*Endereço de Entrega:* ${encodeURIComponent(address)}%0A%0A`;
      msg += `*Itens do Pedido:*%0A`;
      cart.forEach(p => {
        const itemTotal = (parseFloat(p.price_promo || p.price) * p.qtd).toFixed(2);
        msg += `- ${encodeURIComponent(p.name)} x${p.qtd} — R$ ${itemTotal.replace('.', ',')}%0A`;
      });
      msg += `%0A*Forma de Pagamento:* ${encodeURIComponent(payment_method + ' (' + payment_time + ')')}%0A`;
      msg += `*Total:* R$ ${total.toFixed(2).replace('.', ',')}%0A%0A`;
      msg += `❓ *Qual o tempo estimado de entrega?*`;

      // 🔥 CORREÇÃO CRÍTICA: Use WhatsApp Web
      const whatsappNumber = '5515997998877'; // ALTERE PARA SEU NÚMERO
      const whatsappUrl = `https://web.whatsapp.com/send?phone=${whatsappNumber}&text=${msg}`;

      localStorage.removeItem('carrinho');
      setTimeout(() => {
        window.location.href = whatsappUrl;
      }, 2000);
    } else {
      alert('Erro: ' + (data.message || 'Falha ao enviar pedido.'));
    }
  } catch (err) {
    // 🔥 CORREÇÃO: Em caso de erro, vai direto para WhatsApp
    console.error('Erro de rede, indo direto para WhatsApp:', err);
    
    document.getElementById('modalSuccess').style.display = 'flex';

    let msg = `*Novo Pedido Recebido!* 🍽️%0A%0A`;
    msg += `*Número do Pedido:* ${orderNumber}%0A`;
    msg += `*Cliente:* ${encodeURIComponent(name)}%0A`;
    msg += `*Telefone:* ${encodeURIComponent(phone)}%0A`;
    msg += `*Endereço de Entrega:* ${encodeURIComponent(address)}%0A%0A`;

    msg += `*Itens do Pedido:*%0A`;
    cart.forEach(p => {
      const itemTotal = (parseFloat(p.price_promo || p.price) * p.qtd).toFixed(2);
      msg += `- ${encodeURIComponent(p.name)} x${p.qtd} — R$ ${itemTotal.replace('.', ',')}%0A`;
    });

    msg += `%0A*Forma de Pagamento:* ${encodeURIComponent(payment_method + ' (' + payment_time + ')')}%0A`;
    msg += `*Total:* R$ ${total.toFixed(2).replace('.', ',')}%0A%0A`;
    msg += `❓ *Qual o tempo estimado de entrega?*`;

    const whatsappNumber = '5515997998877'; // ALTERE PARA SEU NÚMERO
    const whatsappUrl = `https://web.whatsapp.com/send?phone=${whatsappNumber}&text=${msg}`;

    localStorage.removeItem('carrinho');
    setTimeout(() => {
      window.location.href = whatsappUrl;
    }, 2000);
  }
});
</script>

</body> 
</html>