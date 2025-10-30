<a id="snackshop-guia-de-despliegue"></a>
<a id="-snackshop-guia-de-despliegue"></a>
# 🚀 SnackShop - Guía de Despliegue
<!-- TOC -->
<a id="contenido"></a>
<a id="-contenido"></a>
## Contenido

- [🧭 Navegación](#-navegacion)
- [📋 Índice](#-indice)
- [🎯 Resumen de Opciones de Despliegue](#-resumen-de-opciones-de-despliegue)
  - [Por Entorno de Uso](#por-entorno-de-uso)
  - [Recomendaciones por Escala](#recomendaciones-por-escala)
- [💻 Desarrollo Local](#-desarrollo-local)
  - [Opción 1: Servidor Embebido PHP (Recomendado)](#opcion-1-servidor-embebido-php-recomendado)
  - [Opción 2: XAMPP (Windows)](#opcion-2-xampp-windows)
  - [Opción 3: Laravel Valet (Avanzado)](#opcion-3-laravel-valet-avanzado)
- [🐳 Despliegue con Docker](#-despliegue-con-docker)
  - [Docker Compose Completo](#docker-compose-completo)
  - [Comandos Docker](#comandos-docker)
- [🌐 Servidor Compartido](#-servidor-compartido)
  - [Preparación del Proyecto](#preparacion-del-proyecto)
  - [Estructura en Hosting Compartido](#estructura-en-hosting-compartido)
  - [Configuración .htaccess](#configuracion-htaccess)
  - [Configuración cPanel](#configuracion-cpanel)
- [🖥️ VPS / Servidor Dedicado](#-vps-servidor-dedicado)
  - [Ubuntu 22.04 LTS Setup](#ubuntu-2204-lts-setup)
- [☁️ Cloud Platforms](#-cloud-platforms)
  - [DigitalOcean Droplet](#digitalocean-droplet)
  - [AWS EC2](#aws-ec2)
  - [Azure App Service](#azure-app-service)
- [🔧 Variables de Entorno](#-variables-de-entorno)
  - [Archivo `.env` Completo](#archivo-env-completo)
  - [Variables por Entorno](#variables-por-entorno)
  - [Gestión de Variables](#gestion-de-variables)
- [🔒 SSL y Seguridad](#-ssl-y-seguridad)
  - [Certificados SSL](#certificados-ssl)
  - [Configuración de Seguridad](#configuracion-de-seguridad)
- [📊 Monitoreo y Logs](#-monitoreo-y-logs)
  - [Configuración de Logs](#configuracion-de-logs)
  - [Monitoreo con Scripts](#monitoreo-con-scripts)
  - [Monitoreo Avanzado](#monitoreo-avanzado)
- [💾 Backup Automatizado](#-backup-automatizado)
  - [Script de Backup Completo](#script-de-backup-completo)
  - [Backup Remoto (S3/Cloud)](#backup-remoto-s3cloud)
  - [Programación de Backups](#programacion-de-backups)
- [🚨 Troubleshooting](#-troubleshooting)
  - [Problemas Comunes](#problemas-comunes)
  - [Scripts de Diagnóstico](#scripts-de-diagnostico)
- [✅ Checklist de Producción](#-checklist-de-produccion)
  - [Pre-Despliegue](#pre-despliegue)
  - [Post-Despliegue](#post-despliegue)
  - [Mantenimiento Continuo](#mantenimiento-continuo)
- [🔗 Documentos Relacionados](#-documentos-relacionados)
- [📞 Soporte](#-soporte)
<!-- /TOC -->

**🏠 Ubicación:** `DEPLOYMENT.md`
**📅 Última actualización:** 28 de octubre, 2025
**🎯 Propósito:** Guía completa para desplegar SnackShop en desarrollo, staging y producción

---

<a id="navegacion"></a>
<a id="-navegacion"></a>
## 🧭 Navegación

**[📖 Índice General](docs/INDEX.md)** | **[🏠 README](README.md)** | **[🏗️ Arquitectura](ARCHITECTURE.md)** | **[🗄️ Database](DATABASE.md)**

---

<a id="indice"></a>
<a id="-indice"></a>
## 📋 Índice

- [Resumen de Opciones de Despliegue](#resumen-de-opciones-de-despliegue)
- [Desarrollo Local](#desarrollo-local)
- [Despliegue con Docker](#despliegue-con-docker)
- [Servidor Compartido](#servidor-compartido)
-- [VPS / Servidor Dedicado](#vps-servidor-dedicado)
- [Cloud Platforms](#cloud-platforms)
- [Variables de Entorno](#variables-de-entorno)
- [SSL y Seguridad](#ssl-y-seguridad)
- [Monitoreo y Logs](#monitoreo-y-logs)
- [Backup Automatizado](#backup-automatizado)
- [Troubleshooting](#troubleshooting)
- [Checklist de Producción](#checklist-de-producción)

---

<a id="resumen-de-opciones-de-despliegue"></a>
<a id="-resumen-de-opciones-de-despliegue"></a>
## 🎯 Resumen de Opciones de Despliegue

<a id="por-entorno-de-uso"></a>
<a id="-por-entorno-de-uso"></a>
### Por Entorno de Uso

| Entorno | Tecnología | Complejidad | Costo | Recomendado Para |
|---------|------------|-------------|-------|------------------|
| **Desarrollo** | PHP embebido + SQLite | ⭐ | Gratis | Desarrollo local |
| **Staging** | Docker Compose | ⭐⭐ | Gratis | Testing pre-producción |
| **Shared Hosting** | cPanel/FileManager | ⭐⭐ | $5-15/mes | Proyectos pequeños |
| **VPS** | Nginx + MySQL | ⭐⭐⭐ | $10-50/mes | Proyectos medianos |
| **Cloud** | DigitalOcean/AWS | ⭐⭐⭐⭐ | $20-100/mes | Escalabilidad |

<a id="recomendaciones-por-escala"></a>
<a id="-recomendaciones-por-escala"></a>
### Recomendaciones por Escala

- **1-10 usuarios:** Shared hosting o VPS básico
- **10-100 usuarios:** VPS con optimización
- **100+ usuarios:** Cloud con load balancing

---

<a id="desarrollo-local"></a>
<a id="-desarrollo-local"></a>
## 💻 Desarrollo Local

<a id="opcion-1-servidor-embebido-php-recomendado"></a>
<a id="-opcion-1-servidor-embebido-php-recomendado"></a>
### Opción 1: Servidor Embebido PHP (Recomendado)

```powershell
<a id="1-verificar-requisitos"></a>
<a id="-1-verificar-requisitos"></a>
# 1. Verificar requisitos
php --version  # PHP 7.4+
composer --version  # Composer

<a id="2-clonar-e-instalar"></a>
<a id="-2-clonar-e-instalar"></a>
# 2. Clonar e instalar
git clone https://github.com/Equinoxe-Grammer/SnackShack.git
cd SnackShack\SnackShop\www\Snackshop
composer install

<a id="3-verificar-base-de-datos"></a>
<a id="-3-verificar-base-de-datos"></a>
# 3. Verificar base de datos
<a id="asegurar-que-datasnackshopdb-existe"></a>
<a id="-asegurar-que-datasnackshopdb-existe"></a>
# Asegurar que data\snackshop.db existe

<a id="4-iniciar-servidor"></a>
<a id="-4-iniciar-servidor"></a>
# 4. Iniciar servidor
php -S localhost:8000 -t public

<a id="5-abrir-navegador"></a>
<a id="-5-abrir-navegador"></a>
# 5. Abrir navegador
start http://localhost:8000
```

<a id="opcion-2-xampp-windows"></a>
<a id="-opcion-2-xampp-windows"></a>
### Opción 2: XAMPP (Windows)

```powershell
<a id="1-instalar-xampp"></a>
<a id="-1-instalar-xampp"></a>
# 1. Instalar XAMPP
<a id="descargar-desde-httpswwwapachefriendsorg"></a>
<a id="-descargar-desde-httpswwwapachefriendsorg"></a>
# Descargar desde https://www.apachefriends.org/

<a id="2-copiar-proyecto"></a>
<a id="-2-copiar-proyecto"></a>
# 2. Copiar proyecto
Copy-Item -Recurse "SnackShop" "C:\xampp\htdocs\"

<a id="3-configurar-virtual-host-opcional"></a>
<a id="-3-configurar-virtual-host-opcional"></a>
# 3. Configurar virtual host (opcional)
<a id="editar-cxamppapacheconfextrahttpd-vhostsconf"></a>
<a id="-editar-cxamppapacheconfextrahttpd-vhostsconf"></a>
# Editar C:\xampp\apache\conf\extra\httpd-vhosts.conf
```

**httpd-vhosts.conf:**
```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/SnackShop/SnackShop/www/Snackshop/public"
    ServerName snackshop.local
    <Directory "C:/xampp/htdocs/SnackShop/SnackShop/www/Snackshop/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**hosts file (C:\Windows\System32\drivers\etc\hosts):**
```
127.0.0.1    snackshop.local
```

<a id="opcion-3-laravel-valet-avanzado"></a>
<a id="-opcion-3-laravel-valet-avanzado"></a>
### Opción 3: Laravel Valet (Avanzado)

```powershell
<a id="instalar-valet-para-windows"></a>
<a id="-instalar-valet-para-windows"></a>
# Instalar Valet para Windows
composer global require cretueusebiu/valet-windows
valet install

<a id="en-directorio-del-proyecto"></a>
<a id="-en-directorio-del-proyecto"></a>
# En directorio del proyecto
valet link snackshop
valet secure snackshop  # SSL opcional

<a id="acceder-en-httpssnackshoptest"></a>
<a id="-acceder-en-httpssnackshoptest"></a>
# Acceder en: https://snackshop.test
```

---

<a id="despliegue-con-docker"></a>
<a id="-despliegue-con-docker"></a>
## 🐳 Despliegue con Docker

<a id="docker-compose-completo"></a>
<a id="-docker-compose-completo"></a>
### Docker Compose Completo

<a id="docker-composeyml"></a>
<a id="-docker-composeyml"></a>
#### `docker-compose.yml`

```yaml
version: '3.8'

services:
  web:
    build:
      context: .
      dockerfile: Dockerfile
    ports:
      - "8000:80"
    volumes:
      - .:/var/www/html
      - ./data:/var/www/html/data
    environment:
      - SNACKSHOP_DB_HOST=db
      - SNACKSHOP_DB_NAME=snackshop
      - SNACKSHOP_DB_USER=snackshop
      - SNACKSHOP_DB_PASS=snackshop123
    depends_on:
      - db
    networks:
      - snackshop

  db:
    image: mysql:8.0
    environment:
      - MYSQL_ROOT_PASSWORD=rootpass123
      - MYSQL_DATABASE=snackshop
      - MYSQL_USER=snackshop
      - MYSQL_PASSWORD=snackshop123
    volumes:
      - db_data:/var/lib/mysql
      - ./sql/init.sql:/docker-entrypoint-initdb.d/init.sql
    ports:
      - "3306:3306"
    networks:
      - snackshop

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx/nginx.conf:/etc/nginx/nginx.conf
      - ./nginx/ssl:/etc/nginx/ssl
      - .:/var/www/html
    depends_on:
      - web
    networks:
      - snackshop

volumes:
  db_data:

networks:
  snackshop:
    driver: bridge
```

<a id="dockerfile"></a>
<a id="-dockerfile"></a>
#### `Dockerfile`

```dockerfile
FROM php:8.1-apache

<a id="instalar-extensiones-php"></a>
<a id="-instalar-extensiones-php"></a>
# Instalar extensiones PHP
RUN apt-get update && apt-get install -y \\
    libpng-dev \\
    libjpeg-dev \\
    libfreetype6-dev \\
    libonig-dev \\
    libxml2-dev \\
    zip \\
    unzip \\
    && docker-php-ext-configure gd --with-freetype --with-jpeg \\
    && docker-php-ext-install -j$(nproc) gd \\
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath

<a id="habilitar-mod_rewrite"></a>
<a id="-habilitar-mod_rewrite"></a>
# Habilitar mod_rewrite
RUN a2enmod rewrite

<a id="instalar-composer"></a>
<a id="-instalar-composer"></a>
# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

<a id="configurar-directorio-de-trabajo"></a>
<a id="-configurar-directorio-de-trabajo"></a>
# Configurar directorio de trabajo
WORKDIR /var/www/html

<a id="copiar-archivos-del-proyecto"></a>
<a id="-copiar-archivos-del-proyecto"></a>
# Copiar archivos del proyecto
COPY . .

<a id="instalar-dependencias"></a>
<a id="-instalar-dependencias"></a>
# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader

<a id="configurar-permisos"></a>
<a id="-configurar-permisos"></a>
# Configurar permisos
RUN chown -R www-data:www-data /var/www/html \\
    && chmod -R 755 /var/www/html \\
    && chmod -R 775 /var/www/html/data

<a id="configurar-apache-documentroot"></a>
<a id="-configurar-apache-documentroot"></a>
# Configurar Apache DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

<a id="puerto-expuesto"></a>
<a id="-puerto-expuesto"></a>
# Puerto expuesto
EXPOSE 80

<a id="comando-de-inicio"></a>
<a id="-comando-de-inicio"></a>
# Comando de inicio
CMD ["apache2-foreground"]
```

<a id="configuracion-nginx-nginxnginxconf"></a>
<a id="-configuracion-nginx-nginxnginxconf"></a>
#### Configuración Nginx (`nginx/nginx.conf`)

```nginx
events {
    worker_connections 1024;
}

http {
    include       /etc/nginx/mime.types;
    default_type  application/octet-stream;

    sendfile        on;
    keepalive_timeout  65;

    upstream php-backend {
        server web:80;
    }

    server {
        listen 80;
        server_name localhost;

        # Redirect HTTP to HTTPS
        return 301 https://$server_name$request_uri;
    }

    server {
        listen 443 ssl http2;
        server_name localhost;

        # SSL Configuration
        ssl_certificate /etc/nginx/ssl/cert.pem;
        ssl_certificate_key /etc/nginx/ssl/key.pem;
        ssl_protocols TLSv1.2 TLSv1.3;
        ssl_ciphers HIGH:!aNULL:!MD5;

        # Security headers
        add_header X-Frame-Options "SAMEORIGIN" always;
        add_header X-Content-Type-Options "nosniff" always;
        add_header X-XSS-Protection "1; mode=block" always;
        add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

        # Rate limiting
        limit_req_zone $binary_remote_addr zone=api:10m rate=100r/m;
        limit_req_zone $binary_remote_addr zone=web:10m rate=60r/m;

        location / {
            limit_req zone=web burst=20 nodelay;
            proxy_pass http://php-backend;
            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
        }

        location /api/ {
            limit_req zone=api burst=50 nodelay;
            proxy_pass http://php-backend;
            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
        }

        # Static files
        location ~* \\.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
            expires 1y;
            add_header Cache-Control "public, immutable";
            proxy_pass http://php-backend;
        }
    }
}
```

<a id="comandos-docker"></a>
<a id="-comandos-docker"></a>
### Comandos Docker

```powershell
<a id="construccion-y-arranque"></a>
<a id="-construccion-y-arranque"></a>
# Construcción y arranque
docker-compose up -d --build

<a id="ver-logs"></a>
<a id="-ver-logs"></a>
# Ver logs
docker-compose logs -f web
docker-compose logs -f db

<a id="acceso-a-contenedor"></a>
<a id="-acceso-a-contenedor"></a>
# Acceso a contenedor
docker-compose exec web bash
docker-compose exec db mysql -u snackshop -p snackshop

<a id="backup-de-bd"></a>
<a id="-backup-de-bd"></a>
# Backup de BD
docker-compose exec db mysqldump -u snackshop -p snackshop > backup.sql

<a id="parar-servicios"></a>
<a id="-parar-servicios"></a>
# Parar servicios
docker-compose down

<a id="limpiar-volumenes-elimina-datos"></a>
<a id="-limpiar-volumenes-elimina-datos"></a>
# Limpiar volúmenes (⚠️ Elimina datos)
docker-compose down -v
```

---

<a id="servidor-compartido"></a>
<a id="-servidor-compartido"></a>
## 🌐 Servidor Compartido

<a id="preparacion-del-proyecto"></a>
<a id="-preparacion-del-proyecto"></a>
### Preparación del Proyecto

```powershell
<a id="1-preparar-para-produccion"></a>
<a id="-1-preparar-para-produccion"></a>
# 1. Preparar para producción
composer install --no-dev --optimize-autoloader

<a id="2-crear-archivo-env"></a>
<a id="-2-crear-archivo-env"></a>
# 2. Crear archivo .env
copy .env.example .env
<a id="editar-variables-de-produccion"></a>
<a id="-editar-variables-de-produccion"></a>
# Editar variables de producción

<a id="3-crear-zip-para-upload"></a>
<a id="-3-crear-zip-para-upload"></a>
# 3. Crear ZIP para upload
Compress-Archive -Path * -DestinationPath snackshop-prod.zip
```

<a id="estructura-en-hosting-compartido"></a>
<a id="-estructura-en-hosting-compartido"></a>
### Estructura en Hosting Compartido

```
public_html/
├── snackshop/           # Carpeta del proyecto
│   ├── src/
│   ├── vendor/
│   ├── data/
│   └── .env
└── index.php           # Archivo de entrada
```

<a id="public_htmlindexphp-punto-de-entrada"></a>
<a id="-public_htmlindexphp-punto-de-entrada"></a>
#### `public_html/index.php` (Punto de entrada)

```php
<?php
// Redirigir al proyecto SnackShop
require_once __DIR__ . '/snackshop/public/index.php';
```

<a id="configuracion-htaccess"></a>
<a id="-configuracion-htaccess"></a>
### Configuración .htaccess

<a id="public_htmlhtaccess"></a>
<a id="-public_htmlhtaccess"></a>
#### `public_html/.htaccess`

```apache
<a id="forzar-https"></a>
<a id="-forzar-https"></a>
# Forzar HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

<a id="redirigir-al-proyecto"></a>
<a id="-redirigir-al-proyecto"></a>
# Redirigir al proyecto
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ snackshop/public/$1 [L]
```

<a id="public_htmlsnackshoppublichtaccess"></a>
<a id="-public_htmlsnackshoppublichtaccess"></a>
#### `public_html/snackshop/public/.htaccess`

```apache
RewriteEngine On

<a id="handle-angularvue-router-opcional"></a>
<a id="-handle-angularvue-router-opcional"></a>
# Handle Angular/Vue Router (opcional)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]

<a id="security"></a>
<a id="-security"></a>
# Security
<Files .env>
    Order allow,deny
    Deny from all
</Files>

<a id="cache-static-files"></a>
<a id="-cache-static-files"></a>
# Cache static files
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
</IfModule>
```

<a id="configuracion-cpanel"></a>
<a id="-configuracion-cpanel"></a>
### Configuración cPanel

1. **File Manager:** Subir proyecto a `public_html/snackshop/`
2. **MySQL Databases:** Crear base de datos y usuario
3. **Cron Jobs:** Configurar backup automático
4. **SSL/TLS:** Activar certificado gratuito

---

<a id="vps-servidor-dedicado"></a>
<a id="-vps-servidor-dedicado"></a>
## 🖥️ VPS / Servidor Dedicado

<a id="ubuntu-2204-lts-setup"></a>
<a id="-ubuntu-2204-lts-setup"></a>
### Ubuntu 22.04 LTS Setup

<a id="1-preparacion-del-servidor"></a>
<a id="-1-preparacion-del-servidor"></a>
#### 1. Preparación del Servidor

```bash
<a id="actualizar-sistema"></a>
<a id="-actualizar-sistema"></a>
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

<a id="instalar-dependencias-base"></a>
<a id="-instalar-dependencias-base"></a>
# Instalar dependencias base
sudo apt install -y curl wget git unzip software-properties-common

<a id="instalar-php-81"></a>
<a id="-instalar-php-81"></a>
# Instalar PHP 8.1
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.1 php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip php8.1-gd php8.1-sqlite3

<a id="instalar-nginx"></a>
<a id="-instalar-nginx"></a>
# Instalar Nginx
sudo apt install -y nginx

<a id="instalar-mysql"></a>
<a id="-instalar-mysql"></a>
# Instalar MySQL
sudo apt install -y mysql-server

<a id="instalar-composer"></a>
<a id="-instalar-composer"></a>
# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

<a id="2-configuracion-mysql"></a>
<a id="-2-configuracion-mysql"></a>
#### 2. Configuración MySQL

```bash
<a id="configuracion-segura"></a>
<a id="-configuracion-segura"></a>
# Configuración segura
sudo mysql_secure_installation

<a id="crear-base-de-datos-y-usuario"></a>
<a id="-crear-base-de-datos-y-usuario"></a>
# Crear base de datos y usuario
sudo mysql -u root -p
```

```sql
CREATE DATABASE snackshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'snackshop'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON snackshop.* TO 'snackshop'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

<a id="3-configuracion-nginx"></a>
<a id="-3-configuracion-nginx"></a>
#### 3. Configuración Nginx

**`/etc/nginx/sites-available/snackshop`**
```nginx
server {
    listen 80;
    server_name tu-dominio.com www.tu-dominio.com;
    root /var/www/snackshop/public;
    index index.php index.html;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Main location
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP handling
    location ~ \\.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;

        # Security
        fastcgi_hide_header X-Powered-By;
    }

    # Static files
    location ~* \\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Deny access to sensitive files
    location ~ /\\. {
        deny all;
    }

    location ~ \\.(env|log)$ {
        deny all;
    }
}
```

```bash
<a id="habilitar-sitio"></a>
<a id="-habilitar-sitio"></a>
# Habilitar sitio
sudo ln -s /etc/nginx/sites-available/snackshop /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

<a id="4-despliegue-del-proyecto"></a>
<a id="-4-despliegue-del-proyecto"></a>
#### 4. Despliegue del Proyecto

```bash
<a id="crear-directorio"></a>
<a id="-crear-directorio"></a>
# Crear directorio
sudo mkdir -p /var/www/snackshop
cd /var/www/snackshop

<a id="clonar-proyecto-o-subir-via-sftp"></a>
<a id="-clonar-proyecto-o-subir-via-sftp"></a>
# Clonar proyecto (o subir via SFTP)
sudo git clone https://github.com/Equinoxe-Grammer/SnackShack.git .

<a id="instalar-dependencias"></a>
<a id="-instalar-dependencias"></a>
# Instalar dependencias
sudo composer install --no-dev --optimize-autoloader

<a id="configurar-permisos"></a>
<a id="-configurar-permisos"></a>
# Configurar permisos
sudo chown -R www-data:www-data /var/www/snackshop
sudo chmod -R 755 /var/www/snackshop
sudo chmod -R 775 /var/www/snackshop/data

<a id="configurar-variables-de-entorno"></a>
<a id="-configurar-variables-de-entorno"></a>
# Configurar variables de entorno
sudo cp .env.example .env
sudo nano .env
```

<a id="5-ssl-con-lets-encrypt"></a>
<a id="-5-ssl-con-lets-encrypt"></a>
#### 5. SSL con Let's Encrypt

```bash
<a id="instalar-certbot"></a>
<a id="-instalar-certbot"></a>
# Instalar Certbot
sudo apt install -y certbot python3-certbot-nginx

<a id="obtener-certificado"></a>
<a id="-obtener-certificado"></a>
# Obtener certificado
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com

<a id="verificar-renovacion-automatica"></a>
<a id="-verificar-renovacion-automatica"></a>
# Verificar renovación automática
sudo crontab -e
<a id="anadir-0-12-usrbincertbot-renew-quiet"></a>
<a id="-anadir-0-12-usrbincertbot-renew-quiet"></a>
# Añadir: 0 12 * * * /usr/bin/certbot renew --quiet
```

---

<a id="cloud-platforms"></a>
<a id="-cloud-platforms"></a>
## ☁️ Cloud Platforms

<a id="digitalocean-droplet"></a>
<a id="-digitalocean-droplet"></a>
### DigitalOcean Droplet

<a id="1-crear-droplet"></a>
<a id="-1-crear-droplet"></a>
#### 1. Crear Droplet

- **Imagen:** Ubuntu 22.04 LTS
- **Plan:** $12/mes (2GB RAM, 1 vCPU)
- **Región:** Más cercana a usuarios
- **SSH Keys:** Añadir clave pública

<a id="2-script-de-configuracion-automatica"></a>
<a id="-2-script-de-configuracion-automatica"></a>
#### 2. Script de Configuración Automática

**`deploy.sh`**
```bash
<a id="binbash"></a>
<a id="-binbash"></a>
#!/bin/bash

<a id="variables"></a>
<a id="-variables"></a>
# Variables
DOMAIN="tu-dominio.com"
DB_PASSWORD="$(openssl rand -base64 32)"
APP_PATH="/var/www/snackshop"

echo "🚀 Iniciando despliegue de SnackShop..."

<a id="actualizar-sistema"></a>
<a id="-actualizar-sistema"></a>
# Actualizar sistema
apt update && apt upgrade -y

<a id="instalar-stack-lemp-php"></a>
<a id="-instalar-stack-lemp-php"></a>
# Instalar stack LEMP + PHP
apt install -y nginx mysql-server php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip php8.1-gd php8.1-sqlite3 git unzip

<a id="instalar-composer"></a>
<a id="-instalar-composer"></a>
# Instalar Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

<a id="configurar-mysql"></a>
<a id="-configurar-mysql"></a>
# Configurar MySQL
mysql -e "CREATE DATABASE snackshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER 'snackshop'@'localhost' IDENTIFIED BY '$DB_PASSWORD';"
mysql -e "GRANT ALL PRIVILEGES ON snackshop.* TO 'snackshop'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

<a id="descargar-proyecto"></a>
<a id="-descargar-proyecto"></a>
# Descargar proyecto
mkdir -p $APP_PATH
cd $APP_PATH
git clone https://github.com/Equinoxe-Grammer/SnackShack.git .

<a id="configurar-proyecto"></a>
<a id="-configurar-proyecto"></a>
# Configurar proyecto
composer install --no-dev --optimize-autoloader

<a id="crear-configuracion"></a>
<a id="-crear-configuracion"></a>
# Crear configuración
cp .env.example .env
sed -i "s/SNACKSHOP_DB_PASS=/SNACKSHOP_DB_PASS=$DB_PASSWORD/" .env
sed -i "s/SNACKSHOP_DB_HOST=127.0.0.1/SNACKSHOP_DB_HOST=localhost/" .env

<a id="permisos"></a>
<a id="-permisos"></a>
# Permisos
chown -R www-data:www-data $APP_PATH
chmod -R 755 $APP_PATH
chmod -R 775 $APP_PATH/data

<a id="configurar-nginx"></a>
<a id="-configurar-nginx"></a>
# Configurar Nginx
cat > /etc/nginx/sites-available/snackshop << EOF
server {
    listen 80;
    server_name $DOMAIN www.$DOMAIN;
    root $APP_PATH/public;
    index index.php;

    location / {
        try_files \\$uri \\$uri/ /index.php?\\$query_string;
    }

    location ~ \\.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \\$realpath_root\\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\\. {
        deny all;
    }
}
EOF

ln -s /etc/nginx/sites-available/snackshop /etc/nginx/sites-enabled/
rm /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx

<a id="ssl-automatico"></a>
<a id="-ssl-automatico"></a>
# SSL automático
apt install -y certbot python3-certbot-nginx
certbot --nginx -d $DOMAIN -d www.$DOMAIN --non-interactive --agree-tos --email admin@$DOMAIN

echo "✅ Despliegue completado!"
echo "🔑 Password MySQL: $DB_PASSWORD"
echo "🌐 Sitio: https://$DOMAIN"
```

```bash
<a id="ejecutar-script"></a>
<a id="-ejecutar-script"></a>
# Ejecutar script
chmod +x deploy.sh
sudo ./deploy.sh
```

<a id="aws-ec2"></a>
<a id="-aws-ec2"></a>
### AWS EC2

<a id="1-lanzar-instancia"></a>
<a id="-1-lanzar-instancia"></a>
#### 1. Lanzar Instancia

- **AMI:** Ubuntu Server 22.04 LTS
- **Tipo:** t3.micro (para desarrollo) o t3.small (producción)
- **Security Group:** HTTP (80), HTTPS (443), SSH (22)

<a id="2-configuracion-con-user-data"></a>
<a id="-2-configuracion-con-user-data"></a>
#### 2. Configuración con User Data

```bash
<a id="binbash"></a>
<a id="-binbash"></a>
#!/bin/bash
<a id="script-de-bootstrap-para-ec2"></a>
<a id="-script-de-bootstrap-para-ec2"></a>
# Script de bootstrap para EC2

<a id="logs-de-instalacion"></a>
<a id="-logs-de-instalacion"></a>
# Logs de instalación
exec > >(tee /var/log/user-data.log|logger -t user-data -s 2>/dev/console) 2>&1

<a id="variables-desde-ec2-tags-o-parametros"></a>
<a id="-variables-desde-ec2-tags-o-parametros"></a>
# Variables desde EC2 tags o parámetros
DOMAIN=$(curl -s http://169.254.169.254/latest/meta-data/tags/instance/Domain || echo "example.com")

<a id="ejecutar-script-de-instalacion"></a>
<a id="-ejecutar-script-de-instalacion"></a>
# Ejecutar script de instalación
curl -sSL https://raw.githubusercontent.com/tu-usuario/snackshop-deploy/main/aws-deploy.sh | bash -s -- $DOMAIN
```

<a id="azure-app-service"></a>
<a id="-azure-app-service"></a>
### Azure App Service

<a id="1-configuracion-web-app"></a>
<a id="-1-configuracion-web-app"></a>
#### 1. Configuración Web App

- **Runtime:** PHP 8.1
- **OS:** Linux
- **Plan:** B1 Basic ($13/mes)

<a id="2-configuracion-de-despliegue"></a>
<a id="-2-configuracion-de-despliegue"></a>
#### 2. Configuración de Despliegue

```yaml
<a id="githubworkflowsazureyml"></a>
<a id="-githubworkflowsazureyml"></a>
# .github/workflows/azure.yml
name: Deploy to Azure

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest

    steps:
    - uses: actions/checkout@v2

    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: 8.1
        extensions: pdo, pdo_mysql, mbstring, xml, curl, zip, gd

    - name: Install dependencies
      run: composer install --no-dev --optimize-autoloader

    - name: Deploy to Azure
      uses: azure/webapps-deploy@v2
      with:
        app-name: 'snackshop-app'
        publish-profile: ${{ secrets.AZURE_WEBAPP_PUBLISH_PROFILE }}
```

---

<a id="variables-de-entorno"></a>
<a id="-variables-de-entorno"></a>
## 🔧 Variables de Entorno

<a id="archivo-env-completo"></a>
<a id="-archivo-env-completo"></a>
### Archivo `.env` Completo

```bash
<a id="deployment"></a>
<a id="-deployment"></a>
# ==============================================
<a id="snackshop-configuration"></a>
<a id="-snackshop-configuration"></a>
# SnackShop Configuration
<a id="deployment"></a>
<a id="-deployment"></a>
# ==============================================

<a id="application"></a>
<a id="-application"></a>
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

<a id="database"></a>
<a id="-database"></a>
# Database
SNACKSHOP_DB_HOST=localhost
SNACKSHOP_DB_PORT=3306
SNACKSHOP_DB_NAME=snackshop
SNACKSHOP_DB_USER=snackshop
SNACKSHOP_DB_PASS=secure_password_here
SNACKSHOP_DB_CHARSET=utf8mb4

<a id="session"></a>
<a id="-session"></a>
# Session
SESSION_LIFETIME=7200
SESSION_SECURE=true
SESSION_HTTPONLY=true
SESSION_SAMESITE=Strict

<a id="security"></a>
<a id="-security"></a>
# Security
CSRF_TOKEN_LIFETIME=3600
BCRYPT_ROUNDS=12

<a id="file-uploads"></a>
<a id="-file-uploads"></a>
# File uploads
MAX_UPLOAD_SIZE=5242880  # 5MB
ALLOWED_IMAGE_TYPES=jpg,jpeg,png,gif,webp

<a id="logging"></a>
<a id="-logging"></a>
# Logging
LOG_LEVEL=warning
LOG_FILE=/var/log/snackshop/app.log

<a id="cache"></a>
<a id="-cache"></a>
# Cache
CACHE_DRIVER=file  # file, redis, memcached
CACHE_TTL=3600

<a id="email-opcional"></a>
<a id="-email-opcional"></a>
# Email (opcional)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@tu-dominio.com
MAIL_FROM_NAME="SnackShop"

<a id="backup"></a>
<a id="-backup"></a>
# Backup
BACKUP_ENABLED=true
BACKUP_SCHEDULE="0 2 * * *"  # Diario a las 2 AM
BACKUP_RETENTION_DAYS=30
BACKUP_PATH=/var/backups/snackshop

<a id="performance"></a>
<a id="-performance"></a>
# Performance
ENABLE_GZIP=true
ENABLE_CACHE_HEADERS=true
```

<a id="variables-por-entorno"></a>
<a id="-variables-por-entorno"></a>
### Variables por Entorno

<a id="desarrollo"></a>
<a id="-desarrollo"></a>
#### Desarrollo

```bash
APP_ENV=development
APP_DEBUG=true
SNACKSHOP_DB_HOST=127.0.0.1
SESSION_SECURE=false
LOG_LEVEL=debug
```

<a id="staging"></a>
<a id="-staging"></a>
#### Staging

```bash
APP_ENV=staging
APP_DEBUG=true
SNACKSHOP_DB_HOST=staging-db.interno
SESSION_SECURE=true
LOG_LEVEL=info
```

<a id="produccion"></a>
<a id="-produccion"></a>
#### Producción

```bash
APP_ENV=production
APP_DEBUG=false
SNACKSHOP_DB_HOST=prod-db.interno
SESSION_SECURE=true
LOG_LEVEL=warning
```

<a id="gestion-de-variables"></a>
<a id="-gestion-de-variables"></a>
### Gestión de Variables

<a id="con-docker"></a>
<a id="-con-docker"></a>
#### Con Docker

```yaml
<a id="docker-composeyml"></a>
<a id="-docker-composeyml"></a>
# docker-compose.yml
environment:
  - APP_ENV=${APP_ENV:-production}
  - SNACKSHOP_DB_HOST=${DB_HOST}
  - SNACKSHOP_DB_PASS=${DB_PASS}
env_file:
  - .env.production
```

<a id="con-systemd-vps"></a>
<a id="-con-systemd-vps"></a>
#### Con Systemd (VPS)

```ini
<a id="etcsystemdsystemsnackshopservice"></a>
<a id="-etcsystemdsystemsnackshopservice"></a>
# /etc/systemd/system/snackshop.service
[Unit]
Description=SnackShop Application
After=network.target

[Service]
Type=forking
User=www-data
WorkingDirectory=/var/www/snackshop
Environment=APP_ENV=production
Environment=SNACKSHOP_DB_HOST=localhost
EnvironmentFile=/var/www/snackshop/.env
ExecStart=/usr/bin/php -S 0.0.0.0:8000 -t public/
Restart=always

[Install]
WantedBy=multi-user.target
```

---

<a id="ssl-y-seguridad"></a>
<a id="-ssl-y-seguridad"></a>
## 🔒 SSL y Seguridad

<a id="certificados-ssl"></a>
<a id="-certificados-ssl"></a>
### Certificados SSL

<a id="lets-encrypt-gratuito"></a>
<a id="-lets-encrypt-gratuito"></a>
#### Let's Encrypt (Gratuito)

```bash
<a id="instalar-certbot"></a>
<a id="-instalar-certbot"></a>
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx

<a id="obtener-certificado"></a>
<a id="-obtener-certificado"></a>
# Obtener certificado
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com

<a id="verificar-renovacion"></a>
<a id="-verificar-renovacion"></a>
# Verificar renovación
sudo certbot renew --dry-run

<a id="cron-para-renovacion-automatica"></a>
<a id="-cron-para-renovacion-automatica"></a>
# Cron para renovación automática
echo "0 12 * * * /usr/bin/certbot renew --quiet" | sudo crontab -
```

<a id="certificado-comercial"></a>
<a id="-certificado-comercial"></a>
#### Certificado Comercial

```bash
<a id="generar-csr"></a>
<a id="-generar-csr"></a>
# Generar CSR
openssl req -new -newkey rsa:2048 -nodes -keyout tu-dominio.key -out tu-dominio.csr

<a id="configurar-en-nginx"></a>
<a id="-configurar-en-nginx"></a>
# Configurar en Nginx
ssl_certificate /etc/ssl/certs/tu-dominio.crt;
ssl_certificate_key /etc/ssl/private/tu-dominio.key;
```

<a id="configuracion-de-seguridad"></a>
<a id="-configuracion-de-seguridad"></a>
### Configuración de Seguridad

<a id="nginx-security-headers"></a>
<a id="-nginx-security-headers"></a>
#### Nginx Security Headers

```nginx
<a id="configuracion-de-seguridad-robusta"></a>
<a id="-configuracion-de-seguridad-robusta"></a>
# Configuración de seguridad robusta
server {
    # SSL Configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    # Security Headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self';" always;

    # Hide server information
    server_tokens off;

    # Rate limiting
    limit_req_zone $binary_remote_addr zone=login:10m rate=5r/m;
    limit_req_zone $binary_remote_addr zone=api:10m rate=100r/m;

    location /login {
        limit_req zone=login burst=3 nodelay;
        # ... resto de configuración
    }

    location /api/ {
        limit_req zone=api burst=20 nodelay;
        # ... resto de configuración
    }
}
```

<a id="firewall-ufw"></a>
<a id="-firewall-ufw"></a>
#### Firewall (UFW)

```bash
<a id="configurar-firewall-basico"></a>
<a id="-configurar-firewall-basico"></a>
# Configurar firewall básico
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw enable

<a id="ver-estado"></a>
<a id="-ver-estado"></a>
# Ver estado
sudo ufw status verbose
```

<a id="fail2ban"></a>
<a id="-fail2ban"></a>
#### Fail2Ban

```bash
<a id="instalar"></a>
<a id="-instalar"></a>
# Instalar
sudo apt install fail2ban

<a id="configurar"></a>
<a id="-configurar"></a>
# Configurar
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
```

**`/etc/fail2ban/jail.local`**
```ini
[sshd]
enabled = true
port = ssh
filter = sshd
logpath = /var/log/auth.log
maxretry = 3
bantime = 3600

[nginx-http-auth]
enabled = true
filter = nginx-http-auth
logpath = /var/log/nginx/error.log
maxretry = 5
bantime = 1800
```

---

<a id="monitoreo-y-logs"></a>
<a id="-monitoreo-y-logs"></a>
## 📊 Monitoreo y Logs

<a id="configuracion-de-logs"></a>
<a id="-configuracion-de-logs"></a>
### Configuración de Logs

<a id="nginx-logs"></a>
<a id="-nginx-logs"></a>
#### Nginx Logs

```nginx
<a id="etcnginxnginxconf"></a>
<a id="-etcnginxnginxconf"></a>
# /etc/nginx/nginx.conf
http {
    # Formato de log personalizado
    log_format detailed '$remote_addr - $remote_user [$time_local] '
                       '"$request" $status $body_bytes_sent '
                       '"$http_referer" "$http_user_agent" '
                       '$request_time $upstream_response_time';

    access_log /var/log/nginx/access.log detailed;
    error_log /var/log/nginx/error.log warn;
}
```

<a id="php-logs"></a>
<a id="-php-logs"></a>
#### PHP Logs

```ini
; /etc/php/8.1/fpm/php.ini
log_errors = On
error_log = /var/log/php/error.log
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
```

<a id="application-logs-personalizado"></a>
<a id="-application-logs-personalizado"></a>
#### Application Logs (Personalizado)

```php
// src/Utils/Logger.php
class Logger {
    public static function log($level, $message, $context = []) {
        $logFile = getenv('LOG_FILE') ?: '/var/log/snackshop/app.log';
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = $context ? json_encode($context) : '';

        $logLine = "[$timestamp] $level: $message $contextStr" . PHP_EOL;
        file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
    }
}

// Uso en controllers
Logger::log('INFO', 'User login', ['user_id' => $userId, 'ip' => $_SERVER['REMOTE_ADDR']]);
Logger::log('ERROR', 'Database connection failed', ['error' => $e->getMessage()]);
```

<a id="monitoreo-con-scripts"></a>
<a id="-monitoreo-con-scripts"></a>
### Monitoreo con Scripts

<a id="script-de-salud-del-sistema"></a>
<a id="-script-de-salud-del-sistema"></a>
#### Script de Salud del Sistema

```bash
<a id="binbash"></a>
<a id="-binbash"></a>
#!/bin/bash
<a id="usrlocalbinhealth-checksh"></a>
<a id="-usrlocalbinhealth-checksh"></a>
# /usr/local/bin/health-check.sh

<a id="variables"></a>
<a id="-variables"></a>
# Variables
DOMAIN="tu-dominio.com"
LOG_FILE="/var/log/snackshop/health.log"
EMAIL="admin@tu-dominio.com"

<a id="funcion-de-log"></a>
<a id="-funcion-de-log"></a>
# Función de log
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" >> $LOG_FILE
}

<a id="verificar-servicio-web"></a>
<a id="-verificar-servicio-web"></a>
# Verificar servicio web
if ! curl -f -s "https://$DOMAIN" > /dev/null; then
    log "ERROR: Web service down"
    echo "SnackShop web service is down" | mail -s "ALERT: Service Down" $EMAIL
fi

<a id="verificar-base-de-datos"></a>
<a id="-verificar-base-de-datos"></a>
# Verificar base de datos
if ! mysqladmin ping -h localhost > /dev/null 2>&1; then
    log "ERROR: Database down"
    echo "MySQL database is down" | mail -s "ALERT: Database Down" $EMAIL
fi

<a id="verificar-espacio-en-disco"></a>
<a id="-verificar-espacio-en-disco"></a>
# Verificar espacio en disco
DISK_USAGE=$(df / | awk 'NR==2 {print $5}' | sed 's/%//')
if [ $DISK_USAGE -gt 80 ]; then
    log "WARNING: Disk usage at ${DISK_USAGE}%"
    echo "Disk usage is at ${DISK_USAGE}%" | mail -s "WARNING: High Disk Usage" $EMAIL
fi

<a id="verificar-memoria"></a>
<a id="-verificar-memoria"></a>
# Verificar memoria
MEM_USAGE=$(free | awk 'NR==2{printf "%.2f", $3*100/$2}')
if (( $(echo "$MEM_USAGE > 90" | bc -l) )); then
    log "WARNING: Memory usage at ${MEM_USAGE}%"
fi

log "Health check completed"
```

```bash
<a id="programar-en-crontab"></a>
<a id="-programar-en-crontab"></a>
# Programar en crontab
crontab -e
<a id="anadir-5-usrlocalbinhealth-checksh"></a>
<a id="-anadir-5-usrlocalbinhealth-checksh"></a>
# Añadir: */5 * * * * /usr/local/bin/health-check.sh
```

<a id="monitoreo-avanzado"></a>
<a id="-monitoreo-avanzado"></a>
### Monitoreo Avanzado

<a id="prometheus-grafana-opcional"></a>
<a id="-prometheus-grafana-opcional"></a>
#### Prometheus + Grafana (Opcional)

```yaml
<a id="docker-composemonitoringyml"></a>
<a id="-docker-composemonitoringyml"></a>
# docker-compose.monitoring.yml
version: '3.8'

services:
  prometheus:
    image: prom/prometheus
    ports:
      - "9090:9090"
    volumes:
      - ./monitoring/prometheus.yml:/etc/prometheus/prometheus.yml

  grafana:
    image: grafana/grafana
    ports:
      - "3000:3000"
    environment:
      - GF_SECURITY_ADMIN_PASSWORD=admin
    volumes:
      - grafana-data:/var/lib/grafana

  node-exporter:
    image: prom/node-exporter
    ports:
      - "9100:9100"

volumes:
  grafana-data:
```

<a id="uptime-monitoring"></a>
<a id="-uptime-monitoring"></a>
#### Uptime Monitoring

```bash
<a id="script-simple-de-uptime"></a>
<a id="-script-simple-de-uptime"></a>
# Script simple de uptime
<a id="binbash"></a>
<a id="-binbash"></a>
#!/bin/bash
<a id="usrlocalbinuptime-monitorsh"></a>
<a id="-usrlocalbinuptime-monitorsh"></a>
# /usr/local/bin/uptime-monitor.sh

URL="https://tu-dominio.com"
STATUS=$(curl -o /dev/null -s -w "%{http_code}" $URL)

if [ $STATUS -eq 200 ]; then
    echo "$(date): OK - $URL is up" >> /var/log/uptime.log
else
    echo "$(date): ERROR - $URL returned $STATUS" >> /var/log/uptime.log
    # Enviar alerta
    curl -X POST "https://api.telegram.org/botTOKEN/sendMessage" \\
         -d "chat_id=CHAT_ID&text=🚨 SnackShop is down! Status: $STATUS"
fi
```

---

<a id="backup-automatizado"></a>
<a id="-backup-automatizado"></a>
## 💾 Backup Automatizado

<a id="script-de-backup-completo"></a>
<a id="-script-de-backup-completo"></a>
### Script de Backup Completo

```bash
<a id="binbash"></a>
<a id="-binbash"></a>
#!/bin/bash
<a id="usrlocalbinbackup-snackshopsh"></a>
<a id="-usrlocalbinbackup-snackshopsh"></a>
# /usr/local/bin/backup-snackshop.sh

<a id="configuracion"></a>
<a id="-configuracion"></a>
# Configuración
BACKUP_DIR="/var/backups/snackshop"
APP_DIR="/var/www/snackshop"
DB_NAME="snackshop"
DB_USER="snackshop"
DB_PASS="password_aqui"
RETENTION_DAYS=30
DATE=$(date +%Y%m%d_%H%M%S)

<a id="crear-directorio-de-backup"></a>
<a id="-crear-directorio-de-backup"></a>
# Crear directorio de backup
mkdir -p $BACKUP_DIR

<a id="backup-de-base-de-datos"></a>
<a id="-backup-de-base-de-datos"></a>
# Backup de base de datos
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

<a id="backup-de-archivos-de-aplicacion"></a>
<a id="-backup-de-archivos-de-aplicacion"></a>
# Backup de archivos de aplicación
tar -czf $BACKUP_DIR/app_$DATE.tar.gz -C $APP_DIR \\
    --exclude='vendor' \\
    --exclude='node_modules' \\
    --exclude='.git' \\
    .

<a id="backup-de-archivos-subidos-data"></a>
<a id="-backup-de-archivos-subidos-data"></a>
# Backup de archivos subidos (data)
tar -czf $BACKUP_DIR/data_$DATE.tar.gz $APP_DIR/data/

<a id="limpiar-backups-antiguos"></a>
<a id="-limpiar-backups-antiguos"></a>
# Limpiar backups antiguos
find $BACKUP_DIR -name "*.gz" -mtime +$RETENTION_DAYS -delete

<a id="log-del-backup"></a>
<a id="-log-del-backup"></a>
# Log del backup
echo "[$(date)] Backup completed: db_$DATE.sql.gz, app_$DATE.tar.gz, data_$DATE.tar.gz" >> /var/log/snackshop/backup.log

<a id="verificar-integridad"></a>
<a id="-verificar-integridad"></a>
# Verificar integridad
if [ -f "$BACKUP_DIR/db_$DATE.sql.gz" ] && [ -f "$BACKUP_DIR/app_$DATE.tar.gz" ]; then
    echo "[$(date)] Backup verification: SUCCESS" >> /var/log/snackshop/backup.log
else
    echo "[$(date)] Backup verification: FAILED" >> /var/log/snackshop/backup.log
    echo "Backup failed for SnackShop" | mail -s "BACKUP FAILED" admin@tu-dominio.com
fi
```

<a id="backup-remoto-s3cloud"></a>
<a id="-backup-remoto-s3cloud"></a>
### Backup Remoto (S3/Cloud)

```bash
<a id="binbash"></a>
<a id="-binbash"></a>
#!/bin/bash
<a id="backup-con-sincronizacion-a-aws-s3"></a>
<a id="-backup-con-sincronizacion-a-aws-s3"></a>
# Backup con sincronización a AWS S3

<a id="instalar-aws-cli"></a>
<a id="-instalar-aws-cli"></a>
# Instalar AWS CLI
<a id="apt-install-awscli"></a>
<a id="-apt-install-awscli"></a>
# apt install awscli

<a id="configurar-credenciales"></a>
<a id="-configurar-credenciales"></a>
# Configurar credenciales
<a id="aws-configure"></a>
<a id="-aws-configure"></a>
# aws configure

<a id="sincronizar-backups-a-s3"></a>
<a id="-sincronizar-backups-a-s3"></a>
# Sincronizar backups a S3
aws s3 sync $BACKUP_DIR s3://tu-bucket-backup/snackshop/ --delete

<a id="log-de-sincronizacion"></a>
<a id="-log-de-sincronizacion"></a>
# Log de sincronización
echo "[$(date)] Remote backup sync completed" >> /var/log/snackshop/backup.log
```

<a id="programacion-de-backups"></a>
<a id="-programacion-de-backups"></a>
### Programación de Backups

```bash
<a id="crontab-para-backups-automaticos"></a>
<a id="-crontab-para-backups-automaticos"></a>
# Crontab para backups automáticos
crontab -e

<a id="backup-diario-a-las-2-am"></a>
<a id="-backup-diario-a-las-2-am"></a>
# Backup diario a las 2 AM
0 2 * * * /usr/local/bin/backup-snackshop.sh

<a id="backup-de-logs-semanales-domingos-a-las-3-am"></a>
<a id="-backup-de-logs-semanales-domingos-a-las-3-am"></a>
# Backup de logs semanales (domingos a las 3 AM)
0 3 * * 0 tar -czf /var/backups/logs_$(date +\%Y\%m\%d).tar.gz /var/log/nginx/ /var/log/snackshop/

<a id="verificacion-de-espacio-en-disco-diario"></a>
<a id="-verificacion-de-espacio-en-disco-diario"></a>
# Verificación de espacio en disco diario
0 6 * * * df -h | mail -s "Disk Space Report" admin@tu-dominio.com
```

---

<a id="troubleshooting"></a>
<a id="-troubleshooting"></a>
## 🚨 Troubleshooting

<a id="problemas-comunes"></a>
<a id="-problemas-comunes"></a>
### Problemas Comunes

<a id="1-error-500-internal-server-error"></a>
<a id="-1-error-500-internal-server-error"></a>
#### 1. Error 500 - Internal Server Error

**Diagnóstico:**
```bash
<a id="verificar-logs-de-error"></a>
<a id="-verificar-logs-de-error"></a>
# Verificar logs de error
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/php8.1-fpm.log

<a id="verificar-permisos"></a>
<a id="-verificar-permisos"></a>
# Verificar permisos
ls -la /var/www/snackshop/
ls -la /var/www/snackshop/data/

<a id="verificar-configuracion-php"></a>
<a id="-verificar-configuracion-php"></a>
# Verificar configuración PHP
php --ini
php -m | grep -E "(pdo|mysql)"
```

**Soluciones:**
```bash
<a id="corregir-permisos"></a>
<a id="-corregir-permisos"></a>
# Corregir permisos
sudo chown -R www-data:www-data /var/www/snackshop
sudo chmod -R 755 /var/www/snackshop
sudo chmod -R 775 /var/www/snackshop/data

<a id="verificar-configuracion-nginx"></a>
<a id="-verificar-configuracion-nginx"></a>
# Verificar configuración Nginx
sudo nginx -t

<a id="reiniciar-servicios"></a>
<a id="-reiniciar-servicios"></a>
# Reiniciar servicios
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
```

<a id="2-error-de-base-de-datos"></a>
<a id="-2-error-de-base-de-datos"></a>
#### 2. Error de Base de Datos

**Diagnóstico:**
```bash
<a id="verificar-mysql"></a>
<a id="-verificar-mysql"></a>
# Verificar MySQL
sudo systemctl status mysql
sudo mysql -u snackshop -p

<a id="verificar-conectividad"></a>
<a id="-verificar-conectividad"></a>
# Verificar conectividad
telnet localhost 3306

<a id="verificar-logs-mysql"></a>
<a id="-verificar-logs-mysql"></a>
# Verificar logs MySQL
sudo tail -f /var/log/mysql/error.log
```

**Soluciones:**
```bash
<a id="reiniciar-mysql"></a>
<a id="-reiniciar-mysql"></a>
# Reiniciar MySQL
sudo systemctl restart mysql

<a id="verificar-configuracion"></a>
<a id="-verificar-configuracion"></a>
# Verificar configuración
sudo mysql_secure_installation

<a id="recuperar-desde-backup"></a>
<a id="-recuperar-desde-backup"></a>
# Recuperar desde backup
gunzip < /var/backups/snackshop/db_latest.sql.gz | mysql -u snackshop -p snackshop
```

<a id="3-certificado-ssl-expirado"></a>
<a id="-3-certificado-ssl-expirado"></a>
#### 3. Certificado SSL Expirado

**Diagnóstico:**
```bash
<a id="verificar-certificado"></a>
<a id="-verificar-certificado"></a>
# Verificar certificado
openssl s_client -connect tu-dominio.com:443 -servername tu-dominio.com

<a id="verificar-certbot"></a>
<a id="-verificar-certbot"></a>
# Verificar Certbot
sudo certbot certificates
```

**Solución:**
```bash
<a id="renovar-certificado"></a>
<a id="-renovar-certificado"></a>
# Renovar certificado
sudo certbot renew
sudo systemctl reload nginx
```

<a id="4-alto-uso-de-cpuram"></a>
<a id="-4-alto-uso-de-cpuram"></a>
#### 4. Alto Uso de CPU/RAM

**Diagnóstico:**
```bash
<a id="monitorear-procesos"></a>
<a id="-monitorear-procesos"></a>
# Monitorear procesos
top
htop
ps aux --sort=-%cpu | head -10
ps aux --sort=-%mem | head -10

<a id="verificar-conexiones"></a>
<a id="-verificar-conexiones"></a>
# Verificar conexiones
netstat -tuln
ss -tuln
```

**Soluciones:**
```bash
<a id="optimizar-mysql"></a>
<a id="-optimizar-mysql"></a>
# Optimizar MySQL
sudo mysql_secure_installation
<a id="ajustar-mycnf-para-el-tamano-del-servidor"></a>
<a id="-ajustar-mycnf-para-el-tamano-del-servidor"></a>
# Ajustar my.cnf para el tamaño del servidor

<a id="optimizar-php-fpm"></a>
<a id="-optimizar-php-fpm"></a>
# Optimizar PHP-FPM
sudo nano /etc/php/8.1/fpm/pool.d/www.conf
<a id="ajustar-pmmax_children-pmstart_servers-etc"></a>
<a id="-ajustar-pmmax_children-pmstart_servers-etc"></a>
# Ajustar pm.max_children, pm.start_servers, etc.

<a id="anadir-swap-si-es-necesario"></a>
<a id="-anadir-swap-si-es-necesario"></a>
# Añadir swap si es necesario
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
```

<a id="scripts-de-diagnostico"></a>
<a id="-scripts-de-diagnostico"></a>
### Scripts de Diagnóstico

<a id="diagnosticsh"></a>
<a id="-diagnosticsh"></a>
#### `diagnostic.sh`

```bash
<a id="binbash"></a>
<a id="-binbash"></a>
#!/bin/bash
<a id="script-de-diagnostico-completo"></a>
<a id="-script-de-diagnostico-completo"></a>
# Script de diagnóstico completo

echo "=== SnackShop Diagnostic Report ==="
echo "Date: $(date)"
echo

echo "=== System Information ==="
uname -a
lsb_release -a
echo

echo "=== Disk Usage ==="
df -h
echo

echo "=== Memory Usage ==="
free -h
echo

echo "=== Service Status ==="
systemctl status nginx --no-pager
systemctl status php8.1-fpm --no-pager
systemctl status mysql --no-pager
echo

echo "=== Network Connectivity ==="
ping -c 3 google.com
curl -I https://tu-dominio.com
echo

echo "=== Recent Errors ==="
echo "--- Nginx Errors ---"
tail -20 /var/log/nginx/error.log
echo
echo "--- PHP Errors ---"
tail -20 /var/log/php8.1-fpm.log
echo

echo "=== Database Status ==="
mysql -u snackshop -p -e "SELECT COUNT(*) as usuarios FROM usuarios;" snackshop
mysql -u snackshop -p -e "SELECT COUNT(*) as productos FROM productos;" snackshop
mysql -u snackshop -p -e "SELECT COUNT(*) as ventas FROM ventas;" snackshop
echo

echo "=== SSL Certificate ==="
echo | openssl s_client -connect tu-dominio.com:443 -servername tu-dominio.com 2>/dev/null | openssl x509 -noout -dates
echo

echo "=== Backup Status ==="
ls -la /var/backups/snackshop/ | tail -5
echo

echo "=== End of Report ==="
```

---

<a id="checklist-de-produccion"></a>
<a id="-checklist-de-produccion"></a>
## ✅ Checklist de Producción

<a id="pre-despliegue"></a>
<a id="-pre-despliegue"></a>
### Pre-Despliegue

- [ ] **Configuración**
  - [ ] Variables de entorno configuradas
  - [ ] Base de datos creada y migrada
  - [ ] Permisos de archivos correctos (755/775)
  - [ ] Composer instalado con `--no-dev --optimize-autoloader`

- [ ] **Seguridad**
  - [ ] SSL/TLS configurado
  - [ ] Firewall configurado (UFW/iptables)
  - [ ] Fail2Ban instalado y configurado
  - [ ] Headers de seguridad habilitados
  - [ ] Archivos sensibles protegidos (.env, logs)

- [ ] **Performance**
  - [ ] PHP-FPM optimizado para el servidor
  - [ ] Nginx con compresión gzip habilitada
  - [ ] Caché de archivos estáticos configurado
  - [ ] Rate limiting configurado

<a id="post-despliegue"></a>
<a id="-post-despliegue"></a>
### Post-Despliegue

- [ ] **Verificación**
  - [ ] Sitio web accesible vía HTTPS
  - [ ] Login funciona correctamente
  - [ ] Base de datos conecta sin errores
  - [ ] Subida de imágenes funciona
  - [ ] API endpoints responden correctamente

- [ ] **Monitoreo**
  - [ ] Logs configurados y rotando
  - [ ] Backup automático configurado
  - [ ] Monitoreo de salud del sistema activo
  - [ ] Alertas por email/Telegram configuradas

- [ ] **Documentación**
  - [ ] Credenciales documentadas de forma segura
  - [ ] Procedimientos de backup documentados
  - [ ] Contactos de emergencia definidos
  - [ ] Runbook de troubleshooting actualizado

<a id="mantenimiento-continuo"></a>
<a id="-mantenimiento-continuo"></a>
### Mantenimiento Continuo

- [ ] **Semanal**
  - [ ] Verificar logs de error
  - [ ] Comprobar uso de disco/memoria
  - [ ] Verificar estado de backups
  - [ ] Revisar alertas de seguridad

- [ ] **Mensual**
  - [ ] Actualizar sistema operativo
  - [ ] Actualizar dependencias PHP
  - [ ] Renovar certificados SSL si es necesario
  - [ ] Revisar métricas de performance

- [ ] **Trimestral**
  - [ ] Auditoría de seguridad
  - [ ] Revisión de configuración
  - [ ] Prueba de restauración desde backup
  - [ ] Optimización de base de datos

---

<a id="documentos-relacionados"></a>
<a id="-documentos-relacionados"></a>
## 🔗 Documentos Relacionados

- **[📖 Índice General](docs/INDEX.md)** — Navegación completa del manual
- **[🏗️ Architecture](ARCHITECTURE.md)** — Comprende la arquitectura antes del despliegue
- **[🗄️ Database](DATABASE.md)** — Configuración de base de datos
- **[🔌 API](API.md)** — Endpoints que deben funcionar después del despliegue
- **[🔧 Development](DEVELOPMENT.md)** — Setup para desarrolladores

---

<a id="soporte"></a>
<a id="-soporte"></a>
## 📞 Soporte

**¿Problemas con el despliegue?**
- **Issues:** [GitHub Issues](https://github.com/Equinoxe-Grammer/SnackShack/issues) con etiqueta `deployment`
- **Backup:** Siempre mantén backups antes de cambios importantes
- **Testing:** Prueba en staging antes de desplegar a producción

---

**[📖 Índice](docs/INDEX.md)** | **[🏗️ Ver Arquitectura](ARCHITECTURE.md)** | **[🗄️ Ver Database](DATABASE.md)** | **[🔧 Ver Development](DEVELOPMENT.md)**
