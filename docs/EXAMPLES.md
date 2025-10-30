# Ejemplos y Tutoriales

Este documento proporciona ejemplos prácticos y tutoriales paso a paso para las operaciones más comunes en SnackShop. Incluye fragmentos de código, flujos completos y enlaces a la documentación relevante.

---

## Navegación

- [Índice General](INDEX.md)
- [Arquitectura](ARCHITECTURE.md)
- [API](API.md)
- [Base de Datos](DATABASE.md)
- [Despliegue](DEPLOYMENT.md)
- [Configuración](CONFIGURATION.md)
- [Desarrollo](DEVELOPMENT.md)
- [Testing](TESTING.md)
- [Contribución](CONTRIBUTING.md)
- [Servicios](SERVICES.md)
- [Troubleshooting](TROUBLESHOOTING.md)

---

## 1. Crear un Producto con Variantes

### Paso 1: Crear el Producto

```php
$productService = new ProductService($productRepo, $variantRepo);
$productId = $productService->createProduct([
    'name' => 'Brownie',
    'category_id' => 2,
    'price' => 30.00
]);
```

### Paso 2: Agregar Variantes

```php
$variantId = $productService->addVariant($productId, [
    'name' => 'Brownie con nuez',
    'price' => 35.00
]);
```

---

## 2. Registrar una Venta

```php
$saleService = new SaleService($saleRepo, $productRepo);
$saleId = $saleService->registerSale([
    'user_id' => 1,
    'items' => [
        ['product_id' => $productId, 'quantity' => 2],
        ['variant_id' => $variantId, 'quantity' => 1]
    ],
    'payment_method_id' => 3
]);
```

---

## 3. Calcular Costo y Margen de un Producto

```php
$costoService = new CostoService($ingredientesRepo, $recetaRepo);
$costo = $costoService->calculateProductCost($productId);
$margen = $productService->getMargin($productId);
```

---

## 4. Subir y Procesar Imagen de Producto

```php
$imageService = new ImageProcessingService();
$path = $imageService->resizeImage($originalPath, 400, 400);
$imageService->optimizeImage($path);
$productService->updateImage($productId, $path);
```

---

## 5. Aplicar Impuestos a una Venta

```php
$impuestosService = new ImpuestosService($config);
$tax = $impuestosService->calculateTax($amount, $productId);
$saleService->applyTax($saleId, $tax);
```

---

## 6. Tutorial: Flujo Completo de Alta de Producto y Venta

1. Crear producto y variantes ([ver ejemplo 1](#1-crear-un-producto-con-variantes))
2. Subir imagen ([ver ejemplo 4](#4-subir-y-procesar-imagen-de-producto))
3. Registrar venta ([ver ejemplo 2](#2-registrar-una-venta))
4. Calcular costo y margen ([ver ejemplo 3](#3-calcular-costo-y-margen-de-un-producto))
5. Aplicar impuestos ([ver ejemplo 5](#5-aplicar-impuestos-a-una-venta))

---

## 7. Consejos y Buenas Prácticas

- Validar siempre los datos de entrada.
- Manejar excepciones y errores de negocio.
- Utilizar los servicios y repositorios en vez de acceso directo a la base de datos.
- Seguir los estándares de codificación y documentación definidos en [DEVELOPMENT.md](DEVELOPMENT.md).

---

[⬅️ Volver al Índice](INDEX.md)
