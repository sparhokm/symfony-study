<?php

declare(strict_types=1);

namespace App\Auth;

use App\Data\Doctrine\FixDefaultSchemaSubscriber;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $di): void {
    $services = $di
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->set(FixDefaultSchemaSubscriber::class)
        ->tag('doctrine.event_subscriber')
    ;
};
