<?php
header("Access-Control-Allow-Origin: *"); // Permite cualquier origen (solo para desarrollo local)

require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

// Configuración CORS para desarrollo
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");

try {
    $db = new Database();
    $conn = $db->getConnection();

    // 3. Obtener parámetros (GET para local)
    $productIds = explode(',', $_GET['product_ids'] ?? '');
    $lastCheck = $_GET['last_check'] ?? 0;

    // 4. Validar IDs
    $filteredIds = array_filter(array_map('intval', $productIds), fn($id) => $id > 0);

    // 5. Consulta SQL para cambios recientes
    $query = "SELECT 
                id AS product_id, 
                stock AS total_stock,
                reserved_stock,
                (stock - reserved_stock) AS available_stock,
                UNIX_TIMESTAMP(last_updated) AS last_updated,
                (stock - reserved_stock) <= low_stock_threshold AS is_low_stock
              FROM productos 
              WHERE id IN (" . implode(',', $filteredIds) . ")
              AND last_updated > FROM_UNIXTIME(?)";

    $stmt = $conn->prepare($query);
    $stmt->execute([$lastCheck]);

    // 6. Retornar respuesta
    echo json_encode([
        'success' => true,
        'updates' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);

} catch (Exception $e) {
    // 7. Manejo de errores (útil para debug local)
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => [
            'received_ids' => $productIds,
            'filtered_ids' => $filteredIds
        ]
    ]);
}