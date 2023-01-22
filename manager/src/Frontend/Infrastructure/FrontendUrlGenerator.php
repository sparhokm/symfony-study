<?php

declare(strict_types=1);

namespace App\Frontend\Infrastructure;

final class FrontendUrlGenerator
{
    public function __construct(private readonly string $frontendHost)
    {
    }

    public function generate(string $uri, array $params = []): string
    {
        return $this->frontendHost
            . ($uri ? '/' . $uri : '')
            . ($params ? '?' . http_build_query($params) : '');
    }
}
