<?php
/**
 * Configuración y Manejo de Base de Datos para TechShop
 * Archivo: config/database.php
 * Versión: 2.1 - Corregida y Optimizada
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'techshop');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');    
define('DEV_MODE', ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1'));

// Configuración adicional
define('DB_PORT', 3306);
define('DB_TIMEOUT', 30);

// Configuración de PDO optimizada
$dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_PERSISTENT         => false,
    PDO::ATTR_TIMEOUT            => DB_TIMEOUT,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
];

// Conexión global (mantenida para compatibilidad)
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    error_log('Error de conexión a la base de datos: ' . $e->getMessage());
    die('Error de conexión a la base de datos. Por favor, contacte al administrador.');
}

/**
 * Clase para manejo de base de datos con patrón Singleton
 */
class Database {
    private static $instance = null;
    private $connection;
    private $transactionLevel = 0;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
                PDO::ATTR_TIMEOUT            => DB_TIMEOUT,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET,
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
            throw new Exception('Error al conectar con la base de datos');
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Ejecutar consulta preparada
     * @param string $query
     * @param array $params
     * @return PDOStatement
     */
    public function prepare($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage() . " | Query: " . $query);
            throw new Exception('Error en la consulta a la base de datos');
        }
    }
    
    /**
     * Obtener un registro
     * @param string $query
     * @param array $params
     * @return array|false
     */
    public function fetchOne($query, $params = []) {
        $stmt = $this->prepare($query, $params);
        return $stmt->fetch();
    }
    
    /**
     * Obtener múltiples registros
     * @param string $query
     * @param array $params
     * @return array
     */
    public function fetchAll($query, $params = []) {
        $stmt = $this->prepare($query, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener un valor específico
     * @param string $query
     * @param array $params
     * @return mixed
     */
    public function fetchColumn($query, $params = []) {
        $stmt = $this->prepare($query, $params);
        return $stmt->fetchColumn();
    }

    /**
     * Ejecutar consulta sin retorno de datos
     * @param string $query
     * @param array $params
     * @return bool
     */
    public function execute($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Database Execute Error: " . $e->getMessage() . " | Query: " . $query);
            throw new Exception('Error ejecutando la consulta');
        }
    }
    
    /**
     * Insertar registro y obtener ID
     * @param string $table
     * @param array $data
     * @return string
     */
    public function insert($table, $data) {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $query = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->prepare($query, $data);
        
        return $this->lastInsertId();
    }
    
    /**
     * Actualizar registros
     * @param string $table
     * @param array $data
     * @param string $where
     * @param array $whereParams
     * @return int
     */
    public function update($table, $data, $where, $whereParams = []) {
        $set = [];
        foreach (array_keys($data) as $key) {
            $set[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $set);
        
        $query = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        $params = array_merge($data, $whereParams);
        
        $stmt = $this->prepare($query, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Eliminar registros
     * @param string $table
     * @param string $where
     * @param array $params
     * @return int
     */
    public function delete($table, $where, $params = []) {
        $query = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->prepare($query, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Obtener el último ID insertado
     * @return string
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Comenzar transacción
     */
    public function beginTransaction() {
        if ($this->transactionLevel == 0) {
            $this->connection->beginTransaction();
        }
        $this->transactionLevel++;
        return true;
    }
    
    /**
     * Confirmar transacción
     */
    public function commit() {
        $this->transactionLevel--;
        if ($this->transactionLevel == 0) {
            return $this->connection->commit();
        }
        return true;
    }
    
    /**
     * Revertir transacción
     */
    public function rollback() {
        if ($this->transactionLevel > 0) {
            $this->transactionLevel = 0;
            return $this->connection->rollback();
        }
        return true;
    }
    
    /**
     * Verificar conexión
     * @return bool
     */
    public function isConnected() {
        try {
            $this->connection->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}

/**
 * Obtener ID del usuario actual de la sesión
 * @return int|null
 */
function getCurrentUserId() {
    // Iniciar sesión si no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return $_SESSION['user_id'] ?? null;
}

/**
 * Función para logging de sesiones (mejorada)
 * @param string $action
 * @param array $data
 */
function logSessionActivity($action, $data = []) {
    $db = Database::getInstance();
    
    try {
        // Verificar si la tabla existe antes de insertar
        $tableExists = $db->fetchColumn(
            "SELECT COUNT(*) FROM information_schema.tables 
             WHERE table_schema = ? AND table_name = 'session_logs'",
            [DB_NAME]
        );
        
        if (!$tableExists) {
            error_log("Tabla session_logs no existe. Creando tabla...");
            createSessionLogsTable($db);
        }
        
        $logData = [
            'user_id' => $data['user_id'] ?? getCurrentUserId(),
            'action' => $action,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'data' => json_encode($data),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $db->insert('session_logs', $logData);
    } catch (Exception $e) {
        error_log("Error logging session activity: " . $e->getMessage());
    }
}

/**
 * Crear tabla de logs de sesión si no existe
 * @param Database $db
 */
function createSessionLogsTable($db) {
    $sql = "CREATE TABLE IF NOT EXISTS session_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        action VARCHAR(100) NOT NULL,
        ip_address VARCHAR(45) NOT NULL,
        user_agent TEXT,
        data JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_action (action),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->execute($sql);
}

/**
 * Sanitizar entrada de datos
 * @param mixed $data
 * @return mixed
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validar email
 * @param string $email
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validar que una cadena no esté vacía después de sanitizar
 * @param string $value
 * @return bool
 */
function validateRequired($value) {
    return !empty(trim($value));
}

/**
 * Validar longitud mínima y máxima de cadena
 * @param string $value
 * @param int $min
 * @param int $max
 * @return bool
 */
function validateLength($value, $min = 1, $max = 255) {
    $length = strlen(trim($value));
    return $length >= $min && $length <= $max;
}
?>
