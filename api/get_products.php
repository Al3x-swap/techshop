<?php
header('Content-Type: application/json');

// Configuración de la base de datos (ejemplo)
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'techshop';

try {
    $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $search = isset($_POST['search']) ? "%{$_POST['search']}%" : '%';
    
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE :search OR description LIKE :search");
    $stmt->bindParam(':search', $search);
    $stmt->execute();
    
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'products' => $products
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
}
?>
