<?php

namespace App\Message;

class UserLoggedOutEvent {
    public function __construct(
        public string $userId,
        public string $event = 'auth.user.logout'
    ) {}
}
