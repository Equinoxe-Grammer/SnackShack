<?php
namespace App\Services;

/**
 * Logger mínimo JSONL para diagnóstico de la app.
 * Escribe líneas JSON en data/logs/app.jsonl
 */
class AppLogger
{
    private string $file;

    public function __construct(?string $file = null)
    {
        $root = dirname(__DIR__, 2);
        $logsDir = $root . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'logs';
        if (!is_dir($logsDir)) {
            @mkdir($logsDir, 0775, true);
        }
        $this->file = $file ?: ($logsDir . DIRECTORY_SEPARATOR . 'app.jsonl');
    }

    /**
     * @param array<string,mixed> $context
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $record = [
            'time' => date('c'),
            'level' => strtoupper($level),
            'message' => $message,
            'context' => $this->sanitize($context),
        ];
        $line = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($line === false) {
            $line = '{"time":"' . date('c') . '","level":"ERROR","message":"json_encode failed","context":{}}';
        }
        @file_put_contents($this->file, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    /**
     * Mejor esfuerzo para ocultar secretos en contexto.
     * @param array<string,mixed> $ctx
     * @return array<string,mixed>
     */
    private function sanitize(array $ctx): array
    {
        $out = [];
        foreach ($ctx as $k => $v) {
            if (preg_match('/pass(word)?|token|secret|apikey|api_key/i', (string)$k)) {
                $out[$k] = '***';
                continue;
            }
            if (is_string($v)) {
                $out[$k] = $this->scrubString($v);
            } else {
                $out[$k] = $v;
            }
        }
        return $out;
    }

    private function scrubString(string $s): string
    {
        // Ocultar patrones simples tipo key=xxxxx, token: xxxxx, etc.
        $s = preg_replace('/(pass(word)?\s*[=:]\s*)([^\s,;]{4,})/i', '$1***', $s) ?? $s;
        $s = preg_replace('/(token\s*[=:]\s*)([^\s,;]{4,})/i', '$1***', $s) ?? $s;
        $s = preg_replace('/(secret\s*[=:]\s*)([^\s,;]{4,})/i', '$1***', $s) ?? $s;
        return $s;
    }

    public function getFilePath(): string
    {
        return $this->file;
    }
}
