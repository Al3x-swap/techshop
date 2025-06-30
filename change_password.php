<?php
require_once 'config/database.php';
require_once 'helpers/functions.php';
redirectIfNotLoggedIn();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($current_password)) {
        $errors['current_password'] = 'La contraseña actual es requerida';
    }
    
    if (empty($new_password)) {
        $errors['new_password'] = 'La nueva contraseña es requerida';
    } elseif (strlen($new_password) < 8) {
        $errors['new_password'] = 'La nueva contraseña debe tener al menos 8 caracteres';
    }
    
    if ($new_password !== $confirm_password) {
        $errors['confirm_password'] = 'Las contraseñas no coinciden';
    }
    
    if (empty($errors)) {
        // Verificar contraseña actual
        $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($current_password, $user['password'])) {
            // Actualizar contraseña
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $stmt->execute([$new_hashed_password, $_SESSION['user_id']]);
            
            $_SESSION['success_message'] = 'Contraseña cambiada correctamente';
            header('Location: account.php');
            exit;
        } else {
            $errors['current_password'] = 'La contraseña actual es incorrecta';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña - TechShop</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Estilos similares a account.php */
    </style>
</head>
<body>
    <!-- Header igual al de tu página principal -->
    
    <div class="main-container">
        <h1>Cambiar Contraseña</h1>
        
        <form method="POST" class="account-info">
            <h2>Seguridad</h2>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="current_password" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Contraseña Actual</label>
                <input type="password" id="current_password" name="current_password" class="form-control" required>
                <?php if (!empty($errors['current_password'])): ?>
                    <span class="error-message"><?php echo $errors['current_password']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="new_password" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Nueva Contraseña</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required>
                <?php if (!empty($errors['new_password'])): ?>
                    <span class="error-message"><?php echo $errors['new_password']; ?></span>
                <?php endif; ?>
                <small style="color: #666;">La contraseña debe tener al menos 8 caracteres.</small>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="confirm_password" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Confirmar Nueva Contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                <?php if (!empty($errors['confirm_password'])): ?>
                    <span class="error-message"><?php echo $errors['confirm_password']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="account-actions">
                <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                <a href="account.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
    
    <?php displayToast(); ?>
</body>
</html>