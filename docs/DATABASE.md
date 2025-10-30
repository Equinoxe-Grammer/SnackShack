<a id="snackshop-documentacion-de-base-de-datos"></a>
<a id="-snackshop-documentacion-de-base-de-datos"></a>
# 🗄️ SnackShop - Documentación de Base de Datos
<!-- TOC -->
<a id="contenido"></a>
<a id="-contenido"></a>
## Contenido

- [🧭 Navegación](#-navegacion)
- [📋 Índice](#-indice)
- [🎯 Resumen del Sistema de Datos](#-resumen-del-sistema-de-datos)
  - [Características Principales](#caracteristicas-principales)
  - [Entidades Principales](#entidades-principales)
- [⚙️ Configuración de Base de Datos](#-configuracion-de-base-de-datos)
  - [SQLite (Desarrollo)](#sqlite-desarrollo)
  - [MySQL (Producción)](#mysql-produccion)
  - [Variables de Entorno](#variables-de-entorno)
- [📊 Esquema de Tablas](#-esquema-de-tablas)
  - [👥 Tabla: `usuarios`](#-tabla-usuarios)
  - [🏷️ Tabla: `categorias`](#-tabla-categorias)
  - [🍔 Tabla: `productos`](#-tabla-productos)
  - [📦 Tabla: `variantes`](#-tabla-variantes)
  - [💳 Tabla: `metodos_de_pago`](#-tabla-metodos_de_pago)
  - [🛒 Tabla: `ventas`](#-tabla-ventas)
  - [🧾 Tabla: `detalle_ventas`](#-tabla-detalle_ventas)
  - [🥬 Tabla: `ingredientes`](#-tabla-ingredientes)
  - [🛍️ Tabla: `compras`](#-tabla-compras)
  - [🧮 Tabla: `recetas` (Opcional - Para Costeo)](#-tabla-recetas-opcional-para-costeo)
- [🔗 Diagrama de Relaciones](#-diagrama-de-relaciones)
  - [ERD (Entidad-Relación)](#erd-entidad-relacion)
  - [Relaciones Principales](#relaciones-principales)
- [🔍 Queries Importantes](#-queries-importantes)
  - [Consultas de Ventas](#consultas-de-ventas)
  - [Consultas de Inventario y Costeo](#consultas-de-inventario-y-costeo)
  - [Consultas de Análisis](#consultas-de-analisis)
  - [Consultas de Integridad](#consultas-de-integridad)
- [🔄 Migraciones y Versionado](#-migraciones-y-versionado)
  - [Estado Actual](#estado-actual)
  - [Propuesta de Sistema de Migraciones](#propuesta-de-sistema-de-migraciones)
  - [Esquema Inicial para Nueva Instalación](#esquema-inicial-para-nueva-instalacion)
- [🌱 Seeds y Datos de Prueba](#-seeds-y-datos-de-prueba)
  - [Datos de Demostración](#datos-de-demostracion)
  - [Script de Seeding](#script-de-seeding)
- [⚡ Índices y Performance](#-indices-y-performance)
  - [Índices Recomendados](#indices-recomendados)
  - [Optimizaciones SQLite](#optimizaciones-sqlite)
  - [Queries de Monitoreo](#queries-de-monitoreo)
- [💾 Backup y Restauración](#-backup-y-restauracion)
  - [Backup SQLite](#backup-sqlite)
  - [Backup MySQL](#backup-mysql)
  - [Restauración](#restauracion)
  - [Estrategia de Backup Recomendada](#estrategia-de-backup-recomendada)
- [🚨 Troubleshooting](#-troubleshooting)
  - [Problemas Comunes](#problemas-comunes)
  - [Herramientas de Diagnóstico](#herramientas-de-diagnostico)
- [🔗 Documentos Relacionados](#-documentos-relacionados)
- [📞 Soporte](#-soporte)
<!-- /TOC -->

**🏠 Ubicación:** `DATABASE.md`
**📅 Última actualización:** 28 de octubre, 2025
**🎯 Propósito:** Esquema completo, relaciones, queries y estrategias de gestión de datos

---

<a id="navegacion"></a>
<a id="-navegacion"></a>
## 🧭 Navegación

**[📖 Índice General](docs/INDEX.md)** | **[🏠 README](README.md)** | **[🏗️ Arquitectura](ARCHITECTURE.md)** | **[🔌 API](API.md)**

---

<a id="indice"></a>
<a id="-indice"></a>
## 📋 Índice

- [Resumen del Sistema de Datos](#resumen-del-sistema-de-datos)
- [Configuración de Base de Datos](#configuración-de-base-de-datos)
- [Esquema de Tablas](#esquema-de-tablas)
- [Diagrama de Relaciones](#diagrama-de-relaciones)
- [Queries Importantes](#queries-importantes)
- [Migraciones y Versionado](#migraciones-y-versionado)
- [Seeds y Datos de Prueba](#seeds-y-datos-de-prueba)
- [Índices y Performance](#índices-y-performance)
- [Backup y Restauración](#backup-y-restauración)
- [Troubleshooting](#troubleshooting)

---

<a id="resumen-del-sistema-de-datos"></a>
<a id="-resumen-del-sistema-de-datos"></a>
## 🎯 Resumen del Sistema de Datos

<a id="caracteristicas-principales"></a>
<a id="-caracteristicas-principales"></a>
### Características Principales

- **Base de datos:** SQLite (desarrollo) / MySQL (producción)
- **Acceso:** PDO con prepared statements
- **Integridad:** Foreign keys habilitadas
- **Transacciones:** Soporte completo para operaciones atómicas
- **Ubicación:** `data/snackshop.db` (SQLite) o configuración MySQL

<a id="entidades-principales"></a>
<a id="-entidades-principales"></a>
### Entidades Principales

- **Productos** y sus **Variantes** (tallas, presentaciones)
- **Ventas** con **Detalle de Items** vendidos
- **Usuarios** con roles (admin/cajero)
- **Categorías** para organizar productos
- **Ingredientes** y **Compras** para costeo
- **Métodos de Pago**

---

<a id="configuracion-de-base-de-datos"></a>
<a id="-configuracion-de-base-de-datos"></a>
## ⚙️ Configuración de Base de Datos

<a id="sqlite-desarrollo"></a>
<a id="-sqlite-desarrollo"></a>
### SQLite (Desarrollo)

```php
// src/Database/Connection.php
$dbPath = dirname(__DIR__, 2) . '/data/snackshop.db';
$dsn = 'sqlite:' . $dbPath;

// Configuración automática
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// Foreign keys habilitadas
$pdo->exec('PRAGMA foreign_keys = ON');
```

<a id="mysql-produccion"></a>
<a id="-mysql-produccion"></a>
### MySQL (Producción)

```php
// src/Config/AppConfig.php
return [
    'host' => getenv('SNACKSHOP_DB_HOST') ?: '127.0.0.1',
    'name' => getenv('SNACKSHOP_DB_NAME') ?: 'snackshop',
    'user' => getenv('SNACKSHOP_DB_USER') ?: 'root',
    'pass' => getenv('SNACKSHOP_DB_PASS') ?: '',
    'charset' => 'utf8mb4'
];
```

<a id="variables-de-entorno"></a>
<a id="-variables-de-entorno"></a>
### Variables de Entorno

```bash
<a id="env"></a>
<a id="-env"></a>
# .env
SNACKSHOP_DB_HOST=127.0.0.1
SNACKSHOP_DB_NAME=snackshop
SNACKSHOP_DB_USER=snackshop_user
SNACKSHOP_DB_PASS=secure_password
```

---

<a id="esquema-de-tablas"></a>
<a id="-esquema-de-tablas"></a>
## 📊 Esquema de Tablas

<a id="tabla-usuarios"></a>
<a id="-tabla-usuarios"></a>
### 👥 Tabla: `usuarios`

```sql
CREATE TABLE usuarios (
    usuario_id INTEGER PRIMARY KEY AUTOINCREMENT,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena_hash VARCHAR(255) NOT NULL,
    rol VARCHAR(20) NOT NULL DEFAULT 'cajero',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT 1
);
```

**Campos:**
- `usuario_id` — ID único del usuario
- `usuario` — Nombre de usuario (único)
- `contrasena_hash` — Hash de la contraseña (bcrypt)
- `rol` — Rol del usuario: 'admin' o 'cajero'
- `fecha_creacion` — Timestamp de creación
- `activo` — Estado del usuario (soft delete)

<a id="tabla-categorias"></a>
<a id="-tabla-categorias"></a>
### 🏷️ Tabla: `categorias`

```sql
CREATE TABLE categorias (
    categoria_id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre_categoria VARCHAR(100) NOT NULL,
    descripcion TEXT,
    activo BOOLEAN DEFAULT 1
);
```

**Campos:**
- `categoria_id` — ID único de la categoría
- `nombre_categoria` — Nombre de la categoría
- `descripcion` — Descripción opcional
- `activo` — Estado de la categoría

<a id="tabla-productos"></a>
<a id="-tabla-productos"></a>
### 🍔 Tabla: `productos`

```sql
CREATE TABLE productos (
    producto_id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre_producto VARCHAR(100) NOT NULL,
    descripcion TEXT,
    categoria_id INTEGER,
    activo BOOLEAN DEFAULT 1,
    url_imagen VARCHAR(255),
    imagen BLOB,
    imagen_mime VARCHAR(50),
    imagen_size INTEGER,
    imagen_original_name VARCHAR(255),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(categoria_id)
);
```

**Campos:**
- `producto_id` — ID único del producto
- `nombre_producto` — Nombre del producto
- `descripcion` — Descripción detallada
- `categoria_id` — FK a categorías
- `activo` — Estado del producto
- `url_imagen` — URL de imagen externa (opcional)
- `imagen` — BLOB de imagen almacenada
- `imagen_mime` — Tipo MIME de la imagen
- `imagen_size` — Tamaño de la imagen en bytes
- `imagen_original_name` — Nombre original del archivo

<a id="tabla-variantes"></a>
<a id="-tabla-variantes"></a>
### 📦 Tabla: `variantes`

```sql
CREATE TABLE variantes (
    variante_id INTEGER PRIMARY KEY AUTOINCREMENT,
    producto_id INTEGER NOT NULL,
    nombre_variante VARCHAR(50) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    volumen_onzas DECIMAL(8,2),
    descripcion TEXT,
    activo BOOLEAN DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(producto_id) ON DELETE CASCADE
);
```

**Campos:**
- `variante_id` — ID único de la variante
- `producto_id` — FK al producto padre
- `nombre_variante` — Nombre de la variante (ej: "Grande", "Mediano")
- `precio` — Precio de esta variante
- `volumen_onzas` — Volumen en onzas (para bebidas)
- `descripcion` — Descripción específica de la variante
- `activo` — Estado de la variante

<a id="tabla-metodos_de_pago"></a>
<a id="-tabla-metodos_de_pago"></a>
### 💳 Tabla: `metodos_de_pago`

```sql
CREATE TABLE metodos_de_pago (
    metodo_id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre_metodo VARCHAR(50) NOT NULL,
    descripcion TEXT,
    activo BOOLEAN DEFAULT 1
);
```

**Campos:**
- `metodo_id` — ID único del método de pago
- `nombre_metodo` — Nombre (ej: "Efectivo", "Tarjeta")
- `descripcion` — Descripción del método
- `activo` — Estado del método

<a id="tabla-ventas"></a>
<a id="-tabla-ventas"></a>
### 🛒 Tabla: `ventas`

```sql
CREATE TABLE ventas (
    venta_id INTEGER PRIMARY KEY AUTOINCREMENT,
    codigo VARCHAR(20) UNIQUE NOT NULL,
    usuario_id INTEGER NOT NULL,
    metodo_id INTEGER NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    estado VARCHAR(20) DEFAULT 'completada',
    notas TEXT,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
    FOREIGN KEY (metodo_id) REFERENCES metodos_de_pago(metodo_id)
);
```

**Campos:**
- `venta_id` — ID único de la venta
- `codigo` — Código alfanumérico único para la venta
- `usuario_id` — FK al usuario que procesó la venta
- `metodo_id` — FK al método de pago utilizado
- `total` — Monto total de la venta
- `estado` — Estado: 'completada', 'pendiente', 'cancelada'
- `notas` — Notas adicionales del cajero
- `fecha_hora` — Timestamp de la venta

<a id="tabla-detalle_ventas"></a>
<a id="-tabla-detalle_ventas"></a>
### 🧾 Tabla: `detalle_ventas`

```sql
CREATE TABLE detalle_ventas (
    detalle_id INTEGER PRIMARY KEY AUTOINCREMENT,
    venta_id INTEGER NOT NULL,
    variante_id INTEGER NOT NULL,
    cantidad INTEGER NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    iva DECIMAL(10,2) DEFAULT 0,
    sub_total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venta_id) REFERENCES ventas(venta_id) ON DELETE CASCADE,
    FOREIGN KEY (variante_id) REFERENCES variantes(variante_id)
);
```

**Campos:**
- `detalle_id` — ID único del item de venta
- `venta_id` — FK a la venta padre
- `variante_id` — FK a la variante vendida
- `cantidad` — Cantidad vendida
- `precio_unitario` — Precio por unidad al momento de la venta
- `iva` — IVA calculado para este item
- `sub_total` — Subtotal del item (cantidad × precio_unitario)

<a id="tabla-ingredientes"></a>
<a id="-tabla-ingredientes"></a>
### 🥬 Tabla: `ingredientes`

```sql
CREATE TABLE ingredientes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre VARCHAR(100) NOT NULL,
    unidad_base VARCHAR(20) NOT NULL,
    merma_pct DECIMAL(5,2) DEFAULT 0,
    activo BOOLEAN DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

**Campos:**
- `id` — ID único del ingrediente
- `nombre` — Nombre del ingrediente
- `unidad_base` — Unidad de medida base (gramo, litro, etc.)
- `merma_pct` — Porcentaje de merma esperado
- `activo` — Estado del ingrediente

<a id="tabla-compras"></a>
<a id="-tabla-compras"></a>
### 🛍️ Tabla: `compras`

```sql
CREATE TABLE compras (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ingrediente_id INTEGER NOT NULL,
    cantidad DECIMAL(10,3) NOT NULL,
    costo_total DECIMAL(10,2) NOT NULL,
    iva_incluido BOOLEAN DEFAULT 0,
    fecha DATE NOT NULL,
    proveedor VARCHAR(100),
    notas TEXT,
    FOREIGN KEY (ingrediente_id) REFERENCES ingredientes(id)
);
```

**Campos:**
- `id` — ID único de la compra
- `ingrediente_id` — FK al ingrediente comprado
- `cantidad` — Cantidad comprada
- `costo_total` — Costo total de la compra
- `iva_incluido` — Si el IVA está incluido en el costo
- `fecha` — Fecha de la compra
- `proveedor` — Nombre del proveedor
- `notas` — Notas adicionales

<a id="tabla-recetas-opcional-para-costeo"></a>
<a id="-tabla-recetas-opcional-para-costeo"></a>
### 🧮 Tabla: `recetas` (Opcional - Para Costeo)

```sql
CREATE TABLE recetas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    producto_id INTEGER NOT NULL,
    ingrediente_id INTEGER NOT NULL,
    cantidad DECIMAL(10,3) NOT NULL,
    unidad VARCHAR(20) NOT NULL,
    FOREIGN KEY (producto_id) REFERENCES productos(producto_id) ON DELETE CASCADE,
    FOREIGN KEY (ingrediente_id) REFERENCES ingredientes(id)
);
```

**Campos:**
- `id` — ID único de la receta
- `producto_id` — FK al producto
- `ingrediente_id` — FK al ingrediente
- `cantidad` — Cantidad del ingrediente necesaria
- `unidad` — Unidad de medida para esta receta

---

<a id="diagrama-de-relaciones"></a>
<a id="-diagrama-de-relaciones"></a>
## 🔗 Diagrama de Relaciones

<a id="erd-entidad-relacion"></a>
<a id="-erd-entidad-relacion"></a>
### ERD (Entidad-Relación)

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│    usuarios     │    │   categorias    │    │  metodos_pago   │
├─────────────────┤    ├─────────────────┤    ├─────────────────┤
│ usuario_id (PK) │    │categoria_id (PK)│    │ metodo_id (PK)  │
│ usuario         │    │nombre_categoria │    │ nombre_metodo   │
│ contrasena_hash │    │ descripcion     │    │ descripcion     │
│ rol             │    │ activo          │    │ activo          │
│ fecha_creacion  │    └─────────────────┘    └─────────────────┘
│ activo          │             │                       │
└─────────────────┘             │                       │
         │                      │                       │
         │               ┌─────────────────┐             │
         │               │    productos    │             │
         │               ├─────────────────┤             │
         │               │producto_id (PK) │             │
         │               │nombre_producto  │             │
         │               │descripcion      │             │
         │               │categoria_id (FK)│─────────────┘
         │               │activo           │
         │               │url_imagen       │
         │               │imagen (BLOB)    │
         │               └─────────────────┘
         │                        │
         │                        │ 1:N
         │                        │
         │               ┌─────────────────┐
         │               │    variantes    │
         │               ├─────────────────┤
         │               │variante_id (PK) │
         │               │producto_id (FK) │─────────────┘
         │               │nombre_variante  │
         │               │precio           │
         │               │volumen_onzas    │
         │               │activo           │
         │               └─────────────────┘
         │                        │
         │                        │ N:1
         │                        │
┌─────────────────┐               │
│     ventas      │               │
├─────────────────┤               │
│ venta_id (PK)   │               │
│ codigo          │               │
│ usuario_id (FK) │───────────────┘
│ metodo_id (FK)  │───────────────────────────────────────┘
│ total           │
│ estado          │
│ fecha_hora      │
└─────────────────┘
         │ 1:N
         │
┌─────────────────┐
│ detalle_ventas  │
├─────────────────┤
│ detalle_id (PK) │
│ venta_id (FK)   │─────────────────┘
│ variante_id(FK) │─────────────────────────────────────────┘
│ cantidad        │
│ precio_unitario │
│ sub_total       │
└─────────────────┘

┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│  ingredientes   │    │     compras     │    │    recetas      │
├─────────────────┤    ├─────────────────┤    ├─────────────────┤
│ id (PK)         │    │ id (PK)         │    │ id (PK)         │
│ nombre          │    │ingrediente_id   │    │ producto_id(FK) │
│ unidad_base     │    │cantidad         │    │ingrediente_id   │
│ merma_pct       │    │costo_total      │    │cantidad         │
│ activo          │    │fecha            │    │unidad           │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                      │
         └───────────────────────┼──────────────────────┘
                                 │ N:1
                                 │
                        ┌─────────────────┐
                        │   (costeo)      │
                        │ CostoService    │
                        └─────────────────┘
```

<a id="relaciones-principales"></a>
<a id="-relaciones-principales"></a>
### Relaciones Principales

1. **usuarios** 1:N **ventas** — Un usuario puede procesar múltiples ventas
2. **categorias** 1:N **productos** — Una categoría puede tener múltiples productos
3. **productos** 1:N **variantes** — Un producto puede tener múltiples variantes
4. **metodos_de_pago** 1:N **ventas** — Un método puede usarse en múltiples ventas
5. **ventas** 1:N **detalle_ventas** — Una venta puede tener múltiples items
6. **variantes** 1:N **detalle_ventas** — Una variante puede venderse múltiples veces
7. **ingredientes** 1:N **compras** — Un ingrediente puede comprarse múltiples veces
8. **productos** N:N **ingredientes** (via **recetas**) — Para costeo de producción

---

<a id="queries-importantes"></a>
<a id="-queries-importantes"></a>
## 🔍 Queries Importantes

<a id="consultas-de-ventas"></a>
<a id="-consultas-de-ventas"></a>
### Consultas de Ventas

<a id="reporte-de-ventas-diarias"></a>
<a id="-reporte-de-ventas-diarias"></a>
#### Reporte de Ventas Diarias

```sql
SELECT
    DATE(fecha_hora) as fecha,
    COUNT(*) as num_ventas,
    SUM(total) as total_ventas,
    AVG(total) as ticket_promedio
FROM ventas
WHERE fecha_hora >= DATE('now', '-30 days')
GROUP BY DATE(fecha_hora)
ORDER BY fecha DESC;
```

<a id="top-productos-vendidos"></a>
<a id="-top-productos-vendidos"></a>
#### Top Productos Vendidos

```sql
SELECT
    p.nombre_producto,
    v.nombre_variante,
    SUM(dv.cantidad) as total_vendido,
    SUM(dv.sub_total) as ingresos_total
FROM detalle_ventas dv
JOIN variantes v ON dv.variante_id = v.variante_id
JOIN productos p ON v.producto_id = p.producto_id
JOIN ventas ve ON dv.venta_id = ve.venta_id
WHERE ve.fecha_hora >= DATE('now', '-30 days')
GROUP BY p.producto_id, v.variante_id
ORDER BY total_vendido DESC
LIMIT 10;
```

<a id="ventas-por-usuario-cajeros"></a>
<a id="-ventas-por-usuario-cajeros"></a>
#### Ventas por Usuario (Cajeros)

```sql
SELECT
    u.usuario,
    COUNT(v.venta_id) as num_ventas,
    SUM(v.total) as total_vendido,
    DATE(v.fecha_hora) as fecha
FROM usuarios u
JOIN ventas v ON u.usuario_id = v.usuario_id
WHERE v.fecha_hora >= DATE('now', '-7 days')
GROUP BY u.usuario_id, DATE(v.fecha_hora)
ORDER BY fecha DESC, total_vendido DESC;
```

<a id="consultas-de-inventario-y-costeo"></a>
<a id="-consultas-de-inventario-y-costeo"></a>
### Consultas de Inventario y Costeo

<a id="costo-de-produccion-por-producto"></a>
<a id="-costo-de-produccion-por-producto"></a>
#### Costo de Producción por Producto

```sql
SELECT
    p.nombre_producto,
    SUM(
        r.cantidad * (
            SELECT AVG(c.costo_total / c.cantidad)
            FROM compras c
            WHERE c.ingrediente_id = r.ingrediente_id
            AND c.fecha >= DATE('now', '-90 days')
        )
    ) as costo_estimado
FROM productos p
JOIN recetas r ON p.producto_id = r.producto_id
JOIN ingredientes i ON r.ingrediente_id = i.id
WHERE p.activo = 1
GROUP BY p.producto_id
ORDER BY costo_estimado DESC;
```

<a id="ingredientes-con-stock-bajo-estimado"></a>
<a id="-ingredientes-con-stock-bajo-estimado"></a>
#### Ingredientes con Stock Bajo (Estimado)

```sql
SELECT
    i.nombre,
    SUM(c.cantidad) as total_comprado,
    SUM(
        r.cantidad * dv.cantidad
    ) as total_consumido_estimado,
    (SUM(c.cantidad) - SUM(r.cantidad * dv.cantidad)) as stock_estimado
FROM ingredientes i
LEFT JOIN compras c ON i.id = c.ingrediente_id
LEFT JOIN recetas r ON i.id = r.ingrediente_id
LEFT JOIN variantes v ON r.producto_id = v.producto_id
LEFT JOIN detalle_ventas dv ON v.variante_id = dv.variante_id
LEFT JOIN ventas ve ON dv.venta_id = ve.venta_id
WHERE ve.fecha_hora >= DATE('now', '-30 days') OR ve.fecha_hora IS NULL
GROUP BY i.id
HAVING stock_estimado < 100
ORDER BY stock_estimado ASC;
```

<a id="consultas-de-analisis"></a>
<a id="-consultas-de-analisis"></a>
### Consultas de Análisis

<a id="analisis-de-margenes"></a>
<a id="-analisis-de-margenes"></a>
#### Análisis de Márgenes

```sql
SELECT
    p.nombre_producto,
    v.nombre_variante,
    v.precio as precio_venta,
    AVG(dv.precio_unitario) as precio_promedio_real,
    -- Costo estimado (requiere datos de recetas)
    (
        SELECT SUM(r.cantidad * c.costo_unitario)
        FROM recetas r
        JOIN (
            SELECT
                ingrediente_id,
                AVG(costo_total/cantidad) as costo_unitario
            FROM compras
            WHERE fecha >= DATE('now', '-90 days')
            GROUP BY ingrediente_id
        ) c ON r.ingrediente_id = c.ingrediente_id
        WHERE r.producto_id = p.producto_id
    ) as costo_estimado,
    (v.precio - costo_estimado) as margen_estimado
FROM productos p
JOIN variantes v ON p.producto_id = v.producto_id
LEFT JOIN detalle_ventas dv ON v.variante_id = dv.variante_id
WHERE p.activo = 1 AND v.activo = 1
GROUP BY p.producto_id, v.variante_id
ORDER BY margen_estimado DESC;
```

<a id="consultas-de-integridad"></a>
<a id="-consultas-de-integridad"></a>
### Consultas de Integridad

<a id="verificar-integridad-de-datos"></a>
<a id="-verificar-integridad-de-datos"></a>
#### Verificar Integridad de Datos

```sql
-- Ventas sin detalles
SELECT v.* FROM ventas v
LEFT JOIN detalle_ventas dv ON v.venta_id = dv.venta_id
WHERE dv.venta_id IS NULL;

-- Productos sin variantes
SELECT p.* FROM productos p
LEFT JOIN variantes v ON p.producto_id = v.producto_id
WHERE v.producto_id IS NULL AND p.activo = 1;

-- Variantes huérfanas
SELECT v.* FROM variantes v
LEFT JOIN productos p ON v.producto_id = p.producto_id
WHERE p.producto_id IS NULL;
```

---

<a id="migraciones-y-versionado"></a>
<a id="-migraciones-y-versionado"></a>
## 🔄 Migraciones y Versionado

<a id="estado-actual"></a>
<a id="-estado-actual"></a>
### Estado Actual

- **Sin sistema de migraciones implementado**
- **Recomendación:** Implementar sistema de versionado

<a id="propuesta-de-sistema-de-migraciones"></a>
<a id="-propuesta-de-sistema-de-migraciones"></a>
### Propuesta de Sistema de Migraciones

<a id="tabla-de-control-migrations"></a>
<a id="-tabla-de-control-migrations"></a>
#### Tabla de Control `migrations`

```sql
CREATE TABLE migrations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    version VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    executed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    checksum VARCHAR(64)
);
```

<a id="estructura-de-archivos"></a>
<a id="-estructura-de-archivos"></a>
#### Estructura de Archivos

```
migrations/
├── v1.0.0_initial_schema.sql
├── v1.1.0_add_images_to_products.sql
├── v1.2.0_add_ingredientes_table.sql
└── v1.3.0_add_compras_table.sql
```

<a id="script-de-migracion-propuesta"></a>
<a id="-script-de-migracion-propuesta"></a>
#### Script de Migración (Propuesta)

```php
// scripts/migrate.php
class MigrationManager {
    public function migrate(): void {
        $migrations = $this->getPendingMigrations();
        foreach ($migrations as $migration) {
            $this->executeMigration($migration);
        }
    }

    private function executeMigration(string $file): void {
        $sql = file_get_contents($file);
        $this->pdo->exec($sql);
        $this->markAsExecuted($file);
    }
}
```

<a id="esquema-inicial-para-nueva-instalacion"></a>
<a id="-esquema-inicial-para-nueva-instalacion"></a>
### Esquema Inicial para Nueva Instalación

<a id="migrationsv100_initial_schemasql"></a>
<a id="-migrationsv100_initial_schemasql"></a>
#### `migrations/v1.0.0_initial_schema.sql`

```sql
-- Crear todas las tablas base
-- (Incluir aquí todo el esquema completo)

-- Datos iniciales
INSERT INTO metodos_de_pago (nombre_metodo, descripcion) VALUES
('Efectivo', 'Pago en efectivo'),
('Tarjeta', 'Pago con tarjeta de crédito/débito');

INSERT INTO categorias (nombre_categoria, descripcion) VALUES
('Hamburguesas', 'Hamburguesas y sandwiches'),
('Bebidas', 'Bebidas frías y calientes'),
('Acompañamientos', 'Papas, aros de cebolla, etc.');

INSERT INTO usuarios (usuario, contrasena_hash, rol) VALUES
('admin', '$2y$10$example...', 'admin');
```

---

<a id="seeds-y-datos-de-prueba"></a>
<a id="-seeds-y-datos-de-prueba"></a>
## 🌱 Seeds y Datos de Prueba

<a id="datos-de-demostracion"></a>
<a id="-datos-de-demostracion"></a>
### Datos de Demostración

<a id="seedsdemo_datasql"></a>
<a id="-seedsdemo_datasql"></a>
#### `seeds/demo_data.sql`

```sql
-- Productos de ejemplo
INSERT INTO productos (nombre_producto, descripcion, categoria_id, activo) VALUES
('Hamburguesa Clásica', 'Hamburguesa con carne, lechuga, tomate y cebolla', 1, 1),
('Hamburguesa Doble', 'Doble carne con queso y bacon', 1, 1),
('Coca Cola', 'Bebida gaseosa', 2, 1),
('Papas Fritas', 'Papas fritas crujientes', 3, 1);

-- Variantes de ejemplo
INSERT INTO variantes (producto_id, nombre_variante, precio, activo) VALUES
(1, 'Normal', 15.50, 1),
(1, 'Grande', 18.50, 1),
(2, 'Normal', 22.00, 1),
(3, '350ml', 8.00, 1),
(3, '500ml', 10.00, 1),
(4, 'Pequeña', 6.50, 1),
(4, 'Grande', 9.50, 1);

-- Ingredientes de ejemplo
INSERT INTO ingredientes (nombre, unidad_base, merma_pct) VALUES
('Carne de res', 'gramo', 5.0),
('Pan de hamburguesa', 'unidad', 2.0),
('Lechuga', 'gramo', 10.0),
('Tomate', 'gramo', 8.0),
('Cebolla', 'gramo', 15.0),
('Queso', 'gramo', 3.0),
('Papas', 'gramo', 12.0);

-- Recetas de ejemplo
INSERT INTO recetas (producto_id, ingrediente_id, cantidad, unidad) VALUES
-- Hamburguesa Clásica
(1, 1, 120, 'gramo'),  -- 120g carne
(1, 2, 1, 'unidad'),   -- 1 pan
(1, 3, 20, 'gramo'),   -- 20g lechuga
(1, 4, 30, 'gramo'),   -- 30g tomate
(1, 5, 15, 'gramo');   -- 15g cebolla
```

<a id="script-de-seeding"></a>
<a id="-script-de-seeding"></a>
### Script de Seeding

```php
// scripts/seed.php
class DatabaseSeeder {
    public function run(): void {
        $this->seedUsers();
        $this->seedCategories();
        $this->seedPaymentMethods();
        $this->seedProducts();
        $this->seedVariants();
        $this->seedIngredients();
        $this->seedRecipes();
    }

    public function seedDemoSales(): void {
        // Generar ventas de prueba para testing
        for ($i = 0; $i < 50; $i++) {
            $this->createRandomSale();
        }
    }
}
```

---

<a id="indices-y-performance"></a>
<a id="-indices-y-performance"></a>
## ⚡ Índices y Performance

<a id="indices-recomendados"></a>
<a id="-indices-recomendados"></a>
### Índices Recomendados

```sql
-- Índices para consultas frecuentes
CREATE INDEX idx_ventas_fecha ON ventas(fecha_hora);
CREATE INDEX idx_ventas_usuario ON ventas(usuario_id);
CREATE INDEX idx_ventas_estado ON ventas(estado);

CREATE INDEX idx_detalle_venta ON detalle_ventas(venta_id);
CREATE INDEX idx_detalle_variante ON detalle_ventas(variante_id);

CREATE INDEX idx_variantes_producto ON variantes(producto_id);
CREATE INDEX idx_variantes_activo ON variantes(activo);

CREATE INDEX idx_productos_categoria ON productos(categoria_id);
CREATE INDEX idx_productos_activo ON productos(activo);

CREATE INDEX idx_compras_ingrediente ON compras(ingrediente_id);
CREATE INDEX idx_compras_fecha ON compras(fecha);

CREATE INDEX idx_recetas_producto ON recetas(producto_id);
CREATE INDEX idx_recetas_ingrediente ON recetas(ingrediente_id);
```

<a id="optimizaciones-sqlite"></a>
<a id="-optimizaciones-sqlite"></a>
### Optimizaciones SQLite

```sql
-- Configuración de performance para SQLite
PRAGMA cache_size = 10000;
PRAGMA temp_store = MEMORY;
PRAGMA mmap_size = 268435456; -- 256MB
PRAGMA optimize;

-- Análisis de estadísticas
ANALYZE;
```

<a id="queries-de-monitoreo"></a>
<a id="-queries-de-monitoreo"></a>
### Queries de Monitoreo

<a id="tablas-con-mas-registros"></a>
<a id="-tablas-con-mas-registros"></a>
#### Tablas con Más Registros

```sql
SELECT
    name as tabla,
    (
        SELECT COUNT(*)
        FROM sqlite_master
        WHERE type='table' AND name=m.name
    ) as registros_estimados
FROM sqlite_master m
WHERE type='table'
AND name NOT LIKE 'sqlite_%';
```

<a id="espacio-en-disco"></a>
<a id="-espacio-en-disco"></a>
#### Espacio en Disco

```sql
-- Solo SQLite
SELECT
    name,
    (SELECT COUNT(*) FROM ventas) as ventas_count,
    (SELECT COUNT(*) FROM detalle_ventas) as detalles_count,
    (SELECT COUNT(*) FROM productos) as productos_count;

-- Tamaño del archivo
-- Ver desde sistema operativo el tamaño de data/snackshop.db
```

---

<a id="backup-y-restauracion"></a>
<a id="-backup-y-restauracion"></a>
## 💾 Backup y Restauración

<a id="backup-sqlite"></a>
<a id="-backup-sqlite"></a>
### Backup SQLite

<a id="backup-completo"></a>
<a id="-backup-completo"></a>
#### Backup Completo

```bash
<a id="backup-directo-del-archivo"></a>
<a id="-backup-directo-del-archivo"></a>
# Backup directo del archivo
cp data/snackshop.db backups/snackshop_$(date +%Y%m%d_%H%M%S).db

<a id="backup-sql-dump"></a>
<a id="-backup-sql-dump"></a>
# Backup SQL dump
sqlite3 data/snackshop.db .dump > backups/snackshop_$(date +%Y%m%d_%H%M%S).sql
```

<a id="backup-incremental-propuesta"></a>
<a id="-backup-incremental-propuesta"></a>
#### Backup Incremental (Propuesta)

```sql
-- Backup de ventas nuevas desde última fecha
SELECT * FROM ventas
WHERE fecha_hora > '2025-10-27 00:00:00'
ORDER BY fecha_hora;
```

<a id="backup-mysql"></a>
<a id="-backup-mysql"></a>
### Backup MySQL

```bash
<a id="backup-completo"></a>
<a id="-backup-completo"></a>
# Backup completo
mysqldump -u snackshop_user -p snackshop > backups/snackshop_$(date +%Y%m%d_%H%M%S).sql

<a id="backup-solo-datos-sin-estructura"></a>
<a id="-backup-solo-datos-sin-estructura"></a>
# Backup solo datos (sin estructura)
mysqldump -u snackshop_user -p --no-create-info snackshop > backups/snackshop_data_$(date +%Y%m%d_%H%M%S).sql

<a id="backup-comprimido"></a>
<a id="-backup-comprimido"></a>
# Backup comprimido
mysqldump -u snackshop_user -p snackshop | gzip > backups/snackshop_$(date +%Y%m%d_%H%M%S).sql.gz
```

<a id="restauracion"></a>
<a id="-restauracion"></a>
### Restauración

<a id="sqlite"></a>
<a id="-sqlite"></a>
#### SQLite

```bash
<a id="restaurar-desde-archivo"></a>
<a id="-restaurar-desde-archivo"></a>
# Restaurar desde archivo
cp backups/snackshop_20251028_143000.db data/snackshop.db

<a id="restaurar-desde-sql-dump"></a>
<a id="-restaurar-desde-sql-dump"></a>
# Restaurar desde SQL dump
sqlite3 data/snackshop.db < backups/snackshop_20251028_143000.sql
```

<a id="mysql"></a>
<a id="-mysql"></a>
#### MySQL

```bash
<a id="restaurar-base-completa"></a>
<a id="-restaurar-base-completa"></a>
# Restaurar base completa
mysql -u snackshop_user -p snackshop < backups/snackshop_20251028_143000.sql

<a id="restaurar-tabla-especifica"></a>
<a id="-restaurar-tabla-especifica"></a>
# Restaurar tabla específica
mysql -u snackshop_user -p snackshop < backups/tabla_ventas.sql
```

<a id="estrategia-de-backup-recomendada"></a>
<a id="-estrategia-de-backup-recomendada"></a>
### Estrategia de Backup Recomendada

1. **Diario:** Backup automático a las 2 AM
2. **Semanal:** Backup completo los domingos
3. **Mensual:** Backup archivado para largo plazo
4. **Pre-actualización:** Backup antes de cambios importantes

```bash
<a id="script-cron-para-backup-automatico"></a>
<a id="-script-cron-para-backup-automatico"></a>
# Script cron para backup automático
<a id="binbash"></a>
<a id="-binbash"></a>
#!/bin/bash
<a id="0-2-pathtobackup_scriptsh"></a>
<a id="-0-2-pathtobackup_scriptsh"></a>
# 0 2 * * * /path/to/backup_script.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/path/to/backups"
DB_PATH="/path/to/data/snackshop.db"

<a id="backup-diario"></a>
<a id="-backup-diario"></a>
# Backup diario
cp "$DB_PATH" "$BACKUP_DIR/daily/snackshop_$DATE.db"

<a id="limpiar-backups-antiguos-mantener-7-dias"></a>
<a id="-limpiar-backups-antiguos-mantener-7-dias"></a>
# Limpiar backups antiguos (mantener 7 días)
find "$BACKUP_DIR/daily" -name "*.db" -mtime +7 -delete

<a id="backup-semanal-domingos"></a>
<a id="-backup-semanal-domingos"></a>
# Backup semanal (domingos)
if [ $(date +%u) -eq 7 ]; then
    cp "$DB_PATH" "$BACKUP_DIR/weekly/snackshop_$DATE.db"
fi
```

---

<a id="troubleshooting"></a>
<a id="-troubleshooting"></a>
## 🚨 Troubleshooting

<a id="problemas-comunes"></a>
<a id="-problemas-comunes"></a>
### Problemas Comunes

<a id="error-database-is-locked"></a>
<a id="-error-database-is-locked"></a>
#### Error: "Database is locked"

```bash
<a id="verificar-procesos-que-usan-la-bd"></a>
<a id="-verificar-procesos-que-usan-la-bd"></a>
# Verificar procesos que usan la BD
lsof data/snackshop.db

<a id="solucion-cerrar-conexiones-activas"></a>
<a id="-solucion-cerrar-conexiones-activas"></a>
# Solución: cerrar conexiones activas
<a id="reiniciar-servidor-web"></a>
<a id="-reiniciar-servidor-web"></a>
# Reiniciar servidor web
sudo systemctl restart apache2
```

<a id="error-no-such-table"></a>
<a id="-error-no-such-table"></a>
#### Error: "No such table"

```sql
-- Verificar tablas existentes
.tables  -- En SQLite CLI

-- Verificar esquema
.schema productos  -- En SQLite CLI

-- Para MySQL
SHOW TABLES;
DESCRIBE productos;
```

<a id="error-foreign-key-constraint-failed"></a>
<a id="-error-foreign-key-constraint-failed"></a>
#### Error: "Foreign key constraint failed"

```sql
-- Verificar foreign keys
PRAGMA foreign_key_check;

-- Deshabilitar temporalmente (solo para debug)
PRAGMA foreign_keys = OFF;
-- Hacer operación
PRAGMA foreign_keys = ON;
```

<a id="performance-lenta"></a>
<a id="-performance-lenta"></a>
#### Performance Lenta

```sql
-- Analizar query plan
EXPLAIN QUERY PLAN
SELECT * FROM ventas v
JOIN detalle_ventas dv ON v.venta_id = dv.venta_id
WHERE v.fecha_hora >= '2025-10-01';

-- Actualizar estadísticas
ANALYZE;

-- Verificar índices
SELECT name FROM sqlite_master WHERE type='index';
```

<a id="herramientas-de-diagnostico"></a>
<a id="-herramientas-de-diagnostico"></a>
### Herramientas de Diagnóstico

<a id="verificacion-de-integridad"></a>
<a id="-verificacion-de-integridad"></a>
#### Verificación de Integridad

```sql
-- SQLite
PRAGMA integrity_check;
PRAGMA foreign_key_check;

-- MySQL
CHECK TABLE ventas;
CHECK TABLE productos;
```

<a id="estadisticas-de-uso"></a>
<a id="-estadisticas-de-uso"></a>
#### Estadísticas de Uso

```sql
-- Conteo por tablas
SELECT
    'usuarios' as tabla, COUNT(*) as registros FROM usuarios
UNION ALL
SELECT
    'productos' as tabla, COUNT(*) as registros FROM productos
UNION ALL
SELECT
    'ventas' as tabla, COUNT(*) as registros FROM ventas
UNION ALL
SELECT
    'detalle_ventas' as tabla, COUNT(*) as registros FROM detalle_ventas;
```

<a id="log-de-errores"></a>
<a id="-log-de-errores"></a>
#### Log de Errores

```php
// En Connection.php - logging de errores
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    error_log("DSN: " . $dsn);
    error_log("Time: " . date('Y-m-d H:i:s'));
    throw $e;
}
```

---

<a id="documentos-relacionados"></a>
<a id="-documentos-relacionados"></a>
## 🔗 Documentos Relacionados

- **[📖 Índice General](docs/INDEX.md)** — Navegación completa del manual
- **[🏗️ Architecture](ARCHITECTURE.md)** — Patrones y capas del sistema
- **[🔌 API Documentation](API.md)** — Endpoints que usan la base de datos
- **[🚀 Deployment](DEPLOYMENT.md)** — Configuración de BD en producción
- **[🔧 Development](DEVELOPMENT.md)** — Setup de base de datos para desarrollo

---

<a id="soporte"></a>
<a id="-soporte"></a>
## 📞 Soporte

**¿Problemas con la base de datos?**
- **Issues:** [GitHub Issues](https://github.com/Equinoxe-Grammer/SnackShack/issues) con etiqueta `database`
- **Backup:** Siempre hacer backup antes de cambios estructurales
- **Testing:** Usar SQLite para desarrollo, MySQL para producción

---

**[📖 Índice](docs/INDEX.md)** | **[🏗️ Ver Arquitectura](ARCHITECTURE.md)** | **[🔌 Ver API](API.md)** | **[🚀 Ver Deployment](DEPLOYMENT.md)**
