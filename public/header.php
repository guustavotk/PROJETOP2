    <?php

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }


    $current_page = basename($_SERVER['PHP_SELF']);
    $current_uri = $_SERVER['REQUEST_URI'];


    $no_search_pages = ['carrinho.php', 'painel.php','pedidos.php','login.php','cadastroCliente.php','finalizarPedido.php','alterarUsuario.php','alterarProduto.php','cadastroProduto.php','alterarPedidos.php'];

    $is_cart_page = ($current_page === 'carrinho.php');
    $is_request_page = ($current_page === 'pedidos.php');
$is_promo_page = (strpos($current_uri, 'promo=1') !== false);
    $is_painel_page = ($current_page === 'painel.php');


    $show_search = !in_array($current_page, $no_search_pages);
    ?>

    <header class="top-bar">
        <div class="top-row">
            <div class="logo">
                <img src="../imgs/logo.png" alt="Logo">
                <span class="brand">Pastéis & Bebidas</span>
            </div>
            
            <?php if ($show_search): ?>
            
                <div class="search desktop-search" id="desktop-only">
                    <input type="text" id="searchInput" placeholder="Buscar produtos...">
                </div>
            <?php else: ?>
           
                <div id="desktop-only"></div>
            <?php endif; ?>
            
            <div class="actions">
                <a href="carrinho.php" class="btn cart-btn" id="desktop-only">
                    <i class="bi bi-cart"></i>
                    <span class="cart-count">0</span>
                </a>
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin' && $current_page === 'alterarProduto.php' ||$current_page === 'alterarUsuario.php' || $current_page === 'alterarPedidos.php' || $current_page === 'cadastroCliente.php' || $current_page === 'cadastroProduto.php' ): ?>
         
                    <a href="painel.php" class="btn" style="background:#777;">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                      <?php endif; ?>
                <?php if ($is_cart_page || $is_request_page || $is_promo_page || $is_painel_page): ?>
               
                    <a href="index.php" class="btn" style="background:#777;">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                <?php else: ?>
                   
                   
                    <?php if (!isset($_SESSION['user']) ): ?>
                        <a href="login.php" class="btn"><i class="bi bi-person-circle"></i> Login</a>
                    <?php else: ?>
                        <a href="logout.php" class="btn">
                            <i class="bi bi-person-fill"></i> <?= htmlspecialchars($_SESSION['user']['username']) ?> (Sair)
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($show_search): ?>
      
            <div class="bottom-row mobile-only">
                <div class="search">
                    <input type="text" id="searchInputMobile" placeholder="Buscar produtos...">
                </div>
            </div>
        <?php endif; ?>
    </header>
