<?php

declare(strict_types=1);

namespace App\Module\Auth\Test\Builder;

use App\Common\Infrastructure\Uuid;
use App\Module\Auth\Domain\Entity\User\Email;
use App\Module\Auth\Domain\Entity\User\Id;
use App\Module\Auth\Domain\Entity\User\Network;
use App\Module\Auth\Domain\Entity\User\Token;
use App\Module\Auth\Domain\Entity\User\User;
use DateTimeImmutable;

final class UserBuilder
{
    private Id $id;
    private Email $email;
    private string $passwordHash = 'hash';
    private readonly DateTimeImmutable $date;
    private Token $joinConfirmToken;
    private bool $active = false;
    private ?Network $networkIdentity = null;

    public function __construct()
    {
        $this->id = Id::generate();
        $this->email = new Email('mail@example.com');
        $this->date = new DateTimeImmutable();
        $this->joinConfirmToken = new Token(Uuid::getUuid7(), $this->date->modify('+1 day'));
    }

    public function withId(Id $id): self
    {
        $clone = clone $this;
        $clone->id = $id;

        return $clone;
    }

    public function withJoinConfirmToken(Token $token): self
    {
        $clone = clone $this;
        $clone->joinConfirmToken = $token;

        return $clone;
    }

    public function withEmail(Email $email): self
    {
        $clone = clone $this;
        $clone->email = $email;

        return $clone;
    }

    public function withPasswordHash(string $passwordHash): self
    {
        $clone = clone $this;
        $clone->passwordHash = $passwordHash;

        return $clone;
    }

    public function viaNetwork(Network $network = null): self
    {
        $clone = clone $this;
        $clone->networkIdentity = $network ?? new Network('vk', '0000001');

        return $clone;
    }

    public function active(): self
    {
        $clone = clone $this;
        $clone->active = true;

        return $clone;
    }

    public function build(): User
    {
        if ($this->networkIdentity !== null) {
            return User::joinByNetwork(
                $this->id,
                $this->date,
                $this->email,
                $this->networkIdentity,
            );
        }

        $user = User::joinByEmail(
            $this->id,
            $this->date,
            $this->email,
            $this->passwordHash,
            $this->joinConfirmToken,
        );

        if ($this->active) {
            $user->confirmJoin(
                $this->joinConfirmToken->getValue(),
                $this->joinConfirmToken->getExpires()->modify('-1 day'),
            );
        }

        return $user;
    }
}
