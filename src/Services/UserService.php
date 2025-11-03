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

        $hash = $user->getPasswordHash();
        if (is_string($hash) && $hash !== '') {
            // Normal path: verify against hash
            if (password_verify($password, $hash)) {
                return $user;
            }
            return null;
        }

        // Legacy path: no hash present -> attempt on-the-fly migration if plaintext matches
        // Create a new hash and ask repository to migrate atomically when plaintext matches
        $newHash = $this->hashPassword($password);
        $migrated = $this->users->migratePlaintextPasswordIfMatches($username, $password, $newHash);
        if ($migrated) {
            // Refetch user to get fresh hash and proceed
            $refetched = $this->users->findByUsername($username);
            if ($refetched && ($refetched->getPasswordHash() !== null) && password_verify($password, $refetched->getPasswordHash())) {
                return $refetched;
            }
        }

        return null;
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
