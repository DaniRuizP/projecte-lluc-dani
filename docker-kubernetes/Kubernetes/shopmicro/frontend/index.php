<?php
session_start();
if(!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛒 ShopMicro</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="styles/index.css" rel="stylesheet">
</head>
<body>
<div class="page-header">
    <div class="container-main">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1><i class="fas fa-shopping-cart" style="margin-right: 10px;"></i>ShopMicro</h1>
            <div class="user-section">
                <h5>Benvingut, <span class="username"><?= htmlspecialchars($_SESSION['username']) ?></span>!</h5>
                <a href="logout.php" class="btn-logout">Tancar Sessió</a>
            </div>
        </div>
    </div>
</div>

<div class="container-main">
    <div id="status-banner" class="status-banner" style="display:none;"></div>
    
    <div style="display: grid; grid-template-columns: 1fr 320px; gap: 30px;">
        <!-- Productes -->
        <div>
            <h2 class="products-title">Catàleg de Productes</h2>
            <div class="product-grid" id="product-list"></div>
        </div>
        <!-- Carret -->
        <div>
            <div class="cart-card cart-sidebar">
                <div class="cart-header"><i class="fas fa-shopping-bag"></i> El teu carret</div>
                <div class="cart-body">
                    <ul id="cart-items" class="cart-items-list"></ul>
                    <div class="cart-total">
                        <div class="cart-total-label">Total a pagar</div>
                        <div class="cart-total-amount"><span id="cart-total">0.00</span> €</div>
                    </div>
                    <button class="btn-checkout" onclick="checkout()"><i class="fas fa-credit-card"></i> Pagar Ara</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const CURRENT_USER_ID = <?= $_SESSION['user_id'] ?>;
    let cart = [];

    async function loadProducts() {
        try {
            const response = await fetch('/api/products', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            const res = await response.json();
            const banner = document.getElementById('status-banner');
            banner.style.display = 'block';
            banner.className = 'status-banner ' + (res.source === 'REDIS' ? 'success' : 'warning');
            banner.innerHTML = `<i class="fas ${res.source === 'REDIS' ? 'fa-database' : 'fa-exclamation-triangle'}"></i> Origen de dades: <b>${res.source}</b>`;

            let html = '';
            res.data.forEach(p => {
                const isOutOfStock = p.stock <= 0;
                html += `<div class="product-card" style="display: flex; flex-direction: column; overflow: hidden;">
                            <img src="${p.image}" alt="${p.name}" style="width: 100%; height: 200px; object-fit: cover;">
                            
                            <div class="product-body" style="padding: 15px; display: flex; flex-direction: column; flex-grow: 1;">
                                <h5 class="product-name">${p.name}</h5>
                                <div class="product-price">${p.price.toFixed(2)} €</div>
                                <p class="product-stock ${isOutOfStock ? 'low' : ''}" style="margin-bottom: 15px;">
                                    <i class="fas fa-box"></i> Estoc: ${p.stock}
                                </p>
                                <button style="margin-top: auto;" class="btn-add-cart" onclick="addToCart(${p.id}, '${p.name}', ${p.price})" ${isOutOfStock ? 'disabled' : ''}>
                                    ${isOutOfStock ? '<i class="fas fa-ban"></i> Esgotat' : '<i class="fas fa-shopping-cart"></i> Afegir'}
                                </button>
                            </div>
                         </div>`;
            });
            document.getElementById('product-list').innerHTML = html;
        } catch (err) {
            console.error('Error loading products:', err);
            document.getElementById('product-list').innerHTML = '<div style="color: red;">Error al carregar productes: ' + err.message + '</div>';
        }
    }

    function addToCart(id, name, price) {
        cart.push({id, name, price});
        renderCart();
    }

    function renderCart() {
        let html = '';
        let total = 0;
        if(cart.length === 0) {
            document.getElementById('cart-items').innerHTML = '<div class="cart-empty">El carret està buit</div>';
            document.getElementById('cart-total').innerText = '0.00';
            return;
        }
        cart.forEach((item, idx) => {
            html += `<li class="cart-item">
                        <span class="cart-item-name">${item.name}</span>
                        <span><span class="cart-item-price">${item.price.toFixed(2)} €</span> <button class="btn-remove-item" onclick="cart.splice(${idx},1);renderCart()"><i class="fas fa-trash"></i></button></span>
                     </li>`;
            total += item.price;
        });
        document.getElementById('cart-items').innerHTML = html;
        document.getElementById('cart-total').innerText = total.toFixed(2);
    }

    async function checkout() {
        if(cart.length === 0) return alert('⚠️ El carret està buit!');
        
        let totalToPay = 0;
        cart.forEach(item => totalToPay += item.price);

        try {
            const response = await fetch('/api/orders', {
                method: 'POST', 
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    user_id: CURRENT_USER_ID, 
                    products: cart.map(i => i.id)
                })
            });
            const res = await response.json();
            
            if(res.status === 'OK') { 
                cart.length = 0;
                window.location.href = 'success.php?total=' + totalToPay.toFixed(2);
            } else {
                alert('❌ Error: ' + res.message);
            }
        } catch (err) {
            console.error('Error en checkout:', err);
            alert('❌ Error de connexió amb el servidor: ' + err.message);
        }
    }

    // Cargar productos cuando se carga la página
    window.addEventListener('DOMContentLoaded', loadProducts);
</script>
</body></html>
