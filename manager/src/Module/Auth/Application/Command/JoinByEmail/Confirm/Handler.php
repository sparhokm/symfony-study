<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\JoinByEmail\Confirm;

use App\Common\Application\FlusherInterface;
use App\Common\Application\Validator\ValidatorInterface;
use App\Module\Auth\Domain\Exception\Token;
use App\Module\Auth\Infrastructure\UserRepository;
use DateTimeImmutable;

final class Handler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly FlusherInterface $flusher,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function handle(Command $command): void
    {
        $this->validator->validate($command);

        if (!$user = $this->userRepository->findByConfirmToken($command->token)) {
            throw new Token\Invalid();
        }

        $user->confirmJoin($command->token, new DateTimeImmutable());

        $this->flusher->flush();
    }
}
