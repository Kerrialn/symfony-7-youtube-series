<?php

namespace App\Service\EmailService;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

final class EmailService
{
    private const APPLICATION_EMAIL_ADDRESS = 'something@example.com';


    public function __construct(
        private readonly MailerInterface $mailer
    )
    {
    }

    public function welcomeEmail(string $emailAddress, array $context = []): TemplatedEmail
    {

        $email = new TemplatedEmail();
        $email->from(self::APPLICATION_EMAIL_ADDRESS);
        $email->to($emailAddress);
        $email->htmlTemplate('email/welcome.html.twig');
        $email->context($context);

        $this->send($email);

        return $email;
    }


    /**
     * @throws TransportExceptionInterface
     */
    private function send(TemplatedEmail $email): void
    {
        try {
            $this->mailer->send($email);
        } catch (
        TransportExceptionInterface $exception
        ) {
            throw new $exception;
        }
    }

}