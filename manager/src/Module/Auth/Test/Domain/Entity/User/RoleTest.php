<?php

declare(strict_types=1);

namespace App\Module\Auth\Test\Domain\Entity\User;

use App\Module\Auth\Domain\Entity\User\Role;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Module\Auth\Domain\Entity\User\Role
 *
 * @internal
 */
final class RoleTest extends TestCase
{
    public function testSuccess(): void
    {
        $role = new Role($name = Role::ADMIN);

        self::assertEquals($name, $role->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Role('none');
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Role('');
    }

    public function testUserFactory(): void
    {
        $role = Role::user();

        self::assertEquals(Role::USER, $role->getValue());
    }
}
