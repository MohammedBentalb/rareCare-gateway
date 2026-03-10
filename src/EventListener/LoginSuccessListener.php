<?php

namespace App\EventListener;

use DateTimeImmutable;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Security\TokenManager;
use App\Security\CookieManager;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[AsEventListener(event: 'lexik_jwt_authentication.on_authentication_success')]
final class LoginSuccessListener {
    public function __construct( private TokenManager $tokenManager, private CookieManager $cookieManager) {}

    public function __invoke(AuthenticationSuccessEvent $event): void {
        $user = $event->getUser();
        if (!$user) return;

        $accessToken = $this->tokenManager->createAccessToken($user);
        $refreshToken = $this->tokenManager->createRefreshToken($user);

        $data = $event->getData();
        $response = $event->getResponse();

        $data['token'] = $accessToken;
        $data['message'] = 'success';

        $response->headers->setCookie($this->cookieManager->createRefreshTokenCookie($refreshToken));
        $event->setData($data);
    }
}


