<?php

declare(strict_types=1);

namespace App\Auth\Test\Domain\Unit\Entity\User;

use App\Auth\Domain\Entity\User\Status;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Auth\Domain\Entity\User\Status
 *
 * @internal
 */
final class StatusTest extends TestCase
{
    public function testSuccess(): void
    {
        $status = new Status($name = 'wait');

        self::assertEquals($name, $status->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Status('none');
    }

    public function testWait(): void
    {
        $status = new Status('wait');

        self::assertTrue($status->isEqualTo(Status::wait()));
        self::assertFalse($status->isEqualTo(Status::active()));
    }
}
