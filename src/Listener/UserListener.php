<?php

namespace App\Listener;

use App\Service\EmailService;

class UserListener
{
    public function __construct(private EmailService $emailService)
    {}
    
    /**
     * postPersist
     * Envoie d'un email après l'inscription d'un utilisateur
     *
     * @return void
     */
    public function postPersist($args): void
    {
        $this->emailService->register($args);
    }
}