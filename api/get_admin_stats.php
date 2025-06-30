// get_admin_stats.php
require_once '../config/database.php';
require_once '../includes/session.php';
requireAdmin();

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'data' => [
        'total_products' => $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn(),
        // ... otras stats
    ]
]);