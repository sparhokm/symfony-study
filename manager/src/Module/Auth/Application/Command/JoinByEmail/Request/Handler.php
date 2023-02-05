<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\JoinByEmail\Request;

use App\Common\Application\FlusherInterface;
use App\Common\Application\Validator\ValidatorInterface;
use App\Module\Auth\Domain\Entity\User\Email;
use App\Module\Auth\Domain\Entity\User\Id;
use App\Module\Auth\Domain\Entity\User\User;
use App\Module\Auth\Domain\Exception\User\UserExists;
use App\Module\Auth\Infrastructure\Service\JoinConfirmationSender\Sender;
use App\Module\Auth\Infrastructure\Service\PasswordHasher;
use App\Module\Auth\Infrastructure\Service\Tolenizer\Tokenizer;
use App\Module\Auth\Infrastructure\UserRepository;
use DateTimeImmutable;

final class Handler
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly PasswordHasher $hasher,
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

        if ($this->users->hasByEmail($email)) {
            throw new UserExists();
        }

        $date = new DateTimeImmutable();

        $user = User::joinUpByEmail(
            Id::generate(),
            $date,
            $email,
            $this->hasher->hash($command->password),
            $token = $this->tokenizer->generate($date)
        );

        $this->users->add($user);

        $this->flusher->flush();

        $this->sender->send($email, $token);
    }
}
