# SnackShop

Proyecto web PHP ligero para la gestión de una tienda de snacks (productos, ventas, usuarios, variantes e inventario). Este README explica cómo instalar, configurar, ejecutar y contribuir al proyecto.

## Tabla de contenidos

- [Resumen](#resumen)
- [Requisitos](#requisitos)
- [Instalación rápida](#instalaci%C3%B3n-r%C3%A1pida)
- [Configuración](#configuraci%C3%B3n)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Arrancar la aplicación](#arrancar-la-aplicaci%C3%B3n)
- [Arquitectura y responsabilidades](#arquitectura-y-responsabilidades)
- [Modelos, Repositorios y Servicios](#modelos-repositorios-y-servicios)
- [Middlewares y seguridad](#middlewares-y-seguridad)
- [Vistas y assets](#vistas-y-assets)
- [Pruebas y calidad](#pruebas-y-calidad)
- [Despliegue](#despliegue)
- [Solución de problemas comunes](#soluci%C3%B3n-de-problemas-comunes)
- [Contratos y casos límite](#contratos-y-casos-l%C3%ADmite)
- [Contribuir](#contribuir)
- [Licencia](#licencia)

## Resumen

SnackShop es una aplicación backend + vistas PHP que implementa: autenticación básica, gestión de productos y variantes, gestión de ventas y ventas por cajero, control de stock a través de servicios y repositorios, y una estructura organizada por controllers, services, repositories y views.

El proyecto está pensado para ser pequeño y fácilmente desplegable en servidores LAMP/LEMP o ejecutado con el servidor embebido de PHP para desarrollo.

## Requisitos

- PHP 7.4 o superior (recomendado PHP 8.0+). (Suposición basada en presencia de composer y estructura; ajusta según tu entorno.)
- Composer instalado (https://getcomposer.org)
- Extensiones PHP típicas: PDO (para la DB que uses), mbstring, openssl, fileinfo (para subida de imágenes). Ajusta según errores.
- Un servidor de base de datos compatible (MySQL/MariaDB/Postgres — la conexión está en `src/Database/Connection.php` y la configuración en `src/Config/AppConfig.php`).

## Instalación rápida

En PowerShell (Windows) o terminal:

```powershell
cd c:\Users\david\Downloads\SnackShop\SnackShop\www\Snackshop
composer install
```

Notas:
- Si tu versión de PHP no está en PATH, usa la ruta completa hacia el ejecutable php.
- Si composer falla por dependencias, revisa `composer.json`.

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

## Arrancar la aplicación

Modo desarrollo (servidor embebido PHP):

```powershell
# Desde la carpeta project root que contiene public/
php -S localhost:8000 -t public
# Abre http://localhost:8000 en tu navegador
```

Usando Apache o Nginx:
- Apunta el DocumentRoot a `.../public/`.
- Habilita mod_rewrite (si `pretty-urls.php`/htaccess lo requiere).

## Arquitectura y responsabilidades

- Controllers: reciben Request, usan Services y Repositories, devuelven Views o JSON.
- Repositories: encapsulan consultas SQL/ORM y operaciones CRUD.
- Services: lógica de negocio (validaciones, cálculos, operaciones compuestas).
- Models: simples DTOs/POPOs que representan filas de tablas.
- Middlewares: control de acceso y protección CSRF.

Controladores observables en `src/Controllers/` (ejemplos): `ProductController`, `SalesController`, `UserController`, `AuthController`, `DashboardController`, `ComprasController`, `VariantController`, etc. Revisa esos archivos para ver acciones disponibles (index, create, update, delete, show, etc.).

## Modelos, Repositorios y Servicios

- `src/Models/` contiene modelos como `Product.php`, `User.php`, `Sale.php`, `Variant.php`.
- `src/Repositories/` implementa repositorios para cada agregado (ProductRepository, SaleRepository, UserRepository, VariantRepository, etc.).
- `src/Services/` contiene servicios transversales: `ImageProcessingService` (procesamiento de imágenes), `ImpuestosService` (cálculo de impuestos), `CostoService` (costeo de productos) y otros.

Si necesitas crear nuevas entidades siguen el patrón:
- Crear Model
- Añadir Repository/Interface
- Crear Service si la lógica es compleja
- Añadir Controller y rutas
- Crear vistas si es UI

## Middlewares y seguridad

Middlewares principales:
- `AuthMiddleware.php` — protege rutas que requieren autenticación.
- `CsrfMiddleware.php` — protege formularios contra CSRF (asegúrate de que los tokens se incluyan y verifiquen).
- `RoleMiddleware.php` — control de permisos por rol.

Recomendaciones de seguridad:
- No exponer credenciales en el repo; usa variables de entorno o un archivo de configuración fuera del control de versiones.
- Validar y sanitizar todas las entradas; usar prepared statements en la capa de repositorios (PDO con bound parameters).
- Limitar tamaños y tipos de archivos en subidas (ImageProcessingService puede ayudar).

## Vistas y assets

- Plantillas en `src/Views/` separadas por área (`auth`, `products`, `sales`, etc.).
- CSS y JS en `public/assets/` o `public/css`, `public/js`.
- Incluye partials reutilizables en `src/Views/partials/` (por ejemplo `sidebar.php`).

## Pruebas y calidad

Actualmente el repositorio no incluye tests automáticos. Recomendación mínima para empezar con PHPUnit:

1. Añadir PHPUnit como dependencia de desarrollo:

```powershell
composer require --dev phpunit/phpunit ^9
```

2. Crear un `phpunit.xml` básico en la raíz y tests en `tests/`.
3. Ejecutar tests:

```powershell
# En Windows PowerShell
vendor\bin\phpunit.bat --colors=always
# O si vendor\bin\phpunit existe como ejecutable
vendor\bin\phpunit --colors=always
```

Pruebas sugeridas:
- Repositorios: pruebas de integración con una BD SQLite en memoria.
- Services: pruebas unitarias de cálculo de impuestos y costos.
- Controllers: pruebas de integración simulando requests (o tests funcionales).

# SnackShop

![PHP](https://img.shields.io/badge/PHP-7.4%2B-8892BF) ![Composer](https://img.shields.io/badge/Composer-OK-0F62FE)

Aplicación web ligera en PHP para gestionar una tienda de snacks: productos, variantes, ventas, usuarios y control básico de inventario. Este README está escrito como guía técnica completa para desarrolladores y operadores.

## Tabla de contenidos

- [Resumen rápido](#resumen-r%C3%A1pido)
- [Estado y objetivos](#estado-y-objetivos)
- [Requisitos](#requisitos)
- [Instalación y arranque rápido](#instalaci%C3%B3n-y-arranque-r%C3%A1pido)
- [Configuración (variables y archivos)](#configuraci%C3%B3n-variables-y-archivos)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Flujo de petici%C3%B3n: controllers-›-services-›-repositories](#flujo-de-petici%C3%B3n-controllers--services--repositories)
- [Vistas, activos y rutas públicas](#vistas-activos-y-rutas-p%C3%BAblicas)
- [Comandos útiles (PowerShell)](#comandos-%C3%BAtiles-powershell)
- [Pruebas y calidad de código](#pruebas-y-calidad-de-codigo)
- [Despliegue y contenedores (opcional)](#despliegue-y-contenedores-opcional)
- [Seguridad y buenas prácticas](#seguridad-y-buenas-pr%C3%A1cticas)
- [Solución de problemas frecuentes](#soluci%C3%B3n-de-problemas-frecuentes)
- [Contrato API mínimo y casos de borde](#contrato-api-m%C3%ADnimo-y-casos-de-borde)
- [Contribuir](#contribuir)
- [Siguientes pasos recomendados](#siguientes-pasos-recomendados)
- [Licencia](#licencia)

---

## Resumen rápido

SnackShop combina un backend PHP con vistas server-rendered para gestionar productos, ventas y usuarios. El código está organizado siguiendo capas claras: Controllers, Services, Repositories y Views.

## Estado y objetivos

- Estado actual: prototipo funcional con vistas y rutas principales en `src/Controllers` y `public/` como punto de entrada.
- Objetivo: servir como base ligera para tiendas de tamaño pequeño/mediano; fácil de extender, testear y desplegar.

## Requisitos

- PHP 7.4+ (recomendado PHP 8.0+)
- Composer
- Extensiones PHP: pdo, pdo_mysql (o pdo_pgsql), mbstring, fileinfo
- Base de datos: MySQL/MariaDB (u otra compatible; ajustar `src/Database/Connection.php`)

Si quieres soporte para `.env`, se recomienda `vlucas/phpdotenv` (puedo añadirlo si quieres).

## Instalación y arranque rápido

1. Instalar dependencias con Composer:

```powershell
cd C:\Users\david\Downloads\SnackShop\SnackShop\www\Snackshop
composer install
```

2. Iniciar servidor de desarrollo (DocumentRoot = `public/`):

```powershell
# Desde la carpeta del proyecto
php -S localhost:8000 -t public
# Abrir http://localhost:8000
```

3. Si usas Apache/Nginx: apunta el DocumentRoot a `.../public/` y habilita reescritura si usas URLs amigables.

## Configuración (variables y archivos)

Actualmente la configuración principal está en `src/Config/AppConfig.php`. Variables comunes:

- DB_HOST
- DB_PORT
- DB_NAME
- DB_USER
- DB_PASS
- APP_ENV
- APP_DEBUG
- BASE_URL

Ejemplo de `.env.example` (opcional, puedo generarlo):

```text
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=snackshop
DB_USER=root
DB_PASS=
APP_ENV=development
APP_DEBUG=true
BASE_URL=http://localhost:8000
```

Si prefieres, puedo añadir cargado de variables con `vlucas/phpdotenv`.

## Estructura del proyecto (resumen)

Raíz del proyecto (rutas relevantes):

```
public/
  index.php
  pretty-urls.php
  assets/
src/
  Config/
    AppConfig.php
  Controllers/
  Database/
    Connection.php
  Middleware/
  Models/
  Repositories/
  Routes/
  Services/
  Views/
vendor/
data/
```

- `public/` -> punto de entrada web.
- `src/Controllers` -> lógica HTTP.
- `src/Services` -> reglas de negocio (impuestos, costos, procesamiento de imagenes).
- `src/Repositories` -> acceso a datos.

## Flujo de petición: controllers » services » repositories

- Controller: valida request, maneja permisos, orquesta la llamada.
- Service: aplica lógica de negocio y reglas (p. ej. cálculo de impuestos, manejo de stock, procesamiento de imágenes).
- Repository: realiza queries/operaciones con la base de datos (usar prepared statements / PDO siempre que sea posible).

Este patrón facilita pruebas unitarias y separación de responsabilidades.

## Vistas, activos y rutas públicas

- Vistas PHP en `src/Views/`.
- Partials: `src/Views/partials/` (ej. `sidebar.php`).
- CSS/JS en `public/css` y `public/js`.

Rutas principales y controllers (ejemplos):
- `ProductController` -> CRUD productos
- `SalesController` -> operaciones de venta
- `AuthController` -> login/logout

Revisa `src/Routes/routes.php` o `src/Routes/Router.php` para ver definición exacta de rutas.

## Comandos útiles (PowerShell)

- Instalar dependencias:

```powershell
composer install
```

- Regenerar autoload:

```powershell
composer dump-autoload
```

- Ejecutar servidor de desarrollo:

```powershell
php -S localhost:8000 -t public
```

- Instalar PHPUnit (dev):

```powershell
composer require --dev phpunit/phpunit ^9
```

## Pruebas y calidad de código

Recomendaciones:

- Añadir PHPUnit y tests en `tests/`.
- Usar SQLite en memoria para pruebas de repositorios.
- Integrar PHPStan (análisis estático) y PHPCS/PSR12 para estilo.

Ejemplo rápido para correr tests (tras instalar PHPUnit):

```powershell
# En Windows PowerShell
vendor\bin\phpunit.bat --colors=always
```

Si quieres, puedo crear un `phpunit.xml` mínimo y un test de ejemplo para `CostoService`.

## Despliegue y contenedores (opcional)

Ejemplo minimal de `docker-compose.yml` (idea, puedo añadir archivos reales si quieres):

```yaml
version: '3.8'
services:
  web:
    image: php:8.1-apache
    volumes:
      - ./:/var/www/html
    ports:
      - 8000:80
    depends_on:
      - db
  db:
    image: mysql:8
    environment:
      MYSQL_ROOT_PASSWORD: example
      MYSQL_DATABASE: snackshop
    volumes:
      - db-data:/var/lib/mysql
volumes:
  db-data:
```

Para producción: `composer install --no-dev --optimize-autoloader` y configurar correctamente variables de entorno.

## Seguridad y buenas prácticas

- No subir credenciales al repositorio. Usa `.env` fuera del control de versiones.
- Escapar/sanitizar entradas y usar prepared statements.
- Limitar tipos y tamaño de archivos en uploads.
- Implementar CSRF tokens en formularios (ya hay `CsrfMiddleware.php`).
- Revisa `RoleMiddleware.php` para control de permisos por rutas.

## Solución de problemas frecuentes

- Clase no encontrada: `composer dump-autoload`.
- Error DB: revisar `src/Config/AppConfig.php` y `src/Database/Connection.php`.
- Rutas: revisar `public/pretty-urls.php` y la configuración del servidor web.

## Contrato API mínimo y casos de borde

Contrato (inputs/outputs):

- Input: peticiones HTTP (form-data o JSON).
- Output: HTML (vistas) o JSON (endpoints API).
- Errores: páginas en `src/Views/errors/` con códigos HTTP apropiados.

Casos de borde:
1. Formularios con campos vacíos — validar en servidor.
2. Subidas grandes — imponer límites en `php.ini` y en la lógica de subida.
3. Concurrencia en stock — proteger con transacciones o bloqueo optimista si corresponde.
4. Intentos de acceso sin permisos — asegurar middleware de roles.

## Contribuir

- Fork + branch con nombre claro (`feature/mi-cambio`).
- Añadir tests para funcionalidades nuevas.
- Crear PR con descripción y casos de prueba.

Si quieres, puedo crear una plantilla `CONTRIBUTING.md`.

## Siguientes pasos recomendados

Opcionales que puedo implementar ahora si me lo pides:

1. `.env.example` y soporte via `vlucas/phpdotenv`.
2. `phpunit.xml` + test de ejemplo.
3. Script SQL inicial para crear tablas basadas en los modelos.
4. Dockerfile y `docker-compose.yml` funcional.

Indica qué prefieres y lo hago.

## Licencia

Revisa `LICENSE.txt` en la raíz del repositorio.

---

Contacto / soporte

Si quieres que haga alguna de las tareas sugeridas (`.env.example`, tests, docker), dime cuál y la implemento.
