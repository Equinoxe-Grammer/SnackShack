# 🏗️ SnackShop - Arquitectura del Sistema

**🏠 Ubicación:** `ARCHITECTURE.md`  
**📅 Última actualización:** 28 de octubre, 2025  
**🎯 Propósito:** Documentación completa de la arquitectura, patrones y diseño del sistema

---

## 🧭 Navegación

**[📖 Índice General](docs/INDEX.md)** | **[🏠 README](README.md)** | **[🔌 API](API.md)** | **[🗄️ Database](DATABASE.md)**

---

## 📋 Índice

- [Visión General](#-visión-general)
- [Patrón de Arquitectura](#-patrón-de-arquitectura)
- [Capas del Sistema](#-capas-del-sistema)
- [Flujo de Peticiones](#-flujo-de-peticiones)
- [Componentes Principales](#-componentes-principales)
- [Patrones de Diseño](#-patrones-de-diseño)
- [Manejo de Dependencias](#-manejo-de-dependencias)
- [Seguridad y Middleware](#-seguridad-y-middleware)
- [Diagramas de Arquitectura](#-diagramas-de-arquitectura)
- [Decisiones de Diseño](#-decisiones-de-diseño)
- [Puntos de Extensión](#-puntos-de-extensión)

---

## 🎯 Visión General

SnackShop implementa una **arquitectura en capas** basada en el patrón **MVC ampliado** con separación clara de responsabilidades. El sistema está diseñado para ser:

- **Mantenible:** Separación clara entre lógica de presentación, negocio y datos
- **Testeable:** Dependencias inyectables y abstracciones por capas
- **Escalable:** Patrones que permiten extensión sin modificar código existente
- **Seguro:** Middlewares transversales para autenticación, autorización y CSRF

### Tecnologías Base
- **PHP 7.4+** con namespaces PSR-4
- **PDO** para acceso a base de datos
- **Session-based authentication** con roles
- **Sin framework externo** — arquitectura custom ligera

---

## 🏛️ Patrón de Arquitectura

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

### Flujo Unidireccional de Dependencias

```
Controllers ──depends on──> Services ──depends on──> Repositories
     │                          │                          │
     │                          │                          │
     └─────> Views              └─> Business Logic        └─> Models + DB
```

---

## 🏗️ Capas del Sistema

### 1. **Capa de Presentación** (`public/`, `src/Views/`)

**Responsabilidad:** Interfaz de usuario y puntos de entrada HTTP

```
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

### 2. **Capa de Aplicación** (`src/Controllers/`, `src/Routes/`, `src/Middleware/`)

**Responsabilidad:** Orquestación de peticiones HTTP y control de flujo

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

#### Routes (🔗)
```php
// src/Routes/routes.php - Registro centralizado
$router->get('/productos', [ProductController::class, 'index'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin'])
]);
```

#### Middleware (🛡️)
- **`AuthMiddleware`** — verificación de sesión activa
- **`CsrfMiddleware`** — protección contra CSRF en formularios
- **`RoleMiddleware`** — autorización basada en roles (admin/cajero)

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

### 4. **Capa de Datos** (`src/Repositories/`, `src/Models/`, `src/Database/`)

**Responsabilidad:** Persistencia y acceso a datos

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

#### Database (🗄️)
- **`Connection`** — singleton PDO con configuración centralizada
- Soporte MySQL/SQLite via configuración
- Manejo de transacciones a nivel de Service

---

## 🔄 Flujo de Peticiones

### 1. **Request HTTP**
```
Usuario ──HTTP──> public/index.php
```

### 2. **Bootstrapping**
```php
// public/index.php
require_once '../vendor/autoload.php';  // Composer PSR-4
session_start();                        // Gestión de sesiones
require_once '../src/Routes/routes.php'; // Registro de rutas
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
```

### 3. **Resolución de Ruta**
```
Router ──matching──> Route + Middlewares + Controller
```

### 4. **Ejecución de Middlewares**
```
AuthMiddleware ──> RoleMiddleware ──> CsrfMiddleware ──> Controller
```

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

### 8. **Response**
```
View Template ──HTML──> Browser
  o
JSON Response ──API──> Client
```

---

## 🧩 Componentes Principales

### Router (`src/Routes/Router.php`)
- **Propósito:** Matching de URLs a controllers + middlewares
- **Características:** 
  - Soporte para parámetros en rutas (`/products/{id}`)
  - Registro de middlewares por ruta
  - Métodos HTTP (GET, POST, PUT, DELETE)

### Service Container
- **Estado actual:** Dependencias manuales en constructores
- **Patrón:** Dependency Injection manual
- **Futuro:** Considerar PSR-11 Container para mayor flexibilidad

### Error Handling
- **Desarrollo:** Errores visibles con detalles
- **Producción:** Páginas de error custom (`src/Views/errors/`)
- **Logs:** Manejo básico de excepciones

### Session Management
- **Autenticación:** Session-based con `$_SESSION`
- **Datos almacenados:** `usuario_id`, `usuario`, `rol`
- **Seguridad:** Regeneración de session ID en login

---

## 🎨 Patrones de Diseño

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

### 3. **Front Controller Pattern**
```php
// public/index.php actúa como front controller
$router->dispatch($uri, $method);
```
**Beneficio:** Punto único de entrada para todas las requests

### 4. **Middleware Pattern**
```php
$router->get('/admin', [Controller::class, 'method'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin'])
]);
```
**Beneficio:** Concerns transversales (auth, logging, etc.)

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

## 📦 Manejo de Dependencias

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

### Dependency Injection Manual
```php
class ProductController {
    private ProductService $service;
    
    public function __construct(?ProductService $service = null) {
        $this->service = $service ?? new ProductService();
    }
}
```

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

## 🔒 Seguridad y Middleware

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

## 📊 Diagramas de Arquitectura

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

## 🎯 Decisiones de Diseño

### ¿Por qué sin Framework?
- **Pros:** Control total, mínimas dependencias, fácil debugging
- **Cons:** Más código boilerplate, menos features out-of-the-box
- **Decisión:** Adecuado para proyectos pequeños-medianos con requerimientos específicos

### ¿Por qué Session-based Auth?
- **Alternativas:** JWT, OAuth2
- **Decisión:** Simplicidad para aplicación web tradicional
- **Trade-off:** Menos escalable que tokens, pero más simple de implementar

### ¿Por qué Repository Pattern sin ORM?
- **Alternativas:** Active Record, Eloquent, Doctrine
- **Decisión:** Control directo sobre SQL, performance predecible
- **Trade-off:** Más código manual, pero mayor flexibilidad

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

## 🔧 Puntos de Extensión

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

### 4. **Añadir APIs JSON**
```php
// En Controller
public function apiMethod() {
    header('Content-Type: application/json');
    $data = $this->service->getData();
    echo json_encode($data);
}
```

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

## 🔗 Documentos Relacionados

- **[📖 Índice General](docs/INDEX.md)** — Navegación completa del manual
- **[🔌 API Documentation](API.md)** — Endpoints y ejemplos de uso
- **[🗄️ Database Schema](DATABASE.md)** — Esquema y relaciones
- **[🚀 Deployment Guide](DEPLOYMENT.md)** — Despliegue en producción
- **[🔧 Development Setup](DEVELOPMENT.md)** — Configuración para desarrolladores

---

## 📞 Soporte

**¿Tienes preguntas sobre la arquitectura?**
- Crea un issue en [GitHub](https://github.com/Equinoxe-Grammer/SnackShack/issues) con la etiqueta `architecture`
- Consulta la documentación específica de cada componente en `docs/`

---

**[📖 Índice](docs/INDEX.md)** | **[🔌 Ver API](API.md)** | **[🗄️ Ver Database](DATABASE.md)** | **[🚀 Ver Deployment](DEPLOYMENT.md)**