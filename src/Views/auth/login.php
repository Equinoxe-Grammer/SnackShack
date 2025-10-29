<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Snackshop</title>
    <link rel="icon" type="image/svg+xml" href="/assets/favicon.svg">
    <link rel="stylesheet" href="/css/theme.css?v=20251013">
    <link rel="stylesheet" href="/css/Login.css">
    <link rel="stylesheet" href="/css/login-validation.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-container">
                <img src="/assets/logo_snack.jpg" alt="Snackshop Logo">
            </div>
            <h2>Iniciar Sesión</h2>

            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['login_error']) ?>
                    <?php unset($_SESSION['login_error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['csrf_error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['csrf_error']) ?>
                    <?php unset($_SESSION['csrf_error']); ?>
                </div>
            <?php endif; ?>

            <form action="/login" method="POST" id="loginForm">
                <?= \App\Middleware\CsrfMiddleware::field() ?>
                
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="usuario" placeholder="Ingresa tu usuario" required class="<?= isset($_SESSION['login_error']) ? 'input-error' : '' ?>">

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required class="<?= isset($_SESSION['login_error']) ? 'input-error' : '' ?>">

                <button type="submit">Iniciar Sesión</button>
            </form>
        </div>
    </div>
    <script>
        // Animación shake si hay error de login
        <?php if (isset($_SESSION['login_error']) || isset($_SESSION['csrf_error'])): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const loginCard = document.querySelector('.login-card');
            loginCard.classList.add('shake-error');
            setTimeout(() => loginCard.classList.remove('shake-error'), 600);
        });
        <?php endif; ?>
    </script>
</body>
</html>
