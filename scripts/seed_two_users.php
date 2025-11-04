<?php
declare(strict_types=1);

// Use the app bootstrap/autoload and the central DB Connection
require_once __DIR__ . '/../bootstrap.php';

use App\Database\Connection;

$adminPasswordPlain  = 'admin';
$cajeroPasswordPlain = 'cajero';

try {
    $pdo = Connection::get();
    $pdo->beginTransaction();

    // Remove all existing users
    $pdo->exec('DELETE FROM usuarios');

    // Compute hashes compatible with current auth code
    $adminHash  = password_hash($adminPasswordPlain, PASSWORD_BCRYPT);
    $cajeroHash = password_hash($cajeroPasswordPlain, PASSWORD_BCRYPT);

    // Detect if legacy column contrasena_plain exists to explicitly NULL it on insert
    $hasPlainCol = false;
    $colsStmt = $pdo->query("PRAGMA table_info(usuarios)");
    if ($colsStmt !== false) {
        $cols = $colsStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        foreach ($cols as $col) {
            if (isset($col['name']) && $col['name'] === 'contrasena_plain') {
                $hasPlainCol = true;
                break;
            }
        }
    }

    if ($hasPlainCol) {
        $sql = 'INSERT INTO usuarios (usuario, contrasena_hash, contrasena_plain, rol, fecha_creacion)
                VALUES (:usuario, :hash, NULL, :rol, datetime("now"))';
    } else {
        $sql = 'INSERT INTO usuarios (usuario, contrasena_hash, rol, fecha_creacion)
                VALUES (:usuario, :hash, :rol, datetime("now"))';
    }

    $ins = $pdo->prepare($sql);

    // admin
    $ins->execute([
        ':usuario' => 'admin',
        ':hash'    => $adminHash,
        ':rol'     => 'admin',
    ]);

    // cajero
    $ins->execute([
        ':usuario' => 'cajero',
        ':hash'    => $cajeroHash,
        ':rol'     => 'cajero',
    ]);

    $pdo->commit();
    echo "Seed completed: admin/admin and cajero/cajero created." . PHP_EOL;
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, 'Seed failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
