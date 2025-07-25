<?php
// includes/google_translate_config.php

class GoogleTranslateConfig {
    private static $api_key = '';
    private static $base_url = 'https://translation.googleapis.com/language/translate/v2';
    
    public static function getApiKey() {
        return self::$api_key;
    }
    
    public static function translateText($text, $targetLanguage, $sourceLanguage = 'es') {
        try {
            $url = self::$base_url . '?key=' . self::$api_key;
            
            $data = [
                'q' => $text,
                'target' => $targetLanguage,
                'source' => $sourceLanguage,
                'format' => 'text'
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_error($ch)) {
                throw new Exception('Error cURL: ' . curl_error($ch));
            }
            
            curl_close($ch);
            
            if ($httpCode !== 200) {
                throw new Exception('Error HTTP: ' . $httpCode);
            }
            
            $responseData = json_decode($response, true);
            
            if (isset($responseData['data']['translations'][0]['translatedText'])) {
                return $responseData['data']['translations'][0]['translatedText'];
            } else {
                throw new Exception('Respuesta inválida de la API');
            }
            
        } catch (Exception $e) {
            error_log('Error en Google Translate: ' . $e->getMessage());
            return $text; // Retornar texto original si falla la traducción
        }
    }
    
    public static function getLanguageCode($language) {
        $codes = [
            'español' => 'es',
            'aimara' => 'ay',
            'quechua' => 'qu'
        ];
        
        return isset($codes[$language]) ? $codes[$language] : 'es';
    }
    
    public static function getLanguageName($code) {
        $names = [
            'es' => 'Español',
            'ay' => 'Aimara', 
            'qu' => 'Quechua'
        ];
        
        return isset($names[$code]) ? $names[$code] : 'Español';
    }
}
?>