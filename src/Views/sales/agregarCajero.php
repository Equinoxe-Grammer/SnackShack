<?php
use App\Database\Connection;
use App\Repositories\UserRepository;
use App\Middleware\CsrfMiddleware;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF Token
$csrfToken = CsrfMiddleware::getToken();

$success = null;
$error = null;
// Leer flashes de sesión (PRG)
if (isset($_SESSION['flash_success'])) { $success = $_SESSION['flash_success']; unset($_SESSION['flash_success']); }
if (isset($_SESSION['flash_error'])) { $error = $_SESSION['flash_error']; unset($_SESSION['flash_error']); }

// ----------------------
// PROCESAR FORMULARIO
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'create';
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        $error = 'Token CSRF inválido. Actualiza la página e inténtalo de nuevo.';
    } else {
        try {
            $db = Connection::get();

            if ($action === 'create') {
                $nombre = trim($_POST['nombre'] ?? '');
                $passwordPlain = $_POST['password'] ?? '';
                if ($nombre === '' || $passwordPlain === '') {
                    throw new \RuntimeException('Por favor complete todos los campos');
                }
                // Validar unicidad
                $stmtCheck = $db->prepare("SELECT 1 FROM usuarios WHERE usuario = :u LIMIT 1");
                $stmtCheck->execute([':u' => $nombre]);
                if ($stmtCheck->fetchColumn()) {
                    throw new \RuntimeException('El usuario ya existe, elige otro nombre');
                }
                $hashedPassword = password_hash($passwordPlain, PASSWORD_DEFAULT);
                $sql = "INSERT INTO usuarios (usuario, rol, contrasena_hash) VALUES (:usuario, 'cajero', :password)";
                $stmt = $db->prepare($sql);
                $ok = $stmt->execute([':usuario' => $nombre, ':password' => $hashedPassword]);
                if (!$ok) throw new \RuntimeException('No se pudo crear el cajero');
                $_SESSION['flash_success'] = 'Cajero creado correctamente';
                header('Location: /agregarCajero');
                exit;
            } elseif ($action === 'update') {
                $id = (int)($_POST['id'] ?? 0);
                $nombre = trim($_POST['nombre'] ?? '');
                if ($id <= 0 || $nombre === '') {
                    throw new \RuntimeException('Datos inválidos para actualizar');
                }
                $stmt = $db->prepare("UPDATE usuarios SET usuario = :usuario WHERE usuario_id = :id AND rol = 'cajero'");
                $ok = $stmt->execute([':usuario' => $nombre, ':id' => $id]);
                if (!$ok) throw new \RuntimeException('No se pudo actualizar el cajero');
                // Reset de contraseña opcional
                $newPass = trim($_POST['password'] ?? '');
                if ($newPass !== '') {
                    $hash = password_hash($newPass, PASSWORD_DEFAULT);
                    $stmt2 = $db->prepare("UPDATE usuarios SET contrasena_hash = :pwd WHERE usuario_id = :id AND rol = 'cajero'");
                    $stmt2->execute([':pwd' => $hash, ':id' => $id]);
                }
                $_SESSION['flash_success'] = 'Cajero actualizado correctamente';
                header('Location: /agregarCajero');
                exit;
            } elseif ($action === 'delete') {
                $id = (int)($_POST['id'] ?? 0);
                if ($id <= 0) throw new \RuntimeException('ID inválido');
                // Evitar borrar el usuario en sesión
                if (isset($_SESSION['usuario_id']) && (int)$_SESSION['usuario_id'] === $id) {
                    throw new \RuntimeException('No puedes eliminar el usuario en sesión');
                }
                $stmt = $db->prepare("DELETE FROM usuarios WHERE usuario_id = :id AND rol = 'cajero'");
                $ok = $stmt->execute([':id' => $id]);
                if (!$ok || $stmt->rowCount() === 0) throw new \RuntimeException('No se encontró el cajero o no se pudo eliminar');
                $_SESSION['flash_success'] = 'Cajero eliminado';
                header('Location: /agregarCajero');
                exit;
            } elseif ($action === 'delete_invalid') {
                // Eliminar usuarios inválidos (no admin) con username vacío o rol inválido
                $sql = "DELETE FROM usuarios 
                        WHERE (COALESCE(rol,'') <> 'admin') 
                          AND (
                                COALESCE(TRIM(usuario),'') = ''
                             OR  TRIM(COALESCE(rol,'')) NOT IN ('admin','cajero')
                          )";
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $count = $stmt->rowCount();
                $_SESSION['flash_success'] = $count . ' usuario(s) inválido(s) eliminado(s)';
                header('Location: /agregarCajero');
                exit;
            }
        } catch (\Throwable $e) {
            // Mapear error de unicidad a mensaje más claro
            $msg = $e->getMessage();
            if (stripos($msg, 'UNIQUE constraint failed') !== false || stripos($msg, '23000') !== false) {
                $msg = 'El usuario ya existe. Elige otro nombre.';
            }
            $_SESSION['flash_error'] = 'Error: ' . $msg;
            header('Location: /agregarCajero');
            exit;
        }
    }
}

// ----------------------
// OBTENER USUARIOS
// ----------------------
$userRepo = new UserRepository();
$usuarios = $userRepo->findAll();

// Contar usuarios inválidos detectados en memoria (por seguridad visual)
$invalidCount = 0;
foreach ($usuarios as $u) {
    $name = trim($u->getUsername() ?? '');
    $role = trim($u->getRole() ?? '');
    if ($name === '' || ($role !== 'admin' && $role !== 'cajero')) {
        $invalidCount++;
    }
}

// Modo edición si viene por query
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editUser = null;
if ($editId > 0) {
    foreach ($usuarios as $u) {
        if (method_exists($u, 'getId') && $u->getId() === $editId) {
            $editUser = $u; break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
    <title>Agregar Cajero</title>
    <link rel="icon" type="image/svg+xml" href="/assets/favicon.svg">
    <link rel="stylesheet" href="/css/theme.css">
    <link rel="stylesheet" href="/css/catalogofunciones.css">
    <link rel="stylesheet" href="/css/agregarcajero.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    <script src="/js/theme-toggle.js"></script>
</head>
<body>
    <div class="pos-container">
        <?php $active='cajeros'; include __DIR__ . '/../partials/sidebar.php'; ?>

        <main class="main-content">
            <header class="dashboard-header">
                <div>
                    <h1><i class="fas fa-users"></i> Gestión de Cajeros</h1>
                    <p class="subtitle">Administra los usuarios con rol de cajero del sistema</p>
                </div>
            </header>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <section class="users-section">
                <div class="section-header">
                    <h2><i class="fas fa-list"></i> Usuarios Registrados</h2>
                    <div style="display:flex; align-items:center; gap:.6rem;">
                        <span class="users-count"><?= count($usuarios) ?> usuario(s)</span>
                        <?php if ($invalidCount > 0): ?>
                        <form id="deleteInvalidForm" method="post" action="" style="display:none;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                            <input type="hidden" name="action" value="delete_invalid">
                        </form>
                        <button type="button" id="cleanInvalidBtn" class="action-btn delete" title="Eliminar usuarios inválidos">
                            <i class="fas fa-user-slash"></i> Limpiar inválidos (<?= (int)$invalidCount ?>)
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="users-table-card">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-user"></i> Usuario</th>
                                <th><i class="fas fa-tag"></i> Rol</th>
                                <th><i class="fas fa-cog"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($usuarios)): ?>
                                <?php foreach ($usuarios as $u): ?>
                                    <tr>
                                        <td class="user-name"><?= htmlspecialchars($u->getUsername()) ?></td>
                                        <td>
                                            <span class="role-badge <?= $u->getRole() === 'admin' ? 'role-admin' : 'role-cajero' ?>">
                                                <i class="fas fa-<?= $u->getRole() === 'admin' ? 'crown' : 'cash-register' ?>"></i>
                                                <?= htmlspecialchars($u->getRole()) ?>
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                            <a href="/agregarCajero?edit=<?= (int)$u->getId() ?>" class="action-btn edit" title="Editar usuario">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($u->getRole() === 'cajero'): ?>
                                            <form method="post" action="" style="display:inline-block;" class="delete-cajero-form" data-username="<?= htmlspecialchars($u->getUsername()) ?>">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= (int)$u->getId() ?>">
                                                <button type="submit" class="action-btn delete" title="Eliminar cajero">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="empty-state">
                                        <i class="fas fa-user-slash"></i>
                                        <span>No hay usuarios registrados</span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <aside class="cart-sidebar">
            <div class="cart-header">
                <h2>
                    <i class="fas fa-<?= $editUser ? 'user-edit' : 'user-plus' ?>"></i>
                    <?= $editUser ? 'Editar Cajero' : 'Agregar Cajero' ?>
                </h2>
                <?php if ($editUser): ?>
                    <a href="/agregarCajero" class="cancel-edit" title="Cancelar edición">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>

            <div class="cart-items">
                <?php if ($editUser): ?>
                <form id="editCajeroForm" method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= (int)$editUser->getId() ?>">
                    
                    <div class="form-group">
                        <label for="nombre">
                            <i class="fas fa-user"></i>
                            Nombre de Usuario
                        </label>
                        <input type="text" id="nombre" name="nombre" required placeholder="Nombre del cajero" value="<?= htmlspecialchars($editUser->getUsername()) ?>">
                    </div>

                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i>
                            Nueva Contraseña
                        </label>
                        <input type="password" id="password" name="password" placeholder="Dejar vacío para conservar">
                        <small class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Solo completa si deseas cambiar la contraseña
                        </small>
                    </div>

                    <div class="cart-actions">
                        <button type="submit" class="btn-process">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="/agregarCajero" class="btn-clear">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
                <?php else: ?>
                <form id="addCajeroForm" method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="form-group">
                        <label for="nombre">
                            <i class="fas fa-user"></i>
                            Nombre de Usuario
                        </label>
                        <input type="text" id="nombre" name="nombre" required placeholder="Nombre del cajero" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i>
                            Contraseña
                        </label>
                        <input type="password" id="password" name="password" required placeholder="Contraseña segura" autocomplete="new-password">
                        <small class="form-hint">
                            <i class="fas fa-shield-alt"></i>
                            Mínimo 6 caracteres recomendados
                        </small>
                    </div>

                    <div class="cart-actions">
                        <button type="submit" class="btn-process">
                            <i class="fas fa-check"></i> Agregar Cajero
                        </button>
                        <button type="reset" class="btn-clear">
                            <i class="fas fa-eraser"></i> Limpiar
                        </button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </aside>
    </div>

    <!-- Modal de confirmación para eliminar cajero -->
    <div id="deleteCajeroModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-trash"></i> Eliminar cajero</h3>
            </div>
            <div class="modal-body">
                <p>¿Seguro que deseas eliminar a <strong id="cajero-name"></strong>?<br><b>Esta acción no se puede deshacer.</b></p>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancelDeleteBtn" class="btn">Cancelar</button>
                <button type="button" id="confirmDeleteBtn" class="btn danger"><i class="fas fa-trash"></i> Eliminar</button>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para limpiar usuarios inválidos -->
    <div id="cleanInvalidModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user-slash"></i> Eliminar usuarios inválidos</h3>
            </div>
            <div class="modal-body">
                <p>Se eliminarán todos los usuarios no válidos (no admin) con nombre vacío o rol desconocido.<br>
                <b>Esta acción no se puede deshacer.</b></p>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancelCleanInvalidBtn" class="btn">Cancelar</button>
                <button type="button" id="confirmCleanInvalidBtn" class="btn danger"><i class="fas fa-user-slash"></i> Eliminar</button>
            </div>
        </div>
    </div>

    <script>
    let deleteFormToSubmit = null;
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-cajero-form').forEach(form => {
            const btn = form.querySelector('.action-btn.delete');
            if (btn) {
                btn.addEventListener('click', function(e) {
                    console.log('[MODAL] Click en botón eliminar:', form.dataset.username);
                    e.preventDefault();
                    // Ignorar si no hay username válido
                    const username = (form.dataset.username || '').trim();
                    if (!username) {
                        console.warn('[MODAL] Ignorado: username vacío en form de eliminación');
                        return;
                    }
                    deleteFormToSubmit = form;
                    document.getElementById('cajero-name').textContent = username;
                    document.getElementById('deleteCajeroModal').classList.add('show');
                });
            }
            // Bloquea el submit normal (por enter/autocompletado)
            form.addEventListener('submit', function(e) {
                if (deleteFormToSubmit !== form) {
                    console.log('[MODAL] Submit bloqueado (no por click):', form.dataset.username);
                    e.preventDefault(); // Solo permite submit si fue confirmado por el modal
                } else {
                    console.log('[MODAL] Submit permitido tras confirmación:', form.dataset.username);
                }
            });
        });

        // Limpiar usuarios inválidos (si existe el botón)
        const cleanBtn = document.getElementById('cleanInvalidBtn');
        if (cleanBtn) {
            cleanBtn.addEventListener('click', function(){
                document.getElementById('cleanInvalidModal').classList.add('show');
            });
            document.getElementById('cancelCleanInvalidBtn').onclick = function(){
                document.getElementById('cleanInvalidModal').classList.remove('show');
            };
            document.getElementById('confirmCleanInvalidBtn').onclick = function(){
                const f = document.getElementById('deleteInvalidForm');
                const modal = document.getElementById('cleanInvalidModal');
                this.disabled = true;
                modal.classList.remove('show');
                setTimeout(() => { if (f) f.submit(); this.disabled = false; }, 10);
            };
            document.getElementById('cleanInvalidModal').addEventListener('click', function(e){
                if (e.target === this) this.classList.remove('show');
            });
        }

        document.getElementById('cancelDeleteBtn').onclick = function() {
            document.getElementById('deleteCajeroModal').classList.remove('show');
            deleteFormToSubmit = null;
        };
        document.getElementById('confirmDeleteBtn').onclick = function() {
            const modal = document.getElementById('deleteCajeroModal');
            const btn = this;
            if (deleteFormToSubmit) {
                btn.disabled = true;
                modal.classList.remove('show');
                setTimeout(() => {
                    deleteFormToSubmit.submit();
                    deleteFormToSubmit = null;
                    btn.disabled = false;
                }, 10);
            }
        };
        document.getElementById('deleteCajeroModal').addEventListener('click', function(e){
            if(e.target === this){ 
                this.classList.remove('show'); 
                deleteFormToSubmit = null;
            }
        });
    });
    </script>
</body>
</html>
