# 🧪 SnackShop - Testing Guide

**🏠 Ubicación:** `TESTING.md`  
**📅 Última actualización:** 29 de octubre, 2025  
**🎯 Propósito:** Guía completa de testing: unit tests, integration tests, workflows y coverage

---

## 🧭 Navegación

**[📖 Índice General](docs/INDEX.md)** | **[🏠 README](README.md)** | **[🛠️ Development](DEVELOPMENT.md)** | **[🏗️ Arquitectura](ARCHITECTURE.md)**

---

## 📋 Índice

- [Setup de Testing](#setup-de-testing)
- [Unit Testing](#unit-testing)
- [Integration Testing](#integration-testing)
- [Database Testing](#database-integration-tests)
- [API Testing](#api-integration-tests)
- [Frontend Testing](#frontend-testing)
- [Testing Workflows](#testing-workflows)
- [Code Coverage](#code-coverage)
- [Performance Testing](#performance-testing)
- [Security Testing](#security-testing)
- [Mocking y Fixtures](#mocking-y-fixtures)
- [CI/CD Integration](#cicd-integration)

---

## Testing Workflows

Descripción de los flujos de testing recomendados (local -> CI -> staging):

- Ejecutar unit tests en desarrollo.
- Ejecutar integration tests con base de datos de testing (containers o sqlite).
- Pipeline CI: ejecutar linters, unit tests, integration tests y publicar reportes de coverage.

## Mocking y Fixtures

Breve guía sobre cómo usar mocks y fixtures en los tests:

- Usa fixtures ligeros para poblar datos en integration tests.
- Prefiere mocks para llamadas externas (servicios, APIs) en unit tests.
- Ejemplo con PHPUnit + Prophecy/Mockery en `tests/`.

## 🚀 Setup de Testing

### Instalación de PHPUnit

```bash
# Instalar PHPUnit via Composer
composer require --dev phpunit/phpunit ^9.5
composer require --dev phpunit/php-code-coverage ^9.2

# Verificar instalación
./vendor/bin/phpunit --version
```

### Estructura de Directorios

```
tests/
├── 📁 Unit/                   # Tests unitarios
│   ├── Controllers/           # Tests de controladores
│   ├── Services/             # Tests de servicios
│   ├── Models/               # Tests de modelos
│   ├── Repositories/         # Tests de repositorios
│   └── Utils/                # Tests de utilidades
├── 📁 Integration/           # Tests de integración
│   ├── Database/             # Tests de BD
│   ├── API/                  # Tests de endpoints
│   └── Features/             # Tests de funcionalidades
├── 📁 Fixtures/              # Datos de prueba
├── 📁 Mocks/                 # Objetos mock
├── bootstrap.php             # Bootstrap de tests
└── phpunit.xml              # Configuración PHPUnit
```

### Configuración PHPUnit

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!-- phpunit.xml -->
<phpunit
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/9.5/phpunit.xsd"
    bootstrap="tests/bootstrap.php"
    cacheResultFile=".phpunit.cache/test-results"
    executionOrder="depends,defects"
    beStrictAboutCoversAnnotation="true"
    beStrictAboutOutputDuringTests="true"
    beStrictAboutTodoAnnotatedTests="true"
    convertDeprecationsToExceptions="true"
    failOnRisky="true"
    failOnWarning="true"
    verbose="true">
    
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory suffix="Test.php">tests/Integration</directory>
        </testsuite>
    </testsuites>

    <coverage cacheDirectory=".phpunit.cache/code-coverage"
              processUncoveredFiles="true">
        <include>
            <directory suffix=".php">src</directory>
        </include>
        <exclude>
            <directory>src/Views</directory>
            <file>src/Config/AppConfig.php</file>
        </exclude>
        <report>
            <html outputDirectory="coverage/html"/>
            <text outputFile="coverage/coverage.txt"/>
            <clover outputFile="coverage/clover.xml"/>
        </report>
    </coverage>

    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_DATABASE" value="snackshop_test"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="MAIL_DRIVER" value="array"/>
    </php>
</phpunit>
```

### Bootstrap para Tests

```php
<?php
// tests/bootstrap.php

declare(strict_types=1);

// Configurar autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Configurar entorno de testing
$_ENV['APP_ENV'] = 'testing';
$_ENV['APP_DEBUG'] = 'true';
$_ENV['DB_DATABASE'] = 'snackshop_test';
$_ENV['CACHE_DRIVER'] = 'array';
$_ENV['MAIL_DRIVER'] = 'array';

// Inicializar base de datos de testing
initTestDatabase();

function initTestDatabase() {
    $testDbPath = __DIR__ . '/database/test.db';
    
    // Crear directorio si no existe
    $dir = dirname($testDbPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Crear BD de testing si no existe
    if (!file_exists($testDbPath)) {
        copy(__DIR__ . '/../data/snackshop.db', $testDbPath);
    }
}

// Base class para tests
abstract class TestCase extends PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        parent::setUp();
        $this->resetDatabase();
    }
    
    protected function tearDown(): void {
        parent::tearDown();
        $this->cleanupTestData();
    }
    
    protected function resetDatabase() {
        // Implementar reset de BD para cada test
    }
    
    protected function cleanupTestData() {
        // Limpieza post-test
    }
}
```

---

## 🔬 Unit Testing

### Testing de Models

```php
<?php
// tests/Unit/Models/ProductTest.php

declare(strict_types=1);

use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testProductCreation()
    {
        // Arrange
        $productData = [
            'id' => 1,
            'nombre' => 'Coca Cola',
            'descripcion' => 'Bebida refrescante',
            'precio' => 15.50,
            'categoria_id' => 1,
            'activo' => true
        ];

        // Act
        $product = new Product($productData);

        // Assert
        $this->assertEquals(1, $product->getId());
        $this->assertEquals('Coca Cola', $product->getNombre());
        $this->assertEquals('Bebida refrescante', $product->getDescripcion());
        $this->assertEquals(15.50, $product->getPrecio());
        $this->assertEquals(1, $product->getCategoriaId());
        $this->assertTrue($product->isActivo());
    }

    public function testProductValidation()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El nombre del producto es requerido');

        new Product(['nombre' => '']);
    }

    public function testPriceFormatting()
    {
        $product = new Product([
            'nombre' => 'Test Product',
            'precio' => 10.567
        ]);

        $this->assertEquals(10.57, $product->getPrecio());
        $this->assertEquals('$10.57', $product->getFormattedPrice());
    }

    /**
     * @dataProvider priceProvider
     */
    public function testPriceValidation($price, $isValid)
    {
        if (!$isValid) {
            $this->expectException(InvalidArgumentException::class);
        }

        $product = new Product([
            'nombre' => 'Test Product',
            'precio' => $price
        ]);

        if ($isValid) {
            $this->assertEquals($price, $product->getPrecio());
        }
    }

    public function priceProvider(): array
    {
        return [
            'valid positive price' => [10.50, true],
            'valid zero price' => [0.0, true],
            'invalid negative price' => [-5.0, false],
            'invalid null price' => [null, false],
            'valid string price' => ['15.99', true],
            'invalid string price' => ['abc', false]
        ];
    }
}
```

### Testing de Services

```php
<?php
// tests/Unit/Services/ProductServiceTest.php

declare(strict_types=1);

use App\Services\ProductService;
use App\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ProductServiceTest extends TestCase
{
    private ProductService $productService;
    private ProductRepositoryInterface|MockObject $productRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->productService = new ProductService($this->productRepository);
    }

    public function testGetProductById()
    {
        // Arrange
        $productId = 1;
        $expectedProduct = new Product([
            'id' => $productId,
            'nombre' => 'Test Product',
            'precio' => 10.0
        ]);

        $this->productRepository
            ->expects($this->once())
            ->method('findById')
            ->with($productId)
            ->willReturn($expectedProduct);

        // Act
        $result = $this->productService->getProductById($productId);

        // Assert
        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($productId, $result->getId());
        $this->assertEquals('Test Product', $result->getNombre());
    }

    public function testGetProductByIdNotFound()
    {
        // Arrange
        $productId = 999;
        
        $this->productRepository
            ->expects($this->once())
            ->method('findById')
            ->with($productId)
            ->willReturn(null);

        // Act & Assert
        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage("Product with ID $productId not found");
        
        $this->productService->getProductById($productId);
    }

    public function testCreateProduct()
    {
        // Arrange
        $productData = [
            'nombre' => 'New Product',
            'precio' => 25.0,
            'categoria_id' => 1
        ];

        $expectedProduct = new Product(array_merge($productData, ['id' => 123]));

        $this->productRepository
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(function($product) use ($productData) {
                return $product instanceof Product &&
                       $product->getNombre() === $productData['nombre'] &&
                       $product->getPrecio() === $productData['precio'];
            }))
            ->willReturn($expectedProduct);

        // Act
        $result = $this->productService->createProduct($productData);

        // Assert
        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals(123, $result->getId());
        $this->assertEquals('New Product', $result->getNombre());
    }

    public function testGetActiveProducts()
    {
        // Arrange
        $activeProducts = [
            new Product(['id' => 1, 'nombre' => 'Product 1', 'activo' => true]),
            new Product(['id' => 2, 'nombre' => 'Product 2', 'activo' => true])
        ];

        $this->productRepository
            ->expects($this->once())
            ->method('findByStatus')
            ->with(true)
            ->willReturn($activeProducts);

        // Act
        $result = $this->productService->getActiveProducts();

        // Assert
        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(Product::class, $result);
    }

    public function testCalculateTotalPrice()
    {
        // Arrange
        $items = [
            ['product_id' => 1, 'quantity' => 2],
            ['product_id' => 2, 'quantity' => 1]
        ];

        $products = [
            1 => new Product(['id' => 1, 'precio' => 10.0]),
            2 => new Product(['id' => 2, 'precio' => 15.0])
        ];

        $this->productRepository
            ->expects($this->once())
            ->method('findByIds')
            ->with([1, 2])
            ->willReturn(array_values($products));

        // Act
        $total = $this->productService->calculateTotalPrice($items);

        // Assert
        $this->assertEquals(35.0, $total); // (10 * 2) + (15 * 1)
    }
}
```

### Testing de Controllers

```php
<?php
// tests/Unit/Controllers/ProductControllerTest.php

declare(strict_types=1);

use App\Controllers\ProductController;
use App\Services\ProductService;
use App\Models\Product;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ProductControllerTest extends TestCase
{
    private ProductController $controller;
    private ProductService|MockObject $productService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->productService = $this->createMock(ProductService::class);
        $this->controller = new ProductController($this->productService);
        
        // Mock global variables
        $_GET = [];
        $_POST = [];
        $_SERVER = ['REQUEST_METHOD' => 'GET'];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Reset global variables
        $_GET = [];
        $_POST = [];
        $_SERVER = [];
    }

    public function testIndexReturnsProductsList()
    {
        // Arrange
        $expectedProducts = [
            new Product(['id' => 1, 'nombre' => 'Product 1']),
            new Product(['id' => 2, 'nombre' => 'Product 2'])
        ];

        $this->productService
            ->expects($this->once())
            ->method('getActiveProducts')
            ->willReturn($expectedProducts);

        // Act
        ob_start();
        $this->controller->index();
        $output = ob_get_clean();

        // Assert
        $this->assertStringContainsString('Product 1', $output);
        $this->assertStringContainsString('Product 2', $output);
    }

    public function testCreateProductSuccess()
    {
        // Arrange
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'nombre' => 'New Product',
            'precio' => '25.00',
            'categoria_id' => '1'
        ];

        $createdProduct = new Product([
            'id' => 123,
            'nombre' => 'New Product',
            'precio' => 25.00
        ]);

        $this->productService
            ->expects($this->once())
            ->method('createProduct')
            ->with($this->callback(function($data) {
                return $data['nombre'] === 'New Product' &&
                       $data['precio'] === 25.00 &&
                       $data['categoria_id'] === 1;
            }))
            ->willReturn($createdProduct);

        // Act
        ob_start();
        $this->controller->create();
        $output = ob_get_clean();

        // Assert
        $response = json_decode($output, true);
        $this->assertTrue($response['success']);
        $this->assertEquals(123, $response['product_id']);
    }

    public function testCreateProductValidationError()
    {
        // Arrange
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'nombre' => '', // Invalid: empty name
            'precio' => '25.00'
        ];

        $this->productService
            ->expects($this->once())
            ->method('createProduct')
            ->willThrowException(new ValidationException('Product name is required'));

        // Act
        ob_start();
        $this->controller->create();
        $output = ob_get_clean();

        // Assert
        $response = json_decode($output, true);
        $this->assertFalse($response['success']);
        $this->assertEquals('Product name is required', $response['message']);
    }
}
```

---

## 🔗 Integration Testing

### Database Integration Tests

```php
<?php
// tests/Integration/Database/ProductRepositoryTest.php

declare(strict_types=1);

use App\Repositories\ProductRepository;
use App\Database\Connection;
use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductRepositoryTest extends TestCase
{
    private ProductRepository $repository;
    private PDO $db;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Usar base de datos de testing
        $this->db = new PDO('sqlite::memory:');
        $this->createTestSchema();
        $this->repository = new ProductRepository($this->db);
    }

    private function createTestSchema(): void
    {
        $sql = "
            CREATE TABLE productos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL,
                descripcion TEXT,
                precio DECIMAL(10,2) NOT NULL,
                categoria_id INTEGER,
                imagen_url TEXT,
                activo BOOLEAN DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE categorias (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL,
                activo BOOLEAN DEFAULT 1
            );

            INSERT INTO categorias (id, nombre) VALUES (1, 'Bebidas');
            INSERT INTO categorias (id, nombre) VALUES (2, 'Snacks');
        ";

        $this->db->exec($sql);
    }

    public function testCreateProduct()
    {
        // Arrange
        $productData = [
            'nombre' => 'Coca Cola',
            'descripcion' => 'Bebida refrescante',
            'precio' => 15.50,
            'categoria_id' => 1
        ];

        // Act
        $product = $this->repository->create($productData);

        // Assert
        $this->assertInstanceOf(Product::class, $product);
        $this->assertNotNull($product->getId());
        $this->assertEquals('Coca Cola', $product->getNombre());
        $this->assertEquals(15.50, $product->getPrecio());

        // Verify in database
        $stmt = $this->db->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$product->getId()]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertEquals('Coca Cola', $row['nombre']);
        $this->assertEquals(15.50, $row['precio']);
    }

    public function testFindById()
    {
        // Arrange
        $stmt = $this->db->prepare("
            INSERT INTO productos (nombre, precio, categoria_id) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute(['Test Product', 20.0, 1]);
        $productId = $this->db->lastInsertId();

        // Act
        $product = $this->repository->findById($productId);

        // Assert
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($productId, $product->getId());
        $this->assertEquals('Test Product', $product->getNombre());
        $this->assertEquals(20.0, $product->getPrecio());
    }

    public function testFindByIdNotFound()
    {
        // Act
        $product = $this->repository->findById(999);

        // Assert
        $this->assertNull($product);
    }

    public function testFindByCategory()
    {
        // Arrange
        $this->db->exec("
            INSERT INTO productos (nombre, precio, categoria_id) VALUES 
            ('Producto 1', 10.0, 1),
            ('Producto 2', 15.0, 1),
            ('Producto 3', 20.0, 2)
        ");

        // Act
        $products = $this->repository->findByCategory(1);

        // Assert
        $this->assertCount(2, $products);
        $this->assertContainsOnlyInstancesOf(Product::class, $products);
        
        foreach ($products as $product) {
            $this->assertEquals(1, $product->getCategoriaId());
        }
    }

    public function testUpdateProduct()
    {
        // Arrange
        $stmt = $this->db->prepare("
            INSERT INTO productos (nombre, precio, categoria_id) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute(['Original Name', 10.0, 1]);
        $productId = $this->db->lastInsertId();

        $updateData = [
            'nombre' => 'Updated Name',
            'precio' => 25.0
        ];

        // Act
        $updated = $this->repository->update($productId, $updateData);

        // Assert
        $this->assertTrue($updated);

        $product = $this->repository->findById($productId);
        $this->assertEquals('Updated Name', $product->getNombre());
        $this->assertEquals(25.0, $product->getPrecio());
    }

    public function testDeleteProduct()
    {
        // Arrange
        $stmt = $this->db->prepare("
            INSERT INTO productos (nombre, precio, categoria_id) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute(['To Delete', 10.0, 1]);
        $productId = $this->db->lastInsertId();

        // Act
        $deleted = $this->repository->delete($productId);

        // Assert
        $this->assertTrue($deleted);
        $this->assertNull($this->repository->findById($productId));
    }
}
```

### API Integration Tests

```php
<?php
// tests/Integration/API/ProductAPITest.php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class ProductAPITest extends TestCase
{
    private string $baseUrl = 'http://localhost:8000/api/v1';
    private array $headers = ['Content-Type: application/json'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupTestData();
    }

    private function setupTestData(): void
    {
        // Insert test data via API or direct DB access
        $this->makeRequest('POST', '/products', [
            'nombre' => 'Test Product API',
            'precio' => 15.99,
            'categoria_id' => 1
        ]);
    }

    public function testGetProducts()
    {
        // Act
        $response = $this->makeRequest('GET', '/products');

        // Assert
        $this->assertEquals(200, $response['status']);
        $this->assertArrayHasKey('products', $response['body']);
        $this->assertIsArray($response['body']['products']);
    }

    public function testGetProductById()
    {
        // Arrange
        $createResponse = $this->makeRequest('POST', '/products', [
            'nombre' => 'API Test Product',
            'precio' => 29.99,
            'categoria_id' => 1
        ]);
        $productId = $createResponse['body']['id'];

        // Act
        $response = $this->makeRequest('GET', "/products/$productId");

        // Assert
        $this->assertEquals(200, $response['status']);
        $this->assertEquals($productId, $response['body']['id']);
        $this->assertEquals('API Test Product', $response['body']['nombre']);
        $this->assertEquals(29.99, $response['body']['precio']);
    }

    public function testCreateProduct()
    {
        // Arrange
        $productData = [
            'nombre' => 'New API Product',
            'descripcion' => 'Created via API',
            'precio' => 45.50,
            'categoria_id' => 2
        ];

        // Act
        $response = $this->makeRequest('POST', '/products', $productData);

        // Assert
        $this->assertEquals(201, $response['status']);
        $this->assertArrayHasKey('id', $response['body']);
        $this->assertEquals('New API Product', $response['body']['nombre']);
        $this->assertEquals(45.50, $response['body']['precio']);
    }

    public function testCreateProductValidationError()
    {
        // Arrange - Missing required field
        $invalidData = [
            'descripcion' => 'Missing name',
            'precio' => 10.0
        ];

        // Act
        $response = $this->makeRequest('POST', '/products', $invalidData);

        // Assert
        $this->assertEquals(400, $response['status']);
        $this->assertArrayHasKey('errors', $response['body']);
        $this->assertStringContainsString('nombre', $response['body']['errors']['nombre']);
    }

    public function testUpdateProduct()
    {
        // Arrange
        $createResponse = $this->makeRequest('POST', '/products', [
            'nombre' => 'To Update',
            'precio' => 10.0,
            'categoria_id' => 1
        ]);
        $productId = $createResponse['body']['id'];

        $updateData = [
            'nombre' => 'Updated Product',
            'precio' => 25.0
        ];

        // Act
        $response = $this->makeRequest('PUT', "/products/$productId", $updateData);

        // Assert
        $this->assertEquals(200, $response['status']);
        $this->assertEquals('Updated Product', $response['body']['nombre']);
        $this->assertEquals(25.0, $response['body']['precio']);
    }

    public function testDeleteProduct()
    {
        // Arrange
        $createResponse = $this->makeRequest('POST', '/products', [
            'nombre' => 'To Delete',
            'precio' => 5.0,
            'categoria_id' => 1
        ]);
        $productId = $createResponse['body']['id'];

        // Act
        $response = $this->makeRequest('DELETE', "/products/$productId");

        // Assert
        $this->assertEquals(204, $response['status']);

        // Verify product is deleted
        $getResponse = $this->makeRequest('GET', "/products/$productId");
        $this->assertEquals(404, $getResponse['status']);
    }

    public function testGetProductsWithFilters()
    {
        // Arrange - Create products in different categories
        $this->makeRequest('POST', '/products', [
            'nombre' => 'Bebida Test',
            'precio' => 15.0,
            'categoria_id' => 1
        ]);
        $this->makeRequest('POST', '/products', [
            'nombre' => 'Snack Test',
            'precio' => 25.0,
            'categoria_id' => 2
        ]);

        // Act
        $response = $this->makeRequest('GET', '/products?categoria_id=1');

        // Assert
        $this->assertEquals(200, $response['status']);
        $products = $response['body']['products'];
        
        foreach ($products as $product) {
            $this->assertEquals(1, $product['categoria_id']);
        }
    }

    private function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . $endpoint;
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $this->headers,
            CURLOPT_TIMEOUT => 30
        ]);

        if (!empty($data) && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $statusCode,
            'body' => json_decode($response, true) ?: []
        ];
    }
}
```

---

## 🎭 Frontend Testing

### JavaScript Unit Tests (usando Jest)

```javascript
// tests/frontend/product-manager.test.js

import ProductManager from '../assets/js/modules/product-manager.js';

// Mock fetch
global.fetch = jest.fn();

describe('ProductManager', () => {
    let productManager;

    beforeEach(() => {
        productManager = new ProductManager('/api/v1');
        fetch.mockClear();
    });

    describe('getProducts', () => {
        test('should fetch products successfully', async () => {
            // Arrange
            const mockProducts = [
                { id: 1, nombre: 'Product 1', precio: 10.0 },
                { id: 2, nombre: 'Product 2', precio: 15.0 }
            ];

            fetch.mockResolvedValueOnce({
                ok: true,
                json: async () => ({ products: mockProducts })
            });

            // Act
            const result = await productManager.getProducts();

            // Assert
            expect(fetch).toHaveBeenCalledWith('/api/v1/products?');
            expect(result).toEqual(mockProducts);
            expect(productManager.products).toEqual(mockProducts);
        });

        test('should handle fetch error', async () => {
            // Arrange
            fetch.mockResolvedValueOnce({
                ok: false,
                status: 500,
                statusText: 'Internal Server Error'
            });

            // Act & Assert
            await expect(productManager.getProducts()).rejects.toThrow(
                'HTTP 500: Internal Server Error'
            );
        });

        test('should apply filters', async () => {
            // Arrange
            const filters = { categoria_id: 1, precio_min: 10 };
            
            fetch.mockResolvedValueOnce({
                ok: true,
                json: async () => ({ products: [] })
            });

            // Act
            await productManager.getProducts(filters);

            // Assert
            expect(fetch).toHaveBeenCalledWith(
                '/api/v1/products?categoria_id=1&precio_min=10'
            );
        });
    });

    describe('renderProducts', () => {
        beforeEach(() => {
            // Setup DOM
            document.body.innerHTML = '<div id="products-container"></div>';
            
            productManager.products = [
                { 
                    id: 1, 
                    nombre: 'Test Product', 
                    precio: 25.99,
                    imagen_url: '/images/test.jpg'
                }
            ];
        });

        test('should render products correctly', () => {
            // Act
            productManager.renderProducts('products-container');

            // Assert
            const container = document.getElementById('products-container');
            expect(container.innerHTML).toContain('Test Product');
            expect(container.innerHTML).toContain('$25.99');
            expect(container.innerHTML).toContain('/images/test.jpg');
        });

        test('should handle missing container', () => {
            // Arrange
            const consoleSpy = jest.spyOn(console, 'warn').mockImplementation();

            // Act
            productManager.renderProducts('non-existent');

            // Assert
            expect(consoleSpy).toHaveBeenCalledWith('Container non-existent not found');
            consoleSpy.mockRestore();
        });

        test('should use default image for products without image', () => {
            // Arrange
            productManager.products = [
                { id: 1, nombre: 'No Image Product', precio: 10.0 }
            ];

            // Act
            productManager.renderProducts('products-container');

            // Assert
            const container = document.getElementById('products-container');
            expect(container.innerHTML).toContain('/assets/images/no-image.png');
        });
    });

    describe('addToCart', () => {
        test('should add product to cart', () => {
            // Arrange
            productManager.cart = [];
            productManager.products = [
                { id: 1, nombre: 'Test Product', precio: 15.0 }
            ];

            // Act
            productManager.addToCart(1);

            // Assert
            expect(productManager.cart).toHaveLength(1);
            expect(productManager.cart[0]).toEqual({
                product_id: 1,
                nombre: 'Test Product',
                precio: 15.0,
                cantidad: 1
            });
        });

        test('should increase quantity if product already in cart', () => {
            // Arrange
            productManager.cart = [
                { product_id: 1, cantidad: 1, precio: 15.0 }
            ];

            // Act
            productManager.addToCart(1);

            // Assert
            expect(productManager.cart).toHaveLength(1);
            expect(productManager.cart[0].cantidad).toBe(2);
        });
    });
});
```

### End-to-End Testing (usando Cypress)

```javascript
// cypress/integration/product-management.spec.js

describe('Product Management', () => {
    beforeEach(() => {
        // Setup test data
        cy.task('seedDatabase');
        cy.visit('/dashboard');
        cy.login('admin@test.com', 'password');
    });

    it('should display products list', () => {
        cy.visit('/productos');
        
        cy.get('[data-testid="products-table"]').should('be.visible');
        cy.get('[data-testid="product-row"]').should('have.length.greaterThan', 0);
    });

    it('should create new product', () => {
        cy.visit('/productos/nuevo');
        
        // Fill form
        cy.get('#nombre').type('Nuevo Producto Cypress');
        cy.get('#descripcion').type('Descripción de prueba');
        cy.get('#precio').type('25.99');
        cy.get('#categoria_id').select('Bebidas');
        
        // Submit form
        cy.get('[data-testid="submit-button"]').click();
        
        // Verify success
        cy.get('[data-testid="success-message"]')
          .should('be.visible')
          .and('contain', 'Producto creado exitosamente');
          
        // Verify in list
        cy.visit('/productos');
        cy.get('[data-testid="products-table"]')
          .should('contain', 'Nuevo Producto Cypress');
    });

    it('should edit existing product', () => {
        cy.visit('/productos');
        
        // Click edit on first product
        cy.get('[data-testid="edit-button"]').first().click();
        
        // Update name
        cy.get('#nombre').clear().type('Producto Editado');
        cy.get('[data-testid="submit-button"]').click();
        
        // Verify update
        cy.get('[data-testid="success-message"]')
          .should('contain', 'Producto actualizado');
    });

    it('should delete product', () => {
        cy.visit('/productos');
        
        // Get initial count
        cy.get('[data-testid="product-row"]').then($rows => {
            const initialCount = $rows.length;
            
            // Delete first product
            cy.get('[data-testid="delete-button"]').first().click();
            cy.get('[data-testid="confirm-delete"]').click();
            
            // Verify deletion
            cy.get('[data-testid="product-row"]')
              .should('have.length', initialCount - 1);
        });
    });

    it('should search products', () => {
        cy.visit('/productos');
        
        // Search for specific product
        cy.get('[data-testid="search-input"]').type('Coca');
        cy.get('[data-testid="search-button"]').click();
        
        // Verify filtered results
        cy.get('[data-testid="product-row"]').each($row => {
            cy.wrap($row).should('contain.text', 'Coca');
        });
    });

    it('should filter by category', () => {
        cy.visit('/productos');
        
        // Apply category filter
        cy.get('[data-testid="category-filter"]').select('Bebidas');
        
        // Verify all results are from selected category
        cy.get('[data-testid="product-row"]').each($row => {
            cy.wrap($row).find('[data-testid="category"]')
              .should('contain', 'Bebidas');
        });
    });
});
```

---

## 📊 Code Coverage

### Configuración de Coverage

```bash
# Generar coverage report
./vendor/bin/phpunit --coverage-html coverage/

# Coverage con filtros específicos
./vendor/bin/phpunit --coverage-html coverage/ --testsuite Unit

# Coverage en formato texto
./vendor/bin/phpunit --coverage-text

# Coverage mínimo requerido
./vendor/bin/phpunit --coverage-text --coverage-clover coverage.xml
```

### Coverage Goals

```xml
<!-- phpunit.xml - Coverage thresholds -->
<coverage>
    <include>
        <directory suffix=".php">src</directory>
    </include>
    <exclude>
        <directory>src/Views</directory>
        <file>src/Config/AppConfig.php</file>
    </exclude>
    <report>
        <html outputDirectory="coverage/html" lowUpperBound="50" highLowerBound="80"/>
        <text outputFile="coverage/coverage.txt" showUncoveredFiles="true"/>
    </report>
</coverage>
```

### Coverage Analysis Script

```php
#!/usr/bin/env php
<?php
// scripts/coverage-analysis.php

require_once __DIR__ . '/../vendor/autoload.php';

class CoverageAnalyzer {
    private $coverageFile;
    private $thresholds;

    public function __construct($coverageFile = 'coverage/clover.xml') {
        $this->coverageFile = $coverageFile;
        $this->thresholds = [
            'line' => 80,
            'method' => 90,
            'class' => 85
        ];
    }

    public function analyze() {
        if (!file_exists($this->coverageFile)) {
            echo "❌ Coverage file not found: {$this->coverageFile}\n";
            echo "Run: ./vendor/bin/phpunit --coverage-clover {$this->coverageFile}\n";
            return 1;
        }

        $xml = simplexml_load_file($this->coverageFile);
        $metrics = $xml->xpath('//metrics')[0];

        $coverage = [
            'line' => $this->calculatePercentage($metrics['coveredstatements'], $metrics['statements']),
            'method' => $this->calculatePercentage($metrics['coveredmethods'], $metrics['methods']),
            'class' => $this->calculatePercentage($metrics['coveredclasses'], $metrics['classes'])
        ];

        $this->printReport($coverage);
        return $this->checkThresholds($coverage) ? 0 : 1;
    }

    private function calculatePercentage($covered, $total) {
        return $total > 0 ? round(($covered / $total) * 100, 2) : 0;
    }

    private function printReport($coverage) {
        echo "📊 Code Coverage Report\n";
        echo "========================\n\n";

        foreach ($coverage as $type => $percentage) {
            $status = $percentage >= $this->thresholds[$type] ? '✅' : '❌';
            $threshold = $this->thresholds[$type];
            
            echo "$status " . ucfirst($type) . " Coverage: $percentage% (threshold: $threshold%)\n";
        }

        echo "\n";
    }

    private function checkThresholds($coverage) {
        $passed = true;
        foreach ($coverage as $type => $percentage) {
            if ($percentage < $this->thresholds[$type]) {
                $passed = false;
                echo "❌ $type coverage below threshold: $percentage% < {$this->thresholds[$type]}%\n";
            }
        }

        if ($passed) {
            echo "🎉 All coverage thresholds met!\n";
        }

        return $passed;
    }
}

$analyzer = new CoverageAnalyzer();
exit($analyzer->analyze());
```

---

## 🚀 Performance Testing

### Load Testing con Artillery

```yaml
# artillery-config.yml
config:
  target: 'http://localhost:8000'
  phases:
    - duration: 60
      arrivalRate: 10
      name: "Warm up"
    - duration: 120
      arrivalRate: 50
      name: "Load test"
    - duration: 60
      arrivalRate: 100
      name: "Stress test"

scenarios:
  - name: "Product browsing"
    weight: 60
    flow:
      - get:
          url: "/productos"
      - think: 3
      - get:
          url: "/api/v1/products"
      - think: 2
      - get:
          url: "/api/v1/products/{{ $randomInt(1, 100) }}"

  - name: "User authentication"
    weight: 20
    flow:
      - post:
          url: "/auth/login"
          json:
            email: "test@example.com"
            password: "password"
      - get:
          url: "/dashboard"

  - name: "Product search"
    weight: 20
    flow:
      - get:
          url: "/api/v1/products"
          qs:
            search: "{{ $randomString() }}"
            categoria_id: "{{ $randomInt(1, 5) }}"
```

### Database Performance Tests

```php
<?php
// tests/Performance/DatabasePerformanceTest.php

class DatabasePerformanceTest extends TestCase
{
    private $db;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->db = new PDO('sqlite::memory:');
        $this->setupLargeDataset();
    }

    public function testQueryPerformance()
    {
        $startTime = microtime(true);
        
        // Test query performance
        $stmt = $this->db->prepare("
            SELECT p.*, c.nombre as categoria 
            FROM productos p 
            JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.activo = 1 
            ORDER BY p.nombre
        ");
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        $executionTime = microtime(true) - $startTime;
        
        // Assert performance benchmark
        $this->assertLessThan(0.1, $executionTime, 'Query should execute in less than 100ms');
        $this->assertGreaterThan(0, count($results), 'Query should return results');
    }

    public function testInsertPerformance()
    {
        $startTime = microtime(true);
        
        // Test bulk insert performance
        $this->db->beginTransaction();
        $stmt = $this->db->prepare("INSERT INTO productos (nombre, precio, categoria_id) VALUES (?, ?, ?)");
        
        for ($i = 0; $i < 1000; $i++) {
            $stmt->execute(["Product $i", rand(10, 100), rand(1, 5)]);
        }
        
        $this->db->commit();
        $executionTime = microtime(true) - $startTime;
        
        // Assert bulk insert performance
        $this->assertLessThan(1.0, $executionTime, 'Bulk insert should complete in less than 1 second');
    }

    private function setupLargeDataset()
    {
        // Create tables and populate with test data
        $this->db->exec("
            CREATE TABLE categorias (id INTEGER PRIMARY KEY, nombre TEXT);
            CREATE TABLE productos (
                id INTEGER PRIMARY KEY,
                nombre TEXT,
                precio DECIMAL(10,2),
                categoria_id INTEGER,
                activo BOOLEAN DEFAULT 1,
                FOREIGN KEY (categoria_id) REFERENCES categorias(id)
            );
            
            INSERT INTO categorias (nombre) VALUES 
            ('Bebidas'), ('Snacks'), ('Dulces'), ('Comida'), ('Otros');
        ");

        // Insert test products
        $stmt = $this->db->prepare("INSERT INTO productos (nombre, precio, categoria_id) VALUES (?, ?, ?)");
        for ($i = 1; $i <= 10000; $i++) {
            $stmt->execute(["Test Product $i", rand(10, 100), rand(1, 5)]);
        }
    }
}
```

---

## 🔒 Security Testing

### Security Test Suite

```php
<?php
// tests/Security/SecurityTest.php

class SecurityTest extends TestCase
{
    public function testSQLInjectionPrevention()
    {
        // Test SQL injection attempts
        $maliciousInputs = [
            "'; DROP TABLE productos; --",
            "1' OR '1'='1",
            "' UNION SELECT * FROM usuarios --"
        ];

        foreach ($maliciousInputs as $input) {
            $response = $this->makeAPIRequest('GET', "/api/v1/products/$input");
            
            // Should return 404 or 400, not 500 (which could indicate SQL error)
            $this->assertContains($response['status'], [400, 404]);
        }
    }

    public function testXSSPrevention()
    {
        $xssPayloads = [
            '<script>alert("XSS")</script>',
            '<img src="x" onerror="alert(1)">',
            'javascript:alert(1)'
        ];

        foreach ($xssPayloads as $payload) {
            $response = $this->makeAPIRequest('POST', '/api/v1/products', [
                'nombre' => $payload,
                'precio' => 10.0,
                'categoria_id' => 1
            ]);

            if ($response['status'] === 201) {
                // If created, ensure output is properly escaped
                $getResponse = $this->makeAPIRequest('GET', "/api/v1/products/{$response['body']['id']}");
                $this->assertStringNotContainsString('<script>', $getResponse['body']['nombre']);
            }
        }
    }

    public function testCSRFProtection()
    {
        // Test CSRF protection on state-changing operations
        $response = $this->makeRequest('POST', '/productos/crear', [
            'nombre' => 'Test Product',
            'precio' => 15.0
        ], false); // Without CSRF token

        $this->assertEquals(403, $response['status']);
    }

    public function testAuthenticationRequired()
    {
        $protectedEndpoints = [
            '/dashboard',
            '/productos/crear',
            '/api/v1/products'
        ];

        foreach ($protectedEndpoints as $endpoint) {
            $response = $this->makeRequestWithoutAuth('GET', $endpoint);
            $this->assertContains($response['status'], [401, 403]);
        }
    }

    public function testRateLimiting()
    {
        // Test rate limiting on login endpoint
        for ($i = 0; $i < 10; $i++) {
            $response = $this->makeRequest('POST', '/auth/login', [
                'email' => 'wrong@email.com',
                'password' => 'wrongpassword'
            ]);
        }

        // Should be rate limited after multiple failed attempts
        $finalResponse = $this->makeRequest('POST', '/auth/login', [
            'email' => 'wrong@email.com',
            'password' => 'wrongpassword'
        ]);

        $this->assertEquals(429, $finalResponse['status']);
    }

    public function testFileUploadSecurity()
    {
        $maliciousFiles = [
            ['name' => 'malicious.php', 'content' => '<?php system($_GET["cmd"]); ?>'],
            ['name' => 'exploit.phtml', 'content' => '<?php phpinfo(); ?>'],
            ['name' => '../../../etc/passwd', 'content' => 'root:x:0:0:root:/root:/bin/bash']
        ];

        foreach ($maliciousFiles as $file) {
            $response = $this->uploadFile('/api/v1/upload', $file);
            
            // Should reject malicious files
            $this->assertContains($response['status'], [400, 415]);
        }
    }

    private function makeRequestWithoutAuth($method, $endpoint)
    {
        // Implementation for unauthenticated requests
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost:8000$endpoint");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['status' => $status, 'body' => json_decode($response, true)];
    }
}
```

---

## 🤖 CI/CD Integration

### GitHub Actions Workflow

```yaml
# .github/workflows/tests.yml
name: Tests

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest

    strategy:
      matrix:
        php-version: [8.0, 8.1, 8.2]

    steps:
    - uses: actions/checkout@v3

    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: ${{ matrix.php-version }}
        extensions: pdo, pdo_sqlite, mbstring, xml, curl, zip, gd
        coverage: xdebug

    - name: Cache Composer packages
      id: composer-cache
      uses: actions/cache@v3
      with:
        path: vendor
        key: ${{ runner.os }}-php-${{ hashFiles('**/composer.lock') }}
        restore-keys: |
          ${{ runner.os }}-php-

    - name: Install dependencies
      run: composer install --prefer-dist --no-progress

    - name: Create test database
      run: |
        mkdir -p tests/database
        cp data/snackshop.db tests/database/test.db

    - name: Run unit tests
      run: ./vendor/bin/phpunit --testsuite=Unit

    - name: Run integration tests
      run: ./vendor/bin/phpunit --testsuite=Integration

    - name: Generate coverage report
      run: ./vendor/bin/phpunit --coverage-clover coverage.xml

    - name: Check coverage thresholds
      run: php scripts/coverage-analysis.php

    - name: Upload coverage to Codecov
      uses: codecov/codecov-action@v3
      with:
        file: ./coverage.xml
        flags: unittests
        name: codecov-umbrella

  security:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: 8.1

    - name: Install dependencies
      run: composer install --prefer-dist --no-progress

    - name: Run security tests
      run: ./vendor/bin/phpunit tests/Security/

    - name: Security audit
      run: composer audit

  lint:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: 8.1

    - name: Install dependencies
      run: composer install --prefer-dist --no-progress

    - name: Check code style
      run: ./vendor/bin/php-cs-fixer fix --dry-run --diff

    - name: Run static analysis
      run: ./vendor/bin/phpstan analyse src/ --level=5
```

### Test Commands for Development

```bash
# Ejecutar todos los tests
composer test

# Tests específicos
composer test -- --testsuite=Unit
composer test -- --testsuite=Integration
composer test -- tests/Unit/Services/ProductServiceTest.php

# Tests con coverage
composer test-coverage

# Tests en modo watch (con entr)
find tests/ -name "*.php" | entr -c composer test

# Linting y análisis
composer lint
composer analyze

# Security tests
composer test -- tests/Security/

# Performance tests
composer test -- tests/Performance/
```

---

## 🔗 Documentos Relacionados

- **[📖 Índice General](docs/INDEX.md)** — Navegación completa del manual
- **[🛠️ Development](DEVELOPMENT.md)** — Setup y herramientas de desarrollo
- **[🏗️ Architecture](ARCHITECTURE.md)** — Comprende la arquitectura para testing efectivo
- **[🔌 API](API.md)** — Endpoints para integration testing
- **[🗄️ Database](DATABASE.md)** — Esquemas para database testing
- **[🤝 Contributing](CONTRIBUTING.md)** — Proceso de contribución y testing

---

## 📞 Soporte

**¿Problemas con los tests?**
- **Setup Issues:** Ejecuta `php scripts/test-setup.php`
- **Coverage Problems:** Verifica configuración de Xdebug
- **Integration Failures:** Revisa conexión a base de datos de testing
- **CI/CD Issues:** Consulta logs de GitHub Actions

---

**[📖 Índice](docs/INDEX.md)** | **[🛠️ Ver Development](DEVELOPMENT.md)** | **[🏗️ Ver Arquitectura](ARCHITECTURE.md)** | **[🤝 Ver Contributing](CONTRIBUTING.md)**