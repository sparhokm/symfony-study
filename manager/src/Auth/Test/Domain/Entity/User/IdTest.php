<?php

declare(strict_types=1);

namespace App\Auth\Test\Domain\Entity\User;

use App\Auth\Domain\Entity\User\Id;
use App\Common\Infrastructure\Uuid;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Auth\Domain\Entity\User\Id
 *
 * @internal
 */
final class IdTest extends TestCase
{
    public function testSuccess(): void
    {
        $id = new Id($value = Uuid::getUuid7());

        self::assertEquals($value, $id->getValue());
    }

    public function testCase(): void
    {
        $value = Uuid::getUuid7();

        $id = new Id(mb_strtoupper($value));

        self::assertEquals($value, $id->getValue());
    }

    public function testGenerate(): void
    {
        $id = Id::generate();

        self::assertNotEmpty($id->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Id('12345');
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Id('');
    }
}
