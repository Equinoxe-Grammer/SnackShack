<a id="ejemplos-y-tutoriales"></a>
<a id="-ejemplos-y-tutoriales"></a>
# Ejemplos y Tutoriales
<!-- TOC -->
<a id="contenido"></a>
<a id="-contenido"></a>
## Contenido

- [Navegación](#navegacion)
- [1. Crear un Producto con Variantes](#1-crear-un-producto-con-variantes)
  - [Paso 1: Crear el Producto](#paso-1-crear-el-producto)
  - [Paso 2: Agregar Variantes](#paso-2-agregar-variantes)
- [2. Registrar una Venta](#2-registrar-una-venta)
- [3. Calcular Costo y Margen de un Producto](#3-calcular-costo-y-margen-de-un-producto)
- [4. Subir y Procesar Imagen de Producto](#4-subir-y-procesar-imagen-de-producto)
- [5. Aplicar Impuestos a una Venta](#5-aplicar-impuestos-a-una-venta)
- [6. Tutorial: Flujo Completo de Alta de Producto y Venta](#6-tutorial-flujo-completo-de-alta-de-producto-y-venta)
- [7. Consejos y Buenas Prácticas](#7-consejos-y-buenas-practicas)
<!-- /TOC -->

Este documento proporciona ejemplos prácticos y tutoriales paso a paso para las operaciones más comunes en SnackShop. Incluye fragmentos de código, flujos completos y enlaces a la documentación relevante.

---

<a id="navegacion"></a>
<a id="-navegacion"></a>
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

<a id="1-crear-un-producto-con-variantes"></a>
<a id="-1-crear-un-producto-con-variantes"></a>
## 1. Crear un Producto con Variantes

<a id="paso-1-crear-el-producto"></a>
<a id="-paso-1-crear-el-producto"></a>
### Paso 1: Crear el Producto

```php
$productService = new ProductService($productRepo, $variantRepo);
$productId = $productService->createProduct([
    'name' => 'Brownie',
    'category_id' => 2,
    'price' => 30.00
]);
```

<a id="paso-2-agregar-variantes"></a>
<a id="-paso-2-agregar-variantes"></a>
### Paso 2: Agregar Variantes

```php
$variantId = $productService->addVariant($productId, [
    'name' => 'Brownie con nuez',
    'price' => 35.00
]);
```

---

<a id="2-registrar-una-venta"></a>
<a id="-2-registrar-una-venta"></a>
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

<a id="3-calcular-costo-y-margen-de-un-producto"></a>
<a id="-3-calcular-costo-y-margen-de-un-producto"></a>
## 3. Calcular Costo y Margen de un Producto

```php
$costoService = new CostoService($ingredientesRepo, $recetaRepo);
$costo = $costoService->calculateProductCost($productId);
$margen = $productService->getMargin($productId);
```

---

<a id="4-subir-y-procesar-imagen-de-producto"></a>
<a id="-4-subir-y-procesar-imagen-de-producto"></a>
## 4. Subir y Procesar Imagen de Producto

```php
$imageService = new ImageProcessingService();
$path = $imageService->resizeImage($originalPath, 400, 400);
$imageService->optimizeImage($path);
$productService->updateImage($productId, $path);
```

---

<a id="5-aplicar-impuestos-a-una-venta"></a>
<a id="-5-aplicar-impuestos-a-una-venta"></a>
## 5. Aplicar Impuestos a una Venta

```php
$impuestosService = new ImpuestosService($config);
$tax = $impuestosService->calculateTax($amount, $productId);
$saleService->applyTax($saleId, $tax);
```

---

<a id="6-tutorial-flujo-completo-de-alta-de-producto-y-venta"></a>
<a id="-6-tutorial-flujo-completo-de-alta-de-producto-y-venta"></a>
## 6. Tutorial: Flujo Completo de Alta de Producto y Venta

1. Crear producto y variantes ([ver ejemplo 1](#1-crear-un-producto-con-variantes))
2. Subir imagen ([ver ejemplo 4](#4-subir-y-procesar-imagen-de-producto))
3. Registrar venta ([ver ejemplo 2](#2-registrar-una-venta))
4. Calcular costo y margen ([ver ejemplo 3](#3-calcular-costo-y-margen-de-un-producto))
5. Aplicar impuestos ([ver ejemplo 5](#5-aplicar-impuestos-a-una-venta))

---

<a id="7-consejos-y-buenas-practicas"></a>
<a id="-7-consejos-y-buenas-practicas"></a>
## 7. Consejos y Buenas Prácticas

- Validar siempre los datos de entrada.
- Manejar excepciones y errores de negocio.
- Utilizar los servicios y repositorios en vez de acceso directo a la base de datos.
- Seguir los estándares de codificación y documentación definidos en [DEVELOPMENT.md](DEVELOPMENT.md).

---

[⬅️ Volver al Índice](INDEX.md)
