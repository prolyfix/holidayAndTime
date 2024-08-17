<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendRegistrationEmail(string $userEmail): void
    {
        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($userEmail)
            ->subject('Welcome to Our Service')
            ->text('Thank you for registering!');

        $this->mailer->send($email);
    }
}