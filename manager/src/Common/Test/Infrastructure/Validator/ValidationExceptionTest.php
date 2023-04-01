<?php

declare(strict_types=1);

namespace App\Common\Test\Infrastructure\Validator;

use App\Common\Application\Validator\ValidationException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @covers \App\Common\Application\Validator\ValidationException
 *
 * @internal
 */
final class ValidationExceptionTest extends TestCase
{
    public function testValid(): void
    {
        $exception = new ValidationException(
            $violations = new ConstraintViolationList(),
        );

        self::assertEquals('Ошибка входных данных.', $exception->getMessage());
        self::assertEquals($violations, $exception->getViolations());
    }
}
