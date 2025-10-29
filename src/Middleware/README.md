# src/Middleware/

Propósito

Contiene middlewares que interceptan peticiones HTTP antes o después de que lleguen a los controllers. Su función principal es controlar autenticación, autorización, protección CSRF y otras políticas transversales.

Middlewares presentes

- `AuthMiddleware.php` — fuerza autenticación en rutas protegidas.
- `CsrfMiddleware.php` — valida tokens CSRF en formularios (POST/PUT/DELETE) para prevenir ataques CSRF.
- `RoleMiddleware.php` — valida que el usuario tenga el rol/permiso requerido para acceder a una ruta.

Cómo funcionan (convención)

- Cada middleware debe recibir la request, procesar/validar y llamar al siguiente handler (o abortar devolviendo una respuesta con el código HTTP adecuado).
- Mantén los middlewares puros: no deben contener lógica de negocio compleja; delega en Services si hace falta.

Registro y orden

- El orden de los middlewares importa (p. ej. autenticación antes de autorización y CSRF solo en rutas mutativas).
- Revisa `src/Routes/Router.php` o el bootstrap en `public/index.php` para ver cómo se registran y aplican.

Buenas prácticas

- Reusar middlewares para rutas similares en vez de duplicar código.
- Probar middlewares con tests unitarios donde se simule una request y el siguiente handler.
- En desarrollo, loguear (con precaución) causas de fallos (p. ej. token CSRF ausente) para facilitar debugging.

Seguridad

- Asegúrate de que `CsrfMiddleware` consulte la cookie/session y el campo oculto del formulario.
- Mantén las sesiones seguras (configura cookie params, SameSite, secure flag cuando uses HTTPS).
