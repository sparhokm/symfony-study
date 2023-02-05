<?php

namespace App\Common\Application\Validator;

interface ValidatorInterface
{
    /** @throws ValidationException */
    public function validate(object $object): void;
}
