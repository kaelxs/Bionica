<?php
header('Content-Type: application/json');

require_once '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $simulacion_id = isset($_GET['simulacion_id']) ? (int)$_GET['simulacion_id'] : 0;
    
    if ($simulacion_id > 0) {
        try {
            $pdo = getDB();
            
            $stmt = $pdo->prepare("SELECT * FROM interacciones WHERE simulacion_id = ? ORDER BY timestamp");
            $stmt->execute([$simulacion_id]);
            $mensajes = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'mensajes' => $mensajes
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Error de base de datos: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'ID de simulación inválido'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido'
    ]);
}
?>