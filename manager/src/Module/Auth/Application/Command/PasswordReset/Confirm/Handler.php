<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\PasswordReset\Confirm;

use App\Common\Application\Validator\ValidatorInterface;
use App\Common\Infrastructure\Flusher;
use App\Module\Auth\Domain\Exception\Token;
use App\Module\Auth\Infrastructure\Service\PasswordHasher;
use App\Module\Auth\Infrastructure\UserRepository;
use DateTimeImmutable;

final class Handler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PasswordHasher $hasher,
        private readonly Flusher $flusher,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function handle(Command $command): void
    {
        $this->validator->validate($command);

        if (!$user = $this->userRepository->findByPasswordResetToken($command->token)) {
            throw new Token\Invalid();
        }

        $user->resetPassword(
            $command->token,
            new DateTimeImmutable(),
            $this->hasher->hash($command->password),
        );

        $this->flusher->flush();
    }
}
