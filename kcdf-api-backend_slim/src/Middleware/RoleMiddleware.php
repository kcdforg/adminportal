<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class RoleMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly array $allowedRoles
    ) {}

    public function process(Request $request, Handler $handler): Response
    {
        $payload = $request->getAttribute('jwt_payload', []);
        $userRoles = $payload['roles'] ?? [];

        foreach ($this->allowedRoles as $role) {
            if (in_array($role, $userRoles, true)) {
                return $handler->handle($request);
            }
        }

        $response = $this->responseFactory->createResponse(403);
        $response->getBody()->write(json_encode([
            'success' => false,
            'error'   => [
                'code'    => 'UNAUTHORIZED',
                'message' => 'You do not have permission to perform this action.',
            ],
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
