<?php

declare(strict_types=1);

namespace App\Http\Application\Action\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class HomeAction extends AbstractController
{
    public function request(): Response
    {
        return $this->json('hello world');
    }
}
