
function getCart() {
    return JSON.parse(localStorage.getItem('carrinho')) || [];
}


function saveCart(cart) {
    localStorage.setItem('carrinho', JSON.stringify(cart));
    updateCartCount();
    if (typeof renderCart === 'function') {
        renderCart();
    }
}


function updateCartCount() {
    const count = getCart().reduce((total, item) => total + item.qtd, 0);
    document.querySelectorAll('.cart-count, .cart-count-bot').forEach(element => {
        element.textContent = count;
    });
}


function cartAdd(product) {
    let cart = getCart();
    const index = cart.findIndex(item => item.id === product.id);
    
    if (index > -1) {
        cart[index].qtd++;
    } else {
        cart.push({ ...product, qtd: 1 });
    }
    
    saveCart(cart);
    alert(`${product.name} adicionado ao carrinho!`);
}


function changeQty(id, delta) {
    let cart = getCart();
    const index = cart.findIndex(item => item.id === id);
    
    if (index > -1) {
        cart[index].qtd += delta;
        if (cart[index].qtd <= 0) {
            cart.splice(index, 1);
        }
    }
    
    saveCart(cart);
}


function removeItem(id) {
    let cart = getCart().filter(item => item.id !== id);
    saveCart(cart);
}


function renderCart() {
    const listElement = document.getElementById('listaCarrinho');
    const totalElement = document.getElementById('totalPedido');
    const cart = getCart();
    
    if (!listElement) return;
    
    listElement.innerHTML = '';
    
    if (cart.length === 0) {
        listElement.innerHTML = `
            <div class="alert">
                <i class="bi bi-cart-x"></i><br>
                Seu carrinho está vazio.
            </div>
        `;
        if (totalElement) totalElement.textContent = '0,00';
        return;
    }
    
    let total = 0;
    
    cart.forEach((item, index) => {
        const subtotal = item.qtd * parseFloat(item.price);
        total += subtotal;
        
        const row = document.createElement('div');
        row.className = 'carrinho-item fade-in';
        row.innerHTML = `
            <div>${index + 1}</div>
            <img src="${item.image}" alt="${item.name}" onerror="this.src='imgs/produtos/1.png'">
            <div>
                <b>${item.name}</b>
                <div class="small">R$ ${parseFloat(item.price).toFixed(2).replace('.', ',')}</div>
            </div>
            <div class="qty">
                <button onclick="changeQty(${item.id}, -1)">-</button>
                <span>${item.qtd}</span>
                <button onclick="changeQty(${item.id}, 1)">+</button>
            </div>
            <div style="width:90px; text-align:right; font-weight:600;">
                R$ ${subtotal.toFixed(2).replace('.', ',')}
            </div>
            <button class="btn" style="background:#777; padding:6px 10px;" onclick="removeItem(${item.id})" title="Remover">
                <i class="bi bi-trash"></i>
            </button>
        `;
        
        listElement.appendChild(row);
    });
    
    if (totalElement) {
        totalElement.textContent = total.toFixed(2).replace('.', ',');
    }
}


function initModal() {
  const overlay = document.getElementById('modalOverlay');
  const img = document.getElementById('modalImg');
  const title = document.getElementById('modalTitle');
  const desc = document.getElementById('modalDesc');
  const priceBox = document.getElementById('modalPrice');
  const btnAdd = document.getElementById('btnAdd');
  const btnClose = document.getElementById('btnClose');


  document.querySelectorAll('.card').forEach(card => {
    card.addEventListener('click', e => {
   
      if (e.target.closest('.btn')) return;

      const produto = JSON.parse(card.dataset.product);

  
      img.src = produto.image;
      title.textContent = produto.name;
      desc.textContent = produto.description || '';

      const preco = parseFloat(produto.price).toFixed(2);
      const precoPromo = produto.price_promo ? parseFloat(produto.price_promo).toFixed(2) : null;

      // ✅ Exibe o preço promocional se existir
      if (precoPromo && precoPromo < preco) {
        const desconto = Math.round(((preco - precoPromo) / preco) * 100);
        priceBox.innerHTML = `
          <div class="small">
            <span style="text-decoration:line-through; color:#999;">R$ ${preco.replace('.', ',')}</span><br>
            <strong style="color:#e53935;">R$ ${precoPromo.replace('.', ',')}</strong>
            <span class="promo-tag">-${desconto}%</span>
          </div>`;
      } else {
        priceBox.innerHTML = `<div class="small">R$ ${preco.replace('.', ',')}</div>`;
      }


      btnAdd.onclick = () => {
        cartAdd(produto);
        overlay.style.display = 'none';
      };


      overlay.style.display = 'flex';
    });
  });


  btnClose.addEventListener('click', () => {
    overlay.style.display = 'none';
  });


  overlay.addEventListener('click', e => {
    if (e.target === overlay) overlay.style.display = 'none';
  });
}



function aplicarBusca(query) {
    query = query.trim().toLowerCase();
    
    document.querySelectorAll('#produtos .card').forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(query) ? 'block' : 'none';
    });
}


function initSearch() {
    const searchInput = document.getElementById('searchInput');
    const searchInputMobile = document.getElementById('searchInputMobile');
    
    if (searchInput) {
        searchInput.addEventListener('input', event => {
            aplicarBusca(event.target.value);
        });
    }
    
    if (searchInputMobile) {
        searchInputMobile.addEventListener('input', event => {
            aplicarBusca(event.target.value);
        });
    }
}


function initCategoryFilter() {
    const categoryButtons = document.querySelectorAll('#categorias button');
    
    categoryButtons.forEach(button => {
        button.addEventListener('click', () => {
      
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            
       
            button.classList.add('active');
            
            const category = button.dataset.cat;
            
           
            document.querySelectorAll('#produtos .card').forEach(card => {
                const show = category === 'all' || card.dataset.cat === category;
                card.style.display = show ? 'block' : 'none';
            });
        });
    });
}

function finalizarPedido() {
    const btnFinalizar = document.getElementById('btnFinalizar');
    
    if (btnFinalizar) {
        btnFinalizar.addEventListener('click', () => {
            if (getCart().length === 0) {
                alert('Seu carrinho está vazio!');
                return;
            }
            
            if (confirm('Deseja Finalizar o pedido?')) {
             
                window.location.href = 'finalizarPedido.php';
            }
        });
    }
}


function initApp() {
  
    updateCartCount();
    
    
    initModal();           
    initSearch();        
    initCategoryFilter();  
    renderCart();          
    finalizarPedido();    
    
    console.log('🚀 Sistema inicializado com sucesso!');
}



document.addEventListener('DOMContentLoaded', initApp);