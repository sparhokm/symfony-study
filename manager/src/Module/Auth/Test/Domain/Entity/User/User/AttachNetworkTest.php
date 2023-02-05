<?php

declare(strict_types=1);

namespace App\Module\Auth\Test\Domain\Entity\User\User;

use App\Module\Auth\Domain\Entity\User\Network;
use App\Module\Auth\Domain\Exception\User\NetworkAlreadyAttached;
use App\Module\Auth\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Module\Auth\Domain\Entity\User\User
 *
 * @internal
 */
final class AttachNetworkTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build()
        ;

        $network = new Network('vk', '0000001');
        $user->attachNetwork($network);

        self::assertCount(1, $networks = $user->getNetworks());
        self::assertEquals($network, $networks[0] ?? null);
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build()
        ;

        $network = new Network('vk', '0000001');

        $user->attachNetwork($network);

        $this->expectException(NetworkAlreadyAttached::class);
        $user->attachNetwork($network);
    }
}
