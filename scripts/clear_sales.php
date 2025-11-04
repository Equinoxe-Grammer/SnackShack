<?php
// scripts/clear_sales.php
// Limpia ventas (y detalle_ventas) por fecha o todas.
// Uso:
//   php scripts/clear_sales.php --date=YYYY-MM-DD   # elimina solo esa fecha (por defecto hoy)
//   php scripts/clear_sales.php --all               # elimina todas las ventas

require_once __DIR__ . '/../bootstrap.php';

use App\Database\Connection;

function parseArgs(array $argv): array {
    $opts = [
        'all' => false,
        'date' => null,
    ];
    foreach ($argv as $arg) {
        if ($arg === '--all') { $opts['all'] = true; }
        if (strpos($arg, '--date=') === 0) {
            $val = substr($arg, 7);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) {
                $opts['date'] = $val;
            } else {
                fwrite(STDERR, "Formato de fecha inválido. Use YYYY-MM-DD\n");
                exit(1);
            }
        }
    }
    return $opts;
}

$opts = parseArgs($argv);

try {
    $pdo = Connection::get();
    $pdo->beginTransaction();

    if ($opts['all']) {
        // Borrar todo
        $pdo->exec('DELETE FROM detalle_ventas');
        $pdo->exec("DELETE FROM ventas");
        $pdo->commit();
        echo "OK: Se eliminaron todas las ventas.\n";
        exit(0);
    }

    $date = $opts['date'] ?: date('Y-m-d');

    // Identificar ventas por fecha
    $idsStmt = $pdo->prepare("SELECT venta_id FROM ventas WHERE date(fecha_hora) = :d");
    $idsStmt->execute([':d' => $date]);
    $ids = $idsStmt->fetchAll(PDO::FETCH_COLUMN, 0);

    if (!$ids) {
        $pdo->rollBack();
        echo "No hay ventas para la fecha $date.\n";
        exit(0);
    }

    // Borrar detalle y ventas
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $delDet = $pdo->prepare("DELETE FROM detalle_ventas WHERE venta_id IN ($placeholders)");
    foreach ($ids as $i => $id) { $delDet->bindValue($i+1, (int)$id, PDO::PARAM_INT); }
    $delDet->execute();

    $delVen = $pdo->prepare("DELETE FROM ventas WHERE venta_id IN ($placeholders)");
    foreach ($ids as $i => $id) { $delVen->bindValue($i+1, (int)$id, PDO::PARAM_INT); }
    $delVen->execute();

    $pdo->commit();
    echo "OK: Eliminadas ".count($ids)." ventas de la fecha $date.\n";
    exit(0);
} catch (Throwable $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, "Error limpiando ventas: ".$e->getMessage()."\n");
    exit(1);
}
