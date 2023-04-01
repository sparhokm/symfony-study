<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\JoinByNetwork;

use App\Common\Application\FlusherInterface;
use App\Module\Auth\Domain\Entity\User\Email;
use App\Module\Auth\Domain\Entity\User\Id;
use App\Module\Auth\Domain\Entity\User\Network;
use App\Module\Auth\Domain\Entity\User\User;
use App\Module\Auth\Domain\Exception\User\NetworkAlreadyExists;
use App\Module\Auth\Domain\Exception\User\UserExists;
use App\Module\Auth\Infrastructure\UserRepository;
use DateTimeImmutable;

final class Handler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly FlusherInterface $flusher
    ) {
    }

    public function handle(Command $command): void
    {
        $network = new Network($command->network, $command->identity);
        $email = new Email($command->email);

        if ($this->userRepository->hasByNetwork($network)) {
            throw new NetworkAlreadyExists();
        }

        if ($this->userRepository->hasByEmail($email)) {
            throw new UserExists();
        }

        $user = User::joinByNetwork(
            Id::generate(),
            new DateTimeImmutable(),
            $email,
            $network,
        );

        $this->userRepository->add($user);

        $this->flusher->flush();
    }
}
