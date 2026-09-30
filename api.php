<?php
/**
 * API Endpoint de lectura rápida (optimizado para móvil y hosting compartido)
 * 
 * - Si la tasa en caché tiene menos de 30 minutos (TTL) y no se solicita ?force=1:
 *   Devuelve instantáneamente cache.json (< 2ms) SIN tocar ni raspar la web del BCV.
 * - Si la caché expiró (más de 30 minutos) o se presiona "Actualizar" (?force=1):
 *   Intenta actualizar con BCV/fallbacks. Si el hosting no tiene salida o falla,
 *   responde de inmediato con la última tasa conocida en caché de forma resiliente.
 */

// Evitar que warnings del hosting (ej: InfinityFree) corrompan la salida JSON
error_reporting(0);
ini_set('display_errors', '0');
ob_start();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

date_default_timezone_set('America/Caracas');

$cacheFile = __DIR__ . '/cache.json';
$historyFile = __DIR__ . '/history.json';
$cacheTtlSeconds = 1800; // 30 minutos de vigencia

if (!function_exists('getHistory')) {
    function getHistory() {
        global $historyFile;
        $history = file_exists($historyFile) ? json_decode(@file_get_contents($historyFile), true) : [];
        return is_array($history) ? $history : [];
    }
}

require_once __DIR__ . '/cron.php';

if (!empty($_GET["update_p2p"])) {
    $p2p = fetchParallelP2PRates();
    if (ob_get_length()) ob_clean();
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
        $isExpired = (time() - $timestamp) > $cacheTtlSeconds;
    }
}

// Obtener datos P2P protegidos contra fallos
$p2pData = null;
try {
    $p2pData = getP2PRates($forceUpdate);
} catch (\Throwable $e) {
    $p2pCacheFile = __DIR__ . "/p2p_cache.json";
    if (file_exists($p2pCacheFile)) {
        $p2pData = json_decode(@file_get_contents($p2pCacheFile), true);
    }
}

// 2. Si la caché de BCV está fresca y no es forzada, responder DE INMEDIATO
if (!$forceUpdate && $cachedData && !$isExpired) {
    $cachedData['history'] = getHistory();
    $cachedData['p2p'] = $p2pData;
    $cachedData['is_cached'] = true;
    if (ob_get_length()) ob_clean();
    echo json_encode($cachedData);
    exit;
}

// 3. Si expiró o es forzada, intentar actualizar tasas oficiales
$updateResult = ['status' => 'error', 'message' => 'No ejecutado'];
try {
    $updateResult = executeRatesUpdate();
} catch (\Throwable $e) {
    $updateResult = ['status' => 'error', 'message' => $e->getMessage()];
}

if ($updateResult['status'] === 'success' && !empty($updateResult['data'])) {
    $freshData = $updateResult['data'];
    $freshData['history'] = getHistory();
    $freshData['p2p'] = $p2pData;
    $freshData['is_cached'] = false;
    if (ob_get_length()) ob_clean();
    echo json_encode($freshData);
    exit;
}

// 4. Fallback resiliente: SIEMPRE devolver la última tasa en caché si existe
if ($cachedData) {
    $cachedData['history'] = getHistory();
    $cachedData['p2p'] = $p2pData;
    $cachedData['is_cached'] = true;
    $cachedData['stale'] = true;
    if (!empty($updateResult['debug_bcv'])) {
        $cachedData['debug_bcv'] = $updateResult['debug_bcv'];
    }
    if (ob_get_length()) ob_clean();
    echo json_encode($cachedData);
    exit;
}

// 5. En el caso extremo de que todo falle y no haya caché previa
http_response_code(503);
if (ob_get_length()) ob_clean();
echo json_encode([
    "error" => "Información de tasas no disponible actualmente",
    "is_cached" => false,
    "debug" => $updateResult['message'] ?? null
]);
