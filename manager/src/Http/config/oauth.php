<?php

declare(strict_types=1);

use App\Http\Infrastructure\Security\OAuth\Vk\Provider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (ContainerConfigurator $di): void {
    $di->extension('knpu_oauth2_client', [
        'clients' => [
            'vk' => [
                'type' => 'generic',
                'provider_class' => Provider::class,
                'client_id' => env('OAUTH_VKONTAKTE_CLIENT_ID'),
                'client_secret' => env('OAUTH_VKONTAKTE_CLIENT_SECRET'),
                'redirect_route' => 'oauth_vk_check',
                'redirect_params' => [],
            ],
        ],
    ]);
};
