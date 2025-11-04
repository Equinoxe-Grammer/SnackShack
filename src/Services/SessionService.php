<?php
namespace App\Services;

/**
 * Servicio de gestión de sesiones seguras
 * Implementa sesiones de 24 horas con regeneración de ID y validaciones de seguridad
 */
class SessionService
{
    // Duración de la sesión: 24 horas (en segundos)
    private const SESSION_LIFETIME = 86400; // 24 * 60 * 60
    
    // Tiempo para regenerar el ID de sesión: 30 minutos
    private const REGENERATE_TIME = 1800; // 30 * 60
    
    // Claves para datos de sesión
    private const KEY_USER_ID = 'usuario_id';
    private const KEY_USERNAME = 'usuario';
    private const KEY_ROLE = 'rol';
    private const KEY_CREATED = 'session_created';
    private const KEY_LAST_ACTIVITY = 'session_last_activity';
    private const KEY_LAST_REGENERATION = 'session_last_regeneration';
    private const KEY_USER_AGENT = 'session_user_agent';
    private const KEY_IP_ADDRESS = 'session_ip_address';
    
    /**
     * Configura e inicia una sesión segura
     */
    public static function configure(): void
    {
        // Prevenir inicio múltiple de sesión
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        
        // Detectar si estamos en HTTPS
        $isHttps = (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (string)$_SERVER['SERVER_PORT'] === '443');
        
        // Configurar parámetros de cookie de sesión
        if (PHP_VERSION_ID >= 70300) {
            session_set_cookie_params([
                'lifetime' => self::SESSION_LIFETIME,
                'path' => '/',
                'domain' => '',
                'secure' => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        } else {
            session_set_cookie_params(
                self::SESSION_LIFETIME,
                '/; samesite=Lax',
                '',
                $isHttps,
                true
            );
        }
        
        // Configurar nombre de sesión más seguro
        session_name('SNACKSHOP_SID');
        
        // Configurar opciones de sesión
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Lax');
        
        if ($isHttps) {
            ini_set('session.cookie_secure', '1');
        }
        
        // Configurar garbage collection
        ini_set('session.gc_maxlifetime', (string)self::SESSION_LIFETIME);
        ini_set('session.gc_probability', '1');
        ini_set('session.gc_divisor', '100');
        
        // Iniciar sesión
        session_start();
    }
    
    /**
     * Inicia sesión de usuario
     */
    public static function login(int $userId, string $username, string $role): void
    {
        // Regenerar ID de sesión para prevenir fijación
        self::regenerateId(true);
        
        // Guardar datos del usuario
        $_SESSION[self::KEY_USER_ID] = $userId;
        $_SESSION[self::KEY_USERNAME] = $username;
        $_SESSION[self::KEY_ROLE] = $role;
        
        // Guardar metadatos de sesión
        $now = time();
        $_SESSION[self::KEY_CREATED] = $now;
        $_SESSION[self::KEY_LAST_ACTIVITY] = $now;
        $_SESSION[self::KEY_LAST_REGENERATION] = $now;
        $_SESSION[self::KEY_USER_AGENT] = self::getUserAgent();
        $_SESSION[self::KEY_IP_ADDRESS] = self::getIpAddress();
    }
    
    /**
     * Verifica si la sesión es válida y actualiza la actividad
     */
    public static function validate(): bool
    {
        // Si no hay sesión activa
        if (!self::isActive()) {
            return false;
        }
        
        // Verificar expiración
        if (!self::checkLifetime()) {
            self::destroy();
            return false;
        }
        
        // Validar seguridad (User Agent e IP)
        if (!self::validateSecurity()) {
            self::destroy();
            return false;
        }
        
        // Actualizar última actividad
        $_SESSION[self::KEY_LAST_ACTIVITY] = time();
        
        // Regenerar ID periódicamente
        self::periodicRegeneration();
        
        return true;
    }
    
    /**
     * Verifica si hay una sesión activa con usuario autenticado
     */
    public static function isActive(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE
            && isset($_SESSION[self::KEY_USER_ID])
            && isset($_SESSION[self::KEY_CREATED]);
    }
    
    /**
     * Verifica el tiempo de vida de la sesión
     */
    private static function checkLifetime(): bool
    {
        if (!isset($_SESSION[self::KEY_CREATED])) {
            return false;
        }
        
        $created = (int)$_SESSION[self::KEY_CREATED];
        $elapsed = time() - $created;
        
        // Si han pasado más de 24 horas desde la creación
        if ($elapsed > self::SESSION_LIFETIME) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida que User Agent e IP no hayan cambiado (protección contra hijacking)
     */
    private static function validateSecurity(): bool
    {
        // Validar User Agent
        if (isset($_SESSION[self::KEY_USER_AGENT])) {
            if ($_SESSION[self::KEY_USER_AGENT] !== self::getUserAgent()) {
                return false;
            }
        }
        
        // Validar IP (opcional, puede causar problemas con proxies/VPNs)
        // Comentado por defecto, descomentar si necesitas validación estricta
        /*
        if (isset($_SESSION[self::KEY_IP_ADDRESS])) {
            if ($_SESSION[self::KEY_IP_ADDRESS] !== self::getIpAddress()) {
                return false;
            }
        }
        */
        
        return true;
    }
    
    /**
     * Regenera el ID de sesión periódicamente
     */
    private static function periodicRegeneration(): void
    {
        if (!isset($_SESSION[self::KEY_LAST_REGENERATION])) {
            $_SESSION[self::KEY_LAST_REGENERATION] = time();
            return;
        }
        
        $lastRegeneration = (int)$_SESSION[self::KEY_LAST_REGENERATION];
        $elapsed = time() - $lastRegeneration;
        
        // Regenerar cada 30 minutos
        if ($elapsed > self::REGENERATE_TIME) {
            self::regenerateId(false);
            $_SESSION[self::KEY_LAST_REGENERATION] = time();
        }
    }
    
    /**
     * Regenera el ID de sesión
     */
    private static function regenerateId(bool $deleteOld = true): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id($deleteOld);
        }
    }
    
    /**
     * Destruye la sesión completamente
     */
    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Limpiar variables de sesión
            $_SESSION = [];
            
            // Destruir cookie de sesión
            if (isset($_COOKIE[session_name()])) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }
            
            // Destruir sesión
            session_destroy();
        }
    }
    
    /**
     * Obtiene el ID del usuario autenticado
     */
    public static function getUserId(): ?int
    {
        return isset($_SESSION[self::KEY_USER_ID]) ? (int)$_SESSION[self::KEY_USER_ID] : null;
    }
    
    /**
     * Obtiene el nombre de usuario autenticado
     */
    public static function getUsername(): ?string
    {
        return $_SESSION[self::KEY_USERNAME] ?? null;
    }
    
    /**
     * Obtiene el rol del usuario autenticado
     */
    public static function getRole(): ?string
    {
        return $_SESSION[self::KEY_ROLE] ?? null;
    }
    
    /**
     * Obtiene el tiempo restante de sesión en segundos
     */
    public static function getRemainingTime(): int
    {
        if (!isset($_SESSION[self::KEY_CREATED])) {
            return 0;
        }
        
        $created = (int)$_SESSION[self::KEY_CREATED];
        $elapsed = time() - $created;
        $remaining = self::SESSION_LIFETIME - $elapsed;
        
        return max(0, $remaining);
    }
    
    /**
     * Obtiene información de la sesión
     */
    public static function getInfo(): array
    {
        if (!self::isActive()) {
            return [];
        }
        
        $created = isset($_SESSION[self::KEY_CREATED]) ? (int)$_SESSION[self::KEY_CREATED] : 0;
        $lastActivity = isset($_SESSION[self::KEY_LAST_ACTIVITY]) ? (int)$_SESSION[self::KEY_LAST_ACTIVITY] : 0;
        
        return [
            'user_id' => self::getUserId(),
            'username' => self::getUsername(),
            'role' => self::getRole(),
            'created_at' => $created ? date('Y-m-d H:i:s', $created) : null,
            'last_activity' => $lastActivity ? date('Y-m-d H:i:s', $lastActivity) : null,
            'remaining_seconds' => self::getRemainingTime(),
            'remaining_hours' => round(self::getRemainingTime() / 3600, 1),
        ];
    }
    
    /**
     * Obtiene el User Agent del cliente
     */
    private static function getUserAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
    
    /**
     * Obtiene la dirección IP del cliente
     */
    private static function getIpAddress(): string
    {
        // Revisar headers de proxy
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Tomar la primera IP de la lista
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }
    
    /**
     * Extiende la sesión (reinicia el contador de 24 horas)
     */
    public static function extend(): void
    {
        if (self::isActive()) {
            $_SESSION[self::KEY_CREATED] = time();
            $_SESSION[self::KEY_LAST_ACTIVITY] = time();
        }
    }
}
