<?php
require_once '../../../includes/session.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../listar.php');
    exit;
}

if (!verifyCSRFToken($_POST['csrf_token'])) {
    $_SESSION['error'] = 'Token CSRF inválido';
    header('Location: ../listar.php');
    exit;
}

$productId = (int)$_POST['id'];

try {
    // Obtener estado actual
    $stmt = $pdo->prepare("SELECT activo FROM productos WHERE id = ?");
    $stmt->execute([$productId]);
    $currentStatus = $stmt->fetchColumn();
    
    // Cambiar estado
    $newStatus = $currentStatus ? 0 : 1;
    $stmt = $pdo->prepare("UPDATE productos SET activo = ? WHERE id = ?");
    $stmt->execute([$newStatus, $productId]);
    
    $_SESSION['success'] = 'Estado del producto actualizado';
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error al cambiar estado: ' . $e->getMessage();
}

header('Location: ../listar.php');
exit;