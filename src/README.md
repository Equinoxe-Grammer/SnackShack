
# src/

Carpeta raíz del código fuente. Aquí se encuentran todas las capas principales del backend.

## Estructura y responsabilidades

- [`Config/`](Config/README.md): Configuración global (DB, entorno, flags)
- [`Controllers/`](Controllers/README.md): Orquestación HTTP
- [`Services/`](Services/README.md): Lógica de negocio reutilizable
- [`Repositories/`](Repositories/README.md): Acceso y persistencia de datos
- [`Models/`](Models/README.md): Entidades del dominio
- [`Views/`](Views/README.md): Plantillas y partials
- [`Routes/`](Routes/README.md): Enrutamiento y middlewares
- [`Database/`](Database/README.md): Conexión y helpers DB
- [`Middleware/`](Middleware/README.md): Interceptores (auth, csrf, roles)

## Buenas prácticas

- Sigue PSR-12/PSR-4 y autoload por Composer
- Mantén controllers delgados; lógica en Services
- Define interfaces para Repositories para facilitar tests
- Usa prepared statements y maneja errores de BD en repositorios

## Testing y mantenimiento

- Añade tests unitarios para Services y de integración para Repositories (SQLite en memoria para CI)
- Mantén un README por carpeta con convenciones y ejemplos
- Documenta migraciones y seeds; considera Phinx o scripts SQL

## Referencias

- [Manual técnico modular](../docs/INDEX.md)
