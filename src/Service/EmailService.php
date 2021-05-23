<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class EmailService
{
    public function __construct(private MailerInterface $mailer)
    {}
    
    /**
     * @param  User $user
     * @return void
     */
    public function register(User $user): void
    {
        $email = (new TemplatedEmail())
            ->from($user->getEmail())
            ->to(new Address('geoffroy.colpart81@gmail.com'))
            ->subject('Inscription')
            ->htmlTemplate('front/email/inscription.html.twig')
            ->context([
                'username'  => $user->getUsername()
            ])
        ;

        $this->mailer->send($email);
    }
}