<?php
/**
 * Página de Login/Registro para TechShop - Versión corregida
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';
require_once 'includes/session.php';

// Redirección si ya está logueado
if (isLoggedIn()) {
    header('Location: index.php?login=success'); // Redirige a index.php con un parámetro de éxito
    exit;
}

$error_message = '';
$success_message = '';

// Mensajes de URL
if (isset($_GET['expired'])) {
    $error_message = 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.';
}
if (isset($_GET['logout'])) {
    $success_message = 'Has cerrado sesión correctamente.';
}
if (isset($_GET['registered'])) {
    $success_message = 'Registro exitoso. Ya puedes iniciar sesión.';
}

// Función para autenticar usuario
function authenticateUser($email, $password) {
    try {
        $db = Database::getInstance();
        $user = $db->fetchOne(
            "SELECT id, email, name, password, role, status FROM usuarios WHERE email = ?",
            [$email]
        );
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    } catch (Exception $e) {
        error_log("Error en autenticación: " . $e->getMessage());
        return false;
    }
}

// Función para registrar usuario (versión corregida)
function registerUser($name, $email, $password) {
    try {
        $db = Database::getInstance();
        
        // Verificar si el email ya existe
        if ($db->fetchOne("SELECT id FROM usuarios WHERE email = ?", [$email])) {
            return ['success' => false, 'message' => 'Este email ya está registrado.'];
        }
        
        // Crear nuevo usuario
        $userId = $db->insert('usuarios', [
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        return [
            'success' => (bool)$userId, 
            'message' => $userId ? 'Usuario registrado exitosamente.' : 'Error al crear la cuenta.'
        ];
        
    } catch (PDOException $e) {
        error_log("Error en registro: " . $e->getMessage());
        return [
            'success' => false, 
            'message' => 'Error en el servidor. Por favor, intente más tarde.'
        ];
    }
}

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'login') {
        // Procesar login
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember_me = isset($_POST['remember_me']);
        
        if (empty($email) || empty($password)) {
            $error_message = 'Por favor, completa todos los campos.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = 'Por favor, introduce un email válido.';
        } else {
            // Credenciales demo
            if ($email === 'demo@techshop.com' && $password === 'demo123') {
                $_SESSION['user_id'] = 999;
                $_SESSION['user_name'] = 'Usuario Demo';
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 'user';
                header('Location: dashboard.php');
                exit;
            }
            
            // Autenticación real
            $user = authenticateUser($email, $password);
            
            if ($user) {
                if ($user['status'] !== 'active') {
                    $error_message = 'Tu cuenta está desactivada. Contacta al administrador.';
                } else {
                    // Iniciar sesión
                    loginUser($user['id'], $user['email'], $user['name'], $user['role']);
                            // Redirigir a index.php con mensaje de éxito
        header('Location: index.php?login=success'); 
        exit;
                    
                    // Configurar "Remember Me"
                    if ($remember_me) {
                        $token = bin2hex(random_bytes(32));
                        setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', true, true);
                        
                        $db = Database::getInstance();
                        $db->execute(
    "UPDATE usuarios SET remember_token = ?, last_login = ? WHERE id = ?",
    [$token, date('Y-m-d H:i:s'), $user['id']]
);
                    } else {
                        $db = Database::getInstance();
                        $db->execute(
    "UPDATE usuarios SET last_login = ? WHERE id = ?",
    [date('Y-m-d H:i:s'), $user['id']]
);  
                    }
                    
                    // Redirigir según rol
                    header('Location: ' . ($user['role'] === 'admin' ? 'admin.php' : 'dashboard.php'));
                    exit;
                }
            } else {
                $error_message = 'Email o contraseña incorrectos.';
            }
        }
    } elseif ($action === 'register') {
        // Procesar registro
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $terms = isset($_POST['terms']);
        
        // Validaciones
        if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
            $error_message = 'Por favor, completa todos los campos.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = 'Por favor, introduce un email válido.';
        } elseif (strlen($password) < 6) {
            $error_message = 'La contraseña debe tener al menos 6 caracteres.';
        } elseif ($password !== $confirm_password) {
            $error_message = 'Las contraseñas no coinciden.';
        } elseif (!$terms) {
            $error_message = 'Debes aceptar los términos y condiciones.';
        } else {
            $result = registerUser($name, $email, $password);
            if ($result['success']) {
                $success_message = $result['message'];
            } else {
                $error_message = $result['message'];
            }
        }
    }
}

// Verificar cookie "Remember Me"
if (isset($_COOKIE['remember_token']) && !isLoggedIn()) {
    $token = $_COOKIE['remember_token'];
    $db = Database::getInstance();
    
    $user = $db->fetchOne(
        "SELECT id, email, name, role FROM usuarios WHERE remember_token = ? AND status = 'active'",
        [$token]
    );
    
    if ($user) {
        loginUser($user['id'], $user['email'], $user['name'], $user['role']);
        $db->execute(
    "UPDATE usuarios SET last_login = ? WHERE id = ?",
    [date('Y-m-d H:i:s'), $user['id']]
);
        
        header('Location: ' . ($user['role'] === 'admin' ? 'admin.php' : 'index.php'));
        exit;
    } else {
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - TechShop</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            padding: 15px 0;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #ff6b35;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-link {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .back-link:hover {
            background: #f0f0f0;
            color: #ff6b35;
        }

        /* Main Container */
        .main-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .auth-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            position: relative;
            overflow: hidden;
        }

        .auth-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ff6b35, #f9ca24, #ff6b35);
            background-size: 200% 100%;
            animation: shimmer 2s ease-in-out infinite;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-title {
            font-size: 28px;
            color: #333;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .auth-subtitle {
            color: #666;
            font-size: 16px;
        }

        /* Tabs */
        .auth-tabs {
            display: flex;
            background: #f8f9fa;
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 30px;
        }

        .tab-button {
            flex: 1;
            padding: 12px 20px;
            border: none;
            background: transparent;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .tab-button.active {
            background: white;
            color: #ff6b35;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* Forms */
        .auth-form {
            display: none;
        }

        .auth-form.active {
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            background: white;
        }

        .form-input:focus {
            outline: none;
            border-color: #ff6b35;
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        .form-input.error {
            border-color: #ff4757;
        }

        .input-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            pointer-events: none;
        }

        .form-group.has-icon .form-input {
            padding-right: 45px;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            padding: 4px;
        }

        .password-toggle:hover {
            color: #ff6b35;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .checkbox {
            width: 18px;
            height: 18px;
            accent-color: #ff6b35;
        }

        .checkbox-label {
            font-size: 14px;
            color: #666;
            cursor: pointer;
        }

        .auth-button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #ff6b35, #f9ca24);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .auth-button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
        }

        .auth-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .auth-links {
            text-align: center;
            margin-top: 20px;
        }

        .auth-link {
            color: #ff6b35;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .auth-link:hover {
            text-decoration: underline;
        }

        /* Alerts */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-error {
            background: #fff5f5;
            color: #c53030;
            border: 1px solid #fed7d7;
        }

        .alert-success {
            background: #f0fff4;
            color: #38a169;
            border: 1px solid #c6f6d5;
        }

        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .demo-credentials {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 14px;
            color: #666;
            text-align: center;
            border: 1px solid #e0e0e0;
        }

        .demo-credentials strong {
            color: #333;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .auth-container {
                margin: 20px;
                padding: 30px 25px;
            }

            .header-container {
                padding: 0 15px;
            }

            .logo {
                font-size: 24px;
            }

            .auth-title {
                font-size: 24px;
            }

            .main-container {
                padding: 20px 15px;
            }

            .tab-button {
                font-size: 13px;
                padding: 10px 16px;
            }
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .shake {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <a href="index.php" class="logo">
                <i class="fas fa-bolt"></i>
                TechShop
            </a>
            <a href="index.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Volver a la tienda
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-container">
        <div class="auth-container fade-in">
            <div class="auth-header">
                <h1 class="auth-title">¡Bienvenido!</h1>
                <p class="auth-subtitle">Accede a tu cuenta o crea una nueva</p>
            </div>

            <!-- Tabs -->
            <div class="auth-tabs">
                <button class="tab-button active" onclick="switchTab('login')">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar Sesión
                </button>
                <button class="tab-button" onclick="switchTab('register')">
                    <i class="fas fa-user-plus"></i>
                    Registrarse
                </button>
            </div>

            <!-- Alerts -->
            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form class="auth-form active" id="login-form" method="POST" action="">
                <input type="hidden" name="action" value="login">
                
                <div class="form-group has-icon">
                    <label class="form-label" for="login-email">Email</label>
                    <input type="email" id="login-email" name="email" class="form-input" 
                           placeholder="tu@email.com" required>
                    <i class="input-icon fas fa-envelope"></i>
                </div>

                <div class="form-group has-icon">
                    <label class="form-label" for="login-password">Contraseña</label>
                    <input type="password" id="login-password" name="password" class="form-input" 
                           placeholder="Tu contraseña" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('login-password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="remember_me" name="remember_me" class="checkbox">
                    <label for="remember_me" class="checkbox-label">Recordarme por 30 días</label>
                </div>

                <button type="submit" class="auth-button">
                    <span class="button-text">Iniciar Sesión</span>
                    <div class="loading" style="display: none;"></div>
                </button>

                <div class="auth-links">
                    <a href="forgot-password.php" class="auth-link">¿Olvidaste tu contraseña?</a>
                </div>
            </form>

            <!-- Register Form -->
            <form class="auth-form" id="register-form" method="POST" action="">
                <input type="hidden" name="action" value="register">
                
                <div class="form-group has-icon">
                    <label class="form-label" for="register-name">Nombre completo</label>
                    <input type="text" id="register-name" name="name" class="form-input" 
                           placeholder="Tu nombre completo" required>
                    <i class="input-icon fas fa-user"></i>
                </div>

                <div class="form-group has-icon">
                    <label class="form-label" for="register-email">Email</label>
                    <input type="email" id="register-email" name="email" class="form-input" 
                           placeholder="tu@email.com" required>
                    <i class="input-icon fas fa-envelope"></i>
                </div>

                <div class="form-group has-icon">
                    <label class="form-label" for="register-password">Contraseña</label>
                    <input type="password" id="register-password" name="password" class="form-input" 
                           placeholder="Mínimo 6 caracteres" required minlength="6">
                    <button type="button" class="password-toggle" onclick="togglePassword('register-password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="form-group has-icon">
                    <label class="form-label" for="confirm-password">Confirmar contraseña</label>
                    <input type="password" id="confirm-password" name="confirm_password" class="form-input" 
                           placeholder="Repite tu contraseña" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('confirm-password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="terms" name="terms" class="checkbox" required>
                    <label for="terms" class="checkbox-label">
                        Acepto los <a href="#" class="auth-link">términos y condiciones</a>
                    </label>
                </div>

                <button type="submit" class="auth-button">
                    <span class="button-text">Crear Cuenta</span>
                    <div class="loading" style="display: none;"></div>
                </button>
            </form>

            <!-- Demo credentials -->
            <div class="demo-credentials">
                <strong>Credenciales de prueba:</strong><br>
                Email: demo@techshop.com<br>
                Contraseña: demo123
            </div>
        </div>
    </div>

    <script>
        // Cambiar entre tabs
        function switchTab(tab) {
            // Actualizar botones
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            // Mostrar formulario correspondiente
            document.querySelectorAll('.auth-form').forEach(form => {
                form.classList.remove('active');
            });
            document.getElementById(tab + '-form').classList.add('active');

            // Limpiar mensajes de error
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (!alert.classList.contains('alert-success') || tab === 'login') {
                    alert.remove();
                }
            });

            // Focus en el primer campo
            setTimeout(() => {
                const firstInput = document.querySelector('.auth-form.active .form-input');
                if (firstInput) firstInput.focus();
            }, 100);
        }

        // Toggle password visibility
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Validación en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            // Validar confirmación de contraseña
            const password = document.getElementById('register-password');
            const confirmPassword = document.getElementById('confirm-password');
            
            if (confirmPassword) {
                confirmPassword.addEventListener('input', function() {
                    if (password.value !== confirmPassword.value) {
                        confirmPassword.classList.add('error');
                    } else {
                        confirmPassword.classList.remove('error');
                    }
                });
            }

            // Validación de email
            document.querySelectorAll('input[type="email"]').forEach(input => {
                input.addEventListener('blur', function() {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (this.value && !emailRegex.test(this.value)) {
                        this.classList.add('error');
                    } else {
                        this.classList.remove('error');
                    }
                });

                input.addEventListener('input', function() {
                    this.classList.remove('error');
                });
            });

            // Agregar loading a los formularios
            document.querySelectorAll('.auth-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const button = this.querySelector('.auth-button');
                    const buttonText = button.querySelector('.button-text');
                    const loading = button.querySelector('.loading');
                    
                    // Validar formulario antes de mostrar loading
                    if (!this.checkValidity()) {
                        return;
                    }

                    // Validaciones adicionales para registro
                    if (this.id === 'register-form') {
                        const password = this.querySelector('#register-password').value;
                        const confirmPassword = this.querySelector('#confirm-password').value;
                        const terms = this.querySelector('#terms').checked;

                        if (password !== confirmPassword) {
                            e.preventDefault();
                            alert('Las contraseñas no coinciden.');
                            return;
                        }

                        if (!terms) {
                            e.preventDefault();
                            alert('Debes aceptar los términos y condiciones.');
                            return;
                        }
                    }
                    
                    button.disabled = true;
                    buttonText.style.opacity = '0';
                    loading.style.display = 'inline-block';
                });
            });

            // Animación de shake para errores
            <?php if ($error_message): ?>
                document.querySelector('.auth-container').classList.add('shake');
                setTimeout(() => {
                    document.querySelector('.auth-container').classList.remove('shake');
                }, 500);
            <?php endif; ?>

            // Auto-hide alerts después de 5 segundos
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });

            // Auto-focus en el primer campo
            const firstInput = document.querySelector('.auth-form.active .form-input');
            if (firstInput) firstInput.focus();
        });
    </script>
</body>
</html>