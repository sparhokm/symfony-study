<?php

namespace App\Http\Application\Action\Auth\Login;

use App\Http\Application\Exception\LoginException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class LoginAction extends AbstractController
{
    public function __construct(private readonly AuthenticationUtils $authenticationUtils)
    {
    }

    public function request(): Response
    {
        $lastError = $this->authenticationUtils->getLastAuthenticationError();
        if (!$lastError) {
            return $this->json([]);
        }

        throw new LoginException($lastError->getMessage());
    }
}
