<?php
require_once 'config/database.php';
require_once 'includes/session.php';

$user = getUserInfo(); // null si no está logueado

// Verificar si el usuario es admin para mostrar el enlace
$isAdmin = isAdmin(); // Debes implementar esta función en session.php

// Mostrar mensaje si el login fue exitoso
if (isset($_GET['login']) && $_GET['login'] === 'success' && $user) {
    $success_message = '¡Inicio de sesión exitoso! Bienvenido/a, ' . htmlspecialchars($user['nombre']) . '!';
}
?>

<?php
// Configuración de la base de datos (si no estás en modo desarrollo)
// index.php
$isDevelopmentMode = true;

// Datos de prueba para modo dessarrollo
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
        'reserved_stock' => 2,
        'last_updated' => time(),
        'low_stock_threshold' => 5
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
        'reserved_stock' => 1,
        'last_updated' => time(),
        'low_stock_threshold' => 3
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
        'reserved_stock' => 0,
        'last_updated' => time(),
        'low_stock_threshold' => 2
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
                'stock_quantity' => 22,
        'reserved_stock' => 5,
        'last_updated' => time(),
        'low_stock_threshold' => 5
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
        'stock_quantity' => 6,
        'reserved_stock' => 1,
        'last_updated' => time(),
        'low_stock_threshold' => 2
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
        'stock_quantity' => 6,
        'reserved_stock' => 1,
        'last_updated' => time(),
        'low_stock_threshold' => 2
    ]
    
];

// Si hay búsqueda, filtrar productos
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$filteredProducts = $mockProducts;

if ($searchTerm) {
    $filteredProducts = array_filter($mockProducts, function($product) use ($searchTerm) {
        return stripos($product['name'], $searchTerm) !== false || 
               stripos($product['description'], $searchTerm) !== false;
    });
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TechShop - Electrónica y Tecnología</title>
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

    .logo i {
      color: #ff6b35;
    }

    .search-container {
      flex: 1;
      max-width: 600px;
      position: relative;
    }

    .search-form {
      display: flex;
      border: 2px solid #ff6b35;
      border-radius: 8px;
      overflow: hidden;
      background: white;
    }

    .search-bar {
      flex: 1;
      padding: 12px 16px;
      border: none;
      outline: none;
      font-size: 16px;
    }

    .search-btn {
      background: #ff6b35;
      border: none;
      padding: 12px 20px;
      color: white;
      cursor: pointer;
      transition: background 0.3s;
    }

    .search-btn:hover {
      background: #e55a2b;
    }

    .header-actions {
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

    .account-btn:hover, .cart-btn:hover {
      background: #f5f5f5;
    }

    .cart-btn {
      position: relative;
      background: #ff6b35;
      color: white;
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

    /* Navigation */
    .nav-container {
      background: #232f3e;
      color: white;
    }

    .nav-content {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      display: flex;
      align-items: center;
      height: 45px;
      gap: 30px;
    }

    .nav-link {
      color: white;
      text-decoration: none;
      font-size: 14px;
      padding: 8px 12px;
      border-radius: 4px;
      transition: background 0.3s;
    }

    .nav-link:hover {
      background: rgba(255,255,255,0.1);
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

    /* Filters */
    .filters-container {
      background: white;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .filters-title {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 15px;
      color: #333;
    }

    .filter-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .filter-tag {
      padding: 6px 12px;
      background: #f0f0f0;
      border: 1px solid #ddd;
      border-radius: 20px;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.3s;
    }

    .filter-tag:hover, .filter-tag.active {
      background: #ff6b35;
      color: white;
      border-color: #ff6b35;
    }

    /* Products Grid */
    .products-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .results-count {
      font-size: 16px;
      color: #666;
    }

    .sort-container {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .sort-select {
      padding: 8px 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      background: white;
      font-size: 14px;
    }

    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
      margin-bottom: 40px;
    }

    .product-card {
      background: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      cursor: pointer;
      position: relative;
    }

    .product-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .product-image-container {
      position: relative;
      width: 100%;
      height: 250px;
      overflow: hidden;
    }

    .product-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .product-card:hover .product-image {
      transform: scale(1.05);
    }

    .discount-badge {
      position: absolute;
      top: 10px;
      left: 10px;
      background: #ff4757;
      color: white;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: bold;
    }

    .wishlist-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: rgba(255,255,255,0.9);
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s;
      color: #666;
    }

    .wishlist-btn:hover {
      background: white;
      color: #ff4757;
      transform: scale(1.1);
    }

    .product-info {
      padding: 16px;
    }

    .product-title {
      font-size: 16px;
      font-weight: 600;
      color: #333;
      margin-bottom: 8px;
      line-height: 1.4;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .product-description {
      font-size: 14px;
      color: #666;
      margin-bottom: 12px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .product-rating {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 12px;
    }

    .stars {
      display: flex;
      gap: 2px;
    }

    .star {
      color: #ffc107;
      font-size: 14px;
    }

    .star.empty {
      color: #e0e0e0;
    }

    .rating-text {
      font-size: 14px;
      color: #666;
    }

    .price-container {
      margin-bottom: 16px;
    }

    .current-price {
      font-size: 24px;
      font-weight: bold;
      color: #ff6b35;
    }

    .original-price {
      font-size: 16px;
      color: #999;
      text-decoration: line-through;
      margin-left: 8px;
    }

    .product-features {
      margin-bottom: 16px;
    }

    .feature-tag {
      display: inline-block;
      background: #e8f5e8;
      color: #2e7d2e;
      padding: 2px 8px;
      border-radius: 12px;
      font-size: 12px;
      margin-right: 6px;
      margin-bottom: 4px;
    }

    .stock-status {
      font-size: 14px;
      margin-bottom: 12px;
    }

    .in-stock {
      color: #2e7d2e;
    }

    .out-of-stock {
      color: #ff4757;
    }

    .product-actions {
      display: flex;
      gap: 8px;
    }

    .add-to-cart {
      flex: 1;
      background: #ff6b35;
      color: white;
      border: none;
      padding: 12px 16px;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .add-to-cart:hover:not(:disabled) {
      background: #e55a2b;
      transform: translateY(-1px);
    }

    .add-to-cart:disabled {
      background: #ccc;
      cursor: not-allowed;
    }

    .quick-view {
      padding: 12px;
      background: transparent;
      border: 2px solid #ff6b35;
      color: #ff6b35;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.3s;
    }

    .quick-view:hover {
      background: #ff6b35;
      color: white;
    }

    /* Loading and Messages */
    .loading-indicator {
      text-align: center;
      padding: 40px;
      color: #666;
      display: none;
    }

    .loading-indicator.show {
      display: block;
    }

    .error-message {
      background: #fff5f5;
      color: #c53030;
      padding: 16px;
      border-radius: 8px;
      margin: 20px 0;
      border-left: 4px solid #fc8181;
      display: none;
    }

    .error-message.show {
      display: block;
    }

    .no-products {
      text-align: center;
      padding: 60px 20px;
      color: #666;
    }

    .no-products i {
      font-size: 64px;
      color: #ddd;
      margin-bottom: 20px;
    }

    /* Modal de Vista Rápida */
    .quick-view-modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 2000;
      display: flex;
      justify-content: center;
      align-items: center;
      display: none;
    }

    .modal-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
    }

    .modal-content {
      position: relative;
      background: white;
      border-radius: 8px;
      width: 90%;
      max-width: 900px;
      max-height: 90vh;
      overflow-y: auto;
      z-index: 2001;
      animation: modalFadeIn 0.3s ease-out;
    }

    .modal-close {
      position: absolute;
      top: 15px;
      right: 15px;
      background: transparent;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: #666;
      z-index: 10;
    }

    .modal-close:hover {
      color: #ff4757;
    }

    .modal-body {
      padding: 30px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
    }

    .modal-product-image {
      width: 100%;
      height: 400px;
      object-fit: contain;
      border-radius: 8px;
      background: #f5f5f5;
    }

    .modal-product-info {
      display: flex;
      flex-direction: column;
    }

    .modal-product-title {
      font-size: 24px;
      margin-bottom: 15px;
      color: #333;
    }

    .modal-product-price {
      font-size: 28px;
      color: #ff6b35;
      margin-bottom: 15px;
    }

    .modal-product-original-price {
      font-size: 20px;
      color: #999;
      text-decoration: line-through;
      margin-left: 10px;
    }

    .modal-product-description {
      margin-bottom: 20px;
      line-height: 1.6;
    }

    .modal-product-features {
      margin-bottom: 20px;
    }

    .modal-feature {
      display: flex;
      align-items: center;
      margin-bottom: 8px;
    }

    .modal-feature i {
      margin-right: 8px;
      color: #ff6b35;
    }

    .modal-stock-status {
      font-size: 16px;
      margin-bottom: 20px;
      padding: 8px 12px;
      border-radius: 4px;
      display: inline-block;
    }

    .modal-in-stock {
      background: #e8f5e8;
      color: #2e7d2e;
    }

    .modal-out-of-stock {
      background: #fff5f5;
      color: #ff4757;
    }

    .modal-actions {
      display: flex;
      gap: 15px;
      margin-top: auto;
    }

    .modal-add-to-cart {
      flex: 1;
      background: #ff6b35;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 6px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
    }

    .modal-add-to-cart:hover:not(:disabled) {
      background: #e55a2b;
    }

    .modal-add-to-cart:disabled {
      background: #ccc;
      cursor: not-allowed;
    }

    @keyframes modalFadeIn {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .header-container {
        flex-direction: column;
        height: auto;
        padding: 15px 20px;
        gap: 15px;
      }

      .search-container {
        order: 3;
        width: 100%;
        max-width: none;
      }

      .header-actions {
        order: 2;
        justify-content: space-between;
        width: 100%;
      }

      .nav-content {
        flex-wrap: wrap;
        height: auto;
        padding: 10px 20px;
        gap: 15px;
      }

      .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
      }

      .filters-container {
        padding: 15px;
      }

      .filter-tags {
        gap: 8px;
      }

      .filter-tag {
        font-size: 12px;
        padding: 4px 8px;
      }

      .modal-body {
        grid-template-columns: 1fr;
        padding: 20px;
      }
      
      .modal-product-image {
        height: 300px;
      }
    }

    @media (max-width: 480px) {
      .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
      }

      .main-container {
        padding: 15px;
      }

      .product-info {
        padding: 12px;
      }

      .current-price {
        font-size: 20px;
      }
    }

    /* Animations */
    @keyframes slideInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .product-card {
      animation: slideInUp 0.6s ease-out;
    }

    .product-card:nth-child(even) {
      animation-delay: 0.1s;
    }

    .product-card:nth-child(odd) {
      animation-delay: 0.2s;
    }
  </style>
</head>
<body>
  <body>
<div class="top-bar" style="text-align: right; padding: 10px; background: #f0f0f0;">
    <?php if (isLoggedIn()): ?>
        <span>Hola, <?php echo htmlspecialchars($user['nombre']); ?></span>
        |
        <a href="account.php">Mi Cuenta</a>
        |
        <?php if (isAdmin()): ?>
            <a href="admin/" style="color: #ff6b35; font-weight: bold;">Panel Admin</a>
            |
        <?php endif; ?>
        <a href="logout.php" style="color: red;">Cerrar sesión</a>
    <?php else: ?>
        <a href="login.php" style="font-weight: bold;">Iniciar sesión / Registrarse</a>
    <?php endif; ?>
</div>
  <!-- Header -->
  <header>
    <div class="header-container">
      <a href="index.php" class="logo">
        <i class="fas fa-bolt"></i>
        TechShop
      </a>
      
      <div class="search-container">
        <form method="GET" action="index.php" class="search-form">
          <input 
            type="text" 
            name="search" 
            class="search-bar" 
            placeholder="Buscar productos, marcas y más..." 
            value="<?php echo htmlspecialchars($searchTerm); ?>"
          />
          <button type="submit" class="search-btn">
            <i class="fas fa-search"></i>
          </button>
        </form>
      </div>
      
      <div class="header-actions">
        <a href="#" class="account-btn">
          <i class="fas fa-user"></i>
          Mi Cuenta
        </a>
        <button class="cart-btn" onclick="window.location.href='cart.php'">
          <i class="fas fa-shopping-cart"></i>
          Carrito
          <span class="cart-count" id="cart-count">0</span>
        </button>
      </div>
    </div>
  </header>

  <!-- Navigation -->
  <nav class="nav-container">
    <div class="nav-content">
      <a href="#" class="nav-link"><i class="fas fa-mobile-alt"></i> Smartphones</a>
      <a href="#" class="nav-link"><i class="fas fa-laptop"></i> Laptops</a>
      <a href="#" class="nav-link"><i class="fas fa-tablet-alt"></i> Tablets</a>
      <a href="#" class="nav-link"><i class="fas fa-headphones"></i> Audio</a>
      <a href="#" class="nav-link"><i class="fas fa-camera"></i> Cámaras</a>
      <a href="#" class="nav-link"><i class="fas fa-gamepad"></i> Gaming</a>
      <a href="#" class="nav-link"><i class="fas fa-tags"></i> Ofertas</a>
    </div>
  </nav>

  <?php if (isset($success_message)): ?>
    <div class="alert alert-success" style="
        margin: 10px auto;
        max-width: 1200px;
        padding: 15px;
        border-radius: 8px;
        background: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
        display: flex;
        align-items: center;
        gap: 10px;
    ">
        <i class="fas fa-check-circle"></i>
        <?php echo $success_message; ?>
    </div>
<?php endif; ?>

  <!-- Main Content -->
  <div class="main-container">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
      <a href="index.php">Inicio</a> > 
      <?php if ($searchTerm): ?>
        Resultados para "<?php echo htmlspecialchars($searchTerm); ?>"
      <?php else: ?>
        Todos los productos
      <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="filters-container">
      <div class="filters-title">Filtrar por categoría</div>
      <div class="filter-tags">
        <div class="filter-tag active" data-category="all">Todos</div>
        <div class="filter-tag" data-category="Smartphones">Smartphones</div>
        <div class="filter-tag" data-category="Laptops">Laptops</div>
        <div class="filter-tag" data-category="Tablets">Tablets</div>
        <div class="filter-tag" data-category="Audio">Audio</div>
        <div class="filter-tag" data-category="Cámaras">Cámaras</div>
        <div class="filter-tag" data-category="Wearables">Wearables</div>
      </div>
    </div>

    <!-- Products Header -->
    <div class="products-header">
      <div class="results-count">
        <?php echo count($filteredProducts); ?> productos encontrados
      </div>
      <div class="sort-container">
        <label for="sort">Ordenar por:</label>
        <select id="sort" class="sort-select">
          <option value="relevance">Más relevantes</option>
          <option value="price-low">Precio: menor a mayor</option>
          <option value="price-high">Precio: mayor a menor</option>
          <option value="rating">Mejor valorados</option>
          <option value="newest">Más nuevos</option>
        </select>
      </div>
    </div>

    <!-- Loading Indicator -->
    <div class="loading-indicator" id="loading">
      <i class="fas fa-spinner fa-spin"></i>
      Cargando productos...
    </div>
    
    <!-- Error Message -->
    <div class="error-message" id="error-message">
      Error al cargar los productos. Por favor, intenta nuevamente.
    </div>

    <!-- Products Grid -->
    <div class="products-grid" id="products">
      <?php foreach ($filteredProducts as $product): ?>
        <div class="product-card" data-category="<?php echo $product['category']; ?>">
          <div class="product-image-container" onclick="window.location.href='producto.php?id=<?php echo $product['id']; ?>'">
            <?php if (isset($product['discount']) && $product['discount'] > 0): ?>
              <div class="discount-badge">-<?php echo $product['discount']; ?>%</div>
            <?php endif; ?>
            <button class="wishlist-btn" onclick="event.stopPropagation(); toggleWishlist(<?php echo $product['id']; ?>)">
              <i class="far fa-heart"></i>
            </button>
            <img 
              src="<?php echo $product['image']; ?>" 
              class="product-image" 
              alt="<?php echo htmlspecialchars($product['name']); ?>"
              loading="lazy"
              onerror="this.src='https://via.placeholder.com/400x400?text=No+Image'"
            >
          </div>
          
          <div class="product-info">
            <h3 class="product-title" onclick="window.location.href='producto.php?id=<?php echo $product['id']; ?>'">
              <?php echo htmlspecialchars($product['name']); ?>
            </h3>
            
            <p class="product-description">
              <?php echo htmlspecialchars($product['description']); ?>
            </p>
            
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
              <span class="rating-text">(<?php echo number_format($product['reviews']); ?>)</span>
            </div>
            
            <div class="price-container">
              <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
              <?php if (isset($product['original_price']) && $product['original_price'] > $product['price']): ?>
                <span class="original-price">$<?php echo number_format($product['original_price'], 2); ?></span>
              <?php endif; ?>
            </div>
            
            <div class="product-features">
              <?php if ($product['free_shipping']): ?>
                <span class="feature-tag">Envío gratis</span>
              <?php endif; ?>
              <?php if (isset($product['discount']) && $product['discount'] > 0): ?>
                <span class="feature-tag">Oferta</span>
              <?php endif; ?>
            </div>
            
            <div class="stock-status <?php echo $product['in_stock'] ? 'in-stock' : 'out-of-stock'; ?>">
              <?php echo $product['in_stock'] ? 'En stock' : 'Agotado'; ?>
            </div>
            
            <div class="product-actions">
              <button 
                class="add-to-cart" 
                onclick="event.stopPropagation(); addToCart(<?php echo $product['id']; ?>)"
                <?php echo !$product['in_stock'] ? 'disabled' : ''; ?>
              >
                <i class="fas fa-shopping-cart"></i>
                <?php echo $product['in_stock'] ? 'Agregar al carrito' : 'Agotado'; ?>
              </button>
              <button class="quick-view" onclick="event.stopPropagation(); quickView(<?php echo $product['id']; ?>)">
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    
    <?php if (empty($filteredProducts)): ?>
      <div class="no-products">
        <i class="fas fa-search"></i>
        <h2>No se encontraron productos</h2>
        <p>Intenta con otro término de búsqueda o explora nuestras categorías</p>
      </div>
    <?php endif; ?>
  </div>

  <!-- Modal de Vista Rápida -->
  <div class="quick-view-modal" id="quick-view-modal">
    <div class="modal-overlay" onclick="closeQuickView()"></div>
    <div class="modal-content">
      <button class="modal-close" onclick="closeQuickView()">
        <i class="fas fa-times"></i>
      </button>
      <div class="modal-body" id="quick-view-content">
        <!-- Contenido se cargará dinámicamente -->
      </div>
    </div>
  </div>

  <script>
    // Variables globales
    let isDevelopmentMode = <?php echo $isDevelopmentMode ? 'true' : 'false'; ?>;
    let searchTimeout;

    // Función para obtener el conteo del carrito
    async function fetchCartCount() {
      if (isDevelopmentMode) {
        const cart = JSON.parse(localStorage.getItem('cart') || '{}');
        const count = Object.values(cart).reduce((sum, qty) => sum + qty, 0);
        document.getElementById('cart-count').textContent = count;
        return;
      }
      
      try {
        const response = await fetch('api/get_cart_count.php');
        const data = await response.json();
        
        if (data.success) {
          document.getElementById('cart-count').textContent = data.count;
        }
      } catch (error) {
        console.error('Error al obtener conteo del carrito:', error);
        const cart = JSON.parse(localStorage.getItem('cart') || '{}');
        const count = Object.values(cart).reduce((sum, qty) => sum + qty, 0);
        document.getElementById('cart-count').textContent = count;
      }
    }

    function showLoading(show) {
      const loading = document.getElementById('loading');
      if (show) {
        loading.classList.add('show');
      } else {
        loading.classList.remove('show');
      }
    }

    function showError(message) {
      const errorDiv = document.getElementById('error-message');
      errorDiv.textContent = message;
      errorDiv.classList.add('show');
    }

    function hideError() {
      const errorDiv = document.getElementById('error-message');
      errorDiv.classList.remove('show');
    }

    // Función para agregar al carrito
// Función para agregar al carrito
async function addToCart(productId) {
    const button = event.target.closest('.add-to-cart');
    const originalText = button.innerHTML;
    
    // Deshabilitar botón temporalmente
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Agregando...';
    
    try {
        // Enviar datos al servidor
        const response = await fetch('acciones/agregar_carrito.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                product_id: productId,
                quantity: 1 
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Actualizar conteo del carrito
            const cartCount = document.getElementById('cart-count');
            cartCount.style.transform = 'scale(1.5)';
            cartCount.textContent = data.cart_count;
            
            setTimeout(() => {
                cartCount.style.transform = 'scale(1)';
            }, 300);
            
            // Mostrar notificación
            showToast('Producto agregado al carrito');
            
            button.innerHTML = '<i class="fas fa-check"></i> Agregado';
            button.style.background = '#28a745';
        } else {
            throw new Error(data.message || 'Error al agregar al carrito');
        }
    } catch (error) {
        console.error('Error:', error);
        button.innerHTML = '<i class="fas fa-times"></i> Error';
        button.style.background = '#dc3545';
        showToast(error.message, 'error');
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
      const button = event.target.closest('.wishlist-btn');
      const icon = button.querySelector('i');
      
      if (icon.classList.contains('far')) {
        icon.classList.remove('far');
        icon.classList.add('fas');
        button.style.color = '#ff4757';
        
        // Guardar en localStorage
        const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
        if (!wishlist.includes(productId)) {
          wishlist.push(productId);
          localStorage.setItem('wishlist', JSON.stringify(wishlist));
        }
      } else {
        icon.classList.remove('fas');
        icon.classList.add('far');
        button.style.color = '#666';
        
        // Remover de localStorage
        const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
        const index = wishlist.indexOf(productId);
        if (index > -1) {
          wishlist.splice(index, 1);
          localStorage.setItem('wishlist', JSON.stringify(wishlist));
        }
      }
    }

    // Función para vista rápida
    async function quickView(productId) {
      event.stopPropagation();
      
      // Mostrar modal y overlay
      const modal = document.getElementById('quick-view-modal');
      modal.style.display = 'block';
      document.body.style.overflow = 'hidden';
      
      // Mostrar loader
      const modalContent = document.getElementById('quick-view-content');
      modalContent.innerHTML = `
        <div style="width:100%;height:400px;display:flex;justify-content:center;align-items:center;">
          <i class="fas fa-spinner fa-spin" style="font-size:40px;color:#ff6b35;"></i>
        </div>
      `;
      
      try {
        // En modo desarrollo, usar los datos mock
        if (isDevelopmentMode) {
          const product = <?php echo json_encode($mockProducts); ?>.find(p => p.id == productId);
          
          if (product) {
            // Construir HTML del producto
            modalContent.innerHTML = `
              <div class="modal-product-image-container">
                <img src="${product.image}" class="modal-product-image" alt="${product.name}" 
                     onerror="this.src='https://via.placeholder.com/400x400?text=No+Image'">
              </div>
              <div class="modal-product-info">
                <h2 class="modal-product-title">${product.name}</h2>
                
                <div class="modal-product-price">
                  $${product.price.toFixed(2)}
                  ${product.original_price > product.price ? 
                    `<span class="modal-product-original-price">$${product.original_price.toFixed(2)}</span>` : ''}
                </div>
                
                <div class="modal-product-description">
                  ${product.description}
                </div>
                
                <div class="modal-product-features">
                  <div class="modal-feature">
                    <i class="fas fa-star"></i>
                    <span>${product.rating.toFixed(1)}/5 (${product.reviews} reseñas)</span>
                  </div>
                  <div class="modal-feature">
                    <i class="fas fa-box"></i>
                    <span>Categoría: ${product.category}</span>
                  </div>
                  ${product.free_shipping ? `
                    <div class="modal-feature">
                      <i class="fas fa-truck"></i>
                      <span>Envío gratis</span>
                    </div>` : ''}
                  ${product.discount > 0 ? `
                    <div class="modal-feature">
                      <i class="fas fa-tag"></i>
                      <span>Descuento: ${product.discount}%</span>
                    </div>` : ''}
                </div>
                
                <div class="modal-stock-status ${product.in_stock ? 'modal-in-stock' : 'modal-out-of-stock'}">
                  ${product.in_stock ? 'Disponible en stock' : 'Producto agotado'}
                </div>
                
                <div class="modal-actions">
                  <button class="modal-add-to-cart" 
                    onclick="addToCart(${product.id}); closeQuickView()"
                    ${!product.in_stock ? 'disabled' : ''}>
                    <i class="fas fa-shopping-cart"></i>
                    ${product.in_stock ? 'Agregar al carrito' : 'Agotado'}
                  </button>
                </div>
              </div>
            `;
          } else {
            modalContent.innerHTML = `
              <div style="width:100%;text-align:center;padding:40px;">
                <i class="fas fa-exclamation-triangle" style="font-size:40px;color:#ff6b35;margin-bottom:20px;"></i>
                <h3>Producto no encontrado</h3>
              </div>
            `;
          }
        } else {
          // En modo producción, hacer fetch a la API
          const response = await fetch(`api/get_product.php?id=${productId}`);
          const product = await response.json();
          
          if (product.success) {
            // Construir HTML similar al bloque anterior pero con datos de la API
            modalContent.innerHTML = `
              <div class="modal-product-image-container">
                <img src="${product.image}" class="modal-product-image" alt="${product.name}">
              </div>
              <div class="modal-product-info">
                <h2 class="modal-product-title">${product.name}</h2>
                <!-- Resto del contenido similar al bloque de arriba -->
              </div>
            `;
          } else {
            throw new Error(product.message || 'Error al cargar el producto');
          }
        }
      } catch (error) {
        console.error('Error en vista rápida:', error);
        modalContent.innerHTML = `
          <div style="width:100%;text-align:center;padding:40px;">
            <i class="fas fa-exclamation-triangle" style="font-size:40px;color:#ff6b35;margin-bottom:20px;"></i>
            <h3>Error al cargar el producto</h3>
            <p>${error.message}</p>
          </div>
        `;
      }
    }

    // Función para cerrar el modal
    function closeQuickView() {
      const modal = document.getElementById('quick-view-modal');
      modal.style.display = 'none';
      document.body.style.overflow = 'auto';
    }

    // Filtros por categoría
    document.querySelectorAll('.filter-tag').forEach(tag => {
      tag.addEventListener('click', function() {
        // Remover active de todos los tags
        document.querySelectorAll('.filter-tag').forEach(t => t.classList.remove('active'));
        // Agregar active al tag clickeado
        this.classList.add('active');
        
        const category = this.dataset.category;
        const products = document.querySelectorAll('.product-card');
        
        products.forEach(product => {
          if (category === 'all' || product.dataset.category === category) {
            product.style.display = 'block';
          } else {
            product.style.display = 'none';
          }
        });
        
        // Actualizar contador de resultados
        const visibleProducts = document.querySelectorAll('.product-card[style="display: block"], .product-card:not([style])').length;
        const actualVisible = category === 'all' ? products.length : visibleProducts;
        document.querySelector('.results-count').textContent = `${actualVisible} productos encontrados`;
      });
    });

    // Ordenamiento
    document.getElementById('sort').addEventListener('change', function() {
      const sortBy = this.value;
      const productsContainer = document.getElementById('products');
      const products = Array.from(productsContainer.children);
      
      products.sort((a, b) => {
        switch(sortBy) {
          case 'price-low':
            const priceA = parseFloat(a.querySelector('.current-price').textContent.replace('$', '').replace(',', ''));
            const priceB = parseFloat(b.querySelector('.current-price').textContent.replace('$', '').replace(',', ''));
            return priceA - priceB;
          case 'price-high':
            const priceA2 = parseFloat(a.querySelector('.current-price').textContent.replace('$', '').replace(',', ''));
            const priceB2 = parseFloat(b.querySelector('.current-price').textContent.replace('$', '').replace(',', ''));
            return priceB2 - priceA2;
          case 'rating':
            const ratingA = a.querySelectorAll('.star.fas').length;
            const ratingB = b.querySelectorAll('.star.fas').length;
            return ratingB - ratingA;
          default:
            return 0;
        }
      });
      
      // Reordenar elementos en el DOM
      products.forEach(product => productsContainer.appendChild(product));
    });

    // Búsqueda con debounce
    document.querySelector('.search-bar').addEventListener('input', (e) => {
      const searchTerm = e.target.value.trim();
      
      // Limpiar timeout anterior
      clearTimeout(searchTimeout);
      
      // Esperar 500ms después de que el usuario deje de escribir
      searchTimeout = setTimeout(() => {
        if (searchTerm.length > 2 || searchTerm.length === 0) {
          // Auto-submit del formulario
          e.target.form.submit();
        }
      }, 500);
    });

    // Cargar wishlist al iniciar
    function loadWishlist() {
      const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
      wishlist.forEach(productId => {
        const button = document.querySelector(`[onclick*="toggleWishlist(${productId})"]`);
        if (button) {
          const icon = button.querySelector('i');
          icon.classList.remove('far');
          icon.classList.add('fas');
          button.style.color = '#ff4757';
        }
      });
    }

    // Animación de entrada para productos
    function animateProductsOnScroll() {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
          if (entry.isIntersecting) {
            setTimeout(() => {
              entry.target.style.opacity = '1';
              entry.target.style.transform = 'translateY(0)';
            }, index * 100);
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.1, rootMargin: '50px' });

      document.querySelectorAll('.product-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease-out';
        observer.observe(card);
      });
    }

    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeQuickView();
      }
    });

    // Inicializar la aplicación
    document.addEventListener('DOMContentLoaded', () => {
      fetchCartCount();
      loadWishlist();
      animateProductsOnScroll();
    });

    // Manejar errores de imágenes
    document.querySelectorAll('.product-image').forEach(img => {
      img.addEventListener('error', function() {
        this.src = 'https://via.placeholder.com/400x400/f0f0f0/999999?text=Imagen+no+disponible';
      });
    });

    // Prevenir comportamiento por defecto en algunos clicks
    document.addEventListener('click', function(e) {
      if (e.target.closest('.add-to-cart, .wishlist-btn, .quick-view')) {
        e.stopPropagation();
      }
    });


  </script>
</body>

</html>