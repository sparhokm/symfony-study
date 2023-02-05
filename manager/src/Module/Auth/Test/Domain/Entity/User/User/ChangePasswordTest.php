<?php

declare(strict_types=1);

namespace App\Module\Auth\Test\Domain\Entity\User\User;

use App\Module\Auth\Domain\Exception\User\PasswordEqualOldPassword;
use App\Module\Auth\Domain\Exception\User\PasswordIncorrect;
use App\Module\Auth\Infrastructure\Service\PasswordHasher;
use App\Module\Auth\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Module\Auth\Domain\Entity\User\User
 *
 * @internal
 */
final class ChangePasswordTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build()
        ;

        $hasher = $this->createHasher(true, $hash = 'new-hash');

        $user->changePassword(
            'old-password',
            'new-password',
            $hasher
        );

        self::assertEquals($hash, $user->getPasswordHash());
    }

    public function testWrongCurrent(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build()
        ;

        $hasher = $this->createHasher(false, 'new-hash');

        $this->expectException(PasswordIncorrect::class);
        $user->changePassword(
            'wrong-old-password',
            'new-password',
            $hasher
        );
    }

    public function testByNetwork(): void
    {
        $user = (new UserBuilder())
            ->viaNetwork()
            ->build()
        ;

        $hasher = $this->createHasher(false, 'new-hash');

        $this->expectException(PasswordEqualOldPassword::class);
        $user->changePassword(
            'any-old-password',
            'new-password',
            $hasher
        );
    }

    private function createHasher(bool $valid, string $hash): PasswordHasher
    {
        $hasher = $this->createStub(PasswordHasher::class);
        $hasher->method('validate')->willReturn($valid);
        $hasher->method('hash')->willReturn($hash);

        return $hasher;
    }
}
