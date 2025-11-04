# 🔐 Sistema de Sesiones Seguras - SnackShop POS

## 📋 Descripción

Sistema de gestión de sesiones seguras con duración de **24 horas**, regeneración automática de ID, validaciones de seguridad y monitoreo en tiempo real.

---

## ✨ Características

### 🔒 Seguridad

- ✅ **Duración de 24 horas** desde el login
- ✅ **Regeneración de ID cada 30 minutos** (previene session fixation)
- ✅ **Validación de User-Agent** (detecta hijacking)
- ✅ **Cookies HttpOnly y Secure** (HTTPS)
- ✅ **SameSite=Lax** (protección CSRF)
- ✅ **Strict mode** activado
- ✅ **Destrucción segura de sesión** en logout

### ⚡ Funcionalidad

- ✅ **Auto-validación** en cada request
- ✅ **Extensión automática** con actividad del usuario
- ✅ **Monitoreo en tiempo real** (JavaScript)
- ✅ **Alertas de expiración** (5 minutos antes)
- ✅ **API REST** para gestión

---

## 🏗️ Arquitectura

### Backend (PHP)

#### `SessionService.php`
Clase principal de gestión de sesiones.

**Métodos principales:**

```php
// Configurar e iniciar sesión
SessionService::configure();

// Iniciar sesión de usuario
SessionService::login($userId, $username, $role);

// Validar sesión
$isValid = SessionService::validate();

// Verificar si está activa
$isActive = SessionService::isActive();

// Extender sesión (reset 24h)
SessionService::extend();

// Destruir sesión
SessionService::destroy();

// Obtener información
$info = SessionService::getInfo();
$userId = SessionService::getUserId();
$username = SessionService::getUsername();
$role = SessionService::getRole();
$remaining = SessionService::getRemainingTime();
```

#### `SessionApiController.php`
Endpoints REST para frontend.

**Rutas:**

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/session/info` | Info completa de sesión |
| GET | `/api/session/check` | Verificar si está activa |
| POST | `/api/session/extend` | Extender por 24h más |

### Frontend (JavaScript)

#### `session-monitor.js`
Monitor automático de sesión.

**Características:**

- Verificación cada 1 minuto
- Alerta cuando quedan 5 minutos
- Extensión automática con actividad
- Redirección a login si expira

**Uso:**

```javascript
const monitor = new SessionMonitor({
    checkInterval: 60000,        // ms
    warningTime: 300,            // segundos
    extendOnActivity: true,
    onExpired: () => {
        // Callback personalizado
    },
    onWarning: (remaining) => {
        // Callback personalizado
    }
});

monitor.start();
```

---

## 🔧 Configuración

### Constantes del Sistema

```php
// SessionService.php
const SESSION_LIFETIME = 86400;      // 24 horas
const REGENERATE_TIME = 1800;        // 30 minutos
```

### Parámetros de Cookie

```php
[
    'lifetime' => 86400,              // 24 horas
    'path' => '/',
    'domain' => '',
    'secure' => true,                 // Solo HTTPS
    'httponly' => true,               // No accesible desde JS
    'samesite' => 'Lax'              // Protección CSRF
]
```

---

## 📊 Flujo de Sesión

### 1. Login

```
Usuario → AuthController::login()
       → UserService::authenticate()
       → SessionService::login(userId, username, role)
       → Guarda metadatos de sesión
       → Redirige al dashboard
```

### 2. Validación en cada Request

```
Request → index.php
       → SessionService::configure()
       → SessionService::validate()
       ├─ ✓ Válida → Continuar
       └─ ✗ Inválida → Redirigir a /login
```

### 3. Extensión Automática

```
Usuario activo (click, tecla, scroll)
       → session-monitor.js detecta actividad
       → Espera 5 minutos desde última extensión
       → POST /api/session/extend
       → SessionService::extend()
       → Reset timer 24h
```

### 4. Expiración

```
24 horas sin extensión
       → SessionService::validate() = false
       → Destruye sesión
       → Redirige a /login
       → Monitor JS muestra alerta
```

---

## 🔐 Validaciones de Seguridad

### 1. Tiempo de Vida

```php
$created = $_SESSION['session_created'];
$elapsed = time() - $created;

if ($elapsed > SESSION_LIFETIME) {
    return false; // Expirada
}
```

### 2. User-Agent

```php
if ($_SESSION['session_user_agent'] !== getUserAgent()) {
    return false; // Posible hijacking
}
```

### 3. Regeneración Periódica

```php
$lastRegen = $_SESSION['session_last_regeneration'];
if (time() - $lastRegen > REGENERATE_TIME) {
    session_regenerate_id(true);
}
```

---

## 🌐 API REST

### GET /api/session/info

Obtiene información completa de la sesión.

**Response:**

```json
{
    "ok": true,
    "session": {
        "user_id": 1,
        "username": "admin",
        "role": "admin",
        "created_at": "2025-11-04 10:00:00",
        "last_activity": "2025-11-04 15:30:00",
        "remaining_seconds": 45600,
        "remaining_hours": 12.7
    }
}
```

### GET /api/session/check

Verifica rápidamente si la sesión está activa.

**Response:**

```json
{
    "ok": true,
    "active": true,
    "remaining_seconds": 45600
}
```

### POST /api/session/extend

Extiende la sesión por 24 horas más.

**Response:**

```json
{
    "ok": true,
    "message": "Sesión extendida por 24 horas más",
    "session": { ... }
}
```

---

## 💻 Implementación en Vistas

### Agregar Monitor a una Vista

```php
<!DOCTYPE html>
<html>
<head>
    <title>Mi Vista</title>
</head>
<body>
    <!-- Tu contenido -->
    
    <!-- Monitor de sesión -->
    <script src="/js/session-monitor.js"></script>
</body>
</html>
```

El monitor se **auto-inicia** en todas las páginas excepto `/login`.

### Configuración Personalizada

```javascript
// Sobrescribir configuración por defecto
document.addEventListener('DOMContentLoaded', () => {
    if (window.sessionMonitor) {
        window.sessionMonitor.stop(); // Detener el auto-iniciado
    }
    
    const customMonitor = new SessionMonitor({
        checkInterval: 30000,  // Verificar cada 30 segundos
        warningTime: 600,      // Alertar con 10 minutos
        extendOnActivity: false, // No extender automáticamente
        onExpired: () => {
            console.log('Sesión expirada');
            window.location.href = '/login';
        }
    });
    
    customMonitor.start();
});
```

---

## 🧪 Pruebas

### Prueba Manual de Expiración

```php
// Modificar temporalmente en SessionService.php
const SESSION_LIFETIME = 120; // 2 minutos
const REGENERATE_TIME = 30;   // 30 segundos
```

### Prueba de Extensión

```javascript
// Consola del navegador
sessionMonitor.getInfo().then(info => console.log(info));
sessionMonitor.extend().then(() => console.log('Extendida'));
```

### Prueba de Hijacking

```php
// Simular cambio de User-Agent
$_SESSION['session_user_agent'] = 'FakeAgent';
// Siguiente request será rechazado
```

---

## 🔍 Debugging

### Ver Estado de Sesión

```javascript
// En consola del navegador
sessionMonitor.getInfo().then(console.log);
```

### Logs del Servidor

```php
error_log('[SESSION] ' . json_encode(SessionService::getInfo()));
```

### Variables de Sesión

```php
// Debug temporal (¡NO en producción!)
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
```

---

## ⚠️ Consideraciones

### 1. HTTPS en Producción

```php
// Cookies Secure solo funcionan en HTTPS
'secure' => $isHttps
```

### 2. Garbage Collection

```php
ini_set('session.gc_probability', '1');
ini_set('session.gc_divisor', '100');
// 1% de probabilidad de limpiar sesiones expiradas
```

### 3. Almacenamiento de Sesiones

**Desarrollo (SQLite):**
- Sesiones en archivos del sistema

**Producción (MySQL):**
- Considerar almacenar sesiones en Redis o base de datos

---

## 📈 Mejoras Futuras

- [ ] Almacenamiento de sesiones en Redis
- [ ] Logs de auditoría de sesiones
- [ ] IP whitelisting opcional
- [ ] Two-factor authentication
- [ ] Sesiones concurrentes por usuario
- [ ] Remember me (cookie persistente)

---

## 🔗 Archivos Relacionados

- `src/Services/SessionService.php` - Servicio principal
- `src/Controllers/Api/SessionApiController.php` - API REST
- `public/js/session-monitor.js` - Monitor frontend
- `public/index.php` - Configuración inicial
- `src/Controllers/AuthController.php` - Login/Logout

---

## 📞 Soporte

Para preguntas o problemas:

1. Revisar logs del servidor
2. Verificar console del navegador
3. Comprobar configuración de cookies
4. Validar que HTTPS esté habilitado en producción

---

**Fecha:** Noviembre 4, 2025  
**Versión:** 1.0  
**Sistema:** SnackShop POS
