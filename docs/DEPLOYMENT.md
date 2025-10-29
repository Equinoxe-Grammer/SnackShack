# 🚀 SnackShop - Guía de Despliegue

**🏠 Ubicación:** `DEPLOYMENT.md`  
**📅 Última actualización:** 28 de octubre, 2025  
**🎯 Propósito:** Guía completa para desplegar SnackShop en desarrollo, staging y producción

---

## 🧭 Navegación

**[📖 Índice General](docs/INDEX.md)** | **[🏠 README](README.md)** | **[🏗️ Arquitectura](ARCHITECTURE.md)** | **[🗄️ Database](DATABASE.md)**

---

## 📋 Índice

- [Resumen de Opciones de Despliegue](#-resumen-de-opciones-de-despliegue)
- [Desarrollo Local](#-desarrollo-local)
- [Despliegue con Docker](#-despliegue-con-docker)
- [Servidor Compartido](#-servidor-compartido)
- [VPS / Servidor Dedicado](#-vps--servidor-dedicado)
- [Cloud Platforms](#-cloud-platforms)
- [Variables de Entorno](#-variables-de-entorno)
- [SSL y Seguridad](#-ssl-y-seguridad)
- [Monitoreo y Logs](#-monitoreo-y-logs)
- [Backup Automatizado](#-backup-automatizado)
- [Troubleshooting](#-troubleshooting)
- [Checklist de Producción](#-checklist-de-producción)

---

## 🎯 Resumen de Opciones de Despliegue

### Por Entorno de Uso

| Entorno | Tecnología | Complejidad | Costo | Recomendado Para |
|---------|------------|-------------|-------|------------------|
| **Desarrollo** | PHP embebido + SQLite | ⭐ | Gratis | Desarrollo local |
| **Staging** | Docker Compose | ⭐⭐ | Gratis | Testing pre-producción |
| **Shared Hosting** | cPanel/FileManager | ⭐⭐ | $5-15/mes | Proyectos pequeños |
| **VPS** | Nginx + MySQL | ⭐⭐⭐ | $10-50/mes | Proyectos medianos |
| **Cloud** | DigitalOcean/AWS | ⭐⭐⭐⭐ | $20-100/mes | Escalabilidad |

### Recomendaciones por Escala
- **1-10 usuarios:** Shared hosting o VPS básico
- **10-100 usuarios:** VPS con optimización
- **100+ usuarios:** Cloud con load balancing

---

## 💻 Desarrollo Local

### Opción 1: Servidor Embebido PHP (Recomendado)

```powershell
# 1. Verificar requisitos
php --version  # PHP 7.4+
composer --version  # Composer

# 2. Clonar e instalar
git clone https://github.com/Equinoxe-Grammer/SnackShack.git
cd SnackShack\SnackShop\www\Snackshop
composer install

# 3. Verificar base de datos
# Asegurar que data\snackshop.db existe

# 4. Iniciar servidor
php -S localhost:8000 -t public

# 5. Abrir navegador
start http://localhost:8000
```

### Opción 2: XAMPP (Windows)

```powershell
# 1. Instalar XAMPP
# Descargar desde https://www.apachefriends.org/

# 2. Copiar proyecto
Copy-Item -Recurse "SnackShop" "C:\xampp\htdocs\"

# 3. Configurar virtual host (opcional)
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

### Opción 3: Laravel Valet (Avanzado)

```powershell
# Instalar Valet para Windows
composer global require cretueusebiu/valet-windows
valet install

# En directorio del proyecto
valet link snackshop
valet secure snackshop  # SSL opcional

# Acceder en: https://snackshop.test
```

---

## 🐳 Despliegue con Docker

### Docker Compose Completo

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

#### `Dockerfile`
```dockerfile
FROM php:8.1-apache

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

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html \\
    && chmod -R 755 /var/www/html \\
    && chmod -R 775 /var/www/html/data

# Configurar Apache DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Puerto expuesto
EXPOSE 80

# Comando de inicio
CMD ["apache2-foreground"]
```

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

### Comandos Docker

```powershell
# Construcción y arranque
docker-compose up -d --build

# Ver logs
docker-compose logs -f web
docker-compose logs -f db

# Acceso a contenedor
docker-compose exec web bash
docker-compose exec db mysql -u snackshop -p snackshop

# Backup de BD
docker-compose exec db mysqldump -u snackshop -p snackshop > backup.sql

# Parar servicios
docker-compose down

# Limpiar volúmenes (⚠️ Elimina datos)
docker-compose down -v
```

---

## 🌐 Servidor Compartido

### Preparación del Proyecto

```powershell
# 1. Preparar para producción
composer install --no-dev --optimize-autoloader

# 2. Crear archivo .env
copy .env.example .env
# Editar variables de producción

# 3. Crear ZIP para upload
Compress-Archive -Path * -DestinationPath snackshop-prod.zip
```

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

#### `public_html/index.php` (Punto de entrada)
```php
<?php
// Redirigir al proyecto SnackShop
require_once __DIR__ . '/snackshop/public/index.php';
```

### Configuración .htaccess

#### `public_html/.htaccess`
```apache
# Forzar HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Redirigir al proyecto
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ snackshop/public/$1 [L]
```

#### `public_html/snackshop/public/.htaccess`
```apache
RewriteEngine On

# Handle Angular/Vue Router (opcional)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]

# Security
<Files .env>
    Order allow,deny
    Deny from all
</Files>

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

### Configuración cPanel

1. **File Manager:** Subir proyecto a `public_html/snackshop/`
2. **MySQL Databases:** Crear base de datos y usuario
3. **Cron Jobs:** Configurar backup automático
4. **SSL/TLS:** Activar certificado gratuito

---

## 🖥️ VPS / Servidor Dedicado

### Ubuntu 22.04 LTS Setup

#### 1. Preparación del Servidor

```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar dependencias base
sudo apt install -y curl wget git unzip software-properties-common

# Instalar PHP 8.1
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.1 php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip php8.1-gd php8.1-sqlite3

# Instalar Nginx
sudo apt install -y nginx

# Instalar MySQL
sudo apt install -y mysql-server

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### 2. Configuración MySQL

```bash
# Configuración segura
sudo mysql_secure_installation

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
# Habilitar sitio
sudo ln -s /etc/nginx/sites-available/snackshop /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### 4. Despliegue del Proyecto

```bash
# Crear directorio
sudo mkdir -p /var/www/snackshop
cd /var/www/snackshop

# Clonar proyecto (o subir via SFTP)
sudo git clone https://github.com/Equinoxe-Grammer/SnackShack.git .

# Instalar dependencias
sudo composer install --no-dev --optimize-autoloader

# Configurar permisos
sudo chown -R www-data:www-data /var/www/snackshop
sudo chmod -R 755 /var/www/snackshop
sudo chmod -R 775 /var/www/snackshop/data

# Configurar variables de entorno
sudo cp .env.example .env
sudo nano .env
```

#### 5. SSL con Let's Encrypt

```bash
# Instalar Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtener certificado
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com

# Verificar renovación automática
sudo crontab -e
# Añadir: 0 12 * * * /usr/bin/certbot renew --quiet
```

---

## ☁️ Cloud Platforms

### DigitalOcean Droplet

#### 1. Crear Droplet
- **Imagen:** Ubuntu 22.04 LTS
- **Plan:** $12/mes (2GB RAM, 1 vCPU)
- **Región:** Más cercana a usuarios
- **SSH Keys:** Añadir clave pública

#### 2. Script de Configuración Automática

**`deploy.sh`**
```bash
#!/bin/bash

# Variables
DOMAIN="tu-dominio.com"
DB_PASSWORD="$(openssl rand -base64 32)"
APP_PATH="/var/www/snackshop"

echo "🚀 Iniciando despliegue de SnackShop..."

# Actualizar sistema
apt update && apt upgrade -y

# Instalar stack LEMP + PHP
apt install -y nginx mysql-server php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip php8.1-gd php8.1-sqlite3 git unzip

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Configurar MySQL
mysql -e "CREATE DATABASE snackshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER 'snackshop'@'localhost' IDENTIFIED BY '$DB_PASSWORD';"
mysql -e "GRANT ALL PRIVILEGES ON snackshop.* TO 'snackshop'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Descargar proyecto
mkdir -p $APP_PATH
cd $APP_PATH
git clone https://github.com/Equinoxe-Grammer/SnackShack.git .

# Configurar proyecto
composer install --no-dev --optimize-autoloader

# Crear configuración
cp .env.example .env
sed -i "s/SNACKSHOP_DB_PASS=/SNACKSHOP_DB_PASS=$DB_PASSWORD/" .env
sed -i "s/SNACKSHOP_DB_HOST=127.0.0.1/SNACKSHOP_DB_HOST=localhost/" .env

# Permisos
chown -R www-data:www-data $APP_PATH
chmod -R 755 $APP_PATH
chmod -R 775 $APP_PATH/data

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

# SSL automático
apt install -y certbot python3-certbot-nginx
certbot --nginx -d $DOMAIN -d www.$DOMAIN --non-interactive --agree-tos --email admin@$DOMAIN

echo "✅ Despliegue completado!"
echo "🔑 Password MySQL: $DB_PASSWORD"
echo "🌐 Sitio: https://$DOMAIN"
```

```bash
# Ejecutar script
chmod +x deploy.sh
sudo ./deploy.sh
```

### AWS EC2

#### 1. Lanzar Instancia
- **AMI:** Ubuntu Server 22.04 LTS
- **Tipo:** t3.micro (para desarrollo) o t3.small (producción)
- **Security Group:** HTTP (80), HTTPS (443), SSH (22)

#### 2. Configuración con User Data
```bash
#!/bin/bash
# Script de bootstrap para EC2

# Logs de instalación
exec > >(tee /var/log/user-data.log|logger -t user-data -s 2>/dev/console) 2>&1

# Variables desde EC2 tags o parámetros
DOMAIN=$(curl -s http://169.254.169.254/latest/meta-data/tags/instance/Domain || echo "example.com")

# Ejecutar script de instalación
curl -sSL https://raw.githubusercontent.com/tu-usuario/snackshop-deploy/main/aws-deploy.sh | bash -s -- $DOMAIN
```

### Azure App Service

#### 1. Configuración Web App
- **Runtime:** PHP 8.1
- **OS:** Linux
- **Plan:** B1 Basic ($13/mes)

#### 2. Configuración de Despliegue
```yaml
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

## 🔧 Variables de Entorno

### Archivo `.env` Completo

```bash
# ==============================================
# SnackShop Configuration
# ==============================================

# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

# Database
SNACKSHOP_DB_HOST=localhost
SNACKSHOP_DB_PORT=3306
SNACKSHOP_DB_NAME=snackshop
SNACKSHOP_DB_USER=snackshop
SNACKSHOP_DB_PASS=secure_password_here
SNACKSHOP_DB_CHARSET=utf8mb4

# Session
SESSION_LIFETIME=7200
SESSION_SECURE=true
SESSION_HTTPONLY=true
SESSION_SAMESITE=Strict

# Security
CSRF_TOKEN_LIFETIME=3600
BCRYPT_ROUNDS=12

# File uploads
MAX_UPLOAD_SIZE=5242880  # 5MB
ALLOWED_IMAGE_TYPES=jpg,jpeg,png,gif,webp

# Logging
LOG_LEVEL=warning
LOG_FILE=/var/log/snackshop/app.log

# Cache
CACHE_DRIVER=file  # file, redis, memcached
CACHE_TTL=3600

# Email (opcional)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@tu-dominio.com
MAIL_FROM_NAME="SnackShop"

# Backup
BACKUP_ENABLED=true
BACKUP_SCHEDULE="0 2 * * *"  # Diario a las 2 AM
BACKUP_RETENTION_DAYS=30
BACKUP_PATH=/var/backups/snackshop

# Performance
ENABLE_GZIP=true
ENABLE_CACHE_HEADERS=true
```

### Variables por Entorno

#### Desarrollo
```bash
APP_ENV=development
APP_DEBUG=true
SNACKSHOP_DB_HOST=127.0.0.1
SESSION_SECURE=false
LOG_LEVEL=debug
```

#### Staging
```bash
APP_ENV=staging
APP_DEBUG=true
SNACKSHOP_DB_HOST=staging-db.interno
SESSION_SECURE=true
LOG_LEVEL=info
```

#### Producción
```bash
APP_ENV=production
APP_DEBUG=false
SNACKSHOP_DB_HOST=prod-db.interno
SESSION_SECURE=true
LOG_LEVEL=warning
```

### Gestión de Variables

#### Con Docker
```yaml
# docker-compose.yml
environment:
  - APP_ENV=${APP_ENV:-production}
  - SNACKSHOP_DB_HOST=${DB_HOST}
  - SNACKSHOP_DB_PASS=${DB_PASS}
env_file:
  - .env.production
```

#### Con Systemd (VPS)
```ini
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

## 🔒 SSL y Seguridad

### Certificados SSL

#### Let's Encrypt (Gratuito)
```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx

# Obtener certificado
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com

# Verificar renovación
sudo certbot renew --dry-run

# Cron para renovación automática
echo "0 12 * * * /usr/bin/certbot renew --quiet" | sudo crontab -
```

#### Certificado Comercial
```bash
# Generar CSR
openssl req -new -newkey rsa:2048 -nodes -keyout tu-dominio.key -out tu-dominio.csr

# Configurar en Nginx
ssl_certificate /etc/ssl/certs/tu-dominio.crt;
ssl_certificate_key /etc/ssl/private/tu-dominio.key;
```

### Configuración de Seguridad

#### Nginx Security Headers
```nginx
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

#### Firewall (UFW)
```bash
# Configurar firewall básico
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw enable

# Ver estado
sudo ufw status verbose
```

#### Fail2Ban
```bash
# Instalar
sudo apt install fail2ban

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

## 📊 Monitoreo y Logs

### Configuración de Logs

#### Nginx Logs
```nginx
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

#### PHP Logs
```ini
; /etc/php/8.1/fpm/php.ini
log_errors = On
error_log = /var/log/php/error.log
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
```

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

### Monitoreo con Scripts

#### Script de Salud del Sistema
```bash
#!/bin/bash
# /usr/local/bin/health-check.sh

# Variables
DOMAIN="tu-dominio.com"
LOG_FILE="/var/log/snackshop/health.log"
EMAIL="admin@tu-dominio.com"

# Función de log
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" >> $LOG_FILE
}

# Verificar servicio web
if ! curl -f -s "https://$DOMAIN" > /dev/null; then
    log "ERROR: Web service down"
    echo "SnackShop web service is down" | mail -s "ALERT: Service Down" $EMAIL
fi

# Verificar base de datos
if ! mysqladmin ping -h localhost > /dev/null 2>&1; then
    log "ERROR: Database down"
    echo "MySQL database is down" | mail -s "ALERT: Database Down" $EMAIL
fi

# Verificar espacio en disco
DISK_USAGE=$(df / | awk 'NR==2 {print $5}' | sed 's/%//')
if [ $DISK_USAGE -gt 80 ]; then
    log "WARNING: Disk usage at ${DISK_USAGE}%"
    echo "Disk usage is at ${DISK_USAGE}%" | mail -s "WARNING: High Disk Usage" $EMAIL
fi

# Verificar memoria
MEM_USAGE=$(free | awk 'NR==2{printf "%.2f", $3*100/$2}')
if (( $(echo "$MEM_USAGE > 90" | bc -l) )); then
    log "WARNING: Memory usage at ${MEM_USAGE}%"
fi

log "Health check completed"
```

```bash
# Programar en crontab
crontab -e
# Añadir: */5 * * * * /usr/local/bin/health-check.sh
```

### Monitoreo Avanzado

#### Prometheus + Grafana (Opcional)
```yaml
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

#### Uptime Monitoring
```bash
# Script simple de uptime
#!/bin/bash
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

## 💾 Backup Automatizado

### Script de Backup Completo

```bash
#!/bin/bash
# /usr/local/bin/backup-snackshop.sh

# Configuración
BACKUP_DIR="/var/backups/snackshop"
APP_DIR="/var/www/snackshop"
DB_NAME="snackshop"
DB_USER="snackshop"
DB_PASS="password_aqui"
RETENTION_DAYS=30
DATE=$(date +%Y%m%d_%H%M%S)

# Crear directorio de backup
mkdir -p $BACKUP_DIR

# Backup de base de datos
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup de archivos de aplicación
tar -czf $BACKUP_DIR/app_$DATE.tar.gz -C $APP_DIR \\
    --exclude='vendor' \\
    --exclude='node_modules' \\
    --exclude='.git' \\
    .

# Backup de archivos subidos (data)
tar -czf $BACKUP_DIR/data_$DATE.tar.gz $APP_DIR/data/

# Limpiar backups antiguos
find $BACKUP_DIR -name "*.gz" -mtime +$RETENTION_DAYS -delete

# Log del backup
echo "[$(date)] Backup completed: db_$DATE.sql.gz, app_$DATE.tar.gz, data_$DATE.tar.gz" >> /var/log/snackshop/backup.log

# Verificar integridad
if [ -f "$BACKUP_DIR/db_$DATE.sql.gz" ] && [ -f "$BACKUP_DIR/app_$DATE.tar.gz" ]; then
    echo "[$(date)] Backup verification: SUCCESS" >> /var/log/snackshop/backup.log
else
    echo "[$(date)] Backup verification: FAILED" >> /var/log/snackshop/backup.log
    echo "Backup failed for SnackShop" | mail -s "BACKUP FAILED" admin@tu-dominio.com
fi
```

### Backup Remoto (S3/Cloud)

```bash
#!/bin/bash
# Backup con sincronización a AWS S3

# Instalar AWS CLI
# apt install awscli

# Configurar credenciales
# aws configure

# Sincronizar backups a S3
aws s3 sync $BACKUP_DIR s3://tu-bucket-backup/snackshop/ --delete

# Log de sincronización
echo "[$(date)] Remote backup sync completed" >> /var/log/snackshop/backup.log
```

### Programación de Backups

```bash
# Crontab para backups automáticos
crontab -e

# Backup diario a las 2 AM
0 2 * * * /usr/local/bin/backup-snackshop.sh

# Backup de logs semanales (domingos a las 3 AM)
0 3 * * 0 tar -czf /var/backups/logs_$(date +\%Y\%m\%d).tar.gz /var/log/nginx/ /var/log/snackshop/

# Verificación de espacio en disco diario
0 6 * * * df -h | mail -s "Disk Space Report" admin@tu-dominio.com
```

---

## 🚨 Troubleshooting

### Problemas Comunes

#### 1. Error 500 - Internal Server Error

**Diagnóstico:**
```bash
# Verificar logs de error
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/php8.1-fpm.log

# Verificar permisos
ls -la /var/www/snackshop/
ls -la /var/www/snackshop/data/

# Verificar configuración PHP
php --ini
php -m | grep -E "(pdo|mysql)"
```

**Soluciones:**
```bash
# Corregir permisos
sudo chown -R www-data:www-data /var/www/snackshop
sudo chmod -R 755 /var/www/snackshop
sudo chmod -R 775 /var/www/snackshop/data

# Verificar configuración Nginx
sudo nginx -t

# Reiniciar servicios
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
```

#### 2. Error de Base de Datos

**Diagnóstico:**
```bash
# Verificar MySQL
sudo systemctl status mysql
sudo mysql -u snackshop -p

# Verificar conectividad
telnet localhost 3306

# Verificar logs MySQL
sudo tail -f /var/log/mysql/error.log
```

**Soluciones:**
```bash
# Reiniciar MySQL
sudo systemctl restart mysql

# Verificar configuración
sudo mysql_secure_installation

# Recuperar desde backup
gunzip < /var/backups/snackshop/db_latest.sql.gz | mysql -u snackshop -p snackshop
```

#### 3. Certificado SSL Expirado

**Diagnóstico:**
```bash
# Verificar certificado
openssl s_client -connect tu-dominio.com:443 -servername tu-dominio.com

# Verificar Certbot
sudo certbot certificates
```

**Solución:**
```bash
# Renovar certificado
sudo certbot renew
sudo systemctl reload nginx
```

#### 4. Alto Uso de CPU/RAM

**Diagnóstico:**
```bash
# Monitorear procesos
top
htop
ps aux --sort=-%cpu | head -10
ps aux --sort=-%mem | head -10

# Verificar conexiones
netstat -tuln
ss -tuln
```

**Soluciones:**
```bash
# Optimizar MySQL
sudo mysql_secure_installation
# Ajustar my.cnf para el tamaño del servidor

# Optimizar PHP-FPM
sudo nano /etc/php/8.1/fpm/pool.d/www.conf
# Ajustar pm.max_children, pm.start_servers, etc.

# Añadir swap si es necesario
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
```

### Scripts de Diagnóstico

#### `diagnostic.sh`
```bash
#!/bin/bash
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

## ✅ Checklist de Producción

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

## 🔗 Documentos Relacionados

- **[📖 Índice General](docs/INDEX.md)** — Navegación completa del manual
- **[🏗️ Architecture](ARCHITECTURE.md)** — Comprende la arquitectura antes del despliegue
- **[🗄️ Database](DATABASE.md)** — Configuración de base de datos
- **[🔌 API](API.md)** — Endpoints que deben funcionar después del despliegue
- **[🔧 Development](DEVELOPMENT.md)** — Setup para desarrolladores

---

## 📞 Soporte

**¿Problemas con el despliegue?**
- **Issues:** [GitHub Issues](https://github.com/Equinoxe-Grammer/SnackShack/issues) con etiqueta `deployment`
- **Backup:** Siempre mantén backups antes de cambios importantes
- **Testing:** Prueba en staging antes de desplegar a producción

---

**[📖 Índice](docs/INDEX.md)** | **[🏗️ Ver Arquitectura](ARCHITECTURE.md)** | **[🗄️ Ver Database](DATABASE.md)** | **[🔧 Ver Development](DEVELOPMENT.md)**