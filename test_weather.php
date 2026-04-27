<?php
/**
 * test_weather.php — Página de diagnóstico de la API del tiempo
 * SOLO PARA DESARROLLO. Borrar antes de entregar el proyecto.
 * Acceso: http://localhost/Code%20TFG/test_weather.php
 */

require_once __DIR__ . '/model/WeatherService.php';

// Prueba con varias ciudades
$ciudades = ['Madrid', 'Barcelona', 'Sevilla', 'London'];
$resultados = [];

foreach ($ciudades as $ciudad) {
    $resultados[$ciudad] = WeatherService::getByAddress($ciudad);
}

// También probar la URL raw para ver si la API responde (con cURL)
$apiKey  = 'e7dfcf62b38e9a27a91ae61471500fdc';
$rawUrl  = "https://api.openweathermap.org/data/2.5/weather?q=Madrid&appid={$apiKey}&units=metric&lang=es";
$ch      = curl_init($rawUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 6,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_USERAGENT      => 'HelloNeighbor/1.0',
]);
$raw      = curl_exec($ch);
$curlErr  = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
$rawJson = $raw ? json_decode($raw, true) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test Weather API — Hello Neighbor</title>
    <style>
        body { font-family: monospace; background: #111; color: #eee; padding: 2rem; }
        h1 { color: #0055FF; }
        h2 { color: #38caef; margin-top: 2rem; }
        pre { background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 1rem; overflow-x: auto; white-space: pre-wrap; }
        .ok   { color: #22c55e; font-weight: bold; }
        .fail { color: #ef4444; font-weight: bold; }
        .widget-preview {
            display: inline-flex; align-items: center; gap: 10px;
            background: rgba(0,85,255,0.12); border: 1px solid rgba(0,85,255,0.3);
            border-radius: 10px; padding: 10px 16px; margin: 6px 0;
        }
        .temp { font-size: 1.2rem; font-weight: 700; }
        .desc { font-size: 0.8rem; color: #9ca3af; }
    </style>
</head>
<body>

<h1>🌤️ Diagnóstico OpenWeatherMap API</h1>

<h2>1. Respuesta RAW de la API (Madrid)</h2>
<?php if ($raw && $httpCode === 200): ?>
    <p class="ok">✅ Conexión con la API: OK (HTTP <?php echo $httpCode; ?>)</p>
    <pre><?php echo htmlspecialchars(json_encode($rawJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
<?php elseif ($raw && $httpCode === 401): ?>
    <p class="fail">❌ HTTP 401 — API Key inválida o no activada aún</p>
    <p style="color:#fbbf24">⏳ Espera 10-15 minutos y recarga esta página. Las keys nuevas de OpenWeatherMap tardan en activarse.</p>
    <pre><?php echo htmlspecialchars(json_encode($rawJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
<?php elseif ($raw): ?>
    <p class="fail">❌ HTTP <?php echo $httpCode; ?> — Respuesta inesperada</p>
    <pre><?php echo htmlspecialchars($raw); ?></pre>
<?php else: ?>
    <p class="fail">❌ Sin respuesta del servidor. Error cURL: <strong><?php echo htmlspecialchars($curlErr ?: 'desconocido'); ?></strong></p>
    <p>Prueba abrir esta URL directamente en el navegador:</p>
    <pre><a href="<?php echo htmlspecialchars($rawUrl); ?>" target="_blank" style="color:#38caef"><?php echo htmlspecialchars($rawUrl); ?></a></pre>
<?php endif; ?>

<h2>2. Resultados del WeatherService (procesados)</h2>
<?php foreach ($resultados as $ciudad => $data): ?>
    <h3><?php echo $ciudad; ?></h3>
    <?php if ($data): ?>
        <p class="ok">✅ Datos recibidos</p>
        <div class="widget-preview">
            <span style="font-size:1.8rem"><?php echo $data['emoji']; ?></span>
            <div>
                <div class="temp"><?php echo $data['temp']; ?>°C</div>
                <div class="desc"><?php echo htmlspecialchars($data['descripcion']); ?></div>
            </div>
            <div style="font-size:0.75rem;color:#9ca3af;text-align:right">
                <div>💧 <?php echo $data['humedad']; ?>%</div>
                <div>💨 <?php echo $data['viento']; ?> km/h</div>
            </div>
        </div>
        <pre><?php echo htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
    <?php else: ?>
        <p class="fail">❌ Sin datos (API no respondió o key inactiva)</p>
    <?php endif; ?>
<?php endforeach; ?>

<h2>3. Configuración PHP</h2>
<pre>
allow_url_fopen : <?php echo ini_get('allow_url_fopen') ? '✅ ON' : '❌ OFF (el problema está aquí)'; ?>

PHP version      : <?php echo PHP_VERSION; ?>

</pre>

<p style="color:#6b7280;margin-top:3rem;font-size:0.8rem">
    ⚠️ Borrar este archivo antes de entregar el proyecto.
</p>
</body>
</html>
