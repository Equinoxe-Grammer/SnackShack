/**
 * Monitor de Sesión - SnackShop POS
 * Verifica el estado de la sesión y alerta al usuario antes de que expire
 */

class SessionMonitor {
    constructor(options = {}) {
        this.checkInterval = options.checkInterval || 60000; // Verificar cada 1 minuto por defecto
        this.warningTime = options.warningTime || 300; // Alertar cuando queden 5 minutos (300 segundos)
        this.extendOnActivity = options.extendOnActivity !== false; // true por defecto
        this.onExpired = options.onExpired || this.defaultOnExpired;
        this.onWarning = options.onWarning || this.defaultOnWarning;
        
        this.intervalId = null;
        this.hasWarned = false;
        this.lastActivity = Date.now();
        
        // Bind methods
        this.check = this.check.bind(this);
        this.handleActivity = this.handleActivity.bind(this);
        this.extend = this.extend.bind(this);
    }
    
    /**
     * Inicia el monitoreo de sesión
     */
    start() {
        console.log('[SessionMonitor] Iniciando monitoreo de sesión');
        
        // Verificar inmediatamente
        this.check();
        
        // Configurar verificación periódica
        this.intervalId = setInterval(this.check, this.checkInterval);
        
        // Monitorear actividad del usuario
        if (this.extendOnActivity) {
            this.setupActivityTracking();
        }
    }
    
    /**
     * Detiene el monitoreo
     */
    stop() {
        console.log('[SessionMonitor] Deteniendo monitoreo de sesión');
        
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
        
        this.removeActivityTracking();
    }
    
    /**
     * Verifica el estado de la sesión
     */
    async check() {
        try {
            const response = await fetch('/api/session/check');
            const data = await response.json();
            
            if (!data.ok) {
                console.error('[SessionMonitor] Error al verificar sesión:', data);
                return;
            }
            
            if (!data.active) {
                console.warn('[SessionMonitor] Sesión expirada');
                this.onExpired();
                this.stop();
                return;
            }
            
            const remaining = data.remaining_seconds;
            console.log(`[SessionMonitor] Sesión activa. Tiempo restante: ${this.formatTime(remaining)}`);
            
            // Alertar si queda poco tiempo
            if (remaining <= this.warningTime && !this.hasWarned) {
                this.hasWarned = true;
                this.onWarning(remaining);
            }
            
            // Resetear warning si la sesión fue extendida
            if (remaining > this.warningTime && this.hasWarned) {
                this.hasWarned = false;
            }
            
        } catch (error) {
            console.error('[SessionMonitor] Error al verificar sesión:', error);
        }
    }
    
    /**
     * Extiende la sesión por 24 horas más
     */
    async extend() {
        try {
            const response = await fetch('/api/session/extend', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.ok) {
                console.log('[SessionMonitor] Sesión extendida:', data.message);
                this.hasWarned = false;
                return true;
            } else {
                console.error('[SessionMonitor] Error al extender sesión:', data.error);
                return false;
            }
            
        } catch (error) {
            console.error('[SessionMonitor] Error al extender sesión:', error);
            return false;
        }
    }
    
    /**
     * Configura el tracking de actividad del usuario
     */
    setupActivityTracking() {
        const events = ['mousedown', 'keydown', 'scroll', 'touchstart'];
        
        events.forEach(event => {
            document.addEventListener(event, this.handleActivity, { passive: true });
        });
    }
    
    /**
     * Remueve el tracking de actividad
     */
    removeActivityTracking() {
        const events = ['mousedown', 'keydown', 'scroll', 'touchstart'];
        
        events.forEach(event => {
            document.removeEventListener(event, this.handleActivity);
        });
    }
    
    /**
     * Maneja la actividad del usuario
     */
    handleActivity() {
        const now = Date.now();
        const timeSinceLastActivity = now - this.lastActivity;
        
        // Solo extender si han pasado al menos 5 minutos desde la última extensión
        if (timeSinceLastActivity > 300000) { // 5 minutos
            this.lastActivity = now;
            console.log('[SessionMonitor] Actividad detectada, extendiendo sesión...');
            this.extend();
        }
    }
    
    /**
     * Callback por defecto cuando la sesión expira
     */
    defaultOnExpired() {
        alert('Tu sesión ha expirado. Serás redirigido al inicio de sesión.');
        window.location.href = '/login';
    }
    
    /**
     * Callback por defecto cuando la sesión está por expirar
     */
    defaultOnWarning(remainingSeconds) {
        const minutes = Math.floor(remainingSeconds / 60);
        const message = `Tu sesión expirará en ${minutes} minutos. ¿Deseas extenderla?`;
        
        if (confirm(message)) {
            this.extend();
        }
    }
    
    /**
     * Formatea segundos a tiempo legible
     */
    formatTime(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        
        if (hours > 0) {
            return `${hours}h ${minutes}m`;
        } else if (minutes > 0) {
            return `${minutes}m ${secs}s`;
        } else {
            return `${secs}s`;
        }
    }
    
    /**
     * Obtiene información de la sesión
     */
    async getInfo() {
        try {
            const response = await fetch('/api/session/info');
            const data = await response.json();
            
            if (data.ok) {
                return data.session;
            } else {
                console.error('[SessionMonitor] Error al obtener info de sesión:', data.error);
                return null;
            }
            
        } catch (error) {
            console.error('[SessionMonitor] Error al obtener info de sesión:', error);
            return null;
        }
    }
}

// Exportar para uso global
if (typeof window !== 'undefined') {
    window.SessionMonitor = SessionMonitor;
}

// Auto-iniciar si estamos en una página autenticada
document.addEventListener('DOMContentLoaded', () => {
    // Solo iniciar en páginas que no sean login
    if (!window.location.pathname.includes('/login')) {
        const monitor = new SessionMonitor({
            checkInterval: 60000,        // Verificar cada 1 minuto
            warningTime: 300,            // Alertar con 5 minutos restantes
            extendOnActivity: true,      // Extender automáticamente con actividad
            onExpired: () => {
                alert('Tu sesión ha expirado por inactividad. Serás redirigido al inicio de sesión.');
                window.location.href = '/login';
            },
            onWarning: (remainingSeconds) => {
                const minutes = Math.floor(remainingSeconds / 60);
                const message = `⏰ Tu sesión expirará en ${minutes} minutos.\n\n¿Deseas continuar trabajando?`;
                
                if (confirm(message)) {
                    monitor.extend().then(success => {
                        if (success) {
                            alert('✓ Sesión extendida por 24 horas más');
                        }
                    });
                }
            }
        });
        
        monitor.start();
        
        // Hacer disponible globalmente
        window.sessionMonitor = monitor;
        
        console.log('[SessionMonitor] Monitor de sesión activado');
    }
});
