<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure;

use App\Auth\Domain\Entity\User\User;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Webmozart\Assert\Assert;

final class PasswordHasher
{
    private PasswordHasherInterface $passwordHasher;

    public function __construct(PasswordHasherFactoryInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher->getPasswordHasher(User::class);
    }

    public function hash(string $password): string
    {
        Assert::notEmpty($password);

        return $this->passwordHasher->hash($password);
    }

    public function validate(string $password, string $hash): bool
    {
        return $this->passwordHasher->verify(hashedPassword: $hash, plainPassword: $password);
    }
}
