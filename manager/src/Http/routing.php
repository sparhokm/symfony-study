<?php

namespace App\Http;

use App\Http\Action\Auth;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes) {
    $routes->add('auth_join_by_email', '/auth/join')
        ->methods(['POST'])
        ->controller([Auth\Join\RequestAction::class, 'request'])
    ;

    $routes->add('auth_join_by_email_confirm', '/auth/join/confirm')
        ->methods(['POST'])
        ->controller([Auth\Join\ConfirmAction::class, 'request'])
    ;

    $routes->add('auth_password_reset', '/auth/password/reset')
        ->methods(['POST'])
        ->controller([Auth\PasswordReset\RequestAction::class, 'request'])
    ;

    $routes->add('auth_password_reset_confirm', '/auth/password/reset/confirm')
        ->methods(['POST'])
        ->controller([Auth\PasswordReset\ConfirmAction::class, 'request'])
    ;
};
