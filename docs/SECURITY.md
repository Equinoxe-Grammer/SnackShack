# 🔒 SnackShop - Seguridad

**🏠 Ubicación:** `SECURITY.md`
**📅 Última actualización:** 30 de octubre, 2025
**🎯 Propósito:** Resumen de las medidas de seguridad recomendadas y configuraciones críticas para producción.

---

## Visión general

Este documento recoge las prácticas y configuraciones mínimas recomendadas para ejecutar SnackShop de forma segura en producción.

## Autenticación y autorización

- Usa contraseñas robustas y salted hashing (bcrypt o Argon2). En la app se recomienda bcrypt con coste adecuado.
- Limita intentos de login y aplica bloqueo temporal (throttling).
- Mantén sesiones seguras: cookies Secure, HttpOnly y SameSite=strict donde sea posible.
- Requerir HTTPS en producción.

## CSRF

- Protege formularios con tokens CSRF en todas las operaciones que muten estado.
- Verifica tokens en el servidor y expira tokens periódicamente.

## Cabeceras HTTP y CSP

- Habilita cabeceras de seguridad:
  - Strict-Transport-Security (HSTS)
  - X-Frame-Options: DENY
  - X-Content-Type-Options: nosniff
  - Referrer-Policy: no-referrer-when-downgrade (o stricter)
- Configura Content-Security-Policy (CSP) para limitar orígenes de scripts y recursos.

## TLS / SSL

- Usa certificados válidos (Let's Encrypt o proveedor) y configura renovación automática.
- Desactiva TLS < 1.2, habilita TLS 1.2+ o 1.3.
- Revisa la configuración con SSL Labs y corrige puntos críticos.

## Gestión de secret keys y configuración

- No guardes secretos en el repositorio. Usa variables de entorno o secretos del orquestador.
- Asegura el acceso a `.env` en producción; no exponerlo por el servidor web.
- Rota claves y secretos de forma periódica.

## Bases de datos y backups

- Concede permisos mínimos a cuentas de base de datos (principio de menor privilegio).
- Realiza backups periódicos cifrados y prueba restauraciones.
- Usa conexiones seguras entre servicios cuando sea posible.

## Dependencias y actualizaciones

- Mantén las dependencias actualizadas (composer audit, security advisories).
- Escanea regularmente con herramientas de análisis de seguridad.

## Logging y monitoreo

- Centraliza logs y monitoriza errores y patrones inusuales.
- Protege los logs para que no contengan secretos o datos sensibles.

## Respuesta a incidentes

- Ten un plan de respuesta: identificación, contención, erradicación, recuperación y post-mortem.
- Mantén contactos y procedimientos para notificación si hay brechas.

---

Si necesitas que añada checks automáticos o ejemplos de configuración Nginx/Apache para HSTS, CSP o TLS, puedo incluirlos aquí.
