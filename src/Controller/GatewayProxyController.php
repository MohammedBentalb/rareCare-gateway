<?php

namespace App\Controller;

use App\Security\TokenManager;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class GatewayProxyController extends AbstractController
{

    public function __construct(private HttpClientInterface $httpClient) {}

    #[Route('/api/v1/{service}/{path}', requirements: ['path' => ".*"] , name: 'app_gateway_proxey')]
    public function proxy(string $service, string $path, Request $request):StreamedResponse {
        $serviceMap = [];

        if(!isset($serviceMap[$service])) throw new RuntimeException('Unknown Service');
        $targetUrl = rtrim($serviceMap[$service], '/') . '/' . $path;

        $headers = $request->headers->all();
        unset($headers['host'], $headers['content-length'], $headers['transfer-encoding'], $headers['connection']);
        
        $clientResponse = $this->httpClient->request(
            $request->getMethod(),
            $targetUrl,
            [
                'headers' => $headers,
                'query' => $request->query->all(),
                'body' => $request->getContent() 
            ]
        );

        return new StreamedResponse(function() use ($clientResponse) {
            foreach ($this->httpClient->stream($clientResponse) as $chunk) {
                if($chunk->isTimeout()){
                    continue;
                }
                echo $chunk->getContent();
                flush();
            }
        }, $clientResponse->getStatusCode() ,$clientResponse->getHeaders(false));
    }
}
