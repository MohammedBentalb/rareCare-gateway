<?php

namespace App\Security;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Cookie;

class CookieManager {
    public function createRefreshTokenCookie(string $token): Cookie {
        return Cookie::create('REFRESH_TOKEN')
            ->withValue($token)
            ->withExpires(new DateTimeImmutable('+3 days'))
            ->withPath('/')
            ->withHttpOnly(true)
            ->withSecure(isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'prod')
            ->withSameSite(Cookie::SAMESITE_LAX);
    }
}
