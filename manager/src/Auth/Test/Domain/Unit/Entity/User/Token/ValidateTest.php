<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\Token;

use App\Auth\Domain\Entity\User\Token;
use App\Common\Infrastructure\Uuid;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Auth\Domain\Entity\User\Token::validate
 *
 * @internal
 */
final class ValidateTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testSuccess(): void
    {
        $token = new Token(
            $value = Uuid::getUuid(),
            $expires = new DateTimeImmutable()
        );

        $token->validate($value, $expires->modify('-1 secs'));
    }

    public function testWrong(): void
    {
        $token = new Token(
            Uuid::getUuid(),
            $expires = new DateTimeImmutable()
        );

        $this->expectExceptionMessage('Token is invalid.');
        $token->validate(Uuid::getUuid(), $expires->modify('-1 secs'));
    }

    public function testExpired(): void
    {
        $token = new Token(
            $value = Uuid::getUuid(),
            $expires = new DateTimeImmutable()
        );

        $this->expectExceptionMessage('Token is expired.');
        $token->validate($value, $expires->modify('+1 secs'));
    }
}
