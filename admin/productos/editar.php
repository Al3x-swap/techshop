<?php
require_once '../includes/session.php';
requireAdmin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = 'ID de producto inválido';
    header('Location: listar.php');
    exit;
}

$productId = (int)$_GET['id'];

// Obtener datos del producto
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$productId]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    $_SESSION['error'] = 'Producto no encontrado';
    header('Location: listar.php');
    exit;
}

// Obtener categorías para el select
$categorias = $pdo->query("SELECT id, nombre FROM categorias")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Editar Producto</h1>
            <a href="listar.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); endif; ?>

        <form action="acciones/guardar.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="id" value="<?= $producto['id'] ?>">

            <div class="row g-3">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">Información Básica</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre*</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="<?= htmlspecialchars($producto['nombre']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" 
                                          rows="3"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">Precio y Stock</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="precio_original" class="form-label">Precio Original*</label>
                                    <input type="number" step="0.01" class="form-control" id="precio_original" 
                                           name="precio_original" value="<?= $producto['precio_original'] ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="precio" class="form-label">Precio Actual*</label>
                                    <input type="number" step="0.01" class="form-control" id="precio" 
                                           name="precio" value="<?= $producto['precio'] ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="descuento" class="form-label">Descuento (%)</label>
                                    <input type="number" class="form-control" id="descuento" 
                                           name="descuento" value="<?= $producto['descuento'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="stock" class="form-label">Stock*</label>
                                    <input type="number" class="form-control" id="stock" 
                                           name="stock" value="<?= $producto['stock'] ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">Imagen</div>
                        <div class="card-body text-center">
                            <?php if ($producto['imagen']): ?>
                            <img src="../uploads/<?= htmlspecialchars($producto['imagen']) ?>" 
                                 alt="Imagen actual" class="img-fluid mb-3 rounded" style="max-height: 200px;">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="eliminar_imagen" name="eliminar_imagen">
                                <label class="form-check-label" for="eliminar_imagen">
                                    Eliminar imagen actual
                                </label>
                            </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <label for="imagen" class="form-label">Nueva imagen</label>
                                <input class="form-control" type="file" id="imagen" name="imagen" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">Configuración</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="categoria" class="form-label">Categoría*</label>
                                <select class="form-select" id="categoria" name="categoria" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" 
                                        <?= $cat['id'] == $producto['categoria_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nombre']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="activo" name="activo" 
                                    <?= $producto['activo'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="activo">Producto activo</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Calcular descuento automáticamente al cambiar precios
        document.getElementById('precio').addEventListener('change', calcularDescuento);
        document.getElementById('precio_original').addEventListener('change', calcularDescuento);

        function calcularDescuento() {
            const precioOriginal = parseFloat(document.getElementById('precio_original').value);
            const precio = parseFloat(document.getElementById('precio').value);
            
            if (precioOriginal > 0 && precio <= precioOriginal) {
                const descuento = Math.round(((precioOriginal - precio) / precioOriginal * 100));
                document.getElementById('descuento').value = descuento;
            }
        }
    </script>
</body>
</html>