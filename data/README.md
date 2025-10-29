
# data/

Carpeta para almacenar datos generados localmente: archivos subidos, cachés simples, exportaciones, etc. Puede ser persistente entre despliegues.

## Recomendaciones

- No versionar archivos binarios o subidos en el repositorio
- Asegura permisos adecuados (lectura/escritura) para el proceso PHP
- Si usas contenedores, mapea un volumen para persistir `data/`

## Seguridad

- No sirvas archivos de `data/` desde `public/` sin control de acceso

## Referencias

- [Manual técnico modular](../docs/INDEX.md)
