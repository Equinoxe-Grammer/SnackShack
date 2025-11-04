<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $product ? 'Editar Producto' : 'Nuevo Producto'; ?></title>
  <link rel="icon" type="image/svg+xml" href="/assets/favicon.svg">

  <!--  <!-- Sistema de diseño universal - DEBE CARGARSE PRIMERO -->
  <link rel="stylesheet" href="/css/theme.css?v=20251013">
  <!-- Estilos específicos de página -->
  <link rel="stylesheet" href="/css/catalogofunciones.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <script src="/js/theme-toggle.js"></script>
</head>
<body style="background: var(--bg);">
t-flex">
  <?php $active = 'productos'; include __DIR__ . '/../partials/sidebar.php'; ?>
  <main class="main-content">
    <header class="catalog-header">
      <h1><?php echo $product ? 'Editar Producto' : 'Nuevo Producto'; ?></h1>
      <p><?php echo $product ? 'Modifica la información del producto' : 'Crea un nuevo producto para el catálogo'; ?></p>
    </header>

    <section class="products-section">
      <div class="product-form-container">
        <div class="card">
          <div class="card-body">
            <form method="post" action="<?php echo $product ? '/productos/actualizar/' . (int)$product->id : '/productos/guardar'; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">

            <div class="form-grid">
              <div class="form-group">
                <label for="nombre_producto">Nombre del Producto</label>
                <input type="text" id="nombre_producto" name="nombre_producto" class="form-control" required 
                       value="<?php echo htmlspecialchars($product->name ?? ''); ?>" 
                       placeholder="Ej: Frappé de Chocolate">
              </div>
              
              <div class="form-group">
                <label for="categoria_id">Categoría</label>
                <select id="categoria_id" name="categoria_id" class="form-control" required>
                  <option value="">Selecciona una categoría</option>
                  <?php foreach ($categories as $c): ?>
                    <option value="<?php echo (int)$c->id; ?>" <?php echo ($product && $product->categoryId == $c->id) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($c->name); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              
              <div class="form-group full-width">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" class="form-control" rows="4" 
                          placeholder="Describe el producto, ingredientes, características..."><?php echo htmlspecialchars($product->description ?? ''); ?></textarea>
              </div>
              
              <div class="form-group full-width">
                <label for="url_imagen">URL de Imagen (opcional)</label>
                <input type="text" id="url_imagen" name="url_imagen" class="form-control" 
                       value="<?php echo htmlspecialchars($product->imageUrl ?? ''); ?>" 
                       placeholder="https://ejemplo.com/imagen.jpg">
                <small style="color: #6b7280; font-size: .8rem; margin-top: .4rem; display: block;">
                  <i class="fas fa-info-circle"></i> También puedes subir imágenes directamente desde el catálogo
                </small>
              </div>
              
              <div class="form-group full-width">
                <div class="checkbox-wrapper">
                  <input type="checkbox" name="activo" id="activo" <?php echo (!$product || $product->active) ? 'checked' : ''; ?>>
                  <label for="activo">Producto activo en el catálogo</label>
                </div>
              </div>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn primary">
                <i class="fas fa-save"></i> 
                <?php echo $product ? 'Actualizar' : 'Crear'; ?> Producto
              </button>
              <a class="btn btn-secondary" href="/productos">
                <i class="fas fa-times"></i> Cancelar
              </a>
            </div>
          </form>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>

  <script>
  function showToast(msg, type = 'info') {
    let el = document.getElementById('toast');
    if (!el) {
      el = document.createElement('div');
      el.id = 'toast';
      el.className = 'toast';
      document.body.appendChild(el);
    }
    el.className = `toast ${type} show`;
    el.textContent = msg;
    setTimeout(() => { el.className = 'toast'; }, 3000);
  }

  // Mostrar feedback si viene por query (?ok=1 o ?err=msg)
  (function() {
    const params = new URLSearchParams(window.location.search);
    if (params.has('ok')) showToast('Producto guardado correctamente', 'success');
    if (params.has('err')) showToast(params.get('err') || 'Ocurrió un error', 'error');
  })();
  </script>
</body>
</html>