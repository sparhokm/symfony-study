<?php

declare(strict_types=1);

use App\Module\Frontend\Infrastructure\FrontendUrlGenerator;
use App\Module\Frontend\Infrastructure\FrontendUrlTwigExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $di): void {
    $services = $di
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->set(FrontendUrlGenerator::class)->arg(0, '%frontend.host%');
    $services->set(FrontendUrlTwigExtension::class)->tag('twig.extension');
};
