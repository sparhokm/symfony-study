<?php

declare(strict_types=1);

namespace App\Module\Auth\Test\Domain\Entity\User\Token;

use App\Common\Infrastructure\Uuid;
use App\Module\Auth\Domain\Entity\User\Token;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Module\Auth\Domain\Entity\User\Token::isExpiredTo
 *
 * @internal
 */
final class ExpiresTest extends TestCase
{
    public function testNot(): void
    {
        $token = new Token(
            Uuid::getUuid7(),
            $expires = new DateTimeImmutable()
        );

        self::assertFalse($token->isExpiredTo($expires->modify('-1 secs')));
        self::assertTrue($token->isExpiredTo($expires));
        self::assertTrue($token->isExpiredTo($expires->modify('+1 secs')));
    }
}
