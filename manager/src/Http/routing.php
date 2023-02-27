<?php

namespace App\Http;

use App\Http\Application\Action\Auth;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
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

    $routes->add('auth_login', '/auth/login')
        ->methods(['POST'])
        ->controller([Auth\Login\LoginAction::class, 'request'])
    ;
    $routes->add('auth_logout', '/auth/logout')
        ->methods(['GET'])
        ->controller([Auth\Logout\LogoutAction::class, 'request'])
    ;

    $routes->add('home', '/')
        ->methods(['GET'])
        ->controller([Auth\HomeAction::class, 'request'])
    ;

    $routes->add('auth_user', '/auth/user')
        ->methods(['GET'])
        ->controller([Auth\User\UserAction::class, 'request'])
    ;
};
