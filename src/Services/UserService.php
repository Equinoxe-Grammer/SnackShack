<?php
namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    private UserRepository $users;

    public function __construct(?UserRepository $users = null)
    {
        $this->users = $users ?? new UserRepository();
    }

    public function list(): array
    {
        return $this->users->findAll();
    }
    
    /**
     * Autentica un usuario con sus credenciales
     * 
     * @param string $username Nombre de usuario
     * @param string $password Contraseña
     * @return \App\Models\User|null Usuario autenticado o null si las credenciales son inválidas
     */
    public function authenticateUser(string $username, string $password): ?\App\Models\User
    {
        $user = $this->users->findByUsername($username);
        
        if (!$user) {
            return null;
        }
        
        // Verificar contraseña
        if ($this->verifyPassword($password, $user->getPasswordPlain())) {
            return $user;
        }
        
        return null;
    }
    
    /**
     * Verifica si la contraseña ingresada coincide con la almacenada
     * 
     * @param string $inputPassword Contraseña ingresada
     * @param string $storedPassword Contraseña almacenada
     * @return bool
     */
    private function verifyPassword(string $inputPassword, string $storedPassword): bool {
        // Verificar si la contraseña está hasheada (empieza con $2y$ para bcrypt)
        if (strpos($storedPassword, '$2y$') === 0) {
            // Contraseña hasheada, usar password_verify
            return password_verify($inputPassword, $storedPassword);
        }
        
        // Contraseña en texto plano (para transición)
        // Verificar si coincide y hashear si es correcto
        if ($inputPassword === $storedPassword) {
            // TODO: Aquí podrías actualizar la contraseña a hash
            // $this->users->updatePasswordHash($userId, password_hash($inputPassword, PASSWORD_BCRYPT));
            return true;
        }
        
        return false;
    }
    
    /**
     * Hashea una contraseña de forma segura
     * 
     * @param string $password Contraseña en texto plano
     * @return string Contraseña hasheada
     */
    public function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
