<?php
use App\Middleware\CsrfMiddleware;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = [
    'nombre' => $_SESSION['usuario'] ?? 'Usuario',
    'rol' => $_SESSION['rol'] ?? 'admin',
];

$csrfToken = CsrfMiddleware::getToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
    <title>Catálogo de Productos</title>
    <link rel="icon" type="image/svg+xml" href="/assets/favicon.svg">
     <link rel="stylesheet" href="/css/theme.css">
    <link rel="stylesheet" href="/css/catalogo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <script src="/js/theme-toggle.js"></script>
</head>
<body>
    <?php $active = 'productos'; include __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="main-content">
        <header class="dashboard-header">
            <div>
                <h1><i class="fas fa-box-open"></i> Catálogo de Productos</h1>
                <p class="subtitle">Gestiona tu inventario de productos</p>
            </div>
        </header>

        <!-- Filtros -->
        <section class="filters-section">
            <div class="filters-container">
                <div class="filter-group">
                    <label for="searchInput">
                        <i class="fas fa-search"></i>
                        Buscar
                    </label>
                    <input type="text" id="searchInput" placeholder="Nombre del producto..." autocomplete="off">
                </div>
                <div class="filter-group">
                    <label for="categoryFilter">
                        <i class="fas fa-tag"></i>
                        Categoría
                    </label>
                    <select id="categoryFilter">
                        <option value="0">Todas las categorías</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo (int)$cat->id; ?>"><?php echo htmlspecialchars($cat->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="statusFilter">
                        <i class="fas fa-toggle-on"></i>
                        Estado
                    </label>
                    <select id="statusFilter">
                        <option value="all">Todos los estados</option>
                        <option value="active">Activos</option>
                        <option value="inactive">Inactivos</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <a href="/productos/nuevo" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Nuevo Producto
                    </a>
                    <button id="clearFilters" class="btn btn-outline">
                        <i class="fas fa-eraser"></i>
                        Limpiar Filtros
                    </button>
                </div>
            </div>
        </section>

        <!-- KPIs -->
        <section class="summary-section">
            <div class="summary-card">
                <div class="card-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="card-content">
                    <h3 id="statCount">0</h3>
                    <p class="text-muted">Productos Filtrados</p>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="card-content">
                    <h3 id="statVariants">0</h3>
                    <p class="text-muted">Variantes Totales</p>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="card-content">
                    <h3 id="statActive">0</h3>
                    <p class="text-muted">Productos Activos</p>
                </div>
            </div>
        </section>

        <!-- Lista de productos -->
        <section class="products-section">
            <div id="productsContainer" class="products-list">
                <?php if (empty($products)): ?>
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <h3>No hay productos registrados</h3>
                        <p>Comienza agregando tu primer producto al catálogo</p>
                        <a href="/productos/nuevo" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Crear Producto
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                <div class="product-card" 
                    data-product-id="<?php echo (int)$p->id; ?>"
                         data-categoria="<?php echo (int)($p->categoryId ?? 0); ?>"
                         data-activo="<?php echo $p->active ? '1' : '0'; ?>"
                         data-variantes="<?php echo count($p->variants); ?>">
                        
                        <!-- Imagen del producto -->
                        <div class="product-image">
                            <?php if ($p->hasImage): ?>
                                <img src="/api/productos/<?php echo (int)$p->id; ?>/imagen" alt="<?php echo htmlspecialchars($p->name); ?>">
                            <?php elseif (!empty($p->imageUrl)): ?>
                                <img src="/assets/<?php echo htmlspecialchars($p->imageUrl); ?>" alt="<?php echo htmlspecialchars($p->name); ?>">
                            <?php else: ?>
                                <div class="no-image">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Información del producto -->
                        <div class="product-info">
                            <div class="product-header">
                                <h3 class="product-name"><?php echo htmlspecialchars($p->name); ?></h3>
                                <?php if ($p->active): ?>
                                    <span class="badge badge-active">
                                        <i class="fas fa-check-circle"></i>
                                        Activo
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-details">
                                <span class="detail-item text-muted">
                                    <i class="fas fa-tag"></i>
                                    <?php echo htmlspecialchars($p->categoryName ?? 'Sin categoría'); ?>
                                </span>
                                <span class="detail-separator">•</span>
                                <span class="detail-item text-muted">
                                    <i class="fas fa-layer-group"></i>
                                    <?php echo count($p->variants); ?> variante(s)
                                </span>
                            </div>

                            <!-- Información de precios y márgenes -->
                            <div class="product-pricing-info">
                                <?php if (isset($p->precio_min) && isset($p->precio_max)): ?>
                                <div class="pricing-row">
                                    <span class="price-label text-muted">
                                        <i class="fas fa-dollar-sign"></i>
                                        Rango de precios:
                                    </span>
                                    <span class="price-value">
                                        <?php if ($p->precio_min == $p->precio_max): ?>
                                            $<?php echo number_format($p->precio_min, 2); ?>
                                        <?php else: ?>
                                            $<?php echo number_format($p->precio_min, 2); ?> - $<?php echo number_format($p->precio_max, 2); ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <?php endif; ?>

                                <?php if (isset($p->precio_promedio)): ?>
                                <div class="pricing-row">
                                    <span class="price-label text-muted">
                                        <i class="fas fa-chart-bar"></i>
                                        Precio promedio:
                                    </span>
                                    <span class="price-value">$<?php echo number_format($p->precio_promedio, 2); ?></span>
                                </div>
                                <?php endif; ?>

                                <?php if (isset($p->neto_promedio)): ?>
                                <div class="pricing-row">
                                    <span class="price-label text-muted">
                                        <i class="fas fa-coins"></i>
                                        Neto promedio:
                                    </span>
                                    <span class="price-value">$<?php echo number_format($p->neto_promedio, 2); ?></span>
                                </div>
                                <?php endif; ?>

                                <?php if (isset($p->iva_promedio)): ?>
                                <div class="pricing-row">
                                    <span class="price-label text-muted">
                                        <i class="fas fa-receipt"></i>
                                        IVA promedio:
                                    </span>
                                    <span class="price-value">$<?php echo number_format($p->iva_promedio, 2); ?></span>
                                </div>
                                <?php endif; ?>

                                <?php if (isset($p->margen) && $p->margen !== null): ?>
                                <div class="pricing-row margin-row">
                                    <span class="price-label text-muted">
                                        <i class="fas fa-chart-line"></i>
                                        Margen:
                                    </span>
                                    <span class="price-value <?php echo $p->margen > 0 ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo number_format($p->margenPct ?? 0, 1); ?>% 
                                        ($<?php echo number_format($p->margen, 2); ?>)
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($p->description)): ?>
                            <p class="product-description text-muted"><?php echo htmlspecialchars($p->description); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Acciones -->
                        <div class="product-actions">
                            <button class="action-btn edit" 
                                    data-action="edit" 
                                    data-id="<?php echo (int)$p->id; ?>"
                                    title="Editar producto">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            <button class="action-btn image" 
                                    data-action="image" 
                                    data-id="<?php echo (int)$p->id; ?>"
                                    title="Cambiar imagen">
                                <i class="fas fa-camera"></i>
                            </button>
                            
                            <a href="/productos/<?php echo (int)$p->id; ?>/variantes" 
                               class="action-btn variants"
                               title="Gestionar variantes">
                                <i class="fas fa-cubes"></i>
                            </a>
                            
                            <form method="post" 
                                  action="/productos/estado/<?php echo (int)$p->id; ?>" 
                                  class="toggle-form"
                                  style="display: inline;">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                                <input type="hidden" name="estado" value="<?php echo $p->active ? '0' : '1'; ?>">
                                <button type="submit" 
                                        class="action-btn toggle <?php echo $p->active ? 'active' : 'inactive'; ?>" 
                                        title="<?php echo $p->active ? 'Desactivar' : 'Activar'; ?> producto">
                                    <i class="fas fa-toggle-<?php echo $p->active ? 'on' : 'off'; ?>"></i>
                                </button>
                            </form>
                            
                            <form method="post" 
                                  action="/productos/eliminar/<?php echo (int)$p->id; ?>" 
                                  class="delete-form"
                                  style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                                <button type="button" 
                                        class="action-btn delete" 
                                        data-action="delete" 
                                        data-id="<?php echo (int)$p->id; ?>"
                                        title="Eliminar producto">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Modal para subir imagen -->
        <div id="imageModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2><i class="fas fa-camera"></i> Subir Imagen de Producto</h2>
                    <button class="modal-close" id="cancelImageBtn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="imageForm" enctype="multipart/form-data" autocomplete="off" class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    
                    <div class="form-group">
                        <label for="productImage">
                            <i class="fas fa-file-image"></i>
                            Selecciona una imagen
                        </label>
                        <input type="file" 
                               id="productImage" 
                               name="imagen" 
                               accept=".jpg,.jpeg,.png,.webp"
                               required
                               style="width:100%;"
                        >
                        <small style="display:block; margin-top:.3rem; color:#6c757d;">
                            <b>Formatos permitidos:</b> JPEG (*.jpg, *.jpeg), PNG (*.png), WEBP (*.webp)<br>
                            <b>Custom Files:</b> *.jpg, *.jpeg, *.png, *.webp
                        </small>
                        <div id="imagePreviewContainer" class="image-preview" style="display:none; margin-top: .6rem; max-height:350px; overflow:auto;">
                            <img id="imagePreview" alt="Vista previa" style="max-width:100%; max-height:320px; border-radius: 8px; border:1px solid #e5e7eb; display:block; margin:auto;">
                            <small id="imageInfo" class="form-hint" style="display:block; margin-top:.4rem;"></small>
                        </div>
                        <small class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Formatos: JPG, PNG, WebP. Máximo: 5MB. Recomendado: 800x800px
                        </small>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="cancelImageBtn2">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i>
                            Subir Imagen
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal para confirmar eliminación de producto -->
        <div id="deleteProductModal" class="modal">
            <div class="modal-content" style="max-width:420px;">
                <div class="modal-header">
                    <h2><i class="fas fa-trash"></i> Eliminar producto</h2>
                    <button class="modal-close" id="cancelDeleteProductBtn" title="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>¿Seguro que deseas eliminar este producto?<br>
                    <b>Se desactivará del catálogo y no estará disponible para ventas.</b></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelDeleteProductBtn2">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="button" class="btn-primary danger" id="confirmDeleteProductBtn">
                        <i class="fas fa-trash"></i>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>

        <!-- Toast para notificaciones -->
        <div id="toast" class="toast"></div>
    </main>

<script>
const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
const csrfToken = csrfTokenMeta ? csrfTokenMeta.content : '';
// Referencias al DOM
const searchInput = document.getElementById('searchInput');
const categoryFilter = document.getElementById('categoryFilter');
const statusFilter = document.getElementById('statusFilter');
const clearFiltersBtn = document.getElementById('clearFilters');
const productsContainer = document.getElementById('productsContainer');
const imageModal = document.getElementById('imageModal');
const imageForm = document.getElementById('imageForm');
const cancelImageBtn = document.getElementById('cancelImageBtn');
const cancelImageBtn2 = document.getElementById('cancelImageBtn2');
const imagePreviewContainer = document.getElementById('imagePreviewContainer');
const imagePreview = document.getElementById('imagePreview');
const imageInfo = document.getElementById('imageInfo');
const productImageInput = document.getElementById('productImage');

// Modal eliminar producto
const deleteProductModal = document.getElementById('deleteProductModal');
const cancelDeleteProductBtn = document.getElementById('cancelDeleteProductBtn');
const cancelDeleteProductBtn2 = document.getElementById('cancelDeleteProductBtn2');
const confirmDeleteProductBtn = document.getElementById('confirmDeleteProductBtn');
let formToDeleteProduct = null;

let currentProductIdForImage = null;

// Toast notifications
function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = `toast show ${type}`;
    setTimeout(() => {
        toast.className = 'toast';
    }, 3000);
}

// Feedback desde query string (mensajes después de acciones como eliminar)
(function() {
    const params = new URLSearchParams(window.location.search);
    if (params.has('ok')) {
        showToast('Producto eliminado correctamente', 'success');
    }
    if (params.has('info')) {
        showToast(params.get('info'), 'info');
    }
    if (params.has('err')) {
        showToast(params.get('err') || 'Ocurrió un error', 'error');
    }
})();

// Filtrar productos
function filterProducts() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    const categoryId = categoryFilter.value;
    const status = statusFilter.value;
    
    const products = productsContainer.querySelectorAll('.product-card');
    
    let visibleCount = 0;
    let activeCount = 0;
    let totalVariants = 0;
    
    products.forEach(card => {
        const productName = card.querySelector('.product-name')?.textContent.toLowerCase() || '';
        const productCategory = card.dataset.categoria;
        const productActive = card.dataset.activo === '1';
        const productVariants = parseInt(card.dataset.variantes || '0', 10);
        
        // Aplicar filtros
        const matchesSearch = !searchTerm || productName.includes(searchTerm);
        const matchesCategory = !categoryId || categoryId === '0' || productCategory === categoryId;
        const matchesStatus = !status || status === 'all' || 
                             (status === 'active' && productActive) || 
                             (status === 'inactive' && !productActive);
        
        const isVisible = matchesSearch && matchesCategory && matchesStatus;
        
        card.style.display = isVisible ? 'flex' : 'none';
        
        if (isVisible) {
            visibleCount++;
            if (productActive) activeCount++;
            totalVariants += productVariants;
        }
    });
    
    // Actualizar KPIs
    document.getElementById('statCount').textContent = visibleCount;
    document.getElementById('statActive').textContent = activeCount;
    document.getElementById('statVariants').textContent = totalVariants;
}

// Limpiar filtros
clearFiltersBtn.addEventListener('click', () => {
    searchInput.value = '';
    categoryFilter.value = '0';
    statusFilter.value = 'all';
    filterProducts();
});

// Event listeners para filtros
searchInput.addEventListener('input', filterProducts);
categoryFilter.addEventListener('change', filterProducts);
statusFilter.addEventListener('change', filterProducts);

// Manejar acciones de productos
productsContainer.addEventListener('click', async (e) => {
    const btn = e.target.closest('button[data-action]');
    if (!btn) return;
    
    const action = btn.dataset.action;
    const productId = parseInt(btn.dataset.id, 10);
    
    switch (action) {
        case 'edit':
            window.location.href = `/productos/editar/${productId}`;
            break;
            
        case 'image':
            currentProductIdForImage = productId;
            imageModal.classList.add('show');
            break;
            
        case 'delete':
            formToDeleteProduct = btn.closest('form.delete-form');
            if (deleteProductModal) deleteProductModal.classList.add('show');
            break;
    }
});

// Handlers del modal de eliminar producto
function closeDeleteProductModal() {
    if (deleteProductModal) deleteProductModal.classList.remove('show');
}
if (cancelDeleteProductBtn) cancelDeleteProductBtn.addEventListener('click', closeDeleteProductModal);
if (cancelDeleteProductBtn2) cancelDeleteProductBtn2.addEventListener('click', closeDeleteProductModal);
if (deleteProductModal) deleteProductModal.addEventListener('click', (e) => {
    if (e.target === deleteProductModal) closeDeleteProductModal();
});
if (confirmDeleteProductBtn) confirmDeleteProductBtn.addEventListener('click', () => {
    if (formToDeleteProduct) {
        formToDeleteProduct.submit();
        formToDeleteProduct = null;
    }
    closeDeleteProductModal();
});

// Modal handlers
cancelImageBtn.addEventListener('click', () => {
    imageModal.classList.remove('show');
    currentProductIdForImage = null;
    imageForm.reset();
    if (imagePreviewContainer) imagePreviewContainer.style.display = 'none';
});

cancelImageBtn2.addEventListener('click', () => {
    imageModal.classList.remove('show');
    currentProductIdForImage = null;
    imageForm.reset();
    if (imagePreviewContainer) imagePreviewContainer.style.display = 'none';
});

imageModal.addEventListener('click', (e) => {
    if (e.target === imageModal) {
        imageModal.classList.remove('show');
        currentProductIdForImage = null;
        imageForm.reset();
        if (imagePreviewContainer) imagePreviewContainer.style.display = 'none';
    }
});

// Preview de imagen seleccionada
if (productImageInput) {
    productImageInput.addEventListener('change', () => {
        const file = productImageInput.files && productImageInput.files[0];
        if (!file) {
            if (imagePreviewContainer) imagePreviewContainer.style.display = 'none';
            return;
        }
        const reader = new FileReader();
        reader.onload = () => {
            if (imagePreview) imagePreview.src = reader.result;
            if (imagePreviewContainer) imagePreviewContainer.style.display = 'block';
            const img = new Image();
            img.onload = () => {
                const sizeKB = (file.size / 1024).toFixed(1);
                if (imageInfo) imageInfo.textContent = `${img.width}×${img.height}px • ${sizeKB}KB`;
            };
            img.src = reader.result;
        };
        reader.readAsDataURL(file);
    });
}

// Submit imagen
imageForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!currentProductIdForImage) return;
    
    const fileInput = imageForm.querySelector('input[type="file"]');
    const file = fileInput.files[0];
    
    if (!file) {
        showToast('Por favor selecciona una imagen', 'error');
        return;
    }
    
    // Validar tamaño (5MB máximo)
    if (file.size > 5242880) {
        const sizeMB = (file.size / 1048576).toFixed(1);
        showToast(`Imagen demasiado grande (${sizeMB}MB). Máximo: 5MB`, 'error');
        return;
    }
    
    const formData = new FormData(imageForm);
    
    try {
        showToast('Subiendo imagen...', 'info');
        
        const res = await fetch(`/api/productos/${currentProductIdForImage}/imagen`, {
            method: 'POST',
                    headers: {
                'X-CSRF-Token': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const data = await res.json();
        
        if (!res.ok) {
            throw new Error(data.error || `HTTP ${res.status}`);
        }
        
        let successMsg = data.message || 'Imagen actualizada correctamente';
        if (data.processing_info) {
            const info = data.processing_info;
            successMsg += ` (${info.processed_size_kb}KB)`;
            if (info.saved_space && info.saved_space !== 'Sin compresión adicional') {
                successMsg += ` - ${info.saved_space}`;
            }
        }
        
        showToast(successMsg, 'success');
        // Actualizar imagen del card sin recargar
        const card = document.querySelector(`.product-card[data-product-id='${currentProductIdForImage}']`);
        if (card) {
            let imgEl = card.querySelector('.product-image img');
            if (!imgEl) {
                const container = card.querySelector('.product-image');
                if (container) {
                    container.innerHTML = '';
                    imgEl = document.createElement('img');
                    container.appendChild(imgEl);
                }
            }
            if (imgEl) {
                imgEl.src = `/api/productos/${currentProductIdForImage}/imagen?t=${Date.now()}`;
                imgEl.alt = 'Imagen de producto actualizada';
            }
        }
        // Cerrar modal y limpiar
        imageModal.classList.remove('show');
        currentProductIdForImage = null;
        imageForm.reset();
        if (imagePreviewContainer) imagePreviewContainer.style.display = 'none';
        
    } catch (err) {
        showToast('Error subiendo imagen: ' + (err.message || err), 'error');
    }
});

// Inicializar
filterProducts();
</script>
</body>
</html>