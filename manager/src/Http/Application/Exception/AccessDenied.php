<?php

declare(strict_types=1);

namespace App\Http\Application\Exception;

use App\Common\Application\AppException;
use Throwable;

final class AccessDenied extends AppException
{
    private const MESSAGE = 'Доступ запрещен.';

    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = ($message) ?: self::MESSAGE;
        parent::__construct($message, $code, $previous);
    }
}
