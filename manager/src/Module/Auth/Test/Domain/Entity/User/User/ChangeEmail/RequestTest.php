<?php

declare(strict_types=1);

namespace App\Module\Auth\Test\Domain\Entity\User\User\ChangeEmail;

use App\Common\Infrastructure\Uuid;
use App\Module\Auth\Domain\Entity\User\Email;
use App\Module\Auth\Domain\Entity\User\Token;
use App\Module\Auth\Domain\Exception\User\EmailAlreadySame;
use App\Module\Auth\Domain\Exception\User\EmailChangeAlreadyRequested;
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
        $user = (new UserBuilder())
            ->withEmail($old = new Email('old-email@app.test'))
            ->active()
            ->build()
        ;

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $user->requestEmailChanging($token, $now, $new = new Email('new-email@app.test'));

        self::assertEquals($token, $user->getNewEmailToken());
        self::assertEquals($old, $user->getEmail());
        self::assertEquals($new, $user->getNewEmail());
    }

    public function testSame(): void
    {
        $user = (new UserBuilder())
            ->withEmail($old = new Email('old-email@app.test'))
            ->active()
            ->build()
        ;

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $this->expectException(EmailAlreadySame::class);
        $user->requestEmailChanging($token, $now, $old);
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $user->requestEmailChanging($token, $now, $email = new Email('new-email@app.test'));

        $this->expectException(EmailChangeAlreadyRequested::class);
        $user->requestEmailChanging($token, $now, $email);
    }

    public function testExpired(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));
        $user->requestEmailChanging($token, $now, new Email('temp-email@app.test'));

        $newDate = $now->modify('+2 hours');
        $newToken = $this->createToken($newDate->modify('+1 hour'));
        $user->requestEmailChanging($newToken, $newDate, $newEmail = new Email('new-email@app.test'));

        self::assertEquals($newToken, $user->getNewEmailToken());
        self::assertEquals($newEmail, $user->getNewEmail());
    }

    public function testNotActive(): void
    {
        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $user = (new UserBuilder())->build();

        $this->expectException(UserNotActive::class);
        $user->requestEmailChanging($token, $now, new Email('temp-email@app.test'));
    }

    private function createToken(DateTimeImmutable $date): Token
    {
        return new Token(
            Uuid::getUuid7(),
            $date,
        );
    }
}
