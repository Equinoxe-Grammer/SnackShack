<?php
/**
 * Sidebar compartida para vistas del panel
 * Variables esperadas:
 * - $active (string): 'dashboard' | 'ventas' | 'historial' | 'productos'
 * - $user (array): ['nombre' => string, 'rol' => string]
 */
?>
<aside class="sidebar">
    <div class="sidebar-top">
        <div class="logo">
            <img src="/assets/logo_snack.jpg" alt="Logo">
        </div>
        <nav class="navigation menu">
            <ul>
                <?php if (($user['rol'] ?? '') === 'admin'): ?>
                    <li class="<?php echo ($active === 'dashboard') ? 'active' : ''; ?>"><a href="/menu"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <?php endif; ?>
                <li class="<?php echo ($active === 'ventas') ? 'active' : ''; ?>"><a href="/ventas"><i class="fas fa-cash-register"></i> Ventas</a></li>
                <?php if (($user['rol'] ?? '') === 'admin'): ?>
                    <li class="<?php echo ($active === 'historial') ? 'active' : ''; ?>"><a href="/historial"><i class="fas fa-history"></i> Historial</a></li>
                    <li class="<?php echo ($active === 'productos') ? 'active' : ''; ?>"><a href="/productos"><i class="fas fa-box"></i> Catálogo</a></li>
                    <li class="<?php echo ($active === 'cajeros') ? 'active' : ''; ?>"><a href="/agregarCajero"><i class="fas fa-user-plus"></i> Cajeros</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <div class="sidebar-bottom">
        <div class="user-status">
            <span class="status-dot"></span>
            <div class="user-info">
                <strong><?php echo htmlspecialchars($user['nombre'] ?? ''); ?></strong>
                <small><?php echo htmlspecialchars(ucfirst($user['rol'] ?? '')); ?> • En línea</small>
            </div>
        </div>
        <button class="logout logout-btn" onclick="window.location.href='/logout'">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </button>
    </div>
</aside>
