
# src/Config/

Configuración global de la aplicación: parámetros de conexión a la base de datos, opciones de entorno y valores globales.

## Archivos principales

- `AppConfig.php`: mapa de configuración principal (DB, APP, DEBUG, BASE_URL)

## Buenas prácticas

- No versiones credenciales en texto plano
- Si usas `.env`, mantén `AppConfig.php` como lector de variables
- Documenta cualquier clave nueva y su significado

## Ejemplo de uso

- Para cambiar la conexión DB, edita `AppConfig.php` o configura las variables de entorno y actualiza el loader

## Referencias

- [Manual técnico modular](../../docs/INDEX.md)
