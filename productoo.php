<?php
// Configuración de la base de datos (si no estás en modo desarrollo)
$isDevelopmentMode = true;

// Datos de prueba para modo desarrollo
$mockProducts = [
    1 => [ 
        'id' => 1, 
        'name' => 'Smartphone X1 Pro', 
        'price' => 399, 
        'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&h=800&fit=crop',
        'description' => 'Smartphone de última generación con pantalla AMOLED de 6.5", procesador Snapdragon 888, 8GB RAM y 256GB almacenamiento.',
        'specs' => [
            'Pantalla' => '6.5" AMOLED 120Hz',
            'Procesador' => 'Snapdragon 888',
            'RAM' => '8GB LPDDR5',
            'Almacenamiento' => '256GB UFS 3.1',
            'Batería' => '4500mAh',
            'Cámara' => 'Triple: 64MP + 12MP + 8MP'
        ]
    ],
    2 => [ 
        'id' => 2, 
        'name' => 'Laptop Gamer Ultra', 
        'price' => 1499, 
        'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800&h=800&fit=crop',
        'description' => 'Laptop para gaming de alto rendimiento con pantalla 144Hz, RTX 3070 y procesador i7 de 11ª generación.',
        'specs' => [
            'Pantalla' => '15.6" FHD 144Hz',
            'Procesador' => 'Intel Core i7-11800H',
            'GPU' => 'NVIDIA RTX 3070 8GB',
            'RAM' => '16GB DDR4',
            'Almacenamiento' => '1TB NVMe SSD',
            'Sistema Operativo' => 'Windows 11 Pro'
        ]
    ],
    3 => [ 
        'id' => 3, 
        'name' => 'Tablet 4K Max', 
        'price' => 299, 
        'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=800&h=800&fit=crop',
        'description' => 'Tablet con resolución 4K y lápiz óptico incluido para artistas y profesionales.',
        'specs' => [
            'Pantalla' => '10.5" 4K HDR',
            'Procesador' => 'Snapdragon 860',
            'RAM' => '6GB',
            'Almacenamiento' => '128GB (expandible)',
            'Batería' => '7000mAh',
            'Incluye' => 'Lápiz óptico'
        ]
    ],
    4 => [ 
        'id' => 4, 
        'name' => 'Smartwatch Pro', 
        'price' => 199, 
        'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&h=800&fit=crop',
        'description' => 'Reloj inteligente profesional con monitor de ritmo cardíaco, oxígeno en sangre y GPS integrado.',
        'specs' => [
            'Pantalla' => '1.78" AMOLED',
            'Resistencia' => '5ATM',
            'Batería' => 'Hasta 14 días',
            'Sensores' => 'Ritmo cardíaco, SpO2, GPS',
            'Compatibilidad' => 'Android e iOS',
            'Notificaciones' => 'Sí'
        ]
    ],
    5 => [ 
        'id' => 5, 
        'name' => 'Auriculares Premium', 
        'price' => 89, 
        'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&h=800&fit=crop',
        'description' => 'Auriculares inalámbricos con cancelación de ruido y sonido Hi-Res.',
        'specs' => [
            'Tipo' => 'Inalámbricos',
            'Cancelación de ruido' => 'Sí',
            'Autonomía' => '30 horas',
            'Conexión' => 'Bluetooth 5.0',
            'Resistencia' => 'IPX4',
            'Micrófono' => 'Integrado'
        ]
    ],
    6 => [ 
        'id' => 6, 
        'name' => 'Cámara 4K Ultra', 
        'price' => 799, 
        'image' => 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=800&h=800&fit=crop',
        'description' => 'Cámara profesional 4K con sensor de 1" y zoom óptico 10x.',
        'specs' => [
            'Sensor' => '1" 20.1MP',
            'Video' => '4K 60fps',
            'Zoom' => 'Óptico 10x',
            'Pantalla' => 'LCD táctil 3"',
            'Conectividad' => 'Wi-Fi, Bluetooth',
            'Estabilización' => '5 ejes'
        ]
    ]
];

// Obtener ID del producto
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Buscar el producto
$product = isset($mockProducts[$productId]) ? $mockProducts[$productId] : null;

if (!$product) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($product['name']); ?> - TechShop Premium</title>
  <style>
    /* ... (todo el CSS permanece igual) ... */
    
    /* Estilos adicionales para la página de producto */
    .product-detail {
      max-width: 1200px;
      margin: 3rem auto;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 3rem;
      padding: 0 2rem;
    }
    
    .product-gallery {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    
    .main-image {
      width: 100%;
      height: 500px;
      object-fit: contain;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 20px;
      padding: 2rem;
    }
    
    .thumbnail-container {
      display: flex;
      gap: 1rem;
    }
    
    .thumbnail {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 10px;
      cursor: pointer;
      transition: all 0.3s ease;
      background: rgba(255, 255, 255, 0.05);
      padding: 0.5rem;
    }
    
    .thumbnail:hover {
      transform: scale(1.1);
      box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
    }
    
    .product-info {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      padding: 2rem;
    }
    
    .product-title {
      font-size: 2.5rem;
      margin-bottom: 1rem;
      background: linear-gradient(135deg, #fff 0%, #f0f0f0 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .product-price {
      font-size: 2.5rem;
      font-weight: 700;
      background: var(--accent);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin: 1.5rem 0;
    }
    
    .product-description {
      line-height: 1.6;
      margin-bottom: 2rem;
      color: rgba(255, 255, 255, 0.8);
    }
    
    .specs-table {
      width: 100%;
      border-collapse: collapse;
      margin: 2rem 0;
    }
    
    .specs-table tr:nth-child(even) {
      background: rgba(255, 255, 255, 0.03);
    }
    
    .specs-table td {
      padding: 1rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .specs-table td:first-child {
      font-weight: 600;
      color: rgba(255, 255, 255, 0.7);
    }
    
    .back-link {
      display: inline-block;
      margin-top: 2rem;
      color: #667eea;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .back-link:hover {
      color: #764ba2;
      transform: translateX(-5px);
    }
    
    @media (max-width: 768px) {
      .product-detail {
        grid-template-columns: 1fr;
        padding: 0 1rem;
      }
      
      .main-image {
        height: 300px;
      }
    }
  </style>
</head>
<body>
  <div class="scroll-indicator" id="scroll-indicator"></div>
  
  <header>
    <div class="logo">TechShop</div>
    <nav>
      <a href="index.php">Inicio</a>
      <a href="index.php#products">Productos</a>
      <a href="#about">Soporte</a>
    </nav>
    <form method="GET" action="index.php" style="display: inline;">
      <input type="text" name="search" class="search-bar" placeholder="Buscar productos..." />
    </form>
    <button class="cart-btn">🛒 (<span id="cart-count">0</span>)</button>
  </header>

  <div class="product-detail">
    <div class="product-gallery">
      <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="main-image" id="main-image">
      
      <div class="thumbnail-container">
        <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="thumbnail" onclick="document.getElementById('main-image').src = this.src">
        <!-- Más miniaturas podrían agregarse aquí -->
      </div>
    </div>
    
    <div class="product-info">
      <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
      <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
      
      <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
      
      <button class="add-to-cart" onclick="addToCart(<?php echo $product['id']; ?>)" style="width: auto; padding: 1rem 2rem;">
        Agregar al Carrito ✨
      </button>
      
      <h3 style="margin-top: 2rem; color: rgba(255, 255, 255, 0.9);">Especificaciones</h3>
      <table class="specs-table">
        <?php foreach ($product['specs'] as $key => $value): ?>
          <tr>
            <td><?php echo htmlspecialchars($key); ?></td>
            <td><?php echo htmlspecialchars($value); ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
      
      <a href="index.php" class="back-link">← Volver a todos los productos</a>
    </div>
  </div>

  <script>
    // Variables globales
    let isDevelopmentMode = <?php echo $isDevelopmentMode ? 'true' : 'false'; ?>;
    
    // Scroll indicator
    window.addEventListener('scroll', () => {
      const scrolled = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
      document.getElementById('scroll-indicator').style.width = scrolled + '%';
    });

    // Función para obtener el conteo del carrito (con fallback)
    async function fetchCartCount() {
      if (isDevelopmentMode) {
        // En modo desarrollo, obtener del localStorage
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
        // Fallback a localStorage
        const cart = JSON.parse(localStorage.getItem('cart') || '{}');
        const count = Object.values(cart).reduce((sum, qty) => sum + qty, 0);
        document.getElementById('cart-count').textContent = count;
      }
    }

    // Función para agregar al carrito (con fallback a localStorage)
    async function addToCart(productId) {
      const button = event.target;
      const originalText = button.textContent;
      
      // Deshabilitar botón temporalmente
      button.disabled = true;
      button.style.transform = 'scale(0.95)';
      button.textContent = 'Agregando... ⏳';
      
      try {
        if (isDevelopmentMode) {
          // Modo desarrollo: usar localStorage
          const cart = JSON.parse(localStorage.getItem('cart') || '{}');
          cart[productId] = (cart[productId] || 0) + 1;
          localStorage.setItem('cart', JSON.stringify(cart));
          
          const cartCount = Object.values(cart).reduce((sum, qty) => sum + qty, 0);
          
          // Simular delay
          await new Promise(resolve => setTimeout(resolve, 500));
          
          // Actualizar UI
          const cartCountElement = document.getElementById('cart-count');
          cartCountElement.style.transform = 'scale(1.5)';
          cartCountElement.textContent = cartCount;
          
          setTimeout(() => {
            cartCountElement.style.transform = 'scale(1)';
          }, 300);
          
          button.textContent = 'Agregado! ✅';
          button.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
          
        } else {
          // Modo producción: usar base de datos
          const response = await fetch('api/add_to_cart.php', {
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
            
            // Mostrar éxito
            button.textContent = 'Agregado! ✅';
            button.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
            
          } else {
            throw new Error(data.message || 'Error al agregar al carrito');
          }
        }
        
      } catch (error) {
        console.error('Error:', error);
        
        // Fallback a localStorage si falla la base de datos
        if (!isDevelopmentMode) {
          try {
            const cart = JSON.parse(localStorage.getItem('cart') || '{}');
            cart[productId] = (cart[productId] || 0) + 1;
            localStorage.setItem('cart', JSON.stringify(cart));
            
            const cartCount = Object.values(cart).reduce((sum, qty) => sum + qty, 0);
            const cartCountElement = document.getElementById('cart-count');
            cartCountElement.style.transform = 'scale(1.5)';
            cartCountElement.textContent = cartCount;
            
            setTimeout(() => {
              cartCountElement.style.transform = 'scale(1)';
            }, 300);
            
            button.textContent = 'Agregado! ✅';
            button.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
            
          } catch (fallbackError) {
            button.textContent = 'Error ❌';
            button.style.background = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
          }
        } else {
          button.textContent = 'Error ❌';
          button.style.background = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
        }
      } finally {
        // Restaurar botón después de 2 segundos
        setTimeout(() => {
          button.disabled = false;
          button.style.transform = 'scale(1)';
          button.textContent = originalText;
          button.style.background = 'var(--secondary)';
        }, 2000);
      }
    }

    // Inicializar la aplicación
    document.addEventListener('DOMContentLoaded', () => {
      fetchCartCount(); // Obtener conteo inicial del carrito
    });

    // Add some interactive particles on click
    document.addEventListener('click', (e) => {
      createParticles(e.clientX, e.clientY);
    });

    function createParticles(x, y) {
      for (let i = 0; i < 6; i++) {
        const particle = document.createElement('div');
        particle.style.position = 'fixed';
        particle.style.left = x + 'px';
        particle.style.top = y + 'px';
        particle.style.width = '4px';
        particle.style.height = '4px';
        particle.style.background = 'rgba(102, 126, 234, 0.8)';
        particle.style.borderRadius = '50%';
        particle.style.pointerEvents = 'none';
        particle.style.zIndex = '9999';
        
        document.body.appendChild(particle);
        
        const angle = (i / 6) * Math.PI * 2;
        const velocity = 100;
        const vx = Math.cos(angle) * velocity;
        const vy = Math.sin(angle) * velocity;
        
        let posX = x;
        let posY = y;
        let opacity = 1;
        
        const animate = () => {
          posX += vx * 0.02;
          posY += vy * 0.02;
          opacity -= 0.02;
          
          particle.style.left = posX + 'px';
          particle.style.top = posY + 'px';
          particle.style.opacity = opacity;
          
          if (opacity > 0) {
            requestAnimationFrame(animate);
          } else {
            particle.remove();
          }
        };
        
        animate();
      }
    }
  </script>
</body>
</html>