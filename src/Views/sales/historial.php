<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Ventas</title>
    <link rel="icon" type="image/svg+xml" href="/assets/favicon.svg">
    <link rel="stylesheet" href="/css/theme.css?v=20251013">
    <link rel="stylesheet" href="/css/historial.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="/js/theme-toggle.js"></script>
</head>
<body>
    <?php $active='historial'; include __DIR__ . '/../partials/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <header class="dashboard-header">
            <div>
                <h1><i class="fas fa-history"></i> Historial de Ventas</h1>
                <p class="subtitle">Consulta todas tus ventas anteriores</p>
            </div>
        </header>

        <!-- Filtros -->
        <section class="filters-section">
            <h2>Filtros de Búsqueda</h2>
            <div class="filters-container">
                <div class="filter-group">
                    <label for="filtro-busqueda">
                        <i class="fas fa-search"></i>
                        Buscar
                    </label>
                    <input 
                        type="text" 
                        id="filtro-busqueda" 
                        placeholder="Código de venta o usuario..."
                        autocomplete="off"
                    >
                </div>

                <div class="filter-group">
                    <label for="filtro-fecha">
                        <i class="fas fa-calendar"></i>
                        Fecha
                    </label>
                    <input 
                        type="date" 
                        id="filtro-fecha"
                    >
                </div>

                <div class="filter-group">
                    <label for="filtro-categoria">
                        <i class="fas fa-tags"></i>
                        Categoría
                    </label>
                    <select id="filtro-categoria">
                        <option value="">Todas las categorías</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button id="btn-limpiar-filtros" class="btn-secondary">
                        <i class="fas fa-eraser"></i>
                        Limpiar Filtros
                    </button>
                </div>
            </div>
        </section>

        <!-- Resumen -->
        <section class="summary-section">
            <div class="summary-card">
                <div class="card-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="card-content">
                    <h3 id="ventas-filtradas">0</h3>
                    <p>Ventas Filtradas</p>
                </div>
            </div>

            <div class="summary-card">
                <div class="card-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="card-content">
                    <h3 id="total-filtrado">$0.00</h3>
                    <p>Total Filtrado</p>
                </div>
            </div>

            <div class="summary-card">
                <div class="card-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="card-content">
                    <h3 id="promedio-filtrado">$0.00</h3>
                    <p>Promedio</p>
                </div>
            </div>
        </section>

        <!-- Tabla de historial -->
        <section class="history-section">
            <h2>Historial Completo</h2>
            <div class="table-container">
                <table id="tabla-historial">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Productos</th>
                            <th>Cantidad</th>
                            <th>Método</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="loading-row">
                            <td colspan="7">
                                <i class="fas fa-spinner fa-spin"></i>
                                Cargando historial...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Modal de detalle -->
    <div id="modal-detalle" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Detalle de Venta</h2>
                <button class="close-modal" onclick="cerrarModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="sale-info">
                    <div class="info-row">
                        <span class="label">
                            <i class="fas fa-barcode"></i>
                            Código:
                        </span>
                        <span id="detalle-codigo">-</span>
                    </div>
                    <div class="info-row">
                        <span class="label">
                            <i class="fas fa-calendar-alt"></i>
                            Fecha:
                        </span>
                        <span id="detalle-fecha">-</span>
                    </div>
                    <div class="info-row">
                        <span class="label">
                            <i class="fas fa-clock"></i>
                            Hora:
                        </span>
                        <span id="detalle-hora">-</span>
                    </div>
                    <div class="info-row">
                        <span class="label">
                            <i class="fas fa-user"></i>
                            Usuario:
                        </span>
                        <span id="detalle-usuario">-</span>
                    </div>
                    <div class="info-row">
                        <span class="label">
                            <i class="fas fa-credit-card"></i>
                            Método de Pago:
                        </span>
                        <span id="detalle-metodo">-</span>
                    </div>
                </div>

                <div class="items-table">
                    <h3>
                        <i class="fas fa-shopping-basket"></i>
                        Productos
                    </h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Variante</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detalle-items">
                            <!-- Items se cargan dinámicamente -->
                        </tbody>
                    </table>
                </div>

                <div class="sale-total">
                    <span class="label">
                        <i class="fas fa-receipt"></i>
                        Total:
                    </span>
                    <span id="detalle-total" class="total-amount">$0.00</span>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="cerrarModal()">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
        // Estado de filtros
        let filtros = {
            q: '',
            fecha: '',
            categoria_id: ''
        };

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            inicializar();
        });

        async function inicializar() {
            try {
                await cargarCategorias();
                await cargarHistorial();
                configurarEventos();
            } catch (error) {
                console.error('Error al inicializar:', error);
                mostrarError('Error al cargar la página');
            }
        }

        function configurarEventos() {
            // Filtro de búsqueda
            const inputBusqueda = document.getElementById('filtro-busqueda');
            let timeoutBusqueda;
            inputBusqueda.addEventListener('input', function(e) {
                clearTimeout(timeoutBusqueda);
                timeoutBusqueda = setTimeout(() => {
                    filtros.q = e.target.value.trim();
                    cargarHistorial();
                }, 500);
            });

            // Filtro de fecha
            document.getElementById('filtro-fecha').addEventListener('change', function(e) {
                filtros.fecha = e.target.value;
                cargarHistorial();
            });

            // Filtro de categoría
            document.getElementById('filtro-categoria').addEventListener('change', function(e) {
                filtros.categoria_id = e.target.value;
                cargarHistorial();
            });

            // Botón limpiar filtros
            document.getElementById('btn-limpiar-filtros').addEventListener('click', limpiarFiltros);

            // Cerrar modal al hacer clic fuera
            document.getElementById('modal-detalle').addEventListener('click', function(e) {
                if (e.target === this) {
                    cerrarModal();
                }
            });
        }

        async function cargarCategorias() {
            try {
                const response = await fetch('/api/categorias');
                if (!response.ok) throw new Error('Error al cargar categorías');

                const categorias = await response.json();
                const select = document.getElementById('filtro-categoria');

                categorias.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.categoria_id;
                    option.textContent = cat.nombre_categoria;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Error al cargar categorías:', error);
            }
        }

        async function cargarHistorial() {
            try {
                // Construir URL con filtros
                const params = new URLSearchParams();
                if (filtros.q) params.append('q', filtros.q);
                if (filtros.fecha) params.append('fecha', filtros.fecha);
                if (filtros.categoria_id) params.append('categoria_id', filtros.categoria_id);

                const url = `/api/ventas/historial${params.toString() ? '?' + params.toString() : ''}`;
                const response = await fetch(url);

                if (!response.ok) throw new Error('Error al cargar historial');

                const data = await response.json();
                
                // Actualizar resumen
                actualizarResumen(data.resumen);
                
                // Actualizar tabla
                renderizarTabla(data.ventas);

            } catch (error) {
                console.error('Error al cargar historial:', error);
                mostrarError('Error al cargar el historial de ventas');
            }
        }

        function actualizarResumen(resumen) {
            document.getElementById('ventas-filtradas').textContent = resumen.num_ventas || 0;
            document.getElementById('total-filtrado').textContent = formatearMoneda(resumen.total_ventas || 0);
            document.getElementById('promedio-filtrado').textContent = formatearMoneda(resumen.promedio || 0);
        }

        function renderizarTabla(ventas) {
            const tbody = document.querySelector('#tabla-historial tbody');
            
            if (!ventas || ventas.length === 0) {
                tbody.innerHTML = `
                    <tr class="empty-row">
                        <td colspan="7">
                            <i class="fas fa-inbox"></i>
                            No se encontraron ventas con los filtros aplicados
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = ventas.map(venta => {
                const numProductos = venta.items ? venta.items.length : 0;
                const productosNombres = venta.items 
                    ? venta.items.map(item => htmlEscape(item.producto)).join(', ')
                    : '-';
                
                const cantidadTotal = venta.items 
                    ? venta.items.reduce((sum, item) => sum + parseInt(item.cantidad || 0), 0)
                    : 0;

                // Determinar clase de método de pago
                const metodoLower = (venta.metodo_pago || '').toLowerCase();
                let metodoClass = 'method-efectivo';
                if (metodoLower.includes('tarjeta') || metodoLower.includes('card')) {
                    metodoClass = 'method-tarjeta';
                }

                return `
                    <tr onclick="verDetalle(${venta.venta_id})" style="cursor: pointer;">
                        <td><strong>${venta.codigo || 'V' + String(venta.venta_id).padStart(3, '0')}</strong></td>
                        <td>${formatearFecha(venta.fecha)}</td>
                        <td>${venta.hora}</td>
                        <td>${productosNombres}</td>
                        <td style="text-align: center;"><strong>${cantidadTotal}</strong></td>
                        <td><span class="${metodoClass}">${htmlEscape(venta.metodo_pago)}</span></td>
                        <td><strong>${formatearMoneda(venta.total)}</strong></td>
                    </tr>
                `;
            }).join('');
        }

        async function verDetalle(ventaId) {
            try {
                const response = await fetch(`/api/ventas/${ventaId}`);
                if (!response.ok) throw new Error('Error al cargar detalle');

                const venta = await response.json();
                mostrarModalDetalle(venta);

            } catch (error) {
                console.error('Error al cargar detalle:', error);
                mostrarError('Error al cargar el detalle de la venta');
            }
        }

        function mostrarModalDetalle(venta) {
            // Información general
            document.getElementById('detalle-codigo').textContent = venta.codigo;
            document.getElementById('detalle-fecha').textContent = formatearFecha(venta.fecha);
            document.getElementById('detalle-hora').textContent = venta.hora;
            document.getElementById('detalle-usuario').textContent = venta.usuario;
            document.getElementById('detalle-metodo').textContent = venta.metodo_pago;
            document.getElementById('detalle-total').textContent = formatearMoneda(venta.total);

            // Items
            const tbody = document.getElementById('detalle-items');
            tbody.innerHTML = venta.items.map(item => `
                <tr>
                    <td><strong>${htmlEscape(item.producto)}</strong></td>
                    <td>${htmlEscape(item.variante || '-')}</td>
                    <td style="text-align: center;"><strong>${item.cantidad}</strong></td>
                    <td style="text-align: right;">${formatearMoneda(item.precio_unitario)}</td>
                    <td style="text-align: right;"><strong>${formatearMoneda(item.subtotal)}</strong></td>
                </tr>
            `).join('');

            // Mostrar modal
            document.getElementById('modal-detalle').classList.add('active');
        }

        function cerrarModal() {
            document.getElementById('modal-detalle').classList.remove('active');
        }

        function limpiarFiltros() {
            filtros = {
                q: '',
                fecha: '',
                categoria_id: ''
            };

            document.getElementById('filtro-busqueda').value = '';
            document.getElementById('filtro-fecha').value = '';
            document.getElementById('filtro-categoria').value = '';

            cargarHistorial();
        }

        // Utilidades
        function formatearMoneda(valor) {
            return new Intl.NumberFormat('es-MX', {
                style: 'currency',
                currency: 'MXN'
            }).format(valor);
        }

        function formatearFecha(fecha) {
            const date = new Date(fecha + 'T00:00:00');
            const dia = String(date.getDate()).padStart(2, '0');
            const mes = String(date.getMonth() + 1).padStart(2, '0');
            const anio = date.getFullYear();
            return `${dia}/${mes}/${anio}`;
        }

        function htmlEscape(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function mostrarError(mensaje) {
            showToast(mensaje, 'error');
        }

        // Toast visual
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
    </script>
</body>
</html>
