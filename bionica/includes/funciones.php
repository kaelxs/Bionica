<?php
require_once 'conexion.php';

// Función para registrar usuario
function registrarUsuario($nombre, $email, $password) {
    $pdo = getDB();
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$nombre, $email, $hashed_password]);
    } catch(PDOException $e) {
        error_log("Error registrando usuario: " . $e->getMessage());
        return false;
    }
}

// Función para iniciar sesión
function iniciarSesion($email, $password) {
    $pdo = getDB();
    
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    
    if ($usuario && password_verify($password, $usuario['password'])) {
        session_start();
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['tipo'] = $usuario['tipo'];
        $_SESSION['nivel'] = $usuario['nivel'];
        return true;
    }
    return false;
}

// Función para obtener casos clínicos
function obtenerCasosClinicos($dificultad = null, $especialidad = null) {
    $pdo = getDB();
    
    $sql = "SELECT * FROM casos_clinicos WHERE activo = 1";
    $params = [];
    
    if ($dificultad) {
        $sql .= " AND dificultad = ?";
        $params[] = $dificultad;
    }
    
    if ($especialidad) {
        $sql .= " AND especialidad = ?";
        $params[] = $especialidad;
    }
    
    $sql .= " ORDER BY id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll();
}

// Función para obtener un caso específico
function obtenerCasoPorId($id) {
    $pdo = getDB();
    
    $stmt = $pdo->prepare("SELECT * FROM casos_clinicos WHERE id = ? AND activo = 1");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Función para iniciar una simulación
function iniciarSimulacion($usuario_id, $caso_id) {
    $pdo = getDB();
    
    $stmt = $pdo->prepare("INSERT INTO simulaciones (usuario_id, caso_id) VALUES (?, ?)");
    $stmt->execute([$usuario_id, $caso_id]);
    return $pdo->lastInsertId();
}

// Función para guardar interacción
function guardarInteraccion($simulacion_id, $tipo, $contenido) {
    $pdo = getDB();
    
    $stmt = $pdo->prepare("INSERT INTO interacciones (simulacion_id, tipo, contenido) VALUES (?, ?, ?)");
    return $stmt->execute([$simulacion_id, $tipo, $contenido]);
}

// Función para obtener historial de simulaciones
function obtenerHistorialSimulaciones($usuario_id) {
    $pdo = getDB();
    
    $stmt = $pdo->prepare("
        SELECT s.*, c.titulo as caso_titulo, c.especialidad, c.dificultad 
        FROM simulaciones s 
        JOIN casos_clinicos c ON s.caso_id = c.id 
        WHERE s.usuario_id = ? 
        ORDER BY s.fecha_inicio DESC
    ");
    $stmt->execute([$usuario_id]);
    return $stmt->fetchAll();
}

// Función para obtener datos de simulación completa
function obtenerDatosSimulacion($simulacion_id, $usuario_id = null) {
    $pdo = getDB();
    
    $sql = "
        SELECT s.*, c.*, c.datos_json as datos_caso 
        FROM simulaciones s 
        JOIN casos_clinicos c ON s.caso_id = c.id 
        WHERE s.id = ?";
    
    $params = [$simulacion_id];
    
    if ($usuario_id) {
        $sql .= " AND s.usuario_id = ?";
        $params[] = $usuario_id;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
}

// Función para obtener historial de interacciones
function obtenerHistorialInteracciones($simulacion_id) {
    $pdo = getDB();
    
    $stmt = $pdo->prepare("SELECT * FROM interacciones WHERE simulacion_id = ? ORDER BY timestamp");
    $stmt->execute([$simulacion_id]);
    return $stmt->fetchAll();
}

// Función para finalizar simulación
function finalizarSimulacion($simulacion_id, $puntuacion = null, $tiempo = null) {
    $pdo = getDB();
    
    $sql = "UPDATE simulaciones SET estado = 'completada'";
    $params = [];
    
    if ($puntuacion !== null) {
        $sql .= ", puntuacion = ?";
        $params[] = $puntuacion;
    }
    
    if ($tiempo !== null) {
        $sql .= ", tiempo_duracion = ?";
        $params[] = $tiempo;
    }
    
    $sql .= ", fecha_fin = NOW() WHERE id = ?";
    $params[] = $simulacion_id;
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

// Función para guardar reporte
function guardarReporte($usuario_id, $simulacion_id, $datos) {
    $pdo = getDB();
    
    $stmt = $pdo->prepare("
        INSERT INTO reportes 
        (usuario_id, simulacion_id, precision_diagnostica, habilidades_comunicacion, 
         tiempo_respuesta_promedio, areas_mejora, recomendaciones, datos_analisis) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    return $stmt->execute([
        $usuario_id, $simulacion_id,
        $datos['precision_diagnostica'],
        $datos['habilidades_comunicacion'],
        $datos['tiempo_respuesta_promedio'],
        $datos['areas_mejora'],
        $datos['recomendaciones'],
        json_encode($datos['analisis_completo'])
    ]);
}

// Función para obtener reporte
function obtenerReporte($reporte_id) {
    $pdo = getDB();
    
    $stmt = $pdo->prepare("
        SELECT r.*, u.nombre as usuario_nombre, c.titulo as caso_titulo
        FROM reportes r
        JOIN usuarios u ON r.usuario_id = u.id
        JOIN simulaciones s ON r.simulacion_id = s.id
        JOIN casos_clinicos c ON s.caso_id = c.id
        WHERE r.id = ?
    ");
    $stmt->execute([$reporte_id]);
    return $stmt->fetch();
}

// Función para obtener estadísticas del usuario
function obtenerEstadisticasUsuario($usuario_id) {
    $pdo = getDB();
    
    // Total simulaciones
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM simulaciones WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    $total = $stmt->fetch()['total'];
    
    // Simulaciones completadas
    $stmt = $pdo->prepare("SELECT COUNT(*) as completadas FROM simulaciones WHERE usuario_id = ? AND estado = 'completada'");
    $stmt->execute([$usuario_id]);
    $completadas = $stmt->fetch()['completadas'];
    
    // Promedio de puntuación
    $stmt = $pdo->prepare("SELECT AVG(puntuacion) as promedio FROM simulaciones WHERE usuario_id = ? AND estado = 'completada' AND puntuacion IS NOT NULL");
    $stmt->execute([$usuario_id]);
    $promedio = $stmt->fetch()['promedio'];
    
    // Última simulación
    $stmt = $pdo->prepare("SELECT fecha_inicio FROM simulaciones WHERE usuario_id = ? ORDER BY fecha_inicio DESC LIMIT 1");
    $stmt->execute([$usuario_id]);
    $ultima = $stmt->fetch();
    
    return [
        'total_simulaciones' => $total,
        'simulaciones_completadas' => $completadas,
        'promedio_puntuacion' => round($promedio ?? 0, 1),
        'ultima_actividad' => $ultima ? $ultima['fecha_inicio'] : null
    ];
}
?>