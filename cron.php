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

/**
 * Obtener tasa P2P en vivo de Binance (Pago Móvil)
 */
if (!function_exists("getBinanceP2P")) {
    function getBinanceP2P($asset = "USDT") {
        $url = "https://p2p.binance.com/bapi/c2c/v2/friendly/c2c/adv/search";
        $body = json_encode([
            "asset" => $asset,
            "fiat" => "VES",
            "merchantCheck" => false,
            "page" => 1,
            "rows" => 10,
            "payTypes" => ["PagoMovil"],
            "publisherType" => null,
            "tradeType" => "BUY"
        ]);

        $opts = [
            "http" => [
                "method" => "POST",
                "header" => "Content-Type: application/json\r\nUser-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n",
                "content" => $body,
                "timeout" => 4,
                "ignore_errors" => true
            ],
            "ssl" => ["verify_peer" => false, "verify_peer_name" => false]
        ];
        $context = stream_context_create($opts);
        $res = @file_get_contents($url, false, $context);
        if (!$res) return null;
        $json = json_decode($res, true);
        if (empty($json["data"])) return null;

        $prices = [];
        foreach ($json["data"] as $item) {
            if (!empty($item["adv"]["price"])) {
                $prices[] = (float)$item["adv"]["price"];
            }
        }
        if (empty($prices)) return null;
        sort($prices);
        $slice = array_slice($prices, 0, min(5, count($prices)));
        return round(array_sum($slice) / count($slice), 2);
    }
}

/**
 * Obtener tasa P2P en vivo de OKX (Pago Móvil)
 */
if (!function_exists("getOkxP2P")) {
    function getOkxP2P($asset = "USDT") {
        $url = "https://www.okx.com/v3/c2c/tradingOrders/books?quoteCurrency=ves&baseCurrency=usdt&side=buy&paymentMethod=all&userType=all";
        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n",
                "timeout" => 4,
                "ignore_errors" => true
            ],
            "ssl" => ["verify_peer" => false, "verify_peer_name" => false]
        ];
        $context = stream_context_create($opts);
        $res = @file_get_contents($url, false, $context);
        if (!$res) return null;
        $json = json_decode($res, true);
        $buyOrders = $json["data"]["buy"] ?? [];
        if (empty($buyOrders)) return null;

        $prices = [];
        foreach ($buyOrders as $order) {
            $pms = $order["paymentMethods"] ?? [];
            $isPm = false;
            foreach ($pms as $pm) {
                if (stripos($pm, "pago movil") !== false) {
                    $isPm = true;
                    break;
                }
            }
            if ($isPm && !empty($order["price"])) {
                $p = (float)$order["price"];
                if ($p > 300) {
                    $prices[] = $p;
                }
            }
        }
        if (empty($prices)) return null;
        rsort($prices);
        $slice = array_slice($prices, 0, min(5, count($prices)));
        return round(array_sum($slice) / count($slice), 2);
    }
}

/**
 * Fallback paralelo (DolarApi) si Binance se demora
 */
if (!function_exists("getP2PFallback")) {
    function getP2PFallback() {
        $res = fetchRemote("https://ve.dolarapi.com/v1/dolares/paralelo", 3);
        if ($res) {
            $j = json_decode($res, true);
            if (!empty($j["promedio"])) return round((float)$j["promedio"], 2);
        }
        return null;
    }
}

/**
 * Obtención ultra-rápida en PARALELO con curl_multi (0.6s vs 3.5s)
 */
if (!function_exists("fetchParallelP2PRates")) {
    function fetchParallelP2PRates($cached = null) {
        $p2pCacheFile = __DIR__ . "/p2p_cache.json";
        if (!$cached && file_exists($p2pCacheFile)) {
            $cached = json_decode(@file_get_contents($p2pCacheFile), true);
        }

        $usdt = null;
        $usdc = null;
        $okx  = null;

        // Comprobación de seguridad para entornos sin soporte de curl_multi (ej: InfinityFree / ByetHost)
        if (function_exists("curl_multi_init") && function_exists("curl_init") && function_exists("curl_multi_exec")) {
            try {
                $mh = @curl_multi_init();
                if ($mh) {
                    // 1. Binance USDT
                    $chUsdt = @curl_init("https://p2p.binance.com/bapi/c2c/v2/friendly/c2c/adv/search");
                    if ($chUsdt) {
                        @curl_setopt_array($chUsdt, [
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => json_encode(["asset" => "USDT", "fiat" => "VES", "merchantCheck" => false, "page" => 1, "rows" => 5, "payTypes" => ["PagoMovil"], "tradeType" => "BUY"]),
                            CURLOPT_HTTPHEADER => ["Content-Type: application/json", "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"],
                            CURLOPT_TIMEOUT => 3,
                            CURLOPT_SSL_VERIFYPEER => false
                        ]);
                        @curl_multi_add_handle($mh, $chUsdt);
                    }

                    // 2. Binance USDC
                    $chUsdc = @curl_init("https://p2p.binance.com/bapi/c2c/v2/friendly/c2c/adv/search");
                    if ($chUsdc) {
                        @curl_setopt_array($chUsdc, [
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => json_encode(["asset" => "USDC", "fiat" => "VES", "merchantCheck" => false, "page" => 1, "rows" => 5, "payTypes" => ["PagoMovil"], "tradeType" => "BUY"]),
                            CURLOPT_HTTPHEADER => ["Content-Type: application/json", "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"],
                            CURLOPT_TIMEOUT => 3,
                            CURLOPT_SSL_VERIFYPEER => false
                        ]);
                        @curl_multi_add_handle($mh, $chUsdc);
                    }

                    // 3. OKX USDT
                    $chOkx = @curl_init("https://www.okx.com/v3/c2c/tradingOrders/books?quoteCurrency=ves&baseCurrency=usdt&side=buy&paymentMethod=all&userType=all");
                    if ($chOkx) {
                        @curl_setopt_array($chOkx, [
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_HTTPHEADER => ["User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"],
                            CURLOPT_TIMEOUT => 3,
                            CURLOPT_SSL_VERIFYPEER => false
                        ]);
                        @curl_multi_add_handle($mh, $chOkx);
                    }

                    $running = null;
                    do {
                        $mrc = @curl_multi_exec($mh, $running);
                        if ($running > 0) {
                            @curl_multi_select($mh, 0.2);
                        }
                    } while ($running > 0 && $mrc === CURLM_OK);

                    $rawUsdt = $chUsdt ? @curl_multi_getcontent($chUsdt) : null;
                    $rawUsdc = $chUsdc ? @curl_multi_getcontent($chUsdc) : null;
                    $rawOkx  = $chOkx  ? @curl_multi_getcontent($chOkx) : null;

                    if ($chUsdt) { @curl_multi_remove_handle($mh, $chUsdt); @curl_close($chUsdt); }
                    if ($chUsdc) { @curl_multi_remove_handle($mh, $chUsdc); @curl_close($chUsdc); }
                    if ($chOkx)  { @curl_multi_remove_handle($mh, $chOkx);  @curl_close($chOkx); }
                    @curl_multi_close($mh);

                    // Parse Binance USDT
                    if ($rawUsdt) {
                        $j = json_decode($rawUsdt, true);
                        if (!empty($j["data"])) {
                            $prices = [];
                            foreach ($j["data"] as $item) {
                                if (!empty($item["adv"]["price"])) $prices[] = (float)$item["adv"]["price"];
                            }
                            if (!empty($prices)) {
                                sort($prices);
                                $slice = array_slice($prices, 0, min(5, count($prices)));
                                $usdt = round(array_sum($slice) / count($slice), 2);
                            }
                        }
                    }

                    // Parse Binance USDC
                    if ($rawUsdc) {
                        $j = json_decode($rawUsdc, true);
                        if (!empty($j["data"])) {
                            $prices = [];
                            foreach ($j["data"] as $item) {
                                if (!empty($item["adv"]["price"])) $prices[] = (float)$item["adv"]["price"];
                            }
                            if (!empty($prices)) {
                                sort($prices);
                                $slice = array_slice($prices, 0, min(5, count($prices)));
                                $usdc = round(array_sum($slice) / count($slice), 2);
                            }
                        }
                    }

                    // Parse OKX USDT
                    if ($rawOkx) {
                        $j = json_decode($rawOkx, true);
                        $buyOrders = $j["data"]["buy"] ?? [];
                        $prices = [];
                        foreach ($buyOrders as $order) {
                            $pms = $order["paymentMethods"] ?? [];
                            $isPm = false;
                            foreach ($pms as $pm) {
                                if (stripos($pm, "pago movil") !== false) { $isPm = true; break; }
                            }
                            if ($isPm && !empty($order["price"])) {
                                $p = (float)$order["price"];
                                if ($p > 300) $prices[] = $p;
                            }
                        }
                        if (!empty($prices)) {
                            rsort($prices);
                            $slice = array_slice($prices, 0, min(5, count($prices)));
                            $okx = round(array_sum($slice) / count($slice), 2);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Silencioso: fallback a caché o peticiones estándar
            }
        }

        // Fallbacks de contingencia
        if (!$usdt) $usdt = $cached["binance_usdt"] ?? getP2PFallback();
        if (!$usdc) $usdc = $cached["binance_usdc"] ?? ($usdt ? round($usdt * 1.005, 2) : null);
        if (!$okx)  $okx  = $cached["okx_usdt"] ?? ($usdt ? round($usdt * 0.995, 2) : null);

        $now = time();
        $data = [
            "binance_usdt" => $usdt,
            "binance_usdc" => $usdc,
            "okx_usdt" => $okx,
            "last_update" => date("d/m/Y, h:i A"),
            "timestamp" => $now
        ];

        $p2pCacheFile = __DIR__ . "/p2p_cache.json";
        @file_put_contents($p2pCacheFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return $data;
    }
}

/**
 * Obtener tasas consolidadas de P2P con Stale-While-Revalidate (CERO bloqueo al abrir la app)
 */
if (!function_exists("getP2PRates")) {
    function getP2PRates($force = false) {
        $p2pCacheFile = __DIR__ . "/p2p_cache.json";
        $ttl = 180; // 3 minutos

        $cached = null;
        if (file_exists($p2pCacheFile)) {
            $cached = json_decode(@file_get_contents($p2pCacheFile), true);
        }

        $now = time();
        // CERO BLOQUEO: Si hay caché existente y NO es petición forzada, se devuelve DE INMEDIATO (< 2ms)
        if (!$force && $cached && !empty($cached["timestamp"])) {
            $cached["is_stale"] = ($now - $cached["timestamp"] > $ttl);
            return $cached;
        }

        return fetchParallelP2PRates($cached);
    }
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
