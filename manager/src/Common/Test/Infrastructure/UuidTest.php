<?php

declare(strict_types=1);

namespace App\Common\Test\Infrastructure;

use App\Common\Infrastructure\Uuid;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Common\Infrastructure\Uuid
 *
 * @internal
 */
final class UuidTest extends TestCase
{
    public function testSuccess(): void
    {
        $uuid = Uuid::getUuid7();
        self::assertMatchesRegularExpression('/^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-7[0-9A-Fa-f]{3}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$/', $uuid, 'not uuid v7');
    }
}
