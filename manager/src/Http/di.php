<?php

declare(strict_types=1);

namespace App\Http;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $di): void {
    $di->import('./config/parameters.yml');
    $di->import("./config/{{$di->env()}}/parameters.yml");

    $di->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->load(__NAMESPACE__ . '\\', '.')
        ->exclude('./{Domain,Test,config,di.php}')
    ;

    $di->import('./config/*.php');
    $di->import("./config/{{$di->env()}}/*.php");
};
