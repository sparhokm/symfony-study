<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Query\UserByEmail;

use App\Common\Application\Validator\ValidationException;
use App\Common\Application\Validator\ValidatorInterface;
use App\Module\Auth\Domain\Entity\User\Email;
use App\Module\Auth\Domain\Exception\User\UserNotFound;
use App\Module\Auth\Infrastructure\UserRepository;

final class Fetcher
{
    public function __construct(
        public readonly UserRepository $userRepository,
        public readonly ValidatorInterface $validator
    ) {
    }

    /**
     * @throws ValidationException
     * @throws UserNotFound
     */
    public function fetch(Query $query): User
    {
        $this->validator->validate($query);

        $user = $this->userRepository->getByEmail(new Email($query->email));

        return new User(
            $user->getId()->getValue(),
            $user->getEmail()->getValue(),
            $user->getPasswordHash(),
            $user->getRole()->getValue(),
            $user->isActive()
        );
    }
}
