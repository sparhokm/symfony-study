<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Exception\Token;

use App\Common\Domain\DomainException;
use Throwable;

final class Expired extends DomainException
{
    private const MESSAGE = 'Токен просрочен.';

    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = ($message) ?: self::MESSAGE;
        parent::__construct($message, $code, $previous);
    }
}
