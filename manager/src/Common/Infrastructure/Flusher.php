<?php

declare(strict_types=1);

namespace App\Common\Infrastructure;

use App\Common\Application\FlusherInterface;
use Doctrine\ORM\EntityManagerInterface;

final class Flusher implements FlusherInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
