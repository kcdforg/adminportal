<?php

declare(strict_types=1);

// TEMPORARY: Enable error display for debugging
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$root = dirname(__DIR__);
$lockFile = $root . '/storage/installed.lock';

if (!is_file($lockFile)) {
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    header('Location: ' . ($base === '' ? '' : $base) . '/install/');
    exit;
}

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable($root);
$dotenv->load();

// TEMPORARY: Force debug mode for troubleshooting
putenv('APP_DEBUG=true');
$_ENV['APP_DEBUG'] = 'true';

// Build DI container
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/container.php');
$container = $containerBuilder->build();

// Create Slim app
AppFactory::setContainer($container);
$app = AppFactory::create();

// Load bootstrap (middleware, error handling)
(require __DIR__ . '/../bootstrap/app.php')($app);

// Load API routes (under /api/v1)
(require __DIR__ . '/../routes/api.php')($app);

// Load documentation routes (Swagger under /swagger)
(require __DIR__ . '/../routes/documentation.php')($app);

$app->run();
