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
    <title>Punto de Venta</title>
    <link rel="icon" type="image/svg+xml" href="/assets/favicon.svg">
        <link rel="stylesheet" href="/css/theme.css">
    <link rel="stylesheet" href="/css/venta.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
</head>
<body>
    <div class="pos-container">
        <?php $active='ventas'; include __DIR__ . '/../partials/sidebar.php'; ?>

                <main class="main-content">
            <header>
                <h1>Punto de Venta</h1>
                <p>Selecciona productos para agregar al carrito</p>
            </header>

            <!-- Filtros y categorías -->
            <div class="filters-header">
                <div class="filters-row">
                    <div class="category-tabs-container">
                        <label class="category-tabs-label">
                            <i class="fas fa-filter"></i>
                            Filtrar por Categoría
                        </label>
                        <div class="category-tabs" id="categoryTabs">
                            <!-- Tabs se generan dinámicamente -->
                        </div>
                    </div>

                    <div class="order-selector">
                        <label for="orderSelect">
                            <i class="fas fa-sort"></i>
                            Ordenar por
                        </label>
                        <select id="orderSelect">
                            <option value="category">Categoría</option>
                            <option value="name">Nombre (A-Z)</option>
                            <option value="priceAsc">Precio (Menor a Mayor)</option>
                            <option value="priceDesc">Precio (Mayor a Menor)</option>
                            <option value="popularity">Popularidad</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="product-grid" id="productGrid">
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i> Cargando productos...
                </div>
            </div>
        </main>

        <aside class="cart-sidebar">
            <div class="cart-header">
                <h2><i class="fas fa-shopping-cart"></i> Carrito</h2>
                <span id="itemsCount">Items: 0</span>
            </div>

            <div class="cart-items" id="cartItems">
                <div class="empty-cart">
                    <i class="fas fa-shopping-bag"></i>
                    <p>Carrito vacío</p>
                    <small>Agrega productos para empezar</small>
                </div>
            </div>

            <div class="cart-footer">
                <div class="payment-section">
                    <h3>Método de Pago</h3>
                    <div class="payment-options" id="paymentOptions">
                        <div class="loading-small">
                            <i class="fas fa-spinner fa-spin"></i> Cargando...
                        </div>
                    </div>
                </div>

                <div class="cart-total">
                    <span>Total:</span>
                    <strong id="totalAmount">$0.00</strong>
                </div>

                <div class="cart-actions">
                    <button type="button" class="btn-process" id="processBtn" disabled>
                        <i class="fas fa-check"></i> Procesar Venta
                    </button>
                    <button type="button" class="btn-clear" id="clearBtn" disabled>
                        <i class="fas fa-trash"></i> Limpiar
                    </button>
                </div>
            </div>
        </aside>
    </div>

    <!-- Modal de confirmación para limpiar carrito -->
<div id="clearCartModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-trash"></i> Limpiar carrito</h3>
        </div>
        <div class="modal-body">
            <p>¿Seguro que deseas limpiar el carrito?<br><b>Esta acción no se puede deshacer.</b></p>
        </div>
        <div class="modal-footer">
            <button type="button" id="cancelClearCartBtn" class="btn">Cancelar</button>
            <button type="button" id="confirmClearCartBtn" class="btn danger"><i class="fas fa-trash"></i> Limpiar</button>
        </div>
    </div>
</div>

    <script>
        // ===== CONSTANTES Y ESTADO GLOBAL =====
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenMeta ? csrfTokenMeta.content : '';
        const formatoMoneda = new Intl.NumberFormat('es-MX', {
            style: 'currency',
            currency: 'MXN'
        });

    let productosMap = new Map();
    let productosList = [];
    let categorias = new Map(); // categoria_id -> nombre_categoria
    let categoriaActiva = 0; // 0 = Todas
        let cart = [];
        let metodoSeleccionado = null;

        // ===== REFERENCIAS AL DOM =====
        const productGrid = document.getElementById('productGrid');
        const cartItemsEl = document.getElementById('cartItems');
        const itemsCountEl = document.getElementById('itemsCount');
        const totalAmountEl = document.getElementById('totalAmount');
        const paymentOptionsEl = document.getElementById('paymentOptions');
        const processBtn = document.getElementById('processBtn');
        const clearBtn = document.getElementById('clearBtn');

        // ===== INICIALIZACIÓN =====
        document.addEventListener('DOMContentLoaded', () => {
            inicializar();
        });

        async function inicializar() {
            await cargarProductos();
            await cargarMetodosPago();
            configurarEventos();
        }

        function configurarEventos() {
    productGrid.addEventListener('click', manejarClickProducto);
    cartItemsEl.addEventListener('click', manejarClickCarrito);
    processBtn.addEventListener('click', (event) => {
        event.preventDefault();
        procesarVenta();
    });
    clearBtn.addEventListener('click', (event) => {
        event.preventDefault();
        document.getElementById('clearCartModal').classList.add('show');
    });
}

function manejarClickProducto(event) {
    const boton = event.target.closest('.add-btn');
    if (!boton) {
        return;
    }

    const productoId = parseInt(boton.dataset.productoId, 10);
    const varianteId = parseInt(boton.dataset.varianteId, 10);

    if (!productoId || !varianteId) {
        return;
    }

    const producto = productosMap.get(productoId);
    if (!producto) {
        return;
    }

    const variante = producto.variantes.find((v) => v.variante_id === varianteId);
    if (!variante) {
        return;
    }

    agregarAlCarrito(producto, variante);
}

function manejarClickCarrito(event) {
    const itemElemento = event.target.closest('.cart-item');
    if (!itemElemento) {
        return;
    }

    const varianteId = parseInt(itemElemento.dataset.varianteId, 10);

    if (event.target.closest('.delete-item')) {
        eliminarDelCarrito(varianteId);
        return;
    }

    const botonCantidad = event.target.closest('.quantity-selector button');
    if (!botonCantidad) {
        return;
    }

    const accion = botonCantidad.dataset.action;
    if (accion === 'increase') {
        ajustarCantidad(varianteId, 1);
    } else if (accion === 'decrease') {
        ajustarCantidad(varianteId, -1);
    }
}

function agregarAlCarrito(producto, variante) {
    const existente = cart.find((item) => item.variante_id === variante.variante_id);

    if (existente) {
        existente.cantidad += 1;
    } else {
        cart.push({
            variante_id: variante.variante_id,
            producto_nombre: producto.nombre,
            variante_nombre: variante.nombre,
            precio_unitario: variante.precio,
            costo_produccion_unitario: producto.costo_produccion ?? null,
            cantidad: 1
        });
    }

    renderCarrito();
}

function ajustarCantidad(varianteId, diferencia) {
    const item = cart.find((elemento) => elemento.variante_id === varianteId);
    if (!item) {
        return;
    }

    item.cantidad += diferencia;

    if (item.cantidad <= 0) {
        cart = cart.filter((elemento) => elemento.variante_id !== varianteId);
    }

    renderCarrito();
}

function eliminarDelCarrito(varianteId) {
    cart = cart.filter((item) => item.variante_id !== varianteId);
    renderCarrito();
}

        function limpiarCarrito() {
            if (!cart.length) return;
            document.getElementById('clearCartModal').classList.add('show');
        }

        function limpiarCarritoSinConfirmar() {
            cart = [];
            metodoSeleccionado = null;
            document.querySelectorAll('.payment-btn').forEach((btn) => btn.classList.remove('active'));
            renderCarrito();
        }

        function renderCarrito() {
    cartItemsEl.innerHTML = '';

    if (!cart.length) {
        const vacio = document.createElement('div');
        vacio.className = 'cart-item empty';
        vacio.textContent = 'El carrito está vacío.';
        cartItemsEl.appendChild(vacio);
    } else {
        cart.forEach((item) => {
            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item';
            cartItem.dataset.varianteId = item.variante_id;

            const info = document.createElement('div');
            info.className = 'item-info';

            const nombre = document.createElement('p');
            nombre.className = 'item-name';
            nombre.textContent = item.producto_nombre;

            const size = document.createElement('p');
            size.className = 'item-size';
            size.textContent = item.variante_nombre;

            const precioUnitario = document.createElement('p');
            precioUnitario.className = 'item-price-single';
            precioUnitario.textContent = formatoMoneda.format(item.precio_unitario);

            info.appendChild(nombre);
            info.appendChild(size);
            info.appendChild(precioUnitario);

            const controles = document.createElement('div');
            controles.className = 'item-controls';

            const selectorCantidad = document.createElement('div');
            selectorCantidad.className = 'quantity-selector';

            const btnMenos = document.createElement('button');
            btnMenos.type = 'button';
            btnMenos.dataset.action = 'decrease';
            btnMenos.textContent = '-';

            const cantidadActual = document.createElement('span');
            cantidadActual.textContent = item.cantidad;

            const btnMas = document.createElement('button');
            btnMas.type = 'button';
            btnMas.dataset.action = 'increase';
            btnMas.textContent = '+';

            selectorCantidad.appendChild(btnMenos);
            selectorCantidad.appendChild(cantidadActual);
            selectorCantidad.appendChild(btnMas);

            const totalItem = document.createElement('p');
            totalItem.className = 'item-price-total';
            totalItem.textContent = formatoMoneda.format(item.precio_unitario * item.cantidad);

            controles.appendChild(selectorCantidad);
            controles.appendChild(totalItem);

            const eliminarBtn = document.createElement('button');
            eliminarBtn.type = 'button';
            eliminarBtn.className = 'delete-item';

            const iconoEliminar = document.createElement('i');
            iconoEliminar.className = 'fas fa-trash-alt';
            eliminarBtn.appendChild(iconoEliminar);

            cartItem.appendChild(info);
            cartItem.appendChild(controles);
            cartItem.appendChild(eliminarBtn);

            cartItemsEl.appendChild(cartItem);
        });
    }

    const totalItems = cart.reduce((acc, item) => acc + item.cantidad, 0);
    const total = cart.reduce((acc, item) => acc + item.cantidad * item.precio_unitario, 0);
    const totalCostoProduccion = cart.reduce((acc, item) => {
        if (item.costo_produccion_unitario != null) {
            return acc + (item.costo_produccion_unitario * item.cantidad);
        }
        return acc;
    }, 0);

    // IVA 15% breakdown assuming total is final price (incluye IVA)
    const neto = +(total / 1.15).toFixed(2);
    const iva = +(total - neto).toFixed(2);

    itemsCountEl.textContent = `Items: ${totalItems}`;
    totalAmountEl.textContent = formatoMoneda.format(total);

    // Show production cost somewhere in footer (append or update existing element)
    let prodEl = document.getElementById('productionCost');
    if (!prodEl) {
        prodEl = document.createElement('div');
        prodEl.id = 'productionCost';
        prodEl.className = 'cart-production-cost';
        totalAmountEl.parentElement.insertAdjacentElement('afterend', prodEl);
    }
    prodEl.innerHTML = `<span>Costo producción:</span> <strong>${formatoMoneda.format(totalCostoProduccion)}</strong><br>
                         <small>Net: ${formatoMoneda.format(neto)} | IVA(15%): ${formatoMoneda.format(iva)}</small>`;
    processBtn.disabled = !cart.length || !metodoSeleccionado;
    clearBtn.disabled = !cart.length;
}

async function cargarProductos() {
    try {
        productGrid.innerHTML = '';
        console.log('[DEBUG] Cargando productos...');
        const respuesta = await fetch('/api/productos', { cache: 'no-store' });

        if (respuesta.status === 401) {
            console.warn('[AUTH] Sesión expirada, redirigiendo a login');
            window.location.href = '/login';
            return;
        }

        if (!respuesta.ok) {
            throw new Error(`HTTP ${respuesta.status}`);
        }

        const datos = await respuesta.json();
        console.log('[DEBUG] Productos recibidos:', datos.length);
        
        if (!Array.isArray(datos)) {
            throw new Error('Formato de respuesta inválido');
        }

        // Guardar catálogo y construir mapas auxiliares
        productosList = datos;
        productosMap = new Map();
        categorias = new Map();
        productosList.forEach((p) => {
            if (p && p.categoria_id && p.nombre_categoria) {
                categorias.set(p.categoria_id, p.nombre_categoria);
            }
        });
        renderTabsCategorias();
        renderProductos(filtrarYOrdenar(productosList));
    } catch (error) {
        console.error('[ERROR] cargarProductos:', error);
        mostrarMensajeProductos('No se pudieron cargar los productos. Por favor, intenta de nuevo.');
    }
}

function renderProductos(productos) {
    if (!productos.length) {
        mostrarMensajeProductos('No hay productos disponibles.');
        return;
    }

    productGrid.innerHTML = '';

    let rendered = false;

    productos.forEach((producto) => {
        // Filtrar por categoría si hay una activa
        if (categoriaActiva && Number(producto.categoria_id) !== Number(categoriaActiva)) {
            return;
        }
        const variantes = Array.isArray(producto.variantes) ? producto.variantes : [];

        const normalizado = {
            producto_id: Number(producto.producto_id) || 0,
            nombre: producto.nombre_producto || 'Producto',
            categoria_id: Number(producto.categoria_id) || 0,
            categoria: producto.nombre_categoria || '',
            url_imagen: producto.url_imagen || '',
            costo_produccion: producto.costo_produccion != null ? Number(producto.costo_produccion) : null,
            sales_count: Number(producto.sales_count) || 0,
            variantes: variantes.map((variante) => ({
                variante_id: Number(variante.variante_id) || 0,
                nombre: variante.nombre_variante || 'Variante',
                precio: Number(variante.precio) || 0
            }))
        };

        if (normalizado.producto_id <= 0 || !normalizado.variantes.length) {
            return;
        }

        productosMap.set(normalizado.producto_id, normalizado);

        const card = document.createElement('div');
        card.className = 'product-card';

        const imagen = document.createElement('img');
        // Usar placeholder por defecto
        imagen.src = '/assets/logo_snack.jpg';
        imagen.alt = normalizado.nombre;

        // Preferir imagen desde la BD si está disponible (API expone image_url cuando existe BLOB)
        if (producto.image_url) {
            imagen.src = producto.image_url;
        } else if (normalizado.url_imagen && normalizado.url_imagen.trim()) {
            // Fallback a assets si hay URL relativa
            const imagenReal = new Image();
            imagenReal.onload = () => {
                imagen.src = `/assets/${normalizado.url_imagen}`;
            };
            imagenReal.onerror = () => {
                // Ya tiene el placeholder, no hacer nada
                console.warn(`[WARN] No se pudo cargar imagen: ${normalizado.url_imagen}`);
            };
            imagenReal.src = `/assets/${normalizado.url_imagen}`;
        }

        const detalles = document.createElement('div');
        detalles.className = 'product-details';

        const titulo = document.createElement('h3');
        titulo.textContent = normalizado.nombre;

        const categoria = document.createElement('span');
        categoria.className = 'category';
        categoria.textContent = normalizado.categoria;

        // Mostrar costo de producción cuando esté disponible
        const costoProdEl = document.createElement('div');
        costoProdEl.className = 'product-cost';
        if (producto.costo_produccion != null) {
            costoProdEl.textContent = `Costo producción: ${formatoMoneda.format(Number(producto.costo_produccion))}`;
        } else {
            costoProdEl.textContent = '';
        }

        const listaVariantes = document.createElement('ul');
        listaVariantes.className = 'size-options';

        normalizado.variantes.forEach((variante) => {
            const item = document.createElement('li');

            const texto = document.createElement('span');
            texto.append(document.createTextNode(`${variante.nombre} `));

            const precio = document.createElement('strong');
            precio.textContent = formatoMoneda.format(variante.precio);
            texto.appendChild(precio);

            const boton = document.createElement('button');
            boton.type = 'button';
            boton.className = 'add-btn';
            boton.dataset.productoId = normalizado.producto_id;
            boton.dataset.varianteId = variante.variante_id;
            boton.textContent = '+';

            item.appendChild(texto);
            item.appendChild(boton);
            listaVariantes.appendChild(item);
        });

    detalles.appendChild(titulo);
    detalles.appendChild(categoria);
    detalles.appendChild(costoProdEl);
        detalles.appendChild(listaVariantes);

        card.appendChild(imagen);
        card.appendChild(detalles);

        productGrid.appendChild(card);
        rendered = true;
    });

    if (!rendered) {
        mostrarMensajeProductos('No hay productos disponibles.');
    }
}

function mostrarMensajeProductos(mensaje) {
    productGrid.innerHTML = '';
    const aviso = document.createElement('div');
    aviso.className = 'product-card';
    const texto = document.createElement('p');
    texto.textContent = mensaje;
    aviso.appendChild(texto);
    productGrid.appendChild(aviso);
}

// ==== Tabs de categorías y ordenamiento ====
function renderTabsCategorias() {
    const container = document.getElementById('categoryTabs');
    if (!container) return;
    container.innerHTML = '';

    const makeTab = (id, label) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'tab' + (Number(categoriaActiva) === Number(id) ? ' active' : '');
        btn.textContent = label;
        btn.addEventListener('click', () => {
            categoriaActiva = id;
            renderProductos(filtrarYOrdenar(productosList));
            renderTabsCategorias();
        });
        return btn;
    };

    // Tab "Todos"
    container.appendChild(makeTab(0, 'Todos'));

    // Orden lógico de categorías
    const orderMap = new Map([
        ['Frappes Gourmet', 1],
        ['Elotes', 2],
        ['Tostitos con Elote', 3],
        ['Vasolokos', 4],
        ['Adiciones', 5]
    ]);

    const cats = Array.from(categorias.entries())
        .map(([id, nombre]) => ({ id, nombre }))
        .sort((a, b) => (orderMap.get(a.nombre) || 99) - (orderMap.get(b.nombre) || 99));

    cats.forEach(c => container.appendChild(makeTab(c.id, c.nombre)));

    // Enlazar el selector de orden si no está enlazado
    const orderSelect = document.getElementById('orderSelect');
    if (orderSelect && !orderSelect._bound) {
        orderSelect.addEventListener('change', () => {
            renderProductos(filtrarYOrdenar(productosList));
        });
        orderSelect._bound = true;
    }
}

function filtrarYOrdenar(lista) {
    const orderSelect = document.getElementById('orderSelect');
    const modo = orderSelect ? orderSelect.value : 'category';

    const clone = Array.isArray(lista) ? lista.slice(0) : [];
    if (modo === 'name') {
        clone.sort((a, b) => String(a.nombre_producto||'').localeCompare(String(b.nombre_producto||''), 'es'));
    } else if (modo === 'priceAsc' || modo === 'priceDesc') {
        const getMinPrice = (p) => {
            const vars = Array.isArray(p.variantes) ? p.variantes : [];
            if (!vars.length) return Number.MAX_SAFE_INTEGER;
            return Math.min(...vars.map(v => Number(v.precio)||0));
        };
        clone.sort((a, b) => {
            const da = getMinPrice(a);
            const db = getMinPrice(b);
            return modo === 'priceAsc' ? (da - db) : (db - da);
        });
    } else if (modo === 'popularity') {
        // Popularidad: usar sales_count descendente (inyectado por API)
        clone.sort((a, b) => (Number(b.sales_count||0) - Number(a.sales_count||0)) || String(a.nombre_producto||'').localeCompare(String(b.nombre_producto||''), 'es'));
    } else {
        // Categoría: usar orden lógico primero, luego nombre
        const orderMap = new Map([
            ['Frappes Gourmet', 1],
            ['Elotes', 2],
            ['Tostitos con Elote', 3],
            ['Vasolokos', 4],
            ['Adiciones', 5]
        ]);
        const rank = (p) => orderMap.get(p.nombre_categoria) || 99;
        clone.sort((a, b) => {
            const r = rank(a) - rank(b);
            if (r !== 0) return r;
            return String(a.nombre_producto||'').localeCompare(String(b.nombre_producto||''), 'es');
        });
    }

    return clone;
}

async function cargarMetodosPago() {
    try {
        console.log('[DEBUG] Cargando métodos de pago...');
        const respuesta = await fetch('/api/metodos-pago', { cache: 'no-store' });

        if (respuesta.status === 401) {
            console.warn('[AUTH] Sesión expirada, redirigiendo a login');
            window.location.href = '/login';
            return;
        }

        if (!respuesta.ok) {
            throw new Error(`HTTP ${respuesta.status}`);
        }

        const metodos = await respuesta.json();
        console.log('[DEBUG] Métodos de pago recibidos:', metodos.length);
        
        if (!Array.isArray(metodos)) {
            throw new Error('Formato de respuesta inválido');
        }

        renderMetodosPago(metodos);
    } catch (error) {
        console.error('[ERROR] cargarMetodosPago:', error);
        paymentOptionsEl.innerHTML = '';
        const aviso = document.createElement('button');
        aviso.type = 'button';
        aviso.className = 'payment-btn';
        aviso.disabled = true;
        aviso.textContent = 'Error al cargar métodos de pago';
        paymentOptionsEl.appendChild(aviso);
        metodoSeleccionado = null;
        processBtn.disabled = true;
    }
}

function renderMetodosPago(metodos) {
    paymentOptionsEl.innerHTML = '';

    if (!metodos.length) {
        const aviso = document.createElement('button');
        aviso.type = 'button';
        aviso.className = 'payment-btn';
        aviso.disabled = true;
        aviso.textContent = 'Sin métodos disponibles';
        paymentOptionsEl.appendChild(aviso);
        metodoSeleccionado = null;
        processBtn.disabled = true;
        return;
    }

    metodos.forEach((metodo, indice) => {
        // Validación null safety
        const metodoId = Number(metodo.metodo_id) || 0;
        const nombreMetodo = metodo.nombre_metodo || 'Método de pago';

        if (metodoId <= 0) {
            console.warn('[WARN] Método de pago con ID inválido:', metodo);
            return;
        }

        const boton = document.createElement('button');
        boton.type = 'button';
        boton.className = 'payment-btn';
        boton.dataset.metodoId = metodoId;
        
        // Agregar data-method para los estilos CSS
        const nombreLower = nombreMetodo.toLowerCase();
        if (nombreLower.includes('efectivo') || nombreLower.includes('cash')) {
            boton.dataset.method = 'cash';
        } else if (nombreLower.includes('tarjeta') || nombreLower.includes('card') || nombreLower.includes('crédito') || nombreLower.includes('débito')) {
            boton.dataset.method = 'card';
        } else {
            boton.dataset.method = 'other';
        }

        const icono = document.createElement('i');
        icono.className = obtenerIconoMetodo(nombreMetodo);
        boton.appendChild(icono);
        boton.appendChild(document.createTextNode(` ${nombreMetodo}`));

        if (indice === 0) {
            boton.classList.add('active');
            metodoSeleccionado = metodoId;
        }

        boton.addEventListener('click', () => seleccionarMetodoPago(metodoId, boton));
        paymentOptionsEl.appendChild(boton);
    });

    processBtn.disabled = !cart.length || !metodoSeleccionado;
}

function obtenerIconoMetodo(nombre) {
    const texto = nombre.toLowerCase();
    if (texto.includes('efect')) {
        return 'fas fa-money-bill-wave';
    }
    if (texto.includes('tarj') || texto.includes('card')) {
        return 'far fa-credit-card';
    }
    if (texto.includes('transfer')) {
        return 'fas fa-university';
    }
    return 'fas fa-money-check-alt';
}

function seleccionarMetodoPago(metodoId, boton) {
    metodoSeleccionado = metodoId;

    paymentOptionsEl.querySelectorAll('.payment-btn').forEach((btn) => {
        btn.classList.toggle('active', btn === boton);
    });

    processBtn.disabled = !cart.length || !metodoSeleccionado;
}

async function procesarVenta() {
    if (!cart.length) { showToast('Agrega productos al carrito antes de procesar la venta.', 'error'); return; }

    if (!metodoSeleccionado) { showToast('Selecciona un método de pago.', 'error'); return; }

    // Loading state
    const textoOriginal = processBtn.textContent;
    processBtn.disabled = true;
    processBtn.textContent = 'Procesando...';
    console.log('[DEBUG] Iniciando procesamiento de venta');

    const payload = {
        metodo_id: metodoSeleccionado,
        items: cart.map((item) => ({
            variante_id: item.variante_id,
            cantidad: item.cantidad
        }))
    };

    console.log('[DEBUG] Payload:', payload);

    try {
        const respuesta = await fetch('/api/ventas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify(payload)
        });

        console.log('[DEBUG] Response status:', respuesta.status);

        if (respuesta.status === 401) {
            console.warn('[AUTH] Sesión expirada, redirigiendo a login');
            window.location.href = '/login';
            return;
        }

        const data = await respuesta.json();
        console.log('[DEBUG] Response data:', data);

        if (!respuesta.ok) {
            const errorMsg = data && data.error ? data.error : 'Error al procesar la venta';
            throw new Error(errorMsg);
        }

        // Validar estructura de respuesta
        if (!data || typeof data.ok === 'undefined') {
            throw new Error('Respuesta inválida del servidor');
        }

        if (!data.ok) {
            throw new Error(data.error || 'Error desconocido al procesar la venta');
        }

        // Validar datos de venta
        const ventaId = data.venta_id || data.id;
        const codigo = data.codigo || 'SIN-CODIGO';
        const total = typeof data.total === 'number' ? data.total : 0;

        console.log('[SUCCESS] Venta registrada:', { ventaId, codigo, total });
        showToast(`Venta registrada. Folio: ${codigo} · Total: ${formatoMoneda.format(total)}`, 'success');
        
        limpiarCarritoSinConfirmar();
        
    } catch (error) {
        console.error('[ERROR] procesarVenta:', error);
        showToast(error.message || 'No se pudo procesar la venta. Intenta de nuevo.', 'error');
    } finally {
        processBtn.disabled = !cart.length || !metodoSeleccionado;
        processBtn.textContent = textoOriginal;
    }
}

// Modal para limpiar carrito
let limpiarForm = null;
window.addEventListener('DOMContentLoaded', function() {
    const limpiarBtn = document.getElementById('limpiarCarritoBtn');
    if (limpiarBtn) {
        limpiarBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('clearCartModal').style.display = 'block';
        });
    }
    document.getElementById('cancelClearCartBtn').onclick = function() {
        document.getElementById('clearCartModal').classList.remove('show');
    };
    document.getElementById('confirmClearCartBtn').onclick = function() {
        limpiarCarritoSinConfirmar();
        document.getElementById('clearCartModal').classList.remove('show');
    };
    // Cerrar al hacer clic fuera del contenido
    document.getElementById('clearCartModal').addEventListener('click', function(e){
        if(e.target === this){ this.classList.remove('show'); }
    });
});
// Toast visual para errores
function showToast(msg, type = 'error') {
    let el = document.getElementById('toast');
    if (!el) {
        el = document.createElement('div');
        el.id = 'toast';
        el.className = 'toast';
        document.body.appendChild(el);
    }
    el.textContent = msg;
    el.className = `toast show ${type}`;
    setTimeout(() => { el.className = 'toast'; }, 3000);
}
// Reemplaza console.error por showToast
window.onerror = function(msg) {
    showToast(msg, 'error');
    return true;
};
    </script>
</body>
</html>