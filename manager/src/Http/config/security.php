<?php

use App\Http\Infrastructure\Security\LoginFormAuthenticator;
use App\Http\Infrastructure\Security\UserChecker;
use App\Http\Infrastructure\Security\UserProvider;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    $security->provider('user_provider')->id(UserProvider::class);

    $devFirewall = $security->firewall('dev');
    $devFirewall->pattern('^/(_(profiler|wdt)|css|images|js)/');
    $devFirewall->security(false);

    $mainFirewall = $security->firewall('main');
    $mainFirewall->lazy(true);
    $mainFirewall->provider('user_provider');
    $mainFirewall->customAuthenticators([LoginFormAuthenticator::class]);
    $mainFirewall->userChecker(UserChecker::class);
    $mainFirewall->rememberMe()
        ->secret('%kernel.secret%')
        ->lifetime(604800)
        ->alwaysRememberMe(true)
        ->tokenProvider([
            'doctrine' => true,
        ])
    ;
    $mainFirewall->logout()->path('auth_logout');

    $security->accessControl()->path('^/auth/login')->roles(AuthenticatedVoter::PUBLIC_ACCESS);
    $security->accessControl()->path('^/auth/logout')->roles(AuthenticatedVoter::PUBLIC_ACCESS);
    $security->accessControl()->path('^/auth/join')->roles(AuthenticatedVoter::PUBLIC_ACCESS);
    $security->accessControl()->path('^/auth/password/reset')->roles(AuthenticatedVoter::PUBLIC_ACCESS);
    $security->accessControl()->path('^/')->roles(AuthenticatedVoter::PUBLIC_ACCESS);

    $security->accessControl()->path('^/auth/user')->roles('ROLE_USER');
};
