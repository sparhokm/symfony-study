<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\JoinByEmail\Request;

use App\Auth\Domain\Entity\User\Email;
use App\Auth\Domain\Entity\User\Id;
use App\Auth\Domain\Entity\User\User;
use App\Auth\Domain\Exception\User\UserExists;
use App\Auth\Infrastructure\Service\JoinConfirmationSender;
use App\Auth\Infrastructure\Service\PasswordHasher;
use App\Auth\Infrastructure\Service\Tolenizer\Tokenizer;
use App\Auth\Infrastructure\UserRepository;
use App\Common\Application\FlusherInterface;
use DateTimeImmutable;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class Handler
{
    public function __construct(
        private UserRepository $users,
        private PasswordHasher $hasher,
        private Tokenizer $tokenizer,
        private FlusherInterface $flusher,
        private JoinConfirmationSender\Sender $sender,
        private ValidatorInterface $validator
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
