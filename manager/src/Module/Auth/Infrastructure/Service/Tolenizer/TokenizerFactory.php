<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Service\Tolenizer;

use DateInterval;

final class TokenizerFactory
{
    public static function create(string $interval): Tokenizer
    {
        return new Tokenizer(new DateInterval($interval));
    }
}
