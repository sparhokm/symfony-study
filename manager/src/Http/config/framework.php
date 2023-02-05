<?php

declare(strict_types=1);

use App\Http\Action\ErrorController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;

return static function (ContainerConfigurator $di, FrameworkConfig $framework): void {
    $services = $di->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->set(ErrorController::class)
        ->args(['%http.error.show_detail%'])
    ;

    $framework->errorController(ErrorController::class . '::show');
};
