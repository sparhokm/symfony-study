<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\PasswordReset\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank]
        public readonly string $email
    ) {
    }
}
