<?php

declare(strict_types=1);

namespace Hrct\Common\Application\Exception;

use Throwable;

final class UserNotFound extends AppException
{
    private const MESSAGE = 'Пользователь не найден';

    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = ($message) ?: self::MESSAGE;
        parent::__construct($message, $code, $previous);
    }
}
