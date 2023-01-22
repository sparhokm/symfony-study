<?php

declare(strict_types=1);

namespace App\Http;

use App\Http\Action\ErrorController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;

return static function (ContainerConfigurator $di, FrameworkConfig $framework): void {
    $di->import('./config/*.yml');
    $di->import("./config/{{$di->env()}}/*.yml");

    $di
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->load(__NAMESPACE__ . '\\Action\\', 'Action')
    ;

    $framework->errorController(ErrorController::class . '::show');
};
