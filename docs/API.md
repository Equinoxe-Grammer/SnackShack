<a id="snackshop-documentacion-de-api"></a>
<a id="-snackshop-documentacion-de-api"></a>
# 🔌 SnackShop - Documentación de API
<!-- TOC -->
<a id="contenido"></a>
<a id="-contenido"></a>
## Contenido

- [🧭 Navegación](#-navegacion)
- [📋 Índice](#-indice)
- [🎯 Resumen de APIs](#-resumen-de-apis)
  - [🌐 **Web Routes** (Server-Side Rendering)](#-web-routes-server-side-rendering)
  - [🔌 **JSON API** (REST-like)](#-json-api-rest-like)
- [🔐 Autenticación y Autorización](#-autenticacion-y-autorizacion)
  - [Sistema de Autenticación](#sistema-de-autenticacion)
  - [Roles del Sistema](#roles-del-sistema)
  - [Headers Requeridos](#headers-requeridos)
  - [Middleware de Seguridad](#middleware-de-seguridad)
- [🌐 Endpoints Web (Views)](#-endpoints-web-views)
  - [Autenticación](#autenticacion)
  - [Dashboard y Navegación](#dashboard-y-navegacion)
  - [Gestión de Productos](#gestion-de-productos)
  - [Gestión de Variantes](#gestion-de-variantes)
  - [Ventas](#ventas)
  - [Historial y Reportes](#historial-y-reportes)
- [🔌 Endpoints JSON API](#-endpoints-json-api)
  - [Dashboard y Datos Generales](#dashboard-y-datos-generales)
  - [Costos y Análisis](#costos-y-analisis)
  - [Gestión de Imágenes](#gestion-de-imagenes)
  - [Transacciones de Venta](#transacciones-de-venta)
  - [Historial y Reportes](#historial-y-reportes)
  - [Gestión de Usuarios](#gestion-de-usuarios)
- [📊 Códigos de Estado HTTP](#-codigos-de-estado-http)
- [🧪 Ejemplos de Integración](#-ejemplos-de-integracion)
  - [Ejemplo 1: Autenticación y Obtener Productos](#ejemplo-1-autenticacion-y-obtener-productos)
  - [Ejemplo 2: Procesar Venta](#ejemplo-2-procesar-venta)
  - [Ejemplo 3: Análisis de Costos](#ejemplo-3-analisis-de-costos)
  - [Ejemplo 4: Upload de Imagen](#ejemplo-4-upload-de-imagen)
- [🚨 Manejo de Errores](#-manejo-de-errores)
  - [Estructura de Respuestas de Error](#estructura-de-respuestas-de-error)
  - [Errores Comunes](#errores-comunes)
- [🛡️ Tokens CSRF](#-tokens-csrf)
  - [Implementación](#implementacion)
  - [Obtener Token CSRF](#obtener-token-csrf)
  - [Validación](#validacion)
- [⚡ Rate Limiting](#-rate-limiting)
  - [Estado Actual](#estado-actual)
  - [Límites Sugeridos](#limites-sugeridos)
- [🔗 Documentos Relacionados](#-documentos-relacionados)
- [📞 Soporte](#-soporte)
<!-- /TOC -->

**🏠 Ubicación:** `API.md`
**📅 Última actualización:** 28 de octubre, 2025
**🎯 Propósito:** Documentación completa de todos los endpoints web y API JSON del sistema

---

<a id="navegacion"></a>
<a id="-navegacion"></a>
## 🧭 Navegación

**[📖 Índice General](docs/INDEX.md)** | **[🏠 README](README.md)** | **[🏗️ Arquitectura](ARCHITECTURE.md)** | **[🗄️ Database](DATABASE.md)**

---

<a id="indice"></a>
<a id="-indice"></a>
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

<a id="resumen-de-apis"></a>
<a id="-resumen-de-apis"></a>
## 🎯 Resumen de APIs

SnackShop proporciona dos tipos de interfaces:

<a id="web-routes-server-side-rendering"></a>
<a id="-web-routes-server-side-rendering"></a>
### 🌐 **Web Routes** (Server-Side Rendering)

- **Propósito:** Interfaz web tradicional con formularios HTML
- **Respuesta:** HTML renderizado server-side
- **Uso:** Aplicación web principal para administradores y cajeros

<a id="json-api-rest-like"></a>
<a id="-json-api-rest-like"></a>
### 🔌 **JSON API** (REST-like)

- **Propósito:** Endpoints para integraciones y AJAX
- **Respuesta:** JSON estructurado
- **Uso:** Integraciones externas, SPA frontend, mobile apps

---

<a id="autenticacion-y-autorizacion"></a>
<a id="-autenticacion-y-autorizacion"></a>
## 🔐 Autenticación y Autorización

<a id="sistema-de-autenticacion"></a>
<a id="-sistema-de-autenticacion"></a>
### Sistema de Autenticación

- **Método:** Session-based authentication
- **Cookie de sesión:** `PHPSESSID`
- **Variables de sesión:** `usuario_id`, `usuario`, `rol`

<a id="roles-del-sistema"></a>
<a id="-roles-del-sistema"></a>
### Roles del Sistema

| Rol | Descripción | Permisos |
|-----|-------------|----------|
| **admin** | Administrador completo | CRUD productos, ver historial, gestionar usuarios |
| **cajero** | Operador de ventas | Procesar ventas, ver productos |

<a id="headers-requeridos"></a>
<a id="-headers-requeridos"></a>
### Headers Requeridos

```http
Cookie: PHPSESSID=abc123...
```

<a id="middleware-de-seguridad"></a>
<a id="-middleware-de-seguridad"></a>
### Middleware de Seguridad

- **`AuthMiddleware`** — verificación de sesión activa
- **`RoleMiddleware`** — control de acceso por rol
- **`CsrfMiddleware`** — protección contra CSRF en formularios

---

<a id="endpoints-web-views"></a>
<a id="-endpoints-web-views"></a>
## 🌐 Endpoints Web (Views)

<a id="autenticacion"></a>
<a id="-autenticacion"></a>
### Autenticación

<a id="get-pagina-de-login"></a>
<a id="-get-pagina-de-login"></a>
#### `GET /` - Página de Login

- **Descripción:** Formulario de inicio de sesión
- **Autenticación:** No requerida
- **Respuesta:** HTML del formulario de login

<a id="get-login-formulario-de-login"></a>
<a id="-get-login-formulario-de-login"></a>
#### `GET /login` - Formulario de Login

- **Descripción:** Alias para la página principal
- **Autenticación:** No requerida
- **Respuesta:** HTML del formulario de login

<a id="post-login-procesar-login"></a>
<a id="-post-login-procesar-login"></a>
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

<a id="get-logout-cerrar-sesion"></a>
<a id="-get-logout-cerrar-sesion"></a>
#### `GET /logout` - Cerrar Sesión

- **Descripción:** Destruir sesión y redirigir
- **Autenticación:** Requerida
- **Respuesta:** Redirect 302 a `/login`

---

<a id="dashboard-y-navegacion"></a>
<a id="-dashboard-y-navegacion"></a>
### Dashboard y Navegación

<a id="get-menu-panel-principal"></a>
<a id="-get-menu-panel-principal"></a>
#### `GET /menu` - Panel Principal

- **Descripción:** Dashboard administrativo
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Respuesta:** HTML con widgets de dashboard

<a id="get-dashboard-alias-del-panel"></a>
<a id="-get-dashboard-alias-del-panel"></a>
#### `GET /dashboard` - Alias del Panel

- **Descripción:** Alias para `/menu`
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Respuesta:** HTML con dashboard

---

<a id="gestion-de-productos"></a>
<a id="-gestion-de-productos"></a>
### Gestión de Productos

<a id="get-productos-lista-de-productos"></a>
<a id="-get-productos-lista-de-productos"></a>
#### `GET /productos` - Lista de Productos

- **Descripción:** Catálogo completo de productos con costos calculados
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Respuesta:** HTML con tabla de productos

<a id="get-productosnuevo-formulario-nuevo-producto"></a>
<a id="-get-productosnuevo-formulario-nuevo-producto"></a>
#### `GET /productos/nuevo` - Formulario Nuevo Producto

- **Descripción:** Formulario para crear producto
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Respuesta:** HTML con formulario

<a id="post-productosguardar-crear-producto"></a>
<a id="-post-productosguardar-crear-producto"></a>
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

<a id="get-productoseditarid-formulario-editar"></a>
<a id="-get-productoseditarid-formulario-editar"></a>
#### `GET /productos/editar/{id}` - Formulario Editar

- **Descripción:** Formulario pre-llenado para editar producto
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Parámetros de URL:** `{id}` - ID del producto
- **Respuesta:** HTML con formulario de edición

<a id="post-productosactualizarid-actualizar-producto"></a>
<a id="-post-productosactualizarid-actualizar-producto"></a>
#### `POST /productos/actualizar/{id}` - Actualizar Producto

- **Descripción:** Actualizar producto existente
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Middleware:** `CsrfMiddleware`
- **Parámetros de URL:** `{id}` - ID del producto
- **Respuesta:** Redirect 302 a `/productos`

<a id="post-productoseliminarid-eliminar-producto"></a>
<a id="-post-productoseliminarid-eliminar-producto"></a>
#### `POST /productos/eliminar/{id}` - Eliminar Producto

- **Descripción:** Soft delete del producto
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Middleware:** `CsrfMiddleware`
- **Parámetros de URL:** `{id}` - ID del producto
- **Respuesta:** Redirect 302 a `/productos`

---

<a id="gestion-de-variantes"></a>
<a id="-gestion-de-variantes"></a>
### Gestión de Variantes

<a id="get-productosidvariantes-lista-de-variantes"></a>
<a id="-get-productosidvariantes-lista-de-variantes"></a>
#### `GET /productos/{id}/variantes` - Lista de Variantes

- **Descripción:** Variantes del producto específico
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Parámetros de URL:** `{id}` - ID del producto
- **Respuesta:** HTML con tabla de variantes

<a id="get-productosidvariantesnueva-nueva-variante"></a>
<a id="-get-productosidvariantesnueva-nueva-variante"></a>
#### `GET /productos/{id}/variantes/nueva` - Nueva Variante

- **Descripción:** Formulario para crear variante
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Parámetros de URL:** `{id}` - ID del producto
- **Respuesta:** HTML con formulario

<a id="post-productosidvariantesguardar-crear-variante"></a>
<a id="-post-productosidvariantesguardar-crear-variante"></a>
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

<a id="get-productosidvarianteseditarvid-editar-variante"></a>
<a id="-get-productosidvarianteseditarvid-editar-variante"></a>
#### `GET /productos/{id}/variantes/editar/{vid}` - Editar Variante

- **Descripción:** Formulario para editar variante
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Parámetros de URL:**
  - `{id}` - ID del producto
  - `{vid}` - ID de la variante

<a id="post-productosidvariantesactualizarvid-actualizar-variante"></a>
<a id="-post-productosidvariantesactualizarvid-actualizar-variante"></a>
#### `POST /productos/{id}/variantes/actualizar/{vid}` - Actualizar Variante

- **Descripción:** Actualizar variante existente
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Middleware:** `CsrfMiddleware`
- **Parámetros de URL:**
  - `{id}` - ID del producto
  - `{vid}` - ID de la variante

<a id="post-productosidvarianteseliminarvid-eliminar-variante"></a>
<a id="-post-productosidvarianteseliminarvid-eliminar-variante"></a>
#### `POST /productos/{id}/variantes/eliminar/{vid}` - Eliminar Variante

- **Descripción:** Eliminar variante
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Middleware:** `CsrfMiddleware`
- **Parámetros de URL:**
  - `{id}` - ID del producto
  - `{vid}` - ID de la variante

---

<a id="ventas"></a>
<a id="-ventas"></a>
### Ventas

<a id="get-ventas-interfaz-de-ventas"></a>
<a id="-get-ventas-interfaz-de-ventas"></a>
#### `GET /ventas` - Interfaz de Ventas

- **Descripción:** Punto de venta con catálogo y carrito
- **Autenticación:** Requerida
- **Autorización:** `admin`, `cajero`
- **Respuesta:** HTML con interfaz de POS

<a id="get-venta-alias-de-ventas-legacy"></a>
<a id="-get-venta-alias-de-ventas-legacy"></a>
#### `GET /venta` - Alias de Ventas (Legacy)

- **Descripción:** Alias legacy para `/ventas`
- **Autenticación:** Requerida
- **Autorización:** `admin`, `cajero`
- **Respuesta:** Redirect 302 a `/ventas`

---

<a id="historial-y-reportes"></a>
<a id="-historial-y-reportes"></a>
### Historial y Reportes

<a id="get-historial-historial-de-ventas"></a>
<a id="-get-historial-historial-de-ventas"></a>
#### `GET /historial` - Historial de Ventas

- **Descripción:** Histórico completo de transacciones
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Respuesta:** HTML con tabla de ventas

<a id="get-agregarcajero-gestion-de-usuarios"></a>
<a id="-get-agregarcajero-gestion-de-usuarios"></a>
#### `GET /agregarCajero` - Gestión de Usuarios

- **Descripción:** Lista y formulario para gestionar cajeros
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Respuesta:** HTML con gestión de usuarios

<a id="post-agregarcajero-crear-usuario"></a>
<a id="-post-agregarcajero-crear-usuario"></a>
#### `POST /agregarCajero` - Crear Usuario

- **Descripción:** Crear nuevo usuario cajero
- **Autenticación:** Requerida
- **Autorización:** `admin`
- **Middleware:** `CsrfMiddleware`

---

<a id="endpoints-json-api"></a>
<a id="-endpoints-json-api"></a>
## 🔌 Endpoints JSON API

<a id="dashboard-y-datos-generales"></a>
<a id="-dashboard-y-datos-generales"></a>
### Dashboard y Datos Generales

<a id="get-apidashboard-datos-del-dashboard"></a>
<a id="-get-apidashboard-datos-del-dashboard"></a>
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

<a id="get-apiproductos-catalogo-json"></a>
<a id="-get-apiproductos-catalogo-json"></a>
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

<a id="get-apicategorias-lista-de-categorias"></a>
<a id="-get-apicategorias-lista-de-categorias"></a>
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

<a id="get-apimetodos-pago-metodos-de-pago"></a>
<a id="-get-apimetodos-pago-metodos-de-pago"></a>
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

<a id="costos-y-analisis"></a>
<a id="-costos-y-analisis"></a>
### Costos y Análisis

<a id="get-apiproductosidcosto-analisis-de-costos"></a>
<a id="-get-apiproductosidcosto-analisis-de-costos"></a>
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

<a id="get-apiingredientescostos-costos-de-ingredientes"></a>
<a id="-get-apiingredientescostos-costos-de-ingredientes"></a>
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

<a id="gestion-de-imagenes"></a>
<a id="-gestion-de-imagenes"></a>
### Gestión de Imágenes

<a id="get-apiproductosidimagen-obtener-imagen"></a>
<a id="-get-apiproductosidimagen-obtener-imagen"></a>
#### `GET /api/productos/{id}/imagen` - Obtener Imagen

- **Descripción:** Imagen BLOB del producto
- **Autenticación:** Requerida
- **Parámetros de URL:** `{id}` - ID del producto
- **Respuesta:** Binary image data
- **Content-Type:** `image/jpeg`, `image/png`, etc.

<a id="post-apiproductosidimagen-subir-imagen"></a>
<a id="-post-apiproductosidimagen-subir-imagen"></a>
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

<a id="transacciones-de-venta"></a>
<a id="-transacciones-de-venta"></a>
### Transacciones de Venta

<a id="post-apiventas-procesar-venta"></a>
<a id="-post-apiventas-procesar-venta"></a>
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

<a id="historial-y-reportes"></a>
<a id="-historial-y-reportes"></a>
### Historial y Reportes

<a id="get-apiventashistorial-historial-de-ventas"></a>
<a id="-get-apiventashistorial-historial-de-ventas"></a>
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

<a id="get-apiventasid-detalle-de-venta"></a>
<a id="-get-apiventasid-detalle-de-venta"></a>
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

<a id="gestion-de-usuarios"></a>
<a id="-gestion-de-usuarios"></a>
### Gestión de Usuarios

<a id="get-apiusuarios-lista-de-usuarios"></a>
<a id="-get-apiusuarios-lista-de-usuarios"></a>
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

<a id="codigos-de-estado-http"></a>
<a id="-codigos-de-estado-http"></a>
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

<a id="ejemplos-de-integracion"></a>
<a id="-ejemplos-de-integracion"></a>
## 🧪 Ejemplos de Integración

<a id="ejemplo-1-autenticacion-y-obtener-productos"></a>
<a id="-ejemplo-1-autenticacion-y-obtener-productos"></a>
### Ejemplo 1: Autenticación y Obtener Productos

```bash
<a id="1-login-web-form"></a>
<a id="-1-login-web-form"></a>
# 1. Login (web form)
curl -X POST 'http://localhost:8000/login' \\
  -H 'Content-Type: application/x-www-form-urlencoded' \\
  -d 'usuario=admin&password=secreto&csrf_token=abc123' \\
  -c cookies.txt

<a id="2-obtener-productos-json-api"></a>
<a id="-2-obtener-productos-json-api"></a>
# 2. Obtener productos (JSON API)
curl -X GET 'http://localhost:8000/api/productos' \\
  -H 'Accept: application/json' \\
  -b cookies.txt
```

<a id="ejemplo-2-procesar-venta"></a>
<a id="-ejemplo-2-procesar-venta"></a>
### Ejemplo 2: Procesar Venta

```bash
<a id="procesar-venta-con-multiples-items"></a>
<a id="-procesar-venta-con-multiples-items"></a>
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

<a id="ejemplo-3-analisis-de-costos"></a>
<a id="-ejemplo-3-analisis-de-costos"></a>
### Ejemplo 3: Análisis de Costos

```bash
<a id="obtener-analisis-de-costos-de-producto"></a>
<a id="-obtener-analisis-de-costos-de-producto"></a>
# Obtener análisis de costos de producto
curl -X GET 'http://localhost:8000/api/productos/1/costo' \\
  -H 'Accept: application/json' \\
  -b cookies.txt
```

<a id="ejemplo-4-upload-de-imagen"></a>
<a id="-ejemplo-4-upload-de-imagen"></a>
### Ejemplo 4: Upload de Imagen

```bash
<a id="subir-imagen-de-producto"></a>
<a id="-subir-imagen-de-producto"></a>
# Subir imagen de producto
curl -X POST 'http://localhost:8000/api/productos/1/imagen' \\
  -H 'Content-Type: multipart/form-data' \\
  -b cookies.txt \\
  -F 'imagen=@product.jpg' \\
  -F 'csrf_token=abc123'
```

---

<a id="manejo-de-errores"></a>
<a id="-manejo-de-errores"></a>
## 🚨 Manejo de Errores

<a id="estructura-de-respuestas-de-error"></a>
<a id="-estructura-de-respuestas-de-error"></a>
### Estructura de Respuestas de Error

<a id="para-apis-json"></a>
<a id="-para-apis-json"></a>
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

<a id="para-formularios-web"></a>
<a id="-para-formularios-web"></a>
#### Para Formularios Web:

- **Redirect** a página de error con mensaje en session
- **Render** de vista con errores inline

<a id="errores-comunes"></a>
<a id="-errores-comunes"></a>
### Errores Comunes

<a id="401-sesion-expirada"></a>
<a id="-401-sesion-expirada"></a>
#### 401 - Sesión Expirada

```json
{
  \"error\": \"Sesión expirada\",
  \"code\": \"SESSION_EXPIRED\",
  \"redirect\": \"/login\"
}
```

<a id="403-permisos-insuficientes"></a>
<a id="-403-permisos-insuficientes"></a>
#### 403 - Permisos Insuficientes

```json
{
  \"error\": \"No tiene permisos para esta acción\",
  \"code\": \"INSUFFICIENT_PERMISSIONS\",
  \"required_role\": \"admin\",
  \"current_role\": \"cajero\"
}
```

<a id="404-producto-no-encontrado"></a>
<a id="-404-producto-no-encontrado"></a>
#### 404 - Producto No Encontrado

```json
{
  \"error\": \"Producto no encontrado\",
  \"code\": \"PRODUCT_NOT_FOUND\",
  \"product_id\": 999
}
```

<a id="422-error-de-validacion"></a>
<a id="-422-error-de-validacion"></a>
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

<a id="tokens-csrf"></a>
<a id="-tokens-csrf"></a>
## 🛡️ Tokens CSRF

<a id="implementacion"></a>
<a id="-implementacion"></a>
### Implementación

- **Middleware:** `CsrfMiddleware`
- **Métodos protegidos:** POST, PUT, DELETE
- **Token en formularios:** Campo oculto `csrf_token`

<a id="obtener-token-csrf"></a>
<a id="-obtener-token-csrf"></a>
### Obtener Token CSRF

```php
// En vista PHP
echo '<input type=\"hidden\" name=\"csrf_token\" value=\"' . $_SESSION['csrf_token'] . '\">';
```

<a id="validacion"></a>
<a id="-validacion"></a>
### Validación

```php
// El middleware valida automáticamente
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    // Error 403
}
```

---

<a id="rate-limiting"></a>
<a id="-rate-limiting"></a>
## ⚡ Rate Limiting

<a id="estado-actual"></a>
<a id="-estado-actual"></a>
### Estado Actual

- **No implementado** en la versión actual
- **Recomendación:** Implementar en nivel de servidor web (Nginx/Apache)

<a id="limites-sugeridos"></a>
<a id="-limites-sugeridos"></a>
### Límites Sugeridos

- **Web routes:** 60 requests/minuto por IP
- **API routes:** 100 requests/minuto por sesión
- **Upload endpoints:** 10 requests/minuto por IP

---

<a id="documentos-relacionados"></a>
<a id="-documentos-relacionados"></a>
## 🔗 Documentos Relacionados

- **[📖 Índice General](docs/INDEX.md)** — Navegación completa del manual
- **[🏗️ Architecture](ARCHITECTURE.md)** — Patrones y diseño del sistema
- **[🗄️ Database Schema](DATABASE.md)** — Esquema y relaciones de datos
- **[🚀 Deployment](DEPLOYMENT.md)** — Configuración en producción
- **[🔧 Development](DEVELOPMENT.md)** — Setup para desarrolladores

---

<a id="soporte"></a>
<a id="-soporte"></a>
## 📞 Soporte

**¿Problemas con la API?**
- **Issues:** [GitHub Issues](https://github.com/Equinoxe-Grammer/SnackShack/issues) con etiqueta `api`
- **Documentación:** Este documento es la referencia oficial
- **Testing:** Usa herramientas como Postman o curl para probar endpoints

---

**[📖 Índice](docs/INDEX.md)** | **[🏗️ Ver Arquitectura](ARCHITECTURE.md)** | **[🗄️ Ver Database](DATABASE.md)** | **[🚀 Ver Deployment](DEPLOYMENT.md)**
