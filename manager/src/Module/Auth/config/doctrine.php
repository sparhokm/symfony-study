<?php

declare(strict_types=1);

use App\Module\Auth\Infrastructure\Doctrine\Type\EmailType;
use App\Module\Auth\Infrastructure\Doctrine\Type\IdType;
use App\Module\Auth\Infrastructure\Doctrine\Type\RoleType;
use App\Module\Auth\Infrastructure\Doctrine\Type\StatusType;
use Symfony\Config\DoctrineConfig;

return static function (DoctrineConfig $doctrine): void {
    $doctrine->dbal()->type(EmailType::NAME, EmailType::class);
    $doctrine->dbal()->type(IdType::NAME, IdType::class);
    $doctrine->dbal()->type(RoleType::NAME, RoleType::class);
    $doctrine->dbal()->type(StatusType::NAME, StatusType::class);
};
