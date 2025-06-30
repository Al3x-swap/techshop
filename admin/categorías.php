<?php
require_once '../config/database.php';
require_once '../includes/session.php';

// Verificación de admin
requireAdmin();
$user = getUserInfo();

// Obtener categorías (ejemplo)
if (DEV_MODE) {
    $categorias = [
        ['id' => 1, 'nombre' => 'Smartphones', 'productos' => 15, 'activa' => 1],
        ['id' => 2, 'nombre' => 'Laptops', 'productos' => 8, 'activa' => 1],
        ['id' => 3, 'nombre' => 'Tablets', 'productos' => 5, 'activa' => 0],
    ];
} else {
    $stmt = $pdo->query("SELECT c.*, COUNT(p.id) as productos FROM categorias c LEFT JOIN productos p ON c.id = p.categoria_id GROUP BY c.id");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - Panel Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- COPIA EXACTAMENTE TODOS LOS ESTILOS DE TU INDEX.PHP -->
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

        /* ESTILOS ADICIONALES PARA LA TABLA */
        .content-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #ecf0f1;
        }

        .card-header h2 {
            margin: 0;
            color: #2c3e50;
        }

        .btn-primary {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary:hover {
            background: #2980b9;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }

        .data-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge.active {
            background: #d4edda;
            color: #155724;
        }

        .badge.inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .btn-actions {
            display: flex;
            gap: 5px;
        }

        .btn-edit, .btn-delete {
            background: none;
            border: none;
            padding: 8px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-edit {
            color: #f39c12;
        }

        .btn-edit:hover {
            background: #fff3cd;
        }

        .btn-delete {
            color: #e74c3c;
        }

        .btn-delete:hover {
            background: #f8d7da;
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
                <a href="index.php" class="menu-item">
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
                <a href="categorias.php" class="menu-item active">
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
                <h1>Gestión de Categorías</h1>
                <a href="index.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Volver al Dashboard
                </a>
            </div>

            <!-- Content -->
            <div class="content-card">
                <div class="card-header">
                    <h2>Lista de Categorías</h2>
                    <a href="categorias.php?action=add" class="btn-primary">
                        <i class="fas fa-plus"></i>
                        Nueva Categoría
                    </a>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Productos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><?php echo $categoria['id']; ?></td>
                            <td><?php echo htmlspecialchars($categoria['nombre']); ?></td>
                            <td><?php echo $categoria['productos']; ?></td>
                            <td>
                                <span class="badge <?php echo $categoria['activa'] ? 'active' : 'inactive'; ?>">
                                    <?php echo $categoria['activa'] ? 'Activa' : 'Inactiva'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-actions">
                                    <button class="btn-edit" onclick="editCategory(<?php echo $categoria['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-delete" onclick="deleteCategory(<?php echo $categoria['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function editCategory(id) {
            // Implementar edición
            alert('Editar categoría ID: ' + id);
        }

        function deleteCategory(id) {
            if(confirm('¿Estás seguro de eliminar esta categoría?')) {
                // Implementar eliminación
                alert('Eliminar categoría ID: ' + id);
            }
        }
    </script>
</body>
</html>

<?php