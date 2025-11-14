<?php

session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seu Carrinho - Pastéis & Bebidas</title>
    <link rel="stylesheet" href="css/style.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  
</head>
<body class="carrinho-page">
    <?php include 'header.php'; ?>

    <main class="container">
        <h2 style="margin-bottom: 20px;">🛒 Seu Carrinho</h2>
        
        <section id="listaCarrinho" class="carrinho-list"></section>

        <div id="totalContainer" style="display: none;">
            Total do Pedido: R$ <span id="totalPedido">0,00</span>
        </div>

        <button id="btnFinalizar" class="btn" style="display: none;">
            ✅ Finalizar Pedido
        </button>

        <div id="emptyCartMessage" class="empty-cart" style="display: none;">
            <i class="bi bi-cart-x"></i>
            <h3>Seu carrinho está vazio</h3>
            <p>Que tal adicionar alguns pastéis deliciosos?</p>
            <a href="index.php" class="btn" style="margin-top: 15px;">
                <i class="bi bi-arrow-left"></i> Ver Produtos
            </a>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
     
        const originalRenderCart = renderCart;
        
        renderCart = function() {
            const cart = getCart();
            const totalContainer = document.getElementById('totalContainer');
            const btnFinalizar = document.getElementById('btnFinalizar');
            const emptyCartMessage = document.getElementById('emptyCartMessage');
            
    
            originalRenderCart();
            
            if (cart.length === 0) {
                totalContainer.style.display = 'none';
                btnFinalizar.style.display = 'none';
                emptyCartMessage.style.display = 'block';
            } else {
                totalContainer.style.display = 'block';
                btnFinalizar.style.display = 'block';
                emptyCartMessage.style.display = 'none';
            }
        };


        document.addEventListener('DOMContentLoaded', function() {
            renderCart();
        });
    </script>
</body>
</html>