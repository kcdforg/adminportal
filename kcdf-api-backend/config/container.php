<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;

return [

    // PSR Response Factory
    ResponseFactoryInterface::class => function () {
        return new ResponseFactory();
    },

    // Database — Eloquent via Capsule
    Capsule::class => function () {
        $capsule = new Capsule();
        $capsule->addConnection(require __DIR__ . '/database.php');
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        return $capsule;
    },

    // Logger
    LoggerInterface::class => function () {
        $logger = new Logger('kcdf');
        $logger->pushHandler(new StreamHandler(__DIR__ . '/../storage/logs/app.log', Logger::DEBUG));
        return $logger;
    },

    // App config
    'config' => require __DIR__ . '/app.php',

];
