# public/

Propósito

Directorio público (DocumentRoot). Contiene el punto de entrada de la aplicación y activos estáticos que sirven al navegador.

Contenido importante

- `index.php` — Punto de entrada principal para solicitudes HTTP.
- `pretty-urls.php` — Manejador/compatibilidad para URLs amigables (si se usa).
- `assets/`, `css/`, `js/` — Archivos estáticos (CSS, JS, imágenes).

Convenciones y recomendaciones

- El servidor web (Apache/Nginx) debe apuntar el DocumentRoot a `public/`.
- No colocar archivos con credenciales en este directorio.
- Versiona sólo los orígenes; los activos compilados pueden incluirse si son producidos por el proyecto.

Despliegue

- En producción: copiar el contenido de `public/` como DocumentRoot.
- Asegurar mecanismos de caché y compresión en el servidor (gzip, headers de cache).

Notas para desarrolladores

- Para desarrollo rápido con el servidor embebido de PHP: `php -S localhost:8000 -t public`.
