<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Compras de Ingredientes</title>
  <link rel="icon" type="image/svg+xml" href="/assets/favicon.svg">
  <link rel="stylesheet" href="/css/theme.css?v=20251013">
  <link rel="stylesheet" href="/css/catalogofunciones.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <script src="/js/theme-toggle.js"></script>
</head>
<body style="background: var(--bg);">
  <div class="layout-flex">
    <?php $active = 'compras'; include __DIR__ . '/../partials/sidebar.php'; ?>
    <main class="main-content">
      <header class="catalog-header">
        <h1><i class="fas fa-shopping-cart"></i> Compras de Ingredientes</h1>
        <p>Gestiona las compras y egresos por insumo</p>
      </header>

      <section class="products-section">
        <div class="card mb-4">
          <div class="card-header">
            <h3>Registrar Nueva Compra</h3>
          </div>
          <div class="card-body">
            <form method="post" action="/compras">
              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">
              <div class="form-grid">
                <div class="form-group">
                  <label for="ingrediente_id">Ingrediente</label>
                  <select id="ingrediente_id" name="ingrediente_id" class="form-control" required>
                    <option value="">Selecciona un ingrediente</option>
                    <?php foreach ($ingredientes as $ing): ?>
                      <option value="<?php echo $ing['id']; ?>"><?php echo htmlspecialchars($ing['nombre']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="cantidad">Cantidad</label>
                  <input type="number" id="cantidad" name="cantidad" class="form-control" step="0.01" min="0.01" required>
                </div>
                <div class="form-group">
                  <label for="costo_total">Costo Total</label>
                  <input type="number" id="costo_total" name="costo_total" class="form-control" step="0.01" min="0.01" required>
                </div>
                <div class="form-group">
                  <label for="fecha">Fecha</label>
                  <input type="datetime-local" id="fecha" name="fecha" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>">
                </div>
                <div class="form-group">
                  <label class="checkbox-label">
                    <input type="checkbox" id="iva_incluido" name="iva_incluido" checked>
                    IVA incluido
                  </label>
                </div>
              </div>
              <button type="submit" class="btn btn-primary">Registrar Compra</button>
            </form>
          </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
          <div class="card-header">
            <h3>Filtros</h3>
          </div>
          <div class="card-body">
            <form method="get" action="/compras" id="filter-form">
              <div class="form-grid">
                <div class="form-group">
                  <label for="ingrediente_filter">Ingrediente</label>
                  <select id="ingrediente_filter" name="ingrediente_id" class="form-control">
                    <option value="">Todos</option>
                    <?php foreach ($ingredientes as $ing): ?>
                      <option value="<?php echo $ing['id']; ?>" <?php echo (isset($_GET['ingrediente_id']) && $_GET['ingrediente_id'] == $ing['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($ing['nombre']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="form-group">
                  <label for="fecha_from">Fecha Desde</label>
                  <input type="date" id="fecha_from" name="fecha_from" class="form-control" value="<?php echo $_GET['fecha_from'] ?? ''; ?>">
                </div>

                <div class="form-group">
                  <label for="fecha_to">Fecha Hasta</label>
                  <input type="date" id="fecha_to" name="fecha_to" class="form-control" value="<?php echo $_GET['fecha_to'] ?? ''; ?>">
                </div>
              </div>

              <button type="submit" class="btn btn-secondary">Filtrar</button>
              <a href="/compras" class="btn btn-outline-secondary">Limpiar</a>
            </form>
          </div>
        </div>

        <!-- Tabla de compras -->
        <div class="card">
          <div class="card-header">
            <h3>Historial de Compras</h3>
            <div class="card-header-actions">
              <span>Total Compras: <?php echo $totalCompras; ?></span>
              <span>Total Costo: $<?php echo number_format($totalCosto, 2); ?></span>
            </div>
          </div>
          <div class="card-body">
            <table class="table">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Ingrediente</th>
                  <th>Cantidad</th>
                  <th>Costo Total</th>
                  <th>IVA Incluido</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($compras)): ?>
                  <tr>
                    <td colspan="6" class="text-center">No hay compras registradas.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($compras as $compra): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($compra['fecha']); ?></td>
                      <td><?php echo htmlspecialchars($compra['ingrediente_nombre']); ?></td>
                      <td><?php echo number_format($compra['cantidad'], 2); ?></td>
                      <td>$<?php echo number_format($compra['costo_total'], 2); ?></td>
                      <td><?php echo $compra['iva_incluido'] ? 'Sí' : 'No'; ?></td>
                      <td>
                        <form method="post" action="/compras/eliminar/<?php echo $compra['id']; ?>" class="delete-form" style="display: inline;">
                          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">
                          <button type="button" class="btn btn-danger btn-sm delete-btn" title="Eliminar">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </main>
  </div>

  <!-- Modal de confirmación para eliminar -->
  <div id="delete-modal" class="modal" style="display: none;">
    <div class="modal-content">
      <h3>Confirmar Eliminación</h3>
      <p>¿Estás seguro de que deseas eliminar esta compra?</p>
      <div class="modal-actions">
        <button id="confirm-delete" class="btn btn-danger">Eliminar</button>
        <button id="cancel-delete" class="btn btn-secondary">Cancelar</button>
      </div>
    </div>
  </div>

  <script>
    // Manejar eliminación con confirmación
    let deleteForm = null;
    document.querySelectorAll('.delete-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        deleteForm = this.closest('.delete-form');
        document.getElementById('delete-modal').style.display = 'flex';
      });
    });

    document.getElementById('confirm-delete').addEventListener('click', function() {
      if (deleteForm) {
        deleteForm.submit();
      }
    });

    document.getElementById('cancel-delete').addEventListener('click', function() {
      document.getElementById('delete-modal').style.display = 'none';
      deleteForm = null;
    });

    // Mostrar mensajes flash
    <?php if (isset($_SESSION['success'])): ?>
      alert('<?php echo addslashes($_SESSION['success']); ?>');
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      alert('<?php echo addslashes($_SESSION['error']); ?>');
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
  </script>
</body>
</html>