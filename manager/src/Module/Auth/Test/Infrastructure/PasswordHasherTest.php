<?php

namespace App\Module\Auth\Test\Infrastructure;

use App\Module\Auth\Infrastructure\Service\PasswordHasher;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Module\Auth\Infrastructure\Service\PasswordHasher
 *
 * @internal
 */
final class PasswordHasherTest extends KernelTestCase
{
    public function testHash(): void
    {
        self::bootKernel();
        $passwordHasher = self::getContainer()->get(PasswordHasher::class);

        $hash = $passwordHasher->hash($password = 'new-password');

        self::assertNotEmpty($hash);
        self::assertNotEquals($password, $hash);
    }

    public function testHashEmpty(): void
    {
        self::bootKernel();
        $passwordHasher = self::getContainer()->get(PasswordHasher::class);

        $this->expectException(InvalidArgumentException::class);
        $passwordHasher->hash('');
    }

    public function testValidate(): void
    {
        self::bootKernel();
        $passwordHasher = self::getContainer()->get(PasswordHasher::class);

        $hash = $passwordHasher->hash($password = 'new-password');

        self::assertTrue($passwordHasher->validate($password, $hash));
        self::assertFalse($passwordHasher->validate('wrong-password', $hash));
    }
}
