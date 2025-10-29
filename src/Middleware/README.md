
# src/Middleware/

Middlewares que interceptan peticiones HTTP antes o después de los controllers. Controlan autenticación, autorización, CSRF y otras políticas transversales.

## Middlewares presentes

- `AuthMiddleware.php`: fuerza autenticación en rutas protegidas
- `CsrfMiddleware.php`: valida tokens CSRF en formularios
- `RoleMiddleware.php`: valida roles/permisos para rutas

## Funcionamiento

- Cada middleware recibe la request, procesa/valida y llama al siguiente handler (o aborta con respuesta HTTP)
- Mantén los middlewares puros; delega lógica compleja en Services

## Registro y orden

- El orden importa (autenticación antes de autorización, CSRF solo en rutas mutativas)
- Revisa `src/Routes/Router.php` o `public/index.php` para ver el registro

## Buenas prácticas

- Reusa middlewares para rutas similares
- Prueba middlewares con tests unitarios simulando request/handler
- En desarrollo, loguea causas de fallos para debugging

## Seguridad

- `CsrfMiddleware` debe consultar cookie/session y campo oculto
- Mantén sesiones seguras (cookie params, SameSite, secure flag en HTTPS)

## Referencias

- [Manual técnico modular](../../docs/INDEX.md)
