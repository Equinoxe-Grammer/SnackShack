# src/Controllers/

Propósito

Contiene los controllers HTTP que reciben requests, validan/autorizar, delegan en Services y devuelven vistas o JSON.

Convenciones

- Cada controller agrupa acciones relacionadas (index, create, store, edit, update, delete, show).
- Mantén la mínima lógica posible en controllers: validar, mapear datos y orquestar llamadas a Services.
- Los controllers deben lanzar excepciones o retornar respuestas adecuadas que el router/handler traduzca a códigos HTTP.

Ejemplo

- `ProductController.php` — acciones para listar, crear/editar productos y manejar variantes.

Testing

- Testear controllers mediante pruebas funcionales o tests que simulen request/response. Preferible: testear Services y Repositories por separado y mantener controllers delgados.
