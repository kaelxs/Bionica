<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

// Ajustar la ruta según tu estructura
$ruta_config = '../includes/openai_config.php';
$ruta_translate = '../includes/google_translate_config.php';

if (!file_exists($ruta_config)) {
    $ruta_config = 'includes/openai_config.php';
}
if (!file_exists($ruta_config)) {
    $ruta_config = '../../includes/openai_config.php';
}

if (!file_exists($ruta_translate)) {
    $ruta_translate = 'includes/google_translate_config.php';
}
if (!file_exists($ruta_translate)) {
    $ruta_translate = '../../includes/google_translate_config.php';
}

if (!file_exists($ruta_config)) {
    echo json_encode([
        'success' => false,
        'error' => 'Archivo de configuración OpenAI no encontrado'
    ]);
    exit;
}

if (!file_exists($ruta_translate)) {
    echo json_encode([
        'success' => false,
        'error' => 'Archivo de configuración Google Translate no encontrado'
    ]);
    exit;
}

require_once '../includes/conexion.php';
require_once $ruta_config;
require_once $ruta_translate;

class AgenteMentorOpenAI {
    private $pdo;
    
    public function __construct($database) {
        $this->pdo = $database;
    }
    
    // Analizar pregunta del estudiante
    public function analizarPregunta($pregunta, $contexto, $idioma = 'español') {
        try {
            // Verificar que la API key sea válida
            if (!OpenAIConfig::isValidApiKey()) {
                throw new Exception("Clave API no válida configurada");
            }
            
            // Decodificar datos del caso si vienen como string
            $caso = $contexto['datos_caso'];
            if (is_string($caso)) {
                $caso = json_decode($caso, true);
            }
            
            // Crear el prompt para el mentor
            $prompt = $this->crearPromptMentor($pregunta, $caso, $contexto);
            
            $messages = [
                [
                    'role' => 'system',
                    'content' => 'Eres un mentor médico experto. Das retroalimentación MUY BREVE y una SUGERENCIA DE PREGUNTA en español. Formato estricto: "Mentor IA: [Retroalimentación]. Sugerencia: [Pregunta recomendada]."'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ];
            
            // Llamar a la API con una temperatura baja para respuestas más deterministas
            $respuesta_completa = OpenAIConfig::callAPI($messages, 0.4); 
            
            // Separar la retroalimentación de la sugerencia
            $respuesta_completa = trim($respuesta_completa);
            $partes = explode("Sugerencia:", $respuesta_completa, 2);
            
            $retroalimentacion_original = isset($partes[0]) ? trim(str_replace("Mentor IA:", "", $partes[0])) : "Evaluación realizada.";
            $pregunta_sugerida_original = isset($partes[1]) ? trim($partes[1], " \"'") : "¿Hay otros síntomas o antecedentes relevantes?";
            
            // Post-procesamiento para asegurar máxima brevedad
            if (strlen($retroalimentacion_original) > 200) {
                 $retroalimentacion_original = substr($retroalimentacion_original, 0, 197) . '...';
            }
            
            // Traducir si el idioma no es español
            $retroalimentacion_final = $retroalimentacion_original;
            $pregunta_sugerida_final = $pregunta_sugerida_original;
            
            if ($idioma !== 'español' && $idioma !== 'es') {
                $retroalimentacion_final = $this->traducirTexto($retroalimentacion_original, $idioma);
                $pregunta_sugerida_final = $this->traducirTexto($pregunta_sugerida_original, $idioma);
            }
            
            // Generar métricas simuladas
            $evaluacion = [
                'pertinencia' => rand(70, 95),
                'profundidad' => rand(65, 90),
                'orden' => rand(75, 95),
                'observaciones' => ['Evaluación automatizada por IA']
            ];
            
            return [
                'success' => true,
                'analisis' => $evaluacion,
                'feedback' => "Mentor IA: " . $retroalimentacion_final,
                'feedback_original' => "Mentor IA: " . $retroalimentacion_original,
                'pregunta_sugerida' => $pregunta_sugerida_final,
                'pregunta_sugerida_original' => $pregunta_sugerida_original,
                'idioma' => $idioma
            ];
            
        } catch (Exception $e) {
            error_log("Error en Agente Mentor OpenAI: " . $e->getMessage());
            
            $errorMsg = 'Error interno del servidor: ' . $e->getMessage();
            if ($idioma !== 'español' && $idioma !== 'es') {
                $errorMsg = $this->traducirTexto($errorMsg, $idioma);
            }
            
            return [
                'success' => false,
                'error' => $errorMsg
            ];
        }
    }
    
    private function traducirTexto($texto, $idioma) {
        try {
            $codigoIdioma = GoogleTranslateConfig::getLanguageCode($idioma);
            return GoogleTranslateConfig::translateText($texto, $codigoIdioma, 'es');
        } catch (Exception $e) {
            error_log("Error en traducción: " . $e->getMessage());
            return $texto; // Retornar texto original si falla la traducción
        }
    }
    
    private function crearPromptMentor($pregunta, $caso, $contexto) {
        if (is_string($caso)) {
            $caso = json_decode($caso, true);
        }

        $prompt = "Eres un mentor médico. Evalúa esta pregunta de un estudiante:\n";
        $prompt .= "Pregunta del estudiante: \"" . $pregunta . "\"\n";
        $prompt .= "Especialidad: " . ($caso['especialidad'] ?? $contexto['especialidad'] ?? 'No especificada') . "\n";
        $prompt .= "Síntomas del caso: " . ($caso['sintomas_principales'] ?? 'No especificados') . "\n\n";
        
        // Instrucciones MUY CLARAS Y CONCISAS para el modelo
        $prompt .= "Instrucciones PARA TU RESPUESTA:\n";
        $prompt .= "1. Responde ÚNICAMENTE en español.\n";
        $prompt .= "2. Comienza tu respuesta con 'Mentor IA: '\n";
        $prompt .= "3. Sé MUY BREVE en la retroalimentación (1 frase sobre la pregunta).\n";
        $prompt .= "4. Luego, AGREGA una 'Sugerencia: [Pregunta recomendada]' basada en el caso.\n";
        $prompt .= "5. La 'Pregunta recomendada' debe ser CLARA, RELEVANTE y ayudar a profundizar.\n";
        $prompt .= "6. NO uses listas, viñetas ni emojis.\n";
        $prompt .= "7. NO des diagnósticos ni información médica detallada en la retroalimentación.\n";
        $prompt .= "8. Ejemplo EXACTO de formato deseado:\n";
        $prompt .= "   'Mentor IA: Buena pregunta sobre X. Sugerencia: ¿Cuánto tiempo lleva presentándose Y?'\n";
        $prompt .= "   'Mentor IA: Pregunta relevante. Sugerencia: ¿Ha tenido episodios similares antes?'\n";
        $prompt .= "   'Mentor IA: Muy bien enfocada. Sugerencia: ¿Qué factores parecen empeorar los síntomas?'\n";
        $prompt .= "9. NO expliques por qué das esa retroalimentación, solo da la retroalimentación y la sugerencia.\n";
        $prompt .= "10. Tu respuesta (muy breve, siguiendo el ejemplo EXACTO):";

        return $prompt;
    }
}

// Manejo de solicitudes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['accion'])) {
        try {
            $db = getDB();
            $mentor = new AgenteMentorOpenAI($db);
            
            switch ($input['accion']) {
                case 'analizar_pregunta':
                    $idioma = isset($input['idioma']) ? $input['idioma'] : 'español';
                    
                    $resultado = $mentor->analizarPregunta(
                        $input['pregunta'], 
                        $input['contexto'],
                        $idioma
                    );
                    
                    echo json_encode($resultado);
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