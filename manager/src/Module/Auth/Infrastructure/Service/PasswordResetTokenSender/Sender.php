<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Service\PasswordResetTokenSender;

use App\Module\Auth\Domain\Entity\User\Email;
use App\Module\Auth\Domain\Entity\User\Token;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

final class Sender
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    public function send(Email $email, Token $token): void
    {
        $message = (new TemplatedEmail())
            ->subject('Password Reset')
            ->to($email->getValue())
            ->htmlTemplate('@auth/password/confirm.html.twig')
            ->context(['token' => $token])
        ;

        $this->mailer->send($message);
    }
}
