# Servicios Específicos

Este documento detalla la arquitectura, responsabilidades, métodos principales, ejemplos de uso y mejores prácticas para los servicios clave del backend de SnackShop. Cada sección incluye navegación, referencias cruzadas y recomendaciones de extensión.

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
- [Ejemplos y Tutoriales](EXAMPLES.md)
- [Troubleshooting](TROUBLESHOOTING.md)

---

## 1. CostoService

**Archivo:** `src/Services/CostoService.php`

### Propósito

Gestiona el cálculo de costos de productos y variantes, considerando ingredientes, cantidades y precios de compra.

### Responsabilidades

- Calcular el costo total de un producto o variante.
- Integrarse con repositorios de ingredientes y recetas.
- Proveer métodos para obtener desglose de costos.

### Métodos Principales

- `calculateProductCost(int $productId): float` — Calcula el costo total de un producto.
- `calculateVariantCost(int $variantId): float` — Calcula el costo de una variante específica.
- `getCostBreakdown(int $productId): array` — Devuelve el desglose de costos por ingrediente.

### Ejemplo de Uso

```php
$costoService = new CostoService($ingredientesRepo, $recetaRepo);
$costo = $costoService->calculateProductCost($productId);
$desglose = $costoService->getCostBreakdown($productId);
```

### Mejores Prácticas

- Validar existencia de ingredientes y recetas antes de calcular.
- Manejar excepciones para productos sin receta.
- Extender para soportar promociones o descuentos.

---

## 2. ImageProcessingService

**Archivo:** `src/Services/ImageProcessingService.php`

### Propósito

Procesa imágenes de productos: redimensiona, optimiza y almacena imágenes en el sistema de archivos.

### Responsabilidades

- Redimensionar imágenes a tamaños estándar.
- Optimizar imágenes para web.
- Gestionar almacenamiento y rutas de acceso.

### Métodos Principales

- `resizeImage(string $path, int $width, int $height): string` — Redimensiona y guarda la imagen.
- `optimizeImage(string $path): void` — Optimiza la imagen para web.
- `deleteImage(string $path): void` — Elimina una imagen del sistema.

### Ejemplo de Uso

```php
$imageService = new ImageProcessingService();
$path = $imageService->resizeImage($originalPath, 400, 400);
$imageService->optimizeImage($path);
```

### Mejores Prácticas

- Validar tipo y tamaño de archivo antes de procesar.
- Usar rutas relativas y seguras.
- Integrar con CDN o almacenamiento externo si es necesario.

---

## 3. ImpuestosService

**Archivo:** `src/Services/ImpuestosService.php`

### Propósito

Gestiona el cálculo y aplicación de impuestos sobre ventas y productos.

### Responsabilidades

- Calcular impuestos según tipo de producto y configuración.
- Aplicar reglas fiscales dinámicas.
- Proveer desglose de impuestos en ventas.

### Métodos Principales

- `calculateTax(float $amount, int $productId): float` — Calcula el impuesto para un monto y producto.
- `getTaxBreakdown(int $saleId): array` — Devuelve el desglose de impuestos de una venta.

### Ejemplo de Uso

```php
$impuestosService = new ImpuestosService($config);
$tax = $impuestosService->calculateTax($amount, $productId);
$breakdown = $impuestosService->getTaxBreakdown($saleId);
```

### Mejores Prácticas

- Configurar tasas de impuestos en variables de entorno o base de datos.
- Permitir reglas fiscales personalizables.
- Validar cambios fiscales periódicamente.

---

## 4. Otros Servicios

- **PaymentMethodService:** Gestión de métodos de pago, integración con pasarelas.
- **ProductService:** Lógica de negocio para productos y variantes.
- **SaleService:** Procesamiento de ventas, generación de tickets y reportes.
- **UserService:** Gestión de usuarios, roles y permisos.
- **VariantService:** Manejo de variantes de productos.

Cada uno sigue el mismo estándar: responsabilidades claras, métodos bien definidos, integración con repositorios y control de errores.

---

## Referencias Cruzadas

- [API.md](API.md): Endpoints relacionados con cada servicio.
- [DATABASE.md](DATABASE.md): Tablas y relaciones utilizadas por los servicios.
- [DEVELOPMENT.md](DEVELOPMENT.md): Patrones de diseño y mejores prácticas.

---

## Extensión y Personalización

- Añadir nuevos servicios siguiendo la estructura aquí documentada.
- Documentar cada nuevo servicio en este archivo y enlazar desde el índice.
- Mantener ejemplos de uso y mejores prácticas actualizados.

---

[⬅️ Volver al Índice](INDEX.md)
