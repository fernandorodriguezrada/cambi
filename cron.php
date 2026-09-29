<?php
/**
 * Script de actualización en segundo plano (Cron Worker)
 * Obtiene las tasas del BCV (o fallbacks), actualiza history.json y cache.json.
 * 
 * Se puede ejecutar:
 * 1. Desde consola / DDEV: ddev exec php cron.php
 * 2. Vía HTTP (Web Cron): https://tusitio.com/cron.php?token=cambi_secret_key_2026
 * 3. Internamente desde api.php cuando la caché expira (TTL) o con ?force=1
 */

date_default_timezone_set('America/Caracas');

define('CRON_TOKEN', 'cambi_secret_key_2026');

$cacheFile = __DIR__ . '/cache.json';
$historyFile = __DIR__ . '/history.json';

if (!function_exists('fetchRemote')) {
    function fetchRemote($url, $timeout = 8) {
        $sep = str_contains($url, '?') ? '&' : '?';
        $finalUrl = $url . $sep . 't=' . time();

        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36\r\n" .
                            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8\r\n" .
                            "Accept-Language: es-ES,es;q=0.8,en-US;q=0.5,en;q=0.3\r\n",
                "timeout" => $timeout,
                "ignore_errors" => true
            ],
            "ssl" => [
                "verify_peer" => false, 
                "verify_peer_name" => false,
                "allow_self_signed" => true
            ]
        ];
        $context = stream_context_create($opts);
        return @file_get_contents($finalUrl, false, $context);
    }
}

/**
 * Raspado directo desde el portal oficial del BCV
 */
if (!function_exists('getDirectBCV')) {
    function getDirectBCV() {
        $html = fetchRemote("https://www.bcv.org.ve/", 6);
        if (!$html) return ["error" => "No se pudo conectar con el sitio del BCV"];

        $data = [];

        if (preg_match('/id="dolar".*?<strong[^>]*>\s*([\d.,]+)\s*<\/strong>/is', $html, $usd)) {
            $data['usd'] = (float) str_replace(',', '.', trim($usd[1]));
        }

        if (preg_match('/id="euro".*?<strong[^>]*>\s*([\d.,]+)\s*<\/strong>/is', $html, $eur)) {
            $data['eur'] = (float) str_replace(',', '.', trim($eur[1]));
        }

        if (isset($data['usd'])) {
            return [
                "usd" => $data['usd'],
                "eur" => $data['eur'] ?? null,
                "last_update" => date("d/m/Y, h:i A"),
                "source" => "Directo BCV"
            ];
        }
        return ["error" => "No se encontraron los selectores id='dolar' o id='euro'"];
    }
}

if (!function_exists('updateHistory')) {
    function updateHistory($usd, $eur) {
        global $historyFile;
        $history = file_exists($historyFile) ? json_decode(file_get_contents($historyFile), true) : [];
        if (!is_array($history)) $history = [];
        
        $lastEntry = !empty($history) ? end($history) : null;
        
        if (!$lastEntry || (float)$lastEntry['usd'] !== (float)$usd || (float)($lastEntry['eur'] ?? 0) !== (float)($eur ?? 0)) {
            $history[] = [
                "date" => date("d/m/Y, h:i A"),
                "usd" => $usd,
                "eur" => $eur
            ];
            if (count($history) > 20) array_shift($history);
            file_put_contents($historyFile, json_encode($history, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }
}

if (!function_exists('getHistory')) {
    function getHistory() {
        global $historyFile;
        $history = file_exists($historyFile) ? json_decode(file_get_contents($historyFile), true) : [];
        return is_array($history) ? $history : [];
    }
}

if (!function_exists('getLastKnownEuro')) {
    function getLastKnownEuro() {
        global $cacheFile;
        if (file_exists($cacheFile)) {
            $c = json_decode(file_get_contents($cacheFile), true);
            if (!empty($c['eur'])) return $c['eur'];
        }
        return null;
    }
}

if (!function_exists('saveToCache')) {
    function saveToCache($data) {
        global $cacheFile;
        $data['timestamp'] = time();
        $data['is_cached'] = true;
        @file_put_contents($cacheFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

/**
 * Función central de actualización de tasas (BCV + Fallbacks)
 */
function executeRatesUpdate() {
    global $cacheFile;

    // 1. Intentar directo desde BCV
    $directData = getDirectBCV();
    if ($directData && isset($directData['usd'])) {
        updateHistory($directData['usd'], $directData['eur']);
        $directData['history'] = getHistory();
        saveToCache($directData);
        
        return [
            "status" => "success",
            "message" => "Datos actualizados exitosamente desde BCV",
            "data" => $directData
        ];
    }

    $bcv_debug_error = $directData['error'] ?? 'Error desconocido';

    // 2. Fallbacks de respaldo si BCV no responde
    // 2.1 Primero DolarApi
    $dolarApiRes = fetchRemote("https://ve.dolarapi.com/v1/dolares/oficial");
    if ($dolarApiRes) {
        $dolarJson = json_decode($dolarApiRes, true);
        if (!empty($dolarJson['promedio'])) {
            $usd = (float)$dolarJson['promedio'];
            
            $euro = null;
            $euroApiRes = fetchRemote("https://ve.dolarapi.com/v1/euros/oficial");
            if ($euroApiRes) {
                $euroJson = json_decode($euroApiRes, true);
                if (!empty($euroJson['promedio'])) {
                    $euro = (float)$euroJson['promedio'];
                }
            }
            if (!$euro) {
                $euro = getLastKnownEuro();
            }

            $formattedDate = !empty($dolarJson['fechaActualizacion']) 
                ? date("d/m/Y, h:i A", strtotime($dolarJson['fechaActualizacion']))
                : date("d/m/Y, h:i A");

            $data = [
                "usd" => $usd,
                "eur" => $euro,
                "last_update" => $formattedDate,
                "source" => "Oficial (DolarApi)",
                "debug_bcv" => $bcv_debug_error
            ];

            updateHistory($data['usd'], $data['eur']);
            $data['history'] = getHistory();
            saveToCache($data);

            return [
                "status" => "success",
                "message" => "Datos actualizados mediante fallback (DolarApi)",
                "data" => $data
            ];
        }
    }

    // 2.2 Fallbacks secundarios (PyDolar)
    $altProviders = [
        [
            "url" => "https://pydolarve.com/api/v1/dollar?page=bcv",
            "parse" => function($d) { 
                $usd = $d['monitors']['usd']['price'] ?? $d['monitors']['bcv']['price'] ?? null;
                $eur = $d['monitors']['eur']['price'] ?? null;
                $update = $d['monitors']['usd']['last_update'] ?? $d['monitors']['bcv']['last_update'] ?? date("d/m/Y, h:i A");
                return $usd ? ["usd" => (float)$usd, "eur" => $eur ? (float)$eur : getLastKnownEuro(), "last_update" => $update, "source" => "PyDolar"] : null;
            }
        ],
        [
            "url" => "https://pydolarvenezuela-api.vercel.app/api/v1/dollar?page=bcv",
            "parse" => function($d) { 
                $usd = $d['monitors']['usd']['price'] ?? $d['monitors']['bcv']['price'] ?? null;
                $eur = $d['monitors']['eur']['price'] ?? null;
                return $usd ? ["usd" => (float)$usd, "eur" => $eur ? (float)$eur : getLastKnownEuro(), "last_update" => date("d/m/Y, h:i A"), "source" => "Vercel"] : null;
            }
        ]
    ];

    foreach ($altProviders as $p) {
        $res = fetchRemote($p['url']);
        $json = $res ? json_decode($res, true) : null;
        if ($json) {
            $data = $p['parse']($json);
            if ($data) {
                $data['debug_bcv'] = $bcv_debug_error;
                updateHistory($data['usd'], $data['eur']);
                $data['history'] = getHistory();
                saveToCache($data);

                return [
                    "status" => "success",
                    "message" => "Datos actualizados mediante fallback (" . $data['source'] . ")",
                    "data" => $data
                ];
            }
        }
    }

    return [
        "status" => "error",
        "message" => "No se pudo actualizar ninguna fuente",
        "debug_bcv" => $bcv_debug_error
    ];
}

// Ejecución directa de cron.php (CLI o Webhook)
$isDirectCli = (php_sapi_name() === 'cli' && basename($_SERVER['argv'][0] ?? '') === 'cron.php');
$isDirectWeb = (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'cron.php');

if ($isDirectCli || $isDirectWeb) {
    header('Content-Type: application/json');
    header('Cache-Control: no-store, no-cache, must-revalidate');

    $providedToken = $_GET['token'] ?? '';
    if (!$isDirectCli && $providedToken !== CRON_TOKEN) {
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'message' => 'Acceso denegado. Token no proporcionado o inválido.'
        ]);
        exit;
    }

    $result = executeRatesUpdate();
    if ($result['status'] !== 'success') {
        http_response_code(502);
    }
    echo json_encode($result);
    exit;
}
