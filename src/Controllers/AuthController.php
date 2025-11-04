<?php
namespace App\Controllers;

use App\Services\UserService;
use App\Services\SessionService;

class AuthController {
    private $userService;
    
    public function __construct() {
        $this->userService = new UserService();
    }
    
    /**
     * Muestra el formulario de login
     */
    public function showLoginForm() {
        // Si ya está autenticado, redirigir al dashboard
        if (SessionService::isActive() && SessionService::validate()) {
            $this->redirectByRole(SessionService::getRole());
            return;
        }
        
        require_once __DIR__ . '/../Views/auth/login.php';
    }
    
    /**
     * Procesa el login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /login");
            exit;
        }
        
        $usuario = $_POST['usuario'] ?? '';
        $contrasena = $_POST['password'] ?? '';
        
        if (empty($usuario) || empty($contrasena)) {
            $_SESSION['login_error'] = 'Por favor, complete todos los campos';
            header("Location: /login");
            exit;
        }
        
        try {
            $user = $this->userService->authenticateUser($usuario, $contrasena);
            
            if ($user) {
                // Iniciar sesión segura de 24 horas
                SessionService::login(
                    $user->getId(),
                    $user->getUsername(),
                    $user->getRole()
                );
                
                $this->redirectByRole($user->getRole());
            } else {
                $_SESSION['login_error'] = 'Usuario o contraseña incorrectos';
                header("Location: /login");
                exit;
            }
        } catch (\Exception $e) {
            $_SESSION['login_error'] = 'Error en el sistema. Por favor, intente más tarde.';
            header("Location: /login");
            exit;
        }
    }
    
    /**
     * Cierra la sesión del usuario
     */
    public function logout() {
        SessionService::destroy();
        header("Location: /login");
        exit;
    }
    
    /**
     * Redirige al usuario según su rol
     */
    private function redirectByRole($role) {
        switch ($role) {
            case 'admin':
                header("Location: /menu");
                break;
            case 'cajero':
                header("Location: /venta");
                break;
            default:
                header("Location: /menu");
                break;
        }
        exit;
    }
}
