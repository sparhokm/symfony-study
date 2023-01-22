<?php

declare(strict_types=1);

namespace App\Auth\Domain\Exception\Token;

use Throwable;
use App\Common\Domain\Exception\AppException;

final class Expired extends AppException
{
    private const MESSAGE = 'Токен просрочен.';

    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = ($message) ?: self::MESSAGE;
        parent::__construct($message, $code, $previous);
    }
}
