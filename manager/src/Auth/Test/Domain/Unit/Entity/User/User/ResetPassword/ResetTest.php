<?php

declare(strict_types=1);

namespace App\Auth\Test\Domain\Unit\Entity\User\User\ResetPassword;

use App\Auth\Domain\Entity\User\Token;
use App\Auth\Test\Builder\UserBuilder;
use App\Common\Infrastructure\Uuid;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Auth\Domain\Entity\User\User
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

        $this->expectExceptionMessage('Token is invalid.');
        $user->resetPassword(Uuid::getUuid(), $now, 'hash');
    }

    public function testExpiredToken(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestPasswordReset($token, $now);

        $this->expectExceptionMessage('Token is expired.');
        $user->resetPassword($token->getValue(), $now->modify('+1 day'), 'hash');
    }

    public function testNotRequested(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();

        $this->expectExceptionMessage('Resetting is not requested.');
        $user->resetPassword(Uuid::getUuid(), $now, 'hash');
    }

    private function createToken(DateTimeImmutable $date): Token
    {
        return new Token(
            Uuid::getUuid(),
            $date
        );
    }
}
