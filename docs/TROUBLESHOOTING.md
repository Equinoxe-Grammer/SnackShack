<a id="troubleshooting-y-optimizacion"></a>
<a id="-troubleshooting-y-optimizacion"></a>
# Troubleshooting y Optimización
<!-- TOC -->
<a id="contenido"></a>
<a id="-contenido"></a>
## Contenido

- [Navegación](#navegacion)
- [1. Errores Comunes y Soluciones](#1-errores-comunes-y-soluciones)
  - [1.1. Error de Conexión a Base de Datos](#11-error-de-conexion-a-base-de-datos)
  - [1.2. Problemas con Composer](#12-problemas-con-composer)
  - [1.3. Errores de Permisos en Archivos/Imágenes](#13-errores-de-permisos-en-archivosimagenes)
  - [1.4. Problemas de CORS o API](#14-problemas-de-cors-o-api)
- [2. Debugging Avanzado](#2-debugging-avanzado)
- [3. Optimización de Desempeño](#3-optimizacion-de-desempeno)
  - [3.1. Backend](#31-backend)
  - [3.2. Frontend](#32-frontend)
  - [3.3. Infraestructura](#33-infraestructura)
- [4. Seguridad](#4-seguridad)
- [5. Recursos y Enlaces Útiles](#5-recursos-y-enlaces-utiles)
<!-- /TOC -->

Guía avanzada para la resolución de problemas, errores comunes, debugging y optimización de SnackShop. Incluye enlaces a documentación relevante y recomendaciones para ambientes de desarrollo y producción.

---

<a id="navegacion"></a>
<a id="-navegacion"></a>
## Navegación

- [Índice General](INDEX.md)
- [Arquitectura](ARCHITECTURE.md)
- [API](API.md)
- [Base de Datos](DATABASE.md)
- [Despliegue](DEPLOYMENT.md)
- [Configuración](CONFIGURATION.md)
- [Desarrollo](DEVELOPMENT.md)
- [Testing](TESTING.md)
- [Contribución](CONTRIBUTING.md)
- [Servicios](SERVICES.md)
- [Ejemplos y Tutoriales](EXAMPLES.md)

---

<a id="1-errores-comunes-y-soluciones"></a>
<a id="-1-errores-comunes-y-soluciones"></a>
## 1. Errores Comunes y Soluciones

<a id="11-error-de-conexion-a-base-de-datos"></a>
<a id="-11-error-de-conexion-a-base-de-datos"></a>
### 1.1. Error de Conexión a Base de Datos

- **Síntoma:** "Could not connect to database" o similar.
- **Solución:**
  - Verifica credenciales y host en `.env` o configuración.
  - Asegúrate de que el servicio de base de datos esté activo.
  - Revisa puertos y firewalls.

<a id="12-problemas-con-composer"></a>
<a id="-12-problemas-con-composer"></a>
### 1.2. Problemas con Composer

- **Síntoma:** Dependencias no resueltas, autoload fallando.
- **Solución:**
  - Ejecuta `composer install` o `composer update`.
  - Borra la carpeta `vendor/` y vuelve a instalar.
  - Verifica la versión de PHP y extensiones requeridas.

<a id="13-errores-de-permisos-en-archivosimagenes"></a>
<a id="-13-errores-de-permisos-en-archivosimagenes"></a>
### 1.3. Errores de Permisos en Archivos/Imágenes

- **Síntoma:** No se pueden subir o eliminar imágenes.
- **Solución:**
  - Ajusta permisos de carpetas (`public/assets/`, `storage/`).
  - Usa rutas relativas y seguras.

<a id="14-problemas-de-cors-o-api"></a>
<a id="-14-problemas-de-cors-o-api"></a>
### 1.4. Problemas de CORS o API

- **Síntoma:** Errores de acceso desde frontend.
- **Solución:**
  - Configura correctamente los headers CORS en el backend.
  - Verifica rutas y métodos permitidos.

---

<a id="2-debugging-avanzado"></a>
<a id="-2-debugging-avanzado"></a>
## 2. Debugging Avanzado

- Habilita el modo debug en `.env` (`APP_DEBUG=true`).
- Usa Xdebug para depuración paso a paso.
- Agrega logs detallados en puntos críticos (`src/Services/`, `src/Controllers/`).
- Utiliza PHPUnit y cobertura para detectar errores lógicos.
- Consulta los logs de servidor y PHP (`storage/logs/`, `php_error.log`).

---

<a id="3-optimizacion-de-desempeno"></a>
<a id="-3-optimizacion-de-desempeno"></a>
## 3. Optimización de Desempeño

<a id="31-backend"></a>
<a id="-31-backend"></a>
### 3.1. Backend

- Usa caché para consultas frecuentes (APCu, Redis).
- Optimiza queries SQL y usa índices en la base de datos.
- Minimiza el uso de recursos en controladores y servicios.
- Configura OPcache en PHP para producción.

<a id="32-frontend"></a>
<a id="-32-frontend"></a>
### 3.2. Frontend

- Minifica y agrupa archivos CSS/JS.
- Usa imágenes optimizadas y lazy loading.
- Habilita compresión GZIP en el servidor web.

<a id="33-infraestructura"></a>
<a id="-33-infraestructura"></a>
### 3.3. Infraestructura

- Usa Docker para ambientes reproducibles.
- Configura monitoreo y alertas (Grafana, Prometheus, Sentry).
- Realiza backups automáticos de la base de datos.

---

<a id="4-seguridad"></a>
<a id="-4-seguridad"></a>
## 4. Seguridad

- Mantén dependencias actualizadas (`composer update`, `npm update`).
- Usa HTTPS y certificados válidos.
- Valida y sanitiza toda entrada de usuario.
- Configura variables sensibles en `.env` y nunca las subas al repositorio.
- Limita permisos de archivos y carpetas.

---

<a id="5-recursos-y-enlaces-utiles"></a>
<a id="-5-recursos-y-enlaces-utiles"></a>
## 5. Recursos y Enlaces Útiles

- [Documentación oficial de PHP](https://www.php.net/docs.php)
- [Composer](https://getcomposer.org/doc/)
- [PHPUnit](https://phpunit.de/documentation.html)
- [Xdebug](https://xdebug.org/docs/)
- [Docker](https://docs.docker.com/)
- [MySQL](https://dev.mysql.com/doc/)
- [VSCode](https://code.visualstudio.com/docs)

---

[⬅️ Volver al Índice](INDEX.md)
