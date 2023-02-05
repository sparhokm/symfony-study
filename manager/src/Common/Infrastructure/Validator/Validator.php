<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Validator;

use App\Common\Application\Validator\ValidationException;
use App\Common\Application\Validator\ValidatorInterface;

final class Validator implements ValidatorInterface
{
    public function __construct(private readonly \Symfony\Component\Validator\Validator\ValidatorInterface $validator)
    {
    }

    public function validate(object $object): void
    {
        $violations = $this->validator->validate($object);
        if ($violations->count() > 0) {
            throw new ValidationException($violations);
        }
    }
}
