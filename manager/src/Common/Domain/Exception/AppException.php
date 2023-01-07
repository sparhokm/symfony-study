<?php

declare(strict_types=1);

namespace Hrct\Common\Application\Exception;

use Exception;
use Throwable;

class AppException extends Exception
{
    public const MESSAGE = '';
    public const CODE = '';

    private array $extra;

    public function __construct(string $message = '', string $code = '', Throwable $previous = null, array $extra = [])
    {
        parent::__construct($message, 0, $previous);

        /** @psalm-suppress InvalidPropertyAssignmentValue $code */
        $this->code = $code;
        $this->extra = $extra;
    }

    public function getExtra(): array
    {
        return $this->extra;
    }

    public function getDebugInfo(): string
    {
        return $this->getMessage() . ' ' . $this->getTraceAsString();
    }
}
