<?php
// Habilitar errores solo en desarrollo (comentar en producción)
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');

// Configurar zona horaria para México
date_default_timezone_set('America/Mexico_City');

$autoload = __DIR__ . '/vendor/autoload.php';

if (file_exists($autoload)) {
    require_once $autoload;
} else {
    spl_autoload_register(static function (string $class): void {
        $prefix = 'App\\';
        $baseDir = __DIR__ . '/src/';

        if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
            return;
        }

        $relativeClass = substr($class, strlen($prefix));
        $path = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

        if (file_exists($path)) {
            require_once $path;
        }
    });
}

if (!ini_get('date.timezone')) {
    date_default_timezone_set('America/Mexico_City');
}
