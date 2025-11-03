<?php
namespace App\Models;

class User
{
    public int $id;
    public string $username;
    public string $role;
    public string $createdAt;
    private ?string $passwordHash;

    public function __construct(int $id, string $username, string $role, string $createdAt, ?string $passwordHash = null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->role = $role;
        $this->createdAt = $createdAt;
        $this->passwordHash = $passwordHash;
    }
    
    // Getters
    public function getId(): int
    {
        return $this->id;
    }
    
    public function getUsername(): string
    {
        return $this->username;
    }
    
    public function getRole(): string
    {
        return $this->role;
    }
    
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
    
    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }
    
}
