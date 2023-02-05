<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Denormalizer;

use App\Common\Application\Denormalizer\DenormalizerException;
use App\Common\Application\Denormalizer\DenormalizerInterface;
use Throwable;

final class Denormalizer implements DenormalizerInterface
{
    /**
     * @template T
     * @param class-string<T> $type
     * @return T
     * @throws DenormalizerException
     */
    public function denormalize(array $data, string $type): object
    {
        try {
            /** @psalm-suppress MixedMethodCall */
            return new $type(...$data);
        } catch (Throwable $throwable) {
            throw new DenormalizerException(message: $throwable->getMessage(), previous: $throwable);
        }
    }
}
