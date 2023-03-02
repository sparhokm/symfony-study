<?php

declare(strict_types=1);

namespace App\Http\Application\Action\OAuth\Vk;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class CheckAction extends AbstractController
{
    public function request(): Response
    {
        return $this->json([]);
    }
}
