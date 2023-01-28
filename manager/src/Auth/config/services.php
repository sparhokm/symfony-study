<?php

declare(strict_types=1);

use App\Auth\Infrastructure\Service\Tolenizer\Tokenizer;
use App\Auth\Infrastructure\Service\Tolenizer\TokenizerFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $di): void {
    $services = $di->services();

    $services->set(Tokenizer::class)
        ->factory([TokenizerFactory::class, 'create'])
        ->arg(0, '%auth.token.ttl%')
    ;
};
