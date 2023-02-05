<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Exception\User;

use App\Common\Domain\DomainException;
use Throwable;

final class NetworkAlreadyAttached extends DomainException
{
    private const MESSAGE = 'Социальная сеть уже подключена.';

    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = ($message) ?: self::MESSAGE;
        parent::__construct($message, $code, $previous);
    }
}
