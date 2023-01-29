<?php

declare(strict_types=1);

namespace App\Frontend;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $di): void {
    $di->import('./config/parameters.yml');
    $di->import("./config/{{$di->env()}}/parameters.yml");

    $di
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->load(__NAMESPACE__ . '\\', '.')
        ->exclude('./{Domain,Test,Application,di.php}')
    ;

    $di->import('./config/*.php');
    $di->import("./config/{{$di->env()}}/*.php");
};
