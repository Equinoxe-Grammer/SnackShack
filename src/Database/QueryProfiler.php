<?php
namespace App\Database;

/**
 * Sencillo perfilador de consultas (por petición).
 * Mide cantidad de consultas y tiempo total invertido.
 */
class QueryProfiler
{
    private static bool $enabled = false;
    private static int $count = 0;
    private static float $totalSeconds = 0.0;

    public static function enable(): void
    {
        self::$enabled = true;
        self::reset();
    }

    public static function disable(): void
    {
        self::$enabled = false;
        self::reset();
    }

    public static function isEnabled(): bool
    {
        return self::$enabled;
    }

    public static function addQuery(float $seconds): void
    {
        if (!self::$enabled) {
            return;
        }
        if ($seconds < 0) {
            $seconds = 0.0;
        }
        self::$count++;
        self::$totalSeconds += $seconds;
    }

    public static function getSummary(): array
    {
        return [
            'query_count' => self::$count,
            'total_ms' => (int) round(self::$totalSeconds * 1000),
        ];
    }

    public static function reset(): void
    {
        self::$count = 0;
        self::$totalSeconds = 0.0;
    }
}
