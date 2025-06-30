<?php
require_once '../config/database.php';
require_once '../helpers/functions.php';

if (isLoggedIn()) {
    header('Location: account.php');
    exit;
}

$token = $_GET['token'] ?? '';
$errors = [];
$valid_token = false;
$user_id = null;

// Verificar token
if (!empty($token)) {
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        $valid_token = true;
        $user_id = $user['id'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($password)) {
        $errors['password'] = 'La contraseña es requerida';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'La contraseña debe tener al menos 8 caracteres';
    }
    
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Las contraseñas no coinciden';
    }
    
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Actualizar contraseña y limpiar token
        $stmt = $pdo->prepare("UPDATE usuarios SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $stmt->execute([$hashed_password, $user_id]);
        
        showToast('Contraseña restablecida correctamente. Ahora puedes iniciar sesión.');
        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - TechShop</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Estilos similares a login.php */
    </style>
</head>
<body>
    <div class="auth-container">
        <?php if ($valid_token): ?>
            <h1 class="auth-title">Restablecer Contraseña</h1>
            
            <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                <div class="form-group">
                    <label for="password">Nueva Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <?php if (!empty($errors['password'])): ?>
                        <span class="error-message"><?php echo $errors['password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmar Nueva Contraseña</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    <?php if (!empty($errors['confirm_password'])): ?>
                        <span class="error-message"><?php echo $errors['confirm_password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn">Restablecer Contraseña</button>
            </form>
        <?php else: ?>
            <h1 class="auth-title">Enlace Inválido o Expirado</h1>
            <p style="text-align: center;">
                El enlace de restablecimiento de contraseña no es válido o ha expirado.
                Por favor solicita un nuevo enlace.
            </p>
            <div class="auth-footer">
                <a href="forgot_password.php" class="btn">Solicitar Nuevo Enlace</a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php displayToast(); ?>
</body>
</html>