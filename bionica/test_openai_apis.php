<?php
echo "<h1>🔍 Test de Configuración OpenAI</h1>";

// Verificar si el archivo existe
if (!file_exists('includes/openai_config.php')) {
    echo "<div style='color: red;'><strong>❌ Archivo includes/openai_config.php no encontrado</strong></div>";
    exit;
}

// Incluir el archivo
require_once 'includes/openai_config.php';

// Verificar si la clase existe
if (!class_exists('OpenAIConfig')) {
    echo "<div style='color: red;'><strong>❌ Clase OpenAIConfig no encontrada</strong></div>";
    exit;
}

// Verificar la clave API
$api_key = OpenAIConfig::getApiKey();
echo "<p><strong>Clave API obtenida:</strong> " . (strlen($api_key) > 10 ? substr($api_key, 0, 10) . '...' . substr($api_key, -5) : 'No válida') . "</p>";

if (OpenAIConfig::isValidApiKey()) {
    echo "<div style='color: green;'><strong>✅ Clave API válida</strong></div>";
} else {
    echo "<div style='color: red;'><strong>❌ Clave API no válida</strong></div>";
    echo "<p>La clave debe comenzar con 'sk-' y tener más de 50 caracteres</p>";
}

// Verificar cURL
if (function_exists('curl_version')) {
    echo "<div style='color: green;'><strong>✅ cURL está disponible</strong></div>";
    $curl_info = curl_version();
    echo "<p>Versión cURL: " . $curl_info['version'] . "</p>";
} else {
    echo "<div style='color: red;'><strong>❌ cURL no está disponible</strong></div>";
}

// Test de conexión simple
echo "<h2>📡 Test de Conexión a OpenAI</h2>";

if (OpenAIConfig::isValidApiKey() && function_exists('curl_version')) {
    try {
        // Test simple de conexión
        $test_headers = [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/models');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $test_headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            echo "<div style='color: orange;'><strong>⚠️ Error de conexión:</strong> " . $error . "</div>";
        } elseif ($http_code === 200) {
            echo "<div style='color: green;'><strong>✅ Conexión exitosa a OpenAI API</strong></div>";
        } else {
            echo "<div style='color: orange;'><strong>⚠️ Respuesta HTTP:</strong> " . $http_code . "</div>";
            echo "<p>Respuesta: " . substr($response, 0, 200) . "...</p>";
        }
    } catch (Exception $e) {
        echo "<div style='color: red;'><strong>❌ Error en test de conexión:</strong> " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div style='color: gray;'><strong>⏭️ No se puede realizar test de conexión - requisitos no cumplidos</strong></div>";
}
?>