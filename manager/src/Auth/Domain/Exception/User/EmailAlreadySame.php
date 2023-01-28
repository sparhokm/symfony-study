<?php

declare(strict_types=1);

namespace App\Auth\Domain\Exception\User;

use App\Common\Domain\Exception\AppException;
use Throwable;

final class EmailAlreadySame extends AppException
{
    private const MESSAGE = 'Новый email совпадает со старым.';

    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = ($message) ?: self::MESSAGE;
        parent::__construct($message, $code, $previous);
    }
}
