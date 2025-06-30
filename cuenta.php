<?php
// config.php - Configuración de la base de datos
class Database {
    private $host = 'localhost';
    private $db_name = 'techshop_db';
    private $username = 'root'; // Cambiar por tu usuario de DB
    private $password = '';     // Cambiar por tu contraseña de DB
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

// User.php - Clase para manejo de usuarios
class User {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Crear usuario
    public function create($name, $email, $password, $phone = null) {
        try {
            // Verificar si el email ya existe
            if ($this->emailExists($email)) {
                return ['success' => false, 'message' => 'Este email ya está registrado'];
            }
            
            $query = "INSERT INTO users (name, email, password, phone, created_at) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($query);
            
            // Encriptar contraseña
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt->execute([$name, $email, $hashed_password, $phone]);
            
            return ['success' => true, 'message' => 'Usuario creado exitosamente'];
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Error al crear usuario: ' . $e->getMessage()];
        }
    }
    
    // Verificar si email existe
    public function emailExists($email) {
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }
    
    // Autenticar usuario
    public function authenticate($email, $password) {
        try {
            $query = "SELECT id, name, email, password, is_active FROM users WHERE email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$user['is_active']) {
                    return ['success' => false, 'message' => 'Cuenta desactivada'];
                }
                
                if (password_verify($password, $user['password'])) {
                    return [
                        'success' => true, 
                        'user' => [
                            'id' => $user['id'],
                            'name' => $user['name'],
                            'email' => $user['email']
                        ]
                    ];
                }
            }
            
            return ['success' => false, 'message' => 'Email o contraseña incorrectos'];
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Error de autenticación'];
        }
    }
    
    // Obtener usuario por ID
    public function getUserById($id) {
        try {
            $query = "SELECT id, name, email, phone, created_at FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() == 1) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return null;
            
        } catch(PDOException $e) {
            return null;
        }
    }
}

// registro.php - Script principal
session_start();

// Incluir clases (normalmente estarían en archivos separados)
$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$error = '';
$success = '';
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : 'login');

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'register') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $terms = isset($_POST['terms']);
        
        // Validaciones
        if (empty($name) || empty($email) || empty($password)) {
            $error = 'Por favor completa todos los campos obligatorios';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Email no válido';
        } elseif (strlen($password) < 6) {
            $error = 'La contraseña debe tener al menos 6 caracteres';
        } elseif ($password !== $confirm_password) {
            $error = 'Las contraseñas no coinciden';
        } elseif (!$terms) {
            $error = 'Debes aceptar los términos y condiciones';
        } else {
            // Crear usuario
            $result = $user->create($name, $email, $password, $phone);
            
            if ($result['success']) {
                $success = $result['message'] . ' Ya puedes iniciar sesión.';
                $action = 'login';
            } else {
                $error = $result['message'];
            }
        }
        
    } elseif ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $error = 'Por favor completa todos los campos';
        } else {
            $result = $user->authenticate($email, $password);
            
            if ($result['success']) {
                // Iniciar sesión
                $_SESSION['user_id'] = $result['user']['id'];
                $_SESSION['user_email'] = $result['user']['email'];
                $_SESSION['user_name'] = $result['user']['name'];
                $_SESSION['logged_in'] = true;
                
                header('Location: perfil.php');
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}

// Cerrar sesión
if ($action === 'logout') {
    session_destroy();
    header('Location: registro.php');
    exit;
}

// Si ya está logueado, redirigir al perfil
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && $action !== 'logout') {
    header('Location: perfil.php');
    exit;
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
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 800px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 600px;
        }

        .visual-section {
            background: linear-gradient(45deg, #ff6b35, #f7931e);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            color: white;
            text-align: center;
        }

        .visual-icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.9;
        }

        .visual-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .visual-subtitle {
            font-size: 16px;
            opacity: 0.9;
            line-height: 1.5;
        }

        .form-section {
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }

        .form-subtitle {
            color: #666;
            font-size: 16px;
        }

        .tabs {
            display: flex;
            margin-bottom: 30px;
            background: #f5f5f5;
            border-radius: 10px;
            padding: 4px;
        }

        .tab-btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            background: transparent;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s;
            color: #666;
        }

        .tab-btn.active {
            background: white;
            color: #ff6b35;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #333;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            background: #f9f9f9;
        }

        .form-input:focus {
            outline: none;
            border-color: #ff6b35;
            background: white;
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
        }

        .checkbox {
            width: 18px;
            height: 18px;
            accent-color: #ff6b35;
        }

        .checkbox-label {
            font-size: 14px;
            color: #666;
        }

        .checkbox-label a {
            color: #ff6b35;
            text-decoration: none;
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            background: #ff6b35;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 20px;
        }

        .submit-btn:hover {
            background: #e55a2b;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }

        .alert-error {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #ff6b35;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
                margin: 10px;
            }

            .visual-section {
                padding: 30px 20px;
                min-height: 200px;
            }

            .form-section {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="visual-section">
            <div class="visual-icon">
                <i class="fas fa-user-circle"></i>
            </div>
            <h2 class="visual-title">
                <?php echo $action === 'register' ? '¡Únete a TechShop!' : '¡Bienvenido de vuelta!'; ?>
            </h2>
            <p class="visual-subtitle">
                <?php echo $action === 'register' 
                    ? 'Crea tu cuenta y descubre los mejores productos tecnológicos.' 
                    : 'Accede a tu cuenta para continuar explorando.'; ?>
            </p>
        </div>

        <div class="form-section">
            <div class="form-header">
                <h1 class="form-title">Mi Cuenta</h1>
                <p class="form-subtitle">
                    <?php echo $action === 'register' ? 'Crea tu cuenta gratuita' : 'Inicia sesión'; ?>
                </p>
            </div>

            <div class="tabs">
                <button class="tab-btn <?php echo $action === 'login' ? 'active' : ''; ?>" 
                        onclick="switchTab('login')">
                    Iniciar Sesión
                </button>
                <button class="tab-btn <?php echo $action === 'register' ? 'active' : ''; ?>" 
                        onclick="switchTab('register')">
                    Registrarse
                </button>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Formulario de Login -->
            <form method="POST" id="loginForm" style="<?php echo $action === 'login' ? 'display: block;' : 'display: none;'; ?>">
                <input type="hidden" name="action" value="login">
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" name="email" placeholder="tu@email.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input type="password" class="form-input" name="password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar Sesión
                </button>
            </form>

            <!-- Formulario de Registro -->
            <form method="POST" id="registerForm" style="<?php echo $action === 'register' ? 'display: block;' : 'display: none;'; ?>">
                <input type="hidden" name="action" value="register">
                
                <div class="form-group">
                    <label class="form-label">Nombre completo *</label>
                    <input type="text" class="form-input" name="name" placeholder="Juan Pérez" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" class="form-input" name="email" placeholder="tu@email.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="tel" class="form-input" name="phone" placeholder="+52 55 1234 5678">
                </div>

                <div class="form-group">
                    <label class="form-label">Contraseña *</label>
                    <input type="password" class="form-input" name="password" placeholder="••••••••" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirmar Contraseña *</label>
                    <input type="password" class="form-input" name="confirm_password" placeholder="••••••••" required>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" class="checkbox" name="terms" required>
                    <label class="checkbox-label">
                        Acepto los <a href="#" target="_blank">términos y condiciones</a>
                    </label>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-user-plus"></i>
                    Crear Cuenta
                </button>
            </form>

            <div class="back-link">
                <a href="index.php">
                    <i class="fas fa-arrow-left"></i>
                    Volver a la tienda
                </a>
            </div>
        </div>
    </div>

    <script>
        function switchTab(action) {
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            
            if (action === 'login') {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
            } else {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
            }
        }
    </script>
</body>
</html>