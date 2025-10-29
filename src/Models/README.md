
# src/Models/

Clases que representan las entidades del dominio (Product, User, Sale, Variant, etc.). Son POPOs/DTOs ligeros que modelan filas de tablas y facilitan el transporte de datos entre capas.

## Convenciones

- Nombres en singular: `Product`, `User`, `Sale`
- No incluir lógica de negocio compleja (debe estar en Services)
- Documenta propiedades y tipos (docblocks) para autocompletado y análisis estático

## Serialización

- Define getters/setters o propiedades públicas según convención
- Para serializar a JSON, implementa `toArray()` o `jsonSerialize()`

## Relaciones

- Si las relaciones se modelan manualmente, documenta cómo se cargan y qué métodos usar

## Testing

- Crea fixtures/objetos de muestra para tests de Services y Repositories

## Notas

- Revisa `src/Repositories/` para ver cómo se construyen y rellenan los Models desde la BD
- Si se introduce un ORM, actualiza este README

## Referencias

- [Manual técnico modular](../../docs/INDEX.md)
