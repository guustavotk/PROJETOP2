
<nav class="bottom-nav">

    <a href="carrinho.php" class="<?= basename($_SERVER['PHP_SELF']) === 'carrinho.php' ? 'active' : '' ?>">
        <i class="bi bi-cart"></i>
        <span>Carrinho</span>
        <span class="cart-count-bot">0</span>
    </a>

   
    <a href="index.php?promo=1" class="<?= isset($_GET['promo']) ? 'active' : '' ?>">
        <i class="bi bi-star-fill"></i>
        <span>Promoções</span>
    </a>

   
    <a href="pedidos.php" class="<?= basename($_SERVER['PHP_SELF']) === 'pedidos.php' ? 'active' : '' ?>">
        <i class="bi bi-layout-text-sidebar"></i>
        <span>Pedidos</span>
    </a>

  
    <a href="#" id="btnMais">
        <i class="bi bi-list"></i>
        <span>Mais</span>
    </a>
</nav>


<div id="sideMenu" class="side-menu">
    <div class="side-menu-content">
        <button id="closeMenu" class="close-menu"><i class="bi bi-x-lg"></i></button>
        <h3 class="menu-title">Menu</h3>
        <ul>
            <li><a href="index.php"><i class="bi bi-house-door"></i> Início</a></li>
            <li><a href="carrinho.php"><i class="bi bi-cart3"></i> Carrinho</a></li>
            <li><a href="index.php?promo=1"><i class="bi bi-star-fill"></i> Promoções</a></li>
            <li><a href="pedidos.php"><i class="bi bi-layout-text-sidebar"></i> Pedidos</a></li>
            <li><a href="sobre.php"><i class="bi bi-info-circle"></i> Quem Somos</a></li>

            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user']['role'] === 'user'): ?>
                <li> <a href="alterarUsuario.php?id=<?= $_SESSION['user']['id'] ?>"><i class="bi bi-person-gear"></i> Alterar Conta</a></li>
                  <?php endif; ?>

                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <li><a href="painel.php"><i class="bi bi-speedometer2"></i> Painel Admin</a></li>
                <?php endif; ?>

            <?php else: ?>
                <li><a href="login.php"><i class="bi bi-person"></i> Login</a></li>
            <?php endif; ?>
        </ul>

        <footer class="side-menu-footer">
            <strong>Pastéis & Bebidas</strong><br>
            <a href="https://wa.me/5588999999999" target="_blank">
                <i class="bi bi-whatsapp"></i> (88) 99999-9999
            </a>
        </footer>
    </div>
</div>


<div id="modalOverlay" class="modal-overlay" style="display:none;">
    <div class="modal">
        <img id="modalImg" src="" alt="Produto">
        <div class="modal-content">
            <div class="modal-title" id="modalTitle"></div>
            <div class="modal-desc" id="modalDesc"></div>
            <div class="modal-price" id="modalPrice"></div>
            <div class="modal-footer" style="margin-top:10px;">
                <button class="btn" id="btnClose" style="background:#ccc;color:#000;">Fechar</button>
                <button class="btn" id="btnAdd" style="background:#e53935;">Adicionar</button>
            </div>
        </div>
    </div>
</div>


<script src="js/engine.js"></script>

<script>

const sideMenu = document.getElementById('sideMenu');
const btnMais = document.getElementById('btnMais');
const closeMenu = document.getElementById('closeMenu');

btnMais.addEventListener('click', e => {
    e.preventDefault();
    sideMenu.classList.add('open');
});

closeMenu.addEventListener('click', () => {
    sideMenu.classList.remove('open');
});

window.addEventListener('click', e => {
    if (e.target === sideMenu) sideMenu.classList.remove('open');
});
</script>

<div id="lgpd-consent" class="lgpd-banner">
  <div class="lgpd-content">
    <p>
      Usamos cookies e armazenamos dados pessoais para melhorar sua experiência,
      conforme a <a href="politica_privacidade.php" target="_blank">Política de Privacidade</a>
      e a <a href="termos_uso.php" target="_blank">Lei Geral de Proteção de Dados (LGPD)</a>.
    </p>
    <div class="lgpd-buttons">
      <button id="btnAceitarLGPD">Aceitar</button>
    </div>
  </div>
</div>

<style>
.lgpd-banner {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  background: #fff;
  border-top: 2px solid #e53935;
  box-shadow: 0 -2px 10px rgba(0,0,0,0.15);
  padding: 15px 20px;
  display: none;
  z-index: 99999;
}

.lgpd-content {
  display: flex;
  flex-direction: column;
  gap: 8px;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.lgpd-content p {
  font-size: 0.95rem;
  color: #333;
  margin: 0;
}

.lgpd-content a {
  color: #e53935;
  text-decoration: underline;
}

.lgpd-buttons button {
  background: #e53935;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 10px 18px;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.95rem;
  transition: background 0.2s;
}

.lgpd-buttons button:hover {
  background: #c62828;
}
</style>

<script>

document.addEventListener('DOMContentLoaded', () => {
  const aceitou = localStorage.getItem('lgpd_aceito');
  const banner = document.getElementById('lgpd-consent');
  if (!aceitou && banner) {
    banner.style.display = 'block';
  }

  document.getElementById('btnAceitarLGPD').addEventListener('click', () => {
    localStorage.setItem('lgpd_aceito', 'true');
    banner.style.display = 'none';
  });
});
</script>
