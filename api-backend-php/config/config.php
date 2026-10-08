<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$envFile = $root . '/.env';

if (is_file($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines !== false) {
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            [$key, $value] = array_pad(explode('=', $trimmed, 2), 2, '');
            $key = trim($key);
            $value = trim($value);
            if (strlen($value) >= 2 && $value[0] === '"' && str_ends_with($value, '"')) {
                $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                if (!is_string($value)) {
                    throw new RuntimeException("Invalid quoted environment value for {$key}.");
                }
            }
            $_ENV[$key] = $value;
            putenv($key . '=' . $value);
        }
    }
}

$allowedOrigins = getenv('CORS_ALLOWED_ORIGINS') ?: 'http://localhost:4200,http://localhost:8100';

return [
    'app' => [
        'name' => getenv('APP_NAME') ?: 'KCDF Parents API',
        'env' => getenv('APP_ENV') ?: 'development',
        'debug' => filter_var(getenv('APP_DEBUG') ?: 'true', FILTER_VALIDATE_BOOLEAN),
    ],
    'database' => [
        'type' => getenv('DB_TYPE') ?: 'mysql',
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_DATABASE') ?: 'kcdf_parents',
        'username' => getenv('DB_USERNAME') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
    'jwt' => [
        'secret' => getenv('JWT_SECRET') ?: '',
        'algorithm' => getenv('JWT_ALGORITHM') ?: 'HS256',
        'access_ttl' => (int) (getenv('JWT_ACCESS_TTL') ?: 900),
        'refresh_ttl' => (int) (getenv('JWT_REFRESH_TTL') ?: 2592000),
    ],
    'cors' => [
        'allowed_origins' => array_values(array_filter(array_map('trim', explode(',', $allowedOrigins)), static fn ($origin) => $origin !== '')),
    ],
    'logging' => [
        'path' => $root . '/storage/logs/app.log',
        'level' => getenv('LOG_LEVEL') ?: 'info',
    ],
];
