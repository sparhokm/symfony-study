<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Entity\User;

use App\Module\Auth\Domain\Exception\Token\Expired;
use App\Module\Auth\Domain\Exception\Token\Invalid;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final class Token
{
    #[ORM\Column(type: 'string', nullable: true)]
    private string $value;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private DateTimeImmutable $expires;

    public function __construct(string $value, DateTimeImmutable $expires)
    {
        Assert::uuid($value);
        $this->value = mb_strtolower($value);
        $this->expires = $expires;
    }

    public function validate(string $value, DateTimeImmutable $date): void
    {
        if (!$this->isEqualTo($value)) {
            throw new Invalid();
        }
        if ($this->isExpiredTo($date)) {
            throw new Expired();
        }
    }

    public function isExpiredTo(DateTimeImmutable $date): bool
    {
        return $this->expires <= $date;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getExpires(): DateTimeImmutable
    {
        return $this->expires;
    }

    public function isEmpty(): bool
    {
        return empty($this->value);
    }

    private function isEqualTo(string $value): bool
    {
        return $this->value === $value;
    }
}
