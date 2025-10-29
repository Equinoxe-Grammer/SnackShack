# src/

Propósito

Contiene el código fuente de la aplicación: controllers, modelos, repositorios, servicios, rutas, middlewares y configuración.

Estructura destacada

- `Config/` — Configuración de la aplicación (valores de DB, APP env, claves).
- `Controllers/` — Lógica que maneja peticiones HTTP y prepara respuestas (vistas o JSON).
- `Database/` — Conexión a la base de datos y utilidades relacionadas.
- `Middleware/` — Middlewares (auth, csrf, role).
- `Models/` — Representación de entidades/objetos de dominio.
- `Repositories/` — Abstracción de acceso a datos (CRUD y queries).
- `Routes/` — Router y definiciones de rutas.
- `Services/` — Lógica de negocio reusable (impuestos, cálculos, procesamiento de imágenes).
- `Views/` — Plantillas/partials para renderizado server-side.

Convenciones y buenas prácticas

- Las clases deben seguir PSR-4 (ya configurado por Composer/autoload).
- Separar lógica de negocio (Services) de la manipulación directa de la BD (Repositories).
- Documentar públicamente las interfaces en `Repositories/` y `Services/`.

Sugerencias de mantenimiento

- Añadir tests unitarios para Services y pruebas de integración para Repositories.
- Mantener `Config` fuera del control de versiones si contiene secretos; usar `.env` en su lugar.
