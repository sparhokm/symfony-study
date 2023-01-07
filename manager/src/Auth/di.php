<?php

declare(strict_types=1);

namespace App\Auth;

use App\Auth\Infrastructure\Doctrine\Type\EmailType;
use App\Auth\Infrastructure\Doctrine\Type\IdType;
use App\Auth\Infrastructure\Doctrine\Type\RoleType;
use App\Auth\Infrastructure\Doctrine\Type\StatusType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\DoctrineConfig;

return static function (ContainerConfigurator $di, DoctrineConfig $doctrine): void {
    $di
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->load(__NAMESPACE__.'\\', '.')
        ->exclude('./{Domain,di.php}')
    ;

    $emDefault = $doctrine->orm()->entityManager('default');
    $emDefault->autoMapping(true);
    $emDefault->mapping(__NAMESPACE__)
        ->dir( __DIR__.'/Domain/Entity')
        ->isBundle(false)
        ->prefix(__NAMESPACE__.'\Domain\Entity')
        ->alias(basename(__DIR__))
    ;

    $doctrine->dbal()->type(EmailType::NAME, EmailType::class);
    $doctrine->dbal()->type(IdType::NAME, IdType::class);
    $doctrine->dbal()->type(RoleType::NAME, RoleType::class);
    $doctrine->dbal()->type(StatusType::NAME, StatusType::class);
};
