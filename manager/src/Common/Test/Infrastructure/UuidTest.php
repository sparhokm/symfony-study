<?php

declare(strict_types=1);

namespace App\Common\Infrastructure;

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
        $uuid = Uuid::getUuid();
        self::assertMatchesRegularExpression('/^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-7[0-9A-Fa-f]{3}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$/', $uuid, 'not uuid v7');
    }
}
