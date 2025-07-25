<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

// Asegúrate de que la ruta sea correcta
require_once '../includes/conexion.php';

class AgentePaciente {
    private $pdo;
    
    public function __construct($database) {
        $this->pdo = $database;
    }
    
    // Simular respuesta del paciente
    public function generarRespuesta($mensaje, $contexto) {
        // Lógica de procesamiento del mensaje
        $respuesta = $this->procesarPregunta($mensaje, $contexto);
        return $respuesta;
    }
    
    private function procesarPregunta($pregunta, $contexto) {
        // Análisis simple de la pregunta
        $pregunta = strtolower($pregunta);
        
        // Decodificar datos del caso si vienen como string
        $caso = $contexto['datos_caso'];
        if (is_string($caso)) {
            $caso = json_decode($caso, true);
        }
        
        if (strpos($pregunta, 'dolor') !== false) {
            return "El dolor es intenso, " . ($caso['respuestas']['dolor'] ?? 'muy fuerte') . ". Me duele especialmente cuando " . $this->generarContextoDolor();
        }
        elseif (strpos($pregunta, 'ansiedad') !== false || strpos($pregunta, 'preocupado') !== false || strpos($pregunta, 'miedo') !== false) {
            return "Sí, me siento muy " . ($caso['respuestas']['ansiedad'] ?? 'ansioso') . ". Tengo miedo de lo que pueda estar pasando.";
        }
        elseif (strpos($pregunta, 'antecedente') !== false) {
            return $this->generarAntecedentes($caso);
        }
        elseif (strpos($pregunta, 'medicamento') !== false) {
            return $this->generarMedicamentos($caso);
        }
        elseif (strpos($pregunta, 'cuando') !== false && strpos($pregunta, 'comenz') !== false) {
            return "Los síntomas comenzaron hace " . rand(2, 24) . " horas. Fue de forma repentina.";
        }
        else {
            return $this->respuestaAleatoria();
        }
    }
    
    private function generarContextoDolor() {
        $contextos = [
            "respiro profundamente",
            "me muevo",
            "estoy nervioso",
            "hago ejercicio"
        ];
        return $contextos[array_rand($contextos)];
    }
    
    private function generarAntecedentes($caso) {
        if (!empty($caso['historia_clinica'])) {
            return "Tengo " . $caso['historia_clinica'] . ". Además, he tenido problemas similares antes.";
        }
        return "No tengo antecedentes médicos importantes.";
    }
    
    private function generarMedicamentos($caso) {
        if (!empty($caso['medicamentos']) && is_array($caso['medicamentos'])) {
            if (count($caso['medicamentos']) > 0) {
                return "Actualmente tomo " . implode(", ", $caso['medicamentos']) . " según me lo recetaron.";
            }
        }
        return "No estoy tomando ningún medicamento actualmente.";
    }
    
    private function respuestaAleatoria() {
        $respuestas = [
            "Entiendo su preocupación, doctor. ¿Puede explicarme más sobre eso?",
            "Esa es una buena pregunta. Déjeme pensar...",
            "Me siento un poco cansado últimamente, además de los síntomas principales.",
            "He notado que los síntomas empeoran por la noche.",
            "Tengo miedo de lo que esto pueda significar para mi salud."
        ];
        return $respuestas[array_rand($respuestas)];
    }
}

// Manejo de solicitudes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['accion'])) {
        try {
            $agente = new AgentePaciente(getDB());
            
            switch ($input['accion']) {
                case 'responder':
                    $respuesta = $agente->generarRespuesta(
                        $input['mensaje'], 
                        $input['contexto']
                    );
                    
                    echo json_encode([
                        'success' => true,
                        'respuesta' => $respuesta,
                        'timestamp' => date('Y-m-d H:i:s')
                    ]);
                    break;
                    
                default:
                    echo json_encode([
                        'success' => false,
                        'error' => 'Acción no reconocida'
                    ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Falta parámetro acción'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido'
    ]);
}
?>