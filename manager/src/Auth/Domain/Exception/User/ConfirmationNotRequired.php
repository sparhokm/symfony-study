<?php

declare(strict_types=1);

namespace App\Auth\Domain\Exception\User;

use App\Common\Domain\Exception\AppException;
use Throwable;

final class ConfirmationNotRequired extends AppException
{
    private const MESSAGE = 'Подтверждение не требуется.';

    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = ($message) ?: self::MESSAGE;
        parent::__construct($message, $code, $previous);
    }
}
