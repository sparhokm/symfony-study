<?php

declare(strict_types=1);

namespace App\Auth\Test\Domain\Entity\User\User\ChangeEmail;

use App\Auth\Domain\Entity\User\Email;
use App\Auth\Domain\Entity\User\Token;
use App\Auth\Domain\Exception\Token\Expired;
use App\Auth\Domain\Exception\Token\Invalid;
use App\Auth\Domain\Exception\User\EmailChangeNotRequested;
use App\Auth\Test\Builder\UserBuilder;
use App\Common\Infrastructure\Uuid;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Auth\Domain\Entity\User\User
 *
 * @internal
 */
final class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = new Token(Uuid::getUuid7(), $now->modify('+1 day'));

        $user->requestEmailChanging($token, $now, $new = new Email('new-email@app.test'));

        $user->confirmEmailChanging($token->getValue(), $now);

        self::assertNull($user->getNewEmail());
        self::assertNull($user->getNewEmailToken());
        self::assertEquals($new, $user->getEmail());
    }

    public function testInvalidToken(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = new Token(Uuid::getUuid7(), $now->modify('+1 day'));

        $user->requestEmailChanging($token, $now, new Email('new-email@app.test'));

        $this->expectException(Invalid::class);
        $user->confirmEmailChanging('invalid', $now);
    }

    public function testExpiredToken(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = new Token(Uuid::getUuid7(), $now);

        $user->requestEmailChanging($token, $now, new Email('new-email@app.test'));

        $this->expectException(Expired::class);
        $user->confirmEmailChanging($token->getValue(), $now->modify('+1 day'));
    }

    public function testNotRequested(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = new Token(Uuid::getUuid7(), $now->modify('+1 day'));

        $this->expectException(EmailChangeNotRequested::class);
        $user->confirmEmailChanging($token->getValue(), $now);
    }
}
