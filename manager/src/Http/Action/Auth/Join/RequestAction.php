<?php

declare(strict_types=1);

namespace App\Http\Action\Auth\Join;

use App\Common\Application\Denormalizer\DenormalizerInterface;
use App\Module\Auth\Application\Command\JoinByEmail\Request\Command;
use App\Module\Auth\Application\Command\JoinByEmail\Request\Handler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RequestAction extends AbstractController
{
    public function __construct(
        private readonly DenormalizerInterface $denormalizer,
        private readonly Handler $handler,
    ) {
    }

    public function request(Request $request): Response
    {
        $command = $this->denormalizer->denormalize($request->toArray(), Command::class);

        $this->handler->handle($command);

        return $this->json([], 201);
    }
}
