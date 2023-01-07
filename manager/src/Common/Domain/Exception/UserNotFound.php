<?php

declare(strict_types=1);

namespace Hrct\Common\Application\Exception;

use Throwable;

final class UserNotFound extends AppException
{
    public const MESSAGE = 'Пользователь не найден';
    public const CODE = 'user-not-found';

    public function __construct(string $message = '', string $code = '', Throwable $previous = null)
    {
        $message = ($message) ?: self::MESSAGE;
        $code = ($code) ?: self::CODE;

        parent::__construct($message, $code, $previous);
    }
}
