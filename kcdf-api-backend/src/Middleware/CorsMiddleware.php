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
            $allowedOrigins = array_values(array_filter(array_map(
                'trim',
                $this->config['cors']['allowed_origins'] ?? []
            )));

            $isAllowed = $origin !== '' && in_array($origin, $allowedOrigins, true);

            if ($request->getMethod() === 'OPTIONS') {
                $response = $this->responseFactory->createResponse(204);

                if ($isAllowed) {
                    $response = $this->withCorsHeaders($response, $origin);
                }

                return $response;
            }

            $response = $handler->handle($request);

            if ($isAllowed) {
                $response = $this->withCorsHeaders($response, $origin);
            }

            return $response;
        } catch (\Throwable) {
            return $handler->handle($request);
        }
    }

    private function withCorsHeaders(Response $response, string $origin): Response
    {
        return $response
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withHeader('Access-Control-Max-Age', '86400')
            ->withHeader('Access-Control-Allow-Credentials', 'true');
    }
}
