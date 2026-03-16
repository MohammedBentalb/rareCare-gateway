<?php

namespace App\Controller;

use App\Service\RegisteryService;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class GatewayProxyController extends AbstractController
{
    public function __construct(private HttpClientInterface $httpClient, private RegisteryService $registeryService) {}

    #[Route('/api/v1/{service}/{path}', requirements: ['path' => ".*"], defaults: ['path' => ''], name: 'app_gateway_proxy')]
    public function proxy(string $service, Request $request, string $path = ''):StreamedResponse {
        if(!$this->registeryService->has($service)) throw new RuntimeException('Unknown Service');
        
        $targetUrl = rtrim($this->registeryService->get($service), '/');
        if ($path !== '') {
            $targetUrl .= '/' . ltrim($path, '/');
        }


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
