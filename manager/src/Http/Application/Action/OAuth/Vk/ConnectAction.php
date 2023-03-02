<?php

declare(strict_types=1);

namespace App\Http\Application\Action\OAuth\Vk;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class ConnectAction extends AbstractController
{
    public function request(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('vk')
            ->redirect([], [])
        ;
    }
}
