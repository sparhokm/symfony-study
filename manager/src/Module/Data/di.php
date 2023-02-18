<?php

declare(strict_types=1);

namespace App\Module\Data;

use App\Module\Data\Doctrine\FixDefaultSchemaSubscriber;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\DoctrineMigrationsConfig;

return static function (ContainerConfigurator $di, DoctrineMigrationsConfig $doctrineMigrationsConfig): void {
    $services = $di
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->set(FixDefaultSchemaSubscriber::class)
        ->tag('doctrine.event_subscriber')
    ;

    $doctrineMigrationsConfig->migrationsPath(__NAMESPACE__, __DIR__ . '/Migrations');
    $doctrineMigrationsConfig->organizeMigrations('BY_YEAR');
    $doctrineMigrationsConfig->enableProfiler(false);
};
