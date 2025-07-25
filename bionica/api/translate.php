<?php
// api/translate.php - Versión universal que funciona en cualquier servidor
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers obligatorios SIEMPRE
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Log de debug
function debug_log($message) {
    $log_file = 'api_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

// Función para respuesta JSON garantizada
function json_response($data, $http_code = 200) {
    http_response_code($http_code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

debug_log("=== NUEVA PETICIÓN ===");
debug_log("Método: " . $_SERVER['REQUEST_METHOD']);
debug_log("Headers: " . print_r(getallheaders(), true));

// Manejar OPTIONS (preflight CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    debug_log("Respondiendo OPTIONS");
    json_response(['status' => 'ok']);
}

// Permitir tanto GET como POST para debugging
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    debug_log("Petición GET recibida");
    json_response([
        'success' => true,
        'message' => 'API de traducción funcionando',
        'method' => 'GET',
        'timestamp' => date('Y-m-d H:i:s'),
        'test' => 'Para probar, usa POST con JSON'
    ]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    debug_log("Método no soportado: " . $_SERVER['REQUEST_METHOD']);
    json_response([
        'success' => false,
        'error' => 'Método no soportado. Use POST o GET.'
    ], 405);
}

try {
    // Leer datos de entrada
    $input_raw = file_get_contents('php://input');
    debug_log("Input raw: " . $input_raw);
    
    if (empty($input_raw)) {
        // Si no hay input raw, intentar con $_POST
        if (!empty($_POST)) {
            $input = $_POST;
            debug_log("Usando datos POST: " . print_r($_POST, true));
        } else {
            throw new Exception('No se recibieron datos');
        }
    } else {
        $input = json_decode($input_raw, true);
        if ($input === null) {
            throw new Exception('JSON inválido: ' . json_last_error_msg());
        }
    }
    
    // Validar parámetros requeridos
    if (!isset($input['text']) || !isset($input['target_language'])) {
        throw new Exception('Parámetros requeridos: text y target_language');
    }
    
    $text = trim($input['text']);
    $target_language = trim($input['target_language']);
    $source_language = isset($input['source_language']) ? trim($input['source_language']) : 'es';
    
    debug_log("Traduciendo: '$text' de $source_language a $target_language");
    
    // Si el idioma objetivo es español, no traducir
    if ($target_language === 'es' || $target_language === 'español') {
        json_response([
            'success' => true,
            'translated_text' => $text,
            'original_text' => $text,
            'source_language' => $source_language,
            'target_language' => 'es',
            'target_language_name' => 'Español'
        ]);
    }
    
    // Mapear idiomas a códigos
    $language_codes = [
        'español' => 'es',
        'aimara' => 'ay',
        'quechua' => 'qu'
    ];
    
    $target_code = isset($language_codes[$target_language]) ? $language_codes[$target_language] : $target_language;
    
    // Configurar Google Translate
    $api_key = 'AIzaSyDHUs3_ymnIkVzP-Ioed24Gg3FSUOJorRw';
    $url = 'https://translation.googleapis.com/language/translate/v2?key=' . $api_key;
    
    // Preparar datos para Google Translate
    $post_data = http_build_query([
        'q' => $text,
        'target' => $target_code,
        'source' => $source_language,
        'format' => 'text'
    ]);
    
    debug_log("URL Google: $url");
    debug_log("Datos POST: $post_data");
    
    // Verificar si cURL está disponible
    if (!function_exists('curl_init')) {
        throw new Exception('cURL no está disponible en este servidor');
    }
    
    // Llamada a Google Translate
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; TranslateAPI/1.0)');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    debug_log("HTTP Code: $http_code");
    debug_log("cURL Error: $curl_error");
    debug_log("Respuesta Google: " . substr($response, 0, 200));
    
    if ($curl_error) {
        throw new Exception("Error cURL: $curl_error");
    }
    
    if ($http_code !== 200) {
        throw new Exception("Error HTTP $http_code de Google Translate");
    }
    
    $google_data = json_decode($response, true);
    if ($google_data === null) {
        throw new Exception('Respuesta inválida de Google Translate');
    }
    
    if (isset($google_data['error'])) {
        $error_message = $google_data['error']['message'] ?? 'Error desconocido';
        throw new Exception("Error de Google: $error_message");
    }
    
    if (!isset($google_data['data']['translations'][0]['translatedText'])) {
        throw new Exception('Formato de respuesta inesperado de Google');
    }
    
    $translated_text = $google_data['data']['translations'][0]['translatedText'];
    debug_log("Traducción exitosa: $translated_text");
    
    // Respuesta exitosa
    json_response([
        'success' => true,
        'translated_text' => $translated_text,
        'original_text' => $text,
        'source_language' => $source_language,
        'target_language' => $target_code,
        'target_language_name' => ucfirst($target_language),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    debug_log("ERROR: " . $e->getMessage());
    
    json_response([
        'success' => false,
        'error' => $e->getMessage(),
        'fallback_text' => isset($text) ? $text : '',
        'timestamp' => date('Y-m-d H:i:s')
    ], 500);
}
?>