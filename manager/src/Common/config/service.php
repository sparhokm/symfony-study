<?php

declare(strict_types=1);

use App\Common\Application\Denormalizer\DenormalizerInterface;
use App\Common\Application\FlusherInterface;
use App\Common\Application\Validator\ValidatorInterface;
use App\Common\Infrastructure\Denormalizer\Denormalizer;
use App\Common\Infrastructure\Flusher;
use App\Common\Infrastructure\Validator\Validator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $di): void {
    $services = $di->services();

    $services->alias(FlusherInterface::class, Flusher::class);
    $services->alias(ValidatorInterface::class, Validator::class);
    $services->alias(DenormalizerInterface::class, Denormalizer::class);
};
