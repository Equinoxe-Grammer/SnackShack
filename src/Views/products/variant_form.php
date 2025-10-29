<?php
/** @var int $productId ID del producto */
/** @var \App\Models\Variant|null $variant Variante a editar (null si es nueva) */
/** @var string $csrf Token CSRF */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $variant ? 'Editar Variante' : 'Nueva Variante'; ?></title>
    <!-- Sistema de diseño universal - DEBE CARGARSE PRIMERO -->
    <link rel="stylesheet" href="/css/theme.css?v=20251013">
    <!-- Estilos específicos de página -->
    <link rel="stylesheet" href="/css/catalogofunciones.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body style="background: #f8f9fe;">
<div class="layout-flex">
    <?php $active = 'productos'; include __DIR__ . '/../partials/sidebar.php'; ?>
    <main class="main-content">
        <header class="catalog-header">
            <h1><?php echo $variant ? 'Editar Variante' : 'Nueva Variante'; ?></h1>
            <p><?php echo $variant ? 'Modifica los detalles de la variante' : 'Crea una nueva variante para el producto'; ?></p>
        </header>

                <section class="products-section">
                    <div class="form-container">
                        <div class="card">
                            <div class="card-body">
                    <form method="post" action="<?php echo $variant ? '/productos/' . (int)$productId . '/variantes/actualizar/' . (int)$variant->id : '/productos/' . (int)$productId . '/variantes/guardar'; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">
                        
                        <div class="form-group">
                            <label for="nombre_variante">Nombre de Variante</label>
                            <input type="text" id="nombre_variante" name="nombre_variante" class="form-control" required 
                                   value="<?php echo htmlspecialchars($variant->name ?? ''); ?>" 
                                   placeholder="Ej: Grande, Mediano, Pequeño">
                        </div>
                        
                        <div class="form-group">
                            <label for="volumen_onzas">Volumen (oz)</label>
                            <input type="number" id="volumen_onzas" step="0.01" name="volumen_onzas" class="form-control"
                                   value="<?php echo htmlspecialchars($variant->volumeOunces ?? ''); ?>" 
                                   placeholder="Ej: 16.5">
                        </div>
                        
                        <div class="form-group">
                            <label for="precio">Precio</label>
                            <input type="number" id="precio" step="0.01" name="precio" class="form-control" required 
                                   value="<?php echo htmlspecialchars($variant->price ?? ''); ?>" 
                                   placeholder="Ej: 25.99">
                        </div>
                        
                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" name="activo" id="activo" <?php echo (!$variant || $variant->active) ? 'checked' : ''; ?>>
                                <label for="activo">Variante activa</label>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn primary">
                                <i class="fas fa-save"></i> 
                                <?php echo $variant ? 'Actualizar' : 'Crear'; ?> Variante
                            </button>
                            <a class="btn" href="/productos/<?php echo (int)$productId; ?>/variantes">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
<script>
function showToast(msg, type = 'info') {
    let el = document.getElementById('toast');
    if (!el) {
        el = document.createElement('div');
        el.id = 'toast';
        el.style.position = 'fixed';
        el.style.right = '16px';
        el.style.bottom = '16px';
        el.style.padding = '.75rem 1rem';
        el.style.borderRadius = '8px';
        el.style.boxShadow = '0 6px 18px rgba(0,0,0,.15)';
        el.style.color = '#fff';
        el.style.zIndex = 9999;
        document.body.appendChild(el);
    }
    el.style.background = type === 'success' ? '#28a745' : (type === 'error' ? '#dc3545' : '#343a40');
    el.textContent = msg;
    el.style.opacity = '1';
    setTimeout(() => { el.style.transition = 'opacity .4s'; el.style.opacity = '0'; }, 2200);
}
</script>
