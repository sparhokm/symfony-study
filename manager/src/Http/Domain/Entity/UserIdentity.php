<?php

declare(strict_types=1);

namespace App\Http\Domain\Entity;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserIdentity implements UserInterface, EquatableInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private readonly string $id,
        private readonly string $email,
        private readonly ?string $password,
        private readonly string $role,
        private readonly bool $isActive
    ) {
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return [$this->role];
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function eraseCredentials(): void
    {
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        return
            $this->id === $user->id
            && $this->email === $user->email
            && $this->password === $user->password
            && $this->role === $user->role
            && $this->isActive === $user->isActive;
    }
}
