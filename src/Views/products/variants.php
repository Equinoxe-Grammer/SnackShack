<?php
/** @var array $variants Variantes del producto */
/** @var int $productId ID del producto */
/** @var string $csrf Token CSRF */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Variantes del Producto</title>
  
    <!--    <!-- Sistema de diseño universal - DEBE CARGARSE PRIMERO -->
    <link rel="stylesheet" href="/css/theme.css?v=20251013">
    <!-- Estilos específicos de página -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="/css/catalogofunciones.css">
</head>
<body style="background: #f8f9fe;">
flex">
    <?php $active = 'productos'; include __DIR__ . '/../partials/sidebar.php'; ?>
    <main class="main-content">
        <header class="catalog-header">
            <h1>Variantes del Producto</h1>
            <p>Administra las variantes del producto seleccionado</p>
        </header>

        <div class="toolbar">
            <div>
                <a href="/productos" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver al catálogo</a>
            </div>
            <div>
                <a href="/productos/<?php echo (int)$productId; ?>/variantes/nueva" class="btn primary"><i class="fas fa-plus"></i> Nueva Variante</a>
            </div>
        </div>

    <section class="products-section">
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Volumen (oz)</th>
                <th>Precio</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($variants)): ?>
            <tr>
                <td colspan="5" style="text-align: center; padding: 2rem; color: #6b7280;">
                    <i class="fas fa-box-open" style="font-size: 2rem; margin-bottom: 1rem; display: block; color: #d1d5db;"></i>
                    <p>No hay variantes creadas para este producto</p>
                    <a href="/productos/<?php echo (int)$productId; ?>/variantes/nueva" class="btn primary" style="margin-top: 1rem;">
                        <i class="fas fa-plus"></i> Crear primera variante
                    </a>
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($variants as $v): ?>
            <tr>
                <td data-label="Nombre"><?php echo htmlspecialchars($v->name); ?></td>
                <td data-label="Volumen (oz)"><?php echo $v->volumeOunces !== null ? htmlspecialchars($v->volumeOunces) : '-'; ?></td>
                <td data-label="Precio">$<?php echo number_format($v->price, 2); ?></td>
                <td data-label="Estado"><?php echo $v->active ? '<span class="badge">Activo</span>' : '<span class="badge muted">Inactivo</span>'; ?></td>
                <td data-label="Acciones">
                    <div class="actions">
                        <form method="post" action="/productos/<?php echo (int)$productId; ?>/variantes/estado/<?php echo (int)$v->id; ?>" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">
                            <input type="hidden" name="estado" value="<?php echo $v->active ? '0' : '1'; ?>">
                            <button type="submit" class="btn <?php echo $v->active ? 'warning' : 'success'; ?>">
                                <i class="fas fa-toggle-<?php echo $v->active ? 'off' : 'on'; ?>"></i>
                                <?php echo $v->active ? 'Desactivar' : 'Activar'; ?>
                            </button>
                        </form>
                        <a class="btn btn-outline" href="/productos/<?php echo (int)$productId; ?>/variantes/editar/<?php echo (int)$v->id; ?>"><i class="fas fa-pen"></i>Editar</a>
                        <form method="post" action="/productos/<?php echo (int)$productId; ?>/variantes/eliminar/<?php echo (int)$v->id; ?>" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">
                            <button type="submit" class="btn danger"><i class="fas fa-trash"></i>Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    </main>
</div>
</body>
</html>
<!-- Modal de confirmación de eliminación -->
<div id="deleteVariantModal" class="modal" style="display:none;">
    <div class="modal-content" style="max-width:400px; margin:auto;">
        <div class="modal-header">
            <h3 style="margin:0;"><i class="fas fa-trash"></i> Confirmar eliminación</h3>
        </div>
        <div class="modal-body" style="padding:1.2rem;">
            <p>¿Estás seguro de que deseas eliminar esta variante?<br><b>Esta acción no se puede deshacer.</b></p>
        </div>
        <div class="modal-footer" style="display:flex; gap:1rem; justify-content:flex-end;">
            <button type="button" id="cancelDeleteBtn" class="btn btn-secondary">Cancelar</button>
            <button type="button" id="confirmDeleteBtn" class="btn danger"><i class="fas fa-trash"></i> Eliminar</button>
        </div>
    </div>
</div>
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

// Mostrar feedback si viene por query (?ok=1 o ?err=msg)
(function() {
    const params = new URLSearchParams(window.location.search);
    if (params.has('ok')) showToast('Operación realizada correctamente', 'success');
    if (params.has('info')) showToast(params.get('info'), 'info');
    if (params.has('err')) showToast(params.get('err') || 'Ocurrió un error', 'error');
})();

// Protección contra doble click en botones de agregar variante
function disableOnClick(selector) {
    document.querySelectorAll(selector).forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (btn.disabled) {
                e.preventDefault();
                return false;
            }
            btn.disabled = true;
            setTimeout(() => { btn.disabled = false; }, 2000); // Rehabilita tras 2s
        });
    });
}
// Aplica a todos los botones de agregar variante
window.addEventListener('DOMContentLoaded', function() {
    disableOnClick('a.btn.primary');
    disableOnClick('a.btn[style*="margin-top"]'); // Para el botón en el estado vacío
});

// Modal de confirmación de eliminación
let formToDelete = null;

// Intercepta todos los formularios de eliminar variante
window.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form[action*="/variantes/eliminar/"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            formToDelete = form;
            document.getElementById('deleteVariantModal').style.display = 'block';
        });
    });
    document.getElementById('cancelDeleteBtn').onclick = function() {
        document.getElementById('deleteVariantModal').style.display = 'none';
        formToDelete = null;
    };
    document.getElementById('confirmDeleteBtn').onclick = function() {
        if (formToDelete) formToDelete.submit();
        document.getElementById('deleteVariantModal').style.display = 'none';
        formToDelete = null;
    };
});
</script>
