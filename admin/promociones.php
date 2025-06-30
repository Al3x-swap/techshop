<?php
require_once '../config/database.php';
require_once '../includes/session.php';

requireAdmin();
$user = getUserInfo();

// Datos de ejemplo para promociones
if (DEV_MODE) {
    $promociones = [
        ['id' => 1, 'nombre' => 'Descuento Navidad', 'descuento' => 20, 'tipo' => 'porcentaje', 'fecha_fin' => '2024-12-31', 'activa' => 1],
        ['id' => 2, 'nombre' => 'Black Friday', 'descuento' => 50, 'tipo' => 'porcentaje', 'fecha_fin' => '2024-11-30', 'activa' => 0],
        ['id' => 3, 'nombre' => 'Envío Gratis', 'descuento' => 0, 'tipo' => 'envio', 'fecha_fin' => '2024-12-15', 'activa' => 1],
    ];
} else {
    $stmt = $pdo->query("SELECT * FROM promociones ORDER BY fecha_fin DESC");
    $promociones = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promociones - Panel Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- MISMOS ESTILOS QUE CATEGORIAS.PHP -->
    <style>
        /* Copia exactamente los mismos estilos de categorias.php */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f7fa; color: #333; }
        /* ... resto de estilos igual ... */
        .admin-container { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #2c3e50; color: white; position: fixed; height: 100vh; overflow-y: auto; }
        /* ... (todos los estilos iguales) ... */
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar (igual que categorias.php, solo cambia el active) -->
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
                <!-- ... otros menús ... -->
                <a href="categorias.php" class="menu-item">
                    <i class="fas fa-tags"></i>
                    Categorías
                </a>
                <a href="promociones.php" class="menu-item active">
                    <i class="fas fa-percent"></i>
                    Promociones
                </a>
                <!-- ... resto del menú ... -->
            </nav>
        </div>

        <!-- Main Content (solo cambia el contenido) -->
        <div class="main-content">
            <div class="header">
                <h1>Gestión de Promociones</h1>
                <a href="index.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Volver al Dashboard
                </a>
            </div>

            <div class="content-card">
                <div class="card-header">
                    <h2>Lista de Promociones</h2>
                    <a href="promociones.php?action=add" class="btn-primary">
                        <i class="fas fa-plus"></i>
                        Nueva Promoción
                    </a>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descuento</th>
                            <th>Tipo</th>
                            <th>Válido hasta</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($promociones as $promo): ?>
                        <tr>
                            <td><?php echo $promo['id']; ?></td>
                            <td><?php echo htmlspecialchars($promo['nombre']); ?></td>
                            <td>
                                <?php 
                                if ($promo['tipo'] == 'porcentaje') {
                                    echo $promo['descuento'] . '%';
                                } elseif ($promo['tipo'] == 'fijo') {
                                    echo '$' . $promo['descuento'];
                                } else {
                                    echo 'Envío gratis';
                                }
                                ?>
                            </td>
                            <td><?php echo ucfirst($promo['tipo']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($promo['fecha_fin'])); ?></td>
                            <td>
                                <span class="badge <?php echo $promo['activa'] ? 'active' : 'inactive'; ?>">
                                    <?php echo $promo['activa'] ? 'Activa' : 'Inactiva'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-actions">
                                    <button class="btn-edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-delete">
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
</body>
</html>

<?php
