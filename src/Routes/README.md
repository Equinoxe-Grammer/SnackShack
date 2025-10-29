
# src/Routes/

Contiene el router y la definición de rutas. Aquí se asocian endpoints con controllers, middlewares y nombres de ruta.

## Archivos principales

- `Router.php`: implementación del enrutador
- `routes.php`: definición de rutas, controllers y middlewares

## Convenciones

- Define rutas por recurso en `routes.php`
- Usa middlewares para autorización y CSRF en rutas mutativas

## Buenas prácticas

- Mantén las rutas pequeñas y manejables; delega lógica a Controllers/Services
- Documenta la API básica: método HTTP, ruta, parámetros y respuesta esperada

## Referencias

- [Manual técnico modular](../../docs/INDEX.md)
