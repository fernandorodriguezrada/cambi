<?php
/**
 * API Endpoint de lectura rápida (optimizado para móvil)
 * Devuelve instantáneamente los datos cacheados sin ralentizar la app.
 * Si se recibe ?force=1 (por ejemplo, desde el botón manual de Actualizar),
 * sincroniza llamando a cron.php de forma aislada antes de responder.
 */

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');

$cacheFile = __DIR__ . '/cache.json';
$historyFile = __DIR__ . '/history.json';

if (!function_exists('getHistory')) {
    function getHistory() {
        global $historyFile;
        $history = file_exists($historyFile) ? json_decode(file_get_contents($historyFile), true) : [];
        return is_array($history) ? $history : [];
    }
}

$forceUpdate = !empty($_GET['force']);

// Si solicitó actualización forzada manual, ejecutar cron.php
if ($forceUpdate && file_exists(__DIR__ . '/cron.php')) {
    // Si CLI o proc_open está disponible, o vía HTTP local
    $ch = curl_init();
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $cronUrl = $protocol . $host . '/cron.php?token=cambi_secret_key_2026';
    
    curl_setopt($ch, CURLOPT_URL, $cronUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 12);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $res = curl_exec($ch);
    curl_close($ch);
}

// 1. Si existe la caché y tiene datos válidos, responder de inmediato
if (file_exists($cacheFile)) {
    $cachedData = json_decode(file_get_contents($cacheFile), true);
    if (!empty($cachedData) && isset($cachedData['usd'])) {
        $cachedData['history'] = getHistory();
        echo json_encode($cachedData);
        exit;
    }
}

// 2. Si no hay caché previa, intentar generar llamando a cron.php vía curl
if (file_exists(__DIR__ . '/cron.php')) {
    $ch = curl_init();
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $cronUrl = $protocol . $host . '/cron.php?token=cambi_secret_key_2026';
    
    curl_setopt($ch, CURLOPT_URL, $cronUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 12);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_exec($ch);
    curl_close($ch);

    if (file_exists($cacheFile)) {
        $freshData = json_decode(file_get_contents($cacheFile), true);
        if (!empty($freshData) && isset($freshData['usd'])) {
            $freshData['history'] = getHistory();
            echo json_encode($freshData);
            exit;
        }
    }
}

// 3. Si todo falló y no hay nada en caché
http_response_code(503);
echo json_encode([
    "error" => "Información de tasas no disponible actualmente",
    "is_cached" => false
]);
