<?php
require_once 'config/database.php';
require_once 'helpers/functions.php';
redirectIfNotLoggedIn();

$user = getUserData();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $telefono = trim($_POST['telefono'] ?? null);
    $direccion = trim($_POST['direccion'] ?? null);
    
    if (empty($nombre)) {
        $errors['nombre'] = 'El nombre es requerido';
    }
    
    if (empty($apellidos)) {
        $errors['apellidos'] = 'Los apellidos son requeridos';
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellidos = ?, telefono = ?, direccion = ? WHERE id = ?");
            $stmt->execute([$nombre, $apellidos, $telefono, $direccion, $_SESSION['user_id']]);
            
            // Actualizar datos de sesión
            $_SESSION['user_nombre'] = $nombre;
            $_SESSION['user_apellidos'] = $apellidos;
            $_SESSION['user_telefono'] = $telefono;
            $_SESSION['user_direccion'] = $direccion;
            
            $_SESSION['success_message'] = 'Perfil actualizado correctamente';
            header('Location: account.php');
            exit;
        } catch (PDOException $e) {
            $errors['general'] = 'Error al actualizar el perfil: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - TechShop</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Estilos similares a account.php */
    </style>
</head>
<body>
    <!-- Header igual al de tu página principal -->
    
    <div class="main-container">
        <h1>Editar Perfil</h1>
        
        <?php if (!empty($errors['general'])): ?>
            <div class="error-message" style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                <?php echo $errors['general']; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="account-info">
            <h2>Información Personal</h2>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="nombre" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" 
                       value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
                <?php if (!empty($errors['nombre'])): ?>
                    <span class="error-message"><?php echo $errors['nombre']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="apellidos" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Apellidos</label>
                <input type="text" id="apellidos" name="apellidos" class="form-control" 
                       value="<?php echo htmlspecialchars($user['apellidos']); ?>" required>
                <?php if (!empty($errors['apellidos'])): ?>
                    <span class="error-message"><?php echo $errors['apellidos']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="email" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Email</label>
                <input type="email" id="email" class="form-control" 
                       value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                <small style="color: #666;">Para cambiar tu email, por favor contacta con soporte.</small>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="telefono" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Teléfono</label>
                <input type="tel" id="telefono" name="telefono" class="form-control" 
                       value="<?php echo htmlspecialchars($user['telefono'] ?? ''); ?>">
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="direccion" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Dirección</label>
                <textarea id="direccion" name="direccion" class="form-control" rows="3" style="width: 100%; padding: 12px 16px; border: 1px solid #ddd; border-radius: 6px; font-size: 16px;"><?php 
                    echo htmlspecialchars($user['direccion'] ?? ''); 
                ?></textarea>
            </div>
            
            <div class="account-actions">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="account.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
    
    <?php displayToast(); ?>
</body>
</html>