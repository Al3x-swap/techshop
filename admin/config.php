<?php
require_once '../config/database.php';
require_once '../includes/session.php';

// Verificar que el usuario sea admin
requireAdmin();

// Funciones específicas del admin
function getTotalProducts() {
    global $pdo;
    return $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();
}

function getTotalUsers() {
    // Lógica para contar usuarios
}

function getDeletedUsers() {
    // Lógica para contar usuarios eliminados
}

function getLowStockProducts() {
    // Productos con stock bajo
}

function getRecentOrders() {
    // Órdenes recientes
}

function formatActivityDate($fecha) {
    $now = new DateTime();
    $fecha = new DateTime($fecha);
    $diff = $now->diff($fecha);
    
    if ($diff->d > 0) return "Hace {$diff->d} día" . ($diff->d > 1 ? 's' : '');
    if ($diff->h > 0) return "Hace {$diff->h} hora" . ($diff->h > 1 ? 's' : '');
    return "Hace unos minutos";
}

// En config.php
function getDashboardStats() {
    global $pdo;
    $stats = $pdo->query("
        SELECT 
            (SELECT COUNT(*) FROM productos) as total_products,
            (SELECT COUNT(*) FROM productos WHERE stock > 0) as products_in_stock,
            (SELECT SUM(total) FROM pedidos WHERE estado = 'completado') as total_revenue
        ")->fetch(PDO::FETCH_ASSOC);
    return $stats;
}
?>