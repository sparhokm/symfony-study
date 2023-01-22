<?php

declare(strict_types=1);

namespace App\Auth\Domain\Exception\User;

use Throwable;
use App\Common\Domain\Exception\AppException;

final class RoleAlreadySame extends AppException
{
    private const MESSAGE = 'У пользователя уже задана эта роль.';

    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = ($message) ?: self::MESSAGE;
        parent::__construct($message, $code, $previous);
    }
}
