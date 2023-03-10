<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Entity\User;

use Webmozart\Assert\Assert;

final class Email
{
    private readonly string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        Assert::email($value);
        $this->value = mb_strtolower($value);
    }

    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
