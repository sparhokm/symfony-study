<?php

declare(strict_types=1);

namespace App\Module\Auth\Test\Domain\Entity\User\User\ResetPassword;

use App\Common\Infrastructure\Uuid;
use App\Module\Auth\Domain\Entity\User\Token;
use App\Module\Auth\Domain\Exception\User\PasswordResetAlreadyRequested;
use App\Module\Auth\Domain\Exception\User\UserNotActive;
use App\Module\Auth\Test\Builder\UserBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Module\Auth\Domain\Entity\User\User
 *
 * @internal
 */
final class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getPasswordResetToken());
        self::assertEquals($token, $user->getPasswordResetToken());
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestPasswordReset($token, $now);

        $this->expectException(PasswordResetAlreadyRequested::class);
        $user->requestPasswordReset($token, $now);
    }

    public function testExpired(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));
        $user->requestPasswordReset($token, $now);

        $newDate = $now->modify('+2 hours');
        $newToken = $this->createToken($newDate->modify('+1 hour'));
        $user->requestPasswordReset($newToken, $newDate);

        self::assertEquals($newToken, $user->getPasswordResetToken());
    }

    public function testNotActive(): void
    {
        $user = (new UserBuilder())->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $this->expectException(UserNotActive::class);
        $user->requestPasswordReset($token, $now);
    }

    private function createToken(DateTimeImmutable $date): Token
    {
        return new Token(
            Uuid::getUuid7(),
            $date,
        );
    }
}
