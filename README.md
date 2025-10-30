
<a id="snackshop"></a>
<a id="-snackshop"></a>
# SnackShop
<!-- TOC -->
<a id="contenido"></a>
<a id="-contenido"></a>
## Contenido

- [📚 Documentación Modular](#-documentacion-modular)
- [Resumen](#resumen)
- [Requisitos](#requisitos)
- [Instalación Rápida](#instalacion-rapida)
- [Configuración](#configuracion)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Arrancar la Aplicación](#arrancar-la-aplicacion)
- [Buenas Prácticas y Optimización](#buenas-practicas-y-optimizacion)
- [Testing y Calidad](#testing-y-calidad)
- [Contribuir](#contribuir)
- [Licencia](#licencia)
- [Requisitos](#requisitos)
- [Instalación rápida](#instalacion-rapida)
- [Configuración](#configuracion)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Arrancar la aplicación](#arrancar-la-aplicacion)
- [Arquitectura y responsabilidades](#arquitectura-y-responsabilidades)
- [Modelos, Repositorios y Servicios](#modelos-repositorios-y-servicios)
- [Contenido rápido](#contenido-rapido)
- [Resumen](#resumen)
- [Requisitos mínimos](#requisitos-minimos)
- [Instalación y arranque (rápido)](#instalacion-y-arranque-rapido)
- [Configuración](#configuracion)
- [Estructura del proyecto (resumen)](#estructura-del-proyecto-resumen)
- [Comandos útiles](#comandos-utiles)
- [Pruebas y calidad](#pruebas-y-calidad)
- [Siguientes pasos recomendados](#siguientes-pasos-recomendados)
<!-- /TOC -->

Aplicación PHP ligera para gestionar una tienda de snacks — pensada para desarrolladores que quieren un punto de partida claro, extensible y fácil de desplegar.

Quick links:

- 📚 Índice maestro: [docs/INDEX.md](docs/INDEX.md)
- 🧭 Guía de arquitectura: [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md)
- 🔌 API: [docs/API.md](docs/API.md)
- 🚀 Despliegue: [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md)

Developer quickstart (copy & paste):

```powershell
cd C:\Users\david\Downloads\SnackShop\SnackShop\www\Snackshop
composer install
php -S localhost:8000 -t public
```

Abre http://localhost:8000

Resumen rápido:
Proyecto modular (Controllers → Services → Repositories → Models) con ejemplos y guías de testing para desarrolladores. Actualmente el repositorio incluye documentación y ejemplos de tests (ver `docs/TESTING.md` y `docs/examples/`), pero no contiene una suite de tests automatizados por defecto.

<a id="documentacion-modular"></a>
<a id="-documentacion-modular"></a>
## 📚 Documentación Modular

- [Índice Maestro](docs/INDEX.md)
- [Arquitectura](docs/ARCHITECTURE.md)
- [API](docs/API.md)
- [Base de Datos](docs/DATABASE.md)
- [Despliegue](docs/DEPLOYMENT.md)
- [Configuración](docs/CONFIGURATION.md)
- [Desarrollo](docs/DEVELOPMENT.md)
- [Testing](docs/TESTING.md)
- [Contribución](docs/CONTRIBUTING.md)
- [Servicios](docs/SERVICES.md)
- [Ejemplos y Tutoriales](docs/EXAMPLES.md)
- [Troubleshooting](docs/TROUBLESHOOTING.md)

<a id="resumen"></a>
<a id="-resumen"></a>
## Resumen

SnackShop es una aplicación PHP backend + vistas, con autenticación, gestión de productos, ventas, variantes, control de stock y estructura limpia (MVC, servicios, repositorios, middlewares y vistas). Pensado para despliegue rápido en LAMP/LEMP o desarrollo local.

<a id="requisitos"></a>
<a id="-requisitos"></a>
## Requisitos

- PHP 7.4+ (recomendado 8.0+)
- Composer ([getcomposer.org](https://getcomposer.org))
- Extensiones: PDO, mbstring, openssl, fileinfo
- MySQL/MariaDB/Postgres (ver `src/Database/Connection.php` y `src/Config/AppConfig.php`)

<a id="instalacion-rapida"></a>
<a id="-instalacion-rapida"></a>
## Instalación Rápida

```powershell
cd c:\Users\david\Downloads\SnackShop\SnackShop\www\Snackshop
composer install
```

Si composer falla, revisa `composer.json` y tu versión de PHP.

<a id="configuracion"></a>
<a id="-configuracion"></a>
## Configuración

Edita `src/Config/AppConfig.php` o usa `.env` si está soportado. Define:

- DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS
- APP_ENV (development|production)
- APP_DEBUG (true|false)

<a id="estructura-del-proyecto"></a>
<a id="-estructura-del-proyecto"></a>
## Estructura del Proyecto

Ver detalles y convenciones en los README de cada subcarpeta:

- [`src/`](src/README.md): Código fuente principal
- [`src/Controllers/`](src/Controllers/README.md): Controladores HTTP
- [`src/Services/`](src/Services/README.md): Lógica de negocio
- [`src/Repositories/`](src/Repositories/README.md): Acceso a datos
- [`src/Models/`](src/Models/README.md): Entidades del dominio
- [`src/Database/`](src/Database/README.md): Conexión y utilidades DB
- [`src/Middleware/`](src/Middleware/README.md): Middlewares
- [`src/Routes/`](src/Routes/README.md): Enrutamiento
- [`src/Views/`](src/Views/README.md): Plantillas y vistas
- [`public/`](public/README.md): DocumentRoot y assets
- [`data/`](data/README.md): Archivos generados/locales

<a id="arrancar-la-aplicacion"></a>
<a id="-arrancar-la-aplicacion"></a>
## Arrancar la Aplicación

```powershell
php -S localhost:8000 -t public
```

### Ejecutar con phpdesktop (Windows)

Si prefieres distribuir o ejecutar la app como una aplicación de escritorio ligera en Windows, puedes usar phpdesktop.

- Descarga phpdesktop (por ejemplo, https://github.com/cztomczak/phpdesktop) y extrae el contenido en una carpeta junto al repositorio o dentro de una estructura de distribución.
- Ajusta el campo "document_root" en `settings.json` de phpdesktop para apuntar a la carpeta `public/` de este proyecto.
- Asegúrate de que la versión de PHP incluida en phpdesktop tenga la extensión PDO habilitada y, si vas a usar SQLite, que `pdo_sqlite` esté disponible (revisa `php.ini` dentro del paquete phpdesktop).

Ejemplo mínimo de fragmento `settings.json` (sólo las claves relevantes):

```json
{
  "application_title": "SnackShop",
  "start_url": "/",
  "document_root": "public",
  "php": {
    "enable": true,
    "ini": "php.ini"
  }
}
```

Nota sobre permisos y base de datos SQLite:

- Este repositorio incluye un ejemplo de base de datos SQLite en `data/snackshop.db` (útil para pruebas locales). Si la aplicación usa SQLite, asegúrate de que el archivo y la carpeta `data/` sean escribibles por el usuario que ejecuta phpdesktop.
- Para usar SQLite como backend en lugar de MySQL, ajusta tu configuración en `src/Config/AppConfig.php` para usar PDO SQLite y apunta al path correcto, por ejemplo:

```php
// Ejemplo conceptual: configurar PDO para SQLite
return [
  'db' => [
    'driver' => 'sqlite',
    'database' => __DIR__ . '/../../data/snackshop.db',
  ],
  'app' => [/* ... */]
];
```

Si necesitas que adapte el código para detectar automáticamente SQLite o añadir soporte `.env` para seleccionar el driver, lo puedo añadir como un cambio pequeño.


<a id="buenas-practicas-y-optimizacion"></a>
<a id="-buenas-practicas-y-optimizacion"></a>
## Buenas Prácticas y Optimización

- Sigue PSR-12/PSR-4 y mantén controllers delgados.
- Usa servicios y repositorios para separar lógica y persistencia.
- Configura OPcache y caché en producción.
- Usa prepared statements y valida toda entrada de usuario.
- Consulta [TROUBLESHOOTING.md](docs/TROUBLESHOOTING.md) para resolución de problemas y optimización avanzada.

<a id="testing-y-calidad"></a>
<a id="-testing-y-calidad"></a>
## Testing y Calidad

- Usa PHPUnit para tests unitarios/integración.
- Mockea repositorios en tests de servicios.
- Usa PHPStan y PHP CS Fixer para análisis estático y estilo.

<a id="contribuir"></a>
<a id="-contribuir"></a>
## Contribuir

Consulta [CONTRIBUTING.md](docs/CONTRIBUTING.md) para el flujo de trabajo, estándares y código de conducta.

<a id="licencia"></a>
<a id="-licencia"></a>
## Licencia

Este proyecto se distribuye bajo la licencia GNU Affero General Public License v3 (AGPL-3.0).
Consulte el fichero `LICENSE` en la raíz del repositorio para el texto completo.

SPDX: AGPL-3.0

<a id="requisitos"></a>
<a id="-requisitos"></a>
## Requisitos

- PHP 7.4 o superior (recomendado PHP 8.0+). (Suposición basada en presencia de composer y estructura; ajusta según tu entorno.)
- Composer instalado (https://getcomposer.org)
- Extensiones PHP típicas: PDO (para la DB que uses), mbstring, openssl, fileinfo (para subida de imágenes). Ajusta según errores.
- Un servidor de base de datos compatible (MySQL/MariaDB/Postgres — la conexión está en `src/Database/Connection.php` y la configuración en `src/Config/AppConfig.php`).

<a id="instalacion-rapida"></a>
<a id="-instalacion-rapida"></a>
## Instalación rápida

En PowerShell (Windows) o terminal:

```powershell
cd c:\Users\david\Downloads\SnackShop\SnackShop\www\Snackshop
composer install
```

Notas:
- Si tu versión de PHP no está en PATH, usa la ruta completa hacia el ejecutable php.
- Si composer falla por dependencias, revisa `composer.json`.

<a id="configuracion"></a>
<a id="-configuracion"></a>
## Configuración

1. Configura la conexión a la base de datos. Edita `src/Config/AppConfig.php` o crea un archivo `.env` si tu proyecto lo soporta. Valores típicos a definir:

- DB_HOST
- DB_PORT
- DB_NAME
- DB_USER
- DB_PASS
- APP_ENV (development|production)
- APP_DEBUG (true|false)
- BASE_URL (ej. http://localhost:8000)

Ejemplo (conceptual) — no hay `.env` por defecto en el repo, así que aplica la configuración directamente en `AppConfig.php` o añade soporte a dotenv:

```php
// src/Config/AppConfig.php (ajusta según sea necesario)
return [
  'db' => [
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'snackshop',
    'user' => 'root',
    'password' => '',
  ],
  'app' => [
    'debug' => true,
    'base_url' => 'http://localhost:8000'
  ]
];
```

2. Asegura permisos en la carpeta `data/` si se usa para almacenaje local.

<a id="estructura-del-proyecto"></a>
<a id="-estructura-del-proyecto"></a>
## Estructura del proyecto

Raíz (resumen):

- `public/` — Punto de entrada público (contiene `index.php`, `pretty-urls.php` y assets estáticos como CSS/JS/imagenes).
- `src/` — Código fuente de la aplicación:
  - `Config/` — Configuración de la app.
  - `Controllers/` — Lógica que responde a rutas HTTP.
  - `Database/Connection.php` — Conexión DB central.
  - `Middleware/` — Middlewares como autenticación y CSRF.
  - `Models/` — Entidades (Product, User, Sale, etc.).
  - `Repositories/` — Acceso a datos y abstracción de persistencia.
  - `Services/` — Lógica de negocio reutilizable (impuestos, imagenes, cálculo de costo, etc.).
  - `Routes/` — Router y definición de rutas.
  - `Views/` — Plantillas PHP para render.
- `vendor/` — Dependencias instaladas por Composer.
- `data/` — Almacenamiento local (si se utiliza).

<a id="arrancar-la-aplicacion"></a>
<a id="-arrancar-la-aplicacion"></a>
## Arrancar la aplicación

Modo desarrollo (servidor embebido PHP):

```powershell
<a id="desde-la-carpeta-project-root-que-contiene-public"></a>
<a id="-desde-la-carpeta-project-root-que-contiene-public"></a>
# Desde la carpeta project root que contiene public/
php -S localhost:8000 -t public
<a id="abre-httplocalhost8000-en-tu-navegador"></a>
<a id="-abre-httplocalhost8000-en-tu-navegador"></a>
# Abre http://localhost:8000 en tu navegador
```

Usando Apache o Nginx:
- Apunta el DocumentRoot a `.../public/`.
- Habilita mod_rewrite (si `pretty-urls.php`/htaccess lo requiere).

<a id="arquitectura-y-responsabilidades"></a>
<a id="-arquitectura-y-responsabilidades"></a>
## Arquitectura y responsabilidades

- Controllers: reciben Request, usan Services y Repositories, devuelven Views o JSON.
- Repositories: encapsulan consultas SQL/ORM y operaciones CRUD.
- Services: lógica de negocio (validaciones, cálculos, operaciones compuestas).
- Models: simples DTOs/POPOs que representan filas de tablas.
- Middlewares: control de acceso y protección CSRF.

Controladores observables en `src/Controllers/` (ejemplos): `ProductController`, `SalesController`, `UserController`, `AuthController`, `DashboardController`, `ComprasController`, `VariantController`, etc. Revisa esos archivos para ver acciones disponibles (index, create, update, delete, show, etc.).

<a id="modelos-repositorios-y-servicios"></a>
<a id="-modelos-repositorios-y-servicios"></a>
## Modelos, Repositorios y Servicios

- `src/Models/` contiene modelos como `Product.php`, `User.php`, `Sale.php`, `Variant.php`.
<a id="snackshop"></a>
<a id="-snackshop"></a>
# SnackShop

Documentación general y guía rápida del proyecto SnackShop — aplicación PHP ligera para gestionar una tienda de snacks (productos, variantes, ventas y usuarios).

Este README ha sido reorganizado y condensado para facilitar la lectura por desarrolladores y operadores.

<a id="contenido-rapido"></a>
<a id="-contenido-rapido"></a>
## Contenido rápido

- Resumen
- Requisitos mínimos
- Instalación y arranque rápido
- Configuración (dónde poner credenciales)
- Estructura del proyecto
- Comandos frecuentes
- Siguientes pasos y recomendaciones

---

<a id="resumen"></a>
<a id="-resumen"></a>
## Resumen

SnackShop es una aplicación backend con vistas server-rendered en PHP. Sigue un patrón en capas: Controllers → Services → Repositories → Models → Views. Es una base mínima pensada para ser fácil de entender, extender y desplegar en plataformas LAMP/LEMP o contenedores.

<a id="requisitos-minimos"></a>
<a id="-requisitos-minimos"></a>
## Requisitos mínimos

- PHP 7.4+ (se recomienda PHP 8.0+)
- Composer
- Extensiones: pdo, pdo_mysql (o pdo_pgsql), mbstring, fileinfo
- Base de datos: MySQL/MariaDB (o adaptarla a Postgres/SQLite según sea necesario)

Si prefieres variables de entorno, recomiendo añadir `vlucas/phpdotenv`; puedo añadir la integración si lo deseas.

<a id="instalacion-y-arranque-rapido"></a>
<a id="-instalacion-y-arranque-rapido"></a>
## Instalación y arranque (rápido)

En PowerShell:

```powershell
cd C:\Users\david\Downloads\SnackShop\SnackShop\www\Snackshop
composer install
php -S localhost:8000 -t public
```

Abrir http://localhost:8000

Para producción, apunta tu DocumentRoot a la carpeta `public/` y configura el servidor web (Nginx/Apache) correctamente.

<a id="configuracion"></a>
<a id="-configuracion"></a>
## Configuración

La configuración principal se carga desde `src/Config/AppConfig.php`. Valores habituales:

- DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS
- APP_ENV (development|production)
- APP_DEBUG (true|false)
- BASE_URL

No guardes credenciales en el repo. Preferible: `.env` fuera del control de versiones o variables de entorno del servidor.

Ejemplo mínimo (`.env.example`):

```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=snackshop
DB_USER=root
DB_PASS=
APP_ENV=development
APP_DEBUG=true
BASE_URL=http://localhost:8000
```

<a id="estructura-del-proyecto-resumen"></a>
<a id="-estructura-del-proyecto-resumen"></a>
## Estructura del proyecto (resumen)

- `public/` — Punto de entrada (DocumentRoot). Contiene `index.php`, assets y rutas públicas.
- `src/Config/` — Configuración de la app.
- `src/Controllers/` — Controladores que reciben requests.
- `src/Services/` — Lógica de negocio (impuestos, costos, procesos compuestos).
- `src/Repositories/` — Acceso a datos (PDO / SQL).
- `src/Models/` — Entidades/DTOs.
- `src/Views/` — Plantillas y partials.
- `src/Routes/` — Router y registro de rutas.
- `src/Database/` — Conexión a la BD.
- `src/Middleware/` — Autenticación, CSRF, roles.
- `data/` — Almacenamiento local si aplica (subidas, cache simple).

Consulta los README específicos en `src/` y `public/` para detalles por capa.

<a id="comandos-utiles"></a>
<a id="-comandos-utiles"></a>
## Comandos útiles

- Instalar dependencias: `composer install`
- Regenerar autoload: `composer dump-autoload`
- Servidor embebido: `php -S localhost:8000 -t public`
- Instalar PHPUnit (dev): `composer require --dev phpunit/phpunit ^9`

<a id="pruebas-y-calidad"></a>
<a id="-pruebas-y-calidad"></a>
## Pruebas y calidad

No hay tests en el repo actualmente. Recomendación mínima:

1. Añadir PHPUnit y crear carpeta `tests/`.
2. Usar SQLite en memoria para pruebas de repositorios.
3. Añadir PHPStan (nivel inicial) y ajustar reglas PSR-12.

Puedo generar un `phpunit.xml` y un test ejemplo si lo deseas.

<a id="siguientes-pasos-recomendados"></a>
<a id="-siguientes-pasos-recomendados"></a>
## Siguientes pasos recomendados

- Añadir `.env` y `vlucas/phpdotenv` para manejar configuraciones.
- Crear migraciones (Phinx o scripts SQL) y un seed inicial.
- Añadir tests básicos para `CostoService` y `ProductRepository`.
- Añadir README en carpetas faltantes (he añadido varios en este commit).

---

Si quieres que escriba un `CONTRIBUTING.md`, un `phpunit.xml` y un ejemplo de `docker-compose.yml`, dime y lo añado.
<a id="desde-la-carpeta-del-proyecto"></a>
<a id="-desde-la-carpeta-del-proyecto"></a>
# Desde la carpeta del proyecto
