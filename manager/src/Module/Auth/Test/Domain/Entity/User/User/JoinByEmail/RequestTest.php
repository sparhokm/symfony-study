<?php

declare(strict_types=1);

namespace App\Module\Auth\Test\Domain\Entity\User\User\JoinByEmail;

use App\Common\Infrastructure\Uuid;
use App\Module\Auth\Domain\Entity\User\Email;
use App\Module\Auth\Domain\Entity\User\Id;
use App\Module\Auth\Domain\Entity\User\Role;
use App\Module\Auth\Domain\Entity\User\Token;
use App\Module\Auth\Domain\Entity\User\User;
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
        $user = User::joinUpByEmail(
            $id = Id::generate(),
            $date = new DateTimeImmutable(),
            $email = new Email('mail@example.com'),
            $hash = 'hash',
            $token = new Token(Uuid::getUuid7(), new DateTimeImmutable())
        );

        self::assertEquals($id, $user->getId());
        self::assertEquals($date, $user->getDate());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($hash, $user->getPasswordHash());
        self::assertEquals($token, $user->getConfirmToken());

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());

        self::assertTrue(Role::user()->isEqualTo($user->getRole()));
    }
}
