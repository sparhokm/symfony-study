<?php

declare(strict_types=1);

namespace App\Auth;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\DoctrineConfig;

return static function (ContainerConfigurator $di, DoctrineConfig $doctrine): void {
    $di->import('./config/parameters.yml');
    $di->import("./config/{{$di->env()}}/parameters.yml");

    $di->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->load(__NAMESPACE__ . '\\', '.')
        ->exclude('./{Domain,Test,config,di.php}')
    ;

    $emDefault = $doctrine->orm()->entityManager('default');
    $emDefault->autoMapping(true);
    $emDefault->mapping(__NAMESPACE__)
        ->dir(__DIR__ . '/Domain/Entity')
        ->isBundle(false)
        ->prefix(__NAMESPACE__ . '\Domain\Entity')
        ->alias(basename(__DIR__))
    ;

    $di->import('./config/*.php');
    $di->import("./config/{{$di->env()}}/*.php");
};
