# src/Repositories/

Propósito

Implementan la capa de acceso a datos. Los Repositories encapsulan consultas SQL/ORM y proporcionan una API estable para el resto de la aplicación.

Patrón y convenciones

- Cada entidad importante debe tener un Repository (p. ej. `ProductRepository`, `UserRepository`, `SaleRepository`).
- Preferir interfaces (`XRepositoryInterface.php`) para facilitar mocking en tests.
- Los métodos comunes: `find($id)`, `findAll()`, `create($data)`, `update($id, $data)`, `delete($id)` y consultas específicas `findByCategory($categoryId)`.

Transacciones y operaciones compuestas

- Si una operación requiere varias consultas (e.g. crear venta y crear items de venta + decrementar stock), el Service que orquesta debe abrir una transacción usando la conexión del `src/Database/Connection.php`.

Testing

- Mockear las interfaces de repositorio en tests de Services para aislar la lógica de negocio.
- Para pruebas de integración, usar una BD en memoria (SQLite) y ejecutar los mismos queries para validar el comportamiento.

Buenas prácticas

- Evitar lógica de negocio compleja en Repositories: limitarse a persistencia y transformaciones sencillas.
- Manejar errores de BD y mapearlos a excepciones de dominio cuando corresponda.
- Documentar queries que sean críticas en rendimiento y considerar índices en la BD.

Ejemplo (pseudocódigo)

```php
interface ProductRepositoryInterface {
  public function find(int $id): ?Product;
  public function create(array $data): Product;
}

class ProductRepository implements ProductRepositoryInterface {
  public function find(int $id): ?Product {
    $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    return $row ? Product::fromArray($row) : null;
  }
}
```

Notas

- Revisa las interfaces existentes en `src/Repositories/` (por ejemplo `ComprasRepositoryInterface.php`) y mantén la coherencia.
