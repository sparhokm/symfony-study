<?php

declare(strict_types=1);

namespace App\Auth\Test\Domain\Unit\Entity\User\User;

use App\Auth\Domain\Entity\User\Email;
use App\Auth\Domain\Entity\User\Id;
use App\Auth\Domain\Entity\User\Network;
use App\Auth\Domain\Entity\User\Role;
use App\Auth\Domain\Entity\User\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Auth\Domain\Entity\User\User
 *
 * @internal
 */
final class JoinByNetworkTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = User::signUpByNetwork(
            $id = Id::generate(),
            $date = new DateTimeImmutable(),
            $email = new Email('email@app.test'),
            $network = new Network('vk', '0000001')
        );

        self::assertEquals($id, $user->getId());
        self::assertEquals($date, $user->getDate());
        self::assertEquals($email, $user->getEmail());

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        self::assertEquals(Role::USER, $user->getRole()->getValue());

        self::assertCount(1, $networks = $user->getNetworks());
        self::assertEquals($network, $networks[0] ?? null);
    }
}
