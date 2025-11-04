# Reset de usuarios (admin/cajero)

Este proyecto incluye un mecanismo simple para dejar la tabla `usuarios` con dos cuentas por defecto para desarrollo/pruebas.

## 1) Resumen de cuentas por defecto

- admin / admin (rol: `admin`)
- cajero / cajero (rol: `cajero`)

Las contraseñas se almacenan con `password_hash` (bcrypt). Para iniciar sesión usa las credenciales en texto claro indicadas arriba; el sistema verificará contra el hash.

## 2) Advertencia importante

- El proceso de “reset” BORRA todos los usuarios existentes en la tabla `usuarios`.
- Está pensado para entornos de desarrollo o pruebas.
- Recomendación: haz un respaldo del archivo de base de datos (ej. `data/snackshop.db`) antes de ejecutar cualquier reset.

## 3) Opción A – Reset con script PHP (recomendado)

Existe un script de seed:

- `scripts/seed_two_users.php`

Pasos:

1. Asegúrate de que la app está apagada.
2. Realiza un respaldo de la base de datos (por ejemplo, copia `data/snackshop.db`).
3. Desde la raíz del proyecto, ejecuta uno de los siguientes (según ubicación):
   
   ```powershell
   php scripts/seed_two_users.php
   ```
4. Deberías ver el mensaje: `Seed completed: admin/admin and cajero/cajero created.`
5. Inicia la app y entra con:
   - Usuario: `admin`, Contraseña: `admin`
   - Usuario: `cajero`, Contraseña: `cajero`

Notas:
- El script usa la misma `Connection` del proyecto para abrir la base SQLite.
- Detecta si existe la columna `contrasena_plain` y la deja en `NULL` en las filas nuevas.

## 4) Opción B – Reset con SQL directo (SQLite)

Puedes ejecutar este SQL directamente sobre la base (SQLite):

```sql
BEGIN TRANSACTION;
DELETE FROM usuarios;
INSERT INTO usuarios (usuario, contrasena_hash, contrasena_plain, rol, fecha_creacion)
VALUES ('admin', 'HASH_ADMIN_AQUI', NULL, 'admin', datetime('now'));
INSERT INTO usuarios (usuario, contrasena_hash, contrasena_plain, rol, fecha_creacion)
VALUES ('cajero', 'HASH_CAJERO_AQUI', NULL, 'cajero', datetime('now'));
COMMIT;
```

Antes, genera los hashes con PHP y reemplaza los placeholders:

```powershell
php -r "echo password_hash('admin', PASSWORD_BCRYPT), PHP_EOL;"
php -r "echo password_hash('cajero', PASSWORD_BCRYPT), PHP_EOL;"
```

- Copia los valores en `HASH_ADMIN_AQUI` y `HASH_CAJERO_AQUI`.
- Si tu tabla no tiene la columna `contrasena_plain`, puedes eliminarla de los `INSERT` (deja solo `usuario, contrasena_hash, rol, fecha_creacion`).

## 5) Notas sobre la migración antigua

- Existe una lógica de migración “on-login” desde `contrasena_plain` → `contrasena_hash` para instalaciones heredadas.
- Cuando usas el seed, los usuarios ya se crean con `contrasena_hash` y `contrasena_plain = NULL`, por lo que la migración solo queda como respaldo para datos antiguos.