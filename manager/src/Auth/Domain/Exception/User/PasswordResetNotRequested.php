<?php

declare(strict_types=1);

namespace App\Auth\Domain\Exception\User;

use Throwable;
use App\Common\Domain\Exception\AppException;

final class PasswordResetNotRequested extends AppException
{
    private const MESSAGE = 'Запрос сброса пароля не отправлялся.';

    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = ($message) ?: self::MESSAGE;
        parent::__construct($message, $code, $previous);
    }
}
