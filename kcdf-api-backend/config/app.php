<?php

declare(strict_types=1);

return [
    'name'        => $_ENV['APP_NAME'] ?? 'KCDF Parents API',
    'env'         => $_ENV['APP_ENV'] ?? 'production',
    'debug'       => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'jwt' => [
        'secret'      => $_ENV['JWT_SECRET'],
        'access_ttl'  => (int) ($_ENV['JWT_ACCESS_TTL'] ?? 900),
        'refresh_ttl' => (int) ($_ENV['JWT_REFRESH_TTL'] ?? 2592000),
    ],
    'cors' => [
        'allowed_origins' => explode(',', $_ENV['CORS_ALLOWED_ORIGINS'] ?? ''),
    ],
];
