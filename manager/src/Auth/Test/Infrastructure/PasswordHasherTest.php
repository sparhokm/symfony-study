<?php

namespace App\Auth\Test\Infrastructure;

use App\Auth\Infrastructure\Service\PasswordHasher;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Auth\Infrastructure\Service\PasswordHasher
 *
 * @internal
 */
final class PasswordHasherTest extends KernelTestCase
{
    private ?PasswordHasher $passwordHasher;

    public function testHash(): void
    {
        self::bootKernel();

        $hash = $this->passwordHasher->hash($password = 'new-password');

        self::assertNotEmpty($hash);
        self::assertNotEquals($password, $hash);
    }

    public function testHashEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->passwordHasher->hash('');
    }

    public function testValidate(): void
    {
        $hash = $this->passwordHasher->hash($password = 'new-password');

        self::assertTrue($this->passwordHasher->validate($password, $hash));
        self::assertFalse($this->passwordHasher->validate('wrong-password', $hash));
    }

    protected function setUp(): void
    {
        self::bootKernel();

        $this->passwordHasher = static::getContainer()->get(PasswordHasher::class);
    }

    protected function tearDown(): void
    {
        $this->passwordHasher = null;
    }
}
