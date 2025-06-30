<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_id']) && ($_SESSION['user_es_admin'] ?? 0) == 1;
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }
}

function redirectIfNotAdmin() {
    if (!isAdmin()) {
        header('Location: index.php');
        exit;
    }
}

function getUserData() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'nombre' => $_SESSION['user_nombre'],
            'apellidos' => $_SESSION['user_apellidos'],
            'email' => $_SESSION['user_email'],
            'telefono' => $_SESSION['user_telefono'] ?? null,
            'direccion' => $_SESSION['user_direccion'] ?? null,
            'es_admin' => $_SESSION['user_es_admin'] ?? 0
        ];
    }
    return null;
}

function showToast($message, $type = 'success') {
    $_SESSION['toast'] = [
        'message' => $message,
        'type' => $type
    ];
}

function displayToast() {
    if (!empty($_SESSION['toast'])) {
        $toast = $_SESSION['toast'];
        $class = $toast['type'] === 'error' ? 'error' : 'success';
        echo "<div class='toast {$class}'>{$toast['message']}</div>";
        unset($_SESSION['toast']);
    }
}
?>
