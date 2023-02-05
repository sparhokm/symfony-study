<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\PasswordReset\Request;

use App\Common\Application\FlusherInterface;
use App\Common\Application\Validator\ValidatorInterface;
use App\Module\Auth\Domain\Entity\User\Email;
use App\Module\Auth\Infrastructure\Service\PasswordResetTokenSender\Sender;
use App\Module\Auth\Infrastructure\Service\Tolenizer\Tokenizer;
use App\Module\Auth\Infrastructure\UserRepository;
use DateTimeImmutable;

final class Handler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly Tokenizer $tokenizer,
        private readonly FlusherInterface $flusher,
        private readonly Sender $sender,
        private readonly ValidatorInterface $validator
    ) {
    }

    public function handle(Command $command): void
    {
        $this->validator->validate($command);

        $email = new Email($command->email);

        $user = $this->userRepository->getByEmail($email);

        $date = new DateTimeImmutable();

        $user->requestPasswordReset(
            $token = $this->tokenizer->generate($date),
            $date
        );

        $this->flusher->flush();

        $this->sender->send($email, $token);
    }
}
