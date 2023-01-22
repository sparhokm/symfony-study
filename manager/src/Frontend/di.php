<?php

declare(strict_types=1);

namespace App\Frontend;

use App\Frontend\Infrastructure\FrontendUrlGenerator;
use App\Frontend\Infrastructure\FrontendUrlTwigExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $di): void {
    $di->import('./config/*.yml');
    $di->import("./config/{{$di->env()}}/*.yml");

    $services = $di
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->load(__NAMESPACE__.'\\', '.')
        ->exclude('./{Domain,Test,Application,di.php}')
    ;

    $services->set(FrontendUrlGenerator::class)->arg('$frontendHost', '%frontend.host%');
    $services->set(FrontendUrlTwigExtension::class)->tag('twig.extension');
};
