<?php

declare(strict_types=1);

namespace App\Common\Infrastructure;

use App\Common\Application\FlusherInterface;
use Doctrine\ORM\EntityManagerInterface;

final class Flusher implements FlusherInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
