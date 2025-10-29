
# src/Repositories/

Implementan la capa de acceso a datos. Los Repositories encapsulan consultas SQL/ORM y proporcionan una API estable para el resto de la aplicación.

## Patrón y convenciones

- Cada entidad importante debe tener un Repository (ej. `ProductRepository`, `UserRepository`, `SaleRepository`)
- Preferir interfaces (`XRepositoryInterface.php`) para facilitar mocking en tests
- Métodos comunes: `find($id)`, `findAll()`, `create($data)`, `update($id, $data)`, `delete($id)`, consultas específicas

## Transacciones y operaciones compuestas

- Si una operación requiere varias consultas, el Service debe abrir una transacción usando la conexión de `src/Database/Connection.php`

## Testing

- Mockea interfaces de repositorio en tests de Services
- Para integración, usa una BD en memoria (SQLite) y ejecuta los mismos queries

## Buenas prácticas

- Evita lógica de negocio compleja en Repositories
- Maneja errores de BD y mapea a excepciones de dominio
- Documenta queries críticas y considera índices en la BD

## Ejemplo (pseudocódigo)

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

## Referencias

- [Manual técnico modular](../../docs/INDEX.md)
