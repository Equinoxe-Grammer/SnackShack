/**
 * Sistema de Toggle de Tema Claro/Oscuro
 * Persiste la preferencia del usuario en localStorage
 */

(function() {
    'use strict';

    // Obtener preferencia guardada o usar preferencia del sistema
    function getThemePreference() {
        const saved = localStorage.getItem('theme');
        if (saved) {
            return saved;
        }
        // Detectar preferencia del sistema
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return 'dark';
        }
        return 'light';
    }

    // Aplicar tema
    function applyTheme(theme) {
        const root = document.documentElement;
        if (theme === 'dark') {
            root.setAttribute('data-theme', 'dark');
        } else {
            root.removeAttribute('data-theme');
        }
        localStorage.setItem('theme', theme);
        
        // Actualizar icono del botón si existe
        updateToggleButton(theme);
    }

    // Actualizar icono del botón
    function updateToggleButton(theme) {
        const toggleBtn = document.getElementById('themeToggle');
        if (toggleBtn) {
            const icon = toggleBtn.querySelector('i');
            const span = toggleBtn.querySelector('span');
            if (icon) {
                if (theme === 'dark') {
                    icon.className = 'fas fa-sun';
                    toggleBtn.setAttribute('title', 'Modo claro');
                    if (span) span.textContent = 'Modo claro';
                } else {
                    icon.className = 'fas fa-moon';
                    toggleBtn.setAttribute('title', 'Modo oscuro');
                    if (span) span.textContent = 'Modo oscuro';
                }
            }
        }
    }

    // Toggle entre temas
    function toggleTheme() {
        const currentTheme = getThemePreference();
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        applyTheme(newTheme);
    }

    // Aplicar tema inmediatamente al cargar
    const initialTheme = getThemePreference();
    applyTheme(initialTheme);

    // Función para inicializar el botón
    function initializeButton() {
        const toggleBtn = document.getElementById('themeToggle');
        if (toggleBtn) {
            // Remover listeners anteriores si existen
            const newBtn = toggleBtn.cloneNode(true);
            toggleBtn.parentNode.replaceChild(newBtn, toggleBtn);
            
            // Agregar nuevo listener
            newBtn.addEventListener('click', function(e) {
                e.preventDefault();
                toggleTheme();
            });
            
            updateToggleButton(getThemePreference());
            return true;
        }
        return false;
    }

    // Intentar inicializar inmediatamente
    if (document.readyState === 'loading') {
        // DOM aún cargando, esperar DOMContentLoaded
        document.addEventListener('DOMContentLoaded', initializeButton);
    } else {
        // DOM ya listo, inicializar inmediatamente
        if (!initializeButton()) {
            // Si no existe el botón, esperar un poco más
            setTimeout(initializeButton, 100);
        }
    }

    // Exportar función global para uso manual si es necesario
    window.toggleTheme = toggleTheme;
})();
