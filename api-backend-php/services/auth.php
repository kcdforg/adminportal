<?php

declare(strict_types=1);

function getJwtConfig(): array
{
    $config = require dirname(__DIR__) . '/config/config.php';
    $algorithm = strtoupper((string) ($config['jwt']['algorithm'] ?? 'HS256'));
    $allowedAlgorithms = ['HS256', 'HS384', 'HS512'];

    if (!in_array($algorithm, $allowedAlgorithms, true)) {
        throw new RuntimeException('Unsupported JWT algorithm configured. Allowed algorithms: HS256, HS384, HS512.');
    }

    $secret = trim((string) ($config['jwt']['secret'] ?? ''));
    if ($secret === '') {
        throw new RuntimeException('JWT secret is missing.');
    }

    if (strlen($secret) < 32) {
        throw new RuntimeException('JWT secret is too weak for the configured algorithm.');
    }

    return ['algorithm' => $algorithm, 'secret' => $secret];
}

function getJwtAlgorithm(): string
{
    return getJwtConfig()['algorithm'];
}

function getJwtSecret(): string
{
    return getJwtConfig()['secret'];
}

function validateJwtConfiguration(): void
{
    try {
        getJwtConfig();
    } catch (Throwable $exception) {
        error_log('JWT startup validation failed: ' . $exception->getMessage());
        throw $exception;
    }
}

function createToken(array $claims, string $type = 'access', ?string $algorithm = null): string
{
    $jwtConfig = getJwtConfig();
    $secret = $jwtConfig['secret'];
    $algorithm = strtoupper($algorithm ?? $jwtConfig['algorithm']);
    $normalizedClaims = $claims;

    if (!isset($normalizedClaims['sub'])) {
        $normalizedClaims['sub'] = (string) (
            $normalizedClaims['login_id'] ?? $normalizedClaims['profile_id'] ?? ''
        );
    }

    $normalizedClaims['type'] = $type === 'refresh' ? 'refresh' : 'access';
    $normalizedClaims['iat'] = (int) ($normalizedClaims['iat'] ?? time());
    $normalizedClaims['nbf'] = (int) ($normalizedClaims['nbf'] ?? $normalizedClaims['iat']);
    $normalizedClaims['exp'] = (int) ($normalizedClaims['exp'] ?? (
        $normalizedClaims['iat'] + ($type === 'refresh' ? 30 * 24 * 60 * 60 : 15 * 60)
    ));

    return \Firebase\JWT\JWT::encode($normalizedClaims, $secret, $algorithm);
}

function extractBearerToken(?string $authorizationHeader = null): string
{
    $header = $authorizationHeader;
    if ($header === null) {
        foreach (['HTTP_AUTHORIZATION', 'REDIRECT_HTTP_AUTHORIZATION'] as $serverKey) {
            if (isset($_SERVER[$serverKey]) && is_string($_SERVER[$serverKey]) && trim($_SERVER[$serverKey]) !== '') {
                $header = $_SERVER[$serverKey];
                break;
            }
        }
    }
    if ($header === null && function_exists('getallheaders')) {
        foreach (getallheaders() as $name => $value) {
            if (strcasecmp((string) $name, 'Authorization') === 0 && is_string($value)) {
                $header = $value;
                break;
            }
        }
    }
    $header ??= '';

    if (!is_string($header) || trim($header) === '') {
        throw new RuntimeException('A valid authentication token is required.', 401);
    }

    if (!preg_match('/^Bearer\s+(.+)$/i', trim($header), $matches)) {
        throw new RuntimeException('A valid authentication token is required.', 401);
    }

    $token = trim($matches[1]);
    if ($token === '') {
        throw new RuntimeException('A valid authentication token is required.', 401);
    }

    return $token;
}

function decodeJwtToken(string $token, bool $allowRefreshToken = false): array
{
    try {
        $jwtConfig = getJwtConfig();
        $algorithm = $jwtConfig['algorithm'];
        $secret = $jwtConfig['secret'];

        $payload = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($secret, $algorithm));
        $decoded = json_decode(json_encode($payload), true);
        if (!is_array($decoded)) {
            throw new RuntimeException('The token payload could not be decoded.', 401);
        }

        $decoded['profile_id'] = (int) ($decoded['profile_id'] ?? 0);
        $decoded['sub'] = (string) ($decoded['sub'] ?? $decoded['login_id'] ?? '');
        $decoded['roles'] = array_values(array_filter(
            array_map('strval', (array) ($decoded['roles'] ?? [])),
            static fn ($value) => $value !== ''
        ));
        $decoded['family_ids'] = array_values(array_map('intval', (array) ($decoded['family_ids'] ?? [])));

        $tokenType = (string) ($decoded['type'] ?? 'access');
        if ($tokenType === 'refresh' && !$allowRefreshToken) {
            throw new RuntimeException('Refresh tokens are not valid for this request.', 401);
        }

        if ((int) ($decoded['login_id'] ?? 0) <= 0
            || !in_array(($decoded['user_type'] ?? null), ['admin', 'member'], true)
            || ($decoded['portal'] ?? null) !== $decoded['user_type']
            || ($decoded['user_type'] === 'member' && $decoded['profile_id'] <= 0)) {
            throw new RuntimeException('Invalid token payload.', 401);
        }

        return $decoded;
    } catch (Throwable $exception) {
        if ((int) $exception->getCode() === 500) {
            throw $exception;
        }
        throw new RuntimeException('A valid authentication token is required.', 401);
    }
}

function checkAuth(?string $authorizationHeader = null, bool $allowRefreshToken = false): array
{
    return decodeJwtToken(extractBearerToken($authorizationHeader), $allowRefreshToken);
}
