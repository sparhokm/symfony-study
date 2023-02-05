<?php

declare(strict_types=1);

namespace App\Module\Auth\Test\Domain\Entity\User\User\ResetPassword;

use App\Common\Infrastructure\Uuid;
use App\Module\Auth\Domain\Entity\User\Token;
use App\Module\Auth\Domain\Exception\Token\Expired;
use App\Module\Auth\Domain\Exception\Token\Invalid;
use App\Module\Auth\Domain\Exception\User\PasswordResetNotRequested;
use App\Module\Auth\Test\Builder\UserBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Module\Auth\Domain\Entity\User\User
 *
 * @internal
 */
final class ResetTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestPasswordReset($token, $now);

        $user->resetPassword($token->getValue(), $now, $hash = 'hash');

        self::assertNull($user->getPasswordResetToken());
        self::assertEquals($hash, $user->getPasswordHash());
    }

    public function testInvalidToken(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestPasswordReset($token, $now);

        $this->expectException(Invalid::class);
        $user->resetPassword(Uuid::getUuid7(), $now, 'hash');
    }

    public function testExpiredToken(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestPasswordReset($token, $now);

        $this->expectException(Expired::class);
        $user->resetPassword($token->getValue(), $now->modify('+1 day'), 'hash');
    }

    public function testNotRequested(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();

        $this->expectException(PasswordResetNotRequested::class);
        $user->resetPassword(Uuid::getUuid7(), $now, 'hash');
    }

    private function createToken(DateTimeImmutable $date): Token
    {
        return new Token(
            Uuid::getUuid7(),
            $date
        );
    }
}
