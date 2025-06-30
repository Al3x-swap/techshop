<?php
function logActivity($tipo, $descripcion, $icono = null) {
    global $pdo;
    
    $iconos = [
        'new' => 'plus',
        'update' => 'edit',
        'delete' => 'trash'
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO actividad (tipo, icono, descripcion, usuario_id)
        VALUES (?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $tipo,
        $icono ?? $iconos[$tipo] ?? 'info-circle',
        $descripcion,
        $_SESSION['user_id'] ?? null
    ]);
}
