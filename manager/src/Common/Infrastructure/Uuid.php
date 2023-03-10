<?php

declare(strict_types=1);

namespace App\Common\Infrastructure;

final class Uuid
{
    public static function getUuid7(): string
    {
        return \Symfony\Component\Uid\Uuid::v7()->toRfc4122();
    }
}
