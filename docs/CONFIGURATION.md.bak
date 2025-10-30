<a id="snackshop-configuracion-avanzada"></a>
<a id="-snackshop-configuracion-avanzada"></a>
# ⚙️ SnackShop - Configuración Avanzada
<!-- TOC -->
<a id="contenido"></a>
<a id="-contenido"></a>
## Contenido

- [🧭 Navegación](#-navegacion)
- [📋 Índice](#-indice)
- [🔧 Variables de Entorno Detalladas](#-variables-de-entorno-detalladas)
  - [Core Application Settings](#core-application-settings)
  - [Database Configuration](#database-configuration)
  - [Session & Security](#session-security)
  - [File Upload & Processing](#file-upload-processing)
  - [Logging & Monitoring](#logging-monitoring)
  - [Cache y Redis](#cache-y-redis)
  - [Configuración de SSL](#configuracion-de-ssl)
  - [Configuración de Backup](#configuracion-de-backup)
  - [Email Configuration](#email-configuration)
  - [API Configuration](#api-configuration)
- [🐘 Configuración PHP](#-configuracion-php)
  - [`php.ini` Optimizado](#phpini-optimizado)
  - [PHP-FPM Pool Configuration](#php-fpm-pool-configuration)
- [🌐 Configuración Nginx](#-configuracion-nginx)
  - [Virtual Host Optimizado](#virtual-host-optimizado)
  - [Nginx Performance Tuning](#nginx-performance-tuning)
- [🗄️ Configuración MySQL](#-configuracion-mysql)
  - [`my.cnf` Optimizado](#mycnf-optimizado)
  - [Optimización por Tamaño de Servidor](#optimizacion-por-tamano-de-servidor)
- [🚀 Optimización de Performance](#-optimizacion-de-performance)
  - [Configuración de Cache](#configuracion-de-cache)
  - [Database Optimization](#database-optimization)
  - [Image Optimization](#image-optimization)
- [🔒 Configuración de Seguridad](#-configuracion-de-seguridad)
  - [Security Headers avanzados](#security-headers-avanzados)
  - [Input Validation & Sanitization](#input-validation-sanitization)
- [📊 Logging Avanzado](#-logging-avanzado)
  - [Structured Logging](#structured-logging)
  - [Log Rotation & Management](#log-rotation-management)
- [🧬 Configuración por Entorno](#-configuracion-por-entorno)
  - [Development Environment](#development-environment)
  - [Staging Environment](#staging-environment)
  - [Production Environment](#production-environment)
- [🔔 Monitoring y Alertas](#-monitoring-y-alertas)
  - [Health Check Endpoint](#health-check-endpoint)
  - [Alerting System](#alerting-system)
- [📝 Configuración de Logs Centralizados](#-configuracion-de-logs-centralizados)
  - [ELK Stack Integration (Opcional)](#elk-stack-integration-opcional)
- [🔗 Documentos Relacionados](#-documentos-relacionados)
- [📞 Soporte](#-soporte)
<!-- /TOC -->

**🏠 Ubicación:** `CONFIGURATION.md`
**📅 Última actualización:** 28 de octubre, 2025
**🎯 Propósito:** Configuración avanzada, optimización de producción y fine-tuning del sistema

---

<a id="navegacion"></a>
<a id="-navegacion"></a>
## 🧭 Navegación

**[📖 Índice General](docs/INDEX.md)** | **[🏠 README](README.md)** | **[🚀 Deployment](DEPLOYMENT.md)** | **[🏗️ Arquitectura](ARCHITECTURE.md)**

---

<a id="indice"></a>
<a id="-indice"></a>
## 📋 Índice

- [Variables de Entorno Detalladas](#variables-de-entorno-detalladas)
- [Configuración PHP](#configuración-php)
- [Configuración Nginx](#configuración-nginx)
- [Configuración MySQL](#configuración-mysql)
- [Optimización de Performance](#optimización-de-performance)
- [Configuración de Seguridad](#configuración-de-seguridad)
- [Logging Avanzado](#logging-avanzado)
- [Cache y Redis](#cache-y-redis)
- [Configuración por Entorno](#configuración-por-entorno)
- [Configuración de SSL](#configuración-de-ssl)
- [Monitoring y Alertas](#monitoring-y-alertas)
- [Configuración de Backup](#configuración-de-backup)

---

<a id="variables-de-entorno-detalladas"></a>
<a id="-variables-de-entorno-detalladas"></a>
## 🔧 Variables de Entorno Detalladas

<a id="core-application-settings"></a>
<a id="-core-application-settings"></a>
### Core Application Settings

```bash
<a id="configuration"></a>
<a id="-configuration"></a>
# ==============================================
<a id="core-configuration"></a>
<a id="-core-configuration"></a>
# CORE CONFIGURATION
<a id="configuration"></a>
<a id="-configuration"></a>
# ==============================================

<a id="environment"></a>
<a id="-environment"></a>
# Environment
APP_ENV=production                    # development, staging, production
APP_DEBUG=false                      # true en desarrollo, false en producción
APP_NAME="SnackShop"                 # Nombre de la aplicación
APP_VERSION=1.0.0                    # Versión para versionado de assets
APP_URL=https://tu-dominio.com        # URL base de la aplicación
APP_TIMEZONE=America/Mexico_City      # Zona horaria

<a id="performance"></a>
<a id="-performance"></a>
# Performance
APP_CACHE_ENABLED=true               # Habilitar sistema de cache
APP_CACHE_TTL=3600                   # TTL por defecto del cache (segundos)
APP_GZIP_ENABLED=true                # Compresión gzip para responses
APP_ETAG_ENABLED=true                # ETags para cache HTTP
```

<a id="database-configuration"></a>
<a id="-database-configuration"></a>
### Database Configuration

```bash
<a id="configuration"></a>
<a id="-configuration"></a>
# ==============================================
<a id="database-configuration"></a>
<a id="-database-configuration"></a>
# DATABASE CONFIGURATION
<a id="configuration"></a>
<a id="-configuration"></a>
# ==============================================

<a id="primary-database"></a>
<a id="-primary-database"></a>
# Primary Database
SNACKSHOP_DB_HOST=localhost          # Host de la base de datos
SNACKSHOP_DB_PORT=3306               # Puerto MySQL
SNACKSHOP_DB_NAME=snackshop          # Nombre de la base de datos
SNACKSHOP_DB_USER=snackshop          # Usuario de la base de datos
SNACKSHOP_DB_PASS=secure_password    # Password de la base de datos
SNACKSHOP_DB_CHARSET=utf8mb4         # Charset para MySQL
SNACKSHOP_DB_COLLATION=utf8mb4_unicode_ci  # Collation

<a id="connection-pool"></a>
<a id="-connection-pool"></a>
# Connection Pool
DB_PERSISTENT=true                   # Conexiones persistentes
DB_TIMEOUT=30                        # Timeout de conexión (segundos)
DB_MAX_CONNECTIONS=100               # Máximo conexiones simultáneas

<a id="readwrite-split-opcional"></a>
<a id="-readwrite-split-opcional"></a>
# Read/Write Split (Opcional)
DB_READ_HOST=read-replica.interno    # Host de lectura (replica)
DB_WRITE_HOST=master.interno         # Host de escritura (master)

<a id="backup-database-opcional"></a>
<a id="-backup-database-opcional"></a>
# Backup Database (Opcional)
DB_BACKUP_HOST=backup.interno        # Host de backup para operaciones read-only
DB_BACKUP_USER=backup_user           # Usuario solo lectura para backups
DB_BACKUP_PASS=backup_password       # Password para usuario backup
```

<a id="session-security"></a>
<a id="-session-security"></a>
### Session & Security

```bash
<a id="configuration"></a>
<a id="-configuration"></a>
# ==============================================
<a id="session-security"></a>
<a id="-session-security"></a>
# SESSION & SECURITY
<a id="configuration"></a>
<a id="-configuration"></a>
# ==============================================

<a id="session-configuration"></a>
<a id="-session-configuration"></a>
# Session Configuration
SESSION_DRIVER=file                  # file, database, redis
SESSION_LIFETIME=7200                # Duración sesión (segundos) - 2 horas
SESSION_PATH=/tmp/snackshop_sessions # Directorio para sesiones (driver=file)
SESSION_SECURE=true                  # Solo HTTPS (producción)
SESSION_HTTPONLY=true                # No accesible via JavaScript
SESSION_SAMESITE=Strict              # Strict, Lax, None

<a id="csrf-protection"></a>
<a id="-csrf-protection"></a>
# CSRF Protection
CSRF_ENABLED=true                    # Habilitar protección CSRF
CSRF_TOKEN_LIFETIME=3600             # Duración token CSRF (segundos)
CSRF_REGENERATE_ON_LOGIN=true        # Regenerar token en login

<a id="password-security"></a>
<a id="-password-security"></a>
# Password Security
BCRYPT_ROUNDS=12                     # Costo de bcrypt (10-15)
PASSWORD_MIN_LENGTH=8                # Longitud mínima de passwords
PASSWORD_REQUIRE_SPECIAL=true        # Requerir caracteres especiales
PASSWORD_REQUIRE_NUMBERS=true        # Requerir números
PASSWORD_REQUIRE_UPPERCASE=true      # Requerir mayúsculas

<a id="authentication"></a>
<a id="-authentication"></a>
# Authentication
AUTH_LOGIN_ATTEMPTS=5                # Intentos de login antes de bloqueo
AUTH_LOCKOUT_TIME=900               # Tiempo de bloqueo (segundos) - 15 min
AUTH_REMEMBER_TOKEN_LIFETIME=2592000 # Remember me (segundos) - 30 días
```

<a id="file-upload-processing"></a>
<a id="-file-upload-processing"></a>
### File Upload & Processing

```bash
<a id="configuration"></a>
<a id="-configuration"></a>
# ==============================================
<a id="file-upload-processing"></a>
<a id="-file-upload-processing"></a>
# FILE UPLOAD & PROCESSING
<a id="configuration"></a>
<a id="-configuration"></a>
# ==============================================

<a id="upload-limits"></a>
<a id="-upload-limits"></a>
# Upload Limits
MAX_UPLOAD_SIZE=5242880              # Tamaño máximo (bytes) - 5MB
MAX_UPLOADS_PER_REQUEST=10           # Máximo archivos por request
UPLOAD_TIMEOUT=300                   # Timeout de upload (segundos)

<a id="image-processing"></a>
<a id="-image-processing"></a>
# Image Processing
ALLOWED_IMAGE_TYPES=jpg,jpeg,png,gif,webp,svg
IMAGE_MAX_WIDTH=2048                 # Máximo ancho en pixels
IMAGE_MAX_HEIGHT=2048                # Máximo alto en pixels
IMAGE_QUALITY=85                     # Calidad JPEG (1-100)
IMAGE_PROGRESSIVE=true               # JPEG progresivo
THUMBNAIL_SIZES=150x150,300x300,500x500  # Tamaños de thumbnails

<a id="image-storage"></a>
<a id="-image-storage"></a>
# Image Storage
IMAGES_PATH=data/images              # Directorio de imágenes
IMAGES_URL_PREFIX=/images            # Prefijo URL para imágenes
IMAGES_ORGANIZE_BY_DATE=true         # Organizar por fecha (Y/m/d)
```

<a id="logging-monitoring"></a>
<a id="-logging-monitoring"></a>
### Logging & Monitoring

```bash
<a id="configuration"></a>
<a id="-configuration"></a>
# ==============================================
<a id="logging-monitoring"></a>
<a id="-logging-monitoring"></a>
# LOGGING & MONITORING
<a id="configuration"></a>
<a id="-configuration"></a>
# ==============================================

<a id="application-logging"></a>
<a id="-application-logging"></a>
# Application Logging
LOG_LEVEL=info                       # debug, info, warning, error, critical
LOG_FILE=/var/log/snackshop/app.log  # Archivo de log principal
LOG_MAX_SIZE=10485760               # Tamaño máximo (bytes) - 10MB
LOG_ROTATE=true                      # Rotación automática
LOG_MAX_FILES=5                      # Máximo archivos de log a mantener

<a id="error-logging"></a>
<a id="-error-logging"></a>
# Error Logging
ERROR_LOG_FILE=/var/log/snackshop/error.log
SLOW_QUERY_LOG=true                  # Log de queries lentas
SLOW_QUERY_THRESHOLD=2               # Threshold para query lenta (segundos)

<a id="access-logging"></a>
<a id="-access-logging"></a>
# Access Logging
ACCESS_LOG_ENABLED=true              # Log de accesos HTTP
ACCESS_LOG_FILE=/var/log/snackshop/access.log
ACCESS_LOG_FORMAT=combined           # combined, common, custom

<a id="performance-monitoring"></a>
<a id="-performance-monitoring"></a>
# Performance Monitoring
PERFORMANCE_MONITORING=true          # Monitoreo de performance
MEMORY_LIMIT_WARNING=512M            # Advertencia uso memoria
EXECUTION_TIME_WARNING=5             # Advertencia tiempo ejecución (segundos)
```

<a id="cache-y-redis"></a>
<a id="-cache-y-redis"></a>
### Cache y Redis

```bash
<a id="configuracion-basica-para-redis-en-produccion"></a>
<a id="-configuracion-basica-para-redis-en-produccion"></a>
# Configuración básica para Redis en producción
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_TTL=3600
```

Añade aquí instrucciones sobre cómo instalar y tunear Redis (maxmemory, eviction policy), y cómo habilitar el driver de cache en `config/cache.php` o la configuración equivalente.

<a id="configuracion-de-ssl"></a>
<a id="-configuracion-de-ssl"></a>
### Configuración de SSL

```bash
<a id="notas-rapidas-para-ssltls"></a>
<a id="-notas-rapidas-para-ssltls"></a>
# Notas rápidas para SSL/TLS
TLS_CERT_PATH=/etc/letsencrypt/live/tu-dominio/fullchain.pem
TLS_KEY_PATH=/etc/letsencrypt/live/tu-dominio/privkey.pem
```

Describe la configuración recomendada: forzar HTTPS, HSTS, redirecciones en Nginx/Apache, renovación automática con certbot y pruebas con SSL Labs.

<a id="configuracion-de-backup"></a>
<a id="-configuracion-de-backup"></a>
### Configuración de Backup

```bash
<a id="ejemplo-de-script-de-backup-esquema-resumido"></a>
<a id="-ejemplo-de-script-de-backup-esquema-resumido"></a>
# Ejemplo de script de backup (esquema resumido)
<a id="1-dump-de-base-de-datos"></a>
<a id="-1-dump-de-base-de-datos"></a>
# 1) Dump de base de datos
mysqldump -u user -p password snackshop > /backups/snackshop_$(date +%F).sql
<a id="2-comprimir-y-rotar"></a>
<a id="-2-comprimir-y-rotar"></a>
# 2) Comprimir y rotar
tar -czf /backups/snackshop_$(date +%F).tar.gz /backups/snackshop_$(date +%F).sql
find /backups -type f -mtime +30 -delete  # eliminar backups viejos a 30 días
```

Explica políticas de retención, backups offsite y pruebas de restauración periódicas.

<a id="email-configuration"></a>
<a id="-email-configuration"></a>
### Email Configuration

```bash
<a id="configuration"></a>
<a id="-configuration"></a>
# ==============================================
<a id="email-configuration"></a>
<a id="-email-configuration"></a>
# EMAIL CONFIGURATION
<a id="configuration"></a>
<a id="-configuration"></a>
# ==============================================

<a id="smtp-settings"></a>
<a id="-smtp-settings"></a>
# SMTP Settings
MAIL_DRIVER=smtp                     # smtp, sendmail, mail
MAIL_HOST=smtp.gmail.com             # Servidor SMTP
MAIL_PORT=587                        # Puerto SMTP
MAIL_USERNAME=tu-email@gmail.com     # Usuario SMTP
MAIL_PASSWORD=app_password           # Password SMTP (app password recomendado)
MAIL_ENCRYPTION=tls                  # tls, ssl, null

<a id="email-defaults"></a>
<a id="-email-defaults"></a>
# Email Defaults
MAIL_FROM_ADDRESS=no-reply@tu-dominio.com
MAIL_FROM_NAME="SnackShop Sistema"
MAIL_REPLY_TO=soporte@tu-dominio.com

<a id="email-features"></a>
<a id="-email-features"></a>
# Email Features
MAIL_QUEUE_ENABLED=false             # Queue para emails (requiere worker)
MAIL_RATE_LIMIT=100                  # Emails por hora
MAIL_HTML_ENABLED=true               # Soporte para HTML emails
```

<a id="api-configuration"></a>
<a id="-api-configuration"></a>
### API Configuration

```bash
<a id="configuration"></a>
<a id="-configuration"></a>
# ==============================================
<a id="api-configuration"></a>
<a id="-api-configuration"></a>
# API CONFIGURATION
<a id="configuration"></a>
<a id="-configuration"></a>
# ==============================================

<a id="api-settings"></a>
<a id="-api-settings"></a>
# API Settings
API_ENABLED=true                     # Habilitar API endpoints
API_VERSION=v1                       # Versión de la API
API_BASE_URL=/api/v1                 # Base URL de la API
API_DOCUMENTATION_ENABLED=true       # Documentación automática

<a id="rate-limiting"></a>
<a id="-rate-limiting"></a>
# Rate Limiting
API_RATE_LIMIT_REQUESTS=1000         # Requests por hora por IP
API_RATE_LIMIT_WINDOW=3600           # Ventana de tiempo (segundos)
API_BURST_LIMIT=50                   # Burst limit para requests

<a id="api-authentication"></a>
<a id="-api-authentication"></a>
# API Authentication
API_TOKEN_LIFETIME=86400             # Duración tokens API (segundos) - 24h
API_REFRESH_TOKEN_LIFETIME=604800    # Duración refresh tokens - 7 días
API_CORS_ENABLED=true                # Habilitar CORS
API_CORS_ORIGINS=https://app.tu-dominio.com,https://admin.tu-dominio.com
```

---

<a id="configuracion-php"></a>
<a id="-configuracion-php"></a>
## 🐘 Configuración PHP

<a id="phpini-optimizado"></a>
<a id="-phpini-optimizado"></a>
### `php.ini` Optimizado

```ini
; ==============================================
; PHP CONFIGURATION FOR SNACKSHOP
; ==============================================

; Basic Settings
memory_limit = 256M                  ; Aumentar si procesas muchas imágenes
max_execution_time = 120             ; Para operaciones pesadas
max_input_time = 120                 ; Tiempo máximo para input
max_input_vars = 3000               ; Variables POST/GET máximas

; Upload Settings
file_uploads = On
upload_max_filesize = 10M           ; Tamaño máximo por archivo
max_file_uploads = 20               ; Máximo archivos simultáneos
post_max_size = 50M                 ; Tamaño máximo POST total

; Session Settings
session.auto_start = 0
session.cookie_httponly = 1         ; Seguridad: solo HTTP
session.cookie_secure = 1           ; Solo HTTPS en producción
session.cookie_samesite = "Strict"  ; CSRF protection
session.use_strict_mode = 1         ; Prevenir session fixation
session.cookie_lifetime = 0         ; Expirar con el navegador
session.gc_maxlifetime = 7200       ; 2 horas
session.gc_probability = 1
session.gc_divisor = 100

; Error Handling
display_errors = Off                ; NUNCA On en producción
display_startup_errors = Off
log_errors = On
error_log = /var/log/php/error.log
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT

; Performance
opcache.enable = 1                  ; OPcache habilitado
opcache.enable_cli = 1
opcache.memory_consumption = 128    ; MB para OPcache
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 4000
opcache.revalidate_freq = 2         ; Revalidar cada 2 segundos
opcache.fast_shutdown = 1
opcache.save_comments = 1           ; Para frameworks que usan annotations

; Security
expose_php = Off                    ; Ocultar versión PHP
allow_url_fopen = Off              ; Deshabilitar por seguridad
allow_url_include = Off            ; Deshabilitar por seguridad
disable_functions = exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source

; Date & Locale
date.timezone = "America/Mexico_City"
default_charset = "UTF-8"

; Output
output_buffering = 4096
implicit_flush = Off
zlib.output_compression = On        ; Compresión gzip
zlib.output_compression_level = 6
```

<a id="php-fpm-pool-configuration"></a>
<a id="-php-fpm-pool-configuration"></a>
### PHP-FPM Pool Configuration

**`/etc/php/8.1/fpm/pool.d/snackshop.conf`**
```ini
; SnackShop Pool Configuration
[snackshop]

; Process management
user = www-data
group = www-data
listen = /var/run/php/php8.1-fpm-snackshop.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

; Process manager
pm = dynamic                        ; static, dynamic, ondemand
pm.max_children = 20               ; Máximo procesos (ajustar según RAM)
pm.start_servers = 3               ; Procesos al inicio
pm.min_spare_servers = 2           ; Mínimo procesos idle
pm.max_spare_servers = 6           ; Máximo procesos idle
pm.max_requests = 500              ; Requests antes de restart

; Performance
request_terminate_timeout = 120     ; Timeout por request
request_slowlog_timeout = 5         ; Log requests lentas
slowlog = /var/log/php/snackshop-slow.log

; Environment
env[PATH] = /usr/local/bin:/usr/bin:/bin
env[TMP] = /tmp
env[TMPDIR] = /tmp
env[TEMP] = /tmp

; Custom PHP values
php_admin_value[memory_limit] = 256M
php_admin_value[max_execution_time] = 120
php_admin_value[upload_max_filesize] = 10M
php_admin_value[post_max_size] = 50M
php_admin_value[session.cookie_secure] = 1
php_admin_value[session.cookie_httponly] = 1

; Security
php_admin_flag[log_errors] = on
php_admin_value[error_log] = /var/log/php/snackshop-error.log
```

---

<a id="configuracion-nginx"></a>
<a id="-configuracion-nginx"></a>
## 🌐 Configuración Nginx

<a id="virtual-host-optimizado"></a>
<a id="-virtual-host-optimizado"></a>
### Virtual Host Optimizado

**`/etc/nginx/sites-available/snackshop`**
```nginx
<a id="snackshop-nginx-configuration"></a>
<a id="-snackshop-nginx-configuration"></a>
# SnackShop Nginx Configuration
server {
    listen 80;
    server_name tu-dominio.com www.tu-dominio.com;

    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name tu-dominio.com www.tu-dominio.com;

    root /var/www/snackshop/public;
    index index.php index.html;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/tu-dominio.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/tu-dominio.com/privkey.pem;
    ssl_trusted_certificate /etc/letsencrypt/live/tu-dominio.com/chain.pem;

    # SSL Settings
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    ssl_session_tickets off;
    ssl_stapling on;
    ssl_stapling_verify on;

    # Security Headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self'; connect-src 'self';" always;

    # Hide server information
    server_tokens off;

    # Rate Limiting Zones
    limit_req_zone $binary_remote_addr zone=login:10m rate=5r/m;
    limit_req_zone $binary_remote_addr zone=api:10m rate=100r/m;
    limit_req_zone $binary_remote_addr zone=general:10m rate=60r/m;

    # Performance Settings
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    client_max_body_size 50M;
    client_body_timeout 60s;
    client_header_timeout 60s;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/json
        application/javascript
        application/xml+rss
        application/atom+xml
        image/svg+xml;

    # Main location
    location / {
        limit_req zone=general burst=20 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Authentication endpoints (stricter rate limiting)
    location ~ ^/(login|logout|auth) {
        limit_req zone=login burst=3 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    # API endpoints
    location ~ ^/api/ {
        limit_req zone=api burst=50 nodelay;
        try_files $uri $uri/ /index.php?$query_string;

        # CORS headers for API
        add_header Access-Control-Allow-Origin "https://app.tu-dominio.com" always;
        add_header Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS" always;
        add_header Access-Control-Allow-Headers "Authorization, Content-Type, X-Requested-With" always;

        if ($request_method = 'OPTIONS') {
            add_header Access-Control-Allow-Origin "https://app.tu-dominio.com";
            add_header Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS";
            add_header Access-Control-Allow-Headers "Authorization, Content-Type, X-Requested-With";
            add_header Access-Control-Max-Age 86400;
            add_header Content-Length 0;
            add_header Content-Type text/plain;
            return 204;
        }
    }

    # PHP handling
    location ~ \\.php$ {
        fastcgi_split_path_info ^(.+\\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php8.1-fpm-snackshop.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;

        # Security & Performance
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 120;
        fastcgi_connect_timeout 5;
        fastcgi_send_timeout 120;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }

    # Static files with aggressive caching
    location ~* \\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot|webp)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        add_header Vary "Accept-Encoding";
        access_log off;

        # Optional: serve WebP if available
        location ~* \\.(png|jpg|jpeg)$ {
            add_header Vary "Accept";
            try_files $uri$webp_suffix $uri =404;
        }
    }

    # Deny access to sensitive files
    location ~ /\\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    location ~ \\.(env|log|sql|md|txt|conf)$ {
        deny all;
        access_log off;
        log_not_found off;
    }

    location ~ /vendor/ {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Robots and favicon
    location = /robots.txt {
        access_log off;
        log_not_found off;
    }

    location = /favicon.ico {
        access_log off;
        log_not_found off;
    }

    # Error pages
    error_page 404 /errors/404.php;
    error_page 403 /errors/403.php;
    error_page 500 502 503 504 /errors/500.php;

    # Logging
    access_log /var/log/nginx/snackshop-access.log combined;
    error_log /var/log/nginx/snackshop-error.log warn;
}

<a id="webp-support-map"></a>
<a id="-webp-support-map"></a>
# WebP Support Map
map $http_accept $webp_suffix {
    "~*webp" ".webp";
}
```

<a id="nginx-performance-tuning"></a>
<a id="-nginx-performance-tuning"></a>
### Nginx Performance Tuning

**`/etc/nginx/nginx.conf`**
```nginx
user www-data;
worker_processes auto;              # Auto-detect CPU cores
worker_rlimit_nofile 65535;        # Max open files per worker
pid /run/nginx.pid;

events {
    worker_connections 4096;        # Max connections per worker
    use epoll;                      # Efficient event model for Linux
    multi_accept on;                # Accept multiple connections
}

http {
    # Basic Settings
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    keepalive_requests 1000;
    types_hash_max_size 2048;
    server_tokens off;

    # MIME
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    # Buffer Settings
    client_body_buffer_size 128k;
    client_max_body_size 50m;
    client_header_buffer_size 1k;
    large_client_header_buffers 4 4k;
    output_buffers 1 32k;
    postpone_output 1460;

    # Timeouts
    client_header_timeout 30s;
    client_body_timeout 60s;
    send_timeout 60s;

    # Logging Format
    log_format main '$remote_addr - $remote_user [$time_local] "$request" '
                   '$status $body_bytes_sent "$http_referer" '
                   '"$http_user_agent" "$http_x_forwarded_for" '
                   '$request_time $upstream_response_time';

    access_log /var/log/nginx/access.log main;
    error_log /var/log/nginx/error.log warn;

    # Gzip Settings
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_min_length 1024;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/json
        application/javascript
        application/xml+rss
        application/atom+xml
        image/svg+xml;

    # Rate Limiting (Global)
    limit_req_zone $binary_remote_addr zone=global:10m rate=30r/m;
    limit_req_status 429;

    # Include virtual hosts
    include /etc/nginx/conf.d/*.conf;
    include /etc/nginx/sites-enabled/*;
}
```

---

<a id="configuracion-mysql"></a>
<a id="-configuracion-mysql"></a>
## 🗄️ Configuración MySQL

<a id="mycnf-optimizado"></a>
<a id="-mycnf-optimizado"></a>
### `my.cnf` Optimizado

**`/etc/mysql/mysql.conf.d/snackshop.cnf`**
```ini
<a id="snackshop-mysql-configuration"></a>
<a id="-snackshop-mysql-configuration"></a>
# SnackShop MySQL Configuration

[mysql]
default-character-set = utf8mb4

[mysqld]
<a id="basic-settings"></a>
<a id="-basic-settings"></a>
# Basic Settings
user = mysql
pid-file = /var/run/mysqld/mysqld.pid
socket = /var/run/mysqld/mysqld.sock
port = 3306
basedir = /usr
datadir = /var/lib/mysql
tmpdir = /tmp
lc-messages-dir = /usr/share/mysql

<a id="character-set"></a>
<a id="-character-set"></a>
# Character Set
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci
init_connect = 'SET NAMES utf8mb4'

<a id="innodb-settings-para-aplicaciones-oltp-como-snackshop"></a>
<a id="-innodb-settings-para-aplicaciones-oltp-como-snackshop"></a>
# InnoDB Settings (para aplicaciones OLTP como SnackShop)
default-storage-engine = InnoDB
innodb_buffer_pool_size = 512M      # 70-80% de RAM disponible para MySQL
innodb_log_file_size = 128M         # 25% de buffer_pool_size
innodb_log_buffer_size = 16M
innodb_flush_log_at_trx_commit = 2  # Performance vs durabilidad
innodb_file_per_table = 1
innodb_flush_method = O_DIRECT      # Evitar double buffering

<a id="connection-settings"></a>
<a id="-connection-settings"></a>
# Connection Settings
max_connections = 200               # Conexiones simultáneas máximas
max_connect_errors = 10000
connect_timeout = 10
wait_timeout = 28800               # 8 horas
interactive_timeout = 28800

<a id="buffer-settings"></a>
<a id="-buffer-settings"></a>
# Buffer Settings
key_buffer_size = 32M              # Para tablas MyISAM (si las hay)
sort_buffer_size = 2M              # Para ORDER BY
read_buffer_size = 2M              # Para table scans
read_rnd_buffer_size = 2M          # Para random reads
myisam_sort_buffer_size = 64M

<a id="query-cache-deprecado-en-mysql-80"></a>
<a id="-query-cache-deprecado-en-mysql-80"></a>
# Query Cache (deprecado en MySQL 8.0+)
<a id="query_cache_type-1"></a>
<a id="-query_cache_type-1"></a>
# query_cache_type = 1
<a id="query_cache_size-64m"></a>
<a id="-query_cache_size-64m"></a>
# query_cache_size = 64M
<a id="query_cache_limit-2m"></a>
<a id="-query_cache_limit-2m"></a>
# query_cache_limit = 2M

<a id="table-cache"></a>
<a id="-table-cache"></a>
# Table Cache
table_open_cache = 4000
table_definition_cache = 2000

<a id="temporary-tables"></a>
<a id="-temporary-tables"></a>
# Temporary Tables
tmp_table_size = 64M
max_heap_table_size = 64M

<a id="logging"></a>
<a id="-logging"></a>
# Logging
log_error = /var/log/mysql/error.log
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow.log
long_query_time = 2                # Log queries > 2 segundos
log_queries_not_using_indexes = 1

<a id="binary-logging-para-replication-si-es-necesario"></a>
<a id="-binary-logging-para-replication-si-es-necesario"></a>
# Binary Logging (para replication si es necesario)
<a id="log_bin-varlogmysqlmysql-binlog"></a>
<a id="-log_bin-varlogmysqlmysql-binlog"></a>
# log_bin = /var/log/mysql/mysql-bin.log
<a id="server_id-1"></a>
<a id="-server_id-1"></a>
# server_id = 1
<a id="expire_logs_days-7"></a>
<a id="-expire_logs_days-7"></a>
# expire_logs_days = 7

<a id="security"></a>
<a id="-security"></a>
# Security
bind-address = 127.0.0.1           # Solo conexiones locales
<a id="skip-networking-deshabilitar-tcpip-completamente"></a>
<a id="-skip-networking-deshabilitar-tcpip-completamente"></a>
# skip-networking                   # Deshabilitar TCP/IP completamente
local-infile = 0                   # Deshabilitar LOAD DATA LOCAL INFILE

<a id="performance-schema-mysql-56"></a>
<a id="-performance-schema-mysql-56"></a>
# Performance Schema (MySQL 5.6+)
performance_schema = ON
performance_schema_max_table_instances = 400
performance_schema_max_table_handles = 2000

[mysqldump]
quick
quote-names
max_allowed_packet = 64M

[isamchk]
key_buffer_size = 16M
sort_buffer_size = 16M
read_buffer = 2M
write_buffer = 2M
```

<a id="optimizacion-por-tamano-de-servidor"></a>
<a id="-optimizacion-por-tamano-de-servidor"></a>
### Optimización por Tamaño de Servidor

<a id="servidor-pequeno-1-2gb-ram"></a>
<a id="-servidor-pequeno-1-2gb-ram"></a>
#### Servidor Pequeño (1-2GB RAM)

```ini
innodb_buffer_pool_size = 512M
max_connections = 50
innodb_log_file_size = 64M
```

<a id="servidor-mediano-4-8gb-ram"></a>
<a id="-servidor-mediano-4-8gb-ram"></a>
#### Servidor Mediano (4-8GB RAM)

```ini
innodb_buffer_pool_size = 2G
max_connections = 150
innodb_log_file_size = 256M
```

<a id="servidor-grande-16gb-ram"></a>
<a id="-servidor-grande-16gb-ram"></a>
#### Servidor Grande (16GB+ RAM)

```ini
innodb_buffer_pool_size = 8G
max_connections = 300
innodb_log_file_size = 512M
innodb_buffer_pool_instances = 8
```

---

<a id="optimizacion-de-performance"></a>
<a id="-optimizacion-de-performance"></a>
## 🚀 Optimización de Performance

<a id="configuracion-de-cache"></a>
<a id="-configuracion-de-cache"></a>
### Configuración de Cache

<a id="implementacion-de-cache-en-php"></a>
<a id="-implementacion-de-cache-en-php"></a>
#### Implementación de Cache en PHP

```php
// src/Utils/Cache.php
class Cache {
    private static $redis = null;
    private static $enabled = false;

    public static function init() {
        if (getenv('CACHE_DRIVER') === 'redis') {
            self::$redis = new Redis();
            self::$redis->connect(
                getenv('REDIS_HOST') ?: '127.0.0.1',
                getenv('REDIS_PORT') ?: 6379
            );
            self::$enabled = true;
        }
    }

    public static function get($key, $default = null) {
        if (!self::$enabled) return $default;

        $value = self::$redis->get($key);
        return $value !== false ? json_decode($value, true) : $default;
    }

    public static function set($key, $value, $ttl = 3600) {
        if (!self::$enabled) return false;

        return self::$redis->setex($key, $ttl, json_encode($value));
    }

    public static function delete($key) {
        if (!self::$enabled) return false;

        return self::$redis->del($key);
    }

    public static function flush() {
        if (!self::$enabled) return false;

        return self::$redis->flushAll();
    }
}

// Uso en ProductService
class ProductService {
    public function getProductById($id) {
        $cacheKey = "product_$id";
        $product = Cache::get($cacheKey);

        if (!$product) {
            $product = $this->productRepository->findById($id);
            Cache::set($cacheKey, $product, 1800); // 30 minutos
        }

        return $product;
    }
}
```

<a id="redis-configuration"></a>
<a id="-redis-configuration"></a>
#### Redis Configuration

```bash
<a id="variables-de-entorno-para-redis"></a>
<a id="-variables-de-entorno-para-redis"></a>
# Variables de entorno para Redis
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=
REDIS_DATABASE=0
REDIS_MAX_CONNECTIONS=20
```

**`/etc/redis/redis.conf`**
```conf
<a id="network"></a>
<a id="-network"></a>
# Network
bind 127.0.0.1
port 6379
timeout 300
tcp-keepalive 60

<a id="memory"></a>
<a id="-memory"></a>
# Memory
maxmemory 256mb
maxmemory-policy allkeys-lru
maxmemory-samples 5

<a id="persistence-para-cache-no-es-critico"></a>
<a id="-persistence-para-cache-no-es-critico"></a>
# Persistence (para cache no es crítico)
save ""
<a id="save-900-1"></a>
<a id="-save-900-1"></a>
# save 900 1
<a id="save-300-10"></a>
<a id="-save-300-10"></a>
# save 300 10
<a id="save-60-10000"></a>
<a id="-save-60-10000"></a>
# save 60 10000

<a id="security"></a>
<a id="-security"></a>
# Security
requirepass your_redis_password_here

<a id="performance"></a>
<a id="-performance"></a>
# Performance
tcp-backlog 511
databases 16
stop-writes-on-bgsave-error no
```

<a id="database-optimization"></a>
<a id="-database-optimization"></a>
### Database Optimization

<a id="query-optimization"></a>
<a id="-query-optimization"></a>
#### Query Optimization

```php
// src/Utils/QueryOptimizer.php
class QueryOptimizer {
    private $db;
    private $slowQueries = [];

    public function __construct($database) {
        $this->db = $database;
    }

    public function executeWithProfiling($query, $params = []) {
        $start = microtime(true);

        $stmt = $this->db->prepare($query);
        $result = $stmt->execute($params);

        $executionTime = microtime(true) - $start;

        // Log slow queries
        if ($executionTime > (float)getenv('SLOW_QUERY_THRESHOLD')) {
            $this->logSlowQuery($query, $params, $executionTime);
        }

        return $result;
    }

    private function logSlowQuery($query, $params, $time) {
        $logEntry = [
            'query' => $query,
            'params' => $params,
            'execution_time' => $time,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        file_put_contents(
            '/var/log/snackshop/slow-queries.log',
            json_encode($logEntry) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }
}
```

<a id="database-indices"></a>
<a id="-database-indices"></a>
#### Database Indices

```sql
-- Índices recomendados para SnackShop

-- Tabla usuarios
CREATE INDEX idx_usuarios_email ON usuarios(email);
CREATE INDEX idx_usuarios_activo ON usuarios(activo);

-- Tabla productos
CREATE INDEX idx_productos_categoria ON productos(categoria_id);
CREATE INDEX idx_productos_activo ON productos(activo);
CREATE INDEX idx_productos_precio ON productos(precio);

-- Tabla ventas
CREATE INDEX idx_ventas_fecha ON ventas(fecha_venta);
CREATE INDEX idx_ventas_usuario ON ventas(usuario_id);
CREATE INDEX idx_ventas_metodo_pago ON ventas(metodo_pago_id);

-- Tabla detalle_ventas
CREATE INDEX idx_detalle_venta_id ON detalle_ventas(venta_id);
CREATE INDEX idx_detalle_producto_id ON detalle_ventas(producto_id);

-- Índices compuestos para consultas frecuentes
CREATE INDEX idx_productos_categoria_activo ON productos(categoria_id, activo);
CREATE INDEX idx_ventas_fecha_usuario ON ventas(fecha_venta, usuario_id);

-- Full-text search para búsqueda de productos
ALTER TABLE productos ADD FULLTEXT(nombre, descripcion);
```

<a id="image-optimization"></a>
<a id="-image-optimization"></a>
### Image Optimization

```php
// src/Services/OptimizedImageProcessing.php
class OptimizedImageProcessingService {
    private $allowedTypes;
    private $maxWidth;
    private $maxHeight;
    private $quality;

    public function __construct() {
        $this->allowedTypes = explode(',', getenv('ALLOWED_IMAGE_TYPES'));
        $this->maxWidth = (int)getenv('IMAGE_MAX_WIDTH');
        $this->maxHeight = (int)getenv('IMAGE_MAX_HEIGHT');
        $this->quality = (int)getenv('IMAGE_QUALITY');
    }

    public function optimizeImage($inputPath, $outputPath) {
        $imageInfo = getimagesize($inputPath);
        if (!$imageInfo) {
            throw new InvalidArgumentException('Invalid image file');
        }

        list($width, $height, $type) = $imageInfo;

        // Calcular nuevas dimensiones si excede límites
        if ($width > $this->maxWidth || $height > $this->maxHeight) {
            $ratio = min($this->maxWidth / $width, $this->maxHeight / $height);
            $newWidth = (int)($width * $ratio);
            $newHeight = (int)($height * $ratio);
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        // Crear imagen fuente
        $source = $this->createImageFromType($inputPath, $type);

        // Crear imagen destino
        $destination = imagecreatetruecolor($newWidth, $newHeight);

        // Preservar transparencia para PNG y GIF
        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
        }

        // Redimensionar
        imagecopyresampled(
            $destination, $source,
            0, 0, 0, 0,
            $newWidth, $newHeight, $width, $height
        );

        // Guardar imagen optimizada
        $this->saveOptimizedImage($destination, $outputPath, $type);

        // Limpiar memoria
        imagedestroy($source);
        imagedestroy($destination);

        return [
            'original_size' => filesize($inputPath),
            'optimized_size' => filesize($outputPath),
            'compression_ratio' => round((1 - filesize($outputPath) / filesize($inputPath)) * 100, 2),
            'dimensions' => "$newWidth x $newHeight"
        ];
    }

    private function createImageFromType($path, $type) {
        switch ($type) {
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($path);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($path);
            case IMAGETYPE_GIF:
                return imagecreatefromgif($path);
            case IMAGETYPE_WEBP:
                return imagecreatefromwebp($path);
            default:
                throw new InvalidArgumentException('Unsupported image type');
        }
    }

    private function saveOptimizedImage($image, $path, $type) {
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($image, $path, $this->quality);
                break;
            case IMAGETYPE_PNG:
                // PNG: 0-9 (compresión), convertir quality 0-100 a 0-9
                $compression = 9 - round(($this->quality / 100) * 9);
                imagepng($image, $path, $compression);
                break;
            case IMAGETYPE_GIF:
                imagegif($image, $path);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($image, $path, $this->quality);
                break;
        }
    }
}
```

---

<a id="configuracion-de-seguridad"></a>
<a id="-configuracion-de-seguridad"></a>
## 🔒 Configuración de Seguridad

<a id="security-headers-avanzados"></a>
<a id="-security-headers-avanzados"></a>
### Security Headers avanzados

```php
// src/Middleware/SecurityHeadersMiddleware.php
class SecurityHeadersMiddleware {
    public function handle($request, $next) {
        $response = $next($request);

        // HSTS
        $response->header('Strict-Transport-Security',
            'max-age=31536000; includeSubDomains; preload');

        // CSP
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval'; " .
               "style-src 'self' 'unsafe-inline'; " .
               "img-src 'self' data: https:; " .
               "font-src 'self'; " .
               "connect-src 'self'; " .
               "frame-ancestors 'none'; " .
               "base-uri 'self'; " .
               "form-action 'self'";

        $response->header('Content-Security-Policy', $csp);

        // Otros headers
        $response->header('X-Frame-Options', 'SAMEORIGIN');
        $response->header('X-Content-Type-Options', 'nosniff');
        $response->header('X-XSS-Protection', '1; mode=block');
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        return $response;
    }
}
```

<a id="input-validation-sanitization"></a>
<a id="-input-validation-sanitization"></a>
### Input Validation & Sanitization

```php
// src/Utils/SecurityValidator.php
class SecurityValidator {

    public static function sanitizeInput($input, $type = 'string') {
        if (is_array($input)) {
            return array_map(function($item) use ($type) {
                return self::sanitizeInput($item, $type);
            }, $input);
        }

        switch ($type) {
            case 'email':
                return filter_var($input, FILTER_SANITIZE_EMAIL);
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'url':
                return filter_var($input, FILTER_SANITIZE_URL);
            case 'html':
                return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            default:
                return trim(strip_tags($input));
        }
    }

    public static function validateCSRF($token) {
        if (!isset($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $token)) {
            throw new SecurityException('Invalid CSRF token');
        }

        // Regenerar token después de uso
        self::generateCSRFToken();
        return true;
    }

    public static function generateCSRFToken() {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    public static function validatePassword($password) {
        $minLength = (int)getenv('PASSWORD_MIN_LENGTH') ?: 8;
        $requireSpecial = getenv('PASSWORD_REQUIRE_SPECIAL') === 'true';
        $requireNumbers = getenv('PASSWORD_REQUIRE_NUMBERS') === 'true';
        $requireUppercase = getenv('PASSWORD_REQUIRE_UPPERCASE') === 'true';

        $errors = [];

        if (strlen($password) < $minLength) {
            $errors[] = "Password must be at least $minLength characters long";
        }

        if ($requireSpecial && !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }

        if ($requireNumbers && !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }

        if ($requireUppercase && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }

        return empty($errors) ? true : $errors;
    }
}
```

---

<a id="logging-avanzado"></a>
<a id="-logging-avanzado"></a>
## 📊 Logging Avanzado

<a id="structured-logging"></a>
<a id="-structured-logging"></a>
### Structured Logging

```php
// src/Utils/StructuredLogger.php
class StructuredLogger {
    private static $handlers = [];

    public static function addHandler($name, $handler) {
        self::$handlers[$name] = $handler;
    }

    public static function log($level, $message, $context = [], $channel = 'app') {
        $logEntry = [
            'timestamp' => date('c'),
            'level' => strtoupper($level),
            'message' => $message,
            'context' => $context,
            'channel' => $channel,
            'server' => $_SERVER['SERVER_NAME'] ?? 'cli',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'request_id' => $_SERVER['HTTP_X_REQUEST_ID'] ?? uniqid(),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true)
        ];

        foreach (self::$handlers as $handler) {
            $handler->handle($logEntry);
        }
    }

    public static function error($message, $context = []) {
        self::log('error', $message, $context);
    }

    public static function warning($message, $context = []) {
        self::log('warning', $message, $context);
    }

    public static function info($message, $context = []) {
        self::log('info', $message, $context);
    }

    public static function debug($message, $context = []) {
        if (getenv('APP_DEBUG') === 'true') {
            self::log('debug', $message, $context);
        }
    }
}

// Handler para archivos JSON
class JSONFileHandler {
    private $filePath;

    public function __construct($filePath) {
        $this->filePath = $filePath;
    }

    public function handle($logEntry) {
        $json = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . PHP_EOL;
        file_put_contents($this->filePath, $json, FILE_APPEND | LOCK_EX);
    }
}

// Handler para syslog
class SyslogHandler {
    public function handle($logEntry) {
        $priority = $this->getLevelPriority($logEntry['level']);
        $message = json_encode($logEntry, JSON_UNESCAPED_UNICODE);
        syslog($priority, $message);
    }

    private function getLevelPriority($level) {
        switch (strtolower($level)) {
            case 'emergency': return LOG_EMERG;
            case 'alert': return LOG_ALERT;
            case 'critical': return LOG_CRIT;
            case 'error': return LOG_ERR;
            case 'warning': return LOG_WARNING;
            case 'notice': return LOG_NOTICE;
            case 'info': return LOG_INFO;
            case 'debug': return LOG_DEBUG;
            default: return LOG_INFO;
        }
    }
}
```

<a id="log-rotation-management"></a>
<a id="-log-rotation-management"></a>
### Log Rotation & Management

```bash
<a id="binbash"></a>
<a id="-binbash"></a>
#!/bin/bash
<a id="etclogrotatedsnackshop"></a>
<a id="-etclogrotatedsnackshop"></a>
# /etc/logrotate.d/snackshop

/var/log/snackshop/*.log {
    daily                    # Rotar diariamente
    missingok               # OK si archivo no existe
    rotate 30               # Mantener 30 días
    compress                # Comprimir archivos rotados
    delaycompress           # Comprimir en siguiente rotación
    notifempty              # No rotar si está vacío
    sharedscripts           # Ejecutar script una vez para todos los logs
    postrotate
        # Recargar PHP-FPM para reabrir logs
        systemctl reload php8.1-fpm
        # Opcional: enviar señal USR1 a aplicación custom
        # kill -USR1 $(cat /var/run/snackshop.pid) 2>/dev/null || true
    endscript
}
```

---

<a id="configuracion-por-entorno"></a>
<a id="-configuracion-por-entorno"></a>
## 🧬 Configuración por Entorno

<a id="development-environment"></a>
<a id="-development-environment"></a>
### Development Environment

```bash
<a id="envdevelopment"></a>
<a id="-envdevelopment"></a>
# .env.development
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

<a id="database"></a>
<a id="-database"></a>
# Database
SNACKSHOP_DB_HOST=127.0.0.1
SNACKSHOP_DB_NAME=snackshop_dev

<a id="session"></a>
<a id="-session"></a>
# Session
SESSION_SECURE=false
SESSION_DRIVER=file

<a id="logging"></a>
<a id="-logging"></a>
# Logging
LOG_LEVEL=debug
LOG_FILE=/tmp/snackshop-dev.log

<a id="cache"></a>
<a id="-cache"></a>
# Cache
CACHE_DRIVER=file

<a id="performance-relaxed"></a>
<a id="-performance-relaxed"></a>
# Performance (relaxed)
MAX_EXECUTION_TIME=0
MEMORY_LIMIT=512M

<a id="email-testing"></a>
<a id="-email-testing"></a>
# Email (testing)
MAIL_DRIVER=log
```

<a id="staging-environment"></a>
<a id="-staging-environment"></a>
### Staging Environment

```bash
<a id="envstaging"></a>
<a id="-envstaging"></a>
# .env.staging
APP_ENV=staging
APP_DEBUG=true
APP_URL=https://staging.tu-dominio.com

<a id="database"></a>
<a id="-database"></a>
# Database
SNACKSHOP_DB_HOST=staging-mysql.interno
SNACKSHOP_DB_NAME=snackshop_staging

<a id="session"></a>
<a id="-session"></a>
# Session
SESSION_SECURE=true
SESSION_DRIVER=database

<a id="logging"></a>
<a id="-logging"></a>
# Logging
LOG_LEVEL=info
LOG_FILE=/var/log/snackshop/staging.log

<a id="cache"></a>
<a id="-cache"></a>
# Cache
CACHE_DRIVER=redis
REDIS_HOST=staging-redis.interno

<a id="performance"></a>
<a id="-performance"></a>
# Performance
MAX_EXECUTION_TIME=120
MEMORY_LIMIT=256M

<a id="email-testing-with-real-smtp"></a>
<a id="-email-testing-with-real-smtp"></a>
# Email (testing with real SMTP)
MAIL_DRIVER=smtp
MAIL_HOST=smtp-staging.interno
```

<a id="production-environment"></a>
<a id="-production-environment"></a>
### Production Environment

```bash
<a id="envproduction"></a>
<a id="-envproduction"></a>
# .env.production
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

<a id="database-con-replica-para-lectura"></a>
<a id="-database-con-replica-para-lectura"></a>
# Database (con replica para lectura)
SNACKSHOP_DB_HOST=prod-mysql-master.interno
DB_READ_HOST=prod-mysql-read.interno
SNACKSHOP_DB_NAME=snackshop

<a id="session"></a>
<a id="-session"></a>
# Session
SESSION_SECURE=true
SESSION_DRIVER=redis
SESSION_LIFETIME=7200

<a id="logging"></a>
<a id="-logging"></a>
# Logging
LOG_LEVEL=warning
LOG_FILE=/var/log/snackshop/production.log
ACCESS_LOG_ENABLED=true

<a id="cache"></a>
<a id="-cache"></a>
# Cache
CACHE_DRIVER=redis
REDIS_HOST=prod-redis.interno
REDIS_PASSWORD=secure_redis_password

<a id="performance"></a>
<a id="-performance"></a>
# Performance
MAX_EXECUTION_TIME=60
MEMORY_LIMIT=256M

<a id="email"></a>
<a id="-email"></a>
# Email
MAIL_DRIVER=smtp
MAIL_HOST=smtp.tu-dominio.com

<a id="security"></a>
<a id="-security"></a>
# Security
CSRF_ENABLED=true
BCRYPT_ROUNDS=12

<a id="backup"></a>
<a id="-backup"></a>
# Backup
BACKUP_ENABLED=true
BACKUP_SCHEDULE="0 2 * * *"
```

---

<a id="monitoring-y-alertas"></a>
<a id="-monitoring-y-alertas"></a>
## 🔔 Monitoring y Alertas

<a id="health-check-endpoint"></a>
<a id="-health-check-endpoint"></a>
### Health Check Endpoint

```php
// src/Controllers/HealthController.php
class HealthController {
    public function check() {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'disk_space' => $this->checkDiskSpace(),
            'memory' => $this->checkMemory(),
            'services' => $this->checkServices()
        ];

        $overallStatus = $this->determineOverallStatus($checks);

        return json_response([
            'status' => $overallStatus,
            'timestamp' => date('c'),
            'checks' => $checks,
            'version' => getenv('APP_VERSION'),
            'environment' => getenv('APP_ENV')
        ], $overallStatus === 'healthy' ? 200 : 503);
    }

    private function checkDatabase() {
        try {
            $db = new PDO(
                "mysql:host=" . getenv('SNACKSHOP_DB_HOST') .
                ";dbname=" . getenv('SNACKSHOP_DB_NAME'),
                getenv('SNACKSHOP_DB_USER'),
                getenv('SNACKSHOP_DB_PASS')
            );

            $stmt = $db->query('SELECT 1');
            $result = $stmt->fetch();

            return [
                'status' => 'healthy',
                'response_time' => '< 1ms',
                'message' => 'Database connection successful'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }
    }

    private function checkCache() {
        if (getenv('CACHE_DRIVER') !== 'redis') {
            return ['status' => 'healthy', 'message' => 'File cache is working'];
        }

        try {
            $redis = new Redis();
            $redis->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));
            $redis->ping();

            return [
                'status' => 'healthy',
                'message' => 'Redis connection successful'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Redis connection failed: ' . $e->getMessage()
            ];
        }
    }

    private function checkDiskSpace() {
        $diskFree = disk_free_space('/');
        $diskTotal = disk_total_space('/');
        $usagePercent = (1 - $diskFree / $diskTotal) * 100;

        $status = $usagePercent > 90 ? 'critical' :
                 ($usagePercent > 80 ? 'warning' : 'healthy');

        return [
            'status' => $status,
            'usage_percent' => round($usagePercent, 2),
            'free_space' => $this->formatBytes($diskFree),
            'total_space' => $this->formatBytes($diskTotal)
        ];
    }

    private function formatBytes($size, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, $precision) . ' ' . $units[$i];
    }
}
```

<a id="alerting-system"></a>
<a id="-alerting-system"></a>
### Alerting System

```php
// src/Services/AlertingService.php
class AlertingService {
    private $channels = [];

    public function addChannel($name, $channel) {
        $this->channels[$name] = $channel;
    }

    public function alert($level, $message, $context = []) {
        $alert = [
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'timestamp' => date('c'),
            'server' => gethostname(),
            'environment' => getenv('APP_ENV')
        ];

        foreach ($this->channels as $channel) {
            $channel->send($alert);
        }
    }

    public function critical($message, $context = []) {
        $this->alert('critical', $message, $context);
    }

    public function warning($message, $context = []) {
        $this->alert('warning', $message, $context);
    }
}

// Telegram Alert Channel
class TelegramAlertChannel {
    private $botToken;
    private $chatId;

    public function __construct($botToken, $chatId) {
        $this->botToken = $botToken;
        $this->chatId = $chatId;
    }

    public function send($alert) {
        $emoji = $this->getLevelEmoji($alert['level']);
        $text = "$emoji *SnackShop Alert*\n\n" .
                "*Level:* {$alert['level']}\n" .
                "*Message:* {$alert['message']}\n" .
                "*Server:* {$alert['server']}\n" .
                "*Environment:* {$alert['environment']}\n" .
                "*Time:* {$alert['timestamp']}";

        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
        $data = [
            'chat_id' => $this->chatId,
            'text' => $text,
            'parse_mode' => 'Markdown'
        ];

        $this->sendRequest($url, $data);
    }

    private function getLevelEmoji($level) {
        switch ($level) {
            case 'critical': return '🚨';
            case 'error': return '❌';
            case 'warning': return '⚠️';
            case 'info': return 'ℹ️';
            default: return '📝';
        }
    }

    private function sendRequest($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_exec($ch);
        curl_close($ch);
    }
}
```

---

<a id="configuracion-de-logs-centralizados"></a>
<a id="-configuracion-de-logs-centralizados"></a>
## 📝 Configuración de Logs Centralizados

<a id="elk-stack-integration-opcional"></a>
<a id="-elk-stack-integration-opcional"></a>
### ELK Stack Integration (Opcional)

```bash
<a id="variables-para-logging-centralizado"></a>
<a id="-variables-para-logging-centralizado"></a>
# Variables para logging centralizado
ELK_ENABLED=true
ELASTICSEARCH_HOST=elk.interno:9200
LOGSTASH_HOST=elk.interno:5044
KIBANA_URL=https://kibana.tu-dominio.com
```

```php
// src/Utils/ElasticsearchLogger.php
class ElasticsearchLogger {
    private $client;
    private $index;

    public function __construct($host, $index = 'snackshop-logs') {
        $this->client = new \GuzzleHttp\Client(['base_uri' => $host]);
        $this->index = $index;
    }

    public function log($level, $message, $context = []) {
        $document = [
            '@timestamp' => date('c'),
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'application' => 'snackshop',
            'environment' => getenv('APP_ENV'),
            'server' => gethostname(),
            'tags' => ['php', 'snackshop']
        ];

        try {
            $this->client->post("/{$this->index}/_doc", [
                'json' => $document,
                'timeout' => 5
            ]);
        } catch (Exception $e) {
            // Fallback a archivo local si Elasticsearch falla
            error_log("Elasticsearch logging failed: " . $e->getMessage());
        }
    }
}
```

---

<a id="documentos-relacionados"></a>
<a id="-documentos-relacionados"></a>
## 🔗 Documentos Relacionados

- **[📖 Índice General](docs/INDEX.md)** — Navegación completa del manual
- **[🚀 Deployment](DEPLOYMENT.md)** — Ver guía de despliegue
- **[🏗️ Architecture](ARCHITECTURE.md)** — Comprende la arquitectura del sistema
- **[🗄️ Database](DATABASE.md)** — Configuración de base de datos
- **[🔧 Development](DEVELOPMENT.md)** — Setup para desarrolladores

---

<a id="soporte"></a>
<a id="-soporte"></a>
## 📞 Soporte

**¿Necesitas ayuda con la configuración?**
- **Performance Issues:** Revisa la sección de optimización y monitoring
- **Security Concerns:** Consulta las configuraciones de seguridad avanzada
- **Environment Setup:** Usa las configuraciones específicas por entorno

---

**[📖 Índice](docs/INDEX.md)** | **[🚀 Ver Deployment](DEPLOYMENT.md)** | **[🏗️ Ver Arquitectura](ARCHITECTURE.md)** | **[🔧 Ver Development](DEVELOPMENT.md)**
