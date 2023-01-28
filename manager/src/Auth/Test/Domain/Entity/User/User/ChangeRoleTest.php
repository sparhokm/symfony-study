<?php

declare(strict_types=1);

namespace App\Auth\Test\Domain\Entity\User\User;

use App\Auth\Domain\Entity\User\Role;
use App\Auth\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class ChangeRoleTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->build()
        ;

        $user->changeRole($role = new Role(Role::ADMIN));

        self::assertEquals($role, $user->getRole());
    }
}
