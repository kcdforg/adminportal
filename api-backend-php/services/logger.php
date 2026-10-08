<?php

declare(strict_types=1);

function startupLogger(): ?Psr\Log\LoggerInterface
{
    static $logger = null;
    if ($logger !== null) {
        return $logger;
    }

    $config = require dirname(__DIR__) . '/config/config.php';
    $logPath = $config['logging']['path'] ?? dirname(__DIR__) . '/storage/logs/app.log';
    $dir = dirname($logPath);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Unable to create the application log directory.');
    }

    if (!class_exists('Monolog\Logger')) {
        return null;
    }

    $logger = new Monolog\Logger('api-backend-php');
    $logger->pushHandler(new Monolog\Handler\StreamHandler(
        $logPath,
        Monolog\Logger::toMonologLevel($config['logging']['level'] ?? 'info')
    ));

    return $logger;
}
