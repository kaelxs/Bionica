<?php
header('Content-Type: application/json');

require_once '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['simulacion_id']) && isset($input['tipo']) && isset($input['contenido'])) {
        try {
            $pdo = getDB();
            
            $stmt = $pdo->prepare("INSERT INTO interacciones (simulacion_id, tipo, contenido) VALUES (?, ?, ?)");
            $result = $stmt->execute([
                $input['simulacion_id'],
                $input['tipo'],
                $input['contenido']
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Interacción guardada correctamente',
                    'id' => $pdo->lastInsertId()
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Error al guardar la interacción'
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Error de base de datos: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Faltan parámetros requeridos'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido'
    ]);
}
?>