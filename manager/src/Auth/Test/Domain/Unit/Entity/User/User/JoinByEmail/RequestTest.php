<?php

declare(strict_types=1);

namespace App\Auth\Test\Domain\Unit\Entity\User\User\JoinByEmail;

use App\Auth\Domain\Entity\User\Email;
use App\Auth\Domain\Entity\User\Id;
use App\Auth\Domain\Entity\User\Role;
use App\Auth\Domain\Entity\User\Token;
use App\Auth\Domain\Entity\User\User;
use App\Common\Infrastructure\Uuid;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Auth\Domain\Entity\User\User
 *
 * @internal
 */
final class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = User::signUpByEmail(
            $id = Id::generate(),
            $date = new DateTimeImmutable(),
            $email = new Email('mail@example.com'),
            $hash = 'hash',
            $token = new Token(Uuid::getUuid(), new DateTimeImmutable())
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
