
# src/Controllers/

Controladores HTTP: reciben requests, validan/autorizan, delegan en Services y devuelven vistas o JSON.

## Convenciones

- Agrupa acciones relacionadas (index, create, store, edit, update, delete, show)
- Mantén la mínima lógica posible en controllers: validar, mapear datos y orquestar llamadas a Services
- Lanza excepciones o retorna respuestas adecuadas que el router traduzca a códigos HTTP

## Ejemplo

- `ProductController.php`: listar, crear/editar productos y manejar variantes

## Testing

- Testea controllers mediante pruebas funcionales o tests que simulen request/response
- Preferible: testear Services y Repositories por separado y mantener controllers delgados

## Referencias

- [Manual técnico modular](../../docs/INDEX.md)
