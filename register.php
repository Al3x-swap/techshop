<?php
require_once 'config/database.php';
require_once 'helpers/functions.php';

if (isLoggedIn()) {
    header('Location: account.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $telefono = trim($_POST['telefono'] ?? null);

    $errors = [];
    
    if (empty($nombre)) {
        $errors['nombre'] = 'El nombre es requerido';
    }
    
    if (empty($apellidos)) {
        $errors['apellidos'] = 'Los apellidos son requeridos';
    }
    
    if (empty($email)) {
        $errors['email'] = 'El email es requerido';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'El email no es válido';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $errors['email'] = 'Este email ya está registrado';
        }
    }
    
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
        
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellidos, email, password, telefono) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $apellidos, $email, $hashed_password, $telefono]);
        
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['user_nombre'] = $nombre;
        $_SESSION['user_apellidos'] = $apellidos;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_telefono'] = $telefono;
        $_SESSION['user_es_admin'] = 0; // Por defecto no es admin
        
        showToast('¡Registro exitoso! Bienvenido a TechShop');
        header('Location: account.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - TechShop</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Estilos del formulario de registro */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        
        .auth-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            padding: 30px;
        }
        
        .auth-title {
            font-size: 24px;
            color: #ff6b35;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        .form-control:focus {
            border-color: #ff6b35;
            outline: none;
        }
        
        .btn {
            display: inline-block;
            background: #ff6b35;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn:hover {
            background: #e55a2b;
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        
        .auth-footer a {
            color: #ff6b35;
            text-decoration: none;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            color: #ff4757;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 6px;
            color: white;
            z-index: 1000;
            animation: slideIn 0.3s, fadeOut 0.5s 2.5s forwards;
        }
        
        .toast.success {
            background: #2ecc71;
        }
        
        .toast.error {
            background: #e74c3c;
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1 class="auth-title">Regístrate en TechShop</h1>
        
        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" 
                       value="<?php echo htmlspecialchars($nombre ?? ''); ?>" required>
                <?php if (!empty($errors['nombre'])): ?>
                    <span class="error-message"><?php echo $errors['nombre']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="apellidos">Apellidos</label>
                <input type="text" id="apellidos" name="apellidos" class="form-control" 
                       value="<?php echo htmlspecialchars($apellidos ?? ''); ?>" required>
                <?php if (!empty($errors['apellidos'])): ?>
                    <span class="error-message"><?php echo $errors['apellidos']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                <?php if (!empty($errors['email'])): ?>
                    <span class="error-message"><?php echo $errors['email']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <?php if (!empty($errors['password'])): ?>
                    <span class="error-message"><?php echo $errors['password']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                <?php if (!empty($errors['confirm_password'])): ?>
                    <span class="error-message"><?php echo $errors['confirm_password']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="telefono">Teléfono (opcional)</label>
                <input type="tel" id="telefono" name="telefono" class="form-control" 
                       value="<?php echo htmlspecialchars($telefono ?? ''); ?>">
            </div>
            
            <button type="submit" class="btn">Registrarse</button>
        </form>
        
        <div class="auth-footer">
            ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a>
        </div>
    </div>
    
    <?php displayToast(); ?>
</body>
</html>