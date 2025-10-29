## src/Views/

Propósito

Carpeta para plantillas (views) y partials que renderizan la interfaz server-side. Aquí se mantiene la presentación (HTML/CSS snippets) y se separa de la lógica de negocio.

Convenciones

- Mantener vistas organizadas por área: `auth/`, `products/`, `sales/`, `users/`, `partials/`, etc.
- `partials/` deben contener componentes reutilizables: encabezado, sidebar, footer, alerts.
- Evitar lógica compleja en las vistas; solo presentarlas y escapar datos. La transformación de datos debe estar en Services/Controllers.

Buenas prácticas

- Usar helpers o funciones de escape para evitar XSS: `htmlspecialchars()` o un helper común.
- Incluir assets (CSS/JS) desde rutas relativas en `public/`.
- Para formularios, incluir tokens CSRF generados por `CsrfMiddleware`.

Testing y mantenimiento

- Mantener ejemplos de fixtures HTML si se hace testing visual/manual.
- Cuando se añadan nuevos partials, documentar su API (qué variables esperan).
