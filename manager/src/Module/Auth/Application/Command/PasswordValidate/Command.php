<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\PasswordValidate;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $password,
        #[Assert\NotBlank]
        public readonly string $passwordHash
    ) {
    }
}
