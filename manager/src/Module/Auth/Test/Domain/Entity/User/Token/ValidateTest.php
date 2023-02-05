<?php

declare(strict_types=1);

namespace App\Module\Auth\Test\Domain\Entity\User\Token;

use App\Common\Infrastructure\Uuid;
use App\Module\Auth\Domain\Entity\User\Token;
use App\Module\Auth\Domain\Exception\Token\Expired;
use App\Module\Auth\Domain\Exception\Token\Invalid;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Module\Auth\Domain\Entity\User\Token::validate
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
            $value = Uuid::getUuid7(),
            $expires = new DateTimeImmutable()
        );

        $token->validate($value, $expires->modify('-1 secs'));
    }

    public function testWrong(): void
    {
        $token = new Token(
            Uuid::getUuid7(),
            $expires = new DateTimeImmutable()
        );

        $this->expectException(Invalid::class);
        $token->validate(Uuid::getUuid7(), $expires->modify('-1 secs'));
    }

    public function testExpired(): void
    {
        $token = new Token(
            $value = Uuid::getUuid7(),
            $expires = new DateTimeImmutable()
        );

        $this->expectException(Expired::class);
        $token->validate($value, $expires->modify('+1 secs'));
    }
}
