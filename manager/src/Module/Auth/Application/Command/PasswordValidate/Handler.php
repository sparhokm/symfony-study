<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\PasswordValidate;

use App\Common\Application\Validator\ValidationException;
use App\Common\Application\Validator\ValidatorInterface;
use App\Module\Auth\Domain\Exception\User\PasswordIncorrect;
use App\Module\Auth\Infrastructure\Service\PasswordHasher;

final class Handler
{
    public function __construct(
        private readonly PasswordHasher $hasher,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @throws ValidationException
     * @throws PasswordIncorrect
     */
    public function handle(Command $query): void
    {
        $this->validator->validate($query);
        $this->hasher->validate($query->password, $query->passwordHash) ?: throw new PasswordIncorrect();
    }
}
