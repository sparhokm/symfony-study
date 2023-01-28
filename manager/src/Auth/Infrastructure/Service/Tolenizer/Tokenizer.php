<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Service\Tolenizer;

use App\Auth\Domain\Entity\User\Token;
use App\Common\Infrastructure\Uuid;
use DateInterval;
use DateTimeImmutable;

final class Tokenizer
{
    public function __construct(private readonly DateInterval $interval)
    {
    }

    public function generate(DateTimeImmutable $date): Token
    {
        return new Token(
            Uuid::getUuid7(),
            $date->add($this->interval)
        );
    }
}
