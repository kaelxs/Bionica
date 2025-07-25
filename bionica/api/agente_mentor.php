<?php
header('Content-Type: application/json');

require_once '../includes/conexion.php';

class AgenteMentor {
    private $pdo;
    
    public function __construct($database) {
        $this->pdo = $database;
    }
    
    // Analizar pregunta del estudiante
    public function analizarPregunta($pregunta, $contexto) {
        $analisis = $this->evaluarPregunta($pregunta, $contexto);
        return $analisis;
    }
    
    // Evaluar diagnóstico propuesto
    public function evaluarDiagnostico($diagnostico, $contexto) {
        $evaluacion = $this->analizarDiagnostico($diagnostico, $contexto);
        return $evaluacion;
    }
    
    // Generar retroalimentación
    public function generarFeedback($tipo, $analisis) {
        return $this->crearFeedback($tipo, $analisis);
    }
    
    private function evaluarPregunta($pregunta, $contexto) {
        $pregunta = strtolower($pregunta);
        $caso = json_decode($contexto['datos_caso'], true);
        
        $evaluacion = [
            'pertinencia' => 0,
            'profundidad' => 0,
            'orden' => 0,
            'observaciones' => []
        ];
        
        // Evaluar pertinencia
        if ($this->esPreguntaRelevante($pregunta, $caso)) {
            $evaluacion['pertinencia'] = rand(75, 95);
            $evaluacion['observaciones'][] = "✅ Excelente pregunta relevante para el caso";
        } else {
            $evaluacion['pertinencia'] = rand(25, 45);
            $evaluacion['observaciones'][] = "⚠️ La pregunta podría ser más específica al caso";
        }
        
        // Evaluar profundidad
        if (strlen($pregunta) > 25) {
            $evaluacion['profundidad'] = rand(70, 90);
            $evaluacion['observaciones'][] = "✅ Buena profundidad en la pregunta";
        } else {
            $evaluacion['profundidad'] = rand(30, 50);
            $evaluacion['observaciones'][] = "⚠️ Considera hacer preguntas más detalladas";
        }
        
        // Evaluar orden lógico
        $evaluacion['orden'] = $this->evaluarOrdenPreguntas($pregunta, $contexto);
        
        return $evaluacion;
    }
    
    private function esPreguntaRelevante($pregunta, $caso) {
        $sintomas = explode(',', strtolower($caso['sintomas_principales']));
        $relevantes = ['dolor', 'sintoma', 'cuando', 'como', 'antecedente', 'medicamento', 'familia', 'mejor', 'peor'];
        
        foreach ($sintomas as $sintoma) {
            if (strpos($pregunta, trim($sintoma)) !== false) {
                return true;
            }
        }
        
        foreach ($relevantes as $relevante) {
            if (strpos($pregunta, $relevante) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    private function evaluarOrdenPreguntas($pregunta, $contexto) {
        // Lógica simple para evaluar orden
        $historial = $contexto['historial'] ?? [];
        if (count($historial) < 3) {
            return rand(85, 95); // Bien en las primeras preguntas
        }
        return rand(65, 80); // Regular si hay muchas preguntas
    }
    
    private function analizarDiagnostico($diagnostico, $contexto) {
        $caso = json_decode($contexto['datos_caso'], true);
        $diagnosticos_posibles = $caso['diagnosticos_posibles'] ?? [];
        
        $evaluacion = [
            'precision' => 0,
            'justificacion' => 0,
            'completitud' => 0,
            'observaciones' => []
        ];
        
        $diagnostico = strtolower($diagnostico);
        
        // Verificar si el diagnóstico está entre los posibles
        $diagnostico_correcto = false;
        foreach ($diagnosticos_posibles as $diag) {
            if (strpos($diagnostico, strtolower($diag)) !== false) {
                $diagnostico_correcto = true;
                break;
            }
        }
        
        if ($diagnostico_correcto) {
            $evaluacion['precision'] = rand(80, 95);
            $evaluacion['observaciones'][] = "✅ Diagnóstico correcto y relevante";
        } else {
            $evaluacion['precision'] = rand(25, 45);
            $evaluacion['observaciones'][] = "⚠️ Considera revisar los síntomas principales nuevamente";
        }
        
        return $evaluacion;
    }
    
    private function crearFeedback($tipo, $analisis) {
        switch ($tipo) {
            case 'pregunta':
                return $this->feedbackPregunta($analisis);
            case 'diagnostico':
                return $this->feedbackDiagnostico($analisis);
            default:
                return "📊 Buen trabajo. Sigue practicando.";
        }
    }
    
    private function feedbackPregunta($analisis) {
        $feedback = "🎓 **Evaluación de tu pregunta:**\n";
        $feedback .= "🎯 **Pertinencia:** " . $analisis['pertinencia'] . "%\n";
        $feedback .= "🔍 **Profundidad:** " . $analisis['profundidad'] . "%\n";
        $feedback .= "📋 **Orden lógico:** " . $analisis['orden'] . "%\n\n";
        
        foreach ($analisis['observaciones'] as $obs) {
            $feedback .= $obs . "\n";
        }
        
        if ($analisis['pertinencia'] < 50) {
            $feedback .= "\n💡 **Sugerencias:**\n";
            $feedback .= "- Enfócate en los síntomas principales\n";
            $feedback .= "- Pregunta sobre antecedentes relevantes\n";
            $feedback .= "- Considera el contexto del paciente";
        }
        
        return $feedback;
    }
    
    private function feedbackDiagnostico($analisis) {
        $feedback = "📋 **Evaluación de tu diagnóstico:**\n";
        $feedback .= "🎯 **Precisión:** " . $analisis['precision'] . "%\n";
        $feedback .= "📝 **Justificación:** " . $analisis['justificacion'] . "%\n";
        $feedback .= "✅ **Completitud:** " . $analisis['completitud'] . "%\n\n";
        
        foreach ($analisis['observaciones'] as $obs) {
            $feedback .= $obs . "\n";
        }
        
        return $feedback;
    }
}

// Manejo de solicitudes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['accion'])) {
        $mentor = new AgenteMentor(getDB());
        
        switch ($input['accion']) {
            case 'analizar_pregunta':
                $analisis = $mentor->analizarPregunta(
                    $input['pregunta'], 
                    $input['contexto']
                );
                
                $feedback = $mentor->generarFeedback('pregunta', $analisis);
                
                echo json_encode([
                    'success' => true,
                    'analisis' => $analisis,
                    'feedback' => $feedback
                ]);
                break;
                
            case 'evaluar_diagnostico':
                $evaluacion = $mentor->evaluarDiagnostico(
                    $input['diagnostico'], 
                    $input['contexto']
                );
                
                $feedback = $mentor->generarFeedback('diagnostico', $evaluacion);
                
                echo json_encode([
                    'success' => true,
                    'evaluacion' => $evaluacion,
                    'feedback' => $feedback
                ]);
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'error' => 'Acción no reconocida'
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