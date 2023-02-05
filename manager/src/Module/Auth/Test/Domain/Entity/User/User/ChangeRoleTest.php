<?php

declare(strict_types=1);

namespace App\Module\Auth\Test\Domain\Entity\User\User;

use App\Module\Auth\Domain\Entity\User\Role;
use App\Module\Auth\Test\Builder\UserBuilder;
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
