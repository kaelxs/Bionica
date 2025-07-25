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

class AgentePacienteOpenAI {
    private $pdo;
    
    public function __construct($database) {
        $this->pdo = $database;
    }
    
    public function generarRespuesta($mensaje, $contexto, $idioma = 'español') {
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
            
            // Crear el prompt para el paciente virtual
            $prompt = $this->crearPromptPaciente($mensaje, $caso, $contexto);
            
            $messages = [
                [
                    'role' => 'system',
                    'content' => 'Eres un paciente virtual en una simulación médica. Debes responder como un paciente real con los síntomas y características proporcionadas. Sé coherente, realista y proporciona información médica relevante cuando se te pregunte. No des diagnósticos, solo responde como paciente. Responde en español.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ];
            
            // Obtener respuesta de OpenAI en español
            $respuestaOriginal = OpenAIConfig::callAPI($messages, 0.8);
            
            // Traducir si el idioma no es español
            $respuestaFinal = $respuestaOriginal;
            if ($idioma !== 'español' && $idioma !== 'es') {
                $respuestaFinal = $this->traducirTexto($respuestaOriginal, $idioma);
            }
            
            return [
                'respuesta_original' => $respuestaOriginal,
                'respuesta_traducida' => $respuestaFinal,
                'idioma_seleccionado' => $idioma
            ];
            
        } catch (Exception $e) {
            error_log("Error en Agente Paciente OpenAI: " . $e->getMessage());
            $errorMsg = "Lo siento, no me siento bien en este momento. ¿Podría hacerme otra pregunta?";
            
            // Traducir mensaje de error si es necesario
            if ($idioma !== 'español' && $idioma !== 'es') {
                $errorMsg = $this->traducirTexto($errorMsg, $idioma);
            }
            
            return [
                'respuesta_original' => "Lo siento, no me siento bien en este momento. ¿Podría hacerme otra pregunta?",
                'respuesta_traducida' => $errorMsg,
                'idioma_seleccionado' => $idioma
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
    
    private function crearPromptPaciente($pregunta, $caso, $contexto) {
        $prompt = "Datos del paciente:\n";
        $prompt .= "- Edad: " . ($caso['edad_paciente'] ?? $contexto['edad_paciente'] ?? 'No especificada') . " años\n";
        $prompt .= "- Género: " . ($caso['genero_paciente'] ?? $contexto['genero_paciente'] ?? 'No especificado') . "\n";
        $prompt .= "- Síntomas principales: " . ($caso['sintomas_principales'] ?? 'No especificados') . "\n";
        $prompt .= "- Historia clínica: " . ($caso['historia_clinica'] ?? 'No hay antecedentes') . "\n";
        
        if (isset($caso['respuestas'])) {
            $prompt .= "- Nivel de ansiedad: " . ($caso['respuestas']['ansiedad'] ?? 'Moderada') . "\n";
            $prompt .= "- Intensidad del dolor: " . ($caso['respuestas']['dolor'] ?? 'Moderado') . "\n";
        }
        
        $prompt .= "\nPregunta del médico: " . $pregunta . "\n";
        $prompt .= "\nResponde como el paciente, siendo realista y proporcionando información relevante. No des diagnósticos, solo responde como paciente. Responde en español.";
        
        return $prompt;
    }
}

// Manejo de solicitudes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['accion'])) {
        try {
            // Verificar conexión a base de datos
            $db = getDB();
            $agente = new AgentePacienteOpenAI($db);
            
            switch ($input['accion']) {
                case 'responder':
                    $idioma = isset($input['idioma']) ? $input['idioma'] : 'español';
                    
                    $resultado = $agente->generarRespuesta(
                        $input['mensaje'], 
                        $input['contexto'],
                        $idioma
                    );
                    
                    echo json_encode([
                        'success' => true,
                        'respuesta' => $resultado['respuesta_traducida'],
                        'respuesta_original' => $resultado['respuesta_original'],
                        'idioma' => $resultado['idioma_seleccionado'],
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