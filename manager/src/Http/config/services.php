<?php

declare(strict_types=1);

use App\Http\Infrastructure\Security\LoginFormAuthenticator;
use App\Http\Infrastructure\Security\UserProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $di): void {
    $services = $di->services()->defaults()->autowire()->autoconfigure();

    $services->set(LoginFormAuthenticator::class)
        ->arg('$userProvider', service(UserProvider::class))
    ;
};
