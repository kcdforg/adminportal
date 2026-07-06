<?php

declare(strict_types=1);

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class JwtAuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly array $config
    ) {}

    public function process(Request $request, Handler $handler): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
            return $this->unauthenticated();
        }

        $token = substr($authHeader, 7);

        try {
            $decoded = JWT::decode($token, new Key($this->config['jwt']['secret'], 'HS256'));
            $request = $request->withAttribute('jwt_payload', (array) $decoded);
            return $handler->handle($request);
        } catch (\Throwable $e) {
            return $this->unauthenticated();
        }
    }

    private function unauthenticated(): Response
    {
        $response = $this->responseFactory->createResponse(401);
        $response->getBody()->write(json_encode([
            'success' => false,
            'error'   => [
                'code'    => 'UNAUTHENTICATED',
                'message' => 'A valid authentication token is required.',
            ],
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
