<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Service\Tolenizer;

final class TokenizerFactory
{
    public static function create(string $interval): Tokenizer
    {
        return new Tokenizer(new \DateInterval($interval));
    }
}
