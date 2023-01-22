<?php

declare(strict_types=1);

namespace App\Common;

use App\Common\Application\FlusherInterface;
use App\Common\Infrastructure\Flusher;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;
use Symfony\Config\TwigConfig;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (ContainerConfigurator $di, TwigConfig $twigConfig, FrameworkConfig $frameworkConfig): void {
    $services = $di
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->load(__NAMESPACE__ . '\\', '.')
        ->exclude('./{Domain,Test,Application,di.php}');

    $services->alias(FlusherInterface::class, Flusher::class);

    $twigConfig->path(__DIR__ . '/Infrastructure/templates', null);
    $mailerConfig = $frameworkConfig->mailer();
    $mailerConfig->envelope()->sender(env('MAILER_FROM_EMAIL'));
    $mailerConfig->header('From')->value(sprintf('"%s" <%s>', env('MAILER_FROM_NAME'), env('MAILER_FROM_EMAIL')));
};
