<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class CorsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly array $config
    ) {}

    public function process(Request $request, Handler $handler): Response
    {
        try {
            $origin = $request->getHeaderLine('Origin');
            $allowedOrigins = $this->config['cors']['allowed_origins'] ?? [];

            // Check if origin is allowed
            $isAllowed = false;
            
            // Check against explicit whitelist
            if (in_array($origin, $allowedOrigins, true)) {
                $isAllowed = true;
            }
            
            // Allow all subdomains of kcdfindia.com (both http and https)
            if (!$isAllowed && !empty($origin)) {
                $parsedUrl = parse_url($origin);
                $host = $parsedUrl['host'] ?? '';
                
                // Match kcdfindia.com and all its subdomains
                if ($host === 'kcdfindia.com' || preg_match('/\.kcdfindia\.com$/', $host)) {
                    $isAllowed = true;
                }
            }

            // Handle preflight requests
            if ($request->getMethod() === 'OPTIONS') {
                $response = $this->responseFactory->createResponse(204);
                
                if ($isAllowed && !empty($origin)) {
                    $response = $response->withHeader('Access-Control-Allow-Origin', $origin);
                    $response = $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
                    $response = $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
                    $response = $response->withHeader('Access-Control-Max-Age', '86400');
                    $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
                }
                
                return $response;
            }

            $response = $handler->handle($request);

            if ($isAllowed && !empty($origin)) {
                $response = $response->withHeader('Access-Control-Allow-Origin', $origin);
                $response = $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
                $response = $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
                $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
            }

            return $response;
        } catch (\Throwable $e) {
            // If there's an error in CORS middleware, let the request continue
            // This prevents CORS processing from breaking the entire application
            return $handler->handle($request);
        }
    }
}
