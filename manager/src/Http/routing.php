<?php

namespace App\Http;

use App\Http\Action\HomeController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->add('home', '/')->controller([HomeController::class, 'index']);
};
