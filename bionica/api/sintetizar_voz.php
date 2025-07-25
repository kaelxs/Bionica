<?php
// api/sintetizar_voz.php - Versión simplificada
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Función de logging
function logError($message) {
    error_log("[TTS API] " . $message);
}

try {
    // Verificar método
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido. Use POST.');
    }
    
    // Leer input
    $input_raw = file_get_contents('php://input');
    if (!$input_raw) {
        throw new Exception('No se recibieron datos');
    }
    
    $input = json_decode($input_raw, true);
    if ($input === null) {
        throw new Exception('JSON inválido: ' . json_last_error_msg());
    }
    
    // Validar parámetros
    if (!isset($input['text']) || !isset($input['language'])) {
        throw new Exception('Parámetros requeridos: text y language');
    }
    
    $text = trim($input['text']);
    $language = trim($input['language']);
    
    if (empty($text)) {
        throw new Exception('El texto no puede estar vacío');
    }
    
    logError("Sintetizando: '{$text}' en idioma: {$language}");
    
    // Para esta versión simplificada, vamos a usar fallback directo
    // porque Google Cloud TTS requiere autenticación más compleja
    
    // Mapear idiomas a códigos de navegador
    $languageMapping = [
        'español' => 'es-ES',
        'aimara' => 'es-BO',
        'quechua' => 'es-PE'
    ];
    
    $languageCode = isset($languageMapping[$language]) ? $languageMapping[$language] : 'es-ES';
    
    // Por ahora, devolver configuración para que el navegador haga TTS
    echo json_encode([
        'success' => false, // Indica que debe usar fallback
        'error' => 'Usando TTS del navegador',
        'fallback' => true,
        'fallback_config' => [
            'language_code' => $languageCode,
            'use_browser_tts' => true,
            'text' => $text,
            'language' => $language
        ]
    ]);
    
} catch (Exception $e) {
    logError("Error: " . $e->getMessage());
    
    // Respuesta de error con fallback
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'fallback' => true,
        'fallback_config' => [
            'language_code' => 'es-ES',
            'use_browser_tts' => true,
            'text' => isset($text) ? $text : '',
            'language' => isset($language) ? $language : 'español'
        ]
    ]);
}
?>