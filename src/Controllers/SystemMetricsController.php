<?php
namespace App\Controllers;

class SystemMetricsController
{
    /**
     * Devuelve métricas de sistema y PHP para monitoreo básico.
     * Respuesta: JSON
     */
    public function system(): void
    {
        // Proteger JSON
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-store, must-revalidate');
            header('Pragma: no-cache');
        }

    $now = microtime(true);
        $appRoot = dirname(__DIR__, 2); // .../src -> project root
    $deep = isset($_GET['deep']) && (int)$_GET['deep'] === 1;
    $logLines = isset($_GET['log_lines']) ? max(0, min(1000, (int)$_GET['log_lines'])) : 0;

        // Memoria PHP
        $memUsage = memory_get_usage(true);
        $memPeak  = memory_get_peak_usage(true);
        $memLimitRaw = ini_get('memory_limit');
        $memLimitBytes = self::toBytes($memLimitRaw);
        $memPct = ($memLimitBytes > 0) ? round(($memUsage / $memLimitBytes) * 100, 1) : null;

        // Disco
        $diskFree = @disk_free_space($appRoot) ?: null;
        $diskTotal = @disk_total_space($appRoot) ?: null;
        $diskFreePct = ($diskFree !== null && $diskTotal) ? round(($diskFree / $diskTotal) * 100, 1) : null;

        // OPcache
        $opcache = function_exists('opcache_get_status') ? @opcache_get_status(false) : null;

        // Proceso (Windows: mejor esfuerzo con WMIC si está disponible)
        $pid = getmypid();
        $proc = [
            'pid' => $pid,
            'working_set' => null,
            'private_bytes' => null,
        ];
        try {
            // Nota: exec puede estar deshabilitado en algunos entornos
            if (stripos(PHP_OS, 'WIN') === 0 && function_exists('exec')) {
                // WorkingSetSize via wmic (bytes)
                $out = [];
                @exec('wmic process where processid=' . (int)$pid . ' get WorkingSetSize /value', $out);
                foreach ($out as $line) {
                    if (stripos($line, 'WorkingSetSize=') === 0) {
                        $val = (int)trim(substr($line, strlen('WorkingSetSize=')));
                        if ($val > 0) {
                            $proc['working_set'] = $val;
                        }
                    }
                }
                $out2 = [];
                @exec('wmic process where processid=' . (int)$pid . ' get PageFileUsage /value', $out2);
                foreach ($out2 as $line) {
                    if (stripos($line, 'PageFileUsage=') === 0) {
                        $kb = (int)trim(substr($line, strlen('PageFileUsage=')));
                        if ($kb > 0) {
                            $proc['private_bytes'] = $kb * 1024; // KB -> bytes
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignorar errores: campos quedarán null
        }

        // PHP ini relevantes
        $phpIni = [
            'memory_limit' => $memLimitRaw,
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_execution_time' => ini_get('max_execution_time'),
            'max_input_vars' => ini_get('max_input_vars'),
            'error_log' => ini_get('error_log'),
            'display_errors' => ini_get('display_errors'),
            'log_errors' => ini_get('log_errors'),
        ];

        // DB health (rápido)
        $db = [ 'ok' => null, 'driver' => null, 'latency_ms' => null, 'details' => null ];
        $dbStart = microtime(true);
        try {
            $pdo = \App\Database\Connection::get();
            $driver = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
            $db['driver'] = $driver;
            // SELECT 1 rápido
            $pdo->query('SELECT 1')->fetch();
            $db['ok'] = true;
            $db['latency_ms'] = round((microtime(true) - $dbStart) * 1000, 1);
            if ($driver === 'sqlite') {
                $sqlitePath = $appRoot . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'snackshop.db';
                $db['sqlite'] = [
                    'path' => $sqlitePath,
                    'exists' => file_exists($sqlitePath),
                    'size_bytes' => file_exists($sqlitePath) ? filesize($sqlitePath) : null,
                    'writable' => file_exists($sqlitePath) ? is_writable($sqlitePath) : null,
                    'mtime' => file_exists($sqlitePath) ? @filemtime($sqlitePath) : null,
                ];
            }
        } catch (\Throwable $e) {
            $db['ok'] = false;
            $db['latency_ms'] = round((microtime(true) - $dbStart) * 1000, 1);
            $db['details'] = substr($e->getMessage(), 0, 280);
        }

        // Deep info opcional (más costosa)
        $deepInfo = null;
        if ($deep) {
            $deepInfo = [];
            // Tamaño de la carpeta data/
            $dataDir = $appRoot . DIRECTORY_SEPARATOR . 'data';
            $deepInfo['data_dir'] = [
                'path' => $dataDir,
                'exists' => is_dir($dataDir),
                'writable' => is_dir($dataDir) ? is_writable($dataDir) : null,
                'approx_size_bytes' => is_dir($dataDir) ? self::dirSize($dataDir, 2000) : null, // limita a 2000 archivos
            ];
            // Uptime del SO (Windows, mejor esfuerzo)
            $deepInfo['os'] = [ 'uptime_seconds' => null ];
            try {
                if (stripos(PHP_OS, 'WIN') === 0 && function_exists('exec')) {
                    $o = [];
                    @exec('wmic os get lastbootuptime /value', $o);
                    foreach ($o as $line) {
                        if (stripos($line, 'LastBootUpTime=') === 0) {
                            $val = trim(substr($line, strlen('LastBootUpTime=')));
                            // Formato: yyyymmddHHMMSS.mmmmmms+UUU
                            $ts = strtotime(substr($val,0,14));
                            if ($ts) {
                                $deepInfo['os']['uptime_seconds'] = (int)(time() - $ts);
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // ignorar
            }
            // Tail del error_log si se pide
            if ($logLines > 0) {
                $logFile = (string)($phpIni['error_log'] ?? '');
                $deepInfo['error_log'] = [
                    'path' => $logFile !== '' ? $logFile : null,
                    'exists' => $logFile && file_exists($logFile),
                    'tail' => ($logFile && file_exists($logFile)) ? self::tailFile($logFile, $logLines) : null,
                ];
            }
            // OPcache detalles extra
            if (is_array($opcache)) {
                $deepInfo['opcache'] = $opcache;
            }
        }

        $data = [
            'timestamp' => date('c', (int)$now),
            'php' => [
                'version' => PHP_VERSION,
                'sapi' => PHP_SAPI,
                'memory' => [
                    'usage_bytes' => $memUsage,
                    'peak_bytes' => $memPeak,
                    'limit' => $memLimitRaw,
                    'limit_bytes' => $memLimitBytes,
                    'usage_pct_of_limit' => $memPct,
                ],
                'loaded_extensions' => get_loaded_extensions(),
                'opcache' => [
                    'enabled' => is_array($opcache) ? (bool)($opcache['opcache_enabled'] ?? false) : null,
                    'cache_full' => is_array($opcache) ? (bool)($opcache['cache_full'] ?? false) : null,
                    'opcache_memory_usage' => is_array($opcache) && isset($opcache['memory_usage']) ? $opcache['memory_usage'] : null,
                ],
                'ini' => $phpIni,
            ],
            'system' => [
                'os' => PHP_OS,
                'uname' => function_exists('php_uname') ? php_uname() : null,
                'process' => $proc,
                'disk' => [
                    'free_bytes' => $diskFree,
                    'total_bytes' => $diskTotal,
                    'free_pct' => $diskFreePct,
                ],
            ],
            'app' => [
                'included_files' => count(get_included_files()),
                'session_user' => isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : null,
                'role' => $_SESSION['rol'] ?? null,
                'request' => [
                    'uri' => $_SERVER['REQUEST_URI'] ?? null,
                    'method' => $_SERVER['REQUEST_METHOD'] ?? null,
                    'client' => self::maskIp($_SERVER['REMOTE_ADDR'] ?? null),
                ],
            ],
            'database' => $db,
            'deep' => $deepInfo,
        ];

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private static function toBytes($val)
    {
        // Convierte valores tipo "512M" en bytes
        $val = trim((string)$val);
        if ($val === '' || $val === '-1') {
            return -1; // sin límite
        }
        $last = strtolower($val[strlen($val) - 1]);
        $num = (float)$val;
        switch ($last) {
            case 'g':
                $num *= 1024;
                // no break
            case 'm':
                $num *= 1024;
                // no break
            case 'k':
                $num *= 1024;
        }
        return (int)$num;
    }

    private static function tailFile(string $file, int $lines): ?string
    {
        if ($lines <= 0 || !is_readable($file)) return null;
        $f = @fopen($file, 'rb');
        if (!$f) return null;
        $buffer = '';
        $chunkSize = 4096;
        $pos = -1;
        $lineCount = 0;
        fseek($f, 0, SEEK_END);
        $fileSize = ftell($f);
        while ($fileSize + $pos > 0 && $lineCount <= $lines) {
            $seek = max(0, $fileSize + $pos - $chunkSize);
            $read = $fileSize + $pos - $seek;
            fseek($f, $seek, SEEK_SET);
            $chunk = fread($f, $read);
            $buffer = $chunk . $buffer;
            $lineCount = substr_count($buffer, "\n");
            $pos -= $chunkSize;
        }
        fclose($f);
        $arr = explode("\n", $buffer);
        $arr = array_slice($arr, -$lines);
        return implode("\n", $arr);
    }

    private static function dirSize(string $path, int $maxFiles = 5000): int
    {
        $total = 0;
        $count = 0;
        if (!is_dir($path)) return 0;
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS));
        foreach ($it as $file) {
            $total += $file->getSize();
            $count++;
            if ($count >= $maxFiles) break;
        }
        return $total;
    }

    private static function maskIp(?string $ip): ?string
    {
        if (!$ip) return null;
        if (strpos($ip, ':') !== false) { // IPv6
            return preg_replace('/:[0-9a-f]{0,4}$/i', ':*', $ip);
        }
        $parts = explode('.', $ip);
        if (count($parts) === 4) {
            $parts[3] = '*';
            return implode('.', $parts);
        }
        return $ip;
    }
}
