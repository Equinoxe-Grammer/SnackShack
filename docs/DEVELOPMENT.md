# 🛠️ SnackShop - Guía de Desarrollo

**🏠 Ubicación:** `DEVELOPMENT.md`
**📅 Última actualización:** 29 de octubre, 2025
**🎯 Propósito:** Guía completa para desarrolladores: setup, herramientas, workflows y estándares de código

---

## 🧭 Navegación

**[📖 Índice General](docs/INDEX.md)** | **[🏠 README](README.md)** | **[🏗️ Arquitectura](ARCHITECTURE.md)** | **[🚀 Deployment](DEPLOYMENT.md)** | **[⚙️ Configuration](CONFIGURATION.md)**

---

## 📋 Índice

- [Setup Inicial](#setup-inicial)
- [Entorno de Desarrollo](#entorno-de-desarrollo)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Estándares de Código](#estándares-de-código)
- [Workflows de Desarrollo](#workflows-de-desarrollo)
- [Debugging y Profiling](#debugging-y-profiling)
- [Herramientas de Desarrollo](#herramientas-de-desarrollo)
    - [Comandos Útiles](#comandos-útiles)
    - [Git Workflows](#git-workflows)
- [Performance Guidelines](#performance-guidelines)
- [Security Guidelines](#security-guidelines)
- [API Development](#api-development)

---

## 🚀 Setup Inicial

### Prerrequisitos

```bash
# Verificar versiones requeridas
php --version          # PHP 7.4+ (recomendado 8.1+)
composer --version     # Composer 2.0+
node --version         # Node.js 16+ (para herramientas frontend)
git --version          # Git 2.20+
```

### Instalación Rápida

```bash
# 1. Clonar repositorio
git clone https://github.com/Equinoxe-Grammer/SnackShack.git
cd SnackShack/SnackShop/www/Snackshop

# 2. Instalar dependencias PHP
composer install

# 3. Configurar entorno de desarrollo
cp .env.example .env.development
# Editar .env.development con configuraciones locales

# 4. Verificar estructura de base de datos
ls -la data/          # Verificar snackshop.db existe
php -r "new PDO('sqlite:data/snackshop.db'); echo 'DB OK\n';"

# 5. Iniciar servidor de desarrollo
php -S localhost:8000 -t public

# 6. Verificar instalación
curl http://localhost:8000
```

### Configuración .env para Desarrollo

```bash
# .env.development
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database - SQLite para desarrollo local
SNACKSHOP_DB_HOST=127.0.0.1
SNACKSHOP_DB_NAME=data/snackshop.db
SNACKSHOP_DB_TYPE=sqlite

# Session
SESSION_SECURE=false
SESSION_DRIVER=file
SESSION_PATH=/tmp/snackshop_sessions

# Logging
LOG_LEVEL=debug
LOG_FILE=/tmp/snackshop-dev.log
ERROR_REPORTING=E_ALL

# Development tools
QUERY_LOG_ENABLED=true
MEMORY_PROFILING=true
EXECUTION_TIME_TRACKING=true

# Cache (disabled para desarrollo)
CACHE_DRIVER=none

# Email (log driver para testing)
MAIL_DRIVER=log
MAIL_LOG_FILE=/tmp/snackshop-emails.log
```

---

## Comandos Útiles

Pequeña guía de comandos y scripts útiles para desarrollo local y tareas diarias:

```powershell
# Instalar dependencias
composer install

# Ejecutar migraciones
php scripts/migrate.php

# Ejecutar tests unitarios
./vendor/bin/phpunit --testsuite unit

# Correr servidor de desarrollo
php -S localhost:8000 -t public

# Limpiar cache (ejemplos)
php scripts/clear-cache.php
```

## API Development

Consejos rápidos para desarrollar y probar endpoints API:

- Mantén rutas RESTful y versionadas (/api/v1/...).
- Valida la entrada en el servidor; centraliza validadores en Services/Validators.
- Ejemplo de prueba rápida con curl:

```bash
curl -X GET http://localhost:8000/api/v1/products
```

---

## 💻 Entorno de Desarrollo

### Opción 1: Servidor Embebido PHP (Recomendado)

```bash
# Servidor básico
php -S localhost:8000 -t public

# Con configuración personalizada
php -S localhost:8000 -t public -c php-dev.ini

# Con logs detallados
php -S localhost:8000 -t public 2>&1 | tee server.log
```

### Opción 2: Docker para Desarrollo

```yaml
# docker-compose.dev.yml
version: '3.8'

services:
  web:
    build:
      context: .
      dockerfile: Dockerfile.dev
    ports:
      - "8000:80"
    volumes:
      - .:/var/www/html
      - ./logs:/var/log/snackshop
    environment:
      - APP_ENV=development
      - APP_DEBUG=true
    depends_on:
      - db

  db:
    image: mysql:8.0
    environment:
      - MYSQL_ROOT_PASSWORD=dev
      - MYSQL_DATABASE=snackshop_dev
      - MYSQL_USER=dev
      - MYSQL_PASSWORD=dev
    ports:
      - "3306:3306"
    volumes:
      - dev_db_data:/var/lib/mysql

  phpmyadmin:
    image: phpmyadmin:latest
    ports:
      - "8080:80"
    environment:
      - PMA_HOST=db
      - PMA_USER=dev
      - PMA_PASSWORD=dev

volumes:
  dev_db_data:
```

```dockerfile
# Dockerfile.dev
FROM php:8.1-apache

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev zip unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql mbstring xml

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Instalar Xdebug para desarrollo
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Configurar Xdebug
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Configurar DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

WORKDIR /var/www/html
```

### Opción 3: XAMPP/WAMP (Windows)

```powershell
# Configuración para XAMPP
# 1. Copiar proyecto a C:\xampp\htdocs\snackshop
# 2. Configurar virtual host

# httpd-vhosts.conf
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/snackshop/public"
    ServerName snackshop.local
    <Directory "C:/xampp/htdocs/snackshop/public">
        AllowOverride All
        Require all granted
        Options Indexes FollowSymLinks
    </Directory>
</VirtualHost>

# hosts file (C:\Windows\System32\drivers\etc\hosts)
127.0.0.1    snackshop.local
```

---

## 🏗️ Estructura del Proyecto

### Organización de Archivos

```bash
SnackShop/
├── 📁 public/              # Document root, assets públicos
│   ├── index.php           # Entry point principal
│   ├── .htaccess          # Apache URL rewriting
│   └── assets/            # CSS, JS, imágenes
├── 📁 src/                # Código fuente principal
│   ├── 📁 Controllers/    # Controladores MVC
│   ├── 📁 Models/         # Modelos de datos
│   ├── 📁 Services/       # Lógica de negocio
│   ├── 📁 Repositories/   # Acceso a datos
│   ├── 📁 Views/          # Templates PHP
│   ├── 📁 Middleware/     # Middleware HTTP
│   ├── 📁 Routes/         # Definición de rutas
│   ├── 📁 Config/         # Configuración de la app
│   └── 📁 Database/       # Conexión y utilidades DB
├── 📁 data/               # Datos de la aplicación
│   ├── snackshop.db      # Base de datos SQLite
│   └── images/           # Imágenes subidas
├── 📁 vendor/             # Dependencias Composer
├── 📁 docs/               # Documentación técnica
├── composer.json          # Dependencias PHP
├── .env.example          # Template de configuración
└── README.md             # Documentación principal
```

### Convenciones de Naming

```php
// Clases: PascalCase
class ProductController {}
class UserService {}
class PaymentMethodRepository {}

// Métodos y variables: camelCase
public function getUserById($userId) {}
private $connectionPool;

// Constantes: SNAKE_CASE
const MAX_UPLOAD_SIZE = 5242880;
const DEFAULT_CACHE_TTL = 3600;

// Archivos: PascalCase para clases, kebab-case para otros
ProductController.php
payment-methods.php
user-profile.css

// Tablas DB: snake_case
usuarios, productos, detalle_ventas

// Columnas DB: snake_case
user_id, created_at, payment_method_id
```

### Namespaces y Autoloading

```php
// composer.json - PSR-4 autoloading
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}

// Estructura de namespaces
namespace App\Controllers;         // src/Controllers/
namespace App\Services;           // src/Services/
namespace App\Models;             // src/Models/
namespace App\Repositories;       // src/Repositories/
namespace App\Middleware;         // src/Middleware/
namespace App\Utils;              // src/Utils/

// Ejemplo de uso
use App\Controllers\ProductController;
use App\Services\ProductService;
use App\Models\Product;
```

---

## 📏 Estándares de Código

### PHP Coding Standards (PSR-12)

```php
<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\ProductService;
use App\Utils\Validator;
use Exception;

/**
 * Controller para gestión de productos
 *
 * @package App\Controllers
 * @author  SnackShop Team
 * @since   1.0.0
 */
class ProductController extends BaseController
{
    private ProductService $productService;
    private Validator $validator;

    public function __construct(
        ProductService $productService,
        Validator $validator
    ) {
        $this->productService = $productService;
        $this->validator = $validator;
    }

    /**
     * Obtiene lista de productos con filtros
     *
     * @param array $filters Filtros de búsqueda
     * @return array Lista de productos
     * @throws Exception Si hay error en la consulta
     */
    public function getProducts(array $filters = []): array
    {
        try {
            // Validar parámetros de entrada
            $validatedFilters = $this->validator->validate($filters, [
                'categoria' => 'integer|min:1',
                'activo' => 'boolean',
                'precio_min' => 'numeric|min:0',
                'precio_max' => 'numeric|min:0'
            ]);

            // Delegar lógica al service
            $products = $this->productService->getFilteredProducts($validatedFilters);

            return [
                'success' => true,
                'data' => $products,
                'count' => count($products)
            ];
        } catch (Exception $e) {
            $this->logger->error('Error getting products', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Crea un nuevo producto
     */
    public function createProduct(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $productData = [
            'nombre' => $_POST['nombre'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? '',
            'precio' => (float)($_POST['precio'] ?? 0),
            'categoria_id' => (int)($_POST['categoria_id'] ?? 0)
        ];

        try {
            $newProduct = $this->productService->createProduct($productData);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Producto creado exitosamente',
                'product_id' => $newProduct->getId()
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
```

### Code Quality Guidelines

#### 1. SOLID Principles

```php
// Single Responsibility Principle
class UserAuthenticator {
    public function authenticate(string $email, string $password): bool {}
}

class UserRegistrar {
    public function register(array $userData): User {}
}

// Open/Closed Principle
interface PaymentProcessorInterface {
    public function processPayment(float $amount): bool;
}

class CashPaymentProcessor implements PaymentProcessorInterface {}
class CardPaymentProcessor implements PaymentProcessorInterface {}

// Dependency Inversion
class OrderService {
    public function __construct(
        private PaymentProcessorInterface $paymentProcessor,
        private OrderRepositoryInterface $orderRepository
    ) {}
}
```

#### 2. Error Handling

```php
// Custom Exception Classes
class ValidationException extends Exception {}
class DatabaseException extends Exception {}
class AuthenticationException extends Exception {}

// Proper error handling
try {
    $user = $this->userService->getUserById($userId);
} catch (UserNotFoundException $e) {
    $this->logger->warning('User not found', ['user_id' => $userId]);
    return $this->render('errors/404.php');
} catch (DatabaseException $e) {
    $this->logger->error('Database error', ['error' => $e->getMessage()]);
    return $this->render('errors/500.php');
}
```

#### 3. Documentation Standards

```php
/**
 * Calcula el precio total de una venta incluyendo impuestos
 *
 * @param array $items Lista de items [['producto_id' => int, 'cantidad' => int]]
 * @param int $descuento Descuento en porcentaje (0-100)
 * @param bool $incluirImpuestos Si incluir impuestos en el cálculo
 * @return array ['subtotal' => float, 'impuestos' => float, 'total' => float]
 * @throws InvalidArgumentException Si los items están malformados
 * @throws ProductNotFoundException Si un producto no existe
 *
 * @example
 * $calculator = new PriceCalculator();
 * $result = $calculator->calculateTotal([
 *     ['producto_id' => 1, 'cantidad' => 2],
 *     ['producto_id' => 3, 'cantidad' => 1]
 * ], 10, true);
 * echo $result['total']; // 45.60
 */
public function calculateTotal(
    array $items,
    int $descuento = 0,
    bool $incluirImpuestos = true
): array {
    // Implementation...
}
```

### Frontend Standards

#### CSS/SCSS Organization

```scss
// assets/css/main.scss
@import 'variables';
@import 'mixins';
@import 'base';
@import 'components/buttons';
@import 'components/forms';
@import 'components/cards';
@import 'pages/dashboard';
@import 'pages/products';

// BEM Methodology
.product-card {
    &__image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    &__title {
        font-size: 1.2rem;
        font-weight: bold;
    }

    &__price {
        color: $primary-color;

        &--discounted {
            color: $error-color;
        }
    }
}
```

#### JavaScript Standards

```javascript
// assets/js/modules/product-manager.js
class ProductManager {
    constructor(apiBaseUrl) {
        this.apiBaseUrl = apiBaseUrl;
        this.products = [];
    }

    /**
     * Obtiene productos del servidor
     * @param {Object} filters - Filtros de búsqueda
     * @returns {Promise<Array>} Lista de productos
     */
    async getProducts(filters = {}) {
        try {
            const params = new URLSearchParams(filters);
            const response = await fetch(`${this.apiBaseUrl}/products?${params}`);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            this.products = data.products || [];
            return this.products;
        } catch (error) {
            console.error('Error fetching products:', error);
            throw error;
        }
    }

    /**
     * Renderiza lista de productos en el DOM
     * @param {string} containerId - ID del contenedor
     */
    renderProducts(containerId) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.warn(`Container ${containerId} not found`);
            return;
        }

        container.innerHTML = this.products.map(product => `
            <div class="product-card" data-product-id="${product.id}">
                <img class="product-card__image"
                     src="${product.imagen_url || '/assets/images/no-image.png'}"
                     alt="${product.nombre}">
                <h3 class="product-card__title">${product.nombre}</h3>
                <p class="product-card__price">$${product.precio}</p>
                <button class="btn btn--primary"
                        onclick="productManager.addToCart(${product.id})">
                    Agregar al Carrito
                </button>
            </div>
        `).join('');
    }
}

// Inicialización
document.addEventListener('DOMContentLoaded', () => {
    window.productManager = new ProductManager('/api/v1');
});
```

---

## 🔄 Workflows de Desarrollo

### Feature Development Workflow

```bash
# 1. Crear rama para nueva feature
git checkout -b feature/add-product-variants
git push -u origin feature/add-product-variants

# 2. Desarrollar en pequeños commits
git add src/Controllers/VariantController.php
git commit -m "feat: add VariantController with CRUD operations"

git add src/Services/VariantService.php
git commit -m "feat: implement variant business logic"

git add src/Views/products/variants.php
git commit -m "feat: add variant management UI"

# 3. Testing local
php -S localhost:8000 -t public
# Probar manualmente la funcionalidad

# 4. Crear Pull Request
git push origin feature/add-product-variants
# Abrir PR en GitHub con descripción detallada
```

### Commit Message Convention

```bash
# Formato: <type>(<scope>): <description>

# Types:
feat:     # Nueva funcionalidad
fix:      # Bug fix
docs:     # Documentación
style:    # Formato, espacios, etc (no código)
refactor: # Refactoring sin cambiar funcionalidad
test:     # Agregar/modificar tests
chore:    # Tareas de mantenimiento

# Ejemplos:
git commit -m "feat(products): add image upload functionality"
git commit -m "fix(auth): resolve session timeout issue"
git commit -m "docs(api): update endpoint documentation"
git commit -m "refactor(database): optimize query performance"
git commit -m "test(services): add unit tests for UserService"
git commit -m "chore(deps): update composer dependencies"
```

### Code Review Checklist

```markdown
## Code Review Checklist

### Funcionalidad
- [ ] ¿El código hace lo que se supone que debe hacer?
- [ ] ¿Los casos edge están manejados correctamente?
- [ ] ¿La funcionalidad está completa según los requirements?

### Código
- [ ] ¿El código es fácil de entender?
- [ ] ¿Los nombres de variables/funciones son descriptivos?
- [ ] ¿Hay código duplicado que se pueda refactorizar?
- [ ] ¿Se siguen los estándares de código del proyecto?

### Seguridad
- [ ] ¿Los inputs están validados y sanitizados?
- [ ] ¿No hay vulnerabilidades de SQL injection?
- [ ] ¿Los datos sensibles están protegidos?
- [ ] ¿Se usan tokens CSRF donde corresponde?

### Performance
- [ ] ¿No hay queries N+1?
- [ ] ¿Se usan índices de base de datos apropiados?
- [ ] ¿El código es eficiente en memoria?
- [ ] ¿Se implementa caching donde es beneficioso?

### Testing
- [ ] ¿Hay tests unitarios para la nueva funcionalidad?
- [ ] ¿Los tests existentes siguen pasando?
- [ ] ¿Los casos edge están cubiertos por tests?

### Documentación
- [ ] ¿El código está bien documentado?
- [ ] ¿Los README están actualizados si es necesario?
- [ ] ¿Los cambios de API están documentados?
```

---

## 🐛 Debugging y Profiling

### Xdebug Configuration

```ini
; php.ini para desarrollo con Xdebug
[xdebug]
zend_extension=xdebug
xdebug.mode=debug,profile,trace
xdebug.client_host=127.0.0.1
xdebug.client_port=9003
xdebug.start_with_request=trigger
xdebug.discover_client_host=false
xdebug.idekey=VSCODE

; Profiling
xdebug.output_dir=/tmp/xdebug
xdebug.profiler_output_name=cachegrind.out.%t.%p
xdebug.trace_output_name=trace.%c

; Log
xdebug.log=/tmp/xdebug.log
xdebug.log_level=7
```

### VSCode Debug Configuration

```json
// .vscode/launch.json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for Xdebug",
            "type": "php",
            "request": "launch",
            "port": 9003,
            "pathMappings": {
                "/var/www/html": "${workspaceFolder}"
            },
            "xdebugSettings": {
                "max_children": 128,
                "max_data": 512,
                "max_depth": 3
            }
        },
        {
            "name": "Launch Built-in Server",
            "type": "php",
            "request": "launch",
            "program": "",
            "cwd": "${workspaceFolder}/public",
            "port": 8000,
            "serverReadyAction": {
                "pattern": "Development Server \\(http://localhost:([0-9]+)\\) started",
                "uriFormat": "http://localhost:%s",
                "action": "openExternally"
            }
        }
    ]
}
```

### Debug Utilities

```php
// src/Utils/Debug.php
class Debug {
    private static $enabled = false;
    private static $startTime = 0;
    private static $queries = [];
    private static $memoryPeaks = [];

    public static function init() {
        self::$enabled = getenv('APP_DEBUG') === 'true';
        self::$startTime = microtime(true);

        if (self::$enabled) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }
    }

    public static function dump($var, $label = null) {
        if (!self::$enabled) return;

        echo "<pre style='background:#f5f5f5;padding:10px;border:1px solid #ddd;margin:10px;'>";
        if ($label) {
            echo "<strong>$label:</strong>\n";
        }
        var_dump($var);
        echo "</pre>";
    }

    public static function log($message, $context = []) {
        if (!self::$enabled) return;

        $timestamp = date('Y-m-d H:i:s');
        $memory = self::formatBytes(memory_get_usage());
        $contextStr = $context ? json_encode($context) : '';

        error_log("[$timestamp] [$memory] $message $contextStr");
    }

    public static function timer($label) {
        if (!self::$enabled) return;

        static $timers = [];

        if (!isset($timers[$label])) {
            $timers[$label] = microtime(true);
            echo "⏱️ Timer '$label' started\n";
        } else {
            $elapsed = round((microtime(true) - $timers[$label]) * 1000, 2);
            echo "⏱️ Timer '$label': {$elapsed}ms\n";
            unset($timers[$label]);
        }
    }

    public static function queryLog($query, $params = [], $time = 0) {
        if (!self::$enabled) return;

        self::$queries[] = [
            'query' => $query,
            'params' => $params,
            'time' => $time,
            'memory' => memory_get_usage()
        ];
    }

    public static function getStats() {
        if (!self::$enabled) return [];

        return [
            'execution_time' => round((microtime(true) - self::$startTime) * 1000, 2),
            'memory_usage' => self::formatBytes(memory_get_usage()),
            'memory_peak' => self::formatBytes(memory_get_peak_usage()),
            'queries_count' => count(self::$queries),
            'queries' => self::$queries
        ];
    }

    public static function showDebugBar() {
        if (!self::$enabled) return;

        $stats = self::getStats();
        echo "
        <div style='position:fixed;bottom:0;left:0;right:0;background:#333;color:#fff;padding:10px;font-family:monospace;font-size:12px;z-index:9999;'>
            <strong>Debug Info:</strong>
            Time: {$stats['execution_time']}ms |
            Memory: {$stats['memory_usage']} (Peak: {$stats['memory_peak']}) |
            Queries: {$stats['queries_count']}
            <button onclick='this.parentNode.style.display=\"none\"' style='float:right;'>✕</button>
        </div>";
    }

    private static function formatBytes($size, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, $precision) . ' ' . $units[$i];
    }
}
```

### Query Debugging

```php
// src/Database/QueryDebugger.php
class QueryDebugger {
    private static $queries = [];
    private static $enabled = false;

    public static function enable() {
        self::$enabled = getenv('QUERY_LOG_ENABLED') === 'true';
    }

    public static function logQuery($query, $params = [], $executionTime = 0) {
        if (!self::$enabled) return;

        self::$queries[] = [
            'sql' => $query,
            'params' => $params,
            'time' => $executionTime,
            'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5),
            'timestamp' => microtime(true)
        ];

        // Log slow queries
        if ($executionTime > 1.0) {
            error_log("SLOW QUERY ({$executionTime}s): $query");
        }
    }

    public static function getQueries() {
        return self::$queries;
    }

    public static function getSlowQueries($threshold = 1.0) {
        return array_filter(self::$queries, function($query) use ($threshold) {
            return $query['time'] > $threshold;
        });
    }

    public static function explain($query, $params = []) {
        if (!self::$enabled) return null;

        try {
            $db = Database::getConnection();
            $explainQuery = "EXPLAIN " . $query;
            $stmt = $db->prepare($explainQuery);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
```

---

## 🛠️ Herramientas de Desarrollo

### Composer Scripts

```json
{
    "scripts": {
        "dev": "php -S localhost:8000 -t public",
        "test": "phpunit --configuration phpunit.xml",
        "test-coverage": "phpunit --coverage-html coverage/",
        "lint": "php-cs-fixer fix --dry-run --diff",
        "fix": "php-cs-fixer fix",
        "analyze": "phpstan analyse src/ --level=5",
        "docs": "phpdoc -d src/ -t docs/api/",
        "db-migrate": "php scripts/migrate.php",
        "db-seed": "php scripts/seed.php",
        "cache-clear": "rm -rf tmp/cache/*",
        "logs-clear": "rm -rf logs/*.log"
    },
    "scripts-descriptions": {
        "dev": "Start development server",
        "test": "Run unit tests",
        "lint": "Check code style",
        "analyze": "Run static analysis"
    }
}
```

### Development Scripts

```php
#!/usr/bin/env php
<?php
// scripts/dev-setup.php - Script de configuración para nuevos desarrolladores

require_once __DIR__ . '/../vendor/autoload.php';

echo "🚀 SnackShop Development Setup\n\n";

// 1. Verificar prerrequisitos
echo "1. Verificando prerrequisitos...\n";
$phpVersion = PHP_VERSION;
if (version_compare($phpVersion, '7.4.0', '<')) {
    die("❌ PHP 7.4+ requerido. Versión actual: $phpVersion\n");
}
echo "✅ PHP $phpVersion\n";

// 2. Configurar .env
echo "\n2. Configurando archivo .env...\n";
if (!file_exists('.env')) {
    if (copy('.env.example', '.env')) {
        echo "✅ Archivo .env creado\n";
    } else {
        echo "❌ Error creando .env\n";
    }
} else {
    echo "ℹ️ Archivo .env ya existe\n";
}

// 3. Verificar base de datos
echo "\n3. Verificando base de datos...\n";
try {
    $dbPath = 'data/snackshop.db';
    if (file_exists($dbPath)) {
        $pdo = new PDO("sqlite:$dbPath");
        $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "✅ Base de datos SQLite encontrada con " . count($tables) . " tablas\n";
    } else {
        echo "❌ Base de datos no encontrada en $dbPath\n";
        echo "   Ejecutar: php scripts/init-db.php\n";
    }
} catch (Exception $e) {
    echo "❌ Error verificando BD: " . $e->getMessage() . "\n";
}

// 4. Crear directorios necesarios
echo "\n4. Creando directorios necesarios...\n";
$dirs = ['logs', 'tmp/cache', 'data/images', 'data/backups'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✅ Directorio creado: $dir\n";
        } else {
            echo "❌ Error creando: $dir\n";
        }
    } else {
        echo "ℹ️ Directorio existe: $dir\n";
    }
}

// 5. Verificar permisos
echo "\n5. Verificando permisos...\n";
$writableDirs = ['data', 'logs', 'tmp'];
foreach ($writableDirs as $dir) {
    if (is_writable($dir)) {
        echo "✅ Escribible: $dir\n";
    } else {
        echo "❌ No escribible: $dir\n";
        echo "   Ejecutar: chmod 755 $dir\n";
    }
}

echo "\n🎉 Setup completado!\n";
echo "🌐 Iniciar servidor: composer run dev\n";
echo "🧪 Ejecutar tests: composer run test\n";
```

### Useful Development Commands

```bash
# Desarrollo diario
composer run dev                    # Iniciar servidor
composer run test                   # Ejecutar tests
composer run lint                   # Verificar código
composer run fix                    # Corregir estilo

# Base de datos
php scripts/migrate.php             # Ejecutar migraciones
php scripts/seed.php                # Poblar datos de prueba
php scripts/db-backup.php           # Crear backup

# Limpieza
composer run cache-clear            # Limpiar cache
composer run logs-clear             # Limpiar logs
rm -rf vendor/ && composer install  # Reinstalar deps

# Análisis de código
composer run analyze                # PHPStan análisis
composer run docs                   # Generar documentación

# Git workflows
git flow feature start nueva-feature
git flow feature finish nueva-feature
git flow release start 1.1.0
git flow release finish 1.1.0
```

---

## 📊 Performance Guidelines

### Database Optimization

```php
// ❌ N+1 Query Problem
foreach ($sales as $sale) {
    $user = $userRepository->findById($sale->getUserId());
    echo $user->getName();
}

// ✅ Eager Loading
$sales = $saleRepository->findWithUsers();
foreach ($sales as $sale) {
    echo $sale->getUser()->getName();
}

// ✅ Batch Loading
$userIds = array_unique(array_column($sales, 'user_id'));
$users = $userRepository->findByIds($userIds);
$usersById = array_column($users, null, 'id');

foreach ($sales as $sale) {
    $user = $usersById[$sale->getUserId()];
    echo $user->getName();
}
```

### Caching Strategies

```php
// Service Layer Caching
class ProductService {
    private $cache;
    private $repository;

    public function getPopularProducts($limit = 10) {
        $cacheKey = "popular_products_$limit";

        $products = $this->cache->get($cacheKey);
        if ($products === null) {
            $products = $this->repository->findPopular($limit);
            $this->cache->set($cacheKey, $products, 3600); // 1 hora
        }

        return $products;
    }

    public function invalidateProductCache($productId) {
        $this->cache->delete("product_$productId");
        $this->cache->delete("popular_products_*"); // Wildcard
    }
}
```

### Memory Management

```php
// ❌ Memory Leak
class ReportGenerator {
    private $data = [];

    public function generateReport() {
        $this->data = $this->loadLargeDataset(); // Nunca se libera
        return $this->processData();
    }
}

// ✅ Proper Memory Management
class ReportGenerator {
    public function generateReport() {
        $data = $this->loadLargeDataset();
        $result = $this->processData($data);
        unset($data); // Liberar memoria explícitamente
        return $result;
    }

    // ✅ Generator para datasets grandes
    public function processLargeDataset() {
        $handle = fopen('large-file.csv', 'r');
        while (($line = fgetcsv($handle)) !== false) {
            yield $this->processLine($line);
        }
        fclose($handle);
    }
}
```

---

## 🔒 Security Guidelines

### Input Validation

```php
// src/Utils/SecurityValidator.php
class SecurityValidator {
    public static function validateProductData($data) {
        $rules = [
            'nombre' => [
                'required' => true,
                'type' => 'string',
                'max_length' => 100,
                'pattern' => '/^[a-zA-Z0-9\s\-]+$/'
            ],
            'precio' => [
                'required' => true,
                'type' => 'numeric',
                'min' => 0.01,
                'max' => 99999.99
            ],
            'categoria_id' => [
                'required' => true,
                'type' => 'integer',
                'min' => 1
            ]
        ];

        return self::validate($data, $rules);
    }

    public static function sanitizeInput($input, $type = 'string') {
        switch ($type) {
            case 'email':
                return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
            case 'int':
                return (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return (float) filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'html':
                return htmlspecialchars(trim($input), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            default:
                return trim(strip_tags($input));
        }
    }
}
```

### SQL Injection Prevention

```php
// ❌ Vulnerable to SQL Injection
$query = "SELECT * FROM usuarios WHERE email = '" . $_POST['email'] . "'";
$result = $db->query($query);

// ✅ Prepared Statements
$query = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_POST['email']]);
$result = $stmt->fetchAll();

// ✅ Named Parameters
$query = "SELECT * FROM productos WHERE categoria_id = :categoria AND precio BETWEEN :min AND :max";
$stmt = $db->prepare($query);
$stmt->execute([
    ':categoria' => $categoryId,
    ':min' => $minPrice,
    ':max' => $maxPrice
]);
```

### XSS Prevention

```php
// ❌ Vulnerable to XSS
echo "<h1>Bienvenido " . $_GET['name'] . "</h1>";

// ✅ Properly Escaped
echo "<h1>Bienvenido " . htmlspecialchars($_GET['name'], ENT_QUOTES, 'UTF-8') . "</h1>";

// ✅ Template Helper
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

echo "<h1>Bienvenido " . h($_GET['name']) . "</h1>";
```

---

## 🔗 Documentos Relacionados

- **[📖 Índice General](docs/INDEX.md)** — Navegación completa del manual
- **[🏗️ Architecture](ARCHITECTURE.md)** — Comprende la arquitectura antes de desarrollar
- **[🔌 API](API.md)** — Endpoints disponibles para desarrollo
- **[🗄️ Database](DATABASE.md)** — Esquemas y consultas de base de datos
- **[🚀 Deployment](DEPLOYMENT.md)** — Despliegue para staging/producción
- **[⚙️ Configuration](CONFIGURATION.md)** — Configuración avanzada del sistema

---

## 📞 Soporte para Desarrolladores

**¿Necesitas ayuda con el desarrollo?**
- **Setup Issues:** Ejecuta `php scripts/dev-setup.php`
- **Debug Problems:** Habilita Xdebug y revisa la configuración
- **Performance Issues:** Revisa las guidelines de optimización
- **Code Standards:** Ejecuta `composer run lint` para verificar estilo

---

**[📖 Índice](docs/INDEX.md)** | **[🏗️ Ver Arquitectura](ARCHITECTURE.md)** | **[🔌 Ver API](API.md)** | **[🧪 Ver Testing](TESTING.md)**
