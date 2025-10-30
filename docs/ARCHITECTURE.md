<a id="snackshop-arquitectura-del-sistema"></a>
<a id="-snackshop-arquitectura-del-sistema"></a>
# 🏗️ SnackShop - Arquitectura del Sistema
<!-- TOC -->
<a id="contenido"></a>
<a id="-contenido"></a>
## Contenido

- [🧭 Navegación](#-navegacion)
- [📋 Índice](#-indice)
- [🎯 Visión General](#-vision-general)
  - [Tecnologías Base](#tecnologias-base)
- [🏛️ Patrón de Arquitectura](#-patron-de-arquitectura)
  - [Arquitectura en Capas (Layered Architecture)](#arquitectura-en-capas-layered-architecture)
  - [Flujo Unidireccional de Dependencias](#flujo-unidireccional-de-dependencias)
- [🏗️ Capas del Sistema](#-capas-del-sistema)
  - [1. **Capa de Presentación** (`public/`, `src/Views/`)](#1-capa-de-presentacion-public-srcviews)
  - [2. **Capa de Aplicación** (`src/Controllers/`, `src/Routes/`, `src/Middleware/`)](#2-capa-de-aplicacion-srccontrollers-srcroutes-srcmiddleware)
  - [3. **Capa de Negocio** (`src/Services/`)](#3-capa-de-negocio-srcservices)
  - [4. **Capa de Datos** (`src/Repositories/`, `src/Models/`, `src/Database/`)](#4-capa-de-datos-srcrepositories-srcmodels-srcdatabase)
- [🔄 Flujo de Peticiones](#-flujo-de-peticiones)
  - [1. **Request HTTP**](#1-request-http)
  - [2. **Bootstrapping**](#2-bootstrapping)
  - [3. **Resolución de Ruta**](#3-resolucion-de-ruta)
  - [4. **Ejecución de Middlewares**](#4-ejecucion-de-middlewares)
  - [5. **Ejecución del Controller**](#5-ejecucion-del-controller)
  - [6. **Llamada a Services**](#6-llamada-a-services)
  - [7. **Acceso a Datos via Repository**](#7-acceso-a-datos-via-repository)
  - [8. **Response**](#8-response)
- [🧩 Componentes Principales](#-componentes-principales)
  - [Router (`src/Routes/Router.php`)](#router-srcroutesrouterphp)
  - [Service Container](#service-container)
  - [Error Handling](#error-handling)
  - [Session Management](#session-management)
- [🎨 Patrones de Diseño](#-patrones-de-diseno)
  - [1. **Repository Pattern**](#1-repository-pattern)
  - [2. **Service Layer Pattern**](#2-service-layer-pattern)
  - [3. **Front Controller Pattern**](#3-front-controller-pattern)
  - [4. **Middleware Pattern**](#4-middleware-pattern)
  - [5. **Factory Pattern** (Limitado)](#5-factory-pattern-limitado)
- [📦 Manejo de Dependencias](#-manejo-de-dependencias)
  - [Composer (PSR-4)](#composer-psr-4)
  - [Dependency Injection Manual](#dependency-injection-manual)
  - [Configuration Management](#configuration-management)
- [🔒 Seguridad y Middleware](#-seguridad-y-middleware)
  - [Autenticación](#autenticacion)
  - [Autorización por Roles](#autorizacion-por-roles)
  - [CSRF Protection](#csrf-protection)
- [📊 Diagramas de Arquitectura](#-diagramas-de-arquitectura)
  - [Diagrama de Componentes](#diagrama-de-componentes)
  - [Flujo de Datos (Request Lifecycle)](#flujo-de-datos-request-lifecycle)
- [🎯 Decisiones de Diseño](#-decisiones-de-diseno)
  - [¿Por qué sin Framework?](#por-que-sin-framework)
  - [¿Por qué Session-based Auth?](#por-que-session-based-auth)
  - [¿Por qué Repository Pattern sin ORM?](#por-que-repository-pattern-sin-orm)
  - [¿Por qué estructura de carpetas actual?](#por-que-estructura-de-carpetas-actual)
- [🔧 Puntos de Extensión](#-puntos-de-extension)
  - [1. **Añadir Nuevos Controllers**](#1-anadir-nuevos-controllers)
  - [2. **Crear Services Custom**](#2-crear-services-custom)
  - [3. **Implementar Middlewares Adicionales**](#3-implementar-middlewares-adicionales)
  - [4. **Añadir APIs JSON**](#4-anadir-apis-json)
  - [5. **Integrar Cache**](#5-integrar-cache)
- [🔗 Documentos Relacionados](#-documentos-relacionados)
- [📞 Soporte](#-soporte)
<!-- /TOC -->

**🏠 Ubicación:** `ARCHITECTURE.md`
**📅 Última actualización:** 28 de octubre, 2025
**🎯 Propósito:** Documentación completa de la arquitectura, patrones y diseño del sistema

---

<a id="navegacion"></a>
<a id="-navegacion"></a>
## 🧭 Navegación

**[📖 Índice General](docs/INDEX.md)** | **[🏠 README](README.md)** | **[🔌 API](API.md)** | **[🗄️ Database](DATABASE.md)**

---

<a id="indice"></a>
<a id="-indice"></a>
## 📋 Índice

- [Visión General](#visión-general)
- [Patrón de Arquitectura](#patrón-de-arquitectura)
- [Capas del Sistema](#capas-del-sistema)
- [Flujo de Peticiones](#flujo-de-peticiones)
- [Componentes Principales](#componentes-principales)
- [Patrones de Diseño](#patrones-de-diseño)
- [Manejo de Dependencias](#manejo-de-dependencias)
- [Seguridad y Middleware](#seguridad-y-middleware)
- [Diagramas de Arquitectura](#diagramas-de-arquitectura)
- [Decisiones de Diseño](#decisiones-de-diseño)
- [Puntos de Extensión](#puntos-de-extensión)

---

<a id="vision-general"></a>
<a id="-vision-general"></a>
## 🎯 Visión General

SnackShop implementa una **arquitectura en capas** basada en el patrón **MVC ampliado** con separación clara de responsabilidades. El sistema está diseñado para ser:

- **Mantenible:** Separación clara entre lógica de presentación, negocio y datos
- **Testeable:** Dependencias inyectables y abstracciones por capas
- **Escalable:** Patrones que permiten extensión sin modificar código existente
- **Seguro:** Middlewares transversales para autenticación, autorización y CSRF

<a id="tecnologias-base"></a>
<a id="-tecnologias-base"></a>
### Tecnologías Base

- **PHP 7.4+** con namespaces PSR-4
- **PDO** para acceso a base de datos
- **Session-based authentication** con roles
- **Sin framework externo** — arquitectura custom ligera

---

<a id="patron-de-arquitectura"></a>
<a id="-patron-de-arquitectura"></a>
## 🏛️ Patrón de Arquitectura

<a id="arquitectura-en-capas-layered-architecture"></a>
<a id="-arquitectura-en-capas-layered-architecture"></a>
### Arquitectura en Capas (Layered Architecture)

```
┌─────────────────────────────────────────────────────┐
│                   PRESENTATION                      │
│  🌐 Public/   🎨 Views/   🔗 Routes/               │
├─────────────────────────────────────────────────────┤
│                   APPLICATION                       │
│  🎮 Controllers/   🛡️ Middleware/                   │
├─────────────────────────────────────────────────────┤
│                    BUSINESS                         │
│  🏭 Services/   📊 Business Logic                   │
├─────────────────────────────────────────────────────┤
│                     DATA                            │
│  🗃️ Repositories/   📦 Models/   🗄️ Database/      │
└─────────────────────────────────────────────────────┘
```

<a id="flujo-unidireccional-de-dependencias"></a>
<a id="-flujo-unidireccional-de-dependencias"></a>
### Flujo Unidireccional de Dependencias

```
Controllers ──depends on──> Services ──depends on──> Repositories
     │                          │                          │
     │                          │                          │
     └─────> Views              └─> Business Logic        └─> Models + DB
```

---

<a id="capas-del-sistema"></a>
<a id="-capas-del-sistema"></a>
## 🏗️ Capas del Sistema

<a id="1-capa-de-presentacion-public-srcviews"></a>
<a id="-1-capa-de-presentacion-public-srcviews"></a>
### 1. **Capa de Presentación** (`public/`, `src/Views/`)

**Responsabilidad:** Interfaz de usuario y puntos de entrada HTTP

```bash
public/
├── index.php ..................... Entry point principal
├── pretty-urls.php ............... URLs amigables (opcional)
├── css/ .......................... Estilos del frontend
├── js/ ........................... JavaScript del frontend
└── assets/ ....................... Imágenes y recursos estáticos

src/Views/
├── auth/ ......................... Formularios de login/logout
├── dashboard/ .................... Panel de administración
├── products/ ..................... CRUD de productos
├── sales/ ........................ Interfaz de ventas
├── users/ ........................ Gestión de usuarios
└── partials/ ..................... Componentes reutilizables
    ├── sidebar.php
    └── header.php
```

<a id="2-capa-de-aplicacion-srccontrollers-srcroutes-srcmiddleware"></a>
<a id="-2-capa-de-aplicacion-srccontrollers-srcroutes-srcmiddleware"></a>
### 2. **Capa de Aplicación** (`src/Controllers/`, `src/Routes/`, `src/Middleware/`)

**Responsabilidad:** Orquestación de peticiones HTTP y control de flujo

<a id="controllers-"></a>
<a id="-controllers-"></a>
#### Controllers (🎮)

```php
// Ejemplo: ProductController
class ProductController {
    public function index() {
        // 1. Validar autenticación (middleware)
        // 2. Obtener datos via Services
        // 3. Preparar datos para la vista
        // 4. Renderizar respuesta
    }
}
```

**Controllers existentes:**
- `AuthController` — autenticación y sesiones
- `ProductController` — CRUD productos
- `VariantController` — variantes de productos
- `SalesController` — gestión de ventas
- `DashboardController` — panel administrativo
- `HistorialController` — historial de transacciones
- `AgregarCajeroController` — gestión de usuarios cajero
- `ApiController` + `Api/CostoController` — endpoints JSON

<a id="routes-"></a>
<a id="-routes-"></a>
#### Routes (🔗)

```php
// src/Routes/routes.php - Registro centralizado
$router->get('/productos', [ProductController::class, 'index'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin'])
]);
```

<a id="middleware-"></a>
<a id="-middleware-"></a>
#### Middleware (🛡️)

- **`AuthMiddleware`** — verificación de sesión activa
- **`CsrfMiddleware`** — protección contra CSRF en formularios
- **`RoleMiddleware`** — autorización basada en roles (admin/cajero)

<a id="3-capa-de-negocio-srcservices"></a>
<a id="-3-capa-de-negocio-srcservices"></a>
### 3. **Capa de Negocio** (`src/Services/`)

**Responsabilidad:** Lógica de negocio y reglas de dominio

```php
// Ejemplo: Cálculo complejo de costos
class CostoService {
    public function costoProductoNeto(int $productId): float {
        // 1. Obtener ingredientes via Repository
        // 2. Aplicar algoritmos de cálculo
        // 3. Considerar reglas de negocio
        // 4. Retornar resultado
    }
}
```

**Services existentes:**
- **`ProductService`** — lógica de productos y catálogo
- **`CostoService`** — cálculo de costos de producción
- **`ImpuestosService`** — aplicación de impuestos y descuentos
- **`SaleService`** — procesamiento de ventas
- **`UserService`** — gestión de usuarios
- **`CategoryService`** — organización de categorías
- **`PaymentMethodService`** — métodos de pago
- **`VariantService`** — gestión de variantes
- **`ImageProcessingService`** — procesamiento de imágenes

<a id="4-capa-de-datos-srcrepositories-srcmodels-srcdatabase"></a>
<a id="-4-capa-de-datos-srcrepositories-srcmodels-srcdatabase"></a>
### 4. **Capa de Datos** (`src/Repositories/`, `src/Models/`, `src/Database/`)

**Responsabilidad:** Persistencia y acceso a datos

<a id="repositories-"></a>
<a id="-repositories-"></a>
#### Repositories (🗃️)

```php
interface ProductRepositoryInterface {
    public function find(int $id): ?Product;
    public function findActiveProducts(): array;
    public function create(array $data): Product;
}

class ProductRepository implements ProductRepositoryInterface {
    // Implementación con PDO prepared statements
}
```

<a id="models-"></a>
<a id="-models-"></a>
#### Models (📦)

```php
class Product {
    public int $id;
    public string $name;
    public ?string $description;
    public bool $active;
    public array $variants = [];
    // POPOs simples sin lógica de negocio
}
```

<a id="database-"></a>
<a id="-database-"></a>
#### Database (🗄️)

- **`Connection`** — singleton PDO con configuración centralizada
- Soporte MySQL/SQLite via configuración
- Manejo de transacciones a nivel de Service

---

<a id="flujo-de-peticiones"></a>
<a id="-flujo-de-peticiones"></a>
## 🔄 Flujo de Peticiones

<a id="1-request-http"></a>
<a id="-1-request-http"></a>
### 1. **Request HTTP**

```
Usuario ──HTTP──> public/index.php
```

<a id="2-bootstrapping"></a>
<a id="-2-bootstrapping"></a>
### 2. **Bootstrapping**

```php
// public/index.php
require_once '../vendor/autoload.php';  // Composer PSR-4
session_start();                        // Gestión de sesiones
require_once '../src/Routes/routes.php'; // Registro de rutas
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
```

<a id="3-resolucion-de-ruta"></a>
<a id="-3-resolucion-de-ruta"></a>
### 3. **Resolución de Ruta**

```
Router ──matching──> Route + Middlewares + Controller
```

<a id="4-ejecucion-de-middlewares"></a>
<a id="-4-ejecucion-de-middlewares"></a>
### 4. **Ejecución de Middlewares**

```
AuthMiddleware ──> RoleMiddleware ──> CsrfMiddleware ──> Controller
```

<a id="5-ejecucion-del-controller"></a>
<a id="-5-ejecucion-del-controller"></a>
### 5. **Ejecución del Controller**

```php
public function index() {
    // Validaciones de entrada
    $service = new ProductService();
    $data = $service->getAllWithVariants();

    // Preparar datos para vista
    require_once '../src/Views/products/index.php';
}
```

<a id="6-llamada-a-services"></a>
<a id="-6-llamada-a-services"></a>
### 6. **Llamada a Services**

```php
class ProductService {
    public function getAllWithVariants(): array {
        $products = $this->repository->findActiveProducts();
        // Lógica de negocio
        return $products;
    }
}
```

<a id="7-acceso-a-datos-via-repository"></a>
<a id="-7-acceso-a-datos-via-repository"></a>
### 7. **Acceso a Datos via Repository**

```php
class ProductRepository {
    public function findActiveProducts(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE active = 1");
        $stmt->execute();
        return array_map([Product::class, 'fromArray'], $stmt->fetchAll());
    }
}
```

<a id="8-response"></a>
<a id="-8-response"></a>
### 8. **Response**

```
View Template ──HTML──> Browser
  o
JSON Response ──API──> Client
```

---

<a id="componentes-principales"></a>
<a id="-componentes-principales"></a>
## 🧩 Componentes Principales

<a id="router-srcroutesrouterphp"></a>
<a id="-router-srcroutesrouterphp"></a>
### Router (`src/Routes/Router.php`)

- **Propósito:** Matching de URLs a controllers + middlewares
- **Características:**
  - Soporte para parámetros en rutas (`/products/{id}`)
  - Registro de middlewares por ruta
  - Métodos HTTP (GET, POST, PUT, DELETE)

<a id="service-container"></a>
<a id="-service-container"></a>
### Service Container

- **Estado actual:** Dependencias manuales en constructores
- **Patrón:** Dependency Injection manual
- **Futuro:** Considerar PSR-11 Container para mayor flexibilidad

<a id="error-handling"></a>
<a id="-error-handling"></a>
### Error Handling

- **Desarrollo:** Errores visibles con detalles
- **Producción:** Páginas de error custom (`src/Views/errors/`)
- **Logs:** Manejo básico de excepciones

<a id="session-management"></a>
<a id="-session-management"></a>
### Session Management

- **Autenticación:** Session-based con `$_SESSION`
- **Datos almacenados:** `usuario_id`, `usuario`, `rol`
- **Seguridad:** Regeneración de session ID en login

---

<a id="patrones-de-diseno"></a>
<a id="-patrones-de-diseno"></a>
## 🎨 Patrones de Diseño

<a id="1-repository-pattern"></a>
<a id="-1-repository-pattern"></a>
### 1. **Repository Pattern**

```php
interface ProductRepositoryInterface {
    public function find(int $id): ?Product;
}

class ProductRepository implements ProductRepositoryInterface {
    // Encapsula acceso a datos
}
```
**Beneficio:** Abstrae la persistencia del negocio

<a id="2-service-layer-pattern"></a>
<a id="-2-service-layer-pattern"></a>
### 2. **Service Layer Pattern**

```php
class SaleService {
    public function processSale(array $items, int $paymentMethod): Sale {
        // Coordinación de múltiples repositories
        // Aplicación de reglas de negocio
        // Manejo de transacciones
    }
}
```
**Beneficio:** Centraliza lógica de negocio compleja

<a id="3-front-controller-pattern"></a>
<a id="-3-front-controller-pattern"></a>
### 3. **Front Controller Pattern**

```php
// public/index.php actúa como front controller
$router->dispatch($uri, $method);
```
**Beneficio:** Punto único de entrada para todas las requests

<a id="4-middleware-pattern"></a>
<a id="-4-middleware-pattern"></a>
### 4. **Middleware Pattern**

```php
$router->get('/admin', [Controller::class, 'method'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin'])
]);
```
**Beneficio:** Concerns transversales (auth, logging, etc.)

<a id="5-factory-pattern-limitado"></a>
<a id="-5-factory-pattern-limitado"></a>
### 5. **Factory Pattern** (Limitado)

```php
// src/Database/Connection.php
class Connection {
    public static function get(): PDO {
        // Factory para conexiones PDO
    }
}
```

---

<a id="manejo-de-dependencias"></a>
<a id="-manejo-de-dependencias"></a>
## 📦 Manejo de Dependencias

<a id="composer-psr-4"></a>
<a id="-composer-psr-4"></a>
### Composer (PSR-4)

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

<a id="dependency-injection-manual"></a>
<a id="-dependency-injection-manual"></a>
### Dependency Injection Manual

```php
class ProductController {
    private ProductService $service;

    public function __construct(?ProductService $service = null) {
        $this->service = $service ?? new ProductService();
    }
}
```

<a id="configuration-management"></a>
<a id="-configuration-management"></a>
### Configuration Management

```php
// src/Config/AppConfig.php
class AppConfig {
    public static function database(): array {
        return [
            'host' => getenv('SNACKSHOP_DB_HOST') ?: '127.0.0.1',
            'name' => getenv('SNACKSHOP_DB_NAME') ?: 'snackshop',
            // Variables de entorno con fallbacks
        ];
    }
}
```

---

<a id="seguridad-y-middleware"></a>
<a id="-seguridad-y-middleware"></a>
## 🔒 Seguridad y Middleware

<a id="autenticacion"></a>
<a id="-autenticacion"></a>
### Autenticación

```php
class AuthMiddleware {
    public function handle($request, $next) {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /login');
            exit;
        }
        return $next($request);
    }
}
```

<a id="autorizacion-por-roles"></a>
<a id="-autorizacion-por-roles"></a>
### Autorización por Roles

```php
class RoleMiddleware {
    private array $allowedRoles;

    public function handle($request, $next) {
        if (!in_array($_SESSION['rol'], $this->allowedRoles)) {
            header('Location: /acceso-denegado');
            exit;
        }
        return $next($request);
    }
}
```

<a id="csrf-protection"></a>
<a id="-csrf-protection"></a>
### CSRF Protection

```php
class CsrfMiddleware {
    public function handle($request, $next) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar token CSRF
        }
        return $next($request);
    }
}
```

---

<a id="diagramas-de-arquitectura"></a>
<a id="-diagramas-de-arquitectura"></a>
## 📊 Diagramas de Arquitectura

<a id="diagrama-de-componentes"></a>
<a id="-diagrama-de-componentes"></a>
### Diagrama de Componentes

```
                    ┌─────────────────┐
                    │   PUBLIC WEB    │
                    │  (index.php)    │
                    └─────────┬───────┘
                              │
                    ┌─────────▼───────┐
                    │     ROUTER      │
                    │  (routes.php)   │
                    └─────────┬───────┘
                              │
              ┌───────────────┼───────────────┐
              │               │               │
    ┌─────────▼───────┐ ┌─────▼─────┐ ┌───────▼───────┐
    │  MIDDLEWARES    │ │CONTROLLERS│ │     VIEWS     │
    │ Auth│CSRF│Role  │ │Product│API│ │ Templates│404│
    └─────────────────┘ └─────┬─────┘ └───────────────┘
                              │
                    ┌─────────▼───────┐
                    │    SERVICES     │
                    │Cost│Sale│Product│
                    └─────────┬───────┘
                              │
                    ┌─────────▼───────┐
                    │  REPOSITORIES   │
                    │Product│User│Sale│
                    └─────────┬───────┘
                              │
              ┌───────────────┼───────────────┐
              │               │               │
    ┌─────────▼───────┐ ┌─────▼─────┐ ┌───────▼───────┐
    │     MODELS      │ │ DATABASE  │ │  EXTERNAL     │
    │Product│User│Sale│ │Connection │ │    APIs       │
    └─────────────────┘ └───────────┘ └───────────────┘
```

<a id="flujo-de-datos-request-lifecycle"></a>
<a id="-flujo-de-datos-request-lifecycle"></a>
### Flujo de Datos (Request Lifecycle)

```
HTTP Request ──▶ public/index.php
                        │
                        ▼
                   Router::dispatch()
                        │
                        ▼
                ┌─────────────────┐
                │   MIDDLEWARES   │◀─── Execute in order
                │ 1. Auth         │
                │ 2. Role         │
                │ 3. CSRF         │
                └─────┬───────────┘
                      │
                      ▼
              ┌─────────────────┐
              │   CONTROLLER    │
              │ validate input  │
              │ call service    │
              │ prepare data    │
              └─────┬───────────┘
                    │
                    ▼
            ┌─────────────────┐
            │    SERVICE      │
            │ business logic  │
            │ call repository │
            │ transform data  │
            └─────┬───────────┘
                  │
                  ▼
          ┌─────────────────┐
          │   REPOSITORY    │
          │ SQL queries     │
          │ PDO operations  │
          │ return Models   │
          └─────┬───────────┘
                │
                ▼
        ┌─────────────────┐
        │     MODEL       │
        │ data transfer   │
        │ simple POPOs    │
        └─────┬───────────┘
              │
              ▼
      ┌─────────────────┐
      │      VIEW       │
      │ render HTML     │
      │ or JSON API     │
      └─────┬───────────┘
            │
            ▼
     HTTP Response ──▶ Browser/Client
```

---

<a id="decisiones-de-diseno"></a>
<a id="-decisiones-de-diseno"></a>
## 🎯 Decisiones de Diseño

<a id="por-que-sin-framework"></a>
<a id="-por-que-sin-framework"></a>
### ¿Por qué sin Framework?

- **Pros:** Control total, mínimas dependencias, fácil debugging
- **Cons:** Más código boilerplate, menos features out-of-the-box
- **Decisión:** Adecuado para proyectos pequeños-medianos con requerimientos específicos

<a id="por-que-session-based-auth"></a>
<a id="-por-que-session-based-auth"></a>
### ¿Por qué Session-based Auth?

- **Alternativas:** JWT, OAuth2
- **Decisión:** Simplicidad para aplicación web tradicional
- **Trade-off:** Menos escalable que tokens, pero más simple de implementar

<a id="por-que-repository-pattern-sin-orm"></a>
<a id="-por-que-repository-pattern-sin-orm"></a>
### ¿Por qué Repository Pattern sin ORM?

- **Alternativas:** Active Record, Eloquent, Doctrine
- **Decisión:** Control directo sobre SQL, performance predecible
- **Trade-off:** Más código manual, pero mayor flexibilidad

<a id="por-que-estructura-de-carpetas-actual"></a>
<a id="-por-que-estructura-de-carpetas-actual"></a>
### ¿Por qué estructura de carpetas actual?

```
src/
├── Config/      ← Configuración centralizada
├── Controllers/ ← Thin controllers, orquestación
├── Services/    ← Fat services, lógica de negocio
├── Repositories/← Data access layer
├── Models/      ← Simple DTOs
├── Views/       ← Presentation layer
├── Routes/      ← Routing configuration
├── Middleware/  ← Cross-cutting concerns
└── Database/    ← DB connection and utilities
```
**Rationale:** Separación clara por responsabilidades, fácil navegación

---

<a id="puntos-de-extension"></a>
<a id="-puntos-de-extension"></a>
## 🔧 Puntos de Extensión

<a id="1-anadir-nuevos-controllers"></a>
<a id="-1-anadir-nuevos-controllers"></a>
### 1. **Añadir Nuevos Controllers**

```php
namespace App\Controllers;

class NewController {
    public function index() {
        // Seguir patrón existente
    }
}

// Registrar en routes.php
$router->get('/new-feature', [NewController::class, 'index'], [
    AuthMiddleware::class
]);
```

<a id="2-crear-services-custom"></a>
<a id="-2-crear-services-custom"></a>
### 2. **Crear Services Custom**

```php
namespace App\Services;

class CustomService {
    private CustomRepository $repository;

    public function __construct(?CustomRepository $repo = null) {
        $this->repository = $repo ?? new CustomRepository();
    }
}
```

<a id="3-implementar-middlewares-adicionales"></a>
<a id="-3-implementar-middlewares-adicionales"></a>
### 3. **Implementar Middlewares Adicionales**

```php
namespace App\Middleware;

class LoggingMiddleware {
    public function handle($request, $next) {
        // Log request
        $response = $next($request);
        // Log response
        return $response;
    }
}
```

<a id="4-anadir-apis-json"></a>
<a id="-4-anadir-apis-json"></a>
### 4. **Añadir APIs JSON**

```php
// En Controller
public function apiMethod() {
    header('Content-Type: application/json');
    $data = $this->service->getData();
    echo json_encode($data);
}
```

<a id="5-integrar-cache"></a>
<a id="-5-integrar-cache"></a>
### 5. **Integrar Cache**

```php
// Ejemplo de extensión con cache
class ProductService {
    public function getAllWithVariants(): array {
        $cacheKey = 'products_with_variants';

        if ($cached = Cache::get($cacheKey)) {
            return $cached;
        }

        $result = $this->repository->findActiveProducts();
        Cache::set($cacheKey, $result, 300); // 5 min

        return $result;
    }
}
```

---

<a id="documentos-relacionados"></a>
<a id="-documentos-relacionados"></a>
## 🔗 Documentos Relacionados

- **[📖 Índice General](docs/INDEX.md)** — Navegación completa del manual
- **[🔌 API Documentation](API.md)** — Endpoints y ejemplos de uso
- **[🗄️ Database Schema](DATABASE.md)** — Esquema y relaciones
- **[🚀 Deployment Guide](DEPLOYMENT.md)** — Despliegue en producción
- **[🔧 Development Setup](DEVELOPMENT.md)** — Configuración para desarrolladores

---

<a id="soporte"></a>
<a id="-soporte"></a>
## 📞 Soporte

**¿Tienes preguntas sobre la arquitectura?**
- Crea un issue en [GitHub](https://github.com/Equinoxe-Grammer/SnackShack/issues) con la etiqueta `architecture`
- Consulta la documentación específica de cada componente en `docs/`

---

**[📖 Índice](docs/INDEX.md)** | **[🔌 Ver API](API.md)** | **[🗄️ Ver Database](DATABASE.md)** | **[🚀 Ver Deployment](DEPLOYMENT.md)**
