<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Entity\User;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final class Network
{
    #[ORM\Column(type: Types::STRING, length: 16)]
    private readonly string $name;

    #[ORM\Column(type: Types::STRING, length: 16)]
    private readonly string $identity;

    public function __construct(string $name, string $identity)
    {
        Assert::notEmpty($name);
        Assert::notEmpty($identity);
        $this->name = mb_strtolower($name);
        $this->identity = mb_strtolower($identity);
    }

    public function isEqualTo(self $network): bool
    {
        if ($this->getName() !== $network->getName()) {
            return false;
        }

        return $this->getIdentity() === $network->getIdentity();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }
}
