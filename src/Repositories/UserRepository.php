<?php
namespace App\Repositories;

use App\Database\Connection;
use App\Models\User;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Connection::get();
    }

    /**
     * @return User[]
     */
    public function findAll(): array
    {
        $sql = 'SELECT usuario_id, usuario, rol, fecha_creacion FROM usuarios ORDER BY usuario';
        $stmt = $this->db->query($sql);

        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User(
                (int) $row['usuario_id'],
                $row['usuario'] ?? '',
                $row['rol'] ?? 'cajero',
                $row['fecha_creacion'] ?? ''
            );
        }

        return $users;
    }
    
    /**
     * Busca un usuario por su nombre de usuario
     * 
     * @param string $username Nombre de usuario
     * @return User|null Usuario encontrado o null
     */
    public function findByUsername(string $username): ?User
    {
        $sql = 'SELECT usuario_id, usuario, rol, fecha_creacion, 
                       contrasena_hash
                FROM usuarios 
                WHERE usuario = :username 
                LIMIT 1';
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return new User(
            (int) $row['usuario_id'],
            $row['usuario'] ?? '',
            $row['rol'] ?? 'cajero',
            $row['fecha_creacion'] ?? '',
            $row['contrasena_hash'] ?? null
        );
    }
    
    /**
     * Encuentra un usuario por su ID
     */
    public function findById(int $userId): ?User
    {
        $sql = 'SELECT usuario_id, usuario, rol, fecha_creacion, 
                       contrasena_hash
                FROM usuarios 
                WHERE usuario_id = :id 
                LIMIT 1';
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $userId]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return new User(
            (int) $row['usuario_id'],
            $row['usuario'] ?? '',
            $row['rol'] ?? 'cajero',
            $row['fecha_creacion'] ?? '',
            $row['contrasena_hash'] ?? null
        );
    }
    
    /**
     * Crea un nuevo usuario
     */
    public function create(string $username, string $passwordHash, string $rol = 'cajero'): int
    {
        $sql = 'INSERT INTO usuarios (usuario, contrasena_hash, rol, fecha_creacion) 
                VALUES (:username, :password, :rol, datetime("now"))';
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'password' => $passwordHash,
            'rol' => $rol
        ]);
        
        return (int) $this->db->lastInsertId();
    }
    
    /**
     * Actualiza un usuario existente
     */
    public function update(int $userId, string $username, string $rol, ?string $newPasswordHash = null): bool
    {
        if ($newPasswordHash !== null) {
            $sql = 'UPDATE usuarios 
                    SET usuario = :username, rol = :rol, contrasena_hash = :password 
                    WHERE usuario_id = :id';
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'id' => $userId,
                'username' => $username,
                'rol' => $rol,
                'password' => $newPasswordHash
            ]);
        } else {
            $sql = 'UPDATE usuarios 
                    SET usuario = :username, rol = :rol 
                    WHERE usuario_id = :id';
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'id' => $userId,
                'username' => $username,
                'rol' => $rol
            ]);
        }
    }
    
    /**
     * Elimina un usuario
     */
    public function delete(int $userId): bool
    {
        $sql = 'DELETE FROM usuarios WHERE usuario_id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $userId]);
    }
    
    /**
     * Actualiza solo el hash de contraseña de un usuario
     */
    public function updatePasswordHash(int $userId, string $passwordHash): bool
    {
        $sql = 'UPDATE usuarios SET contrasena_hash = :password WHERE usuario_id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $userId,
            'password' => $passwordHash
        ]);
    }

    /**
     * Migrates a legacy plaintext password to a hashed password if the provided
     * input matches the stored plaintext. This performs the comparison at the
     * database level without exposing plaintext to application objects.
     * Returns true if the migration was performed.
     */
    public function migratePlaintextPasswordIfMatches(string $username, string $inputPassword, string $newHash): bool
    {
        $sql = 'UPDATE usuarios
                SET contrasena_hash = :hash, contrasena_plain = NULL
                WHERE usuario = :username AND contrasena_plain = :plain';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':hash', $newHash);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':plain', $inputPassword);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
