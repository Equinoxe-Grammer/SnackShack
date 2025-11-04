<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acceso Denegado</title>
    <script src="/js/theme-toggle.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #ff6b6b 0%, #c92a2a 100%);
            color: white;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
            max-width: 600px;
        }
        h1 {
            font-size: 6rem;
            margin: 0;
        }
        .icon {
            font-size: 5rem;
            margin-bottom: 1rem;
        }
        p {
            font-size: 1.5rem;
            margin: 1rem 0;
        }
        .debug-info {
            background: rgba(0,0,0,0.3);
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            font-size: 0.9rem;
            text-align: left;
        }
        .debug-info code {
            background: rgba(255,255,255,0.1);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            display: inline-block;
            margin: 0.2rem 0;
        }
        a {
            color: white;
            text-decoration: none;
            padding: 0.8rem 2rem;
            background: rgba(255,255,255,0.2);
            border-radius: 25px;
            display: inline-block;
            margin-top: 1rem;
            transition: background 0.3s;
        }
        a:hover {
            background: rgba(255,255,255,0.3);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="icon">🚫</div>
        <h1>403</h1>
        <p>Acceso Denegado</p>
        <p style="font-size: 1.1rem;">No tienes permisos para acceder a este recurso.</p>
        
        <?php if (isset($_SESSION['rol'])): ?>
        <div class="debug-info">
            <strong>Tu rol actual:</strong><br>
            <code><?= htmlspecialchars($_SESSION['rol']) ?></code>
        </div>
        <?php endif; ?>
        
        <a href="/venta">Volver a la pantalla de ventas</a>
        <a href="/logout">Cerrar Sesión</a>
    </div>
</body>
</html>
