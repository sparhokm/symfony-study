<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Entity\User;

use Webmozart\Assert\Assert;

final class Role
{
    public const USER = 'ROLE_USER';
    public const ADMIN = 'ROLE_ADMIN';

    private readonly string $value;

    public function __construct(string $value)
    {
        Assert::oneOf($value, [
            self::USER,
            self::ADMIN,
        ]);

        $this->value = $value;
    }

    public static function user(): self
    {
        return new self(self::USER);
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
