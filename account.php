<?php
require_once 'config/database.php';
require_once 'includes/session.php';

requireLogin();
$user = getUserInfo();

$stmt = $pdo->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY fecha_pedido DESC LIMIT 10");
$stmt->execute([$user['id']]);
$pedidos = $stmt->fetchAll();
?>
