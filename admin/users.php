<?php
require_once '../config/database.php';
require_once '../helpers/functions.php';
redirectIfNotAdmin();

// Paginación
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Obtener usuarios
$stmt = $pdo->prepare("SELECT * FROM usuarios ORDER BY fecha_registro DESC LIMIT :offset, :per_page");
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();

// Contar total de usuarios para paginación
$total_users = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$total_pages = ceil($total_users / $per_page);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - TechShop</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Estilos del panel de administración */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .admin-table th, .admin-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .admin-table th {
            background: #f5f5f5;
            font-weight: 600;
        }
        
        .admin-table tr:hover {
            background: #f9f9f9;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 14px;
        }
        
        .btn-success {
            background: #2ecc71;
            color: white;
            border: none;
        }
        
        .btn-danger {
            background: #e74c3c;
            color: white;
            border: none;
        }
        
        .pagination {
            display: flex;
            gap: 5px;
            margin-top: 20px;
        }
        
        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        
        .pagination a:hover {
            background: #f5f5f5;
        }
        
        .pagination .active {
            background: #ff6b35;
            color: white;
            border-color: #ff6b35;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar igual que en index.php -->
        
        <div class="admin-content">
            <div class="admin-header">
                <h1 class="admin-title">Gestión de Usuarios</h1>
                <a href="add_user.php" class="btn btn-primary">Agregar Usuario</a>
            </div>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Registro</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellidos']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['telefono'] ?? '-'); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($user['fecha_registro'])); ?></td>
                        <td>
                            <?php if ($user['activo']): ?>
                                <span style="color: #2ecc71;">Activo</span>
                            <?php else: ?>
                                <span style="color: #e74c3c;">Inactivo</span>
                            <?php endif; ?>
                            
                            <?php if ($user['es_admin']): ?>
                                <span style="display: block; font-size: 12px; color: #ff6b35;">Admin</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm">Editar</a>
                            <?php if ($user['activo']): ?>
                                <a href="deactivate_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger">Desactivar</a>
                            <?php else: ?>
                                <a href="activate_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-success">Activar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>">&laquo; Anterior</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>">Siguiente &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
