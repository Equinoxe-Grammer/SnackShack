<a id="servicios-especificos"></a>
<a id="-servicios-especificos"></a>
# Servicios Específicos
<!-- TOC -->
<a id="contenido"></a>
<a id="-contenido"></a>
## Contenido

- [Navegación](#navegacion)
- [1. CostoService](#1-costoservice)
  - [Propósito](#proposito)
  - [Responsabilidades](#responsabilidades)
  - [Métodos Principales](#metodos-principales)
  - [Ejemplo de Uso](#ejemplo-de-uso)
  - [Mejores Prácticas](#mejores-practicas)
- [2. ImageProcessingService](#2-imageprocessingservice)
  - [Propósito](#proposito)
  - [Responsabilidades](#responsabilidades)
  - [Métodos Principales](#metodos-principales)
  - [Ejemplo de Uso](#ejemplo-de-uso)
  - [Mejores Prácticas](#mejores-practicas)
- [3. ImpuestosService](#3-impuestosservice)
  - [Propósito](#proposito)
  - [Responsabilidades](#responsabilidades)
  - [Métodos Principales](#metodos-principales)
  - [Ejemplo de Uso](#ejemplo-de-uso)
  - [Mejores Prácticas](#mejores-practicas)
- [4. Otros Servicios](#4-otros-servicios)
- [Referencias Cruzadas](#referencias-cruzadas)
- [Extensión y Personalización](#extension-y-personalizacion)
<!-- /TOC -->

Este documento detalla la arquitectura, responsabilidades, métodos principales, ejemplos de uso y mejores prácticas para los servicios clave del backend de SnackShop. Cada sección incluye navegación, referencias cruzadas y recomendaciones de extensión.

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
- [Ejemplos y Tutoriales](EXAMPLES.md)
- [Troubleshooting](TROUBLESHOOTING.md)

---

<a id="1-costoservice"></a>
<a id="-1-costoservice"></a>
## 1. CostoService

**Archivo:** `src/Services/CostoService.php`

<a id="proposito"></a>
<a id="-proposito"></a>
### Propósito

Gestiona el cálculo de costos de productos y variantes, considerando ingredientes, cantidades y precios de compra.

<a id="responsabilidades"></a>
<a id="-responsabilidades"></a>
### Responsabilidades

- Calcular el costo total de un producto o variante.
- Integrarse con repositorios de ingredientes y recetas.
- Proveer métodos para obtener desglose de costos.

<a id="metodos-principales"></a>
<a id="-metodos-principales"></a>
### Métodos Principales

- `calculateProductCost(int $productId): float` — Calcula el costo total de un producto.
- `calculateVariantCost(int $variantId): float` — Calcula el costo de una variante específica.
- `getCostBreakdown(int $productId): array` — Devuelve el desglose de costos por ingrediente.

<a id="ejemplo-de-uso"></a>
<a id="-ejemplo-de-uso"></a>
### Ejemplo de Uso

```php
$costoService = new CostoService($ingredientesRepo, $recetaRepo);
$costo = $costoService->calculateProductCost($productId);
$desglose = $costoService->getCostBreakdown($productId);
```

<a id="mejores-practicas"></a>
<a id="-mejores-practicas"></a>
### Mejores Prácticas

- Validar existencia de ingredientes y recetas antes de calcular.
- Manejar excepciones para productos sin receta.
- Extender para soportar promociones o descuentos.

---

<a id="2-imageprocessingservice"></a>
<a id="-2-imageprocessingservice"></a>
## 2. ImageProcessingService

**Archivo:** `src/Services/ImageProcessingService.php`

<a id="proposito"></a>
<a id="-proposito"></a>
### Propósito

Procesa imágenes de productos: redimensiona, optimiza y almacena imágenes en el sistema de archivos.

<a id="responsabilidades"></a>
<a id="-responsabilidades"></a>
### Responsabilidades

- Redimensionar imágenes a tamaños estándar.
- Optimizar imágenes para web.
- Gestionar almacenamiento y rutas de acceso.

<a id="metodos-principales"></a>
<a id="-metodos-principales"></a>
### Métodos Principales

- `resizeImage(string $path, int $width, int $height): string` — Redimensiona y guarda la imagen.
- `optimizeImage(string $path): void` — Optimiza la imagen para web.
- `deleteImage(string $path): void` — Elimina una imagen del sistema.

<a id="ejemplo-de-uso"></a>
<a id="-ejemplo-de-uso"></a>
### Ejemplo de Uso

```php
$imageService = new ImageProcessingService();
$path = $imageService->resizeImage($originalPath, 400, 400);
$imageService->optimizeImage($path);
```

<a id="mejores-practicas"></a>
<a id="-mejores-practicas"></a>
### Mejores Prácticas

- Validar tipo y tamaño de archivo antes de procesar.
- Usar rutas relativas y seguras.
- Integrar con CDN o almacenamiento externo si es necesario.

---

<a id="3-impuestosservice"></a>
<a id="-3-impuestosservice"></a>
## 3. ImpuestosService

**Archivo:** `src/Services/ImpuestosService.php`

<a id="proposito"></a>
<a id="-proposito"></a>
### Propósito

Gestiona el cálculo y aplicación de impuestos sobre ventas y productos.

<a id="responsabilidades"></a>
<a id="-responsabilidades"></a>
### Responsabilidades

- Calcular impuestos según tipo de producto y configuración.
- Aplicar reglas fiscales dinámicas.
- Proveer desglose de impuestos en ventas.

<a id="metodos-principales"></a>
<a id="-metodos-principales"></a>
### Métodos Principales

- `calculateTax(float $amount, int $productId): float` — Calcula el impuesto para un monto y producto.
- `getTaxBreakdown(int $saleId): array` — Devuelve el desglose de impuestos de una venta.

<a id="ejemplo-de-uso"></a>
<a id="-ejemplo-de-uso"></a>
### Ejemplo de Uso

```php
$impuestosService = new ImpuestosService($config);
$tax = $impuestosService->calculateTax($amount, $productId);
$breakdown = $impuestosService->getTaxBreakdown($saleId);
```

<a id="mejores-practicas"></a>
<a id="-mejores-practicas"></a>
### Mejores Prácticas

- Configurar tasas de impuestos en variables de entorno o base de datos.
- Permitir reglas fiscales personalizables.
- Validar cambios fiscales periódicamente.

---

<a id="4-otros-servicios"></a>
<a id="-4-otros-servicios"></a>
## 4. Otros Servicios

- **PaymentMethodService:** Gestión de métodos de pago, integración con pasarelas.
- **ProductService:** Lógica de negocio para productos y variantes.
- **SaleService:** Procesamiento de ventas, generación de tickets y reportes.
- **UserService:** Gestión de usuarios, roles y permisos.
- **VariantService:** Manejo de variantes de productos.

Cada uno sigue el mismo estándar: responsabilidades claras, métodos bien definidos, integración con repositorios y control de errores.

---

<a id="referencias-cruzadas"></a>
<a id="-referencias-cruzadas"></a>
## Referencias Cruzadas

- [API.md](API.md): Endpoints relacionados con cada servicio.
- [DATABASE.md](DATABASE.md): Tablas y relaciones utilizadas por los servicios.
- [DEVELOPMENT.md](DEVELOPMENT.md): Patrones de diseño y mejores prácticas.

---

<a id="extension-y-personalizacion"></a>
<a id="-extension-y-personalizacion"></a>
## Extensión y Personalización

- Añadir nuevos servicios siguiendo la estructura aquí documentada.
- Documentar cada nuevo servicio en este archivo y enlazar desde el índice.
- Mantener ejemplos de uso y mejores prácticas actualizados.

---

[⬅️ Volver al Índice](INDEX.md)
