<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\PasswordReset\Confirm;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $token,
        #[Assert\NotBlank]
        public readonly string $password
    ) {
    }
}
