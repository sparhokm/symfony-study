<?php

namespace App\Http\Application\Action\Auth\Logout;

use App\Http\Domain\Entity\UserIdentity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class LogoutAction extends AbstractController
{
    public function __construct(private readonly Security $security)
    {
    }

    public function request(#[CurrentUser] ?UserIdentity $userIdentity): Response
    {
        if ($userIdentity) {
            $this->security->logout(false);
        }

        return new JsonResponse([]);
    }
}
