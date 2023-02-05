<?php

namespace App\Common\Application\Denormalizer;

interface DenormalizerInterface
{
    /**
     * @template T
     * @param class-string<T> $type
     * @return T
     * @throws DenormalizerException
     */
    public function denormalize(array $data, string $type): object;
}
