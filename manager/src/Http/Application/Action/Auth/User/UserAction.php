<?php

declare(strict_types=1);

namespace App\Http\Application\Action\Auth\User;

use App\Http\Domain\Entity\UserIdentity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class UserAction extends AbstractController
{
    public function request(#[CurrentUser] UserIdentity $userIdentity): Response
    {
        return $this->json(['id' => $userIdentity->getUserIdentifier()]);
    }
}
