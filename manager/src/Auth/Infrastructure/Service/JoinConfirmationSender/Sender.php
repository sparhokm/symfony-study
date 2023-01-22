<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Service\JoinConfirmationSender;

use App\Auth\Domain\Entity\User\Email;
use App\Auth\Domain\Entity\User\Token;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

final readonly class Sender
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function send(Email $email, Token $token): void
    {
        $message = (new TemplatedEmail())
            ->subject('Join Confirmation')
            ->to($email->getValue())
            ->htmlTemplate('@auth/confirm.html.twig')
            ->context(['token' => $token]);

        $this->mailer->send($message);
    }
}
