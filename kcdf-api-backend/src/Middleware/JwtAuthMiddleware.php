<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\SystemClock;
use DateInterval;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Log\LoggerInterface;

class JwtAuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly array $config,
        private readonly ?LoggerInterface $logger = null,
    ) {}

    public function process(Request $request, Handler $handler): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
            return $this->unauthenticated();
        }

        $token = substr($authHeader, 7);

        try {
            $secret = $this->config['jwt']['secret'] ?? null;
            if (empty($secret)) {
                $this->logger?->error('JWT secret is not configured');
                throw new \RuntimeException('JWT secret is not configured');
            }

            $jwtConfig = Configuration::forSymmetricSigner(
                new Sha256(),
                InMemory::plainText($secret)
            );

            $parsedToken = $jwtConfig->parser()->parse($token);

            $constraints = [
                new SignedWith($jwtConfig->signer(), $jwtConfig->signingKey()),
                new StrictValidAt(new SystemClock(), new DateInterval('PT60S')),
            ];

            if (!$jwtConfig->validator()->validate($parsedToken, ...$constraints)) {
                return $this->unauthenticated();
            }

            $claims = $parsedToken->claims();

            // Access routes must not accept refresh tokens
            if ($claims->get('type') === 'refresh') {
                return $this->unauthenticated();
            }

            $payload = [];
            foreach ($claims->all() as $name => $value) {
                if ($value instanceof DateTimeImmutable) {
                    $payload[$name] = $value->getTimestamp();
                    continue;
                }
                $payload[$name] = $value;
            }

            $payload['profile_id'] = (int) ($payload['profile_id'] ?? $payload['sub'] ?? 0);
            $payload['sub'] = (string) ($payload['sub'] ?? $payload['profile_id']);
            $payload['roles'] = array_values((array) ($payload['roles'] ?? []));
            $payload['family_ids'] = array_map('intval', (array) ($payload['family_ids'] ?? []));

            if ($payload['profile_id'] <= 0) {
                return $this->unauthenticated();
            }

            $request = $request->withAttribute('jwt_payload', $payload);
            return $handler->handle($request);
        } catch (\Throwable $e) {
            $this->logger?->warning('JWT authentication failed', [
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
            ]);
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
