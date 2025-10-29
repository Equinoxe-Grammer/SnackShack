# src/Config/

Propósito

Almacena la configuración de la aplicación: parámetros de conexión a la base de datos, opciones de entorno y otros valores globales.

Archivos principales

- `AppConfig.php` — Mapa de configuración principal (DB, APP, DEBUG, BASE_URL).

Buenas prácticas

- No versionar credenciales en texto plano. Si se añade soporte a `.env`, mantén `AppConfig.php` como lector de esas variables.
- Documentar cualquier clave nueva y su significado.

Ejemplo de uso

- Para cambiar la conexión DB, edita `AppConfig.php` o configura las variables de entorno y actualiza el loader.
