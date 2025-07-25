<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class ExternalAuthUser implements UserInterface
{
    private int $id;

    private string $email;

    /**
     * @var list<string> The user roles
     */
    private array $roles;

    private ?string $phone;

    private string $name;

    private ?string $token;

    public function __construct(array $userData)
    {
        $this->id = $userData['id'];
        $this->email = $userData['email'];
        $this->roles = $userData['roles'] ?? ['ROLE_USER'];
        $this->phone = $userData['phone'] ?? null;
        $this->name = $userData['name'];
        $this->token = $userData['token'] ?? null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->getToken();
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }
}
