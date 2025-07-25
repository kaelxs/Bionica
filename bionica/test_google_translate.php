<?php
// test_google_translate.php
echo "<h1>🌐 Test de Google Translate API</h1>";

// Verificar si el archivo de configuración existe
if (file_exists('includes/google_translate_config.php')) {
    echo "<div style='color: green;'><strong>✅ Archivo includes/google_translate_config.php encontrado</strong></div>";
    
    require_once 'includes/google_translate_config.php';
    
    if (class_exists('GoogleTranslateConfig')) {
        echo "<div style='color: green;'><strong>✅ Clase GoogleTranslateConfig cargada correctamente</strong></div>";
        
        $api_key = GoogleTranslateConfig::getApiKey();
        echo "<p><strong>Clave API:</strong> " . (strlen($api_key) > 10 ? substr($api_key, 0, 10) . '...' . substr($api_key, -5) : 'No válida') . "</p>";
        
        // Test de traducción a Aimara
        echo "<h2>🔄 Test de Traducción a Aimara</h2>";
        $texto_prueba = "Hola doctor, me siento mal y necesito ayuda médica.";
        echo "<p><strong>Texto original:</strong> " . $texto_prueba . "</p>";
        
        try {
            $traduccion_aimara = GoogleTranslateConfig::translateText($texto_prueba, 'ay', 'es');
            echo "<div style='color: green;'><strong>✅ Traducción a Aimara exitosa:</strong> " . $traduccion_aimara . "</div>";
        } catch (Exception $e) {
            echo "<div style='color: red;'><strong>❌ Error en traducción a Aimara:</strong> " . $e->getMessage() . "</div>";
        }
        
        // Test de traducción a Quechua
        echo "<h2>🔄 Test de Traducción a Quechua</h2>";
        try {
            $traduccion_quechua = GoogleTranslateConfig::translateText($texto_prueba, 'qu', 'es');
            echo "<div style='color: green;'><strong>✅ Traducción a Quechua exitosa:</strong> " . $traduccion_quechua . "</div>";
        } catch (Exception $e) {
            echo "<div style='color: red;'><strong>❌ Error en traducción a Quechua:</strong> " . $e->getMessage() . "</div>";
        }
        
        // Test de códigos de idioma
        echo "<h2>📋 Test de Códigos de Idioma</h2>";
        $idiomas = ['español', 'aimara', 'quechua'];
        foreach ($idiomas as $idioma) {
            $codigo = GoogleTranslateConfig::getLanguageCode($idioma);
            $nombre = GoogleTranslateConfig::getLanguageName($codigo);
            echo "<p><strong>{$idioma}:</strong> código '{$codigo}' → nombre '{$nombre}'</p>";
        }
        
    } else {
        echo "<div style='color: red;'><strong>❌ Clase GoogleTranslateConfig NO encontrada</strong></div>";
    }
} else {
    echo "<div style='color: red;'><strong>❌ Archivo includes/google_translate_config.php NO encontrado</strong></div>";
}

// Test de la API de traducción
echo "<h2>🌐 Test de API de Traducción</h2>";
if (file_exists('api/translate.php')) {
    echo "<div style='color: green;'><strong>✅ Archivo api/translate.php encontrado</strong></div>";
    
    // Test de solicitud HTTP
    $test_data = json_encode([
        'text' => 'Buenos días doctor, tengo dolor de cabeza',
        'target_language' => 'aimara',
        'source_language' => 'es'
    ]);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost/find/api/translate.php");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $test_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "<div style='color: orange;'><strong>⚠️ Error de conexión:</strong> {$error}</div>";
    } elseif ($http_code === 200) {
        echo "<div style='color: green;'><strong>✅ Conexión HTTP exitosa (200)</strong></div>";
        $responseData = json_decode($response, true);
        if ($responseData && isset($responseData['success'])) {
            if ($responseData['success']) {
                echo "<p><strong>Traducción exitosa:</strong> " . $responseData['translated_text'] . "</p>";
            } else {
                echo "<div style='color: red;'><strong>❌ Error en API:</strong> " . ($responseData['error'] ?? 'Error desconocido') . "</div>";
            }
        } else {
            echo "<div style='color: orange;'><strong>⚠️ Respuesta inesperada:</strong> " . substr($response, 0, 200) . "...</div>";
        }
    } else {
        echo "<div style='color: orange;'><strong>⚠️ Código HTTP:</strong> {$http_code}</div>";
        echo "<p>Respuesta: " . substr($response, 0, 200) . "...</p>";
    }
} else {
    echo "<div style='color: red;'><strong>❌ Archivo api/translate.php NO encontrado</strong></div>";
}

echo "<h2>📋 Resumen y Siguientes Pasos</h2>";
echo "<p>Si todos los tests muestran resultados positivos, la funcionalidad de traducción debería funcionar correctamente.</p>";
echo "<p><strong>Para implementar completamente:</strong></p>";
echo "<ol>";
echo "<li>Coloca el archivo <code>includes/google_translate_config.php</code> en tu servidor</li>";
echo "<li>Coloca el archivo <code>api/translate.php</code> en tu directorio api/</li>";
echo "<li>Reemplaza los archivos de agentes (agente_paciente_openai.php y agente_mentor_openai.php) con las versiones modificadas</li>";
echo "<li>Reemplaza el archivo <code>iniciar_simulacion.php</code> con la versión modificada</li>";
echo "<li>Verifica que tu clave API de Google Translate tenga los permisos necesarios</li>";
echo "</ol>";

echo "<h2>🎯 Funcionalidades Implementadas</h2>";
echo "<ul>";
echo "<li>✅ Selector de idioma (Español, Aimara, Quechua)</li>";
echo "<li>✅ Traducción automática de respuestas del paciente</li>";
echo "<li>✅ Traducción automática de feedback del mentor</li>";
echo "<li>✅ Síntesis de voz con configuración de idioma</li>";
echo "<li>✅ Almacenamiento en BD del texto original (en español)</li>";
echo "<li>✅ Interfaz de usuario para controlar voz y idioma</li>";
echo "</ul>";
?>