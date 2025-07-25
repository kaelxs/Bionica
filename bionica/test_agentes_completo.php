<?php
echo "<h1>🧪 Test Completo de Agentes con OpenAI</h1>";

// Verificar estructura de directorios
echo "<h2>📁 Estructura de Directorios</h2>";
echo "<p><strong>Directorio actual:</strong> " . getcwd() . "</p>";

$directorios = ['api', 'includes'];
foreach ($directorios as $dir) {
    if (is_dir($dir)) {
        echo "<div style='color: green;'><strong>✅ Directorio {$dir} encontrado</strong></div>";
        $archivos = array_filter(scandir($dir), function($file) {
            return strpos($file, '.php') !== false;
        });
        echo "<p>Archivos en {$dir}/: " . implode(', ', $archivos) . "</p>";
    } else {
        echo "<div style='color: red;'><strong>❌ Directorio {$dir} NO encontrado</strong></div>";
    }
}

// Test de includes/openai_config.php
echo "<h2>⚙️ Test de Configuración OpenAI</h2>";
if (file_exists('includes/openai_config.php')) {
    echo "<div style='color: green;'><strong>✅ Archivo includes/openai_config.php encontrado</strong></div>";
    
    require_once 'includes/openai_config.php';
    
    if (class_exists('OpenAIConfig')) {
        echo "<div style='color: green;'><strong>✅ Clase OpenAIConfig cargada correctamente</strong></div>";
        
        $api_key = OpenAIConfig::getApiKey();
        echo "<p><strong>Clave API:</strong> " . (strlen($api_key) > 10 ? substr($api_key, 0, 10) . '...' . substr($api_key, -5) : 'No válida') . "</p>";
        
        if (OpenAIConfig::isValidApiKey()) {
            echo "<div style='color: green;'><strong>✅ Clave API válida</strong></div>";
        } else {
            echo "<div style='color: red;'><strong>❌ Clave API NO válida</strong></div>";
        }
    } else {
        echo "<div style='color: red;'><strong>❌ Clase OpenAIConfig NO encontrada</strong></div>";
    }
} else {
    echo "<div style='color: red;'><strong>❌ Archivo includes/openai_config.php NO encontrado</strong></div>";
}

// Verificar cURL
echo "<h2>🌐 Test de cURL</h2>";
if (function_exists('curl_version')) {
    echo "<div style='color: green;'><strong>✅ cURL está disponible</strong></div>";
} else {
    echo "<div style='color: red;'><strong>❌ cURL NO está disponible</strong></div>";
}

// Test de conexión a las APIs
echo "<h2>🔌 Test de Conexión a APIs</h2>";

$apis = [
    'agente_paciente_openai.php' => 'Agente Paciente',
    'agente_mentor_openai.php' => 'Agente Mentor'
];

foreach ($apis as $archivo => $nombre) {
    echo "<h3>🤖 {$nombre} ({$archivo})</h3>";
    
    if (file_exists("api/{$archivo}")) {
        echo "<div style='color: green;'><strong>✅ Archivo encontrado</strong></div>";
        
        // Test de solicitud básica
        $test_data = json_encode(['test' => 'conexion']);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/find/api/{$archivo}");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $test_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            echo "<div style='color: orange;'><strong>⚠️ Error de conexión:</strong> {$error}</div>";
        } elseif ($http_code === 200) {
            echo "<div style='color: green;'><strong>✅ Conexión HTTP exitosa (200)</strong></div>";
            echo "<p>Respuesta: " . substr($response, 0, 100) . "...</p>";
        } else {
            echo "<div style='color: orange;'><strong>⚠️ Código HTTP:</strong> {$http_code}</div>";
            echo "<p>Respuesta: " . substr($response, 0, 100) . "...</p>";
        }
    } else {
        echo "<div style='color: red;'><strong>❌ Archivo NO encontrado</strong></div>";
    }
}

echo "<h2>📋 Resumen</h2>";
echo "<p>Si todos los tests muestran resultados positivos, tus agentes deberían funcionar correctamente.</p>";
echo "<p>Si hay errores, verifica:</p>";
echo "<ol>";
echo "<li>Que la clave API esté correctamente configurada en includes/openai_config.php</li>";
echo "<li>Que los archivos estén en las rutas correctas</li>";
echo "<li>Que cURL esté habilitado en tu servidor</li>";
echo "<li>Que tengas conexión a internet</li>";
echo "</ol>";
?>