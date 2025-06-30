<?php
header('Content-Type: application/json');
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit();
}

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($productId <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit();
}

// Configuración de la base de datos
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'techshop';

try {
    $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar si el producto ya está en el carrito
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->bindParam(':product_id', $productId);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // Actualizar cantidad
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + :quantity 
                               WHERE user_id = :user_id AND product_id = :product_id");
    } else {
        // Insertar nuevo registro
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) 
                               VALUES (:user_id, :product_id, :quantity)");
    }
    
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->bindParam(':product_id', $productId);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->execute();
    
    // Obtener conteo actualizado del carrito
    $stmt = $conn->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'cart_count' => $result['count'] ? $result['count'] : 0
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
}
?>