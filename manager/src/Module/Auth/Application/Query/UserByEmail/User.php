<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Query\UserByEmail;

final class User
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly ?string $passwordHash,
        public readonly string $role,
        public readonly bool $isActive,
    ) {
    }
}
