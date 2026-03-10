<?php

namespace App\Service;

use App\DTO\RegisterDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService {
    public function __construct( private UserRepository $userRepository, private UserPasswordHasherInterface $passwordHasher) {}
    public function register(RegisterDTO $dto): User {
        $user = new User();
        $user->setEmail($dto->email);
        $user->setUsername($dto->username);
        
        $hashedPassword = $this->passwordHasher->hashPassword($user, $dto->password);
        $user->setPassword($hashedPassword);

        $this->userRepository->create($user);
        return $user;
    }
}
