# src/Models/

Propósito

Contiene las clases que representan las entidades del dominio (Product, User, Sale, Variant, etc.). En este proyecto son POPOs/DTOs ligeros que modelan filas de tablas y facilitan transporte de datos entre capas.

Convenciones sugeridas

- Nombres en singular: `Product`, `User`, `Sale`.
- Mantener las responsabilidades limitadas: los Models no deben contener lógica de negocio compleja; esa lógica pertenece a Services.
- Documentar las propiedades esperadas y tipos (docblocks) para mejorar autocompletado y análisis estático.

Campos y serialización

- Define getters/setters o propiedades públicas según la convención del proyecto.
- Para serializar a JSON, implementa `toArray()` o `jsonSerialize()` cuando sea necesario.

Relaciones

- Si las relaciones (p. ej. Product -> Variants) se modelan manualmente, documenta en el Model cómo se cargan (lazy / eager) y qué métodos usar para obtenerlos.

Testing

- Crear fixtures/objetos de muestra para usar en tests de Services y Repositories.

Notas

- Revisa `src/Repositories/` para ver cómo se construyen y rellenan los Models desde filas de la BD.
- Si se decide introducir un ORM (Eloquent, Doctrine), actualiza este README con las nuevas convenciones.
