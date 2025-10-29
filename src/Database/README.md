
# src/Database/

Lógica de conexión y utilidades relacionadas con la base de datos. Aquí se inicializa la conexión PDO y se proveen helpers transaccionales.

## Archivo principal

- `Connection.php`: inicializa y devuelve la instancia de conexión (PDO)

## Recomendaciones

- Usa prepared statements para todas las consultas
- Centraliza la configuración de conexión en `src/Config/AppConfig.php` o variables de entorno
- Implementa manejo de excepciones claro

## Transacciones

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

## Migraciones y seeds

- Añade una solución de migraciones (Phinx, scripts SQL) para reproducibilidad
- Para pruebas, usa SQLite en memoria

## Testing

- Expón la posibilidad de inyectar una instancia de PDO para pruebas

## Notas

- Revisa los repositorios para ver cómo consumen la conexión
- Si añades pooling o adaptadores, documenta el cambio aquí

## Referencias

- [Manual técnico modular](../../docs/INDEX.md)
