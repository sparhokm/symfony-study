<?php

declare(strict_types=1);

namespace App\Common\Application;

final class Uuid
{
    static public function getUuid(): string
    {
        return \Symfony\Component\Uid\Uuid::v7()->toRfc4122();
    }
}
