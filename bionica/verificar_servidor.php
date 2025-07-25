<?php
// verificar_servidor.php - Crear este archivo en la raíz del proyecto
echo "<h1>🔍 Verificación del Servidor</h1>";

// 1. Verificar PHP
echo "<h2>📋 Información PHP</h2>";
echo "<p><strong>Versión PHP:</strong> " . phpversion() . "</p>";
echo "<p><strong>Método de petición:</strong> " . $_SERVER['REQUEST_METHOD'] . "</p>";
echo "<p><strong>Servidor:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido') . "</p>";

// 2. Verificar estructura de archivos
echo "<h2>📁 Estructura de Archivos</h2>";
$archivos_buscar = [
    'api/translate.php',
    'api/sintetizar_voz.php',
    'includes/google_translate_config.php'
];

foreach ($archivos_buscar as $archivo) {
    if (file_exists($archivo)) {
        $size = filesize($archivo);
        echo "<div style='color: green;'>✅ {$archivo} - {$size} bytes</div>";
    } else {
        echo "<div style='color: red;'>❌ {$archivo} - NO EXISTE</div>";
    }
}

// 3. Test directo de la API de traducción
echo "<h2>🧪 Test Directo de API</h2>";

if (file_exists('api/translate.php')) {
    echo "<p>Intentando incluir api/translate.php...</p>";
    
    // Simular POST data
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $test_data = json_encode([
        'text' => 'Hola doctor',
        'target_language' => 'aimara',
        'source_language' => 'es'
    ]);
    
    // Simular input
    $old_input = file_get_contents('php://input');
    
    ob_start();
    
    // Capturar cualquier output de la API
    try {
        // Preparar el input simulado
        $_POST = []; // Limpiar POST
        
        echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px 0;'>";
        echo "<strong>Ejecutando API...</strong><br>";
        
        // Incluir la API
        include 'api/translate.php';
        
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div style='color: red;'>❌ Error ejecutando API: " . $e->getMessage() . "</div>";
    }
    
    $api_output = ob_get_clean();
    echo "<h3>Salida de la API:</h3>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border: 1px solid #ddd;'>";
    echo htmlspecialchars($api_output);
    echo "</pre>";
    
} else {
    echo "<div style='color: red;'>❌ No se puede probar - api/translate.php no existe</div>";
}

// 4. Verificar si el servidor soporta métodos HTTP
echo "<h2>🌐 Información de Petición</h2>";
echo "<p><strong>URL actual:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>Host:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p><strong>Puerto:</strong> " . ($_SERVER['SERVER_PORT'] ?? 'Desconocido') . "</p>";

// 5. Test de cURL
echo "<h2>🔗 Test de cURL</h2>";
if (function_exists('curl_version')) {
    echo "<div style='color: green;'>✅ cURL disponible</div>";
    $curl_version = curl_version();
    echo "<p>Versión: " . $curl_version['version'] . "</p>";
} else {
    echo "<div style='color: red;'>❌ cURL NO disponible</div>";
}

// 6. Crear API simple de prueba
echo "<h2>🔧 Crear API de Prueba</h2>";

$api_test_content = '<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

echo json_encode([
    "success" => true,
    "message" => "API de prueba funcionando",
    "method" => $_SERVER["REQUEST_METHOD"],
    "timestamp" => date("Y-m-d H:i:s")
]);
?>';

if (!file_exists('api')) {
    if (mkdir('api', 0755, true)) {
        echo "<div style='color: green;'>✅ Directorio api/ creado</div>";
    } else {
        echo "<div style='color: red;'>❌ No se pudo crear directorio api/</div>";
    }
}

if (file_put_contents('api/test.php', $api_test_content)) {
    echo "<div style='color: green;'>✅ API de prueba creada: api/test.php</div>";
    echo "<p><a href='api/test.php' target='_blank'>🔗 Probar API de prueba (GET)</a></p>";
} else {
    echo "<div style='color: red;'>❌ No se pudo crear API de prueba</div>";
}

// 7. JavaScript para test POST
echo "<h2>📡 Test JavaScript POST</h2>";
echo "<button onclick='testPostAPI()'>🧪 Probar POST a API de prueba</button>";
echo "<div id='post-result' style='margin-top: 10px;'></div>";

echo "<script>
async function testPostAPI() {
    const resultDiv = document.getElementById('post-result');
    resultDiv.innerHTML = '🔄 Probando...';
    
    try {
        const response = await fetch('api/test.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({test: 'data'})
        });
        
        const data = await response.json();
        resultDiv.innerHTML = '<div style=\"background: #d4edda; padding: 10px; border-radius: 5px;\">✅ POST funcionando: ' + JSON.stringify(data) + '</div>';
        
    } catch (error) {
        resultDiv.innerHTML = '<div style=\"background: #f8d7da; padding: 10px; border-radius: 5px;\">❌ Error: ' + error.message + '</div>';
    }
}
</script>";

echo "<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
h1, h2 { color: #333; }
div { margin: 5px 0; }
button { background: #007bff; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; }
button:hover { background: #0056b3; }
</style>";
?>