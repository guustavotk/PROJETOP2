<?php 
session_start(); 
$isPromoPage = isset($_GET['promo']) && $_GET['promo'] == 1;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Pastéis & Bebidas</title> 
    
    <link rel="stylesheet" href="css/style.css"> 


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"> 
</head> 

<body class="home-page">
    <?php include 'header.php'; ?>

    <!-- ===== CATEGORIAS ===== -->
    <section class="categorias" id="categorias">
        <button data-cat="all" class="active"><i class="bi bi-grid"></i> Todos</button>
    </section>

    <!-- ===== CONTEÚDO ===== -->
    <main class="container">
        <section id="produtos" class="grid">
    
        </section>
    </main>

    <?php include 'footer.php'; ?>

  <!-- MODAL DE PRODUTO -->
    <div id="modalOverlay" class="modal-overlay">
      <div class="modal">
        <img id="modalImg" src="" alt="Produto">
        <div class="modal-content">
          <h3 id="modalTitle" class="modal-title"></h3>
          <p id="modalDesc" class="modal-desc"></p>
          <div class="modal-price" id="modalPrice"></div>

          <div class="modal-footer">
            <button id="btnClose" class="btn" style="background:#999;">Fechar</button>
            <button id="btnAdd" class="btn">Adicionar</button>
          </div>
        </div>
      </div>
    </div>
        <script src="js/engine.js"></script>
<script>


async function fetchCategorias() {
    const res = await fetch('api.php?endpoint=categories');
    return res.json();
}

async function fetchProdutos() {
    const res = await fetch('api.php?endpoint=products');
    return res.json();
}

function renderCategorias(categorias) {
    const catContainer = document.getElementById('categorias');
    catContainer.innerHTML = `
        <button data-cat="all" class="active"><i class="bi bi-grid"></i> Todos</button>
    `;
    categorias.forEach(cat => {
        const btn = document.createElement('button');
        btn.dataset.cat = cat.id;
        btn.innerHTML = `${cat.icon ? `<i class="${cat.icon}"></i>` : ''} ${cat.name}`;
        catContainer.appendChild(btn);
    });

    catContainer.addEventListener('click', e => {
        if (e.target.closest('button')) {
            const selected = e.target.closest('button');
            document.querySelectorAll('.categorias button').forEach(b => b.classList.remove('active'));
            selected.classList.add('active');
            const catId = selected.dataset.cat;
            filtrarProdutos(catId);
        }
    });
}


function renderProdutos(produtos) {
    const container = document.getElementById('produtos');
    container.innerHTML = '';

    // ✅ Se for página de promoções, mostra só produtos com preço promocional
    const promoMode = window.location.search.includes('promo=1');
    if (promoMode) {
        produtos = produtos.filter(p => p.price_promo && parseFloat(p.price_promo) > 0);
    }

    produtos.forEach(p => {
        const card = document.createElement('article');
        card.className = 'card fade-in';
        card.dataset.cat = p.category_id;
        card.dataset.product = JSON.stringify(p);

        const preco = parseFloat(p.price).toFixed(2);
        let precoPromo = p.price_promo ? parseFloat(p.price_promo).toFixed(2) : null;
        let precoHTML = '';

        if (precoPromo && precoPromo < preco) {
            const desconto = Math.round(((preco - precoPromo) / preco) * 100);
            precoHTML = `
                <div class="small">
                    <span style="text-decoration:line-through; color:#999;">R$ ${preco.replace('.', ',')}</span><br>
                    <strong style="color:#e53935;">R$ ${precoPromo.replace('.', ',')}</strong>
                    <span class="promo-tag">-${desconto}%</span>
                </div>`;
        } else {
            precoHTML = `<div class="small">R$ ${preco.replace('.', ',')}</div>`;
        }

        card.innerHTML = `
            <img src="${p.image}" alt="${p.name}">
            <div class="title">${p.name}</div>
            <div class="desc">${p.description || ''}</div>
            <div class="meta">
                ${precoHTML}
                <button class="btn" data-add='${JSON.stringify(p)}'>Adicionar</button>
            </div>
        `;
        container.appendChild(card);
    });

    if (typeof initModal === 'function') initModal();
}

function filtrarProdutos(catId) {
    const cards = document.querySelectorAll('#produtos .card');
    cards.forEach(c => {
        c.style.display = (catId === 'all' || c.dataset.cat === catId) ? '' : 'none';
    });
}

function aplicarBusca(valor) {
    const termo = valor.toLowerCase();
    document.querySelectorAll('#produtos .card').forEach(card => {
        const nome = card.querySelector('.title').textContent.toLowerCase();
        const desc = card.querySelector('.desc').textContent.toLowerCase();
        card.style.display = (nome.includes(termo) || desc.includes(termo)) ? '' : 'none';
    });
}

document.getElementById('searchInput')?.addEventListener('input', e => aplicarBusca(e.target.value));
document.getElementById('searchInputMobile')?.addEventListener('input', e => aplicarBusca(e.target.value));


function getCart() {
    return JSON.parse(localStorage.getItem('carrinho')) || [];
}

function saveCart(cart) {
    localStorage.setItem('carrinho', JSON.stringify(cart));
    updateCartCount();
}

function updateCartCount() {
    const count = getCart().reduce((a,b) => a + b.qtd, 0);
    document.querySelectorAll('.cart-count, .cart-count-bot').forEach(el => el.textContent = count);
}

function cartAdd(produto) {
    let cart = getCart();


    const precoFinal = (produto.price_promo && parseFloat(produto.price_promo) > 0)
        ? parseFloat(produto.price_promo)
        : parseFloat(produto.price);


    produto.price = precoFinal;

    const index = cart.findIndex(p => p.id == produto.id);
    if (index > -1) {
        cart[index].qtd++;
    } else {
        cart.push({ ...produto, qtd: 1 });
    }

    saveCart(cart);
    showToast(`${produto.name} adicionado ao carrinho!`);
}


function showToast(msg) {
    let toast = document.createElement('div');
    toast.textContent = msg;
    toast.style.position = 'fixed';
    toast.style.bottom = '80px';
    toast.style.right = '20px';
    toast.style.background = '#e53935';
    toast.style.color = '#fff';
    toast.style.padding = '12px 16px';
    toast.style.borderRadius = '8px';
    toast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
    toast.style.zIndex = '9999';
    toast.style.opacity = '0';
    toast.style.transition = 'opacity 0.3s ease';
    document.body.appendChild(toast);
    setTimeout(() => toast.style.opacity = '1', 50);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 2000);
}

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const [catRes, prodRes] = await Promise.all([fetchCategorias(), fetchProdutos()]);
        if (catRes.success) renderCategorias(catRes.categories);
        if (prodRes.success) {
            renderProdutos(prodRes.products);
            updateCartCount();
        }

      
        document.addEventListener('click', e => {
            const btn = e.target.closest('[data-add]');
            if (btn) {
                const produto = JSON.parse(btn.dataset.add);
                cartAdd(produto);
            }
        });

    } catch (e) {
        console.error('Erro ao carregar dados:', e);
        document.getElementById('produtos').innerHTML = `<div class="alert">Erro ao carregar os dados.</div>`;
    }
});
</script>

</body>
</html>
