<?php

declare(strict_types=1);

use App\Middleware\CorsMiddleware;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;

return function (App $app) {

    // Parse JSON request bodies
    $app->addBodyParsingMiddleware();

    // Routing middleware
    $app->addRoutingMiddleware();

    // CORS middleware (must be added before routing)
    $app->add(CorsMiddleware::class);

    // Error middleware
    $displayErrors = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $errorMiddleware = $app->addErrorMiddleware($displayErrors, true, true);

    // Bootstrap Eloquent by resolving Capsule from container
    $app->getContainer()->get(\Illuminate\Database\Capsule\Manager::class);

};
