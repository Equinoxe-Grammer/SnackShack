
# public/

Carpeta pública (DocumentRoot). Contiene el punto de entrada de la aplicación y todos los activos estáticos servidos al navegador.

## Contenido principal

- `index.php`: punto de entrada principal para solicitudes HTTP
- `pretty-urls.php`: soporte para URLs amigables
- `assets/`, `css/`, `js/`: archivos estáticos (CSS, JS, imágenes, fuentes)

## Recomendaciones

- En producción, configura el servidor (Nginx/Apache) para que el DocumentRoot apunte aquí
- No guardes archivos sensibles o credenciales en `public/`
- Habilita compresión y caching estático (gzip/brotli, headers de cache)

## Desarrollo rápido

Para instrucciones de arranque local y comandos de desarrollo, consulte el `README.md` en la raíz del proyecto: [../README.md](../README.md).

Si usas un pipeline de assets (build de CSS/JS), documenta aquí el paso para compilar/actualizar los assets.

## Referencias

- [Manual técnico modular](../docs/INDEX.md)
