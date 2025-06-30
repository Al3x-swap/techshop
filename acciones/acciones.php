<?php
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

session_start();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $productId = $input['product_id'] ?? null;
    $quantity = $input['quantity'] ?? 1;

    // Validaciones básicas
    if (!$productId || !is_numeric($productId)) {
        throw new Exception("ID de producto inválido");
    }

    $db = new Database();
    $conn = $db->getConnection();

    // 1. Verificar stock disponible
    $stmt = $conn->prepare("
        SELECT 
            (stock - reserved_stock) AS available 
        FROM productos 
        WHERE id = ?
    ");
    $stmt->execute([$productId]);
    $stock = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$stock || $stock['available'] < $quantity) {
        throw new Exception("Stock insuficiente");
    }

    // 2. Reservar stock (transacción)
    $conn->beginTransaction();
    
    $update = $conn->prepare("
        UPDATE productos 
        SET reserved_stock = reserved_stock + ? 
        WHERE id = ?
    ");
    $update->execute([$quantity, $productId]);

    // Aquí iría tu lógica para agregar al carrito...
    // Ejemplo simplificado:
    $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + $quantity;

    $conn->commit();

    // 3. Devolver stock actualizado
    $stmt = $conn->prepare("
        SELECT 
            id AS product_id,
            stock AS total_stock,
            reserved_stock,
            (stock - reserved_stock) AS available_stock
        FROM productos 
        WHERE id = ?
    ");
    $stmt->execute([$productId]);
    $updatedStock = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'cart_count' => array_sum($_SESSION['cart'] ?? []),
        'updated_stock' => $updatedStock
    ]);

} catch (Exception $e) {
    $conn->rollBack();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
