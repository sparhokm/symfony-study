<?php

declare(strict_types=1);

namespace App\Module\Auth\Test\Domain\Entity\User\User\JoinByEmail;

use App\Common\Infrastructure\Uuid;
use App\Module\Auth\Domain\Entity\User\Token;
use App\Module\Auth\Domain\Exception\Token\Expired;
use App\Module\Auth\Domain\Exception\Token\Invalid;
use App\Module\Auth\Domain\Exception\User\ConfirmationNotRequired;
use App\Module\Auth\Test\Builder\UserBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Module\Auth\Domain\Entity\User\User
 *
 * @internal
 */
final class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = $this->createToken())
            ->build()
        ;

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());

        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->modify('-1 day'),
        );

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        self::assertNull($user->getConfirmToken());
    }

    public function testWrong(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = $this->createToken())
            ->build()
        ;

        $this->expectException(Invalid::class);

        $user->confirmJoin(
            Uuid::getUuid7(),
            $token->getExpires()->modify('-1 day'),
        );
    }

    public function testExpired(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = $this->createToken())
            ->build()
        ;

        $this->expectException(Expired::class);

        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->modify('+1 day'),
        );
    }

    public function testAlready(): void
    {
        $token = $this->createToken();

        $user = (new UserBuilder())
            ->withJoinConfirmToken($token)
            ->active()
            ->build()
        ;

        $this->expectException(ConfirmationNotRequired::class);

        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->modify('-1 day'),
        );
    }

    private function createToken(): Token
    {
        return new Token(
            Uuid::getUuid7(),
            new DateTimeImmutable('+1 day'),
        );
    }
}
