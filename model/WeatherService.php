<?php
/**
 * WeatherService — Integración con OpenWeatherMap API
 * Obtiene el tiempo actual para una dirección/ciudad dada.
 * Cachea el resultado en sesión 30 minutos para no gastar llamadas API.
 */
class WeatherService {

    // ⚠️ SUSTITUYE ESTO POR TU API KEY DE openweathermap.org (es gratuita)
    private static $apiKey = 'e7dfcf62b38e9a27a91ae61471500fdc';
    private static $baseUrl = 'https://api.openweathermap.org/data/2.5/weather';
    private static $cacheTtl = 1800; // 30 minutos en segundos

    /**
     * Obtiene el tiempo para una dirección.
     * Extrae la ciudad de la dirección (primer token hasta la coma o número).
     * Devuelve array con datos del tiempo o null si falla.
     */
    public static function getByAddress(string $address): ?array {
        if (empty($address) || empty(self::$apiKey) || self::$apiKey === 'TU_API_KEY_AQUI') {
            return null;
        }

        // Extraer ciudad de la dirección: "Calle Ejemplo 12, Madrid" → "Madrid"
        // Intentamos coger lo que hay después de la última coma, si no la dirección completa
        $city = self::extractCity($address);
        $cacheKey = 'weather_' . md5($city);

        // Comprobar caché en sesión
        if (isset($_SESSION[$cacheKey], $_SESSION[$cacheKey . '_ts'])) {
            if ((time() - $_SESSION[$cacheKey . '_ts']) < self::$cacheTtl) {
                return $_SESSION[$cacheKey];
            }
        }

        // Llamada a la API
        $url = self::$baseUrl . '?' . http_build_query([
            'q'     => $city,
            'appid' => self::$apiKey,
            'units' => 'metric',
            'lang'  => 'es',
        ]);

        // Usamos cURL en lugar de file_get_contents para mejor compatibilidad SSL en XAMPP
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_SSL_VERIFYPEER => false, // XAMPP no siempre tiene los certificados raíz
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT      => 'HelloNeighbor/1.0',
        ]);
        $raw      = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$raw || $httpCode !== 200) return null;

        $data = json_decode($raw, true);
        if (!$data || ($data['cod'] ?? 0) != 200) return null;

        $result = [
            'ciudad'      => $data['name'],
            'temp'        => round($data['main']['temp']),
            'temp_min'    => round($data['main']['temp_min']),
            'temp_max'    => round($data['main']['temp_max']),
            'descripcion' => ucfirst($data['weather'][0]['description']),
            'icono_code'  => $data['weather'][0]['icon'],
            'icono_url'   => 'https://openweathermap.org/img/wn/' . $data['weather'][0]['icon'] . '@2x.png',
            'humedad'     => $data['main']['humidity'],
            'viento'      => round($data['wind']['speed'] * 3.6), // m/s → km/h
            'emoji'       => self::iconToEmoji($data['weather'][0]['icon']),
        ];

        // Guardar en caché
        $_SESSION[$cacheKey] = $result;
        $_SESSION[$cacheKey . '_ts'] = time();

        return $result;
    }

    /**
     * Extrae la ciudad de una cadena de dirección.
     * "Calle Mayor 5, Barcelona" → "Barcelona"
     * "Madrid" → "Madrid"
     */
    private static function extractCity(string $address): string {
        $parts = explode(',', $address);
        if (count($parts) > 1) {
            return trim(end($parts));
        }
        // Si no hay coma, intentar quitar número del inicio
        return trim(preg_replace('/^\w+\s+\d+\s*/', '', $address) ?: $address);
    }

    /**
     * Convierte el código de icono de OWM a un emoji representativo.
     */
    private static function iconToEmoji(string $icon): string {
        $map = [
            '01d' => '☀️',  '01n' => '🌙',
            '02d' => '⛅',  '02n' => '☁️',
            '03d' => '☁️',  '03n' => '☁️',
            '04d' => '☁️',  '04n' => '☁️',
            '09d' => '🌧️', '09n' => '🌧️',
            '10d' => '🌦️', '10n' => '🌧️',
            '11d' => '⛈️', '11n' => '⛈️',
            '13d' => '❄️',  '13n' => '❄️',
            '50d' => '🌫️', '50n' => '🌫️',
        ];
        return $map[$icon] ?? '🌡️';
    }
}
?>
