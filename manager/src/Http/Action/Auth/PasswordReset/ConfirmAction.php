<?php

declare(strict_types=1);

namespace App\Http\Action\Auth\PasswordReset;

use App\Common\Infrastructure\Denormalizer\Denormalizer;
use App\Module\Auth\Application\Command\PasswordReset\Confirm\Command;
use App\Module\Auth\Application\Command\PasswordReset\Confirm\Handler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ConfirmAction extends AbstractController
{
    public function __construct(
        private readonly Denormalizer $denormalizer,
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
