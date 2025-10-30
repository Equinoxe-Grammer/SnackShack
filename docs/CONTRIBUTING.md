<a id="snackshop-guia-de-contribucion"></a>
<a id="-snackshop-guia-de-contribucion"></a>
# 🤝 SnackShop - Guía de Contribución
<!-- TOC -->
<a id="contenido"></a>
<a id="-contenido"></a>
## Contenido

- [🧭 Navegación](#-navegacion)
- [📋 Índice](#-indice)
- [📜 Código de Conducta](#-codigo-de-conducta)
  - [Nuestro Compromiso](#nuestro-compromiso)
  - [Nuestros Estándares](#nuestros-estandares)
  - [Aplicación](#aplicacion)
  - [Consecuencias](#consecuencias)
- [🚀 Cómo Contribuir](#-como-contribuir)
  - [Pasos Rápidos para Empezar](#pasos-rapidos-para-empezar)
  - [Tipos de Contribuciones Bienvenidas](#tipos-de-contribuciones-bienvenidas)
- [🛠️ Setup para Contribuidores](#-setup-para-contribuidores)
  - [Configuración Inicial](#configuracion-inicial)
  - [Herramientas Requeridas](#herramientas-requeridas)
  - [Configuración del Editor](#configuracion-del-editor)
- [📏 Estándares de Código](#-estandares-de-codigo)
  - [PHP Code Style (PSR-12 Extended)](#php-code-style-psr-12-extended)
  - [Naming Conventions](#naming-conventions)
  - [Comments & Documentation](#comments-documentation)
  - [Error Handling](#error-handling)
- [🔄 Proceso de Pull Request](#-proceso-de-pull-request)
  - [1. Preparación del PR](#1-preparacion-del-pr)
  - [2. Template de Pull Request](#2-template-de-pull-request)
- [🎯 Descripción](#-descripcion)
  - [Tipo de cambio](#tipo-de-cambio)
- [🧪 Cómo se ha probado](#-como-se-ha-probado)
- [📋 Checklist](#-checklist)
- [📷 Screenshots (si aplica)](#-screenshots-si-aplica)
- [🔗 Issues relacionados](#-issues-relacionados)
- [📝 Notas adicionales](#-notas-adicionales)
  - [3. Criterios para Aprobar PR](#3-criterios-para-aprobar-pr)
  - [4. Review Process](#4-review-process)
- [👥 Code Review Guidelines](#-code-review-guidelines)
  - [Para Reviewers](#para-reviewers)
- [Problema Específico](#problema-especifico)
- [Pregunta para Clarificar](#pregunta-para-clarificar)
- [Reconocimiento Positivo](#reconocimiento-positivo)
  - [Para Authors](#para-authors)
- [Aceptar Sugerencia](#aceptar-sugerencia)
- [Explicar Decisión](#explicar-decision)
- [Pedir Clarificación](#pedir-clarificacion)
- [Agradecer Feedback](#agradecer-feedback)
- [🎯 Tipos de Contribuciones](#-tipos-de-contribuciones)
  - [🐛 Bug Fixes](#-bug-fixes)
- [🐛 Bug Report](#-bug-report)
  - [Descripción](#descripcion)
  - [Pasos para Reproducir](#pasos-para-reproducir)
  - [Comportamiento Esperado](#comportamiento-esperado)
  - [Comportamiento Actual](#comportamiento-actual)
  - [Screenshots](#screenshots)
  - [Entorno](#entorno)
  - [Contexto Adicional](#contexto-adicional)
  - [✨ New Features](#-new-features)
- [✨ Feature Request](#-feature-request)
  - [¿Tu feature request está relacionada con un problema?](#tu-feature-request-esta-relacionada-con-un-problema)
  - [Describe la solución que te gustaría](#describe-la-solucion-que-te-gustaria)
  - [Describe alternativas que has considerado](#describe-alternativas-que-has-considerado)
  - [Contexto adicional](#contexto-adicional)
  - [Criterios de Aceptación](#criterios-de-aceptacion)
  - [📚 Documentation](#-documentation)
- [🗣️ Reportar Bugs](#-reportar-bugs)
  - [Severity Levels](#severity-levels)
  - [Bug Report Checklist](#bug-report-checklist)
- [💡 Solicitar Features](#-solicitar-features)
  - [Feature Evaluation Criteria](#feature-evaluation-criteria)
  - [Feature Request Process](#feature-request-process)
- [🏆 Reconocimientos](#-reconocimientos)
  - [Hall of Fame](#hall-of-fame)
  - [Contribution Recognition](#contribution-recognition)
  - [Levels of Recognition](#levels-of-recognition)
- [📞 Comunidad y Comunicación](#-comunidad-y-comunicacion)
  - [Canales de Comunicación](#canales-de-comunicacion)
  - [Meetings & Events](#meetings-events)
  - [Getting Help](#getting-help)
- [🔗 Documentos Relacionados](#-documentos-relacionados)
- [🙏 Agradecimientos](#-agradecimientos)
<!-- /TOC -->

**🏠 Ubicación:** `CONTRIBUTING.md`
**📅 Última actualización:** 29 de octubre, 2025
**🎯 Propósito:** Guía para contribuidores: código de conducta, pull requests, code style y review process

---

<a id="navegacion"></a>
<a id="-navegacion"></a>
## 🧭 Navegación

**[📖 Índice General](docs/INDEX.md)** | **[🏠 README](README.md)** | **[🛠️ Development](DEVELOPMENT.md)** | **[🧪 Testing](TESTING.md)**

---

<a id="indice"></a>
<a id="-indice"></a>
## 📋 Índice

- [Código de Conducta](#código-de-conducta)
- [Cómo Contribuir](#cómo-contribuir)
- [Setup para Contribuidores](#setup-para-contribuidores)
- [Estándares de Código](#estándares-de-código)
- [Proceso de Pull Request](#proceso-de-pull-request)
- [Code Review Guidelines](#code-review-guidelines)
- [Tipos de Contribuciones](#tipos-de-contribuciones)
- [Documentación](#documentation)
- [Reportar Bugs](#reportar-bugs)
- [Solicitar Features](#solicitar-features)
- [Comunidad y Comunicación](#comunidad-y-comunicación)
- [Reconocimientos](#reconocimientos)

---

<a id="codigo-de-conducta"></a>
<a id="-codigo-de-conducta"></a>
## 📜 Código de Conducta

<a id="nuestro-compromiso"></a>
<a id="-nuestro-compromiso"></a>
### Nuestro Compromiso

En el interés de fomentar un ambiente abierto y acogedor, nosotros como contribuidores y mantenedores nos comprometemos a hacer de la participación en nuestro proyecto y nuestra comunidad una experiencia libre de acoso para todos.

<a id="nuestros-estandares"></a>
<a id="-nuestros-estandares"></a>
### Nuestros Estándares

<a id="comportamientos-que-contribuyen-a-crear-un-ambiente-positivo"></a>
<a id="-comportamientos-que-contribuyen-a-crear-un-ambiente-positivo"></a>
#### ✅ Comportamientos que contribuyen a crear un ambiente positivo:

- **Respeto mutuo**: Usar un lenguaje acogedor e inclusivo
- **Diversidad**: Respetar diferentes puntos de vista y experiencias
- **Constructividad**: Enfocarse en lo que es mejor para la comunidad
- **Empatía**: Mostrar empatía hacia otros miembros de la comunidad
- **Colaboración**: Trabajar juntos hacia objetivos comunes

<a id="comportamientos-inaceptables"></a>
<a id="-comportamientos-inaceptables"></a>
#### ❌ Comportamientos inaceptables:

- Uso de lenguaje o imágenes sexualizadas y atención sexual no deseada
- Comentarios despectivos, ataques personales o políticos
- Acoso público o privado
- Publicar información privada de terceros sin permiso explícito
- Otras conductas que puedan considerarse inapropiadas en un entorno profesional

<a id="aplicacion"></a>
<a id="-aplicacion"></a>
### Aplicación

Los casos de comportamiento abusivo, acosador o inaceptable pueden ser reportados contactando al equipo del proyecto en [admin@snackshop-project.com]. Todas las quejas serán revisadas e investigadas.

<a id="consecuencias"></a>
<a id="-consecuencias"></a>
### Consecuencias

Los mantenedores del proyecto tienen el derecho y la responsabilidad de remover, editar o rechazar comentarios, commits, código, ediciones de documentación, issues y otras contribuciones que no estén alineadas con este Código de Conducta.

---

<a id="como-contribuir"></a>
<a id="-como-contribuir"></a>
## 🚀 Cómo Contribuir

<a id="pasos-rapidos-para-empezar"></a>
<a id="-pasos-rapidos-para-empezar"></a>
### Pasos Rápidos para Empezar

```bash
<a id="1-fork-el-repositorio-en-github"></a>
<a id="-1-fork-el-repositorio-en-github"></a>
# 1. Fork el repositorio en GitHub
<a id="2-clonar-tu-fork"></a>
<a id="-2-clonar-tu-fork"></a>
# 2. Clonar tu fork
git clone https://github.com/TU-USERNAME/SnackShack.git
cd SnackShack/SnackShop/www/Snackshop

<a id="3-configurar-remote-upstream"></a>
<a id="-3-configurar-remote-upstream"></a>
# 3. Configurar remote upstream
git remote add upstream https://github.com/Equinoxe-Grammer/SnackShack.git

<a id="4-crear-rama-para-tu-feature"></a>
<a id="-4-crear-rama-para-tu-feature"></a>
# 4. Crear rama para tu feature
git checkout -b feature/mi-nueva-feature

<a id="5-hacer-cambios-commit-y-push"></a>
<a id="-5-hacer-cambios-commit-y-push"></a>
# 5. Hacer cambios, commit y push
git add .
git commit -m "feat: agregar nueva funcionalidad"
git push origin feature/mi-nueva-feature

<a id="6-crear-pull-request-en-github"></a>
<a id="-6-crear-pull-request-en-github"></a>
# 6. Crear Pull Request en GitHub
```

<a id="tipos-de-contribuciones-bienvenidas"></a>
<a id="-tipos-de-contribuciones-bienvenidas"></a>
### Tipos de Contribuciones Bienvenidas

| Tipo | Descripción | Dificultad | Tiempo Estimado |
|------|-------------|------------|-----------------|
| 🐛 **Bug Fixes** | Corregir errores reportados | ⭐ | 1-4 horas |
| ✨ **Features** | Nuevas funcionalidades | ⭐⭐⭐ | 1-3 días |
| 📚 **Documentación** | Mejorar docs, README, comentarios | ⭐ | 2-8 horas |
| 🧪 **Tests** | Agregar/mejorar test coverage | ⭐⭐ | 4-8 horas |
| 🎨 **UI/UX** | Mejoras de interfaz | ⭐⭐ | 1-2 días |
| ⚡ **Performance** | Optimizaciones | ⭐⭐⭐ | 2-5 días |
| 🔒 **Security** | Mejoras de seguridad | ⭐⭐⭐⭐ | 3-7 días |

---

<a id="setup-para-contribuidores"></a>
<a id="-setup-para-contribuidores"></a>
## 🛠️ Setup para Contribuidores

<a id="configuracion-inicial"></a>
<a id="-configuracion-inicial"></a>
### Configuración Inicial

```bash
<a id="1-instalar-dependencias-de-desarrollo"></a>
<a id="-1-instalar-dependencias-de-desarrollo"></a>
# 1. Instalar dependencias de desarrollo
composer install
npm install  # Si hay assets frontend

<a id="2-configurar-hooks-de-git"></a>
<a id="-2-configurar-hooks-de-git"></a>
# 2. Configurar hooks de Git
cp .githooks/pre-commit .git/hooks/pre-commit
chmod +x .git/hooks/pre-commit

<a id="3-configurar-entorno-de-desarrollo"></a>
<a id="-3-configurar-entorno-de-desarrollo"></a>
# 3. Configurar entorno de desarrollo
cp .env.example .env.development
<a id="editar-envdevelopment-segun-necesidades"></a>
<a id="-editar-envdevelopment-segun-necesidades"></a>
# Editar .env.development según necesidades

<a id="4-ejecutar-setup-de-desarrollo"></a>
<a id="-4-ejecutar-setup-de-desarrollo"></a>
# 4. Ejecutar setup de desarrollo
php scripts/dev-setup.php

<a id="5-verificar-que-todo-funciona"></a>
<a id="-5-verificar-que-todo-funciona"></a>
# 5. Verificar que todo funciona
composer run test
composer run lint
```

<a id="herramientas-requeridas"></a>
<a id="-herramientas-requeridas"></a>
### Herramientas Requeridas

```bash
<a id="verificar-herramientas-instaladas"></a>
<a id="-verificar-herramientas-instaladas"></a>
# Verificar herramientas instaladas
php --version          # PHP 8.1+
composer --version     # Composer 2.0+
git --version          # Git 2.20+
node --version         # Node.js 16+ (opcional)

<a id="herramientas-de-desarrollo-instaladas-via-composer"></a>
<a id="-herramientas-de-desarrollo-instaladas-via-composer"></a>
# Herramientas de desarrollo (instaladas via Composer)
./vendor/bin/phpunit --version      # PHPUnit para testing
./vendor/bin/php-cs-fixer --version # PHP CS Fixer para estilo
./vendor/bin/phpstan --version      # PHPStan para análisis estático
```

<a id="configuracion-del-editor"></a>
<a id="-configuracion-del-editor"></a>
### Configuración del Editor

<a id="vscode-settings"></a>
<a id="-vscode-settings"></a>
#### VSCode Settings

```json
// .vscode/settings.json
{
    "php.validate.executablePath": "/usr/bin/php",
    "php.format.rules.indentBraces": false,
    "php.format.rules.blankLineBeforeDeclaration": false,
    "files.associations": {
        "*.php": "php"
    },
    "editor.insertSpaces": true,
    "editor.tabSize": 4,
    "editor.detectIndentation": false,
    "files.trimTrailingWhitespace": true,
    "files.insertFinalNewline": true,
    "[php]": {
        "editor.defaultFormatter": "bmewburn.vscode-intelephense-client",
        "editor.tabSize": 4
    },
    "[javascript]": {
        "editor.tabSize": 2
    },
    "[css]": {
        "editor.tabSize": 2
    }
}
```

<a id="extensions-recomendadas"></a>
<a id="-extensions-recomendadas"></a>
#### Extensions Recomendadas

```json
// .vscode/extensions.json
{
    "recommendations": [
        "bmewburn.vscode-intelephense-client",
        "xdebug.php-debug",
        "recca0120.vscode-phpunit",
        "valeryanm.vscode-phpsab",
        "ms-vscode.vscode-json",
        "bradlc.vscode-tailwindcss",
        "formulahendry.auto-rename-tag"
    ]
}
```

---

<a id="estandares-de-codigo"></a>
<a id="-estandares-de-codigo"></a>
## 📏 Estándares de Código

<a id="php-code-style-psr-12-extended"></a>
<a id="-php-code-style-psr-12-extended"></a>
### PHP Code Style (PSR-12 Extended)

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use App\Exceptions\ValidationException;
use Psr\Log\LoggerInterface;

/**
 * Servicio para gestión de productos
 *
 * Maneja la lógica de negocio relacionada con productos,
 * incluyendo validación, cálculos y transformaciones.
 *
 * @package App\Services
 * @author  Equipo SnackShop
 * @since   1.0.0
 */
final class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private LoggerInterface $logger
    ) {
    }

    /**
     * Crea un nuevo producto
     *
     * @param array<string, mixed> $data Datos del producto
     * @return Product El producto creado
     * @throws ValidationException Si los datos son inválidos
     */
    public function createProduct(array $data): Product
    {
        // Validar datos de entrada
        $validatedData = $this->validateProductData($data);

        try {
            // Crear producto
            $product = $this->productRepository->create($validatedData);

            // Log del evento
            $this->logger->info('Product created successfully', [
                'product_id' => $product->getId(),
                'name' => $product->getNombre()
            ]);

            return $product;
        } catch (\Exception $e) {
            $this->logger->error('Failed to create product', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Valida los datos del producto
     */
    private function validateProductData(array $data): array
    {
        $rules = [
            'nombre' => 'required|string|max:100',
            'precio' => 'required|numeric|min:0.01',
            'categoria_id' => 'required|integer|exists:categorias,id'
        ];

        // Implementación de validación...
        return $data;
    }
}
```

<a id="naming-conventions"></a>
<a id="-naming-conventions"></a>
### Naming Conventions

```php
// ✅ Buenos nombres
class ProductService           // Clases: PascalCase
interface PaymentProcessor     // Interfaces: PascalCase + descriptivo
trait Timestampable           // Traits: PascalCase + able/ible

public function getUserById($id)          // Métodos: camelCase
private $connectionPool                   // Propiedades: camelCase
const MAX_RETRY_ATTEMPTS = 3             // Constantes: SNAKE_CASE

// ✅ Variables descriptivas
$activeProducts = $this->getActiveProducts();
$userEmail = $request->getEmail();
$totalPrice = $calculator->calculateTotal($items);

// ❌ Evitar nombres genéricos
$data = [];     // ❌ Muy genérico
$temp = null;   // ❌ No descriptivo
$var1 = '';     // ❌ Sin significado

// ✅ Nombres específicos
$productData = [];      // ✅ Específico
$temporaryToken = null; // ✅ Descriptivo
$userName = '';         // ✅ Con significado
```

<a id="comments-documentation"></a>
<a id="-comments-documentation"></a>
### Comments & Documentation

```php
<?php

/**
 * Calcula el precio total de una venta
 *
 * Este método procesa una lista de items, aplica descuentos,
 * calcula impuestos y retorna el desglose completo del precio.
 *
 * @param array<int, array{product_id: int, quantity: int}> $items Lista de items
 * @param float $discountPercent Descuento en porcentaje (0-100)
 * @param bool $includeTax Si incluir impuestos en el cálculo
 * @return array{subtotal: float, discount: float, tax: float, total: float}
 *
 * @throws InvalidArgumentException Si algún item es inválido
 * @throws ProductNotFoundException Si un producto no existe
 *
 * @example
 * $items = [
 *     ['product_id' => 1, 'quantity' => 2],
 *     ['product_id' => 3, 'quantity' => 1]
 * ];
 * $result = $calculator->calculateTotal($items, 10.0, true);
 * // $result = ['subtotal' => 50.0, 'discount' => 5.0, 'tax' => 7.65, 'total' => 52.65]
 */
public function calculateTotal(array $items, float $discountPercent = 0.0, bool $includeTax = true): array
{
    // Validar parámetros de entrada
    if (empty($items)) {
        throw new InvalidArgumentException('Items array cannot be empty');
    }

    $subtotal = 0.0;

    // Calcular subtotal de todos los items
    foreach ($items as $item) {
        // TODO: Implement caching for product prices
        $product = $this->productRepository->findById($item['product_id']);

        if (!$product) {
            throw new ProductNotFoundException("Product {$item['product_id']} not found");
        }

        $subtotal += $product->getPrecio() * $item['quantity'];
    }

    // Aplicar descuento
    $discount = $subtotal * ($discountPercent / 100);
    $afterDiscount = $subtotal - $discount;

    // Calcular impuestos si es necesario
    $tax = $includeTax ? $afterDiscount * 0.16 : 0.0; // IVA 16%

    $total = $afterDiscount + $tax;

    return [
        'subtotal' => round($subtotal, 2),
        'discount' => round($discount, 2),
        'tax' => round($tax, 2),
        'total' => round($total, 2)
    ];
}
```

<a id="error-handling"></a>
<a id="-error-handling"></a>
### Error Handling

```php
// ✅ Manejo específico de errores
try {
    $product = $this->productService->getProductById($productId);
} catch (ProductNotFoundException $e) {
    // Error específico - producto no encontrado
    $this->logger->warning('Product not found', ['product_id' => $productId]);
    return $this->render('errors/product-not-found.php', ['id' => $productId]);
} catch (DatabaseException $e) {
    // Error de base de datos
    $this->logger->error('Database error while fetching product', [
        'product_id' => $productId,
        'error' => $e->getMessage()
    ]);
    return $this->render('errors/500.php');
} catch (\Exception $e) {
    // Error genérico - último recurso
    $this->logger->critical('Unexpected error', [
        'product_id' => $productId,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    return $this->render('errors/500.php');
}

// ✅ Validación temprana
public function createProduct(array $data): Product
{
    // Fail fast - validar inmediatamente
    if (empty($data['nombre'])) {
        throw new ValidationException('Product name is required');
    }

    if ($data['precio'] <= 0) {
        throw new ValidationException('Product price must be greater than zero');
    }

    // Continuar con la lógica...
}
```

---

<a id="proceso-de-pull-request"></a>
<a id="-proceso-de-pull-request"></a>
## 🔄 Proceso de Pull Request

<a id="1-preparacion-del-pr"></a>
<a id="-1-preparacion-del-pr"></a>
### 1. Preparación del PR

```bash
<a id="asegurar-que-la-rama-esta-actualizada"></a>
<a id="-asegurar-que-la-rama-esta-actualizada"></a>
# Asegurar que la rama está actualizada
git checkout main
git pull upstream main
git checkout feature/mi-feature
git rebase main

<a id="ejecutar-checks-locales"></a>
<a id="-ejecutar-checks-locales"></a>
# Ejecutar checks locales
composer run lint       # Verificar estilo de código
composer run analyze    # Análisis estático
composer run test       # Ejecutar tests
composer run test-coverage  # Verificar coverage
```

<a id="2-template-de-pull-request"></a>
<a id="-2-template-de-pull-request"></a>
### 2. Template de Pull Request

```markdown
<a id="descripcion"></a>
<a id="-descripcion"></a>
## 🎯 Descripción

Breve descripción de los cambios realizados.

<a id="tipo-de-cambio"></a>
<a id="-tipo-de-cambio"></a>
### Tipo de cambio
- [ ] 🐛 Bug fix (cambio que corrige un issue)
- [ ] ✨ Nueva feature (cambio que añade funcionalidad)
- [ ] 💥 Breaking change (fix o feature que causaría que funcionalidad existente no funcione como se espera)
- [ ] 📚 Documentación (cambios solo en documentación)
- [ ] 🎨 Estilo (formato, espacios, etc; sin cambios de código)
- [ ] ♻️ Refactor (cambio de código que no corrige bug ni añade feature)
- [ ] ⚡ Performance (cambio que mejora performance)
- [ ] 🧪 Tests (añadir tests faltantes o corregir tests existentes)

<a id="como-se-ha-probado"></a>
<a id="-como-se-ha-probado"></a>
## 🧪 Cómo se ha probado

Describe las pruebas que has ejecutado para verificar tus cambios.

- [ ] Tests unitarios pasan
- [ ] Tests de integración pasan
- [ ] Probado manualmente en [especificar entorno]
- [ ] Coverage de código mantenido/mejorado

<a id="checklist"></a>
<a id="-checklist"></a>
## 📋 Checklist

- [ ] Mi código sigue los estándares de estilo del proyecto
- [ ] He realizado una auto-revisión de mi código
- [ ] He comentado mi código, particularmente en áreas difíciles de entender
- [ ] He realizado los cambios correspondientes en la documentación
- [ ] Mis cambios no generan nuevas advertencias
- [ ] He añadido tests que prueban que mi fix es efectivo o que mi feature funciona
- [ ] Los tests unitarios nuevos y existentes pasan localmente con mis cambios
- [ ] Cualquier cambio dependiente ha sido merged y publicado en módulos downstream

<a id="screenshots-si-aplica"></a>
<a id="-screenshots-si-aplica"></a>
## 📷 Screenshots (si aplica)

Si hay cambios en la UI, incluir screenshots del antes y después.

<a id="issues-relacionados"></a>
<a id="-issues-relacionados"></a>
## 🔗 Issues relacionados

Fixes #(número del issue)

<a id="notas-adicionales"></a>
<a id="-notas-adicionales"></a>
## 📝 Notas adicionales

Cualquier información adicional que los reviewers deberían saber.
```

<a id="3-criterios-para-aprobar-pr"></a>
<a id="-3-criterios-para-aprobar-pr"></a>
### 3. Criterios para Aprobar PR

<a id="requirements-minimos"></a>
<a id="-requirements-minimos"></a>
#### ✅ Requirements Mínimos

- **Tests**: Todos los tests pasan (unit + integration)
- **Coverage**: No reducir coverage existente
- **Linting**: Código pasa todos los checks de estilo
- **Documentation**: Cambios documentados apropiadamente
- **Breaking Changes**: Claramente identificados y justificados

<a id="criterios-de-calidad"></a>
<a id="-criterios-de-calidad"></a>
#### ✅ Criterios de Calidad

- **Single Responsibility**: Cada PR tiene un propósito claro
- **Atomic Changes**: Cambios lógicamente relacionados juntos
- **Readable**: Código fácil de entender
- **Tested**: Nueva funcionalidad tiene tests apropiados
- **Backwards Compatible**: No rompe funcionalidad existente

<a id="4-review-process"></a>
<a id="-4-review-process"></a>
### 4. Review Process

```mermaid
graph TD
    A[PR Creado] --> B{Checks Automáticos}
    B -->|❌ Fail| C[Corregir Issues]
    C --> A
    B -->|✅ Pass| D[Review Manual]
    D --> E{Reviewer Comments}
    E -->|Changes Requested| F[Aplicar Cambios]
    F --> D
    E -->|Approved| G[Merge a Main]
    G --> H[Deploy Automático]
```

---

<a id="code-review-guidelines"></a>
<a id="-code-review-guidelines"></a>
## 👥 Code Review Guidelines

<a id="para-reviewers"></a>
<a id="-para-reviewers"></a>
### Para Reviewers

<a id="que-revisar"></a>
<a id="-que-revisar"></a>
#### 🎯 Qué Revisar

**Funcionalidad**
- ¿El código hace lo que se supone que debe hacer?
- ¿Los casos edge están manejados?
- ¿Hay riesgos de seguridad?

**Calidad del Código**
- ¿El código es legible y mantenible?
- ¿Se siguen los patrones establecidos?
- ¿Hay duplicación innecesaria?

**Performance**
- ¿Hay queries N+1?
- ¿Se usan índices apropiados?
- ¿El algoritmo es eficiente?

**Testing**
- ¿Los tests cubren los casos importantes?
- ¿Los tests son mantenibles?
- ¿El coverage es apropiado?

<a id="como-dar-feedback"></a>
<a id="-como-dar-feedback"></a>
#### 💬 Cómo Dar Feedback

```markdown
<a id="feedback-constructivo"></a>
<a id="-feedback-constructivo"></a>
# ✅ Feedback Constructivo

<a id="problema-especifico"></a>
<a id="-problema-especifico"></a>
## Problema Específico
**Issue:** Esta función podría causar un memory leak con datasets grandes.

**Sugerencia:** Considera usar un generator para procesar los datos en chunks.

**Ejemplo:**
```
// En lugar de:
public function processAllData() {
    $data = $this->getAllData(); // Carga todo en memoria
    return array_map($this->process, $data);
}

// Usar:
public function processAllData() {
    foreach ($this->getDataChunks() as $chunk) {
        yield from array_map($this->process, $chunk);
    }
}
```

<a id="pregunta-para-clarificar"></a>
<a id="-pregunta-para-clarificar"></a>
## Pregunta para Clarificar
**Question:** ¿Has considerado qué pasa si el producto ya está en el carrito?
¿Deberíamos incrementar la cantidad o mostrar un error?

<a id="reconocimiento-positivo"></a>
<a id="-reconocimiento-positivo"></a>
## Reconocimiento Positivo
**Nice work!** Me gusta cómo has separado la validación del procesamiento.
Hace el código mucho más testeable.
```

<a id="feedback-a-evitar"></a>
<a id="-feedback-a-evitar"></a>
#### ❌ Feedback a Evitar

```markdown
<a id="feedback-no-constructivo"></a>
<a id="-feedback-no-constructivo"></a>
# ❌ Feedback No Constructivo

"Este código está mal."
// Sin explicar qué está mal o cómo mejorarlo

"Esto no funcionará."
// Sin evidencia o sugerencias alternativas

"¿Por qué hiciste esto así?"
// Tono crítico sin ofrecer ayuda

"Cambia todo esto."
// Sin dirección específica
```

<a id="para-authors"></a>
<a id="-para-authors"></a>
### Para Authors

<a id="responder-a-feedback"></a>
<a id="-responder-a-feedback"></a>
#### 📝 Responder a Feedback

```markdown
<a id="buenas-respuestas"></a>
<a id="-buenas-respuestas"></a>
# ✅ Buenas Respuestas

<a id="aceptar-sugerencia"></a>
<a id="-aceptar-sugerencia"></a>
## Aceptar Sugerencia
"Excelente punto! He implementado el generator como sugeriste.
Commit: abc123f"

<a id="explicar-decision"></a>
<a id="-explicar-decision"></a>
## Explicar Decisión
"Decidí usar este approach porque mantenemos compatibility con
la API existente. Si cambiamos esto, rompería 3 endpoints.
¿Te parece aceptable o prefieres que creemos una nueva versión de la API?"

<a id="pedir-clarificacion"></a>
<a id="-pedir-clarificacion"></a>
## Pedir Clarificación
"No estoy seguro de entender tu sugerencia sobre el caching.
¿Podrías dar un ejemplo de cómo implementarías el TTL variable?"

<a id="agradecer-feedback"></a>
<a id="-agradecer-feedback"></a>
## Agradecer Feedback
"Gracias por revisar! He aplicado todas las sugerencias.
El punto sobre testing edge cases era muy válido."
```

---

<a id="tipos-de-contribuciones"></a>
<a id="-tipos-de-contribuciones"></a>
## 🎯 Tipos de Contribuciones

<a id="bug-fixes"></a>
<a id="-bug-fixes"></a>
### 🐛 Bug Fixes

<a id="proceso-para-bug-fixes"></a>
<a id="-proceso-para-bug-fixes"></a>
#### Proceso para Bug Fixes

```bash
<a id="1-crear-issue-si-no-existe"></a>
<a id="-1-crear-issue-si-no-existe"></a>
# 1. Crear issue si no existe
<a id="2-crear-rama-especifica"></a>
<a id="-2-crear-rama-especifica"></a>
# 2. Crear rama específica
git checkout -b fix/issue-123-login-error

<a id="3-escribir-test-que-reproduzca-el-bug"></a>
<a id="-3-escribir-test-que-reproduzca-el-bug"></a>
# 3. Escribir test que reproduzca el bug
<a id="testsunitauthlogintestphp"></a>
<a id="-testsunitauthlogintestphp"></a>
# tests/Unit/Auth/LoginTest.php
public function testLoginWithSpecialCharacters() {
    // Test que falla por el bug
}

<a id="4-implementar-fix"></a>
<a id="-4-implementar-fix"></a>
# 4. Implementar fix
<a id="5-verificar-que-test-pasa"></a>
<a id="-5-verificar-que-test-pasa"></a>
# 5. Verificar que test pasa
composer run test

<a id="6-commit-con-referencia-al-issue"></a>
<a id="-6-commit-con-referencia-al-issue"></a>
# 6. Commit con referencia al issue
git commit -m "fix: resolve login error with special characters

Fixes issue where users with special characters in email
couldn't log in due to improper URL encoding.

Fixes #123"
```

<a id="template-para-bug-reports"></a>
<a id="-template-para-bug-reports"></a>
#### Template para Bug Reports

```markdown
<a id="bug-report"></a>
<a id="-bug-report"></a>
## 🐛 Bug Report

<a id="descripcion"></a>
<a id="-descripcion"></a>
### Descripción
Descripción clara y concisa del bug.

<a id="pasos-para-reproducir"></a>
<a id="-pasos-para-reproducir"></a>
### Pasos para Reproducir
1. Ir a '...'
2. Hacer click en '....'
3. Scrollear hasta '....'
4. Ver error

<a id="comportamiento-esperado"></a>
<a id="-comportamiento-esperado"></a>
### Comportamiento Esperado
Descripción clara de lo que esperabas que pasara.

<a id="comportamiento-actual"></a>
<a id="-comportamiento-actual"></a>
### Comportamiento Actual
Descripción clara de lo que pasó en su lugar.

<a id="screenshots"></a>
<a id="-screenshots"></a>
### Screenshots
Si aplica, añadir screenshots para ayudar a explicar el problema.

<a id="entorno"></a>
<a id="-entorno"></a>
### Entorno
- OS: [e.g. iOS]
- Browser [e.g. chrome, safari]
- Version [e.g. 22]
- PHP Version: [e.g. 8.1.0]

<a id="contexto-adicional"></a>
<a id="-contexto-adicional"></a>
### Contexto Adicional
Cualquier otro contexto sobre el problema.
```

<a id="new-features"></a>
<a id="-new-features"></a>
### ✨ New Features

<a id="proceso-para-features"></a>
<a id="-proceso-para-features"></a>
#### Proceso para Features

```bash
<a id="1-crear-issue-de-feature-request"></a>
<a id="-1-crear-issue-de-feature-request"></a>
# 1. Crear issue de feature request
<a id="2-discutir-approach-en-el-issue"></a>
<a id="-2-discutir-approach-en-el-issue"></a>
# 2. Discutir approach en el issue
<a id="3-crear-rama"></a>
<a id="-3-crear-rama"></a>
# 3. Crear rama
git checkout -b feature/product-variants

<a id="4-implementar-con-tdd"></a>
<a id="-4-implementar-con-tdd"></a>
# 4. Implementar con TDD
<a id="escribir-tests-primero"></a>
<a id="-escribir-tests-primero"></a>
# - Escribir tests primero
<a id="implementar-funcionalidad"></a>
<a id="-implementar-funcionalidad"></a>
# - Implementar funcionalidad
<a id="refactor-si-es-necesario"></a>
<a id="-refactor-si-es-necesario"></a>
# - Refactor si es necesario

<a id="5-documentar-la-feature"></a>
<a id="-5-documentar-la-feature"></a>
# 5. Documentar la feature
<a id="actualizar-readme-si-es-necesario"></a>
<a id="-actualizar-readme-si-es-necesario"></a>
# - Actualizar README si es necesario
<a id="anadir-documentacion-de-api"></a>
<a id="-anadir-documentacion-de-api"></a>
# - Añadir documentación de API
<a id="incluir-ejemplos-de-uso"></a>
<a id="-incluir-ejemplos-de-uso"></a>
# - Incluir ejemplos de uso
```

<a id="template-para-feature-requests"></a>
<a id="-template-para-feature-requests"></a>
#### Template para Feature Requests

```markdown
<a id="feature-request"></a>
<a id="-feature-request"></a>
## ✨ Feature Request

<a id="tu-feature-request-esta-relacionada-con-un-problema"></a>
<a id="-tu-feature-request-esta-relacionada-con-un-problema"></a>
### ¿Tu feature request está relacionada con un problema?
Descripción clara y concisa del problema. Ej. "Estoy frustrado cuando [...]"

<a id="describe-la-solucion-que-te-gustaria"></a>
<a id="-describe-la-solucion-que-te-gustaria"></a>
### Describe la solución que te gustaría
Descripción clara y concisa de lo que quieres que pase.

<a id="describe-alternativas-que-has-considerado"></a>
<a id="-describe-alternativas-que-has-considerado"></a>
### Describe alternativas que has considerado
Descripción clara y concisa de cualquier solución o feature alternativa que hayas considerado.

<a id="contexto-adicional"></a>
<a id="-contexto-adicional"></a>
### Contexto adicional
Añade cualquier otro contexto o screenshots sobre el feature request aquí.

<a id="criterios-de-aceptacion"></a>
<a id="-criterios-de-aceptacion"></a>
### Criterios de Aceptación
- [ ] Como usuario, puedo...
- [ ] Cuando hago X, entonces Y pasa...
- [ ] El sistema debe...
```

<a id="documentation"></a>
<a id="-documentation"></a>
### 📚 Documentation

<a id="contribuir-a-documentacion"></a>
<a id="-contribuir-a-documentacion"></a>
#### Contribuir a Documentación

```bash
<a id="documentacion-tecnica"></a>
<a id="-documentacion-tecnica"></a>
# Documentación técnica
docs/                   # Documentación principal
README.md              # Overview del proyecto
DEVELOPMENT.md         # Setup para desarrolladores
API.md                 # Documentación de API

<a id="documentacion-en-codigo"></a>
<a id="-documentacion-en-codigo"></a>
# Documentación en código
src/                   # PHPDoc en clases y métodos
public/assets/js/      # JSDoc en JavaScript
```

<a id="estandares-de-documentacion"></a>
<a id="-estandares-de-documentacion"></a>
#### Estándares de Documentación

```php
/**
 * Breve descripción de una línea
 *
 * Descripción más detallada que puede extenderse
 * a múltiples líneas si es necesario.
 *
 * @param Type $param Descripción del parámetro
 * @return Type Descripción de lo que retorna
 * @throws ExceptionType Cuándo se lanza esta excepción
 *
 * @example
 * $result = $service->methodName($param);
 *
 * @since 1.0.0
 * @author Nombre del Autor
 */
public function methodName(Type $param): Type
{
    // Implementation
}
```

---

<a id="reportar-bugs"></a>
<a id="-reportar-bugs"></a>
## 🗣️ Reportar Bugs

<a id="severity-levels"></a>
<a id="-severity-levels"></a>
### Severity Levels

| Level | Descripción | Tiempo de Respuesta |
|-------|-------------|-------------------|
| 🔴 **Critical** | App crashed, data loss, security vulnerabilities | 24 horas |
| 🟠 **High** | Major functionality broken, workaround exists | 3 días |
| 🟡 **Medium** | Minor functionality issues, cosmetic problems | 1 semana |
| 🟢 **Low** | Enhancement requests, nice-to-have features | 2 semanas |

<a id="bug-report-checklist"></a>
<a id="-bug-report-checklist"></a>
### Bug Report Checklist

- [ ] ¿Has buscado en issues existentes?
- [ ] ¿Puedes reproducir el bug consistentemente?
- [ ] ¿Has incluido pasos detallados para reproducir?
- [ ] ¿Has incluido información del entorno?
- [ ] ¿Has incluido logs/screenshots relevantes?
- [ ] ¿Has probado con la última versión?

---

<a id="solicitar-features"></a>
<a id="-solicitar-features"></a>
## 💡 Solicitar Features

<a id="feature-evaluation-criteria"></a>
<a id="-feature-evaluation-criteria"></a>
### Feature Evaluation Criteria

Los features son evaluados basándose en:

1. **Impact**: ¿Cuántos usuarios se beneficiarán?
2. **Effort**: ¿Cuánto trabajo requiere implementar?
3. **Alignment**: ¿Se alinea con la visión del producto?
4. **Maintenance**: ¿Qué carga de mantenimiento añade?

<a id="feature-request-process"></a>
<a id="-feature-request-process"></a>
### Feature Request Process

```mermaid
graph TD
    A[Feature Request] --> B[Community Discussion]
    B --> C{Core Team Review}
    C -->|Approved| D[Add to Roadmap]
    C -->|Needs More Info| E[Request Clarification]
    C -->|Rejected| F[Explain Reasoning]
    D --> G[Implementation]
    E --> B
```

---

<a id="reconocimientos"></a>
<a id="-reconocimientos"></a>
## 🏆 Reconocimientos

<a id="hall-of-fame"></a>
<a id="-hall-of-fame"></a>
### Hall of Fame

Reconocemos a nuestros contribuidores más activos:

<a id="core-contributors"></a>
<a id="-core-contributors"></a>
#### 🥇 Core Contributors

- **@equinoxe-grammer** - Project founder & lead maintainer
- **@contributor1** - Security & performance expert
- **@contributor2** - Documentation & testing specialist

<a id="regular-contributors"></a>
<a id="-regular-contributors"></a>
#### 🥈 Regular Contributors

- **@contributor3** - UI/UX improvements
- **@contributor4** - Bug fixes & maintenance
- **@contributor5** - API development

<a id="community-champions"></a>
<a id="-community-champions"></a>
#### 🥉 Community Champions

- **@contributor6** - Community support & mentoring
- **@contributor7** - Issue triage & reproduction
- **@contributor8** - Translations & localization

<a id="contribution-recognition"></a>
<a id="-contribution-recognition"></a>
### Contribution Recognition

Reconocemos contribuciones de varias formas:

- **GitHub Profile**: Contribuciones aparecen en tu perfil
- **CONTRIBUTORS.md**: Lista de todos los contribuidores
- **Release Notes**: Menciones en notas de versión
- **Social Media**: Reconocimiento en redes sociales
- **Swag**: Stickers y merchandise para contributors activos

<a id="levels-of-recognition"></a>
<a id="-levels-of-recognition"></a>
### Levels of Recognition

| Nivel | Criterio | Beneficios |
|-------|----------|------------|
| **Contributor** | Primera contribución merged | Aparición en CONTRIBUTORS.md |
| **Regular** | 5+ PRs merged | Acceso a channels privados |
| **Core** | 20+ PRs + domain expertise | Voting rights en decisiones |
| **Maintainer** | Invitación del equipo | Admin access al repositorio |

---

<a id="comunidad-y-comunicacion"></a>
<a id="-comunidad-y-comunicacion"></a>
## 📞 Comunidad y Comunicación

<a id="canales-de-comunicacion"></a>
<a id="-canales-de-comunicacion"></a>
### Canales de Comunicación

- **📧 Email**: admin@snackshop-project.com
- **💬 Discord**: [SnackShop Community](https://discord.gg/snackshop)
- **🐦 Twitter**: [@SnackShopDev](https://twitter.com/snackshodev)
- **📱 GitHub Discussions**: Para discusiones técnicas
- **📋 GitHub Issues**: Para bugs y feature requests

<a id="meetings-events"></a>
<a id="-meetings-events"></a>
### Meetings & Events

- **Weekly Office Hours**: Jueves 3-4 PM (GMT-6)
- **Monthly Community Call**: Primer viernes de cada mes
- **Quarterly Roadmap Review**: Cada 3 meses
- **Annual Contributors Summit**: Una vez al año (virtual)

<a id="getting-help"></a>
<a id="-getting-help"></a>
### Getting Help

<a id="para-nuevos-contribuidores"></a>
<a id="-para-nuevos-contribuidores"></a>
#### Para Nuevos Contribuidores

1. **Lee esta guía completamente**
2. **Configura tu entorno de desarrollo**
3. **Busca issues etiquetados como "good first issue"**
4. **Únete a nuestro Discord para hacer preguntas**
5. **Asiste a Office Hours si necesitas ayuda en vivo**

<a id="para-contribuidores-experienciales"></a>
<a id="-para-contribuidores-experienciales"></a>
#### Para Contribuidores Experienciales

1. **Considera mentorear nuevos contribuidores**
2. **Participa en discusiones de arquitectura**
3. **Ayuda con code reviews**
4. **Propón mejoras al proceso de contribución**

---

<a id="documentos-relacionados"></a>
<a id="-documentos-relacionados"></a>
## 🔗 Documentos Relacionados

- **[📖 Índice General](docs/INDEX.md)** — Navegación completa del manual
- **[🛠️ Development](DEVELOPMENT.md)** — Setup y herramientas de desarrollo
- **[🧪 Testing](TESTING.md)** — Guías de testing y quality assurance
- **[🏗️ Architecture](ARCHITECTURE.md)** — Comprende la arquitectura del sistema
- **[🔌 API](API.md)** — Documentación de endpoints para desarrollo
- **[🚀 Deployment](DEPLOYMENT.md)** — Guías de despliegue y producción

---

<a id="agradecimientos"></a>
<a id="-agradecimientos"></a>
## 🙏 Agradecimientos

Gracias por considerar contribuir a SnackShop! Tu tiempo y esfuerzo ayudan a hacer este proyecto mejor para todos.

Cada contribución, sin importar el tamaño, es valiosa y apreciada. Desde reportar un bug hasta implementar una feature major, todo ayuda a mejorar el proyecto.

**¡Esperamos ver tus contribuciones pronto!** 🎉

---

**[📖 Índice](docs/INDEX.md)** | **[🛠️ Ver Development](DEVELOPMENT.md)** | **[🧪 Ver Testing](TESTING.md)** | **[🏗️ Ver Arquitectura](ARCHITECTURE.md)**
