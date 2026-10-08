<?php

declare(strict_types=1);

require __DIR__ . '/../config/init.php';

$dispatcher = FastRoute\simpleDispatcher(static function (FastRoute\RouteCollector $routeCollector): void {
    $register = require __DIR__ . '/../api/routes.php';
    $register($routeCollector);
});

$mustMatch = [
    ['GET', '/api/v1/auth/me'],
    ['POST', '/api/v1/auth/login'],
    ['GET', '/api/v1/programs'],
    ['GET', '/api/v1/families/42'],
    ['PATCH', '/api/v1/notifications/7/read'],
];

foreach ($mustMatch as [$method, $uri]) {
    $routeInfo = $dispatcher->dispatch($method, $uri);
    if ($routeInfo[0] !== FastRoute\Dispatcher::FOUND) {
        fwrite(STDERR, "Route not matched: {$method} {$uri}\n");
        exit(1);
    }
}

$notFound = $dispatcher->dispatch('GET', '/api/v1/not-real');
if ($notFound[0] !== FastRoute\Dispatcher::NOT_FOUND) {
    fwrite(STDERR, "Expected NOT_FOUND for unknown route\n");
    exit(1);
}

$methodNotAllowed = $dispatcher->dispatch('DELETE', '/api/v1/auth/me');
if ($methodNotAllowed[0] !== FastRoute\Dispatcher::METHOD_NOT_ALLOWED) {
    fwrite(STDERR, "Expected METHOD_NOT_ALLOWED for unsupported method\n");
    exit(1);
}

echo "phase-1 smoke checks passed\n";
