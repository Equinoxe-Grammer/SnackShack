<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <script src="/js/theme-toggle.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        <h1>404</h1>
        <p>¡Oops! La página que buscas no existe.</p>
        
        <?php if (isset($_SERVER['REQUEST_URI'])): ?>
        <div class="debug-info">
            <strong>Información de Debug:</strong><br>
            <code>Ruta solicitada: <?= htmlspecialchars($_SERVER['REQUEST_URI']) ?></code><br>
            <code>Método: <?= htmlspecialchars($_SERVER['REQUEST_METHOD']) ?></code>
        </div>
        <?php endif; ?>
        
        <a href="/login">Ir al Login</a>
        <a href="/test-router.php">Test del Router</a>
    </div>
</body>
</html>
