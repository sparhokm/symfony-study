<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Entity\User;

use Webmozart\Assert\Assert;

final class Status
{
    private const WAIT = 'wait';
    private const ACTIVE = 'active';
    private const BLOCKED = 'blocked';

    private string $value;

    public function __construct(string $value)
    {
        Assert::oneOf($value, [
            self::WAIT,
            self::ACTIVE,
            self::BLOCKED,
        ]);
        $this->value = $value;
    }

    public static function wait(): self
    {
        return new self(self::WAIT);
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public static function blocked(): self
    {
        return new self(self::BLOCKED);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isEqualTo(self $role): bool
    {
        return $role->getValue() === $this->getValue();
    }
}
