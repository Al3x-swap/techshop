<?php
require_once 'config/database.php';
require_once 'helpers/functions.php';

if (isLoggedIn()) {
    header('Location: account.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $errors['email'] = 'El email es requerido';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'El email no es válido';
    } else {
        // Verificar si el email existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND activo = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $errors['email'] = 'No existe una cuenta con este email';
        } else {
            // Generar token de restablecimiento
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Guardar token en la base de datos
            $stmt = $pdo->prepare("UPDATE usuarios SET reset_token = ?, reset_expires = ? WHERE id = ?");
            $stmt->execute([$token, $expires, $user['id']]);
            
            // Enviar email con el enlace de restablecimiento (simulado)
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=$token";
            
            // En un entorno real, aquí enviarías el email
            // mail($email, "Restablecer contraseña", "Haz clic en este enlace: $reset_link");
            
            $success = true;
            showToast('Se ha enviado un enlace para restablecer tu contraseña a tu correo electrónico.');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - TechShop</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Estilos similares a login.php */
    </style>
</head>
<body>
    <div class="auth-container">
        <h1 class="auth-title">Recuperar Contraseña</h1>
        
        <?php if ($success): ?>
            <div style="text-align: center; margin-bottom: 20px;">
                <p>Hemos enviado un enlace para restablecer tu contraseña a tu correo electrónico.</p>
                <p>Por favor revisa tu bandeja de entrada.</p>
            </div>
            
            <div class="auth-footer">
                <a href="login.php" class="btn btn-primary">Volver a Iniciar Sesión</a>
            </div>
        <?php else: ?>
            <p style="text-align: center; margin-bottom: 20px;">
                Ingresa tu dirección de correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
            </p>
            
            <form action="forgot_password.php" method="POST">
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                    <?php if (!empty($errors['email'])): ?>
                        <span class="error-message"><?php echo $errors['email']; ?></span>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn">Enviar Enlace</button>
            </form>
            
            <div class="auth-footer">
                <a href="login.php">Volver a Iniciar Sesión</a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php displayToast(); ?>
</body>
</html>