<?php

declare(strict_types=1);

namespace App\Http\Infrastructure\Security;

use App\Common\Application\AppException;
use App\Common\Domain\DomainException;
use App\Http\Domain\Entity\UserIdentity;
use App\Module\Auth\Application\Query\UserByEmail;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class UserProvider implements UserProviderInterface
{
    public function __construct(private readonly UserByEmail\Fetcher $userByEmailFetcher)
    {
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return $class === UserIdentity::class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        try {
            return $this->identityByUser($this->userByEmailFetcher->fetch(new UserByEmail\Query($identifier)));
        } catch (AppException|DomainException) {
            throw new UserNotFoundException();
        }
    }

    private function identityByUser(UserByEmail\User $user): UserIdentity
    {
        return new UserIdentity(
            $user->id,
            $user->email,
            $user->passwordHash,
            $user->role,
            $user->isActive,
        );
    }
}
