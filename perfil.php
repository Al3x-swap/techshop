<?php
session_start();
require 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Obtener datos del usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mi perfil - TechShop</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    /* Estilos copiados de registro.php */
    /* ... (los mismos estilos que en registro.php) ... */
    
    .profile-info {
      margin-bottom: 20px;
    }
    
    .profile-info p {
      margin-bottom: 10px;
    }
    
    .profile-actions {
      margin-top: 30px;
    }
    
    .btn-logout {
      background: #dc3545;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s;
    }
    
    .btn-logout:hover {
      background: #c82333;
    }
  </style>
</head>
<body>
  <header>
    <div class="header-container">
      <a href="index.php" class="logo">
        <i class="fas fa-bolt"></i>
        TechShop
      </a>
      <div class="header-actions">
        <a href="perfil.php" class="account-btn">
          <i class="fas fa-user"></i>
          Mi Cuenta
        </a>
      </div>
    </div>
  </header>

  <div class="register-container">
    <div class="register-form">
      <h2 class="register-title">Mi perfil</h2>
      
      <div class="profile-info">
        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?></p>
        <p><strong>Apellidos:</strong> <?php echo htmlspecialchars($usuario['apellidos']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
        <?php if ($usuario['telefono']): ?>
          <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($usuario['telefono']); ?></p>
        <?php endif; ?>
        <?php if ($usuario['direccion']): ?>
          <p><strong>Dirección:</strong> <?php echo htmlspecialchars($usuario['direccion']); ?></p>
        <?php endif; ?>
      </div>
      
      <div class="profile-actions">
        <a href="editar_perfil.php" class="btn-register">Editar perfil</a>
        <form action="logout.php" method="POST" style="margin-top: 10px;">
          <button type="submit" class="btn-logout">Cerrar sesión</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>