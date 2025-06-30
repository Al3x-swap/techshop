<?php
define('DEV_MODE', true); // Cambiar a 'false' en producción

require_once '../config/database.php';
require_once '../includes/session.php';
require_once 'config.php';

// Verificación de admin mejorada
requireAdmin(); // Esto manejará todo: login, rol y expiración de sesión

$user = getUserInfo();

// Obtener estadísticas
if (DEV_MODE) {
    $stats = [
        'total_products' => 6,
        'products_in_stock' => 5,
        'products_out_stock' => 1,
        'total_users' => 25,
        'active_users' => 20,
        'deleted_users' => 5,
        'total_orders' => 150,
        'pending_orders' => 12,
        'completed_orders' => 138,
        'total_revenue' => 45750.80,
        'monthly_revenue' => 8200.50
    ];
} else {
    else {
    $recent_activity = $pdo->query("
        SELECT tipo, icono, descripcion, fecha 
        FROM actividad 
        ORDER BY fecha DESC 
        LIMIT 4
    ")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - TechShop</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: #2c3e50;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px;
            background: #34495e;
            border-bottom: 1px solid #4a5f7a;
        }

        .sidebar-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .admin-user {
            font-size: 14px;
            color: #bdc3c7;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .menu-item:hover, .menu-item.active {
            background: #34495e;
            border-left-color: #3498db;
        }

        .menu-item i {
            width: 20px;
            margin-right: 12px;
        }

        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #2c3e50;
        }

        .back-btn {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s;
        }

        .back-btn:hover {
            background: #2980b9;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #3498db;
        }

        .stat-card.products { border-left-color: #e74c3c; }
        .stat-card.users { border-left-color: #f39c12; }
        .stat-card.orders { border-left-color: #27ae60; }
        .stat-card.revenue { border-left-color: #9b59b6; }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .stat-title {
            font-size: 14px;
            color: #7f8c8d;
            text-transform: uppercase;
            font-weight: 600;
        }

        .stat-icon {
            font-size: 24px;
            opacity: 0.7;
        }

        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .stat-subtitle {
            font-size: 12px;
            color: #95a5a6;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .action-btn {
            background: white;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            color: #2c3e50;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .action-btn:hover {
            border-color: #3498db;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .action-btn i {
            font-size: 32px;
            color: #3498db;
        }

        .recent-activity {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .activity-header {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #ecf0f1;
            font-weight: 600;
        }

        .activity-list {
            padding: 20px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #ecf0f1;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 16px;
        }

        .activity-icon.new { background: #e8f5e9; color: #27ae60; }
        .activity-icon.update { background: #fff3e0; color: #f39c12; }
        .activity-icon.delete { background: #ffebee; color: #e74c3c; }

        .activity-content {
            flex: 1;
        }

        .activity-text {
            font-size: 14px;
            color: #2c3e50;
            margin-bottom: 2px;
        }

        .activity-time {
            font-size: 12px;
            color: #95a5a6;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-title">Panel Admin</div>
                <div class="admin-user"><?php echo htmlspecialchars($user['nombre']); ?></div>
            </div>
            <nav class="sidebar-menu">
    <a href="index.php" class="menu-item active">
        <i class="fas fa-tachometer-alt"></i>
        Dashboard
    </a>
    <a href="productos.php" class="menu-item">
        <i class="fas fa-box"></i>
        Productos
    </a>
    <a href="usuarios.php" class="menu-item">
        <i class="fas fa-users"></i>
        Usuarios
    </a>
    <a href="pedidos.php" class="menu-item">
        <i class="fas fa-shopping-cart"></i>
        Pedidos
    </a>
    <a href="inventario.php" class="menu-item">
        <i class="fas fa-warehouse"></i>
        Inventario
    </a>
    <a href="reportes.php" class="menu-item">
        <i class="fas fa-chart-bar"></i>
        Reportes
    </a>
    
    <!-- AGREGAR ESTAS LÍNEAS NUEVAS AQUÍ -->
    <a href="categorias.php" class="menu-item">
        <i class="fas fa-tags"></i>
        Categorías
    </a>
    <a href="promociones.php" class="menu-item">
        <i class="fas fa-percent"></i>
        Promociones
    </a>
    <a href="cupones.php" class="menu-item">
        <i class="fas fa-ticket-alt"></i>
        Cupones
    </a>
    
    <a href="configuracion.php" class="menu-item">
        <i class="fas fa-cog"></i>
        Configuración
    </a>
</nav>

        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Dashboard Administrativo</h1>
                <a href="../index.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Volver a la tienda
                </a>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card products">
                    <div class="stat-header">
                        <span class="stat-title">Productos</span>
                        <i class="fas fa-box stat-icon"></i>
                    </div>
                    <div class="stat-value"><?php echo $stats['total_products']; ?></div>
                    <div class="stat-subtitle">
                        <?php echo $stats['products_in_stock']; ?> en stock, 
                        <?php echo $stats['products_out_stock']; ?> agotados
                    </div>
                </div>

                <div class="stat-card users">
                    <div class="stat-header">
                        <span class="stat-title">Usuarios</span>
                        <i class="fas fa-users stat-icon"></i>
                    </div>
                    <div class="stat-value"><?php echo $stats['total_users']; ?></div>
                    <div class="stat-subtitle">
                        <?php echo $stats['active_users']; ?> activos, 
                        <?php echo $stats['deleted_users']; ?> eliminados
                    </div>
                </div>

                <div class="stat-card orders">
                    <div class="stat-header">
                        <span class="stat-title">Pedidos</span>
                        <i class="fas fa-shopping-cart stat-icon"></i>
                    </div>
                    <div class="stat-value"><?php echo $stats['total_orders']; ?></div>
                    <div class="stat-subtitle">
                        <?php echo $stats['pending_orders']; ?> pendientes, 
                        <?php echo $stats['completed_orders']; ?> completados
                    </div>
                </div>

                <div class="stat-card revenue">
                    <div class="stat-header">
                        <span class="stat-title">Ingresos</span>
                        <i class="fas fa-dollar-sign stat-icon"></i>
                    </div>
                    <div class="stat-value">$<?php echo number_format($stats['total_revenue'], 2); ?></div>
                    <div class="stat-subtitle">
                        $<?php echo number_format($stats['monthly_revenue'], 2); ?> este mes
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="productos.php?action=add" class="action-btn">
                    <i class="fas fa-plus"></i>
                    <span>Agregar Producto</span>
                </a>
                <a href="usuarios.php" class="action-btn">
                    <i class="fas fa-user-plus"></i>
                    <span>Gestionar Usuarios</span>
                </a>
                <a href="pedidos.php?status=pending" class="action-btn">
                    <i class="fas fa-clock"></i>
                    <span>Pedidos Pendientes</span>
                </a>
                <a href="reportes.php" class="action-btn">
                    <i class="fas fa-download"></i>
                    <span>Exportar Reportes</span>
                </a>
            </div>

            <!-- Recent Activity -->
<div class="recent-activity">
    <div class="activity-header">Actividad Reciente</div>
    <div class="activity-list">
        <?php foreach ($recent_activity as $activity): ?>
        <div class="activity-item">
            <div class="activity-icon <?php echo htmlspecialchars($activity['tipo']); ?>">
                <i class="fas fa-<?php echo htmlspecialchars($activity['icono']); ?>"></i>
            </div>
            <div class="activity-content">
                <div class="activity-text"><?php echo htmlspecialchars($activity['descripcion']); ?></div>
                <div class="activity-time"><?php echo formatActivityDate($activity['fecha']); ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>                        <div class="activity-content">
                            <div class="activity-text">Nuevo producto agregado: Smartphone X1 Pro</div>
                            <div class="activity-time">Hace 2 horas</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon update">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-text">Stock actualizado para Laptop Gamer Ultra</div>
                            <div class="activity-time">Hace 4 horas</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon new">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-text">Nuevo usuario registrado: María González</div>
                            <div class="activity-time">Hace 6 horas</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon delete">
                            <i class="fas fa-trash"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-text">Producto eliminado: Tablet obsoleta</div>
                            <div class="activity-time">Hace 1 día</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle sidebar en móvil
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
        }

        // Actualizar estadísticas cada 30 segundos
        setInterval(async () => {
            try {
                const response = await fetch('api/get_admin_stats.php');
                const stats = await response.json();
                
                if (stats.success) {
                    // Actualizar valores en las tarjetas
                    document.querySelector('.stat-card.products .stat-value').textContent = stats.data.total_products;
                    document.querySelector('.stat-card.users .stat-value').textContent = stats.data.total_users;
                    // ... actualizar otros valores
                }
            } catch (error) {
                console.error('Error al actualizar estadísticas:', error);
            }
        }, 30000);
    </script>
</body>
</html>