<?php

declare(strict_types=1);

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

// Build DI container
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/container.php');
$container = $containerBuilder->build();

// Create Slim app
AppFactory::setContainer($container);
$app = AppFactory::create();

// Load bootstrap (middleware, error handling)
(require __DIR__ . '/../bootstrap/app.php')($app);

// Load routes
(require __DIR__ . '/../routes/api.php')($app);

$app->run();
