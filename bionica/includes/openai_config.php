<?php
// Configuración de OpenAI API
class OpenAIConfig {
    private static $api_key = '';
    private static $api_url = 'https://api.openai.com/v1/chat/completions';
    private static $model = 'gpt-3.5-turbo';
    
    public static function getApiKey() {
        return self::$api_key;
    }
    
    public static function getApiUrl() {
        return self::$api_url;
    }
    
    public static function getModel() {
        return self::$model;
    }
    
    // Función para hacer llamadas a la API de OpenAI
    public static function callAPI($messages, $temperature = 0.7) {
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . self::$api_key
        ];
        
        $data = [
            'model' => self::$model,
            'messages' => $messages,
            'temperature' => $temperature,
            'max_tokens' => 500
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Solo para entornos de desarrollo
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Solo para entornos de desarrollo
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception("Error en la llamada a OpenAI: " . $error);
        }
        
        if ($http_code !== 200) {
            $error_data = json_decode($response, true);
            throw new Exception("Error HTTP " . $http_code . ": " . ($error_data['error']['message'] ?? 'Error desconocido'));
        }
        
        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'] ?? '';
    }
    
    // Función para verificar si la clave API es válida
    public static function isValidApiKey() {
        return !empty(self::$api_key) && strlen(self::$api_key) > 50;
    }
}
?>