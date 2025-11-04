<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Ventas - Snack's</title>
    <link rel="icon" type="image/svg+xml" href="/assets/favicon.svg">
        <link rel="stylesheet" href="/css/theme.css">
    <link rel="stylesheet" href="/css/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" crossorigin="anonymous" defer></script>
</head>
<body>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $user = [
        'nombre' => $_SESSION['usuario'] ?? 'Usuario',
        'rol' => $_SESSION['rol'] ?? 'admin',
    ];

    $active = 'dashboard';
    include __DIR__ . '/../partials/sidebar.php';
    ?>

    <main class="main-content">
        <header class="dashboard-header">
            <div>
                <h1><i class="fas fa-chart-pie"></i> Dashboard de Ventas</h1>
                <p class="subtitle">Centro de control del negocio</p>
            </div>
            <div class="action-buttons-header">
                <a href="/metrics.html" target="_blank" rel="noopener" title="Métricas"
                   style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--border-color,#ddd);background:var(--surface-2,#f7f7f7);color:inherit;margin-right:8px;">
                    <i class="fas fa-square" aria-hidden="true"></i>
                </a>
                <button class="btn btn-primary" onclick="window.location.href='/agregarCajero'">
                    <i class="fas fa-plus"></i> Agregar Cajero
                </button>
                <button class="btn btn-primary" onclick="window.location.href='/venta'">
                    <i class="fas fa-plus"></i> Nueva Venta
                </button>
                <button class="btn btn-secondary" onclick="window.location.href='/historial'">
                    <i class="fas fa-receipt"></i> Ver Historial
                </button>
                <button id="themeToggle" class="btn btn-secondary" title="Cambiar tema">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </header>

        <!-- KPI Cards -->
        <section class="summary-cards">
            <div class="card kpi-card">
                <div class="card-icon-wrapper green">
                    <i class="fas fa-dollar-sign card-icon"></i>
                </div>
                <div class="card-info">
                    <h2 id="ventas-dia">$0.00</h2>
                    <p>Ventas del Día</p>
                </div>
            </div>
            <div class="card kpi-card">
                <div class="card-icon-wrapper blue">
                    <i class="fas fa-shopping-cart card-icon"></i>
                </div>
                <div class="card-info">
                    <h2 id="total-transacciones">0</h2>
                    <p>Transacciones Hoy</p>
                </div>
            </div>
            <div class="card kpi-card">
                <div class="card-icon-wrapper orange">
                    <i class="fas fa-chart-line card-icon"></i>
                </div>
                <div class="card-info">
                    <h2 id="promedio-venta">$0.00</h2>
                    <p>Promedio por Venta</p>
                </div>
            </div>
        </section>

        <!-- Weekly Progress Bar -->
        <section class="weekly-progress-section">
            <div class="progress-card">
                <div class="progress-header">
                    <div>
                        <h3><i class="fas fa-calendar-week"></i> Progreso Semanal</h3>
                        <p class="progress-subtitle">Meta de la semana: <strong>$2,000.00</strong></p>
                    </div>
                    <div class="progress-stats">
                        <span class="progress-amount" id="ventas-semana">$0.00</span>
                        <span class="progress-percentage" id="progreso-porcentaje">0%</span>
                    </div>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar" id="progress-bar-fill"></div>
                </div>
            </div>
        </section>

        <!-- Charts Section -->
        <section class="charts-section">
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-line"></i> Ventas Últimos 7 Días</h3>
                </div>
                <div class="chart-container">
                    <canvas id="salesLineChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-pie"></i> Métodos de Pago</h3>
                </div>
                <div class="chart-container">
                    <canvas id="paymentDoughnutChart"></canvas>
                </div>
            </div>
        </section>

        <!-- Recent Sales Table -->
        <section class="recent-sales">
            <div class="section-header">
                <h2><i class="fas fa-clock"></i> Últimas Ventas</h2>
                <span class="view-all-link" onclick="window.location.href='/historial'">
                    Ver todas <i class="fas fa-arrow-right"></i>
                </span>
            </div>
            <div class="sales-table-wrapper">
                <ul id="lista-ventas" class="sales-list">
                    <li class="sale-item empty">
                        <i class="fas fa-spinner fa-spin"></i> Cargando información...
                    </li>
                </ul>
            </div>
        </section>
    </main>

    <!-- Toast visual universal -->
    <div id="toast" class="toast"></div>

    <script>
    const formatoMoneda = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });
    let salesLineChart = null;
    let paymentDoughnutChart = null;

    // Configurar Chart.js cuando esté disponible
    function configureChartDefaults() {
        if (typeof Chart !== 'undefined') {
            Chart.defaults.font.family = 'Poppins, sans-serif';
            Chart.defaults.devicePixelRatio = window.devicePixelRatio || 2;
        }
    }

    function renderUltimasVentas(ventas, contenedor) {
        contenedor.innerHTML = '';

        if (!Array.isArray(ventas) || ventas.length === 0) {
            contenedor.innerHTML = '<li class="sale-item empty"><i class="fas fa-inbox"></i> Sin ventas registradas hoy</li>';
            return;
        }

        ventas.forEach((venta) => {
            const item = document.createElement('li');
            item.className = 'sale-item';
            
            // Determinar clase de método de pago
            const metodoLower = (venta.metodo_pago || '').toLowerCase();
            let metodoClass = 'method-otros';
            if (metodoLower.includes('efectivo') || metodoLower.includes('cash')) {
                metodoClass = 'method-efectivo';
            } else if (metodoLower.includes('tarjeta') || metodoLower.includes('card')) {
                metodoClass = 'method-tarjeta';
            }
            
            const usuario = venta.usuario ? ` · ${venta.usuario}` : '';
            item.innerHTML = `
                <div class="sale-details">
                    <div class="sale-info-left">
                        <strong class="sale-id">#${venta.codigo}</strong>
                        <span class="sale-user">${venta.usuario || 'Usuario'}</span>
                    </div>
                    <span class="sale-time"><i class="far fa-clock"></i> ${venta.hora}</span>
                </div>
                <div class="sale-payment">
                    <span class="method ${metodoClass}">
                        <i class="fas ${metodoClass === 'method-efectivo' ? 'fa-money-bill-wave' : metodoClass === 'method-tarjeta' ? 'fa-credit-card' : 'fa-wallet'}"></i>
                        ${venta.metodo_pago}
                    </span>
                    <strong class="sale-total">${formatoMoneda.format(venta.total || 0)}</strong>
                </div>
            `;
            contenedor.appendChild(item);
        });
    }

    function renderWeeklyProgress(ventasSemana) {
        const META_SEMANAL = 2000;
        const ventasSemanaEl = document.getElementById('ventas-semana');
        const progresoPorcentajeEl = document.getElementById('progreso-porcentaje');
        const progressBarFill = document.getElementById('progress-bar-fill');
        
        // Validar que los elementos existan
        if (!ventasSemanaEl || !progresoPorcentajeEl || !progressBarFill) {
            console.warn('[WARN] Elementos de progreso semanal no encontrados');
            return;
        }
        
        // Calcular porcentaje (asegurar que sea un número válido)
        const ventas = parseFloat(ventasSemana) || 0;
        const porcentaje = Math.min((ventas / META_SEMANAL) * 100, 100);
        
        console.log(`[DEBUG] Progreso semanal: $${ventas} de $${META_SEMANAL} = ${porcentaje.toFixed(1)}%`);
        
        // Actualizar textos
        ventasSemanaEl.textContent = formatoMoneda.format(ventas);
        progresoPorcentajeEl.textContent = `${Math.round(porcentaje)}%`;
        
        // Actualizar barra con animación
        setTimeout(() => {
            progressBarFill.style.width = `${porcentaje}%`;
        }, 100);
        
        // Cambiar color según progreso
        if (porcentaje >= 100) {
            progressBarFill.className = 'progress-bar complete';
        } else if (porcentaje >= 75) {
            progressBarFill.className = 'progress-bar high';
        } else if (porcentaje >= 50) {
            progressBarFill.className = 'progress-bar medium';
        } else {
            progressBarFill.className = 'progress-bar low';
        }
    }

    function renderSalesLineChart(labels, data) {
        const ctx = document.getElementById('salesLineChart').getContext('2d');
        
        if (salesLineChart) {
            salesLineChart.destroy();
        }
        
        // Crear gradiente para el área bajo la línea
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(242, 158, 56, 0.25)');
        gradient.addColorStop(0.5, 'rgba(242, 158, 56, 0.15)');
        gradient.addColorStop(1, 'rgba(242, 158, 56, 0.02)');
        
        salesLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ventas del Día',
                    data: data,
                    borderColor: '#F29E38',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#F29E38',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 9,
                    pointHoverBackgroundColor: '#fd7e14',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2.2,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(139, 76, 36, 0.95)',
                        titleColor: '#fff',
                        titleFont: {
                            family: 'Poppins',
                            size: 14,
                            weight: '600'
                        },
                        bodyColor: '#fff',
                        bodyFont: {
                            family: 'Poppins',
                            size: 13,
                            weight: '500'
                        },
                        padding: 14,
                        cornerRadius: 10,
                        displayColors: false,
                        borderColor: '#F29E38',
                        borderWidth: 2,
                        callbacks: {
                            label: function(context) {
                                return formatoMoneda.format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(139, 76, 36, 0.08)',
                            drawBorder: false,
                            lineWidth: 1
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#6c757d',
                            font: {
                                family: 'Poppins',
                                size: 11,
                                weight: '500'
                            },
                            padding: 8,
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#6c757d',
                            font: {
                                family: 'Poppins',
                                size: 11,
                                weight: '500'
                            },
                            padding: 8
                        }
                    }
                }
            }
        });
    }

    function renderPaymentDoughnutChart(labels, data) {
        const ctx = document.getElementById('paymentDoughnutChart').getContext('2d');
        
        if (paymentDoughnutChart) {
            paymentDoughnutChart.destroy();
        }
        
        paymentDoughnutChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        '#8ED1B2', // Verde pistacho (Efectivo)
                        '#5B9BD5', // Azul profesional (Tarjeta)
                        '#F29E38'  // Naranja (Otros)
                    ],
                    borderColor: '#fff',
                    borderWidth: 4,
                    hoverOffset: 15,
                    hoverBorderWidth: 4,
                    hoverBorderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.4,
                cutout: '65%', // Hacer el agujero más grande para estilo moderno
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#4a4a68',
                            font: {
                                family: 'Poppins',
                                size: 13,
                                weight: '600'
                            },
                            padding: 18,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 12,
                            boxHeight: 12,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return {
                                            text: `${label} (${percentage}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(74, 74, 104, 0.95)',
                        titleColor: '#fff',
                        titleFont: {
                            family: 'Poppins',
                            size: 14,
                            weight: '600'
                        },
                        bodyColor: '#fff',
                        bodyFont: {
                            family: 'Poppins',
                            size: 13,
                            weight: '500'
                        },
                        padding: 14,
                        cornerRadius: 10,
                        displayColors: true,
                        borderColor: '#F29E38',
                        borderWidth: 2,
                        boxWidth: 15,
                        boxHeight: 15,
                        boxPadding: 6,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${formatoMoneda.format(value)} (${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }

    function showToast(msg, type = 'info') {
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

    async function cargarDashboard() {
        const ventasDiaEl = document.getElementById('ventas-dia');
        const transaccionesEl = document.getElementById('total-transacciones');
        const promedioEl = document.getElementById('promedio-venta');
        const listaVentasEl = document.getElementById('lista-ventas');

        // Configurar Chart.js antes de usarlo
        configureChartDefaults();

        try {
            console.log('[DEBUG] Cargando dashboard...');
            const respuesta = await fetch('/api/dashboard', { cache: 'no-store' });
            
            console.log('[DEBUG] Dashboard response status:', respuesta.status);

            if (respuesta.status === 401) {
                console.warn('[AUTH] Sesión expirada, redirigiendo a login');
                window.location.href = '/login';
                return;
            }

            if (!respuesta.ok) {
                throw new Error(`HTTP ${respuesta.status}`);
            }

            const data = await respuesta.json();
            console.log('[DEBUG] Dashboard data:', data);

            if (!data || typeof data !== 'object') {
                throw new Error('Respuesta inválida del servidor');
            }

            // Renderizar KPIs
            const ventasDia = typeof data.ventas_dia === 'number' ? data.ventas_dia : 0;
            const transacciones = typeof data.transacciones === 'number' ? data.transacciones : 0;
            const promedio = typeof data.promedio_venta === 'number' ? data.promedio_venta : 0;
            const ventas = Array.isArray(data.ultimas_ventas) ? data.ultimas_ventas : [];

            ventasDiaEl.textContent = formatoMoneda.format(ventasDia);
            transaccionesEl.textContent = transacciones;
            promedioEl.textContent = formatoMoneda.format(promedio);
            renderUltimasVentas(ventas, listaVentasEl);

            // Renderizar progreso semanal
            const ventasSemana = typeof data.ventas_semana === 'number' ? data.ventas_semana : 0;
            renderWeeklyProgress(ventasSemana);

            // Renderizar gráfico de línea (últimos 7 días)
            if (data.ventas_ultimos_7_dias && Array.isArray(data.ventas_ultimos_7_dias)) {
                const labels = data.ventas_ultimos_7_dias.map(d => d.fecha);
                const valores = data.ventas_ultimos_7_dias.map(d => parseFloat(d.total) || 0);
                renderSalesLineChart(labels, valores);
            }

            // Renderizar gráfico de dona (métodos de pago)
            if (data.metodos_pago && Array.isArray(data.metodos_pago)) {
                const labels = data.metodos_pago.map(m => m.metodo);
                const valores = data.metodos_pago.map(m => parseFloat(m.total) || 0);
                renderPaymentDoughnutChart(labels, valores);
            }

            // No mostrar toast en éxito para evitar ruido visual
        } catch (error) {
            console.error('[ERROR] cargarDashboard:', error);
            ventasDiaEl.textContent = '--';
            transaccionesEl.textContent = '--';
            promedioEl.textContent = '--';
            listaVentasEl.innerHTML = '<li class="sale-item empty"><i class="fas fa-exclamation-triangle"></i> Error al cargar las ventas</li>';
            showToast('Error al cargar el dashboard', 'error');
        }
    }

    // Theme Switcher Functionality
    function initThemeSwitcher() {
        const themeToggle = document.getElementById('themeToggle');
        if (!themeToggle) return;
        
        const html = document.documentElement;
        const icon = themeToggle.querySelector('i');

        // Load saved theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-theme', savedTheme);
        updateThemeIcon(savedTheme);

        // Toggle theme on button click
        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';

            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);

            showToast(`Tema ${newTheme === 'dark' ? 'oscuro' : 'claro'} activado`, 'info');
        });

        function updateThemeIcon(theme) {
            icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
    }

    // Auto-actualización del dashboard cada 30 segundos
    let dashboardInterval = null;
    
    function startDashboardAutoRefresh() {
        // Limpiar intervalo anterior si existe
        if (dashboardInterval) {
            clearInterval(dashboardInterval);
        }
        
        // Actualizar cada 30 segundos (30000 ms)
        dashboardInterval = setInterval(() => {
            console.log('[AUTO-REFRESH] Actualizando dashboard...');
            cargarDashboard();
        }, 30000);
    }
    
    function stopDashboardAutoRefresh() {
        if (dashboardInterval) {
            clearInterval(dashboardInterval);
            dashboardInterval = null;
        }
    }

    // Detener auto-actualización cuando la ventana pierde el foco
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopDashboardAutoRefresh();
        } else {
            cargarDashboard();
            startDashboardAutoRefresh();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        cargarDashboard();
        initThemeSwitcher();
        startDashboardAutoRefresh();
    });
    </script>
</body>
</html>
