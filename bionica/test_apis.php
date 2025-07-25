<?php
echo "<h1>🧪 Test de APIs de Agentes</h1>";

$apis = [
    'api/agente_paciente_openai.php',
    'api/agente_mentor_openai.php',
    'api/guardar_interaccion.php'
];

foreach ($apis as $api) {
    echo "<h2>🔍 Test: {$api}</h2>";
    
    if (file_exists($api)) {
        echo "<div style='color: green;'><strong>✅ Archivo encontrado</strong></div>";
        
        // Test básico de conexión
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/find/{$api}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['test' => 'conexion']));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            echo "<div style='color: red;'><strong>❌ Error:</strong> {$error}</div>";
        } elseif ($http_code === 200) {
            echo "<div style='color: green;'><strong>✅ Conexión exitosa</strong></div>";
            echo "<p>Respuesta: " . substr($response, 0, 200) . "...</p>";
        } else {
            echo "<div style='color: orange;'><strong>⚠️ HTTP {$http_code}</strong></div>";
            echo "<p>Respuesta: " . substr($response, 0, 200) . "...</p>";
        }
    } else {
        echo "<div style='color: red;'><strong>❌ Archivo NO encontrado</strong></div>";
    }
}
?>