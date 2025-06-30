<?php
require_once '../../includes/session.php';
require_once '../../helpers/activity_logger.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../listar.php');
    exit;
}

if (!verifyCSRFToken($_POST['csrf_token'])) {
    $_SESSION['error'] = 'Token CSRF inválido';
    header('Location: ' . (isset($_POST['id']) ? '../editar.php?id='.$_POST['id'] : '../agregar.php'));
    exit;
}

// Procesamiento de datos
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$nombre = trim($_POST['nombre']);
$descripcion = trim($_POST['descripcion']);
$precio = (float)$_POST['precio'];
$precio_original = (float)$_POST['precio_original'];
$descuento = isset($_POST['descuento']) ? (int)$_POST['descuento'] : 0;
$categoria = (int)$_POST['categoria'];
$stock = (int)$_POST['stock'];
$activo = isset($_POST['activo']) ? 1 : 0;

// Validaciones
if (empty($nombre)) {
    $_SESSION['error'] = 'El nombre es obligatorio';
    header('Location: ' . ($id ? '../editar.php?id='.$id : '../agregar.php'));
    exit;
}

if ($precio > $precio_original) {
    $_SESSION['error'] = 'El precio debe ser ≤ al precio original';
    header('Location: ' . ($id ? '../editar.php?id='.$id : '../agregar.php'));
    exit;
}

// Procesamiento de imagen (ejemplo básico)
$imagenNombre = null;
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $imagenNombre = 'producto_' . time() . '_' . basename($_FILES['imagen']['name']);
    move_uploaded_file($_FILES['imagen']['tmp_name'], '../../uploads/' . $imagenNombre);
}

// Calcular descuento si no se proporcionó
if ($descuento === 0 && $precio_original > 0) {
    $descuento = round(($precio_original - $precio) / $precio_original * 100);
}

try {
    if ($id > 0) {
        // Actualización
        $stmt = $pdo->prepare("UPDATE productos SET 
            nombre = ?, descripcion = ?, precio = ?, precio_original = ?, 
            descuento = ?, categoria_id = ?, imagen = ?, stock = ?, activo = ?
            WHERE id = ?");
        $stmt->execute([
            $nombre, $descripcion, $precio, $precio_original,
            $descuento, $categoria, $imagenNombre, $stock, $activo, $id
        ]);

        logActivity(
            'update',
            sprintf('Producto actualizado: %s (ID: %d)', $nombre, $id),
            'edit'
        );
    } else {
        // Creación
        $stmt = $pdo->prepare("INSERT INTO productos (
            nombre, descripcion, precio, precio_original, descuento, 
            categoria_id, imagen, stock, activo, fecha_creacion
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $nombre, $descripcion, $precio, $precio_original, $descuento,
            $categoria, $imagenNombre, $stock, $activo
        ]);
        $id = $pdo->lastInsertId();

        logActivity(
            'new',
            sprintf('Nuevo producto creado: %s (ID: %d)', $nombre, $id),
            'plus'
        );
    }

    $_SESSION['success'] = $id > 0 ? 'Producto actualizado' : 'Producto creado';
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error en la base de datos: ' . $e->getMessage();
}

header('Location: ../listar.php');
exit;
