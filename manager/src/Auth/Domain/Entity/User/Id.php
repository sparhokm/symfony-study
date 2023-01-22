<?php

declare(strict_types=1);

namespace App\Auth\Domain\Entity\User;

use App\Common\Infrastructure\Uuid;
use Stringable;
use Webmozart\Assert\Assert;

final class Id implements Stringable
{
    private string $value;

    public function __construct(string $value)
    {
        Assert::uuid($value);
        $this->value = mb_strtolower($value);
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public static function generate(): self
    {
        return new self(Uuid::getUuid7());
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
