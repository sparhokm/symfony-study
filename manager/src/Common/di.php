<?php

declare(strict_types=1);

namespace App\Common;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $di): void {
    $di
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->load(__NAMESPACE__ . '\\', '.')
        ->exclude('./{Domain,Test,Application,di.php,config}')
    ;

    $di->import('./config/*.php');
    $di->import("./config/{{$di->env()}}/*.php");
};
