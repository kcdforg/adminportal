<?php

declare(strict_types=1);

return [
    'title' => 'KCDF Parents Platform — API',
    'description' => 'REST API for the KCDF Parents platform. Serves Parent App and Admin Portal.',
    'version' => '1.0.0',
    'servers' => [
        'development' => 'http://localhost:8080',
        'production' => 'https://api.example.com',
    ],
    'contact' => [
        'name' => 'KCDF Support',
        'email' => 'support@kcdf.org',
    ],
    'license' => [
        'name' => 'Proprietary',
    ],
    'externalDocs' => [
        'description' => 'Full API Documentation',
        'url' => 'https://docs.kcdf.org/api',
    ],
];
