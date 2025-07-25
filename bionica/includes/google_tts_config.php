<?php
// includes/google_tts_config.php

class GoogleTTSConfig {
    private static $api_key = ''; // Tu misma API key
    private static $base_url = 'https://texttospeech.googleapis.com/v1/text:synthesize';
    
    public static function getApiKey() {
        return self::$api_key;
    }
    
    public static function synthesizeText($text, $languageCode, $voiceName = null) {
        try {
            $url = self::$base_url . '?key=' . self::$api_key;
            
            // Configuración de voz según idioma
            $voiceConfig = self::getVoiceConfig($languageCode, $voiceName);
            
            $data = [
                'input' => [
                    'text' => $text
                ],
                'voice' => $voiceConfig,
                'audioConfig' => [
                    'audioEncoding' => 'MP3',
                    'speakingRate' => 1.0,
                    'pitch' => 0.0,
                    'volumeGainDb' => 0.0
                ]
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_error($ch)) {
                throw new Exception('Error cURL: ' . curl_error($ch));
            }
            
            curl_close($ch);
            
            if ($httpCode !== 200) {
                throw new Exception('Error HTTP: ' . $httpCode . ' - ' . $response);
            }
            
            $responseData = json_decode($response, true);
            
            if (isset($responseData['audioContent'])) {
                return $responseData['audioContent']; // Base64 encoded audio
            } else {
                throw new Exception('Respuesta inválida de la API TTS');
            }
            
        } catch (Exception $e) {
            error_log('Error en Google TTS: ' . $e->getMessage());
            return null;
        }
    }
    
    private static function getVoiceConfig($languageCode, $voiceName = null) {
        $voiceConfigs = [
            'es' => [
                'languageCode' => 'es-ES',
                'name' => 'es-ES-Standard-A',
                'ssmlGender' => 'FEMALE'
            ],
            'es-PE' => [
                'languageCode' => 'es-US', // Usa es-US con acento latino
                'name' => 'es-US-Standard-A',
                'ssmlGender' => 'FEMALE'
            ],
            'es-BO' => [
                'languageCode' => 'es-US',
                'name' => 'es-US-Standard-B',
                'ssmlGender' => 'MALE'
            ],
            'qu' => [
                'languageCode' => 'es-US', // Fallback mejorado para Quechua
                'name' => 'es-US-Standard-C',
                'ssmlGender' => 'FEMALE'
            ],
            'ay' => [
                'languageCode' => 'es-US', // Fallback mejorado para Aimara  
                'name' => 'es-US-Standard-B',
                'ssmlGender' => 'MALE'
            ]
        ];
        
        return $voiceConfigs[$languageCode] ?? $voiceConfigs['es'];
    }
    
    public static function createAudioFile($base64Audio, $filename) {
        try {
            $audioData = base64_decode($base64Audio);
            $filepath = 'temp_audio/' . $filename . '.mp3';
            
            // Crear directorio si no existe
            if (!file_exists('temp_audio')) {
                mkdir('temp_audio', 0755, true);
            }
            
            file_put_contents($filepath, $audioData);
            return $filepath;
            
        } catch (Exception $e) {
            error_log('Error creando archivo de audio: ' . $e->getMessage());
            return null;
        }
    }
}
?>