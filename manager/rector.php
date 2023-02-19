<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\Array_\FirstClassCallableRector;
use Rector\Php82\Rector\Class_\ReadOnlyClassRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->cacheDirectory(__DIR__ . '/var/cache/rector');

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,
        SetList::EARLY_RETURN,
        // SetList::DEAD_CODE,  //run manual
        SetList::TYPE_DECLARATION,
        DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
        DoctrineSetList::DOCTRINE_DBAL_40,
        DoctrineSetList::DOCTRINE_ORM_213,
        SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
        SymfonySetList::SYMFONY_62,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
    ]);

    $rectorConfig->skip([
        ReadOnlyClassRector::class, // cs-fixer conflict
        FirstClassCallableRector::class => [
            __DIR__ . '/src/Module/Auth/config/services.php',
            __DIR__ . '/src/Http/routing.php',
        ],
        ClassPropertyAssignToConstructorPromotionRector::class => [
            __DIR__ . '/src/Module/Auth/Domain/Entity/*',
        ],

        ReturnNeverTypeRector::class,
    ]);
};
