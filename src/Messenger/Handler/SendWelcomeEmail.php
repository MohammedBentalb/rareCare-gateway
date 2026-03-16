<?php

namespace App\Messenger\Handler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Message\UserRegisteredEvent;
use App\Service\EmailService;

#[AsMessageHandler]
class SendWelcomeEmail {

    public function __construct(private EmailService $emailService) {}
    public function __invoke(UserRegisteredEvent $registerEvent){
        $this->emailService->sendEmail($registerEvent);
    }
}
