<?php 


namespace App\Message;

class UserRegisteredEvent {
    public function __construct(public string $userId, public string $email, public string $name, public string $event) {}
}