<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\UniqueEmail;

class RegisterDTO {

    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    public string $username;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[UniqueEmail]
    public string $email;
    
    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public string $password;
}