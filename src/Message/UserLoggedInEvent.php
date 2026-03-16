<?php

namespace App\Message;

class UserLoggedInEvent
{
    public function __construct(
        public string $userId,
        public string $email,
        public string $event = 'auth.user.login'
    ) {}
}
