<?php

declare(strict_types=1);

namespace App\Http\Action\Auth\Join;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Auth\Application\Command\JoinByEmail\Request\Command;
use App\Auth\Application\Command\JoinByEmail\Request\Handler;
use Symfony\Component\Serializer\SerializerInterface;

final class RequestAction extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly Handler $handler
    ) {
    }

    public function request(Request $request): Response
    {
        $command = $this->serializer->deserialize($request->getContent(), Command::class, 'json');

        $this->handler->handle($command);

        return $this->json([], 201);
    }
}
