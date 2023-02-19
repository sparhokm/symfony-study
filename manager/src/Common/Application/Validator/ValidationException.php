<?php

declare(strict_types=1);

namespace App\Common\Application\Validator;

use App\Common\Application\AppException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

final class ValidationException extends AppException
{
    private const MESSAGE = 'Ошибка входных данных.';
    private readonly ConstraintViolationListInterface $violations;

    public function __construct(
        ConstraintViolationListInterface $violations,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        $message = ($message) ?: self::MESSAGE;
        parent::__construct($message, $code, $previous);
        $this->violations = $violations;
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
