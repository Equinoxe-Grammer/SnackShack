<?php
namespace App\Database;

use App\Config\AppConfig;
use App\Database\QueryProfiler;
use PDO;
use PDOException;

class Connection
{
    private static ?PDO $instance = null;

    public static function get(): PDO
    {
        if (self::$instance === null) {
            // MySQL (comentado)
            /*
            $config = AppConfig::database();
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['name'],
                $config['charset']
            );

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$instance = new PDO($dsn, $config['user'], $config['pass'], $options);
            } catch (PDOException $e) {
                throw new PDOException('Database connection failed: ' . $e->getMessage(), (int) $e->getCode(), $e);
            }
            */

            // SQLite (nuevo)
            $dbPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'snackshop.db';
            
            // Verificar que el archivo existe
            if (!file_exists($dbPath)) {
                error_log("ERROR CRÍTICO: Base de datos SQLite no encontrada en: {$dbPath}");
                error_log("Directorio actual: " . getcwd());
                error_log("Directorio esperado: " . dirname($dbPath));
                throw new PDOException("Base de datos no encontrada. Verifique la instalación.", 1001);
            }
            
            // Verificar permisos de lectura/escritura
            if (!is_readable($dbPath)) {
                error_log("ERROR: Base de datos no es legible: {$dbPath}");
                throw new PDOException("Base de datos no tiene permisos de lectura.", 1002);
            }
            
            if (!is_writable($dbPath)) {
                error_log("ADVERTENCIA: Base de datos no es escribible: {$dbPath}");
                error_log("Algunas operaciones pueden fallar. Verifique permisos del archivo.");
            }
            
            $dsn = 'sqlite:' . $dbPath;

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$instance = new PDO($dsn, null, null, $options);
                
                // Habilitar foreign keys
                self::$instance->exec('PRAGMA foreign_keys = ON');
                
                // Verificar integridad de la base de datos
                $integrityCheck = self::$instance->query("PRAGMA integrity_check")->fetch(PDO::FETCH_COLUMN);
                if ($integrityCheck !== 'ok') {
                    error_log("ADVERTENCIA: Verificación de integridad SQLite falló: {$integrityCheck}");
                    error_log("La base de datos puede estar corrupta. Considere restaurar desde backup.");
                }
                
                // Log exitoso solo en modo debug
                if (defined('APP_DEBUG') && APP_DEBUG === true) {
                    error_log("SQLite conectado exitosamente: {$dbPath}");
                }
                
            } catch (PDOException $e) {
                error_log("ERROR de conexión SQLite:");
                error_log("  Mensaje: " . $e->getMessage());
                error_log("  Código: " . $e->getCode());
                error_log("  Archivo DB: {$dbPath}");
                error_log("  Existe: " . (file_exists($dbPath) ? 'Sí' : 'No'));
                error_log("  Legible: " . (is_readable($dbPath) ? 'Sí' : 'No'));
                error_log("  Escribible: " . (is_writable($dbPath) ? 'Sí' : 'No'));
                
                throw new PDOException(
                    'Error al conectar con la base de datos SQLite. Verifique logs para detalles.', 
                    (int) $e->getCode(), 
                    $e
                );
            }
        }
        // Activar el perfilador de consultas solo cuando esté habilitado (no forzar reset a PDOStatement)
        try {
            if (class_exists(QueryProfiler::class) && QueryProfiler::isEnabled()) {
                self::$instance->setAttribute(PDO::ATTR_STATEMENT_CLASS, [\App\Database\ProfilerPDOStatement::class, []]);
            }
            // Si está deshabilitado, dejamos el atributo como esté; ProfilerPDOStatement es seguro
            // porque QueryProfiler::addQuery no suma si está deshabilitado.
        } catch (\Throwable $e) {
            // No romper si el atributo no puede cambiarse en tiempo de ejecución
        }

        return self::$instance;
    }
}
