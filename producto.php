<?php
// Configuración de la base de datos (si no estás en modo desarrollo)
$isDevelopmentMode = true;

// Datos de prueba para modo desarrollo
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
        'in_stock' => true,
        'stock_quantity' => 15,
        'features' => [
            'Pantalla AMOLED 6.7"',
            'Cámara principal 108MP',
            'Batería 5000mAh',
            'Carga rápida 65W',
            'Procesador Snapdragon 8 Gen 2',
            '12GB RAM + 256GB Storage'
        ]
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
        'in_stock' => true,
        'stock_quantity' => 8,
        'features' => [
            'Intel Core i7-13700H',
            'NVIDIA RTX 4070 8GB',
            '32GB DDR5 RAM',
            '1TB NVMe SSD',
            'Pantalla 15.6" 165Hz',
            'Teclado RGB mecánico'
        ]
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
        'in_stock' => false,
        'stock_quantity' => 0,
        'features' => [
            'Pantalla 11" 4K IPS',
            'Procesador A15 Bionic',
            '128GB almacenamiento',
            'Cámara 12MP',
            'Batería 10 horas',
            'Compatible con Apple Pencil'
        ]
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
        'in_stock' => true,
        'stock_quantity' => 25,
        'features' => [
            'GPS integrado',
            'Monitor cardíaco 24/7',
            'Resistente al agua IP68',
            'Batería 7 días',
            'Pantalla AMOLED 1.4"',
            'Más de 100 modos deportivos'
        ]
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
        'in_stock' => true,
        'stock_quantity' => 50,
        'features' => [
            'Cancelación de ruido activa',
            'Bluetooth 5.3',
            'Batería 30 horas',
            'Carga rápida 15 min = 3 horas',
            'Audio Hi-Res certificado',
            'Controles táctiles intuitivos'
        ]
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
        'in_stock' => true,
        'stock_quantity' => 12,
        'features' => [
            'Sensor 24MP APS-C',
            'Video 4K 60fps',
            'Estabilización 5 ejes',
            'Pantalla táctil 3.2"',
            'WiFi y Bluetooth',
            'Lente 18-55mm incluido'
        ]
    ]
];

// Obtener ID del producto
$productId = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Buscar el producto
$product = null;
foreach ($mockProducts as $p) {
    if ($p['id'] == $productId) {
        $product = $p;
        break;
    }
}

// Si no se encuentra el producto, redirigir
if (!$product) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - TechShop</title>
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

        /* Header Styles */
        header {
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
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

        .cart-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #ff6b35;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s;
        }

        .cart-btn:hover {
            background: #e55a2b;
        }

        .cart-count {
            background: #fff;
            color: #ff6b35;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            font-weight: bold;
            min-width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Main Content */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Breadcrumb */
        .breadcrumb {
            margin-bottom: 20px;
            font-size: 14px;
            color: #666;
        }

        .breadcrumb a {
            color: #ff6b35;
            text-decoration: none;
        }

        /* Product Detail */
        .product-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .product-images {
            position: relative;
        }

        .main-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .product-info h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 15px;
            line-height: 1.3;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .stars {
            display: flex;
            gap: 2px;
        }

        .star {
            color: #ffc107;
            font-size: 16px;
        }

        .star.empty {
            color: #e0e0e0;
        }

        .rating-text {
            color: #666;
            font-size: 14px;
        }

        .price-section {
            margin-bottom: 25px;
        }

        .current-price {
            font-size: 36px;
            font-weight: bold;
            color: #ff6b35;
            margin-right: 15px;
        }

        .original-price {
            font-size: 24px;
            color: #999;
            text-decoration: line-through;
        }

        .discount-badge {
            display: inline-block;
            background: #ff4757;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            margin-left: 10px;
        }

        .product-description {
            font-size: 16px;
            color: #666;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .features-list {
            margin-bottom: 25px;
        }

        .features-list h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
        }

        .features-list ul {
            list-style: none;
        }

        .features-list li {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .features-list li:last-child {
            border-bottom: none;
        }

        .features-list i {
            color: #28a745;
            width: 16px;
        }

        .stock-info {
            margin-bottom: 25px;
            padding: 15px;
            background: #e8f5e8;
            border-radius: 8px;
            border-left: 4px solid #28a745;
        }

        .stock-info.out-of-stock {
            background: #fff5f5;
            border-left-color: #ff4757;
        }

        .stock-info .stock-text {
            font-weight: 600;
            color: #28a745;
        }

        .stock-info.out-of-stock .stock-text {
            color: #ff4757;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .quantity-selector label {
            font-weight: 600;
            color: #333;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            border: 2px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .quantity-btn {
            width: 40px;
            height: 40px;
            border: none;
            background: #f8f9fa;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }

        .quantity-btn:hover {
            background: #e9ecef;
        }

        .quantity-btn:disabled {
            background: #f8f9fa;
            color: #ccc;
            cursor: not-allowed;
        }

        .quantity-input {
            width: 60px;
            height: 40px;
            border: none;
            text-align: center;
            font-size: 16px;
            font-weight: 600;
        }

        .add-to-cart-section {
            display: flex;
            gap: 15px;
        }

        .add-to-cart-btn {
            flex: 1;
            background: #ff6b35;
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .add-to-cart-btn:hover:not(:disabled) {
            background: #e55a2b;
            transform: translateY(-2px);
        }

        .add-to-cart-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .wishlist-btn {
            width: 50px;
            height: 50px;
            border: 2px solid #ff6b35;
            background: white;
            color: #ff6b35;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .wishlist-btn:hover {
            background: #ff6b35;
            color: white;
        }

        .wishlist-btn.active {
            background: #ff6b35;
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .product-detail {
                grid-template-columns: 1fr;
                gap: 30px;
                padding: 20px;
            }

            .main-image {
                height: 300px;
            }

            .product-info h1 {
                font-size: 24px;
            }

            .current-price {
                font-size: 28px;
            }

            .add-to-cart-section {
                flex-direction: column;
            }

            .wishlist-btn {
                width: 100%;
                height: 50px;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .product-detail {
            animation: fadeInUp 0.6s ease-out;
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
                <a href="index.php" class="nav-link">
                    <i class="fas fa-arrow-left"></i>
                    Volver a productos
                </a>
                <a href="cart.php" class="cart-btn">
                    <i class="fas fa-shopping-cart"></i>
                    Carrito
                    <span class="cart-count" id="cart-count">0</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="index.php">Inicio</a> > 
            <a href="index.php"><?php echo htmlspecialchars($product['category']); ?></a> > 
            <?php echo htmlspecialchars($product['name']); ?>
        </div>

        <!-- Product Detail -->
        <div class="product-detail">
            <div class="product-images">
                <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="main-image">
            </div>
            
            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                
                <div class="product-rating">
                    <div class="stars">
                        <?php 
                        $rating = $product['rating'];
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= floor($rating)) {
                                echo '<i class="fas fa-star star"></i>';
                            } elseif ($i - 0.5 <= $rating) {
                                echo '<i class="fas fa-star-half-alt star"></i>';
                            } else {
                                echo '<i class="far fa-star star empty"></i>';
                            }
                        }
                        ?>
                    </div>
                    <span class="rating-text"><?php echo $product['rating']; ?> (<?php echo number_format($product['reviews']); ?> reseñas)</span>
                </div>
                
                <div class="price-section">
                    <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
                    <?php if (isset($product['original_price']) && $product['original_price'] > $product['price']): ?>
                        <span class="original-price">$<?php echo number_format($product['original_price'], 2); ?></span>
                        <span class="discount-badge">-<?php echo $product['discount']; ?>%</span>
                    <?php endif; ?>
                </div>
                
                <p class="product-description">
                    <?php echo htmlspecialchars($product['description']); ?>
                </p>
                
                <div class="features-list">
                    <h3>Características principales:</h3>
                    <ul>
                        <?php foreach ($product['features'] as $feature): ?>
                            <li>
                                <i class="fas fa-check"></i>
                                <?php echo htmlspecialchars($feature); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="stock-info <?php echo !$product['in_stock'] ? 'out-of-stock' : ''; ?>">
                    <?php if ($product['in_stock']): ?>
                        <div class="stock-text">
                            <i class="fas fa-check-circle"></i>
                            En stock - <?php echo $product['stock_quantity']; ?> disponibles
                        </div>
                        <?php if ($product['free_shipping']): ?>
                            <div style="margin-top: 5px; color: #28a745;">
                                <i class="fas fa-truck"></i>
                                Envío gratuito
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="stock-text">
                            <i class="fas fa-times-circle"></i>
                            Producto agotado
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($product['in_stock']): ?>
                    <div class="quantity-selector">
                        <label for="quantity">Cantidad:</label>
                        <div class="quantity-controls">
                            <button type="button" class="quantity-btn" onclick="changeQuantity(-1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" id="quantity" class="quantity-input" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                            <button type="button" class="quantity-btn" onclick="changeQuantity(1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="add-to-cart-section">
                    <button 
                        class="add-to-cart-btn" 
                        onclick="addToCart(<?php echo $product['id']; ?>)"
                        <?php echo !$product['in_stock'] ? 'disabled' : ''; ?>
                    >
                        <i class="fas fa-shopping-cart"></i>
                        <?php echo $product['in_stock'] ? 'Agregar al carrito' : 'Producto agotado'; ?>
                    </button>
                    
                    <button class="wishlist-btn" onclick="toggleWishlist(<?php echo $product['id']; ?>)">
                        <i class="far fa-heart"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let isDevelopmentMode = <?php echo $isDevelopmentMode ? 'true' : 'false'; ?>;
        let maxStock = <?php echo $product['stock_quantity']; ?>;

        // Función para cambiar cantidad
        function changeQuantity(delta) {
            const quantityInput = document.getElementById('quantity');
            let currentQuantity = parseInt(quantityInput.value);
            let newQuantity = currentQuantity + delta;
            
            if (newQuantity >= 1 && newQuantity <= maxStock) {
                quantityInput.value = newQuantity;
            }
            
            // Actualizar estado de botones
            updateQuantityButtons();
        }

        function updateQuantityButtons() {
            const quantityInput = document.getElementById('quantity');
            const quantity = parseInt(quantityInput.value);
            const minusBtn = document.querySelector('.quantity-btn:first-child');
            const plusBtn = document.querySelector('.quantity-btn:last-child');
            
            minusBtn.disabled = quantity <= 1;
            plusBtn.disabled = quantity >= maxStock;
        }

        // Función para agregar al carrito
        async function addToCart(productId) {
            const button = document.querySelector('.add-to-cart-btn');
            const originalText = button.innerHTML;
            const quantity = parseInt(document.getElementById('quantity').value);
            
            // Deshabilitar botón temporalmente
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Agregando...';
            
            try {
                if (isDevelopmentMode) {
                    // Modo desarrollo: usar localStorage
                    const cart = JSON.parse(localStorage.getItem('cart') || '{}');
                    cart[productId] = (cart[productId] || 0) + quantity;
                    localStorage.setItem('cart', JSON.stringify(cart));
                    
                    // Simular delay
                    await new Promise(resolve => setTimeout(resolve, 800));
                    
                    // Actualizar UI
                    updateCartCount();
                    
                    button.innerHTML = '<i class="fas fa-check"></i> Agregado al carrito';
                    button.style.background = '#28a745';
                    
                    showToast(`${quantity} producto(s) agregado(s) al carrito`, 'success');
                    
                } else {
                    // Modo producción: usar base de datos
                    const response = await fetch('api/add_to_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ 
                            product_id: productId,
                            quantity: quantity
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        updateCartCount();
                        button.innerHTML = '<i class="fas fa-check"></i> Agregado al carrito';
                        button.style.background = '#28a745';
                        showToast(`${quantity} producto(s) agregado(s) al carrito`, 'success');
                    } else {
                        throw new Error(data.message || 'Error al agregar al carrito');
                    }
                }
                
            } catch (error) {
                console.error('Error:', error);
                button.innerHTML = '<i class="fas fa-times"></i> Error';
                button.style.background = '#dc3545';
                showToast('Error al agregar al carrito', 'error');
            } finally {
                // Restaurar botón después de 3 segundos
                setTimeout(() => {
                    button.disabled = false;
                    button.innerHTML = originalText;
                    button.style.background = '#ff6b35';
                }, 3000);
            }
        }

        // Función para toggle wishlist
        function toggleWishlist(productId) {
            const button = document.querySelector('.wishlist-btn');
            const icon = button.querySelector('i');
            
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                button.classList.add('active');
                
                // Guardar en localStorage
                const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
                if (!wishlist.includes(productId)) {
                    wishlist.push(productId);
                    localStorage.setItem('wishlist', JSON.stringify(wishlist));
                }
                
                showToast('Producto agregado a favoritos', 'success');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                button.classList.remove('active');
                
                // Remover de localStorage
                const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
                const index = wishlist.indexOf(productId);
                if (index > -1) {
                    wishlist.splice(index, 1);
                    localStorage.setItem('wishlist', JSON.stringify(wishlist));
                }
                
                showToast('Producto removido de favoritos', 'success');
            }
        }

        // Función para actualizar contador del carrito
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart') || '{}');
            const count = Object.values(cart).reduce((sum, qty) => sum + qty, 0);
            document.getElementById('cart-count').textContent = count;
        }

        // Función para mostrar notificaciones toast
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#28a745' : '#dc3545'};
                color: white;
                padding: 12px 20px;
                border-radius: 6px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 10000;
                opacity: 0;
                transform: translateX(100%);
                transition: all 0.3s ease;
            `;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            // Animar entrada
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(0)';
            }, 100);
            
            // Remover después de 3 segundos
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

      // Cargar wishlist al iniciar
        function loadWishlist() {
            const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
            const productId = <?php echo $product['id']; ?>;
            
            if (wishlist.includes(productId)) {
                const button = document.querySelector('.wishlist-btn');
                const icon = button.querySelector('i');
                icon.classList.remove('far');
                icon.classList.add('fas');
                button.classList.add('active');
            }
        }

        // Validar entrada de cantidad
        document.getElementById('quantity').addEventListener('input', function() {
            let value = parseInt(this.value);
            
            if (isNaN(value) || value < 1) {
                this.value = 1;
            } else if (value > maxStock) {
                this.value = maxStock;
            }
            
            updateQuantityButtons();
        });

        // Inicializar cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
            loadWishlist();
            updateQuantityButtons();
        });
    </script>
</body>
</html>