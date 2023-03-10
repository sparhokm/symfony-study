<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\JoinByEmail\Confirm;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $token
    ) {
    }
}
