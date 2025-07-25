<?php
// Configuración de la base de datos
$host = 'localhost';
$dbname = 'edumedia_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Función para obtener conexión
function getDB() {
    global $pdo;
    return $pdo;
}

// Función para ejecutar consultas preparadas de forma segura
function ejecutarConsulta($sql, $params = []) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch(PDOException $e) {
        error_log("Error en consulta: " . $e->getMessage());
        return false;
    }
}

// Función para obtener un solo registro
function obtenerUnRegistro($sql, $params = []) {
    $stmt = ejecutarConsulta($sql, $params);
    return $stmt ? $stmt->fetch() : false;
}

// Función para obtener múltiples registros
function obtenerRegistros($sql, $params = []) {
    $stmt = ejecutarConsulta($sql, $params);
    return $stmt ? $stmt->fetchAll() : [];
}

// Función para insertar datos
function insertarDatos($tabla, $datos) {
    $campos = array_keys($datos);
    $valores = array_values($datos);
    $placeholders = implode(',', array_fill(0, count($campos), '?'));
    $sql = "INSERT INTO {$tabla} (" . implode(',', $campos) . ") VALUES ({$placeholders})";
    
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($valores);
        return $pdo->lastInsertId();
    } catch(PDOException $e) {
        error_log("Error insertando datos: " . $e->getMessage());
        return false;
    }
}

// Función para actualizar datos
function actualizarDatos($tabla, $datos, $condicion, $params_condicion = []) {
    $campos = array_keys($datos);
    $valores = array_values($datos);
    $sets = implode(' = ?, ', $campos) . ' = ?';
    $sql = "UPDATE {$tabla} SET {$sets} WHERE {$condicion}";
    
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_merge($valores, $params_condicion));
        return $stmt->rowCount();
    } catch(PDOException $e) {
        error_log("Error actualizando datos: " . $e->getMessage());
        return false;
    }
}

// Función para eliminar datos
function eliminarDatos($tabla, $condicion, $params = []) {
    $sql = "DELETE FROM {$tabla} WHERE {$condicion}";
    
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    } catch(PDOException $e) {
        error_log("Error eliminando datos: " . $e->getMessage());
        return false;
    }
}

// Función para sanitizar entrada de usuario
function sanitizarEntrada($dato) {
    return htmlspecialchars(strip_tags(trim($dato)));
}

// Función para validar email
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Función para validar contraseña (mínimo 6 caracteres)
function validarPassword($password) {
    return strlen($password) >= 6;
}
?>