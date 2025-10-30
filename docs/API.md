# 🔌 SnackShop - Documentación de API

**🏠 Ubicación:** `API.md`
**📅 Última actualización:** 28 de octubre, 2025
**🎯 Propósito:** Documentación completa de todos los endpoints web y API JSON del sistema

---

## 🧭 Navegación

**[📖 Índice General](docs/INDEX.md)** | **[🏠 README](README.md)** | **[🏗️ Arquitectura](ARCHITECTURE.md)** | **[🗄️ Database](DATABASE.md)**

---

## 📋 Índice

- [Resumen de APIs](#resumen-de-apis)
- [Autenticación y Autorización](#autenticación-y-autorización)
- [Endpoints Web (Views)](#endpoints-web-views)
- [Endpoints JSON API](#endpoints-json-api)
- [Códigos de Estado HTTP](#códigos-de-estado-http)
- [Ejemplos de Integración](#ejemplos-de-integración)
- [Manejo de Errores](#manejo-de-errores)
- [Tokens CSRF](#tokens-csrf)
- [Rate Limiting](#rate-limiting)

---

## 🎯 Resumen de APIs

SnackShop proporciona dos tipos de interfaces:

### 🌐 **Web Routes** (Server-Side Rendering)

- **Propósito:** Interfaz web tradicional con formularios HTML
- **Respuesta:** HTML renderizado server-side
- **Uso:** Aplicación web principal para administradores y cajeros

### 🔌 **JSON API** (REST-like)

- **Propósito:** Endpoints para integraciones y AJAX
- **Respuesta:** JSON estructurado
- **Uso:** Integraciones externas, SPA frontend, mobile apps

---

## 🔐 Autenticación y Autorización

### Sistema de Autenticación

- **Método:** Session-based authentication
- **Cookie de sesión:** `PHPSESSID`
- **Variables de sesión:** `usuario_id`, `usuario`, `rol`

### Roles del Sistema

| Rol | Descripción | Permisos |
|-----|-------------|----------|
| **admin** | Administrador completo | CRUD productos, ver historial, gestionar usuarios |
| **cajero** | Operador de ventas | Procesar ventas, ver productos |

### Headers Requeridos

```http
Cookie: PHPSESSID=abc123...
```

### Middleware de Seguridad

- **`AuthMiddleware`** — verificación de sesión activa
- **`RoleMiddleware`** — control de acceso por rol
- **`CsrfMiddleware`** — protección contra CSRF en formularios

---

## 🌐 Endpoints Web (Views)

### Autenticación

#### `GET /` - Página de Login

- **Descripción:** Formulario de inicio de sesión
- **Autenticación:** No requerida
- **Respuesta:** HTML del formulario de login

#### `GET /login` - Formulario de Login

- **Descripción:** Alias para la página principal
- **Autenticación:** No requerida
- **Respuesta:** HTML del formulario de login

#### `POST /login` - Procesar Login

- **Descripción:** Autenticar usuario y crear sesión
- **Autenticación:** No requerida
- **Middleware:** `CsrfMiddleware`
- **Parámetros:**
```http
  Content-Type: application/x-www-form-urlencoded

  usuario=admin&password=secreto&csrf_token=abc123
```
- **Respuesta:**
  - **Éxito:** Redirect 302 a `/menu`
  - **Error:** HTML con mensaje de error

#### `GET /logout` - Cerrar Sesión

- **Descripción:** Destruir sesión y redirigir
- **Autenticación:** Requerida
- **Respuesta:** Redirect 302 a `/login`

---

### Dashboard y Navegación

#### `GET /menu` - Panel Principal

- **Descripción:** Dashboard administrativo
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Respuesta:** HTML con widgets de dashboard

#### `GET /dashboard` - Alias del Panel

- **Descripción:** Alias para `/menu`
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Respuesta:** HTML con dashboard

---

### Gestión de Productos

#### `GET /productos` - Lista de Productos

- **Descripción:** Catálogo completo de productos con costos calculados
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Respuesta:** HTML con tabla de productos

#### `GET /productos/nuevo` - Formulario Nuevo Producto

- **Descripción:** Formulario para crear producto
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Respuesta:** HTML con formulario

#### `POST /productos/guardar` - Crear Producto

- **Descripción:** Guardar nuevo producto
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Middleware:** `CsrfMiddleware`
- **Parámetros:**
```http
  Content-Type: application/x-www-form-urlencoded

  nombre=Hamburguesa&descripcion=Deliciosa hamburguesa&precio=15.50&categoria_id=1
```
- **Respuesta:** Redirect 302 a `/productos`

#### `GET /productos/editar/{id}` - Formulario Editar

- **Descripción:** Formulario pre-llenado para editar producto
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Parámetros de URL:** `{id}` - ID del producto
- **Respuesta:** HTML con formulario de edición

#### `POST /productos/actualizar/{id}` - Actualizar Producto

- **Descripción:** Actualizar producto existente
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Middleware:** `CsrfMiddleware`
- **Parámetros de URL:** `{id}` - ID del producto
- **Respuesta:** Redirect 302 a `/productos`

#### `POST /productos/eliminar/{id}` - Eliminar Producto

- **Descripción:** Soft delete del producto
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Middleware:** `CsrfMiddleware`
- **Parámetros de URL:** `{id}` - ID del producto
- **Respuesta:** Redirect 302 a `/productos`

---

### Gestión de Variantes

#### `GET /productos/{id}/variantes` - Lista de Variantes

- **Descripción:** Variantes del producto específico
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Parámetros de URL:** `{id}` - ID del producto
- **Respuesta:** HTML con tabla de variantes

#### `GET /productos/{id}/variantes/nueva` - Nueva Variante

- **Descripción:** Formulario para crear variante
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Parámetros de URL:** `{id}` - ID del producto
- **Respuesta:** HTML con formulario

#### `POST /productos/{id}/variantes/guardar` - Crear Variante

- **Descripción:** Guardar nueva variante
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Middleware:** `CsrfMiddleware`
- **Parámetros de URL:** `{id}` - ID del producto
- **Parámetros:**
```http
  nombre=Grande&precio=18.50&descripcion=Tamaño grande
```

#### `GET /productos/{id}/variantes/editar/{vid}` - Editar Variante

- **Descripción:** Formulario para editar variante
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Parámetros de URL:**
  - `{id}` - ID del producto
  - `{vid}` - ID de la variante

#### `POST /productos/{id}/variantes/actualizar/{vid}` - Actualizar Variante

- **Descripción:** Actualizar variante existente
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Middleware:** `CsrfMiddleware`
- **Parámetros de URL:**
  - `{id}` - ID del producto
  - `{vid}` - ID de la variante

#### `POST /productos/{id}/variantes/eliminar/{vid}` - Eliminar Variante

- **Descripción:** Eliminar variante
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Middleware:** `CsrfMiddleware`
- **Parámetros de URL:**
  - `{id}` - ID del producto
  - `{vid}` - ID de la variante

---

### Ventas

#### `GET /ventas` - Interfaz de Ventas

- **Descripción:** Punto de venta con catálogo y carrito
- **Autenticación:** Requerida
- **Autorización:** `admin`, `cajero`
- **Respuesta:** HTML con interfaz de POS

#### `GET /venta` - Alias de Ventas (Legacy)

- **Descripción:** Alias legacy para `/ventas`
- **Autenticación:** Requerida
- **Autorización:** `admin`, `cajero`
- **Respuesta:** Redirect 302 a `/ventas`

---

### Historial y Reportes

#### `GET /historial` - Historial de Ventas

- **Descripción:** Histórico completo de transacciones
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Respuesta:** HTML con tabla de ventas

#### `GET /agregarCajero` - Gestión de Usuarios

- **Descripción:** Lista y formulario para gestionar cajeros
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Respuesta:** HTML con gestión de usuarios

#### `POST /agregarCajero` - Crear Usuario

- **Descripción:** Crear nuevo usuario cajero
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Middleware:** `CsrfMiddleware`

---

## 🔌 Endpoints JSON API

### Dashboard y Datos Generales

#### `GET /api/dashboard` - Datos del Dashboard

- **Descripción:** Métricas y estadísticas para el dashboard
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Respuesta:**
```json
  {
    \"ventas_hoy\": 1250.50,
    \"productos_vendidos\": 45,
    \"transacciones\": 12,
    \"productos_activos\": 28
  }
```

#### `GET /api/productos` - Catálogo JSON

- **Descripción:** Lista de productos activos con variantes
- **Autenticación:** Requerida
- **Respuesta:**
```json
  [
    {
      \"id\": 1,
      \"nombre\": \"Hamburguesa Clásica\",
      \"descripcion\": \"Hamburguesa con carne, lechuga y tomate\",
      \"categoria_id\": 1,
      \"active\": true,
      \"variants\": [
        {
          \"id\": 1,
          \"nombre\": \"Normal\",
          \"price\": 15.50,
          \"description\": \"Tamaño normal\"
        }
      ]
    }
  ]
```

#### `GET /api/categorias` - Lista de Categorías

- **Descripción:** Categorías de productos disponibles
- **Autenticación:** Requerida
- **Respuesta:**
```json
  [
    {
      \"id\": 1,
      \"nombre\": \"Hamburguesas\",
      \"descripcion\": \"Hamburguesas y sandwiches\"
    }
  ]
```

#### `GET /api/metodos-pago` - Métodos de Pago

- **Descripción:** Métodos de pago disponibles
- **Autenticación:** Requerida
- **Respuesta:**
```json
  [
    {
      \"id\": 1,
      \"nombre\": \"Efectivo\",
      \"descripcion\": \"Pago en efectivo\"
    },
    {
      \"id\": 2,
      \"nombre\": \"Tarjeta\",
      \"descripcion\": \"Pago con tarjeta\"
    }
  ]
```

---

### Costos y Análisis

#### `GET /api/productos/{id}/costo` - Análisis de Costos

- **Descripción:** Desglose completo de costos de un producto
- **Autenticación:** Requerida
- **Parámetros de URL:** `{id}` - ID del producto
- **Respuesta:**
```json
  {
    \"producto_id\": 1,
    \"nombre_producto\": \"Hamburguesa Clásica\",
    \"precio_final\": 15.50,
    \"costo_neto\": 8.25,
    \"margen_bruto\": 7.25,
    \"porcentaje_margen\": 46.77,
    \"desglose_iva\": {
      \"precio_sin_iva\": 13.84,
      \"iva_calculado\": 1.66,
      \"tasa_iva\": 0.12
    },
    \"desglose_costos\": [
      {
        \"ingrediente\": \"Carne de res\",
        \"cantidad\": 120,
        \"unidad\": \"gramos\",
        \"costo_unitario\": 0.025,
        \"costo_total\": 3.00
      }
    ]
  }
```

#### `GET /api/ingredientes/costos` - Costos de Ingredientes

- **Descripción:** Lista de ingredientes con costos unitarios
- **Autenticación:** Requerida
- **Respuesta:**
```json
  [
    {
      \"id\": 1,
      \"nombre\": \"Carne de res\",
      \"unidad\": \"gramo\",
      \"costo_unitario\": 0.025,
      \"stock_actual\": 5000
    }
  ]
```

---

### Gestión de Imágenes

#### `GET /api/productos/{id}/imagen` - Obtener Imagen

- **Descripción:** Imagen BLOB del producto
- **Autenticación:** Requerida
- **Parámetros de URL:** `{id}` - ID del producto
- **Respuesta:** Binary image data
- **Content-Type:** `image/jpeg`, `image/png`, etc.

#### `POST /api/productos/{id}/imagen` - Subir Imagen

- **Descripción:** Subir imagen del producto
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Middleware:** `CsrfMiddleware`
- **Parámetros de URL:** `{id}` - ID del producto
- **Content-Type:** `multipart/form-data`
- **Parámetros:**
```http
  Content-Type: multipart/form-data

  imagen=@product.jpg&csrf_token=abc123
```
- **Respuesta:**
```json
  {
    \"success\": true,
    \"message\": \"Imagen subida correctamente\"
  }
```

---

### Transacciones de Venta

#### `POST /api/ventas` - Procesar Venta

- **Descripción:** Procesar una nueva venta con múltiples items
- **Autenticación:** Requerida
- **Autorización:** `admin`, `cajero`
- **Content-Type:** `application/json`
- **Parámetros:**
```json
  {
    \"items\": [
      {
        \"variant_id\": 1,
        \"quantity\": 2,
        \"price\": 15.50
      },
      {
        \"variant_id\": 3,
        \"quantity\": 1,
        \"price\": 8.00
      }
    ],
    \"payment_method_id\": 1,
    \"total\": 39.00,
    \"notes\": \"Sin cebolla\"
  }
```
- **Respuesta:**
```json
  {
    \"success\": true,
    \"sale_id\": 123,
    \"total\": 39.00,
    \"items_count\": 3,
    \"timestamp\": \"2025-10-28T14:30:00Z\"
  }
```

---

### Historial y Reportes

#### `GET /api/ventas/historial` - Historial de Ventas

- **Descripción:** Lista paginada de ventas
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Query Parameters:**
  - `page` (opcional) - Número de página (default: 1)
  - `limit` (opcional) - Items por página (default: 50)
  - `date_from` (opcional) - Fecha desde (YYYY-MM-DD)
  - `date_to` (opcional) - Fecha hasta (YYYY-MM-DD)
- **Ejemplo:** `GET /api/ventas/historial?page=1&limit=20&date_from=2025-10-01`
- **Respuesta:**
```json
  {
    \"data\": [
      {
        \"id\": 123,
        \"total\": 39.00,
        \"items_count\": 3,
        \"payment_method\": \"Efectivo\",
        \"user\": \"admin\",
        \"created_at\": \"2025-10-28T14:30:00Z\"
      }
    ],
    \"pagination\": {
      \"current_page\": 1,
      \"total_pages\": 5,
      \"total_items\": 95,
      \"items_per_page\": 20
    }
  }
```

#### `GET /api/ventas/{id}` - Detalle de Venta

- **Descripción:** Detalle completo de una venta específica
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Parámetros de URL:** `{id}` - ID de la venta
- **Respuesta:**
```json
  {
    \"id\": 123,
    \"total\": 39.00,
    \"payment_method\": \"Efectivo\",
    \"user\": \"admin\",
    \"notes\": \"Sin cebolla\",
    \"created_at\": \"2025-10-28T14:30:00Z\",
    \"items\": [
      {
        \"variant_id\": 1,
        \"product_name\": \"Hamburguesa Clásica\",
        \"variant_name\": \"Normal\",
        \"quantity\": 2,
        \"unit_price\": 15.50,
        \"total_price\": 31.00
      }
    ]
  }
```

---

### Gestión de Usuarios

#### `GET /api/usuarios` - Lista de Usuarios

- **Descripción:** Lista de usuarios del sistema
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Respuesta:**
```json
  [
    {
      \"id\": 1,
      \"username\": \"admin\",
      \"role\": \"admin\",
      \"active\": true,
      \"created_at\": \"2025-01-15T09:00:00Z\"
    },
    {
      \"id\": 2,
      \"username\": \"cajero1\",
      \"role\": \"cajero\",
      \"active\": true,
      \"created_at\": \"2025-02-01T10:30:00Z\"
    }
  ]
```

---

## 📊 Códigos de Estado HTTP

| Código | Significado | Cuándo se usa |
|--------|-------------|---------------|
| **200** | OK | Respuesta exitosa con datos |
| **201** | Created | Recurso creado exitosamente |
| **302** | Found | Redirect (formularios web) |
| **400** | Bad Request | Parámetros inválidos o faltantes |
| **401** | Unauthorized | No autenticado (sesión expirada) |
| **403** | Forbidden | No autorizado para esta acción |
| **404** | Not Found | Recurso no encontrado |
| **422** | Unprocessable Entity | Errores de validación |
| **500** | Internal Server Error | Error del servidor |

---

## 🧪 Ejemplos de Integración

### Ejemplo 1: Autenticación y Obtener Productos

```bash
# 1. Login (web form)
curl -X POST 'http://localhost:8000/login' \\
  -H 'Content-Type: application/x-www-form-urlencoded' \\
  -d 'usuario=admin&password=secreto&csrf_token=abc123' \\
  -c cookies.txt

# 2. Obtener productos (JSON API)
curl -X GET 'http://localhost:8000/api/productos' \\
  -H 'Accept: application/json' \\
  -b cookies.txt
```

### Ejemplo 2: Procesar Venta

```bash
# Procesar venta con múltiples items
curl -X POST 'http://localhost:8000/api/ventas' \\
  -H 'Content-Type: application/json' \\
  -H 'Accept: application/json' \\
  -b cookies.txt \\
  -d '{
    \"items\": [
      {\"variant_id\": 1, \"quantity\": 2, \"price\": 15.50},
      {\"variant_id\": 3, \"quantity\": 1, \"price\": 8.00}
    ],
    \"payment_method_id\": 1,
    \"total\": 39.00,
    \"notes\": \"Pedido especial\"
  }'
```

### Ejemplo 3: Análisis de Costos

```bash
# Obtener análisis de costos de producto
curl -X GET 'http://localhost:8000/api/productos/1/costo' \\
  -H 'Accept: application/json' \\
  -b cookies.txt
```

### Ejemplo 4: Upload de Imagen

```bash
# Subir imagen de producto
curl -X POST 'http://localhost:8000/api/productos/1/imagen' \\
  -H 'Content-Type: multipart/form-data' \\
  -b cookies.txt \\
  -F 'imagen=@product.jpg' \\
  -F 'csrf_token=abc123'
```

---

## 🚨 Manejo de Errores

### Estructura de Respuestas de Error

#### Para APIs JSON:

```json
{
  \"error\": \"Descripción del error\",
  \"code\": \"INVALID_PRODUCT_ID\",
  \"details\": {
    \"field\": \"product_id\",
    \"received\": \"abc\",
    \"expected\": \"integer\"
  }
}
```

#### Para Formularios Web:

- **Redirect** a página de error con mensaje en session
- **Render** de vista con errores inline

### Errores Comunes

#### 401 - Sesión Expirada

```json
{
  \"error\": \"Sesión expirada\",
  \"code\": \"SESSION_EXPIRED\",
  \"redirect\": \"/login\"
}
```

#### 403 - Permisos Insuficientes

```json
{
  \"error\": \"No tiene permisos para esta acción\",
  \"code\": \"INSUFFICIENT_PERMISSIONS\",
  \"required_role\": \"admin\",
  \"current_role\": \"cajero\"
}
```

#### 404 - Producto No Encontrado

```json
{
  \"error\": \"Producto no encontrado\",
  \"code\": \"PRODUCT_NOT_FOUND\",
  \"product_id\": 999
}
```

#### 422 - Error de Validación

```json
{
  \"error\": \"Errores de validación\",
  \"code\": \"VALIDATION_ERROR\",
  \"errors\": {
    \"nombre\": [\"El nombre es requerido\"],
    \"precio\": [\"El precio debe ser mayor a 0\"]
  }
}
```

---

## 🛡️ Tokens CSRF

### Implementación

- **Middleware:** `CsrfMiddleware`
- **Métodos protegidos:** POST, PUT, DELETE
- **Token en formularios:** Campo oculto `csrf_token`

### Obtener Token CSRF

```php
// En vista PHP
echo '<input type=\"hidden\" name=\"csrf_token\" value=\"' . $_SESSION['csrf_token'] . '\">';
```

### Validación

```php
// El middleware valida automáticamente
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    // Error 403
}
```

---

## ⚡ Rate Limiting

### Estado Actual

- **No implementado** en la versión actual
- **Recomendación:** Implementar en nivel de servidor web (Nginx/Apache)

### Límites Sugeridos

- **Web routes:** 60 requests/minuto por IP
- **API routes:** 100 requests/minuto por sesión
- **Upload endpoints:** 10 requests/minuto por IP

---

## 🔗 Documentos Relacionados

- **[📖 Índice General](docs/INDEX.md)** — Navegación completa del manual
- **[🏗️ Architecture](ARCHITECTURE.md)** — Patrones y diseño del sistema
- **[🗄️ Database Schema](DATABASE.md)** — Esquema y relaciones de datos
- **[🚀 Deployment](DEPLOYMENT.md)** — Configuración en producción
- **[🔧 Development](DEVELOPMENT.md)** — Setup para desarrolladores

---

## 📞 Soporte

**¿Problemas con la API?**
- **Issues:** [GitHub Issues](https://github.com/Equinoxe-Grammer/SnackShack/issues) con etiqueta `api`
- **Documentación:** Este documento es la referencia oficial
- **Testing:** Usa herramientas como Postman o curl para probar endpoints

---

**[📖 Índice](docs/INDEX.md)** | **[🏗️ Ver Arquitectura](ARCHITECTURE.md)** | **[🗄️ Ver Database](DATABASE.md)** | **[🚀 Ver Deployment](DEPLOYMENT.md)**
