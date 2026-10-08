<?php

declare(strict_types=1);

$root = dirname(__DIR__);

try {
    $config = require $root . '/config/config.php';
} catch (Throwable $exception) {
    error_log('API configuration failure: ' . $exception->getMessage());
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => [
            'code' => 'STARTUP_ERROR',
            'message' => 'The API configuration could not be loaded.',
        ],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit(1);
}

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowedOrigins = $config['cors']['allowed_origins'] ?? [];
if ($origin !== '' && in_array($origin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Vary: Origin');
header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $bootstrap = require $root . '/config/init.php';
    $config = $bootstrap['config'];
} catch (Throwable $exception) {
    error_log('API startup failure: ' . $exception->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => [
            'code' => 'STARTUP_ERROR',
            'message' => 'The API runtime could not be initialized.',
        ],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit(1);
}

$httpMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/api/index.php';
$path = normalizeApiRequestPath($uri, $scriptName);

$dispatcher = FastRoute\simpleDispatcher(static function (FastRoute\RouteCollector $routeCollector): void {
    $register = require __DIR__ . '/routes.php';
    $register($routeCollector);
});

$routeInfo = $dispatcher->dispatch($httpMethod, $path);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 'NOT_FOUND',
                'message' => 'The requested API route was not found.',
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = implode(', ', $routeInfo[1]);
        header('Allow: ' . $allowedMethods);
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 'METHOD_NOT_ALLOWED',
                'message' => 'The requested HTTP method is not allowed for this route.',
                'details' => [
                    'allowed_methods' => $routeInfo[1],
                ],
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;

    case FastRoute\Dispatcher::FOUND:
        [, $handler, $vars] = $routeInfo;
        $routeParams = $vars ?? [];
        $file = __DIR__ . '/' . $handler;

        if (!is_file($file)) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => [
                    'code' => 'ROUTE_HANDLER_NOT_FOUND',
                    'message' => 'The configured route handler is missing.',
                ],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            exit;
        }

        try {
            require $file;
        } catch (Throwable $exception) {
            handleException($exception);
        }
        exit;

    default:
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 'ROUTER_ERROR',
                'message' => 'The router returned an unexpected status.',
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
}
