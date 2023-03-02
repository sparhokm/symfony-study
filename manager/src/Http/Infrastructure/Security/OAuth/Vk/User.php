<?php

declare(strict_types=1);

namespace App\Http\Infrastructure\Security\OAuth\Vk;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * @psalm-type data=array{user_id:int, email? : ?string}
 */
final class User implements ResourceOwnerInterface
{
    /**
     * @param data $data
     */
    public function __construct(private readonly array $data)
    {
    }

    /**
     * @return data
     */
    public function toArray(): array
    {
        return $this->data;
    }

    public function getId(): string
    {
        return (string)$this->data['user_id'];
    }

    public function getEmail(): ?string
    {
        return $this->data['email'] ?? null;
    }
}
