<?php

declare(strict_types=1);

use Symfony\Config\FrameworkConfig;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (FrameworkConfig $frameworkConfig): void {
    $mailerConfig = $frameworkConfig->mailer();
    $mailerConfig->envelope()->sender(env('MAILER_FROM_EMAIL'));
    $mailerConfig->header('From')->value(
        sprintf('"%s" <%s>', (string)env('MAILER_FROM_NAME'), (string)env('MAILER_FROM_EMAIL')),
    );
};
