<?php
require_once '../includes/session.php';
require_once '../helpers/activity_logger.php';
requireAdmin();

// Consulta base
$query = "SELECT 
    p.id, p.nombre, p.descripcion, p.precio, p.precio_original, 
    p.descuento, c.nombre as categoria, p.imagen, p.stock, p.activo
FROM productos p
LEFT JOIN categorias c ON p.categoria_id = c.id
WHERE 1=1";

// Filtros (ejemplo)
if (isset($_GET['categoria']) && $_GET['categoria'] > 0) {
    $query .= " AND p.categoria_id = " . (int)$_GET['categoria'];
}

if (isset($_GET['busqueda'])) {
    $busqueda = trim($_GET['busqueda']);
    $query .= " AND p.nombre LIKE '%" . $pdo->quote($busqueda) . "%'";
}

$query .= " ORDER BY p.fecha_creacion DESC";
$productos = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Actividad específica si se ve un producto individual
$actividadProducto = [];
if (isset($_GET['id'])) {
    $productId = (int)$_GET['id'];
    $actividadProducto = $pdo->query("
        SELECT * FROM actividad 
        WHERE descripcion LIKE '%ID: $productId%' 
        ORDER BY fecha DESC LIMIT 5
    ")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Listado de Productos</h1>
        
        <?php if (isset($_GET['id']) && $actividadProducto): ?>
        <div class="card mb-4">
            <div class="card-header">Historial de cambios</div>
            <div class="card-body">
                <?php foreach ($actividadProducto as $act): ?>
                <div class="alert alert-<?= $act['tipo'] === 'delete' ? 'danger' : 'info' ?> mb-2 p-2">
                    <i class="fas fa-<?= htmlspecialchars($act['icono']) ?> me-2"></i>
                    <?= htmlspecialchars($act['descripcion']) ?>
                    <small class="text-muted ms-2">
                        <?= date('d/m/Y H:i', strtotime($act['fecha'])) ?>
                    </small>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                <tr>
                    <td><?= $producto['id'] ?></td>
                    <td>
                        <a href="?id=<?= $producto['id'] ?>">
                            <?= htmlspecialchars($producto['nombre']) ?>
                        </a>
                    </td>
                    <td>
                        $<?= number_format($producto['precio'], 2) ?>
                        <?php if ($producto['precio_original'] > $producto['precio']): ?>
                            <small class="text-muted d-block">
                                <del>$<?= number_format($producto['precio_original'], 2) ?></del>
                                (<?= $producto['descuento'] ?>% desc)
                            </small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $producto['stock'] ?>
                        <small class="d-block <?= $producto['stock'] > 0 ? 'text-success' : 'text-danger' ?>">
                            <?= $producto['stock'] > 0 ? 'En stock' : 'Agotado' ?>
                        </small>
                    </td>
                    <td><?= htmlspecialchars($producto['categoria']) ?></td>
                    <td>
                        <span class="badge bg-<?= $producto['activo'] ? 'success' : 'secondary' ?>">
                            <?= $producto['activo'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td>
                        <a href="editar.php?id=<?= $producto['id'] ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="acciones/cambiar_estado.php" method="post" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                            <input type="hidden" name="id" value="<?= $producto['id'] ?>">
                            <button type="submit" class="btn btn-sm <?= $producto['activo'] ? 'btn-secondary' : 'btn-success' ?>">
                                <i class="fas fa-<?= $producto['activo'] ? 'times' : 'check' ?>"></i>
                                <?= $producto['activo'] ? 'Desactivar' : 'Activar' ?>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
