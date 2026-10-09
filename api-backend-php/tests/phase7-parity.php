<?php

declare(strict_types=1);

$root = dirname(__DIR__);
require $root . '/config/autoload.php';
require_once $root . '/libraries/fastroute-1.3.0/src/functions.php';
require_once $root . '/http/helpers.php';
require_once $root . '/services/auth.php';

$routeSource = file_get_contents($root . '/api/routes.php');
$inventory = file_get_contents($root . '/MIGRATION_INVENTORY.md');
if ($routeSource === false || $inventory === false) {
    throw new RuntimeException('Unable to read the route registry or migration inventory.');
}

$assertions = 0;
$assert = static function (bool $condition, string $message) use (&$assertions): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
    $assertions++;
};

preg_match_all(
    "/addRoute\\('([^']+)', '([^']+)', '([^']+)'\\);/",
    $routeSource,
    $matches,
    PREG_SET_ORDER
);
$assert($matches !== [], 'The FastRoute registry must contain registered endpoints.');

$dispatcher = FastRoute\simpleDispatcher(static function (FastRoute\RouteCollector $collector) use ($root): void {
    $register = require $root . '/api/routes.php';
    $register($collector);
});

$seenRoutes = [];
foreach ($matches as [, $method, $registeredPath, $handler]) {
    $publicPath = preg_replace_callback(
        '/\{([^}:]+)(?::[^}]+)?\}/',
        static fn (array $parameter): string => '{' . $parameter[1] . '}',
        $registeredPath
    );
    $routeKey = $method . ' ' . $publicPath;
    $assert(!isset($seenRoutes[$routeKey]), "Duplicate route registration: {$routeKey}");
    $seenRoutes[$routeKey] = true;

    $assert(
        preg_match('~^[a-z0-9/-]+\.php$~', $handler) === 1 && is_file($root . '/api/' . $handler),
        "Route handler must be a fixed, existing endpoint: {$handler}"
    );
    $assert(
        str_contains(
            $inventory,
            '| ' . $method . ' | `' . $publicPath . '` | `'
                . $method . ' ' . $publicPath . '` | `api/' . $handler . '` |'
        ),
        "Route is missing from the migration inventory: {$routeKey}"
    );

    $testPath = preg_replace_callback(
        '/\{([^}:]+)(?::([^}]+))?\}/',
        static fn (array $parameter): string => ($parameter[2] ?? '') === '[0-9]+' ? '7' : 'TESTCODE0123',
        $registeredPath
    );
    $requestUri = $testPath . ($method === 'GET' ? '?page=2&per_page=5' : '');
    $dispatchPath = parse_url($requestUri, PHP_URL_PATH);
    $result = $dispatcher->dispatch($method, $dispatchPath);
    $assert(
        $result[0] === FastRoute\Dispatcher::FOUND && $result[1] === $handler,
        "FastRoute did not map {$routeKey} to {$handler}"
    );
    preg_match_all('/\{([^}:]+)(?::([^}]+))?\}/', $registeredPath, $parameters, PREG_SET_ORDER);
    foreach ($parameters as $parameter) {
        $name = $parameter[1];
        $constraint = $parameter[2] ?? '';
        $expectedValue = $constraint === '[0-9]+' ? '7' : 'TESTCODE0123';
        $assert(
            ($result[2][$name] ?? null) === $expectedValue,
            "FastRoute did not pass the expected value for {$name} in {$routeKey}"
        );
    }
    if ($method === 'GET') {
        $assert(
            parse_url($requestUri, PHP_URL_QUERY) === 'page=2&per_page=5',
            "The request query string was not preserved for {$routeKey}"
        );
    }
}

$assert(count($matches) === count($seenRoutes), 'Every registered endpoint must have one unique method and path.');
$assert(
    $dispatcher->dispatch('GET', '/api/v1/not-a-real-endpoint')[0] === FastRoute\Dispatcher::NOT_FOUND,
    'Unknown API endpoints must resolve to NOT_FOUND.'
);
$assert(
    $dispatcher->dispatch('GET', '/api/v1/groups/')[0] === FastRoute\Dispatcher::NOT_FOUND,
    'Trailing slashes must not silently change route matching.'
);
$assert(
    $dispatcher->dispatch('DELETE', '/api/v1/auth/me')[0] === FastRoute\Dispatcher::METHOD_NOT_ALLOWED,
    'Unsupported methods on known endpoints must resolve to METHOD_NOT_ALLOWED.'
);
$originalAuthorization = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
$originalRedirectAuthorization = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? null;
unset($_SERVER['HTTP_AUTHORIZATION']);
$_SERVER['REDIRECT_HTTP_AUTHORIZATION'] = 'Bearer test-token';
$assert(
    extractBearerToken() === 'test-token',
    'Authentication must accept Apache REDIRECT_HTTP_AUTHORIZATION headers.'
);
if ($originalAuthorization === null) {
    unset($_SERVER['HTTP_AUTHORIZATION']);
} else {
    $_SERVER['HTTP_AUTHORIZATION'] = $originalAuthorization;
}
if ($originalRedirectAuthorization === null) {
    unset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
} else {
    $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] = $originalRedirectAuthorization;
}
$mountedPath = normalizeApiRequestPath(
    '/kcdf-parent/api-backend-php/api/v1/auth/login?source=swagger',
    '/kcdf-parent/api-backend-php/api/index.php'
);
$assert(
    $mountedPath === '/api/v1/auth/login'
        && $dispatcher->dispatch('POST', $mountedPath)[0] === FastRoute\Dispatcher::FOUND,
    'The API front controller must strip the backend mount path before dispatching.'
);
$assert(
    normalizeApiRequestPath('/api/v1/auth/login', '/api/index.php') === '/api/v1/auth/login',
    'The API front controller must preserve route paths when installed at the document root.'
);
$rewriteRules = file_get_contents($root . '/.htaccess');
$frontController = file_get_contents($root . '/api/index.php');
$installer = file_get_contents($root . '/install/index.php');
$configLoadPosition = is_string($frontController) ? strpos($frontController, '/config/config.php') : false;
$optionsCheckPosition = is_string($frontController) ? strpos($frontController, "=== 'OPTIONS'") : false;
$runtimeInitPosition = is_string($frontController) ? strpos($frontController, '/config/init.php') : false;
$assert(
    is_string($rewriteRules)
        && str_contains($rewriteRules, '<IfModule mod_rewrite.c>')
        && str_contains($rewriteRules, '</IfModule>')
        && str_contains($rewriteRules, 'Options -Indexes')
        && str_contains($rewriteRules, 'RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]')
        && str_contains($rewriteRules, 'RewriteRule ^(?:\\.[^/]+|config|database|http|install|libraries|repositories|services|storage|tests)(?:/|$) - [F,L,NC]')
        && str_contains($rewriteRules, 'RewriteRule ^api/v1(?:/.*)?$ api/index.php [END,QSA,NC]')
        && str_contains($rewriteRules, 'RewriteRule ^api/.*\\.php(?:/.*)?$ - [F,L,NC]'),
    'Apache rules must preserve the API front-controller rewrite while denying direct PHP requests.'
);
$assert(
    is_string($installer)
        && !str_contains($installer, 'name="admin_first_name"')
        && !str_contains($installer, 'name="admin_last_name"')
        && !str_contains($installer, 'name="user_first_name"')
        && !str_contains($installer, 'name="user_last_name"')
        && !str_contains($installer, 'minlength="12"')
        && !str_contains($installer, 'strlen($password) < 12')
        && !str_contains($installer, 'strlen($userPassword) < 12')
        && !str_contains($installer, 'name="confirm_new_database"')
        && !str_contains($installer, 'name="confirm_existing_database"')
        && str_contains($installer, 'installerVerifyExistingSchema($pdo, $root . \'/database/schema.sql\')'),
    'The installer must omit account-name fields, password minimums, and database confirmation checkboxes, and verify an existing schema without importing it.'
);
$assert(
    is_string($frontController)
        && str_contains($frontController, 'normalizeApiRequestPath($uri, $scriptName)')
        && str_contains($frontController, "'Content-Type: application/json; charset=utf-8'")
        && str_contains($frontController, 'is_file($file)')
        && is_int($configLoadPosition)
        && is_int($optionsCheckPosition)
        && is_int($runtimeInitPosition)
        && $configLoadPosition < $optionsCheckPosition
        && $optionsCheckPosition < $runtimeInitPosition,
    'The front controller must parse paths, return JSON, and handle missing endpoint files.'
);

echo 'phase-7 route parity checks passed (' . $assertions . " assertions; " . count($matches) . " routes)\n";
