<?php
namespace App\Database;

class ProfilerPDOStatement extends \PDOStatement
{
    // Acepta cualquier firma que el driver quiera pasar; ignoramos los argumentos
    protected function __construct(...$args)
    {
        // PDOStatement constructor is protected by design
    }

    /**
     * Intercepta execute() para medir el tiempo.
     * @param array<int|string,mixed>|null $params
     */
    public function execute($params = null): bool
    {
        $start = microtime(true);
        try {
            return parent::execute($params);
        } finally {
            $elapsed = microtime(true) - $start;
            // Acumular en el perfilador global (mejor esfuerzo)
            if (class_exists(QueryProfiler::class)) {
                QueryProfiler::addQuery($elapsed);
            }
        }
    }
}
