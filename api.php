<?php
/**
 * API Endpoint de lectura rápida (optimizado para móvil)
 * 
 * - Si la tasa en caché tiene menos de 30 minutos (TTL) y no se solicita ?force=1:
 *   Devuelve instantáneamente cache.json (< 2ms) SIN tocar ni raspar la web del BCV.
 * - Si la caché expiró (más de 30 minutos) o se presiona "Actualizar" (?force=1):
 *   Ejecuta 1 sola actualización, guarda el nuevo cache.json con timestamp,
 *   y sirve los datos frescos a todos los usuarios siguientes.
 */

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');

date_default_timezone_set('America/Caracas');

$cacheFile = __DIR__ . '/cache.json';
$historyFile = __DIR__ . '/history.json';
$cacheTtlSeconds = 1800; // 30 minutos de vigencia para evitar peticiones redundantes

if (!function_exists('getHistory')) {
    function getHistory() {
        global $historyFile;
        $history = file_exists($historyFile) ? json_decode(file_get_contents($historyFile), true) : [];
        return is_array($history) ? $history : [];
    }
}

if (!empty($_GET["update_p2p"])) {
    require_once __DIR__ . "/cron.php";
    $p2p = fetchParallelP2PRates();
    echo json_encode(["status" => "success", "p2p" => $p2p]);
    exit;
}

$forceUpdate = !empty($_GET['force']);

// 1. Cargar caché actual si existe
$cachedData = null;
$isExpired = true;

if (file_exists($cacheFile)) {
    $cachedData = json_decode(@file_get_contents($cacheFile), true);
    if (!empty($cachedData) && isset($cachedData['usd'])) {
        $timestamp = $cachedData['timestamp'] ?? 0;
        // Si no tiene timestamp numérico pero tiene fecha legible, o si han pasado menos de 30 min:
        $isExpired = (time() - $timestamp) > $cacheTtlSeconds;
    }
}

require_once __DIR__ . '/cron.php';

// P2P en vivo (Binance USDT, Binance USDC, OKX USDT) con caché inteligente de 3 minutos
$p2pData = getP2PRates($forceUpdate);

// 2. Si la caché de BCV está fresca y no es forzada, responder DE INMEDIATO
if (!$forceUpdate && $cachedData && !$isExpired) {
    $cachedData['history'] = getHistory();
    $cachedData['p2p'] = $p2pData;
    $cachedData['is_cached'] = true;
    echo json_encode($cachedData);
    exit;
}

// 3. Si expiró o es forzada, ejecutar la actualización de tasas oficiales
$updateResult = executeRatesUpdate();

if ($updateResult['status'] === 'success' && !empty($updateResult['data'])) {
    $freshData = $updateResult['data'];
    $freshData['history'] = getHistory();
    $freshData['p2p'] = $p2pData;
    $freshData['is_cached'] = false;
    echo json_encode($freshData);
    exit;
}

// 4. Si el raspado falló pero tenemos una tasa previa en caché, devolver la previa (Fallback resiliente)
if ($cachedData) {
    $cachedData['history'] = getHistory();
    $cachedData['p2p'] = $p2pData;
    $cachedData['is_cached'] = true;
    $cachedData['stale'] = true;
    if (!empty($updateResult['debug_bcv'])) {
        $cachedData['debug_bcv'] = $updateResult['debug_bcv'];
    }
    echo json_encode($cachedData);
    exit;
}

// 5. En el caso extremo de que todo falle y no haya caché
http_response_code(503);
echo json_encode([
    "error" => "Información de tasas no disponible actualmente",
    "is_cached" => false,
    "debug" => $updateResult['message'] ?? null
]);
