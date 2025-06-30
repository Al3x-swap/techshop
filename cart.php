<?php
// Configuración de la base de datos (si no estás en modo desarrollo)
$isDevelopmentMode = true;

// Datos de prueba para modo desarrollo (mismos productos que en index.php)
$mockProducts = [
    [ 
        'id' => 1, 
        'name' => 'Smartphone X1 Pro', 
        'price' => 399, 
        'original_price' => 499,
        'discount' => 20,
        'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=400&fit=crop',
        'description' => 'Smartphone de última generación con cámara de 108MP',
        'rating' => 4.5,
        'reviews' => 2847,
        'category' => 'Smartphones',
        'free_shipping' => true,
        'in_stock' => true
    ],
    [ 
        'id' => 2, 
        'name' => 'Laptop Gamer Ultra', 
        'price' => 1499, 
        'original_price' => 1799,
        'discount' => 17,
        'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=400&fit=crop',
        'description' => 'Laptop para gaming de alto rendimiento RTX 4070',
        'rating' => 4.8,
        'reviews' => 1234,
        'category' => 'Laptops',
        'free_shipping' => true,
        'in_stock' => true
    ],
    [ 
        'id' => 3, 
        'name' => 'Tablet 4K Max', 
        'price' => 299, 
        'original_price' => 349,
        'discount' => 14,
        'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400&h=400&fit=crop',
        'description' => 'Tablet con resolución 4K y 128GB de almacenamiento',
        'rating' => 4.2,
        'reviews' => 892,
        'category' => 'Tablets',
        'free_shipping' => true,
        'in_stock' => false
    ],
    [ 
        'id' => 4, 
        'name' => 'Smartwatch Pro', 
        'price' => 199, 
        'original_price' => 249,
        'discount' => 20,
        'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop',
        'description' => 'Reloj inteligente con GPS y monitor cardíaco',
        'rating' => 4.6,
        'reviews' => 3456,
        'category' => 'Wearables',
        'free_shipping' => true,
        'in_stock' => true
    ],
    [ 
        'id' => 5, 
        'name' => 'Auriculares Premium', 
        'price' => 89, 
        'original_price' => 129,
        'discount' => 31,
        'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop',
        'description' => 'Auriculares con cancelación de ruido activa',
        'rating' => 4.4,
        'reviews' => 5678,
        'category' => 'Audio',
        'free_shipping' => true,
        'in_stock' => true
    ],
    [ 
        'id' => 6, 
        'name' => 'Cámara 4K Ultra', 
        'price' => 799, 
        'original_price' => 999,
        'discount' => 20,
        'image' => 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=400&h=400&fit=crop',
        'description' => 'Cámara profesional 4K con estabilización',
        'rating' => 4.7,
        'reviews' => 987,
        'category' => 'Cámaras',
        'free_shipping' => true,
        'in_stock' => true
    ]
];

// Función para obtener producto por ID
function getProductById($id, $products) {
    foreach ($products as $product) {
        if ($product['id'] == $id) {
            return $product;
        }
    }
    return null;
}

// Simular carrito desde localStorage (en desarrollo)
// En producción esto vendría de la base de datos
$cartItems = [];
$subtotal = 0;
$shipping = 0;
$tax = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - TechShop</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        /* Header Styles (igual que index.php) */
        header {
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid #e0e0e0;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            height: 70px;
            gap: 20px;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #ff6b35;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header-actions {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .account-btn, .cart-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border: none;
            background: transparent;
            color: #333;
            text-decoration: none;
            cursor: pointer;
            border-radius: 6px;
            transition: background 0.3s;
            font-size: 14px;
        }

        .account-btn:hover {
            background: #f5f5f5;
        }

        .cart-btn {
            background: #ff6b35;
            color: white;
        }

        .cart-btn:hover {
            background: #e55a2b;
        }

        /* Main Content */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .breadcrumb {
            margin-bottom: 20px;
            font-size: 14px;
            color: #666;
        }

        .breadcrumb a {
            color: #ff6b35;
            text-decoration: none;
        }

        .page-title {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cart-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
            margin-bottom: 40px;
        }

        /* Cart Items */
        .cart-items {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .cart-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
            font-weight: 600;
            color: #333;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 100px 1fr auto auto auto;
            gap: 20px;
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            align-items: center;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            cursor: pointer;
        }

        .item-name:hover {
            color: #ff6b35;
        }

        .item-description {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }

        .item-features {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .feature-tag {
            background: #e8f5e8;
            color: #2e7d2e;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
        }

        .item-price {
            font-size: 20px;
            font-weight: bold;
            color: #ff6b35;
            text-align: right;
        }

        .original-price {
            font-size: 14px;
            color: #999;
            text-decoration: line-through;
            display: block;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
        }

        .qty-btn {
            width: 35px;
            height: 35px;
            border: none;
            background: #f8f9fa;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }

        .qty-btn:hover {
            background: #e9ecef;
        }

        .qty-btn:disabled {
            cursor: not-allowed;
            opacity: 0.5;
        }

        .qty-input {
            width: 50px;
            height: 35px;
            border: none;
            text-align: center;
            font-weight: 600;
        }

        .remove-btn {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            padding: 8px;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .remove-btn:hover {
            background: #fff5f5;
            transform: scale(1.1);
        }

        /* Order Summary */
        .order-summary {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 24px;
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .summary-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            font-size: 16px;
        }

        .summary-row.total {
            font-size: 20px;
            font-weight: bold;
            color: #ff6b35;
            padding-top: 12px;
            border-top: 2px solid #e0e0e0;
            margin-top: 20px;
        }

        .promo-code {
            margin: 20px 0;
        }

        .promo-input {
            display: flex;
            gap: 8px;
            margin-top: 8px;
        }

        .promo-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .promo-btn {
            padding: 10px 16px;
            background: #ff6b35;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        .promo-btn:hover {
            background: #e55a2b;
        }

        .checkout-btn {
            width: 100%;
            padding: 16px;
            background: #ff6b35;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }

        .checkout-btn:hover {
            background: #e55a2b;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
        }

        .checkout-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .continue-shopping {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            color: #ff6b35;
            text-decoration: none;
            border: 2px solid #ff6b35;
            border-radius: 8px;
            margin-top: 16px;
            transition: all 0.3s;
        }

        .continue-shopping:hover {
            background: #ff6b35;
            color: white;
        }

        /* Empty Cart */
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .empty-cart i {
            font-size: 64px;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-cart h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 12px;
        }

        .empty-cart p {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
        }

        .shop-now-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #ff6b35;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .shop-now-btn:hover {
            background: #e55a2b;
            transform: translateY(-2px);
        }

        /* Security badges */
        .security-badges {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin-top: 20px;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 12px;
            color: #666;
        }

        .security-badge {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Loading and Messages */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        }

        .loading-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
        }

        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 6px;
            color: white;
            z-index: 10001;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        }

        .toast.success {
            background: #28a745;
        }

        .toast.error {
            background: #dc3545;
        }

        .toast.show {
            opacity: 1;
            transform: translateX(0);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .cart-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .cart-item {
                grid-template-columns: 80px 1fr;
                gap: 15px;
            }

            .item-price,
            .quantity-controls,
            .remove-btn {
                grid-column: 1 / -1;
                justify-self: start;
                margin-top: 10px;
            }

            .quantity-controls {
                justify-self: center;
            }

            .remove-btn {
                justify-self: end;
            }

            .item-image {
                width: 80px;
                height: 80px;
            }

            .page-title {
                font-size: 24px;
            }

            .order-summary {
                position: static;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <a href="index.php" class="logo">
                <i class="fas fa-bolt"></i>
                TechShop
            </a>
            
            <div class="header-actions">
                <a href="#" class="account-btn">
                    <i class="fas fa-user"></i>
                    Mi Cuenta
                </a>
                <a href="cart.php" class="cart-btn">
                    <i class="fas fa-shopping-cart"></i>
                    Carrito
                    <span id="cart-count">0</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="index.php">Inicio</a> > Carrito de Compras
        </div>

        <!-- Page Title -->
        <h1 class="page-title">
            <i class="fas fa-shopping-cart"></i>
            Mi Carrito
        </h1>

        <!-- Cart Content -->
        <div id="cart-content">
            <!-- El contenido se cargará dinámicamente -->
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loading-overlay">
        <div class="loading-content">
            <i class="fas fa-spinner fa-spin fa-2x" style="color: #ff6b35; margin-bottom: 15px;"></i>
            <p>Procesando...</p>
        </div>
    </div>

    <script>
        // Variables globales
        let isDevelopmentMode = <?php echo $isDevelopmentMode ? 'true' : 'false'; ?>;
        let products = <?php echo json_encode($mockProducts); ?>;
        let cart = {};

        // Función para obtener producto por ID
        function getProductById(id) {
            return products.find(product => product.id == id);
        }

        // Función para cargar el carrito
        function loadCart() {
            if (isDevelopmentMode) {
                cart = JSON.parse(localStorage.getItem('cart') || '{}');
            } else {
                // En producción, cargar desde la base de datos
                fetchCartFromServer();
            }
            renderCart();
        }

        // Función para renderizar el carrito
        function renderCart() {
            const cartContent = document.getElementById('cart-content');
            const cartItems = Object.keys(cart);

            if (cartItems.length === 0) {
                cartContent.innerHTML = `
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart"></i>
                        <h2>Tu carrito está vacío</h2>
                        <p>¡Agrega algunos productos increíbles a tu carrito!</p>
                        <a href="index.php" class="shop-now-btn">
                            <i class="fas fa-shopping-bag"></i>
                            Empezar a comprar
                        </a>
                    </div>
                `;
                return;
            }

            let subtotal = 0;
            let itemsHtml = '';

            cartItems.forEach(productId => {
                const product = getProductById(productId);
                const quantity = cart[productId];
                
                if (product && quantity > 0) {
                    const itemTotal = product.price * quantity;
                    subtotal += itemTotal;

                    itemsHtml += `
                        <div class="cart-item" data-product-id="${productId}">
                            <img src="${product.image}" alt="${product.name}" class="item-image" 
                                 onclick="window.location.href='producto.php?id=${productId}'"
                                 onerror="this.src='https://via.placeholder.com/100x100?text=No+Image'">
                            
                            <div class="item-details">
                                <h3 class="item-name" onclick="window.location.href='producto.php?id=${productId}'">${product.name}</h3>
                                <p class="item-description">${product.description}</p>
                                <div class="item-features">
                                    ${product.free_shipping ? '<span class="feature-tag">Envío gratis</span>' : ''}
                                    ${product.discount > 0 ? '<span class="feature-tag">Oferta</span>' : ''}
                                </div>
                            </div>
                            
                            <div class="item-price">
                                $${product.price.toFixed(2)}
                                ${product.original_price > product.price ? 
                                    `<span class="original-price">$${product.original_price.toFixed(2)}</span>` : ''
                                }
                            </div>
                            
                            <div class="quantity-controls">
                                <button class="qty-btn" onclick="updateQuantity(${productId}, ${quantity - 1})"
                                        ${quantity <= 1 ? 'disabled' : ''}>
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="qty-input" value="${quantity}" min="1" max="99"
                                       onchange="updateQuantity(${productId}, this.value)">
                                <button class="qty-btn" onclick="updateQuantity(${productId}, ${quantity + 1})"
                                        ${quantity >= 99 ? 'disabled' : ''}>
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            
                            <button class="remove-btn" onclick="removeFromCart(${productId})" 
                                    title="Eliminar producto">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            });

            const shipping = subtotal >= 500 ? 0 : 50;
            const tax = subtotal * 0.16; // 16% IVA
            const total = subtotal + shipping + tax;

            cartContent.innerHTML = `
                <div class="cart-container">
                    <div class="cart-items">
                        <div class="cart-header">
                            Productos en tu carrito (${cartItems.reduce((sum, id) => sum + cart[id], 0)} artículos)
                        </div>
                        ${itemsHtml}
                    </div>
                    
                    <div class="order-summary">
                        <h3 class="summary-title">Resumen del pedido</h3>
                        
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>$${subtotal.toFixed(2)}</span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Envío:</span>
                            <span>${shipping === 0 ? 'GRATIS' : '$' + shipping.toFixed(2)}</span>
                        </div>
                        
                        <div class="summary-row">
                            <span>IVA (16%):</span>
                            <span>$${tax.toFixed(2)}</span>
                        </div>
                        
                        <div class="summary-row total">
                            <span>Total:</span>
                            <span>$${total.toFixed(2)}</span>
                        </div>
                        
                        <div class="promo-code">
                            <label for="promo">Código de descuento:</label>
                            <div class="promo-input">
                                <input type="text" id="promo" placeholder="Ingresa tu código">
                                <button class="promo-btn" onclick="applyPromoCode()">Aplicar</button>
                            </div>
                        </div>
                        
                        <button class="checkout-btn" onclick="proceedToCheckout()">
                            <i class="fas fa-lock"></i>
                            Proceder al pago
                        </button>
                        
                        <a href="index.php" class="continue-shopping">
                            <i class="fas fa-arrow-left"></i>
                            Continuar comprando
                        </a>
                        
                        <div class="security-badges">
                            <div class="security-badge">
                                <i class="fas fa-shield-alt" style="color: #28a745;"></i>
                                <span>Pago seguro</span>
                            </div>
                            <div class="security-badge">
                                <i class="fas fa-truck" style="color: #ff6b35;"></i>
                                <span>Envío rápido</span>
                            </div>
                            <div class="security-badge">
                                <i class="fas fa-undo" style="color: #17a2b8;"></i>
                                <span>30 días devolución</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            updateCartCount();
        }

        // Función para actualizar cantidad
        function updateQuantity(productId, newQuantity) {
            newQuantity = parseInt(newQuantity);
            
            if (newQuantity < 1) {
                removeFromCart(productId);
                return;
            }
            
            if (newQuantity > 99) {
                showToast('Cantidad máxima: 99 unidades', 'error');
                return;
            }

            showLoading(true);

            setTimeout(() => {
                cart[productId] = newQuantity;
                saveCart();
                renderCart();
                showLoading(false);
                showToast('Cantidad actualizada', 'success');
            }, 500);
        }

        // Función para eliminar del carrito
        function removeFromCart(productId) {
            const product = getProductById(productId);
            
            if (confirm(`¿Estás seguro de que quieres eliminar "${product.name}" del carrito?`)) {
                showLoading(true);
                
                setTimeout(() => {
                    delete cart[productId];
                    saveCart();
                    renderCart();
                    showLoading(false);
                    showToast('Producto eliminado del carrito', 'success');
                }, 500);
            }
        }

        // Función para guardar carrito
        function saveCart() {
            if (isDevelopmentMode) {
                localStorage.setItem('cart', JSON.stringify(cart));
            } else {
                // Enviar a servidor
                fetch('api/update_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ cart: cart })
                }).catch(error => {
                    console.error('Error al guardar carrito:', error);
                    localStorage.setItem('cart', JSON.stringify(cart));
                });
            }
        }

        // Función para actualizar contador del carrito
        function updateCartCount() {
            const count = Object.values(cart).reduce((sum, qty) => sum + qty, 0);
            document.getElementById('cart-count').textContent = count;
        }

 // Función para aplicar código promocional (continuación)
        function applyPromoCode() {
            const promoInput = document.getElementById('promo');
            const code = promoInput.value.trim().toUpperCase();
            
            // Códigos de ejemplo
            const promoCodes = {
                'DESCUENTO10': 0.10,
                'BIENVENIDO': 0.15,
                'TECHSHOP20': 0.20
            };
            
            if (promoCodes[code]) {
                showToast(`¡Código aplicado! ${(promoCodes[code] * 100)}% de descuento`, 'success');
                promoInput.disabled = true;
                // Aquí podrías recalcular el total con el descuento
                applyDiscount(promoCodes[code]);
            } else if (code === '') {
                showToast('Ingresa un código promocional', 'error');
            } else {
                showToast('Código promocional no válido', 'error');
            }
        }

        // Función para aplicar descuento
        function applyDiscount(discountRate) {
            // Recalcular totales con descuento
            const cartItems = Object.keys(cart);
            let subtotal = 0;

            cartItems.forEach(productId => {
                const product = getProductById(productId);
                const quantity = cart[productId];
                if (product && quantity > 0) {
                    subtotal += product.price * quantity;
                }
            });

            const discount = subtotal * discountRate;
            const shipping = (subtotal - discount) >= 500 ? 0 : 50;
            const tax = (subtotal - discount) * 0.16;
            const total = subtotal - discount + shipping + tax;

            // Actualizar la visualización del resumen
            updateOrderSummary(subtotal, discount, shipping, tax, total);
        }

        // Función para actualizar resumen de pedido
        function updateOrderSummary(subtotal, discount = 0, shipping, tax, total) {
            const summarySection = document.querySelector('.order-summary');
            const summaryContent = summarySection.querySelector('.summary-title').nextElementSibling;
            
            let summaryHtml = `
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>$${subtotal.toFixed(2)}</span>
                </div>
            `;

            if (discount > 0) {
                summaryHtml += `
                    <div class="summary-row" style="color: #28a745;">
                        <span>Descuento:</span>
                        <span>-$${discount.toFixed(2)}</span>
                    </div>
                `;
            }

            summaryHtml += `
                <div class="summary-row">
                    <span>Envío:</span>
                    <span>${shipping === 0 ? 'GRATIS' : '$' + shipping.toFixed(2)}</span>
                </div>
                
                <div class="summary-row">
                    <span>IVA (16%):</span>
                    <span>$${tax.toFixed(2)}</span>
                </div>
                
                <div class="summary-row total">
                    <span>Total:</span>
                    <span>$${total.toFixed(2)}</span>
                </div>
            `;

            // Actualizar solo la parte de los totales
            const existingSummary = document.querySelectorAll('.summary-row');
            existingSummary.forEach(row => row.remove());
            
            const totalSection = document.querySelector('.summary-row.total');
            if (totalSection) {
                totalSection.insertAdjacentHTML('beforebegin', summaryHtml);
            }
        }

        // Función para proceder al checkout
        function proceedToCheckout() {
            const cartItems = Object.keys(cart);
            
            if (cartItems.length === 0) {
                showToast('Tu carrito está vacío', 'error');
                return;
            }

            // Verificar stock disponible
            for (let productId of cartItems) {
                const product = getProductById(productId);
                if (product && !product.in_stock) {
                    showToast(`${product.name} está agotado`, 'error');
                    return;
                }
            }

            showLoading(true);

            // Simular proceso de checkout
            setTimeout(() => {
                showLoading(false);
                
                if (isDevelopmentMode) {
                    // En modo desarrollo, mostrar alerta
                    alert('¡Funcionalidad de pago en desarrollo!\n\nTu pedido será procesado pronto.\nGracias por usar TechShop.');
                } else {
                    // En producción, redirigir a página de pago
                    window.location.href = 'checkout.php';
                }
            }, 1500);
        }

        // Función para mostrar/ocultar loading
        function showLoading(show) {
            const overlay = document.getElementById('loading-overlay');
            overlay.style.display = show ? 'flex' : 'none';
        }

        // Función para mostrar toast notifications
        function showToast(message, type = 'success') {
            // Remover toast existente si hay uno
            const existingToast = document.querySelector('.toast');
            if (existingToast) {
                existingToast.remove();
            }

            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                ${message}
            `;

            document.body.appendChild(toast);

            // Mostrar toast
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);

            // Ocultar y remover toast
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, 3000);
        }

        // Función para cargar carrito desde servidor (para producción)
        function fetchCartFromServer() {
            fetch('api/get_cart.php')
                .then(response => response.json())
                .then(data => {
                    cart = data.cart || {};
                    renderCart();
                })
                .catch(error => {
                    console.error('Error al cargar carrito:', error);
                    // Fallback a localStorage
                    cart = JSON.parse(localStorage.getItem('cart') || '{}');
                    renderCart();
                });
        }

        // Función para manejar teclas especiales
        document.addEventListener('keydown', function(e) {
            // ESC para cerrar loading overlay
            if (e.key === 'Escape') {
                showLoading(false);
            }
        });

        // Función para validar entrada en campos de cantidad
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('qty-input')) {
                let value = parseInt(e.target.value);
                if (isNaN(value) || value < 1) {
                    e.target.value = 1;
                } else if (value > 99) {
                    e.target.value = 99;
                }
            }
        });

        // Función para confirmar salir de página si hay items en carrito
        window.addEventListener('beforeunload', function(e) {
            const cartItems = Object.keys(cart);
            if (cartItems.length > 0 && !confirm('¿Estás seguro de que quieres salir? Los productos en tu carrito se mantendrán guardados.')) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Inicializar cuando la página carga
        document.addEventListener('DOMContentLoaded', function() {
            loadCart();
            
            // Actualizar carrito cada 30 segundos (para sincronizar cambios de otros dispositivos)
            if (!isDevelopmentMode) {
                setInterval(function() {
                    fetchCartFromServer();
                }, 30000);
            }
        });

        // Función para vaciar todo el carrito
        function clearCart() {
            if (confirm('¿Estás seguro de que quieres vaciar todo el carrito?')) {
                showLoading(true);
                
                setTimeout(() => {
                    cart = {};
                    saveCart();
                    renderCart();
                    showLoading(false);
                    showToast('Carrito vaciado', 'success');
                }, 500);
            }
        }

        // Función para compartir carrito (opcional)
        function shareCart() {
            const cartItems = Object.keys(cart);
            if (cartItems.length === 0) {
                showToast('Tu carrito está vacío', 'error');
                return;
            }

            let shareText = 'Mi carrito en TechShop:\n\n';
            let total = 0;

            cartItems.forEach(productId => {
                const product = getProductById(productId);
                const quantity = cart[productId];
                if (product && quantity > 0) {
                    shareText += `• ${product.name} x${quantity} - $${(product.price * quantity).toFixed(2)}\n`;
                    total += product.price * quantity;
                }
            });

            shareText += `\nTotal: $${total.toFixed(2)}`;

            if (navigator.share) {
                navigator.share({
                    title: 'Mi carrito - TechShop',
                    text: shareText,
                    url: window.location.href
                });
            } else {
                // Fallback: copiar al portapapeles
                navigator.clipboard.writeText(shareText).then(() => {
                    showToast('Carrito copiado al portapapeles', 'success');
                });
            }
        }
</script>
</body>
</html>