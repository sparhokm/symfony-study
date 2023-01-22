<?php

namespace App\Http;

use App\Http\Action\Auth;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->add('join_by_email', '/auth/join')
        ->methods(['POST'])
        ->controller([Auth\Join\RequestAction::class, 'request']);
};
