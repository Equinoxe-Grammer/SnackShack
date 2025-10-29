# src/Database/

Propósito

Contiene la lógica de conexión y utilidades relacionadas con la base de datos. Es la capa central donde se inicializa la conexión PDO y se proveen helpers transaccionales si fueran necesarios.

Archivo principal

- `Connection.php` — Inicializa y devuelve la instancia de conexión (normalmente PDO). Revisa este archivo para entender cómo se gestionan DSN, opciones y errores.

Recomendaciones

- Usa prepared statements para todas las consultas para evitar inyección SQL.
- Centraliza la configuración de conexión (host, puerto, nombre, usuario, password) en `src/Config/AppConfig.php` o variables de entorno.
- Implementa manejo de excepciones claro: lanza excepciones específicas o envuelve errores de PDO con mensajes útiles para debugging en desarrollo.

Transacciones

- Para operaciones compuestas (p. ej. crear venta + decrementar stock), utiliza transacciones:

```php
$pdo->beginTransaction();
try {
  // operaciones
  $pdo->commit();
} catch (\Exception $e) {
  $pdo->rollBack();
  throw $e;
}
```

Migraciones y seeds

- El proyecto no incluye migraciones por defecto. Se recomienda añadir una solución de migraciones (Phinx, Laravel Migrations standalone o scripts SQL) para reproducibilidad.
- Para pruebas, considera usar SQLite en memoria.

Testing

- Para tests unitarios/integración se recomienda exponer la posibilidad de inyectar una instancia de PDO configurada para pruebas (SQLite-memory).

Notas

- Revisa los repositorios para ver cómo consumen la conexión (`src/Repositories/*`).
- Si necesitas añadir pooling de conexiones o adaptadores, documenta el cambio en este README.
