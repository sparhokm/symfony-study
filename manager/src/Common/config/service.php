<?php

declare(strict_types=1);

use App\Common\Application\FlusherInterface;
use App\Common\Infrastructure\Flusher;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $di): void {
    $services = $di->services();

    $services->alias(FlusherInterface::class, Flusher::class);
};
