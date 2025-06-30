<?php
/**
 * Sistema de manejo de sesiones mejorado para TechShop
 * Archivo: session.php
 * Versión mejorada que combina funcionalidad básica con características avanzadas de seguridad
 */
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {

    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Strict');
    session_start();
}

/**
 * Verificar si el usuario está logueado
 * Versión mejorada con validaciones adicionales
 * @return bool
 */
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']) && 
               !empty($_SESSION['user_id']) &&
               isset($_SESSION['user_email']) && 
               isset($_SESSION['login_time']);
    }
}

/**
 * Verificar si el usuario es administrador
 * @return bool
 */
if (!function_exists('isAdmin')) {
// Agregar después de la función getUserInfo():
function isAdmin() {
    if (!isLoggedIn()) {
        return false;
    }
    $user = getUserInfo();
    return isset($user['tipo']) && $user['tipo'] === 'admin';
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ../login.php?error=admin_required');
        exit;
    }
}

/**
 * Obtener información del usuario (función original mejorada)
 * @return array|null
 */
if (!function_exists('getUserInfo')) {
    function getUserInfo() {
        if (!isLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'nombre' => $_SESSION['user_name'] ?? null,
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role'] ?? 'user',
            'login_time' => $_SESSION['login_time'] ?? null,
            'last_activity' => $_SESSION['last_activity'] ?? null
        ];
    }
}

/**
 * Obtener información completa del usuario actual
 * @return array|null
 */
if (!function_exists('getCurrentUser')) {
    function getCurrentUser() {
        return getUserInfo(); // Reutiliza la función original mejorada
    }
}

/**
 * Obtener el ID del usuario actual
 * @return int|null
 */
if (!function_exists('getCurrentUserId')) {
    function getCurrentUserId() {
        return isLoggedIn() ? $_SESSION['user_id'] : null;
    }
}

/**
 * Obtener el email del usuario actual
 * @return string|null
 */
if (!function_exists('getCurrentUserEmail')) {
    function getCurrentUserEmail() {
        return isLoggedIn() ? $_SESSION['user_email'] : null;
    }
}

/**
 * Obtener el nombre del usuario actual
 * @return string|null
 */
if (!function_exists('getCurrentUserName')) {
    function getCurrentUserName() {
        return isLoggedIn() ? ($_SESSION['user_name'] ?? null) : null;
    }
}

/**
 * Obtener el rol del usuario actual
 * @return string|null
 */
if (!function_exists('getCurrentUserRole')) {
    function getCurrentUserRole() {
        return isLoggedIn() ? ($_SESSION['user_role'] ?? 'user') : null;
    }
}

/**
 * Iniciar sesión de usuario
 * @param int $userId
 * @param string $email
 * @param string $name
 * @param string $role
 * @return bool
 */
if (!function_exists('loginUser')) {
    function loginUser($userId, $email, $name = null, $role = 'user') {
        // Regenerar ID de sesión por seguridad
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = $role;
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        
        // Generar token CSRF
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        
        // Log de actividad
        logSessionActivity('login', ['user_id' => $userId, 'email' => $email]);
        
        return true;
    }
}

/**
 * Cerrar sesión (función original mejorada)
 * @return void
 */
if (!function_exists('logout')) {
    function logout() {
        logoutUser();
    }
}

/**
 * Cerrar sesión del usuario (versión completa)
 * @return bool
 */
if (!function_exists('logoutUser')) {
    function logoutUser() {
        // Log de actividad antes de cerrar
        if (isLoggedIn()) {
            logSessionActivity('logout', ['user_id' => getCurrentUserId()]);
        }
        
        // Limpiar todas las variables de sesión
        $_SESSION = array();
        
        // Destruir la cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destruir la sesión
        session_destroy();
        
        // Redireccionar a login
        header('Location: login.php');
        exit();
    }
}

/**
 * Requiere que el usuario esté logueado (función original mejorada)
 * @param string $redirect_url
 * @return void
 */
if (!function_exists('requireLogin')) {
    function requireLogin($redirect_url = 'login.php') {
        if (!isLoggedIn()) {
            header('Location: ' . $redirect_url);
            exit();
        }
        
        // Mantener sesión activa
        maintainSession();
    }
}

/**
 * Verificar si la sesión ha expirado
 * @param int $timeout_duration Duración en segundos (por defecto 30 minutos)
 * @return bool
 */
if (!function_exists('isSessionExpired')) {
    function isSessionExpired($timeout_duration = 1800) {
        if (!isLoggedIn()) {
            return true;
        }
        
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity']) > $timeout_duration) {
            return true;
        }
        
        return false;
    }
}

/**
 * Actualizar la última actividad de la sesión
 */
if (!function_exists('updateLastActivity')) {
    function updateLastActivity() {
        if (isLoggedIn()) {
            $_SESSION['last_activity'] = time();
        }
    }
}

/**
 * Generar token CSRF
 * @return string
 */
if (!function_exists('generateCSRFToken')) {
    function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

/**
 * Verificar token CSRF
 * @param string $token
 * @return bool
 */
if (!function_exists('verifyCSRFToken')) {
    function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }
}

/**
 * Verificar permisos del usuario
 * @param string $required_role
 * @return bool
 */
if (!function_exists('hasPermission')) {
    function hasPermission($required_role) {
        if (!isLoggedIn()) {
            return false;
        }
        
        $user_role = getCurrentUserRole();
        
        // Jerarquía de roles
        $role_hierarchy = [
            'user' => 1,
            'admin' => 2,
            'super_admin' => 3
        ];
        
        $user_level = $role_hierarchy[$user_role] ?? 0;
        $required_level = $role_hierarchy[$required_role] ?? 0;
        
        return $user_level >= $required_level;
    }
}

/**
 * Limpiar sesión expirada
 */
if (!function_exists('cleanExpiredSession')) {
    function cleanExpiredSession() {
        if (isSessionExpired()) {
            logoutUser();
            return true;
        }
        return false;
    }
}

/**
 * Verificar y mantener sesión activa
 * Debe ser llamada en cada página protegida
 */
if (!function_exists('maintainSession')) {
    function maintainSession() {
        // Verificar si la sesión ha expirado
        if (cleanExpiredSession()) {
            header('Location: login.php?expired=1');
            exit;
        }
        
        // Actualizar última actividad
        updateLastActivity();
    }
}

/**
 * Middleware para verificar autenticación
 * @param string $required_role Rol requerido (opcional)
 * @param string $redirect_url URL de redirección si no está autenticado
 */
if (!function_exists('requireAuth')) {
    function requireAuth($required_role = null, $redirect_url = 'login.php') {
        if (!isLoggedIn()) {
            header('Location: ' . $redirect_url);
            exit;
        }
        
        if ($required_role && !hasPermission($required_role)) {
            header('Location: unauthorized.php');
            exit;
        }
        
        maintainSession();
    }
}

/**
 * Middleware específico para administradores
 * @param string $redirect_url
 */
if (!function_exists('requireAdmin')) {
    function requireAdmin($redirect_url = 'login.php') {
        requireAuth('admin', $redirect_url);
    }
}

/**
 * Establecer mensaje flash
 * @param string $type (success, error, warning, info)
 * @param string $message
 */
if (!function_exists('setFlashMessage')) {
    function setFlashMessage($type, $message) {
        $_SESSION['flash_messages'][] = [
            'type' => $type,
            'message' => $message
        ];
    }
}

/**
 * Obtener y limpiar mensajes flash
 * @return array
 */
if (!function_exists('getFlashMessages')) {
    function getFlashMessages() {
        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        return $messages;
    }
}

/**
 * Logging de actividad de sesión
 * @param string $action
 * @param array $data
 */
if (!function_exists('logSessionActivity')) {
    function logSessionActivity($action, $data = []) {
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'user_id' => getCurrentUserId(),
            'action' => $action,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'data' => $data
        ];
        
        // Log a archivo (puedes cambiarlo por base de datos)
        error_log('Session Activity: ' . json_encode($log_entry));
    }
}

// Funciones de compatibilidad para mantener el código existente
// Estas mantienen la funcionalidad del archivo original

/**
 * Alias para mantener compatibilidad con código existente
 * @deprecated Usar requireAuth() en su lugar
 */
if (!function_exists('checkAuth')) {
    function checkAuth() {
        requireLogin();
    }
}

/**
 * Verificar si es administrador (función simplificada)
 * @return bool
 */
if (!function_exists('checkAdmin')) {
    function checkAdmin() {
        return isAdmin();
    }
}

// Auto-limpieza de sesión al cargar el archivo
// Solo si hay una sesión activa
if (isLoggedIn()) {
    maintainSession();
}
 }
?>
