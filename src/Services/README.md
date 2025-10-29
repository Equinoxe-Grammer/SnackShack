
# src/Services/

Servicios de lógica de negocio reutilizable. Los Services coordinan operaciones complejas, validaciones y reglas de negocio, y son independientes de la capa HTTP.

## Ejemplos de Services

- `CostoService`: cálculo de costos y composición de precio
- `ImpuestosService`: cálculo de impuestos
- `ImageProcessingService`: validación y redimensionado de imágenes

## Convenciones

- No acceder a $_POST/$_GET directamente
- Recibir parámetros y devolver resultados (arrays, Models, DTOs)
- Lanzar excepciones en errores
- Si la operación afecta varias tablas, abrir transacción en el Service

## Testing

- Crear tests unitarios para Services, mockeando Repositories

## Referencias

- [Documentación de servicios](../../docs/SERVICES.md)
