<?php

namespace App\Controller;

use App\DTO\RegisterDTO;
use App\Repository\UserRepository;
use App\Security\CookieManager;
use App\Security\TokenManager;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController extends AbstractController {
    public function __construct(private AuthService $authService, private TokenManager $tokenManager, private CookieManager $cookieManager, private UserRepository $userRepository) {}

    #[Route('/api/v1/login', name: 'api_login', methods: ['POST'])]
    public function index(): JsonResponse {
        return $this->json(['message' => 'This needs to be intercepted by json_login']);
    }

    #[Route('/api/v1/register', name: 'api_register', methods: ['POST'])]
    public function register(#[MapRequestPayload] RegisterDTO $registerDTO): JsonResponse{
        $user = $this->authService->register($registerDTO);
        $accessToken = $this->tokenManager->createAccessToken($user);
        $refreshCookie = $this->cookieManager->createRefreshTokenCookie($this->tokenManager->createRefreshToken($user)); 
        $response =  $this->json(['message' => 'User registered successfully', 'user' => $user, 'token' => $accessToken], Response::HTTP_CREATED);
        $response->headers->setCookie($refreshCookie);
        return $response;
    }

    #[Route('/api/v1/refresh', name: 'api_refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse {
        $refreshToken = $request->cookies->get('REFRESH_TOKEN');
        if (!$refreshToken) return $this->json(['error' => 'Refresh token missing'], Response::HTTP_UNAUTHORIZED);

        $payload = $this->tokenManager->decode($refreshToken);
        if (($payload['type'] ?? '') !== 'refresh_token') throw new \Exception('Invalid token type', code: Response::HTTP_BAD_REQUEST);

        $user = $this->userRepository->findOneBy(['email' => $payload['username']]);
        if (!$user) throw new NotFoundHttpException('User not found', code: Response::HTTP_NOT_FOUND);

        $newAccessToken = $this->tokenManager->createAccessToken($user);
        $newRefreshToken = $this->tokenManager->createRefreshToken($user);

        $response = $this->json(['token' => $newAccessToken, 'message' => 'Token refreshed successfully']);
        $response->headers->setCookie($this->cookieManager->createRefreshTokenCookie($newRefreshToken));
        return $response;
    }
}

