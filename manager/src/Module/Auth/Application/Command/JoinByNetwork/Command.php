<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\JoinByNetwork;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public readonly string $email,
        #[Assert\NotBlank]
        public readonly string $network,
        #[Assert\NotBlank]
        public readonly string $identity
    ) {
    }
}
