<?php

namespace App\Security;

use App\Entity\User;
use DateTimeImmutable;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TokenManager
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private JWTEncoderInterface $jwtEncoder
    ) {}

    public function createAccessToken(UserInterface $user): string {
        return $this->jwtManager->createFromPayload($user, [
            'type' => 'access_token',
            'exp' => (new DateTimeImmutable('+1 days'))->getTimestamp()
        ]);
    }

    public function createRefreshToken(UserInterface $user): string {
        return $this->jwtManager->createFromPayload($user, [
            'type' => 'refresh_token',
            'exp' => (new DateTimeImmutable('+3 days'))->getTimestamp()
        ]);
    }

    
    public function decode(string $token): array {
        return $this->jwtEncoder->decode($token);
    }
}

