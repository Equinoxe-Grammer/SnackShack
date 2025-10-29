<?php
/**
 * Punto de entrada de la aplicación
 * Todas las solicitudes pasan por este archivo
 */

// Iniciar sesión
session_start();

if (!headers_sent()) {
	header("X-Frame-Options: SAMEORIGIN");
	header("X-Content-Type-Options: nosniff");
	header("Referrer-Policy: strict-origin-when-cross-origin");
	header("Permissions-Policy: camera=(), microphone=(), geolocation=()");
header(
  "Content-Security-Policy: " .
  "default-src 'self'; " .
  // JS: local + cdnjs + jsDelivr (mantén 'unsafe-inline' solo si lo necesitas)
  "script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; " .
  // CSS: local + inline + cdnjs + Google Fonts + jsDelivr
  "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com https://cdn.jsdelivr.net; " .
  // Imágenes: local + data: + blob:
  "img-src 'self' data: blob:; " .
  // Fuentes: local + Google Fonts + cdnjs (Font Awesome) + data:
  "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:; " .
  // XHR/fetch: solo a tu origen (ajusta si llamas APIs externas)
  "connect-src 'self'; " .
  // Frames/bases/formularios
  "frame-ancestors 'self'; " .
  "form-action 'self'; " .
  "base-uri 'self'"
);

	
}

// Cargar autoload de Composer y bootstrap
require_once __DIR__ . '/../bootstrap.php';

// Cargar el enrutador con todas las rutas definidas
$router = require_once __DIR__ . '/../src/Routes/routes.php';

// Despachar la solicitud al controlador correspondiente
$router->dispatch();
