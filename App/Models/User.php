<?php

namespace App\Models;

use Framework\Core\IIdentity;
use Framework\Core\Model;

class User extends Model implements IIdentity
{
    // DB columns must match these protected properties
    protected ?int $id = null;
    protected string $username;
    protected ?string $email = null;
    protected string $password_hash;
    protected ?string $created_at = null;

    // Getters and setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    // Hash and set the password
    public function setPassword(string $plain): void
    {
        $this->password_hash = password_hash($plain, PASSWORD_DEFAULT);
    }

    /**
     * Identity display name used by the framework.
     */
    public function getName(): string
    {
        return $this->username;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }
}
