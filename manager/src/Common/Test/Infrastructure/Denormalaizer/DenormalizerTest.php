<?php

declare(strict_types=1);

namespace App\Common\Test\Infrastructure\Denormalaizer;

use App\Common\Application\Denormalizer\DenormalizerException;
use App\Common\Infrastructure\Denormalizer\Denormalizer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Common\Infrastructure\Denormalizer\Denormalizer
 *
 * @internal
 */
final class DenormalizerTest extends TestCase
{
    public function testValid(): void
    {
        $class = new class($name = 'John') {
            public function __construct(
                public string $name,
            ) {
            }
        };

        $denormalizer = new Denormalizer();

        $result = $denormalizer->denormalize(['name' => $name], $class::class);

        self::assertEquals($name, $result->name);
    }

    public function testNotValidType(): void
    {
        $denormalizer = new Denormalizer();

        $class = new class($name = 'John') {
            public function __construct(
                public string $name,
            ) {
            }
        };

        $this->expectException(DenormalizerException::class);

        $denormalizer->denormalize(['name' => 42], $class::class);
    }

    public function testExtraType(): void
    {
        $class = new class($name = 'John') {
            public function __construct(
                public string $name,
            ) {
            }
        };

        $denormalizer = new Denormalizer();

        $this->expectException(DenormalizerException::class);

        $denormalizer->denormalize(['name' => $name, 'age' => 32], $class::class);
    }
}
